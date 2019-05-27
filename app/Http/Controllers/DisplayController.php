<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\OriginGroup;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisplayController extends Controller
{
	public function index_dp_production_result(){
		$origin_groups = OriginGroup::orderBy('origin_group_name', 'asc')->get();
		return view('displays.production_result', array(
			'origin_groups' => $origin_groups,
		))->with('page', 'Display Production Result')->with('head', 'Display');
	}

	public function index_dp_stockroom_stock(){
		return view('displays.stockroom_stock')->with('page', 'Display Stockroom Stock')->with('head', 'Display');
	}

	public function index_dp_fg_accuracy(){
		return view('displays.fg_accuracy')->with('page', 'Display FG Accuracy')->with('head', 'Display');		
	}

	public function fetch_dp_fg_accuracy_detail(Request $request){
		$first = date('Y-m-d', strtotime(Carbon::parse('first day of '. date('F Y', strtotime($request->get('date'))))));

		$query = "select materials.material_number, materials.material_description, final.plus+final.minus as qty from
		(
		select result.material_number, if(sum(result.actual)-sum(result.plan)>0,sum(result.actual)-sum(result.plan),0) as plus, if(sum(result.actual)-sum(result.plan)<0,sum(result.actual)-sum(result.plan),0) as minus from
		(
		select material_number, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date >= '". $first ."' and due_date <= '". $request->get('date') ."'
		group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as actual
		from flo_details
		where date(created_at) >= '". $first ."' and date(created_at) <= '". $request->get('date') ."'
		group by material_number
		) as result
		group by result.material_number
		) as final
		left join materials on materials.material_number = final.material_number
		where materials.category = 'FG' and materials.hpl in ('" . $request->get('category') . "') and final.plus+final.minus <> 0 order by qty desc";

		$accuracyDetail = db::select($query);

		$response = array(
			'status' => true,
			'accuracyDetail' => $accuracyDetail,
			'title' => 'Details of '. $request->get('category'),
		);
		return Response::json($response);
	}

	public function fetch_dp_fg_accuracy(){
		$now = date('Y-m-d');
		// $queryAccuracyBI = "select g.week_name, g.week_date, sum(g.minus) as minus, sum(g.plus) as plus from
		// (
		// select f.week_name, f.week_date, f.material_number, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
		// (
		// select e.week_name, e.week_date, e.material_number, e.material_mon, e.plan, e.actual, 
		// (@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc, 
		// (@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from 
		// (
		// select c.week_name, c.week_date, b.material_number, concat(date_format(c.week_date, '%Y%m'), b.material_number) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
		// (select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now  . "') as c 
		// cross join
		// (
		// select materials.material_number from materials where materials.category = 'FG' and materials.hpl in ('CLFG', 'ASFG', 'TSFG', 'FLFG')
		// ) as b
		// left join
		// production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
		// left join
		// (select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now  . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
		// order by b.material_number asc, c.week_date asc limit 999999999999999
		// ) as e
		// cross join
		// (select @material := -1, @plan := 0) as params
		// cross join
		// (select @material2 := -1, @actual := 0) as params2
		// ) as f
		// ) as g
		// group by g.week_name, g.week_date order by g.week_date asc";

		// $queryAccuracyEI = "select g.week_name, g.week_date, sum(g.minus) as minus, sum(g.plus) as plus from
		// (
		// select f.week_name, f.week_date, f.material_number, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
		// (
		// select e.week_name, e.week_date, e.material_number, e.material_mon, e.plan, e.actual, 
		// (@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc, 
		// (@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from 
		// (
		// select c.week_name, c.week_date, b.material_number, concat(date_format(c.week_date, '%Y%m'), b.material_number) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
		// (select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now  . "') as c 
		// cross join
		// (
		// select materials.material_number from materials where materials.category = 'FG' and materials.hpl in ('RC', 'PN', 'VENOVA')
		// ) as b
		// left join
		// production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
		// left join
		// (select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now  . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
		// order by b.material_number asc, c.week_date asc limit 999999999999999
		// ) as e
		// cross join
		// (select @material := -1, @plan := 0) as params
		// cross join
		// (select @material2 := -1, @actual := 0) as params2
		// ) as f
		// ) as g
		// group by g.week_name, g.week_date order by g.week_date asc";

		$queryAccuracy = "select g.week_name, g.week_date, g.hpl, sum(g.minus) as minus, sum(g.plus) as plus from
		(
		select f.week_name, f.week_date, f.material_number, f.hpl, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
		(
		select e.week_name, e.week_date, e.material_number, e.hpl, e.material_mon, e.plan, e.actual, 
		(@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc, 
		(@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from 
		(
		select c.week_name, c.week_date, b.material_number, b.hpl, concat(date_format(c.week_date, '%Y%m'), b.material_number, b.hpl) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
		(select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now  . "') as c 
		cross join
		(
		select materials.material_number, materials.hpl from materials where materials.category = 'FG'
		) as b
		left join
		production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
		left join
		(select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now  . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
		order by b.material_number asc, c.week_date asc limit 99999999999
		) as e
		cross join
		(select @material := -1, @plan := 0) as params
		cross join
		(select @material2 := -1, @actual := 0) as params2
		) as f
		) as g
		group by g.week_name, g.week_date, g.hpl order by g.week_date asc";

		$accuracy = db::select($queryAccuracy);
		// $accuracyBI = db::select($queryAccuracyBI);
		// $accuracyEI = db::select($queryAccuracyEI);

		$response = array(
			'status' => true,
			'accuracy' => $accuracy,
			// 'accuracyBI' => $accuracyBI,
			// 'accuracyEI' => $accuracyEI,
		);
		return Response::json($response);
	}

	public function fetch_dp_stockroom_stock(Request $request){
		// $stocks = db::table('kitto.inventories')
		// ->select('kitto.inventories.material_number', db::raw('sum(kitto.inventories.lot) as stock'))
		// ->groupBy('kitto.inventories.material_number')
		// ->get();
		
		$stock_plt_alto = db::table('ympimis.materials')
		->leftjoin('kitto.inventories', 'kitto.inventories.material_number', '=', 'ympimis.materials.material_number')
		->where('ympimis.materials.work_center', '=', 'WS51')
		->where('ympimis.materials.category', '=', 'WIP')
		->where('ympimis.materials.model', 'like', '%PLT%')
		->where('ympimis.materials.material_description', 'like', 'A%')
		->select('ympimis.materials.model', db::raw('sum(kitto.inventories.lot) as stock'))
		->groupBy('ympimis.materials.model')
		->orderBy('ympimis.materials.model', 'asc')
		->get();

		$response = array(
			'status' => true,
			'stock_plt_alto' => $stock_plt_alto,
		);
		return Response::json($response);
	}

	public function fetch_dp_production_result(Request $request){
		if($request->get('hpl') == 'all'){
			$hpl = "where materials.category = 'FG'";
		}
		else{
			$hpl = "where materials.category = 'FG' and materials.origin_group_code = '". $request->get('hpl') ."'";
		}

		$first = date('Y-m-01');
		if(date('Y-m-d') != date('Y-m-01')){
			$last = date('Y-m-d', strtotime(Carbon::yesterday()));
		}
		else{
			$last = date('Y-m-d');
		}
		$now = date('Y-m-d');

		if($first != $now){
			$debt = "union all

			select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
			(
			select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

			union all

			select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
			) as debt
			group by material_number";
		}
		else{
			$debt= "";
		}
		

		$query = "select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
		(
		select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date = '". $now ."' 
		group by material_number

		union all

		select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
		from flo_details 
		where date(created_at) = '". $now ."'  
		group by material_number

		".$debt."

		) as result
		left join materials on materials.material_number = result.material_number
		". $hpl ."
		group by result.material_number, materials.material_description
		having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0";

		$tableData = DB::select($query);

		// $query2 = "select result.material_number, materials.material_description as model, sum(result.plan) as plan, sum(result.actual) as actual from
		// (
		// select material_number, sum(quantity) as plan, 0 as actual 
		// from production_schedules 
		// where due_date >= '". $first ."' and due_date <= '". $now ."' 
		// group by material_number

		// union all

		// select material_number, 0 as plan, sum(quantity) as actual
		// from flo_details
		// where date(created_at) >= '". $first ."' and date(created_at) <= '". $now ."'
		// group by material_number
		// ) as result
		// left join materials on materials.material_number = result.material_number
		// ". $hpl ."
		// group by result.material_number, materials.material_description
		// having sum(result.plan) <> 0 or sum(result.actual) <> 0";

		// $chartData = DB::select($query2);

		// $totalPlan = DB::select();

		$response = array(
			'status' => true,
			'tableData' => $tableData,
			// 'chartData' => $chartData,
			// 'totalPlan' => $totalPlan,
		);
		return Response::json($response);
	}
}
