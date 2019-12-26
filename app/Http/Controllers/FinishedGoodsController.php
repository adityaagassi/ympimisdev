<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use ZipArchive;
use File;
use App\ShipmentSchedule;

class FinishedGoodsController extends Controller
{
	
	public function __construct()
	{
		$this->category = [
			'FG',
			'KD',
		];

		$this->middleware('auth');
	}

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

		return view('finished_goods.weekly_summary')->with('page', 'FG Weekly Summary')->with('head', 'Finished Goods');
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
		$origin_groups = DB::table('origin_groups')->get();
		$categories = $this->category;
		$hpls = DB::table('materials')->whereIn('materials.category', ['KD', 'FG'])
		->select('hpl')
		->distinct()
		->get();

		return view('finished_goods.shipment_schedule', array(
			'periods' => $periods,
			'origin_groups' => $origin_groups,
			'categories' => $categories,
			'hpls' => $hpls,
		))->with('page', 'FG Shipment Schedule')->with('head', 'Finished Goods');		
	}

	public function index_fg_shipment_result(){
		return view('finished_goods.shipment_result')->with('page', 'FG Shipment Result')->with('head', 'Finished Goods');
	}

	public function fetch_fg_shipment_result(Request $request){
		Carbon::setWeekStartsAt(Carbon::SUNDAY);
		Carbon::setWeekEndsAt(Carbon::SATURDAY);

		if($request->get('datefrom') != ""){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		else{
			$datefrom = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
		}

		if($request->get('dateto') != ""){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}
		else{
			$dateto = date('Y-m-d', strtotime(Carbon::now()->addDays(14)));
		}
		
		$query = "select date_format(e.st_date, '%d-%b-%Y') as st_date, e.hpl, act, plan, actual from
		(
		select distinct materials.hpl, c.st_date from materials
		left join
		(
		select shipment_schedules.st_date, materials.category from shipment_schedules left join materials on materials.material_number = shipment_schedules.material_number where shipment_schedules.st_date >= '" . $datefrom . "' and shipment_schedules.st_date <= '" . $dateto . "' group by shipment_schedules.st_date, materials.category
		) as c 
		on c.category = materials.category
		where materials.category = 'FG'
		) as e

		left join
		(
		select b.st_date, b.hpl, a.actual as act, b.plan as plan, round((coalesce(a.actual,0)/b.plan)*100,1) as actual from
		(
		select shipment_schedules.st_date, materials.hpl, sum(flos.actual) as actual from flos 
		left join shipment_schedules on flos.shipment_schedule_id = shipment_schedules.id
		left join materials on materials.material_number = shipment_schedules.material_number
		where materials.category = 'FG'
		group by shipment_schedules.st_date, materials.hpl
		) as a
		right join
		(
		select shipment_schedules.st_date, materials.hpl, sum(shipment_schedules.quantity) as plan from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		where materials.category = 'FG'
		group by  shipment_schedules.st_date, materials.hpl
		) as b on b.st_date = a.st_date and a.hpl = b.hpl
		where b.st_date >= '" . $datefrom . "' and b.st_date <= '" . $dateto . "'
		) as d
		on d.st_date = e.st_date and d.hpl = e.hpl
		order by e.st_date asc, e.hpl desc";

		$shipment_results = db::select($query);

		$response = array(
			'status' => true,
			'shipment_results' => $shipment_results,
		);
		return Response::json($response);
	}

	public function fetch_tb_shipment_result(Request $request){
		$st_date = date('Y-m-d', strtotime($request->get('date')));

		$query = "
		select a.material_number, a.material_description, a.destination_shortname, a.plan, coalesce(b.actual,0) as actual, coalesce(b.actual,0)-a.plan as diff from
		(
		select shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname, sum(shipment_schedules.quantity) as plan from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join destinations on destinations.destination_code = shipment_schedules.destination_code
		where materials.category = 'FG' and shipment_schedules.st_date = '" .$st_date . "' and materials.hpl = '" . $request->get('hpl') . "'
		group by shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname
		) as a
		left join
		(
		select shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code, sum(flos.actual) as actual from flos
		left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id
		group by shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code
		) as b 
		on a.st_date = b.st_date and a.material_number = b.material_number and a.destination_code = b.destination_code
		order by diff asc";

		$shipment_results = DB::select($query);

		$response = array(
			'status' => true,
			'shipment_results' => $shipment_results,
		);
		return Response::json($response);
	} 

	public function fetch_fg_shipment_schedule(Request $request){
		$periodFrom = $request->get('periodFrom');
		$periodTo = $request->get('periodTo');
		$st_month = date('Y-m-01');
		if(strlen($request->get('periodFrom')) > 0 && strlen($request->get('periodTo')) == 0){
			$where1 = " where shipment_schedules.st_month >= '".$periodFrom."'";
		}
		else if(strlen($request->get('periodFrom')) > 0 && strlen($request->get('periodTo')) > 0){
			$where1 = " where shipment_schedules.st_month >= '".$periodFrom."' and shipment_schedules.st_month <= '".$periodTo."'";
		}
		else{		
			$where1 = " where shipment_schedules.st_month = '".$st_month."'";
		}

		if(strlen($request->get('originGroupCode')) > 0){
			$where2 = " and materials.origin_group_code = '".$request->get('originGroupCode')."'";
		}
		else{
			$where2 = "";
		}

		if(strlen($request->get('hpl')) > 0){
			$where3 = " and materials.hpl = '".$request->get('hpl')."'";
		}
		else{
			$where3 = "";
		}

		if(strlen($request->get('category')) > 0){
			$where4 = " and materials.category = '".$request->get('category')."'";
		}
		else{
			$where4 = "";
		}

		$query = "select materials.category, shipment_schedules.id, date_format(shipment_schedules.st_month, '%b-%Y') as st_month, shipment_schedules.sales_order, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.material_number, materials.material_description, shipment_schedules.quantity, date_format(shipment_schedules.st_date, '%d-%b-%Y') as st_date, date_format(shipment_schedules.bl_date, '%d-%b-%Y') as bl_date_plan, sum(coalesce(stock.quantity, 0)) as quantity_production, sum(if(stock.status > 1, stock.quantity, 0)) as quantity_delivery FROM `shipment_schedules` left join
		(select shipment_schedule_id, sum(actual) as quantity, status from flos group by shipment_schedule_id, status
		union all
		select knock_down_details.shipment_schedule_id, sum(knock_down_details.quantity) as quantity, knock_downs.`status` from knock_down_details 
		left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number 
		group by shipment_schedule_id, knock_downs.status) as stock on shipment_schedules.id = stock.shipment_schedule_id
		left join destinations on destinations.destination_code = shipment_schedules.destination_code 
		left join shipment_conditions on shipment_conditions.shipment_condition_code = shipment_schedules.shipment_condition_code 
		left join materials on materials.material_number = shipment_schedules.material_number
		".$where1."
		".$where2."
		".$where3."
		".$where4."
		group by materials.category, shipment_schedules.id, date_format(shipment_schedules.st_month, '%b-%Y'), shipment_schedules.sales_order, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.material_number, materials.material_description, shipment_schedules.quantity, date_format(shipment_schedules.st_date, '%d-%b-%Y'), date_format(shipment_schedules.bl_date, '%d-%b-%Y')";

		$shipment_schedules = DB::select($query);

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
		if($request->get('materialNumber') != null){
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

		if($request->get('originGroup') != null){
			$flo_details = $flo_details->whereIn('materials.origin_group_code', $request->get('originGroup'));
		}

		$flo_details = $flo_details->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id');

		if($request->get('destination') != null){
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
			'flo_details.quantity',
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'), 
			db::raw('if(date_format(flos.bl_date, "%d-%b-%Y") is null, "-", date_format(flos.bl_date, "%d-%b-%Y")) as bl_date'),
			'destinations.destination_shortname', 
			'shipment_schedules.sales_order', 
			'flos.container_id', 
			db::raw('count(container_attachments.container_id) as att'),
			'flo_details.image')
		->groupBy(
			db::raw('date_format(flo_details.created_at, "%d-%b-%Y")'), 
			'flo_details.flo_number', 
			'origin_groups.origin_group_name', 
			'materials.material_number', 
			'materials.material_description', 
			'flo_details.quantity',
			'flo_details.serial_number', 
			db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y")'),
			'flos.bl_date',
			'destinations.destination_shortname', 
			'shipment_schedules.sales_order', 
			'flos.container_id',
			'flo_details.image')
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

		$shipment_schedules = $shipment_schedules->leftJoin(DB::raw('(select flos.shipment_schedule_id, sum(if(flos.bl_date > last_day(shipment_schedules.bl_date), flos.actual, 0)) as delay from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id group by flos.shipment_schedule_id) as flos'), 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->select(db::raw('date_format(st_month, "%b-%Y") as period, sum(shipment_schedules.quantity) as total, sum(flos.delay) as bo, truncate(((sum(shipment_schedules.quantity)-sum(flos.delay))/sum(shipment_schedules.quantity))*100,2) as percentage'))
		->groupBy(db::raw('date_format(st_month, "%b-%Y")'))
		->orderBy('st_month', 'desc')
		->get();

		$response = array(
			'status' => true,
			'tableData' => $shipment_schedules,
		);
		return Response::json($response);
	}

	public function fetch_tb_monthly_summary(Request $request){
		$period = date('Y-m', strtotime($request->get('period'))).'-01';

		$query = "select shipment_schedules.sales_order, shipment_schedules.st_date, shipment_schedules.bl_date as bl_plan, flos.bl_date as bl_actual, materials.material_number, materials.material_description, sum(if(flos.bl_date > last_day(shipment_schedules.bl_date), flos.actual, 0)) as actual from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id left join materials on materials.material_number = flos.material_number where shipment_schedules.st_month = '".$period."' group by materials.material_number, materials.material_description, shipment_schedules.sales_order, shipment_schedules.st_date, shipment_schedules.bl_date, flos.bl_date having actual > 0";

		$flos = db::select($query);

		$response = array(
			'status' => true,
			'resultData' => $flos,
		);
		return Response::json($response);
	}

	public function fetch_fg_weekly_summary(Request $request){

		$last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
		$first = date('Y-m-01');

		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));

		if($request->get('datefrom') != "" && $request->get('dateto') != ""){
			$year = "having week_start >= '" . $date_from . "' and week_start <= '" . $date_to . "'";
		}
		else{
			$year = "having year = '" . date('Y') . "' and week_start >= '" . $first . "' and week_start <= '" . $last . "'";
		}

		$query = "select year, week_name, week_start, week_end, sum(plan) as plan, sum(actual_production) as actual_production, sum(diff_actual) as diff_actual, concat(truncate((sum(actual_production)/sum(plan))*100,2), '%') as prctg_actual, sum(actual_shipment) as actual_shipment, sum(diff_shipment) as diff_shipment, concat(truncate((sum(actual_shipment)/sum(plan))*100,2), '%') as prctg_shipment, sum(delay) as delay, concat(round(((sum(plan)-sum(delay))/sum(plan))*100,2), '%') as prctg_delay from
		(
		select year, week_name, week_start, bl_target as week_end, id, material_number, plan, sum(actual_production)as actual_production, sum(actual_production)-plan as diff_actual, sum(actual_shipment) as actual_shipment, sum(actual_shipment)-plan as diff_shipment, sum(delay) as delay from
		(
		select a.year, a.week_name, a.week_start, b.bl_actual, a.bl_target, b.id, b.material_number, b.quantity as plan, sum(b.actual) as actual_production, if(b.bl_actual is null, 0, sum(b.actual)) as actual_shipment, if(b.bl_actual > a.bl_target, sum(b.actual), 0) as delay from
		(
		(select year(week_date) as year, week_name, min(week_date) as week_start, max(week_date) as bl_target from weekly_calendars group by year(week_date), week_name) as a

		left join

		(select year(shipment_schedules.bl_date) as year, concat('W', date_format(shipment_schedules.bl_date, '%U')+1) as week_name, shipment_schedules.id, shipment_schedules.material_number, shipment_schedules.quantity, flos.actual, shipment_schedules.bl_date as bl_plan, flos.bl_date as bl_actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id) as b on b.year = a.year and b.week_name = a.week_name
		)
		group by year, week_name, week_start, bl_target, id, bl_actual, material_number, quantity
		) as c
		group by year, week_name, week_start, bl_target, material_number, id, plan
		) as d
		group by year, week_name, week_start, week_end
		" . $year . "
		order by week_start asc";

		$weekly_calendars = DB::select($query);
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
		->groupBy('container_schedules.shipment_date')->orderBy('container_schedules.shipment_date')->get();

		$count2 = $container_schedules->select('container_schedules.shipment_date', DB::raw('"Departed" as status'), DB::raw('count(if(container_schedules.container_number is null or container_schedules.container_number = "", null, 1)) as quantity'))
		->groupBy('container_schedules.shipment_date')->orderBy('container_schedules.shipment_date')->get();

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
		->orderBy(db::raw('quantity'), 'desc')
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
		$stock2 = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'shipment_schedules.material_number')
		->whereIn('flos.status', [0,1,2,'M'])
		->where('flos.actual', '>', 0);

		$total_volume = $stock2->sum(DB::raw('((material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton)*flos.actual'));

		$total_stock = $stock2->sum('flos.actual');

		// $jsonData = $stock->select('destinations.destination_shortname as destination', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK")) as location'), DB::raw('sum(flos.actual) as actual'))->groupBy('destinations.destination_shortname', DB::raw('if(flos.status = 0, "Production", if(flos.status = 1, "InTransit", "FSTK"))'))->orderBy(DB::raw('field(location, "Production", "InTransit", "FSTK")'))->get();

		$jsonData = $stock2->select(db::raw('if(destinations.destination_shortname is null, "Maedaoshi", destinations.destination_shortname) as destination'), DB::raw('sum(if(flos.status = "0" or flos.status = "M", flos.actual, 0)) as production'), DB::raw('sum(if(flos.status = "1", flos.actual, 0)) as intransit'), DB::raw('sum(if(flos.status = "2", flos.actual, 0)) as fstk'), DB::raw('sum(flos.actual) as actual'))
		->groupBy('destinations.destination_shortname')->orderBy(db::raw('actual'), 'desc')->get();

		$stock = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('materials', 'materials.material_number', '=', 'flos.material_number')
		->whereIn('flos.status', ['0', '1', '2', 'M'])
		->where('flos.actual', '>', 0)
		->select('materials.material_number', 'materials.material_description', db::raw('if(destinations.destination_shortname is null, "Maedaoshi", destinations.destination_shortname) as destination'), db::raw('if(flos.status = "M" or flos.status = "0", "Production", if(flos.status = "1", "Intransit", "FSTK")) as location'), db::raw('sum(flos.actual) as quantity'))
		->groupBy('materials.material_number', 'materials.material_description', 'destinations.destination_shortname', 'flos.status')
		->orderBy('materials.material_number', 'destinations.destination_shortname')
		->get();

		$response = array(
			'status' => true,
			'jsonData' => $jsonData,
			'total_volume' => $total_volume,
			'total_stock' => $total_stock,
			'stockData' => $stock,
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
		$total_production = $shipment_schedule->where('flos.status', '<>', 'M')->sum('flos.actual');
		$total_delivery = $shipment_schedule->whereIn('flos.status', ['2','3','4'])->sum('flos.actual');
		$total_shipment = $shipment_schedule->whereIn('flos.status', ['3','4'])->sum('flos.actual');

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
		if($request->get('destination') == 'Maedaoshi'){
			$destination = null;
		}
		else{
			$destination = $request->get('destination');
		}
		$stock = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', 'flos.material_number')
		->leftJoin('materials', 'materials.material_number', 'flos.material_number')
		->whereIn('flos.status', $request->get('status'))
		->where('destinations.destination_shortname', '=', $destination)
		->select('flos.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width', 'material_volumes.lot_carton', DB::raw('sum(flos.actual) as actual'))
		->groupBy('flos.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width', 'material_volumes.lot_carton')
		->get();

		if(in_array('0', $request->get('status')) || in_array('M', $request->get('status'))){
			$location = 'Production';
		}
		elseif(in_array('1', $request->get('status'))){
			$location = 'InTransit';
		}
		elseif(in_array('2', $request->get('status'))){
			$location = 'FSTK';
		}
		// else{
		// 	$location = 'Production';
		// }

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
			$table = $shipment_schedules->select('shipment_schedules.id', 'shipment_schedules.sales_order', db::raw('date_format(shipment_schedules.st_month, "%b-%Y") as st_month'), 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity', DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual)) as actual'), DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual))-shipment_schedules.quantity as diff'))->groupBy('shipment_schedules.id', 'shipment_schedules.sales_order', 'shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity')->orderBy('st_date', 'asc')->get();
		}
		elseif($request->get('id') == 'delivery'){
			$query = "select id, sales_order, date_format(st_month, '%b-%Y') as st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 2, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date asc";
			$table = DB::select($query, ['st_month' => $st_month]);		
		}
		elseif($request->get('id') == 'shipment'){
			$query = "select id, sales_order, date_format(st_month, '%b-%Y') as st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 3, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date asc";
			$table = DB::select($query, ['st_month' => $st_month]);
		}

		return DataTables::of($table)->make(true);
	}

	public function download_att_container_departure(Request $request){
		$container_attachments = DB::table('container_attachments')->where('container_id', '=', $request->get('container_id'))->get();

		$zip = new ZipArchive();
		$zip_name = $request->get('container_id').".zip";
		$zip_path = public_path() . '/' . $zip_name;
		File::delete($zip_path);
		$zip->open($zip_name, ZipArchive::CREATE);

		foreach ($container_attachments as $container_attachment) {
			$file_path = public_path() . $container_attachment->file_path . $container_attachment->file_name;
			$file_name = $container_attachment->file_name;
			$zip->addFile($file_path, $file_name);
		}
		$zip->close();

		$path = asset($zip_name);

		$response = array(
			'status' => true,
			'file_path' => $path,
		);
		return Response::json($response); 
	}
}
