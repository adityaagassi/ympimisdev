<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Exception;
use DataTables;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\AssemblyDetail;
use App\AssemblyInventory;
use App\AssemblyLog;
use App\AssemblyNgTemp;
use App\AssemblyNgLog;
use App\AssemblyTag;
use App\AssemblyOnko;
use App\AssemblyFlow;
use App\AssemblySerial;
use App\LogProcess;
use App\StampInventory;
use App\Process;
use App\Material;
use App\Assembly;
use DateTime;
use App\Libraries\ActMLEasyIf;
use App\CodeGenerator;
use App\PlcCounter;

class AssemblyProcessController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
			if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
			{
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
				die();
			}
		}
		$this->location_fl = [
			'kariawase-fungsi',
			'kariawase-visual',
			'perakitanawal-kensa',
			'tanpoawase-kensa',
			'tanpoawase-fungsi',
			'kango-fungsi',
			'kango-kensa',
			'renraku-fungsi',
			'qa-fungsi',
			'fukiage1-visual',
			'qa-visual1',
			'qa-visual2',
			'qa-kensasp',
		];

		$this->location_fl_display = [
			'stamp',
			'perakitan',
			'kariawase',
			'tanpoire',
			'perakitanawal',
			'tanpoawase',
			'seasoning',
			'kango',
			'renraku',
			'qa-fungsi',
			'fukiage1',
			'fukiage2',
			'qa-visual1',
			'qa-visual2',
			'packing'
		];
	}

	public function stampFluteAdjustSerial(Request $request){
		if($request->get('adjust') == 'minus'){
			$code_generator = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
			$code_generator->index = $code_generator->index-1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted',
			);
			return Response::json($response);
		}
		else{
			$code_generator = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted',
			);
			return Response::json($response);
		}		
	}

	public function indexFluteStamp(){

		$models = db::table('materials')->where('origin_group_code', '=', '041')
		->where('category', '=', 'FG')
		->orderBy('model', 'asc')
		->select('model')
		->distinct()
		->get();

		$title = 'Flute Stamp';
		$title_jp = 'フルートの刻印';
		return view('processes.assembly.flute.stamp', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'models' => $models,
			'models2' => $models
		))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
	}

	public function stampFlute(Request $request){
		$counter = PlcCounter::where('origin_group_code', '=', $request->get('origin_group_code'))
		->first();
		$auth_id = Auth::id();

		  $plc = new ActMLEasyIf(0);
		  $datas = $plc->read_data('D50', 5);

		 if($counter->plc_counter == $datas[0]){
		// if($counter->plc_counter == 36){
			$response = array(
				'status' => true,
				'status_code' => 'no_stamp',
			);
			return Response::json($response);
		}

		try{
			$cek_serial = new AssemblySerial([
				'serial_number' => $request->get('serial'),
				'origin_group_code' => $request->get('origin_group_code'),
				'created_by' => $auth_id
			]);
			$cek_serial->save();
		}
		catch(QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				$response = array(
					'status' => false,
					'message' => "Serial number sudah pernah discan.",
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$taghex = $this->dec2hex($request->get('tagBody'));

		$tag = AssemblyTag::where('remark', '=', $request->get('tagName'))->where('tag', '=', $taghex)->first();
		// $material = db::table('materials')->where('model', '=', $request->get('model'))
		// ->where('xy', '=', 'SP')->first();

		$log = new AssemblyDetail([
			'tag' => $tag->tag,
			'serial_number' => $request->get('serial'),
			'model' => $request->get('model'),
			'location' => $request->get('location'),
			'operator_id' => $request->get('op_id'),
			'sedang_start_date' => $request->get('started_at'),
			'sedang_finish_date' => date('Y-m-d H:i:s'),
			'origin_group_code' => $request->get('origin_group_code'),
			'created_by' => $request->get('op_id'),
			'is_send_log' => 0
		]);

		$sp = '';
		// if(count($material) > 0){
		// 	$sp = 'SP';
		// }

		if($request->get('location') != 'stampkd-process'){
			$inventory = AssemblyInventory::firstOrCreate(
				['serial_number' => $request->get('serial'), 'origin_group_code' => $request->get('origin_group_code')],
				['tag' => $tag->tag, 'model' => $request->get('model'), 'location' => $request->get('location'), 'location_next' => 'perakitan-process', 'remark' => $sp, 'created_by' => $request->get('op_id')]
			);
			$inventory->location = $request->get('location');

			$logProcess = new LogProcess([
				'process_code' => 1,
				'serial_number' => $request->get('serial'),
				'model' => $request->get('model'),
				'manpower' => 1,
				'origin_group_code' => $request->get('origin_group_code'),
				'created_by' => 1,
				'remark' => 'FG'
			]);
		}else{
			$logProcess = new LogProcess([
				'process_code' => 1,
				'serial_number' => $request->get('serial'),
				'model' => $request->get('model'),
				'manpower' => 1,
				'origin_group_code' => $request->get('origin_group_code'),
				'created_by' => 1,
			]);
		}

		$stampInventory = new StampInventory([
			'process_code' => 1,
			'serial_number' => $request->get('serial'),
			'model' => $request->get('model'),
			'quantity' => 1,
			'origin_group_code' => $request->get('origin_group_code'),
		]);

		$tag->serial_number = $request->get('serial');
		$tag->model = $request->get('model');
		$serial = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
		$serial->index = $serial->index+1;
		 $counter->plc_counter = $datas[0];
		// $counter->plc_counter = 36;

		try{
			if($request->get('location') != 'stampkd-process'){
				DB::transaction(function() use ($log, $inventory, $tag, $serial, $counter,$stampInventory,$logProcess){
					$inventory->save();
					$log->save();
					$tag->save();
					$serial->save();
					$counter->save();
					$stampInventory->save();
					$logProcess->save();
				});
			}
			else{
				DB::transaction(function() use ($log, $tag, $serial, $counter,$stampInventory,$logProcess){
					$log->save();
					$tag->save();
					$serial->save();
					$counter->save();
					$stampInventory->save();
					$logProcess->save();
				});
			}
			$this->printStamp($request->get('tagName'), $request->get('serial'), $request->get('model'), 'print', 'SUPERMAN');
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'status_code' => 'stamp',
			'message' => 'Stamp berhasil dilakukan',
		);
		return Response::json($response);
	}


	public function scanTagStamp(Request $request){
		$taghex = $this->dec2hex($request->get('tag'));

		$tag = AssemblyTag::whereNull('serial_number')
		->where('origin_group_code', '=', $request->get('origin_group_code'))
		->where('tag', '=', $taghex)
		->first();

		$started_at = date('Y-m-d H:i:s');

		if(count($tag) <= 0){
			$response = array(
				'status' => false,
				'message' => 'Tag tidak ditemukan / Tag masih aktif',
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Tag berhasil ditemukan',
			'tag' => $tag,
			'started_at' => $started_at
		);
		return Response::json($response);
	}

	public function fetchSerialNumber(Request $request){
		$serial = db::table('code_generators')->where('note', '=', $request->get('origin_group_code'))->first();

		$number = sprintf("%'.0" . $serial->length . "d", $serial->index);
		$number2 = sprintf("%'.0" . $serial->length . "d", $serial->index+1);

		$lastCounter = $serial->prefix.$number;
		$nextCounter = $serial->prefix.$number2;

		$response = array(
			'status' => true,
			'lastCounter' => $lastCounter,
			'nextCounter' => $nextCounter
		);
		return Response::json($response);
	}

	public function fetchStampResult(Request $request){
		$now = date('Y-m-d');
		$first = date('Y-m-d', strtotime("-3 days"));
		// $date = '2020-06-15';

		$logs = AssemblyDetail::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_details.operator_id')
		->where('assembly_details.origin_group_code', '=', $request->get('origin_group_code'))
		->where(db::raw('date(assembly_details.created_at)'), '>=', $first)
		->where('assembly_details.location', '=', 'stamp-process')
		->whereOr('assembly_details.location', '=', 'stampkd-process')
		->select('assembly_details.serial_number', 'assembly_details.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_details.created_at', 'assembly_details.id as id_details')
		->orderBy('assembly_details.created_at', 'desc')
		->get();

		$logsall = AssemblyInventory::Join('assembly_tags','assembly_tags.serial_number','assembly_inventories.serial_number')->get();

		$response = array(
			'status' => true,
			'logs' => $logs,
			'logsall' => $logsall,
		);
		return Response::json($response);

	}

	public function printStamp($tag, $serial_number, $model, $category, $printer_name){
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if($category == 'print'){
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setTextSize(3, 1);
			$printer->text($serial_number."\n");
			$printer->feed(1);
			$printer->text($tag.' '.$model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();	
		}
		if($category == 'reprint'){
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setTextSize(3, 1);
			$printer->text($serial_number."\n");
			$printer->feed(1);
			$printer->text($tag.' '.$model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."(Reprint)"."\n");
			$printer->cut();
			$printer->close();	
		}
	}

	public function editStamp(Request $request)
	{
		try {
			$details = AssemblyDetail::find($request->get('id'));

			$response = array(
				'status' => true,
				'details' => $details
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => 'Failed Get Data'
			);
			return Response::json($response);
		}
	}

	public function destroyStamp(Request $request)
	{
		$details = AssemblyDetail::find($request->get('id'));

		$inventories = AssemblyInventory::where('serial_number',$request->get('serial_number'))->where('model',$request->get('model'))->where('location',$details->location)->where('origin_group_code',$request->get('origin_group_code'))->first();

		$serials = AssemblySerial::where('serial_number',$request->get('serial_number'))->where('origin_group_code',$request->get('origin_group_code'))->first();

		$tag = AssemblyTag::where('serial_number',$request->get('serial_number'))->where('model',$request->get('model'))->where('origin_group_code',$request->get('origin_group_code'))->first();
		$tag->serial_number = null;
		$tag->model = null;

		$log_process = LogProcess::where('log_processes.serial_number', '=', $request->get('serial_number'))
		->where('log_processes.model', '=', $request->get('model'))
		->where('origin_group_code',$request->get('origin_group_code'));

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serial_number'))
		->where('stamp_inventories.model', '=', $request->get('model'))
		->where('origin_group_code',$request->get('origin_group_code'));

		try {
			$inventories->forceDelete();
			$serials->forceDelete();
			$tag->save();
			$details->forceDelete();
			$log_process->forceDelete();
			$stamp_inventory->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Delete Serial Number Berhasil'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => 'Failed Delete Data'
			);
			return Response::json($response);
		}
	}

	public function updateStamp(Request $request)
	{
		$details = AssemblyDetail::find($request->get('id'));
		$details->model = $request->get('model');

		$inventories = AssemblyInventory::where('serial_number',$request->get('serial_number'))->where('model',$request->get('model_asli'))->where('location',$details->location)->where('origin_group_code',$request->get('origin_group_code'))->first();
		$inventories->model = $request->get('model');

		$tag = AssemblyTag::where('serial_number',$request->get('serial_number'))->where('model',$request->get('model_asli'))->where('origin_group_code',$request->get('origin_group_code'))->first();
		$tag->model = $request->get('model');

		$log_process = LogProcess::where('log_processes.serial_number', '=', $request->get('serial_number'))
		->where('log_processes.model', '=', $request->get('model_asli'))
		->where('origin_group_code',$request->get('origin_group_code'))->first();
		$log_process->model = $request->get('model');

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serial_number'))
		->where('stamp_inventories.model', '=', $request->get('model_asli'))
		->where('origin_group_code',$request->get('origin_group_code'))->first();
		$stamp_inventory->model = $request->get('model');


		try {
			$inventories->save();
			$tag->save();
			$details->save();
			$log_process->save();
			$stamp_inventory->save();

			$response = array(
				'status' => true,
				'message' => 'Update Model Berhasil'
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => 'Failed Delete Data'
			);
			return Response::json($response);
		}
	}

	public function adjustStamp(Request $request)
	{
		try {
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

			$prefix = $code_generator->prefix;
			$lastIndex = $code_generator->index;

			$response = array(
				'status' => true,
				'prefix' => $prefix,
				'lastIndex' => $lastIndex,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function adjustStampUpdate(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$code_generator->index = $request->get('lastIndex');
		$code_generator->prefix = $request->get('prefix');
		$code_generator->save();

		$response = array(
			'status' => true,
			'message' => 'Serial number adjustment success',
		);
		return Response::json($response);
	}

	public function reprintStamp(Request $request)
	{
		try {
			$inventories = AssemblyInventory::join('assembly_tags','assembly_tags.serial_number','assembly_inventories.serial_number')->where('assembly_inventories.serial_number',$request->get('serial_number'))->where('assembly_inventories.origin_group_code',$request->get('origin_group_code'))->first();

			$this->printStamp($inventories->remark, $request->get('serial_number'), $inventories->model, 'reprint', 'SUPERMAN');

			$response = array(
				'status' => true,
				'message' => 'Reprint Success',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function indexFlutePrintLabel(){
		$title = 'Flute Print Packing Labels';
		$title_jp = 'FLラベル印刷';
		return view('processes.assembly.flute.print_label', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
	}

	public function indexFlutePrintLabelBackup(){
		$title = 'Flute Print Packing Labels';
		$title_jp = '(Flute Print Packing Labels)';
		return view('processes.assembly.flute.print_label_backup', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
	}

	public function indexAssemblyBoard($location)
	{
		$loc_code = explode('-', $location);
		$process = $loc_code[0];
		$loc_spec = $loc_code[1];

		if($location == 'perakitan-process'){
			$title = 'Perakitan Process Flute';
			$title_jp= 'FL組立';
		}
		if($location == 'kariawase-process'){
			$title = 'Kariawase Process Flute';
			$title_jp= 'FL仮合わせ';
		}
		if($location == 'tanpoawase-process'){
			$title = 'Tanpo Awase Process Flute';
			$title_jp= 'FLタンポ合わせ';
		}
		if($location == 'perakitanawal-kensa,tanpoawase-process'){
			$title = 'Perakitan Ulang & Tanpo Awase Process Flute';
			$title_jp= 'フルートの再組立・タンポ合わせ';
		}
		if($location == 'kariawase-fungsi,kariawase-visual,kariawase-repair,tanpoire-process'){
			$title = 'Kariawase & Tanpoire Process Flute';
			$title_jp= 'フルートの仮合わせ・タンポ入れ';
		}
		if($location == 'tanpoawase-kensa,tanpoawase-fungsi,repair-process-1,repair-process-2'){
			$title = 'Tanpoawase Kensa & Fungsi Flute';
			$title_jp= 'フルートのタンポ合わせ・機能の検査';
		}
		if($location == 'seasoning-process'){
			$title = 'Seasoning Process Flute';
			$title_jp= 'フルートのシーズニング';
		}
		if($location == 'kango-process'){
			$title = 'Kango Process Flute';
			$title_jp= 'フルートの嵌合';
		}
		if($location == 'kango-fungsi,renraku-process'){
			$title = 'Renraku Process Flute';
			$title_jp= 'フルートの連絡';
		}
		if($location == 'kango-kensa,renraku-fungsi'){
			$title = 'Cek Fungsi Akhir Flute';
			$title_jp= 'フルートの最終機能検査';
		}
		if($location == 'renraku-repair,qa-fungsi'){
			$title = 'Cek Fungsi QA Flute';
			$title_jp= 'フルートののQA機能検査';
		}
		if($location == 'fukiage1-process,repair-ringan'){
			$title = 'Fukiage 1 Flute';
			$title_jp= 'フルートの拭き上げ ①';
		}
		if($location == 'fukiage1-visual,qa-visual1,fukiage2-process,qa-visual2,pakcing'){
			$title = 'QA Visual, Fukiage 2, & Packing Flute';
			$title_jp= 'フルートのQA外観検査、拭き上げ②、梱包';
		}

		return view('processes.assembly.flute.display.board', array(
			'loc' => $location,
			'loc2' => $location,
			'process' => $process,
			'loc_spec' => $loc_spec,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location',$location);
	}

	public function label_carb_fl($id){

		$date = db::select("SELECT DATE_FORMAT( sedang_start_date, '%m-%Y' ) AS tgl FROM assembly_logs
			WHERE location = 'packing'
			AND origin_group_code = '041'
			AND serial_number = '".$id."'");

		return view('processes.assy_fl.label_temp.label_carb',array(
			'date' => $date,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function labelDeskripsi($id,$remark){
		$barcode = DB::select("SELECT serial_number, model FROM assembly_logs 
			WHERE origin_group_code = '041' 
			AND location = 'packing' 
			AND serial_number = '".$id."'");

		return view('processes.assembly.flute.label.label_desc',array(
			'barcode' => $barcode,
			'sn' => $id,
			'remark' => $remark,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function labelKecil2Fl($id,$remark){
		$barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '".$id."'
			and location = 'packing'
			and origin_group_code = '041')");

		$des = DB::select("SELECT serial_number, model FROM assembly_logs 
			WHERE origin_group_code = '041' 
			AND location = 'packing' 
			AND serial_number = '".$id."'");

		return view('processes.assembly.flute.label.label_kecil2',array(
			'barcode' => $barcode,
			'sn' => $id,
			'remark' => $remark,
			'des' => $des,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function labelKecilFl($id,$remark){
		$barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '".$id."'
			and location = 'packing'
			and origin_group_code = '041')");

		$des = DB::select("SELECT serial_number, model FROM assembly_logs 
			WHERE origin_group_code = '041' 
			AND location = 'packing' 
			AND serial_number = '".$id."'");

		return view('processes.assembly.flute.label.label_kecil',array(
			'barcode' => $barcode,
			'sn' => $id,
			'remark' => $remark,
			'des' => $des,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function labelBesarFl($id,$gmc,$remark){

		if($remark == 'P'){
			$now = new DateTime();

			$sp = AssemblyInventory::where('serial_number', $id)
			->where('origin_group_code', '041')
			->where('remark', 'SP')
			->first();

			if($sp){
				$inventory = AssemblyInventory::where('serial_number', $id)
				->where('origin_group_code', '041')
				->update([
					'location' => 'seasoningsp-process'
				]);

				$material = Material::where('material_number', $gmc)->first();
				$packing = new AssemblyDetail([
					'tag' => $sp->tag,
					'serial_number' => $id,
					'model' => $material->material_description,
					'location' => 'packing',
					'operator_id' => Auth::user()->username,
					'sedang_start_date' => $now,
					'sedang_finish_date' => $now,
					'origin_group_code' => '041',
					'created_by' => Auth::user()->username
				]);
				$packing->save();

				$seasoning = new AssemblyDetail([
					'tag' => $sp->tag,
					'serial_number' => $id,
					'model' => $material->material_description,
					'location' => 'seasoningsp-process',
					'operator_id' => Auth::user()->username,
					'sedang_start_date' => $now,
					'sedang_finish_date' => $now,
					'origin_group_code' => '041',
					'created_by' => Auth::user()->username
				]);
				$seasoning->save();
			}

			$details = AssemblyDetail::where('serial_number', $id)
			->where('origin_group_code', '041')
			->where('is_send_log', '0')
			->get();

			if(count($details) > 0){
				foreach ($details as $detail) {
					$detail = new AssemblyLog([
						'tag' => $detail->tag,
						'serial_number' => $detail->serial_number,
						'model' => $detail->model,
						'location' => $detail->location,
						'operator_id' => $detail->operator_id,
						'sedang_start_date' => $detail->sedang_start_date,
						'sedang_finish_date' => $detail->sedang_finish_date,
						'origin_group_code' => $detail->origin_group_code,
						'created_by' => $detail->created_by
					]);
					$detail->save();
				}

				if(!$sp){
					$material = Material::where('material_number', $gmc)->first();
					$detail = new AssemblyLog([
						'tag' => $details[0]->tag,
						'serial_number' => $id,
						'model' => $material->material_description,
						'location' => 'packing',
						'operator_id' => Auth::user()->username,
						'sedang_start_date' => $now,
						'sedang_finish_date' => $now,
						'origin_group_code' => '041',
						'created_by' => Auth::user()->username
					]);
					$detail->save();
				}

				$details = AssemblyDetail::where('serial_number', $id)
				->where('origin_group_code', '041')
				->where('is_send_log', '0')
				->update([
					'is_send_log' => '1'
				]);
			}

			if(!$sp) {
				$tag = AssemblyTag::where('serial_number', $id)
				->where('origin_group_code', '041')
				->update([
					'serial_number' => null,
					'model' => null,
				]);

				$inventory = AssemblyInventory::where('serial_number', $id)
				->where('origin_group_code', '041')
				->delete();

				$detail = AssemblyDetail::where('serial_number', $id)
				->where('origin_group_code', '041')
				->delete();
			}
		}

		$barcode = db::select("select flute.serial_number, material.finished, material.janean, material.upc, material.remark, material.model from
			(select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '".$gmc."') as material
			left join
			(SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '041' 
			AND location = 'packing' 
			AND serial_number = '".$id."') as flute
			on flute.model = material.model;");

		$date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '".$id."'
			and location = 'packing'
			and origin_group_code = '041')");

		return view('processes.assembly.flute.label.label_besar',array(
			'barcode' => $barcode,
			'date' => $date,
			'remark' => $remark,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function labelBesarOuterFl($id,$gmc,$remark){

		if($remark == 'P'){
			$details = AssemblyDetail::where('serial_number', $id)
			->where('origin_group_code', '041')
			->where('is_send_log', '0')
			->get();
			$now = new DateTime();

			if(count($details) > 0){
				foreach ($details as $detail) {
					$detail = new AssemblyLog([
						'tag' => $detail->tag,
						'serial_number' => $detail->serial_number,
						'model' => $detail->model,
						'location' => $detail->location,
						'operator_id' => $detail->operator_id,
						'sedang_start_date' => $detail->sedang_start_date,
						'sedang_finish_date' => $detail->sedang_finish_date,
						'origin_group_code' => $detail->origin_group_code,
						'created_by' => $detail->created_by
					]);
					$detail->save();
				}

				$material = Material::where('material_number', $gmc)->first();
				$detail = new AssemblyLog([
					'tag' => $details[0]->tag,
					'serial_number' => $id,
					'model' => $material->material_description,
					'location' => 'packing',
					'operator_id' => Auth::user()->username,
					'sedang_start_date' => $now,
					'sedang_finish_date' => $now,
					'origin_group_code' => '041',
					'created_by' => Auth::user()->username
				]);
				$detail->save();

				$details = AssemblyDetail::where('serial_number', $id)
				->where('origin_group_code', '041')
				->where('is_send_log', '0')
				->update([
					'is_send_log' => '1'
				]);
			}
		}

		$barcode = db::select("select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '".$gmc."'");

		$date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '".$id."'
			and location = 'packing'
			and origin_group_code = '041')");

		return view('processes.assembly.flute.label.label_besar_outer',array(
			'barcode' => $barcode,
			'date' => $date,
			'remark' => $remark,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchCheckCarb(Request $request){
		$sn = $request->get('sn');

		$model = AssemblyLog::where('location', 'packing')
		->where('origin_group_code', '041')
		->where('serial_number', $sn)
		->first();

		$response = array(
			'status' => true,
			'model' => $model
		);
		return Response::json($response);
	}

	public function fetchCheckReprint(Request $request){
		$serial_number = $request->get('serial_number');
		$origin_group_code = $request->get('origin_group');

		$inventory = AssemblyInventory::where('serial_number', $serial_number)
		->where('origin_group_code', $origin_group_code)
		->first();

		if($inventory){
			if($inventory->remark != 'SP'){
				$response = array(
					'status' => false,
					'message' => 'Reprint Invalid'
				);
				return Response::json($response);
			}
		}

		$log = AssemblyLog::leftJoin('materials', 'materials.material_description', '=', 'assembly_logs.model')
		->where('assembly_logs.serial_number', $serial_number)
		->where('assembly_logs.origin_group_code', $origin_group_code)
		->where('assembly_logs.location', 'packing')
		->select('assembly_logs.serial_number', 'assembly_logs.model', 'materials.material_number', 'materials.material_description')
		->first();

		if($log){
			$response = array(
				'status' => true,
				'log' => $log
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'message' => 'Serial Number Not Found'
			);
			return Response::json($response);
		}

	}

	public function fetchCheckTag(Request $request){
		$origin_group_code = $request->get('origin_group');		
		$tag = $request->get('tag');
		$tag = strtoupper(dechex($tag));

		$data = AssemblyInventory::where('tag', $tag)
		->where('location', 'qa-visual2')
		->where('origin_group_code', $origin_group_code)
		->first();

		if($data){
			$remark = "";
			if($data->remark == 'SP'){
				$remark = "AND stamp_hierarchies.remark = 'SP'";
			}

			$model = db::select("SELECT material_number, material_description, remark FROM materials
				LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished 
				WHERE stamp_hierarchies.model IN (SELECT model FROM assembly_inventories WHERE tag = '".$tag."') ".$remark);

			$response = array(
				'status' => true,
				'data' => $data,
				'model' => $model,
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false
			);
			return Response::json($response);
		}
	}

	public function fillModelResult(){
		$date = date('Y-m-d');

		$data = db::select("SELECT model, COUNT(id) AS quantity FROM assembly_logs
			WHERE location = 'packing'
			AND DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
			GROUP BY model;");

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
	}

	public function fillResult(){
		$date = date('Y-m-d');

		$data = db::select("SELECT serial_number, model, created_at FROM assembly_logs
			where location = 'packing'
			and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
			ORDER BY created_at DESC");

		return DataTables::of($data)->make(true);
	}


	public function fetchAssemblyBoard(Request $request){
		$loc = $request->get('loc');
		$boards = array();

		$now = date('Y-m-d');

		$locations = explode(",", $loc);
		$location = "";

		for($x = 0; $x < count($locations); $x++) {
			$location = $location."'".$locations[$x]."'";
			if($x != count($locations)-1){
				$location = $location.",";
			}
		}
		$addlocation = "assemblies.location in (".$location.") ";

		if ($loc == 'perakitanawal-kensa,tanpoawase-process') {
			$work_stations = DB::select("SELECT
				IF(assemblies.location = 'perakitanawal-kensa','Perakitan Ulang',assemblies.location) as location,
				assemblies.location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location ) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) AS perolehan,
				(
				SELECT
				GROUP_CONCAT(
				DISTINCT (
				SUBSTRING_INDEX( ng_name, '-', 1 ))) AS ng_name 
				FROM
				assembly_ng_logs 
				WHERE
				operator_id = assemblies.operator_id 
				AND DATE( created_at ) = '".$now."' 
				) AS ng_name,
				(
				SELECT
				GROUP_CONCAT( qty_ng ) AS qty_ng 
				FROM
				( SELECT COUNT( ng_name ) AS qty_ng, operator_id FROM assembly_ng_logs WHERE DATE( created_at ) = '".$now."' GROUP BY ng_name,operator_id ) ss 
				WHERE
				operator_id = assemblies.operator_id 
				) AS qty_ng,
				(
				SELECT
					GROUP_CONCAT(  SUBSTRING_INDEX( ng_name, ' -', 1 )  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as ng_name_detail,
					(
				SELECT
					GROUP_CONCAT(  IF(assembly_ng_logs.value_bawah is null,value_atas,CONCAT(value_atas,'-',value_bawah))  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as qty_ng_detail
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id 
				WHERE
				".$addlocation."
				and location_number in ('1','2','3','4','5','6','7')
				AND assemblies.origin_group_code = '041'
				ORDER BY remark, location_number asc");
		}else if ($loc == 'tanpoawase-process') {
			$work_stations = DB::select("SELECT
				IF(assemblies.location = 'perakitanawal-kensa','Perakitan Ulang',assemblies.location) as location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location ) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) AS perolehan,
				(
				SELECT
				GROUP_CONCAT(
				DISTINCT (
				SUBSTRING_INDEX( ng_name, '-', 1 ))) AS ng_name 
				FROM
				assembly_ng_logs 
				WHERE
				operator_id = assemblies.operator_id 
				AND DATE( created_at ) = '".$now."' 
				) AS ng_name,
				(
				SELECT
				GROUP_CONCAT( qty_ng ) AS qty_ng 
				FROM
				( SELECT COUNT( ng_name ) AS qty_ng, operator_id FROM assembly_ng_logs WHERE DATE( created_at ) = '".$now."' GROUP BY ng_name,operator_id ) ss 
				WHERE
				operator_id = assemblies.operator_id 
				) AS qty_ng,
				(
				SELECT
					GROUP_CONCAT(  SUBSTRING_INDEX( ng_name, ' -', 1 )  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as ng_name_detail,
					(
				SELECT
					GROUP_CONCAT(  IF(assembly_ng_logs.value_bawah is null,value_atas,CONCAT(value_atas,'-',value_bawah))  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as qty_ng_detail
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id 
				WHERE
				".$addlocation."
				and location_number in ('8','9','10','11','12','13','14','15','16','17')
				AND assemblies.origin_group_code = '041'
				ORDER BY remark, location_number asc");
		}else if($loc == 'tanpoawase-kensa,tanpoawase-fungsi,repair-process-1,repair-process-2'){
			$work_stations = DB::select("SELECT
				IF(assemblies.location = 'perakitanawal-kensa','Perakitan Ulang',assemblies.location) as location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location ) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) AS perolehan,
				(
				SELECT
				GROUP_CONCAT(
				DISTINCT (
				SUBSTRING_INDEX( ng_name, '-', 1 ))) AS ng_name 
				FROM
				assembly_ng_logs 
				WHERE
				operator_id = assemblies.operator_id 
				AND DATE( created_at ) = '".$now."' 
				) AS ng_name,
				(
				SELECT
				GROUP_CONCAT( qty_ng ) AS qty_ng 
				FROM
				( SELECT COUNT( ng_name ) AS qty_ng, operator_id FROM assembly_ng_logs WHERE DATE( created_at ) = '".$now."' GROUP BY ng_name,operator_id ) ss 
				WHERE
				operator_id = assemblies.operator_id 
				) AS qty_ng,
				(
				SELECT
					GROUP_CONCAT(  SUBSTRING_INDEX( ng_name, ' -', 1 )  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as ng_name_detail,
					(
				SELECT
					GROUP_CONCAT(  IF(assembly_ng_logs.value_bawah is null,value_atas,CONCAT(value_atas,'-',value_bawah))  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as qty_ng_detail
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id 
				WHERE
				(assemblies.location in ('tanpoawase-kensa','tanpoawase-fungsi') 
				AND assemblies.origin_group_code = '041')
				OR
				(assemblies.location in ('repair-process')
					and assemblies.location_number in ('1','2')
				AND assemblies.origin_group_code = '041')
				ORDER BY location desc");
		}else if($loc == 'fukiage1-process,repair-ringan'){
			$work_stations = DB::select("SELECT
				IF(assemblies.location = 'perakitanawal-kensa','Perakitan Ulang',assemblies.location) as location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location ) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) AS perolehan,
				(
				SELECT
				GROUP_CONCAT(
				DISTINCT (
				SUBSTRING_INDEX( ng_name, '-', 1 ))) AS ng_name 
				FROM
				assembly_ng_logs 
				WHERE
				operator_id = assemblies.operator_id 
				AND DATE( created_at ) = '".$now."' 
				) AS ng_name,
				(
				SELECT
				GROUP_CONCAT( qty_ng ) AS qty_ng 
				FROM
				( SELECT COUNT( ng_name ) AS qty_ng, operator_id FROM assembly_ng_logs WHERE DATE( created_at ) = '".$now."' GROUP BY ng_name,operator_id ) ss 
				WHERE
				operator_id = assemblies.operator_id 
				) AS qty_ng,
				(
				SELECT
					GROUP_CONCAT(  SUBSTRING_INDEX( ng_name, ' -', 1 )  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as ng_name_detail,
					(
				SELECT
					GROUP_CONCAT(  IF(assembly_ng_logs.value_bawah is null,value_atas,CONCAT(value_atas,'-',value_bawah))  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as qty_ng_detail
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id 
				WHERE
				(assemblies.location in ('fukiage1-process') 
				AND assemblies.origin_group_code = '041')
				OR
				(assemblies.location in ('repair-process')
					and assemblies.location_number in ('3','4','5')
				AND assemblies.origin_group_code = '041')
				ORDER BY location,location_number asc");
		}
		else{
			$work_stations = DB::select("SELECT
				assemblies.location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location ) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '".$now."' ) AS perolehan,
				(
				SELECT
				GROUP_CONCAT(
				DISTINCT (
				SUBSTRING_INDEX( ng_name, '-', 1 ))) AS ng_name 
				FROM
				assembly_ng_logs 
				WHERE
				operator_id = assemblies.operator_id 
				AND DATE( created_at ) = '".$now."' 
				) AS ng_name,
				(
				SELECT
				GROUP_CONCAT( qty_ng ) AS qty_ng 
				FROM
				( SELECT COUNT( ng_name ) AS qty_ng, operator_id FROM assembly_ng_logs WHERE DATE( created_at ) = '".$now."' GROUP BY ng_name,operator_id ) ss 
				WHERE
				operator_id = assemblies.operator_id 
				) AS qty_ng,
				(
				SELECT
					GROUP_CONCAT(  SUBSTRING_INDEX( ng_name, ' -', 1 )  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as ng_name_detail,
					(
				SELECT
					GROUP_CONCAT(  IF(assembly_ng_logs.value_bawah is null,value_atas,CONCAT(value_atas,'-',value_bawah))  ) AS ng_name 
				FROM
					assembly_ng_logs 
				WHERE
					operator_id = assemblies.operator_id 
					AND DATE( created_at ) = '".$now."' 
					) as qty_ng_detail
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id 
				WHERE
				".$addlocation."
				AND assemblies.origin_group_code = '041'
				ORDER BY location,location_number asc");
		}

		foreach ($work_stations as $ws) {

			$dt_now = new DateTime();

			$dt_sedang = new DateTime($ws->sedang_time);
			$sedang_time = $dt_sedang->diff($dt_now);

			$board_sedang = '';
			if($ws->sedang_serial_number != null){
				$board_sedang = '('.$ws->sedang_serial_number.')'.'<br>'.$ws->sedang_model;
			}else{
				$board_sedang = '<br>';
			}

			array_push($boards, [
				'ws' => strtoupper($ws->location.' ('.$ws->location_number.')'),
				'employee_id' => $ws->operator_id,
				'employee_name' => strtoupper($ws->name),
				'sedang' => $board_sedang,
				'sedang_time' => str_pad($sedang_time->format('%H'), 2, '0', STR_PAD_LEFT).":".str_pad($sedang_time->format('%i'), 2, '0', STR_PAD_LEFT).":".str_pad($sedang_time->format('%s'), 2, '0', STR_PAD_LEFT),
				'std_time' => $ws->std_time,
				'perolehan' => $ws->perolehan,
				'ng_name' => $ws->ng_name,
				'qty_ng' => $ws->qty_ng,
				'ng_name_detail' => $ws->ng_name_detail,
				'qty_ng_detail' => $ws->qty_ng_detail
			]);
		}

		$ng = DB::SELECT("SELECT
			ng_name,
			count(ng_name) as qty_ng
			FROM
			assembly_ng_logs 
			LEFT JOIN assemblies on assembly_ng_logs.operator_id = assemblies.operator_id
			WHERE
			".$addlocation."
			AND DATE( assembly_ng_logs.created_at ) = '".$now."' GROUP BY ng_name");

		$response = array(
			'status' => true,
			'loc' => $loc,
			'boards' => $boards,
			'ng' => $ng
		);
		return Response::json($response);
	}

	public function kensa($location)
	{
		$loc_code = explode('-', $location);
		$process = $loc_code[0];
		$loc_spec = $loc_code[1];

		if($location == 'kariawase-fungsi'){
			$title = 'Kariawase Kensa Fungsi Flute';
			$title_jp= 'FL仮合わせ機能検査';
		}
		if($location == 'kariawase-visual'){
			$title = 'Kariawase Kensa Visual Flute';
			$title_jp= 'FL仮合わせ外観検査';
		}
		if($location == 'perakitanawal-kensa'){
			$title = 'Perakitan Ulang Kensa Flute';
			$title_jp= 'FL再組立検査';
		}
		if($location == 'tanpoawase-kensa'){
			$title = 'Tanpo Awase Kensa Flute';
			$title_jp= 'FLタンポ合わせ検査';
		}
		if($location == 'tanpoawase-fungsi'){
			$title = 'Tanpo Awase Kensa Fungsi Flute';
			$title_jp= 'FLタンポ合わせ検査（機能検査）';
		}
		if($location == 'kango-fungsi'){
			$title = 'Kango Kensa Fungsi (Gata,Seri) Flute';
			$title_jp= 'FL嵌合機能検査（ガタ、セリ';
		}
		if($location == 'kango-kensa'){
			$title = 'Kango Kensa Visual Flute';
			$title_jp= 'FL嵌合外観検査';
		}
		if($location == 'renraku-fungsi'){
			$title = 'Renraku Kensa Fungsi Flute';
			$title_jp= 'FL連絡機能検査';
		}
		if($location == 'qa-fungsi'){
			$title = 'QA Kensa Fungsi Flute';
			$title_jp= 'FL機能検査（QA';
		}
		if($location == 'fukiage1-visual'){
			$title = 'Fukiage 1 Kensa Visual Flute';
			$title_jp= 'FL拭き上げ外観検査';
		}
		if($location == 'qa-visual1'){
			$title = 'QA 1 Kensa Visual Flute';
			$title_jp= 'FL外観検査（QA1';
		}
		if($location == 'qa-visual2'){
			$title = 'QA 2 Kensa Visual Flute';
			$title_jp= 'FL外観検査（QA2）';
		}
		if($location == 'qa-kensasp'){
			$title = 'QA Kensa SP';
			$title_jp= '特注品QA検査';
		}
		if($location == 'seasoning-process'){
			$title = 'Seasoning Process';
			$title_jp= 'シーズニング';
		}
		if($location == 'repair-process'){
			$title = 'Repair Process';
			$title_jp= '修正';
		}

		if ($location == 'seasoning-process') {
			return view('processes.assembly.flute.seasoning', array(
				'loc' => $location,
				'loc2' => $location,
				'process' => $process,
				'loc_spec' => $loc_spec,
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location',$location);
		}else{
			$ng_lists = DB::select("SELECT DISTINCT(ng_name) FROM assembly_ng_lists where origin_group_code = '041' and location = '".$loc_spec."' and process = '".$process."' and deleted_at is null");

			return view('processes.assembly.flute.kensa', array(
				'ng_lists' => $ng_lists,
				'loc' => $location,
				'loc2' => $location,
				'process' => $process,
				'loc_spec' => $loc_spec,
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location',$location);
		}
	}

	public function scanAssemblyOperator(Request $request){

		$employee = db::table('assembly_operators')->join('employee_syncs','assembly_operators.employee_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('employee_id')))->first();

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			'message' => 'Tag karyawan ditemukan',
			'employee' => $employee,
		);
		return Response::json($response);
	}

	public function scanAssemblyOperatorKensa(Request $request){

		$employee = db::table('assembly_operators')->join('employee_syncs','assembly_operators.employee_id','=','employee_syncs.employee_id')->where('tag', '=', strtoupper(dechex($request->get('employee_id'))))->first();

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak ditemukan',
			);
			return Response::json($response);			
		}
		else{
			if (count($employee->location) > 0) {
				$location = $employee->location;
				$loc = explode("-", $location);
				$number = $loc[2];
				$locfix = $loc[0]."-".$loc[1];
				$assemblies = Assembly::where('location','=',$locfix)->where('location_number','=',$number)->where('remark','=','OTHER')->first();
				if (count($assemblies) > 0) {
					$assemblies->online_time = date('Y-m-d H:i:s');
					$assemblies->operator_id = $employee->employee_id;
					$assemblies->save();
					$response = array(
						'status' => true,
						'message' => 'Tag karyawan ditemukan',
						'employee' => $employee,
						// 'location' => $location
					);
					return Response::json($response);
				}else{
					$response = array(
						'status' => false,
						'message' => 'Tag karyawan tidak ditemukan',
					);
					return Response::json($response);	
				}
			}else{
				$response = array(
					'status' => true,
					'message' => 'Tag karyawan ditemukan',
					'employee' => $employee,
					// 'location' => $location
				);
				return Response::json($response);	
			}
		}
	}

	public function scanAssemblyKensa(Request $request)
	{

		$details = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->first();

		$details2 = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->orderBy('assembly_details.id', 'desc')->get();

		$employee = db::table('assembly_operators')->join('employee_syncs','assembly_operators.employee_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('employee_id')))->first();

		if($details == null){
			$response = array(
				'status' => false,
				'message' => 'Serial Number tidak ditemukan',
			);
			return Response::json($response);			
		}else{
			if (count($employee->location) > 0) {
				$location = $employee->location;
				$loc = explode("-", $location);
				$number = $loc[2];
				$locfix = $loc[0]."-".$loc[1];
				$assemblies = Assembly::where('location','=',$locfix)->where('location_number','=',$number)->where('remark','=','OTHER')->first();
				$assemblies->sedang_tag = strtoupper(dechex($request->get('tag')));
				$assemblies->sedang_serial_number = $details->serial_number;
				$assemblies->sedang_model = $details->model;
				$assemblies->sedang_time = date('Y-m-d H:i:s');
				$assemblies->save();
			}
			
			$response = array(
				'status' => true,
				'message' => 'Serial Number ditemukan',
				'details' => $details,
				'details2' => $details2,
				'started_at' => date('Y-m-d H:i:s'),
			);
			return Response::json($response);
		}
	}

	public function deleteAssemblyKensa(Request $request)
	{
		$employee = db::table('assembly_operators')->join('employee_syncs','assembly_operators.employee_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('employee_id')))->first();

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'Gagal Hapus Assemblies',
			);
			return Response::json($response);			
		}else{
			$location = $employee->location;
			$loc = explode("-", $location);
			$number = $loc[2];
			$locfix = $loc[0]."-".$loc[1];
			$assemblies = Assembly::where('location','=',$locfix)->where('location_number','=',$number)->where('remark','=','OTHER')->first();
			$assemblies->sedang_tag = null;
			$assemblies->sedang_serial_number = null;
			$assemblies->sedang_model = null;
			$assemblies->sedang_time = null;
			$assemblies->save();
			
			$response = array(
				'status' => true,
				'message' => 'Berhasil Hapus Assemblies'
			);
			return Response::json($response);
		}
	}

	public function showNgDetail(Request $request){

		$ng_detail = db::select("select * from assembly_ng_lists where ng_name = '".$request->get('ng_name')."' and location = '".$request->get('location')."' and process = '".$request->get('process')."' and origin_group_code = '041'");

		if ($request->get('ng_name') == 'Renraku') {
			$onko = DB::select("SELECT DISTINCT(assembly_onkos.key),nomor FROM assembly_onkos where origin_group_code = '041' and location = 'renraku'");
			$onko_detail = DB::select("SELECT assembly_onkos.key,nomor,keynomor FROM assembly_onkos where origin_group_code = '041' and location = 'renraku'");
		}else{
			$onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = 'all'");
			$onko_detail = DB::select("SELECT assembly_onkos.key,nomor,keynomor FROM assembly_onkos where origin_group_code = '041' and location = 'all'");
		}

		if($ng_detail == null){
			$response = array(
				'status' => false,
				'message' => 'NG Detail Tidak Ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			'ng_detail' => $ng_detail,
			'onko' => $onko,
			'onko_detail' => $onko_detail,
		);
		return Response::json($response);
	}

	public function fetchNgTemp(Request $request)
	{
		$model = $request->get('model');
		$serial_number = $request->get('serial_number');
		$employee_id = $request->get('employee_id');
		$tag = dechex($request->get('tag'));

		$ng_temp = db::select("SELECT
			*,
			IF ( assembly_ng_temps.operator_id LIKE '%PI%',( SELECT NAME FROM employee_syncs WHERE employee_syncs.employee_id = assembly_ng_temps.operator_id ),
				assembly_ng_temps.operator_id 
			) AS name 
			FROM
			`assembly_ng_temps` 
			WHERE
			model = '".$model."'
			AND serial_number = '".$serial_number."'
			AND employee_id = '".$employee_id."'
			AND tag = '".$tag."'
			AND deleted_at is null");

		if($ng_temp == null){
			$response = array(
				'status' => false,
				'message' => 'NG Detail Tidak Ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			// 'message' => 'Tag karyawan ditemukan',
			'ng_temp' => $ng_temp,
		);
		return Response::json($response);
	}

	public function fetchNgLogs(Request $request)
	{
		$model = $request->get('model');
		$serial_number = $request->get('serial_number');
		$employee_id = $request->get('employee_id');
		$tag = dechex($request->get('tag'));

		$ng_logs = db::select("SELECT
			*,
			IF ( assembly_ng_logs.operator_id LIKE '%PI%',( SELECT NAME FROM employee_syncs WHERE employee_syncs.employee_id = assembly_ng_logs.operator_id ),
				assembly_ng_logs.operator_id 
			) AS name 
			FROM
			`assembly_ng_logs` 
			WHERE
			model = '".$model."'
			AND serial_number = '".$serial_number."'
			AND tag = '".$tag."'
			AND deleted_at is null
			order by id desc");

		if($ng_logs == null){
			$response = array(
				'status' => false,
				'message' => 'NG Detail Tidak Ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			// 'message' => 'Tag karyawan ditemukan',
			'ng_logs' => $ng_logs,
		);
		return Response::json($response);
	}

	public function inputRepairProcess(Request $request)
	{
		try {
			$repair = AssemblyNgLog::where('id',$request->get('id'))->first();
			$repair->repair_status = 'Repaired';
			$repair->repaired_by = $request->get('employee_id');
			$repair->repaired_at = date('Y-m-d H:i:s');
			$repair->save();

			$response = array(
				'status' => true,
				'message' => 'NG Repaired',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'details' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function getProcessBefore(Request $request)
	{
		$model = $request->get('model');
		$serial_number = $request->get('serial_number');
		$tag = dechex($request->get('tag'));
		$location = $request->get('process_before');

		$details = db::select("SELECT
			* 
			FROM
			`assembly_details` 
			join employee_syncs on employee_syncs.employee_id = assembly_details.operator_id
			WHERE
			model = '".$model."' 
			AND tag = '".$tag."' 
			AND serial_number = '".$serial_number."' 
			AND location = '".$location."'");

		if($details == null){
			$response = array(
				'status' => false,
				'message' => 'Data Tidak Ditemukan',
				'details' => $details,
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			'details' => $details,
		);
		return Response::json($response);
	}

	public function fetchOnko(Request $request)
	{
		$location = $request->get('process');

		$onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = '".$location."' ORDER BY `key`");

		if($onko == null){
			$response = array(
				'status' => false,
				'message' => 'NG Onko Tidak Ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			// 'message' => 'Tag karyawan ditemukan',
			'onko' => $onko,
		);
		return Response::json($response);
	}

	public function inputNgTemp(Request $request)
	{
		if($request->get('ng')){
			if ($request->get('ng') == 'Tanpo Awase') {
				$value_atas = $request->get('value_atas');
				$value_bawah = $request->get('value_bawah');
				$ongko = $request->get('onko');
				$lokasi = $request->get('lokasi');
				$operator = $request->get('operator_id');

				for ($i=0; $i < count($ongko); $i++) { 
					$assembly_ng_temp = new AssemblyNgTemp([
						'employee_id' => $request->get('employee_id'),
						'tag' => strtoupper(dechex($request->get('tag'))),
						'serial_number' => $request->get('serial_number'),
						'model' => $request->get('model'),
						'location' => $request->get('location'),
						'ng_name' => $request->get('ng'),
						'ongko' => $ongko[$i],
						'value_atas' => $value_atas[$i],
						'value_bawah' => $value_bawah[$i],
						'value_lokasi' => $lokasi[$i],
						'operator_id' => $operator[$i],
						'started_at' => $request->get('started_at'),
						'origin_group_code' => $request->get('origin_group_code'),
						'created_by' => Auth::id()
					]);

					try{
						$assembly_ng_temp->save();
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}
			}else{
				$assembly_ng_temp = new AssemblyNgTemp([
					'employee_id' => $request->get('employee_id'),
					'tag' => strtoupper(dechex($request->get('tag'))),
					'serial_number' => $request->get('serial_number'),
					'model' => $request->get('model'),
					'location' => $request->get('location'),
					'ng_name' => $request->get('ng'),
					'value_atas' => 1,
					'ongko' => $request->get('onko'),
					'operator_id' => $request->get('operator_id'),
					'started_at' => $request->get('started_at'),
					'origin_group_code' => $request->get('origin_group_code'),
					'created_by' => Auth::id()
				]);

				try{
					$assembly_ng_temp->save();
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
				'message' => 'Sukses Input NG',
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'message' => 'Gagal Input NG',
			);
			return Response::json($response);
		}
	}

	public function deleteNgTemp(Request $request)
	{
		$model = $request->get('model');
		$serial_number = $request->get('serial_number');
		$employee_id = $request->get('employee_id');
		$tag = dechex($request->get('tag'));

		$ng_temp = AssemblyNgTemp::where('model',$model)->where('serial_number',$serial_number)->where('employee_id',$employee_id)->where('tag',$tag)->delete();

		$response = array(
			'status' => true,
			'message' => 'Temp Deleted',
		);
		return Response::json($response);
	}

	public function inputAssemblyKensa(Request $request)
	{
		if($request->get('tag')){
			$model = $request->get('model');
			$serial_number = $request->get('serial_number');
			$employee_id = $request->get('employee_id');
			$tag = strtoupper(dechex($request->get('tag')));

			$started_at = "";
			$finished_at = date('Y-m-d H:i:s');

			$ng_temp = AssemblyNgTemp::where('serial_number',$serial_number)->where('employee_id',$employee_id)->where('tag',$tag)->where('origin_group_code','041')->get();
			$jumlah_ng = 0;
			foreach ($ng_temp as $ng) {
				$assembly_ng_log = new AssemblyNgLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => strtoupper(dechex($request->get('tag'))),
					'serial_number' => $request->get('serial_number'),
					'model' => $request->get('model'),
					'location' => $request->get('location'),
					'ongko' => $ng->ongko,
					'ng_name' => $ng->ng_name,
					'value_atas' => $ng->value_atas,
					'value_bawah' => $ng->value_bawah,
					'value_lokasi' => $ng->value_lokasi,
					'operator_id' => $ng->operator_id,
					'sedang_start_date' => $ng->started_at,
					'sedang_finish_date' => $finished_at,
					'origin_group_code' => $request->get('origin_group_code'),
					'created_by' => Auth::id()
				]);

				$started_at = $ng->started_at;

				try{
					$assembly_ng_log->save();
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
				$jumlah_ng++;
			}

			$assembly_invent = AssemblyInventory::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->first();

			$remark = $assembly_invent->remark;

			if ($remark == 'SP') {
				if ($jumlah_ng == 0) {
					$assembly_details = AssemblyDetail::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->delete();

					$detail = new AssemblyLog([
						'tag' => strtoupper(dechex($request->get('tag'))),
						'serial_number' => $request->get('serial_number'),
						'model' => $request->get('model'),
						'location' => $request->get('location'),
						'operator_id' => $request->get('employee_id'),
						'sedang_start_date' => $request->get('started_at'),
						'sedang_finish_date' => $finished_at,
						'origin_group_code' => $request->get('origin_group_code'),
						'created_by' => $request->get('employee_id')
					]);
					try{
						$detail->save();
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}else{
					$assembly_details = new AssemblyDetail([
						'tag' => strtoupper(dechex($request->get('tag'))),
						'serial_number' => $request->get('serial_number'),
						'model' => $request->get('model'),
						'location' => $request->get('location'),
						'operator_id' => $request->get('employee_id'),
						'sedang_start_date' => $request->get('started_at'),
						'sedang_finish_date' => $finished_at,
						'origin_group_code' => $request->get('origin_group_code'),
						'created_by' => $request->get('employee_id')
					]);

					try{
						$assembly_details->save();
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}

			}else{
				$assembly_details = new AssemblyDetail([
					'tag' => strtoupper(dechex($request->get('tag'))),
					'serial_number' => $request->get('serial_number'),
					'model' => $request->get('model'),
					'location' => $request->get('location'),
					'operator_id' => $request->get('employee_id'),
					'sedang_start_date' => $request->get('started_at'),
					'sedang_finish_date' => $finished_at,
					'origin_group_code' => $request->get('origin_group_code'),
					'created_by' => $request->get('employee_id')
				]);

				try{
					$assembly_details->save();
				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			try{

				$assembly_inventories = AssemblyInventory::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->first();
				$assembly_inventories->location = $request->get('location');
				$assembly_inventories->created_by = $request->get('employee_id');

				$location_next = $assembly_inventories->location_next;

				if ($remark == 'SP') {
					if ($jumlah_ng == 0) {
						$assembly_inventories->delete();
						$asstag = AssemblyTag::where('tag',strtoupper(dechex($request->get('tag'))))->first();
						$asstag->serial_number = null;
						$asstag->model = null;
						$asstag->save();
					}
				}else{
					$assembly_flow = AssemblyFlow::where('process',$location_next)->where('origin_group_code','041')->first();
					$id_flow_now = $assembly_flow->id;
					$id_flow_next = $id_flow_now + 1;
					$assembly_flow_next = AssemblyFlow::where('id',$id_flow_next)->where('origin_group_code','041')->first();
					$next_process = $assembly_flow_next->process;
					if (count($next_process) > 0 && $next_process != "") {
						$assembly_inventories->location_next = $next_process;
					}
					$assembly_inventories->save();
				}

			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}

			$response = array(
				'status' => true,
				'message' => 'Sukses Input NG',
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'message' => 'Gagal Input NG',
			);
			return Response::json($response);
		}
	}

	public function inputAssemblySeasoning(Request $request)
	{
		try {
			$inventories = AssemblyInventory::where('tag',strtoupper(dechex($request->get('tag'))))->where('origin_group_code','041')->first();
			$inventories->location = $request->get('location');
			$inventories->created_by = $request->get('employee_id');

			$flow = AssemblyFlow::where('process',$request->get('location'))->where('origin_group_code','041')->first();
			$next = $flow->flow+1;
			$flownew = AssemblyFlow::where('flow',$next)->where('origin_group_code','041')->first();

			if (count($flownew) > 0) {
				$inventories->location_next = $flownew->process;
			}

			$log = new AssemblyDetail([
				'tag' => $inventories->tag,
				'serial_number' => $inventories->serial_number,
				'model' => $inventories->model,
				'location' => $request->get('location'),
				'operator_id' => $request->get('employee_id'),
				'sedang_start_date' => date('Y-m-d H:i:s'),
				'sedang_finish_date' => date('Y-m-d H:i:s'),
				'origin_group_code' => '041',
				'created_by' => $request->get('employee_id'),
				'is_send_log' => 0
			]);

			$inventories->save();
			$log->save();


			$response = array(
				'status' => true,
				'message' => 'Sukses Input Seasoning',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => 'Gagal Input NG',
			);
			return Response::json($response);
		}
	}

	public function fetchAssembly(Request $request)
	{
		try {
			$assembly = DB::SELECT("SELECT DISTINCT
				assembly_details.serial_number,
				assembly_details.model,
				assembly_details.sedang_start_date AS start_at,
				employee_syncs.employee_id,
				employee_syncs.name 
			FROM
				assembly_details
				JOIN employee_syncs ON employee_syncs.employee_id = assembly_details.operator_id 
			WHERE
				location = '".$request->get('location')."'
				and DATE(assembly_details.created_at) = DATE(NOW())");

			$response = array(
				'status' => true,
				'assembly' => $assembly
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => 'Failed',
			);
			return Response::json($response);
		}
	}

	public function indexRequestDisplay($origin_group_code)
	{
		return view('processes.assembly.flute.display.assembly_request', array( 
			'title' => 'Assembly Request Material',
			'title_jp' => '組立依頼材料',
			'origin_group_code' => $origin_group_code))
		->with('page', 'Assembly Request Material');
	}

	public function fetchRequest(Request $request)
	{
		$origin_group_code = $request->get('origin_group_code');
		$stamps = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%stamp%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$perakitans = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%perakitan%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$kariawases = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%kariawase%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$tanpoires = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%tanpoire%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$tanpoawases = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%tanpoawase%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$seasonings = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%seasoning%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$kangos = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%kango%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$renrakus = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%renraku%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$fukiage1s = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%fukiage1%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$fukiage2s = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%fukiage2%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');
		$qas = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%qa%" and origin_group_code = "'.$origin_group_code.'" GROUP BY model');

		$models = DB::SELECT('SELECT DISTINCT(model) FROM `materials` where origin_group_code = "041" and category = "FG"');

		$log_request = array();

		foreach ($models as $key) {

			$qty_stamp = 0;
			foreach ($stamps as $stamp) {
				if ($key->model == $stamp->model) {
					$qty_stamp = $stamp->qty;
				}
			}

			$qty_perakitan = 0;
			foreach ($perakitans as $perakitan) {
				if ($key->model == $perakitan->model) {
					$qty_perakitan = $perakitan->qty;
				}
			}

			$qty_kariawase = 0;
			foreach ($kariawases as $kariawase) {
				if ($key->model == $kariawase->model) {
					$qty_kariawase = $kariawase->qty;
				}
			}

			$qty_tanpoire = 0;
			foreach ($tanpoires as $tanpoire) {
				if ($key->model == $tanpoire->model) {
					$qty_tanpoire = $tanpoire->qty;
				}
			}

			$qty_tanpoawase = 0;
			foreach ($tanpoawases as $tanpoawase) {
				if ($key->model == $tanpoawase->model) {
					$qty_tanpoawase = $tanpoawase->qty;
				}
			}

			$qty_seasoning = 0;
			foreach ($seasonings as $seasoning) {
				if ($key->model == $seasoning->model) {
					$qty_seasoning = $seasoning->qty;
				}
			}

			$qty_kango = 0;
			foreach ($kangos as $kango) {
				if ($key->model == $kango->model) {
					$qty_kango = $kango->qty;
				}
			}

			$qty_renraku = 0;
			foreach ($renrakus as $renraku) {
				if ($key->model == $renraku->model) {
					$qty_renraku = $renraku->qty;
				}
			}

			$qty_fukiage1 = 0;
			foreach ($fukiage1s as $fukiage1) {
				if ($key->model == $fukiage1->model) {
					$qty_fukiage1 = $fukiage1->qty;
				}
			}

			$qty_fukiage2 = 0;
			foreach ($fukiage2s as $fukiage2) {
				if ($key->model == $fukiage2->model) {
					$qty_fukiage2 = $fukiage2->qty;
				}
			}

			$qty_qa = 0;
			foreach ($qas as $qa) {
				if ($key->model == $qa->model) {
					$qty_qa = $qa->qty;
				}
			}

			array_push($log_request, [
				"model" => $key->model,
				"stamp" => $qty_stamp,
				"perakitan" => $qty_perakitan,
				"kariawase" => $qty_kariawase,
				"tanpoire" => $qty_tanpoire,
				"tanpoawase" => $qty_tanpoawase,
				"seasoning" => $qty_seasoning,
				"kango" => $qty_kango,
				"renraku" => $qty_renraku,
				"fukiage1" => $qty_fukiage1,
				"fukiage2" => $qty_fukiage2,
				"qa" => $qty_qa,
			]);
		}

		$response = array(
			'status' => true,
			'datas' => $log_request,
		);
		return Response::json($response);
	}

	public function indexNgRate(){
		$locations = $this->location_fl;

		return view('processes.assembly.flute.display.ng_rate', array(
			'title' => 'NG Rate',
			'title_jp' => '不良率',
			'locations' => $locations
		))->with('page', 'Assembly Flute Process');
	}

	public function fetchNgRate(Request $request){
		$now = date('Y-m-d');
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
			$addlocation = "and location in (".$location.") ";
		}

		if(strlen($request->get('tanggal'))>0){
			$now = date('Y-m-d', strtotime($request->get('tanggal')));
		}

		$ng = db::select("SELECT DISTINCT
			(
			SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS ng_name,(
			SELECT
			COUNT( ng_name ) 
			FROM
			assembly_ng_logs 
			WHERE
			DATE(created_at) = '".$now."' ".$addlocation."
			AND
			SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS jumlah,(
			SELECT
			COUNT( ng_name ) 
			FROM
			assembly_ng_logs 
			WHERE
			DATE(created_at) = '".$now."' ".$addlocation."
			AND
			SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) / ( SELECT count( DISTINCT ( model )) AS model FROM `assembly_details` WHERE DATE( created_at ) = '".$now."' ".$addlocation." ) * 100 AS rate 
			FROM
			assembly_ng_logs AS a 
			WHERE
			DATE( a.created_at ) = '".$now."' ".$addlocation." ORDER BY jumlah DESC");

		$ngkey = db::select("
			SELECT DISTINCT
			( model ),
			count( ng_name ) AS ng,
			0 AS rate 
			FROM
			assembly_ng_logs 
			WHERE
			DATE( created_at ) = '".$now."' ".$addlocation."
			GROUP BY
			model 
			ORDER BY
			ng DESC"
		);


		$dateTitle = date("d M Y", strtotime($now));


		$datastat = db::select("SELECT
			(
			SELECT
			SUM( check_total.total_check ) AS total_check 
			FROM
			((
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_details 
			WHERE
			DATE( assembly_details.created_at )= '".$now."' ".$addlocation." 
			)) check_total 
			) AS total_check,
			(
			SELECT
			SUM( check_total.total_check ) AS total_check 
			FROM
			((
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_details 
			WHERE
			DATE( assembly_details.created_at )= '".$now."' ".$addlocation." 
			)) check_total 
			) - count( DISTINCT ( serial_number ) ) AS total_ok,
			(count( DISTINCT ( serial_number ) ) / (
			SELECT
			SUM( check_total.total_check ) AS total_check 
			FROM
			((
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( serial_number )) AS total_check 
			FROM
			assembly_details 
			WHERE
			DATE( assembly_details.created_at )= '".$now."' ".$addlocation." 
			)) check_total 
			)) * 100 AS ng_rate,
			count( DISTINCT ( serial_number ) ) AS total_ng 
			FROM
			assembly_ng_logs 
			WHERE
			DATE( assembly_ng_logs.created_at )= '".$now."' ".$addlocation." 
			AND deleted_at IS NULL");

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
			$location = "";
		}
		$location = strtoupper($location);

		$response = array(
			'status' => true,
			// 'checks' => $checks,
			// 'ngs' => $ngs,
			'ng' => $ng,
			'ngkey' => $ngkey,
			'dateTitle' => $dateTitle,
			'data' => $datastat,
			'title' => $location
		);
		return Response::json($response);
	}

	public function indexOpRate(){
		$title = 'NG Rate by Operator';
		$title_jp = '作業者不良率';

		$locations = $this->location_fl;

		return view('processes.assembly.flute.display.op_rate', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Assembly Flute Process');
	}

	public function fetchOpRate(Request $request){
		$now = date('Y-m-d');

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
			$addlocation = "and location in (".$location.") ";
		}

		if(strlen($request->get('tanggal'))>0){
			$now = date('Y-m-d', strtotime($request->get('tanggal')));
		}

		$ng_target = db::table("middle_targets")
		->where('location', '=', 'assy_fl')
		->where('target_name', '=', 'NG Rate')
		->select('target')
		->first();

		$ng_rate = db::select("SELECT
			ao.employee_id AS operator_id,
			SUBSTRING_INDEX( e.NAME, ' ', 2 ) AS `name`,
			COALESCE ( ng.`check`, 0 ) AS `check`,
			COALESCE ( ng.ng, 0 ) AS ng,
			0 AS rate 
			FROM
			assembly_operators ao
			LEFT JOIN employee_syncs e ON ao.employee_id = e.employee_id
			LEFT JOIN (
			SELECT
			count( id ) AS ng,
			count( id ) AS `check`,
			operator_id 
			FROM
			assembly_ng_logs 
			WHERE
			DATE( created_at ) = '".$now."' '".$addlocation."'
			GROUP BY
			operator_id 
		) ng ON ao.employee_id = ng.operator_id");

		$target = db::select("select eg.`group`, eg.employee_id, e.name, ng.material_number, concat(m.model, ' ', m.`key`) as `key`, ng.ng_name, ng.quantity, ng.created_at from employee_groups eg left join 
			(select * from welding_ng_logs where deleted_at is null ".$addlocation." and remark in 
			(select remark.remark from
			(select operator_id, max(remark) as remark from welding_ng_logs where DATE(welding_time) ='".$now."' ".$addlocation." group by operator_id) 
			remark)
			) ng 
			on eg.employee_id = ng.operator_id
			left join materials m on m.material_number = ng.material_number
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering'
			order by eg.`group`, e.`name` asc");

		$operator = db::select("select g.group, g.employee_id, e.name from employee_groups g
			left join employee_syncs e on e.employee_id = g.employee_id
			where g.location = 'soldering'
			order by g.`group`, e.name asc");

		// $dateTitle = date("d M Y", strtotime($now));

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
			$location = "";
		}
		$location = strtoupper($location);

		$response = array(
			'status' => true,
			'ng_rate' => $ng_rate,
			'target' => $target,
			'operator' => $operator,
			'ng_target' => $ng_target->target,
			'dateTitle' => $now,
			'title' => $location
		);
		return Response::json($response);
	}


	public function indexProductionResult(Request $request){
		$title = 'Production Result';
		$title_jp = '生産結果';
		return view('processes.assembly.flute.display.production_result')
		->with('page', 'Process Assy FL')
		->with('head', 'Assembly Process')
		->with('location_all', $this->location_fl_display)
		->with('title', $title)
		->with('title_jp', $title_jp);
	}

	public function fetchProductionResult(Request $request){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$location = $request->get('location');
		if ($request->get('location') == 'stamp') {
			$title_location = 'Stamp';
			$next_location = 'perakitan';
		}
		if ($request->get('location') == 'perakitan') {
			$title_location = 'Perakitan';
			$next_location = 'kariawase';
		}
		if ($request->get('location') == 'kariawase') {
			$title_location = 'Kariawase';
			$next_location = 'tanpoire';
		}
		if ($request->get('location') == 'tanpoire') {
			$title_location = 'Tanpoire';
			$next_location = 'perakitanawal';
		}
		if ($request->get('location') == 'perakitanawal') {
			$title_location = 'Perakitan Awal';
			$next_location = 'tanpoawase';
		}
		if ($request->get('location') == 'tanpoawase') {
			$title_location = 'Tanpo Awase';
			$next_location = 'seasoning';
		}
		if ($request->get('location') == 'seasoning') {
			$title_location = 'Seasoning';
			$next_location = 'kango';
		}
		if ($request->get('location') == 'kango') {
			$title_location = 'Kango';
			$next_location = 'renraku';
		}
		if ($request->get('location') == 'renraku') {
			$title_location = 'Renraku (Chousei)';
			$next_location = 'qa-fungsi';
		}
		if ($request->get('location') == 'qa-fungsi') {
			$title_location = 'QA Cek Fungsi';
			$next_location = 'fukiage1';
		}
		if ($request->get('location') == 'fukiage1') {
			$title_location = 'Fukiage Awal';
			$next_location = 'qa-visual1';
		}
		if ($request->get('location') == 'qa-visual1') {
			$title_location = 'QA Cek Visual 1';
			$next_location = 'fukiage2';
		}
		if ($request->get('location') == 'fukiage2') {
			$title_location = 'Fukiage Akhir';
			$next_location = 'qa-visual2';
		}
		if ($request->get('location') == 'qa-visual2') {
			$title_location = 'QA Cek Visual 2';
			$next_location = 'packing';
		}
		if ($request->get('location') == 'packing') {
			$title_location = 'Packing';
			$next_location = 'warehouse';
		}

		if ($location != "") {
			$query = "SELECT
			model,
			sum( plan ) AS plan,
			sum( out_item ) AS out_item,
			sum( in_item ) AS in_item 
			FROM
			(
			SELECT
			model,
			quantity AS plan,
			0 AS out_item,
			0 AS in_item 
			FROM
			stamp_schedules 
			WHERE
			due_date = '".$now."' UNION ALL
			SELECT
			model,
			0 AS plan,
			COUNT(
			DISTINCT ( serial_number )) AS out_item,
			0 AS in_item 
			FROM
			assembly_details 
			WHERE
			location like '%".$next_location."%' 
			AND date( created_at ) = '".$now."' 
			GROUP BY
			model UNION ALL
			SELECT
			model,
			0 AS plan,
			0 AS out_item,
			COUNT(
			DISTINCT ( serial_number )) AS in_item 
			FROM
			assembly_details 
			WHERE
			location like '%".$location."%' 
			AND date( created_at ) = '".$now."' 
			GROUP BY
			model 
			) AS plan 
			GROUP BY
			model 
			HAVING
			model LIKE 'YFL%'";

			$chartData = DB::select($query);

			if(date('D')=='Fri'){
				if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
					$deduction = 600;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 13:10:00')){
					$deduction = 4800;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 15:00:00')){
					$deduction = 5400;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:30:00')){
					$deduction = 5800;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
					$deduction = 7500;
				}
				else{
					$deduction = 0;
				}
			}
			else{
				if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
					$deduction = 600;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 12:40:00')){
					$deduction = 3000;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 14:30:00')){
					$deduction = 3600;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:00:00')){
					$deduction = 4200;
				}
				elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
					$deduction = 5700;
				}
				else{
					$deduction = 0;
				}
			}

			$query2 = "SELECT
			total.*
			FROM
			(
			SELECT
			max( assembly_details.created_at ) AS last_input,
			count( assembly_details.serial_number ) AS quantity,
			count(
			DISTINCT ( assembly_details.operator_id )) AS manpower,
			ROUND(
			standard_time * count( assembly_details.serial_number )) AS std_time,
			SUM(
			TIMESTAMPDIFF( SECOND, assembly_details.sedang_start_date, assembly_details.sedang_finish_date )) AS act_time 
			FROM
			assembly_details
			LEFT JOIN assembly_std_times ON assembly_std_times.model = assembly_details.model 
			AND assembly_std_times.location LIKE '%".$location."%' 
			WHERE
			assembly_details.location LIKE '%".$location."%' 
			AND DATE( assembly_details.created_at ) = '".$now."' 
			GROUP BY
			DATE( assembly_details.created_at ),standard_time
		) total";

		$effData = DB::select($query2);
	}
	$response = array(
		'status' => true,
		'chartData' => $chartData,
		'effData' => $effData,
		'title_location' => $title_location
	);
	return Response::json($response);
}

public function indexStampRecord(){

	$code = Process::where('remark','=','041')->orderBy('id', 'asc')
	->get();
	return view('processes.assembly.flute.report.resumes',array(
		'code' => $code,
	))

	->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function fetchStampRecord(Request $request){
	$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
	$dateto = date('Y-m-d', strtotime($request->get('dateto')));
	$datenow = date('Y-m-d');
	$datefirst = date('Y-m-01');
	if (strlen($request->get('code')) > 0) {
		$code = 'Where location like "%'.$request->get('code').'%"';

		if($request->get('dateto') == null){
			if($request->get('datefrom') == null){
				$date = "and date(created_at) BETWEEN '".$datefirst."' and '".$datenow."'";
			}
			elseif($request->get('datefrom') != null){
				$date = "and date(created_at) BETWEEN '".$datefrom."' and '".$datenow."'";
			}
		}
		elseif($request->get('dateto') != null){
			if($request->get('datefrom') == null){
				$date = "and date(created_at) <= '".$dateto."'";
			}
			elseif($request->get('datefrom') != null){
				$date = "and date(created_at) BETWEEN '".$datefrom."' and '".$dateto."'";
			}
		}
	}else{
		$code = '';
		if($request->get('dateto') == null){
			if($request->get('datefrom') == null){
				$date = "where date(created_at) BETWEEN '".$datefirst."' and '".$datenow."'";
			}
			elseif($request->get('datefrom') != null){
				$date = "where date(created_at) BETWEEN '".$datefrom."' and '".$datenow."'";
			}
		}
		elseif($request->get('dateto') != null){
			if($request->get('datefrom') == null){
				$date = "where date(created_at) <= '".$dateto."'";
			}
			elseif($request->get('datefrom') != null){
				$date = "where date(created_at) BETWEEN '".$datefrom."' and '".$dateto."'";
			}
		}
	}

	$stamp_detail = DB::SELECT('SELECT *,1 as quantity,created_at as st_date,location as process_name FROM `assembly_logs` '.$code.' '.$date);

	$response = array(
		'status' => true,
		'stamp_detail' => $stamp_detail,
	);
	return Response::json($response);
}

public function indexNgReport($process)
{
	if ($process == 'qa') {
		$title = 'NG Report - QA';
		$title_jp = 'QA不良報告';
		$flow = AssemblyFlow::where('process','like','%qa%')->get();
	}else{
		$title = 'NG Report - Production';
		$title_jp = '生産不良報告';
		$flow = AssemblyFlow::where('process','not like','%qa%')->get();
	}

	return view('processes.assembly.flute.report.ng_report',array(
		'flow' => $flow,
		'process' => $process,
	))->with('page', 'NG Report Assy Fl')
	->with('head', 'Assembly Process')
	->with('title', $title)
	->with('title_jp', $title_jp);
}

public function fetchNgReport($process,Request $request)
{
	$date_from = $request->get('datefrom');
	$date_to = $request->get('dateto');
	$location = $request->get('process');

	if($date_to == null){
		if($date_from == null){
			$from = date('Y-m')."-01";
			$now = date('Y-m-d');
		}
		elseif($date_from != null){
			$from = $date_from;
			$now = date('Y-m-d');
		}
	}
	elseif($date_to != null){
		if($date_from == null){
			$from = date('Y-m')."-01";
			$now = $date_to;
		}
		elseif($date_from != null){
			$from = $request->get('date_from');
			$now = $date_to;
		}
	}

	if ($location != null) {
		$locnow = "AND location = '".$location."'";
	}else{
		$locnow = "";
	}

	if ($process == 'qa') {
		$ng_report = DB::SELECT("SELECT
			*,
			CONCAT( checked.employee_id, '<br>', checked.NAME ) AS checked_by,
			assembly_ng_logs.created_at AS created,
		IF
			(
				location LIKE '%qa-fungsi%',(
				SELECT
					GROUP_CONCAT( CONCAT( fungsi_detail.employee_id, '<br>', fungsi_detail.NAME ) ) 
				FROM
					assembly_details
					LEFT JOIN employee_syncs fungsi_detail ON fungsi_detail.employee_id = assembly_details.operator_id 
				WHERE
					location = 'renraku-fungsi' 
					AND tag = assembly_ng_logs.tag 
					AND serial_number = assembly_ng_logs.serial_number 
				),
				(
				SELECT
					GROUP_CONCAT( CONCAT( visual_detail.employee_id, '<br>', visual_detail.NAME ) ) 
				FROM
					assembly_details
					LEFT JOIN employee_syncs visual_detail ON visual_detail.employee_id = assembly_details.operator_id 
				WHERE
					location = 'fukiage1-visual' 
					AND tag = assembly_ng_logs.tag 
					AND serial_number = assembly_ng_logs.serial_number 
				) 
			) AS operator_id_details,
		IF
			(
				location LIKE '%qa-fungsi%',(
				SELECT
					GROUP_CONCAT( CONCAT( fungsi_log.employee_id, '<br>', fungsi_log.NAME ) ) 
				FROM
					assembly_logs
					LEFT JOIN employee_syncs fungsi_log ON fungsi_log.employee_id = assembly_logs.operator_id 
				WHERE
					location = 'renraku-fungsi' 
					AND tag = assembly_ng_logs.tag 
					AND serial_number = assembly_ng_logs.serial_number 
				),
				(
				SELECT
					GROUP_CONCAT( CONCAT( visual_log.employee_id, '<br>', visual_log.NAME ) ) 
				FROM
					assembly_logs
					LEFT JOIN employee_syncs visual_log ON visual_log.employee_id = assembly_logs.operator_id 
				WHERE
					location = 'fukiage1-visual' 
					AND tag = assembly_ng_logs.tag 
					AND serial_number = assembly_ng_logs.serial_number 
				) 
			) AS operator_id_log 
		FROM
			`assembly_ng_logs`
			LEFT JOIN employee_syncs checked ON checked.employee_id = assembly_ng_logs.employee_id 
		WHERE
			location LIKE '%qa%' 
			".$locnow."
			AND DATE( assembly_ng_logs.created_at ) BETWEEN '".$from."' AND '".$now."'");
	}else{
		$ng_report = DB::SELECT("SELECT
			*,
			CONCAT( checked.employee_id, ' - ', checked.NAME ) AS checked_by,
			COALESCE ( CONCAT( caused.employee_id, ' - ', caused.NAME ), operator_id ) AS operator_id_details,
			null as operator_id_log,
			assembly_ng_logs.created_at as created
			FROM
			`assembly_ng_logs`
			LEFT JOIN employee_syncs checked ON checked.employee_id = assembly_ng_logs.employee_id
			LEFT JOIN employee_syncs caused ON caused.employee_id = assembly_ng_logs.operator_id 
			WHERE
			location NOT LIKE '%qa%'
			".$locnow."
			AND 
			DATE(assembly_ng_logs.created_at) BETWEEN '".$from."' AND '".$now."'");
	}

	try {
		$response = array(
			'status' => true,
			'ng_report' => $ng_report,
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

public function indexKdCardCleaning()
{
	$title = "KD Card Cleaning";
	$title_jp = "";
	return view('processes.assembly.flute.kd_cleaning')
	->with('page', 'KD Card Cleaning')
	->with('head', 'Assembly Process')
	->with('title', $title)
	->with('title_jp', $title_jp);
}

public function scanKdCardCleaning(Request $request)
{
	$details = AssemblyDetail::where('tag',dechex($request->get('tag')))->first();
	$details2 = AssemblyDetail::where('tag',dechex($request->get('tag')))->get();

	$serials = AssemblySerial::where('serial_number',$details->serial_number)->where('origin_group_code',$details->origin_group_code)->first();

	$tag = AssemblyTag::where('serial_number',$details->serial_number)->where('model',$details->model)->where('origin_group_code',$details->origin_group_code)->first();
	$tag->serial_number = null;
	$tag->model = null;

	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $details->serial_number)
	->where('stamp_inventories.model', '=', $details->model)
	->where('origin_group_code',$details->origin_group_code);

	$now = date('Y-m-d H:i:s');

	foreach ($details2 as $detail) {
		$detailss = new AssemblyLog([
			'tag' => $detail->tag,
			'serial_number' => $detail->serial_number,
			'model' => $detail->model,
			'location' => $detail->location,
			'operator_id' => $detail->operator_id,
			'sedang_start_date' => $detail->sedang_start_date,
			'sedang_finish_date' => $detail->sedang_finish_date,
			'origin_group_code' => $detail->origin_group_code,
			'created_by' => $detail->created_by
		]);
		$detailss->save();
	}

	AssemblyDetail::where('tag',dechex($request->get('tag')))->forceDelete();

	$log = new AssemblyLog([
		'tag' => $details->tag,
		'serial_number' => $details->serial_number,
		'model' => $details->model,
		'location' => 'labelkd-print',
		'operator_id' => Auth::user()->username,
		'sedang_start_date' => $now,
		'sedang_finish_date' => $now,
		'origin_group_code' => '041',
		'created_by' => Auth::user()->username
	]);
					

	try {
		$serials->forceDelete();
		$tag->save();
		$stamp_inventory->forceDelete();
		$log->save();

		$response = array(
			'status' => true,
			'message' => 'Cleaning Card Success'
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => 'Failed Cleaning Data'
		);
		return Response::json($response);
	}
}

public function fetchKdCardCleaning(Request $request)
{
	try {
		$history = DB::SELECT("SELECT
			* 
		FROM
			assembly_logs 
		WHERE
			location = 'labelkd-print' 
			AND DATE( created_at ) >= DATE_FORMAT( NOW() - INTERVAL 3 DAY, '%Y-%m-%d' ) 
			AND DATE( created_at ) <= DATE_FORMAT( NOW(), '%Y-%m-%d' )");
		$response = array(
			'status' => true,
			'history' => $history
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => 'Failed Get Data'
		);
		return Response::json($response);
	}
}

function dec2hex($number){

	$hexvalues = array('0','1','2','3','4','5','6','7',
		'8','9','A','B','C','D','E','F');
	$hexval = '';
	while($number != '0')
	{
		$hexval = $hexvalues[bcmod($number,'16')].$hexval;
		$number = bcdiv($number,'16',0);
	}
	return $hexval;
}
}

