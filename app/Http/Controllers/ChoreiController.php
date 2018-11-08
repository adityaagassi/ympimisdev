<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChoreiController extends Controller
{
	public function index_ch_daily_production_result(){
		return view('choreis.production_result')->with('page', 'Chorei Production Result')->with('head', 'Chorei');
	}

	public function fetch_production_accuracy_modal(Request $request){
		$query = "select materials.material_number, materials.material_description, final.plus, final.minus from
		(
		select result.material_number, if(sum(result.actual)-sum(result.plan)>0,sum(result.actual)-sum(result.plan),0) as plus, if(sum(result.actual)-sum(result.plan)<0,sum(result.actual)-sum(result.plan),0) as minus from
		(
		select material_number, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date >= '". $request->get('first') ."' and due_date <= '". $request->get('now') ."'
		group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as actual
		from flo_details
		where date(created_at) >= '". $request->get('first') ."' and date(created_at) <= '". $request->get('now') ."'
		group by material_number
		) as result
		group by result.material_number
		) as final
		left join materials on materials.material_number = final.material_number
		where materials.category = 'FG' and hpl = '". $request->get('hpl') ."'";

		$accuracyData = DB::select($query);

		$response = array(
			'status' => true,
			'accuracyData' => $accuracyData,
		);
		return Response::json($response);
	}

	public function fetch_production_result_modal(Request $request){
		if($request->get('name') == 'Actual'){
			$query = "select final.material_number, materials.material_description, if(final.actual>final.plan, final.plan, final.actual) as quantity from

			(
			select result.material_number, if(sum(result.debt)+sum(result.plan)<0,0,sum(result.debt)+sum(result.plan)) as plan, sum(result.actual) as actual from
			(
			select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
			from production_schedules 
			where due_date = '". $request->get('now') ."' 
			group by material_number

			union all

			select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
			from flo_details 
			where date(created_at) = '". $request->get('now') ."'  
			group by material_number

			union all

			select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
			(
			select material_number, sum(quantity) as debt from production_schedules where due_date >= '". $request->get('first') ."' and due_date <= '". $request->get('last') ."' group by material_number

			union all

			select material_number, -(sum(quantity)) as debt from flo_details where date(created_at) >= '". $request->get('first') ."' and date(created_at) <= '". $request->get('last') ."' group by material_number
			) as debt
			group by material_number

			) as result
			group by result.material_number
			) as final

			left join materials on materials.material_number = final.material_number
			where materials.hpl = '". $request->get('hpl') ."' and final.plan>0 and actual>0";
		}
		else{
			$query="select final.material_number, materials.material_description, if(final.actual>final.plan, 0, final.plan-final.actual) as quantity from

			(
			select result.material_number, if(sum(result.debt)+sum(result.plan)<0,0,sum(result.debt)+sum(result.plan)) as plan, sum(result.actual) as actual from
			(
			select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
			from production_schedules 
			where due_date = '". $request->get('now') ."' 
			group by material_number

			union all

			select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
			from flo_details 
			where date(created_at) = '". $request->get('now') ."'  
			group by material_number

			union all

			select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
			(
			select material_number, sum(quantity) as debt from production_schedules where due_date >= '". $request->get('first') ."' and due_date <= '". $request->get('last') ."' group by material_number

			union all

			select material_number, -(sum(quantity)) as debt from flo_details where date(created_at) >= '". $request->get('first') ."' and date(created_at) <= '". $request->get('last') ."' group by material_number
			) as debt
			group by material_number

			) as result
			group by result.material_number
			) as final

			left join materials on materials.material_number = final.material_number
			where materials.hpl = '". $request->get('hpl') ."' and if(final.actual>final.plan, 0, final.plan-final.actual) > 0";
		}
		

		$resultData = db::select($query);

		$response = array(
			'status' => true,
			'resultData' => $resultData,
		);
		return Response::json($response);
	}

	public function fetch_daily_production_result_week(){
		$first = date('Y-m-d', strtotime(new Carbon('first day of this month')));
		$last = date('Y-m-d', strtotime(new Carbon('last day of this month')));

		$weeks = DB::table('weekly_calendars')->select('week_name')
		->where('week_date', '>=', $first)
		->where('week_date', '<=', $last)
		->distinct()
		->select(db::raw('concat("Week ", mid(week_name,2)) as week'), 'week_name')
		->orderBy(db::raw('convert(mid(week_name,2), unsigned integer)'), 'asc')
		->get();

		$response = array(
			'status' => true,
			'weekData' => $weeks,
		);
		return Response::json($response);
	}

	public function fetch_daily_production_result_date(Request $request){

		$dates = DB::table('weekly_calendars');

		if(strlen($request->get('week')) > 0){
			$dates = $dates->where('week_name', '=', $request->get('week'));
		}
		else{
			$week = DB::table('weekly_calendars')->where('week_date', '=', date('Y-m-d'))->select('week_name')->first();
			$dates = $dates->where('week_name', '=', $week->week_name);
		}

		$dates = $dates->select('week_date', db::raw('date_format(week_date, "%d %M %Y") as week_date_name'))
		->orderBy('week_date', 'asc')
		->get();

		$response = array(
			'status' => true,
			'dateData' => $dates,
		);
		return Response::json($response);
	}

	public function fetch_daily_production_result(Request $request){
		if(strlen($request->get('date')) > 0){
			$date = date('Y-m-d', strtotime($request->get('date')));
			$now = date('Y-m-d', strtotime($date));
			$first = date('Y-m-d', strtotime(Carbon::parse('first day of '. date('F Y', strtotime($date)))));
			$week = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}
		else{
			$date = date('Y-m-d');
			$now = date('Y-m-d');
			$first = date('Y-m-01');
			$week = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}

		if($date == date('Y-m-01', strtotime($date))){
			$last = $date;
		}
		else{
			$last = date('Y-m-d', strtotime('yesterday', strtotime($date)));
		}

		$query = "select materials.hpl, materials.category, sum(final.plan)-sum(final.actual) as plan, sum(final.actual) AS actual from
		(
		select result.material_number, if(sum(result.debt)+sum(result.plan)<0,0,sum(result.debt)+sum(result.plan)) as plan, if(sum(result.actual)>if(sum(result.debt)+sum(result.plan)<0,0,sum(result.debt)+sum(result.plan)), if(sum(result.debt)+sum(result.plan)<0,0,sum(result.debt)+sum(result.plan)), sum(result.actual)) as actual from
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

		union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, sum(quantity) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, -(sum(quantity)) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number

		) as result
		group by result.material_number
		) as final
		
		left join materials on materials.material_number = final.material_number
		where category = 'FG'
		group by materials.hpl, materials.category
		order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'VENOVA', 'RC')";

		$chartResult1 = DB::select($query);

		$query2 = "select materials.hpl, sum(final.plus) as plus, sum(final.minus) as minus from
		(
		select result.material_number, if(sum(result.actual)-sum(result.plan)>0,sum(result.actual)-sum(result.plan),0) as plus, if(sum(result.actual)-sum(result.plan)<0,sum(result.actual)-sum(result.plan),0) as minus from
		(
		select material_number, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date >= '". $first ."' and due_date <= '". $now ."'
		group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as actual
		from flo_details
		where date(created_at) >= '". $first ."' and date(created_at) <= '". $now ."'
		group by material_number
		) as result
		group by result.material_number
		) as final
		left join materials on materials.material_number = final.material_number
		where materials.category = 'FG'
		group by materials.hpl
		order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'VENOVA', 'RC')";

		$chartResult2 = DB::select($query2);

		$chartResult3 = DB::table('shipment_schedules')
		->leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
		->leftJoin('weekly_calendars', 'weekly_calendars.week_date', '=', 'shipment_schedules.bl_date')
		->leftJoin(db::raw('(select flos.shipment_schedule_id, sum(flos.actual) as actual from flos group by flos.shipment_schedule_id) as flos'), 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
		->where('weekly_calendars.week_name', '=', $week->week_name)
		->where('materials.category', '=', 'FG')
		->select('materials.hpl', db::raw('if(sum(shipment_schedules.quantity)-sum(flos.actual)<0, 0, sum(shipment_schedules.quantity)-sum(flos.actual)) as plan'), db::raw('sum(flos.actual) as actual'))
		->groupBy('materials.hpl')
		->orderByRaw('field(materials.hpl, "FLFG", "CLFG", "ASFG", "TSFG", "PN", "VENOVA", "RC")')
		->get();

		$response = array(
			'status' => true,
			'chartResult1' => $chartResult1,
			'chartResult2' => $chartResult2,
			'chartResult3' => $chartResult3,
			'weekTitle' => 'Week ' . substr($week->week_name, 1),
			'dateTitle' => date('d F Y', strtotime($date)),
			'now' => $now,
			'first' => $first,
			'last' => $last,			
		);
		return Response::json($response);
	}
}