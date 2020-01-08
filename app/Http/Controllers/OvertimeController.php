<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\CodeGenerator;
use App\BreakTime;
use App\Overtime;
use App\OvertimeDetail;
use App\OrganizationStructure;
use App\Mutationlog;
use App\WeeklyCalendar;
use PDF;
use Dompdf\Dompdf;

class OvertimeController extends Controller
{

	public function __construct(){
		$this->middleware('auth');
		$this->transport = [
			'-',
			'Bangil',
			'Pasuruan',
		];
		$this->day_status = [
			'Workday',
			'Holiday',
		];
		$this->shift = [
			1,
			2,
			3,
		];

		$purposes = db::table('overtime_purposes')->select('purpose')->get();
		$this->purpose = $purposes;
	}

	public function indexOvertimeConfirmation(){
		return view('overtimes.overtime_confirmation')->with('page', 'Overtime Confirmation');
	}

	public function indexOvertimeControl(){
		return view('overtimes.reports.control_report')->with('page', 'Overtime Control');
	}

	public function indexReportSection(){

		$query = "select cost_center, cost_center_name from cost_centers group by cost_center, cost_center_name order by cost_center_name ASC";
		$cc = db::select($query);

		return view('overtimes.reports.overtime_section', array(
			'title' => 'Overtime by Section',
			'title_jp' => '残業 課別',
			'cost_center' => $cc))->with('page', 'Overtime by Section');
	}

	public function indexReportControlFq()
	{
		return view('overtimes.reports.overtime_monthly', array(
			'title' => 'Monthly Overtime Control',
			'title_jp' => '月次残業管理'))->with('page', 'Overtime Monthly Control Forecast');
	}

	public function indexReportControlBdg()
	{
		return view('overtimes.reports.overtime_monthly_budget', array(
			'title' => 'Monthly Overtime Control',
			'title_jp' => '月次残業管理'))->with('page', 'Overtime Monthly Control Budget');
	}

	public function indexReportOutsouce()
	{
		return view('overtimes.reports.overtime_outsource', array(
			'title' => 'Overtime Outsource Control',
			'title_jp' => '派遣社員の残業管理'))->with('page', 'Overtime Outsource Employee');
	}

	public function indexMonthlyResume()
	{
		$fiscal_years = db::select("select DISTINCT fiscal_year from weekly_calendars order by fiscal_year asc");
		$costcenters = db::select("select DISTINCT cost_center, cost_center_name from ympimis.cost_centers order by cost_center_name asc");

		return view('overtimes.reports.overtime_resume', array(
			'title' => 'Overtime Resume',
			'title_jp' => '残業のまとめ',
			'fiscal_years' => $fiscal_years,
			'costcenters' => $costcenters
		))->with('page', 'Overtime Monthly Resume');
	}

	public function indexOvertimeOutsource()
	{
		return view('overtimes.reports.overtime_data_outsource', array(
			'title' => 'Overtime Outsource Data',
			'title_jp' => '派遣社員の残業データ'))->with('page', 'Overtime Outsource');
	}

	public function indexPrint($id)
	{
		$ot = Overtime::leftJoin("overtime_details","overtimes.overtime_id","=","overtime_details.overtime_id")
		->leftJoin("employees","employees.employee_id","=","overtime_details.employee_id")
		->where("overtimes.overtime_id","=",$id)
		->whereNull("overtime_details.deleted_at")
		->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id","employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time","overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center")
		->get();

		return view('overtimes.overtime_forms.index_print', array(
			'datas' => $ot
		));
	}

	public function indexOvertimeForm(){
		$code_generator = CodeGenerator::where('note', '=', 'OT')->first();
		if($code_generator->prefix != date('ym')){
			db::table('code_generators')->where('note', '=', 'OT')->update([
				'prefix' => strtoupper(date('ym')),
				'index' => 0,
			]);
		}
		return view('overtimes.overtime_forms.index')->with('page', 'Overtime Form');
	}

	public function indexOvertimeData(){
		$title = 'Overtime Data';
		$title_jp = '残業データ';

		$code;
		$cost_center = db::select('select cost_center from ympimis.cost_centers group by cost_center');
		$department = db::select("select child_code from organization_structures where remark = '".'department'."'");
		$section = db::select("select child_code from organization_structures where remark = '".'section'."'");
		$group = db::select("select child_code from organization_structures where remark = '".'group'."'");

		return view('overtimes.reports.overtime_data', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'cost_centers' => $cost_center,
			'departments' => $department,
			'sections' => $section,
			'groups' => $group
		));
	}

	public function indexOvertimeByEmployee(){
		$title = 'Employee Monthly Overtime';
		$title_jp = '社員番号別残業管理';
		$department = db::select("select child_code from organization_structures where remark = '".'department'."'");
		$section = db::select("select child_code from organization_structures where remark = '".'section'."'");
		$nik = db::select("SELECT employee_id from employees where end_date is null");

		return view('overtimes.reports.overtime_by_employee', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'departments' => $department,
			'sections' => $section,
			'niks' => $nik
		));
	}

	public function indexGAReport()
	{
		$title = 'GA - Report';
		$title_jp = '';

		return view('overtimes.reports.ga_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'GA Report')->with('head', 'Overtime Report');
	}

	public function createOvertimeForm(){
		$code_generator = CodeGenerator::where('note', '=', 'OT')->first();
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$ot_id = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		$transports = $this->transport;
		$day_statuses = $this->day_status;
		$shifts = $this->shift;

		$purposes = db::table('overtime_purposes')->get();
		$sections = db::table('organization_structures')->where('remark','=','section')->get();

		return view('overtimes.overtime_forms.create', array(
			'ot_id' => $ot_id,
			'transports' => $transports,
			'day_statuses' => $day_statuses,
			'purposes' => $purposes,
			'sections' => $sections,
			'shifts' => $shifts
		))->with('page', 'Overtime Form');
	}

	public function selectDivisionHierarchy(Request $request){
		$hierarchies = db::table('organization_structures')
		->where([['remark', '=', $request->get('remark')],['parent_name','=',$request->get('parent')]])
		->get();
		$response = array(
			'status' => true,
			'hierarchies' => $hierarchies,
		);
		return Response::json($response);
	}

	public function fetchMonthlyResume(Request $request){
		$fy = '';
		$cc_ot = '';
		$cc_all = '';

		if($request->get('fy') != null){
			$fy =  $request->get('fy');
		}else{
			$get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
			foreach ($get_fy as $key) {
				$fy = $key->fiscal_year;
			}
		}

		if($request->get('cc') != null) {
			$ccs = $request->get('cc');
			$cc = "";

			for($x = 0; $x < count($ccs); $x++) {
				$cc = $cc."'".$ccs[$x]."'";
				if($x != count($ccs)-1){
					$cc = $cc.",";
				}
			}
			$cc_ot = "and ml.cost_center in (".$cc.") ";
			$cc_all = "where cost_center in (".$cc.") ";
		}

		$query1 = "select wc.period, ot.total from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(select DISTINCT DATE_FORMAT(ovr.tanggal,'%m-%Y') as period, sum(ovr.ot) as total from
		(select tanggal, nik, SUM(IF(status = 0, jam, final)) ot from over_time
		left join over_time_member on over_time.id = over_time_member.id_ot
		where deleted_at is null and jam_aktual = 0 and nik not like 'osd%' group by tanggal, nik) ovr
		left join
		(select employee_id, cost_center from ympimis.mutation_logs where valid_to is null) ml
		on ovr.nik = ml.employee_id
		left join
		(select distinct cost_center, cost_center_name from ympimis.cost_centers) cc on cc.cost_center = ml.cost_center
		where ml.cost_center is not null ".$cc_ot."
		group by period
		order by period asc) ot
		on wc.period = ot.period";
		$ot_actual = db::connection('mysql3')->select($query1);

		$query2 = "select wc.period, bg.total_budget from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(select DATE_FORMAT(period,'%m-%Y') as period, ROUND(sum(budget),2) as total_budget
		from budgets ".$cc_all."
		group by period
		order by budgets.period asc) bg
		on wc.period = bg.period";
		$ot_budget = db::select($query2);

		$query3 = "select wc.period, fc.total_forecast from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(select DATE_FORMAT(date,'%m-%Y') as period, ROUND(sum(hour),2) as total_forecast
		from forecasts ".$cc_all."
		group by period
		order by date asc) fc
		on wc.period = fc.period";
		$ot_forecast = db::select($query3);

		$query4 = "select wc.period, mp.emp from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(	
		select count(c.employee_id) as emp, mon from
		(select * from 
		(
		select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
		cross join (
		select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy."' group by date_format(week_date, '%Y-%m')) s
		) m
		where hire_month <= mon and (mon < end_month OR end_month is null)
		) as b
		left join
		(
		select id, employment_logs.employee_id, employment_logs.status, date_format(employment_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(employment_logs.valid_to, '%Y-%m'), date_format((select max(week_date) from weekly_calendars), '%Y-%m')) as mon_to from employment_logs 
		WHERE id IN (
		SELECT MAX(id)
		FROM employment_logs
		GROUP BY employment_logs.employee_id, date_format(employment_logs.valid_from, '%Y-%m')
		)
		) as c on b.employee_id = c.employee_id
		left join
		(select employee_id, cost_center from ympimis.mutation_logs where valid_to is null) ml
		on c.employee_id = ml.employee_id
		left join
		(select distinct cost_center, cost_center_name from ympimis.cost_centers) cc on cc.cost_center = ml.cost_center
		where mon_from <= mon and mon_to >= mon and ml.cost_center is not null ".$cc_ot."
		group by mon
		) mp
		on wc.period = mp.mon";
		$mp_actual = db::select($query4);

		$query5 = "select wc.period, bg.total_budget_mp from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(select DATE_FORMAT(period,'%m-%Y') as period, sum(budget_mp) as total_budget_mp
		from manpower_budgets ".$cc_all."
		group by period
		order by manpower_budgets.period asc) bg
		on wc.period = bg.period;";
		$mp_budget = db::select($query5);

		$query6 = "select wc.period, fc.total_forecast_mp from
		(SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
		left join
		(select DATE_FORMAT(period,'%m-%Y') as period, sum(forecast_mp) as total_forecast_mp
		from manpower_forecasts ".$cc_all."
		group by period
		order by manpower_forecasts.period asc) fc
		on wc.period = fc.period";
		$mp_forecast = db::select($query6);

		$response = array(
			'status' => true,
			'ot_actual' => $ot_actual,
			'ot_budget' => $ot_budget,
			'ot_forecast' => $ot_forecast,
			'mp_actual' => $mp_actual,
			'mp_budget' => $mp_budget,
			'mp_forecast' => $mp_forecast,
			'fy' => $fy
		);
		return Response::json($response);
	}

	public function fetchEmployee(Request $request){
		$employee = db::table('employees')->where('employee_id', '=', $request->get('employee_id'))->first();
		$transports = $this->transport;
		$purposes = $this->purpose;

		$response = array(
			'status' => true,
			'employee' => $employee,
			'transports' => $transports,
			'purposes' => $purposes,
		);
		return Response::json($response);
	}

	public function fetchBreak(Request $request)
	{
		$hari = date('w',strtotime($request->get('tgl')));
		$break = BreakTime::where('day', '=', $hari)
		->where('break_times.shift','=', $request->get('shift'))
		->where('break_times.start','>=', $request->get('from'))
		->where('break_times.end','<=', $request->get('to'))
		->select(DB::raw("IFNULL(sum(TIME_TO_SEC(duration)),'0') as istirahat"))
		->first();

		$response = array(
			'status' => true,
			'break' => $break,
		);
		return Response::json($response);
	}

	public function fetchOvertimeData(Request $request){
		$tanggal = "";
		$addcode = "";
		$addcostcenter = "";
		$adddepartment = "";
		$addsection = "";
		$addgrup = "";

		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$tanggal = "and tanggal >= '".$datefrom."' ";
			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));
				$tanggal = $tanggal."and tanggal <= '".$dateto."' ";
			}
		}

		if($request->get('code') != null) {
			$codes = $request->get('code');
			$codelength = count($codes);
			$code = "";

			for($x = 0; $x < $codelength; $x++) {
				$code = $code."'".$codes[$x]."'";
				if($x != $codelength-1){
					$code = $code.",";
				}
			}
			$addcode = "and code in (".$code.") ";
		}

		if($request->get('costcenter') != null) {
			$costcenter = implode(",", $request->get('costcenter'));
			$addcostcenter = "and bagian.cost_center in (".$costcenter.") ";
		}

		if($request->get('department') != null) {
			$departments = $request->get('department');
			$deptlength = count($departments);
			$department = "";

			for($x = 0; $x < $deptlength; $x++) {
				$department = $department."'".$departments[$x]."'";
				if($x != $deptlength-1){
					$department = $department.",";
				}
			}
			$adddepartment = "and bagian.department in (".$department.") ";
		}

		if($request->get('section') != null) {
			$sections = $request->get('section');
			$sectlength = count($sections);
			$section = "";

			for($x = 0; $x < $sectlength; $x++) {
				$section = $section."'".$sections[$x]."'";
				if($x != $sectlength-1){
					$section = $section.",";
				}
			}
			$addsection = "and bagian.section in (".$section.") ";
		}

		if($request->get('group') != null) {
			$groups = $request->get('group');
			$grplen = count($groups);
			$group = "";

			for($x = 0; $x < $grplen; $x++) {
				$group = $group."'".$groups[$x]."'";
				if($x != $grplen-1){
					$group = $group.",";
				}
			}
			$addgrup = "and bagian.group in (".$group.") ";
		}


		$overtimeData = db::connection('mysql3')->select("select distinct ovr.tanggal, ovr.nik, ovr.id_overtime, emp.name, bagian.cost_center, bagian.department, bagian.section, bagian.group, ot, keperluan, code from
			(select tanggal, nik, SUM(IF(status = 0, jam, final)) ot, GROUP_CONCAT(id_ot) as id_overtime, GROUP_CONCAT(keperluan) keperluan from over_time_member left join over_time on over_time.id = over_time_member.id_ot
			where deleted_at is null and jam_aktual = 0 ".$tanggal." group by tanggal, nik) ovr
			left join ympimis.employees as emp on emp.employee_id = ovr.nik
			left join (select employee_id, cost_center, division, department, section, sub_section, `group` from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
			left join ympimis.cost_centers on ympimis.cost_centers.section = bagian.section and ympimis.cost_centers.sub_sec = bagian.sub_section and ympimis.cost_centers.group = bagian.group
			where ot > 0 ".$addcostcenter."".$adddepartment."".$addsection."".$addcode."".$addgrup."
			order by ot asc
			");

		return DataTables::of($overtimeData)->make(true);
		
	}

	public function saveOvertimeHead(Request $request)
	{
		$ot_id = $request->get('ot_id');

		$org = db::select("select dep.child_code as department, divs.child_code as division from 
			(SELECT  parent_name, child_code FROM `organization_structures` where remark='section') sec
			join (SELECT  parent_name, child_code, status FROM `organization_structures` where remark='department') dep
			on sec.parent_name = dep.status
			join (SELECT child_code, status FROM `organization_structures` where remark='division') divs
			on divs.status = dep.parent_name
			where sec.child_code = '".$request->get('section')."'");

		$section = $request->get('section');
		$sub_section = $request->get('sub_section');
		$group = $request->get('group');
		$ot_date = date('Y-m-d',strtotime($request->get('ot_date')));
		$ot_day = $request->get('ot_day');
		$shift = $request->get('shift');
		$remark = $request->get('remark');

		$overtime = new Overtime([
			'overtime_id' => $ot_id,
			'overtime_date' => $ot_date,
			'day_status' => $ot_day,
			'shift' => $shift,
			'division' => $org[0]->division,
			'department' => $org[0]->department,
			'section' => $section,
			'subsection' => $sub_section,
			'group' => $group,
			'remark' => $remark,
			'created_by' => 1
		]);
		$overtime->save();	


		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function saveOvertimeDetail(Request $request)
	{
		$ot_id = $request->get('ot_id');
		$emp_ids = $request->get('emp_ids');
		$ot_starts = $request->get('ot_starts');
		$ot_ends = $request->get('ot_ends');
		$ot_hours = $request->get('ot_hours');
		$ot_transports = $request->get('ot_transports');
		$ot_foods = $request->get('ot_foods');
		$ot_efoods = $request->get('ot_efoods');
		$ot_purposes = $request->get('ot_purposes');
		$ot_remarks = $request->get('ot_remarks');
		$ot_statuses = $request->get('ot_statuses');

		for ($i=0; $i < sizeof($emp_ids); $i++) {
			$emp = db::table('mutation_logs')
			->where('employee_id','=', $emp_ids[$i])
			->whereNull('valid_to')
			->select('cost_center')
			->get();

			$overtime_detail = new OvertimeDetail([
				'overtime_id' => $ot_id,
				'employee_id' => $emp_ids[$i],
				'cost_center' => $emp[0]->cost_center,
				'food' => $ot_foods[$i],
				'ext_food' => $ot_efoods[$i],
				'transport' => $ot_transports[$i],
				'start_time' => $ot_starts[$i],
				'end_time' => $ot_ends[$i],
				'purpose' => $ot_purposes[$i],
				'remark' => $ot_remarks[$i],
				'final_hour' => $ot_hours[$i],
				'final_overtime' => '0',
				'status' => '0',
				'ot_status' => $ot_statuses[$i],
				'created_by' => Auth::user()->username
			]);
			$overtime_detail->save();
		}		


		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function editOvertimeDetail(Request $request)
	{
		$ot_id = $request->get('ot_id');
		$emp_ids = $request->get('emp_ids');
		$ot_starts = $request->get('ot_starts');
		$ot_ends = $request->get('ot_ends');
		$ot_hours = $request->get('ot_hours');
		$ot_transports = $request->get('ot_transports');
		$ot_foods = $request->get('ot_foods');
		$ot_efoods = $request->get('ot_efoods');
		$ot_purposes = $request->get('ot_purposes');
		$ot_remarks = $request->get('ot_remarks');
		$ot_statuses = $request->get('ot_statuses');

		$emp_id2 = $emp_ids;

		$datas = OvertimeDetail::where('overtime_id',$ot_id)->get();
		foreach ($datas as $ot) {
			if (!in_array($ot->employee_id, $emp_ids)) {
				OvertimeDetail::where('overtime_id',$ot_id)->where('employee_id',$ot->employee_id)->delete();
			}
		}

		for ($i=0; $i < sizeof($emp_ids); $i++) {
			$overtime_detail = OvertimeDetail::where('overtime_id', '=', $ot_id)
			->where('employee_id', '=', $emp_ids[$i])
			->update([
				'food' => $ot_foods[$i],
				'ext_food' => $ot_efoods[$i],
				'transport' => $ot_transports[$i],
				'start_time' => $ot_starts[$i],
				'end_time' => $ot_ends[$i],
				'purpose' => $ot_purposes[$i],
				'remark' => $ot_remarks[$i],
				'final_hour' => $ot_hours[$i],
				'ot_status' => $ot_statuses[$i]
			]);
		}	

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function fetchOvertimeConfirmation(){

		$username = Auth::user()->username;
		$role = Auth::user()->role_code;

		$dep = db::connection('mysql3')->table('karyawan')
		->leftJoin('posisi', 'posisi.nik', '=', 'karyawan.nik')
		->where('karyawan.nik', '=', $username)
		->select('id_dep')
		->first();

		if ($role == 'HR-SPL' || $role == 'S'){
			$where = '';
		} else {
			$where = "and posisi.id_dep = '" . $dep->id_dep . "'";
		}

		$queryConfirmation = "select b.id, date_format(a.tanggal,'%d-%b-%y') as tanggal, a.nik, a.nama_karyawan as name, a.masuk, a.keluar, b.plan_ot, a.jam as act_log, a.jam-b.plan_ot as diff, a.section, b.hari, b.dari, b.sampai from
		( SELECT
		over.tanggal,
		over.nik,
		karyawan.namaKaryawan as nama_karyawan,
		section.nama as section,
		over.jam,
		presensi.masuk,
		presensi.keluar
		FROM
		over
		LEFT JOIN posisi ON posisi.nik = over.nik
		LEFT JOIN karyawan on karyawan.nik = over.nik
		left join section on section.id = posisi.id_sec
		left join presensi on presensi.nik = over.nik and presensi.tanggal = over.tanggal
		WHERE
		status_final = 0 ". $where ."
		) AS a
		LEFT JOIN (
		SELECT
		over_time.id,
		over_time.tanggal,
		over_time_member.nik,
		sum( over_time_member.jam ) AS plan_ot,
		dari,
		sampai,
		over_time.hari
		FROM
		over_time
		LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
		WHERE
		over_time_member.nik IS NOT NULL 
		AND deleted_at IS NULL 
		AND over_time_member.STATUS = 0
		AND over_time_member.jam_aktual = 0
		GROUP BY
		over_time.id,
		over_time.tanggal,
		over_time.hari,
		over_time_member.nik,
		over_time_member.dari,
		over_time_member.sampai
		) AS b ON a.nik = b.nik 
		AND a.tanggal = b.tanggal
		where b.id IS NOT NULL order by a.tanggal asc, a.nik asc";

		$overtimes = db::connection('mysql3')->select($queryConfirmation);


		return DataTables::of($overtimes)
		->addColumn('ot', function($overtimes){
			return '<input type="radio" id="ot_act_radio" name="confirm+' . $overtimes->nik . '+' . $overtimes->tanggal .  '+'.$overtimes->id . '+'.$overtimes->hari .'" value="'.$overtimes->plan_ot.'">';
		})
		->addColumn('log', function($overtimes){
			return '<input type="radio" id="ot_log_radio" name="confirm+' . $overtimes->nik . '+' . $overtimes->tanggal . '+'.$overtimes->id . '+'.$overtimes->hari .'" value="'.$overtimes->act_log.'">';
		})
		->addColumn('edit', function($overtimes){
			$tanggal = date('l, d F Y',strtotime($overtimes->tanggal));
			$tgl2 = date('Y-m-d',strtotime($overtimes->tanggal));
			$nama = str_replace("'","",$overtimes->name);
			return '<input type="button" class="btn btn-warning btn-sm" id="edit+' . $overtimes->nik . '+' .$overtimes->id . '+'.$overtimes->plan_ot .'+'.$overtimes->act_log .'" onclick="editModal(this.id, \'' . $overtimes->masuk . '\', \'' . $overtimes->keluar . '\', \'' . $nama . '\', \'' . $overtimes->diff . '\', \'' . $tanggal . '\', \'' . $tgl2 . '\', \'' . $overtimes->hari . '\',  \'' . $overtimes->id . '\')" value="Edit">';
		})
		->rawColumns(['ot_log' => 'ot', 'ot_plan' => 'log', 'edit' => 'edit'])
		->make(true);
	}	

	public function confirmOvertimeConfirmation(Request $request)
	{
		$datas = $request->get('confirm');

		if($datas == null){
			$response = array(
				'status' => false,
				'message' => "Please choose overtime to confirm",
			);
			return Response::json($response);
		}

		foreach ($datas as $data) {
			$tgl = date('Y-m-d', strtotime($data[1]));
			$nik = $data[0];
			$id_ot = $data[2];
			$jam = $data[3];
			$hari = $data[4];

			try{
				$over_time_member = DB::connection('mysql3')->table('over_time_member')
				->where('over_time_member.id_ot', '=', $id_ot)
				->where('over_time_member.nik', '=', $nik)
				->update([
					'over_time_member.status' => 1,
					'over_time_member.final' => $jam
				]);

				$tes = DB::connection('mysql3')->select('CALL masukDataOverSPLAktual("'.$nik.'","'.$tgl.'", "'.$hari.'", "'.$jam.'")');
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
			'message' => "Overtime Confirmed",
		);
		return Response::json($response);
	}

	public function editOvertimeConfirmation(Request $request)
	{
		$jam = $request->get('jam');
		$nik = $request->get('nik');
		$tgl = date('Y-m-d', strtotime($request->get('tgl')));
		$status = $request->get('hari');

		try{
			$over = DB::connection('mysql3')->table('over')
			->where('over.tanggal', '=', $tgl)
			->where('over.nik', '=', $nik)
			->first();

			if($over == null){
				$insert_over = DB::connection('mysql3')->table('over')
				->where('over.tanggal', '=', $tgl)
				->where('over.nik', '=', $nik)
				->insert([
					'nik' => $nik,
					'tgl' => $tgl,
					'jam' => $jam,
					'status' => $status,
					'status_final' => 0,
				]);
			}
			else{
				$update_over = DB::connection('mysql3')->table('over')
				->where('over.tanggal', '=', $tgl)
				->where('over.nik', '=', $nik)
				->update([
					'jam' => $jam
				]);
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
			'message' => "Overtime Changed",
		);
		return Response::json($response);
	}

	public function deleteOvertimeConfirmation(Request $request){

		$over_time = DB::connection('mysql3')->table('over_time_member')
		->where('over_time_member.id_ot', '=', $request->get('id_ot'))
		->where('over_time_member.nik', '=', $request->get('nik'))
		->update([
			'jam_aktual' => 1
		]);

		$response = array(
			'status' => true,
			'message' => 'Overtime deleted',
		);
		return Response::json($response);
	}

	public function OvertimeControlReport(Request $request)
	{
		if($request->get('tgl') != ""){
			$tgl = date('Y-m-d',strtotime($request->get('tgl')));
			$tgl2 = date('Y-m',strtotime($request->get('tgl')));
		}else{
			$tgl = date('Y-m-d');
			$tgl2 = date('Y-m');
		}

		$query = "SELECT datas.*, DATE_FORMAT('".$tgl."','%d %M %Y') as tanggal, d.jam_harian from
		( SELECT  n.id_cc, master_cc.NAME,
		sum( n.act ) AS act, ROUND( sum( budget_tot ), 1) AS tot, ROUND( sum( budget_tot ) - sum( n.act ), 1) AS diff FROM
		(
		SELECT
		l.id_cc,
		d.tanggal,
		COALESCE ( act, 0 ) act,
		l.budget_tot 
		FROM
		(
		SELECT
		id_cc,
		( budget_total / DATE_FORMAT( LAST_DAY( '".$tgl."' ), '%d' ) ) budget_tot 
		FROM
		cost_center_budget 
		WHERE
		DATE_FORMAT( period, '%Y-%m' ) = '".$tgl2."' 
		) AS l
		CROSS JOIN ( SELECT tanggal FROM over_time WHERE DATE_FORMAT( tanggal, '%Y-%m' ) = '".$tgl2."' and tanggal <='".$tgl."' GROUP BY tanggal ) AS d
		LEFT JOIN (
		SELECT
		d.tanggal,
		sum( jam ) AS act,
		karyawan.costCenter 
		FROM
		(
		SELECT
		over_time_member.nik,
		over_time.tanggal,
		sum(IF(status = 0, jam, final)) AS jam 
		FROM
		over_time
		LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
		WHERE
		DATE_FORMAT( over_time.tanggal, '%Y-%m' ) = '".$tgl2."' 
		AND over_time_member.nik IS NOT NULL and over_time.deleted_at is null
		and jam_aktual = 0
		and nik not like '%os%'
		GROUP BY
		over_time_member.nik,
		over_time.tanggal
		) d
		LEFT JOIN karyawan ON karyawan.nik = d.nik 
		GROUP BY
		tanggal,
		costCenter 
		) x ON x.costCenter = l.id_cc 
		AND x.tanggal = d.tanggal 
		WHERE
		d.tanggal <= '".$tgl."' 
		) AS n
		LEFT JOIN master_cc ON master_cc.id_cc = n.id_cc 
		GROUP BY
		id_cc, master_cc.NAME 
		ORDER BY
		diff ASC    ) AS datas
		LEFT JOIN (
		SELECT
		cost_center,
		sum( jam ) AS jam_harian 
		FROM
		budget_harian 
		WHERE
		DATE_FORMAT( tanggal, '%Y-%m' ) = '".$tgl2."' 
		AND tanggal <= '".$tgl."' 
		GROUP BY
		cost_center 
		) d on datas.id_cc = d.cost_center 
		GROUP BY name,id_cc,act,tot,diff,jam_harian
		ORDER BY diff asc
		";

		$fiskal = "select fiskal from kalender_fy where DATE_FORMAT(tanggal,'%Y-%m') = '".$tgl2."' limit 1";
		$fiskal1 = db::connection('mysql3')->select($fiskal);


		$total = "select mon, costCenter, count(if(if(date_format(a.tanggalMasuk, '%Y-%m') < mon, 1, 0 ) - if(date_format(a.tanggalKeluar, '%Y-%m') < mon, 1, 0 ) = 0, null, 1)) as tot_karyawan from
		(
		select distinct fiskal, date_format(tanggal, '%Y-%m') as mon
		from kalender_fy
		) as b
		join
		(
		select '".$fiskal1[0]->fiskal."' as fy, karyawan.kode, tanggalKeluar, tanggalMasuk, nik, costCenter
		from karyawan
		) as a
		on a.fy = b.fiskal
		group by mon, costCenter
		having mon = '".$tgl2."'";


		$total1 = db::connection('mysql3')->select($total);

		$daily = "select bdg.*, master_cc.name from
		(select cost_center, sum(jam) as jam from budget_harian where DATE_FORMAT(tanggal,'%Y-%m') = '".$tgl2."' and tanggal <= '".$tgl."'
		group by cost_center) as bdg
		left join master_cc on master_cc.id_cc = bdg.cost_center";

		$daily1 = db::connection('mysql3')->select($daily);
		$overtimes = db::connection('mysql3')->select($query);

		$response = array(
			'status' => true,
			'total1' => $total1,
			'daily1' => $daily1,
			'overtimes' => $overtimes,
		);
		return Response::json($response);	
	}


	// ----------------------- CHART REPORT OVERTIME ------------------------

	public function overtimeReport()
	{
	// ----------  Chart Overtime By Dep ----------
		$tanggal = date('Y-m');
		$tanggalMin = date("Y-m", strtotime("-3 months"));


		$fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '".$tanggal."' group by fiscal_year";

		$fy = db::select($fiskal);


		$ot_by_dep = "select mon, department, round(ot_hour / kar,2) as avg from 
		(
		select em.mon ,em.department, IFNULL(sum(ovr.final),0) ot_hour, sum(jml) as kar from
		(
		select emp.*, bagian.department, 1 as jml from 
		(select employee_id, mon from 
		(
		select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
		cross join (
		select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') BETWEEN  '".$tanggalMin."' and  '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
		) m
		where hire_month <= mon and (mon < end_month OR end_month is null)
		) emp
		left join (
		SELECT id, employee_id, department, date_format(valid_from, '%Y-%m') as mon_from, coalesce(date_format(valid_to, '%Y-%m'), date_format(DATE_ADD(now(), INTERVAL 1 MONTH),'%Y-%m')) as mon_to FROM mutation_logs
		WHERE id IN (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id, DATE_FORMAT(valid_from,'%Y-%m'))
		) bagian on emp.employee_id = bagian.employee_id and emp.mon >= bagian.mon_from and emp.mon < mon_to
		where department is not null
		) as em
		left join (
		select nik, date_format(tanggal,'%Y-%m') as mon, sum(if(status = 0,om.jam,om.final)) as final from ftm.over_time as o left join ftm.over_time_member as om on o.id = om.id_ot
		where deleted_at is null and jam_aktual = 0 and DATE_FORMAT(tanggal,'%Y-%m') in (
		select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') BETWEEN  '".$tanggalMin."' and '".$tanggal."' group by date_format(week_date, '%Y-%m')
		)
		group by date_format(tanggal,'%Y-%m'), nik
		) ovr on em.employee_id = ovr.nik and em.mon = ovr.mon
		group by department, em.mon
	) as semua";

	$report_by_dep = db::select($ot_by_dep);

	$response = array(
		'status' => true,
		'report_by_dep' => $report_by_dep
	);

	return Response::json($response);
}


public function overtimeOver(Request $request)
{
	$tgl = $request->get('tanggal');

	if ($tgl == '') {
		$tanggal = date('Y-m');
	} else {
		$tanggal = date('Y-m', strtotime('10-'.$tgl));
	}

	$report = "select kd.department, '".$tanggal."' month_name, COALESCE(tiga.tiga_jam,0) as tiga_jam, COALESCE(patblas.emptblas_jam,0) as emptblas_jam, COALESCE(tiga_patblas.tiga_patblas_jam,0) as tiga_patblas_jam, COALESCE(lima_nam.limanam_jam,0) as limanam_jam from
	(select child_code as department from organization_structures where remark = 'department') kd
	left join
	( select department, count(nik) tiga_jam from (
	select d.nik, karyawan.department from
	(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
	group by nik, tanggal) d 
	left join 
	(
	select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
	SELECT MAX(id)
	FROM mutation_logs
	where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
	GROUP BY employee_id
	)
	) karyawan on karyawan.employee_id  = d.nik
	where jam > 3
	group by d.nik
	) tiga_jam
	group by department
	) as tiga on kd.department = tiga.department
	left join
	(
	select department, count(nik) as emptblas_jam from
	(select s.nik, department from
	(select nik, sum(jam) jam, week_name from
	(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
	group by nik, tanggal) m
	group by nik, week_name) s
	left join 
	(
	select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
	SELECT MAX(id)
	FROM mutation_logs
	where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
	GROUP BY employee_id
	)		
	) employee on employee.employee_id = s.nik
	where jam > 14
	group by s.nik) l
	group by department
	) as patblas on kd.department = patblas.department
	left join
	(
	select employee.department, count(c.nik) as tiga_patblas_jam from 
	( select z.nik from 
	( select d.nik from
	(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
	group by nik, tanggal) d 
	where jam > 3
	group by d.nik ) z

	INNER JOIN

	(select s.nik from
	(select nik, sum(jam) jam, week_name from
	(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
	group by nik, tanggal) m
	group by nik, week_name) s
	where jam > 14
	group by s.nik) x on z.nik = x.nik
	) c
	left join 
	(
	select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
	SELECT MAX(id)
	FROM mutation_logs
	where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
	GROUP BY employee_id
	)
	) employee on employee.employee_id = c.nik
	group by employee.department
	) tiga_patblas on kd.department = tiga_patblas.department
	left join
	(
	select department, count(nik) as limanam_jam from
	( select d.nik, sum(jam) as jam, employee.department from
	(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
	group by nik, tanggal) d
	left join 
	(
	select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
	SELECT MAX(id)
	FROM mutation_logs
	where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
	GROUP BY employee_id
	)
	) employee on employee.employee_id = d.nik
	group by d.nik ) c
	where jam > 56
	group by department
) lima_nam on lima_nam.department = kd.department";

$report2 = db::select($report);

$response = array(
	'status' => true,
	'report' => $report2
);

return Response::json($response);
}


public function overtimeControl(Request $request)
{
	if($request->get('tgl') != ""){
		$tanggal1 = date('Y-m-d',strtotime($request->get('tgl')));
		$tanggal = date('Y-m-01',strtotime($request->get('tgl')));
	}else{
		$tanggal1 = date('Y-m-d');
		$tanggal = date('Y-m-01');
	}	
	// -------------- CHART REPORT CONTROL -----------

	$ot = db::connection('sunfish')->select("select ovr.*, cost_center_code, cost_center_name from
		(select emp_no, SUM(floor((total_ot / 60) * 2  + 0.5) / 2) as jam from VIEW_YMPI_Emp_Attendance where starttime >= '".$tanggal."' and starttime <= '".$tanggal1."' and ovtrequest_no is not null group by emp_no) ovr left join VIEW_YMPI_Emp_OrgUnit on ovr.emp_no = VIEW_YMPI_Emp_OrgUnit.Emp_no");

	$main_q = "select semua.cost_center, cost_center_name, SUM(bdg) as bdg, SUM(fq) as fq, DATE_FORMAT('".$tanggal1."','%d %M %Y') as tanggal from
	(select cost_center, round(budget / DAY(LAST_DAY('".$tanggal1."')) * DAY('".$tanggal1."'),1) as bdg, 0 as fq from budgets 
	where period = '".$tanggal."'
	union all
	select cost_center, 0 as bdg, round(SUM(`hour`),1) as fq from forecasts where date >= '".$tanggal."' and date <= '".$tanggal1."' GROUP BY cost_center) as semua
	left join cost_centers2 on cost_centers2.cost_center = semua.cost_center
	group by cost_center, cost_center_name";

	$main = db::select($main_q);

	$tot = [];

	foreach ($main as $value2) {
		$act = 0;
		$arr = [];
		foreach ($ot as $value) {
			if ($value2->cost_center == $value->cost_center_code) {
				$act += $value->jam;
			}
		}

		$arr['tanggal'] = $value2->tanggal;
		$arr['cost_center'] = $value2->cost_center;
		$arr['cost_center_name'] = $value2->cost_center_name;
		$arr['budget'] = $value2->bdg;
		$arr['forecast'] = $value2->fq;
		$arr['actual'] = $act;
		array_push($tot, $arr);
	}

	$employee = db::table('employees')
	->whereNull('end_date')
	->select(db::raw("count(employee_id) as jml"))
	->first();

	$employee_fc = db::table('manpower_forecasts')
	->where('period','=',date('Y-m-01', strtotime($tanggal1)))
	->select(db::raw("sum(forecast_mp) as jml_fc"))
	->first();

	$employee_bdg = db::table('manpower_budgets')
	->where('period','=',date('Y-m-01', strtotime($tanggal1)))
	->select(db::raw("sum(budget_mp) as jml_bdg"))
	->first();

	$response = array(
		'status' => true,
		'ot_detail' => $ot,
		'semua' => $tot,
		'emp_total' => $employee,
		'emp_fc' => $employee_fc,
		'emp_bdg' => $employee_bdg
	);

	return Response::json($response);
}

public function overtimeReportDetail(Request $request)
{
	$tgl = date('Y-m' ,strtotime($request->get('tanggal')));
	$ctg = $request->get('category');
	$department = $request->get('department');
	$query = "";

	if($ctg == '3 hour(s) / day'){
		$query = 'SELECT s.*, employees.employee_id, employees.name, department, section, `group` from
		(select d.nik, round(avg(jam),2) as avg from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) d 
		where jam > 3
		group by d.nik ) s
		left join employees on employees.employee_id = s.nik
		left join 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = s.nik
		where department = "'.$department.'"';
		

		$lebih_detail = 'select d.nik, jam, null week_name, keperluan, tanggal from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari, group_concat(keperluan) as keperluan from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) d 
		where jam > 3';

		$detail = db::select($lebih_detail);
	}
	if($ctg == '14 hour(s) / week'){
		$query = 'SELECT s.nik, avg(jam) as avg, name, section, department, `group` from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) m
		group by nik, week_name) s
		left join employees on employees.employee_id = s.nik
		left join 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.' and valid_to is null" 
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = s.nik
		where jam > 14 and department = "'.$department.'"
		group by s.nik, name, section, department,`group`';

		$detail = '';

	}
	if($ctg == '3 & 14 hour(s) / week'){
		$query = 'select c.nik, name, department, section, `group`, c.avg from ( select z.nik, x.avg from 
		( select d.nik, round(avg(jam),2) as avg from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) d 
		where jam > 3
		group by d.nik ) z

		INNER JOIN

		( select s.nik, avg(jam) as avg from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) m
		group by nik, week_name) s
		where jam > 14
		group by s.nik) x on z.nik = x.nik
		) c
		left join employees on employees.employee_id = c.nik
		left join
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.' and valid_to is null"
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = c.nik
		where department = "'.$department.'"';

		$detail = '';
	}
	if($ctg == '56 hour(s) / month'){
		$query = 'select semua.nik, employees.name, department, section, `group`, avg from
		(select c.nik, c.jam as avg from
		(select d.nik, sum(jam) as jam from
		(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
		group by nik, tanggal, hari) d
		group by d.nik) c
		where jam > 56) semua
		left join employees on employees.employee_id = semua.nik
		left join
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.' and valid_to is null"
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = semua.nik
		where department = "'.$department.'"';

		$detail = '';
	}

	$ftm = db::select($query);

	$response = array(
		'status' => true,
		'datas' => $ftm,
		'head' => $ctg,
		'detail' => $detail
	);
	return Response::json($response);
}
	// --------------------- END CHART REPORT OVERTIME -------------------

// --------------------- Start Employement ---------------------
public function indexOvertimeDouble()
{
	return view('overtimes.overtime_double')->with('page', 'overtimeDouble');
}

public function fetchDoubleSPL(Request $request)
{
	$bulan = $request->get('bulan');

	$username = Auth::user()->username;
	$role = Auth::user()->role_code;

	$dep = db::connection('mysql3')->table('karyawan')
	->leftJoin('posisi', 'posisi.nik', '=', 'karyawan.nik')
	->where('karyawan.nik', '=', $username)
	->select('id_dep')
	->first();

	if ($role == 'HR-SPL' || $role == 'S'){
		$where = '';
	} else {
		$where = "where posisi.id_dep = '" . $dep->id_dep . "'";
	}
	$double = "select ov.id_ot, date_format(ov.tanggal,'%d %M %Y') as tanggal, ov.nik, namaKaryawan, section.nama as section, sub_section.nama as sub_sec, dari, sampai, jam, IF(ov.status = 1,'confirmed','not yet confirmed') as stat from
	( select id_ot, tanggal, nik, dari, sampai, jam, status from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '".$bulan."' and nik is not null
	order by tanggal asc, nik asc
	) as ov join
	( select nik, tanggal from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '".$bulan."' and nik is not null and jam_aktual = 0
	group by tanggal, nik
	having count(nik) > 1
	) a on ov.nik = a.nik and ov.tanggal = a.tanggal
	left join karyawan on karyawan.nik = ov.nik
	left join posisi on posisi.nik = ov.nik
	join section on section.id = posisi.id_sec
	join sub_section on sub_section.id = posisi.id_sub_sec
	".$where."
	order by ov.tanggal asc, a.nik asc";

	$get_double = db::connection('mysql3')->select($double);

	return DataTables::of($get_double)
	->addColumn('action', function($get_double){
		return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_emp(this.id)" id="'.$get_double->id_ot.'+'.$get_double->nik.'"><i class="fa fa-trash"></i> Delete</a>';
	})
	->rawColumns(['action' => 'action'])
	->make(true);
}
// --------------------- End Employement -----------------------

public function fetchOvertime()
{
	$username = Auth::user()->username;

	$bagian = Mutationlog::where("employee_id","=",$username)
	->whereNull("valid_to")
	->select("department")
	->first();

	if($bagian) {
		$get_overtime = Overtime::whereNull('deleted_at')
		->where("department","=",$bagian->department)
		->select('overtime_date','overtime_id','department','section','subsection','group')
		->get();
	} else {
		$get_overtime = Overtime::whereNull('deleted_at')
		->select('overtime_date','overtime_id','department','section','subsection','group')
		->get();
	}

	return DataTables::of($get_overtime)
	->addColumn('action', function($get_overtime){
		return '
		<a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="edit(this.id)" id="'.$get_overtime->overtime_id.'"><i class="fa fa-pencil"></i></a>
		&nbsp;
		<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="details(this.id)" id="'.$get_overtime->overtime_id.'">Detail</a>
		&nbsp;
		<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_ot(this.id)" id="'.$get_overtime->overtime_id.'"><i class="fa fa-trash"></i></a>';
	})
	->addColumn('libur', function($get_overtime){
		return '
		<button class="btn btn-xs btn-success" onClick="multi('.$get_overtime->overtime_id.')" id="add'.$get_overtime->overtime_id.'">Add <i class="fa fa-level-up"></i></button>';
	})
	->rawColumns(['action' => 'action', 'libur' => 'libur'])
	->make(true);
}

public function fetchOvertimeDetail(Request $request)
{
	$ot_details = Overtime::leftJoin("overtime_details","overtimes.overtime_id","=","overtime_details.overtime_id")
	->leftJoin("employees","employees.employee_id","=","overtime_details.employee_id")
	->where("overtimes.overtime_id","=",$request->get('overtime_id'))
	->whereNull("overtime_details.deleted_at")
	->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id","employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time","overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center")
	->get();

	$response = array(
		'status' => true,
		'data_details' => $ot_details
	);
	return Response::json($response);
}

public function fetchOvertimeEdit($id)
{
	$ot_details = Overtime::leftJoin("overtime_details","overtimes.overtime_id","=","overtime_details.overtime_id")
	->leftJoin("employees","employees.employee_id","=","overtime_details.employee_id")
	->where("overtimes.overtime_id","=",$id)
	->whereNull("overtime_details.deleted_at")
	->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id","employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time","overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center","overtimes.day_status","overtimes.shift","overtime_details.ot_status")
	->get();

	return view('overtimes.overtime_forms.edit', array(
		'datas' => $ot_details,
		'transports' => $this->transport,
		'purposes' => $this->purpose
	));	
}

public function graphPrint(Request $request)
{
	$dt = date('Y-m-d', strtotime($request->get("tanggal")));
	$query = '
	select DATE_FORMAT(cal.week_date,"%d/%m") week_date, COALESCE(jam,0) as final, (select round(budget_total / DAY(LAST_DAY("'.$dt.'")) , 2) as bdg_day from ftm.cost_center_budget where DATE_FORMAT(period,"%Y-%m") = DATE_FORMAT("'.$dt.'","%Y-%m") and id_cc = "'.$request->get("cc").'") as day_bdg from
	(select week_date from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = DATE_FORMAT("'.$dt.'","%Y-%m")) cal
	left join (
	select overtimes.overtime_date, overtime_details.cost_center, SUM(IF(status = 0,final_hour,final_overtime)) jam from overtimes
	join overtime_details on overtimes.overtime_id = overtime_details.overtime_id
	where DATE_FORMAT(overtimes.overtime_date,"%Y-%m") = DATE_FORMAT("'.$dt.'","%Y-%m") and
	overtime_details.cost_center = "'.$request->get("cc").'"
	and overtime_details.deleted_at is null
	and overtimes.deleted_at is null
	group by overtimes.overtime_date, overtime_details.cost_center
	) act on cal.week_date = act.overtime_date
	where cal.week_date <= "'.$dt.'"';

	$get_graph = db::select($query);

	$response = array(
		'status' => true,
		'datas' => $get_graph
	);
	return Response::json($response);
}

public function deleteOvertime(Request $request)
{
	Overtime::where('overtime_id',$request->get('id'))->delete();
	$response = array(
		'status' => true
	);
	return Response::json($response);
}

public function indexReportOvertimeAll()
{
	return view('overtimes.reports.overtime_monthly')->with('page', 'Overtime Management by NIK');
}

public function fetchCostCenterBudget(Request $request)
{
	$tgl = date('Y-m',strtotime($request->get('tgl')));
	$query = "select budgets.cost_center, period, budget from budgets
	left join cost_centers2 on budgets.cost_center = cost_centers2.cost_center
	where DATE_FORMAT(period,'%Y-%m') = '".$tgl."' and cost_centers2.cost_center_name = '".$request->get('cc')."' limit 1";

	$datas = DB::select($query);

	$response = array(
		'status' => true,
		'datas' => $datas
	);

	return Response::json($response);
}

public function overtimeDetail(Request $request)
{
	$from = date('Y-m-01',strtotime($request->get('tgl')));
	$to = date('Y-m-d',strtotime($request->get('tgl')));

	$cost_center = db::table('cost_centers')->where('cost_center_name',$request->get('cc'))
	->select('cost_center')->first();

	$datas = db::connection('sunfish')->select("
		select A.emp_no as nik, VIEW_YMPI_Emp_OrgUnit.Full_name as name, SUM(floor((total_ot / 60) * 2  + 0.5) / 2) as jam, 
		STUFF((
		SELECT ',' + T.remark
		FROM VIEW_YMPI_Emp_Attendance T
		WHERE A.emp_no = T.emp_no
		and T.ovtrequest_no is not null 
		and T.ovtplanfrom >= '".$from."' 
		and T.ovtplanto <= '".$to."'
		FOR XML PATH('')), 1, 1, '') as kep
		from VIEW_YMPI_Emp_Attendance A
		left join VIEW_YMPI_Emp_OrgUnit on VIEW_YMPI_Emp_OrgUnit.Emp_no = A.emp_no
		where VIEW_YMPI_Emp_OrgUnit.cost_center_code = '".$cost_center->cost_center."' 
		and A.ovtrequest_no is not null 
		and A.ovtplanfrom >= '".$from."' 
		and A.ovtplanto <= '".$to."'
		group by A.emp_no, VIEW_YMPI_Emp_OrgUnit.Full_name");

	$response = array(
		'status' => true,
		'datas' => $datas,
		'cc' => $cost_center
	);

	return Response::json($response);
}

public function fetchReportSection(Request $request)
{
	$bulan = date('Y-m');

	$queryDate = "select DATE_FORMAT(week_date,'%Y-%m') as bulan from weekly_calendars where fiscal_year = '".$request->get('tahun')."' GROUP BY DATE_FORMAT(week_date,'%Y-%m')";

	$date = db::select($queryDate);


	$query = "select em.employee_id, name, mon, cost_center, COALESCE(jam,0) as jam from(
	select emp.*, bagian.cost_center from 
	(select employee_id, name, mon from 
	(
	select employee_id, name, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
	cross join (
	select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$request->get('tahun')."' and date_format(week_date, '%Y-%m') <= '".$bulan."' group by date_format(week_date, '%Y-%m')) s
	) m
	where hire_month <= mon and (mon < end_month OR end_month is null)
	) emp
	left join (
	SELECT id, employee_id, cost_center, date_format(valid_from, '%Y-%m') as mon_from, coalesce(date_format(valid_to, '%Y-%m'), date_format(DATE_ADD(now(), INTERVAL 1 MONTH),'%Y-%m')) as mon_to FROM mutation_logs
	WHERE id IN (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id, DATE_FORMAT(valid_from,'%Y-%m'))
	) bagian on emp.employee_id = bagian.employee_id and emp.mon >= bagian.mon_from and emp.mon < mon_to
	where cost_center = '".$request->get('section')."') em left join

	(select DATE_FORMAT(tanggal,'%Y-%m') as bulan, nik, SUM(IF(status=1,final, jam)) as jam from ftm.over_time_member left join ftm.over_time on ftm.over_time.id = ftm.over_time_member.id_ot
	where deleted_at is null and jam_aktual = 0
	group by nik, DATE_FORMAT(tanggal,'%Y-%m')) ovr on em.employee_id = ovr.nik and em.mon = ovr.bulan";

	$datas = db::select($query);


	$queryBudget = "select cost_center, budget, budget_mp from budgets where cost_center = '".$request->get('section')."' and date_format(period,'%Y-%m') in (
	select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$request->get('tahun')."' group by date_format(week_date, '%Y-%m'))";

	$data_budgets = db::select($queryBudget);

	$response = array(
		'status' => true,
		'datas' => $datas,
		'budgets' => $data_budgets,
		'date' => $date
	);

	return Response::json($response);
}

public function fetchOvertimeHead(Request $request)
{
	if ($request->get('tgl') == "") {
		$tgl2 = date('Y-m');
		$tgl = date('Y-m-d',strtotime($tgl2."-01"));
	} else {
		$tgl2 = date('Y-m',strtotime($request->get('tgl')));
		$tgl = date('Y-m-d',strtotime($tgl2."-01"));
	}

	$spl = explode(",", $request->get('id'));

	$ot_grup = Overtime::leftJoin('overtime_details','overtime_details.overtime_id','=','overtimes.overtime_id')
	->whereIn('overtimes.overtime_id', $spl)
	->whereNull('overtime_details.deleted_at')
	->select('overtime_date','overtimes.overtime_id',db::raw('concat(section," - ",subsection," - ",`group`) as bagian'),db::raw('GROUP_CONCAT(DISTINCT overtime_details.remark) as reason'), db::raw('count(employee_id) as count_member'), db::raw('sum(final_hour) as total_hour'))
	->groupBy('overtimes.overtime_id', 'overtime_date', 'section', 'subsection', 'group')
	->get();

	$response = array(
		'status' => true,
		'datas' => $ot_grup
	);
	return Response::json($response);
}

public function indexPrintHead(Request $request)
{
	$ids = explode(",", $request->get('id'));

	$anggota = Overtime::leftJoin('overtime_details','overtime_details.overtime_id','=','overtimes.overtime_id')
	->whereIn('overtimes.overtime_id', $ids)
	->whereNull('overtime_details.deleted_at')
	->select('overtime_date','overtimes.overtime_id',db::raw('concat(section," - ",subsection," - ",`group`) as bagian'),db::raw('GROUP_CONCAT(DISTINCT overtime_details.remark) as reason'), db::raw('count(employee_id) as count_member'), db::raw('sum(final_hour) as total_hour'))
	->groupBy('overtimes.overtime_id', 'overtime_date', 'section', 'subsection', 'group')
	->orderBy('overtime_date')
	->get();

	$tgl = $anggota[0]->overtime_date;
	$mon = date('Y-m',strtotime($anggota[0]->overtime_date));

	$cc = "select DISTINCT ovr.cost_center, cost_centers.cost_center_name, round(budget / DAY(LAST_DAY('".$tgl."')) * DAY('".$tgl."'),1) bdg, act, round((budget / DAY(LAST_DAY('".$tgl."')) * DAY('".$tgl."')) - act,1) as diff  from
	(select cost_center from overtimes left join overtime_details on overtimes.overtime_id = overtime_details.overtime_id where overtimes.overtime_id in (".$request->get('id').") and overtime_details.deleted_at is null group by cost_center, overtime_date) ovr 
	left join (select cost_center, period, budget from budgets where date_format(period,'%Y-%m') = '".$mon."') budgets on budgets.cost_center = ovr.cost_center
	left join (select cost_center, SUM(IF(status = 1, final_overtime, final_hour)) as act from overtimes left join overtime_details on overtimes.overtime_id = overtime_details.overtime_id where date_format(overtime_date,'%Y-%m') = '".$mon."' and overtime_date <= '".$tgl."' and overtimes.deleted_at is null and overtime_details.deleted_at is null group by cost_center) as act on act.cost_center = ovr.cost_center
	left join (select DISTINCT cost_center, cost_center_name from cost_centers) cost_centers on cost_centers.cost_center = ovr.cost_center";

	$cost_center = db::select($cc);

	return view('overtimes.overtime_forms.index_print_head', array(
		'anggota' => $anggota,
		'cc' => $cost_center
	));
}

public function fetchReportOutsource(Request $request)
{	
	if(strlen($request->get('bulan')) > 0){
		$tgl = $request->get("bulan");
	}else{
		$tgl = date("m-Y");
	}


	$ot_outsource_q = "select ovr.period, ovr.nik, emp.namaKaryawan, ovr.jam from
	(select DATE_FORMAT(tanggal,'%m-%Y') as period, nik, SUM(IF(status = 0,jam, final)) jam
	from over_time left join over_time_member
	on over_time.id = over_time_member.id_ot 
	where deleted_at is null and jam_aktual = 0 and nik like 'os%' and DATE_FORMAT(tanggal,'%m-%Y') = '".$tgl."'
	group by period, nik) ovr
	left join
	(select nik, namaKaryawan from karyawan where nik like 'os%' and tanggalKeluar is null) emp
	on ovr.nik = emp.nik";

	$ot_outsource = db::connection('mysql3')->select($ot_outsource_q);

	$response = array(
		'status' => true,
		'datas' => $ot_outsource,
		'bulan' => $tgl
	);
	return Response::json($response);
}

public function fetchDetailOutsource(Request $request)
{
	$period = $request->get("period");
	$nama = $request->get("nama");

	$query = "select ovr.tanggal, ovr.nik, emp.namaKaryawan, ovr.ot, ovr.reason from
	(select tanggal, nik, SUM(IF(status = 0,jam, final)) ot, GROUP_CONCAT(keperluan) reason
	from over_time left join over_time_member
	on over_time.id = over_time_member.id_ot 
	where deleted_at is null and jam_aktual = 0 and nik like 'os%' and DATE_FORMAT(tanggal,'%m-%Y') = '".$period."'
	group by tanggal, nik) ovr
	left join
	(select nik, namaKaryawan from karyawan where nik like 'os%' and tanggalKeluar is null) emp
	on ovr.nik = emp.nik
	where namaKaryawan = '".$nama."'";

	$detail = db::connection('mysql3')->select($query);

	return DataTables::of($detail)->make(true);

	
}

public function fetchOvertimeDataOutsource(Request $request)
{
	$dateto = date('Y-m-d');
	$datefrom = $request->get('datefrom');

	if ($request->get('dateto') != "") {
		$dateto = $request->get('dateto');
	}

	$query = "select ovr.tanggal, ovr.nik, karyawan.namaKaryawan, jam, reason from (select tanggal, nik, SUM(IF(status = 0,jam, final)) jam, GROUP_CONCAT(keperluan) reason from over_time left join over_time_member on over_time.id = over_time_member.id_ot 
	where deleted_at is null and jam_aktual = 0 and nik like 'os%'
	group by nik, tanggal) ovr
	left join karyawan on ovr.nik = karyawan.nik";

	$os_ot = db::connection('mysql3')->select($query);

	$response = array(
		'status' => true,
		'datas' => $os_ot
	);
	return DataTables::of($os_ot)->make(true);
}

public function fetchOvertimeByEmployee(Request $request){
	$tanggal = "";
	$adddepartment = '';
	$addsection = '';
	$addnik = '';

	if(strlen($request->get('datefrom')) > 0){
		$tanggal = "and DATE_FORMAT(tanggal,'%m-%Y') >= '".$request->get('datefrom')."' ";
		if(strlen($request->get('dateto')) > 0){
			$tanggal = $tanggal."and DATE_FORMAT(tanggal,'%m-%Y') <= '".$request->get('dateto')."' ";
		}
	}

	if($request->get('department') != null) {
		$departments = $request->get('department');
		$department = "";
		for($x = 0; $x < count($departments); $x++) {
			$department = $department."'".$departments[$x]."'";
			if($x != count($departments)-1){
				$department = $department.",";
			}
		}
		$adddepartment = "and bagian.department in (".$department.") ";
	}

	if($request->get('section') != null) {
		$sections = $request->get('section');
		$section = "";
		for($x = 0; $x < count($sections); $x++) {
			$section = $section."'".$sections[$x]."'";
			if($x != count($sections)-1){
				$section = $section.",";
			}
		}
		$addsection = "and bagian.section in (".$section.") ";
	}

	if($request->get('nik') != null) {
		$niks = $request->get('nik');
		$nik = "";
		for($x = 0; $x < count($niks); $x++) {
			$nik = $nik."'".$niks[$x]."'";
			if($x != count($niks)-1){
				$nik = $nik.",";
			}
		}
		$addnik = "and ovr.nik in (".$nik.") ";
	}

	$query = "select DATE_FORMAT(tanggal,'%m-%Y') as period, ovr.nik, emp.name, bagian.department, bagian.section, SUM(ot) as total from (select tanggal, nik, SUM(IF(status = 0, jam, final)) ot from over_time left join over_time_member on over_time.id = over_time_member.id_ot
	where deleted_at is null and jam_aktual = 0 ".$tanggal." group by nik, tanggal) ovr
	left join ympimis.employees as emp on emp.employee_id = ovr.nik
	left join (select employee_id, department, section from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
	where ot > 0 ".$addsection."".$addnik."".$adddepartment."
	group by period, nik, ympimis.emp.name
	order by period, total asc";

	$data = db::connection('mysql3')->select($query);

	return DataTables::of($data)
	->addColumn('detail', function($data){
		return '<input type="button" class="btn btn-success btn-sm" id="detail+' . $data->nik . '+' .$data->period .'" onclick="showModal( \'' . $data->nik . '\', \'' . $data->period . '\', \'' . $data->name . '\')" value="Detail">';
	})
	->rawColumns(['action' => 'detail'])
	->make(true);
	
}

public function detailOvertimeByEmployee(Request $request){
	$query = "select distinct ovr.tanggal, ovr.nik, emp.name, bagian.department, bagian.section, ot, ovr.keperluan from
	(select tanggal, nik, SUM(IF(status = 0, jam, final)) ot, GROUP_CONCAT(keperluan) keperluan from over_time_member left join over_time on over_time.id = over_time_member.id_ot
	where deleted_at is null and jam_aktual = 0 and DATE_FORMAT(tanggal,'%m-%Y') = '".$request->get('period')."' group by tanggal, nik) ovr
	left join ympimis.employees as emp on emp.employee_id = ovr.nik
	left join (select employee_id, department, section from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
	where ot > 0 and nik = '".$request->get('nik')."'
	order by ovr.tanggal asc";

	$data = db::connection('mysql3')->select($query);

	return DataTables::of($data)->make(true);
}

public function fetchGAReport(Request $request)
{
	$now = date("Y-m-d", strtotime($request->get('tanggal')));
	$weekly_calendars = WeeklyCalendar::where('week_date', '=', $now)->first();


	if($weekly_calendars->remark == 'H'){
		$details = db::connection('sunfish')->select("
			SELECT CONVERT
			( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 108 ) AS ot_from,
			CONVERT ( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanto, 108 ) AS ot_to,
			VIEW_YMPI_Emp_OvertimePlan.shiftdaily_code,
			VIEW_YMPI_Emp_OvertimePlan.emp_no,
			VIEW_YMPI_Emp_OrgUnit.Full_name,
			VIEW_YMPI_Emp_OrgUnit.Section,
			COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) AS trans,
			CASE

			WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 300 THEN
			'Ya' ELSE '-' 
			END AS food 
			FROM
			VIEW_YMPI_Emp_OvertimePlan
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no 
			WHERE
			CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '01-01-2020' 
			AND (COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-' 
			or
			CASE

			WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 300 THEN
			'Ya' ELSE '-' 
			END <> '-') order by VIEW_YMPI_Emp_OvertimePlan.shiftdaily_code asc, VIEW_YMPI_Emp_OvertimePlan.emp_no asc
			");
		$ot = db::connection('sunfish')->select("
			select ot_from, ot_to, coalesce(sum(makan1),0) as makan1, coalesce(sum(makan2),0) as makan2, coalesce(sum(makan3),0) as makan3, coalesce(sum(extra2),0) as extra2, coalesce(sum(extra3),0) as extra3, coalesce(sum(trn_bgl),0) as trn_bgl, coalesce(sum(trn_psr),0) as trn_psr from 
			(
			SELECT
			convert(varchar, ovtplanfrom, 108) as ot_from, convert(varchar, ovtplanto, 108) as ot_to,
			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_1%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
			THEN 1 
			ELSE null
			END AS makan1,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
			THEN 1 
			ELSE null
			END AS makan2,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_3%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
			THEN 1 
			ELSE null
			END AS makan3,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
			THEN 1 
			ELSE null
			END AS extra2,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_3%'
			THEN 1 
			ELSE null
			END AS extra3,

			CASE
			WHEN
			ovttrans = 'TRNBGL'
			THEN 1 
			ELSE null
			END AS trn_bgl,

			CASE
			WHEN
			ovttrans = 'TRNPSR'
			THEN 1 
			ELSE null
			END AS trn_psr

			FROM
			VIEW_YMPI_Emp_OvertimePlan
			where convert(varchar, ovtplanfrom, 105) = '".$request->get('tanggal')."'
			) as ga_report group by ot_from, ot_to order by ot_to asc
			");
	}
	else{
		$details = db::connection('sunfish')->select("
			SELECT CONVERT
			( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 108 ) AS ot_from,
			CONVERT ( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanto, 108 ) AS ot_to,
			VIEW_YMPI_Emp_OvertimePlan.shiftdaily_code,
			VIEW_YMPI_Emp_OvertimePlan.emp_no,
			VIEW_YMPI_Emp_OrgUnit.Full_name,
			VIEW_YMPI_Emp_OrgUnit.Section,
			COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) AS trans,
			CASE

			WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
			'Ya' ELSE '-' 
			END AS food 
			FROM
			VIEW_YMPI_Emp_OvertimePlan
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no 
			WHERE
			CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '01-01-2020' 
			AND (COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-' 
			or
			CASE

			WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
			'Ya' ELSE '-' 
			END <> '-') order by VIEW_YMPI_Emp_OvertimePlan.shiftdaily_code asc, VIEW_YMPI_Emp_OvertimePlan.emp_no asc
			");
		$ot = db::connection('sunfish')->select("
			select ot_from, ot_to, coalesce(sum(makan1),0) as makan1, coalesce(sum(makan2),0) as makan2, coalesce(sum(makan3),0) as makan3, coalesce(sum(extra2),0) as extra2, coalesce(sum(extra3),0) as extra3, coalesce(sum(trn_bgl),0) as trn_bgl, coalesce(sum(trn_psr),0) as trn_psr from 
			(
			SELECT
			convert(varchar, ovtplanfrom, 108) as ot_from, convert(varchar, ovtplanto, 108) as ot_to,
			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_1%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
			THEN 1 
			ELSE null
			END AS makan1,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
			THEN 1 
			ELSE null
			END AS makan2,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_3%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
			THEN 1 
			ELSE null
			END AS makan3,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
			THEN 1 
			ELSE null
			END AS extra2,

			CASE
			WHEN
			shiftdaily_code LIKE 'Shift_3%'
			THEN 1 
			ELSE null
			END AS extra3,

			CASE
			WHEN
			ovttrans = 'TRNBGL'
			THEN 1 
			ELSE null
			END AS trn_bgl,

			CASE
			WHEN
			ovttrans = 'TRNPSR'
			THEN 1 
			ELSE null
			END AS trn_psr

			FROM
			VIEW_YMPI_Emp_OvertimePlan
			where convert(varchar, ovtplanfrom, 105) = '".$request->get('tanggal')."'
			) as ga_report group by ot_from, ot_to order by ot_to asc
			");
	}

	$response = array(
		'status' => true,
		'datas' => $ot,
		'details' => $details
	);
	return $response;
}

}