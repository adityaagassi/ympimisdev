<?php

namespace App\Http\Controllers;

use Response;
use Config;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Mail\SendEmail;
use App\EmployeeSync;
use App\Employee;
use App\CodeGenerator;
use App\MaintenanceJobOrder;
use App\MaintenanceJobOrderLog;
use App\MaintenanceJobProcess;
use App\MaintenanceJobReport;
use App\Utility;
use App\UtilityCheck;
use App\UtilityUse;
use App\UtilityOrder;
use App\MaintenanceInventory;
use App\MaintenanceInventoryLog;
use App\MaintenanceJobSparepart;
use App\MaintenancePlan;
use App\MaintenanceJobPending;
use App\MaintenancePlanItem;
use App\MaintenancePlanItemCheck;
use App\MaintenancePlanCheck;
use App\MaintenancePic;
use App\Process;

use App\Http\Controllers\Controller;

use PDF;
use Excel;

class MaintenanceController extends Controller
{
	public function __construct(){
		// $this->middleware('auth');

		$this->mt_employee = EmployeeSync::where("department", "like", "%Maintenance%")
		->whereNull("end_date")
		->whereNotNull("group")
		->select("employee_id", db::raw("SUBSTRING_INDEX(`name`,' ',3) as name"))
		->orderBy('hire_date', 'desc')
		->get();

		$this->apar_type = [
			['type' => 'powder', 'valid' => 3],
			['type' => 'liquid', 'valid' => 5],
			['type' => 'CO2', 'valid' => 5],
			['type' => 'foam', 'valid' => 3]
		];

		$this->uom = ['Pcs'];


		$this->inv_ctg = MaintenanceInventory::select("category")
		->groupBy('category')
		->orderBy('category', 'asc')
		->get();

		$this->inv_rack = MaintenanceInventory::select("location")
		->groupBy('location')
		->orderBy('location', 'asc')
		->get();

		$this->spk_category = [
			['category' => 'utility', 'value' => 'Listrik'],
			['category' => 'utility', 'value' => 'Jaringan'],
			['category' => 'utility', 'value' => 'Mesin Utilitas'],
			['category' => 'utility', 'value' => 'Utilitas Umum'],
			['category' => 'Mesin Produksi', 'value' => 'Kelistrikan Mesin'],
			['category' => 'Mesin Produksi', 'value' => 'Mekanis Mesin'],
			['category' => 'Mesin Produksi', 'value' => 'Otomatisasi Mesin'],
			['category' => 'Informasi', 'value' => 'Informasi']
		];
	}

	// -----------------------  START INDEX --------------------

	public function indexMaintenanceForm()
	{
		$title = 'Maintenance Request List';
		$title_jp = '作業依頼書リスト';

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department', 'section')->first();

		$job_order = MaintenanceJobOrder::where('created_by', '=', Auth::user()->username)
		->select(db::raw('count(if(remark="0", 1, null)) as requested, count(if(remark="1", 1, null)) as verifying, count(if(remark="2", 1, null)) as received, count(if(remark="3", 1, null)) as listed, count(if(remark="4", 1, null)) as inProgress, count(if(remark="5", 1, null)) as pending, count(if(remark="6", 1, null)) as finished, count(if(remark="7", 1, null)) as canceled'))->first();

		return view('maintenance.maintenance_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'requested' => $job_order->requested,
			'verifying' => $job_order->verifying,
			'received' => $job_order->received,
			'listed' => $job_order->listed,
			'inProgress' => $job_order->inProgress,
			'pending' => $job_order->pending,
			'finished' => $job_order->finished,
			'canceled' => $job_order->canceled,
		))->with('page', 'Maintenance Form')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexMaintenanceList()
	{
		$title = 'Maintenance Request List';
		$title_jp = '作業依頼書リスト';

		$statuses = Process::where('processes.remark', '=', 'maintenance')
		->orderBy('process_code', 'asc')
		->get();

		$employees = EmployeeSync::whereNotNull('section')
		->whereNotNull('group')
		->select('employee_id', 'name', 'section', 'group')
		->orderBy('employee_id', 'asc')
		->get();

		$keys = [];
		foreach($this->spk_category as $row) {
			foreach($row as $key) {
				if (!in_array($row['category'], $keys)) {
					array_push($keys, $row['category']);
				}
			}
		}

		return view('maintenance.spk_list', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'statuses' => $statuses,
			'employees' => $employees,
			'mt_employees' => $this->mt_employee,
			'category' => $keys
		))->with('page', 'Maintenance List')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexSPK()
	{
		$title = 'SPK Execution';
		$title_jp = '作業依頼書の実行';

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
		->select('employee_id', 'name', 'section', 'group')
		->first();

		return view('maintenance.spk', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => $employee->name
		))->with('page', 'SPK')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexDangerNote($order_no)
	{
		$title = 'Verifying SPK';
		$title_jp = '';

		$spk = MaintenanceJobOrder::where('order_no', '=', $order_no)
		->select('type', 'category', 'machine_condition', 'danger', 'description', 'remark')
		->first();

		// if ($spk->remark == "2") {
		// 	$message = 'SPK dengan Order No. '.$order_no;
		// 	$message2 ='Sudah diverifikasi';
		// 	$stat = 0;
		// } else {
		$message = 'Untuk melakukan verifikasi SPK ini,';
		$message2 = 'Tambahkan catatan bahaya pada kolom dibawah ini :';
		$stat = 1;
		// }


		return view('maintenance.spk_danger_message', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'head' => $order_no,
			'data' => $spk,
			'message' => $message,
			'message2' => $message2,
			'status' => $stat
		))->with('page', 'verifying SPK');
	}

	public function indexMaintenanceMonitoring()
	{
		$title = 'Maintenance SPK Monitoring';
		$title_jp = '';

		return view('maintenance.maintenance_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Maintenance Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexOperatorMonitoring()
	{
		$title = 'Operator SPK Monitoring';
		$title_jp = '';

		return view('maintenance.operator_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'op' => $this->mt_employee
		))->with('page', 'Operator Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexSPKGrafik()
	{
		$title = 'Maintenance SPK Monitoring';
		$title_jp = '作業依頼書の管理';

		return view('maintenance.spk_grafik', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'SPK Monitoring')->with('head2', 'SPK')->with('head', 'Maintenance');	
	}

	public function indexApar()
	{
		$title = 'APAR Check Schedule';
		$title_jp = '消火器・消火栓の点検日程';

		$location = Utility::where('remark', '=', 'APAR')
		->select('group', db::raw('REPLACE(`group`, " ", "_") as group2'))
		->groupBy('group')
		->orderBy('group')
		->get();	

		return view('maintenance.apar.aparMonitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location
		))->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparCheck()
	{
		$title = 'Utility Check';
		$title_jp = 'ユーティリティーチェック';

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
		->select('employee_id', 'name', 'section', 'group')
		->first();

		$check = db::table("utility_check_lists")
		->select('check_point', 'remark', 'synonim')
		->get();
		// $check = "";

		return view('maintenance.apar.aparCheck', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => $employee->name,
			'check_list' => $check
		))->with('page', 'APAR Check')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparExpire()
	{
		$title = 'APAR Expired List';
		$subtitle = 'APAR That will be expire';
		$title_jp = '消火器・消火栓の使用期限一覧';

		$check = db::table("utility_check_lists")
		->select('check_point', 'remark')
		->get();

		return view('maintenance.apar.aparExpired', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'subtitle' => $subtitle,
			'check_list' => $check
		))->with('page', 'APAR expired')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparOrderList()
	{
		$title = 'APAR Order List';
		$title_jp = '消火器・消火栓の発注一覧';

		if (Auth::user()->email == "priyo.jatmiko@music.yamaha.com" || Auth::user()->role_code == "MIS" || Auth::user()->email == "bambang.supriyadi@music.yamaha.com") {
			return view('maintenance.apar.aparOrderList', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'APAR order')->with('head2', 'Utility')->with('head', 'Maintenance');
		} else {
			return redirect()->route('login');
		}	
	}

	public function indexAparTool()
	{
		$title = 'Fire Extinguiser List';
		$title_jp = 'ユーティリティー';

		$locations = Utility::distinct()->select('group','location')->get();

		$types = Utility::where('type', '<>', '-')->distinct()->select('type')->get();

		return view('maintenance.apar.aparTool', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations,
			'types' => $types
		))->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparResume()
	{
		$title = 'Fire Extinguiser Resume';
		$title_jp = '??';

		return view('maintenance.apar.aparResume', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'APAR')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparUses()
	{
		$title = 'Fire Extinguiser Uses';
		$title_jp = '??';

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
		->select('employee_id', 'name', 'section', 'group')
		->first();

		return view('maintenance.apar.aparUses', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => $employee->name,
		))->with('page', 'APAR Uses')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparNG()
	{
		$title = 'Not Good APAR Check';
		$title_jp = '??';

		$check = db::table("utility_check_lists")
		->select('check_point', 'remark')
		->get();

		return view('maintenance.apar.aparNGList', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'check_list' => $check
		))->with('page', 'APAR NG')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexAparMap()
	{
		$title = 'APAR MAP';
		$title_jp = '??';

		return view('maintenance.apar.aparMap', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'APAR MAP')->with('head2', 'Utility')->with('head', 'Maintenance');
	}

	public function indexInventory()
	{
		$title = 'Maintenance Spare Part Inventories';
		$title_jp = '??';

		if($user = Auth::user())
		{
			if (Auth::user()->role_code == "MIS" || strtoupper(Auth::user()->username) == "PI2003013") {
				$permission = 1;
			} else {
				$permission = 0;
			}
		} else {
			$permission = 0;
		}

		

		$op_mtc = EmployeeSync::leftJoin('employees', 'employees.employee_id', '=', 'employee_syncs.employee_id')
		->whereRaw('(department = "Maintenance Department" OR department = "Management Information System Department")')
		->whereNull('employee_syncs.end_date')
		->select('tag', 'employee_syncs.name', 'employee_syncs.employee_id')
		->get();

		return view('maintenance.inventory', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'uom_list' => $this->uom,
			'category_list' => $this->inv_ctg,
			'rack_list' => $this->inv_rack,
			'permission' => $permission,
			'op_mtc' => $op_mtc,
		))->with('page', 'Spare Part')->with('head', 'Maintenance');
	}

	public function indexInventoryTransaction($stat)
	{
		$title = 'Maintenance Inventories Transaction';
		$title_jp = '??';

		if (Auth::user()->role_code == "MIS" || strtoupper(Auth::user()->username) == "PI2003013") {
			$permission = 1;
		} else {
			$permission = 0;
		}

		return view('maintenance.inventory_transaction', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'permission' => $permission
		))->with('page', 'Spare Part Transaction')->with('head', 'Maintenance');
	}

	public function indexPlannedMonitoring()
	{
		$title = 'Planned Maintenance Monitoring';
		$title_jp = '??';

		// $week = db::table("weekly_calendars")->select("week_name")
		// ->whereRaw("DATE_FORMAT(week_date, '%Y-%m') = '".$tgl."'")->groupBy("week_name")->get();

		return view('maintenance.planned.maintenance_plan_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Planned Maintenance Monitoring')->with('head', 'Maintenance');
	}

	public function indexPlannedSchedule()
	{
		$title = 'Planned Maintenance Schedule';
		$title_jp = '??';

		return view('maintenance.planned.maintenance_plan_schedule', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Planned Maintenance Schedule')->with('head', 'Maintenance');
	}

	public function indexPlanMaster()
	{
		$title = 'Planned Maintenance Data';
		$title_jp = '??';

		return view('maintenance.planned.maintenance_plan', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
	}

	public function indexPlannedForm()
	{
		$title = 'Planned Maintenance';
		$title_jp = '??';

		$item_check = MaintenancePlanItem::select('machine_id', 'machine_name', 'description', 'category', 'area')->get();

		$op_mtc = EmployeeSync::where('department', '=', 'Maintenance')->whereNull('end_date')->select('employee_id', 'name')->orderBy('name','asc')->get();

		return view('maintenance.planned.maintenance_plan_form', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'item_check' => $item_check,
			'mtc_op' => $op_mtc
		))->with('page', 'Planned Maintenance Form')->with('head', 'Maintenance');
	}

	public function indexPIC($cat)
	{
		$title = 'Maintenance PICs';
		$title_jp = '??';

		return view('maintenance.maintenance_pic', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'Maintenance');
	}

	public function indexSPKUrgent()
	{
		$title = 'Urgent Maintenance SPK';
		$title_jp = '??';

		$data = db::select("SELECT mjo.order_no, department_shortname as department, priority, description, date(mjo.created_at) as req_date, start_actual, process_name from maintenance_job_orders as mjo
			left join departments on departments.department_name = SUBSTRING_INDEX(mjo.section,'_',1)
			left join (select process_code, process_name from processes where remark = 'Maintenance') as prs on prs.process_code = mjo.remark
			left join maintenance_job_processes as mjp on mjo.order_no = mjp.order_no
			where priority = 'Urgent' and mjo.remark <> 6");

		return view('maintenance.maintenance_urgent', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'datas' => $data
		))->with('head', 'Maintenance');
	}

	public function indexSPKUrgentReport()
	{
		$title = 'Urgent Maintenance SPK Monitoring';
		$title_jp = '??';

		return view('maintenance.maintenance_urgent_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page','Urgent Maintenance SPK Monitoring')->with('head', 'Maintenance');
	}

	public function indexSPKWeekly()
	{
		$title = 'Maintenance SPK Weekly Report';
		$title_jp = '??';

		return view('maintenance.maintenance_spk_weekly', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page','Maintenance SPK Weekly Report')->with('head', 'Maintenance');
	}

	// -----------------------  END INDEX --------------------

	public function fetchMaintenance(Request $request)
	{
		$emp = Auth::user()->username;

		$datas = MaintenanceJobOrder::leftJoin(db::raw('(SELECT process_code ,process_name from processes where remark = "maintenance") as process'), 'process.process_code', '=', 'maintenance_job_orders.remark')
		->select('id','order_no', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),'priority', 'type','target_date','description','process_name', 'remark')
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
		DB::connection()->enableQueryLog();
		$spk = MaintenanceJobProcess::leftJoin("maintenance_job_orders", "maintenance_job_orders.order_no", "=", "maintenance_job_processes.order_no")
		->leftJoin(db::raw('(select * from processes where remark = "maintenance") as prc'), 'prc.process_code', '=', 'maintenance_job_orders.remark')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_orders.created_by')
		->leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_id', '=', 'maintenance_job_orders.machine_name')
		->where("operator_id", "=", Auth::user()->username)
		->whereNull('maintenance_job_processes.deleted_at')
		->whereNull('maintenance_job_orders.deleted_at')
		// ->whereNull('maintenance_job_processes.finish_actual')
		->whereRaw("maintenance_job_orders.remark in (3,4,5,9)")
		->select("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "target_date", "safety_note", "start_plan", "finish_plan", "start_actual", "finish_actual", db::raw("DATE_FORMAT(maintenance_job_orders.created_at,'%d-%m-%Y') as request_date"), 'name', "maintenance_job_orders.remark", "process_name", db::raw("maintenance_job_processes.remark as stat"), db::raw('maintenance_plan_items.description as machine_desc'), 'att', 'machine_remark')
		->orderBy("maintenance_job_orders.remark", "asc")
		->get();

		$op_list = MaintenanceJobOrder::join('maintenance_job_processes', 'maintenance_job_processes.order_no', '=', 'maintenance_job_orders.order_no')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_processes.operator_id')
		->select('maintenance_job_orders.order_no', db::raw('GROUP_CONCAT(SUBSTRING_INDEX(`name`," ",2)) as op_name'))
		->groupBy('maintenance_job_orders.order_no')
		->get();

		// $spk = MaintenanceJobOrder::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_orders.created_by')
		// ->whereIn('remark', [2,4])
		// ->select('order_no', 'maintenance_job_orders.section', 'priority', 'type', 'description', 'category', 'target_date', 'safety_note', db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as request_date'), 'employee_syncs.name')
		// ->get();

		// $proc_log = MaintenanceJobOrder::where('maintenance_job_orders.remark', '=', '4')
		// ->leftJoin('maintenance_job_processes', 'maintenance_job_orders.order_no', '=', 'maintenance_job_processes.order_no')
		// ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_job_processes.operator_id')
		// ->select('maintenance_job_orders.order_no', 'operator_id','name', 'start_actual', 'finish_actual')
		// ->get();

		// $spk = db::select('SELECT * FROM(SELECT maintenance_job_orders.order_no, section, priority, type, description, target_date, safety_note, DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as request_date, start_actual from maintenance_job_orders 
		// 	left join maintenance_job_processes on maintenance_job_orders.order_no = maintenance_job_processes.order_no
		// 	where maintenance_job_orders.remark = 4 and operator_id = "'.Auth::user()->username.'"
		// 	union all
		// 	SELECT order_no, section, priority, type, description, target_date, safety_note, DATE_FORMAT(created_at, "%Y-%m-%d") as request_date, null as start_actual FROM maintenance_job_orders
		// 	where remark = 2) as dd');

		$response = array(
			'status' => false,
			'datas' => $spk,
			'op_list' => $op_list,
			'query' => DB::getQueryLog()
			// 'proses_log' => $proc_log
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
		$bahaya = implode(", ", $request->get('bahaya'));
		$detail = str_replace('\n', ' ', $request->get('detail'));
		$machine_name = $request->get('nama_mesin');
		$machine_detail = $request->get('nama_mesin_detail');
		$reason_urgent = $request->get('reason_urgent');
		
		if (count($request->file('lampiran')) > 0) {
			$num = 1;
			$file = $request->file('lampiran');

			$nama = $file->getClientOriginalName();

			$filename = pathinfo($nama, PATHINFO_FILENAME);
			$extension = pathinfo($nama, PATHINFO_EXTENSION);

			$att = $filename.'_'.date('YmdHis').$num.'.'.$extension;

			$file->move('maintenance/spk_att/', $att);

		} else {
			$att = null;
		}

		// if ($prioritas == "Urgent") {
		$target_time = $request->get('jam_target');

		$hour = sprintf('%02d', explode(':', $target_time)[0]);
		$minute = explode(':', $target_time)[1];

		$target = $request->get('target')." ".$hour.":".$minute.":00";
		// } else {
		// 	$target = date("Y-m-d H:i:s", strtotime('+ 7 days'));
		// }

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
			'machine_name' => $machine_name,
			'machine_remark' => $machine_detail,
			'machine_condition' => $kondisi_mesin,
			'danger' => $bahaya,
			'description' => $detail,
			'target_date' => $target,
			'safety_note' => $safety,
			'remark' => $remark,
			'note' => $reason_urgent,
			'att' => $att,
			'created_by' => Auth::user()->username,
		]);

		$spk_log = new MaintenanceJobOrderLog([
			'order_no' => $order_no,
			'remark' => $remark,
			'created_by' => Auth::user()->username,
		]);

		try {

			DB::transaction(function() use ($spk, $spk_log){
				$spk->save();
				$spk_log->save();
			});	

			$data = db::select("select spk.*, u.`name` from maintenance_job_orders spk
				left join employee_syncs u on spk.created_by = u.employee_id
				where order_no = '".$order_no."'");


			if($prioritas == 'Urgent'){
				$remark = 0;

				// if (date('W') % 2 == 0) {
				// 	// $id = 'PI0004007';
				// 	$id = 'PI1910003';

				// } else {
				// 	// $id = 'PI0805001';
				// 	$id = 'PI1910003';

				// }

				$ids = ['PI0004007', 'PI0805001'];
				// $ids = ['PI2002021', 'PI1910003'];

				$phones = EmployeeSync::select('phone')->whereIn('employee_id', $ids)->get();
				$phone_log = [];

				foreach ($phones as $phone) {
					$new_phone = substr($phone->phone, 1, 15);
					$new_phone = '62'.$new_phone;

					// array_push($phone_log, $new_phone);
					$query_string = "api.aspx?apiusername=API3Y9RTZ5R6Y&apipassword=API3Y9RTZ5R6Y3Y9RT";
					$query_string .= "&senderid=".rawurlencode("PT YMPI")."&mobileno=".rawurlencode($new_phone);
					$query_string .= "&message=".rawurlencode(stripslashes("Ada SPK Urgent Dari ".$data[0]->name.", Mohon segera cek MIRAI > SPK List. Terimakasih")) . "&languagetype=1";
					$url = "http://gateway.onewaysms.co.id:10002/".$query_string; 
					$fd = @implode('', file($url));
				}
				
				// ----------- EMAIL ----------
				// Mail::to('susilo.basri@music.yamaha.com')
				// ->bcc(['aditya.agassi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
				// ->send(new SendEmail($data, 'urgent_spk'));

				// Mail::to('nasiqul.ibat@music.yamaha.com')
				// ->send(new SendEmail($data, 'urgent_spk'));
			} else {
				if(strpos($bahaya, 'Bahan Kimia Beracun') !== false){
					$remark = 2;

				// Mail::to(['rizal.yohandhi@music.yamaha.com', 'whica.parama@music.yamaha.com'])
				// ->bcc(['aditya.agassi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
				// ->send(new SendEmail($data, 'chemical_spk'));

					Mail::to(['rizal.yohandhi@music.yamaha.com', 'whica.parama@music.yamaha.com'])
					->cc(['aditya.agassi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
					->send(new SendEmail($data, 'chemical_spk'));
				}
			}

			$response = array(
				'status' => true,
				'message' => "Pembuatan SPK berhasil"
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

	public function editSPK(Request $request)
	{
		MaintenanceJobOrder::where('order_no', '=', $request->get("spk_edit"))
		->update([
			'machine_condition' => $request->get("kondisi_mesin_edit"),
			'type' => $request->get("workType_edit"),
			'category' => $request->get("kategori_edit"),
			'danger' => implode(", ", $request->get('bahaya_edit')),
			'machine_name' => $request->get('mesin_edit'),
			'machine_remark' => $request->get('nama_mesin_detail'),
			'description' => $request->get('uraian_edit'),
			'safety_note' => $request->get('keamanan_edit'),
			'note' => $request->get('reason_urgent_edit')
		]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function cancelSPK(Request $request)
	{
		MaintenanceJobOrder::where('order_no', '=', $request->get("order_no"))
		->update([
			'remark' => 8
		]);

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function fetchSPKProgress(Request $request)
	{
		if ($request->get('from')) {
			$from = $request->get('from');
		} else {
			$from = date('Y-m-d', strtotime("-1 month"));
			if ($from < '2021-01-01') {
				$from = '2021-01-01';
			}
		}

		if ($request->get('to')) {
			$to = $request->get('to');
		} else if($request->get('from')){
			$to = $request->get('from');
		} else {
			$to = date('Y-m-d');
		}

		$get_data = db::select('
			SELECT order_no, GROUP_CONCAT(priority) as priority, GROUP_CONCAT(bagian) bagian,GROUP_CONCAT(description)	description,GROUP_CONCAT(request_date)	request_date,GROUP_CONCAT(requester)	requester,GROUP_CONCAT(inprogress) 	inprogress,GROUP_CONCAT(pic)	pic,GROUP_CONCAT(target_date)	target_date,GROUP_CONCAT(target)	target,GROUP_CONCAT(process_code)	process_code,GROUP_CONCAT(process_name)	process_name,GROUP_CONCAT(`status`)	`status`,GROUP_CONCAT(cause) cause,GROUP_CONCAT(handling) handling from 
			(select * from
			(SELECT DISTINCT maintenance_job_orders.order_no, priority, department_shortname as bagian, maintenance_job_orders.description, DATE_FORMAT(maintenance_job_orders.created_at,"%Y-%m-%d") as request_date, `name` as requester, inprogress.created_at as inprogress, pic, date(target_date) as target_date, target_date as target, process_code, process_name, maintenance_job_pendings.status,null cause, null handling FROM `maintenance_job_orders` 
			left join employee_syncs on maintenance_job_orders.created_by = employee_syncs.employee_id
			left join (
			select order_no, GROUP_CONCAT(`name`) as pic from maintenance_job_processes 
			left join employee_syncs on employee_syncs.employee_id = maintenance_job_processes.operator_id
			where deleted_at is null
			group by order_no
			) as prcs on prcs.order_no = maintenance_job_orders.order_no
			left join (select * from maintenance_job_processes where start_actual is not null and deleted_at is null) as inprogress on maintenance_job_orders.order_no = inprogress.order_no
			left join (select process_code, process_name from processes where remark = "maintenance") l_pcr on l_pcr.process_code = maintenance_job_orders.remark
			left join maintenance_job_pendings on maintenance_job_orders.order_no = maintenance_job_pendings.order_no
			left join departments on SUBSTRING_INDEX(maintenance_job_orders.section,"_",1) = departments.department_name
			where maintenance_job_orders.remark <> 8
			and maintenance_job_orders.deleted_at is null
			order by target asc
			) as awal
			union all
			
			SELECT order_no, null priority, null bagian, null	description,null	request_date,null	requester,null 	inprogress,null	pic,null	target_date,null	target,null	process_code,null	process_name,null	`status`, cause, handling FROM maintenance_job_reports where id in (SELECT max(id) FROM maintenance_job_reports GROUP BY order_no)) alls
			where request_date >= "'.$from.'" and request_date <= "'.$to.'"
			group by order_no
			order by target asc			
			');

		// $data_progress = db::select('
		// 	SELECT maintenance_job_orders.order_no, IF(TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, target_date) < 0, 0, TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, target_date)) as plan_time,  TIMESTAMPDIFF(SECOND, maintenance_job_order_logs.created_at, now()) as act_time from maintenance_job_orders 
		// 	left join maintenance_job_order_logs on maintenance_job_order_logs.order_no = maintenance_job_orders.order_no 
		// 	where maintenance_job_orders.remark = 4 and maintenance_job_order_logs.remark = 4 and maintenance_job_order_logs.deleted_at is null
		// 	order by maintenance_job_orders.order_no
		// 	');

		$data_bar = db::select('SELECT DATE_FORMAT(mstr.dt,"%d %b %Y") dt, mstr.process_code, mstr.process_name, IFNULL(datas.jml,0) jml from
			(select * from
			(select date(created_at) as dt from maintenance_job_orders where remark <> 8 group by date(created_at)) tgl
			cross join (select process_code, process_name from processes where remark = "maintenance") as prs
			) as mstr
			left join (select remark ,date(created_at) as dt, count(remark) as jml from maintenance_job_orders where remark <> 8 group by remark, date(created_at)) as datas on mstr.dt = datas.dt and mstr.process_code = datas.remark
			where mstr.dt >= "'.$from.'" and mstr.dt <= "'.$to.'"
			order by mstr.dt asc, mstr.process_code asc');

		$response = array(
			'status' => true,
			'datas' => $get_data,
			// 'progress' => $data_progress,
			'data_bar' => $data_bar
		);
		return Response::json($response);
	}

	public function fetchSPKProgressDetail(Request $request)
	{
		$detail = 'SELECT maintenance_job_orders.order_no, department_shortname as bagian, priority, type, category, machine_name, description, DATE_FORMAT(maintenance_job_orders.created_at,"%d %b %Y") target_date, process_code, employee_syncs.name, date(maintenance_job_orders.created_at) as dt';

		if ($request->get('process_name') != 'Listed') {
			$detail .= ', cause, handling';
		}

		$detail .= ' from maintenance_job_orders 
		left join (select process_code, process_name from processes where remark = "maintenance") prs on prs.process_code = maintenance_job_orders.remark
		left join employee_syncs on employee_syncs.employee_id = maintenance_job_orders.created_by
		left join departments on departments.department_name = SUBSTRING_INDEX(maintenance_job_orders.section,"_",1)';

		if ($request->get('process_name') != 'Listed') {
			$detail .= 'left join (SELECT order_no, operator_id, cause, handling FROM maintenance_job_reports where id in (SELECT max(id) FROM maintenance_job_reports GROUP BY order_no)) as rpt on maintenance_job_orders.order_no = rpt.order_no';
		}

		$detail .='where DATE_FORMAT(maintenance_job_orders.created_at,"%d %b %Y") = "'.$request->get('date').'" and process_name = "'.$request->get('process_name').'" order by order_no asc';

		$bar_detail = db::select($detail);

		$response = array(
			'status' => true,
			'datas' => $bar_detail,
		);
		return Response::json($response);
	}

	public function fetchSPKOperator(Request $request)
	{
		$data_op = MaintenanceJobOrder::leftJoin('maintenance_job_processes', 'maintenance_job_orders.order_no', '=', 'maintenance_job_processes.order_no')
		->whereIn('maintenance_job_orders.remark', [3,4])
		->whereNull('maintenance_job_processes.deleted_at')
		->select('maintenance_job_orders.order_no', 'maintenance_job_processes.operator_id', 'start_actual', 'start_plan')
		->get();

		$response = array(
			'status' => true,
			'datas' => $data_op
		);
		return Response::json($response);
	}

	public function fetchMaintenanceList(Request $request)
	{
		DB::connection()->enableQueryLog();
		$maintenance_job_orders = MaintenanceJobOrder::leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
		->leftJoin(db::raw("(select order_no, operator_id, start_actual, finish_actual from maintenance_job_processes where deleted_at is null) as maintenance_job_processes"), "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin(db::raw("employee_syncs AS es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
		->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
		->select("maintenance_job_orders.order_no", "maintenance_job_orders.section", "priority", "type", "machine_condition", "danger", "target_date", db::raw("employee_syncs.`name` as requester"), db::raw("es.`name` as operator"), db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d") as date'), "process_name", "maintenance_job_orders.remark", "maintenance_job_orders.category", "start_actual", "finish_actual", "maintenance_job_pendings.status", "maintenance_job_orders.note");

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
			$maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.remark', [0,2]);
		}
		if(strlen($request->get('status')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_pendings.status', '=', $request->get('status'));
		}
		if(strlen($request->get('username')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.created_by', '=', $request->get('username'));
		}
		if(strlen($request->get('category')) > 0){
			if($request->get('category') != 'all'){
				$keys = [];
				foreach ($this->spk_category as $ctg) {
					if ($ctg['category'] === $request->get('category')) {
						array_push($keys, $ctg['value']);
					}
				}

				$maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.category', $keys);
			}
		}

		if(strlen($request->get('pic')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_processes.operator_id', '=', $request->get('pic'));
		}

		$maintenance_job_orders = $maintenance_job_orders->orderBy('maintenance_job_orders.created_at', 'desc')
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
		DB::connection()->enableQueryLog();
		$detail = MaintenanceJobOrder::where("maintenance_job_orders.order_no", "=", $request->get('order_no'))
		->whereNull('maintenance_job_processes.deleted_at')
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin("maintenance_job_processes", "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
		->leftJoin('maintenance_job_reports', function($join)
		{
			$join->on('maintenance_job_reports.order_no', '=', 'maintenance_job_processes.order_no');
			$join->on('maintenance_job_reports.operator_id','=', 'maintenance_job_processes.operator_id');
		})
		->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
		->leftJoin(db::raw("employee_syncs as  es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
		->leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
		->leftJoin("maintenance_plan_items", "maintenance_plan_items.machine_id", "=", "maintenance_job_orders.machine_name")
		->select("maintenance_job_orders.order_no", "employee_syncs.name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d %H-%i") as date'), "priority", "maintenance_job_orders.section", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "safety_note", "target_date", "process_name", db::raw("es.name as name_op"), db::raw("es.employee_id as id_op"), db::raw("DATE_FORMAT(maintenance_job_processes.start_actual, '%Y-%m-%d %H:%i') start_actual"), db::raw("DATE_FORMAT(maintenance_job_processes.finish_actual, '%Y-%m-%d %H:%i') finish_actual"), "maintenance_job_pendings.status", db::raw("maintenance_job_pendings.description as pending_desc"), "maintenance_job_orders.machine_name", "cause", "handling", "photo", "note", "machine_remark", db::raw("maintenance_plan_items.description as machine_desc"), "maintenance_plan_items.area", "att", db::raw("maintenance_job_pendings.remark as pending_remark"))
		->get();

		$parts = MaintenanceJobOrder::where('maintenance_job_orders.order_no', '=', $request->get('order_no'))
		->join("maintenance_job_spareparts", "maintenance_job_spareparts.order_no", "=", "maintenance_job_orders.order_no")
		->join("maintenance_inventories", "maintenance_inventories.part_number", "=", "maintenance_job_spareparts.part_number")
		->select("maintenance_job_orders.order_no", "maintenance_job_spareparts.part_number", "maintenance_inventories.part_name", "maintenance_inventories.specification", "maintenance_job_spareparts.quantity")
		->get();

		$response = array(
			'status' => true,
			'detail' => $detail,
			'part' => $parts,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function postMemberSPK(Request $request)
	{
		$order_no = $request->get("order_no");
		$member = $request->get("member");
		$datas = [];

		MaintenanceJobProcess::where('order_no', '=', $order_no)->forceDelete();

		foreach ($member as $mbr) {
			$jp = new MaintenanceJobProcess;
			$jp->order_no = $order_no;
			$jp->operator_id = $mbr['operator'];
			$jp->start_plan = $mbr['start_date']." ".$mbr['start_time'];
			$jp->finish_plan = $mbr['finish_date']." ".$mbr['finish_time'];
			$jp->created_by = strtoupper(Auth::user()->username);
			$jp->save();
		};

		MaintenanceJobOrder::where('order_no', '=', $order_no)
		->update(['remark' => 3]);

		$log = new MaintenanceJobOrderLog;
		$log->order_no = $order_no;
		$log->remark = 3;
		$log->created_by = Auth::user()->username;
		$log->save();

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function postNewMemberSPK(Request $request)
	{
		$order_no = $request->get("order_no");
		$member = $request->get("member");
		$datas = [];

		MaintenanceJobProcess::where('order_no', '=', $order_no)->delete();

		foreach ($member as $mbr) {
			$jp = new MaintenanceJobProcess;
			$jp->order_no = $order_no;
			$jp->operator_id = $mbr['operator'];
			$jp->start_plan = $mbr['start_date']." ".$mbr['start_time'];
			$jp->finish_plan = $mbr['finish_date']." ".$mbr['finish_time'];
			$jp->created_by = strtoupper(Auth::user()->username);
			$jp->save();
		};

		MaintenanceJobOrder::where('order_no', '=', $order_no)
		->update(['remark' => 3]);

		$log = new MaintenanceJobOrderLog;
		$log->order_no = $order_no;
		$log->remark = 3;
		$log->created_by = Auth::user()->username;
		$log->save();

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function verifySPK(Request $request)
	{
		$stat = $request->get('stat');
		$order_no = $request->get('order_no');
		
		$get_spk = MaintenanceJobOrder::where('order_no', '=', $order_no)->select('remark', 'target_date', 'danger', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as tanggal'), 'priority')->first();

		if ($get_spk->remark != "2") {
			if ($stat == "1") {
				$target_date = $get_spk->target_date;
				$priority = "Urgent";
				$message2 ='Berhasil di approve sebagai SPK dengan prioritas urgent';
			} else {
				// $target_date = date("Y-m-d", strtotime("+7 day", strtotime($get_spk->tanggal)));
				$priority = "Normal";
				$message2 = $order_no.' berubah sebagai SPK dengan prioritas normal';
			}

			$remark = "2";

			try {
				$spk = MaintenanceJobOrder::where('order_no', '=', $order_no)->first();
				$spk->remark = $remark;
				$spk->priority = $priority;
				// $spk->target_date = $target_date;
				// $spk->note = "Maintenance_OK";

				// $manager = EmployeeSync::where('position', '=', 'Manager')
				// ->where('department', '=', 'Maintenance')
				// ->first();

				$spk_log = new MaintenanceJobOrderLog([
					'order_no' => $order_no,
					'remark' => $remark,
					'created_by' => Auth::user()->username,
				]);

				$spk->save();
				$spk_log->save();

				$message = 'SPK dengan Order No. '.$order_no.' Berhasil Aprove';

				$response = array(
					'status' => true,
					'message' => $message
				);
				return Response::json($response);

				// return view('maintenance.spk_approval_message', array(
				// 	'head' => $order_no,
				// 	'message' => $message,
				// 	'message2' => $message2,
				// ))->with('page', 'SPK Approval');
			} catch (QueryException $e) {
				$message = 'SPK dengan Order No. '.$order_no.' Berhasil Aprove';

				$response = array(
					'status' => true,
					'message' => $message
				);
				return Response::json($response);

				// return view('maintenance.spk_approval_message', array(
				// 	'head' => $order_no,
				// 	'message' => 'Update Error',
				// 	'message2' => $e->getMessage(),
				// ))->with('page', 'SPK Approval');
			}
		} else {
			$message = 'SPK dengan Order No. '.$order_no;
			$message2 ='Sudah di approve/reject';
			return view('maintenance.spk_approval_message', array(
				'head' => $order_no,
				'message' => $message,
				'message2' => $message2,
			))->with('page', 'SPK Approval');
		}
	}

	public function addDangerNote(Request $request)
	{
		// $get_spk = MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))->select('remark')->first();
		// if ($get_spk->remark != "2") {
		// 	$remark = "2";

		$spk = MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))->first();
		// $spk->remark = $remark;
		$spk->safety_note = $request->get('danger_note');
		// $spk->note = "Chemical_OK Manager_None";

		$chemical = EmployeeSync::whereNull('group')
		->where('section', '=', 'Chemical Process Control Section')
		->first();

		$spk_log = new MaintenanceJobOrderLog([
			'order_no' => $request->get('order_no'),
			// 'remark' => $remark,
			'created_by' => $chemical->employee_id
		]);

		try {
			$spk->save();
			$spk_log->save();

			$response = array(
				'status' => true,
				'message' => "Berhasil diverifikasi",
			);
			return Response::json($response);
		} catch (QueryException $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
		// } else {
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => "Sudah di approve/reject",
		// 	);
		// 	return Response::json($response);
		// }
	}

	public function startSPK(Request $request)
	{
		$spk_log = MaintenanceJobOrderLog::firstOrNew(array('order_no' => $request->get('order_no'), 'remark' => 4));
		$spk_log->order_no = $request->get('order_no');
		$spk_log->remark = 4;
		$spk_log->created_by = Auth::user()->username;

		$spk_log->save();

		MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
		->update(['remark' => 4]);

		MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
		->where('operator_id', '=', strtoupper(Auth::user()->username))
		->update(['start_actual' => date('Y-m-d H:i:s')]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function restartSPK(Request $request)
	{
		$spk_log = new MaintenanceJobOrderLog([
			'order_no' => $request->get('order_no'),
			'remark' => 4,
			'created_by' => Auth::user()->username
		]);
		$spk_log->save();

		MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
		->update(['remark' => 4]);

		MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
		->where('operator_id', '=', strtoupper(Auth::user()->username))
		->update(['start_actual' => date('Y-m-d H:i:s'), 'finish_actual' => null]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function reportingSPK(Request $request)
	{
		$data = $request->get('foto');
		define('UPLOAD_DIR', 'images/');
		$upload = [];

		$operator_id = Auth::user()->username;

		try {
			$no = 1;
			foreach ($data as $key) {
				if ($key != "") {
					$image_parts = explode(";base64,", $key);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];
					$image_base64 = base64_decode($image_parts[1]);

					$file = public_path().'\maintenance\\spk_report\\'.$request->get('order_no').$operator_id.$no.'.png';
					$file2 = $request->get('order_no').$operator_id.$no.'.png';

					file_put_contents($file, $image_base64);

					array_push($upload, $file2);
					$no++;
				}
			}

			MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
			->where('operator_id', '=', strtoupper(Auth::user()->username))
			->update(['finish_actual' => date('Y-m-d H:i:s')]);

			$proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
			->where('operator_id', '=', strtoupper(Auth::user()->username))
			->first();

			$rpt = new MaintenanceJobReport;
			$rpt->order_no = $request->get('order_no');
			$rpt->operator_id = $operator_id;
			$rpt->cause = $request->get('penyebab');
			$rpt->handling = $request->get('penanganan');
			$rpt->prevention = $request->get('pencegahan');
			
			$rpt->photo = implode(", ",$upload);
			$rpt->remark = $request->get('other_part');
			$rpt->started_at = $proc->start_actual;
			$rpt->finished_at = date('Y-m-d H:i:s');
			$rpt->created_by = $operator_id;

			$rpt->save();			

			$spk_log = new MaintenanceJobOrderLog();
			$spk_log->order_no = $request->get('order_no');
			$spk_log->remark = 6;
			$spk_log->created_by = Auth::user()->username;

			$spk_log->save();

			MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
			->update(['remark' => 6]);

			$parts = $request->get('spare_part');

			if ($parts) {
				foreach ($parts as $prts) {
					$spk_part = MaintenanceJobSparepart::firstOrNew(array('order_no' => $request->get('order_no'), 'part_number' => $prts['part_number']));
					$spk_part->quantity = $prts['qty'];
					$spk_part->created_by = Auth::user()->username;

					$spk_part->save();
				}
			}

			$response = array(
				'status' => true,
				'message' => ''
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

	public function reportingSPKPending(Request $request)
	{
		try {
			$no = 1;
			$operator_id = Auth::user()->username;

			$data = $request->get('foto');
			define('UPLOAD_DIR', 'images/');
			$upload = [];

			foreach ($data as $key) {
				if ($key != "") {
					$image_parts = explode(";base64,", $key);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];
					$image_base64 = base64_decode($image_parts[1]);

					$file = public_path().'\maintenance\\spk_report\\'.$request->get('order_no').$operator_id.$no.'.png';
					$file2 = $request->get('order_no').$operator_id.$no.'.png';

					file_put_contents($file, $image_base64);

					array_push($upload, $file2);
					$no++;
				}
			}

			MaintenanceJobProcess::where("order_no", "=", $request->get('order_no'))
			->where("operator_id", "=", strtoupper(Auth::user()->username))
			->update(['finish_actual' => date("Y-m-d H:i:s"), 'remark' => $request->get('status')]);

			$proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
			->where('operator_id', '=', strtoupper(Auth::user()->username))
			->first();

			$rpt = new MaintenanceJobReport;
			$rpt->order_no = $request->get('order_no');
			$rpt->operator_id = $operator_id;
			$rpt->cause = $request->get('penyebab');
			$rpt->handling = $request->get('penanganan');
			$rpt->started_at = $proc->start_actual;
			$rpt->finished_at = date('Y-m-d H:i:s');
			
			$rpt->photo = implode(", ",$upload);
			$rpt->created_by = $operator_id;

			$rpt->save();

			$arr_part = [];
			$part = '';
			if (count($request->get('spare_part')) > 0) {
				foreach ($request->get('spare_part') as $prt) {
					array_push($arr_part, $prt['part_number']." : ".$prt['qty']);
				}

				$part = implode("; ", $arr_part);
			}

			$other_part = "";
			if ($request->get('other_part')) {
				$other_part = $request->get('other_part');
			}

			$spk_pending = MaintenanceJobPending::firstOrNew(array('order_no' => $request->get('order_no')));
			$spk_pending->order_no = $request->get('order_no');
			$spk_pending->remark = $other_part;
			$spk_pending->description = $part;
			$spk_pending->status = $request->get('status');


			$spk_pending->created_by = Auth::user()->username;
			$spk_pending->save();

			MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
			->update(['remark' => 5]);

			$spk_log = MaintenanceJobOrderLog::firstOrNew(array(
				'order_no' => $request->get('order_no'),
				'remark' => 5
			));
			$spk_log->created_by = Auth::user()->username;
			$spk_log->save();

			$response = array(
				'status' => true,
				'message' => 'OK'
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

	public function reportingSPKPause(Request $request)
	{
		try {
			$no = 1;
			$operator_id = Auth::user()->username;

			$data = $request->get('foto');
			define('UPLOAD_DIR', 'images/');
			$upload = [];

			foreach ($data as $key) {
				if ($key != "") {
					$image_parts = explode(";base64,", $key);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];
					$image_base64 = base64_decode($image_parts[1]);

					$file = public_path().'\maintenance\\spk_report\\'.$request->get('order_no').$operator_id.$no.'.png';
					$file2 = $request->get('order_no').$operator_id.$no.'.png';

					file_put_contents($file, $image_base64);

					array_push($upload, $file2);
					$no++;
				}
			}

			MaintenanceJobProcess::where("order_no", "=", $request->get('order_no'))
			->where("operator_id", "=", strtoupper(Auth::user()->username))
			->update(['finish_actual' => date("Y-m-d H:i:s"), 'remark' => $request->get('status')]);

			$proc = MaintenanceJobProcess::where('order_no', '=', $request->get('order_no'))
			->where('operator_id', '=', strtoupper(Auth::user()->username))
			->first();

			$rpt = new MaintenanceJobReport;
			$rpt->order_no = $request->get('order_no');
			$rpt->operator_id = $operator_id;
			$rpt->cause = $request->get('penyebab');
			$rpt->handling = $request->get('penanganan');
			$rpt->started_at = $proc->start_actual;
			$rpt->finished_at = date('Y-m-d H:i:s');
			
			$rpt->photo = implode(", ",$upload);
			$rpt->created_by = $operator_id;

			$rpt->save();

			$arr_part = [];
			$part = '';
			if (count($request->get('spare_part')) > 0) {
				foreach ($request->get('spare_part') as $prt) {
					array_push($arr_part, $prt['part_number']." : ".$prt['qty']);
				}

				$part = implode("; ", $arr_part);
			}

			MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
			->update(['remark' => 9]);

			$spk_log = MaintenanceJobOrderLog::firstOrNew(array(
				'order_no' => $request->get('order_no'),
				'remark' => 9
			));
			$spk_log->created_by = Auth::user()->username;
			$spk_log->save();

			$response = array(
				'status' => true,
				'message' => 'OK'
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

	public function fetchSPKStatus()
	{
		$mtc_statuses = db::select("SELECT process_name, count(remark) as tot from (select process_code, process_name from processes where remark = 'maintenance') proc
			left join `maintenance_job_orders` on proc.process_code = maintenance_job_orders.remark
			GROUP BY process_name");
	}
	// --------------------------  APAR ----------------------

	public function fetchAparList(Request $request)
	{
		DB::connection()->enableQueryLog();

		$apars = Utility::select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', db::raw('DATE_FORMAT(exp_date, "%d %M %Y") as exp_date2'), 'exp_date', db::raw("TIMESTAMPDIFF(MONTH, exp_date, now()) as age_left"), 'remark', 'order');

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
		->whereNull('utility_checks.deleted_at')
		->select('utility_id', 'check', db::raw('DATE_FORMAT(check_date,"%d %M %Y") check_date2'), 'utility_checks.remark', 'utility_checks.created_by', 'employee_syncs.name', db::raw('utilities.remark as remark2'), db::raw('utility_checks.id as id_check'), db::raw('IF(DATEDIFF(DATE_FORMAT(check_date,"%Y-%m-%d"), now()) = 0,1, 0) as action'))
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
			$last_check_tool = Utility::where('utilities.id', $request->get('utility_id'))
			->leftJoin('utility_checks', 'utility_checks.utility_id', '=', 'utilities.id')
			->select('utilities.utility_code', 'utility_name', 'exp_date', 'utilities.status', db::raw('DATE_FORMAT(utility_checks.check_date, "%d-%m-%Y") as check_date'), 'utilities.remark')
			->whereNull('utility_checks.deleted_at')
			->orderBy('utility_checks.id', 'desc')
			->limit(2)
			->get();

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

	public function fetchAparExpire(Request $request)
	{
		$exp = Utility::where('remark', '=', 'APAR')
		->leftJoin('utility_orders', 'utilities.id', '=', 'utility_orders.utility_id')
		->whereRaw('TIMESTAMPDIFF(MONTH, now(), exp_date) <= '.$request->get('mon'))
		->select('utilities.id', 'utility_code', 'utility_name', 'exp_date', 'group', 'location', 'last_check', db::raw('TIMESTAMPDIFF(MONTH, now(), exp_date) as exp'), 'capacity', 'type', 'pr_date', 'no_pr')
		->orderBy('exp_date')
		->get();

		$response = array(
			'status' => true,
			'expired_list' => $exp
		);
		return Response::json($response);
	}

	public function fetchAparNG(Request $request)
	{
		$mon = date('Y-m');
		if ($request->get('mon') != "") {
			$mon = $request->get('mon');
		}

		$check_by_operator = db::select("SELECT utilities.id, utility_code, utility_name, `group`, location, utilities.remark, last_check, `check`, capacity, type from utilities
			left join 
			(SELECT id, utility_id, `check`
			FROM utility_checks
			WHERE id IN (
			SELECT MAX(id)
			FROM utility_checks
			WHERE DATE_FORMAT(created_at, '%Y-%m') = '".$mon."' and deleted_at is null and utility_checks.remark is null
			GROUP BY utility_id
			)) as utility_checks
			on utility_checks.utility_id = utilities.id
			where `status` = 'NG'
			order by last_check");

		$response = array(
			'status' => true,
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

		try {
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
				'status' => true,
				'message' => 'OK'
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

	public function updateTool(Request $request)
	{
		try {
			Utility::where('utility_code', '=', $request->get('edit_code'))
			->where('remark', '=', 'APAR')
			->update([
				'utility_name' => $request->get('edit_name'),
				'type' => $request->get('edit_type'),
				'location' => $request->get('edit_location1'),
				'group' => $request->get('edit_location2'),
				'capacity' => $request->get('edit_capacity'),
				'exp_date' => $request->get('edit_exp')
			]);

			$response = array(
				'status' => true,
				'message' => 'OK'
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

	public function replaceTool(Request $request)
	{
		$cat = db::table('utility_check_lists')->where('remark', '=', 'APAR')->select('check_point')->get();

		$utl = Utility::where('remark', '=', 'APAR')
		->where('utility_code', '=', $request->get('code'))
		->first();

		foreach ($this->apar_type as $type) {
			if ($utl->type == $type['type']) {
				$exp_date = date("Y-m-d", strtotime('+'.$type['valid'].' years', strtotime($request->get('entry_date'))));
			}
		}

		Utility::where('remark', '=', 'APAR')
		->where('utility_code', '=', $request->get('code'))
		->update([
			'capacity' => $request->get('capacity'), 
			'exp_date' => $exp_date,
			'entry_date' => $request->get('entry_date'),
			'status' => null
		]);

		$cek = "";
		foreach ($cat as $c) {
			$cek .= "1,";
		}
		$cek = rtrim($cek, ",");

		$utl_check2 = new UtilityCheck;
		$utl_check2::where('utility_id', $utl->id)->whereNull('remark')->update(array('remark' => 'OK', 'check' => $cek));


		$hasil_check = UtilityCheck::select(db::raw('DATE_FORMAT(check_date,"%d-%m-%Y") as cek_date'))->where('utility_id', $utl->id)->orderBy('check_date')->limit(2)->get();

		$uo = UtilityOrder::where('Utility_id', '=', $utl->id)->get()->count();

		if ($uo > 0) {
			UtilityOrder::where('Utility_id', '=', $utl->id)->delete();
		}

		$response = array(
			'status' => true,
			'check' => $hasil_check,
			'new_exp' => $exp_date
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

	public function print_apar2($apar_id, $apar_name, $exp_date, $last_check, $last_check2, $hasil_check, $remark)
	{
		if ($exp_date == "null") {
			$exp = "-";
		} else {
			$exp = $exp_date;
		}

		$data = [
			'apar_code' => $apar_id,
			'apar_name' => $apar_name,
			'exp_date' => $exp,
			'last_check' => $last_check,
			'last_check2' => $last_check2,
			'status' => $hasil_check,
			'remark' => $remark
		];

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		// $pdf->setPaper([0, 0, 141.732, 184.252], 'landscape');
		$pdf->setPaper([0, 0, 150.236, 184.252], 'landscape');
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
			$loc = "Factory II";
		} else if ($request->get('mon') % 2 === 1){
			$loc = "Factory I";
		}

		DB::connection()->enableQueryLog();

		$check = Utility::where("location", "=", $loc)
		->where("remark", "=", "APAR")
		->select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', 'remark', db::raw('DATE_FORMAT(last_check, "%d %M %Y") last_check'), db::raw('DATE_FORMAT(entry_date, "%Y-%m-%d") entry'), db::raw('DATE_FORMAT(DATE_ADD(last_check, INTERVAL 2 MONTH), "%d %M %Y") as cek_before'), db::raw("FLOOR((DayOfMonth(DATE_ADD(DATE(last_check), INTERVAL 2 MONTH)-1)/7)+1) as wek"))
		->whereRaw('DATE_FORMAT(last_check,"%Y-%m") <> DATE_FORMAT("'.$request->get('dt').'", "%Y-%m")')
		->orderBy("wek", "ASC")
		->orderBy("last_check", "ASC")
		->get();

		$hasil_check = db::select("SELECT DATE_FORMAT(check_date,'%Y-%m-%d') as dt_cek, utility_code, utility_name, location, DATE_FORMAT(last_check,'%d %M %Y') as last_check FROM utility_checks
			LEFT JOIN utilities on utilities.id = utility_checks.utility_id
			WHERE utility_checks.id IN (
			SELECT MAX(id)
			FROM utility_checks
			where DATE_FORMAT(check_date,'%Y-%m') = DATE_FORMAT('".$request->get('dt')."', '%Y-%m') and deleted_at is null
			GROUP BY utility_id
		) AND utilities.remark = 'APAR'");

		$response = array(
			'status' => true,
			'check_list' => $check,
			'hasil_check' => $hasil_check,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function fetch_hydrant_monitoring(Request $request)
	{
		DB::connection()->enableQueryLog();

		if ($request->get('mon') % 2 === 0) {
			$loc = "Factory II";
		} else if ($request->get('mon') % 2 === 1){
			$loc = "Factory I";
		}

		$check = Utility::where("location", "=", $loc)
		->where("remark", "=", "HYDRANT")
		->select('id','utility_code','utility_name', 'type', 'group', 'capacity', 'location', 'remark', db::raw('DATE_FORMAT(last_check, "%d %M %Y") last_check'), db::raw('DATE_FORMAT(entry_date, "%Y-%m-%d") entry'), db::raw('DATE_FORMAT(DATE_ADD(last_check, INTERVAL 2 MONTH), "%d %M %Y") as cek_before'), db::raw("FLOOR((DayOfMonth(DATE_ADD(DATE(last_check), INTERVAL 2 MONTH)-1)/7)+1) as wek"))
		->whereRaw('DATE_FORMAT(last_check,"%Y-%m") <> DATE_FORMAT("'.$request->get('dt').'", "%Y-%m")')
		->orderBy("wek", "ASC")
		->orderBy("id", "ASC")
		->get();

		$hasil_check = db::select("SELECT DATE_FORMAT(check_date,'%Y-%m-%d') as dt_cek, utility_code, utility_name, location, DATE_FORMAT(last_check,'%d %M %Y') as last_check FROM utility_checks
			LEFT JOIN utilities on utilities.id = utility_checks.utility_id
			WHERE utility_checks.id IN (
			SELECT MAX(id)
			FROM utility_checks
			where DATE_FORMAT(check_date,'%Y-%m') = DATE_FORMAT('".$request->get('dt')."', '%Y-%m') and deleted_at is null
			GROUP BY utility_id
		) AND utilities.remark = 'HYDRANT'");

		$response = array(
			'status' => true,
			'check_list' => $check,
			'hasil_check' => $hasil_check,
			'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function fetch_apar_resume(Request $request)
	{
		$getCheckedData = DB::select('SELECT mon, jml_tot, IFNULL(jml,0) as jml FROM
			(SELECT COUNT(entry) as jml_tot, mst.mo, mon from
			(SELECT id, IF(location = "FACTORY II", 0, 1) as mo, DATE_FORMAT(entry_date,"%Y-%m-%d") as entry from utilities) utl
			left join
			(select DATE_FORMAT(week_date,"%Y-%m") as mon, MOD(MONTH(week_date),2) as mo from weekly_calendars where week_date >= "2020-01-01" group by DATE_FORMAT(week_date,"%Y-%m"), mo) mst on mst.mo = utl.mo
			where DATE_FORMAT(entry, "%Y-%m") <= mon
			group by mon, mst.mo) base
			left join (
			SELECT count(utility_id) as jml, cek_date from 
			(SELECT utility_checks.utility_id, DATE_FORMAT(check_date, "%Y-%m") as cek_date from utility_checks
			left join utilities on utility_checks.utility_id = utilities.id
			where utilities.remark = "APAR" and utility_checks.deleted_at is null
			group by utility_id, DATE_FORMAT(check_date, "%Y-%m")
			) checked_data
			group by cek_date
			) as cek on base.mon = cek.cek_date
			');

		$getAparNew = DB::select('SELECT mstr.mon, IFNULL(new.jml,0) as new, IFNULL(exp.jml,0) as exp FROM
			(select DATE_FORMAT(week_date,"%Y-%m") as mon from weekly_calendars where week_date >= "2020-01-01" group by DATE_FORMAT(week_date,"%Y-%m")) mstr
			left join 
			(select count(id) as jml, DATE_FORMAT(entry_date,"%Y-%m") as mon from utilities
			where DATE_FORMAT(entry_date,"%Y-%m") >= "2020-01" and remark = "APAR"
			group by DATE_FORMAT(entry_date,"%Y-%m")) as new on mstr.mon = new.mon
			left join
			(select count(id) as jml, DATE_FORMAT(exp_date,"%Y-%m") as mon from utilities where remark = "APAR"
			group by DATE_FORMAT(exp_date,"%Y-%m")) as exp on mstr.mon = exp.mon
			');

		$response = array(
			'status' => true,
			'check_list' => $getCheckedData,
			'replace_list' => $getAparNew,
		);
		return Response::json($response);
	}

	public function fetch_apar_resume_week(Request $request)
	{
		$ym = Date('Y-m');

		$mon = Date('m');

		if ($request->get('mon')) {
			$ym = $request->get('mon');

			$mon = explode("-", $request->get('mon'));
			$mon = $mon[1];
		}

		$mon = intval($mon);

		if ($mon % 2 === 0) {
			$loc = "Factory II";
		} else if ($mon % 2 === 1){
			$loc = "Factory I";
		}

		$cek_week = db::select('select "'.$ym.'" as mon, wek, sum(jml_cek) as uncek, sum(cek) as cek from
			(SELECT wek, COUNT(weeks) as jml_cek, 0 as cek from
			(SELECT IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) as weeks from utilities where remark = "APAR" and location = "'.$loc.'") un
			right join
			(select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "'.$ym.'" GROUP BY wek) mstr on mstr.wek+1 = un.weeks
			group by wek

			union all
			select mstr.wek, 0 as jml_cek, count(cek.wek) as cek from
			(select utility_id, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek from utility_checks 
			left join utilities on utilities.id = utility_checks.utility_id
			where location = "'.$loc.'" and utilities.remark = "APAR" and DATE_FORMAT(check_date,"%Y-%m") = "'.$ym.'" and utility_checks.deleted_at is null
			group by utility_id, wek) cek
			right join
			(select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "'.$ym.'" GROUP BY wek) mstr on mstr.wek = cek.wek
			group by mstr.wek) semua
			group by wek');

		$replace_week = db::select('SELECT mstr.wek, IFNULL(entry_apar.entry,0) as entry, IFNULL(expired_apar.exp,0) as expire from
			(select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "'.$ym.'" GROUP BY wek) as mstr
			left join
			(select wek, count(utility_code) as entry from
			(select utility_code, utility_name, location, DATE(entry_date) as entry, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%Y-%m") = "'.$ym.'") as entry
			group by wek ) as entry_apar on mstr.wek = entry_apar.wek
			left join
			(select wek, count(utility_code) as exp from
			(select utility_code, utility_name, location, DATE(exp_date) as exp, IF(FLOOR((DayOfMonth(exp_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(exp_date)-1)/7)+1) - 1 as wek from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%Y-%m") = "'.$ym.'") as expired
			group by wek) as expired_apar on mstr.wek = expired_apar.wek');

		$apar_progres = db::select('SELECT cal.wek, IFNULL(datas.jml,0) as jml from
			(select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "'.$ym.'" GROUP BY wek) as cal
			left join
			(select wek, COUNT(utilities.id) as jml from utilities 
			join (
			SELECT FLOOR((DayOfMonth(utility_checks.check_date)-1)/7)+1 as wek, utility_id
			FROM utility_checks 
			WHERE id IN (
			SELECT min(id) 
			FROM utility_checks 
			where DATE_FORMAT(utility_checks.check_date, "%Y-%m") = "'.$ym.'" and utility_checks.deleted_at is null
			GROUP BY utility_id
			)
			) utility_checks on utilities.id = utility_checks.utility_id
			where location = "'.$loc.'" and utilities.remark = "APAR"
			group by wek) as datas on cal.wek = datas.wek');

		$apar_total = db::select('select count(id) as total from utilities where location = "'.$loc.'" and remark = "APAR"');


		$response = array(
			'status' => true,
			// 'cek_week' => $cek_week,
			// 'replace_week' => $replace_week,
			'apar_progres' => $apar_progres,
			'apar_total' => $apar_total, 
		);
		return Response::json($response);
	}

	// ------------------------------
	public function fetch_apar_resume_detail(Request $request)
	{
		$detailCheck = DB::select('SELECT utilities.utility_code, utilities.utility_name, utilities.location, utilities.`group`, 1 as cek from utility_checks
			left join utilities on utility_checks.utility_id = utilities.id
			where utilities.remark = "APAR" and DATE_FORMAT(check_date, "%M %Y") = "'.$request->get('mon').'" and utility_checks.deleted_at is null
			group by utilities.utility_code, utilities.utility_name, utilities.location, utilities.`group`, DATE_FORMAT(check_date, "%Y-%m")
			union all			
			SELECT utility_code, utility_name, location, `group`, 0 as cek from utilities 
			LEFT join utility_checks on utilities.id = utility_checks.utility_id
			where utilities.remark = "APAR" AND location = "FACTORY I" AND DATE_FORMAT(entry_date, "%Y-%m") <= "'.$request->get('mon2').'" AND (DATE_FORMAT(check_date, "%Y-%m") <> "'.$request->get('mon2').'" OR check_date is null and utility_checks.deleted_at is null)
			GROUP BY utility_code, utility_name, location, `group`
			ORDER BY cek asc
			');

		$detailNew = DB::select('select utility_code, utility_name, location, `group`, exp_date as dt, "Expired" as stat from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%M %Y") = "'.$request->get('mon').'"
			union all
			select utility_code, utility_name, location, `group`, DATE_FORMAT(entry_date,"%Y-%m-%d") as dt, "Replace/New" as stat from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%M %Y") = "'.$request->get('mon').'"
			order by dt asc');

		$response = array(
			'status' => true,
			'check_detail_list' => $detailCheck,
			'replace_list' => $detailNew,
		);
		return Response::json($response);
	}
	// ---------------------------------

	public function fetch_apar_resume_detail_week(Request $request)
	{
		DB::connection()->enableQueryLog();
		$ym = Date('Y-m');

		$mon = Date('m');

		if ($request->get('mon')) {
			$ym = $request->get('mon');

			$mon = explode("-", $request->get('mon'));
			$mon = $mon[1];
		}

		$mon = intval($mon);

		if ($mon % 2 === 0) {
			$loc = "Factory II";
		} else if ($mon % 2 === 1){
			$loc = "Factory I";
		}


		$detail_cek = db::select('SELECT semua.utility_code, semua.utility_name, semua.location, semua.`group`, IFNULL(cek.cek, 0) as cek from
			(SELECT id, wek, utility_code, utility_name, location, `group`, 0 as cek from
			(SELECT IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) as weeks, utility_code, utility_name, location, `group`, id from utilities where remark = "APAR" and location = "'.$loc.'") un
			right join
			(select FLOOR((DayOfMonth(week_date)-1)/7)+1 as wek from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = "'.$ym.'" GROUP BY wek) mstr on mstr.wek+1 = un.weeks
			where wek = '.$request->get('week').' ) as semua 
			left join
			(SELECT utility_id as id, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek, utility_code, utility_name, location, `group`, 1 as cek from utility_checks 
			left join utilities on utilities.id = utility_checks.utility_id
			where location = "'.$loc.'" and utilities.remark = "APAR" and DATE_FORMAT(check_date,"%Y-%m") = "'.$ym.'" and utility_checks.deleted_at is null
			group by utility_id, wek, utility_code, utility_name, location, `group`) as cek on semua.id = cek.id
			order by cek.cek asc');

		$detail_expired = db::select('select wek, utility_code, utility_name, location, dt, exp from
			(select utility_code, utility_name, location, DATE_FORMAT(exp_date, "%Y-%m-%d") as dt, IF(FLOOR((DayOfMonth(exp_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(exp_date)-1)/7)+1) - 1 as wek, 1 as exp from utilities where remark = "APAR" and DATE_FORMAT(exp_date,"%Y-%m") = "'.$ym.'") as expired
			where wek = '.$request->get('week').'
			union all
			select wek, utility_code, utility_name, location, dt, exp from
			(select utility_code, utility_name, location, DATE_FORMAT(entry_date, "%Y-%m-%d") as dt, IF(FLOOR((DayOfMonth(entry_date)-1)/7)+1 = 1,2,FLOOR((DayOfMonth(entry_date)-1)/7)+1) - 1 as wek, 0 as exp from utilities where remark = "APAR" and DATE_FORMAT(entry_date,"%Y-%m") = "'.$ym.'") as entry
			where wek ='.$request->get('week'));

		// return dd($detail_expired);

		$response = array(
			'status' => true,
			'check_detail_list' => $detail_cek,
			'replace_list' => $detail_expired,
			// 'query' => DB::getQueryLog()
		);
		return Response::json($response);
	}

	public function check_apar_use(request $request)
	{

		$use = new UtilityUse;

		$use->utility_id = $request->get('utility_id');
		$use->created_by = Auth::user()->username;

		$use->save();

		$response = array(
			'status' => true,
			'message' => 'Berhasil'
		);
		return Response::json($response);

	}

	public function fetch_apar_use(Request $request)
	{
		$apar_use = UtilityUse::leftJoin('utilities', 'utilities.id', '=', 'utility_uses.utility_id')->select('utility_code', 'utility_name', 'location', 'group', 'remark', 'utility_uses.created_at')->orderBy('created_at', "DESC")->get();

		$response = array(
			'status' => true,
			'use_list' => $apar_use
		);
		return Response::json($response);
	}

	public function delete_history(Request $request)
	{
		$ck = UtilityCheck::find($request->get('id_check'));

		$ck->delete();

		$cek_apar_ck = UtilityCheck::where('utility_id', '=', $ck->utility_id)->orderBy('id', 'desc')->first();

		if ($cek_apar_ck) {
			if (strpos($cek_apar_ck->check, '0') !== false) {
				$stat = 'NG';
			} else {
				$stat = null;
			}

			Utility::where('id', $ck->utility_id)
			->update(['status' => $stat, 'last_check' => $cek_apar_ck->created_at]);

		} else {
			Utility::where('id', $ck->utility_id)
			->update(['status' => null]);
		}

		$response = array(
			'status' => true,
			'datas' => $cek_apar_ck
		);
		return Response::json($response);
	}

	public function apar_order(Request $request)
	{
		try {
			if ($request->get('param') == 'order') {
				$utl_id = $request->get('utility_id');

				for ($i=0; $i < count($utl_id); $i++) { 
					$order = new UtilityOrder;
					$order->utility_id = $utl_id[$i];
					$order->no_pr = $request->get('pr_num');
					$order->pr_date = $request->get('pr_date');
					$order->created_by = Auth::user()->username;
					$order->save();
				}

			} else {
				UtilityOrder::where('utility_id', $request->get('utility_id'))
				->where('order_date', $request->get('order_date'))
				->update(['ready_date' => date('Y-m-d'), 'order_status' => 'Ready']);
			}

			$response = array(
				'status' => true,
				'message' => 'Berhasil'
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

	public function fetchInventory(Request $request)
	{
		$inv = MaintenanceInventory::select('part_number', 'part_name', 'category', 'location', 'specification', 'maker', 'user', 'stock', 'uom', 'min_stock', 'max_stock', 'updated_at')->get();

		$response = array(
			'status' => true,
			'inventory' => $inv
		);
		return Response::json($response);
	}

	public function fetchPartbyCode(Request $request)
	{
		try {
			$inv_code = MaintenanceInventory::where('part_number', '=', $request->get('code'))->select('part_number', 'part_name', 'specification', 'stock', 'uom')->first();

			$response = array(
				'status' => true,
				'datas' => $inv_code
			);
			return Response::json($response);
		} catch (QueryException $e) {
			$response = array(
				'status' => false,
				'message' => $e->gertMessage()
			);
			return Response::json($response);
		}
	}

	public function postInventory(Request $request)
	{
		try {
			$prt = $request->get('part');

			if ($request->get('stat') == 'in') {
				for ($i=0; $i < count($prt); $i++) { 
					$inventory = MaintenanceInventory::where('part_number', $prt[$i][0])->first();

					MaintenanceInventory::where('part_number', $prt[$i][0])
					->update(['stock' => $inventory->stock + $prt[$i][1] ]);

					$inv_log = new MaintenanceInventoryLog;
					$inv_log->part_number = $prt[$i][0];
					$inv_log->status = "in";
					$inv_log->quantity = $prt[$i][1];
					$inv_log->created_by = Auth::user()->username;
					$inv_log->save();
				}
			} else {
				for ($i=0; $i < count($prt); $i++) { 
					$inventory = MaintenanceInventory::where('part_number', $prt[$i][0])->first();

					MaintenanceInventory::where('part_number', $prt[$i][0])
					->update(['stock' => $inventory->stock - $prt[$i][1] ]);

					$inv_log = new MaintenanceInventoryLog;
					$inv_log->part_number = $prt[$i][0];
					$inv_log->status = "out";
					$inv_log->quantity = $prt[$i][1];
					$inv_log->remark1 = $request->get('ket');
					$inv_log->remark2 = $request->get('ket2');
					$inv_log->created_by = Auth::user()->username;
					$inv_log->save();
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Berhasil'
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

	public function inventory_save(Request $request)
	{
		$inv = new MaintenanceInventory;
		$inv->part_number = $request->get('part_number');
		$inv->item_number = $request->get('item_number');
		$inv->part_name = $request->get('part_name');
		$inv->category = $request->get('category');
		$inv->specification = $request->get('specification');
		$inv->maker = $request->get('maker');
		$inv->location = $request->get('location');
		$inv->stock = $request->get('stock');
		$inv->min_stock = $request->get('min');
		$inv->max_stock = $request->get('max');
		$inv->uom = $request->get('uom');
		$inv->user = $request->get('user');
		$inv->created_by = Auth::user()->username;

		$inv->save();

		$response = array(
			'status' => true,
			'message' => 'Berhasil'
		);
		return Response::json($response);
	}

	public function fetchInventoryPart(Request $request)
	{
		$inv_code = MaintenanceInventory::where('part_number', '=', $request->get('part_number'))->first();

		$response = array(
			'status' => true,
			'message' => 'Berhasil',
			'datas' => $inv_code
		);
		return Response::json($response);
	}

	public function inventory_edit(Request $request)
	{
		try {
			MaintenanceInventory::where('part_number', $request->get('part_number'))
			->update([
				'item_number' => $request->get('item_number'),
				'part_name' => $request->get('part_name'),
				'category' => $request->get('category'),
				'specification' => $request->get('specification'),
				'maker' => $request->get('maker'),
				'location' => $request->get('location'),
				'min_stock' => $request->get('min'),
				'max_stock' => $request->get('max'),
				'uom' => $request->get('uom'),
				'user' => $request->get('user')
			]);

			$response = array(
				'status' => true,
				'message' => 'Berhasil'
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

	public function fetchPM(Request $request)
	{
		$dt = date("F");
		$dt = strtolower($dt);

		if ($request->get('mon')) {
			$select = db::raw($request->get('mon')." as mon");
		} else {
			$select = db::raw('april, may, june, july, august, september, october, november, december, january, february, march');
		}


		$pms = MaintenancePlan::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'maintenance_plans.pic')
		->select("maintenance_plans.id", "item_check", "quantity", "category", "maintenance_plans.status", "schedule", "name", "fiscal", $select);

		if ($request->get('ctg')) {
			$pms = $pms->where('category', '=', $request->get('ctg'));
		}

		if ($request->get('fy')) {
			$pms = $pms->where('fiscal', '=', $request->get('fy'));
		}

		$pms = $pms->get();

		$daily = db::table("maintenance_plan_logs")->get();

		$response = array(
			'status' => true,
			'datas' => $pms,
			'daily' => $daily
		);
		return Response::json($response);
	}

	public function fetchMachine(Request $request)
	{
		$datas = MaintenancePlanItem::select('description', 'machine_name', 'machine_id', 'area')
		->where('category', '=', $request->get('kategori'))
		->get();

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	}

	public function importPM(Request $request)
	{
		if($request->hasFile('excel_file')) {
			try{				
				$file = $request->file('excel_file');
				$file_name = 'import_pm.'.$file->getClientOriginalExtension();
				$file->move(public_path('maintenance/'), $file_name);

				$excel = public_path('maintenance/') . $file_name;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					//Skip Header
					$reader->skipRows(1);
				})->get();
				$rows = $rows->toArray();

				for ($i=0; $i < count($rows); $i++) {
					$plan = MaintenancePlan::firstOrNew(array('item_check' => $rows[$i][1], 'fiscal' => $rows[$i][4]));
					$plan->category = $rows[$i][2];
					$plan->status = $rows[$i][3];
					$plan->schedule = $rows[$i][6];
					$plan->pic = $rows[$i][7];
					$plan->quantity = $rows[$i][5];
					$plan->april = $rows[$i][8];
					$plan->mei = $rows[$i][9];
					$plan->juni = $rows[$i][10];
					$plan->juli = $rows[$i][11];
					$plan->agustus = $rows[$i][12];
					$plan->september = $rows[$i][13];
					$plan->oktober = $rows[$i][14];
					$plan->november = $rows[$i][15];
					$plan->desember = $rows[$i][16];
					$plan->januari = $rows[$i][17];
					$plan->februari = $rows[$i][18];
					$plan->maret = $rows[$i][19];
					$plan->created_by = Auth::user()->username;

					$plan->save();

				}		

				return redirect('/index/maintenance/planned/master')->with('status', 'Upload Schedule success')->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
			}catch(QueryException $e){
				return redirect('/index/maintenance/planned/master')->with('error', $e->getMessage())->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
			}
		}else{
			return redirect('/index/maintenance/planned/master')->with('error', 'File not Found')->with('page', 'Planned Maintenance Data')->with('head', 'Maintenance');
		}		
	}

	public function openSPKPending(Request $request)
	{
		try {
			MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
			->update(['remark' => 2, 'note' => $request->get('reason') ]);

			MaintenanceJobOrderLog::where("order_no", "=", $request->get("order_no"))->delete();

			MaintenanceJobProcess::where("order_no", "=", $request->get("order_no"))->delete();

			$spk_log = new MaintenanceJobOrderLog;
			$spk_log->order_no = $request->get('order_no');
			$spk_log->remark = 2;
			$spk_log->created_by = Auth::user()->username;

			$response = array(
				'status' => true,
				'message' => 'success'
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

	public function postPlannedCheck(Request $request)
	{
		// try {
		// 	foreach ($request->get('check_list') as $val) {
		// 		$mtc_check = new MaintenancePlanCheck;
		// 		$mtc_check->item_code = $request->get('item_check');

		// 		$mtc_check->item_check = $val[0];
		// 		$mtc_check->substance = $val[1];
		// 		$mtc_check->remark = $val[2];

		// 		if ($val[3] == '1') {
		// 			$mtc_check->check = 'OK';
		// 		} else {
		// 			$mtc_check->check = 'NG';
		// 		}

		// 		$mtc_check->check_value = $val[4];
		// 		$mtc_check->created_by = $request->get('operator');

		// 		$mtc_check->save();
		// 	}

		// 	$response = array(
		// 		'status' => true,
		// 		'message' => 'success'
		// 	);
		// 	return Response::json($response);
		// } catch (QueryException $e) {
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => $e->getMessage()
		// 	);
		// 	return Response::json($response);
		// }
		
		$verif = true;
		if ($request->get('ng')) {
			$arr_ng = $request->get('ng');

			foreach ($request->get('ng') as $ngs) {
				if ($request->session()->get('pm.description'.$ngs) == "" || $request->session()->get('pm.before'.$ngs) == "") {
					$verif = false;
				}
			}
		} else {
			$arr_ng = [];
		} 

		if ($verif == false) {
			$response = array(
				'status' => false,
				'message' => 'Harap melengkapi kolom NG'
			);
			return Response::json($response);
		} else {
			$mtc_item_check = MaintenancePlanItemCheck::select('id','machine_name', 'item_check', 'substance', 'remark')->whereIn('id', $request->get('ids'))->get();

			// $mtc_item_check = MaintenancePlanItem::select('id','machine_name', 'item_check', 'substance', 'remark')->where('machine_name', '')->get();

			foreach ($mtc_item_check as $itm) {
				$mtc_check = new MaintenancePlanCheck;
				if (in_array($itm->id, $arr_ng))
				{
					$remark = $request->session()->get('pm.description'.$itm->id);
					$before = $request->session()->get('pm.before'.$itm->id);
					$after = $request->session()->get('pm.after'.$itm->id);

					$before_name = $request->get('operator')."_before_".$itm->id."_".date('Y-m-d H:i');
					$after_name = $request->get('operator')."_after_".$itm->id."_".date('Y-m-d H:i');

					file_put_contents(public_path('images/planned_maintenance')."/".$before_name.".png", $before);
					file_put_contents(public_path('images/planned_maintenance')."/".$after_name.".png", $after);

					$check = "NG";

				} else {
					$check = "OK";
					$before_name = null;
					$after_name = null;
					$remark = null;
				}
				$mtc_check->item_code = $itm->machine_name;

				$mtc_check->item_check = $itm->item_check;
				$mtc_check->substance = $itm->substance;
				$mtc_check->period = $itm->remark;
				$mtc_check->check = $check;
				$mtc_check->photo_before = $before_name;
				$mtc_check->photo_after = $after_name;
				$mtc_check->remark = $remark;

				// $mtc_check->check_value = $val[4];
				$mtc_check->created_by = $request->get('operator');

				$mtc_check->save();
			}

			$response = array(
				'status' => true,
				'message' => 'OK'
			);
			return Response::json($response);
		}
		
	}

	// public function getHistoryPlanned(Request $request)
	// {
	// 	$history = MaintenancePlanCheck::select('item_code', db::raw('GROUP_CONCAT(DISTINCT `check`) as ck'), db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i') as dt"), db::raw('GROUP_CONCAT(DISTINCT created_by) as pic'))
	// 	->where('item_code', '=', $request->get('item_code'))
	// 	->where('remark', '=', $request->get('period'))
	// 	->groupBy('item_code', db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i')"))
	// 	->orderBy(db::raw("DATE_FORMAT(created_at,'%Y-%m-%d %H:%i')"), 'desc')
	// 	->get();

	// 	$response = array(
	// 		'status' => true,
	// 		'datas' => $history
	// 	);
	// 	return Response::json($response);
	// }

	public function fetchItemCheckList(Request $request)
	{
		$datas = MaintenancePlanItemCheck::where("machine_name", "=", $request->get('item_no'))->get();

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	}

	public function postInventoryStock(Request $request)
	{
		try {
			
			$inventory = MaintenanceInventory::where('part_number', '=', $request->get('material_number'))->first();

			if ($request->get('status') == 'out') {
				MaintenanceInventory::where('part_number', $request->get('material_number'))
				->update(['stock' => $inventory->stock - 1 ]);

				$inv_log = new MaintenanceInventoryLog;
				$inv_log->part_number = $request->get('material_number');
				$inv_log->status = $request->get('status');
				$inv_log->quantity = 1;
				$inv_log->created_by = $request->get('employee_id');

				$inv_log->save();
			} else {

			}
			

			$response = array(
				'status' => true,
				'message' => 'success'
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

	public function fetchMaintenanePic(Request $request)
	{
		$pics = MaintenancePic::select('pic_id', 'pic_name', db::raw('GROUP_CONCAT(item_name) as item'), db::raw('GROUP_CONCAT(skill) as skill'))->groupBy('pic_id', 'pic_name')->get();

		$response = array(
			'status' => true,
			'datas' => $pics
		);
		return Response::json($response);
	}

	public function fetchPlanedMonitoring(Request $request)
	{
		$satu_hari = "SELECT masters.machine_name, description from
		(SELECT machine_name FROM `maintenance_plan_item_checks` where remark = '1-HARI' group by machine_name) as masters
		left join maintenance_plan_items on maintenance_plan_items.machine_name = masters.machine_name";

		$response = array(
			'status' => true,
			'datas' => $satu_hari
		);
		return Response::json($response);
	}

	public function postPlannedSession(Request $request)
	{
		$request->session()->put('pm.description'.$request->get('id'), $request->get('desc'));
		$request->session()->put('pm.before'.$request->get('id'), $request->get('before'));
		$request->session()->put('pm.after'.$request->get('id'), $request->get('after'));

		// $request->session()->forget('pm');

		$datas = $request->session()->get('pm.before'.$request->get('id'));

		$response = array(
			'status' => true,
			'datas' => $datas,
			'session' => $request->session()->all()
		);
		return Response::json($response);
	}

	public function getPlannedSession(Request $request)
	{
		$datas = [];
		$datas['desc'] = $request->session()->get('pm.description'.$request->get('id'));
		$datas['before'] = $request->session()->get('pm.before'.$request->get('id'));
		$datas['after'] = $request->session()->get('pm.after'.$request->get('id'));

		return Response::json($datas);
	}

	public function getPlannedSchedule(Request $request)
	{
		$daily = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
		->where('maintenance_plan_item_checks.remark', 'like', '%HARI%')
		->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
		->get();

		$weekly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
		->where('maintenance_plan_item_checks.remark', 'like', '%MINGGU%')
		->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
		->get();

		$monthly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
		->where('maintenance_plan_item_checks.remark', 'like', '%BULAN%')
		->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
		->get();

		$yearly = MaintenancePlanItemCheck::leftJoin('maintenance_plan_items', 'maintenance_plan_items.machine_name', '=', 'maintenance_plan_item_checks.machine_name')
		->where('maintenance_plan_item_checks.remark', 'like', '%TAHUN%')
		->select('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->groupBy('maintenance_plan_item_checks.machine_name', 'maintenance_plan_item_checks.remark', 'maintenance_plan_items.category')
		->orderBy('maintenance_plan_item_checks.machine_name', 'asc')
		->get();

		$last_date = date("t");
		$date = date("d");

		$weeks = db::select('SELECT week_name FROM weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = DATE_FORMAT(now(),"%Y-%m") group by week_name');

		$mons = db::select('SELECT DATE_FORMAT(week_date,"%b %y") mon from weekly_calendars where fiscal_year = "FY197" 
			group by DATE_FORMAT(week_date,"%b %y")
			order by week_date asc');

		$response = array(
			'status' => true,
			'daily' => $daily,
			'weekly' => $weekly,
			'monthly' => $monthly,
			'yearly' => $yearly,
			'dt' => $last_date,
			'now' => $date,
			'week' => $weeks,
			'mon' => $mons
		);
		return Response::json($response);
	}

	public function closePendingVendor(Request $request)
	{		
		// DB::transaction(function () use ($request) {
		MaintenanceJobPending::where('order_no', '=', $request->get('spk_number'))
		->update([
			'description' => $request->get('vendor_po')." ~ ".$request->get('vendor_name'), 
			'time' => $request->get('vendor_start')." ~ ".$request->get('vendor_finish')
		]);

		MaintenanceJobOrder::where('order_no', $request->get('spk_number'))
		->update(['remark' => 7 ]);

		$spk_log = new MaintenanceJobOrderLog;
		$spk_log->order_no = $request->get('order_no');
		$spk_log->remark = 7;
		$spk_log->created_by = Auth::user()->username;
		$spk_log->save();

		// });

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function fetchSPKUrgentReport(Request $request)
	{
		$master = MaintenanceJobOrder::leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin(db::raw('(SELECT process_code ,process_name from processes where remark = "maintenance") as process'), 'process.process_code', '=', 'maintenance_job_orders.remark')
		->where("priority", "=", "Urgent")
		->select("order_no", db::raw("name AS requester"), "priority", "category", "description", "process_name", "note")
		->get();

		$details = db::select("select maintenance_job_orders.order_no, maintenance_job_reports.operator_id, cause, handling, photo, maintenance_job_processes.operator_id as operator_process, start_actual, finish_actual from maintenance_job_orders 
			left join maintenance_job_reports on maintenance_job_reports.order_no = maintenance_job_orders.order_no
			left join maintenance_job_processes on maintenance_job_processes.order_no = maintenance_job_orders.order_no
			where priority = 'Urgent' and maintenance_job_orders.remark < 6");

		$response = array(
			'status' => true,
			'datas' => $master
		);
		return Response::json($response);
	}

	public function receiptSPK(Request $request)
	{
		MaintenanceJobOrder::where('order_no', '=', $request->get('order_no'))
		->update(['remark' => 7]);

		$spk_log = MaintenanceJobOrderLog::firstOrNew(array(
			'order_no' => $request->get('order_no'),
			'remark' => 7
		));
		$spk_log->created_by = Auth::user()->username;
		$spk_log->save();

		MaintenanceJobReport::where('order_no', '=', $request->get('order_no'))
		->where('operator_id', '=', Auth::user()->username)
		->update(['receipt_id' => $request->get('employee_id')]);

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function exportSPKList(Request $request)
	{
		DB::connection()->enableQueryLog();
		$maintenance_job_orders = MaintenanceJobOrder::whereNull('maintenance_job_processes.deleted_at')
		->leftJoin("employee_syncs", "employee_syncs.employee_id", "=", "maintenance_job_orders.created_by")
		->leftJoin("maintenance_job_processes", "maintenance_job_processes.order_no", "=", "maintenance_job_orders.order_no")
		->leftJoin('maintenance_job_reports', function($join)
		{
			$join->on('maintenance_job_reports.order_no', '=', 'maintenance_job_processes.order_no');
			$join->on('maintenance_job_reports.operator_id','=', 'maintenance_job_processes.operator_id');
		})
		->leftJoin("maintenance_job_pendings", "maintenance_job_orders.order_no", "=", "maintenance_job_pendings.order_no")
		->leftJoin(db::raw("employee_syncs as  es"), "es.employee_id", "=", "maintenance_job_processes.operator_id")
		->leftJoin(db::raw("(select process_code, process_name from processes where remark = 'maintenance') AS process"), "maintenance_job_orders.remark", "=", "process.process_code")
		->leftJoin("maintenance_plan_items", "maintenance_plan_items.machine_id", "=", "maintenance_job_orders.machine_name")
		->select("maintenance_job_orders.order_no", "employee_syncs.name", db::raw('DATE_FORMAT(maintenance_job_orders.created_at, "%Y-%m-%d %H:%i") as date'), "priority", "maintenance_job_orders.section", "type", "maintenance_job_orders.category", "machine_condition", "danger", "maintenance_job_orders.description", "safety_note", "target_date", "process_name", db::raw("es.name as name_op"), db::raw("es.employee_id as id_op"), db::raw("DATE_FORMAT(maintenance_job_processes.start_actual, '%Y-%m-%d %H:%i') start_actual"), db::raw("DATE_FORMAT(maintenance_job_processes.finish_actual, '%Y-%m-%d %H:%i') finish_actual"), "maintenance_job_pendings.status", db::raw("maintenance_job_pendings.description as pending_desc"), "maintenance_job_orders.machine_name", "cause", "handling", "photo", "note", "machine_remark", db::raw("maintenance_plan_items.description as machine_desc"), "maintenance_plan_items.location");

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
			$maintenance_job_orders = $maintenance_job_orders->whereIn('maintenance_job_orders.remark', [5,6]);
		}
		if(strlen($request->get('status')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_pendings.status', '=', $request->get('status'));
		}
		if(strlen($request->get('username')) > 0){
			$maintenance_job_orders = $maintenance_job_orders->where('maintenance_job_orders.created_by', '=', $request->get('username'));
		}

		$maintenance_job_orders = $maintenance_job_orders->orderBy('maintenance_job_orders.created_at', 'desc')
		->get();

		$data = array(

			'maintenance_job_orders' => $maintenance_job_orders,
			'query' => DB::getQueryLog()
		);
		ob_clean();
		Excel::create('List SPK', function($excel) use ($data){
			$excel->sheet('SPK', function($sheet) use ($data) {
				return $sheet->loadView('maintenance.spk_excel', $data);
			});
		})->export('xlsx');
	}
}
