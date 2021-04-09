<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\BomComponent;
use App\CodeGenerator;
use App\ContainerSchedule;
use App\DetailChecksheet;
use App\ErrorLog;
use App\Inventory;
use App\KnockDown;
use App\KnockDownDetail;
use App\KnockDownLog;
use App\MasterChecksheet;
use App\Material;
use App\MaterialVolume;
use App\ProductionSchedule;
use App\OriginGroup;
use App\ShipmentSchedule;
use App\StorageLocation;
use App\TransactionCompletion;
use App\TransactionTransfer;
use App\UserActivityLog;
use App\WeeklyCalendar;
use Carbon\Carbon;
use DataTables;
use Response;


class KnockDownController extends Controller{

	public function __construct(){
		$this->middleware('auth');
	}

	public function indexKdTraceability(){
		$origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'asc')->get();
		$materials = DB::table('materials')->where('category', '=', 'KD')->orderBy('material_number', 'asc')->get();
		$destinations = DB::table('destinations')->orderBy('destination_code', 'asc')->get();

		return view('kd.traceability', array(
			'origin_groups' => $origin_groups,
			'materials' => $materials,
			'destinations' => $destinations,
		))->with('page', 'KD Traceability')->with('head', 'Knock Downs');
	}

	public function fetchKdTraceability(Request $request){

		$prodFrom = "";
		$prodTo = "";
		$addmaterial = "";
		$addhpl = "";
		$kdoNumber = "";
		$delivFrom = "";
		$delivTo = "";
		$invoiceNumber = "";
		$addorigin = "";
		$adddestination = "";
		$shipFrom = "";
		$shipTo = "";

		if(strlen($request->get('prodFrom')) > 0){
			$prod_from = date('Y-m-d', strtotime($request->get('prodFrom')));
			$prodFrom = " AND kdd.created_at >= '".$prod_from."'";
		}

		if(strlen($request->get('prodTo')) > 0){
			$prod_to = date('Y-m-d', strtotime($request->get('prodTo')));
			$prodTo = " AND kdd.created_at <= '".$prod_to."'";
		}

		if($request->get('materialNumber') != null){
			$materials = $request->get('materialNumber');
			$materiallength = count($materials);
			$material = "";

			for($x = 0; $x < $materiallength; $x++) {
				$material = $material."'".$materials[$x]."'";
				if($x != $materiallength-1){
					$material = $material.",";
				}
			}
			$addmaterial = "and kdd.material_number in (".$material.") ";
		}

		if($request->get('hpl') != null){
			$hpls = $request->get('hpl');
			$hpllength = count($hpls);
			$hpl = "";

			for($x = 0; $x < $hpllength; $x++) {
				$hpl = $hpl."'".$hpls[$x]."'";
				if($x != $hpllength-1){
					$hpl = $hpl.",";
				}
			}
			$addhpl = "and m.hpl in (".$hpl.") ";

		}

		if(strlen($request->get('kdoNumber')) > 0){
			$kdoNumber = " AND kdd.kd_number = '".$request->get('kdoNumber')."'";
		}

		if(strlen($request->get('delivFrom')) > 0){
			$deliv_from = date('Y-m-d', strtotime($request->get('delivFrom')));
			$delivFrom = " AND del.deliv >= '".$deliv_from."'";

		}

		if(strlen($request->get('delivTo')) > 0){
			$deliv_to = date('Y-m-d', strtotime($request->get('delivTo')));
			$delivTo = " AND del.deliv <= '".$deliv_to."'";
		}

		if(strlen($request->get('invoiceNumber')) > 0){
			$invoiceNumber = " AND kd.invoice_number = '".$request->get('invoiceNumber')."'";
		}

		if($request->get('originGroup') != null){
			$origins = $request->get('originGroup');
			$originlength = count($origins);
			$origin = "";

			for($x = 0; $x < $originlength; $x++) {
				$origin = $origin."'".$origins[$x]."'";
				if($x != $originlength-1){
					$origin = $origin.",";
				}
			}
			$addorigin = "and m.origin_group_code in (".$origin.") ";
		}

		if($request->get('destination') != null){
			$destinations = $request->get('destination');
			$destinationlength = count($destinations);
			$destination = "";

			for($x = 0; $x < $destinationlength; $x++) {
				$destination = $destination."'".$destinations[$x]."'";
				if($x != $destinationlength-1){
					$destination = $destination.",";
				}
			}
			$adddestination= "and ss.destination_code in (".$destination.") ";
		}

		if(strlen($request->get('shipFrom')) > 0){
			$ship_from = date('Y-m-d', strtotime($request->get('shipFrom')));
			$shipFrom = " AND ss.st_date >= '".$ship_from."'";

		}

		if(strlen($request->get('shipTo')) > 0){
			$ship_to = date('Y-m-d', strtotime($request->get('shipTo')));
			$shipTo = " AND ss.st_date <= '".$ship_to."'";
		}

		$knock_down_details = DB::select("SELECT
			kdd.kd_number,
			kdd.material_number,
			m.material_description,
			kdd.quantity,
			kdd.created_at,
			del.deliv,
			ss.st_date,
			ss.bl_date,
			kd.`status`,
			kd.invoice_number,
			ss.sales_order,
			d.destination_shortname 
			FROM
			knock_down_details AS kdd
			LEFT JOIN materials AS m ON m.material_number = kdd.material_number
			LEFT JOIN ( SELECT kd_number, created_at AS deliv FROM knock_down_logs WHERE STATUS = 2 ) AS del ON del.kd_number = kdd.kd_number
			LEFT JOIN shipment_schedules AS ss ON ss.id = kdd.shipment_schedule_id
			LEFT JOIN knock_downs AS kd ON kd.kd_number = kdd.kd_number
			LEFT JOIN destinations AS d ON d.destination_code = ss.destination_code 
			WHERE
			kdd.deleted_at IS NULL
			".$prodFrom."
			".$prodTo."
			".$addmaterial."
			".$addhpl."
			".$kdoNumber."
			".$delivFrom."
			".$delivTo."
			".$invoiceNumber."
			".$addorigin."
			".$adddestination."
			".$shipFrom."
			".$shipTo."
			");

		$response = array(
			'status' => true,
			'knock_down_details' => $knock_down_details,
		);
		return Response::json($response);
	}

	public function indexKD($id){
		if($id == 'z-pro'){
			$title = 'KD Z-PRO';
			$title_jp = '';

			return view('kd.index_kd_zpro', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'm-pro'){
			$title = 'KD M-PRO';
			$title_jp = '';

			return view('kd.index_kd_mpro', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'mouthpiece-packed'){
			$title = 'KD Mouthpiece';
			$title_jp = '';

			return view('kd.index_kd', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'pn-part'){
			$title = 'KD Pianica Part';
			$title_jp = '';

			return view('kd.index_kd_pn_part', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'vn-assy'){
			$title = 'KD Venova Assy';
			$title_jp = '';

			return view('kd.index_kd', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'vn-injection'){
			$title = 'KD Venova Injection';
			$title_jp = '';

			return view('kd.index_kd', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'case'){
			$title = 'KD CASE';
			$title_jp = '';

			return view('kd.index_kd_case', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'tanpo'){
			$title = 'KD TANPO';
			$title_jp = '';

			return view('kd.index_kd_tanpo', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'cl-body'){
			$title = 'KD CL Body';
			$title_jp = '';

			return view('kd.index_kd_ending_stock', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'b-pro'){
			$title = 'KD B-PRO';
			$title_jp = '';

			return view('kd.index_kd_ending_stock', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'welding-body'){
			$title = 'KD Welding Body';
			$title_jp = '';

			return view('kd.index_kd_ending_stock', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else if($id == 'welding-keypost'){
			$title = 'KD Welding Key Post';
			$title_jp = '';

			return view('kd.index_kd', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}
		else{
			if($id == 'sub-assy-sx'){
				$title = 'KD Assy - SubAssy SX';
				$title_jp = '';
			}
			else if($id == 'sub-assy-fl')
			{
				$title = 'KD Sub Assy FL';
				$title_jp = '';
			}
			else if($id == 'sub-assy-cl')
			{
				$title = 'KD Sub Assy CL';
				$title_jp = '';
			}
			return view('kd.index_kd_subassy', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $id,
			))->with('page', $title)->with('head', $title);
		}		
	}

	public function indexKDClosure(){
		return view('kd.kd_closure')->with('page', 'KD Closure');
	}

	public function indexKdShipmentProgress(){
		return view('kd.display.shipment_progress')->with('page', 'KD Shipment Progress');
	}

	public function indexKdStock(){
		return view('kd.display.stock')->with('page', 'KD Stock');
	}

	public function indexKdDailyProductionResult(){
		$activity =  new UserActivityLog([
			'activity' => 'KD Daily Production Result (日常生産実績) ',
			'created_by' => Auth::id(),
		]);
		$activity->save();
		$locations = Material::where('category', '=', 'KD')
		->whereNotNull('hpl')
		->select('hpl')
		->distinct()
		->orderBy('hpl', 'asc')
		->get();

		return view('kd.display.production_result', array(
			'locations' => $locations,
		))->with('page', 'KD Daily Production Result');
	}

	public function indexKdDelivery(){
		return view('kd.kd_delivery')->with('page', 'KD Delivery');
	}

	public function indexKdProductionScheduleData(){
		$origin_groups = DB::table('origin_groups')->get();
		$materials = Material::where('category','=','KD')->orderBy('material_number', 'ASC')->get();
		$locations = Material::where('category', '=', 'KD')
		->whereNotNull('hpl')
		->select('hpl')
		->distinct()
		->orderBy('hpl', 'asc')
		->get();

		return view('kd.display.production_schedule_data', array(
			'title' => 'Production Schedule Data',
			'title_jp' => '生産スケジュールデータ',
			'origin_groups' => $origin_groups,
			'materials' => $materials,
			'locations' => $locations
		))->with('page', 'KD Schedule Data');
	}

	public function indexKdStuffing(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');
		// $container_schedules = ContainerSchedule::orderBy('shipment_date', 'asc')
		// ->where('shipment_date', '>=', $first)
		// ->where('shipment_date', '<=', $now)
		// ->get();
		$container_schedules = MasterChecksheet::whereNull('status')->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'master_checksheets.carier')->get();

		return view('kd.kd_stuffing', array(
			'container_schedules' => $container_schedules,
		))->with('page', 'KD Stuffing');
	}

	public function indexPrintLabelZpro($id){
		$knock_down_detail = KnockDownDetail::leftJoin('materials','materials.material_number','=','knock_down_details.material_number')
		->where('knock_down_details.id','=',$id)
		->select('knock_down_details.material_number','materials.material_description','knock_down_details.quantity')
		->first();

		return view('kd.label.print_label_zpro', array(
			'knock_down_detail' => $knock_down_detail,
		));
	}

	public function indexPrintLabelClBody($id){

		$data = db::select("SELECT knock_down_details.kd_number,
			knock_down_details.material_number,
			materials.material_description,
			knock_down_details.quantity,
			date(knock_down_details.created_at) AS date,
			weekly_calendars.date_code,
			COALESCE(materials.mj, '-') AS mj,
			COALESCE(materials.xy, '-') AS xy
			FROM knock_down_details
			LEFT JOIN materials ON materials.material_number = knock_down_details.material_number
			LEFT JOIN weekly_calendars ON weekly_calendars.week_date = date(knock_down_details.created_at)
			WHERE knock_down_details.id = ". $id);


		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A6', 'landscape');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('kd.label.print_label_cl_body', array(
			'data' => $data
		));
		return $pdf->stream("label_".$data[0]->kd_number.".pdf");
		
	}

	public function indexPrintLabelMpro($shipment_schedule_id, $kd_number){
		$shipment;
		if($shipment_schedule_id != 'null'){
			$shipment = db::select("SELECT kd.*, IF(resume.quantity IS NULL, kd.lot, (resume.target-resume.quantity) % resume.lot_carton) AS quantity FROM
				(SELECT knock_down_details.kd_number, knock_down_details.shipment_schedule_id, knock_down_details.material_number, materials.material_description, COALESCE(materials.xy, '-') AS xy, COALESCE(materials.mj, '-') AS mj,
				IF(shipment_schedules.quantity % material_volumes.lot_carton = 0,
				IF(shipment_schedules.quantity / material_volumes.lot_carton > 1, material_volumes.lot_carton, shipment_schedules.quantity), 
				IF(CEIL(shipment_schedules.quantity / material_volumes.lot_carton ) > 1 , material_volumes.lot_carton, shipment_schedules.quantity % material_volumes.lot_carton) 
				) AS lot  FROM knock_down_details
				LEFT JOIN materials ON materials.material_number = knock_down_details.material_number
				LEFT JOIN material_volumes ON material_volumes.material_number = knock_down_details.material_number
				LEFT JOIN shipment_schedules ON shipment_schedules.id = knock_down_details.shipment_schedule_id
				WHERE knock_down_details.kd_number = '".$kd_number."') kd
				LEFT JOIN 
				(SELECT knock_down_details.shipment_schedule_id, knock_down_details.material_number, shipment_schedules.quantity AS target, material_volumes.lot_carton, SUM(knock_down_details.quantity) AS quantity FROM knock_down_details
				LEFT JOIN knock_downs ON knock_downs.kd_number = knock_down_details.kd_number
				LEFT JOIN shipment_schedules ON shipment_schedules.id = knock_down_details.shipment_schedule_id
				LEFT JOIN material_volumes On material_volumes.material_number = knock_down_details.material_number
				WHERE knock_down_details.shipment_schedule_id = '".$shipment_schedule_id."'
				AND knock_downs.`status` = 1
				GROUP BY knock_down_details.shipment_schedule_id, knock_down_details.material_number, target, material_volumes.lot_carton) resume
				ON kd.shipment_schedule_id = resume.shipment_schedule_id");

		}else{
			$shipment = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
			->where('knock_down_details.kd_number', $kd_number)
			->select(
				'knock_down_details.material_number',
				'materials.material_description',
				db::raw('COALESCE(materials.xy,"-") AS xy'),
				db::raw('COALESCE(materials.mj,"-") AS mj'),
				db::raw('SUM(knock_down_details.quantity) AS quantity')
			)
			->groupBy('knock_down_details.material_number', 'materials.material_description', 'xy', 'mj')
			->get();

		}

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A6', 'landscape');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('kd.label.print_label_mpro', array(
			'shipment' => $shipment,
			'kd_number' => $kd_number
		));
		return $pdf->stream("label_".$shipment[0]->material_number.".pdf");


		// return view('kd.label.print_label_mpro', array(
		// 	'shipment' => $shipment,
		// 	'kd_number' => $kd_number
		// ));

	}

	public function indexPrintLabelA6($id){
		
		$data = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->where('knock_down_details.id', $id)
		->select(
			'knock_down_details.kd_number',
			'knock_down_details.material_number',
			'materials.material_description',
			db::raw('date(knock_down_details.created_at) AS date'),
			db::raw('COALESCE(materials.xy,"-") AS xy'),
			db::raw('COALESCE(materials.mj,"-") AS mj'),
			db::raw('knock_down_details.quantity AS quantity')
		)
		->first();


		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A6', 'landscape');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('kd.label.print_label_a6', array(
			'data' => $data
		));
		return $pdf->stream("label_".$data->kd_number.".pdf");


		// return view('kd.label.print_label_mpro', array(
		// 	'shipment' => $shipment,
		// 	'kd_number' => $kd_number
		// ));

	}

	public function indexPrintLabelTanpo($kd_number){

		$knock_down_details = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->where('knock_down_details.kd_number','=',$kd_number)
		->select('knock_down_details.kd_number',
			'knock_down_details.material_number',
			'shipment_schedules.st_date',
			'materials.material_description',
			'materials.base_unit',
			db::raw('sum(knock_down_details.quantity) as quantity'))
		->groupBy('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description', 'materials.base_unit', 'shipment_schedules.st_date')
		->get();

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A6', 'landscape');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('kd.label.print_label_tanpo', array(
			'kd_number' => $kd_number,
			'knock_down_details' => $knock_down_details
		));
		return $pdf->stream("label_".$kd_number.".pdf");

		// return view('kd.label.print_label_tanpo', array(
		// 	'kd_number' => $kd_number,
		// 	'knock_down_details' => $knock_down_details
		// ));

	}

	public function indexPrintLabelSubassy($id){
		$knock_down_detail = KnockDownDetail::leftJoin('materials','materials.material_number','=','knock_down_details.material_number')
		->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'knock_down_details.storage_location')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->where('knock_down_details.id','=',$id)
		->select('knock_down_details.kd_number',
			'knock_down_details.material_number',
			'knock_down_details.storage_location',
			'materials.material_description',
			'knock_down_details.quantity',
			'materials.kd_name',
			'storage_locations.location',
			'material_volumes.label',
			'shipment_schedules.st_date',
			'destinations.destination_shortname',
			db::raw('if(materials.xy is not null, materials.xy, "-") as xy'),
			db::raw('if(materials.mj is not null, materials.mj, "-") as mj')
		)
		->first();

		if($knock_down_detail->label == '76x35'){
			return view('kd.label.print_label_subassy_kecil', array(
				'knock_down_detail' => $knock_down_detail,
			));
		}else if($knock_down_detail->label == 'thermal'){
			$this->printKDOSub($knock_down_detail);
			return view('close');			
		}else{
			return view('kd.label.print_label_subassy', array(
				'knock_down_detail' => $knock_down_detail,
			));
		}
	}

	public function indexPrintLabelSubassyKecil($id){
		$knock_down_detail = KnockDownDetail::leftJoin('materials','materials.material_number','=','knock_down_details.material_number')
		->where('knock_down_details.id','=',$id)
		->select('knock_down_details.kd_number',
			'knock_down_details.material_number',
			'materials.material_description',
			'knock_down_details.quantity',
			'materials.kd_name',
			db::raw('if(materials.xy is not null, materials.xy, "-") as xy'),
			db::raw('if(materials.mj is not null, materials.mj, "-") as mj')
		)
		->first();

		return view('kd.label.print_label_subassy_kecil', array(
			'knock_down_detail' => $knock_down_detail,
		));
	}


	public function fetchContainerResume(Request $request){
		$container_id = $request->get('container_id');

		// $resume = db::select("SELECT resume.marking, resume.material_number, materials.material_description, materials.category, resume.ck_qty, resume.st_qty FROM
		// 	(SELECT ck.marking, ck.gmc AS material_number, ck.ck_qty, COALESCE(st.st_qty,0) AS st_qty FROM
		// 	(SELECT marking, gmc, SUM(qty_qty) as ck_qty FROM detail_checksheets ck
		// 	WHERE id_checkSheet = '".$container_id."'
		// 	and deleted_at is null
		// 	GROUP BY marking, gmc) AS ck
		// 	LEFT JOIN
		// 	(SELECT d.material_number, SUM(d.quantity) as st_qty FROM knock_down_details d
		// 	LEFT JOIN knock_downs k on k.kd_number = d.kd_number
		// 	WHERE k.container_id = '".$container_id."'
		// 	GROUP BY d.material_number) st
		// 	ON ck.gmc = st.material_number) AS resume
		// 	LEFT JOIN materials ON resume.material_number = materials.material_number
		// 	ORDER BY resume.marking, materials.material_description ASC");

		$resume = db::select("SELECT ck.marking, ck.gmc AS material_number, ck.material_description, ck.category, ck.ck_qty, st_kd.st_qty as kd, st_fg.st_qty as fg,
			iF(st_kd.st_qty IS NOT NULL, st_kd.st_qty, IF(st_fg.st_qty IS NOT NULL, st_fg.st_qty, 0)) AS st_qty FROM
			(SELECT ck.marking, ck.gmc, m.material_description, m.category, SUM(ck.qty_qty) as ck_qty FROM detail_checksheets ck
			LEFT JOIN materials m ON ck.gmc = m.material_number
			WHERE ck.id_checkSheet = '".$container_id."'
			AND ck.deleted_at IS NULL
			AND m.category = 'KD'
			GROUP BY ck.marking, ck.gmc, m.material_description, m.category
			UNION ALL
			SELECT GROUP_CONCAT(ck.marking) AS marking, ck.gmc, m.material_description, m.category, SUM(ck.qty_qty) as ck_qty FROM detail_checksheets ck
			LEFT JOIN materials m ON ck.gmc = m.material_number
			WHERE ck.id_checkSheet = '".$container_id."'
			AND ck.deleted_at IS NULL
			AND m.category <> 'KD'
			GROUP BY ck.gmc, m.material_description, m.category) AS ck
			LEFT JOIN
			(SELECT k.marking, d.material_number, SUM(d.quantity) as st_qty FROM knock_down_details d
			LEFT JOIN knock_downs k on k.kd_number = d.kd_number
			WHERE k.container_id = '".$container_id."'
			GROUP BY k.marking, d.material_number) st_kd
			ON ck.gmc = st_kd.material_number and ck.marking = st_kd.marking
			LEFT JOIN
			(SELECT material_number, SUM(actual) as st_qty FROM flos
			WHERE container_id = '".$container_id."'
			GROUP BY material_number) st_fg
			ON ck.gmc = st_fg.material_number
			ORDER BY ck.marking, ck.material_description ASC");

		$pallet = DetailChecksheet::where('id_checkSheet', $container_id)
		->select('marking')
		->distinct()
		->orderBy('marking', 'ASC')
		->get();

		$stuffing = KnockDownDetail::leftJoin('knock_downs','knock_downs.kd_number','=','knock_down_details.kd_number')
		->where('knock_downs.container_id', $container_id)
		->get();

		$checksheet = db::select("SELECT c.gmc, c.qty/v.lot_completion as box FROM
			(SELECT gmc, SUM(qty_qty) AS qty FROM detail_checksheets c
			WHERE id_checkSheet = '".$container_id."'
			GROUP BY gmc) AS c
			LEFT JOIN material_volumes v ON v.material_number = c.gmc");

		$response = array(
			'status' => true,
			'resume' => $resume,
			'pallet' => $pallet,
			'stuffing' => $stuffing,
			'checksheet' => $checksheet
		);
		return Response::json($response);
	}

	public function fetchKdShipmentProgress(Request $request){
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

		$shipment_results = DB::select("SELECT
			A.st_date, A.hpl, COALESCE(plan,0) as plan, COALESCE(act,0) as act, round(( COALESCE ( act, 0 )/ plan )* 100, 1 ) AS actual 
			FROM
			(
			SELECT DISTINCT
			materials.hpl,
			shipment_schedules.st_date 
			FROM
			materials
			CROSS JOIN shipment_schedules 
			WHERE
			shipment_schedules.st_date >= '".$datefrom."' 
			AND shipment_schedules.st_date <= '".$dateto."' 
			AND materials.category = 'KD' 
			) AS A
			LEFT JOIN (
			SELECT
			shipment_schedules.st_date,
			materials.hpl,
			sum( quantity ) AS plan,
			sum( actual_quantity ) AS act 
			FROM
			shipment_schedules
			LEFT JOIN materials ON materials.material_number = shipment_schedules.material_number 
			WHERE
			shipment_schedules.st_date >= '".$datefrom."' 
			AND shipment_schedules.st_date <= '".$dateto."' 
			AND materials.category = 'KD' 
			GROUP BY
			shipment_schedules.st_date,
			materials.hpl 
			) AS B ON A.hpl = B.hpl and A.st_date = B.st_date
			ORDER BY
			A.st_date ASC,
			A.hpl ASC");

		$response = array(
			'status' => true,
			'shipment_results' => $shipment_results,
		);
		return Response::json($response);
	}

	public function fetchKdShipmentProgressDetail(Request $request){
		$st_date = date('Y-m-d', strtotime($request->get('date')));

		$hpl = $request->get('hpl');

		$shipment_progress = ShipmentSchedule::leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->where('materials.category', '=', 'KD')
		->where('shipment_schedules.st_date', '=', $st_date);

		if($hpl != 'all'){
			$shipment_progress = $shipment_progress->where('materials.hpl', '=', $hpl);
		}

		$shipment_progress = $shipment_progress->select(
			'shipment_schedules.material_number',
			'materials.material_description',
			'destinations.destination_shortname',
			db::raw('sum(shipment_schedules.quantity) as plan'),
			db::raw('sum(shipment_schedules.actual_quantity) as actual'),
			db::raw('sum(shipment_schedules.actual_quantity)-sum(shipment_schedules.quantity) as diff'))
		->groupBy('shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname')
		->get();

		$response = array(
			'status' => true,
			'shipment_progress' => $shipment_progress,
		);
		return Response::json($response);
	}

	
	public function fetchKdStockDetail(Request $request){

		if($request->get('destination') == 'Maedaoshi'){
			$destination = null;
		}
		else{
			$destination = $request->get('destination');
		}

		$stock = KnockDownDetail::leftJoin('knock_downs', 'knock_downs.kd_number', '=', 'knock_down_details.kd_number')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', 'knock_down_details.material_number')
		->leftJoin('materials', 'materials.material_number', 'knock_down_details.material_number')
		->whereIn('knock_downs.status', $request->get('status'))
		->where('destinations.destination_shortname', '=', $destination)
		->select('knock_down_details.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width', 'material_volumes.lot_carton', DB::raw('sum(knock_down_details.quantity) as actual'))
		->groupBy('knock_down_details.material_number', 'materials.material_description', 'material_volumes.length', 'material_volumes.height', 'material_volumes.width', 'material_volumes.lot_carton')
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

		$response = array(
			'status' => true,
			'table' => $stock,
			'title' => $request->get('destination'),
			'location' => $location,
		);
		return Response::json($response); 
	}

	public function fetchKdStock(){

		$stock2 = KnockDownDetail::leftJoin('knock_downs', 'knock_downs.kd_number', '=', 'knock_down_details.kd_number')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'knock_down_details.material_number')
		->whereIn('knock_downs.status', [0,1,2,'M']);

		$total_volume = $stock2->sum(DB::raw('((material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton)*knock_down_details.quantity'));

		$total_stock = $stock2->sum('knock_down_details.quantity');

		$jsonData = $stock2->select(db::raw('if(destinations.destination_shortname is null, "Maedaoshi", destinations.destination_shortname) as destination'), DB::raw('sum(if(knock_downs.status = "0" or knock_downs.status = "M", knock_down_details.quantity, 0)) as production'), DB::raw('sum(if(knock_downs.status = "1", knock_down_details.quantity, 0)) as intransit'), DB::raw('sum(if(knock_downs.status = "2", knock_down_details.quantity, 0)) as fstk'), DB::raw('sum(knock_down_details.quantity) as actual'))
		->groupBy('destinations.destination_shortname')->orderBy(db::raw('knock_down_details.quantity'), 'desc')->get();

		$stock = KnockDownDetail::leftJoin('knock_downs', 'knock_downs.kd_number', '=', 'knock_down_details.kd_number')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->whereIn('knock_downs.status', ['0', '1', '2', 'M'])
		->select('knock_down_details.material_number', 'materials.material_description', db::raw('if(destinations.destination_shortname is null, "Maedaoshi", destinations.destination_shortname) as destination'), db::raw('if(knock_downs.status = "M" or knock_downs.status = "0", "Production", if(knock_downs.status = "1", "Intransit", "FSTK")) as location'), db::raw('sum(knock_down_details.quantity) as quantity'))
		->groupBy('knock_down_details.material_number', 'materials.material_description', 'destinations.destination_shortname', 'knock_downs.status')
		->orderBy('knock_down_details.material_number', 'destinations.destination_shortname')
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

	public function fetchKdProductionScheduleData(Request $request){
		$first = date("Y-m-01", strtotime($request->get("dateTo")));

		$production_schedules = ProductionSchedule::leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
		->where('production_schedules.due_date', '>=', $first)
		->where('materials.category', '=', 'KD');

		$knock_down_details = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->where(db::raw('date(knock_down_details.created_at)'), '>=', $first)
		->where('materials.category', '=', 'KD');

		$knock_down_deliveries = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('knock_downs', 'knock_downs.kd_number', '=', 'knock_down_details.kd_number')
		->where(db::raw('date(knock_down_details.created_at)'), '>=', $first)
		->whereIn('knock_downs.status', [2,3,4])
		->where('materials.category', '=', 'KD');

		if(strlen($request->get('dateTo'))>0){
			$knock_down_details = $knock_down_details->where(db::raw('date(knock_down_details.created_at)'), '<=', $request->get('dateTo'));
			$knock_down_deliveries = $knock_down_deliveries->where(db::raw('date(knock_down_details.created_at)'), '<=', $request->get('dateTo'));
			$production_schedules = $production_schedules->where('production_schedules.due_date', '<=', $request->get('dateTo'));
		}

		if($request->get('originGroup') != null){
			$knock_down_details = $knock_down_details->whereIn('materials.origin_group_code', $request->get('originGroup'));
			$knock_down_deliveries = $knock_down_deliveries->whereIn('materials.origin_group_code', $request->get('originGroup'));
			$production_schedules = $production_schedules->whereIn('materials.origin_group_code', $request->get('originGroup'));
		}
		
		if($request->get('hpl') != null){
			$knock_down_details = $knock_down_details->whereIn('materials.hpl', $request->get('hpl'));
			$knock_down_deliveries = $knock_down_deliveries->whereIn('materials.hpl', $request->get('hpl'));
			$production_schedules = $production_schedules->whereIn('materials.hpl', $request->get('hpl'));
		}

		if($request->get('material_number') != null){
			$knock_down_details = $knock_down_details->whereIn('knock_down_details.material_number', $request->get('material_number'));
			$knock_down_deliveries = $knock_down_deliveries->whereIn('knock_down_details.material_number', $request->get('material_number'));
			$production_schedules = $production_schedules->whereIn('production_schedules.material_number', $request->get('material_number'));
		}

		$production_schedules = $production_schedules->select("production_schedules.due_date", "production_schedules.material_number", "materials.material_description", "production_schedules.quantity","materials.origin_group_code","materials.hpl")
		->get();

		$knock_down_details = $knock_down_details->select("knock_down_details.material_number", db::raw("sum(knock_down_details.quantity) as packing"), db::raw('date(knock_down_details.created_at) as date'))
		->groupBy('knock_down_details.material_number', db::raw('date(knock_down_details.created_at)'))
		->get();

		$knock_down_deliveries = $knock_down_deliveries->select("knock_down_details.material_number", db::raw("sum(knock_down_details.quantity) as deliv"), db::raw('date(knock_down_details.created_at) as date'))
		->groupBy('knock_down_details.material_number', db::raw('date(knock_down_details.created_at)'))
		->get();

		$response = array(
			'status' => true,
			'production_sch' => $production_schedules,
			'packing' => $knock_down_details,
			'deliv' => $knock_down_deliveries
		);
		return Response::json($response);
	}


	public function fetchKdDailyProductionResult(Request $request){
		if($request->get('hpl') == 'all'){
			$hpl = "where materials.category = 'KD'";
		}
		else{
			$hpl = "where materials.category = 'KD' and materials.hpl = '". $request->get('hpl') ."'";
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

			select material_number, sum(quantity) as debt from knock_down_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
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
		from knock_down_details 
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

	public function deleteKdDelivery(Request $request){
		$id = Auth::id();
		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
		->where('status', '=', '2')
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'KDO status tidak sesuai'
			);
			return Response::json($response);
		}

		$knock_down->status = '1';

		$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

		foreach ($knock_down_details as $knock_down_detail) {

			$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $knock_down_detail->material_number, 'storage_location' => $knock_down_detail->storage_location]);
			$inventoryWIP->quantity = ($inventoryWIP->quantity+$knock_down_detail->quantity);

			$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
			$inventoryFSTK->quantity = ($inventoryFSTK->quantity-$knock_down_detail->quantity);

			$transaction_transfer = new TransactionTransfer([
				'plant' => '8190',
				'serial_number' => $knock_down_detail->kd_number,
				'material_number' => $knock_down_detail->material_number,
				'issue_plant' => '8190',
				'issue_location' => $knock_down_detail->storage_location,
				'receive_plant' => '8191',
				'receive_location' => 'FSTK',
				'transaction_code' => 'MB1B',
				'movement_type' => '9P2',
				'quantity' => $knock_down_detail->quantity,
				'created_by' => $id
			]);

			try{
				DB::transaction(function() use ($inventoryWIP, $inventoryFSTK, $transaction_transfer, $knock_down){
					$inventoryWIP->save();
					$inventoryFSTK->save();
					$transaction_transfer->save();
					$knock_down->save();
				});	
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$response = array(
			'status' => true,
			'message' => 'KDO delivery berhasil di cancel'
		);
		return Response::json($response);
	}

	public function deleteKd(Request $request){
		$id = Auth::id();
		$kd_number = $request->get('kd_number');
		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
		->where('status', '=', '1')
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'KDO status tidak sesuai'
			);
			return Response::json($response);
		}

		$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

		foreach ($knock_down_details as $knock_down_detail) {
			$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $knock_down_detail->material_number, 'storage_location' => $knock_down_detail->storage_location]);
			$inventoryWIP->quantity = ($inventoryWIP->quantity-$knock_down_detail->quantity);

			$child = BomComponent::where('material_parent', $knock_down_detail->material_number)->get();
			for ($i=0; $i < count($child); $i++) { 
				$inv_child = Inventory::where('plant','=','8190')
				->where('material_number','=',$child[$i]->material_child)
				->where('storage_location','=',$knock_down_detail->storage_location)
				->first();

				if($inv_child){
					$inv_child->quantity = $inv_child->quantity + ($knock_down_detail->quantity * $child[$i]->usage);
					$inv_child->save();
				}
			}

			$shipment_sch = ShipmentSchedule::where('id', $knock_down_detail->shipment_schedule_id)->first();
			$shipment_sch->actual_quantity = $shipment_sch->actual_quantity - $knock_down_detail->quantity;
			

			$transaction_completion = new TransactionCompletion([
				'serial_number' => $knock_down_detail->kd_number,
				'material_number' => $knock_down_detail->material_number,
				'issue_plant' => '8190',
				'issue_location' => $knock_down_detail->storage_location,
				'quantity' => $knock_down_detail->quantity,
				'movement_type' => '102',
				'reference_file' => 'directly_executed_on_sap',
				'created_by' => $id,
			]);

			try{
				DB::transaction(function() use ($inventoryWIP, $transaction_completion, $shipment_sch){
					$inventoryWIP->save();
					$transaction_completion->save();
					$shipment_sch->save();
				});	
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		try{
			
			DB::transaction(function() use ($knock_down, $kd_number){
				$knock_down->forceDelete();
				$delete_detail = KnockDownDetail::where('kd_number', '=', $kd_number)->forceDelete();
			});

		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'KDO berhasil di cancel'
		);
		return Response::json($response);
	}

	public function deleteKdDetail(Request $request){
		$id = Auth::id();
		$knock_down_detail = KnockDownDetail::where('id', '=', $request->get('id'))->first();
		$knock_down = KnockDown::where('kd_number', '=', $knock_down_detail->kd_number)->first();

		$transaction_completion = new TransactionCompletion([
			'serial_number' => $knock_down_detail->kd_number,
			'material_number' => $knock_down_detail->material_number,
			'issue_plant' => '8190',
			'issue_location' => $knock_down_detail->storage_location,
			'quantity' => $knock_down_detail->quantity,
			'movement_type' => '102',
			'reference_file' => 'directly_executed_on_sap',
			'created_by' => $id,
		]);

		$shipment_sch = ShipmentSchedule::where('id', $knock_down_detail->shipment_schedule_id)->first();
		if($shipment_sch){
			$shipment_sch->actual_quantity = $shipment_sch->actual_quantity - $knock_down_detail->quantity;			
		}

		$production_sch = ProductionSchedule::where('material_number', $knock_down_detail->material_number)
		->where('actual_quantity','>',0)
		->orderBy('due_date', 'desc')
		->first();
		if($production_sch){
			$production_sch->actual_quantity = $production_sch->actual_quantity - $knock_down_detail->quantity;
		}

		$inventory = Inventory::where('plant','=','8190')
		->where('material_number','=',$knock_down_detail->material_number)
		->where('storage_location','=',$knock_down_detail->storage_location)
		->first();
		$inventory->quantity = $inventory->quantity - $knock_down_detail->quantity;
		

		$child = BomComponent::where('material_parent', $knock_down_detail->material_number)->get();
		for ($i=0; $i < count($child); $i++) { 
			$inv_child = Inventory::where('plant','=','8190')
			->where('material_number','=',$child[$i]->material_child)
			->where('storage_location','=',$knock_down_detail->storage_location)
			->first();

			if($inv_child){
				$inv_child->quantity = $inv_child->quantity + ($knock_down_detail->quantity * $child[$i]->usage);
				$inv_child->save();
			}
		}

		try{
			if($knock_down->actual_count-1 == 0){
				$knock_down->forceDelete();
			}
			else{
				$knock_down->actual_count = $knock_down->actual_count-1;
				$knock_down->save(); 
			}

			DB::transaction(function() use ($knock_down_detail, $transaction_completion, $production_sch, $shipment_sch, $inventory){
				$knock_down_detail->forceDelete();
				$transaction_completion->save();
				$inventory->save();
				if($production_sch){				
					$production_sch->save();
				}
				if($shipment_sch){
					$shipment_sch->save();

				}
			});	
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'KDO berhasil di cancel',
			'tes' => $knock_down_detail
		);
		return Response::json($response);
	}

	public function deleteKdStuffing(Request $request){
		$id = Auth::id();
		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
		->where('status', '=', '3')
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'KDO status tidak sesuai'
			);
			return Response::json($response);
		}

		$knock_down->status = '2';
		$knock_down->invoice_number = null;
		$knock_down->container_id = null;

		$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

		foreach ($knock_down_details as $knock_down_detail) {

			$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
			$inventoryFSTK->quantity = ($inventoryFSTK->quantity+$knock_down_detail->quantity);

			try{
				DB::transaction(function() use ($inventoryFSTK, $knock_down){
					$inventoryFSTK->save();
					$knock_down->save();
				});	
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$response = array(
			'status' => true,
			'message' => 'KDO stuffing berhasil di cancel'
		);
		return Response::json($response);
	}

	public function scanKdClosure(Request $request){
		$status = $request->get('status');

		$knock_down = KnockDownDetail::leftJoin('knock_downs','knock_down_details.kd_number','=','knock_downs.kd_number')
		->leftJoin('materials','materials.material_number','=','knock_down_details.material_number')
		->where('knock_down_details.kd_number', '=', $request->get('kd_number'))
		->where('knock_downs.status', '=', ($status-1))
		->select(
			'knock_down_details.kd_number',
			'knock_down_details.material_number',
			'materials.material_description',
			'knock_downs.remark'
		)
		->first();

		if($knock_down){
			$response = array(
				'status' => true,
				'message' => 'Scan KDO Closure Berhasil',
				'knock_down' => $knock_down
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'message' => 'Nomor KDO tidak ditemukan'
			);
			return Response::json($response);
		}

		
	}

	public function fetchKdDeliveryClosure(Request $request){
		$closure_id = $request->get('closure_id');
		$status = $request->get('status');

		$closure = KnockDown::leftJoin('knock_down_details','knock_down_details.kd_number','=','knock_downs.kd_number')
		->leftJoin('materials','materials.material_number','=','knock_down_details.material_number')
		->leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'knock_down_details.storage_location')
		->where('knock_downs.closure_id', '=', $closure_id)
		->select(
			'knock_down_details.kd_number',
			'knock_down_details.material_number',
			'materials.material_description',
			'knock_downs.status',
			'knock_downs.closure_id',
			'knock_downs.remark',
			'storage_locations.location'
		)
		->orderBy('knock_down_details.kd_number', 'ASC')
		->get();

		$response = array(
			'status' => true,
			'closure' => $closure
		);
		return Response::json($response);

	}

	public function scanKdDelivery(Request $request){

		$id = Auth::id();

		$status = $request->get('status');

		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'Nomor KDO tidak ditemukan'
			);
			return Response::json($response);
		}

		if($knock_down->status != ($status-1)){
			if($knock_down->status == $status){
				$response = array(
					'status' => true,
					'knock_down' => $knock_down,
					'message' => 'Nomor KDO sudah scan delivery',
					'update' => false
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Alur proses salah'
				);
				return Response::json($response);	
			}
		}else{
			$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

			$knock_down->status = $status;

			foreach ($knock_down_details as $knock_down_detail) {

				$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $knock_down_detail->material_number, 'storage_location' => $knock_down_detail->storage_location]);
				$inventoryWIP->quantity = ($inventoryWIP->quantity-$knock_down_detail->quantity);

				$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
				$inventoryFSTK->quantity = ($inventoryFSTK->quantity+$knock_down_detail->quantity);

				$transaction_transfer = new TransactionTransfer([
					'plant' => '8190',
					'serial_number' => $knock_down_detail->kd_number,
					'material_number' => $knock_down_detail->material_number,
					'issue_plant' => '8190',
					'issue_location' => $knock_down_detail->storage_location,
					'receive_plant' => '8191',
					'receive_location' => 'FSTK',
					'transaction_code' => 'MB1B',
					'movement_type' => '9P1',
					'quantity' => $knock_down_detail->quantity,
					'created_by' => $id
				]);

				try{
					DB::transaction(function() use ($inventoryWIP, $inventoryFSTK, $transaction_transfer, $knock_down){
						$inventoryWIP->save();
						$inventoryFSTK->save();
						$transaction_transfer->save();
						$knock_down->save();
					});	
				}
				catch(\Exception $e){
					$error_log = new ErrorLog([
						'error_message' => $e->getMessage(),
						'created_by' => $id
					]);
					$error_log->save();
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			try{
				$kd_log = KnockDownLog::updateOrCreate(
					['kd_number' => $request->get('kd_number'), 'status' => $status],
					['created_by' => $id, 'status' => $status, 'updated_at' => Carbon::now()]
				);
				$kd_log->save();
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}

			$response = array(
				'status' => true,
				'message' => 'KDO berhasil ditransfer ke FSTK.',
				'knock_down' => $knock_down,
				'update' => true
			);
			return Response::json($response);
		}
	}


	// public function scanKdDelivery(Request $request){

	// 	$id = Auth::id();

	// 	$status = $request->get('status');

	// 	$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
	// 	->where('status', ($status-1))
	// 	->first();

	// 	if(!$knock_down){
	// 		$response = array(
	// 			'status' => false,
	// 			'message' => 'Nomor KDO tidak ditemukan'
	// 		);
	// 		return Response::json($response);
	// 	}

	// 	$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

	// 	$knock_down->status = $status;

	// 	foreach ($knock_down_details as $knock_down_detail) {

	// 		$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $knock_down_detail->material_number, 'storage_location' => $knock_down_detail->storage_location]);
	// 		$inventoryWIP->quantity = ($inventoryWIP->quantity-$knock_down_detail->quantity);

	// 		$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
	// 		$inventoryFSTK->quantity = ($inventoryFSTK->quantity+$knock_down_detail->quantity);

	// 		$transaction_transfer = new TransactionTransfer([
	// 			'plant' => '8190',
	// 			'serial_number' => $knock_down_detail->kd_number,
	// 			'material_number' => $knock_down_detail->material_number,
	// 			'issue_plant' => '8190',
	// 			'issue_location' => $knock_down_detail->storage_location,
	// 			'receive_plant' => '8191',
	// 			'receive_location' => 'FSTK',
	// 			'transaction_code' => 'MB1B',
	// 			'movement_type' => '9P1',
	// 			'quantity' => $knock_down_detail->quantity,
	// 			'created_by' => $id
	// 		]);

	// 		try{
	// 			DB::transaction(function() use ($inventoryWIP, $inventoryFSTK, $transaction_transfer, $knock_down){
	// 				$inventoryWIP->save();
	// 				$inventoryFSTK->save();
	// 				$transaction_transfer->save();
	// 				$knock_down->save();
	// 			});	
	// 		}
	// 		catch(\Exception $e){
	// 			$error_log = new ErrorLog([
	// 				'error_message' => $e->getMessage(),
	// 				'created_by' => $id
	// 			]);
	// 			$error_log->save();
	// 			$response = array(
	// 				'status' => false,
	// 				'message' => $e->getMessage(),
	// 			);
	// 			return Response::json($response);
	// 		}
	// 	}

	// 	try{
	// 		$kd_log = KnockDownLog::updateOrCreate(
	// 			['kd_number' => $request->get('kd_number'), 'status' => $status],
	// 			['created_by' => $id, 'status' => $status, 'updated_at' => Carbon::now()]
	// 		);
	// 		$kd_log->save();
	// 	}
	// 	catch(\Exception $e){
	// 		$error_log = new ErrorLog([
	// 			'error_message' => $e->getMessage(),
	// 			'created_by' => $id
	// 		]);
	// 		$error_log->save();
	// 		$response = array(
	// 			'status' => false,
	// 			'message' => $e->getMessage(),
	// 		);
	// 		return Response::json($response);
	// 	}

	// 	$response = array(
	// 		'status' => true,
	// 		'message' => 'KDO berhasil ditransfer ke FSTK.',
	// 		'knock_down' => $knock_down,
	// 		'update' => true
	// 	);
	// 	return Response::json($response);
	// }
	

	public function scanKdStuffing(Request $request){
		$id = Auth::id();
		$status = $request->get('status');
		$invoice_number = $request->get('invoice_number');
		$container_id = $request->get('container_id');
		$marking = $request->get('marking');

		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
		->where('status', '=', ($status - 1))
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'Nomor KDO tidak ditemukan'
			);
			return Response::json($response);
		}

		$knock_down_details = db::select("SELECT kd_number, material_number, SUM(quantity) AS quantity FROM knock_down_details
			WHERE kd_number = '".$request->get('kd_number')."'
			GROUP BY kd_number, material_number");


		//Cek Marking		
		foreach ($knock_down_details as $knock_down_detail) {

			$act_pallet = db::select("SELECT kd.kd_number, d.material_number, SUM(d.quantity) AS quantity FROM knock_downs kd
				LEFT JOIN knock_down_details d ON d.kd_number = kd.kd_number
				WHERE kd.container_id = '".$container_id."'
				AND kd.marking = '".$marking."'
				AND d.material_number = '".$knock_down_detail->material_number."'
				GROUP BY kd.kd_number, d.material_number");

			$qty_pallet = db::select("SELECT marking, gmc, SUM(qty_qty) AS quantity FROM `detail_checksheets`
				WHERE id_checkSheet = '".$container_id."'
				AND marking = '".$marking."'
				AND gmc = '".$knock_down_detail->material_number."'
				GROUP BY marking, gmc");


			if(count($qty_pallet) == 0){
				$response = array(
					'status' => false,
					'message' => 'Terdapat material yang tidak untuk di Stuffing pada pallet ' . $marking,
				);
				return Response::json($response);
			}

			if(count($act_pallet) > 0){
				if($act_pallet[0]->quantity + $knock_down_detail->quantity > $qty_pallet[0]->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quantity item pada pallet ' . $marking . ' sudah terpenuhi',
					);
					return Response::json($response);
				}
			}else{
				if($knock_down_detail->quantity > $qty_pallet[0]->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quantity item pada pallet ' . $marking . ' sudah terpenuhi',
					);
					return Response::json($response);
				}
			}


		}


		$knock_down->status = $status;
		$knock_down->invoice_number = $invoice_number;
		$knock_down->container_id = $container_id;
		$knock_down->marking = $marking;

		foreach ($knock_down_details as $knock_down_detail) {

			$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
			$inventoryFSTK->quantity = ($inventoryFSTK->quantity-$knock_down_detail->quantity);


			
			try{
				DB::transaction(function() use ($inventoryFSTK, $knock_down){
					$inventoryFSTK->save();
					$knock_down->save();
				});	
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		try{
			$kd_log = KnockDownLog::updateOrCreate(
				['kd_number' => $request->get('kd_number'), 'status' => $status],
				['created_by' => $id, 'status' => $status, 'updated_at' => Carbon::now()]
			);
			$kd_log->save();
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'KDO berhasil distuffing',
			'knock_down_details' => $knock_down_details
		);
		return Response::json($response);

	}

	public function fetchKDClosure(Request $request){
		$prefix_now = 'CLSR'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','closure')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$closure_number = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		$kd_closure = $request->get('kd_closure');

		for ($i=0; $i < count($kd_closure); $i++) {
			try{
				$knock_down = KnockDown::where('kd_number', $kd_closure[$i])
				->update([
					'status' => 1,
					'closure_id' => $closure_number
				]);


				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_closure[$i], 'status' => 1],
					['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
				);
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => Auth::id()
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}

		}


		$response = array(
			'status' => true,
			'message' => 'KDO berhasil diclosure'
		);
		return Response::json($response);


	}

	public function fetchKDOClosure(Request $request){
		$status = $request->get('status');
		
		$knock_downs = KnockDownDetail::leftJoin('knock_downs', 'knock_down_details.kd_number', '=', 'knock_downs.kd_number')
		->leftJoin('knock_down_logs', 'knock_down_logs.kd_number', '=', 'knock_down_details.kd_number')
		->leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'knock_down_details.storage_location')
		->where('knock_down_logs.status', '=', $status)
		->where('knock_downs.status', '=', $status)
		->where('knock_downs.remark', '=', ['sub-assy-sx','sub-assy-cl','sub-assy-fl'])
		->orderBy('knock_down_logs.updated_at', 'desc')
		->select(
			'knock_downs.kd_number',
			'knock_down_details.material_number',
			'knock_down_details.quantity',
			'materials.material_description',
			'storage_locations.location',
			'knock_downs.remark',
			'knock_downs.closure_id',
			'knock_down_logs.updated_at')
		->get();

		return DataTables::of($knock_downs)
		->addColumn('deleteKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteKDO(id)" id="' . $knock_downs->kd_number . '"><i class="fa fa-trash"></i></a>';
		})
		->rawColumns([
			'deleteKDO' => 'deleteKDO'
		])
		->make(true);
	}

	public function fetchKDO(Request $request){
		$status = $request->get('status');
		$remark = $request->get('remark');

		$knock_downs = KnockDown::leftJoin('knock_down_logs', 'knock_down_logs.kd_number', '=', 'knock_downs.kd_number')
		->leftJoin('master_checksheets', 'master_checksheets.id_checkSheet', '=', 'knock_downs.container_id')
		->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'knock_downs.container_id')
		->where('knock_down_logs.status', '=', $status)
		->where('knock_downs.status', '=', $status);

		if(strlen($request->get('remark')) > 0 ){
			$knock_downs = $knock_downs->where('knock_downs.remark', '=', $request->get('remark'));
		}


		$knock_downs = $knock_downs->orderBy('knock_down_logs.updated_at', 'desc')
		->select(
			'knock_downs.kd_number',
			'master_checksheets.Stuffing_date',
			'container_schedules.shipment_date',
			db::raw('IF(master_checksheets.Stuffing_date is null, container_schedules.shipment_date, master_checksheets.Stuffing_date) as st_date'),
			'knock_downs.actual_count',
			'knock_downs.remark',
			'knock_down_logs.updated_at',
			'knock_downs.invoice_number',
			'knock_downs.container_id')
		->get();

		return DataTables::of($knock_downs)
		->addColumn('detailKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onClick="detailKDO(id)" id="' . $knock_downs->kd_number . '"><i class="fa fa-eye"></i></a>';
		})
		->addColumn('reprintKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onClick="reprintKDO(id)" id="' . $knock_downs->kd_number . '"><i class="fa fa-print"></i></a>';
		})
		->addColumn('deleteKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteKDO(id)" id="' . $knock_downs->kd_number . '"><i class="fa fa-trash"></i></a>';
		})
		->rawColumns([
			'detailKDO' => 'detailKDO',
			'reprintKDO' => 'reprintKDO',
			'deleteKDO' => 'deleteKDO'
		])
		->make(true);
	}


	public function fetchKDODetail(Request $request){
		$status = $request->get('status');
		$remark = $request->get('remark');

		$knock_down_details = KnockDownDetail::leftJoin('knock_downs','knock_down_details.kd_number','=','knock_downs.kd_number')
		->leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'knock_down_details.storage_location')
		->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'knock_downs.container_id')
		->leftJoin('master_checksheets', 'master_checksheets.id_checkSheet', '=', 'knock_downs.container_id')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->where('knock_downs.status', '=', $status);

		if(strlen($request->get('remark')) > 0 ){
			$knock_down_details = $knock_down_details->where('knock_downs.remark', '=', $request->get('remark'));
		}

		$knock_down_details = $knock_down_details->select('knock_down_details.id',
			'knock_down_details.kd_number',
			'master_checksheets.Stuffing_date',
			'container_schedules.shipment_date',
			db::raw('IF(master_checksheets.Stuffing_date is null, container_schedules.shipment_date, master_checksheets.Stuffing_date) as st_date'),
			'knock_down_details.material_number',
			'materials.material_description',
			'storage_locations.location',
			'shipment_schedules.st_date',
			'destinations.destination_shortname',
			db::raw('knock_down_details.updated_at AS detail_updated_at'),
			'knock_downs.updated_at',
			'knock_downs.invoice_number',
			'knock_downs.container_id',
			'knock_down_details.quantity')
		->orderBy('knock_down_details.kd_number', 'DESC')
		->orderBy('knock_down_details.updated_at', 'ASC')
		->get();

		return DataTables::of($knock_down_details)
		->addColumn('reprintKDO', function($knock_down_details){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-info" onClick="reprintKDODetail(id)" id="' . $knock_down_details->id . '+'. $knock_down_details->location . '"><i class="fa fa-print"></i></a>';
		})
		->addColumn('deleteKDO', function($knock_down_details){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteKDODetail(id)" id="' . $knock_down_details->id . '"><i class="fa fa-trash"></i></a>';
		})
		->rawColumns(['reprintKDO' => 'reprintKDO', 'deleteKDO' => 'deleteKDO'])
		->make(true);
	}

	public function fetchKD($id){
		// $datefrom = date('Y-m-01');
		
		$date = date('Y-m-d', strtotime('+14 day'));
		$now = WeeklyCalendar::where('week_date', $date)->first();
		$dateto = WeeklyCalendar::where('week_name', $now->week_name)->orderBy('week_date', 'desc')->first();
		$first = date('Y-m-01');

		$storage = '';
		if($id == 'z-pro'){
			$storage = "('ZPRO')";
		}else if($id == 'sub-assy-sx'){
			$storage = "('SUBASSY-SX', 'ASSY-SX')";
		}else if($id == 'sub-assy-fl'){
			$storage = "('SUBASSY-FL')";
		}else if($id == 'sub-assy-cl'){
			$storage = "('SUBASSY-CL')";
		}else if ($id == 'b-pro') {
			$storage = "('BPRO')";
		}else if ($id == 'cl-body') {
			$storage = "('CL-BODY')";
		}else if ($id == 'welding-body') {
			$storage = "('WELDING') AND m.kd_name = 'BODY-BELL'";
		}

		// $target = db::select("select target.material_number, target.material_description, sum(target.quantity ) as target from
		// 	(select s.material_number, m.material_description, sum(quantity) as quantity from production_schedules s
		// 	left join materials m on m.material_number = s.material_number
		// 	where date(s.due_date) >= '".$datefrom."'
		// 	and date(s.due_date) <= '".$dateto."'
		// 	and m.category = 'KD'
		// 	and m.hpl in '".$storage."'
		// 	group by s.material_number, m.material_description
		// 	union all
		// 	select d.material_number, m.material_description, sum(-quantity) as quantity from knock_down_details d
		// 	left join materials m on m.material_number = d.material_number
		// 	where date(d.created_at) >= '".$datefrom."'
		// 	and date(d.created_at) <= '".$dateto."'
		// 	and m.hpl in '".$storage."'
		// 	group by d.material_number, m.material_description) target
		// 	group by target.material_number, target.material_description
		// 	having target > 0
		// 	order by target desc");

		$target = db::select("SELECT
			p.id,
			p.due_date,
			date_format( p.due_date, '%v') as week,
			p.material_number,
			m.material_description,
			m.hpl,
			m.surface,
			(p.quantity - p.actual_quantity) AS target,
			v.lot_completion,
			((p.quantity - p.actual_quantity) / v.lot_completion) AS box
			FROM production_schedules p
			LEFT JOIN materials m ON m.material_number = p.material_number
			LEFT JOIN material_volumes v ON v.material_number = p.material_number
			WHERE date( p.due_date ) <= '".$dateto->week_date."'
			AND date( p.due_date ) >= '".$first."'
			AND p.actual_quantity < p.quantity
			AND m.category = 'KD' 
			AND m.hpl IN ".$storage."
			HAVING target > 0
			ORDER BY p.due_date ASC, box DESC");

		$response = array(
			'status' => true,
			'target' => $target,
		);
		return Response::json($response);
	}

	public function fetchKDNew($id){
		$dateto = date('Y-m-d', strtotime('+14 day'));

		$storage = '';
		$order = '';
		if($id == 'z-pro'){
			$dateto = date('Y-m-d', strtotime('+30 day'));
			$storage = "('ZPRO')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'm-pro'){
			$storage = "('MPRO')";
			$order = 'sh.st_date ASC, sh.actual_quantity DESC, target DESC';		

		}else if($id == 'sub-assy-sx'){
			$storage = "('SUBASSY-SX', 'ASSY-SX')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'sub-assy-fl'){
			$storage = "('SUBASSY-FL')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'sub-assy-cl'){
			$storage = "('SUBASSY-CL')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'mouthpiece'){
			$storage = "('MP') AND m.kd_name = 'SINGLE'";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'mouthpiece-packed'){
			$storage = "('MP') AND m.kd_name = 'MP'";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'case'){
			$storage = "('CASE')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'tanpo'){
			$storage = "('TANPO')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'cl-body'){
			$storage = "('CL-BODY')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'pn-part'){
			$storage = "('PN-PART')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'vn-assy'){
			$storage = "('VN-ASSY')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'vn-injection'){
			$storage = "('VN-INJECTION')";
			$order = 'sh.st_date ASC, box DESC';

		}else if($id == 'welding-keypost'){
			$storage = "('WELDING') AND m.kd_name = 'KEY POST'";
			$order = 'sh.st_date ASC, box DESC';

		}

		$target = db::select("SELECT
			sh.id,
			sh.st_date,
			sh.material_number,
			m.material_description,
			m.hpl,
			m.surface,
			d.destination_shortname,
			sh.quantity,
			sh.actual_quantity,
			( sh.quantity - sh.actual_quantity ) AS target,
			v.lot_completion,
			v.lot_carton,
			(( sh.quantity - sh.actual_quantity ) / v.lot_completion ) AS box 
			FROM shipment_schedules sh
			LEFT JOIN materials m ON m.material_number = sh.material_number
			LEFT JOIN destinations d ON d.destination_code = sh.destination_code
			LEFT JOIN material_volumes v ON v.material_number = sh.material_number 
			WHERE sh.st_date <= '".$dateto."'
			AND m.category = 'KD'
			AND m.hpl IN ".$storage."
			HAVING target > 0 
			ORDER BY ". $order);

		$response = array(
			'status' => true,
			'target' => $target,
		);
		return Response::json($response);
	}

	public function fetchCheckKd(Request $request){

		$shipment = ShipmentSchedule::leftJoin('material_volumes', 'material_volumes.material_number', '=', 'shipment_schedules.material_number')
		->where('shipment_schedules.id', $request->get('shipment_schedule_id'))
		->where('shipment_schedules.actual_quantity', '>',  0)
		->select(
			'shipment_schedules.st_date',
			'shipment_schedules.material_number',
			'shipment_schedules.st_date',
			'shipment_schedules.actual_quantity',
			'material_volumes.lot_carton'
		)
		->first();

		if($shipment){
			if($shipment->lot_carton > 0){
				$mod = $shipment->actual_quantity % $shipment->lot_carton;

				$knock_down = KnockDownDetail::leftJoin('knock_downs', 'knock_downs.kd_number', '=', 'knock_down_details.kd_number')
				->where('knock_down_details.shipment_schedule_id', $request->get('shipment_schedule_id'))
				->where('knock_downs.status', '=',  '0')
				->orderBy('knock_down_details.created_at', 'DESC')
				->first();


				if($mod > 0){
					if($knock_down){
						$response = array(
							'status' => true,
							'kd_number' => $knock_down->kd_number,
						);
						return Response::json($response);
					}else{
						$response = array(
							'status' => false
						);
						return Response::json($response);
					}
				}else{
					$knock_down = KnockDown::where('kd_number', $knock_down->kd_number)->first();
					$knock_down->status = 1;

					$knock_down_log = KnockDownLog::updateOrCreate(
						['kd_number' => $knock_down->kd_number, 'status' => 1],
						['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
					);

					try {
						DB::transaction(function() use ($knock_down, $knock_down_log){
							$knock_down->save();
							$knock_down_log->save();
						});

						$response = array(
							'status' => false
						);
						return Response::json($response);

					} catch (Exception $e) {
						$response = array(
							'status' => false
						);
						return Response::json($response);
					}
				}
			}
		}

		$response = array(
			'status' => false
		);
		return Response::json($response);
		
	}

	public function fetchKdPack($id){
		$location = $id;

		$knock_down = KnockDown::where('status','=','0')
		->where('remark','=',$location)
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
			);
			return Response::json($response);
		}

		$pack = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=','knock_down_details.shipment_schedule_id')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=','destinations.destination_code')
		->where('knock_down_details.kd_number','=',$knock_down->kd_number)
		->select('shipment_schedules.st_date','knock_down_details.material_number', 'materials.material_description', 'destinations.destination_shortname', 'knock_down_details.quantity')
		->get();

		$response = array(
			'status' => true,
			'pack' => $pack,
		);
		return Response::json($response);
	}

	public function fetchKdDetail(Request $request){
		$location = $request->get('location');

		$detail = db::select("SELECT sh.id, sh.st_date, sh.material_number, m.material_description, sh.quantity, v.lot_completion, d.destination_shortname FROM shipment_schedules sh
			LEFT JOIN materials m ON m.material_number = sh.material_number
			LEFT JOIN destinations d ON d.destination_code = sh.destination_code
			LEFT JOIN material_volumes v ON v.material_number = m.material_number
			WHERE sh.id = ".$request->get('id'));

		$knock_down = KnockDown::where('remark','=',$location)
		->where('status','=',0)
		->orderBy('kd_number','desc')
		->first();

		$actual_count = 0;
		if($knock_down){
			$actual_count = $knock_down->actual_count;
		}

		$response = array(
			'status' => true,
			'detail' => $detail,
			'actual_count' => $actual_count,
		);
		return Response::json($response);
	}

	public function reprintKDO(Request $request){
		$kd_number = $request->get('kd_number');

		try{
			$knock_down = KnockDown::where('kd_number', $kd_number)->first();

			$storage_location;
			if($knock_down->remark == 'z-pro'){
				$storage_location = 'ZPA0';
			}	

			$knock_down_details = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description', db::raw('sum(knock_down_details.quantity) as quantity'))
			->groupBy('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description')
			->get();

			$st_date = KnockDownDetail::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
			->leftJoin('destinations','destinations.destination_code','=','shipment_schedules.destination_code')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number','knock_down_details.material_number','shipment_schedules.st_date','destinations.destination_shortname')
			->orderBy('shipment_schedules.st_date','asc')
			->first();



			$storage_location;
			if($knock_down->remark == 'z-pro'){
				$storage_location = StorageLocation::where('storage_location', '=', $storage_location)->first();

				$this->printKDO($kd_number, $st_date->st_date, $knock_down_details, $storage_location->location, 'REPRINT', $st_date->destination_shortname);
			}


			$response = array(
				'status' => true,
				'message' => 'Print Label Sukses',
				'actual_count' => 0,
			);
			return Response::json($response);
		}catch(Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function forcePrintLabel(Request $request){
		$id = Auth::id();
		$location = $request->get('location');
		$storage_location = '';
		if($location == 'z-pro'){
			$storage_location = 'ZPA0';
		}

		$knock_down = KnockDown::where('remark','=',$location)
		->where('status','=', 0)
		->orderBy('kd_number', 'desc')
		->first();
		$knock_down->status = 1;

		$kd_number = $knock_down->kd_number;

		$knock_down_log = KnockDownLog::updateOrCreate(
			['kd_number' => $kd_number, 'status' => 1],
			['created_by' => $id, 'status' => 1, 'updated_at' => Carbon::now()]
		);

		try{
			DB::transaction(function() use ($knock_down, $knock_down_log){
				$knock_down->save();
				$knock_down_log->save();
			});

			$knock_down_details = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number',
				'knock_down_details.material_number',
				'materials.material_description',
				db::raw('sum(knock_down_details.quantity) as quantity'))
			->groupBy('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description')
			->get();

			$st_date = KnockDownDetail::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
			->leftJoin('destinations','destinations.destination_code','=','shipment_schedules.destination_code')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number','knock_down_details.material_number','shipment_schedules.st_date','destinations.destination_shortname')
			->orderBy('shipment_schedules.st_date','asc')
			->first();


			if($location == 'z-pro'){
				$storage_location = StorageLocation::where('storage_location', '=', $storage_location)->first();
				$this->printKDO($kd_number, $st_date->st_date, $knock_down_details, $storage_location->location, 'PRINT', $st_date->destination_shortname);

			}

			$response = array(
				'status' => true,
				'message' => 'Print Label Sukses',
				'kd_number' => $kd_number,
				'actual_count' => 0,
			);
			return Response::json($response);
		}catch(Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}	
	}

	public function printLabelNewSingle(Request $request){
		$prefix_now = 'KD'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','kd')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$production_id = $request->get('production_id');
		$quantity = $request->get('quantity');
		$location = $request->get('location');


		//Inisiasi Variabel
		$production_schedule = ProductionSchedule::where('id', $production_id)->first();
		$material_number = $production_schedule->material_number;

		//Production Schedule
		$production_schedule->actual_quantity = $production_schedule->actual_quantity + $quantity;


		$storage_location = Material::where('material_number', $material_number)->first();
		$storage_location = $storage_location->issue_storage_location;


		//KnockDown
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$kd_number = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		$knock_down = new KnockDown([
			'kd_number' => $kd_number,
			'created_by' => Auth::id(),
			'max_count' => 1,
			'actual_count' => 1,
			'remark' => $location,
			'status' => 1,
			// 'status' => 0,
		]);


		//KnockDown Log
		// $knock_down_log = KnockDownLog::updateOrCreate(
		// 	['kd_number' => $kd_number, 'status' => 0],
		// 	['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
		// );

		$knock_down_log = KnockDownLog::updateOrCreate(
			['kd_number' => $kd_number, 'status' => 1],
			['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
		);


		//Shipment
		// $shipment_schedule = ShipmentSchedule::where('material_number', '=', $material_number)
		// ->where('actual_quantity','<',db::raw('quantity'))
		// ->orderBy('st_date', 'asc')
		// ->orderBy('id', 'asc')
		// ->first();

		// if($shipment_schedule){
		// 	$shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $quantity;

		// 	//KnockDown Detail
		// 	$knock_down_detail = new KnockDownDetail([
		// 		'kd_number' => $kd_number,
		// 		'material_number' => $material_number,
		// 		'quantity' => $quantity,
		// 		'shipment_schedule_id' => $shipment_schedule->id,
		// 		'storage_location' => $storage_location,
		// 		'created_by' => Auth::id(),
		// 	]);
		// }else{
		// 	//KnockDown Detail
		// 	$knock_down_detail = new KnockDownDetail([
		// 		'kd_number' => $kd_number,
		// 		'material_number' => $material_number,
		// 		'quantity' => $quantity,
		// 		'storage_location' => $storage_location,
		// 		'created_by' => Auth::id(),
		// 	]);
		// }

		//KnockDown Detail
		$knock_down_detail = new KnockDownDetail([
			'kd_number' => $kd_number,
			'material_number' => $material_number,
			'quantity' => $quantity,
			'storage_location' => $storage_location,
			'created_by' => Auth::id(),
		]);

		
		//Inventory
		$inventory = Inventory::where('plant','=','8190')
		->where('material_number','=',$material_number)
		->where('storage_location','=',$storage_location)
		->first();

		if($inventory){
			$inventory->quantity = $inventory->quantity + $quantity;
		}else{	
			$inventory = new Inventory([
				'plant' => '8190',
				'material_number' => $material_number,
				'storage_location' => $storage_location,
				'quantity' => $quantity,
			]);
		}

		$child = BomComponent::where('material_parent', $material_number)->get();
		for ($x=0; $x < count($child); $x++) { 
			$inv_child = Inventory::where('plant','=','8190')
			->where('material_number','=',$child[$x]->material_child)
			->where('storage_location','=',$storage_location)
			->first();

			if($inv_child){
				$inv_child->quantity = $inv_child->quantity - ($quantity * $child[$x]->usage);
				$inv_child->save();
			}
		}


		//Transaction Completion
		$transaction_completion = new TransactionCompletion([
			'serial_number' => $kd_number,
			'material_number' => $material_number,
			'issue_plant' => '8190',
			'issue_location' => $storage_location,
			'quantity' => $quantity,
			'movement_type' => '101',
			'created_by' => Auth::id(),
		]);

		try{
			DB::transaction(function() use ($production_schedule, $knock_down, $knock_down_detail, $knock_down_log, $inventory, $transaction_completion){
				$production_schedule->save();
				$knock_down->save();
				$knock_down_detail->save();
				$knock_down_log->save();
				$inventory->save();
				$transaction_completion->save();

				// if($shipment_schedule){
				// 	$shipment_schedule->save();
				// }

			});

			$knock_down_detail = KnockDownDetail::where('kd_number', $kd_number)
			->select('knock_down_details.id')
			->first();

			$response = array(
				'status' => true,
				'message' => 'Print Label Sukses',
				'knock_down_detail' => $knock_down_detail
			);
			return Response::json($response);

		}catch(Exception $e) {
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
		}	
	}

	public function printLabelNew(Request $request){
		$prefix_now = 'KD'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','kd')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		//Inisialiasi Variabel
		$shipment_id = $request->get('shipment_id');
		$material_number = $request->get('material_number');
		$quantity = $request->get('quantity');
		$location = $request->get('location');
		
		$max_count = 1;
		$status = 1;
		if ($location == 'z-pro'){
			$max_count = 100;
			$status = 0;
		}elseif ($location == 'tanpo'){
			$max_count = 100;
			$status = 0;
		}

		$storage_location = Material::where('material_number','=',$material_number)
		->select('issue_storage_location')
		->first();
		$storage_location = $storage_location->issue_storage_location;


		//KnockDown
		$knock_down = KnockDown::where('remark','=',$location)
		->where('status','=', 0)
		->orderBy('kd_number', 'desc')
		->first();


		$kd_number = '';
		if($knock_down){
			if($knock_down->actual_count < $knock_down->max_count){
				$kd_number = $knock_down->kd_number;
				$knock_down->actual_count = $knock_down->actual_count + 1;

			}else{
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$kd_number = $code_generator->prefix . $number;
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$knock_down = new KnockDown([
					'kd_number' => $kd_number,
					'created_by' => Auth::id(),
					'max_count' => $max_count,
					'actual_count' => 1,
					'remark' => $location,
					'status' => $status,
				]);

			}
		}else{
			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			$kd_number = $code_generator->prefix . $number;
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$knock_down = new KnockDown([
				'kd_number' => $kd_number,
				'created_by' => Auth::id(),
				'max_count' => $max_count,
				'actual_count' => 1,
				'remark' => $location,
				'status' => $status,
			]);

		}

		//KnockDown Log
		$knock_down_log;	
		if($location == 'z-pro'){
			if(($knock_down->actual_count + 1) == $max_count){
				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 1],
					['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
				);
			}else{
				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 0],
					['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
				);
			}
		}else{
			$knock_down_log = KnockDownLog::updateOrCreate(
				['kd_number' => $kd_number, 'status' => 1],
				['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
			);
		}

		

		//KnockDown Detail
		$knock_down_detail = new KnockDownDetail([
			'kd_number' => $kd_number,
			'material_number' => $material_number,
			'quantity' => $quantity,
			'shipment_schedule_id' => $shipment_id,
			'storage_location' => $storage_location,
			'created_by' => Auth::id(),
		]);

		//Shipment Schedule
		$shipment_schedule = ShipmentSchedule::where('id', '=', $shipment_id)->first();
		$shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $quantity;		


		//Inventory
		$inventory = Inventory::where('plant','=','8190')
		->where('material_number','=',$material_number)
		->where('storage_location','=',$storage_location)
		->first();

		if($inventory){
			$inventory->quantity = $inventory->quantity + $quantity;
		}else{	
			$inventory = new Inventory([
				'plant' => '8190',
				'material_number' => $material_number,
				'storage_location' => $storage_location,
				'quantity' => $quantity,
			]);
		}

		$child = BomComponent::where('material_parent', $material_number)->get();
		for ($i=0; $i < count($child); $i++) { 
			$inv_child = Inventory::where('plant','=','8190')
			->where('material_number','=',$child[$i]->material_child)
			->where('storage_location','=',$storage_location)
			->first();

			if($inv_child){
				$inv_child->quantity = $inv_child->quantity - ($quantity * $child[$i]->usage);
				$inv_child->save();
			}
		}

		//Transaction Completion
		$transaction_completion = new TransactionCompletion([
			'serial_number' => $kd_number,
			'material_number' => $material_number,
			'issue_plant' => '8190',
			'issue_location' => $storage_location,
			'quantity' => $quantity,
			'movement_type' => '101',
			'created_by' => Auth::id(),
		]);



		try{
			DB::transaction(function() use ($knock_down, $knock_down_detail, $shipment_schedule, $inventory, $transaction_completion, $knock_down_log){
				$knock_down->save();
				$knock_down_detail->save();
				$shipment_schedule->save();
				$inventory->save();
				$transaction_completion->save();
				$knock_down_log->save();
			});

			$knock_down = KnockDown::where('remark','=',$location)
			->where('status','=',0)
			->orderBy('kd_number', 'desc')
			->first();

			$knock_down = KnockDown::where('kd_number', '=', $kd_number)->first();

			$knock_down_detail = KnockDownDetail::where('kd_number', '=', $kd_number)
			->select('knock_down_details.id')
			->orderBy('knock_down_details.created_at', 'desc')
			->first();

			$response = array(
				'status' => true,
				'message' => 'Print Label Sukses',
				'actual_count' => $knock_down->actual_count,
				'knock_down_detail_id' => $knock_down_detail->id,
			);
			return Response::json($response);
		}catch(Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}		
	}

	public function printLabelNewParsial(Request $request){
		//Inisialiasi Variabel
		$kd_number = $request->get('kd_number');
		$shipment_id = $request->get('shipment_id');
		$material_number = $request->get('material_number');
		$quantity = $request->get('quantity');
		$location = $request->get('location');


		$storage_location = Material::where('material_number','=',$material_number)
		->select('issue_storage_location')
		->first();
		$storage_location = $storage_location->issue_storage_location;

		$max_count = 1;
		$status = 1;
		if($location == 'z-pro' || $location == 'm-pro'){
			$max_count = 100;
			$status = 0;
		}


		//KnockDown
		$knock_down;
		if(strlen($kd_number) > 0){
			$knock_down = KnockDown::where('kd_number','=', $kd_number)
			->where('status','=', 0)
			->first();

			if($knock_down){
				$knock_down->actual_count = $knock_down->actual_count + 1;
			}else{
				$response = array(
					'status' => false,
					'message' => 'KDO Number Not Found'
				);
				return Response::json($response);
			}
		}else{
			$prefix_now = 'KD'.date("y").date("m");
			$code_generator = CodeGenerator::where('note','=','kd')->first();
			if ($prefix_now != $code_generator->prefix){
				$code_generator->prefix = $prefix_now;
				$code_generator->index = '0';
				$code_generator->save();
			}

			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			$kd_number = $code_generator->prefix . $number;
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$knock_down = new KnockDown([
				'kd_number' => $kd_number,
				'created_by' => Auth::id(),
				'max_count' => $max_count,
				'actual_count' => 1,
				'remark' => $location,
				'status' => $status,
			]);

		}	


		//KnockDown Log
		$knock_down_log;	
		if($location == 'z-pro' || $location == 'm-pro'){
			$knock_down_log = KnockDownLog::updateOrCreate(
				['kd_number' => $kd_number, 'status' => 0],
				['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
			);
		}else{
			$knock_down_log = KnockDownLog::updateOrCreate(
				['kd_number' => $kd_number, 'status' => 1],
				['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
			);
		}


		//KnockDown Detail
		$knock_down_detail = new KnockDownDetail([
			'kd_number' => $kd_number,
			'material_number' => $material_number,
			'quantity' => $quantity,
			'shipment_schedule_id' => $shipment_id,
			'storage_location' => $storage_location,
			'created_by' => Auth::id(),
		]);


		//Shipment Schedule
		$shipment_schedule = ShipmentSchedule::where('id', '=', $shipment_id)->first();
		$shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $quantity;

		$closeKDO = false;
		if($shipment_schedule->quantity == $shipment_schedule->actual_quantity){
			$closeKDO = true;
		}		

		//Inventory
		$inventory = Inventory::where('plant','=','8190')
		->where('material_number','=',$material_number)
		->where('storage_location','=',$storage_location)
		->first();


		if($inventory){
			$inventory->quantity = $inventory->quantity + $quantity;
		}else{	
			$inventory = new Inventory([
				'plant' => '8190',
				'material_number' => $material_number,
				'storage_location' => $storage_location,
				'quantity' => $quantity,
			]);
		}

		$child = BomComponent::where('material_parent', $material_number)->get();
		for ($i=0; $i < count($child); $i++) { 
			$inv_child = Inventory::where('plant','=','8190')
			->where('material_number','=',$child[$i]->material_child)
			->where('storage_location','=',$storage_location)
			->first();

			if($inv_child){
				$inv_child->quantity = $inv_child->quantity - ($quantity * $child[$i]->usage);
				$inv_child->save();
			}
		}


		//Transaction Completion
		$transaction_completion = new TransactionCompletion([
			'serial_number' => $kd_number,
			'material_number' => $material_number,
			'issue_plant' => '8190',
			'issue_location' => $storage_location,
			'quantity' => $quantity,
			'movement_type' => '101',
			'created_by' => Auth::id(),
		]);



		try{
			DB::transaction(function() use ($knock_down, $knock_down_detail, $shipment_schedule, $inventory, $transaction_completion, $knock_down_log){
				$knock_down->save();
				$knock_down_detail->save();
				$shipment_schedule->save();
				$inventory->save();
				$transaction_completion->save();
				$knock_down_log->save();
			});

			if($closeKDO){
				$knock_down = KnockDown::where('kd_number', $kd_number)
				->update([
					'status' => 1
				]);

				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 1],
					['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
				);
				$knock_down_log->save();
			}

			$knock_down = KnockDown::where('remark','=',$location)
			->where('status','=',0)
			->orderBy('kd_number', 'desc')
			->first();

			$knock_down = KnockDown::where('kd_number', '=', $kd_number)->first();

			$knock_down_detail = KnockDownDetail::where('kd_number', '=', $kd_number)
			->select('knock_down_details.id')
			->orderBy('knock_down_details.created_at', 'desc')
			->first();

			$response = array(
				'status' => true,
				'message' => 'Material Sukses Ditambahkan',
				'actual_count' => $knock_down->actual_count,
				'knock_down_detail_id' => $knock_down_detail->id,
				'kd_number' => $kd_number
			);
			return Response::json($response);
		}catch(Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}		
	}

	public function printLabel(Request $request){
		$prefix_now = 'KD'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','kd')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$picks = $request->get('pick');
		$location = $request->get('location');
		$knock_down_detail_id = array();


		// dd(count($picks));

		$count = 0;

		for ($i=0; $i < count($picks); $i++) {

			$loop = $picks[$i]['quantity'];

			for ($j=0; $j < $loop; $j++) {

				//Inisiasi Variabel
				$production_id = $picks[$i]['production_id'];
				$production_schedule = ProductionSchedule::where('id', $production_id)->first();
				$material_number = $production_schedule->material_number;

				$material_volumes = MaterialVolume::where('material_number', $material_number)->first();
				$quantity = $material_volumes->lot_completion;

				//Production Schedule
				$production_schedule->actual_quantity = $production_schedule->actual_quantity + $quantity;


				$storage_location = Material::where('material_number', $material_number)->first();
				$storage_location = $storage_location->issue_storage_location;


				//KnockDown
				$code_generator = CodeGenerator::where('note','=','kd')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$kd_number = $code_generator->prefix . $number;
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$knock_down = new KnockDown([
					'kd_number' => $kd_number,
					'created_by' => Auth::id(),
					'actual_count' => 1,
					'remark' => $location,
					'status' => 1,
				]);

				array_push($knock_down_detail_id, $kd_number);


				//KnockDown Log
				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 1],
					['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
				);

				//Shipment
				$shipment_schedule = ShipmentSchedule::where('material_number', '=', $material_number)
				->where('actual_quantity','<',db::raw('quantity'))
				->orderBy('st_date', 'asc')
				->orderBy('id', 'asc')
				->first();

				if($shipment_schedule){
					$shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $quantity;

					//KnockDown Detail
					$knock_down_detail = new KnockDownDetail([
						'kd_number' => $kd_number,
						'material_number' => $material_number,
						'quantity' => $quantity,
						'shipment_schedule_id' => $shipment_schedule->id,
						'storage_location' => $storage_location,
						'created_by' => Auth::id(),
					]);
				}else{
					//KnockDown Detail
					$knock_down_detail = new KnockDownDetail([
						'kd_number' => $kd_number,
						'material_number' => $material_number,
						'quantity' => $quantity,
						'storage_location' => $storage_location,
						'created_by' => Auth::id(),
					]);
				}

				//Inventory
				$inventory = Inventory::where('plant','=','8190')
				->where('material_number','=',$material_number)
				->where('storage_location','=',$storage_location)
				->first();

				if($inventory){
					$inventory->quantity = $inventory->quantity + $quantity;
				}else{	
					$inventory = new Inventory([
						'plant' => '8190',
						'material_number' => $material_number,
						'storage_location' => $storage_location,
						'quantity' => $quantity,
					]);
				}

				$child = BomComponent::where('material_parent', $material_number)->get();
				for ($x=0; $x < count($child); $x++) { 
					$inv_child = Inventory::where('plant','=','8190')
					->where('material_number','=',$child[$x]->material_child)
					->where('storage_location','=',$storage_location)
					->first();

					if($inv_child){
						$inv_child->quantity = $inv_child->quantity - ($quantity * $child[$x]->usage);
						$inv_child->save();
					}
				}


				//Transaction Completion
				$transaction_completion = new TransactionCompletion([
					'serial_number' => $kd_number,
					'material_number' => $material_number,
					'issue_plant' => '8190',
					'issue_location' => $storage_location,
					'quantity' => $quantity,
					'movement_type' => '101',
					'created_by' => Auth::id(),
				]);

				try{
					DB::transaction(function() use ($production_schedule, $knock_down, $knock_down_detail, $knock_down_log, $inventory, $transaction_completion, $shipment_schedule){
						$production_schedule->save();
						$knock_down->save();
						$knock_down_detail->save();
						$knock_down_log->save();
						$inventory->save();
						$transaction_completion->save();

						if($shipment_schedule){
							$shipment_schedule->save();
						}

					});

					$count++;


				}catch(Exception $e) {
					$error_log = new ErrorLog([
						'error_message' => $e->getMessage(),
						'created_by' => $id
					]);
					$error_log->save();
				}
			}
		}

		$knock_down_detail = KnockDownDetail::whereIn('kd_number', $knock_down_detail_id)
		->select('knock_down_details.id')
		->get();

		$response = array(
			'status' => true,
			'message' => 'Print Label Sukses',
			'item_inputted' => $count,
			'knock_down_detail' => $knock_down_detail
		);
		return Response::json($response);
	}

	public function printKDO($kd_number, $st_date, $knock_down_details, $storage_location, $remark, $destination_shortname){

		if(Auth::user()->role_code == 'op-zpro'){
			$printer_name = 'KDO ZPRO';
		}else if (Auth::user()->role_code == 'OP-WH-Exim'){
			$printer_name = 'FLO Printer LOG';
		}else{
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if($remark == 'REPRINT'){
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setReverseColors(true);
			$printer->setTextSize(2, 2);
			$printer->text(" REPRINT "."\n");
			$printer->feed(1);
		}

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Storage Location:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(3, 3);
		$printer->text(strtoupper($storage_location."\n"));
		$printer->initialize();

		$printer->setUnderline(true);
		$printer->text('KDO:');
		$printer->feed(1);
		$printer->setUnderline(false);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($kd_number."\n");

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Destination:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(6, 3);
		$printer->text(strtoupper($destination_shortname."\n\n"));


		$printer->initialize();
		$printer->setUnderline(true);
		$printer->text('Shipment Date:');
		$printer->setUnderline(false);
		$printer->feed(1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(4, 2);
		$printer->text(date('d-M-Y', strtotime($st_date))."\n\n");
		$printer->initialize();
		$printer->text("No |GMC     | Description                 | Qty ");
		$total_qty = 0;
		for ($i=0; $i < count($knock_down_details); $i++) {
			$number = $this->writeString($i+1, 2, ' ');
			$qty = $this->writeString($knock_down_details[$i]->quantity, 4, ' ');
			$material_description = substr($knock_down_details[$i]->material_description, 0,27);
			$material_description = $this->writeString($material_description, 27, ' ');
			$printer->text($number." |".$knock_down_details[$i]->material_number." | ".$material_description." | ".$qty);
			$total_qty += $knock_down_details[$i]->quantity;
		}
		$printer->feed(2);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Qty:             |Qty:            |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|Production       |Logistic        |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->text("Total Qty: ". $total_qty ."\n");
		$printer->feed(2);
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function printKDOSub($knock_down_details){
		$receive = $knock_down_details->storage_location;

		if(Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'S'){
			$printer_name = 'MIS';
		}else{
			if($receive == 'CL91' || $receive == 'CLB9'){
				$printer_name = 'FLO Printer 102';
			}
			else if($receive == 'SX91'){
				$printer_name = 'FLO Printer 103';
			}
			else if($receive == 'FL91'){
				$printer_name = 'FLO Printer 101';
			}
			else if($receive == 'SX51' || $receive == 'CL51' || $receive == 'FL51' || $receive == 'VN51'){
				$printer_name = 'Stockroom-Printer';			
			}
			else if($receive == 'SX21' || $receive == 'CL21' || $receive == 'FL21' || $receive == 'VN21'){
				$printer_name = 'Welding-Printer';			
			}
			else if($receive == 'CS91'){
				$printer_name = 'KDO CASE';
			}
			else if($receive == 'PN91'){
				$printer_name = 'FLO Printer 104';
			}
			else if($receive == 'VN91' || $receive == 'VN11'){
				$printer_name = 'FLO Printer VN';
			}
			else{
				$printer_name = 'MIS';
			}
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Storage Location:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(3, 3);
		$printer->text(strtoupper($knock_down_details->location."\n"));
		$printer->initialize();

		$printer->setUnderline(true);
		$printer->text('KDO:');
		$printer->feed(1);
		$printer->setUnderline(false);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($knock_down_details->kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($knock_down_details->kd_number."\n");

		if($knock_down_details->destination_shortname != null){
			$printer->initialize();
			$printer->setJustification(Printer::JUSTIFY_LEFT);
			$printer->setUnderline(true);
			$printer->text('Destination:');
			$printer->setUnderline(false);
			$printer->feed(1);
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setTextSize(4, 3);
			$printer->text(strtoupper($knock_down_details->destination_shortname."\n\n"));
		}

		if($knock_down_details->st_date != null){
			$printer->initialize();
			$printer->setUnderline(true);
			$printer->text('Shipment Date:');
			$printer->setUnderline(false);
			$printer->feed(1);
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setTextSize(4, 2);
			$printer->text(date('d-M-Y', strtotime($knock_down_details->st_date))."\n\n");
		}

		$printer->feed(2);
		$printer->initialize();
		$printer->text("GMC     | Description                     | Qty ");
		$qty = $this->writeString($knock_down_details->quantity, 4, ' ');
		$material_description = substr($knock_down_details->material_description, 0,31);
		$material_description = $this->writeString($material_description, 31, ' ');
		$printer->text($knock_down_details->material_number." | ".$material_description." | ".$qty);

		$printer->feed(3);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Qty:             |Qty:            |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|Production       |Logistic        |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->text("Total Qty: ". $qty ."\n");
		$printer->feed(3);
		$printer->cut();
		$printer->close();
	}

	public function writeString($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$text .= $char;
			}
		}
		return strtoupper($text);
	}




}

