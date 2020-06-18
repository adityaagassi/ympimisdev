<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
use App\Process;
use App\Material;
use DateTime;
use App\Libraries\ActMLEasyIf;

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

	public function indexFluteStamp(){

		// $models = DB::SELECT('SELECT DISTINCT(model) FROM `materials` where origin_group_code = "041" and category = "FG"');

		$models = db::table('materials')->where('origin_group_code', '=', '041')
		->where('category', '=', 'FG')
		->orderBy('model', 'asc')
		->select('model')
		->distinct()
		->get();

		$title = 'Flute Stamp';
		$title_jp = '(Flute Stamp)';
		return view('processes.assembly.flute.stamp', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'models' => $models
		))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
	}

	public function stampFlute(Request $request){
		$counter = db::table('plc_counters')
		->where('origin_group_code', '=', $request->get('origin_group_code'))
		->first();

		$plc = new ActMLEasyIf(0);
		$datas = $plc->read_data('D0', 5);

		if($counter->plc_counter == $datas[0]){
			$response = array(
				'status' => true,
				'status_code' => 'no_stamp',
			);
			return Response::json($response);
		}

		try{
			$cek_serial = new AssemblySerial([
				'serial_number' => $request->get('serial'),
				'origin_group_code' => $request->get('origin_group_code')
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

		$tag = AssemblyTag::where('remark', '=', $request->get('tagName'))->first();
		$material = db::table('materials')->where('model', '=', $request->get('model'))
		->where('xy', '=', 'SP')->first();

		$log = new AssemblyLog([
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
		if(count($material) >= 0){
			$sp = 'SP';
		}

		if($request->get('location') != 'stampkd-process'){
			$inventory = AssemblyInventory::firstOrCreate(
				['serial_number' => $request->get('serial'), 'origin_group_code' => $request->get('origin_group_code')],
				['tag' => $tag->tag, 'model' => $request->get('model'), 'location' => $request->get('location'), 'location_next' => 'perakitan-process', 'remark' => $sp, 'created_by' => $request->get('op_id')]
			);
			$inventory->location = $request->get('location');
		}

		$tag->serial_number = $request->get('serial');
		$tag->model = $request->get('model');
		
		try{
			DB::transaction(function() use ($log, $inventory, $tag){
				$inventory->save();
				$log->save();
				$tag->save();
			});
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
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
		$date = date('Y-m-d');
		$date = '2020-06-15';

		$logs = AssemblyLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_logs.operator_id')
		->where('assembly_logs.origin_group_code', '=', $request->get('origin_group_code'))
		->where(db::raw('date(assembly_logs.created_at)'), '=', $date)
		->where('assembly_logs.location', '=', 'stamp-process')
		->whereOr('assembly_logs.location', '=', 'stampkd-process')
		->select('assembly_logs.serial_number', 'assembly_logs.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_logs.created_at')
		->orderBy('assembly_logs.created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'logs' => $logs
		);
		return Response::json($response);

	}

	public function indexFlutePrintLabel(){
		$title = 'Flute Print Packing Labels';
		$title_jp = '(Flute Print Packing Labels)';
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
			$title_jp= '??';
		}
		if($location == 'kariawase-process'){
			$title = 'Kariawase Process Flute';
			$title_jp= '??';
		}
		if($location == 'tanpoawase-process'){
			$title = 'Tanpo Awase Process Flute';
			$title_jp= '??';
		}
		if($location == 'tanpoire-process'){
			$title = 'Tanpoire Process Flute';
			$title_jp= '??';
		}
		if($location == 'kango-process'){
			$title = 'Kango Process Flute';
			$title_jp= '??';
		}
		if($location == 'renraku-process'){
			$title = 'Renraku Process Flute';
			$title_jp= '??';
		}
		if($location == 'fukiage1-process'){
			$title = 'Fukiage 1 Process Flute';
			$title_jp= '??';
		}
		if($location == 'fukiage2-process'){
			$title = 'Fukiage 2 Process Flute';
			$title_jp= '??';
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
		$tag = dechex($tag);

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

		$work_stations = DB::select("SELECT
			location,
			location_number,
			online_time,
			operator_id,
			name,
			sedang_serial_number,
			sedang_model,
			TIME(sedang_time) as sedang_time,
			DATE(sedang_time) as sedang_date
			FROM
			assemblies 
			LEFT JOIN employee_syncs on employee_syncs.employee_id = assemblies.operator_id
			WHERE
			location = '".$loc."' 
			AND origin_group_code = '041'");

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
				'ws' => strtoupper($ws->location.'-'.$ws->location_number),
				'employee_id' => $ws->operator_id,
				'employee_name' => strtoupper($ws->name),
				'sedang' => $board_sedang,
				'sedang_time' => $sedang_time->format('%H:%i:%s')
			]);
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'boards' => $boards,
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
			$title_jp= '??';
		}
		if($location == 'kariawase-visual'){
			$title = 'Kariawase Kensa Visual Flute';
			$title_jp= '??';
		}
		if($location == 'perakitanawal-kensa'){
			$title = 'Perakitan Awal Kensa Flute';
			$title_jp= '??';
		}
		if($location == 'tanpoawase-kensa'){
			$title = 'Tanpo Awase Kensa Flute';
			$title_jp= '??';
		}
		if($location == 'tanpoawase-fungsi'){
			$title = 'Tanpo Awase Kensa Fungsi Flute';
			$title_jp= '??';
		}
		if($location == 'kango-fungsi'){
			$title = 'Kango Kensa Fungsi (Gata,Seri) Flute';
			$title_jp= '??';
		}
		if($location == 'kango-kensa'){
			$title = 'Kango Kensa Flute';
			$title_jp= '??';
		}
		if($location == 'renraku-fungsi'){
			$title = 'Renraku Kensa Fungsi Flute';
			$title_jp= '??';
		}
		if($location == 'qa-fungsi'){
			$title = 'QA Kensa Fungsi Flute';
			$title_jp= '??';
		}
		if($location == 'fukiage1-visual'){
			$title = 'Fukiage 1 Kensa Visual Flute';
			$title_jp= '??';
		}
		if($location == 'qa-visual1'){
			$title = 'QA 1 Kensa Visual Flute';
			$title_jp= '??';
		}
		if($location == 'qa-visual2'){
			$title = 'QA 2 Kensa Visual Flute';
			$title_jp= '??';
		}
		if($location == 'qa-kensasp'){
			$title = 'QA Kensa SP';
			$title_jp= '??';
		}

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

	public function scanAssemblyKensa(Request $request)
	{

		$details = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->first();

		$details2 = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', dechex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->orderBy('assembly_details.id', 'asc')->get();

		if($details == null){
			$response = array(
				'status' => false,
				'message' => 'Serial Number tidak ditemukan',
			);
			return Response::json($response);			
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

	public function showNgDetail(Request $request){

		$ng_detail = db::select("select * from assembly_ng_lists where ng_name = '".$request->get('ng_name')."' and location = '".$request->get('location')."' and process = '".$request->get('process')."' and origin_group_code = '041'");

		if ($request->get('ng_name') == 'Renraku') {
			$onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = 'renraku'");
		}else{
			$onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = 'all'");
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
			* 
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
			WHERE
			model = '".$model."' 
			AND tag = '".$tag."' 
			AND serial_number = '".$serial_number."' 
			AND location = '".$location."'");

		if($details == null){
			$response = array(
				'status' => false,
				'message' => 'Data Tidak Ditemukan',
				'details' => $location,
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

		$onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = '".$location."'");

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
			}

			$assembly_invent = AssemblyInventory::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->first();

			$remark = $assembly_invent->remark;

			if ($remark == 'SP') {
				$assembly_details = AssemblyDetail::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->delete();

				$detail = new AssemblyLog([
					'tag' => strtoupper(dechex($request->get('tag'))),
					'serial_number' => $request->get('serial_number'),
					'model' => $request->get('model'),
					'location' => $request->get('location'),
					'operator_id' => $request->get('employee_id'),
					'sedang_start_date' => $started_at,
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
					'sedang_start_date' => $started_at,
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
					$assembly_inventories->delete();
					$asstag = AssemblyTag::where('tag',strtoupper(dechex($request->get('tag'))))->first();
					$asstag->serial_number = null;
					$asstag->model = null;
					$asstag->save();
				}else{
					$assembly_flow = AssemblyFlow::where('process',$location_next)->where('origin_group_code','041')->first();
					$id_flow_now = $assembly_flow->id;
					$id_flow_next = $id_flow_now + 1;
					$assembly_flow_next = AssemblyFlow::where('id',$id_flow_next)->where('origin_group_code','041')->first();
					$next_process = $assembly_flow_next->process;
					$assembly_inventories->location_next = $next_process;
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

	public function indexRequestDisplay($origin_group_code)
	{
		return view('processes.assembly.flute.display.assembly_request', array( 
			'title' => 'Assembly Request Material',
			'title_jp' => '??',
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
			DISTINCT ( model )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( model )) AS total_check 
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
			DISTINCT ( model )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( model )) AS total_check 
			FROM
			assembly_details 
			WHERE
			DATE( assembly_details.created_at )= '".$now."' ".$addlocation." 
			)) check_total 
			) - count( DISTINCT ( model ) ) AS total_ok,
			((
			SELECT
			SUM( check_total.total_check ) AS total_check 
			FROM
			((
			SELECT
			COUNT(
			DISTINCT ( model )) AS total_check 
			FROM
			assembly_logs 
			WHERE
			DATE( assembly_logs.sedang_start_date )= '".$now."' ".$addlocation." UNION ALL
			SELECT
			COUNT(
			DISTINCT ( model )) AS total_check 
			FROM
			assembly_details 
			WHERE
			DATE( assembly_details.created_at )= '".$now."' ".$addlocation." 
			)) check_total 
			) / count( DISTINCT ( model ) )) * 100 AS ng_rate,
			count( DISTINCT ( model ) ) AS total_ng 
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
		$title_jp = '??';
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

