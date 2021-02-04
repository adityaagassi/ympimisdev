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
use App\JigPartStock;
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
		
		$machines = WorkshopProcess::where('process_name', '<>', 'LAIN-LAIN')
		->orderBy('process_name', 'asc')
		->get();

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
			'PI9903004',
			'PI0109001',
			'PI1908024'
		];
	}

	public function indexWorkload(){
		$title = 'Workshop Workload';
		$title_jp = 'ワークショップの作業内容';

		return view('workshop.report.workload', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Workshop Operator Workload')->with('head', 'Workshop');	
	}

	public function indexOperatorload(){
		$title = 'Workshop Operator Work Schedule';
		$title_jp = '??';

		return view('workshop.report.operatorload', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Workshop Operator Work Schedule')->with('head', 'Workshop');
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
		$title_jp = '図面';

		return view('workshop.drawing', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Drawing')->with('head', 'Workshop');
	}

	public function indexWJO(){
		$title = 'WJO Execution';
		$title_jp = '作業依頼書の処理';

		return view('workshop.wjo', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => Auth::user()->name,
		))->with('page', 'WJO Execution')->with('head', 'Workshop');
	}

	public function indexCreateWJO(){
		$title = 'WJO Form';
		$title_jp = '作業依頼書';

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position')->first();

		$workshop_job_orders = WorkshopJobOrder::where('created_by', '=', Auth::user()->username)
		->select(db::raw('count(if(remark="5", 1, null)) as rejected, count(if(remark="0", 1, null)) as requested, count(if(remark="1", 1, null)) as listed, count(if(remark="2", 1, null)) as approved, count(if(remark="3", 1, null)) as inprogress, count(if(remark="4", 1, null)) as finished, count(if(remark="6", 1, null)) as canceled'))->get();

		return view('workshop.wjo_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'statuses' => $this->status,
			'employees' => $this->employee,
			'sections' => $this->section,
			'materials' => $this->material,
			'employee' => $emp,
			'rejected' => $workshop_job_orders[0]->rejected,
			'requested' => $workshop_job_orders[0]->requested,
			'listed' => $workshop_job_orders[0]->listed,
			'approved' => $workshop_job_orders[0]->approved,
			'inprogress' => $workshop_job_orders[0]->inprogress,
			'finished' => $workshop_job_orders[0]->finished,
			'canceled' => $workshop_job_orders[0]->canceled,
		))->with('page', 'WJO Form')->with('head', 'Workshop');
	}

	public function indexListWJO(){
		$title = 'Workshop Job Order Lists';
		$title_jp = '作業依頼書一覧';

		$process = WorkshopProcess::select('process_name','machine_name','machine_code')
		->groupBy('process_name','machine_name','machine_code')
		->get();

		$requester = WorkshopJobOrder::leftJoin("employees", "employees.employee_id", "=", "workshop_job_orders.created_by")
		->select("employees.name","employees.employee_id")
		->groupBy("employees.name","employees.employee_id")
		->orderBy("employees.employee_id")
		->get();

		return view('workshop.wjo_list', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'workshop_materials' => $this->material,
			'statuses' => $this->status,
			'employees' => $this->employee,
			'requesters' => $requester,
			'operators' => $this->operator,
			'machines' => $this->machine,
			'processes' => $process,
		))->with('page', 'WJO List')->with('head', 'Workshop');	
	}

	public function indexJobHistory()
	{
		$title = 'Workshop Job Histories';
		$title_jp = '?';

		$processes = WorkshopProcess::groupBy('process_name')->orderBy('process_name','asc')->select('process_name')->get();

		return view('workshop.report.job_history', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'process' => $processes
		))->with('page', 'WJO History')->with('head', 'Workshop');	
	}

	public function indexWJOReceipt()
	{
		$title = 'Workshop Receipt';
		$title_jp = '?';

		$bagian = EmployeeSync::select('section')->whereNotNull('section')->groupBy('section')->get();

		return view('workshop.wjo_receipt', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'bagian' => $bagian
		))->with('page', 'WJO Receipt')->with('head', 'Workshop');	
	}

	public function editWJO(Request $request){

		$sub_section = $request->get("sub_section_edit");
		$priority = $request->get("priority_edit");
		$type = $request->get("type_edit");
		$category = $request->get("category_edit");
		$item_name = $request->get("item_name_edit");
		$drawing_name = $request->get("drawing_name_edit");
		$item_number = $request->get("item_number_edit");
		$part_number = $request->get("part_number_edit");
		$quantity = $request->get("quantity_edit");

		$material = '';
		if($request->get("material") != 'LAINNYA'){
			$material = $request->get("material_edit");
		}else{
			$material = $request->get("material-other_edit");
		}

		$problem_description = $request->get("problem_desc_edit");
		$target_date = $request->get("request_date_edit");


		$wjo = WorkshopJobOrder::find($request->get("id_edit"));
		$wjo->sub_section = $sub_section;
		$wjo->priority = $priority;
		$wjo->type = $type;
		$wjo->category = $category;
		$wjo->item_name = $item_name;
		$wjo->drawing_name = $drawing_name;
		$wjo->item_number = $item_number;
		$wjo->part_number = $part_number;
		$wjo->quantity = $quantity;
		$wjo->material = $material;
		$wjo->problem_description = $problem_description;
		$wjo->target_date = $target_date;

		if($request->hasFile("upload_file_edit")) {
			if($wjo->attachment != null){
				File::delete('workshop/'. $wjo->attachment);
			}

			$file = $request->file("upload_file_edit");
			$file_name = $wjo->order_no.'.'.$file->getClientOriginalExtension();
			$file->move(public_path('workshop'), $file_name);

			$wjo->attachment = $file_name;
		}


		try{
			
			$wjo->save();

			$response = array(
				'status' => true,
				'datas' => "Berhasil",
			);
			return Response::json($response);

		}
		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				$response = array(
					'status' => false,
					'datas' => "Edit Error",
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false,
					'datas' => "Edit Error",
				);
				return Response::json($response);
			}
		}
	}

	public function fetch_item_edit(Request $request){
		$items = WorkshopJobOrder::find($request->get("id"));

		$material = $this->material;

		$response = array(
			'status' => true,
			'datas' => $items,
			'material' => $material,
		);
		return Response::json($response);
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

				$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
				->leftJOin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_logs.operator_id')
				->where('workshop_logs.order_no', '=', $request->get('order_no'))
				->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name', db::raw('timestampdiff(second, workshop_logs.started_at, workshop_logs.created_at) as actual'), db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as pic'))
				->orderBy('sequence_process', 'asc')
				->get();

				$process = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
				->where('workshop_flow_processes.order_no', '=', $request->get('order_no'))
				->select('workshop_flow_processes.sequence_process', 'workshop_flow_processes.order_no', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.std_time', 'workshop_flow_processes.status')
				->orderBy('sequence_process', 'asc')
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

				$temp = WorkshopTempProcess::where('tag', '=', $wjo->tag)
				->where('operator', '=', Auth::user()->username)
				->first();
				$started_at = $temp->started_at;

				$response = array(
					'status' => true,
					'wjo' => $wjo,
					'started_at' => $started_at,
					'flow_process' => $process,
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
		->leftJoin(db::raw('employee_syncs es'), 'es.employee_id', '=', 'workshop_job_orders.created_by')
		->leftJoin('workshop_tag_availabilities', 'workshop_tag_availabilities.tag', '=', 'workshop_job_orders.tag')
		->where('workshop_job_orders.tag', '=', $request->get('tag'))
		->whereBetween('workshop_job_orders.remark', array(2, 3))
		->select('workshop_job_orders.order_no',
			'workshop_tag_availabilities.remark as tag_remark',
			'workshop_job_orders.item_number as file_name',
			'workshop_job_orders.priority',
			'workshop_job_orders.category',
			'workshop_job_orders.drawing_name',
			'workshop_job_orders.item_number',
			'workshop_job_orders.part_number',
			'workshop_job_orders.category',
			'workshop_job_orders.item_name',
			'workshop_job_orders.tag',
			'workshop_job_orders.type',
			'workshop_job_orders.quantity',
			'workshop_job_orders.material',
			'workshop_job_orders.problem_description',
			'workshop_job_orders.target_date',
			'workshop_job_orders.sub_section',
			db::raw('concat(SPLIT_STRING(es.name, " ", 1)," ",SPLIT_STRING(es.name, " ", 2)) as requester'),
			db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as name'),
			'workshop_job_orders.attachment')
		->first();

		if(!$wjo){
			$response = array(
				'status' => false,
				'message' => 'WJO tidak ditemukan'
			);
			return Response::json($response);
		}

		$path = '/workshop/' . $wjo->attachment;			
		$file_path = asset($path);

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
		->leftJOin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_logs.operator_id')
		->where('workshop_logs.order_no', '=', $wjo->order_no)
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name', db::raw('timestampdiff(second, workshop_logs.started_at, workshop_logs.created_at) as actual'), db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as pic'))
		->orderBy('sequence_process', 'asc')
		->get();

		$response = array(
			'status' => true,
			'wjo' => $wjo,
			'listed_time' => $listed_time,
			'flow_process' => $process,
			'wjo_log' => $workshop_log,
			'file_path' => $file_path,
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

		$temp = WorkshopTempProcess::where('tag', '=', $wjo->tag)
		->where('operator', '=', Auth::user()->username)
		->first();
		if(!$temp){
			$temp = new WorkshopTempProcess([
				'tag' => $wjo->tag,
				'operator' => Auth::user()->username,
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

		if($wjo_log){
			$flow = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
			->where('order_no', '=', $wjo->order_no)
			->where('status', '=', 1)
			->where('sequence_process', '=', ($wjo_log->sequence_process + 1))
			->select('workshop_flow_processes.order_no', 'workshop_flow_processes.sequence_process', 'workshop_flow_processes.machine_code', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.status')
			->first();

			if($flow){
				if($flow->process_name != $current_machine->process_name){
					$response = array(
						'status' => false,
						'message' => 'Proses tidak sama dengan sebelumnya',
						'order_no' => $wjo->order_no,
						'sequence_process' => ($wjo_log->sequence_process + 1)
					);
					return Response::json($response);
				}
			}
		}else{
			$flow = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
			->where('order_no', '=', $wjo->order_no)
			->where('status', '=', 1)
			->where('sequence_process', '=', 1)
			->select('workshop_flow_processes.order_no', 'workshop_flow_processes.sequence_process', 'workshop_flow_processes.machine_code', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.status')
			->first();

			if($flow){
				if($flow->process_name != $current_machine->process_name){
					$response = array(
						'status' => false,
						'message' => 'Proses tidak sama dengan sebelumnya',
						'order_no' => $wjo->order_no,
						'sequence_process' => 1						
					);
					return Response::json($response);
				}
			}
		}
		

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
		->leftJOin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_logs.operator_id')
		->where('workshop_logs.order_no', '=', $wjo->order_no)
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name', db::raw('timestampdiff(second, workshop_logs.started_at, workshop_logs.created_at) as actual'), db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as pic'))
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
		// ->leftJoin(db::raw('(select employee_id, name from employee_syncs) as pic'), 'pic.employee_id', '=', 'workshop_job_orders.operator')
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
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '<=', $targetTo);
		}
		if(strlen($request->get('finFrom')) > 0 ){
			$finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('date(workshop_job_orders.created_at)'), '>=', $finFrom);
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
			'workshop_job_orders.drawing_name',
			'workshop_job_orders.item_number',
			'workshop_job_orders.part_number',
			'workshop_job_orders.item_name',
			'workshop_job_orders.quantity',
			'workshop_job_orders.priority',
			'workshop_job_orders.type',
			'workshop_job_orders.material',
			'workshop_job_orders.problem_description',
			'processes.process_name',
			'workshop_job_orders.target_date',
			'workshop_job_orders.finish_date',
			'workshop_job_orders.difficulty',
			'workshop_job_orders.main_process',
			'workshop_job_orders.rating',
			'workshop_job_orders.note',
			'approver.name as approver_name',
			db::raw('(SELECT concat( SPLIT_STRING ( `name`, " ", 1 ), " ", SPLIT_STRING ( `name`, " ", 2 ) ) AS pic FROM workshop_flow_processes 
				LEFT JOIN employee_syncs on employee_syncs.employee_id = workshop_flow_processes.operator
				WHERE order_no = workshop_job_orders.order_no 
				order by id asc limit 1) as pic_name')
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
		$temp = WorkshopTempProcess::where('tag', '=', $wjo->tag)
		->where('operator', '=', Auth::user()->username)
		->first();

		try {

			// $started_at = new DateTime($temp->started_at);

			// $now = new DateTime("now");
			// $std_time = $now->getTimestamp() - $started_at->getTimestamp();

			// $process = WorkshopFlowProcess::where('order_no', '=', $wjo->order_no)
			// ->orderBy('sequence_process', 'desc')
			// ->first();

			// if($process){
			// 	if($process->status == 0){
			// 		if($log){
			// 			if(($log->sequence_process + 1) > $process->sequence_process){
			// 				$flow_process = new WorkshopFlowProcess([
			// 					'order_no' => $wjo->order_no,
			// 					'sequence_process' => $process->sequence_process + 1,
			// 					'machine_code' => $machine_code,
			// 					'status' => 0,
			// 					'std_time' => $std_time,
			// 					'created_by' => $operator_id,
			// 				]);
			// 				$flow_process->save();	
			// 			}
			// 		}				

			// 	}
			// }else{
			// 	$flow_process = new WorkshopFlowProcess([
			// 		'order_no' => $wjo->order_no,
			// 		'sequence_process' => 1,
			// 		'machine_code' => $machine_code,
			// 		'status' => 0,
			// 		'std_time' => $std_time,
			// 		'created_by' => $operator_id,
			// 	]);
			// 	$flow_process->save();
			// }

			

			DB::transaction(function() use ($wjo, $workshop_log, $temp){
				$wjo->save();
				$workshop_log->save();
				$temp->delete();
			});

			$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
			->leftJOin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_logs.operator_id')
			->where('workshop_logs.order_no', '=', $wjo->order_no)
			->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name', db::raw('timestampdiff(second, workshop_logs.started_at, workshop_logs.created_at) as actual'), db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as pic'))
			->orderBy('sequence_process', 'desc')
			->first();

			$response = array(
				'status' => true,
				'wjo_log' => $workshop_log,
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
		$prefix_now = 'WJO'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','wjo')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$sub_section = $request->get('sub_section');
		$item_name = $request->get('item_name');
		$category = $request->get('category');
		$drawing_name = $request->get('drawing_name');
		$item_number = $request->get('item_number');
		$part_number = $request->get('part_number');
		$quantity = $request->get('quantity');
		$priority = $request->get('priority');
		$type = $request->get('type');
		$material = $request->get('material');
		$problem_desc = $request->get('problem_desc');

		if($material == 'Lainnya'){
			$material = $request->get('material-other');
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
			'drawing_name' => $drawing_name,
			'item_number' => $item_number,
			'part_number' => $part_number,
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
				->bcc(['aditya.agassi@music.yamaha.com', 'darma.bagus@music.yamaha.com'])
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

		$order_no = $request->get('assign_order_no');
		$item_name = $request->get('assign_item_name');
		$quantity = $request->get('assign_quantity');
		$material = $request->get('assign_material');
		$problem_description = $request->get('assign_problem_desc');

		$tag = $request->get('tag');
		$target_date = $request->get('assign_target_date');
		$category = $request->get('assign_category');
		$drawing_name = $request->get('assign_drawing_name');
		$item_number = $request->get('assign_item_number');
		$part_number = $request->get('assign_part_number');
		// $pic = $request->get('assign_pic');
		$difficulty = $request->get('assign_difficulty');

		$file_name;
		if($request->hasFile('assign_drawing')) {
			$file = $request->file('assign_drawing');
			$file_name = 'DRW_'.$order_no.'.'.$file->getClientOriginalExtension();
			$file->move(public_path('drawing'), $file_name);
		}else{
			$file_name = null;
		}

		$wjo = WorkshopJobOrder::where('order_no', '=', $order_no)->first();
		$wjo->item_name = $item_name;
		$wjo->quantity = $quantity;
		$wjo->material = $material;
		$wjo->problem_description = $problem_description;
		$wjo->tag = $tag;		
		$wjo->target_date = $target_date;
		$wjo->category = $category;
		$wjo->drawing_name = $drawing_name;
		$wjo->item_number = $item_number;
		$wjo->part_number = $part_number;
		// $wjo->operator = $pic;
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

			$flow_processes = [];
			$exist_arr = [];
			
			for($x = 1; $x <= $request->get('assign_proses'); $x++) {
				$stat = 0;

				// ==================== EXIST FLOW PROCCESS ====================

				$existed_flow = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
				->leftJoin('workshop_job_orders','workshop_job_orders.order_no', '=', 'workshop_flow_processes.order_no')
				->where('workshop_flow_processes.machine_code', '=', $request->get('process_'.$x))
				->where('workshop_job_orders.remark', '<', '4')
				->select('workshop_flow_processes.order_no',
					'workshop_flow_processes.sequence_process',
					'workshop_flow_processes.machine_code',
					'workshop_processes.category',
					'workshop_processes.machine_name',
					'workshop_processes.process_name',
					'workshop_flow_processes.start_plan',
					'workshop_flow_processes.finish_plan',
					'workshop_flow_processes.std_time')
				->get();

				if(count($existed_flow) > 0){
					if($existed_flow[0]->category == 'MACHINE'){
						for($j = 0; $j < count($existed_flow); $j++) {
							$start = $request->get('start_'.$x) .' '.$request->get('start_time'.$x).':01';
							$finish = $request->get('finish_'.$x) .' '.$request->get('finish_time'.$x).':00';

							if($existed_flow[$j]->finish_plan <> null){
								$start_plan = new DateTime($start);
								$finish_plan = new DateTime($finish);

								$start_exist = new DateTime($existed_flow[$j]->start_plan);
								$finish_exist = new DateTime($existed_flow[$j]->finish_plan);

								if(($start_plan >= $start_exist && $start_plan <= $finish_exist) || ($start_exist >= $start_plan && $start_exist <= $finish_plan)){
									// $response = array(
									// 	'status' => false,
									// 	'message' => 'Plan sudah ada',
									// );
									// return Response::json($response);
									$stat = 1;
									array_push($exist_arr, $x);
								}

								if(($finish_plan >= $start_exist && $finish_plan <= $finish_exist) || ($finish_exist >= $start_plan && $finish_exist <= $finish_plan)){
									// $response = array(
									// 	'status' => false,
									// 	'message' => 'Plan sudah ada',
									// );
									// return Response::json($response);
									if ($stat == 0) {										
										array_push($exist_arr, $x);
									}
								}
							}
						}
					}
				}

				//  =================

				$flow_processes[$x] = new WorkshopFlowProcess([
					'order_no' => $order_no,
					'sequence_process' => $x,
					'machine_code' => $request->get('process_'.$x),
					'status' => 1,
					'start_plan' => date($request->get('start_'.$x) .' '.$request->get('start_time'.$x).':01'),
					'finish_plan' => date($request->get('finish_'.$x) .' '.$request->get('finish_time'.$x).':00'),
					'std_time' => $request->get('process_qty_'.$x) * 60,
					'operator' => $request->get('assign_pic_'.$x),
					'created_by' => Auth::user()->username,
				]);
			}

			if (count($exist_arr) == 0) {
				for($x = 1; $x <= count($flow_processes); $x++) {
					$flow_processes[$x]->save();
				}
			} else {
				$response = array(
					'status' => false,
					'message' => 'Plan sudah ada',
					'exist' => $exist_arr
				);
				return Response::json($response);
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

	public function editLeaderWJO(Request $request){
		$order_no = $request->get('edit_order_no');
		$item_name = $request->get('edit_item_name');
		$quantity = $request->get('edit_quantity');
		$material = $request->get('edit_material');
		$problem_description = $request->get('edit_problem_desc');

		$target_date = $request->get('edit_target_date');
		$category = $request->get('edit_category');
		$difficulty = $request->get('edit_difficulty');

		$drawing_name = $request->get('edit_drawing_name') ? $request->get('edit_drawing_name') : NULL;
		$drawing_number = $request->get('edit_drawing_number') ? $request->get('edit_drawing_number') : NULL;
		$part_number = $request->get('edit_part_number') ? $request->get('edit_part_number') : NULL;

		foreach ($request->get('pic') as $pics) {
			$pic = WorkshopFlowProcess::find($pics[0]);

			$pic->operator = $pics[1];
			$pic->std_time = $pics[2] * 60;
			$pic->start_plan = $pics[3]." ".$pics[4];
			$pic->finish_plan = $pics[5]." ".$pics[6];

			$pic->save();
		}
		// $pic2 = $request->get('edit_pic');

		$wjo = WorkshopJobOrder::where('order_no', '=', $order_no)->first();
		$wjo->item_name = $item_name;
		$wjo->quantity = $quantity;
		$wjo->material = $material;
		$wjo->problem_description = $problem_description;
		$wjo->target_date = $target_date;
		$wjo->category = $category;
		// $wjo->operator = $pic2;
		$wjo->difficulty = $difficulty;
		$wjo->drawing_name = $drawing_name;
		$wjo->item_number = $drawing_number;
		$wjo->part_number = $part_number;
		
		try{
			DB::transaction(function() use ($wjo){
				$wjo->save();
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
				$tag_availability = WorkshopTagAvailability::where('tag', '=', $wjo->tag)->first();
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

		$op = db::select('select op.operator_id, concat(SPLIT_STRING(emp.`name`, " ", 1), " ", SPLIT_STRING(emp.`name`, " ", 2)) as name from workshop_operators op
			left join employee_syncs emp
			on emp.employee_id = op.operator_id');

		$op_workload = db::select('select op_workload.operator, concat(SPLIT_STRING(emp.`name`, " ", 1), " ", SPLIT_STRING(emp.`name`, " ", 2)) as `name`, sum(op_workload.workload) as workload from
			(select workload.operator, round((workload.workload/60), 0) as workload from
			(select workload.order_no, workload.operator, sum(workload.workload) workload from
			(select flow.order_no, flow.operator, sum(std_time) as workload from workshop_flow_processes flow
			left join workshop_job_orders wjo on wjo.order_no = flow.order_no
			where wjo.remark < 4
			group by flow.order_no, flow.operator
			union all
			select log.order_no, log.operator_id as operator, -sum(TIMESTAMPDIFF(second,log.started_at,log.created_at)) as workload from workshop_logs log
			left join workshop_job_orders wjo on wjo.order_no = log.order_no
			where wjo.remark < 4
			group by log.order_no, log.operator_id) as workload
			group by workload.order_no, workload.operator
			having workload > 0) workload
			) op_workload
			left join employee_syncs emp on emp.employee_id = op_workload.operator
			group by operator, `name`');

		$machine = db::select('SELECT machine_code, shortname, shift FROM workshop_processes
			where category = "MACHINE"
			order by shortname asc');

		$mc_workload = db::select("select flow.machine_code, process.shortname, flow.order_no, flow.start_plan, flow.finish_plan, std_time from workshop_flow_processes flow
			left join workshop_job_orders wjo on wjo.order_no = flow.order_no
			left join workshop_processes process on process.machine_code = flow.machine_code
			where wjo.remark < 4
			and process.category = 'MACHINE'
			and start_plan is not null
			order by machine_code, start_plan asc");

		$response = array(
			'status' => true,
			'op' => $op,
			'op_workload' => $op_workload,
			'machine' => $machine,
			'mc_workload' => $mc_workload,
		);
		return Response::json($response);

	}

	public function fetchWorkloadOperatorDetail(Request $request){
		$name = $request->get('name');

		$detail = db::select('select concat(SPLIT_STRING(emp.`name`, " ", 1)," ",SPLIT_STRING(emp.`name`, " ", 2)) as `name`, workload.order_no, wjo.target_date, tag.remark as tag_number, wjo.item_name, round((workload.workload/60), 0) as workload, workload.priority from			
			(select workload.order_no, workload.operator, sum(workload.workload) workload, priority from
			(select flow.order_no, flow.operator, sum(std_time) as workload, wjo.priority from workshop_flow_processes flow
			left join workshop_job_orders wjo on wjo.order_no = flow.order_no
			where wjo.remark < 4
			group by flow.order_no, flow.operator, priority
			union all
			select log.order_no, log.operator_id as operator, -sum(TIMESTAMPDIFF(second,log.started_at,log.created_at)) as workload, wjo.priority from workshop_logs log
			left join workshop_job_orders wjo on wjo.order_no = log.order_no
			where wjo.remark < 4
			group by log.order_no, log.operator_id, priority) as workload
			group by workload.order_no, workload.operator, priority
			having workload > 0) workload
			left join employee_syncs emp
			on emp.employee_id = workload.operator
			left join workshop_job_orders wjo
			on wjo.order_no = workload.order_no
			left join workshop_tag_availabilities tag
			on tag.tag = wjo.tag
			where emp.`name` like "%'.$name.'%"
			and emp.`group` = "Workshop Group"
			order by target_date asc');

		$response = array(
			'status' => true,
			'detail' => $detail
		);
		return Response::json($response);
		
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
		// DB::enableQueryLog();
		$workshop_job_orders = WorkshopJobOrder::leftJoin(db::raw('(select employee_id, name from employee_syncs) as approver'), 'approver.employee_id', '=', 'workshop_job_orders.approved_by')
		->leftJoin(db::raw('(select employee_id, name from employee_syncs) as requester'), 'requester.employee_id', '=', 'workshop_job_orders.created_by')
		->leftJoin(db::raw('(SELECT process_code, process_name FROM processes where remark = "workshop") as processes'), 'processes.process_code', '=', 'workshop_job_orders.remark')
		->leftJoin('workshop_tag_availabilities', 'workshop_tag_availabilities.tag', '=', 'workshop_job_orders.tag')
		->leftJoin('workshop_receipts', 'workshop_receipts.order_no', '=', 'workshop_job_orders.order_no')
		->leftJoin('employees', 'employees.tag', '=', 'workshop_receipts.receiver');
		// ->leftJoin(db::raw('(SELECT order_no, operator FROM workshop_flow_processes WHERE id IN (SELECT min(id) FROM workshop_flow_processes GROUP BY order_no)) as operator'), 'operator.order_no', '=', 'workshop_job_orders.order_no')
		// ->leftJoin(db::raw('(select employee_id, name from employee_syncs) as pic'), 'pic.employee_id', '=', 'operator.operator');

		if(strlen($request->get('order')) > 0 ){
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.order_no', 'desc');
		}else{
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.priority', 'desc');
			$workshop_job_orders = $workshop_job_orders->orderBy('workshop_job_orders.target_date', 'asc');
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
			$workshop_job_orders = $workshop_job_orders->where(db::raw('workshop_job_orders.finish_date'), '>=', $finFrom);
		}
		if(strlen($request->get('finTo')) > 0 ){
			$finTo = date('Y-m-d', strtotime($request->get('finTo')));
			$workshop_job_orders = $workshop_job_orders->where(db::raw('workshop_job_orders.finish_date'), '<=', $finTo);
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
		// if($request->get('rawMaterial') != null){
		// 	$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.material', $request->get('rawMaterial'));
		// }
		// if($request->get('material') != null){
		// 	$workshop_job_orders = $workshop_job_orders->whereIn('workshop_job_orders.item_number', $request->get('material'));
		// }
		if(strlen($request->get('req')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.created_by', '=', $request->get('req'));
		}
		if(strlen($request->get('approvedBy')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.approved_by', '=', $request->get('approvedBy'));
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
			db::raw('concat(SPLIT_STRING(approver.name, " ", 1), " ", SPLIT_STRING(approver.name, " ", 2)) as approver'),
			'workshop_job_orders.item_name',
			'workshop_job_orders.material',
			'workshop_job_orders.quantity',
			db::raw('(SELECT concat( SPLIT_STRING ( `name`, " ", 1 ), " ", SPLIT_STRING ( `name`, " ", 2 ) ) AS pic FROM workshop_flow_processes 
				LEFT JOIN employee_syncs on employee_syncs.employee_id = workshop_flow_processes.operator
				WHERE order_no = workshop_job_orders.order_no 
				order by id asc limit 1) as pic'),
			// db::raw('"-" as pic'),
			'workshop_job_orders.difficulty',
			'workshop_job_orders.priority',
			'workshop_job_orders.target_date',
			'workshop_job_orders.finish_date',
			'processes.process_name',
			'workshop_job_orders.attachment',
			'workshop_job_orders.item_number',
			'workshop_job_orders.remark',
			'employees.name');

		$workshop_job_orders = $workshop_job_orders->get();

		$response = array(
			'status' => true,
			'tableData' => $workshop_job_orders,
			// 'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function fetchAssignForm(Request $request){
		// DB::enableQueryLog();

		$wjo = WorkshopJobOrder::leftJoin('workshop_flow_processes', 'workshop_job_orders.order_no', '=', 'workshop_flow_processes.order_no')
		->leftJoin('workshop_processes', 'workshop_flow_processes.machine_code', '=', 'workshop_processes.machine_code')
		->leftJoin('employee_syncs','workshop_flow_processes.operator', '=', 'employee_syncs.employee_id')
		->where('workshop_job_orders.order_no', '=', $request->get('order_no'))
		->select('workshop_job_orders.order_no', 'workshop_job_orders.priority', 'workshop_job_orders.sub_section', 'workshop_job_orders.type', 'workshop_job_orders.item_name', 'workshop_job_orders.quantity', 'workshop_job_orders.material', 'workshop_job_orders.problem_description', 'workshop_job_orders.target_date', 'workshop_job_orders.difficulty', 'workshop_job_orders.category', 'workshop_job_orders.part_number', 'workshop_job_orders.drawing_name', 'workshop_job_orders.item_number', 'workshop_flow_processes.machine_code', 'workshop_flow_processes.operator', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.sequence_process', db::raw('workshop_flow_processes.id as flow_id'), 'employee_syncs.name','workshop_job_orders.created_at', db::raw('workshop_job_orders.operator as pic'), 'std_time', 'start_plan', 'finish_plan')
		->orderBy('workshop_flow_processes.id', 'asc')
		->get();

		$response = array(
			'status' => true,
			'wjo' => $wjo,
			// 'query' => DB::getQueryLog()
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
		$datefrom = date("Y-m-d",  strtotime('-90 days'));
		$dateto = date("Y-m-d");

		$last = WorkshopJobOrder::where('remark', '<', 4)
		->orderBy('created_at', 'asc')
		->select(db::raw('date(created_at) as created_at'))
		->first();

		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}else{
			// if($last){
			// 	$created_at = date_create($last->created_at);
			// 	$now = date_create(date('Y-m-d'));
			// 	$interval = $now->diff($created_at);
			// 	$diff = $interval->format('%a%');

			// 	if($diff > 30){
			// 		$datefrom = date('Y-m-d', strtotime($last->created_at));
			// 	}
			// }
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

		// $progress = db::select("select wjo.order_no, wjo.priority, concat(SPLIT_STRING(requester.name, ' ', 1), ' ', SPLIT_STRING(requester.name, ' ', 2)) as requester, wjo.item_name, wjo.quantity, concat(SPLIT_STRING(pic.name, ' ', 1), ' ', SPLIT_STRING(pic.name, ' ', 2)) as `pic`, coalesce(date(requested.created_at), date(wjo.created_at)) as requested, date(listed.created_at) as listed, date(approved.created_at) as approved, date(progress.created_at) as progress, target_date, step.std, step.actual from workshop_job_orders wjo
		// 	left join (select * from workshop_job_order_logs where remark = 0) as requested
		// 	on requested.order_no = wjo.order_no
		// 	left join (select * from workshop_job_order_logs where remark = 1) as listed
		// 	on listed.order_no = wjo.order_no
		// 	left join (select * from workshop_job_order_logs where remark = 2) as approved
		// 	on approved.order_no = wjo.order_no
		// 	left join (select * from workshop_job_order_logs where remark = 2) as progress
		// 	on progress.order_no = wjo.order_no
		// 	left join employee_syncs pic on wjo.operator = pic.employee_id
		// 	left join employee_syncs requester on wjo.created_by = requester.employee_id
		// 	left join
		// 	(select wjo.order_no, COALESCE(flow.std,0) as std, COALESCE(log.actual,0) as actual from workshop_job_orders wjo
		// 	left join
		// 	(select flow.order_no, count(flow.order_no) as std from workshop_flow_processes flow
		// 	left join workshop_job_orders wjo
		// 	on flow.order_no = wjo.order_no
		// 	where wjo.remark < 4
		// 	group by flow.order_no) flow
		// 	on flow.order_no = wjo.order_no 
		// 	left join
		// 	(select log.order_no, count(log.order_no) as actual from workshop_logs log
		// 	left join workshop_job_orders wjo
		// 	on log.order_no = wjo.order_no
		// 	where wjo.remark < 4
		// 	group by log.order_no) log
		// 	on log.order_no = wjo.order_no
		// 	where wjo.remark < 4) step
		// 	on step.order_no = wjo.order_no
		// 	where date(wjo.created_at) >= '".$datefrom."'
		// 	and date(wjo.created_at) <= '".$dateto."'
		// 	and wjo.remark = 3
		// 	order by wjo.priority desc, wjo.order_no asc");
		
		$progress = db::select("select wjo.order_no, wjo.priority, concat(SPLIT_STRING(requester.name, ' ', 1), ' ', SPLIT_STRING(requester.name, ' ', 2)) as requester, wjo.item_name, wjo.quantity,
			(SELECT concat( SPLIT_STRING ( `name`, ' ', 1 ), ' ', SPLIT_STRING ( `name`, ' ', 2 ) ) AS pic FROM workshop_flow_processes 
			LEFT JOIN employee_syncs on employee_syncs.employee_id = workshop_flow_processes.operator
			WHERE order_no = wjo.order_no 
			order by id asc limit 1) as pic,
			coalesce(date(requested.created_at), date(wjo.created_at)) as requested, date(listed.created_at) as listed, date(approved.created_at) as approved, date(progress.created_at) as progress, target_date, step.std, step.actual from workshop_job_orders wjo
			left join (select * from workshop_job_order_logs where remark = 0) as requested
			on requested.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 1) as listed
			on listed.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 2) as approved
			on approved.order_no = wjo.order_no
			left join (select * from workshop_job_order_logs where remark = 2) as progress
			on progress.order_no = wjo.order_no
			left join employee_syncs requester on wjo.created_by = requester.employee_id
			left join
			(select wjo.order_no, COALESCE(flow.std,0) as std, COALESCE(log.actual,0) as actual from workshop_job_orders wjo
			left join
			(select flow.order_no, SUM(flow.std_time) as std from workshop_flow_processes flow
			left join workshop_job_orders wjo
			on flow.order_no = wjo.order_no
			where wjo.remark < 4
			group by flow.order_no) flow
			on flow.order_no = wjo.order_no 
			left join
			(select act.order_no, actual + IFNULL(waktu_kerja ,0) as actual from
			(select log.order_no, SUM(TIMESTAMPDIFF(SECOND, started_at, log.created_at)) as actual from workshop_logs log
			left join workshop_job_orders wjo
			on log.order_no = wjo.order_no
			where wjo.remark < 4
			group by log.order_no) as act
			left join (
			select order_no, workshop_temp_processes.operator, TIMESTAMPDIFF(SECOND, workshop_temp_processes.started_at, now()) as waktu_kerja from workshop_temp_processes
			left join workshop_job_orders on workshop_temp_processes.tag = workshop_job_orders.tag
			where remark in (2,3)
			) wjo2 on act.order_no = wjo2.order_no
			) log
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


		$machine = db::select("select m.shortname, COALESCE(CEILING(t.time/60),0) as time from workshop_processes m
			left join
			(select p.shortname, sum(timestampdiff(SECOND, l.started_at, l.created_at)) as time from workshop_logs l
			left join workshop_processes p on p.machine_code = l.machine_code
			where date(l.started_at) = '".$date."'
			group by p.shortname) t
			on m.shortname = t.shortname
			where m.category = 'MACHINE'
			order by shortname asc");

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

		if (strpos($name, "'") !== false) {
			$name = explode("'", $name)[0];
		}

		$detail = db::select("select concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, p.machine_name, p.process_name, l.started_at, l.created_at, l.order_no  from workshop_logs l
			left join employee_syncs e on e.employee_id = l.operator_id
			left join workshop_processes p on p.machine_code = l.machine_code
			where e.`group` = 'Workshop Group'
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

	public function fetchProcessDetail(Request $request)
	{
		$wjo = WorkshopJobOrder::Join(db::raw('(SELECT process_code, process_name FROM processes where remark = "workshop") as processes'), 'processes.process_code', '=', 'workshop_job_orders.remark')->where('order_no',$request->get('order_no'))->select('workshop_job_orders.order_no','item_name','quantity','target_date','process_name','remark','priority','sub_section','type','material','problem_description','drawing_name','part_number','item_number','category','reject_reason')->first();

		$process = WorkshopFlowProcess::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_flow_processes.machine_code')
		->where('workshop_flow_processes.order_no', '=', $request->get('order_no'))
		->select('workshop_flow_processes.sequence_process', 'workshop_flow_processes.order_no', 'workshop_processes.machine_name', 'workshop_processes.process_name', 'workshop_flow_processes.std_time', 'workshop_flow_processes.status')
		->orderBy('sequence_process', 'asc')
		->get();

		$workshop_log = WorkshopLog::leftJOin('workshop_processes', 'workshop_processes.machine_code', '=', 'workshop_logs.machine_code')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'workshop_logs.operator_id')
		->where('workshop_logs.order_no', '=', $request->get('order_no'))
		->select('workshop_logs.order_no', 'workshop_logs.sequence_process', 'workshop_processes.machine_name', 'workshop_processes.process_name', db::raw('timestampdiff(second, workshop_logs.started_at, workshop_logs.created_at) as actual'), db::raw('concat(SPLIT_STRING(employee_syncs.name, " ", 1), " ", SPLIT_STRING(employee_syncs.name, " ", 2)) as pic'))
		->orderBy('sequence_process', 'asc')
		->get();

		$response = array(
			'status' => true,
			'flow' => $process,
			'act' => $workshop_log,
			'detail' => $wjo
		);
		return Response::json($response);
	}

	public function fetchJobHistory(Request $request)
	{
		if ($request->get('mon') == "") {
			$mon = date('Y-m');
			$mon2 = date('F');
		} else {
			$mon = $request->get('mon');
			$mon2 = date('F', strtotime($mon."-01"));
		}

		$job_history = db::select("
			SELECT mesin_operator.process_name, mesin_operator.operator_id, name as operator_name, SUM(waktu) as waktu from 
			(SELECT DISTINCT process_name, operator_id, 0 as waktu FROM workshop_processes
			CROSS JOIN workshop_operators
			union all
			SELECT workshop_processes.process_name, act.operator_id, act.waktu from
			(SELECT operator_id, machine_code, ROUND(SUM(TIME_TO_SEC(TIMEDIFF(created_at,started_at))) / 60,2) as waktu from workshop_logs where DATE_FORMAT(started_at,'%Y-%m') = '".$mon."'
			group by operator_id, machine_code) as act
			left join workshop_processes on act.machine_code = workshop_processes.machine_code) mesin_operator
			join employee_syncs on employee_syncs.employee_id = mesin_operator.operator_id
			group by process_name, operator_id, name
			order by operator_id asc, process_name asc
			");

		$response = array(
			'status' => true,
			'param' => $mon2,
			'datas' => $job_history,
		);
		return Response::json($response);
	}

	public function cancelWJO(Request $request)
	{
		$wjo = WorkshopJobOrder::where('order_no', '=', $request->get('wjo_num'))->update(['remark' => 6]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function fetchOperatorload(){

		$operators = db::select('SELECT operator_id, name FROM workshop_operators
			LEFT JOIN employee_syncs on workshop_operators.operator_id = employee_syncs.employee_id
			order by operator_id asc');

		$op_workloads = db::select("select workshop_flow_processes.operator, process.shortname, workshop_flow_processes.order_no, start_plan, finish_plan, std_time from workshop_flow_processes
			left join workshop_job_orders wjo on wjo.order_no = workshop_flow_processes.order_no
			left join workshop_processes process on process.machine_code = workshop_flow_processes.machine_code
			where wjo.remark < 4 and start_plan is not null
			ORDER BY workshop_flow_processes.operator, start_plan asc");

		$response = array(
			'status' => true,
			'operators' => $operators,
			'op_workloads' => $op_workloads,
		);
		return Response::json($response);

	}

	public function fetchDrawingMaterial()
	{
		$draw = db::select("select drawing_number, drawing_name, part_number, material, quantity, part_name from drawing_materials
			group by drawing_number, drawing_name, part_number, material, quantity, part_name");

		$response = array(
			'status' => true,
			'drawing' => $draw
		);
		return Response::json($response);
	}

	public function fetchFinishedWJO(Request $request)
	{
		$datas = WorkshopJobOrder::leftJoin("workshop_receipts", "workshop_job_orders.order_no","=","workshop_receipts.order_no")
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "workshop_job_orders.created_by")
		->select(db::raw("DATE_FORMAT(workshop_job_orders.created_at, '%Y-%m-%d') as tgl_pengajuan"), "employee_syncs.name", db::raw("workshop_job_orders.sub_section as bagian"), "workshop_job_orders.order_no", "workshop_job_orders.priority" , "workshop_job_orders.type", "workshop_job_orders.item_name", "quantity", "target_date", "attachment")
		->where("remark", "=", "4")
		->where(db::raw("DATE_FORMAT(workshop_job_orders.created_at, '%Y-%m-%d')"), ">=", "2020-04-01")
		->whereNull("workshop_receipts.order_no");

		if(strlen($request->get('pemohon')) > 0 ){
			$datas = $datas->where('employee_syncs.name', 'like', "%".$request->get('pemohon')."%");
		}

		if(strlen($request->get('bagian')) > 0 ){
			$datas = $datas->where('workshop_job_orders.sub_section', '=', $request->get('bagian'));
		}

		$datas = $datas->orderBy("workshop_job_orders.created_at","desc")
		->get();

		$response = array(
			'status' => true,
			'data' => $datas
		);
		return Response::json($response);
	}

	public function fetchReceivedWJO(Request $request)
	{
		$datas = db::table('workshop_receipts');

		if(strlen($request->get('tgl')) > 0 ){
			$datas = $datas->where(db::raw("DATE_FORMAT(workshop_job_orders.created_at, '%Y-%m-%d')"), '=', $request->get('tgl'));
		}

		if(strlen($request->get('pemohon')) > 0 ){
			$datas = $datas->where('employee_syncs.name', 'like', "%".$request->get('pemohon')."%");
		}

		if(strlen($request->get('bagian')) > 0 ){
			$datas = $datas->where('workshop_job_orders.sub_section', '=', $request->get('bagian'));
		}


		$datas = $datas->leftJoin('workshop_job_orders','workshop_receipts.order_no','=','workshop_job_orders.order_no')
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "workshop_job_orders.created_by")
		->leftJoin(db::raw("employees es"), "es.tag", "=", "workshop_receipts.receiver")
		->select(db::raw("DATE_FORMAT(workshop_job_orders.created_at, '%Y-%m-%d') as tgl_pengajuan"), "employee_syncs.name", db::raw("workshop_job_orders.sub_section as bagian"), "workshop_job_orders.order_no", "workshop_job_orders.priority" , "workshop_job_orders.type", "workshop_job_orders.item_name", "quantity", db::raw("es.name as receiver"), "attachment")
		->orderBy("workshop_receipts.created_at")
		->get();

		$response = array(
			'status' => true,
			'data' => $datas
		);
		return Response::json($response);
	}

	public function scanWJOReceipt(Request $request)
	{

		$cek = Employee::where('tag','=', $request->get('tag'))->first();

		if (!$cek) {
			$response = array(
				'status' => false,
				'message' => 'Karyawan tidak terdaftar'
			);
			return Response::json($response);
		}

		try {
			DB::table('workshop_receipts')->insert(
				[
					'order_no' => $request->get('wjo'), 
					'receiver' => $request->get('tag'),
					'created_by' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]
			);

			$wjo_jig = JigPartStock::where("remark", "=", $request->get('wjo'))->get()->count();

			if ($wjo_jig > 0) {
				$updt_jig = JigPartStock::where('remark', '=', $request->get('wjo'))->first();
				$updt_jig->remark = null;
				$updt_jig->quantity = $updt_jig->quantity + $updt_jig->quantity_order;
				$updt_jig->quantity_order = null;
				$updt_jig->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Data Berhasil ditambahkan'
			);
			return Response::json($response);

		} catch (QueryException $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchPickedWJO()
	{
		$datas = DB::table('workshop_receipts')
		->leftJoin('workshop_job_orders','workshop_receipts.order_no', '=', 'workshop_job_orders.order_no')
		->leftJoin('employee_syncs','employee_syncs.employee_id', '=', 'workshop_receipts.receiver')
		->select('workshop_receipts.order_no','employee_syncs.name', db::raw('date_format(workshop_job_orders.created_at, "%Y-%m-%d") as tgl_pengajuan'), '')
		->get();

		return DataTables::of($datas)
		->addColumn('action', function($datas){
			return '<a href="javascript:void(0)" class="btn btn-xs btn-info" onClick="detailReport(id)" id="' . $datas->order_no . '">Details</a>';
		})
		->addColumn('att', function($datas){
			if($datas->attachment){
				return '<a href="javascript:void(0)" id="' . $datas->attachment . '" onClick="downloadAtt(id)" class="fa fa-paperclip"></a>';
			}
			else{
				return '-';
			}
		})
		->rawColumns([ 'att' => 'att','action' => 'action'])
		->make(true);
	}


	public function exportJobHistory(Request $request)
	{
		if ($request->get('mon') == "") {
			$mon = date('Y-m');
			$mon2 = date('F Y');
		} else {
			$mon = $request->get('mon');
			$mon2 = date('F Y', strtotime($request->get('mon')."-01"));
		}

		$excels = WorkshopLog::leftJoin("employee_syncs", "workshop_logs.operator_id" ,"=", "employee_syncs.employee_id")
		->leftJoin("workshop_processes", "workshop_logs.machine_code", "=", "workshop_processes.machine_code")
		->where(db::raw("DATE_FORMAT(started_at,'%Y-%m')"), "=", $mon)
		->whereIn("workshop_logs.machine_code", array('M_26','M_27','M_28','M_8','M_9','M_10','M_36'))
		->select(db::raw("GROUP_CONCAT(DISTINCT process_name) as ket"), "employee_id", "name", db::raw("CEIL(SUM(TIME_TO_SEC(TIMEDIFF(workshop_logs.created_at,started_at))) / 60 / 450) as hari"))
		->groupBy("name", "employee_id")
		->get();

		$data = array(
			'workshop' => $excels,
			'mon' => $mon2
		);
		ob_clean();
		Excel::create('WJO Job Histories', function($excel) use ($data){
			$excel->sheet('WJO', function($sheet) use ($data) {
				return $sheet->loadView('workshop.wjo_job_excel', $data);
			});
		})->export('xlsx');
	}
}
