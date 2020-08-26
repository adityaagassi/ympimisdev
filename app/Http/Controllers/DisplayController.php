<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\OriginGroup;
use App\KnockDownDetail;
use App\UserActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Auth;


class DisplayController extends Controller
{
	public function index_dp_production_result(){
		$activity =  new UserActivityLog([
			'activity' => 'FG Daily Production Result (日常生産実績)',
			'created_by' => Auth::id(),
		]);
		$activity->save();

		$origin_groups = OriginGroup::orderBy('origin_group_name', 'asc')->get();
		return view('displays.production_result', array(
			'origin_groups' => $origin_groups,
		))->with('page', 'Display Production Result')->with('head', 'Display');
	}

	public function indexAllStock(){
		return view('displays.shippings.all_stock')->with('page', 'All Stock')->with('head', 'All Stock');		
	}

	public function indexEffScrap(){
		$title = 'Scrap Monitoring';
		$title_jp = 'スクラップの監視';

		return view('displays.eff_scrap', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display Weekly Shipment')->with('head', 'Display');
	}

	public function fetchEffScrap(Request $request){

		$first = date('Y-m-01');
		$last = date('Y-m-t');

		if(strlen($request->get('period')) > 0){
			$first = date('Y-m-01', strtotime($request->get('period')));
			$last = date('Y-m-t', strtotime($request->get('period')));
		}

		$targets = db::select("SELECT
			* 
			FROM
			scrap_targets 
			WHERE
			due_date >= '".$first."' 
			AND due_date <= '".$last."'");

		$actuals = db::select("SELECT
			w.week_date AS posting_date,
			scrap.movement_type,
			scrap.material_number,
			scrap.material_description,
			scrap.quantity,
			scrap.std_price,
			COALESCE ( scrap.amount, 0 ) AS amount,
			scrap.storage_location,
			IF
			( scrap.receive_location IS NULL OR scrap.receive_location = '', 'no_scrap', scrap.receive_location ) AS receive_location,
			SPLIT_STRING ( scrap.reference, '/', 2 ) AS reason,
			scrap_reasons.reason_name 
			FROM
			( SELECT week_date FROM weekly_calendars WHERE week_date >= '".$first."' AND week_date <= '".$last."' ) AS w
			LEFT JOIN (
			SELECT
			s.posting_date,
			s.movement_type,
			s.material_number,
			m.material_description,
			s.quantity,
			m.standard_price / 1000 AS std_price,
			IF
			(
			s.movement_type = '9S2' 
			OR s.movement_type = '102',
			- 1 * s.quantity *(
			m.standard_price / 1000 
			),
			s.quantity *(
			m.standard_price / 1000 
			)) AS amount,
			IF
			( s.receive_location = '' OR s.receive_location IS NULL, m.storage_location, s.storage_location ) AS storage_location,
			IF
			( s.receive_location = '' OR s.receive_location IS NULL, s.storage_location, s.receive_location ) AS receive_location,
			s.reference 
			FROM
			sap_transactions AS s
			LEFT JOIN material_plant_data_lists AS m ON m.material_number = s.material_number 
			WHERE
			(
			s.receive_location IN ( 'MSCR', 'WSCR' ) 
			OR s.storage_location IN ( 'MSCR', 'WSCR' )) 
			AND s.posting_date >= '".$first."' 
			AND s.posting_date <= '".$last."' 
			AND s.reference NOT LIKE '%TRI%' 
			AND s.reference NOT LIKE '%WAST%' 
			) AS scrap ON scrap.posting_date = w.week_date 
			LEFT JOIN scrap_reasons ON scrap_reasons.reason = SPLIT_STRING ( scrap.reference, '/', 2 ) 
			ORDER BY
			posting_date ASC");

		$categories = db::select("SELECT
			sum( amount ) AS total_amount,
			receive_location,
			reason 
			FROM
			(
			SELECT
			IF
			(
			s.movement_type = '9S2' 
			OR s.movement_type = '102',
			- 1 * s.quantity *(
			m.standard_price / 1000 
			),
			s.quantity *(
			m.standard_price / 1000 
			)) AS amount,
			IF
			( s.receive_location = '' OR s.receive_location IS NULL, s.storage_location, s.receive_location ) AS receive_location,
			SPLIT_STRING ( s.reference, '/', 2 ) AS reason 
			FROM
			sap_transactions s
			LEFT JOIN material_plant_data_lists m ON m.material_number = s.material_number 
			WHERE
			(
			s.receive_location IN ( 'MSCR', 'WSCR' ) 
			OR s.storage_location IN ( 'MSCR', 'WSCR' )) 
			AND s.posting_date >= '".$first."' 
			AND s.posting_date <= '".$last."' 
			AND s.reference NOT LIKE '%TRI%' 
			AND s.reference NOT LIKE '%WAST%' 
			) AS category 
			GROUP BY
			receive_location,
			reason 
			ORDER BY
			receive_location DESC,
			total_amount DESC");

		$actual_mscr = array();
		$actual_wscr = array();

		foreach ($actuals as $actual) {
			if($actual->receive_location == 'MSCR' || $actual->receive_location == 'no_scrap'){
				array_push($actual_mscr, [
					'posting_date' => $actual->posting_date,
					'movement_type' => $actual->movement_type,
					'material_number' => $actual->material_number,
					'material_description' => $actual->material_description,
					'quantity' => $actual->quantity,
					'std_price' => $actual->std_price,
					'amount' => $actual->amount,
					'storage_location' => $actual->storage_location,
					'reason' => $actual->reason,
					'reason_name' => $actual->reason_name,
					'receive_location' => 'MSCR'
				]);
			}
			if($actual->receive_location == 'WSCR' || $actual->receive_location == 'no_scrap'){
				array_push($actual_wscr, [
					'posting_date' => $actual->posting_date,
					'movement_type' => $actual->movement_type,
					'material_number' => $actual->material_number,
					'material_description' => $actual->material_description,
					'quantity' => $actual->quantity,
					'std_price' => $actual->std_price,
					'amount' => $actual->amount,
					'storage_location' => $actual->storage_location,
					'reason' => $actual->reason,
					'reason_name' => $actual->reason_name,
					'receive_location' => 'WSCR'
				]);
			}
		}

		$response = array(
			'status' => true,
			'targets' => $targets,
			'actual_mscr' => $actual_mscr,
			'actual_wscr' => $actual_wscr,
			'categories' => $categories,
			'first' => date('d', strtotime($first)),
			'last' => date('d F Y', strtotime($last))
		);
		return Response::json($response);
	}

	public function indexShipmentReport(){
		$title = 'FG Weekly Shipment';
		$title_jp = 'FG週次出荷';

		return view('displays.shippings.shipment_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display Weekly Shipment')->with('head', 'Display');
	}

	public function indexStuffingProgress(){
		$title = 'Container Stuffing Progress';
		$title_jp = 'コンテナ荷積み進捗';

		return view('displays.shippings.stuffing_progress', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display Stuffing Progress')->with('head', 'Display');
	}

	public function indexStuffingTime()
	{
		$title = 'Container Stuffing Time';
		$title_jp = 'コンテナ荷積み時間';

		return view('displays.shippings.stuffing_time', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display Stuffing Time')->with('head', 'Display');
	}

	public function indexStuffingMonitoring()
	{
		$title = 'Container Stuffing Monitoring';
		$title_jp = 'コンテナ荷積み監視';

		$activity =  new UserActivityLog([
			'activity' => 'Stuffing Monitoring (荷積み監視)',
			'created_by' => Auth::id(),
		]);
		$activity->save();

		return view('displays.shippings.stuffing_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display Stuffing Monitoring')->with('head', 'Display');
	}

	public function indexShipmentProgress(){
		return view('displays.shipment_progress')->with('page', 'Display Shipment Result')->with('head', 'Display');
	}

	public function index_dp_stockroom_stock(){
		return view('displays.stockroom_stock')->with('page', 'Display Stockroom Stock')->with('head', 'Display');
	}

	public function index_dp_fg_accuracy(){
		$title = 'Finished Goods Accuracy';
		$title_jp = 'FG週次出荷';

		return view('displays.fg_accuracy', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Display FG Accuracy')->with('head', 'Display');		
	}

	public function fetchShipmentReport(Request $request){
		if(strlen($request->get('date')) > 0){
			$year = date('Y', strtotime($request->get('date')));
			$date = date('Y-m-d', strtotime($request->get('date')));
			$week_date = date('Y-m-d', strtotime($date. '+ 3 day'));
			$now = date('Y-m-d', strtotime($date));
			$first = date('Y-m-d', strtotime(Carbon::parse('first day of '. date('F Y', strtotime($date)))));
			$week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
			$week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}
		else{
			$year = date('Y');
			$date = date('Y-m-d');
			$now = date('Y-m-d');
			$week_date = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
			$first = date('Y-m-01');
			$week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
			$week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
		}

		$query3 = "select hpl, sum(plan)-sum(actual) as plan, sum(actual) as actual, avg(prc1) as prc_actual, 1-avg(prc1) as prc_plan from
		(
		select material_number, hpl, category, plan, coalesce(actual, 0) as actual, coalesce(actual, 0)/plan as prc1 from
		(
		select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, if(flos.actual>shipment_schedules.quantity, shipment_schedules.quantity, flos.actual) as actual from shipment_schedules 
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		left join materials on materials.material_number = shipment_schedules.material_number
		where weekly_calendars.week_name = '".$week->week_name."' and year(weekly_calendars.week_date) = '" . $year . "' and materials.category = 'FG'

		union all

		select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, flos.actual as actual from shipment_schedules 
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
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
			'chartResult3' => $chartResult3,
			'week' => 'Week ' . substr($week2->week_name, 1),
			'weekTitle' => 'Week ' . substr($week->week_name, 1),
			'dateTitle' => date('d F Y', strtotime($date)),
			'now' => $now,
		);
		return Response::json($response);
	}

	public function fetchShipmentReportDetail(Request $request){
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
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_name = '".$request->get('week')."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "'
		group by shipment_schedules.material_number, materials.material_description
		having if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) > 0

		union all

		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) as quantity 
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
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
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
		left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos 
		on flos.shipment_schedule_id = shipment_schedules.id
		where weekly_calendars.week_name = '".$request->get('week')."' and materials.category = 'FG' and materials.hpl = '".$request->get('hpl')."' and year(weekly_calendars.week_date) = '" . $year . "'
		group by shipment_schedules.material_number, materials.material_description
		having if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) > 0

		union all

		select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) as quantity
		from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
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

	public function fetchAllStock(){

		$query = "select if(stock.destination_code is null, 'Maedaoshi', destinations.destination_shortname) as destination, sum(production) as production, sum(intransit) as intransit, sum(fstk) as fstk, sum(actual) as actual, sum(coalesce(volume,0)) as volume from (

		select shipment_schedules.destination_code, sum(if(flos.status = 'M' or flos.status = '0', flos.actual, 0)) as production, sum(if(flos.status = '2', flos.actual, 0)) as fstk, sum(if(flos.status = '1', flos.actual, 0)) as intransit, sum(flos.actual) as actual, sum(flos.actual*(material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton) as volume
		from flos 
		left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id 
		left join material_volumes on material_volumes.material_number = flos.material_number
		where flos.status in ('0','1','2','M') and flos.actual > 0 
		group by shipment_schedules.destination_code

		union all

		select shipment_schedules.destination_code, sum(if(knock_downs.status = 'M' or knock_downs.status = '0', knock_down_details.quantity, 0)) as production, sum(if(knock_downs.status = '2', knock_down_details.quantity, 0)) as fstk, sum(if(knock_downs.status = '1', knock_down_details.quantity, 0)) as intransit, sum(knock_down_details.quantity) as actual, sum(knock_down_details.quantity*(material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton) as volume
		from knock_down_details 
		left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number 
		left join shipment_schedules on shipment_schedules.id = knock_down_details.shipment_schedule_id 
		left join material_volumes on material_volumes.material_number = knock_down_details.material_number
		where knock_downs.status in ('0','1','2','M') and knock_down_details.quantity > 0
		group by shipment_schedules.destination_code) as stock left join destinations on destinations.destination_code = stock.destination_code group by if(stock.destination_code is null, 'Maedaoshi', destinations.destination_shortname)";

		$jsonData = db::select($query);

		$query2 = "select stock.material_number, materials.material_description, if(stock.status = 'M' or stock.status = '0', 'Production', if(stock.status = '1', 'Intransit', 'FSTK')) as location, if(destinations.destination_shortname is null, 'Maedaoshi', destinations.destination_shortname) as destination, sum(stock.quantity) as quantity from (
		select flos.material_number, flos.destination_code, flos.status, sum(flos.actual) as quantity from flos
		where flos.status in ('M', '0', '1', '2')
		and flos.actual > 0
		group by flos.material_number, flos.destination_code, flos.status

		union all

		select knock_down_details.material_number, shipment_schedules.destination_code, knock_downs.status, sum(knock_down_details.quantity) as quantity from knock_down_details
		left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number
		left join shipment_schedules on shipment_schedules.id = knock_down_details.shipment_schedule_id
		where knock_downs.status in ('M', '0', '1', '2')
		and knock_down_details.quantity > 0
		group by knock_down_details.material_number, shipment_schedules.destination_code, knock_downs.status) as stock
		left join materials on materials.material_number = stock.material_number
		left join destinations on destinations.destination_code = stock.destination_code
		group by stock.material_number, if(destinations.destination_shortname is null, 'Maedaoshi', destinations.destination_shortname), if(stock.status = 'M' or stock.status = '0', 'Production', if(stock.status = '1', 'Intransit', 'FSTK')), stock.status, materials.material_description";

		$stock = db::select($query2);

		$response = array(
			'status' => true,
			'jsonData' => $jsonData,
			'stockData' => $stock,
		);
		return Response::json($response);
	}

	public function fetchStuffingProgress(Request $request){

		if ($request->get('date') == "") {
			$now = date('Y-m-d');
			$end = date('Y-m-d', strtotime($now. ' + 7 days'));
		} else {
			$now = $request->get('date');
			$end = date('Y-m-d', strtotime($now. ' + 7 days'));
		}

		$query = "select if(master_checksheets.`status` is not null, 'DEPARTED', if(actual_stuffing.total_actual > 0, 'LOADING', '-')) as stats, master_checksheets.`status`, master_checksheets.id_checkSheet, master_checksheets.destination, shipment_conditions.shipment_condition_name, actual_stuffing.total_plan, actual_stuffing.total_actual, master_checksheets.reason, master_checksheets.status, master_checksheets.start_stuffing, master_checksheets.finish_stuffing,COALESCE( master_checksheets.deleted_at,'-') as deleted_at from master_checksheets left join shipment_conditions on shipment_conditions.shipment_condition_code = master_checksheets.carier
		left join
		(
		select id_checkSheet, sum(plan_loading) as total_plan, sum(actual_loading) as total_actual from (
		select id_checkSheet, qty_qty as plan_loading, (qty_qty/if(package_qty = '-' or package_qty is null, 1, package_qty))*if(confirm = 0 and bara = 0, 1, confirm) as actual_loading from detail_checksheets where deleted_at is null
		) as stuffings
		group by id_checkSheet
		) as actual_stuffing
		on actual_stuffing.id_checkSheet = master_checksheets.id_checkSheet
		where  master_checksheets.Stuffing_date = '".$now."'
		order by field(stats, 'LOADING', 'INSPECTION', '-', 'DEPARTED')";

		$stuffing_progress = db::select($query);

		$query2 = "select master_checksheets.stuffing_date, count(if(master_checksheets.carier = 'C1', 1, null)) as 'sea', count(if(master_checksheets.carier = 'C2', 1, null)) as 'air', count(if(master_checksheets.carier = 'C4' or master_checksheets.carier = 'TR', 1, null)) as 'truck', sum(stuffings.total_plan) as total_plan from master_checksheets
		left join
		(
		select id_checkSheet, sum(qty_qty) as total_plan from detail_checksheets where deleted_at is null group by id_checkSheet
		) as stuffings 
		on stuffings.id_checkSheet = master_checksheets.id_checkSheet where master_checksheets.deleted_at is null and master_checksheets.Stuffing_date > '".$now."' and master_checksheets.Stuffing_date <= '".$end."' group by master_checksheets.Stuffing_date";

		$stuffing_resume = db::select($query2);

		$response = array(
			'status' => true,
			'stuffing_progress' => $stuffing_progress,
			'stuffing_resume' => $stuffing_resume
		);
		return Response::json($response);
	}

	public function fetchStuffingDetail(Request $request){
		$id_checkSheet = $request->get('id');
		$query = "select id_checkSheet, invoice, gmc, goods, qty_qty as plan_loading, (qty_qty/if(package_qty = '-' or package_qty is null, 1, package_qty))*if(confirm = 0 and bara = 0, 1, confirm) as actual_loading from detail_checksheets where deleted_at is null and id_checkSheet = '".$id_checkSheet."'";
		$stuffing_detail = db::select($query);
		// return DataTables::of($stuffing_detail)->make(true);
		$response = array(
			'status' => true,
			'stuffing_detail' => $stuffing_detail
		);
		return Response::json($response);
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

	public function fetchModalShipmentProgress(Request $request){
		$st_date = date('Y-m-d', strtotime($request->get('date')));


		$hpl = " and materials.hpl = '" . $request->get('hpl') . "'";

		if( $request->get('hpl') == 'all'){
			$hpl = "";
		}

		$query = "
		select a.material_number, a.material_description, a.destination_shortname, a.plan, coalesce(b.actual,0) as actual, coalesce(b.actual,0)-a.plan as diff from
		(
		select shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname, sum(shipment_schedules.quantity) as plan from shipment_schedules
		left join materials on materials.material_number = shipment_schedules.material_number
		left join destinations on destinations.destination_code = shipment_schedules.destination_code
		where materials.category = 'FG' and shipment_schedules.st_date = '" .$st_date . "'

		" . $hpl . "

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

		$shipment_progress = DB::select($query);

		$response = array(
			'status' => true,
			'shipment_progress' => $shipment_progress,
		);
		return Response::json($response);
	}

	public function fetchShipmentProgress(Request $request){
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

		$query = "select date_format(e.st_date, '%d-%b-%Y') as st_date, e.hpl, coalesce(act,0) as act, coalesce(plan,0) as plan, actual from
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

		$response = array(
			'status' => true,
			'tableData' => $tableData,
		);
		return Response::json($response);
	}
}
