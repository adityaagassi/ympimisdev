<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\BreakTime;
use App\Overtime;
use App\OvertimeDetail;
use App\CodeGenerator;
use PDF;
use Dompdf\Dompdf;

class OvertimeController extends Controller
{

	public function __construct(){
		$this->middleware('auth');
		$this->transport = [
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

	public function indexOvertimeReport()
	{
		return view('overtimes.reports.report')->with('page', 'Overtime Report');
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
		$sections = db::table('sections')->get();

		return view('overtimes.overtime_forms.create', array(
			'ot_id' => $ot_id,
			'transports' => $transports,
			'day_statuses' => $day_statuses,
			'purposes' => $purposes,
			'sections' => $sections,
			'shifts' => $shifts,
		))->with('page', 'Overtime Form');
	}

	public function selectDivisionHierarchy(Request $request){
		$hierarchies = db::table('division_hierarchies')->where('parent', '=', $request->get('parent'))->get();
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

	public function fetchOvertimeConfirmation(){

		$username = Auth::user()->username;
		$role = Auth::user()->role_code;

		$date = date('Y-m');

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
		$over_time_member = DB::connection('mysql3')->table('over_time_member')
		->where('over_time_member.id_ot', '=', $request->get('id_ot'))
		->count();

		if($over_time_member == 1){
			$over_time = DB::connection('mysql3')->table('over_time')
			->where('over_time.id', '=', $request->get('id_ot'))
			->update([
				'deleted_at' => date('Y-m-d'),
				'nik_delete' => Auth::user()->username
			]);			
		}

		$over_time_member = DB::connection('mysql3')->table('over_time_member')
		->where('over_time_member.id_ot', '=', $request->get('id_ot'))
		->where('over_time_member.nik', '=', $request->get('nik'))
		->delete();

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

		$query="SELECT datas.*, DATE_FORMAT('".$tgl."','%d %M %Y') as tanggal, d.jam_harian from
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

	public function overtimeReport(Request $request)
	{
		$tgl = $request->get('tanggal');

		if ($tgl == '') {
			$tanggal = date('Y-m');
		} else {
			$tanggal = date('Y-m', strtotime('10-'.$tgl));
		}

		$report = "select kd.code, '".$tanggal."' month_name, COALESCE(tiga.tiga_jam,0) as tiga_jam, COALESCE(patblas.emptblas_jam,0) as emptblas_jam, COALESCE(tiga_patblas.tiga_patblas_jam,0) as tiga_patblas_jam, COALESCE(lima_nam.limanam_jam,0) as limanam_jam from
		(select code from total_meeting_codes group by code) kd
		left join
		( select code, count(nik) tiga_jam from (
		select d.nik, karyawan.code from
		(select tanggal, nik, sum(final) as jam from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
		group by nik, tanggal) d 
		left join 
		(
		select employee_id, code from 
		(
		select employee_id, section, department, `group` from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
		SELECT MAX(id)
		FROM mutation_logs
		where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
		GROUP BY employee_id
		)
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id  = d.nik
		where jam > 3
		group by d.nik
		) tiga_jam
		group by code
		) as tiga on kd.code = tiga.code
		left join
		(
		select code, count(nik) as emptblas_jam from
		(select s.nik, code from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(final) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
		group by nik, tanggal) m
		group by nik, week_name) s
		left join 
		(
		select employee_id, code from 
		(
		select employee_id, section, department, `group` from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
		SELECT MAX(id)
		FROM mutation_logs
		where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
		GROUP BY employee_id
		)		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id = s.nik
		where jam > 14
		group by s.nik) l
		group by code
		) as patblas on kd.code = patblas.code
		left join
		(
		select karyawan.code, count(c.nik) as tiga_patblas_jam from 
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
		select employee_id, code from
		(
		select employee_id, section, department, `group` from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
		SELECT MAX(id)
		FROM mutation_logs
		where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
		GROUP BY employee_id
		)
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id = c.nik
		group by karyawan.code
		) tiga_patblas on kd.code = tiga_patblas.code
		left join
		(
		select code, count(nik) as limanam_jam from
		( select d.nik, sum(jam) as jam, karyawan.code from
		(select tanggal, nik, sum(final) as jam from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$tanggal."' and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = 'N'
		group by nik, tanggal) d
		left join 
		(
		select employee_id, code from
		(
		select employee_id, section, department, `group` from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."' and id IN (
		SELECT MAX(id)
		FROM mutation_logs
		where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tanggal."'
		GROUP BY employee_id
		)
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id = d.nik
		group by d.nik ) c
		where jam > 56
		group by code
	) lima_nam on lima_nam.code = kd.code";

	$report2 = db::select($report);

	$response = array(
		'status' => true,
		'report' => $report2
	);
	return Response::json($response);

}

public function overtimeReportDetail(Request $request)
{
	$tgl = date('Y-m' ,strtotime($request->get('tanggal')));
	$ctg = $request->get('category');
	$code = $request->get('code');
	$query = "";

	if($ctg == '3 hour(s) / day'){
		$query = 'SELECT s.*, employees.employee_id, employees.name, department, section, code from
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
		select employee_id, department, section, code from 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) position on position.employee_id = s.nik
		where code = "'.$code.'"';
	}
	if($ctg == '14 hour(s) / week'){
		$query = 'SELECT s.nik, avg(jam) as avg, code, name, section, department from
		(select nik, sum(jam) jam, week_name from
		(select tanggal, nik, sum(final) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
		left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
		where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "'.$tgl.'" and nik IS NOT NULL and ftm.over_time_member.status = 1 and hari = "N"
		group by nik, tanggal, hari) m
		group by nik, week_name) s
		left join employees on employees.employee_id = s.nik
		left join 
		(
		select employee_id, department, section, code from 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) position on position.employee_id = s.nik
		where jam > 14 and code = "'.$code.'"
		group by s.nik, code, name, section, department';
	}
	if($ctg == '3 & 14 hour(s) / week'){
		$query = 'select c.nik, name, code, department, section, c.avg from ( select z.nik, x.avg from 
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
		select employee_id, code, department, section from 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id = c.nik
		where code = "'.$code.'"';
	}
	if($ctg == '56 hour(s) / month'){
		$query = 'select semua.nik, employees.name, department, section, code, avg from
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
		select employee_id, code, department, section from 
		(
		select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "'.$tgl.'" and valid_to is null
		group by employee_id,`group`, department, section
		) employee
		left join total_meeting_codes on employee.`group` = total_meeting_codes.group_name
		) karyawan on karyawan.employee_id = semua.nik
		where code = "'.$code.'"';
	}

	$ftm = db::select($query);

	$response = array(
		'status' => true,
		'datas' => $ftm,
		'head' => $ctg
	);
	return Response::json($response);
}
	// --------------------- END CHART REPORT OVERTIME -------------------


public function indexOvertimeDouble()
{
	return view('overtimes.overtime_double')->with('page', 'overtimeDouble');
}

public function fetchDoubleSPL()
{
	$bulan = date('Y-m');
	$double = "select ov.id_ot, ov.tanggal, ov.nik, namaKaryawan, section.nama as section, sub_section.nama as sub_sec, dari, sampai, jam, IF(ov.status = 1,'confirmed','not yet confirmed') as stat from
	( select id_ot, tanggal, nik, dari, sampai, jam, status from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '".$bulan."' and nik is not null
	order by tanggal asc, nik asc
	) as ov join
	( select nik, tanggal from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '".$bulan."' and nik is not null
	group by tanggal, nik
	having count(nik) > 1
	) a on ov.nik = a.nik and ov.tanggal = a.tanggal
	left join karyawan on karyawan.nik = ov.nik
	left join posisi on posisi.nik = ov.nik
	join section on section.id = posisi.id_sec
	join sub_section on sub_section.id = posisi.id_sub_sec
	order by ov.tanggal asc, a.nik asc";

	$get_double = db::connection('mysql3')->select($double);

	return DataTables::of($get_double)
	->addColumn('action', function($get_double){
		return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_emp(this.id)" id="'.$get_double->id_ot.'+'.$get_double->nik.'"><i class="fa fa-trash"></i> Delete</a>';
		
	})
	->rawColumns(['action' => 'action'])
	->make(true);
}
}