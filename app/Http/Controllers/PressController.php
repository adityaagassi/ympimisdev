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

class PressController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
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
		->select('mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description', 'mp_kanagatas.id')
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

		$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = '".$request->get('process')."' and material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."'");
		
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

		$kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = '".$request->get('process')."' and material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."'");
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

               $kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = 'Forging' and material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."'");

				$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = 'Forging' and material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."'");

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
	                'process' => 'Forging',
	                'punch_number' => $request->get('punch_number'),
	                'die_number' => $request->get('die_number'),
	                'start_time' => $request->get('start_time'),
	                'end_time' => $request->get('end_time'),
	                'punch_value' => $request->get('punch_value'),
	                'die_value' => $request->get('die_value'),
	                'punch_total' => $total_punch,
	                'die_total' => $total_die,
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

		$operator = db::select("select name, sum(data_ok) as actual_shot, CURDATE() as date, sum(waktu) as waktu_total from 
			(
			select employees.name, 0 as data_ok, CURDATE(), 0 as waktu from employee_groups join employees on employees.employee_id = employee_groups.employee_id where location='Press'
			UNION ALL 
			select employees.name, data_ok, mp_record_prods.date, ROUND(TIME_TO_SEC(process_time) / 60,1) as waktu from employee_groups LEFT JOIN mp_record_prods on employee_groups.employee_id = mp_record_prods.pic join employees on employees.employee_id = employee_groups.employee_id and location='Press' and mp_record_prods.date = '".$date."'
			) as aw GROUP BY name");


		$response = array(
			'status' => true,
			'datas' => $data,
			'date' => $date,
			'operator' => $operator
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
		return view('press.report_press_trouble',$data)->with('page', 'Press Machine Trouble Report')->with('title_jp', "??");
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


		$prod_result = db::select("select *
			from mp_record_prods
			join employee_groups on employee_groups.employee_id = mp_record_prods.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_record_prods.material_number= mp_materials.material_number
			ORDER BY mp_record_prods.id desc");

		$data = array(
                	'process' => $process,
                	'prod_result' => $prod_result,
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


		$prod_result = db::select("select *
			from mp_record_prods
			join employee_groups on employee_groups.employee_id = mp_record_prods.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_record_prods.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_record_prods.id desc");

		$data = array(
                	'process' => $process,
                	'prod_result' => $prod_result,
                	'machine' => $machine);
		return view('press.report_prod_result',$data)->with('page', 'Press Machine Production Result')->with('title_jp', "??");
	}

	public function report_kanagata_lifetime(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id
			from mp_kanagata_logs
			join employee_groups on employee_groups.employee_id = mp_kanagata_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			ORDER BY mp_kanagata_logs.id desc");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	'kanagata_lifetime' => $kanagata_lifetime,
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

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id
			from mp_kanagata_logs
			join employee_groups on employee_groups.employee_id = mp_kanagata_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_kanagata_logs.id desc");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	'kanagata_lifetime' => $kanagata_lifetime,
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

    function update(Request $request,$id)
    {
        try{
                $kanagata_lifetime = MpKanagataLog::find($id);
                $kanagata_lifetime->punch_total = $request->get('punch_total');
                $kanagata_lifetime->die_total = $request->get('die_total');
                $kanagata_lifetime->save();

            // return redirect('index/interview/details/'.$interview_id)
            //   ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
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
}
