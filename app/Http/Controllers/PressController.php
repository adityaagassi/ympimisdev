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

    public function index(){
		return view('press.index')->with('page', 'Press Machine Production')->with('head', 'Press Machine');
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
		->where('product', '=', $request->get('product'))
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

		$dies_data = '';
		foreach ($dies as $dies) {
            $dies_data .= '<option value="'.$dies->punch_die_number.'">'.$dies->punch_die_number.'</option>';
        }

		$response = array(
			'status' => true,
			'count' => $count,
			'punch' => $punch,
			'dies' => $dies,
			'punch_data' => $punch_data,
			'dies_data' => $dies_data,
		);
		return Response::json($response);
	}

	public function create($product){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		$data = array(
                	'process' => $process,
                	'machine' => $machine);
		return view('press.create_press_data',$data)->with('page', 'Press Machine Production')->with('head', $product);
	}

	function store(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
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
                    'lepas_molding' => $request->get('lepas_molding'),
                    'pasang_molding' => $request->get('pasang_molding'),
                    'process_time' => $request->get('process_time'),
                    'electric_supply_time' => $request->get('electric_supply_time'),
                    'data_ok' => $request->get('data_ok'),
                    'punch_value' => $request->get('punch_value'),
                    'die_value' => $request->get('die_value'),
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
              
                // MpRecordProd::create([
                //     'date' => $request->get('date'),
                //     'pic' => $request->get('pic'),
                //     'product' => $request->get('product'),
                //     'machine' => $request->get('machine'),
                //     'material_number' => $request->get('material_number'),
                //     'process' => $request->get('process'),
                //     'punch_number' => $request->get('punch_number'),
                //     'die_number' => $request->get('die_number'),
                //     'start_time' => $request->get('start_time'),
                //     'end_time' => $request->get('end_time'),
                //     'lepas_molding' => $request->get('lepas_molding'),
                //     'pasang_molding' => $request->get('pasang_molding'),
                //     'process_time' => $request->get('process_time'),
                //     'electric_supply_time' => $request->get('electric_supply_time'),
                //     'data_ok' => $request->get('data_ok'),
                //     'punch_value' => $request->get('punch_value'),
                //     'die_value' => $request->get('die_value'),
                //     'created_by' => $id_user
                // ]);

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

    function store_kanagata(Request $request)
    {
        	try{
        	  $id_user = Auth::id();

				$kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = '".$request->get('process')."' and material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."'");

				$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where process = '".$request->get('process')."' and material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."'");

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

    function store_trouble(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
                MpTroubleLog::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
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
    	$title = 'Machine Press Data Result';
		$title_jp = '';

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

		// $where = "";
		// if($request->get('proses') != null) {
		// 	$prosess = $request->get('proses');
		// 	$proses = "";

		// 	for($x = 0; $x < count($prosess); $x++) {
		// 		$proses = $proses."'".substr($prosess[$x],0,3)."'";
		// 		if($x != count($prosess)-1){
		// 			$proses = $proses.",";
		// 		}
		// 	}
		// 	$where = "and m.origin_group_code in (".$code.") ";
		// }


		$data = db::select("select mp_machines.machine_name, COALESCE(sum(mp_record_prods.data_ok),0)  as actual_shoot, COALESCE(mp_record_prods.date,CURDATE()) as tgl from mp_machines left join mp_record_prods on mp_machines.machine_name = mp_record_prods.machine left join mp_processes on mp_record_prods.process = mp_processes.process_desc where DATE_FORMAT(COALESCE(mp_record_prods.date,CURDATE()),'%Y-%m-%d') = '".$date."' GROUP BY mp_machines.machine_name,mp_record_prods.date");

		$response = array(
			'status' => true,
			'datas' => $data,
			'date' => $date,
		);
		return Response::json($response);
	}
}
