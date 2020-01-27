<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\CodeGenerator;
use App\WorkshopJobOrder;
use App\WorkshopLog;
use App\WorkshopJobOrderLog;
use App\WorkshopMaterial;
use App\WorkshopProcess;
use App\WorkshopOperator;
use App\WorkshopTempProcess;
use App\WorkshopTagAvailability;
use App\WorkshopFlowProcess;
use App\Employee;
use App\EmployeeSync;
use Carbon\Carbon;
use DataTables;
use Response;
use Excel;
use DateTime;
use File;

class WorkshopController extends Controller{

	public function __construct(){
		$this->middleware('auth');

		$workshop_materials = WorkshopMaterial::orderBy('workshop_materials.item_description', 'asc')
		->get();

		$statuses = db::table('processes')->where('processes.remark', '=', 'workshop')
		->orderBy('process_code', 'asc')
		->get();
		
		$employees = EmployeeSync::whereNotNull('section')
		->whereNotNull('group')
		->orderBy('employee_id', 'asc')
		->select('employee_id', 'name', 'section', 'group')->get();
		
		$machines = WorkshopProcess::orderBy('process_name', 'asc')->get();

		$sections = db::select("select DISTINCT department, section, `group` from employee_syncs
			where department is not null
			and section is not null
			and grade_code not like '%L%'
			order by department, section, `group` asc");

		$operators = WorkshopOperator::leftJoin('employee_syncs','employee_syncs.employee_id','=','workshop_operators.operator_id')
		->select('workshop_operators.operator_id', db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as name'))
		->orderBy('workshop_operators.operator_id', 'asc')
		->get();

		$this->material = $workshop_materials;
		$this->status = $statuses;
		$this->employee = $employees;
		$this->operator = $operators;
		$this->section = $sections;
		$this->machine = $machines;
		$this->leader = [
			'PI1108003',
			'PI9903004'
		];
	}

	public function indexWorkload(){
		$title = 'Workshop Operator Workload';
		$title_jp = '';

		return view('workshop.report.workload', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Workshop Operator Workload')->with('head', 'Workshop');	
	}

	public function indexProductivity(){
		$title = 'Workshop Productivity';
		$title_jp = '作業依頼書の実現力';

		return view('workshop.report.productivity', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Workshop Productivity')->with('head', 'Workshop');	
	}

	public function indexWJOMonitoring(){
		$title = 'WJO Monitoring';
		$title_jp = '作業依頼書の監視';

		return view('workshop.report.monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'WJO Monitoring')->with('head', 'Workshop');	
	}

	public function indexDrawing(){
		$title = 'Drawing';
		$title_jp = '??';

		return view('workshop.drawing', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Drawing')->with('head', 'Workshop');
	}

	public function indexWJO(){
		$title = 'WJO Execution';
		$title_jp = '??';

		return view('workshop.wjo', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => Auth::user()->name,
		))->with('page', 'WJO Execution')->with('head', 'Workshop');
	}

	public function indexCreateWJO(){
		$title = 'WJO Form';
		$title_jp = '??';

		$workshop_job_orders = WorkshopJobOrder::where('created_by', '=', Auth::user()->username)
		->select(db::raw('count(if(remark="5", 1, null)) as rejected, count(if(remark="0", 1, null)) as requested, count(if(remark="1", 1, null)) as listed, count(if(remark="2", 1, null)) as approved, count(if(remark="3", 1, null)) as inprogress, count(if(remark="4", 1, null)) as finished'))->get();

		return view('workshop.wjo_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'statuses' => $this->status,
			'employees' => $this->employee,
			'sections' => $this->section,
			'materials' => $this->material,
			'rejected' => $workshop_job_orders[0]->rejected,
			'requested' => $workshop_job_orders[0]->requested,
			'listed' => $workshop_job_orders[0]->listed,
			'approved' => $workshop_job_orders[0]->approved,
			'inprogress' => $workshop_job_orders[0]->inprogress,
			'finished' => $workshop_job_orders[0]->finished,
		))->with('page', 'WJO Form')->with('head', 'Workshop');
	}

	public function indexListWJO(){
		$title = 'Workshop Job Order Lists';
		$title_jp = '??';

		return view('workshop.wjo_list', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'workshop_materials' => $this->material,
			'statuses' => $this->status,
			'employees' => $this->employee,
			'operators' => $this->operator,
			'machines' => $this->machine,
		))->with('page', 'WJO List')->with('head', 'Workshop');	
	}

	public function scanOperator(Request $request){
		$tag = Employee::where('tag', '=', $request->get('employee_id'))->first();
		if(!$tag){
			$response = array(
				'status' => false,
				'message' => 'Tag tidak ditemukan',
			);
			return Response::json($response);
		}

		$employee = EmployeeSync::where('employee_id', '=', $tag->employee_id)->first();
		if(!$employee){
			$response = array(
				'status' => false,
				'message' => 'Tag belum terdaftar',
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Tag Operator ditemukan',
			'employee' => $employee,
		);
		return Response::json($response);
	}

	public function scanLeader(Request $request){
		$tag = Employee::where('tag', '=', $request->get('employee_id'))->first();
		if(!$tag){
			$response = array(
				'status' => false,
				'message' => 'Tag tidak ditemukan'
			);
			return Response::json($response);
		}

		$employee = EmployeeSync::where('employee_id', '=', $tag->employee_id)->first();
		if($employee){
			if(in_array($employee->employee_id, $this->leader)){
				
				$process = WorkshopFlowProcess::where('order_no', '=', $request->get('order_no'))
				->update(['status' => 0]);
				$process = WorkshopFlowProcess::where('order_no', '=', $request->get('order_no'))
				->where('sequence_process', '>=', $request->get('sequence_process'))
				->forceDelete();

				$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
				->where('workshop_logs.order_no', '=', $request->get('order_no'))
				->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name')
				->orderBy('workshop_logs.sequence_process', 'asc')
				->get();

				$wjo = WorkshopJobOrder::leftJoin('workshop_materials', 'workshop_materials.item_number', '=', 'workshop_job_orders.item_number')
				->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_job_orders.operator')
				->where('workshop_job_orders.order_no', '=', $request->get('order_no'))
				->select('workshop_job_orders.order_no',
					'workshop_job_orders.tag',
					'workshop_job_orders.item_number',
					'workshop_job_orders.priority',
					'workshop_job_orders.category',
					'workshop_job_orders.item_name',
					'workshop_job_orders.tag',
					'workshop_job_orders.quantity',
					'workshop_job_orders.material',
					'workshop_job_orders.problem_description',
					'workshop_job_orders.target_date',
					'employee_syncs.name',
					'workshop_materials.file_name')
				->first();

				$temp = WorkshopTempProcess::where('tag', '=', $wjo->tag)->first();
				$started_at = $temp->started_at;

				$response = array(
					'status' => true,
					'wjo' => $wjo,
					'started_at' => $started_at,
					'wjo_log' => $workshop_log,
					'message' => 'Proses berhasil diubah'
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Tag tidak ditemukan'
				);
				return Response::json($response);
			}
		}else{
			$response = array(
				'status' => false,
				'message' => 'Tag belum terdaftar'
			);
			return Response::json($response);
		}
	}

	public function scanTag(Request $request){
		$wjo = WorkshopJobOrder::leftJoin('workshop_materials', 'workshop_materials.item_number', '=', 'workshop_job_orders.item_number')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_job_orders.operator')
		->leftJoin('workshop_tag_availabilities', 'workshop_tag_availabilities.tag', '=', 'workshop_job_orders.tag')
		->where('workshop_job_orders.tag', '=', $request->get('tag'))
		->whereBetween('workshop_job_orders.remark', array(2, 3))
		->select('workshop_job_orders.order_no',
			'workshop_tag_availabilities.remark as tag_remark',
			'workshop_job_orders.item_number',
			'workshop_job_orders.priority',
			'workshop_job_orders.category',
			'workshop_job_orders.item_name',
			'workshop_job_orders.tag',
			'workshop_job_orders.quantity',
			'workshop_job_orders.material',
			'workshop_job_orders.problem_description',
			'workshop_job_orders.target_date',
			db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as name'),
			'workshop_job_orders.attachment',
			'workshop_materials.file_name')
		->first();

		if(!$wjo){
			$response = array(
				'status' => false,
				'message' => 'WJO tidak ditemukan'
			);
			return Response::json($response);
		}

		$listed_time = WorkshopJobOrderLog::where('order_no', '=', $wjo->order_no)
		->where('remark', '=', 1)
		->first();
		$listed_time = $listed_time->created_at;

		$process = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
		->where('workshop_flow_processes.order_no', '=', $wjo->order_no)
		->select('workshop_flow_processes.sequence_process', 'workshop_flow_processes.order_no', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.std_time', 'workshop_flow_processes.status')
		->orderBy('sequence_process', 'asc')
		->get();

		$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
		->where('workshop_logs.order_no', '=', $wjo->order_no)
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name')
		->orderBy('sequence_process', 'asc')
		->get();

		$response = array(
			'status' => true,
			'wjo' => $wjo,
			'listed_time' => $listed_time,
			'flow_process' => $process,
			'wjo_log' => $workshop_log,
			'message' => 'WJO ditemukan'
		);
		return Response::json($response);
		
	}

	public function scanMachine(Request $request){
		$current_machine = WorkshopProcess::where('tag', '=', $request->get('machine_tag'))->first();
		if(!$current_machine){
			$response = array(
				'status' => false,
				'message' => 'Mesin tidak ditemukan'
			);
			return Response::json($response);
		}

		$wjo = WorkshopJobOrder::where('order_no', '=', $request->get('order_no'))->first();

		$temp = WorkshopTempProcess::where('tag', '=', $wjo->tag)->first();
		if(!$temp){
			$temp = new WorkshopTempProcess([
				'tag' => $wjo->tag,
				'started_at' => date('Y-m-d H:i:s'),
			]);
			$temp->save();
		}
		$started_at = $temp->started_at;


		$wjo_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
		->where('order_no', '=', $wjo->order_no)
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_logs.machine_code', 'workshop_processes.machine_name', 'workshop_processes.process_name')
		->orderBy('sequence_process', 'desc')
		->first();

		// if($wjo_log){
		// 	$flow = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
		// 	->where('order_no', '=', $wjo->order_no)
		// 	->where('sequence_process', '=', ($wjo_log->sequence_process + 1))
		// 	->select('workshop_flow_processes.order_no', 'workshop_flow_processes.sequence_process', 'workshop_flow_processes.machine_code', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.status')
		// 	->first();

		// 	if($flow){
		// 		if($flow->process_name != $current_machine->process_name){
		// 			$response = array(
		// 				'status' => false,
		// 				'message' => 'Proses tidak sama dengan sebelumnya',
		// 				'order_no' => $wjo->order_no,
		// 				'sequence_process' => ($wjo_log->sequence_process + 1)
		// 			);
		// 			return Response::json($response);
		// 		}
		// 	}
		// }else{
		// 	$flow = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
		// 	->where('order_no', '=', $wjo->order_no)
		// 	->where('sequence_process', '=', 1)
		// 	->select('workshop_flow_processes.order_no', 'workshop_flow_processes.sequence_process', 'workshop_flow_processes.machine_code', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.status')
		// 	->first();

		// 	if($flow){
		// 		if($flow->process_name != $current_machine->process_name){
		// 			$response = array(
		// 				'status' => false,
		// 				'message' => 'Proses tidak sama dengan sebelumnya',
		// 				'order_no' => $wjo->order_no,
		// 				'sequence_process' => 1						
		// 			);
		// 			return Response::json($response);
		// 		}
		// 	}
		// }
		

		$wjo_remark = WorkshopJobOrder::where('order_no', '=', $wjo->order_no)
		->update([
			'remark' => 3
		]);

		$wjo_log = WorkshopJobOrderLog::where('order_no', $wjo->order_no)
		->where('remark', 3)
		->first();
		if(!$wjo_log){
			$wjo_log = new WorkshopJobOrderLog([
				'order_no' => $wjo->order_no,
				'remark' => 3,
				'created_by' => $request->get('operator_id')
			]);
			$wjo_log->save();	
		}


		$process = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
		->where('workshop_flow_processes.order_no', '=', $wjo->order_no)
		->select('workshop_flow_processes.sequence_process', 'workshop_flow_processes.order_no', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.std_time', 'workshop_flow_processes.status')
		->orderBy('sequence_process', 'asc')
		->get();

		$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
		->where('workshop_logs.order_no', '=', $wjo->order_no)
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name')
		->orderBy('sequence_process', 'asc')
		->get();

		$response = array(
			'status' => true,
			'wjo' => $wjo,
			'started_at' => $started_at,
			'flow_process' => $process,
			'wjo_log' => $workshop_log,
			'current_machine' => $current_machine,
			'message' => 'WJO ditemukan'
		);
		return Response::json($response);

	}


	public function exportListWJO(Request $request){

		$workshop_job_orders = WorkshopJobOrder::leftJoin(db::raw('(select employee_id, name from employee_syncs) as approver'), 'approver.employee_id', '=', 'workshop_job_orders.approved_by')
		->leftJoin(db::raw('(select employee_id, name from employee_syncs) as pic'), 'pic.employee_id', '=', 'workshop_job_orders.operator')
		->leftJoin(db::raw('(SELECT process_code, process_name FROM processes where remark = "workshop") as processes'), 'processes.process_code', '=', 'workshop_job_orders.remark');

		if(strlen($request->get('reqFrom')) > 0 ){
			$reqFrom = date('Y-m-d', strtotime($request->get('reqFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '>=', $reqFrom);
		}
		if(strlen($request->get('reqTo')) > 0 ){
			$reqTo = date('Y-m-d', strtotime($request->get('reqTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '<=', $reqTo);
		}
		if(strlen($request->get('targetFrom')) > 0 ){
			$targetFrom = date('Y-m-d', strtotime($request->get('targetFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '>=', $targetFrom);
		}
		if(strlen($request->get('targetTo')) > 0 ){
			$targetTo = date('Y-m-d', strtotime($request->get('targetTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_ordersworkshop_job_orders.created_at)'), '<=', $targetTo);
		}
		if(strlen($request->get('finFrom')) > 0 ){
			$finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_ordersworkshop_job_orders.created_at)'), '>=', $finFrom);
		}
		if(strlen($request->get('finTo')) > 0 ){
			$finTo = date('Y-m-d', strtotime($request->get('finTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '<=', $finTo);
		}
		if(strlen($request->get('orderNo')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.order_no', '=', $request->get('orderNo'));
		}
		if(strlen($request->get('sub_section')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.sub_section', '=', $request->get('sub_section'));
		}
		if(strlen($request->get('priority')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.priority', '=', $request->get('priority'));
		}
		if(strlen($request->get('workType')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.type', '=', $request->get('workType'));
		}
		if($request->get('rawMaterial') != null){
			$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.material', $request->get('rawMaterial'));
		}
		if($request->get('material') != null){
			$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.item_number', $request->get('material'));
		}
		if(strlen($request->get('pic')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.operator', '=', $request->get('pic'));
		}
		if(strlen($request->get('remark')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.remark', '=', $request->get('remark'));
		}

		$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.order_no', 'asc')
		->select(
			'workshop_job_orders.order_no',
			db::raw('date(workshop_job_orders.created_at) as created_at'),
			'workshop_job_orders.sub_section',
			'workshop_job_orders.item_number',
			'workshop_job_orders.item_name',
			'workshop_job_orders.quantity',
			'workshop_job_orders.priority',
			'workshop_job_orders.type',
			'workshop_job_orders.material',
			'workshop_job_orders.problem_description',
			'processes.process_name',
			'workshop_job_orders.target_date',
			'workshop_job_orders.difficulty',
			'workshop_job_orders.main_process',
			'workshop_job_orders.rating',
			'workshop_job_orders.note',
			'workshop_job_orders.item_number',
			'approver.name as approver_name',
			'pic.name as pic_name'
		)
		->get();

		$data = array(
			'workshop_job_orders' => $workshop_job_orders
		);
		ob_clean();
		Excel::create('List WJO', function($excel) use ($data){
			$excel->sheet('WJO', function($sheet) use ($data) {
				return $sheet->loadView('workshop.wjo_excel', $data);
			});
		})->export('xlsx');
	}

	public function editDrawing(Request $request){
		$id = $request->get('edit_id');
		$item_number = $request->get('edit_item_number');
		$item_description = $request->get('edit_item_description');

		$drawing = WorkshopMaterial::where('id', $id)->first();
		if($drawing->file_name){
			File::delete('drawing/'.$drawing->file_name);
		}

		$file = $request->file('edit_file');
		$file_name = $item_number.'.'.$file->getClientOriginalExtension();
		$file->move(public_path('drawing'), $file_name);

		try {
			$drawing = WorkshopMaterial::where('id', $id)
			->update([
				'item_number' => $item_number,
				'item_description' => $item_description,
				'file_name' => $file_name,
				'remark' => 'drawing',
				'created_by' => Auth::id(),
			]);

			$response = array(
				'status' => true,
				'message' => 'Drawing berhasil diedit',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function createDrawing(Request $request){

		$item_number = $request->get('item_number');
		$item_description = $request->get('item_description');

		$file = $request->file('upload_file');
		$file_name = $item_number.'.'.$file->getClientOriginalExtension();
		$file->move(public_path('drawing'), $file_name);


		$drawing = new WorkshopMaterial([
			'item_number' => $item_number,
			'item_description' => $item_description,
			'file_name' => $file_name,
			'remark' => 'drawing',
			'created_by' => Auth::id(),
		]);

		try {
			DB::transaction(function() use ($drawing){
				$drawing->save();
			});

			$response = array(
				'status' => true,
				'message' => 'Drawing berhasil ditambahkan',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function createProcessLog(Request $request){
		$order_no = $request->get('order_no');
		$tag = $request->get('tag');
		$machine_code = $request->get('machine_code');
		$operator_id = $request->get('operator_id');
		$started_at = $request->get('started_at');

		$wjo = WorkshopJobOrder::where('order_no', '=', $order_no)->first();
		$wjo->remark = 3;

		$log = WorkshopLog::where('order_no', '=', $order_no)->orderBy('sequence_process', 'desc')->first();
		if($log){
			$workshop_log = new WorkshopLog([
				'order_no' => $order_no,
				'sequence_process' => $log->sequence_process + 1,
				'machine_code' => $machine_code,
				'operator_id' => $operator_id,
				'started_at' => $started_at,
			]);
		}else{
			$workshop_log = new WorkshopLog([
				'order_no' => $order_no,
				'sequence_process' => 1,
				'machine_code' => $machine_code,
				'operator_id' => $operator_id,
				'started_at' => $started_at,
			]);
		}

		$temp = WorkshopTempProcess::where('tag', '=', $tag)->first();

		try {

			$started_at = new DateTime($temp->started_at);

			$now = new DateTime("now");
			$std_time = $now->getTimestamp() - $started_at->getTimestamp();

			$process = WorkshopFlowProcess::where('order_no', '=', $wjo->order_no)
			->orderBy('sequence_process', 'desc')
			->first();

			if($process){
				if($process->status == 0){
					if($log){
						if(($log->sequence_process + 1) > $process->sequence_process){
							$flow_process = new WorkshopFlowProcess([
								'order_no' => $wjo->order_no,
								'sequence_process' => $process->sequence_process + 1,
								'machine_code' => $machine_code,
								'status' => 0,
								'std_time' => $std_time,
								'created_by' => $operator_id,
							]);
							$flow_process->save();	
						}
					}				

				}
			}else{
				$flow_process = new WorkshopFlowProcess([
					'order_no' => $wjo->order_no,
					'sequence_process' => 1,
					'machine_code' => $machine_code,
					'status' => 0,
					'std_time' => $std_time,
					'created_by' => $operator_id,
				]);
				$flow_process->save();
			}

			

			DB::transaction(function() use ($wjo, $workshop_log, $temp){
				$wjo->save();
				$workshop_log->save();
				$temp->delete();
			});


			$response = array(
				'status' => true,
				'message' => 'Proses berhasil disimpan',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}	

	public function createWJO(Request $request){
		$date = date('Y-m-d');
		$prefix_now = 'WJO'.date("y").date("m").date("d");
		$code_generator = CodeGenerator::where('note','=','wjo')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$sub_section = $request->get('sub_section');
		$item_name = $request->get('item_name');
		$category = $request->get('category');
		$quantity = $request->get('quantity');
		$priority = $request->get('priority');
		$type = $request->get('type');
		$material = $request->get('material');
		$problem_desc = $request->get('problem_desc');

		if($material == 'Lainnya'){
			$material == $request->get('material-other');
		}

		$remark;
		if($priority == 'Normal'){
			$remark = 1;
		}else{
			$remark = 0;
		}

		$request_date;
		if($priority == 'Normal'){
			if($category == 'Equipment'){
				$request_date = date('Y-m-d', strtotime($date. ' + 7 days'));
			}else{
				$request_date = date('Y-m-d', strtotime($date. ' + 14 days'));
			}
		}else{
			$request_date = $request->get('request_date');
		}


		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$order_no = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		$file_name;
		if($request->hasFile('upload_file')) {
			$file = $request->file('upload_file');
			$file_name = $order_no.'.'.$file->getClientOriginalExtension();
			$file->move(public_path('workshop'), $file_name);
		}else{
			$file_name = null;
		}

		$wjo = new WorkshopJobOrder([
			'order_no' => $order_no,
			'sub_section' => $sub_section,
			'item_name' => $item_name,
			'category' => $category,
			'quantity' => $quantity,
			'target_date' => $request_date,
			'priority' => $priority,
			'type' => $type,
			'material' => $material,
			'problem_description' => $problem_desc,
			'remark' => $remark,
			'attachment' => $file_name,
			'created_by' => Auth::user()->username,
		]);

		$wjo_log = new WorkshopJobOrderLog([
			'order_no' => $order_no,
			'remark' => $remark,
			'created_by' => Auth::user()->username,
		]);


		try {
			DB::transaction(function() use ($wjo, $wjo_log){
				$wjo->save();
				$wjo_log->save();
			});	

			if($priority == 'Urgent'){
				$data = db::select("select w.*, u.`name` from workshop_job_orders w
					left join employee_syncs u on w.created_by = u.employee_id
					where order_no = '".$order_no."'");
				
				Mail::to('susilo.basri@music.yamaha.com')
				->cc('aditya.agassi@music.yamaha.com')
				->send(new SendEmail($data, 'urgent_wjo'));
			}

			$response = array(
				'status' => true,
				'message' => 'Pembuatan WJO berhasil',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function checkTag(Request $request){
		$tag = $request->get('tag');

		$tag_availability = WorkshopTagAvailability::where('tag', '=', $tag)
		->first();

		if($tag_availability){
			if($tag_availability->status == 1){
				$response = array(
					'status' => true,
					'message' => 'WJO Tag tersedia',
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'WJO Tag masih digunakan',
				);
				return Response::json($response);
			}
		}else{
			$response = array(
				'status' => false,
				'message' => 'Tag bukan untuk WJO',
			);
			return Response::json($response);
		}
	}

	public function checkCloseTag(Request $request){
		$tag = $request->get('tag');

		$wjo = WorkshopJobOrder::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_job_orders.operator')
		->where('workshop_job_orders.tag', '=', $tag)
		->where('workshop_job_orders.remark', '=', 3)
		->select('workshop_job_orders.order_no', 'workshop_job_orders.target_date', 'workshop_job_orders.priority', 'workshop_job_orders.sub_section', 'employee_syncs.name', 'workshop_job_orders.difficulty', 'workshop_job_orders.category', 'workshop_job_orders.item_name', 'workshop_job_orders.quantity', 'workshop_job_orders.material')
		->first();

		if($wjo){
			$response = array(
				'status' => true,
				'wjo' => $wjo,
				'message' => 'WJO ditemukan',
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'message' => 'WJO tidak sedang dikerjakan',
			);
			return Response::json($response);
		}		
	}

	public function updateWJO(Request $request){
		$date = date('Y-m-d');

		$order_no = $request->get('order_no');
		$item_name = $request->get('item_name');
		$quantity = $request->get('quantity');
		$material = $request->get('material');
		$problem_description = $request->get('problem_description');

		$tag = $request->get('tag');
		$target_date = $request->get('target_date');
		$category = $request->get('category');
		$item_number = $request->get('item_number');
		$pic = $request->get('pic');
		$difficulty = $request->get('difficulty');

		$wjo = WorkshopJobOrder::where('order_no', '=', $order_no)->first();
		$wjo->item_name = $item_name;
		$wjo->quantity = $quantity;
		$wjo->material = $material;
		$wjo->problem_description = $problem_description;
		$wjo->tag = $tag;		
		$wjo->target_date = $target_date;
		$wjo->category = $category;
		$wjo->item_number = $item_number;		
		$wjo->operator = $pic;
		$wjo->difficulty = $difficulty;
		$wjo->approved_by = Auth::user()->username;
		$wjo->remark = 2;

		$wjo_log = new WorkshopJobOrderLog([
			'order_no' => $order_no,
			'remark' => 2,
			'created_by' => Auth::user()->username,
		]);


		try{
			$tag_availability = WorkshopTagAvailability::where('tag', '=', $tag)->first();
			$tag_availability->status = 0;


			$flow_process = $request->get('flow_process');
			$flow_processes = [];
			for($x = 0; $x < count($flow_process); $x++) {
				$flow_processes = new WorkshopFlowProcess([
					'order_no' => $order_no,
					'sequence_process' => $flow_process[$x]['sequence_process'],
					'machine_code' => $flow_process[$x]['machine_code'],
					'status' => 1,
					'std_time' => ($flow_process[$x]['std_time'] * 60),
					'created_by' => Auth::user()->username,
				]);
				$flow_processes->save();
			}

			DB::transaction(function() use ($wjo, $wjo_log, $tag_availability){
				$wjo->save();
				$wjo_log->save();
				$tag_availability->save();
			});		

			$response = array(
				'status' => true,
				'message' => 'Penugasan WJO berhasil',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function rejectWJO(Request $request){

		$reject_reason = $request->get('reason');
		$order_no = $request->get('order_no');

		$wjo = WorkshopJobOrder::where('order_no', '=', $order_no)->first();
		$wjo->approved_by = Auth::user()->username;
		$wjo->remark = 5;
		$wjo->reject_reason = $reject_reason;

		$wjo_log = new WorkshopJobOrderLog([
			'order_no' => $order_no,
			'remark' => 5,
			'created_by' => Auth::user()->username,
		]);

		try{
			if($wjo->tag){
				$tag_availability = WorkshopTagAvailability::where('tag', '=', $tag)->first();
				$tag_availability->status = 1;
				$tag_availability->save();
			}

			DB::transaction(function() use ($wjo, $wjo_log){
				$wjo->save();
				$wjo_log->save();
			});		

			$response = array(
				'status' => true,
				'message' => 'Reject WJO berhasil',
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function closeWJO(Request $request){

		$tag = $request->get('tag');

		$wjo = WorkshopJobOrder::where('tag', '=', $tag)
		->where('remark', '=', 3)
		->first();

		if($wjo){
			try{
				$wjo->remark = 4;
				$wjo->finish_date = date('Y-m-d');

				$wjo_log = new WorkshopJobOrderLog([
					'order_no' => $wjo->order_no,
					'remark' => 4,
					'created_by' => Auth::user()->username,
				]);

				$tag_availability = WorkshopTagAvailability::where('tag', '=', $tag)->first();
				$tag_availability->status = 1;

				$flow_process = WorkshopFlowProcess::where('order_no', '=', $wjo->order_no);

				DB::transaction(function() use ($wjo, $wjo_log, $flow_process, $tag_availability){
					$wjo->save();
					$wjo_log->save();
					$tag_availability->save();
					$flow_process->update(['status' => 1]);
				});		

				$response = array(
					'status' => true,
					'message' => 'Close WJO berhasil',
				);
				return Response::json($response);
			} catch (Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}else{
			$response = array(
				'status' => false,
				'message' => 'WJO tidak sedang dikerjakan',
			);
			return Response::json($response);
		}
	}

	public function downloadAttachment(Request $request, $id){
		$name = $request->get('file');

		if($id == 'attachment'){
			$path = '/workshop/' . $name;			
		}elseif($id == 'drawing'){
			$path = '/drawing/' . $name;	
		}
		$file_path = asset($path);

		$response = array(
			'status' => true,
			'file_path' => $file_path,
		);
		return Response::json($response); 
	}

	public function fetchWorkload(){

	}

	public function fetchDrawing(){
		$drawing = WorkshopMaterial::leftJOin('users', 'users.id', '=', 'workshop_materials.created_by')
		->where('workshop_materials.remark', 'drawing')
		->select('workshop_materials.item_number', 'workshop_materials.item_description', 'users.name', 'workshop_materials.created_at', 'workshop_materials.file_name')
		->get();

		$response = array(
			'status' => true,
			'drawing' => $drawing,
		);
		return Response::json($response);
	}

	public function fetchListWJO(Request $request){
		$workshop_job_orders = WorkshopJobOrder::leftJoin(db::raw('(select employee_id, name from employee_syncs) as approver'), 'approver.employee_id', '=', 'workshop_job_orders.approved_by')
		->leftJoin(db::raw('(select employee_id, name from employee_syncs) as pic'), 'pic.employee_id', '=', 'workshop_job_orders.operator')
		->leftJoin(db::raw('(select employee_id, name from employee_syncs) as requester'), 'requester.employee_id', '=', 'workshop_job_orders.created_by')
		->leftJoin(db::raw('(SELECT process_code, process_name FROM processes where remark = "workshop") as processes'), 'processes.process_code', '=', 'workshop_job_orders.remark')
		->leftJoin('workshop_tag_availabilities', 'workshop_tag_availabilities.tag', '=', 'workshop_job_orders.tag');

		if(strlen($request->get('order')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.order_no', 'desc');
		}else{
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.priority', 'desc');
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.order_no', 'asc');
		}


		if(strlen($request->get('reqFrom')) > 0 ){
			$reqFrom = date('Y-m-d', strtotime($request->get('reqFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '>=', $reqFrom);
		}
		if(strlen($request->get('reqTo')) > 0 ){
			$reqTo = date('Y-m-d', strtotime($request->get('reqTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '<=', $reqTo);
		}
		if(strlen($request->get('targetFrom')) > 0 ){
			$targetFrom = date('Y-m-d', strtotime($request->get('targetFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '>=', $targetFrom);
		}
		if(strlen($request->get('targetTo')) > 0 ){
			$targetTo = date('Y-m-d', strtotime($request->get('targetTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_ordersworkshop_job_orders.created_at)'), '<=', $targetTo);
		}
		if(strlen($request->get('finFrom')) > 0 ){
			$finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_ordersworkshop_job_orders.created_at)'), '>=', $finFrom);
		}
		if(strlen($request->get('finTo')) > 0 ){
			$finTo = date('Y-m-d', strtotime($request->get('finTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '<=', $finTo);
		}
		if(strlen($request->get('orderNo')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.order_no', '=', $request->get('orderNo'));
		}
		if(strlen($request->get('sub_section')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.sub_section', '=', $request->get('sub_section'));
		}
		if(strlen($request->get('priority')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.priority', '=', $request->get('priority'));
		}
		if(strlen($request->get('workType')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.type', '=', $request->get('workType'));
		}
		if($request->get('rawMaterial') != null){
			$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.material', $request->get('rawMaterial'));
		}
		if($request->get('material') != null){
			$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.item_number', $request->get('material'));
		}
		if(strlen($request->get('pic')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.operator', '=', $request->get('pic'));
		}
		if(strlen($request->get('remark')) > 0){
			if($request->get('remark') != 'all'){
				$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.remark', '=', $request->get('remark'));
			}
		}else{
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.remark', '=', 1);
		}
		if(strlen($request->get('approvedBy')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.approved_by', '=', $request->get('approvedBy'));
		}
		if(strlen($request->get('username')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.created_by', '=', $request->get('username'));
		}

		$workshop_job_orders = $workshop_job_orders
		->select('workshop_job_orders.id',
			'workshop_job_orders.order_no',
			'workshop_tag_availabilities.remark as tag',
			'workshop_job_orders.created_at',
			db::raw('concat(SPLIT_STRING(requester.name, " ", 1)," ",SPLIT_STRING(requester.name, " ", 2)) as requester'),
			'workshop_job_orders.sub_section',
			'workshop_job_orders.remark',
			'workshop_job_orders.type',
			'approver.name as approver',
			'workshop_job_orders.item_name',
			'workshop_job_orders.material',
			'workshop_job_orders.quantity',
			db::raw('concat(SPLIT_STRING(pic.name, " ", 1), " ", SPLIT_STRING(pic.name, " ", 2)) as pic'),
			'workshop_job_orders.difficulty',
			'workshop_job_orders.priority',
			'workshop_job_orders.target_date',
			'workshop_job_orders.finish_date',
			'processes.process_name',
			'workshop_job_orders.attachment',
			'workshop_job_orders.item_number',
			'workshop_job_orders.remark');

		$workshop_job_orders = $workshop_job_orders->get();

		$response = array(
			'status' => true,
			'tableData' => $workshop_job_orders,
		);
		return Response::json($response);
	}

	public function fetchAssignForm(Request $request){
		$wjo = WorkshopJobOrder::where('order_no', '=', $request->get('order_no'))->first();

		$response = array(
			'status' => true,
			'wjo' => $wjo,
		);
		return Response::json($response);
	}

	public function fetchEditDrawing(Request $request){
		$drawing = WorkshopMaterial::where('item_number', $request->get('item_number'))->first();

		$response = array(
			'status' => true,
			'drawing' => $drawing,
		);
		return Response::json($response);
	}

	public function fetchWJOMonitoring(Request $request){
		$datefrom = date("Y-m-d",  strtotime('-30 days'));
		$dateto = date("Y-m-d");

		$last = WorkshopJobOrder::where('remark', '<', 4)
		->orderBy('created_at', 'asc')
		->select(db::raw('date(created_at) as created_at'))
		->first();

		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}else{
			if($last){
				$created_at = date_create($last->created_at);
				$now = date_create(date('Y-m-d'));
				$interval = $now->diff($created_at);
				$diff = $interval->format('%a%');

				if($diff > 30){
					$datefrom = date('Y-m-d', strtotime($last->created_at));
				}
			}
		}

		if(strlen($request->get('dateto')) > 0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}

		$wjo = db::select("select date.week_date, coalesce(list.qty, 0) as list, coalesce(progress.qty, 0) as progress, coalesce(finish.qty, 0) as finish, coalesce(reject.qty, 0) as reject from
			(select week_date from weekly_calendars
			where date(week_date) >= '".$datefrom."'
			and date(week_date) <= '".$dateto."'	) date
			left join
			(select date(created_at) as date, count(order_no) as qty from workshop_job_orders
			where date(created_at) >= '".$datefrom."'
			and date(created_at) <= '".$dateto."'
			and remark < 3
			group by date(created_at)) list
			on date.week_date = list.date
			left join
			(select date(created_at) as date, count(order_no) as qty from workshop_job_orders
			where date(created_at) >= '".$datefrom."'
			and date(created_at) <= '".$dateto."'
			and remark = 3
			group by date(created_at)) progress
			on date.week_date = progress.date
			left join
			(select date(created_at) as date, count(order_no) as qty from workshop_job_orders
			where date(created_at) >= '".$datefrom."'
			and date(created_at) <= '".$dateto."'
			and remark = 4
			group by date(created_at)) finish
			on date.week_date = finish.date
			left join
			(select date(created_at) as date, count(order_no) as qty from workshop_job_orders
			where date(created_at) >= '".$datefrom."'
			and date(created_at) <= '".$dateto."'
			and remark = 5
			group by date(created_at)) reject
			on date.week_date = reject.date
			order by week_date asc");

		$progress = db::select("select wjo.order_no, wjo.priority, wjo.item_name, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, coalesce(date(requested.created_at), date(wjo.created_at)) as requested, date(listed.created_at) as listed, date(approved.created_at) as approved, date(progress.created_at) as progress, target_date, step.std, step.actual from workshop_job_orders wjo
			left join (select * from workshop_job_order_logs where remark = 0) as requested
			on requested.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 1) as listed
			on listed.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 2) as approved
			on approved.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 2) as progress
			on progress.order_no = wjo.order_no
			left join employee_syncs e on wjo.operator = e.employee_id
			left join
			(select wjo.order_no, COALESCE(flow.std,0) as std, COALESCE(log.actual,0) as actual from workshop_job_orders wjo
			left join
			(select flow.order_no, count(flow.order_no) as std from workshop_flow_processes flow
			left join workshop_job_orders wjo
			on flow.order_no = wjo.order_no
			where wjo.remark < 4
			group by flow.order_no) flow
			on flow.order_no = wjo.order_no 
			left join
			(select log.order_no, count(log.order_no) as actual from workshop_logs log
			left join workshop_job_orders wjo
			on log.order_no = wjo.order_no
			where wjo.remark < 4
			group by log.order_no) log
			on log.order_no = wjo.order_no
			where wjo.remark < 4) step
			on step.order_no = wjo.order_no
			where date(wjo.created_at) >= '".$datefrom."'
			and date(wjo.created_at) <= '".$dateto."'
			and wjo.remark = 3
			order by wjo.priority desc, wjo.order_no asc");

		$response = array(
			'status' => true,
			'wjo' => $wjo,
			'progress' => $progress,
		);
		return Response::json($response);
	}


	public function fetchProductivity(Request $request){
		$date = date("Y-m-d");

		if(strlen($request->get('date')) > 0){
			$date = date('Y-m-d', strtotime($request->get('date')));
		}


		$machine = db::select("select m.machine_name, COALESCE(CEILING(t.time/60),0) as time from workshop_processes m
			left join
			(select p.machine_name, sum(timestampdiff(SECOND, l.started_at, l.created_at)) as time from workshop_logs l
			left join workshop_processes p on p.machine_code = l.machine_code
			where date(l.started_at) = '".$date."'
			group by p.machine_name) t
			on m.machine_name = t.machine_name
			order by time desc, machine_name asc");

		$operator = db::select("select operator.`name`, COALESCE(CEILING(time.time/60),0) as time from
			(select op.operator_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from workshop_operators op
			left join employee_syncs e on op.operator_id = e.employee_id) operator
			left join
			(select operator_id, sum(timestampdiff(SECOND, started_at, created_at)) as time from workshop_logs
			where date(started_at) = '".$date."'
			group by operator_id) time
			on operator.operator_id = time.operator_id
			order by time desc, `name` asc");

		$response = array(
			'status' => true,
			'machines' => $machine,
			'operators' => $operator,
			'date' => $date,
		);
		return Response::json($response);
	}

	public function fetchOperatorDetail(Request $request){
		$date = date('Y-m-d', strtotime($request->get('date')));
		$name = $request->get('name');

		$detail = db::select("select concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, p.machine_name, p.process_name, l.started_at, l.created_at  from workshop_logs l
			left join employee_syncs e on e.employee_id = l.operator_id
			left join workshop_processes p on p.machine_code = l.machine_code
			where e.`group` = 'Workshop'
			and e.`name` like '%".$name."%'
			and date(l.started_at) = '".$date."'
			order by l.started_at asc");

		$response = array(
			'status' => true,
			'detail' => $detail,
			'date' => $date
		);
		return Response::json($response);
	}

	public function fetchMachineDetail(Request $request){
		$date = date('Y-m-d', strtotime($request->get('date')));
		$name = $request->get('name');

		$detail = db::select("select p.machine_name, p.process_name, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, l.started_at, l.created_at from workshop_logs l
			left join employee_syncs e on e.employee_id = l.operator_id
			left join workshop_processes p on p.machine_code = l.machine_code
			where p.machine_name like '%".$name."%'
			and date(l.started_at) = '".$date."'
			order by l.started_at asc");

		$response = array(
			'status' => true,
			'detail' => $detail,
			'date' => $date
		);
		return Response::json($response);
	}



}
