<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\AssemblyNgTemp;
use App\AssemblyNgLog;
use App\AssemblyDetail;
use App\AssemblyInventory;
use App\AssemblyOnko;
use DateTime;

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
			'tanpoawase-kensa',
			'tanpoawase-fungsi',
			'kango-fungsi',
			'kango-kensa',
			'renraku-fungsi',
			'qa-fungsi',
			'fukiage1-visual',
			'qa-visual1',
			'qa-visual2',
		];
	}

	public function indexFlutePrintLabel(){
		$title = 'Flute Print Packing Labels';
		$title_jp = '(Flute Print Packing Labels)';
		return view('processes.assembly.flute.print_label', array(
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

		$ng_lists = DB::select("SELECT DISTINCT(ng_name) FROM assembly_ng_lists where origin_group_code = '041' and location = '".$loc_spec."' and process = '".$process."'");

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
		
		$employee = db::table('assembly_operators')->join('employee_syncs','assembly_operators.employee_id','=','employee_syncs.employee_id')->where('tag', '=', $request->get('employee_id'))->first();

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

	public function scanAssemblyKensa(Request $request){
		
		$details = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', $request->get('tag'))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->first();

		$details2 = db::table('assembly_details')->join('employee_syncs','assembly_details.operator_id','=','employee_syncs.employee_id')->where('tag', '=', $request->get('tag'))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->orderBy('assembly_details.id', 'asc')->get();

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
		$tag = $request->get('tag');

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
		$tag = $request->get('tag');
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
						'tag' => $request->get('tag'),
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
					'tag' => $request->get('tag'),
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
		$tag = $request->get('tag');

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
			$tag = $request->get('tag');

			$started_at = "";
			$finished_at = date('Y-m-d H:i:s');

			$ng_temp = AssemblyNgTemp::where('model',$model)->where('serial_number',$serial_number)->where('employee_id',$employee_id)->where('tag',$tag)->get();
			foreach ($ng_temp as $ng) {
				$assembly_ng_log = new AssemblyNgLog([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
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

			$assembly_details = new AssemblyDetail([
				'tag' => $request->get('tag'),
				'serial_number' => $request->get('serial_number'),
				'model' => $request->get('model'),
				'location' => $request->get('location'),
				'operator_id' => $request->get('employee_id'),
				'sedang_start_date' => $started_at,
				'value_atas' => $ng->value_atas,
				'sedang_start_date' => $ng->started_at,
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

			try{
				$assembly_inventories = AssemblyInventory::where('model',$model)->where('serial_number',$serial_number)->where('tag',$tag)->first();
				$assembly_inventories->location = $request->get('location');
				$assembly_inventories->created_by = $request->get('employee_id');
				$assembly_inventories->save();
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
				0 as total_check,
				0 as total_ok,
				0 as ng_rate,
				count( id ) AS total_ng 
			FROM
				assembly_ng_logs 
			WHERE
				DATE( assembly_ng_logs.created_at )= '".$now."' '".$addlocation."'
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
}

