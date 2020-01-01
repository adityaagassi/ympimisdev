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
use App\WorkshopMaterial;
use App\EmployeeSync;
use Carbon\Carbon;
use DataTables;
use Response;
use Excel;

class WorkshopController extends Controller{
	public function __construct(){
		$workshop_materials = WorkshopMaterial::orderBy('workshop_materials.material_description', 'asc')
		->get();
		$statuses = db::table('processes')->where('processes.remark', '=', 'workshop')
		->orderBy('process_code', 'asc')
		->get();
		$employees = EmployeeSync::orderBy('employee_id', 'asc')->select('employee_id', 'name', 'section', 'group')->get();

		$this->middleware('auth');
		$this->material = $workshop_materials;
		$this->status = $statuses;
		$this->employee = $employees;
	}

	public function indexCreateWJO(){
		$title = 'WJO Form';
		$title_jp = '??';

		return view('workshop.wjo_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'statuses' => $this->status,
			'employees' => $this->employee,
			'materials' => $this->material,
		))->with('page', 'WJO Form');
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
		))->with('page', 'List WJO');
		
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
			'workshop_job_orders.request_date',
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
			'workshop_job_orders.drawing_number',
			'approver.name as approver_name',
			'pic.name as pic_name'
		)
		->get();


		$data = array(
			'workshop_job_orders' => $workshop_job_orders
		);
		Excel::create('List WJO', function($excel) use ($data){
			$excel->sheet('WJO', function($sheet) use ($data) {
				return $sheet->loadView('workshop.wjo_excel', $data);
			});
		})->download('xlsx');
	}

	public function createWJO(Request $request){
		$prefix_now = 'WJO'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','wjo')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$sub_section = $request->get('sub_section');
		$item_name = $request->get('item_name');
		$quantity = $request->get('quantity');
		$request_date = $request->get('request_date');
		$priority = $request->get('priority');
		$type = $request->get('type');
		$material = $request->get('material');
		$problem_desc = $request->get('problem_desc');

		if($material == 'Lainnya'){
			$material == $request->get('material-other');
		}

		$remark;
		if($priority == 'normal'){
			$remark = 1;
		}else{
			$remark = 0;
		}		

		try {
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
				'quantity' => $quantity,
				'target_date' => $request_date,
				'priority' => $priority,
				'type' => $type,
				'material' => $material,
				'problem_description' => $problem_desc,
				'remark' => $remark,
				'attachment' => $file_name,
				'created_by' => Auth::id(),
				'request_date' => $request_date,

			]);
			$wjo->save();

			if($priority == 'urgent'){
				$data = db::select("select w.*, u.`name` from workshop_job_orders w
					left join users u on w.created_by = u.id
					where order_no = '".$order_no."'");
				Mail::to('aditya.agassi@music.yamaha.com')->send(new SendEmail($data, 'urgent_wjo'));
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

	public function fetchListWJO(Request $request){
		$workshop_job_orders = WorkshopJobOrder::leftJoin(db::raw('(select employee_id, name from employee_syncs) as approver'), 'approver.employee_id', '=', 'workshop_job_orders.approved_by')
		->leftJoin(db::raw('(select employee_id, name from employee_syncs) as pic'), 'pic.employee_id', '=', 'workshop_job_orders.operator')
		->leftJoin(db::raw('(SELECT process_code, process_name FROM processes where remark = "workshop") as processes'), 'processes.process_code', '=', 'workshop_job_orders.remark')
		->orderBy('workshop_job_orders.order_no', 'asc');

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
		if(strlen($request->get('approvedBy')) > 0){
			$workshop_job_orders = $workshop_job_orders->where('workshop_job_orders.approved_by', '=', $request->get('approvedBy'));
		}

		$workshop_job_orders = $workshop_job_orders->select('workshop_job_orders.order_no', 'workshop_job_orders.created_at', 'workshop_job_orders.sub_section', 'workshop_job_orders.order_no', 'approver.name as approver', 'workshop_job_orders.item_name', 'workshop_job_orders.material', 'workshop_job_orders.quantity', 'pic.name as pic', 'workshop_job_orders.difficulty', 'workshop_job_orders.priority', 'workshop_job_orders.target_date', 'workshop_job_orders.finish_date', 'processes.process_name', 'workshop_job_orders.attachment', 'workshop_job_orders.drawing_number');

		$workshop_job_orders = $workshop_job_orders->get();

		$response = array(
			'status' => true,
			'tableData' => $workshop_job_orders,
		);
		return Response::json($response);
	}

}
