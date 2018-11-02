<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use ZipArchive;
use File;

class FinishedGoodsController extends Controller
{

	public function index_fg_production(){
		return view('finished_goods.production')->with('page', 'FG Production')->with('head', 'Finished Goods');
	}

	public function index_fg_stock(){
		return view('finished_goods.stock')->with('page', 'FG Stock')->with('head', 'Finished Goods');
	}

	public function index_fg_container_departure(){
		return view('finished_goods.container_departure')->with('page', 'FG Container Departure')->with('head', 'Finished Goods');
	}

	public function index_fg_weekly_summary(){
		$weeks = DB::table('weekly_calendars')->select('week_name')->distinct()->orderBy(db::raw('convert(mid(week_name,2), unsigned integer)'), 'asc')->get();
		$years = DB::table('weekly_calendars')->select(db::raw('year(week_date) as year'))->distinct()->orderBy(db::raw('year(week_date)'), 'asc')->get();
		$fiscalYears = DB::table('weekly_calendars')->select('fiscal_year')->distinct()->orderBy(db::raw('convert(mid(week_name,2), unsigned integer)'), 'asc')->get();

		return view('finished_goods.weekly_summary', array(
			'weeks' => $weeks,
			'years' => $years,
			'fiscalYears' => $fiscalYears,
		))->with('page', 'FG Weekly Summary')->with('head', 'Finished Goods');
	}

	public function index_fg_monthly_summary(){
		$periods = DB::table('shipment_schedules')->select('st_month')->distinct()->get();

		return view('finished_goods.monthly_summary', array(
			'periods' => $periods,
		))->with('page', 'FG Monthly Summary')->with('head', 'Finished Goods');
	}

	public function index_fg_traceability(){
		$origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'asc')->get();
		$materials = DB::table('materials')->orderBy('material_number', 'asc')->get();
		$destinations = DB::table('destinations')->orderBy('destination_code', 'asc')->get();

		return view('finished_goods.traceability', array(
			'origin_groups' => $origin_groups,
			'materials' => $materials,
			'destinations' => $destinations,
		))->with('page', 'FG Traceability')->with('head', 'Finished Goods');
	}

	public function index_fg_shipment_schedule(){
		$periods = DB::table('shipment_schedules')->select('st_month')->distinct()->get();

		return view('finished_goods.shipment_schedule', array(
			'periods' => $periods,
		))->with('page', 'FG Shipment Schedule')->with('head', 'Finished Goods');		
	}


	public function fetch_fg_shipment_schedule(Request $request){
		$shipment_schedules = db::table('shipment_schedules');

		if(strlen($request->get('periodFrom')) > 0){
			$periodFrom = $request->get('periodFrom');
			$shipment_schedules = $shipment_schedules->where('st_month', '>=', $periodFrom);
		}
		else{
			$st_month = date('Y-m-01');			
			$shipment_schedules = $shipment_schedules->where('st_month', '=', $st_month);
		}
		if(strlen($request->get('periodTo')) > 0){
			$periodTo = $request->get('periodTo');
			$shipment_schedules = $shipment_schedules->where('st_month', '<=', $periodTo);
		}

		$shipment_schedules = $shipment_schedules->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
		->select(
			db::raw('date_format(shipment_schedules.st_month, "%b-%Y") as st_month'), 
			'shipment_schedules.id', 'shipment_schedules.sales_order', 
			'destinations.destination_shortname', 
			'materials.material_number', 
			'materials.material_description', 
			'shipment_schedules.quantity', 
			db::raw('if(sum(flos.actual) is null, 0, sum(flos.actual)) as actual'), 
			db::raw('if(sum(flos.actual) is null, 0, sum(flos.actual))-shipment_schedules.quantity as diff'),
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'),
			db::raw('date_format(shipment_schedules.bl_date, "%d-%b-%Y") as bl_date_plan'),
			db::raw('if(date_format(flos.bl_date, "%d-%b-%Y") is null, "-", date_format(flos.bl_date, "%d-%b-%Y")) as bl_date'),
			db::raw('if(flos.container_id is null, "-", flos.container_id) as container_id')
		)
		->groupBy(
			db::raw('date_format(shipment_schedules.st_month, "%b-%Y")'),
			'shipment_schedules.id', 
			'shipment_schedules.sales_order', 
			'destinations.destination_shortname', 
			'materials.material_number', 
			'materials.material_description', 
			'shipment_schedules.quantity', 
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y")'),
			db::raw('date_format(shipment_schedules.bl_date, "%d-%b-%Y")'),
			'flos.bl_date',
			'flos.container_id'
		)
		->get();

		$response = array(
			'status' => true,
			'tableData' => $shipment_schedules,
		);
		return Response::json($response);
	}

	public function fetch_fg_traceability(Request $request){
		$flo_details = DB::table('flo_details');

		if(strlen($request->get('prodFrom')) > 0){
			$prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
			$flo_details = $flo_details->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $prodFrom);
		}
		if(strlen($request->get('prodTo')) > 0){
			$prodTo = date('Y-m-d', strtotime($request->get('prodTo')));
			$flo_details = $flo_details->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $prodTo);
		}
		if(strlen($request->get('materialNumber')) > 0){
			$flo_details = $flo_details->whereIn('flo_details.material_number', $request->get('materialNumber'));
		}
		if(strlen($request->get('serialNumber')) > 0){
			$flo_details = $flo_details->where('flo_details.serial_number', '=', $request->get('serialNumber'));
		}
		if(strlen($request->get('floNumber')) > 0){
			$flo_details = $flo_details->where('flo_details.flo_number', '=', $request->get('floNumber'));
		}

		$flo_details = $flo_details->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number');

		if(strlen($request->get('blFrom')) > 0){
			$blFrom = date('Y-m-d', strtotime($request->get('blFrom')));
			$flo_details = $flo_details->where('flos.bl_date', '>=', $blFrom);
		}
		if(strlen($request->get('blTo')) > 0){
			$blTo = date('Y-m-d', strtotime($request->get('blTo')));
			$flo_details = $flo_details->where('flos.bl_date', '<=', $blTo);
		}
		if(strlen($request->get('invoiceNumber')) > 0){
			$flo_details = $flo_details->where('flos.invoice_number', '=', $request->get('invoiceNumber'));
		}

		$flo_details = $flo_details->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number');

		if(strlen($request->get('originGroup')) > 0){
			$flo_details = $flo_details->whereIn('materials.origin_group_code', $request->get('originGroup'));
		}

		$flo_details = $flo_details->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id');

		if(strlen($request->get('destination')) > 0){
			$flo_details = $flo_details->whereIn('shipment_schedules.destination_code', $request->get('destination'));
		}
		if(strlen($request->get('shipFrom')) > 0){
			$shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
			$flo_details = $flo_details->where('shipment_schedules.st_date', '>=', $shipFrom);
		}
		if(strlen($request->get('shipTo')) > 0){
			$shipTo = date('Y-m-d', strtotime($request->get('shipTo')));
			$flo_details = $flo_details->where('shipment_schedules.st_date', '<=', $shipTo);
		}

		$flo_details = $flo_details->leftJoin('container_attachments', 'container_attachments.container_id', '=', 'flos.container_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('origin_groups', 'origin_groups.origin_group_code', '=', 'materials.origin_group_code')
		->select(
			db::raw('date_format(flo_details.created_at, "%d-%b-%Y") as pd_date'), 
			'flo_details.flo_number', 
			'origin_groups.origin_group_name', 
			'materials.material_number', 
			'materials.material_description', 
			'flo_details.serial_number', 
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'), 
			db::raw('if(date_format(flos.bl_date, "%d-%b-%Y") is null, "-", date_format(flos.bl_date, "%d-%b-%Y")) as bl_date'),
			'destinations.destination_shortname', 
			'shipment_schedules.sales_order', 
			'flos.container_id', 
			db::raw('count(container_attachments.container_id) as att'))
		->groupBy(
			db::raw('date_format(flo_details.created_at, "%d-%b-%Y")'), 
			'flo_details.flo_number', 
			'origin_groups.origin_group_name', 
			'materials.material_number', 
			'materials.material_description', 
			'flo_details.serial_number', 
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y")'),
			'flos.bl_date',
			'destinations.destination_shortname', 
			'shipment_schedules.sales_order', 
			'flos.container_id')
		->get();

		$response = array(
			'status' => true,
			'tableData' => $flo_details,
		);
		return Response::json($response);
	}

	public function fetch_fg_monthly_summary(Request $request){
		$shipment_schedules = DB::table('shipment_schedules');

		if(strlen($request->get('periodFrom')) > 0){
			$periodFrom = $request->get('periodFrom');
			$shipment_schedules = $shipment_schedules->where('st_month', '>=', $periodFrom);
		}
		if(strlen($request->get('periodTo')) > 0){
			$periodTo = $request->get('periodTo');
			$shipment_schedules = $shipment_schedules->where('st_month', '<=', $periodTo);
		}

		$shipment_schedules = $shipment_schedules->leftJoin(DB::raw('(select flos.shipment_schedule_id, sum(if(flos.bl_date > shipment_schedules.bl_date, flos.actual, 0)) as delay from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id group  by flos.shipment_schedule_id) as flos'), 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->select(db::raw('date_format(st_month, "%b-%Y") as period, format(sum(shipment_schedules.quantity),0) as total, sum(flos.delay) as bo, concat(round(((sum(shipment_schedules.quantity)-sum(flos.delay))/sum(shipment_schedules.quantity))*100,2),"%") as percentage'))
		->groupBy(db::raw('date_format(st_month, "%b-%Y")'))
		->orderBy(db::raw('date_format(st_month, "%b-%Y")'), 'desc');

		// $response = array(
		// 	'status' => true,
		// 	'tableData' => $shipment_schedules,
		// );
		// return Response::json($response);
		return DataTables::of($shipment_schedules)->make(true);
	}

	public function fetch_fg_weekly_summary(Request $request){
		$weekly_calendars = DB::table('weekly_calendars');

		if(strlen($request->get('weekFrom')) > 0){
			$weekFrom = substr($request->get('weekFrom'), 1);
			$weekly_calendars = $weekly_calendars->where(db::raw('mid(week_name,2)'), '>=', $weekFrom);
		}
		if(strlen($request->get('weekTo')) > 0){
			$weekTo = substr($request->get('weekTo'), 1);
			$weekly_calendars = $weekly_calendars->where(db::raw('mid(week_name,2)'), '<=', $weekTo);
		}
		if(strlen($request->get('year')) > 0){
			$year = $request->get('year');
			$weekly_calendars = $weekly_calendars->where(db::raw('year(week_date)'), '=', $year);
		}
		if(strlen($request->get('fiscalYear')) > 0){
			$fiscalYear = $request->get('fiscalYear');
			$weekly_calendars = $weekly_calendars->where('fiscal_year', '=', $fiscalYear);
		}
		if(strlen($request->get('weekFrom')) == 0 && strlen($request->get('weekTo')) == 0 && strlen($request->get('year'))  == 0 && strlen($request->get('fiscalYear')) == 0){
			$month = date('Y-m');
			$weekly_calendars->whereIn('week_name', db::table('weekly_calendars')->select('week_name')->distinct()->where(db::raw('DATE_FORMAT(weekly_calendars.week_date, "%Y-%m")'), '=', $month));
		}

		$weekly_calendars->leftJoin('shipment_schedules', 'shipment_schedules.bl_date', '=', 'weekly_calendars.week_date')
		->leftJoin(DB::raw('(select flos.shipment_schedule_id, sum(flos.actual) as actual, sum(if(flos.bl_date is null or flos.bl_date = "", 0, flos.actual)) as actual_shipment, sum(if(flos.bl_date > shipment_schedules.bl_date, flos.actual, 0)) as delay from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id group  by flos.shipment_schedule_id) as flos'), 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->select('weekly_calendars.fiscal_year', 
			db::raw('year(weekly_calendars.week_date) as year'), 
			'weekly_calendars.week_name', 
			db::raw('concat(date_format(min(weekly_calendars.week_date), "%d %b %Y"), " - ",date_format(max(weekly_calendars.week_date), "%d %b %Y")) as etd'), 
			db::raw('format(sum(shipment_schedules.quantity),0) as plan'), 
			db::raw('format(sum(flos.actual),0) as actual'), 
			db::raw('format(sum(flos.actual)-sum(shipment_schedules.quantity),0) as diff'), 
			db::raw('concat(round((sum(flos.actual)/sum(shipment_schedules.quantity))*100, 2),"%") as diff_percentage'), 
			db::raw('format(sum(flos.actual_shipment),0) as actual_shipment'), 
			db::raw('format(sum(flos.actual_shipment)-sum(shipment_schedules.quantity),0) as diff_shipment'), 
			db::raw('concat(round((sum(flos.actual_shipment)/sum(shipment_schedules.quantity))*100, 2),"%") as diff_shipment_percentage'),
			db::raw('format(sum(flos.delay),0) as delay'), 
			db::raw('concat(round(((sum(shipment_schedules.quantity)-sum(flos.delay))/sum(shipment_schedules.quantity))*100, 2),"%") as delay_percentage'))
		->groupBy('weekly_calendars.fiscal_year', 'weekly_calendars.week_name', db::raw('year(weekly_calendars.week_date)'))
		->get();

		// $response = array(
		// 	'status' => true,
		// 	'tableData' => $weekly_calendars->get(),
		// );
		// return Response::json($response); 
		return DataTables::of($weekly_calendars)->make(true);
	}

	public function fetch_fg_container_departure(Request $request){

		$container_schedules = DB::table('container_schedules');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$container_schedules = $container_schedules->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '>=', $date_from);
		}
		else{
			$month = date('Y-m');
			$container_schedules = $container_schedules->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m")'), '>=', $month);
		}
		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$container_schedules = $container_schedules->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '<=', $date_to);
		}

		$count1 = $container_schedules->select('container_schedules.shipment_date', DB::raw('"Open" as status'), DB::raw('count(container_id)-count(if(container_schedules.container_number is null or container_schedules.container_number = "", null, 1)) as quantity'))
		->groupBy('container_schedules.shipment_date')->get();

		$count2 = $container_schedules->select('container_schedules.shipment_date', DB::raw('"Departed" as status'), DB::raw('count(if(container_schedules.container_number is null or container_schedules.container_number = "", null, 1)) as quantity'))
		->groupBy('container_schedules.shipment_date')->get();

		$table1 = $count1->merge($count2);

		// $count3 = DB::table('flos')
		// ->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'flos.container_id')
		// ->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code');

		$container_schedules2 = DB::table('container_schedules');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '>=', $date_from);
		}
		else{
			$month = date('Y-m');
			$container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m")'), '>=', $month);
		}
		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '<=', $date_to);
		}

		$total_plan = $container_schedules2
		->count('container_id');

		$table2 = $container_schedules2
		->leftJoin('flos', 'container_schedules.container_id', '=', 'flos.container_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code')
		->whereNotNull('container_schedules.container_number')
		->select('destinations.destination_shortname', DB::raw('count(distinct container_schedules.container_id) as quantity'))
		->groupBy('destinations.destination_shortname')
		->get();

		$total_actual = $count2->sum('quantity');

		$response = array(
			'status' => true,
			'jsonData1' => $table1,
			'jsonData2' => $table2,
			'total_plan' => $total_plan,
			'total_actual' => $total_actual,
		);
		return Response::json($response); 
	}

	public function fetch_fg_stock(){
		$stock = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'shipment_schedules.material_number')
		->whereIn('flos.status', [0,1,2]);

		$total_volume = $stock->sum(DB::raw('((material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton)*flos.actual'));

		$total_stock = $stock->sum('flos.actual');

		// $jsonData = $stock->select('destinations.destination_shortname as destination', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK")) as location'), DB::raw('sum(flos.actual) as actual'))->groupBy('destinations.destination_shortname', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK"))'))->orderBy(DB::raw('field(location, "Production", "InTransit", "FSTK")'))->get();

		$jsonData = $stock->select('destinations.destination_shortname as destination', DB::raw('sum(if(flos.status = 0, flos.actual, 0)) as production'), DB::raw('sum(if(flos.status = 1, flos.actual, 0)) as intransit'), DB::raw('sum(if(flos.status = 2, flos.actual, 0)) as fstk'))
		->groupBy('destinations.destination_shortname')->get();

		$response = array(
			'status' => true,
			'jsonData' => $jsonData,
			'total_volume' => $total_volume,
			'total_stock' => $total_stock,
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

	public function fetch_tb_container_departure(Request $request){
		$container_schedules = DB::table('container_schedules')
		->leftJoin('container_attachments', 'container_attachments.container_id', '=', 'container_schedules.container_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code')
		->where('container_schedules.shipment_date', '=', $request->get('st_date'))
		->select('container_schedules.container_id', 'destinations.destination_shortname', 'container_schedules.container_number', 'container_schedules.shipment_date', DB::raw('count(container_attachments.container_id) as att'))
		->groupBy('container_schedules.container_id', 'destinations.destination_shortname', 'container_schedules.container_number', 'container_schedules.shipment_date')
		->get();

		$response = array(
			'status' => true,
			'table' => $container_schedules,
			'st_date' => $request->get('st_date'),
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
			$table = $shipment_schedules->select('shipment_schedules.id', 'shipment_schedules.sales_order','shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity', DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual)) as actual'), DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual))-shipment_schedules.quantity as diff'))->groupBy('shipment_schedules.id', 'shipment_schedules.sales_order', 'shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity')->orderBy('st_date', 'desc')->get();
		}
		elseif($request->get('id') == 'delivery'){
			$query = "select id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 2, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date desc";
			$table = DB::select($query, ['st_month' => $st_month]);			
		}
		elseif($request->get('id') == 'shipment'){
			$query = "select id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 3, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date desc";
			$table = DB::select($query, ['st_month' => $st_month]);
		}

		return DataTables::of($table)->make(true);
	}

	public function download_att_container_departure(Request $request){
		$container_attachments = DB::table('container_attachments')->get();

		$zip = new ZipArchive();
		$zip_name = $request->get('container_id').".zip";
		$zip_path = public_path() . '/' . $zip_name;
		$zip->open($zip_name, ZipArchive::CREATE);

		foreach ($container_attachments as $container_attachment) {
			$file_path = public_path() . $container_attachment->file_path . $container_attachment->file_name;
			$file_name = $container_attachment->file_name;
			$zip->addFile($file_path, $file_name);
		}
		$zip->close();

		// File::put($zip_path, $zip);
		$path = asset($zip_name);

		$response = array(
			'status' => true,
			'file_path' => $path,
		);
		return Response::json($response); 
	}
}
