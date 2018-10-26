<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use DataTables;

class FinishedGoodsController extends Controller
{

	public function index_fg_production(){
		return view('finished_goods.production')->with('page', 'FG Production')->with('head', 'Finished Goods');
	}

	public function index_fg_stock(){
		return view('finished_goods.stock')->with('page', 'FG Stock')->with('head', 'Finished Goods');
	}

	public function fetch_fg_stock(){
		$stock = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->whereIn('flos.status', [0,1,2]);

		$jsonData = $stock->select('destinations.destination_shortname as destination', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK")) as location'), DB::raw('sum(flos.actual) as actual'))->groupBy('destinations.destination_shortname', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK"))'))->orderBy(DB::raw('field(location, "Production", "InTransit", "FSTK")'))->get();

		$response = array(
			'status' => true,
			'jsonData' => $jsonData,
		);
		return Response::json($response);
	}

	public function fetch_fg_production(){

		$st_month = date('Y-m-01');

		$st_month2 = date('F y');

		$shipment_schedule = DB::table('shipment_schedules')
		->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->where('st_month', '=', $st_month);

		$total_plan = DB::table('shipment_schedules')->where('st_month', '=', $st_month)->sum('shipment_schedules.quantity');
		$total_production = $shipment_schedule->sum('flos.actual');
		$total_delivery = $shipment_schedule->where('flos.status', '>', 1)->sum('flos.actual');
		$total_shipment = $shipment_schedule->where('flos.status', '>', 2)->sum('flos.actual');

		$response = array(
			'status' => true,
			'total_plan' => $total_plan,
			'total_production' => $total_production,
			'total_delivery' => $total_delivery,
			'total_shipment' => $total_shipment,
			'st_month' => $st_month2,
		);
		return Response::json($response); 
	}

	public function fetch_tb_stock(Request $request){
		$stock = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', 'shipment_schedules.material_number')
		->leftJoin('materials', 'materials.material_number', 'shipment_schedules.material_number')
		->where('flos.status', '=', $request->get('status'))
		->where('destinations.destination_shortname', '=', $request->get('destination'))
		->select('shipment_schedules.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width', DB::raw('sum(flos.actual) as actual'))
		->groupBy('shipment_schedules.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width')
		->get();

		if($request->get('status') == 0){
			$location = 'Production';
		}
		elseif($request->get('status') == 1){
			$location = 'InTransit';
		}
		elseif($request->get('status') == 2){
			$location = 'FSTK';
		}

		$response = array(
			'status' => true,
			'table' => $stock,
			'title' => $request->get('destination'),
			'location' => $location,
		);
		return Response::json($response); 
	}

	public function fetch_tb_production(Request $request){
		$st_month = date('Y-m-01');

		$shipment_schedules = DB::table('shipment_schedules')
		->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->where('st_month', '=', $st_month);

		if($request->get('id') == 'production'){
			$table = $shipment_schedules->select('shipment_schedules.id', 'shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity', DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual)) as actual'), DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual))-shipment_schedules.quantity as diff'))->groupBy('shipment_schedules.id', 'shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity')->orderBy('st_date', 'desc')->get();
		}
		elseif($request->get('id') == 'delivery'){
			$query = "select id, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 2, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date desc";
			$table = DB::select($query, ['st_month' => $st_month]);			
		}
		elseif($request->get('id') == 'shipment'){
			$query = "select id, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 3, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date desc";
			$table = DB::select($query, ['st_month' => $st_month]);
		}

		return DataTables::of($table)->make(true);
	}
}
