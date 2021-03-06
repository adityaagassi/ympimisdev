<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\MpProcess;
use App\MpMachine;
use App\MpRecordProd;
use App\MpKanagata;
use App\MaterialPlantDataList;
use App\MpKanagataLog;
use App\MpTroubleLog;
use Response;
use DataTables;
use Excel;
use App\User;
use File;
use DateTime;
use Illuminate\Support\Arr;
use App\OriginGroup;

class PressController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->mesin = ['Amada 1',
      			'Amada 2',
      			'Amada 3',
      			'Amada 4',
      			'Amada 5',
      			'Amada 6',
      			'Amada 7'];
    }

    public function indexMasterKanagata()
    {
    	$title = 'Master Kanagata';
		$title_jp = '金型マスター';

		$product = OriginGroup::get();
        $product2 = OriginGroup::get();

		$mpdl = MaterialPlantDataList::get();
        $mpdl2 = MaterialPlantDataList::get();

		return view('press.master_kanagata', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mpdl' => $mpdl,
            'mpdl2' => $mpdl2,
			'product' => $product,
            'product2' => $product2,
		))->with('page', 'Master Kanagata');
    }

    public function fetchMasterKanagata()
    {
    	$lists = MpKanagata::get();

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
    }

    public function addKanagata(Request $request)
	{
		$material = MaterialPlantDataList::where('material_number',$request->get('material_number'))->first();

		$material_description = $material->material_description;

		$lists = DB::table('mp_kanagatas')
		->insert([
			'material_number' => $request->get('material_number'),
			'material_description' => $material_description,
			'material_name' => $request->get('material_name'),
			'process' => 'Forging',
			'product' => $request->get('product'),
			'part' => $request->get('part'),
			'remark' => 'Press',
			'punch_die_number' => $request->get('punch_die_number'),
			'created_by' => Auth::id()]);

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function destroyKanagata($id)
	{
		$mp_kanagata = MpKanagata::find($id);
      	$mp_kanagata->delete();

		return redirect('index/press/master_kanagata')->with('status', 'Kanagata has been deleted.');
	}

	public function getKanagata(Request $request)
	{
		$list = MpKanagata::find($request->get('id'));

		$response = array(
			'status' => true,
			'lists' => $list
		);
		return Response::json($response);
	}

	public function updateKanagata(Request $request)
	{
		$material = MaterialPlantDataList::where('material_number',$request->get('material_number'))->first();

		$material_description = $material->material_description;

		$kanagata = MpKanagata::find($request->get('id'));
		$kanagata->material_number = $request->get('material_number');
		$kanagata->material_description = $material_description;
		$kanagata->material_name = $request->get('material_name');
		$kanagata->part = $request->get('part');
		$kanagata->punch_die_number = $request->get('punch_die_number');
		$kanagata->product = $request->get('product');
		$kanagata->save();
		
		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function scanPressOperator(Request $request){

		$nik = $request->get('employee_id');

		if(strlen($nik) > 9){
			$nik = substr($nik,0,9);
		}

		$employee = db::table('employees')->where('employee_id', 'like', '%'.$nik.'%')->first();

		if(count($employee) > 0 ){
			$response = array(
				'status' => true,
				'message' => 'Logged In',
				'employee' => $employee
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Employee ID Invalid'
			);
			return Response::json($response);
		}
	}

	public function fetchPressList(Request $request){

		$lists = MpKanagata::where('process', '=', $request->get('process'))
		->select('mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description','mp_kanagatas.punch_die_number', 'mp_kanagatas.id')
		->distinct()
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}

	public function fetchTroubleList(Request $request){

		$lists = MpTroubleLog::where('date', '=', $request->get('date'))
		->where('pic', '=', $request->get('pic'))
		->where('material_number', '=', $request->get('material_number'))
		->where('process', '=', $request->get('process'))
		->where('machine', '=', $request->get('machine'))
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}

	public function fetchProcess(Request $request){

		$count = MpProcess::where('mp_processes.process_name', '=', $request->get('process'))
		->get();

		$process_desc = '';
		foreach ($count as $count) {
            $process_desc .= '<option value="'.$count->process_desc.'">'.$count->process_desc.'</option>';
        }

		$response = array(
			'status' => true,
			'count' => $count,
			'process_desc' => $process_desc
		);
		return Response::json($response);
	}

	public function fetchMaterialList(Request $request){

		$count = MpKanagata::where('mp_kanagatas.id', '=', $request->get('id'))
		->first();

		$kanagata_material_number = $count->material_number;
		$process = $count->process;
		$product = $count->product;

		$punch = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', 'PUNCH%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$punch_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', 'PUNCH%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$punch_data = '';
		foreach ($punch as $punch) {
            $punch_data .= '<option value="'.$punch->punch_die_number.'">'.$punch->punch_die_number.'</option>';
        }

		$dies = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', '=', 'DIE')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$dies_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', '=', 'DIE')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$dies_data = '';
		foreach ($dies as $dies) {
            $dies_data .= '<option value="'.$dies->punch_die_number.'">'.$dies->punch_die_number.'</option>';
        }

		$response = array(
			'status' => true,
			'count' => $count,
			'punch' => $punch,
			'dies' => $dies,
			'punch_first' => $punch_first,
			'dies_first' => $dies_first,
			'punch_data' => $punch_data,
			'dies_data' => $dies_data,
		);
		return Response::json($response);
	}

	public function fetchPunch(Request $request){

		$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."' and punch_status = 'Running'");
		
		$total_punch = 0;
	      if(count($kanagata_log_punch) == 0){
	      	$total_punch = 0;
	      }else{
	      	foreach ($kanagata_log_punch as $kanagata_log_punch) {
		       $total_punch = $kanagata_log_punch->punch_total;
		    }
	      }

		$response = array(
			'status' => true,
			'total_punch' => $total_punch
		);
		return Response::json($response);
	}

	public function fetchDie(Request $request){

		$kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."' and die_status = 'Running'");
		$total_die = 0;
	      if(count($kanagata_log_dies) == 0){
	      	$total_die = 0;
	      }else{
	      	foreach ($kanagata_log_dies as $kanagata_log_dies) {
		       $total_die = $kanagata_log_dies->die_total;
		    }
	      }

		$response = array(
			'status' => true,
			'total_die' => $total_die
		);
		return Response::json($response);
	}

	public function create(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		// if($product == "Saxophone"){
		// 	$title_jp = "生産のプレス機　‐　サックス";
		// }
		// elseif($product == "Flute"){
		// 	$title_jp = "生産のプレス機　‐　フルート";
		// }
		// elseif($product == "Clarinet"){
			$title_jp = "生産のプレス機";
		// }

		$data = array(
                	'process' => $process,
                	'machine' => $machine);
		return view('press.create_press_data',$data)->with('page', 'Press Machine Production')->with('title_jp', $title_jp);
	}

	function store(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              // $electric_supply_time = new DateTime($request->get('electric_supply_time'));
              // $lepas_molding = new DateTime($request->get('lepas_molding'));
              // $mins = $electric_supply_time->diff($lepas_molding);
              // if($request->get('lepas_molding') == '0:00:00'){
              	$lepas_molding_new = $request->get('lepas_molding');
              // }else{
              // 	$lepas_molding_new = $mins->format('%H:%I:%S');
              // }
              
                MpRecordProd::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'punch_number' => $request->get('punch_number'),
                    'die_number' => $request->get('die_number'),
                    'start_time' => $request->get('start_time'),
                    'end_time' => $request->get('end_time'),
                    'lepas_molding' => $lepas_molding_new,
                    'pasang_molding' => $request->get('pasang_molding'),
                    'process_time' => $request->get('process_time'),
                    'kensa_time' => $request->get('kensa_time'),
                    'electric_supply_time' => $request->get('electric_supply_time'),
                    'data_ok' => $request->get('data_ok'),
                    'punch_value' => $request->get('punch_value'),
                    'die_value' => $request->get('die_value'),
                    'created_by' => $id_user
                ]);

               $kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process like '%Forging%' and material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."' and die_status = 'Running'");

				$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = '%Forging%' and material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."' and punch_status = 'Running'");

			  $total_punch = 0;
		      if(count($kanagata_log_punch) == 0){
		      	$total_punch = 0;
		      }else{
		      	foreach ($kanagata_log_punch as $kanagata_log_punch) {
			       $total_punch = $kanagata_log_punch->punch_total;
			    }
		      }

		      $total_die = 0;
		      if(count($kanagata_log_dies) == 0){
		      	$total_die = 0;
		      }else{
		      	foreach ($kanagata_log_dies as $kanagata_log_dies) {
			       $total_die = $kanagata_log_dies->die_total;
			    }
		      }

		      $total_punch = $total_punch + $request->get('punch_value');
		      $total_die = $total_die + $request->get('die_value');
              
	          MpKanagataLog::create([
	                'date' => $request->get('date'),
	                'pic' => $request->get('pic'),
	                'product' => $request->get('product'),
	                'machine' => $request->get('machine'),
	                'shift' => $request->get('shift'),
	                'material_number' => $request->get('material_number'),
	                'process' => $request->get('process'),
	                'punch_number' => $request->get('punch_number'),
	                'die_number' => $request->get('die_number'),
	                'start_time' => $request->get('start_time'),
	                'end_time' => $request->get('end_time'),
	                'punch_value' => $request->get('punch_value'),
	                'die_value' => $request->get('die_value'),
	                'punch_total' => $total_punch,
	                'die_total' => $total_die,
	                'punch_status' => 'Running',
	                'die_status' => 'Running',
	                'created_by' => $id_user
	          ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function finish_trouble(Request $request)
    {
        	try{
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              $trouble = MpTroubleLog::find($request->get('id'));
              $trouble->end_time = date('Y-m-d h:i:s');
              $trouble->save();

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function store_trouble(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
                MpTroubleLog::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'start_time' => $request->get('start_time'),
                    'reason' => $request->get('reason'),
                    'created_by' => $id_user
                ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    // Khusus Monitoring / Grafik

    public function monitoring() {
    	$title = 'Press Machine Monitoring';
		$title_jp = 'プレス機管理';

		$process = DB::table('mp_processes')->orderBy('id', 'ASC')->get();

		return view('press.monitoring_result', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'process' => $process
		))->with('page', 'Machine press')->with('head', 'Machine Press');
    }

    public function fetchMonitoring(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$dateTitle = date("d M Y", strtotime($date));

		 $process = $request->get('proses');

	      if ($process != null) {
	          $proses = json_encode($process);
	          $pro = str_replace(array("[","]"),array("(",")"),$proses);

	          $where = 'and mp_record_prods.process in'.$pro;
	      }else{
	          $where = '';
	      }

		// $data = db::select("select mp_machines.machine_name, COALESCE(sum(mp_record_prods.data_ok),0)  as actual_shoot, COALESCE(mp_record_prods.date,CURDATE()) as tgl , TRUNCATE(SUM(TIME_TO_SEC(mp_record_prods.process_time) / 60 ),2) as waktu_mesin from mp_machines left join mp_record_prods on mp_machines.machine_name = mp_record_prods.machine left join mp_processes on mp_record_prods.process = mp_processes.process_desc where DATE_FORMAT(COALESCE(mp_record_prods.date,CURDATE()),'%Y-%m-%d') = '".$date."' ".$where." GROUP BY mp_machines.machine_name,mp_record_prods.date");

	     $data = db::select("select machine_name, sum(data_ok) as actual_shoot, CURDATE() as tgl, sum(waktu) as waktu_mesin from (select machine_name, 0 as data_ok, CURDATE(), 0 as waktu from mp_machines UNION ALL select machine_name, data_ok, date, ROUND(TIME_TO_SEC(process_time) / 60,1) as waktu from mp_machines LEFT JOIN mp_record_prods on mp_machines.machine_name = mp_record_prods.machine where mp_record_prods.date = '".$date."' ) as aw GROUP BY machine_name");

		$operator = db::select("SELECT name,
				sum( data_ok ) AS actual_shot,
				CURDATE() AS date,
				sum( waktu ) AS waktu_total 
			FROM
				(
				SELECT
					employee_syncs.name,
					0 AS data_ok,
					CURDATE(),
					0 AS waktu 
				FROM
					employee_groups
					JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
				WHERE
					location = 'Press'
					and employee_groups.deleted_at is null
					UNION ALL
				SELECT
					employee_syncs.name,
					data_ok,
					mp_record_prods.date,
					ROUND( TIME_TO_SEC( process_time ) / 60, 1 ) AS waktu 
				FROM
					employee_groups
					LEFT JOIN mp_record_prods ON employee_groups.employee_id = mp_record_prods.pic
					JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
					AND location = 'Press' 
					AND mp_record_prods.date = '".$date."'
					and employee_groups.deleted_at is null
				) AS aw 
			GROUP BY
			name");


		$response = array(
			'status' => true,
			'datas' => $data,
			'date' => $date,
			'operator' => $operator,
			'dateTitle' => $dateTitle
		);
		return Response::json($response);
	}

	public function detail_press(Request $request){

      $machine = $request->get("mesin");
      $tanggal = $request->get("tanggal");

      $query = "select date, name, machine, material_number, data_ok from mp_record_prods join employees on mp_record_prods.pic = employees.employee_id where date='".$tanggal."' and machine='".$machine."'";

      $detail = db::select($query);

      return DataTables::of($detail)
      ->make(true);
    }

    public function detail_pic(Request $request){

      $pic = $request->get("pic");
      $tanggal = $request->get("tanggal");

      $query = "select date, name, machine, material_number, data_ok from mp_record_prods join employees on mp_record_prods.pic = employees.employee_id where date='".$tanggal."' and name='".$pic."'";

      $detail = db::select($query);

      return DataTables::of($detail)
      ->make(true);
    }

	public function monitoring2() {
    	$title = 'Press Machine Monitoring';
		$title_jp = 'プレス機管理';

		$process = DB::table('mp_processes')->orderBy('id', 'ASC')->get();

		return view('press.monitoring_result2', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'process' => $process
		))->with('page', 'Machine press')->with('head', 'Machine Press');
    }

	public function report_trouble(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$report_trouble = db::select("select mp_trouble_logs.id,mp_materials.material_number,employees.employee_id,employees.name,date,mp_trouble_logs.product,mp_trouble_logs.process,machine,start_time,end_time,reason,mp_materials.material_name
			from mp_trouble_logs
			join employee_groups on employee_groups.employee_id = mp_trouble_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_trouble_logs.material_number= mp_materials.material_number
			ORDER BY mp_trouble_logs.id desc");

		$data = array(
                	'process' => $process,
                	'report_trouble' => $report_trouble,
                	'machine' => $machine);
		return view('press.report_press_trouble',$data)->with('page', 'Press Machine Trouble Report')->with('title_jp', "プレス機トラブルリポート");
	}

	public function filter_report_trouble(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$report_trouble = db::select("select mp_trouble_logs.id,mp_materials.material_number,employees.employee_id,employees.name,date,mp_trouble_logs.product,mp_trouble_logs.process,machine,start_time,end_time,reason,mp_materials.material_name
			from mp_trouble_logs
			join employee_groups on employee_groups.employee_id = mp_trouble_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_trouble_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_trouble_logs.id desc");

		$data = array(
                	'process' => $process,
                	'report_trouble' => $report_trouble,
                	'machine' => $machine);
		return view('press.report_press_trouble',$data)->with('page', 'Press Machine Trouble Report')->with('title_jp', "??");
	}

	public function report_prod_result(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		// $prod_result = db::select("SELECT
		// 		*,
		// 		mp_record_prods.id AS prod_result_id 
		// 	FROM
		// 		mp_record_prods
		// 		JOIN employee_groups ON employee_groups.employee_id = mp_record_prods.pic
		// 		JOIN employees ON employee_groups.employee_id = employees.employee_id
		// 		JOIN mp_materials ON mp_record_prods.material_number = mp_materials.material_number 
		// 	WHERE
		// 		DATE( mp_record_prods.date ) BETWEEN '".$first."' 
		// 		AND '".$now."' 
		// 	ORDER BY
		// 		mp_record_prods.id DESC");

		$emp = DB::SELECT("SELECT
				* 
			FROM
				employee_groups
				JOIN employee_syncs ON employee_groups.employee_id = employee_syncs.employee_id 
			WHERE
				location = 'Press'");

		$data = array(
                	'process' => $process,
                	// 'prod_result' => $prod_result,
                	'mesin' => $this->mesin,
                	'emp' => $emp,
                	'machine' => $machine);
		return view('press.report_prod_result',$data)->with('page', 'Press Machine Production Result')->with('title_jp', "??");
	}

	public function filter_report_prod_result(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$prod_result = db::select("
			select *,
			mp_record_prods.process as process_asli,
				mp_record_prods.id AS prod_result_id 
			from mp_record_prods
			join employee_syncs on mp_record_prods.pic= employee_syncs.employee_id
			join mp_materials on mp_record_prods.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_record_prods.id desc");

		$emp = DB::SELECT("SELECT
				* 
			FROM
				employee_groups
				JOIN employee_syncs ON employee_groups.employee_id = employee_syncs.employee_id 
			WHERE
				location = 'Press'");

		$data = array(
                	'process' => $process,
                	'prod_result' => $prod_result,
                	'emp' => $emp,
                	'mesin' => $this->mesin,
                	'machine' => $machine);
		return view('press.report_prod_result',$data)->with('page', 'Press Machine Production Result')->with('title_jp', "??");
	}

	public function report_kanagata_lifetime(){

		$process = DB::SELECT("SELECT DISTINCT(process_desc) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$username = Auth::user()->username;

		$kanagata = MpKanagata::where('process', 'like', '%Forging%')
		->select('mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description','mp_kanagatas.punch_die_number', 'mp_kanagatas.id', 'mp_kanagatas.part')
		->distinct()
		->get();

		// $kanagata_lifetime = db::select("SELECT
		// 	*,
		// 	mp_kanagata_logs.id AS kanagata_lifetime_id 
		// FROM
		// 	mp_kanagata_logs
		// 	JOIN employee_groups ON employee_groups.employee_id = mp_kanagata_logs.pic
		// 	JOIN employees ON employee_groups.employee_id = employees.employee_id
		// 	JOIN mp_materials ON mp_kanagata_logs.material_number = mp_materials.material_number 
		// WHERE
		// 	DATE( mp_kanagata_logs.date ) BETWEEN '".$first."' 
		// 	AND '".$now."' 
		// ORDER BY
		// 	mp_kanagata_logs.id DESC");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	// 'kanagata_lifetime' => $kanagata_lifetime,
                	'kanagata' => $kanagata,
                	'username' => $username,
                	'machine' => $machine);
		return view('press.report_kanagata_lifetime',$data)->with('page', 'Press Machine Kanagata Lifetime')->with('title_jp', "??");
	}

	public function filter_report_kanagata_lifetime(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

	    $username = Auth::user()->username;

	    $kanagata = MpKanagata::where('process', 'like', '%Forging%')
		->select('mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description','mp_kanagatas.punch_die_number', 'mp_kanagatas.id', 'mp_kanagatas.part')
		->distinct()
		->get();

		$process = DB::SELECT("SELECT DISTINCT(process_desc) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id
			from mp_kanagata_logs
			join employee_syncs on mp_kanagata_logs.pic = employee_syncs.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_kanagata_logs.id desc");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	'kanagata_lifetime' => $kanagata_lifetime,
					'username' => $username,
					'kanagata' => $kanagata,
                	'machine' => $machine);
		return view('press.report_kanagata_lifetime',$data)->with('page', 'Press Machine Kanagata Lifetime')->with('title_jp', "??");
	}

	function getkanagatalifetime(Request $request)
    {
          try{
            if($request->get('kanagata') == 'Punch'){
            	$detail = MpKanagataLog::where('punch_number',$request->get("kanagata_number"))->orderBy('id', 'DESC')->first();
	            $data = array('kanagata_log_id' => $detail->id,
	            			  'date' => $detail->date,
	                          'pic' => $detail->pic,
	                          'pic_name' => $detail->employee_pic->name,
	                          'shift' => $detail->shift,
	                          'product' => $detail->product,
	                          'material_number' => $detail->material_number,
	                          'part' => $detail->material->material_name,
	                          'process' => $detail->process,
	                      	  'machine' => $detail->machine,
	                      		'punch_number' => $detail->punch_number,
	                      		'die_number' => $detail->die_number,
	                      		'punch_value' => $detail->punch_value,
	                      		'die_value' => $detail->die_value,
	                      		'punch_total' => $detail->punch_total,
	                      		'die_total' => $detail->die_total,
	                      		'start_time' => $detail->start_time,
	                      		'end_time' => $detail->end_time);
            }
            else{
            	$detail = MpKanagataLog::where('die_number',$request->get("kanagata_number"))->orderBy('id', 'DESC')->first();
	            $data = array('kanagata_log_id' => $detail->id,
	            			  'date' => $detail->date,
	                          'pic' => $detail->pic,
	                          'pic_name' => $detail->employee_pic->name,
	                          'shift' => $detail->shift,
	                          'product' => $detail->product,
	                          'material_number' => $detail->material_number,
	                          'part' => $detail->material->material_name,
	                          'process' => $detail->process,
	                      	  'machine' => $detail->machine,
	                      		'punch_number' => $detail->punch_number,
	                      		'die_number' => $detail->die_number,
	                      		'punch_value' => $detail->punch_value,
	                      		'die_value' => $detail->die_value,
	                      		'punch_total' => $detail->punch_total,
	                      		'die_total' => $detail->die_total,
	                      		'start_time' => $detail->start_time,
	                      		'end_time' => $detail->end_time);
            }

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function getprodresult(Request $request)
    {
          try{
        	$detail = MpRecordProd::find($request->get("id"));
            $data = array('prod_result_id' => $detail->id,
            			  'date' => $detail->date,
                          'pic' => $detail->pic,
                          'pic_name' => $detail->employee_pic->name,
                          'shift' => $detail->shift,
                          'product' => $detail->product,
                          'material_number' => $detail->material_number,
                          'part' => $detail->material->material_name,
                          'process' => $detail->process,
                      	  'machine' => $detail->machine,
                      		'punch_number' => $detail->punch_number,
                      		'die_number' => $detail->die_number,
                      		'data_ok' => $detail->data_ok,
                      		'punch_value' => $detail->punch_value,
                      		'die_value' => $detail->die_value,
                      		'start_time' => $detail->start_time,
                      		'end_time' => $detail->end_time,
                      		'lepas_molding' => $detail->lepas_molding,
                      		'pasang_molding' => $detail->pasang_molding,
                      		'process_time' => $detail->process_time,
                      		'kensa_time' => $detail->kensa_time,
                      		'electric_supply_time' => $detail->electric_supply_time,);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function updateProdResult(Request $request,$id)
    {
        try{
                $prod_result = MpRecordProd::find($id);

                $date = $prod_result->date;
              	$pic = $prod_result->pic;
              	$pic_name = $prod_result->employee_pic->name;
              	$shift = $prod_result->shift;
              	$product = $prod_result->product;
              	$material_number = $prod_result->material_number;
              	$part = $prod_result->material->material_name;
              	$process = $prod_result->process;
          	  	$machine = $prod_result->machine;
          		$punch_number = $prod_result->punch_number;
          		$die_number = $prod_result->die_number;
          		$punch_value = $prod_result->punch_value;
          		$die_value = $prod_result->die_value;

          		$kanagatas = DB::SELECT("SELECT
					* 
				FROM
					`mp_kanagata_logs` 
				WHERE
					date = '".$date."' 
					AND pic = '".$pic."' 
					AND shift = '".$shift."' 
					AND product = '".$product."' 
					AND material_number = '".$material_number."' 
					AND machine = '".$machine."' 
					AND punch_number = '".$punch_number."' 
					AND die_number = '".$die_number."' 
					AND punch_value = '".$punch_value."' 
					AND die_value = '".$die_value."'");

                foreach ($kanagatas as $key) {
                	$id_kanagata = $key->id;
                }

                $prod_result->date = $request->get('date');
                $prod_result->pic = $request->get('pic');
                $prod_result->machine = $request->get('mesin');
                $prod_result->save();

                $kanagata = MpKanagataLog::find($id_kanagata);

                $kanagata->date = $request->get('date');
                $kanagata->pic = $request->get('pic');
                $kanagata->machine = $request->get('mesin');
                $kanagata->save();

              $response = array(
                'status' => true,
              );

              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function updateKanagataLifetime(Request $request,$id)
    {
        try{
                $kanagata_lifetime = MpKanagataLog::find($id);
                $kanagata_lifetime->punch_total = $request->get('punch_total');
                $kanagata_lifetime->die_total = $request->get('die_total');
                $kanagata_lifetime->save();

               $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function reset(Request $request)
    {
        try{
        	$kanagata = $request->get('kanagata');
        	$kanagata_number = $request->get('kanagata_number');
        	if ($kanagata == 'Punch') {
        		$kanagata_lifetime = MpKanagataLog::where('punch_number',$kanagata_number)->get();
	        	foreach ($kanagata_lifetime as $key) {
	        		$id_kanagata = $key->id;
	        		$kanagata_lifetime2 = MpKanagataLog::find($id_kanagata);
	                $kanagata_lifetime2->punch_status = 'Close';
	                $kanagata_lifetime2->save();
	        	}
        	}
        	elseif ($kanagata == 'Dies') {
        		$kanagata_lifetime = MpKanagataLog::where('die_number',$kanagata_number)->get();
	        	foreach ($kanagata_lifetime as $key) {
	        		$id_kanagata = $key->id;
	        		$kanagata_lifetime2 = MpKanagataLog::find($id_kanagata);
	                $kanagata_lifetime2->die_status = 'Close';
	                $kanagata_lifetime2->save();
	        	}
        	}
               $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function create_kanagata_lifetime(Request $request)
    {
    	try {
    		$id_user = Auth::id();

    		if ($request->get('part') == 'DIE') {
    			MpKanagataLog::create([
	                'date' => $request->get('date'),
	                'pic' => $request->get('pic'),
	                'product' => $request->get('product'),
	                'machine' => $request->get('machine'),
	                'shift' => $request->get('shift'),
	                'material_number' => $request->get('material_number'),
	                'process' => $request->get('process'),
	                'punch_number' => $request->get('punch_number'),
	                'die_number' => $request->get('die_number'),
	                'start_time' => $request->get('start_time'),
	                'end_time' => $request->get('end_time'),
	                'punch_value' => $request->get('die_value'),
	                'die_value' => $request->get('die_value'),
	                'punch_total' => $request->get('punch_total')+$request->get('die_value'),
	                'die_total' => $request->get('die_total'),
	                'punch_status' => 'Running',
	                'die_status' => 'Running',
	                'created_by' => $id_user
	          ]);

    			MpRecordProd::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'punch_number' => $request->get('punch_number'),
                    'die_number' => $request->get('die_number'),
                    'start_time' => $request->get('start_time'),
                    'end_time' => $request->get('end_time'),
                    'lepas_molding' => '00:00:00',
                    'pasang_molding' => '00:00:00',
                    'process_time' => '00:00:00',
                    'kensa_time' => '00:00:00',
                    'electric_supply_time' => '00:00:00',
                    'data_ok' => $request->get('die_value'),
                    'punch_value' => $request->get('die_value'),
                    'die_value' => $request->get('die_value'),
                    'created_by' => $id_user
                ]);
    		}else{
    			MpKanagataLog::create([
	                'date' => $request->get('date'),
	                'pic' => $request->get('pic'),
	                'product' => $request->get('product'),
	                'machine' => $request->get('machine'),
	                'shift' => $request->get('shift'),
	                'material_number' => $request->get('material_number'),
	                'process' => $request->get('process'),
	                'punch_number' => $request->get('punch_number'),
	                'die_number' => $request->get('die_number'),
	                'start_time' => $request->get('start_time'),
	                'end_time' => $request->get('end_time'),
	                'punch_value' => $request->get('punch_value'),
	                'die_value' => $request->get('punch_value'),
	                'punch_total' => $request->get('punch_total'),
	                'die_total' => $request->get('die_total')+$request->get('punch_value'),
	                'punch_status' => 'Running',
	                'die_status' => 'Running',
	                'created_by' => $id_user
	          ]);
    			MpRecordProd::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'punch_number' => $request->get('punch_number'),
                    'die_number' => $request->get('die_number'),
                    'start_time' => $request->get('start_time'),
                    'end_time' => $request->get('end_time'),
                    'lepas_molding' => '00:00:00',
                    'pasang_molding' => '00:00:00',
                    'process_time' => '00:00:00',
                    'kensa_time' => '00:00:00',
                    'electric_supply_time' => '00:00:00',
                    'data_ok' => $request->get('punch_value'),
                    'punch_value' => $request->get('punch_value'),
                    'die_value' => $request->get('punch_value'),
                    'created_by' => $id_user
                ]);
    		}

		  $response = array(
            'status' => true,
          );
          return Response::json($response);
    	} catch (QueryException $e) {
    		$response = array(
               'status' => false,
               'message' => $e->getMessage(),
            );
            return Response::json($response);
    	}
    }

    public function fetchKanagata(Request $request)
    {
    	try {
    		$date_from = $request->get('date_from');
		      $date_to = $request->get('date_to');
		      $datenow = date('Y-m-d');

		      if($request->get('date_to') == null){
		        if($request->get('date_from') == null){
		          $date = "";
		        }
		        elseif($request->get('date_from') != null){
		          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
		        }
		      }
		      elseif($request->get('date_to') != null){
		        if($request->get('date_from') == null){
		          $date = "where date <= '".$date_to."'";
		        }
		        elseif($request->get('date_from') != null){
		          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
		        }
		      }

		     $kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id,
		     	mp_kanagata_logs.process AS process_detail
			from mp_kanagata_logs
			join employee_syncs on mp_kanagata_logs.pic = employee_syncs.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_kanagata_logs.id desc");

    		$response = array(
               'status' => true,
               'message' => 'Success Get Data',
               'kanagata' => $kanagata_lifetime
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

    public function excelKanagataLastData(Request $request)
    {
    	// try {
		     $kanagata_lifetime = db::select("SELECT
				a.material_number,
				a.material_name,
				a.material_description,
				a.product,
				a.punch_die_number,
				a.part,
			COALESCE(IF
				(
					a.part LIKE '%PUNCH%',(
					SELECT
						punch_total 
					FROM
						mp_kanagata_logs 
					WHERE
						a.punch_die_number = punch_number 
					ORDER BY
						mp_kanagata_logs.id DESC 
						LIMIT 1 
						),(
					SELECT
						die_total 
					FROM
						mp_kanagata_logs 
					WHERE
						a.punch_die_number = die_number 
					ORDER BY
						mp_kanagata_logs.id DESC 
						LIMIT 1 
					)) , 0) as last_data
			FROM
				mp_kanagatas a");

		     $data = array(
				'kanagata_lifetime' => $kanagata_lifetime
			);

		     ob_clean();
			Excel::create('Latest Kanagata Lifetime Report', function($excel) use ($data){
				$excel->sheet('Kanagata Lifetime', function($sheet) use ($data) {
					return $sheet->loadView('press.excel_kanagata_lifetime', $data);
				});
			})->export('xlsx');

		     return view('press.excel_kanagata_lifetime',$data);

			// $response = array(
   //             'status' => true,
   //             'message' => 'Export Excel Success',
   //          );
   //          return Response::json($response);

   //  	} catch (\Exception $e) {
   //  		$response = array(
   //             'status' => false,
   //             'message' => $e->getMessage(),
   //          );
   //          return Response::json($response);
   //  	}
    }
}
