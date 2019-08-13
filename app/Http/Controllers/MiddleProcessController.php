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
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class MiddleProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
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

	public function indexProcessMiddleKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();

		if($id == 'lcq-incoming'){
			$title = 'I.C. Saxophone Key Lacquering';
			$title_jp= '?';
		}
		if($id == 'incoming-lcq2'){
			$title = 'I.C. Saxophone Key After Treatment Lacquering';
			$title_jp= '?';
		}
		if($id == 'incoming-plt-sx'){
			$title = 'I.C. Saxophone Key Plating';
			$title_jp= '?';
		}
		if($id == 'kensa-lcq'){
			$title = 'Kensa Saxophone Key Lacquering';
			$title_jp= '?';
		}
		if($id == 'kensa-plt-sx'){
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
		$employee = db::table('employees')->where('employee_id', '=', $request->get('employee_id'))->first();

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

	public function printMiddleBarrelReprint(Request $request){

		if(Auth::user()->role_code == "OP-Barrel-SX"){
			$printer_name = 'Barrel-Printer';
		}
		if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS"){
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

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
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(1,1);
				$printer->text('ID SLIP '. date('d-M-Y H:i:s')." Reprint\n");
				$printer->setTextSize(4,4);
				$printer->setUnderline(true);
				$printer->text('PLATING'."\n\n");
				$printer->initialize();
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(5,2);
				if($middle_inventory->hpl == 'TSKEY'){
					$printer->setEmphasis(true);
					$printer->setReverseColors(true);					
				}
				$printer->text($middle_inventory->model." ".$middle_inventory->key."\n");
				$printer->text($middle_inventory->surface."\n\n");
				$printer->initialize();
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->qrCode($middle_inventory->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
				$printer->text($middle_inventory->tag."\n\n");
				$printer->initialize();
				$printer->setTextSize(1,1);
				$printer->text("GMC : ".$middle_inventory->material_number."\n");
				$printer->text("DESC: ".$middle_inventory->material_description."\n");
				$printer->text("QTY : ".$middle_inventory->quantity." PC(S)"."\n");
				$printer->cut(Printer::CUT_PARTIAL, 50);
				$printer->close();
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
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(1,1);
						$printer->text('ID SLIP '.date('d-M-Y H:i:s')." Reprint\n");
						$printer->setTextSize(4,4);
						$printer->setUnderline(true);
						$printer->text('LACQUERING'."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(5,2);
						if($barrel->hpl == 'TSKEY'){
							$printer->setEmphasis(true);
							$printer->setReverseColors(true);					
						}
						$printer->text($barrel->model." ".$barrel->key."\n");
						$printer->text($barrel->surface."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel->tag."\n\n");
						$printer->initialize();
						$printer->setTextSize(1,1);
						$printer->text("GMC : ".$barrel->material_number."\n");
						$printer->text("DESC: ".$barrel->material_description."\n");	
						$printer->text("QTY : ".$barrel->quantity." PC(S)                 MACHINE: FLANEL\n");
						$printer->cut(Printer::CUT_PARTIAL, 50);
						$printer->close();
					}
					else{
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(1,1);
						$printer->text('ID SLIP '.date('d-M-Y H:i:s')." Reprint\n");
						$printer->setTextSize(4,4);
						$printer->setUnderline(true);
						$printer->text('LACQUERING'."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(5,2);
						if($barrel->hpl == 'TSKEY'){
							$printer->setEmphasis(true);
							$printer->setReverseColors(true);					
						}
						$printer->text($barrel->model." ".$barrel->key."\n");
						$printer->text($barrel->surface."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel->tag."\n\n");
						$printer->initialize();
						$printer->setTextSize(1,1);
						$printer->text("GMC : ".$barrel->material_number."                           ".$barrel->remark."\n");
						$printer->text("DESC: ".$barrel->material_description."\n");	
						$printer->text("QTY : ".$barrel->quantity." PC(S)                 MACHINE: ".$barrel->machine." JIG: ".$barrel->jig."\n");	
						$printer->cut(Printer::CUT_PARTIAL, 50);
						$printer->close();
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
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(4,4);
						$printer->text('BARREL'."\n");
						$printer->text("MACHINE_".$barrel->machine."\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel->remark, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel->remark."\n\n");
						$printer->cut();
						$printer->close();
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
			->limit(30)
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
		// $count = BarrelQueue::count();
		// if($count >= 64 && ($count-count($request->get('tag'))) <= 64){
			// self::sendEmailMinQueue();
		// 	return Response::json('okecok');
		// }

		$id = Auth::id();

		if(Auth::user()->role_code == "OP-Barrel-SX"){
			$printer_name = 'Barrel-Printer';
		}
		if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS"){
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if($request->get('surface') == 'LCQ'){
			if($request->get('code') == 'FLANEL'){
				try{
					$tags = $request->get('tag');

					foreach ($tags as $tag) {
						$barrel_queue = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
						->where('barrel_queues.tag', '=', $tag[0])
						->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.key', 'materials.hpl', 'materials.model', 'materials.surface', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
						->first();

						$insert_jig = [
							'machine' => $request->get('code'),
							'jig' => 0,
							'key' => $barrel_queue->key,
							'tag' => $tag[0],
							'material_number' => $barrel_queue->material_number,
							'qty' => $barrel_queue->quantity,
							'status' => $request->get('code'),
							'remark' => $request->get('code'),
							'created_by' => $id
						];

						$insert_inventory = [
							'tag' => $tag[0],
							'material_number' => $barrel_queue->material_number,
							'quantity' => $barrel_queue->quantity,
							'location' => 'barrel',
						];

						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(1,1);
						$printer->text('ID SLIP '.date('d-M-Y H:i:s')." ".$barrel_queue->remark."\n");
						$printer->setTextSize(4,4);
						$printer->setUnderline(true);
						$printer->text('LACQUERING'."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(5,2);
						if($barrel_queue->hpl == 'TSKEY'){
							$printer->setEmphasis(true);
							$printer->setReverseColors(true);					
						}
						$printer->text($barrel_queue->model." ".$barrel_queue->key."\n");
						$printer->text($barrel_queue->surface."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel_queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel_queue->tag."\n\n");
						$printer->initialize();
						$printer->setTextSize(1,1);
						$printer->text("GMC : ".$barrel_queue->material_number."\n");
						$printer->text("DESC: ".$barrel_queue->material_description."\n");	
						$printer->text("QTY : ".$barrel_queue->quantity." PC(S)                 MACHINE: ".$request->get('code')."\n");
						$printer->cut(Printer::CUT_PARTIAL, 50);
						$printer->close();

						$barrel = new Barrel($insert_jig);
						$delete_queue = BarrelQueue::where('tag', '=', $tag[0]);

						$middle_inventory = MiddleInventory::firstOrcreate(
							[
								'tag' => $tag[0]
							],
							$insert_inventory
						);
						
						DB::transaction(function() use ($barrel, $delete_queue){
							$barrel->save();
							$delete_queue->forceDelete();
						});

					}

					$response = array(
						'status' => true,
						'message' => 'New kanban has been printed',
						'tes' => $tags,
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
				$code_generator = CodeGenerator::where('note','=','barrel_machine')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$qr_machine = $code_generator->prefix . $number;
				try{
					$tags = $request->get('tag');
					$code_generator->index = $code_generator->index+1;
					$code_generator->save();

					foreach ($tags as $tag) {
						$barrel_queue = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
						->where('barrel_queues.tag', '=', $tag[0])
						->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.key', 'materials.model', 'materials.surface', 'materials.hpl', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
						->first();

						$insert_jig = [
							'machine' => $request->get('no_machine'),
							'jig' => $tag[1],
							'key' => $barrel_queue->key,
							'tag' => $tag[0],
							'material_number' => $barrel_queue->material_number,
							'qty' => $barrel_queue->quantity,
							'status' => 'racking',
							'remark' => $qr_machine,
							'created_by' => $id
						];

						$insert_inventory = [
							'tag' => $tag[0],
							'material_number' => $barrel_queue->material_number,
							'quantity' => $barrel_queue->quantity,
							'location' => 'barrel',
						];

						$footer = EscposImage::load(public_path('mirai.jpg'));
						// $printer->bitImage($footer);

						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(1,1);
						$printer->text('ID SLIP '.date('d-M-Y H:i:s')." ".$barrel_queue->remark."\n");
						$printer->setTextSize(4,4);
						$printer->setUnderline(true);
						$printer->text('LACQUERING'."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(5,2);
						if($barrel_queue->hpl == 'TSKEY'){
							$printer->setEmphasis(true);
							$printer->setReverseColors(true);					
						}
						$printer->text($barrel_queue->model." ".$barrel_queue->key."\n");
						$printer->text($barrel_queue->surface."\n\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel_queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel_queue->tag."\n\n");
						$printer->initialize();
						$printer->setTextSize(1,1);
						$printer->text("GMC : ".$barrel_queue->material_number."                           ".$qr_machine."\n");
						$printer->text("DESC: ".$barrel_queue->material_description."\n");	
						$printer->text("QTY : ".$barrel_queue->quantity." PC(S)                 MACHINE: ".$request->get('no_machine')." JIG: ".$tag[1]."\n");
						$printer->cut(Printer::CUT_PARTIAL, 50);
						$printer->close();

						$barrel = new Barrel($insert_jig);
						$delete_queue = BarrelQueue::where('tag', '=', $tag[0]);

						$middle_inventory = MiddleInventory::firstOrcreate(
							[
								'tag' => $tag[0]
							],
							$insert_inventory
						);

						DB::transaction(function() use ($barrel, $delete_queue){
							$barrel->save();
							$delete_queue->forceDelete();
						});

					}

					$response = array(
						'status' => true,
						'message' => 'New id slip for lacquering has been printed',
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
		elseif($request->get('surface') == 'PLT'){
			$tags = $request->get('tag');

			try{

				foreach ($tags as $tag) {

					$queue = BarrelQueue::leftJoin('materials', 'materials.material_number', 'barrel_queues.material_number')
					->where('barrel_queues.tag', '=', $tag[0])
					// ->whereIn('barrel_queues.tag', $request->get('tag'))
					->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.model', 'materials.hpl', 'materials.key', 'materials.surface', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
					->first();

					$insert_log = [
						'machine' => 'PLT',
						'tag' => $queue->tag,
						'material' => $queue->material_number,
						'qty' => $queue->quantity,
						'status' => 'plt',
						'started_at' => date('Y-m-d H:i:s'),
						'created_by' => $id,
					];

					$insert_inventory = [
						'tag' => $queue->tag,
						'material_number' => $queue->material_number,
						'quantity' => $queue->quantity,
						'location' => 'barrel',
					];

					$barrel_log = new BarrelLog($insert_log);
					$delete_queue = BarrelQueue::where('tag', '=', $queue->tag);

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(1,1);
					$printer->text('ID SLIP '. date('d-M-Y H:i:s')." ".$queue->remark."\n");
					$printer->setTextSize(4,4);
					$printer->setUnderline(true);
					$printer->text('PLATING'."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(5,2);
					if($queue->hpl == 'TSKEY'){
						$printer->setEmphasis(true);
						$printer->setReverseColors(true);					
					}
					$printer->text($queue->model." ".$queue->key."\n");
					$printer->text($queue->surface."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->qrCode($queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
					$printer->text($queue->tag."\n\n");
					$printer->initialize();
					$printer->setTextSize(1,1);
					$printer->text("GMC : ".$queue->material_number."\n");
					$printer->text("DESC: ".$queue->material_description."\n");
					$printer->text("QTY : ".$queue->quantity." PC(S)"."\n");
					$printer->cut(Printer::CUT_PARTIAL, 50);
					$printer->close();

					$middle_inventory = MiddleInventory::firstOrcreate(
						[
							'tag' => $queue->tag
						],
						$insert_inventory
					);

					DB::transaction(function() use ($barrel_log, $delete_queue){
						$barrel_log->save();
						$delete_queue->forceDelete();
					});
				}

				$response = array(
					'status' => true,
					'message' => 'New ID slip for plating has been printed',
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

		elseif($request->get('surface') == 'FLANEL'){
			try{
				$tags = $request->get('tag');

				foreach ($tags as $tag) {
					$barrel_queue = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
					->where('barrel_queues.tag', '=', $tag[0])
					->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.key', 'materials.model', 'materials.hpl', 'materials.surface', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
					->first();

					$insert_jig = [
						'machine' => $request->get('code'),
						'jig' => 0,
						'key' => $barrel_queue->key,
						'tag' => $tag[0],
						'material_number' => $barrel_queue->material_number,
						'qty' => $barrel_queue->quantity,
						'status' => $request->get('code'),
						'remark' => $request->get('code'),
						'created_by' => $id
					];

					$insert_inventory = [
						'tag' => $tag[0],
						'material_number' => $barrel_queue->material_number,
						'quantity' => $barrel_queue->quantity,
						'location' => 'barrel',
					];

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(1,1);
					$printer->text('ID SLIP '.date('d-M-Y H:i:s')." ".$barrel_queue->remark."\n");
					$printer->setTextSize(4,4);
					$printer->setUnderline(true);
					$printer->text('LACQUERING'."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(5,2);
					if($barrel_queue->hpl == 'TSKEY'){
						$printer->setEmphasis(true);
						$printer->setReverseColors(true);					
					}
					$printer->text($barrel_queue->model." ".$barrel_queue->key."\n");
					$printer->text($barrel_queue->surface."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->qrCode($barrel_queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
					$printer->text($barrel_queue->tag."\n\n");
					$printer->initialize();
					$printer->setTextSize(1,1);
					$printer->text("GMC : ".$barrel_queue->material_number."\n");
					$printer->text("DESC: ".$barrel_queue->material_description."\n");	
					$printer->text("QTY : ".$barrel_queue->quantity." PC(S)                 MACHINE: ".$request->get('code')."\n");
					$printer->cut(Printer::CUT_PARTIAL, 50);
					$printer->close();

					$barrel = new Barrel($insert_jig);
					$delete_queue = BarrelQueue::where('tag', '=', $tag[0]);

					$middle_inventory = MiddleInventory::firstOrcreate(
						[
							'tag' => $tag[0]
						],
						$insert_inventory
					);

					DB::transaction(function() use ($barrel, $delete_queue){
						$barrel->save();
						$delete_queue->forceDelete();
					});

				}

				$response = array(
					'status' => true,
					'message' => 'New kanban has been printed',
					'tes' => $tags,
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

	public function scanMiddleBarrel(Request $request){	
		$id = Auth::id();
		
		if(Auth::user()->role_code == "OP-Barrel-SX"){
			$printer_name = 'Barrel-Printer';
		}
		if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS"){
			$printer_name = 'MIS';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

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

						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(4,4);
						$printer->text('BARREL'."\n");
						$printer->text("MACHINE_".$barrel->machine."\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel->remark, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel->remark."\n\n");
						$printer->cut();
						$printer->close();

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

		$result = MiddleLog::where('employee_id', '=', $request->get('employee_id'))
		->whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d") = "'. date('Y-m-d') .'"')
		->sum('quantity');

		$ng = MiddleNgLog::where('employee_id', '=', $request->get('employee_id'))
		->whereRaw('DATE_FORMAT(created_at,"%Y-%m-%d") = "'. date('Y-m-d') .'"')
		->sum('quantity');

		$response = array(
			'status' => true,
			'result' => $result,
			'ng' => $ng,
		);
		return Response::json($response);
	}

	public function ScanMiddleKensa(Request $request){
		$id = Auth::id();

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

		// $tag_material = TagMaterial::where('tag_materials.tag', '=', $request->get('tag'))
		// ->first();

		// $ngName = $request->get('ng_name');
		// $ngQty = $request->get('ng_qty');
		// $count_text = $request->get('count_text');
		// $prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');
		// $id = Auth::id();

		// for ($i=0; $i < count($ngName); $i++) {
		// 	try{
		// 		$log_ng_middle = new LogNgMiddle([
		// 			'group_code' => $request->get('group'),
		// 			'op_kensa' => $request->get('opKensa'),
		// 			'prod_date' => $prodDate,
		// 			'tag' => $request->get('tag'),
		// 			'material_number' => $tag_material->material_number,
		// 			'location' => $request->get('location'),
		// 			'ng_name' => $ngName[$i],
		// 			'qty' => $ngQty[$i],
		// 			'op_prod' => $tag_material->op_prod,
		// 			'created_by' => $id,
		// 		]);
		// 		$log_ng_middle->save();
		// 		$success_count[] = $count_text[$i];
		// 	}
		// 	catch (QueryException $e){
		// 		$fail_count[] = $count_text[$i];
		// 	}
		// }

		// if(isset($fail_count)){
		// 	$response = array(
		// 		'status' => false,
		// 		'fail_count' => $fail_count,
		// 		'message' => 'Material NG has some errors',
		// 	);
		// 	return Response::json($response);
		// }
		// else{
		// 	$response = array(
		// 		'status' => true,
		// 		'success_count' => $success_count,
		// 		'message' => 'Material NG has been inputted',
		// 	);
		// 	return Response::json($response);
		// }
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
					'remark' => 'add',
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
		->where('surface','like','%'.$request->get('surface'))
		->where('hpl', $request->get('key'))
		->where(db::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"),"=", $now)
		->where(db::raw("DATE_FORMAT(created_at,'%H:%i:%s')"), '>=', $awal)
		->where(db::raw("DATE_FORMAT(created_at,'%H:%i:%s')"), '<', $akhir)
		->select('model','key', db::raw("SUM(IF(`status`='set',qty,0)) as `set`"), db::raw("SUM(IF(`status`='reset',qty,0)) as `reset`"), db::raw("SUM(IF(`status`='plt',qty,0)) as `plt`"))
		->groupBy('model','key')
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
		->where('remark', '=', 'barrel_queue')
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
}