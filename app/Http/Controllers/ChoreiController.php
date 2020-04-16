<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class ChoreiController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function indexProductionAchievement(){
		$title = 'Production Achievement';
		$title_jp = '';

		return view('choreis.production_achievement', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Production Achievement');
	}
	
	public function index_ch_daily_production_result(){
		$activity =  new UserActivityLog([
			'activity' => 'Chorei (朝礼)',
			'created_by' => Auth::id(),
		]);
		$activity->save();
		return view('choreis.production_result')->with('page', 'Chorei Production Result')->with('head', 'Chorei');
	}

	public function fetch_production_bl_modal(Request $request){
		$year = date('Y', strtotime($request->get('date')));
		$last_date = DB::table('weekly_calendars')
		->where('week_name', '=', $request->get('week'))
		->where(db::raw('year(weekly_calendars.week_date)'), '=', $year)
		->select(db::raw('min(week_date) as week_date'))
		->first();

		$query1 = "select material_number, material_description, sum(quantity) as quantity from
		(
		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) as quantity 
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_name = '".$request->get('week')."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "'
		group by shipment_schedules.material_number, materials.material_description
		having if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) > 0

		union all

		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) as quantity 
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_date < '".$last_date->week_date."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "' and flos.actual < shipment_schedules.quantity
		group by shipment_schedules.material_number, materials.material_description
		) as result1
		group by material_number, material_description";

		$query2 = "select material_number, material_description, sum(quantity) as quantity from
		(
		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) as quantity
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_name = '".$request->get('week')."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "'
		group by shipment_schedules.material_number, materials.material_description
		having if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) > 0

		union all

		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) as quantity
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_date < '".$last_date->week_date."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "'
		group by shipment_schedules.material_number, materials.material_description
		having if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) > 0 and sum(flos.actual) < sum(shipment_schedules.quantity)
		) as result1
		group by material_number, material_description";

		if($request->get('name') == 'Actual'){
			$blData = db::select($query1);
		}
		if($request->get('name') == 'Plan'){
			$blData = db::select($query2);
		}

		$response = array(
			'status' => true,
			'blData' => $blData,
			'tes' => $last_date,
		);
		return Response::json($response);
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

		$year = date('Y');

		$dates = DB::table('weekly_calendars')->where(db::raw('year(week_date)'), '=', $year);

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
			$year = date('Y', strtotime($request->get('date')));
			$date = date('Y-m-d', strtotime($request->get('date')));
			$week_date = date('Y-m-d', strtotime($date. '+ 2 day'));
			$now = date('Y-m-d', strtotime($date));
			$first = date('Y-m-d', strtotime(Carbon::parse('first day of '. date('F Y', strtotime($date)))));
			$week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
			$week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}
		else{
			$year = date('Y');
			$date = date('Y-m-d');
			$now = date('Y-m-d');
			$week_date = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
			$first = date('Y-m-01');
			$week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
			$week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}

		if($date == date('Y-m-01', strtotime($date))){
			$last = $date;
			$debt = "";
		}
		else{
			$last = date('Y-m-d', strtotime('yesterday', strtotime($date)));
			$debt = "		union all

			select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
			(
			select material_number, sum(quantity) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

			union all

			select material_number, -(sum(quantity)) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
			) as debt
			group by material_number";
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

		".$debt."

		) as result
		group by result.material_number
		) as final
		
		left join materials on materials.material_number = final.material_number
		where category = 'FG'
		group by materials.hpl, materials.category
		order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'RC', 'VENOVA')";

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
		order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'RC', 'VENOVA')";

		$chartResult2 = DB::select($query2);

		$query3 = "select hpl, sum(plan)-sum(actual) as plan, sum(actual) as actual, avg(prc1) as prc_actual, 1-avg(prc1) as prc_plan from
		(
		select material_number, hpl, category, plan, coalesce(actual, 0) as actual, coalesce(actual, 0)/plan as prc1 from
		(
		select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, if(flos.actual>shipment_schedules.quantity, shipment_schedules.quantity, flos.actual) as actual from shipment_schedules 
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		left join materials on materials.material_number = shipment_schedules.material_number
		where weekly_calendars.week_name = '".$week->week_name."' and year(weekly_calendars.week_date) = '" . $year . "' and materials.category = 'FG'

		union all

		select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, flos.actual as actual from shipment_schedules 
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.st_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		left join materials on materials.material_number = shipment_schedules.material_number
		where weekly_calendars.week_name <> '".$week->week_name."' and year(weekly_calendars.week_date) = '" . $year . "' and materials.category = 'FG' and weekly_calendars.week_date < '".$week_date."' and flos.actual < shipment_schedules.quantity
		) as result1
		) result2
		group by hpl
		order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'RC', 'VENOVA')";

		$chartResult3 = DB::select($query3);

		$response = array(
			'status' => true,
			'chartResult1' => $chartResult1,
			'chartResult2' => $chartResult2,
			'chartResult3' => $chartResult3,
			'week' => 'Week ' . substr($week2->week_name, 1),
			'weekTitle' => 'Week ' . substr($week->week_name, 1),
			'dateTitle' => date('d F Y', strtotime($date)),
			'now' => $now,
			'first' => $first,
			'last' => $last,
		);
		return Response::json($response);
	}

	public function fetchProductionAchievement(Request $request){

		$date = db::select("select week_date, remark from weekly_calendars
			where week_date <= '2020-04-16'
			and remark <> 'H'
			order by week_date desc
			limit 5");

		$datefrom = date('Y-m-d', strtotime("-5 day", strtotime(date("Y-m-d"))));
		$dateto = date('Y-m-d');
		$origin_group = '043';

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}
		if(strlen($request->get('origin_group'))>0){
			$origin_group = $request->get('origin_group');
		}

		$data = db::select("SELECT
			due_date,
			origin_group_code,
			sum( target ) as target,
			sum( surface_treatment ) as surface_treatment,
			sum( welding ) as welding 
			FROM
			(
			SELECT
			assy_picking_schedules.due_date,
			materials.origin_group_code,
			CEIL(
			IF
			(
			materials.origin_group_code = '043',
			sum( assy_picking_schedules.quantity ) / 34,
			IF
			( materials.origin_group_code = '042', sum( assy_picking_schedules.quantity ) / 21, sum( assy_picking_schedules.quantity ) / 20 ) 
			) 
			) AS target,
			0 AS surface_treatment,
			0 AS welding 
			FROM
			assy_picking_schedules
			LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number 
			WHERE
			assy_picking_schedules.due_date >= '".$datefrom."' 
			AND assy_picking_schedules.due_date <= '".$dateto."' 
			AND materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND materials.origin_group_code = '".$origin_group."'
			GROUP BY
			assy_picking_schedules.due_date,
			materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS surface_treatment,
			0 AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$origin_group."'
			AND kitto.histories.completion_location IN ( 'SX51', 'CL51', 'FL51' ) 
			GROUP BY
			date( kitto.histories.created_at ),
			ympimis.materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			0 AS surface_treatment,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$origin_group."'
			AND kitto.histories.completion_location IN ( 'SX21', 'CL21', 'FL21' ) 
			GROUP BY
			date( kitto.histories.created_at ) ,
			ympimis.materials.origin_group_code
			) AS wst 
			GROUP BY
			due_date,
			origin_group_code");


		$data2 = db::select("SELECT
			target.due_date,
			target.origin_group_code,
			target.target,
			result.result 
			FROM
			(
			SELECT
			production_schedules.due_date,
			materials.origin_group_code,
			sum( production_schedules.quantity ) AS target 
			FROM
			production_schedules
			LEFT JOIN materials ON production_schedules.material_number = materials.material_number 
			WHERE
			production_schedules.due_date >= '".$datefrom."' 
			AND production_schedules.due_date <= '".$dateto."' 
			AND materials.origin_group_code = '".$origin_group."'
			GROUP BY
			production_schedules.due_date,
			materials.origin_group_code 
			) AS target
			LEFT JOIN (
			SELECT
			date( flo_details.created_at ) AS date,
			flo_details.origin_group_code,
			sum( quantity ) AS result 
			FROM
			flo_details 
			WHERE
			date( flo_details.created_at ) >= '".$datefrom."' 
			AND date( flo_details.created_at ) <= '".$dateto."' 
			AND flo_details.origin_group_code = '".$origin_group."'
			GROUP BY
			date,
			flo_details.origin_group_code 
		) AS result ON result.date = target.due_date");

		$response = array(
			'status' => true,
			'data' => $data,
			'data2' => $data2,
			'datefrom' => $datefrom,
			'dateto' => $dateto,
			'origin_group' => $origin_group,
		);
		return Response::json($response);		
	}
}