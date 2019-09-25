<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\TagMaterial;
use App\MiddleInventory;
use App\BarrelQueue;
use App\Barrel;
use App\BarrelQueueInactive;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;
use App\MiddleNgLog;
use App\MiddleLog;
use App\ErrorLog;
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use App\RfidBuffingInventory;
use Illuminate\Support\Facades\Mail;

class MiddleProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->location = [
			'bff',
			'bff-kensa',
			'lcq-incoming',
			'lcq-kensa',
		];
	}

	public function indexBuffingOpEff(){
		
	}

	public function indexBuffingNgRate(){
		return view('processes.middle.display.buffing_daily_ng', array(
			'title' => 'Daily Buffing NG Rate',
			'title_jp' => '(??)',
		))->with('page', 'Daily NG Buffing');
	}

	public function indexDisplayMonitoring(){
		$locs = db::select("select distinct location from middle_inventories order by location");

		return view('processes.middle.display.monitoring', array(
			'title' => 'Middle Process Monitoring',
			'title_jp' => '(??)',
			'locs' => $locs,
		))->with('page', 'Middle Process Monitoring');

	}	

	public function indexDisplayPicking(){
		$keys = db::select("select DISTINCT `key` from materials order by `key` ASC");
		$models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");

		return view('processes.middle.display.middle_picking', array(
			'title' => 'Middle Process Picking Schedule',
			'title_jp' => '(??)',
			'models' => $models,
			'keys' => $keys,
		))->with('page', 'Middle Process Picking Schedule');
	}

	public function indexReportHourlyLcq(){
		$locations = $this->location;

		return view('processes.middle.report.hourly_report', array(
			'title' => 'Hourly Lacquering Report',
			'title_jp' => '(??)',
			'locations' => $locations
		))->with('page', 'Hourly Report');
	}

	public function indexReportBuffingNg(){
		return view('processes.middle.report.ng_buffing', array(
			'title' => 'NG Buffing Report',
			'title_jp' => '(??)'
		))->with('page', 'NG Buffing');
	}

	public function indexReportLcqNg(){
		$fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

		return view('processes.middle.report.ng_lacquering', array(
			'title' => 'Report NG Lacquering',
			'title_jp' => '塗装不良率',
			'fys' => $fys
		))->with('page', 'NG Lacquering');
	}


	public function indexDisplayProductionResult(){
		$locations = $this->location;

		return view('processes.middle.display.production_result', array(
			'title' => 'Middle Production Result',
			'title_jp' => '中間工程生産実績',
			'locations' => $locations
		))->with('page', 'Production Result');
	}

	public function indexReportNG(){
		$title = 'Not Good Record';
		$title_jp = '不良内容';
		$locations = $this->location;

		return view('processes.middle.report.not_good', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('head', 'Middle Process');
	}

	public function indexReportProductionResult(){
		$title = 'Production Result Record';
		$title_jp = '生産実績';
		$locations = $this->location;

		return view('processes.middle.report.production_result', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('head', 'Middle Process');
	}

	public function indexStockMonitoring(){
		$title = 'Middle Process Stock Monitoring';
		$title_jp = '中間工程の在庫監視';
		return view('processes.middle.display.stock_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'Middle Process');
	}

	public function indexBarrelLog(){
		$title = 'Barrel Log';
		$title_jp = '(?)';

		$origin_groups = db::table('origin_groups')->get();

		return view('processes.middle.report.barrel_log', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'origin_groups' => $origin_groups,
		))->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
	}

	public function indexProcessMiddleSX(){
		return view('processes.middle.index_sx')->with('page', 'Middle Process SX')->with('head', 'Middle Process');
	}

	public function indexProcessBarrelMachine(){
		$title = 'Saxophone Barrel Machine';
		$title_jp = 'サックスのバレル機';

		return view('processes.middle.barrel_machine', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
	}

	public function indexProcessBarrelBoard($id){

		if($id == 'barrel-sx'){
			$title = 'Saxophone Barrel Board';
			$title_jp = 'サックスのバレル加工用モニター';
			$mrpc = 'S51';
			$hpl = 'ASKEY,TSKEY';
		}

		return view('processes.middle.barrel_board', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mrpc' => $mrpc,
			'hpl' => $hpl,
		))->with('page', 'Middle Process Barrel Board')->with('head', 'Middle Process');
	}

	public function indexBuffingPerformance($id){

		if($id == 'op_ng'){
			$title = 'NG Rate By Operators';
			$title_jp = '??';

			$origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'ASC')->get();

			return view('processes.middle.display.buffing_ng', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'origin_groups' => $origin_groups
			))->with('page', 'Middle Process Buffing Performance')->with('head', 'Middle Process');
		}

	}

	public function indexBuffingBoard($id){
		if($id == 'buffing-sx'){
			$title = 'Saxophone Buffing Board';
			$title_jp = '-';
			$mrpc = 'S41';
			$hpl = 'ASKEY,TSKEY';
		}

		return view('processes.middle.buffing_board', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mrpc' => $mrpc,
			'hpl' => $hpl,
		))->with('page', 'Middle Process Barrel Board')->with('head', 'Middle Process');
	}

	public function indexBuffingWorkOrder($id){
		if($id == 'bff-sx'){
			$title = 'Saxophone Buffing Work Order';
			$title_jp = '-';
			$mrpc = 'S41';
			$hpl = 'ASKEY,TSKEY';
		}

		return view('processes.middle.buffing_work_order', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mrpc' => $mrpc,
			'hpl' => $hpl,
		))->with('page', 'Buffing Work Order')->with('head', 'Middle Process');
	}

	public function indexReportMiddle($id){
		if($id == 'slip-fulfillment'){
			$title = "";
			$title_jp = "";

			return view('processes.middle.report.slip_fulfillment', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
		}
	}

	public function indexProcessMiddleBarrel($id){
		if($id == 'barrel-sx-lcq'){
			$title = 'Saxophone Tumbling-Barrel For Lacquering';
			$mrpc = 'S51';
			$hpl = 'ASKEY,TSKEY';
			$surface = 'LCQ';
			return view('processes.middle.barrel_lcq', array(
				'title' => $title,
				'mrpc' => $mrpc,
				'hpl' => $hpl,
				'surface' => $surface,
			))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
		}

		if($id == 'barrel-sx-plt'){
			$title = 'Saxophone Tumbling-Barrel For Plating';
			$mrpc = 'S51';
			$hpl = 'ASKEY,TSKEY';
			$surface = 'PLT';
			return view('processes.middle.barrel_plt', array(
				'title' => $title,
				'mrpc' => $mrpc,
				'hpl' => $hpl,
				'surface' => $surface,
			))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
		}

		if($id == 'barrel-sx-flanel'){
			$title = 'Saxophone Flanel';
			$mrpc = 'S51';
			$hpl = 'ASKEY,TSKEY';
			$surface = 'FLANEL';
			return view('processes.middle.barrel_flanel', array(
				'title' => $title,
				'mrpc' => $mrpc,
				'hpl' => $hpl,
				'surface' => $surface,
			))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
		}
	}

	public function indexProcessMiddleReturn($id){
		if($id == 'buffing'){
			$title = 'Return Material to Buffing';
			$mrpc = 'S51';
			$hpl = 'ASKEY,TSKEY';
			return view('processes.middle.return', array(
				'title' => $title,
				'mrpc' => $mrpc,
				'hpl' => $hpl,
			))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
		}
	}

	public function indexProcessBuffingKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();

		$title = 'Buffing Kensa';
		$title_jp = "";

		return view('processes.middle.buffing_kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Buffing Kensa')->with('head', 'Middle Process');

	}

	public function indexProcessMiddleKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();

		if($id == 'lcq-incoming'){
			$title = 'I.C. Saxophone Key Lacquering';
			$title_jp= '?';
		}
		if($id == 'lcq-incoming2'){
			$title = 'I.C. Saxophone Key After Treatment Lacquering';
			$title_jp= '?';
		}
		if($id == 'plt-incoming-sx'){
			$title = 'I.C. Saxophone Key Plating';
			$title_jp= '?';
		}
		if($id == 'lcq-kensa'){
			$title = 'Kensa Saxophone Key Lacquering';
			$title_jp= '?';
		}
		if($id == 'plt-kensa-sx'){
			$title = 'Kensa Saxophone Key Plating';
			$title_jp= '?';
		}

		return view('processes.middle.kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function indexBarrelAdjustment()
	{
		$title = 'Saxophone Barrel Adjustment';
		$title_jp = '??';
		$mrpc = 'S51';
		$hpl = 'ASKEY,TSKEY';

		return view('processes.middle.barrel_adjustment', array(
			'title' => $title,
			'mrpc' => $mrpc,
			'hpl' => $hpl,
		))->with('page', 'queue')->with('head', 'Middle Process Adjustment');
	}

	public function indexWIPAdjustment()
	{
		$title = 'WIP Adjustment';
		$title_jp = '??';
		$mrpc = 'S51';
		$hpl = 'ASKEY,TSKEY';

		return view('processes.middle.wip_adjustment', array(
			'title' => $title,
			'mrpc' => $mrpc,
			'hpl' => $hpl,
		))->with('page', 'wip')->with('head', 'Middle Process Adjustment');
	}

	public function fetchBuffingNg(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		
		$ng = db::select("select ng_name, sum(quantity) as jml
			from middle_ng_logs
			where location = 'bff-kensa'
			and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
			GROUP BY ng_name order by jml desc");

		$response = array(
			'status' => true,
			'date' => $date,
			'ng' => $ng,
		);
		return Response::json($response);
	}


	public function fetchBuffingNgRate(){
		$datefrom = date("Y-m-d", strtotime("-3 Months"));
		$dateto = date("Y-m-d");

		$ng_alto = db::select("select a.week_date, b.jml from
			(select week_date from weekly_calendars WHERE week_date BETWEEN '".$datefrom."' and '".$dateto."') a
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, sum(quantity) as jml
			from middle_ng_logs l left join materials m on l.material_number = m.material_number
			where m.hpl = 'ASKEY' and l.location = 'bff-kensa'
			group by tgl) b
			on a.week_date = b.tgl
			order by a.week_date");

		$ng_tenor = db::select("select a.week_date, b.jml from
			(select week_date from weekly_calendars WHERE week_date BETWEEN '".$datefrom."' and '".$dateto."') a
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, sum(quantity) as jml
			from middle_ng_logs l left join materials m on l.material_number = m.material_number
			where m.hpl = 'TSKEY' and l.location = 'bff-kensa'
			group by tgl) b
			on a.week_date = b.tgl
			order by a.week_date");

		$buff_alto = db::connection('digital_kanban')->select("select DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') as tgl, sum(l.material_qty) as jml
			from data_log l left join materials m on l.material_number = m.material_number
			where m.hpl = 'ASKEY'
			and DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') BETWEEN '".$datefrom."' and '".$dateto."'
			GROUP BY tgl");

		$buff_tenor = db::connection('digital_kanban')->select("select DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') as tgl, sum(l.material_qty) as jml
			from data_log l left join materials m on l.material_number = m.material_number
			where m.hpl = 'TSKEY'
			and DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') BETWEEN '".$datefrom."' and '".$dateto."'
			GROUP BY tgl");

		$response = array(
			'status' => true,
			'ng_alto' => $ng_alto,
			'ng_tenor' => $ng_tenor,
			'buff_alto' => $buff_alto,
			'buff_tenor' => $buff_tenor,
		);
		return Response::json($response);
	}

	public function fetchBuffingPerformance(Request $request){
		$tanggal = '';
		if(strlen($request->get("tanggal")) > 0){
			$tanggal = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$tanggal = date('Y-m-d');
		}

		$where_ng = "";
		$where_mt = "";
		if($request->get('code') != null) {
			$codes = $request->get('code');
			$code = "";

			for($x = 0; $x < count($codes); $x++) {
				$code = $code."'".substr($codes[$x],0,3)."'";
				if($x != count($codes)-1){
					$code = $code.",";
				}
			}
			$where_ng = "and m.origin_group_code in (".$code.") ";
			$where_mt = "where origin_group_code in (".$code.") ";
		}

		// $ng_logs = db::select("select l.operator_id, e.`name`, sum(quantity) as jml_ng from middle_ng_logs l
		// 	left join employees e
		// 	on l.operator_id = e.employee_id
		// 	left join materials m
		// 	on l.material_number = m.material_number
		// 	where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'".$where_ng."
		// 	and l.location = 'bff-kensa'
		// 	GROUP BY l.operator_id, e.`name`");

		$ng_logs = db::select("SELECT * from (
			SELECT operator_id, `name`, sum(jml) as jml, COUNT(created_at) as kensa from
			(select l.created_at, l.operator_id, e.`name`, sum(quantity) as jml from middle_ng_logs l
			left join employees e
			on l.operator_id = e.employee_id
			left join materials m
			on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tanggal."'".$where_ng."
			and l.location = 'bff-kensa'
			GROUP BY l.created_at,l.operator_id, e.`name`) a
			GROUP BY operator_id, `name`) b
			WHERE kensa > 2");

		$material_number = db::select("select distinct material_number from materials ".$where_mt);
		$material = json_encode($material_number);
		$material = str_replace('{"material_number":"','',$material);
		$material = str_replace('"}','',$material);
		$material = str_replace('[','',$material);
		$material = str_replace(']','',$material);

		$addmaterial = "";
		$materials = explode(",", $material);
		$material_number = "";

		for($x = 0; $x < count($materials); $x++) {
			$material_number = $material_number."'".$materials[$x]."'";
			if($x != count($materials)-1){
				$material_number = $material_number.",";
			}
		}
		$addmaterial = "and material_number in (".$material_number.") ";

		$rfid = db::connection('digital_kanban')->select("select operator_id, material_number, sum(material_qty) as jml_buff from data_log where DATE_FORMAT(selesai_start_time,'%Y-%m-%d') = '".$tanggal."' ".$addmaterial."
			GROUP BY operator_id, material_number order by operator_id");

		$response = array(
			'status' => true,
			'ng_logs' => $ng_logs,
			'rfid' => $rfid,
			
		);
		return Response::json($response);
	}

	public function fetchDisplayMonitoring(Request $request){
		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "where location in (".$location.") ";
		}

		$stock = db::select("select a.diff, a.location, COALESCE(b.jml,0) as jml from
			(select diff, location from
			(select distinct location from middle_inventories) loc
			cross join
			(select distinct DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) as diff from middle_inventories
			where DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) <= 6
			) diff
			order by diff, location asc) a
			left join
			(select location, count(id) as jml, if(DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at)>=6, 6, DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at)) as diff from middle_inventories ".$addlocation."
			group by location, diff order by diff, location asc) b
			on (a.diff = b.diff and a.location = b.location)
			ORDER BY a.diff, a.location");
		$loc = db::select("select distinct location from middle_inventories order by location asc");
		$diff = db::select("select distinct DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) as diff from middle_inventories order by diff asc");

		$response = array(
			'status' => true,
			'stock' => $stock,
			'loc' => $loc,
			'diff' => $diff,
		);
		return Response::json($response);
	}

	public function fetchDetailStockMonitoring(Request $request){

		$diff = $request->get('diff'); 
		if($diff[0] == '0'){
			$diff = '= 0';
		}else if($diff[0] == '<'){
			$diff = '= '.substr($diff,1,1);
		}else{
			$diff = substr($diff,0,2);
		}

		$location = strtolower($request->get('loc'));

		$detail = db::select("select i.tag, i.material_number, m.material_description, i.location, i.quantity
			from middle_inventories i left join materials m
			on i.material_number = m.material_number
			where DATEDIFF(CURRENT_TIMESTAMP, i.created_at) ".$diff."
			and location = '".$location."'");

		$response = array(
			'status' => true,
			'detail' => $detail,
		);
		return Response::json($response);
		
	}

	public function fetchDisplayPicking(Request $request){

		// $tgl = '';
		// if(strlen($request->get("tgl")) > 0){
		// 	$dateto = date('Y-m-d',strtotime($request->get("tgl")));
		// 	$datefrom = date('Y-m-01', strtotime($request->get("tgl")));
		// 	$tgl = "where DATE_FORMAT(a.created_at,'%Y-%m-%d') >= '".$datefrom."' and DATE_FORMAT(a.created_at,'%Y-%m-%d') <= '".$dateto."'";
		// }else{
		// 	$dateto = date('Y-m-d');
		// 	$datefrom = date('Y-m-01');
		// 	$tgl = "where DATE_FORMAT(a.created_at,'%Y-%m-%d') >= '".$datefrom."' and DATE_FORMAT(a.created_at,'%Y-%m-%d') <= '".$dateto."'";
		// }

		// $surface = $request->get("surface");
		// $model = $request->get("model");
		// $key = $request->get("key");

		// $add_surface = "";
		// $add_model = "";
		// $add_key = "";

		// if ($request->get('surface') != "") {
		// 	$surfaces = explode(",",$request->get('surface'));
		// 	$surfacelength = count($surfaces);
		// 	$surface = "";

		// 	for($x = 0; $x < $keylength; $x++) {
		// 		$surface = $surface."'".$surfaces[$x]."'";
		// 		if($x != $keylength -1){
		// 			$surface = $surface.",";
		// 		}
		// 	}
		// 	$add_surface = " AND m.surface IN (".$surface.")";
		// }

		// if ($request->get('model') != "") {
		// 	$models = explode(",",$request->get('model'));
		// 	$modellength = count($models);
		// 	$model = "";

		// 	for($x = 0; $x < $modellength; $x++) {
		// 		$model = $model."'".$models[$x]."'";
		// 		if($x != $modellength -1){
		// 			$model = $model.",";
		// 		}
		// 	}
		// 	$add_model = " AND m.model IN (".$model.")";
		// }

		// if ($request->get('key') != "") {
		// 	$keys = explode(",",$request->get('key'));
		// 	$keylength = count($keys);
		// 	$key = "";

		// 	for($x = 0; $x < $keylength; $x++) {
		// 		$key = $key."'".$keys[$x]."'";
		// 		if($x != $keylength -1){
		// 			$key = $key.",";
		// 		}
		// 	}
		// 	$add_key = " AND m.`key` IN (".$key.")";
		// }

		// $assy = '';
		// $minus = '';

		// $date = substr(date('Y-m-d',strtotime($request->get("tgl"))),8,2);
		// if($date == '01'){
		// 	$assy = db::select("select a.material_number, m.surface, m.model, m.`key`, sum(a.quantity) qty from
		// 		assy_picking_schedules a left join materials m on a.material_number = m.material_number
		// 		".$tgl."".$add_surface."".$add_model."".$add_key."
		// 		group by a.material_number, m.surface, m.model, m.`key`
		// 		order by m.`key`, m.model asc
		// 		limit 20");

		// }else{
		// 	$assy = db::select("select a.material_number, m.surface, m.model, m.`key`, sum(a.quantity) qty from
		// 		assy_picking_schedules a left join materials m on a.material_number = m.material_number
		// 		where DATE_FORMAT(a.created_at,'%Y-%m-%d') >= '".date('Y-m-d',strtotime($request->get("tgl")))."'
		// 		".$add_surface."".$add_model."".$add_key."
		// 		group by a.material_number, m.surface, m.model, m.`key`
		// 		order by m.`key`, m.model asc
		// 		limit 20");

		// 	$minus = db::connection('mysql2')->select("select material_number, minus from
		// 		(select transfer_material_id, SUM(IF(category = 'transfer' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I3',lot,0),0)) - SUM(IF(category = 'transfer_cancel' OR category = 'transfer_return' OR category = 'transfer_adjustment', IF(transfer_movement_type = '9I4',lot,0),0)) as minus from kitto.histories
		// 		".$tgl."
		// 		and transfer_material_id is not null
		// 		group by transfer_material_id) min 
		// 		left join kitto.materials as k_materials on k_materials.id = min.transfer_material_id
		// 		where k_materials.location like '%51%'");
		// }	

		// $response = array(
		// 	'status' => true,
		// 	'assy' => $assy,
		// 	'minus' => $minus,
		// );
		// return Response::json($response);
	}

	public function fetchDisplayProductionResult(Request $request){
		$tgl="";
		if(strlen($request->get('tgl')) > 0){
			$tgl = date('Y-m-d',strtotime($request->get("tgl")));
		}else{
			$tgl = date("Y-m-d");
		}
		$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$tgl."' and";

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and l.location in (".$location.") ";
		}

		$query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
		(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'ASKEY' and surface not like '%PLT%' order by `key`) a
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '00:00:00' and TIME(l.created_at) < '07:00:00' and m.hpl = 'ASKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s3
		on a.keymodel = s3.keymodel
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '07:00:00' and TIME(l.created_at) < '16:00:00' and m.hpl = 'ASKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s1
		on a.keymodel = s1.keymodel
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '16:00:00' and TIME(l.created_at) < '23:59:59' and m.hpl = 'ASKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s2
		on a.keymodel = s2.keymodel
		ORDER BY `key`";
		$alto = db::select($query1);

		$query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
		(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'TSKEY' and surface not like '%PLT%' order by `key`) a
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '00:00:00' and TIME(l.created_at) < '07:00:00' and m.hpl = 'TSKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s3
		on a.keymodel = s3.keymodel
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '07:00:00' and TIME(l.created_at) < '16:00:00' and m.hpl = 'TSKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s1
		on a.keymodel = s1.keymodel
		left join
		(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from middle_logs l
		left join materials m on l.material_number = m.material_number
		WHERE ".$tanggal." TIME(l.created_at) > '16:00:00' and TIME(l.created_at) < '23:59:59' and m.hpl = 'TSKEY' ".$addlocation."
		GROUP BY m.`key`, m.model) s2
		on a.keymodel = s2.keymodel
		ORDER BY `key`";
		$tenor = db::select($query2);

		$query3 = "select distinct `key` from materials where hpl = 'ASKEY' and surface not like '%PLT%' order by `key`";
		$key =  db::select($query3);

		$query4 = "select distinct model from materials where hpl = 'ASKEY' and surface not like '%PLT%' order by model";
		$model_alto =  db::select($query4);

		$query5 = "select distinct model from materials where hpl = 'TSKEY' and surface not like '%PLT%' order by model";
		$model_tenor =  db::select($query5);

		$location = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			for($x = 0; $x < count($locations); $x++) {
				$location = $location." ".$locations[$x]." ";
				if($x != count($locations)-1){
					$location = $location."&";
				}
			}
		}else{
			$location = "lcq-incoming & lcq-kensa";
		}
		$location = strtoupper($location);

		$response = array(
			'status' => true,
			'alto' => $alto,
			'tenor' => $tenor,
			'key' => $key,
			'model_tenor' => $model_tenor,
			'model_alto' => $model_alto,
			'title' => $location
		);
		return Response::json($response);

	}

	public function fetchReportHourlyLcq(Request $request){
		$tanggal = '';
		if(strlen($request->get('date')) > 0){
			$date = date('Y-m-d', strtotime($request->get('date')));
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}else{		
			$date = date('Y-m-d');
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = $request->get('location');
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and l.location in (".$location.") ";
		}

		$key = db::select("select DISTINCT SUBSTRING(`key`, 1, 1) as kunci from materials where hpl = 'ASKEY' and surface not like '%PLT%' ORDER BY `key` asc");

		$jam = [
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '01:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '03:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '05:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '07:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '09:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '11:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '14:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '16:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '18:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '20:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '22:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '23:59:59'"
		];
		
		$dataShift3 = [];
		$dataShift1 = [];
		$dataShift2 = [];

		$z3 = [];
		$z1 = [];
		$z2 = [];

		$push_data = [];
		$push_data_z = [];

		for ($i=0; $i <= 3 ; $i++) {
			$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
			array_push($dataShift3, $push_data[$i]);
			
			$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number 
				where  ".$tanggal." ".$jam[$i]." and m.model = 'A82Z' ".$addlocation."
				GROUP BY tgl, m.model");
			array_push($z3, $push_data_z[$i]);
		}

		for ($i=4; $i <= 7 ; $i++) {
			$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
			array_push($dataShift1, $push_data[$i]);
			
			$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number 
				where  ".$tanggal." ".$jam[$i]." and m.model = 'A82Z' ".$addlocation."
				GROUP BY tgl, m.model");
			array_push($z1, $push_data_z[$i]);
		}

		for ($i=8; $i <= 11 ; $i++) {
			$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where ".$tanggal." ".$jam[$i]." ".$addlocation."
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
			array_push($dataShift2, $push_data[$i]);
			
			$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number 
				where  ".$tanggal." ".$jam[$i]." and m.model = 'A82Z' ".$addlocation."
				GROUP BY tgl, m.model");
			array_push($z2, $push_data_z[$i]);
		}

		$tanggal = substr($tanggal,40,10);

		$response = array(
			'status' => true,
			'tanggal' => $tanggal,
			'key' => $key,
			'dataShift3' => $dataShift3,
			'dataShift1' => $dataShift1,
			'dataShift2' => $dataShift2,
			'z3' => $z3, 
			'z1' => $z1, 
			'z2' => $z2, 
		);
		return Response::json($response);

	}

	public function fetchLcqNgRateWeekly(Request $request){
		$bulan="";
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}else{
			$bulan = date('m-Y');
		}

		$query = "SELECT a.week_name, sum(b.ng) as ng, sum(c.g) as g from
		(SELECT week_name, week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') a
		left join
		(SELECT DATE_FORMAT(n.created_at,'%Y-%m-%d') as tgl, sum(n.quantity) ng from middle_ng_logs n
		left join materials m on m.material_number = n.material_number
		where location = 'lcq-incoming' and m.surface not like '%PLT%' and DATE_FORMAT(n.created_at,'%m-%Y') = '".$bulan."'
		GROUP BY tgl) b on a.week_date = b.tgl
		left join
		(SELECT DATE_FORMAT(g.created_at,'%Y-%m-%d') as tgl, sum(g.quantity) g from middle_logs g
		left join materials m on m.material_number = g.material_number
		where location = 'lcq-incoming' and m.surface not like '%PLT%' and DATE_FORMAT(g.created_at,'%m-%Y') = '".$bulan."'
		GROUP BY tgl) c on a.week_date = c.tgl
		GROUP BY a.week_name";
		$weekly = db::select($query);

		$response = array(
			'status' => true,
			'weekly' => $weekly,
			'bulan' => $bulan
		);
		return Response::json($response);
	}


	public function fetchLcqNgRateMonthly(Request $request){
		$fy = '';
		if($request->get('fy') != null){
			$fys =  explode(",", $request->get('fy'));
			for ($i=0; $i < count($fys); $i++) {
				$fy = $fy."'".$fys[$i]."'";
				if($i != (count($fys)-1)){
					$fy = $fy.',';
				}
			}
		}else{
			$get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
			foreach ($get_fy as $key) {
				$fy = "'".$key->fiscal_year."'";
			}
		}

		$query1 = "SELECT a.tgl, COALESCE(b.ng,b.ng,0) as ng, COALESCE(c.g,c.g,0) as g, (b.ng+c.g) as total FROM
		(SELECT DATE_FORMAT(week_date,'%m-%Y') as tgl from weekly_calendars where fiscal_year in (".$fy.") GROUP BY tgl ORDER BY week_date asc) a
		left join
		(SELECT DATE_FORMAT(n.created_at,'%m-%Y') as tgl, sum(n.quantity) ng from middle_ng_logs n
		left join materials m on m.material_number = n.material_number
		where location = 'lcq-incoming' and m.surface not like '%PLT%'
		GROUP BY tgl) b on a.tgl = b.tgl
		left join
		(SELECT DATE_FORMAT(g.created_at,'%m-%Y') as tgl, sum(g.quantity) g from middle_logs g
		left join materials m on m.material_number = g.material_number
		where location = 'lcq-incoming' and m.surface not like '%PLT%'
		GROUP BY tgl) c on a.tgl = c.tgl";
		$monthly_ic = db::select($query1);

		$query2 = "SELECT a.tgl, COALESCE(b.ng,b.ng,0) as ng, COALESCE(c.g,c.g,0) as g, (b.ng+c.g) as total FROM
		(SELECT DATE_FORMAT(week_date,'%m-%Y') as tgl from weekly_calendars where fiscal_year in (".$fy.") GROUP BY tgl ORDER BY week_date asc) a
		left join
		(SELECT DATE_FORMAT(n.created_at,'%m-%Y') as tgl, sum(n.quantity) ng from middle_ng_logs n
		left join materials m on m.material_number = n.material_number
		where location = 'lcq-kensa' and m.surface not like '%PLT%'
		GROUP BY tgl) b on a.tgl = b.tgl
		left join
		(SELECT DATE_FORMAT(g.created_at,'%m-%Y') as tgl, sum(g.quantity) g from middle_logs g
		left join materials m on m.material_number = g.material_number
		where location = 'lcq-kensa' and m.surface not like '%PLT%'
		GROUP BY tgl) c on a.tgl = c.tgl";
		$monthly_kensa = db::select($query2);

		$fy = "";
		if($request->get('fy') != null) {
			$fys = explode(",", $request->get('fy'));
			for($x = 0; $x < count($fys); $x++) {
				$fy = $fy." ".$fys[$x]." ";
				if($x != count($fys)-1){
					$fy = $fy."&";
				}
			}
		}else{
			$get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
			foreach ($get_fy as $key) {
				$fy = $key->fiscal_year;
			}
		}

		$response = array(
			'status' => true,
			'monthly_ic' => $monthly_ic,
			'monthly_kensa' => $monthly_kensa,
			'fy' => $fy
		);
		return Response::json($response);
	}

	public function fetchLcqNg(Request $request){

		$bulan="";
		if(strlen($request->get('bulan')) > 0){
			$bulan = "DATE_FORMAT(l.created_at,'%m-%Y') = '".$request->get('bulan')."' and ";
		}else{
			$bulan = "DATE_FORMAT(l.created_at,'%m-%Y') = '".date('m-Y')."' and ";
		}


		// IC
		$totalCekIC_alto = db::select("select (a.g+b.ng) as total from
			(select sum(quantity) as g from middle_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'ASKEY') a
			cross join
			(select sum(quantity) as ng from middle_ng_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'ASKEY') b");

		$totalCekIC_tenor = db::select("select (a.g+b.ng) as total from
			(select sum(quantity) as g from middle_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'TSKEY') a
			cross join
			(select sum(quantity) as ng from middle_ng_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'TSKEY') b");

		$ngIC_alto = db::select("select l.ng_name, m.hpl, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-incoming' and m.surface not like '%PLT%' and m.hpl = 'ASKEY' group by l.ng_name, m.hpl order by jml desc");

		$ngIC_tenor = db::select("select l.ng_name, m.hpl, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-incoming' and m.surface not like '%PLT%' and m.hpl = 'TSKEY' group by l.ng_name, m.hpl order by jml desc");

		$ngICKey_alto = db::select("select m.`key`, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-incoming' and m.surface not like '%PLT%' and m.hpl = 'ASKEY' group by m.`key` order by jml desc LIMIT 10;");

		$ngICKey_tenor = db::select("select m.`key`, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-incoming' and m.surface not like '%PLT%' and m.hpl = 'TSKEY' group by m.`key` order by jml desc LIMIT 10;");


		// Kensa
		$totalCekKensa_alto = db::select("select (a.g+b.ng) as total from
			(select sum(quantity) as g from middle_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'ASKEY') a
			cross join
			(select sum(quantity) as ng from middle_ng_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'ASKEY') b");

		$totalCekKensa_tenor = db::select("select (a.g+b.ng) as total from
			(select sum(quantity) as g from middle_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'TSKEY') a
			cross join
			(select sum(quantity) as ng from middle_ng_logs l left join materials m on m.material_number = l.material_number
			where ".$bulan." m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'TSKEY') b");

		$ngKensa_alto = db::select("select l.ng_name, m.hpl, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-kensa' and m.surface not like '%PLT%' and m.hpl = 'ASKEY' group by l.ng_name, m.hpl order by jml desc limit 10");

		$ngKensa_tenor = db::select("select l.ng_name, m.hpl, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-kensa' and m.surface not like '%PLT%' and m.hpl = 'TSKEY' group by l.ng_name, m.hpl order by jml desc limit 10");

		$ngKensaKey = db::select("select m.`key`, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-kensa' and m.surface not like '%PLT%' group by m.`key` order by jml desc;");

		$ngKensaKey_alto = db::select("select m.`key`, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-kensa' and m.surface not like '%PLT%' and m.hpl = 'ASKEY' group by m.`key` order by jml desc LIMIT 10;");

		$ngKensaKey_tenor = db::select("select m.`key`, sum(l.quantity) as jml from middle_ng_logs l
			left join materials m on l.material_number = m.material_number
			where ".$bulan." location = 'lcq-kensa' and m.surface not like '%PLT%' and m.hpl = 'TSKEY' group by m.`key` order by jml desc LIMIT 10;");

		$bulan = substr($bulan,37,7);

		$response = array(
			'status' => true,
			
			'ngIC_alto' => $ngIC_alto,
			'ngIC_tenor' => $ngIC_tenor,
			'totalCekIC_alto' => $totalCekIC_alto,
			'totalCekIC_tenor' => $totalCekIC_tenor,
			'ngICKey_alto' => $ngICKey_alto,	
			'ngICKey_tenor' => $ngICKey_tenor,

			'ngKensa_alto' => $ngKensa_alto,
			'ngKensa_tenor' => $ngKensa_tenor,
			'totalCekKensa_alto' => $totalCekKensa_alto,
			'totalCekKensa_tenor' => $totalCekKensa_tenor,
			'ngKensaKey_alto' => $ngKensaKey_alto,
			'ngKensaKey_tenor' => $ngKensaKey_tenor,

			'bulan' => $bulan
		);
		return Response::json($response);
	}

	public function fetchLcqNgRate(Request $request){

		$bulan = "";
		$bulan1 = "";
		$bulan2 = "";

		if(strlen($request->get('bulan')) > 0){
			$bulan = "where DATE_FORMAT(week_date,'%m-%Y') ='".$request->get('bulan')."' ";
			$bulan1 = "where DATE_FORMAT(n.created_at,'%m-%Y') ='".$request->get('bulan')."' ";
			$bulan2 = "where DATE_FORMAT(g.created_at,'%m-%Y') ='".$request->get('bulan')."' ";
		}else{
			$bulan = "where DATE_FORMAT(week_date,'%m-%Y') ='".date('m-Y')."' ";
			$bulan1 = "where DATE_FORMAT(n.created_at,'%m-%Y') ='".date('m-Y')."' ";
			$bulan2 = "where DATE_FORMAT(g.created_at,'%m-%Y') ='".date('m-Y')."' ";
		}

		// IC
		$dailyICAlto = db::select("SELECT a.tgl, b.hpl, b.ng, (COALESCE(b.ng,0)+COALESCE(c.g,0)) as total FROM
			(SELECT DATE_FORMAT(week_date,'%d-%m-%Y') as tgl from weekly_calendars ".$bulan.") a
			left join
			(SELECT DATE_FORMAT(n.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(n.quantity) as ng from middle_ng_logs n
			left join materials m on m.material_number = n.material_number ".$bulan1." and m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'ASKEY'
			GROUP BY tgl, hpl) b on a.tgl = b.tgl
			left join		
			(SELECT DATE_FORMAT(g.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(g.quantity) as g from middle_logs g
			left join materials m on m.material_number = g.material_number ".$bulan2." and m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'ASKEY'
			GROUP BY tgl, hpl) c on a.tgl = c.tgl;");

		$dailyICTenor = db::select("SELECT a.tgl, b.hpl, b.ng, (COALESCE(b.ng,0)+COALESCE(c.g,0)) as total FROM
			(SELECT DATE_FORMAT(week_date,'%d-%m-%Y') as tgl from weekly_calendars ".$bulan.") a
			left join
			(SELECT DATE_FORMAT(n.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(n.quantity) as ng from middle_ng_logs n
			left join materials m on m.material_number = n.material_number ".$bulan1." and m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'TSKEY'
			GROUP BY tgl, hpl) b on a.tgl = b.tgl
			left join		
			(SELECT DATE_FORMAT(g.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(g.quantity) as g from middle_logs g
			left join materials m on m.material_number = g.material_number ".$bulan2." and m.surface not like '%PLT%' and location = 'lcq-incoming' and m.hpl = 'TSKEY'
			GROUP BY tgl, hpl) c on a.tgl = c.tgl;");


		// Kensa
		$dailyKensaAlto = db::select("SELECT a.tgl, b.hpl, b.ng, (COALESCE(b.ng,0)+COALESCE(c.g,0)) as total FROM
			(SELECT DATE_FORMAT(week_date,'%d-%m-%Y') as tgl from weekly_calendars ".$bulan.") a
			left join
			(SELECT DATE_FORMAT(n.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(n.quantity) as ng from middle_ng_logs n
			left join materials m on m.material_number = n.material_number ".$bulan1." and m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'ASKEY'
			GROUP BY tgl, hpl) b on a.tgl = b.tgl
			left join		
			(SELECT DATE_FORMAT(g.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(g.quantity) as g from middle_logs g
			left join materials m on m.material_number = g.material_number ".$bulan2." and m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'ASKEY'
			GROUP BY tgl, hpl) c on a.tgl = c.tgl;");

		$dailyKensaTenor = db::select("SELECT a.tgl, b.hpl, b.ng, (COALESCE(b.ng,0)+COALESCE(c.g,0)) as total FROM
			(SELECT DATE_FORMAT(week_date,'%d-%m-%Y') as tgl from weekly_calendars ".$bulan.") a
			left join
			(SELECT DATE_FORMAT(n.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(n.quantity) as ng from middle_ng_logs n
			left join materials m on m.material_number = n.material_number ".$bulan1." and m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'TSKEY'
			GROUP BY tgl, hpl) b on a.tgl = b.tgl
			left join		
			(SELECT DATE_FORMAT(g.created_at,'%d-%m-%Y') as tgl, m.hpl, sum(g.quantity) as g from middle_logs g
			left join materials m on m.material_number = g.material_number ".$bulan2." and m.surface not like '%PLT%' and location = 'lcq-kensa' and m.hpl = 'TSKEY'
			GROUP BY tgl, hpl) c on a.tgl = c.tgl;");

		$bulan = substr($bulan,39,7);

		$response = array(
			'status' => true,
			
			'dailyICAlto' => $dailyICAlto,
			'dailyICTenor' => $dailyICTenor,

			'dailyKensaAlto' => $dailyKensaAlto,
			'dailyKensaTenor' => $dailyKensaTenor,
			
			'bulan' => $bulan
		);
		return Response::json($response);

	}

	public function fetchReportNG(Request $request){
		$report = MiddleNgLog::leftJoin('employees', 'employees.employee_id', '=', 'middle_ng_logs.employee_id')
		->leftJoin('materials', 'materials.material_number', '=', 'middle_ng_logs.material_number');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$report = $report->where(db::raw('date_format(middle_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$report = $report->where(db::raw('date_format(middle_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if($request->get('location') != null){
			$report = $report->whereIn('middle_ng_logs.location', $request->get('location'));
		}

		$report = $report->select('middle_ng_logs.employee_id', 'employees.name', 'middle_ng_logs.tag', 'middle_ng_logs.material_number', 'materials.material_description', 'materials.key', 'materials.model', 'materials.surface', 'middle_ng_logs.ng_name', 'middle_ng_logs.quantity', 'middle_ng_logs.location', 'middle_ng_logs.created_at')->get();

		return DataTables::of($report)->make(true);
	}

	public function fetchReportProductionResult(Request $request){
		$report = MiddleLog::leftJoin('employees', 'employees.employee_id', '=', 'middle_logs.employee_id')
		->leftJoin('materials', 'materials.material_number', '=', 'middle_logs.material_number');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$report = $report->where(db::raw('date_format(middle_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$report = $report->where(db::raw('date_format(middle_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if($request->get('location') != null){
			$report = $report->whereIn('middle_logs.location', $request->get('location'));
		}

		$report = $report->select('middle_logs.employee_id', 'employees.name', 'middle_logs.tag', 'middle_logs.material_number', 'materials.material_description', 'materials.key', 'materials.model', 'materials.surface', 'middle_logs.quantity', 'middle_logs.location', 'middle_logs.created_at')->get();

		return DataTables::of($report)->make(true);
	}

	public function fetchBarrelLog(Request $request){

		$barrel_logs = BarrelLog::leftJoin('materials', 'materials.material_number', '=', 'barrel_logs.material');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$barrel_logs = $barrel_logs->where(db::raw('date_format(barrel_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$barrel_logs = $barrel_logs->where(db::raw('date_format(barrel_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if($request->get('code') != null){
			$barrel_logs = $barrel_logs->whereIn('materials.origin_group_code', $request->get('code'));
		}

		$barrel_logs = $barrel_logs->select('barrel_logs.tag', 'barrel_logs.material','materials.material_description', 'barrel_logs.qty', 'barrel_logs.status', 'barrel_logs.created_at')->get();

		return DataTables::of($barrel_logs)->make(true);
		// return Response::json($barrel_logs);


	}

	public function fetchBuffingBoard(Request $request){
		$tmp = [];

		$work_stations = db::connection('digital_kanban')->table('dev_list')
		->whereRaw('SPLIT_STRING(dev_name, "-", 1) = "SXKEY"')
		->where('enable_antrian', '!=', 'RPR')
		->orderBy('dev_name', 'asc')
		->get();

		$boards = array();
		foreach ($work_stations as $work_station) {
			$employee = Employee::where('employee_id', '=', $work_station->dev_operator_id)->select('name')->first();
			if($employee != null){
				$employee_name = $employee->name;
			}
			else{
				$employee_name = "NotFound";
			}

			if($work_station->dev_selesai_detected == 1){
				$selesai = $work_station->dev_selesai_num;
				array_push($tmp, $work_station->dev_selesai_num);
			}
			else{
				$selesai = "";
			}

			$queues = db::connection('digital_kanban')->table('buffing_queues')
			// ->where('rack', '=', $work_station->dev_name)
			->whereRaw('rack = concat(SPLIT_STRING("'.$work_station->dev_name.'", "-", 1), "-",SPLIT_STRING("'.$work_station->dev_name.'", "-", 2))')
			->orderBy('created_at', 'asc')
			->limit(10)
			->get();

			$lists = array();
			for ($i=0; $i < 10 ; $i++) {
				if(isset($queues[$i])){
					array_push($lists, $queues[$i]->material_num);
					array_push($tmp, $queues[$i]->material_num);
				}
				else{
					array_push($lists, "");
				}
			}

			array_push($tmp, $work_station->dev_sedang_num);
			array_push($tmp, $work_station->dev_akan_num);

			array_push($boards, [
				'ws' => $work_station->dev_name,
				'employee_id' => $work_station->dev_operator_id,
				'employee_name' => $employee_name,
				'sedang' => $work_station->dev_sedang_num,
				'akan' => $work_station->dev_akan_num,
				'selesai' => $selesai,
				'queue_1' => $lists[0],
				'queue_2' => $lists[1],
				'queue_3' => $lists[2],
				'queue_4' => $lists[3],
				'queue_5' => $lists[4],
				'queue_6' => $lists[5],
				'queue_7' => $lists[6],
				'queue_8' => $lists[7],
				'queue_9' => $lists[8],
				'queue_10' => $lists[9]
			]);
		}

		$tmp = array_unique($tmp);

		$materials = Material::where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->whereIn('materials.material_number', $tmp)
		->select('material_number',db::raw("concat(material_number,'<br>',model,'_',`key`) as isi"))
		->get();


		for ($i=0; $i < count($boards); $i++) {
			foreach ($materials as $material) {
				if ($boards[$i]['sedang'] == $material->material_number) {
					$boards[$i]['sedang'] = $material->isi;
				}

				if ($boards[$i]['akan'] == $material->material_number) {
					$boards[$i]['akan'] = $material->isi;
				}

				if ($boards[$i]['queue_1'] == $material->material_number) {
					$boards[$i]['queue_1'] = $material->isi;
				}

				if ($boards[$i]['queue_2'] == $material->material_number) {
					$boards[$i]['queue_2'] = $material->isi;
				}

				if ($boards[$i]['queue_3'] == $material->material_number) {
					$boards[$i]['queue_3'] = $material->isi;
				}

				if ($boards[$i]['queue_4'] == $material->material_number) {
					$boards[$i]['queue_4'] = $material->isi;
				}

				if ($boards[$i]['queue_5'] == $material->material_number) {
					$boards[$i]['queue_5'] = $material->isi;
				}

				if ($boards[$i]['queue_6'] == $material->material_number) {
					$boards[$i]['queue_6'] = $material->isi;
				}

				if ($boards[$i]['queue_7'] == $material->material_number) {
					$boards[$i]['queue_7'] = $material->isi;
				}

				if ($boards[$i]['queue_8'] == $material->material_number) {
					$boards[$i]['queue_8'] = $material->isi;
				}

				if ($boards[$i]['queue_9'] == $material->material_number) {
					$boards[$i]['queue_9'] = $material->isi;
				}

				if ($boards[$i]['queue_10'] == $material->material_number) {
					$boards[$i]['queue_10'] = $material->isi;
				}

				if ($boards[$i]['selesai'] == $material->material_number) {
					$boards[$i]['selesai'] = $material->isi;
				}
			}
		}

		$response = array(
			'status' => true,
			'boards' => $boards,
			'materials' => $materials
		);
		return Response::json($response);
	}

	public function fetchBuffingReverse(Request $request)
	{
		$tmp = [];
		$boards = [];

		$work_stations = db::connection('digital_kanban')->table('dev_list')
		->select('idx','dev_name', 'dev_operator_id', 'dev_akan_num', 'dev_sedang_num', 'dev_selesai_num','dev_selesai_detected')
		->whereRaw('SPLIT_STRING(dev_name, "-", 1) = "SXKEY"')
		->where('enable_antrian', '!=', 'RPR')
		->orderBy('dev_name', 'asc')
		->get();

		foreach ($work_stations as $ws) {
			$employee = Employee::where('employee_id', '=', $ws->dev_operator_id)->select('name')->first();

			if($employee != null){
				$employee_name = $employee->name;
			}
			else{
				$employee_name = "NotFound";
			}

			if($ws->dev_selesai_detected == 1){
				$selesai = $ws->dev_selesai_num;
				array_push($tmp, $ws->dev_selesai_num);
			}
			else{
				$selesai = "";
			}

			$queues_q = db::connection('digital_kanban')->table('buffing_queues')
			// ->where('rack', '=', $work_station->dev_name)
			->whereRaw('rack = concat(SPLIT_STRING("'.$ws->dev_name.'", "-", 1), "-",SPLIT_STRING("'.$ws->dev_name.'", "-", 2))')
			->orderBy('created_at', 'asc')
			->get();

			$queues = array();
			for ($i=0; $i < count($queues_q); $i++) {
				if(isset($queues_q[$i])){
					array_push($queues, $queues_q[$i]->material_num);
					array_push($tmp, $queues_q[$i]->material_num);
				}
				else{
					array_push($queues, "");
				}
			}

			array_push($tmp, $ws->dev_sedang_num);
			array_push($tmp, $ws->dev_akan_num);

			array_push($boards, [
				'id' => $ws->idx,
				'ws' => $ws->dev_name,
				'employee_id' => $ws->dev_operator_id,
				'employee_name' => $employee_name,
				'sedang' => $ws->dev_sedang_num,
				'akan' => $ws->dev_akan_num,
				'selesai' => $selesai,
				'queues' => $queues,
			]);

		}

		$tmp = array_unique($tmp);

		$materials = Material::where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->whereIn('materials.material_number', $tmp)
		->select('material_number',db::raw("concat(material_number,'<br>',model,'_',`key`) as isi"))
		->get();

		$response = array(
			'status' => true,
			'boards' => $boards,
			'materials' => $materials
		);
		return Response::json($response);
	}

	public function fetchMiddleBarrelReprint(Request $request){
		if($request->get('surface') == 'LCQ'){
			$barrels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
			->where('materials.category', '=', 'WIP')
			->where('materials.mrpc', '=', $request->get('mrpc'))
			->whereIn('materials.hpl', $request->get('hpl'))
			->where('materials.surface', 'like', '%LCQ')
			->select('barrels.tag', 'barrels.remark', 'materials.key', 'materials.model', 'materials.surface', 'barrels.machine')
			->get();
		}
		if($request->get('surface') == 'PLT'){
			$barrels = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
			->where('materials.category', '=', 'WIP')
			->where('materials.mrpc', '=', $request->get('mrpc'))
			->whereIn('materials.hpl', $request->get('hpl'))
			->where('materials.surface', 'like', '%PLT')
			->where('middle_inventories.location', '=', 'barrel')
			->select('middle_inventories.tag', 'materials.key', 'materials.model', 'materials.surface', 'middle_inventories.quantity')
			->get();
		}

		$response = array(
			'status' => true,
			'barrels' => $barrels,
		);
		return Response::json($response);

	}

	public function scanMiddleOperator(Request $request){
		$employee = db::table('employees')->where('employee_id', 'like', '%'.$request->get('employee_id').'%')->first();

		if(count($employee) > 0 ){
			$response = array(
				'status' => true,
				'message' => 'Logged In',
				'employee' => $employee,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Employee ID Invalid',
			);
			return Response::json($response);
		}
	}

	public function scanMiddleOperatorKensa(Request $request){
		$employee = db::table('employees')->where('tag', '=', $request->get('employee_id'))->first();

		if(count($employee) > 0 ){
			$response = array(
				'status' => true,
				'message' => 'Logged In',
				'employee' => $employee,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Employee Tag Invalid',
			);
			return Response::json($response);
		}
	}

	public function printMiddleBarrelReprint(Request $request){

		if($request->get('id') == 'PLT'){
			if($request->get('tagMaterial') == null){
				$response = array(
					'status' => false,
					'message' => 'No material selected',
				);
				return Response::json($response);
			}

			$middle_inventories = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
			->where('middle_inventories.location', '=', 'barrel');
			if($request->get('tagMaterial') != null){
				$middle_inventories = $middle_inventories->whereIn('middle_inventories.tag', $request->get('tagMaterial'));
			}

			$middle_inventories = $middle_inventories->select('middle_inventories.tag', 'middle_inventories.material_number', 'middle_inventories.quantity', 'materials.key', 'materials.model', 'materials.surface', 'materials.material_description', 'materials.hpl')->get();

			if($middle_inventories == null){
				$response = array(
					'status' => false,
					'message' => 'Material not found in barrel location.',
				);
				return Response::json($response);
			}

			foreach ($middle_inventories as $middle_inventory) {

				self::printSlipMaterial('PLATING', $middle_inventory->hpl, 'Reprint', $middle_inventory->model, $middle_inventory->key, $middle_inventory->surface, $middle_inventory->tag, $middle_inventory->material_number, $middle_inventory->material_description, $middle_inventory->quantity, '-', '-', '-');

			}

			$response = array(
				'status' => true,
				'message' => 'Qr code PLATING has been printed.',
				'tes' => $middle_inventories,
			);
			return Response::json($response);
		}
		else{
			if($request->get('tagMaterial') == null && $request->get('tagMachine') == null){
				$response = array(
					'status' => false,
					'message' => 'No material or machine selected',
				);
				return Response::json($response);
			}

			$barrels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number');
			if($request->get('tagMaterial') != null){
				$barrels = $barrels->whereIn('barrels.tag', $request->get('tagMaterial'));
			}
			if($request->get('tagMachine') != null){
				$barrels = $barrels->whereIn('barrels.remark', $request->get('tagMachine'));
			}

			$barrels = $barrels->select('barrels.tag', 'barrels.material_number', 'barrels.qty', 'materials.key', 'materials.model', 'materials.surface', 'materials.material_description', 'barrels.machine', 'barrels.jig', 'barrels.remark', 'materials.hpl')->get();

			if ($barrels == null) {
				$response = array(
					'status' => false,
					'message' => 'Material not found in barrel location.',
				);
				return Response::json($response);
			}

			if($request->get('id') == 'material'){
				foreach ($barrels as $barrel) {
					if($barrel->machine == 'FLANEL'){
						self::printSlipMaterial('LACQUERING', $barrel->hpl, 'Reprint', $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->quantity, '-', 'FLANEL', '-');
					}
					else{
						self::printSlipMaterial('LACQUERING', $barrel->hpl, 'Reprint', $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->quantity, $barrel->remark, $barrel->machine, $barrel->jig);
					}
				}
				$response = array(
					'status' => true,
					'message' => 'Qr code has been printed.',
				);
				return Response::json($response);
			}
			if($request->get('id') == 'machine'){
				$m = array();
				foreach ($barrels as $barrel) {
					if(!in_array($barrel->remark, $m) && $barrel->remark != 'FLANEL'){
						self::printSlipMachine($barrel->machine, $barrel->remark);
						array_push($m, $barrel->remark);
					}
				}
				$response = array(
					'status' => true,
					'message' => 'Qr code has been printed.',
					'tes' => $barrels,
				);
				return Response::json($response);
			}
		}
	}

	public function fetchMiddleBarrelBoard(Request $request){

		$now = date('Y-m-d');
		$barrel_board =  DB::table('barrel_logs')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_logs.material')
		->where(DB::raw('DATE_FORMAT(barrel_logs.created_at,"%Y-%m-%d")'), '=', $now)
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->select('materials.hpl', 'barrel_logs.status', db::raw('sum(barrel_logs.qty) as qty'), db::raw('IF(TIME(barrel_logs.created_at) > "00:00:00" and TIME(barrel_logs.created_at) < "07:00:00", 3, IF(TIME(barrel_logs.created_at) > "07:00:00" and TIME(barrel_logs.created_at) < "16:00:00", 1, IF(TIME(barrel_logs.created_at) > "16:00:00" and TIME(barrel_logs.created_at) < "23:59:59", 2, "ERROR"))) AS shift'))
		->groupBy('materials.hpl', 'barrel_logs.status', 'barrel_logs.created_at')
		->get();

		$barrel_queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->select('materials.model', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_queues.created_at', db::raw('coalesce(barrel_queues.remark, "-") as remark'))
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		$flanels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
		->where('barrels.machine', '=', 'FLANEL')
		->select('barrels.tag', 'materials.material_number', 'materials.model', 'materials.key', 'barrels.created_at')
		->orderBy('barrels.created_at', 'asc')
		->get();

		$response = array(
			'status' => true,
			'barrel_board' => $barrel_board,
			'barrel_queues' => $barrel_queues,
			'flanels' => $flanels,
		);
		return Response::json($response);
	}

	public function fetchMiddleBarrel(Request $request){
		if($request->get('surface') == 'LCQ'){
			$queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
			->leftJoin('barrel_jigs', function($join)
			{
				$join->on('barrel_jigs.key', '=', 'materials.key');
				$join->on('barrel_jigs.hpl','=', 'materials.hpl');
			})
			->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
			->where('materials.category', '=', 'WIP')
			->where('materials.mrpc', '=', $request->get('mrpc'))
			->whereIn('materials.hpl', $request->get('hpl'))
			->where('materials.surface', 'not like', '%PLT')
			->select('barrel_queues.tag', 'barrel_queues.created_at', 'materials.model', 'materials.hpl', 'materials.material_number', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_jigs.spring', 'bom_components.material_child', 'bom_components.material_description', 'barrel_jigs.lot')
			->orderBy('barrel_queues.created_at', 'asc')
			->get();
			if($queues[0]->spring == 'FLANEL'){
				$code = 'FLANEL';
			}
			else{
				$code = 'BARREL';
			}
		}
		elseif($request->get('surface') == 'PLT'){
			$queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
			->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
			->where('materials.category', '=', 'WIP')
			->where('materials.mrpc', '=', $request->get('mrpc'))
			->whereIn('materials.hpl', $request->get('hpl'))
			->where('materials.surface', 'like', '%PLT')
			->select('barrel_queues.tag', 'materials.key', 'materials.model', 'materials.surface', 'bom_components.material_child', 'bom_components.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
			->orderBy('barrel_queues.created_at', 'asc')
			// ->limit(60)
			->get();
			$code = 'PLT';
		}
		elseif($request->get('surface') == 'FLANEL'){
			$queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
			->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
			->where('materials.category', '=', 'WIP')
			->where('materials.mrpc', '=', $request->get('mrpc'))
			->whereIn('materials.hpl', $request->get('hpl'))
			->where('materials.surface', 'not like', '%PLT')
			->select('barrel_queues.tag', 'materials.key', 'materials.model', 'materials.surface', 'bom_components.material_child', 'bom_components.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
			->orderBy('barrel_queues.created_at', 'asc')
			// ->limit(30)
			->get();
			$code = 'FLANEL';
		}

		$response = array(
			'status' => true,
			'code' => $code,
			'queues' => $queues,
		);
		return Response::json($response);
	}

	public function fetchMiddleBarrelMachine(Request $request){
		$queue = db::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.hpl', '=', $request->get('hpl'))
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->orderBy('barrel_queues.created_at', 'asc')
		->first();

		if($queue != null){
			if(strpos($queue->surface, 'PLT') !== false){
				$no_machine = 'Direct To Plating';
				$capacity = '0';
			}
			else{
				$machine = db::table('barrel_machines')->where('status', '=', 'idle')->orderBy('updated_at', 'asc')->first();

				if($machine != null){
					$no_machine = $machine->machine;
					$capacity = db::table('barrel_jigs')->where('hpl', '=', $request->get('hpl'))->count();
				}
				else{
					$no_machine = 'FULL';
					$capacity = '0';
				}
			}

			$response = array(
				'status' => true,
				'no_machine' => $no_machine,
				'capacity' => $capacity
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'no_machine' => 'No Queue',
				'capacity' => 0
			);
			return Response::json($response);
		}

	}

	public function printMiddleBarrel(Request $request){
		$id = Auth::id();
		$tags = $request->get('tag');

		$count = 0;
		foreach ($tags as $tag) {
			$check = BarrelQueue::where('barrel_queues.tag', '=', $tag[0])->first();
			$count += 1;

			if($check == null){
				$response = array(
					'status' => false,
					'message' => 'Selected tag not in queue, please refresh page',
				);
				return Response::json($response);
			}
		}

		$check2 = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')->count();

		if($check2 >= 64 && ($check2-$count) < 64 ){
			self::sendEmailMinQueue();
		}

		if($request->get('surface') == 'LCQ'){
			if($request->get('code') == 'FLANEL'){
				try{
					foreach ($tags as $tag) {
						$barrel = new Barrel([
							'machine' => $request->get('code'),
							'jig' => 0,
							'tag' => $tag[0],
							'status' => $request->get('code'),
							'remark' => $request->get('code'),
							'created_by' => $id
						]);
						$barrel->save();
					}
					foreach ($tags as $tag) {
						DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '".$tag[0]."'");
					}
					foreach ($tags as $tag) {
						$barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
						->where('barrels.tag', '=', $tag[0])
						->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty')
						->first();

						$group = explode('-', $barrel->key);
						$rack = 'SXKEY-'.$group[0];

						// IF JIKA BUKAN 82 dan KUNCI 'C' SAJA
						if($group[0] == 'C' && preg_match("/82/", $barrel->model) != TRUE){

							$buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

							$buffing_queue = DB::connection('digital_kanban')
							->table('buffing_queues')
							->insert([
								'rack' => $rack,
								'material_num' => $buffing->material_child,
								'created_by' => $id,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
								'material_qty' => $barrel->qty,
								'material_tag_id' => $tag[0]
							]);
						}

						MiddleInventory::firstOrcreate([
							'tag' => $tag[0]
						],
						[
							'tag' => $tag[0],
							'material_number' => $barrel->material_number,
							'quantity' => $barrel->quantity,
							'location' => 'barrel',
						]);

						self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->quantity, '-', $request->get('code'), '-');
					}
					foreach ($tags as $tag) {
						BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
					}

					$response = array(
						'status' => true,
						'message' => 'ID Slip for FLANEL has been printed',
					);
					return Response::json($response);
				}
				catch(\Exception $e){
					$error_log = new ErrorLog([
						'error_message' => $e->getMessage(),
						'created_by' => $id
					]);
					$error_log->save();
				}
			}
			else{
				$code_generator = CodeGenerator::where('note','=','barrel_machine')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$qr_machine = $code_generator->prefix . $number;
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				try{

					foreach ($tags as $tag) {
						$barrel = new Barrel([
							'machine' => $request->get('no_machine'),
							'jig' => $tag[1],
							'tag' => $tag[0],
							'status' => 'racking',
							'remark' => $qr_machine,
							'created_by' => $id
						]);
						$barrel->save();
					}

					foreach ($tags as $tag) {
						DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '".$tag[0]."'");
					}

					foreach ($tags as $tag) {
						$barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
						->where('barrels.tag', '=', $tag[0])
						->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty', 'barrels.remark', 'barrels.machine', 'barrels.jig')
						->first();

						$group = explode('-', $barrel->key);
						$rack = 'SXKEY-'.$group[0];

						if($group[0] == 'C' && preg_match("/82/", $barrel->model) != TRUE){

							$buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

							$buffing_queue = DB::connection('digital_kanban')
							->table('buffing_queues')
							->insert([
								'rack' => $rack,
								'material_num' => $buffing->material_child,
								'created_by' => $id,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
								'material_qty' => $barrel->qty,
								'material_tag_id' => $tag[0]
							]);

						}

						MiddleInventory::firstOrcreate([
							'tag' => $tag[0]
						],
						[
							'tag' => $tag[0],
							'material_number' => $barrel->material_number,
							'quantity' => $barrel->qty,
							'location' => 'barrel',
						]);

						self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->qty, $barrel->remark, $barrel->machine, $barrel->jig);
					}

					foreach ($tags as $tag) {
						BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
					}

					$response = array(
						'status' => true,
						'message' => 'ID Slip for LACUQERING has been printed',
					);
					return Response::json($response);

				}
				catch(\Exception $e){
					$error_log = new ErrorLog([
						'error_message' => $e->getMessage(),
						'created_by' => $id
					]);
					$error_log->save();
				}
			}
		}
		elseif($request->get('surface') == 'PLT'){
			try{
				foreach ($tags as $tag) {
					$barrel = BarrelQueue::leftJoin('materials', 'materials.material_number', 'barrel_queues.material_number')
					->where('barrel_queues.tag', '=', $tag[0])
					->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.model', 'materials.hpl', 'materials.key', 'materials.surface', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
					->first();

					$group = explode('-', $barrel->key);
					$rack = 'SXKEY-'.$group[0];

					if($group[0] == 'C' && preg_match("/82/", $barrel->model) != TRUE){

						$buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

						$buffing_queue = DB::connection('digital_kanban')
						->table('buffing_queues')
						->insert([
							'rack' => $rack,
							'material_num' => $buffing->material_child,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
							'material_qty' => $barrel->quantity,
							'material_tag_id' => $tag[0]
						]);

					}

					$barrel_log = new BarrelLog([
						'machine' => $request->get('surface'),
						'tag' => $tag[0],
						'material' => $barrel->material_number,
						'qty' => $barrel->quantity,
						'status' => $request->get('surface'),
						'started_at' => date('Y-m-d H:i:s'),
						'created_by' => $id,
					]);
					$barrel_log->save();

					MiddleInventory::firstOrcreate([
						'tag' => $tag[0]
					],
					[
						'tag' => $tag[0],
						'material_number' => $barrel->material_number,
						'quantity' => $barrel->quantity,
						'location' => 'barrel',
					]);

					self::printSlipMaterial('PLATING', $barrel->hpl, $barrel->remark, $barrel->model, $barrel->key, $barrel->surface, $tag[0], $barrel->material_number, $barrel->material_description, $barrel->quantity, '-', '-', '-');
				}
				foreach ($tags as $tag) {
					BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
				}

				$response = array(
					'status' => true,
					'message' => 'ID Slip for PLATING has been printed',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
			}
		}
		elseif($request->get('surface') == 'FLANEL'){
			try{
				foreach ($tags as $tag) {
					$barrel = new Barrel([
						'machine' => $request->get('code'),
						'jig' => 0,
						'tag' => $tag[0],
						'status' => $request->get('code'),
						'remark' => $request->get('code'),
						'created_by' => $id
					]);
					$barrel->save();
				}
				foreach ($tags as $tag) {
					DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '".$tag[0]."'");
				}
				foreach ($tags as $tag) {
					$barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
					->where('barrels.tag', '=', $tag[0])
					->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty')
					->first();

					$group = explode('-', $barrel->key);
					$rack = 'SXKEY-'.$group[0];

					if($group[0] == 'C' && preg_match("/82/", $barrel->model) != TRUE){

						$buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

						$buffing_queue = DB::connection('digital_kanban')
						->table('buffing_queues')
						->insert([
							'rack' => $rack,
							'material_num' => $buffing->material_child,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
							'material_qty' => $barrel->qty,
							'material_tag_id' => $tag[0]
						]);
					}

					MiddleInventory::firstOrcreate([
						'tag' => $tag[0]
					],
					[
						'tag' => $tag[0],
						'material_number' => $barrel->material_number,
						'quantity' => $barrel->qty,
						'location' => 'barrel',
					]);

					self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->qty, '-', $request->get('code'), '-');
				}
				foreach ($tags as $tag) {
					BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
				}

				$response = array(
					'status' => true,
					'message' => 'ID Slip for FLANEL has been printed',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
			}
		}
	}

	public function scanMiddleBarrel(Request $request){
		$id = Auth::id();

		if(substr($request->get('qr'),0,3) == 'MCB' || substr($request->get('qr'),0,3) == 'mcb'){
			$barrels = Barrel::where('remark', '=', $request->get('qr'))->get();

			if($barrels->count() > 0){
				$barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->first();
				if($barrel_machine->status == 'idle' && $barrels[0]->status == 'queue'){
					try{
						$update_barrel = Barrel::where('remark', '=', $request->get('qr'))->update([
							'status' => 'running',
							'finish_queue' => date('Y-m-d H:i:s'),
						]);

						$insert_machine_log = new BarrelMachineLog([
							'machine' => $barrels[0]->machine,
							'status' => 'idle',
							'started_at' => date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)),
							'created_by' => $id,
						]);
						$insert_machine_log->save();

						$update_barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->update([
							'status' => 'running',
						]);

						$response = array(
							'status' => true,
							'message' => 'Machine running.',
						);
						return Response::json($response);

					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}
				if($barrel_machine->status == 'running' && $barrels[0]->status == 'running'){
					try{
						$insert_machine_log = new BarrelMachineLog([
							'machine' => $barrels[0]->machine,
							'status' => 'running',
							'started_at' => date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)),
							'created_by' => $id,
						]);
						$insert_machine_log->save();

						foreach ($barrels as $barrel) {
							$insert_log = [
								'machine' => $barrel->machine,
								'tag' => $barrel->tag,
								'material' => $barrel->material_number,
								'qty' => $barrel->qty,
								'status' => 'reset',
								'started_at' => date('Y-m-d H:i:s', strtotime($barrel->finish_queue)),
								'created_by' => $id,
							];

							$barrel_log = new BarrelLog($insert_log);

							$delete_barrel = Barrel::where('tag', '=', $barrel->tag)->where('machine', '=', $barrel->machine)->forceDelete();
							$barrel_log->save();
						}

						$update_barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->update([
							'status' => 'idle',
						]);

						$response = array(
							'status' => true,
							'message' => 'Material has been resetted',
						);
						return Response::json($response);

					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}
				else{
					$response = array(
						'status' => false,
						'message' => 'Machine status invalid.',
					);
					return Response::json($response);
				}
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Qr code cycle is done.',
				);
				return Response::json($response);
			}
		}
		else{
			$barrel = Barrel::where('tag', '=', $request->get('qr'))->first();

			if($barrel == null){
				$response = array(
					'status' => false,
					'message' => 'Qr code not registered.',
				);
				return Response::json($response);
			}

			if($barrel->status == 'racking'){
				try{
					$barrel->finish_racking = date('Y-m-d H:i:s');
					$barrel->status = 'queue';

					$insert_log = [
						'machine' => $barrel->machine,
						'tag' => $barrel->tag,
						'material' => $barrel->material_number,
						'qty' => $barrel->qty,
						'status' => 'set',
						'started_at' => date('Y-m-d H:i:s', strtotime($barrel->created_at)),
						'created_by' => $id,
					];

					$barrel_log = new BarrelLog($insert_log);

					DB::transaction(function() use ($barrel, $barrel_log){
						$barrel->save();
						$barrel_log->save();
					});

					$check_barrels = Barrel::where('remark', '=', $barrel->remark)->where('status', '<>', 'queue')->get();

					if($check_barrels->count() == 0){

						self::printSlipMachine($barrel->machine, $barrel->remark);

						$response = array(
							'status' => true,
							'message' => 'All material has been racked, printing machine label.',
						);
						return Response::json($response);
					}

					$response = array(
						'status' => true,
						'message' => 'Material has been racked',
					);
					return Response::json($response);

				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}
			elseif($barrel->status == 'FLANEL'){
				try{
					$insert_log = [
						'machine' => 'FLANEL',
						'tag' => $barrel->tag,
						'material' => $barrel->material_number,
						'qty' => $barrel->qty,
						'status' => 'reset',
						'started_at' => date('Y-m-d H:i:s', strtotime($barrel->created_at)),
						'created_by' => $id,
					];

					$barrel_log = new BarrelLog($insert_log);

					$barrel_log->save();
					$delete_barrel = Barrel::where('tag', '=', $barrel->tag)->where('machine', '=', 'FLANEL')->forceDelete();

					$response = array(
						'status' => true,
						'message' => 'Material has been resetted',
					);
					return Response::json($response);
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'QR code status invalid.',
				);
				return Response::json($response);
			}
		}
	}

	public function fetchMiddleKensa(Request $request){

		$emp = $request->get('employee_id');

		$result = DB::select("select SUM(quantity) as qty, hpl from
			(select material_number, sum(quantity) as quantity from middle_logs where employee_id = '".$emp."' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d')."' and location = '".$request->get('location')."' group by material_number) as base
			left join materials on materials.material_number = base.material_number
			group by hpl");

		$ng = DB::select("select SUM(quantity) as qty, hpl from
			(select material_number, sum(quantity) as quantity from middle_ng_logs where employee_id = '".$emp."' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".date('Y-m-d')."' and location = '".$request->get('location')."' group by material_number) as base
			left join materials on materials.material_number = base.material_number
			group by hpl");

		$response = array(
			'status' => true,
			'result' => $result,
			'ng' => $ng,
		);
		return Response::json($response);
	}

	public function ScanMiddleKensa(Request $request){
		$id = Auth::id();
		$started_at = date('Y-m-d H:i:s');

		$middle_inventory = MiddleInventory::where('tag', '=', $request->get('tag'))
		->leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
		->select('materials.model', 'materials.key', 'materials.surface', 'middle_inventories.material_number', 'middle_inventories.quantity', 'middle_inventories.tag')
		->first();

		if(count($middle_inventory) > 0){
			if($request->get('loc') == 'lcq-incoming' && strpos($middle_inventory->surface, 'plt') !== false ){
				$response = array(
					'status' => false,
					'message' => 'ID slip location for PLATING, please check ID slip.',
				);
				return Response::json($response);
			}

			$response = array(
				'status' => true,
				'message' => 'ID slip found.',
				'middle_inventory' => $middle_inventory,
				'started_at' => $started_at,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'ID slip not found.',
			);
			return Response::json($response);
		}
	}

	public function inputMiddleKensa(Request $request){

		if($request->get('ng')){
			foreach ($request->get('ng') as $ng) {
				$middle_ng_log = new MiddleNgLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'ng_name' => $ng[0],
					'quantity' => $ng[1],
					'location' => $request->get('loc'),
					'started_at' => $request->get('started_at'),
				]);

				try{
					$middle_ng_log->save();
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}
			$response = array(
				'status' => true,
				'message' => 'NG has been recorded.',
			);
			return Response::json($response);
		}

		if(!$request->get('ng')){
			$middle_inventory = MiddleInventory::where('tag', '=', $request->get('tag'))->first();
			$middle_inventory->location = $request->get('loc');
			$middle_log = new MiddleLog([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'started_at' => $request->get('started_at'),
			]);

			try{
				DB::transaction(function() use ($middle_log, $middle_inventory){
					$middle_log->save();
					$middle_inventory->save();
				});

				$response = array(
					'status' => true,
					'message' => 'Input material successfull.',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
	}

	public function fetchProcessBarrelMachine()
	{
		$data = DB::table('barrel_machines')->select('machine', 'status','updated_at',DB::raw('now() as now'),DB::raw('TIMEDIFF(now(),updated_at) as duration'),DB::raw('ROUND(TIME_TO_SEC(TIMEDIFF(now(),updated_at)) / 60 / 60, 1) as hour'))->get();

		$data2 = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.key' ,'barrels.machine',DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
		->get();

		$response = array(
			'status' => true,
			'datas' => $data,
			'contents' => $data2,
		);
		return Response::json($response);
	}

	public function fetchProcessBarrelMachineContent()
	{
		$data = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.key' ,'barrels.machine',DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
		->get();

		$response = array(
			'status' => true,
			'contents' => $data,
		);
		return Response::json($response);
	}

	public function fetchProcessBarrel()
	{
		$data = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.machine', 'barrels.jig', 'barrels.key', DB::raw('SUM(qty) as qty'), 'barrels.status', 'materials.model',  DB::raw('GROUP_CONCAT(barrels.tag) as tag'))
		->groupBy('barrels.machine', 'barrels.jig', 'barrels.key','barrels.status','materials.model')
		->orderBy('remark','asc')
		->orderBy('jig','asc')
		->get();

		$barrel_machine = DB::table('barrel_machines')
		->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'),DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'), DB::raw('hour(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as jam_cd'), DB::raw('minute(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as menit_cd'), DB::raw('second(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as detik_cd'))
		->get();

		$response = array(
			'status' => true,
			'datas' => $data,
			'machine_stat' => $barrel_machine
		);
		return Response::json($response);
	}

	public function fetchMachine()
	{
		$barrel_machine = DB::table('barrel_machines')
		->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'),DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'))
		->get();

		$response = array(
			'status' => true,
			'machine_stat' => $barrel_machine
		);
		return Response::json($response);
	}

	public function postProcessMiddleReturn(Request $request)
	{
		$tag = $request->get('qr');
		$barrel_inventories = DB::table('middle_inventories')
		->select('tag', 'material_number','location','quantity')
		->where('tag','=', $tag)
		->get();

		$created = DB::table('barrel_queues')
		->select(DB::raw("created_at - INTERVAL 5 SECOND as created_at"))
		->orderBy('created_at','asc')
		->limit(1)
		->get();

		DB::table('barrel_queues')->insert([
			'tag' => $request->get('qr'),
			'material_number' => $barrel_inventories[0]->material_number,
			'remark' => "return+".$barrel_inventories[0]->location,
			'quantity' => $barrel_inventories[0]->quantity,
			'created_at' => $created[0]->created_at,
			'updated_at' => $created[0]->created_at
		]);

		DB::table('middle_inventories')->where('tag', '=', $tag)->delete();

		try{
			DB::table('barrels')->where('tag', '=', $tag)->delete();
		}
		catch(\Exception $e){

		}

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function fetchProcessMiddleReturn(Request $request)
	{
		$barrel_queues = DB::table('barrel_queues')->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->select('barrel_queues.tag', 'materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at', 'barrel_queues.remark')
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->where('barrel_queues.remark','LIKE','return%')
		->orderBy('created_at','asc')
		->get();

		$response = array(
			'status' => true,
			'datas' => $barrel_queues,
		);
		return Response::json($response);
	}

	public function postReturnInventory(Request $request)
	{
		$tag = $request->get('tag');
		$inventory = new MiddleInventory([
			'tag' => $tag,
			'material_number' => $request->get('material'),
			'location' => $request->get('location'),
			'quantity' => $request->get('quantity')
		]);

		$inventory->save();

		DB::table('barrel_queues')->where('tag', '=', $tag)->delete();

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function fetchBarrelAdjustment()
	{
		$adjust = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->select('barrel_queues.tag', 'barrel_queues.material_number', 'materials.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		return DataTables::of($adjust)
		->addColumn('check', function($adjust){
			return '<input type="checkbox" class="queue" id="'.$adjust->tag.'+'.$adjust->material_number.'+'.$adjust->quantity.'+inactive" onclick="inactive(this)">';
		})
		->rawColumns([ 'check' => 'check'])
		->make(true);
	}

	public function fetchBarrelInactive($id)
	{
		$inactive = BarrelQueueInactive::leftJoin('materials', 'materials.material_number', '=', 'barrel_queue_inactives.material_number')
		->select('barrel_queue_inactives.tag', 'barrel_queue_inactives.material_number', 'materials.material_description', 'barrel_queue_inactives.quantity', 'barrel_queue_inactives.created_at')
		->orderBy('barrel_queue_inactives.created_at', 'asc')
		->get();

		if ($id == "kanban") {
			return DataTables::of($inactive)
			->addColumn('check', function($inactive){
				return '<input type="checkbox" class="aktif" id="'.$inactive->tag.'+'.$inactive->material_number.'+'.$inactive->quantity.'+active" onclick="active(this)">';
			})
			->rawColumns([ 'check' => 'check'])
			->make(true);
		} else {
			return DataTables::of($inactive)
			->make(true);
		}
	}

	public function postInactive(Request $request){

		$datas = $request->get('data')[0];

		for ($i=0; $i < count($datas['tag']); $i++) {
			if ($datas['stat'][$i] == 'inactive') {
				DB::table('barrel_queues')->where('tag', '=', $datas['tag'][$i])->delete();

				$inactive = new BarrelQueueInactive([
					'tag' => $datas['tag'][$i],
					'material_number' => $datas['material'][$i],
					'quantity' => $datas['qty'][$i]
				]);
				$inactive->save();
			} else if($datas['stat'][$i] == "active") {
				DB::table('barrel_queue_inactives')->where('tag', '=', $datas['tag'][$i])->delete();

				$active = db::table('barrel_queues')->insert([
					'tag' => $datas['tag'][$i],
					'material_number' => $datas['material'][$i],
					'quantity' => $datas['qty'][$i],
					'remark' => 'up',
					'created_at' => $datas['created_at'][$i],
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}
		}

		$response = array(
			'status' => true,
			// 'tes' => $datas['tag']
		);
		return Response::json($response);
	}

	public function fetchBarrelBoardDetails(Request $request)
	{
		$sif = $request->get('shift');
		$now = date('Y-m-d');

		if ($sif == 1) {
			$awal = '07:00:00';
			$akhir = '16:00:00';
		} elseif($sif == 2) {
			$awal = '16:00:00';
			$akhir = '23:59:59';
		}  elseif($sif == 3) {
			$awal = '00:00:00';
			$akhir = '07:00:00';
		}

		$detailPerolehan = db::table('materials')
		->leftJoin('barrel_logs','materials.material_number','=','barrel_logs.material')
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->where('materials.surface','like','%'.$request->get('surface'))
		->where('materials.hpl', $request->get('key'))
		->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%Y-%m-%d')"),"=", $now)
		->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%H:%i:%s')"), '>=', $awal)
		->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%H:%i:%s')"), '<', $akhir)
		->select('materials.model','materials.key', db::raw("SUM(IF(barrel_logs.`status`='set',qty,0)) as `set`"), db::raw("SUM(IF(barrel_logs.`status`='reset',qty,0)) as `reset`"), db::raw("SUM(IF(barrel_logs.`status`='plt',qty,0)) as `plt`"))
		->groupBy('materials.model','materials.key')
		->get();

		$response = array(
			'status' => true,
			'datas' => $detailPerolehan
		);
		return Response::json($response);
	}

	public function CreateInactive(Request $request)
	{
		try {
			$inactive = new BarrelQueueInactive([
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material'),
				'quantity' => $request->get('quantity')
			]);
			$inactive->save();
			$status = true;
		} catch (Exception $e) {
			$status = false;
		}


		$response = array(
			'status' => $status
		);
		return Response::json($response);
	}

	public function importInactive(Request $request)
	{
		if($request->hasFile('inactive_material')){
			$file = $request->file('inactive_material');

			$data = file_get_contents($file);

			$rows = explode("\r\n", $data);
			foreach ($rows as $row)
			{
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					$inactive = new BarrelQueueInactive([
						'tag' => $row[0],
						'material_number' => $row[1],
						'quantity' => $row[2]
					]);

					$inactive->save();
				}
			}
			return redirect('/index/middle/barrel_adjustment')->with('status', 'New Inactive materials has been imported.')->with('page', 'Middle Process');
		}
		else
		{
			return redirect('/index/middle/barrel_adjustment')->with('error', 'Please select a file.')->with('page', 'Middle Process');
		}
	}

	public function fetchWIP()
	{
		$adjust = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
		->select('middle_inventories.tag', 'middle_inventories.material_number', 'materials.material_description', 'middle_inventories.quantity', 'middle_inventories.created_at')
		->orderBy('middle_inventories.created_at', 'desc')
		->get();

		return DataTables::of($adjust)
		->addColumn('check', function($adjust){
			return '<input type="checkbox" class="queue" id="'.$adjust->tag.'+'.$adjust->material_number.'+'.$adjust->quantity.'+inactive" onclick="inactive(this)">';
		})
		->rawColumns([ 'check' => 'check'])
		->make(true);
	}

	public function postInactiveWIP(Request $request){

		$datas = $request->get('data')[0];

		for ($i=0; $i < count($datas['tag']); $i++) {
			DB::table('middle_inventories')->where('tag', '=', $datas['tag'][$i])->delete();

			$inactive = new BarrelQueueInactive([
				'tag' => $datas['tag'][$i],
				'material_number' => $datas['material'][$i],
				'quantity' => $datas['qty'][$i]
			]);
			$inactive->save();
		}

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function sendEmailMinQueue(){
		$mail_to = db::table('send_emails')
		->where('remark', '=', 'middle')
		->WhereNull('deleted_at')
		->orWhere('remark', '=', 'superman')
		->WhereNull('deleted_at')
		->select('email')
		->get();

		$barrel_queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', 'S51')
		->whereIn('materials.hpl', ['ASKEY', 'TSKEY'])
		->select('barrel_queues.tag', 'barrel_queues.material_number', 'materials.model', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_queues.created_at', db::raw('coalesce(barrel_queues.remark, "-") as remark'))
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		$queues = [
			'barrel_queues' => $barrel_queues,
			'barrel_count' => count($barrel_queues),
		];

		Mail::to($mail_to)->send(new SendEmail($queues, 'min_queue'));
	}

	function printSlipMaterial($location, $hpl, $remark, $model, $key, $surface, $tag, $material_number, $material_description, $quantity, $qr_machine, $machine, $jig){
		if(Auth::user()->role_code == "OP-Barrel-SX"){
			$printer_name = 'Barrel-Printer';
		}
		if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS"){
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(1,1);
		$printer->text('ID SLIP '.date('d-M-Y H:i:s')." ".$remark."\n");
		$printer->setTextSize(4,4);
		$printer->setUnderline(true);
		$printer->text($location."\n\n");
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(5,2);
		if($hpl == 'TSKEY'){
			$printer->setEmphasis(true);
			$printer->setReverseColors(true);
		}
		$printer->text($model." ".$key."\n");
		$printer->text($surface."\n\n");
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($tag."\n\n");
		$printer->initialize();
		$printer->setTextSize(1,1);
		$printer->text("GMC : ".$material_number."                           ".$qr_machine."\n");
		$printer->text("DESC: ".$material_description."\n");
		$printer->text("QTY : ".$quantity." PC(S)                 MACHINE: ".$machine." JIG: ".$jig."\n");
		$printer->cut(Printer::CUT_PARTIAL, 50);
		$printer->close();
	}

	function printSlipMachine($machine, $qr_machine){
		if(Auth::user()->role_code == "OP-Barrel-SX"){
			$printer_name = 'Barrel-Printer';
		}
		if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS"){
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(4,4);
		$printer->text('BARREL'."\n");
		$printer->text("MACHINE_".$machine."\n");
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($qr_machine, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($qr_machine."\n\n");
		$printer->cut();
		$printer->close();
	}

	public function fetchBuffing(Request $request)
	{
		$started_at = date('Y-m-d H:i:s');
		try{
			$tags = db::connection('digital_kanban')->table('buffing_inventories')
			->select('material_num','operator_id',"material_tag_id","material_qty")
			->where('material_tag_id','=', $request->get("tag"))
			->first();

			$material = Material::select("model","key")
			->where("material_number","=", $tags->material_num)
			->first();

			$operator = Employee::select("name")
			->where("employee_id","=",$tags->operator_id)
			->first();

			$buffing_inventory = RfidBuffingInventory::where('material_tag_id', '=', $request->get('tag'))->update([
				'lokasi' => 'BUFFING-KENSA',
			]);

			$response = array(
				'status' => true,
				'datas' => $tags,
				'material' => $material,
				'operator' => $operator,
				'started_at' => $started_at
			);
			return Response::json($response);
		}
		catch (\Exception $e){
			$response = array(
				'status' => false,
				'datas' => $e->getMessage(),
				'message' => 'RFID Invalid'
			);
			return Response::json($response);
		}
	}

	public function inputBuffingKensa(Request $request)
	{
		if($request->get('ng')){
			foreach ($request->get('ng') as $ng) {
				$middle_ng_log = new MiddleNgLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'ng_name' => $ng[0],
					'quantity' => $ng[1],
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'started_at' => $request->get('started_at')
				]);

				try{
					$middle_ng_log->save();
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}
			$response = array(
				'status' => true,
				'message' => 'NG has been recorded.',
			);
			return Response::json($response);
		} else {
			try{
				$buffing_inventory = RfidBuffingInventory::where('material_tag_id', '=', $request->get('tag'))->update([
					'lokasi' => 'BUFFING-AFTER',
				]);

				$middle_log = new MiddleLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'started_at' => $request->get('started_at'),
				]);

				$middle_log->save();

				$response = array(
					'status' => true,
					'message' => 'Input material successfull.',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
	}
}
