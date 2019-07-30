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

	public function indexReportControl()
	{
		return view('overtimes.reports.overtime_monthly', array(
			'title' => 'Overtime Monthly Control',
			'title_jp' => '??'))->with('page', 'Overtime Monthly Control');
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

	public function saveOvertimeHead(Request $request)
	{
		$ot_id = $request->get('ot_id');

		$org = db::select("select dep.child_code as department, divs.child_code as division from 
			(SELECT  parent_name, child_code FROM `organization_structures` where remark='section') sec
			join (SELECT  parent_name, child_code, status FROM `organization_structures` where remark='department') dep
			on sec.parent_name = dep.status
			join (SELECT child_code, status FROM `organization_structures` where remark='division') divs
			on divs.status = dep.parent_name
			where sec.child_code = 'secretary admin'");

		$section = $request->get('section');
		$sub_section = $request->get('sub_section');
		$group = $request->get('group');
		$ot_date = date('Y-m-d',strtotime($request->get('ot_date')));
		$ot_day = $request->get('ot_day');
		$shift = $request->get('shift');

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
		sum( n.act ) AS act, sum( budget_tot ) AS tot, sum( budget_tot ) - sum( n.act ) AS diff FROM
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
		ROUND( ( budget_total / DATE_FORMAT( LAST_DAY( '".$tgl."' ), '%d' ) ), 1 ) budget_tot 
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
		sum( over_time_member.jam ) AS jam 
		FROM
		over_time
		LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
		WHERE
		DATE_FORMAT( over_time.tanggal, '%Y-%m' ) = '".$tgl2."' 
		AND over_time_member.nik IS NOT NULL and over_time.deleted_at is null
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
		select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
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
		select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')
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
	(select tanggal, nik, sum(final) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
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
	(select tanggal, nik, sum(final) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
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
	(select tanggal, nik, sum(final) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
	group by nik, tanggal) d 
	where jam > 3
	group by d.nik ) z

	INNER JOIN

	(select s.nik from
	(select nik, sum(jam) jam, week_name from
	(select tanggal, nik, sum(final) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
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
	(select tanggal, nik, sum(final) as jam from ftm.over_time
	left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
	where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
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
		$tanggal = date('Y-m',strtotime($request->get('tgl')));
	}else{
		$tanggal1 = date('Y-m-d');
		$tanggal = date('Y-m');
	}
	// -------------- CHART REPORT CONTROL -----------

	$ot_control = "SELECT datas.id_cc, datas.name, datas.act, datas.tot, DATE_FORMAT('".$tanggal1."','%d %M %Y') as tanggal, round(coalesce(d.jam_harian,0),2) as jam_harian FROM
	(	SELECT n.id_cc, master_cc.name, sum( n.act ) AS act, sum( budget_tot ) AS tot, sum( budget_tot ) - sum( n.act ) AS diff FROM
	( SELECT l.id_cc, d.tanggal, COALESCE ( act, 0 ) act, l.budget_tot FROM
	( SELECT id_cc, ROUND( ( budget_total / DATE_FORMAT( LAST_DAY( '".$tanggal1."' ), '%d' ) ), 1 ) budget_tot FROM cost_center_budget 
	WHERE DATE_FORMAT( period, '%Y-%m' ) = '".$tanggal."' ) AS l
	CROSS JOIN ( SELECT tanggal FROM over_time WHERE DATE_FORMAT( tanggal, '%Y-%m' ) = '".$tanggal."' AND tanggal <= '".$tanggal1."' GROUP BY tanggal ) AS d
	LEFT JOIN ( SELECT d.tanggal, sum( jam ) AS act, karyawan.costCenter FROM
	( SELECT over_time_member.nik, over_time.tanggal, sum( IF ( STATUS = 0, over_time_member.jam, over_time_member.final ) ) AS jam FROM
	over_time LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
	WHERE DATE_FORMAT( over_time.tanggal, '%Y-%m' ) = '".$tanggal."'  AND over_time_member.nik IS NOT NULL AND over_time.deleted_at IS NULL 
	AND jam_aktual = 0
	GROUP BY over_time_member.nik, over_time.tanggal 
	) d
	LEFT JOIN karyawan ON karyawan.nik = d.nik 
	GROUP BY tanggal, costCenter 
	) x ON x.costCenter = l.id_cc AND x.tanggal = d.tanggal 
	WHERE d.tanggal <= '".$tanggal1."' 
	) AS n
	LEFT JOIN master_cc ON master_cc.id_cc = n.id_cc GROUP BY id_cc,  master_cc.NAME ORDER BY diff ASC 
	) AS datas
	LEFT JOIN ( SELECT cost_center, sum( jam ) AS jam_harian FROM budget_harian WHERE DATE_FORMAT( tanggal, '%Y-%m' ) = '".$tanggal."' AND tanggal <= '".$tanggal1."' 
	GROUP BY cost_center 
	) d ON datas.id_cc = d.cost_center
	order by diff asc";

	$report_control = db::connection('mysql3')->select($ot_control);

	$employee = db::table('employees')
	->whereNull('end_date')
	->select(db::raw("count(employee_id) as jml"))
	->get();

	$response = array(
		'status' => true,
		'report_control' => $report_control,
		'emp_total' => $employee
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
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
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
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari, group_concat(keperluan) as keperluan from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) d 
		where jam > 3';

		$detail = db::select($lebih_detail);
	}
	if($ctg == '14 hour(s) / week'){
		$query = 'SELECT s.nik, avg(jam) as avg, name, section, department, `group` from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) m
		group by nik, week_name) s
		left join employees on employees.employee_id = s.nik
		left join 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = s.nik
		where jam > 14 and department = "'.$department.'"
		group by s.nik, name, section, department,`group`';

		$detail = '';


		// $lebih_detail = 'SELECT s.nik, jam, week_name, tanggal from
		// (select nik, sum(jam) jam, week_name, tanggal from
		// (select tanggal, nik, sum(final) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		// left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		// where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		// group by nik, tanggal, hari) m
		// group by nik, week_name) s
		// where jam > 14
		// group by s.nik, week_name';
	}
	if($ctg == '3 & 14 hour(s) / week'){
		$query = 'select c.nik, name, department, section, `group`, c.avg from ( select z.nik, x.avg from 
		( select d.nik, round(avg(jam),2) as avg from
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) d 
		where jam > 3
		group by d.nik ) z

		INNER JOIN

		( select s.nik, avg(jam) as avg from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) m
		group by nik, week_name) s
		where jam > 14
		group by s.nik) x on z.nik = x.nik
		) c
		left join employees on employees.employee_id = c.nik
		left join
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee on employee.employee_id = c.nik
		where department = "'.$department.'"';

		$detail = '';
	}
	if($ctg == '56 hour(s) / month'){
		$query = 'select semua.nik, employees.name, department, section, `group`, avg from
		(select c.nik, c.jam as avg from
		(select d.nik, sum(jam) as jam from
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) d
		group by d.nik) c
		where jam > 56) semua
		left join employees on employees.employee_id = semua.nik
		left join
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
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
	$get_overtime = Overtime::whereNull('deleted_at')
	->select('overtime_date','overtime_id','division','department','section','subsection','group')
	->get();

	return DataTables::of($get_overtime)
	->addColumn('action', function($get_overtime){
		return '
		<a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="edit(this.id)" id="'.$get_overtime->overtime_id.'"><i class="fa fa-pencil"></i></a>
		&nbsp;
		<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="details(this.id)" id="'.$get_overtime->overtime_id.'">Detail</a>
		&nbsp;
		<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_ot(this.id)" id="'.$get_overtime->overtime_id.'"><i class="fa fa-trash"></i></a>';
	})
	->rawColumns(['action' => 'action'])
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
	left join cost_centers on budgets.cost_center = cost_centers.cost_center
	where DATE_FORMAT(period,'%Y-%m') = '".$tgl."' and cost_centers.cost_center_name = '".$request->get('cc')."'";

	$datas = DB::select($query);

	$response = array(
		'status' => true,
		'datas' => $datas
	);

	return Response::json($response);

}

public function overtimeDetail(Request $request)
{
	$tgl = date('Y-m-d',strtotime($request->get('tgl')));
	$bulan = date('Y-m',strtotime($request->get('tgl')));

	$query = "SELECT
	final2.nik,
	c.namaKaryawan,
	sum( final2.jam ) AS jam,
	GROUP_CONCAT( DISTINCT c.kep ) AS kep 
	FROM
	(
	SELECT
	over_time_member.nik,
	over_time.tanggal,
	sum( over_time_member.jam ) AS jam 
	FROM
	over_time
	LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
	WHERE
	DATE_FORMAT( over_time.tanggal, '%Y-%m' ) = '".$bulan."'
	AND over_time_member.nik IS NOT NULL 
	AND over_time.deleted_at IS NULL 
	and over_time.tanggal <= '".$tgl."'
	and jam_aktual = 0
	GROUP BY
	over_time_member.nik,
	over_time.tanggal 
	) AS final2
	LEFT JOIN (
	SELECT
	over_time_member.nik,
	karyawan.namaKaryawan,
	karyawan.costCenter,
	GROUP_CONCAT( DISTINCT over_time.keperluan ) AS kep 
	FROM
	over_time
	LEFT JOIN over_time_member ON over_time_member.id_ot = over_time.id
	LEFT JOIN karyawan ON karyawan.nik = over_time_member.nik 
	WHERE
	DATE_FORMAT( over_time.tanggal, '%Y-%m' ) = '".$bulan."' 
	AND over_time.tanggal <= '".$tgl."' 
	AND over_time_member.nik IS NOT NULL 
	and jam_aktual = 0
	GROUP BY
	over_time_member.nik,
	karyawan.namaKaryawan,
	karyawan.costCenter
	) AS c ON final2.nik = c.nik 
	WHERE
	c.costCenter = '".$request->get('cc')."' 
	GROUP BY
	final2.nik,
	c.namaKaryawan
	ORDER BY
	sum( final2.jam ) DESC";

	$datas = db::connection('mysql3')->select($query);

	$response = array(
		'status' => true,
		'datas' => $datas
	);

	return Response::json($response);
}
}