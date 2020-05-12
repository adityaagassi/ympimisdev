<?php

namespace App\Http\Controllers;

use Response;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

use App\Mail\SendEmail;
use App\EmployeeSync;
use App\CodeGenerator;
use App\MaintenanceJobOrder;
use App\MaintenanceJobProcess;
use App\Utility;
use App\UtilityCheck;

use App\Http\Controllers\Controller;

use PDF;

class MaintenanceController extends Controller
{
	public function __construct(){
		$this->middleware('auth');

		$this->mt_employee = EmployeeSync::where("department", "=", "Maintenance")
		->whereNull("end_date")
		->whereNotNull("group")
		->select("employee_id", "name")
		->get();
	}

	// -----------------------  START INDEX --------------------

	public function indexMaintenanceForm()
	{
		$title = 'Maintenance Request List';
		$title_jp = '??';

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department', 'section')->first();

		$job_order = MaintenanceJobOrder::where('created_by', '=', Auth::user()->username)
		->select(db::raw('count(if(remark="0", 1, null)) as requested, count(if(remark="1", 1, null)) as verifying, count(if(remark="2", 1, null)) as received, count(if(remark="3", 1, null)) as inProgress, count(if(remark="4", 1, null)) as noPart, count(if(remark="5", 1, null)) as finished, count(if(remark="6", 1, null)) as canceled'))->first();

		return view('maintenance.maintenance_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'requested' => $job_order->requested,
			'verifying' => $job_order->verifying,
			'received' => $job_order->received,
			'inProgress' => $job_order->inProgress,
			'noPart' => $job_order->noPart,
			'finished' => $job_order->finished,
			'canceled' => $job_order->canceled,
		))->with('page', 'Maintenance Form')->with('head', 'Maintenance');	
	}

	public function indexMaintenanceList()
	{
		$title = 'Maintenance Request List';
		$title_jp = '??';

		$statuses = db::table('processes')->where('processes.remark', '=', 'maintenance')
		->orderBy('process_code', 'asc')
		->get();

		$employees = EmployeeSync::whereNotNull('section')
		->whereNotNull('group')
		->select('employee_id', 'name', 'section', 'group')
		->orderBy('employee_id', 'asc')
		->get();

		return view('maintenance.spk_list', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'statuses' => $statuses,
			'employees' => $employees,
			'mt_employees' => $this->mt_employee
		))->with('page', 'Maintenance List')->with('head', 'Maintenance');	
	}

	public function indexSPK()
	{
		$title = 'Maintenance SPK';
		$title_jp = '??';

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
		->select('employee_id', 'name', 'section', 'group')
		->first();

		return view('maintenance.spk', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => $employee->name
		))->with('page', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexApar()
	{
		$title = 'Maintenance APAR Monitoring';
		$title_jp = '??';

		$location = Utility::where('remark', '=', 'APAR')
		->select('group', db::raw('REPLACE(`group`, " ", "_") as group2'))
		->groupBy('group')
		->orderBy('group')
		->get();	

		return view('maintenance.apar.aparMonitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location
		))->with('page', 'APAR')->with('head', 'Maintenance');
	}

	public function indexAparCheck()
	{
		$title = 'Maintenance APAR Check';
		$title_jp = '??';

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
		->select('employee_id', 'name', 'section', 'group')
		->first();

		$check = db::table("utility_check_lists")
		->select('check_point', 'remark')
		->get();
		// $check = "";

		return view('maintenance.apar.aparCheck', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => $employee->name,
			'check_list' => $check
		))->with('page', 'APAR Check')->with('head', 'Maintenance');
	}

	public function indexAparExpire()
	{
		$title = 'Maintenance APAR Quarantine List';
		$subtitle = 'APAR That will be expire';
		$title_jp = '??';

		$check = db::table("utility_check_lists")
		->select('check_point', 'remark')
		->get();

		return view('maintenance.apar.aparExpired', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'subtitle' => $subtitle,
			'check_list' => $check
		))->with('page', 'APAR expired')->with('head', 'Maintenance');
	}

	public function indexAparTool()
	{
		$title = 'Fire Extinguiser List';
		$title_jp = '??';

		$locations = Utility::distinct()->select('group','location')->get();

		$types = Utility::where('type', '<>', '-')->distinct()->select('type')->get();

		return view('maintenance.apar.aparTool', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations,
			'types' => $types
		))->with('page', 'APAR')->with('head', 'Maintenance');
	}

	// -----------------------  END INDEX --------------------

	public function fetchMaintenance(Request $request)
	{
		$emp = Auth::user()->username;

		$datas = MaintenanceJobOrder::leftJoin(db::raw('(SELECT process_code ,process_name from processes where remark = "maintenance") as process'), 'process.process_code', '=', 'maintenance_job_orders.remark')
		->select('id','order_no', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),'priority', 'type','target_date','danger','description','process_name')
		->where('created_by', '=', $emp);

		if ($request->get('status') != 'all') {
			$datas = $datas->where('remark', '=', $request->get('status'));
		}

		$datas = $datas->orderBy('created_at','desc')->get();

		$response = array(
			'status' => false,
			'datas' => $datas
		);
		return Response::json($response);
	}

	public function fetchSPK()
	{
		$spk = MaintenanceJobProcess::leftJoin("maintenance_job_orders", "maintenance_job_orders.order_no", "=", "maintenance_job_processes.order_no")
		->where("operator_id", "=", Auth::user()->username)
		->where("maintenance_job_orders.remark", "=", 3)
		->where("maintenance_job_processes.remark", "=", "persiapan")
		->select("maintenance_job_orders.order_no", "section", "priority", "type", "category", "machine_condition", "danger", "description", "target_date", "safety_note", "start_plan", "finish_plan")
		->get();

		$response = array(
			'status' => false,
			'datas' => $spk
		);
		return Response::json($response);
	}

	public function createSPK(Request $request)
	{
		$date = date('Y-m-d');
		$prefix_now = 'SPK'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','spk')->first();

		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$tanggal = $request->get('tanggal');
		$bagian = $request->get('bagian');
		$prioritas = $request->get('prioritas');
		$jenis_pekerjaan = $request->get('jenis_pekerjaan');
		$kategori = $request->get('kategori');
		$kondisi_mesin = $request->get('kondisi_mesin');
		$bahaya = $request->get('bahaya');
		$detail = $request->get('detail');

		if ($prioritas == "Urgent") {
			$target = $request->get('target');
		} else {
			$target = date("Y-m-d", strtotime('+ 7 days'));
		}

		$safety = $request->get('safety');


		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$order_no = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		if($prioritas == 'Urgent'){
			$remark = 0;
		} else {
			$remark = 2;
		}

		$spk = new MaintenanceJobOrder([
			'order_no' => $order_no,
			'section' => $bagian,
			'priority' => $prioritas,
			'type' => $jenis_pekerjaan,
			'category' => $kategori,
			'machine_condition' => $kondisi_mesin,
			'danger' => $bahaya,
			'description' => $detail,
			'target_date' => $target,
			'safety_note' => $safety,
			'remark' => $remark,
			'created_by' => Auth::user()->username,
		]);

		try {
			$spk->save();

			if($prioritas == 'Urgent'){
				$data = db::select("select spk.*, u.`name` from maintenance_job_orders spk
					left join employee_syncs u on spk.created_by = u.employee_id
					where order_no = '".$order_no."'");
				$remark = 0;

				Mail::to('susilo.basri@music.yamaha.com')
				->bcc(['aditya.agassi@music.yamaha.com', 'darma.bagus@music.yamaha.com'])
				->send(new SendEmail($data, 'urgent_spk'));
			}

			$response = array(
				'status' => true,
				'message' => "Pembuatan SPK berhasil",
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

	public function fetchMaintenanceList(Request $request)
	{
		DB::connection()->enableQueryLog();
		$maintenance_job_orders = MaintenanceJobOrder::leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin(db::raw("employee_syncs AS es"), "es.employee_id", "=", "maintenance_job_orders.approved_by")
		->leftJoin("maintenance_job_processes", "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
		->select("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "machine_condition", "danger", "target_date", db::raw("employee_syncs.`name` as requester"), db::raw("es.`name` as approval"), db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as date'), "process_name", "maintenance_job_orders.remark", "maintenance_job_orders.category", db::raw("GROUP_CONCAT(maintenance_job_processes.operator_id) AS operator_id"));

		if(strlen($request->get('reqFrom')) > 0 ){
			$reqFrom = date('Y-m-d', strtotime($request->get('reqFrom')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $reqFrom);
		}
		if(strlen($request->get('reqTo')) > 0 ){
			$reqTo = date('Y-m-d', strtotime($request->get('reqTo')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $reqTo);
		}
		if(strlen($request->get('targetFrom')) > 0 ){
			$targetFrom = date('Y-m-d', strtotime($request->get('targetFrom')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $targetFrom);
		}
		if(strlen($request->get('targetTo')) > 0 ){
			$targetTo = date('Y-m-d', strtotime($request->get('targetTo')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $targetTo);
		}
		if(strlen($request->get('finFrom')) > 0 ){
			$finFrom = date('Y-m-d', strtotime($request->get('finFrom')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '>=', $finFrom);
		}
		if(strlen($request->get('finTo')) > 0 ){
			$finTo = date('Y-m-d', strtotime($request->get('finTo')));
			$maintenance_job_orders = $maintenance_job_orders->where(db::raw('date(maintenance_job_orders.created_at)'), '<=', $finTo);
		}
		if(strlen($request->get('orderNo')) > 0 ){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.order_no', '=', $request->get('orderNo'));
		}
		if(strlen($request->get('section')) > 0 ){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.section', '=', $request->get('section'));
		}
		if(strlen($request->get('priority')) > 0 ){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.priority', '=', $request->get('priority'));
		}
		if(strlen($request->get('workType')) > 0 ){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.type', '=', $request->get('workType'));
		}
		if(strlen($request->get('remark')) > 0){
			if($request->get('remark') != 'all'){
				$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.remark', '=', $request->get('remark'));
			}
		}else{
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.remark', '=', 2);
		}
		if(strlen($request->get('approvedBy')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.approved_by', '=', $request->get('approvedBy'));
		}
		if(strlen($request->get('username')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.created_by', '=', $request->get('username'));
		}

		$maintenance_job_orders = $maintenance_job_orders->groupBy("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "machine_condition", "danger", "target_date", "employee_syncs.name", "es.name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d")'), "process_name", "maintenance_job_orders.remark", "maintenance_job_orders.category")
		->orderBy('maintenance_job_orders.created_at', 'desc')
		->get();

		$response = array(
			'status' => true,
			'tableData' => $maintenance_job_orders,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function fetchMaintenanceDetail(Request $request)
	{
		$detail = MaintenanceJobOrder::where("order_no", "=", $request->get('order_no'))
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
		->select("order_no", "name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as date'), "priority", "maintenance_job_orders.section", "type", "category", "machine_condition", "danger", "description", "safety_note","target_date")
		->first();

		$response = array(
			'status' => true,
			'detail' => $detail
		);
		return Response::json($response);
	}

	public function postMemberSPK(Request $request)
	{
		$order_no = $request->get("order_no");
		$member = $request->get("member");
		$datas = [];

		foreach ($member as $mbr) {
			array_push($datas, [
				'order_no' => $order_no,
				'operator_id' => $mbr['operator'],
				'start_plan' => $mbr['start_date']." ".$mbr['start_time'],
				'finish_plan' => $mbr['finish_date']." ".$mbr['finish_time'],
				'remark' => 'persiapan',
				'created_by' => Auth::user()->username
			]);
		};

		MaintenanceJobProcess::insert($datas);

		MaintenanceJobOrder::where('order_no', '=', $order_no)
		->update(['remark' => 3]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function fetchAparList(Request $request)
	{
		DB::connection()->enableQueryLog();

		$apars = Utility::select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date2'), 'exp_date', db::raw("(MONTH(exp_date) - MONTH(now())) as age_left"), 'remark', 'last_check');

		if ($request->get('type')) {
			$apars = $apars->where('remark', '=', $request->get('type'));
		}

		if ($request->get('area')) {
			$apars = $apars->where('location', '=', $request->get('area'));
		}

		if ($request->get('location')) {
			$apars = $apars->where('group', '=', $request->get('location'));
		}

		if ($request->get('expMon')) {
			$apars = $apars->where(db::raw('DATE_FORMAT(exp_date,"%m-%Y")'), '=', $request->get('expMon'));
		}

		if ($request->get('order')) {
			$apars = $apars->orderBy($request->get('order'), $request->get('order2'));
		}

		$apars = $apars->get();

		$response = array(
			'status' => true,
			'apar' => $apars,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function fetchAparCheck2(Request $request)
	{
		if ($request->get('mon_check') == "") {
			$mon_check = Date('m-Y');
		} else {
			$mon_check = $request->get('mon_check');
		}

		$apars = Utility::whereRaw('DATE_FORMAT(last_check, "%m-%Y") ="'.$mon_check.'"')
		->select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date'), db::raw("(MONTH(exp_date) - MONTH(now())) as age_left"), 'remark', 'last_check')->get();

		$response = array(
			'status' => true,
			'apar' => $apars
		);
		return Response::json($response);
	}

	public function fetchAparCheck(Request $request)
	{
		$checks = Utility::leftJoin('utility_checks', 'utilities.id', '=', 'utility_checks.utility_id')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'utility_checks.created_by')
		->where('utilities.id', '=', $request->get('utility_id'))
		->select('utility_id', 'check', db::raw('DATE_FORMAT(check_date,"%d %M %Y") check_date2'), 'utility_checks.remark', 'utility_checks.created_by', 'employee_syncs.name', db::raw('utilities.remark as remark2'))
		->orderBy('check_date', 'desc')
		->limit(5)
		->get();

		$response = array(
			'status' => true,
			'check' => $checks
		);
		return Response::json($response);
	}

	public function postCheck(Request $request)
	{
		try {
			$utl_check = new UtilityCheck;

			Utility::where('id', $request->get('utility_id'))
			->update(['last_check' => date('Y-m-d H:i:s')]);

			$utl_check->utility_id = $request->get('utility_id');

			$check = "";
			$arrCheck = $request->get('check');

			foreach ($arrCheck as $cek) {
				$check .= $cek.",";
			}
			$check = rtrim($check, ",");

			if (strpos($check, '0') !== false) {
				Utility::where('id', $request->get('utility_id'))
				->update(['status' => 'NG']);
			} else {
				$utl_check->remark = 'OK';

				$utl_check2 = new UtilityCheck;
				$utl_check2::where('utility_id', $request->get('utility_id'))->whereNull('remark')->update(array('remark' => 'OK'));

				Utility::where('id', $request->get('utility_id'))
				->update(['status' => null]);
			}

			$utl_check->check = $check;
			$utl_check->check_date = date('Y-m-d H:i:s');
			$utl_check->created_by = Auth::user()->username;

			$utl_check->save();

			// GET DATA LAST CHECK
			$last_check_tool = Utility::where('id', $request->get('utility_id'))
			->select('utility_code', 'utility_name', 'exp_date', 'status', db::raw('DATE_FORMAT(last_check, "%d-%m-%Y") last_check'))
			->first();

			// $this->printApar($last_check_tool);

			$response = array(
				'status' => true,
				'cek' => $check,
				'checked_apar' => $last_check_tool
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

	public function fetchAparExpire()
	{
		$exp = Utility::where('remark', '=', 'APAR')
		->where(db::raw('(MONTH(exp_date) - MONTH(now()))'), '<=', '2')
		->select('id', 'utility_code', 'utility_name', 'exp_date', 'group', 'location', 'last_check', db::raw('(MONTH(exp_date) - MONTH(now())) as exp'), 'capacity')
		->orderBy('exp_date')
		->get();

		$check_by_operator = Utility::leftJoin('utility_checks', 'utility_checks.utility_id', '=', 'utilities.id')
		->where('status', '=', 'NG')
		->whereNull('utility_checks.remark')
		->select('utilities.id', 'utility_code', 'utility_name', 'group', 'location', 'utilities.remark', 'last_check', 'check')
		->orderBy('last_check')
		->get();

		$response = array(
			'status' => true,
			'expired_list' => $exp,
			'operator_check' => $check_by_operator
		);
		return Response::json($response);
	}

	public function createTool(Request $request)
	{
		$type = $request->get('extinguisher_type');
		$exp = $request->get('extinguisher_exp');

		if ($request->get('extinguisher_type') == "") {
			$type = "-";
		}

		if ($request->get('extinguisher_exp') == "") {
			$exp = null;
		}

		$utl = new Utility;
		$utl->utility_code = $request->get('extinguisher_id');
		$utl->utility_name = $request->get('extinguisher_name');
		$utl->type = $type;
		$utl->group = $request->get('extinguisher_location2');
		$utl->capacity = $request->get('extinguisher_capacity');
		$utl->location = $request->get('extinguisher_location1');
		$utl->remark = $request->get('extinguisher_category');
		$utl->exp_date = $exp;
		$utl->last_check = date('Y-m-d H:i:s');
		$utl->created_by = Auth::user()->username;

		$utl->save();

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function replaceTool(Request $request)
	{
		$cat = db::table('utility_check_lists')->where('remark', '=', 'APAR')->select('check_point')->get();

		$utl = Utility::where('remark', '=', 'APAR')
		->where('utility_code', '=', $request->get('code'))
		->first();

		Utility::where('remark', '=', 'APAR')
		->where('utility_code', '=', $request->get('code'))
		->update([
			'capacity' => $request->get('capacity'), 
			'exp_date' => $request->exp, 
			'last_check' => date('Y-m-d H:i:s'),
			'status' => null
		]);

		$utl_check2 = new UtilityCheck;
		$utl_check2::where('utility_id', $utl->id)->whereNull('remark')->update(array('remark' => 'OK'));

		$utl_check = new UtilityCheck;
		$utl_check->utility_id = $utl->id;

		$cek = "";
		foreach ($cat as $c) {
			$cek .= "1,";
		}
		$cek = rtrim($cek, ",");

		$utl_check->check = $cek;
		$utl_check->check_date = date('Y-m-d H:i:s');
		$utl_check->remark = "OK";
		$utl_check->created_by = Auth::user()->username;

		$utl_check->save();

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	// public function fetchAparbyCode(Request $request)
	// {
	// 	$utl = Utility::where('remark', '=', 'APAR')
	// 	->where('utility_code', '=', $request->get('utility_code'))
	// 	->first();

	// 	$response = array(
	// 		'status' => true,
	// 		'data' => $utl
	// 	);
	// 	return Response::json($response);
	// }

	// public function printApar($apar){
	// 	$printer_name = 'TESTPRINTER';
	// 	$connector = new WindowsPrintConnector($printer_name);
	// 	$printer = new Printer($connector);

	// 	$utility_code = $apar->utility_code;
	// 	$utility_name = $apar->utility_name;
	// 	$expired_date = $apar->exp_date;

	// 	$qr = $utility_code."/".$utility_name;

	// 	if (is_null($apar->status)) {
	// 		$status = "BAIK";
	// 	} else {
	// 		$status = "KURANG";
	// 	}

	// 	$last_check = $apar->last_check;

	// 	$printer->setJustification(Printer::JUSTIFY_CENTER);
	// 	$printer->setEmphasis(true);
	// 	$printer->setReverseColors(true);
	// 	$printer->setTextSize(2, 1);
	// 	$printer->text("  APAR  "."\n");
	// 	$printer->initialize();
	// 	$printer->setTextSize(2, 1);
	// 	$printer->setJustification(Printer::JUSTIFY_CENTER);
	// 	$printer->text($utility_code."\n");
	// 	$printer->text($utility_name."\n");
	// 	$printer->qrCode($qr, Printer::QR_ECLEVEL_L, 5, Printer::QR_MODEL_2);
	// 	$printer->initialize();
	// 	$printer->setEmphasis(true);
	// 	$printer->setTextSize(1, 1);
	// 	$printer->setJustification(Printer::JUSTIFY_CENTER);
	// 	$printer->text("Exp.  ".$expired_date."\n");
	// 	$printer->text("Last Check : ".$last_check." (".$status.") \n");
	// 	$printer->feed(1);
	// 	$printer->cut();
	// 	$printer->close();
	// }

	public function print_apar2($apar_id, $apar_name, $exp_date, $last_check, $hasil_check)
	{
		$data = [
			'apar_code' => $apar_id,
			'apar_name' => $apar_name,
			'exp_date' => $exp_date,
			'last_check' => $last_check,
			'status' => $hasil_check,
		];
		
		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper([0, 0, 141.732, 184.252], 'landscape');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('maintenance.apar.aparPrint', array(
			'data' => $data
		));

		// return $pdf->download("APAR_QR.pdf");

		// $pdf->save(public_path() . "/APAR_QR.pdf");

		return $pdf->stream("APAR_QR.pdf");
	}

	public function fetch_apar_monitoring(Request $request)
	{
		if ($request->get('mon') % 2 === 0) {
			$loc = "Factory I";
		} else if ($request->get('mon') % 2 === 1){
			$loc = "Factory II";
		}

		$check = Utility::where("location", "=", $loc)
		->where("remark", "=", "APAR")
		->select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date2'), 'exp_date', 'remark', 'last_check', db::raw('IF(IFNULL(MONTH(last_check), 0) >= '.$request->get('mon').', 1, 0) as cek'), db::raw('IFNULL(FLOOR((DayOfMonth(last_check)-1)/7)+1, FLOOR((DayOfMonth(exp_date)-1)/7)+1) AS `week`'))
		->orderBy("cek", "ASC")
		->orderBy("week", "ASC")
		->get();

		$response = array(
			'status' => true,
			'check_list' => $check,
		);
		return Response::json($response);
	}

	public function fetch_hydrant_monitoring(Request $request)
	{
		DB::connection()->enableQueryLog();
		
		if ($request->get('mon') % 2 === 0) {
			$loc = "Factory I";
		} else if ($request->get('mon') % 2 === 1){
			$loc = "Factory II";
		}

		$check = Utility::where("location", "=", $loc)
		->where("remark", "=", "HYDRANT")
		->select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date2'), 'exp_date', 'remark', 'last_check', db::raw('IF(IFNULL(MONTH(last_check), 0) >= '.$request->get('mon').', 1, 0) as cek'), db::raw('IFNULL(FLOOR((DayOfMonth(last_check)-1)/7)+1, FLOOR((DayOfMonth("2020-01-01")-1)/7)+1) AS `week`'))
		->orderBy("cek", "ASC")
		->orderBy("week", "ASC")
		->get();

		$response = array(
			'status' => true,
			'check_list' => $check,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}
}
