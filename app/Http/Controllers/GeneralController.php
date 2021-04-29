<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Mail\SendEmail;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\Employee;
use App\EmployeeSync;
use App\GeneralTransportation;
use App\GeneralTransportationData;
use App\GeneralDoctor;
use App\CodeGenerator;
use App\GeneralShoesLog;
use App\GeneralShoesRequest;
use App\GeneralShoesStock;
use App\User;
use App\Agreement;
use App\SafetyRiding;
use App\AgreementAttachment;
use App\GeneralAirVisualLog;
use App\WeeklyCalendar;
use PDF;
use Auth;
use Excel;
use DataTables;
use Response;

class GeneralController extends Controller{

	public function __construct(){
		$this->middleware('auth');

		$this->printers = [
			'Barcode Printer Sax',
			'Barrel-Printer',
			'FLO Printer 101',
			'FLO Printer 102',
			'FLO Printer 103',
			'FLO Printer 104',
			'FLO Printer 105',
			'FLO Printer LOG',
			'FLO Printer RC',
			'FLO Printer VN',
			'KDO ZPRO',
			'MIS',
			'MIS2',
			'Stockroom-Printer',
			'Welding-Printer'
		];

		$this->agreement_statuses = [
			'In Use',
			'Terminated',
		];

	}

	public function indexSafetyRiding(){
		$title = "Safety Riding";
		$title_jp = "『安全運転宣言』　実践記録表";

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

		$employees = EmployeeSync::where('department', '=', $employee->department)->orderBy('hire_date', 'asc')->get();

		return view('general.pointing_call.safety_riding', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'agreement_statuses' => $this->agreement_statuses
		))->with('page', 'Safety Riding');
	}

	public function fetchSafetyRiding(){
		$employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		->select('employees.remark', 'employee_syncs.department')
		->first();

		// $safety_ridings = SafetyRiding::leftJoin('users', 'users.id', '=', 'safety_ridings.created_by')
		// ->where('department', '=', $employee->department)
		// ->where('location', '=', $employee->remark)
		// ->select(
		// 	db::raw('date_format(safety_ridings.period, "%b %Y") as vperiod'), 
		// 	db::raw('date_format(safety_ridings.created_at, "%d-%b-%Y") as vcreated'), 
		// 	'safety_ridings.period', 
		// 	'safety_ridings.location', 
		// 	'safety_ridings.department', 
		// 	'users.username', 
		// 	'users.name', 
		// 	db::raw('date_format(safety_ridings.created_at, "%Y-%m-%d") as created'))
		// ->distinct()
		// ->get();

		$safety_ridings = db::select("SELECT
			date_format( safety_ridings.period, '%b %Y' ) AS vperiod,
			date_format( safety_ridings.created_at, '%d-%b-%Y' ) AS vcreated,
			safety_ridings.period,
			safety_ridings.location,
			safety_ridings.department,
			group_concat( DISTINCT users.NAME ) AS name,
			date_format( safety_ridings.created_at, '%Y-%m-%d' ) AS created 
			FROM
			safety_ridings
			LEFT JOIN users ON users.id = safety_ridings.created_by 
			WHERE
			safety_ridings.department = '".$employee->department."' 
			AND safety_ridings.location = '".$employee->remark."' 
			GROUP BY
			date_format( safety_ridings.period, '%b %Y' ),
			date_format( safety_ridings.created_at, '%d-%b-%Y' ),
			safety_ridings.period,
			safety_ridings.location,
			safety_ridings.department,
			date_format(
			safety_ridings.created_at,
			'%Y-%m-%d' 
		)");

		$response = array(
			'status' => true,
			'safety_ridings' => $safety_ridings
		);
		return Response::json($response);
	}

	public function fetchSafetyRidingPdf($id){

		$param = explode('_', $id);

		$first = date('Y-m-01', strtotime($param[0]));
		$last = date('Y-m-t', strtotime($param[0]));

		$weekly_calendars = WeeklyCalendar::where('week_date', '>=', $first)
		->where('week_date', '<=', $last)
		->get();

		$safety_ridings = SafetyRiding::leftJoin('users', 'users.id', '=', 'safety_ridings.created_by')
		->where('location', '=', $param[1])
		->where('period', '=', $param[0])
		->where('department', '=', $param[2])
		->select(
			'safety_ridings.period',
			'safety_ridings.department',
			'safety_ridings.employee_name',
			'safety_ridings.safety_riding',
			'users.name'
		)
		->get();

		$chief = Employee::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		->where('employees.remark', '=', $param[1])
		->where('employee_syncs.department', '=', $param[2])
		->where('employee_syncs.position', '=', 'Chief')
		->select('employee_syncs.name')
		->first();

		$manager = Employee::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		->where('employees.remark', '=', $param[1])
		->where('employee_syncs.department', '=', $param[2])
		->where('employee_syncs.position', '=', 'Manager')
		->select('employee_syncs.name')
		->first();

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'landscape');

		// $pdf->loadView('general.pointing_call.safety_riding_pdf', array(
		// 	'weekly_calendars' => $weekly_calendars,
		// 	'safety_ridings' => $safety_ridings,
		// 	'chief' => $chief,
		// 	'manager' => $manager
		// ));

		// return $pdf->stream("general.pointing_call.safety_riding_pdf");

		return view('general.pointing_call.safety_riding_pdf', array(
			'weekly_calendars' => $weekly_calendars,
			'safety_ridings' => $safety_ridings,
			'chief' => $chief,
			'manager' => $manager
		));
	}

	public function fetchSafetyRidingMember(Request $request){
		$employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		->select('employees.remark', 'employee_syncs.department')
		->first();

		// $employees = Employee::where('employees.remark', '=', $employee->remark)
		// ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		// ->leftJoin('safety_ridings', 'safety_ridings.employee_id', '=', 'employee_syncs.employee_id')
		// ->where('safety_ridings.period', '=', date('Y-m-01', strtotime($request->get('period'))))
		// ->where('employee_syncs.department', '=', $employee->department)
		// ->whereNull('employee_syncs.end_date')
		// ->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'employees.remark', 'safety_ridings.safety_riding')
		// ->get();

		$employees = db::select("SELECT
			employee_syncs.employee_id,
			employee_syncs.name,
			employee_syncs.department,
			employees.remark,
			sr.safety_riding 
			FROM
			employees
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = employees.employee_id
			LEFT JOIN ( SELECT employee_id, safety_riding FROM safety_ridings WHERE safety_ridings.period = '".date('Y-m-01', strtotime($request->get('period')))."' ) AS sr ON sr.employee_id = employees.employee_id 
			WHERE
			employees.remark = '".$employee->remark."' 
			AND employee_syncs.department = '".$employee->department."' 
			AND employee_syncs.end_date IS NULL");

		$response = array(
			'status' => true,
			'employees' => $employees
		);
		return Response::json($response);
	}

	public function createSafetyRiding(Request $request){
		try{

			foreach($request->get('safety_ridings') as $safety_riding){
				$safety = explode('_', $safety_riding);

				$input = SafetyRiding::updateOrCreate(
					[
						'period' => date('Y-m-01', strtotime($request->get('period'))),
						'employee_id' => $safety[0],
						'department' => $request->get('department')
					],
					[
						'period' => date('Y-m-01', strtotime($request->get('period'))),
						'location' => $request->get('location'),
						'department' => $request->get('department'),
						'employee_id' => $safety[0],
						'employee_name' => $safety[1],
						'safety_riding' => $safety[2],
						'created_by' => Auth::id()
					]
				);

				$input->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Safety Riding berhasil dibuat'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function indexAgreement(){

		$title = "Company Agreement List";
		$title_jp = "会社の契約書";

		$employees = EmployeeSync::orderBy('department', 'asc')->get();

		return view('general.agreements.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'agreement_statuses' => $this->agreement_statuses
		));

	}

	public function editAgreement(Request $request){
		$filename = "";
		$file_destination = 'files/agreements';

		if (count($request->file('newAttachment')) > 0) {
			$file = $request->file('newAttachment');
			$filename = date('YmdHis').'.'.$request->input('extension');
			$file->move($file_destination, $filename);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Please select file to attach'
			);
			return Response::json($response);
		}

		try{
			$agreement = Agreement::where('id', '=', $request->get('newId'))->first();

			if($agreement->department == $request->input('newDepartment') && $agreement->vendor == $request->input('newVendor') && $agreement->description == $request->input('newDescription') && $agreement->valid_from == $request->input('newValidFrom') && $agreement->valid_to == $request->input('newValidTo') && $agreement->status == $request->input('newStatus') && $agreement->remark == $request->input('newRemark')){

				$response = array(
					'status' => false,
					'message' => 'Tidak ada perubahan yang dibuat'
				);
				return Response::json($response);

			}

			$agreement->department = $request->input('newDepartment');
			$agreement->vendor = $request->input('newVendor');
			$agreement->description = $request->input('newDescription');
			$agreement->valid_from = $request->input('newValidFrom');
			$agreement->valid_to = $request->input('newValidTo');
			$agreement->status = $request->input('newStatus');
			$agreement->remark = $request->input('newRemark');
			$agreement->created_by = Auth::user()->username;
			$agreement->save();

			$attachment = new AgreementAttachment([
				'agreement_id' => $request->get('newId'),
				'file_name' => $filename,
				'created_by' => Auth::user()->username
			]);

			$attachment->save();

			$agreements = db::select("SELECT
				'update_agreement' as cat,
				a.id,
				a.department,
				d.department_shortname,
				a.vendor,
				a.description,
				a.valid_from,
				a.valid_to,
				TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
				TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
				a.`status`,
				a.remark,
				a.created_at,
				a.updated_at,
				a.created_by,
				es.`name`,
				att.file_name 
				FROM
				agreements AS a
				LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
				LEFT JOIN departments AS d ON d.department_name = a.department
				LEFT JOIN (
				SELECT
				agreement_id,
				file_name 
				FROM
				agreement_attachments 
				WHERE
				id = ( SELECT id FROM agreement_attachments WHERE agreement_id = ".$request->get('newId')." ORDER BY created_at DESC LIMIT 1 )) AS att ON att.agreement_id = a.id
				WHERE
				a.id = ".$request->get('newId')."");

			$manager = db::select("select email from send_emails where remark = '".$request->input('newDepartment')."'");

			Mail::to(['adhi.satya.indradhi@music.yamaha.com', Auth::user()->email])->cc(['prawoto@music.yamaha.com', $manager[0]->email])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($agreements, 'update_agreement'));

			$response = array(
				'status' => true,
				'message' => 'Agreement Updated'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function downloadAgreement(Request $request){
		$filenames = $request->get('file_name');
		$paths = array();

		if(is_array($filenames)){
			foreach ($filenames as $filename) {
				$path = "files/agreements/" . $filename;
				array_push($paths, 
					[
						"download" => asset($path),
						"filename" => $filename
					]);
			}
		}
		else{
			$path = "files/agreements/" . $filenames;
			array_push($paths, 
				[
					"download" => asset($path),
					"filename" => $filenames
				]);
		}

		$response = array(
			'status' => true,
			'file_paths' => $paths,
		);
		return Response::json($response);
	}

	public function fetchAgreementDetail(Request $request){
		$employees = EmployeeSync::orderBy('department', 'asc')
		->select('department')
		->distinct()
		->get();

		$agreement = Agreement::where('agreements.id', '=', $request->get('id'))
		->first();

		$response = array(
			'status' => true,
			'employees' => $employees,
			'agreement' => $agreement,
			'agreement_statuses' => $this->agreement_statuses
		);
		return Response::json($response);
	}

	public function fetchAgreementDownload(Request $request){
		$files = AgreementAttachment::where('id', '=', $request->get('id'))->orderBy('created_at', 'desc')->get();

		$response = array(
			'status' => true,
			'files' => $files,
		);
		return Response::json($response);
	}

	public function createAgreement(Request $request){
		$filename = "";
		$file_destination = 'files/agreements';

		if (count($request->file('newAttachment')) > 0) {
			$file = $request->file('newAttachment');
			$filename = date('YmdHis').'.'.$request->input('extension');
			$file->move($file_destination, $filename);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Please select file to attach'
			);
			return Response::json($response);
		}

		try{
			$agreement = new Agreement([
				'department' => $request->input('newDepartment'),
				'vendor' => $request->input('newVendor'),
				'description' => $request->input('newDescription'),
				'valid_from' => $request->input('newValidFrom'),
				'valid_to' => $request->input('newValidTo'),
				'status' => $request->input('newStatus'),
				'remark' => $request->input('newRemark'),
				'created_by' => Auth::user()->username
			]);

			$agreement->save();

			$attachment = new AgreementAttachment([
				'agreement_id' => $agreement->id,
				'file_name' => $filename,
				'created_by' => Auth::user()->username
			]);

			$attachment->save();

			$agreements = db::select("SELECT
				'new_agreement' as cat,
				a.id,
				a.department,
				d.department_shortname,
				a.vendor,
				a.description,
				a.valid_from,
				a.valid_to,
				TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
				TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
				a.`status`,
				a.remark,
				a.created_at,
				a.updated_at,
				a.created_by,
				es.`name`,
				att.file_name 
				FROM
				agreements AS a
				LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
				LEFT JOIN departments AS d ON d.department_name = a.department
				LEFT JOIN (
				SELECT
				agreement_id,
				file_name 
				FROM
				agreement_attachments 
				WHERE
				id = ( SELECT id FROM agreement_attachments WHERE agreement_id = ".$agreement->id." ORDER BY created_at DESC LIMIT 1 )) AS att ON att.agreement_id = a.id
				WHERE
				a.id = ".$agreement->id."");

			$manager = db::select("select email from send_emails where remark = '".$request->input('newDepartment')."'");

			Mail::to(['adhi.satya.indradhi@music.yamaha.com', Auth::user()->email])->cc(['prawoto@music.yamaha.com', $manager[0]->email])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($agreements, 'new_agreement'));

			$response = array(
				'status' => true,
				'message' => 'New Agreement Successfully Added'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchAgreement(){
		$employee_id = Auth::user()->username;
		$employee = EmployeeSync::where('employee_id', '=', $employee_id)->first();

		$where = "";

		if($employee->department != 'Management Information System Department' && $employee->department != 'Human Resources Department' && Auth::user()->role_code != 'S'){
			$where = "WHERE a.department = '".$employee->department."'";
		}

		$agreements = db::select("SELECT
			a.id,
			a.department,
			d.department_shortname,
			a.vendor,
			a.description,
			a.valid_from,
			a.valid_to,
			TIMESTAMPDIFF( DAY, a.valid_from, a.valid_to ) AS total_validity,
			TIMESTAMPDIFF( DAY, date( now()), a.valid_to ) AS validity,
			a.`status`,
			a.remark,
			a.created_at,
			a.updated_at,
			a.created_by,
			es.`name`,
			COALESCE ( aa.att, 0 ) AS att 
			FROM
			agreements AS a
			LEFT JOIN employee_syncs AS es ON es.employee_id = a.created_by
			LEFT JOIN ( SELECT agreement_id, count( id ) AS att FROM agreement_attachments GROUP BY agreement_id ) AS aa ON aa.agreement_id = a.id
			LEFT JOIN departments AS d ON d.department_name = a.department 
			".$where."
			");

		$response = array(
			'status' => true,
			'agreements' => $agreements
		);
		return Response::json($response);
	}

	public function indexSafetyShoesLog(){
		$title = "Safety Shoes Log";
		$title_jp = "";

		$employees = EmployeeSync::orderBy('name', 'asc')->get();

		$users = User::where('username', 'like', 'PI%')->get();

		return view('general.safety_shoes.safety_shoes_log', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'users' => $users
		));
	}

	public function indexSafetyShoes(){
		$title = "Safety Shoes Control";
		$title_jp = "安全靴管理システム";

		$employees = EmployeeSync::orderBy('name', 'asc')->get();

		$user = EmployeeSync::where('employee_id', Auth::user()->username)->first();

		return view('general.safety_shoes.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'user' => $user,
			'printers' => $this->printers
		));
	}

	public function indexMosaic(){
		$title = "Yamaha Day Mosaic Art Project";
		$title_jp = "";

		$mosaics = db::select("SELECT COALESCE
			( employee_syncs.department, 'Management' ) AS department,
			count( DISTINCT general_mosaics.employee_id ) AS count_person,
			count( general_mosaics.mosaic_id ) count_upload 
			FROM
			general_mosaics
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = general_mosaics.employee_id 
			GROUP BY
			employee_syncs.department");

		return view('general.mosaic', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mosaics' => $mosaics
		));
	}

	public function fetchMosaicDetail(){

	}

	public function indexReportSuratDokter(){
		$title = 'Laporan Dropbox Surat Dokter';
		$title_jp = '診断書のドロップボックス';

		return view('general.dropbox.surat_dokter_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Report Surat Dokter')
		->with('head', 'HR Report');
	}

	public function confirmSuratDokterReport(Request $request){
		try{

			$doctor = GeneralDoctor::where('id', '=', $request->get('id'))->first();

			if($request->get('status') == '1'){
				$doctor->remark = 1;
				$doctor->save();

				$response = array(
					'status' => true,
					'message' => 'Data berhasil dikonfirmasi'
				);
				return Response::json($response);
			}
			else if($request->get('status') == '2'){
				$doctor->remark = $request->get('status');
				$doctor->save();

				$response = array(
					'status' => true,
					'message' => 'Data berhasil ditolak'
				);
				return Response::json($response);
			}
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}		
	}

	public function fetchReportSuratDokter(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$doctors = db::select('SELECT
			date( g.created_at ) AS tanggal_pengajuan,
			g.id,
			g.employee_id,
			e.`name`,
			e.`department`,
			e.`section`,
			g.doctor_name,
			g.diagnose,
			g.date_from,
			g.date_to,
			g.attachment_file,
			g.remark,
			g.created_by,
			e2.`name` AS created_name 
			FROM
			`general_doctors` g
			LEFT JOIN employee_syncs AS e ON g.employee_id = e.employee_id
			LEFT JOIN employee_syncs e2 ON e2.employee_id = g.created_by 
			WHERE
			date_format( g.created_at, "%Y-%m" ) = "'.$month.'"');

		$datas = array();

		foreach ($doctors as $doctor) {
			array_push($datas, 
				[
					"tanggal_pengajuan" => $doctor->tanggal_pengajuan,
					"id" => $doctor->id,
					"employee_id" => $doctor->employee_id,
					"name" => $doctor->name,
					"department" => $doctor->department,
					"section" => $doctor->section,
					"doctor_name" => $doctor->doctor_name,
					"diagnose" => $doctor->diagnose,
					"date_from" => $doctor->date_from,
					"date_to" => $doctor->date_to,
					"remark" => $doctor->remark,
					"created_by" => $doctor->created_by,
					"created_name" => $doctor->created_name,
					"attachment_file" => asset('files/surat_dokter/'.$doctor->attachment_file)
				]);

		}

		$response = array(
			'status' => true,
			'doctors' => $datas,
			'period' => date('F Y', strtotime($month))
		);
		return Response::json($response);		
	}

	public function indexSuratDokter(){
		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

		$employees = EmployeeSync::whereNull('end_date')
		->select('employee_id', 'name');

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && Auth::user()->role_code != 'HR'){
			$employees = $employees->where('department', '=', $employee->department);
		}

		$employees = $employees->orderBy('employee_id', 'ASC')->get();

		$title = 'Dropbox Report Surat Dokter';
		$title_jp = '';

		return view('general.dropbox.surat_dokter', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
		))->with('page', 'Surat Dokter');

	}

	public function fetchSuratDokter(Request $request){
		$first = date('Y-m-d', strtotime("-90 days"));

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();
		$general_doctors = db::table('general_doctors')->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_doctors.employee_id')
		->where('general_doctors.created_by', '=', Auth::user()->username);

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && Auth::user()->role_code != 'HR'){
			$general_doctors = $general_doctors->where('employee_syncs.department', '=', $employee->department);
		}

		if(strlen($request->get('date_from')) > 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', date('Y-m-d', strtotime($request->get('date_from'))));
		}
		if(strlen($request->get('date_to')) > 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '<=', date('Y-m-d', strtotime($request->get('date_to'))));
		}
		if(strlen($request->get('date_from')) == 0 && strlen($request->get('date_from')) == 0){
			$general_doctors = $general_doctors->where(DB::raw('DATE_FORMAT(general_doctors.created_at, "%Y-%m-%d")'), '>=', $first);		
		}

		$general_doctors = $general_doctors->whereNull('general_doctors.deleted_at')
		->select('general_doctors.id', 'general_doctors.employee_id', 'employee_syncs.name', 'general_doctors.doctor_name', 'general_doctors.diagnose', 'general_doctors.date_from', 'general_doctors.date_to', 'general_doctors.attachment_file', 'remark', 'created_by', db::raw('date(general_doctors.created_at) as created_at'))->get();

		$datas = array();

		foreach ($general_doctors as $general_doctor) {
			array_push($datas, 
				[
					"id" => $general_doctor->id,
					"employee_id" => $general_doctor->employee_id,
					"attachment_file" => asset('files/surat_dokter/'.$general_doctor->attachment_file),
					"name" => $general_doctor->name,
					"doctor_name" => $general_doctor->doctor_name,
					"diagnose" => $general_doctor->diagnose,
					"date_from" => $general_doctor->date_from,
					"date_to" => $general_doctor->date_to,
					"remark" => $general_doctor->remark,
					"created_by" => $general_doctor->created_by,
					"created_at" => $general_doctor->created_at
				]);
		}

		$response = array(
			'status' => true,
			'general_doctors' => $datas
		);
		return Response::json($response);
	}

	public function deleteSuratDokter(Request $request){
		try{
			$general_doctor = GeneralDoctor::where('id', '=', $request->get('id'))->forceDelete();	

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dihapus'
			);
			return Response::json($response);		
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function inputReceiveSafetyShoes(Request $request){
		$msg = $request->get('msg');
		$request_id = $request->get('request_id');

		if($msg == 'receive'){

			DB::beginTransaction();
			$request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
			->where('general_shoes_requests.request_id', $request_id)
			->select(
				'general_shoes_requests.merk',
				'general_shoes_requests.gender',
				'general_shoes_requests.size',
				'general_shoes_requests.employee_id',
				'general_shoes_requests.created_by',
				'employee_syncs.name',
				'employee_syncs.department',
				'employee_syncs.section',
				'employee_syncs.group',
				'employee_syncs.sub_group'
			)
			->get();

			for ($i=0; $i < count($request); $i++) {
				try {
					$log = new GeneralShoesLog([
						'merk' => $request[$i]['merk'],
						'gender' => $request[$i]['gender'],
						'size' => $request[$i]['size'],
						'quantity' => -1,
						'status' => 'Pinjam',
						'employee_id' => $request[$i]['employee_id'],
						'name' => $request[$i]['name'],
						'department' => $request[$i]['department'],
						'section' => $request[$i]['section'],
						'group' => $request[$i]['group'],
						'sub_group' => $request[$i]['sub_group'],
						'requested_by' => $request[$i]['created_by'],
						'created_by' => Auth::id()
					]);
					$log->save();


					$stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
					->where('merk', $request[$i]['merk'])
					->where('size', $request[$i]['size'])
					->first();

					$stock->quantity = $stock->quantity - 1;
					$stock->save();


				} catch (Exception $e) {
					DB::rollback();
					$response = array(
						'status' => false,
						'message' => $e->getMessage()
					);
					return Response::json($response);
				}			
			}

			try {
				$request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}

			DB::commit();
			$response = array(
				'status' => true,
				'message' => 'Request Safety Shoes berhasil diterima'
			);
			return Response::json($response);


		}else{
			DB::beginTransaction();
			$request = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
			->where('general_shoes_requests.request_id', $request_id)
			->select('employee_syncs.gender', 'general_shoes_requests.size', db::raw('COUNT(general_shoes_requests.id) AS qty'))
			->groupBy('employee_syncs.gender', 'general_shoes_requests.size')
			->get();

			for ($i=0; $i < count($request); $i++) {
				$stock = GeneralShoesStock::where('gender', $request[$i]['gender'])
				->where('size', $request[$i]['size'])
				->first();

				$stock->temp_stock = $stock->temp_stock + $request[$i]['qty'];

				try {
					$stock->save();
				} catch (Exception $e) {
					DB::rollback();
					$response = array(
						'status' => false,
						'message' => $e->getMessage()
					);
					return Response::json($response);
				}
			}

			try {
				$request = GeneralShoesRequest::where('request_id', $request_id)->forceDelete();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}

			DB::commit();
			$response = array(
				'status' => true,
				'message' => 'Request Safety Shoes berhasil ditolak'
			);
			return Response::json($response);
		}

	}

	public function inputSafetyShoes(Request $request){
		$stock = $request->get('stock');
		$data = array();


		DB::beginTransaction();
		for ($i=0; $i < count($stock); $i++) {
			try {
				if($stock[$i]['status'] == 'Simpan'){
					$shoes = GeneralShoesStock::where('merk',  $stock[$i]['merk'])
					->where('gender',  $stock[$i]['gender'])
					->where('size',  $stock[$i]['size'])
					->first();

					if($shoes){
						$shoes->temp_stock = $shoes->temp_stock + $stock[$i]['qty'];
						$shoes->quantity = $shoes->quantity + $stock[$i]['qty'];
						$shoes->save();
					}else{
						$shoes = new GeneralShoesStock([
							'condition' => 'Layak Pakai',
							'merk' => $stock[$i]['merk'],
							'gender' => $stock[$i]['gender'],
							'size' => $stock[$i]['size'],
							'temp_stock' => $stock[$i]['qty'],
							'quantity' => $stock[$i]['qty'],
							'created_by' => Auth::id()
						]);
						$shoes->save();
					}
				}

				$emp = EmployeeSync::where('employee_id', $stock[$i]['employee_id'])->first();

				array_push($data,[
					'employee_id' => $emp->employee_id,
					'name' => $emp->name,
					'department' => $emp->department,
					'section' => $emp->section,
					'group' => $emp->group,
					'merk' => $stock[$i]['merk'],
					'gender' => $stock[$i]['gender'],
					'size' => $stock[$i]['size'],
					'quantity' => $stock[$i]['qty'],
					'status' => $stock[$i]['status']
				]);


				$log = new GeneralShoesLog([
					'merk' => $stock[$i]['merk'],
					'gender' => $stock[$i]['gender'],
					'size' => $stock[$i]['size'],
					'quantity' => $stock[$i]['qty'],
					'status' => $stock[$i]['status'],
					'employee_id' => $emp->employee_id,
					'name' => $emp->name,
					'department' => $emp->department,
					'section' => $emp->section,
					'group' => $emp->group,
					'sub_group' => $emp->sub_group,
					'created_by' => Auth::id()
				]);
				$log->save();
			} catch (Exception $e) {
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}
		}

		$mail_to = db::table('send_emails')
		->where('remark', '=', 'safety_shoes')
		->WhereNull('deleted_at')
		->select('email')
		->get();

		Mail::to($mail_to)
		->bcc('aditya.agassi@music.yamaha.com')
		->send(new SendEmail($data, 'safety_shoes'));

		DB::commit();

		$response = array(
			'status' => true,
			'message' => 'Safety Shoes berhasil ditambahkan'
		);
		return Response::json($response);
	}

	public function inputReqSafetyShoes(Request $request){
		$employee = $request->get('employee');
		$printer = $request->get('printer');

		DB::beginTransaction();
		for ($i=0; $i < count($employee); $i++) {
			$stock = GeneralShoesStock::where('merk',  $employee[$i]['merk']);
			if($employee[$i]['gender'] == 'L'){
				$stock = $stock->where('gender',  $employee[$i]['gender']);
			}
			$stock = $stock->where('size',  $employee[$i]['size'])
			->first();

			if($stock){
				if($stock->temp_stock >= 1){
					$stock->temp_stock = $stock->temp_stock - 1;
					$stock->save();
				}else{
					DB::rollback();

					$stock = GeneralShoesStock::where('merk',  $employee[$i]['merk'])
					->where('gender',  $employee[$i]['gender'])
					->where('size',  $employee[$i]['size'])
					->first();

					$response = array(
						'status' => false,
						'message' => 'Sepatu '.$employee[$i]['merk'].' ukuran '.$employee[$i]['size'].' ('.$employee[$i]['gender'].') stock tidak cukup. Stock tersisa '.$stock->temp_stock
					);
					return Response::json($response);
				}
			}else{
				DB::rollback();
				$response = array(
					'status' => false,
					'message' => 'Tidak ada Sepatu '.$employee[$i]['merk']. ' ukuran '.$employee[$i]['size'].' ('.$employee[$i]['gender'].')'
				);
				return Response::json($response);
			}			
		}
		DB::commit();


		$prefix_now = 'REQ'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','general-request')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$request_id = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();


		for ($i=0; $i < count($employee); $i++) {
			try{
				$request = new GeneralShoesRequest([
					'request_id' => $request_id,
					'employee_id' => $employee[$i]['employee_id'],
					'gender' => $employee[$i]['gender'],
					'merk' => $employee[$i]['merk'],
					'size' => $employee[$i]['size'],
					'created_by' => Auth::id()
				]);
				$request->save();

			} catch (Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}
		}

		$data = GeneralShoesRequest::leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
		->where('general_shoes_requests.request_id', $request_id)
		->select(
			'general_shoes_requests.gender',
			'general_shoes_requests.merk',
			'general_shoes_requests.size',
			'users.name',
			db::raw('COUNT(general_shoes_requests.id) AS qty')
		)
		->groupBy(
			'general_shoes_requests.gender',
			'general_shoes_requests.merk',
			'general_shoes_requests.size',
			'users.name'
		)
		->orderBy('general_shoes_requests.gender', 'ASC')
		->orderBy('general_shoes_requests.size', 'ASC')
		->get();

		$this->safetyShoesSlip($data, $request_id, $printer, 'Print');

		$response = array(
			'status' => true,
			'message' => 'Safety Shoes berhasil ditambahkan'
		);
		return Response::json($response);
	}

	public function reprintReqSafetyShoes(Request $request){
		$printer = $request->get('printer');
		$request_id = $request->get('request_id');


		$data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
		->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
		->where('general_shoes_requests.request_id', $request_id)
		->select(
			'employee_syncs.gender',
			'general_shoes_requests.merk',
			'general_shoes_requests.size',
			'users.name',
			db::raw('COUNT(general_shoes_requests.id) AS qty')
		)
		->groupBy(
			'employee_syncs.gender',
			'general_shoes_requests.merk',
			'general_shoes_requests.size',
			'users.name'
		)
		->orderBy('employee_syncs.gender', 'ASC')
		->orderBy('general_shoes_requests.size', 'ASC')
		->get();

		$this->safetyShoesSlip($data, $request_id, $printer, 'Reprint');

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function safetyShoesSlip($data, $request_id, $printer_name, $remark){


		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->initialize();

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text(strtoupper(" safety shoes request \n"));
		$printer->initialize();

		$printer->feed(1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->setTextSize(1, 1);
		$printer->text($request_id."\n");
		$printer->feed(1);


		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$total_qty = 0;
		for ($i=0; $i < count($data); $i++) {
			$gender = $this->writeString('('.$data[$i]->gender.')', 2, ' ');
			$size = $this->writeString($data[$i]->size, 2, ' ');
			$qty = $this->writeNumber($data[$i]->qty, 2, ' ');
			$merk = $this->writeString($data[$i]->merk, 8, ' ');

			$printer->text($merk ." ".$gender." Size ".$size." -> ".$qty. " Pasang");
			$printer->feed(1);

		}
		$printer->feed(2);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		if($remark == 'Reprint'){
			$printer->text(" Reprint "."\n");
		}
		$printer->textRaw($data[0]->name."\n");
		$printer->textRaw("(".date("d-M-Y H:i:s").")\n");
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function writeString($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$text .= $char;
			}
		}
		return strtoupper($text);
	}

	public function writeNumber($text, $maxLength, $char){
		$return = "";

		if ($maxLength > 0) {

			$textLength = strlen($text);
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$return .= $char;
			}

			$return .= $text;

		}
		return strtoupper($return);
	}

	public function inputSuratDokter(Request $request){
		$filename = "";
		$file_destination = 'files/surat_dokter';

		if (count($request->file('attachment')) > 0) {
			$file = $request->file('attachment');
			$filename = md5($request->input('employee_id').date('YmdHis')).'.'.$request->input('extension');
			$file->move($file_destination, $filename);
		}

		try{
			GeneralDoctor::create([
				'employee_id' => $request->input('employee_id'),
				'doctor_name' => $request->input('doctor_name'),
				'diagnose' => $request->input('diagnose'),
				'date_from' => date('Y-m-d', strtotime($request->input('date_from'))),
				'date_to' => date('Y-m-d', strtotime($request->input('date_to'))),
				'attachment_file' => $filename,
				'remark' => 0,
				'created_by' => Auth::user()->username
			]);

			$response = array(
				'status' => true,
				'message' => 'Data baru berhasil ditambahkan'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function indexReportTransportation(){
		$title = 'Online Attendace And Transportation Report';
		$title_jp = '出社・移動費のオンライン報告';

		return view('general.dropbox.online_transportation_report', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Report Attendances & Tsransportations')
		->with('head', 'HR Report');
	}

	public function indexOnlineTransportation(){
		$title = 'Online Attendace And Transportation Record';
		$title_jp = '';

		$employee = EmployeeSync::leftJoin('domiciles', 'domiciles.employee_id', '=', 'employee_syncs.employee_id')
		->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'employee_syncs.department', 'employee_syncs.section', 'domiciles.domicile_address', 'employee_syncs.zona')
		->where('employee_syncs.employee_id', '=', Auth::user()->username)
		->first();

		if(Auth::user()->role_code != 'MIS' && Auth::user()->role_code != 'S' && substr($employee->grade_code, 0, 1) != 'L' && substr($employee->grade_code, 0, 1) != 'M' && Auth::user()->username != 'pi0603019' && Auth::user()->username != 'pi9902018' && Auth::user()->username != 'pi9809008' && Auth::user()->username != 'pi9903003'){
			return view('404');
		}

		return view('general.dropbox.online_transportation', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $employee
		))->with('head', 'Online Attendance And Transportation');
	}

	public function deleteOnlineTransportation(Request $request){
		try{
			$general_transporation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
			$general_transporation->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dihapus'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function inputOnlineTransportation(Request $request){
		$filename = "";
		$file_destination = 'files/general_transportation';

		// if (count($request->file('newAttachment')) > 0) {
		// 	$file = $request->file('newAttachment');
		// 	$filename = md5($request->input('employee_id').date('YmdHis')).'.'.$request->input('extension');
		// 	$file->move($file_destination, $filename);
		// }

		try{
			$ivms = DB::SELECT("SELECT
				* 
				FROM
				ivms.ivms_attendance_triggers 
				WHERE
				employee_id = '".$request->input('employee_id')."' 
				AND auth_date = '".date('Y-m-d', strtotime($request->input('newDate')))."'");
			// if (count($ivms) > 0) {
			$general = GeneralTransportation::where('employee_id',$request->input('employee_id'))->where('attend_code',$request->input('newAttend'))->where('check_date',date('Y-m-d', strtotime($request->input('newDate'))))->get();

			if (count($general) == 0) {
				if ($request->get('newAttend') == 'cuti' || $request->get('newAttend') == 'izin' || $request->get('newAttend') == 'sakit') {
					if ($filename == "") {
						GeneralTransportation::create([
							'employee_id' => $request->input('employee_id'),
							'grade' => $request->input('grade'),
							'zona' => $request->input('zona'),
							'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
							'attend_code' => $request->input('newAttend'),
							'vehicle' => $request->input('newVehicle'),
							'origin' => $request->input('newOrigin'),
							'destination' => $request->input('newDestination'),
							'highway_amount' => $request->input('newHighwayAmount'),
							'distance' => $request->input('newDistance'),
								// 'highway_attachment' => $filename,
							'remark' => 0,
							'created_by' => Auth::id()
						]);
					}else{
						GeneralTransportation::create([
							'employee_id' => $request->input('employee_id'),
							'grade' => $request->input('grade'),
							'zona' => $request->input('zona'),
							'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
							'attend_code' => $request->input('newAttend'),
							'vehicle' => $request->input('newVehicle'),
							'origin' => $request->input('newOrigin'),
							'destination' => $request->input('newDestination'),
							'highway_amount' => $request->input('newHighwayAmount'),
							'distance' => $request->input('newDistance'),
							'highway_attachment' => $filename,
							'remark' => 0,
							'created_by' => Auth::id()
						]);
					}

					$general_data = GeneralTransportationData::firstOrNew(['employee_id' => $request->input('employee_id'), 'attend_code' => $request->input('newAttend')]);
					$general_data->employee_id = $request->input('employee_id');
					$general_data->attend_code = $request->input('newAttend');
					$general_data->vehicle = $request->input('newVehicle');
					$general_data->origin = $request->input('newOrigin');
					$general_data->distance = $request->input('newDistance');
					$general_data->destination = $request->input('newDestination');
					$general_data->highway_amount = $request->input('newHighwayAmount');
					$general_data->created_by = Auth::id();
					$general_data->save();

					$response = array(
						'status' => true,
						'message' => 'Data baru berhasil ditambahkan'
					);
					return Response::json($response);
				}else{
					if (count($ivms) > 0) {
						if ($filename == "") {
							GeneralTransportation::create([
								'employee_id' => $request->input('employee_id'),
								'grade' => $request->input('grade'),
								'zona' => $request->input('zona'),
								'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
								'attend_code' => $request->input('newAttend'),
								'vehicle' => $request->input('newVehicle'),
								'origin' => $request->input('newOrigin'),
								'destination' => $request->input('newDestination'),
								'highway_amount' => $request->input('newHighwayAmount'),
								'distance' => $request->input('newDistance'),
									// 'highway_attachment' => $filename,
								'remark' => 0,
								'created_by' => Auth::id()
							]);
						}else{
							GeneralTransportation::create([
								'employee_id' => $request->input('employee_id'),
								'grade' => $request->input('grade'),
								'zona' => $request->input('zona'),
								'check_date' => date('Y-m-d', strtotime($request->input('newDate'))),
								'attend_code' => $request->input('newAttend'),
								'vehicle' => $request->input('newVehicle'),
								'origin' => $request->input('newOrigin'),
								'destination' => $request->input('newDestination'),
								'highway_amount' => $request->input('newHighwayAmount'),
								'distance' => $request->input('newDistance'),
								'highway_attachment' => $filename,
								'remark' => 0,
								'created_by' => Auth::id()
							]);
						}

						$general_data = GeneralTransportationData::firstOrNew(['employee_id' => $request->input('employee_id'), 'attend_code' => $request->input('newAttend')]);
						$general_data->employee_id = $request->input('employee_id');
						$general_data->attend_code = $request->input('newAttend');
						$general_data->vehicle = $request->input('newVehicle');
						$general_data->origin = $request->input('newOrigin');
						$general_data->distance = $request->input('newDistance');
						$general_data->destination = $request->input('newDestination');
						$general_data->highway_amount = $request->input('newHighwayAmount');
						$general_data->created_by = Auth::id();
						$general_data->save();

						$response = array(
							'status' => true,
							'message' => 'Data baru berhasil ditambahkan'
						);
						return Response::json($response);
					}else{
						$response = array(
							'status' => false,
							'message' => 'Checklog Anda tidak tersedia pada tanggal '.date('Y-m-d', strtotime($request->input('newDate')))
						);
						return Response::json($response);
					}
				}
			}else{
				$response = array(
					'status' => false,
					'message' => 'Data Pernah Ditambahkan'
				);
				return Response::json($response);
			}
			// }else{
			// 	$response = array(
			// 		'status' => false,
			// 		'message' => 'Checklog Anda tidak tersedia pada tanggal '.date('Y-m-d', strtotime($request->input('newDate')))
			// 	);
			// 	return Response::json($response);
			// }
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function fetchOnlineTransportationData(Request $request)
	{
		try {
			$employee_id = $request->get('employee_id');
			$attend_code = $request->get('attend_code');

			$data = GeneralTransportationData::where('employee_id',$employee_id)->where('attend_code',$attend_code)->first();

			if (count($data) > 0) {
				$response = array(
					'status' => true,
					'datas' => $data
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => true,
					'datas' => ""
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function confirmOnlineTransportationReport(Request $request){
		try{
			$transportation = GeneralTransportation::where('id', '=', $request->get('id'))->first();
			$transportation->remark = 1;
			$transportation->save();

			$response = array(
				'status' => true,
				'message' => 'Data berhasil dikonfirmasi'
			);
			return Response::json($response);
		}
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchSafetyShoesLog(Request $request){

		$data = GeneralShoesLog::leftJoin(db::raw("(SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS request_user"), 'general_shoes_logs.requested_by', '=', 'request_user.id')
		->leftJoin(db::raw("(SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS create_user"), 'general_shoes_logs.created_by', '=', 'create_user.id');

		if(strlen($request->get('datefrom')) > 0 ){
			$data = $data->where(db::raw('date(general_shoes_logs.created_at)'), '>=', $request->get('datefrom'));
		}

		if(strlen($request->get('dateto')) > 0 ){
			$data = $data->where(db::raw('date(general_shoes_logs.created_at)'), '<=', $request->get('dateto'));
		}

		if($request->get('department') != null){
			$data = $data->whereIn('general_shoes_logs.department', $request->get('department'));
		}

		if($request->get('section') != null){
			$data = $data->whereIn('general_shoes_logs.section', $request->get('section'));
		}

		if($request->get('group') != null){
			$data = $data->whereIn('general_shoes_logs.group', $request->get('group'));
		}

		if($request->get('status') != null){
			$data = $data->whereIn('general_shoes_logs.status', $request->get('status'));
		}

		if($request->get('requested_by') != null){
			$data = $data->whereIn('general_shoes_logs.requested_by', $request->get('requested_by'));
		}

		if($request->get('created_by') != null){
			$data = $data->whereIn('general_shoes_logs.created_by', $request->get('created_by'));
		}

		$data = $data->orderBy('general_shoes_logs.created_at', 'desc')
		->select(
			'general_shoes_logs.merk',
			'general_shoes_logs.status',
			'general_shoes_logs.size',
			'general_shoes_logs.gender',
			'general_shoes_logs.employee_id',
			db::raw("concat(SPLIT_STRING(general_shoes_logs.name, ' ', 1), ' ', SPLIT_STRING(general_shoes_logs.name, ' ', 2)) as `name`"),
			'general_shoes_logs.department',
			'general_shoes_logs.section',
			'general_shoes_logs.group',
			'general_shoes_logs.sub_group',
			'general_shoes_logs.quantity',
			db::raw('request_user.name AS requester'),
			db::raw('create_user.name AS creator'),
			'general_shoes_logs.created_at'
		)
		->get();

		return DataTables::of($data)->make(true);
	}

	public function fetchRequestSafetyShoes(){

		$request = db::select("SELECT request.request_id, request.created_at, u.`name`, SUM(request.l) AS l, SUM(request.p) AS p FROM
			(SELECT r.request_id, date(r.created_at) AS created_at, r.created_by, IF(e.gender = 'L',1,0) AS l, IF(e.gender = 'P',1,0) AS p FROM general_shoes_requests r
			LEFT JOIN employee_syncs e ON e.employee_id = r.employee_id) request
			LEFT JOIN users u ON u.id = request.created_by
			GROUP BY request.request_id, request.created_at, u.`name`
			ORDER BY request.request_id");

		$response = array(
			'status' => true,
			'request' => $request
		);
		return Response::json($response);


	}

	public function fetchDetailSafetyShoes(Request $request){
		$request_id = $request->get('request_id');

		$data = GeneralShoesRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_shoes_requests.employee_id')
		->leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
		->where('general_shoes_requests.request_id', $request_id)
		->select(
			'employee_syncs.employee_id',
			'employee_syncs.name',
			'employee_syncs.gender',
			'employee_syncs.department',
			'employee_syncs.section',
			'employee_syncs.group',
			'general_shoes_requests.gender',
			'general_shoes_requests.merk',
			'general_shoes_requests.size',
			db::raw('users.name AS requester')
		)
		->get();

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
	}

	public function fetchSafetyShoes(){
		$data = GeneralShoesStock::where('quantity', '>', 0)->get();

		$resume = GeneralShoesStock::where('quantity', '>', 0)
		->select('size', 'gender', db::raw('sum(quantity) AS quantity'))
		->groupBy('size', 'gender')
		->get();

		$response = array(
			'status' => true,
			'data' => $data,
			'resume' => $resume
		);
		return Response::json($response);
	}

	public function fetchSafetyShoesDetail(Request $request){
		$data = GeneralShoesStock::where('gender', $request->get('gender'))
		->where('size', $request->get('size'))
		->where('quantity', '>', 0)
		->get();

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);

	}

	public function fetchOnlineTransportationResumeReport(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$transportations = db::select("SELECT
			A.employee_id,
			employee_syncs.name,
			A.zona,
			A.att_in,
			A.att_out,
			A.remark_in,
			A.remark_out,
			A.id_in,
			A.id_out,
			A.grade,
			A.check_date,
			A.attend_code,
			A.attend_count,
			A.vehicle,
			A.highway_amount_in,
			A.highway_amount_out,
			A.distance_in,
			A.distance_out,
			A.origin_in,
			A.origin_out,
			A.destination_in,
			A.destination_out,
			A.highway_amount_total,
			A.distance_total,
			A.remark 
			FROM
			(
			SELECT
			attendance.zona,
			max( attendance.att_in ) AS att_in,
			max( attendance.att_out ) AS att_out,
			max( attendance.remark_in ) AS remark_in,
			max( attendance.remark_out ) AS remark_out,
			max( attendance.id_in ) AS id_in,
			max( attendance.id_out ) AS id_out,
			attendance.employee_id,
			attendance.grade,
			attendance.check_date,
			attendance.attend_code,
			max( attendance.attend_count ) AS attend_count,
			max( attendance.vehicle ) AS vehicle,
			max( attendance.highway_in ) AS highway_amount_in,
			max( attendance.highway_out ) AS highway_amount_out,
			max( attendance.distance_in ) AS distance_in,
			max( attendance.distance_out ) AS distance_out,
			max( attendance.origin_in ) AS origin_in,
			max( attendance.origin_out ) AS origin_out,
			max( attendance.destination_in ) AS destination_in,
			max( attendance.destination_out) AS destination_out,
			max( attendance.highway_in ) + max( attendance.highway_out ) AS highway_amount_total,
			max( attendance.distance_in ) + max( attendance.distance_out ) AS distance_total,
			min( attendance.remark ) AS remark 
			FROM
			(
			SELECT
			zona,
			highway_attachment AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			'hadir' AS attend_code,
			1 AS attend_count,
			vehicle,
			highway_amount AS highway_in,
			0 AS highway_out,
			distance AS distance_in,
			0 AS distance_out,
			origin AS origin_in,
			0 AS origin_out,
			destination AS destination_in,
			0 AS destination_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
			AND remark = 1 
			AND attend_code = 'in' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			highway_attachment AS att_out,
			0 AS remark_in,
			remark AS remark_out,
			0 AS id_in,
			id AS id_out,
			employee_id,
			grade,
			check_date,
			'hadir' AS attend_code,
			1 AS attend_count,
			vehicle,
			0 AS highway_in,
			highway_amount AS highway_out,
			0 AS distance_in,
			distance AS distance_out,
			0 AS origin_in,
			origin AS origin_out,
			0 AS destination_in,
			destination AS destination_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
			AND remark = 1 
			AND attend_code = 'out' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			attend_code,
			0 AS attend_count,
			0 AS vehicle,
			0 AS highway_in,
			0 AS highway_out,
			0 AS distance_in,
			0 AS distance_out,
			0 AS origin_in,
			0 AS origin_out,
			0 AS destination_in,
			0 AS destination_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
			AND remark = 1 
			AND attend_code <> 'out' 
			AND attend_code <> 'in' 
			) AS attendance 
			GROUP BY
			zona,
			employee_id,
			grade,
			check_date,
			attend_code 
			) AS A
			LEFT JOIN employee_syncs ON A.employee_id = employee_syncs.employee_id 
			ORDER BY
			A.employee_id ASC,
			A.check_date ASC");

		$datas = array();

		foreach ($transportations as $transportation) {
			$fuel = 0;
			$divider = 0;
			$multiplier = 0;
			$grade = "";

			if($transportation->grade != null){
				$grade = $transportation->grade;
			}

			if(substr($grade, 0, 1) == 'M'){
				$divider = 5;
				$multiplier = 7650;
			}
			else if(substr($grade, 0, 1) == 'L'){
				$divider = 7;
				$multiplier = 7650;
			}
			else{
				$divider = $transportation->distance_total;
				$multiplier = 0;
			}

			if($transportation->vehicle == 'car'){
				if($transportation->distance_total <= 150){
					$fuel = ($transportation->distance_total/$divider)*$multiplier;
				}
				else{
					$fuel = (150/$divider)*$multiplier;						
				}
			}

			if($transportation->vehicle == 'other'){
				if($transportation->zona == '1'){
					$fuel = 11000;
				}
				else if($transportation->zona == '2'){
					$fuel = 12400;
				}
				else{
					$fuel = 17000;
				}
			}

			if(substr($grade, 0, 1) == 'M' || substr($grade, 0, 1) == 'L'){
				$total_amount = $fuel+$transportation->highway_amount_total;
			}else{
				$total_amount = 0;
			}

			array_push($datas, 
				[
					"zona" => $transportation->zona,
					"att_in" => asset('files/general_transportation/'.$transportation->att_in),
					"att_out" => asset('files/general_transportation/'.$transportation->att_out),
					"remark_in" => $transportation->remark_in,
					"remark_out" => $transportation->remark_out,
					"id_in" => $transportation->id_in,
					"id_out" => $transportation->id_out,
					"employee_id" => $transportation->employee_id,
					"name" => $transportation->name,
					"grade" => $transportation->grade,
					"check_date" => $transportation->check_date,
					"attend_code" => $transportation->attend_code,
					"attend_count" => $transportation->attend_count,
					"vehicle" => $transportation->vehicle,
					"highway_amount_in" => $transportation->highway_amount_in,
					"highway_amount_out" => $transportation->highway_amount_out,
					"distance_in" => $transportation->distance_in,
					"distance_out" => $transportation->distance_out,
					"origin_in" => $transportation->origin_in,
					"destination_in" => $transportation->destination_in,
					"origin_out" => $transportation->origin_out,
					"destination_out" => $transportation->destination_out,
					"highway_amount_total" => $transportation->highway_amount_total,
					"distance_total" => $transportation->distance_total,
					"remark" => $transportation->remark,
					"fuel" => round($fuel,2),
					"total_amount" => $total_amount
				]);
		}

		$response = array(
			'status' => true,
			'transportations' => $datas,
		);
		return Response::json($response);

	}

	public function fetchOnlineTransportationReport(Request $request){
		$month = date('Y-m');
		if(strlen($request->get('month_from')) > 0){
			$month = date('Y-m', strtotime($request->get('month_from')));
		}

		$transportations = GeneralTransportation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_transportations.employee_id')
		->select('general_transportations.id', 'general_transportations.employee_id', 'employee_syncs.name', 'employee_syncs.grade_code', 'general_transportations.check_date','general_transportations.vehicle','general_transportations.origin','general_transportations.destination', 'general_transportations.attend_code',  'general_transportations.highway_amount', 'general_transportations.distance', 'general_transportations.highway_attachment')
		->where(db::raw('date_format(general_transportations.check_date, "%Y-%m")'), '=', $month)
		->where('general_transportations.remark', '=', 0)
		->get();

		$datas = array();

		foreach ($transportations as $transportation) {
			array_push($datas, 
				[
					"id" => $transportation->id,
					"employee_id" => $transportation->employee_id,
					"name" => $transportation->name,
					"grade_code" => $transportation->grade_code,
					"check_date" => $transportation->check_date,
					"attend_code" => $transportation->attend_code,
					"vehicle" => $transportation->vehicle,
					"origin" => $transportation->origin,
					"destination" => $transportation->destination,
					"highway_amount" => $transportation->highway_amount,
					"distance" => $transportation->distance,
					"highway_attachment" => asset('files/general_transportation/'.$transportation->highway_attachment)
				]);

		}

		$response = array(
			'status' => true,
			'transportations' => $datas,
			'period' => date('F Y', strtotime($month))
		);
		return Response::json($response);
	}

	public function fetchOnlineTransportation(Request $request){
		$date_from = date('Y-m-01');
		$date_to = date('Y-m-t');

		if(strlen($request->get('date_from')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('date_from')));
		}

		if(strlen($request->get('date_to')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('date_to')));
		}

		$transportations = db::select("
			SELECT
			calendar.week_date AS check_date,
			calendar.remark AS h,
			COALESCE ( attendance.zona, 0 ) AS zona,
			COALESCE ( attendance.att_in, 0 ) AS att_in,
			COALESCE ( attendance.att_out, 0 ) AS att_out,
			COALESCE ( attendance.remark_in, 0 ) AS remark_in,
			COALESCE ( attendance.remark_out, 0 ) AS remark_out,
			COALESCE ( attendance.id_in, 0 ) AS id_in,
			COALESCE ( attendance.id_out, 0 ) AS id_out,
			COALESCE ( attendance.employee_id, 0 ) AS employee_id,
			COALESCE ( attendance.grade, 0 ) AS grade,
			COALESCE ( attendance.attend_code, 0 ) AS attend_code,
			COALESCE ( attendance.vehicle, 0 ) AS vehicle,
			COALESCE ( attendance.origin_in, 0 ) AS origin_in,
			COALESCE ( attendance.destination_in, 0 ) AS destination_in,
			COALESCE ( attendance.origin_out, 0 ) AS origin_out,
			COALESCE ( attendance.destination_out, 0 ) AS destination_out,
			COALESCE ( attendance.highway_amount_in, 0 ) AS highway_amount_in,
			COALESCE ( attendance.highway_amount_out, 0 ) AS highway_amount_out,
			COALESCE ( attendance.distance_in, 0 ) AS distance_in,
			COALESCE ( attendance.distance_out, 0 ) AS distance_out,
			COALESCE ( attendance.highway_amount_total, 0 ) AS highway_amount_total,
			COALESCE ( attendance.distance_total, 0 ) AS distance_total,
			attendance.remark AS remark 
			FROM
			( SELECT * FROM weekly_calendars WHERE week_date >= '".$date_from."' AND week_date <= '".$date_to."' ) AS calendar
			LEFT JOIN (
			SELECT
			zona,
			max( att_in ) AS att_in,
			max( att_out ) AS att_out,
			max( remark_in ) AS remark_in,
			max( remark_out ) AS remark_out,
			max( id_in ) AS id_in,
			max( id_out ) AS id_out,
			employee_id,
			grade,
			check_date,
			attend_code,
			max( vehicle ) AS vehicle,
			max( origin_in ) AS origin_in,
			max( destination_in ) AS destination_in,
			max( origin_out ) AS origin_out,
			max( destination_out ) AS destination_out,
			max( highway_in ) AS highway_amount_in,
			max( highway_out ) AS highway_amount_out,
			max( distance_in ) AS distance_in,
			max( distance_out ) AS distance_out,
			max( highway_in ) + max( highway_out ) AS highway_amount_total,
			max( distance_in ) + max( distance_out ) AS distance_total,
			min( remark ) AS remark 
			FROM
			(
			SELECT
			zona,
			highway_attachment AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			'hadir' AS attend_code,
			vehicle,
			origin as origin_in,
			destination as destination_in,
			0 AS origin_out,
			0 AS destination_out,
			highway_amount AS highway_in,
			0 AS highway_out,
			distance AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code = 'in' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			highway_attachment AS att_out,
			0 AS remark_in,
			remark AS remark_out,
			0 AS id_in,
			id AS id_out,
			employee_id,
			grade,
			check_date,
			'hadir' AS attend_code,
			vehicle,
			0 AS origin_in,
			0 AS destination_in,
			origin as origin_out,
			destination as destination_out,
			0 AS highway_in,
			highway_amount AS highway_out,
			0 AS distance_in,
			distance AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code = 'out' UNION ALL
			SELECT
			zona,
			0 AS att_in,
			0 AS att_out,
			remark AS remark_in,
			0 AS remark_out,
			id AS id_in,
			0 AS id_out,
			employee_id,
			grade,
			check_date,
			attend_code,
			0 AS vehicle,
			0 AS origin_in,
			0 AS destination_in,
			0 AS origin_out,
			0 AS destination_out,
			0 AS highway_in,
			0 AS highway_out,
			0 AS distance_in,
			0 AS distance_out,
			remark 
			FROM
			`general_transportations` 
			WHERE
			employee_id = '".Auth::user()->username."' 
			AND check_date >= '".$date_from."' 
			AND check_date <= '".$date_to."' 
			AND attend_code <> 'out' 
			AND attend_code <> 'in' 
			) AS A 
			GROUP BY
			zona,
			employee_id,
			grade,
			check_date,
			attend_code 
			) AS attendance ON calendar.week_date = attendance.check_date 
			ORDER BY
			calendar.week_date ASC");

$datas = array();

foreach ($transportations as $transportation) {
	array_push($datas, 
		[
			"h" => $transportation->h,
			"zona" => $transportation->zona,
			"att_in" => asset('files/general_transportation/'.$transportation->att_in),
			"att_out" => asset('files/general_transportation/'.$transportation->att_out),
			"remark_in" => $transportation->remark_in,
			"remark_out" => $transportation->remark_out,
			"id_in" => $transportation->id_in,
			"id_out" => $transportation->id_out,
			"employee_id" => $transportation->employee_id,
			"grade" => $transportation->grade,
			"check_date" => $transportation->check_date,
			"attend_code" => $transportation->attend_code,
			"origin_in" => $transportation->origin_in,
			"destination_in" => $transportation->destination_in,
			"origin_out" => $transportation->origin_out,
			"destination_out" => $transportation->destination_out,
			"vehicle" => $transportation->vehicle,
			"highway_amount_in" => $transportation->highway_amount_in,
			"highway_amount_out" => $transportation->highway_amount_out,
			"distance_in" => $transportation->distance_in,
			"distance_out" => $transportation->distance_out,
			"highway_amount_total" => $transportation->highway_amount_total,
			"distance_total" => $transportation->distance_total,
			"remark" => $transportation->remark
		]);

}

$response = array(
	'status' => true,
	'transportations' => $datas
);
return Response::json($response);
}

public function editOnlineTransportation(Request $request)
{
	try {

		$datas = GeneralTransportation::where('id',$request->get('id'))->first();
		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}
}

public function updateOnlineTransportation(Request $request)
{
	try {

		$datas = GeneralTransportation::where('id',$request->get('id_transport'))->first();
		$datas->check_date = $request->get('editDate');
		$datas->distance = $request->get('editDistance');
		$datas->origin = $request->get('editOrigin');
		$datas->destination = $request->get('editDestination');
		$datas->highway_amount = $request->get('editHighwayAmount');
		$datas->save();

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}
}

public function indexOmiVisitor(){
	$title = 'Koyami Visitor';
	$title_jp = '売店来客';

	return view('general.omi_visitor', array(
		'title' => $title,
		'title_jp' => $title_jp
	))->with('head', 'Koyami Visitor');
}

public function fetchOmiVisitor(){
	$visitors = db::connection('rfid')->table('omi_lists')->get();

	$response = array(
		'status' => true,
		'visitors' => $visitors
	);
	return Response::json($response);
}

public function indexGeneralPointingCall($id){
	if($id == 'japanese'){
		$title = 'Japanese Pointing Calls';
		$title_jp = '駐在員指差し呼称';

		return view('general.pointing_call.japanese', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'default_language' => 'jp',
			'location' => $id
		))->with('head', 'Pointing Calls');
	}
	if($id == 'national'){
		$title = 'National Staff Pointing Calls';
		$title_jp = 'ナショナル・スタッフ用の指差し呼称';

		return view('general.pointing_call.national', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'default_language' => 'id',
			'location' => $id
		))->with('head', 'Pointing Calls');
	}
}

public function editGeneralPointingCallPic(Request $request){

	$pics = db::table('pointing_calls')
	->where('location', '=', $request->get('location'))
	->where('point_title', '=', $request->get('point_title'))
	->update([
		'remark' => 0
	]);

	$pic = db::table('pointing_calls')
	->where('id', '=', $request->get('id'))
	->update([
		'remark' => 1
	]);

	$response = array(
		'status' => true
	);
	return Response::json($response);


}

public function fetchGeneralPointingCall(Request $request){
	if($request->get('location') == 'japanese') {
		$pics = db::table('pointing_calls')
		->where('location', '=', $request->get('location'))
		->where('point_title', '=', 'pic')
		->whereNull('deleted_at')
		->get();

		$pointing_calls = db::table('pointing_calls')
		->where('location', '=', $request->get('location'))
		->where('point_title', '<>', 'pic')
		->where('point_title', '<>', 'safety_riding')
		->where('remark', '1')
		->whereNull('deleted_at')
		->get();

		$response = array(
			'status' => true,
			'pointing_calls' => $pointing_calls,
			'pics' => $pics
		);
		return Response::json($response);
	}

	if($request->get('location') == 'national'){
		// $pics = db::table('pointing_calls')
		// ->where('location', '=', $request->get('location'))
		// ->where('point_title', '=', 'pic')
		// ->whereNull('deleted_at')
		// ->get();

		// $pointing_calls = db::table('pointing_calls')
		// ->where('location', '=', $request->get('location'))
		// ->where('point_title', '<>', 'pic')
		// ->where('point_title', '<>', 'safety_riding')
		// ->where('remark', '1')
		// ->whereNull('deleted_at')
		// ->get();

		$safety_ridings = "";
		$department = "";
		
		$employee = Employee::where('employees.employee_id', '=', Auth::user()->username)
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employees.employee_id')
		->select('employees.remark', 'employee_syncs.department')
		->first();

		if($employee){
			$safety_ridings = SafetyRiding::where('department', '=', $employee->department);
			
			if($employee->department == 'Educational Instrument (EI) Department'){
				$safety_ridings = $safety_ridings->orWhere('department', '=', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department');
			}

			$safety_ridings = $safety_ridings->where('period', '=', date('Y-m-01'))->where('location', '=', $employee->remark)
			->get();

			$department = $employee->department;
		}

		$pointing_calls = db::select("SELECT
			pc.point_title,
			pc.point_description,
			pc.point_no,
			pm.point_max 
			FROM
			pointing_calls AS pc
			LEFT JOIN (
			SELECT
			point_title,
			max( point_no ) AS point_max 
			FROM
			`pointing_calls`
			WHERE
			location = '".$request->get('location')."' 
			AND deleted_at IS NULL 
			AND point_title <> 'pic' 
			AND point_title <> 'safety_riding' 
			GROUP BY
			point_title 
			) AS pm ON pm.point_title = pc.point_title 
			WHERE
			pc.location = '".$request->get('location')."' 
			AND pc.remark = '1' 
			AND pc.deleted_at IS NULL 
			AND pc.point_title <> 'pic' 
			AND pc.point_title <> 'safety_riding'");

		$response = array(
			'status' => true,
			'pointing_calls' => $pointing_calls,
			'safety_ridings' => $safety_ridings,
			'department' => $department
			// 'pics' => $pics
		);
		return Response::json($response);
	}
}

public function indexGeneralAttendanceCheck(){
	$title = "Attendance Check";
	$title_jp = "";

	$purposes = GeneralAttendance::orderBy('purpose_code', 'asc')
	->select('purpose_code')
	->distinct()
	->get();

	return view('general.attendance_check', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'purposes' => $purposes
	))->with('head', 'GA Control')->with('page', 'Driver Control');
}

public function scanSafetyShoes(Request $request){

	$request_id = $request->get('request_id');

	$data = GeneralShoesRequest::leftJoin('users', 'users.id', '=', 'general_shoes_requests.created_by')
	->where('general_shoes_requests.request_id', $request_id)
	->select(
		'general_shoes_requests.gender',
		'general_shoes_requests.merk',
		'general_shoes_requests.size',
		'users.name',
		db::raw('COUNT(general_shoes_requests.id) AS qty')
	)
	->groupBy(
		'general_shoes_requests.gender',
		'general_shoes_requests.merk',
		'general_shoes_requests.size',
		'users.name'
	)
	->orderBy('general_shoes_requests.gender', 'ASC')
	->orderBy('general_shoes_requests.size', 'ASC')
	->get();


	$response = array(
		'status' => true,
		'data' => $data
	);
	return Response::json($response);
}

public function scanGeneralAttendanceCheck(Request $request){
	$employee = Employee::where('tag', '=', $request->get('tag'))->first();

	if($employee == ""){
		$response = array(
			'status' => false,
			'message' => 'Tag karyawan tidak terdaftar, hubungi bagian MIS.'
		);
		return Response::json($response);
	}

	$attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
	->where('due_date', '=', date('Y-m-d'))
	->first();

	if($attendance == "" || $attendance->due_date > date('Y-m-d')){
		$response = array(
			'status' => false,
			'message' => 'Karyawan tidak ada pada schedule.'
		);
		return Response::json($response);
	}

	if($attendance->attend_date != null){
		$response = array(
			'status' => false,
			'message' => 'Karyawan sudah menghadiri schedule.'
		);
		return Response::json($response);
	}

	try{
		$attendance->attend_date = date('Y-m-d H:i:s');
		$attendance->save();

		$response = array(
			'status' => true,
			'message' => $employee->name.' berhasil hadir.'
		);
		return Response::json($response);

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
		'message' => 'Berhasil'
	);
	return Response::json($response);

}

public function fetchGeneralAttendanceCheck(Request $request){

	try{
		$now = date('Y-m-d');

		$attendance_lists = db::select("SELECT
			ga.purpose_code,
			ga.employee_id,
			es.`name`,
			ga.attend_date 
			FROM
			general_attendances AS ga
			LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
			WHERE
			ga.due_date = '".$now."' 
			ORDER BY
			attend_date DESC");

		// $query = "SELECT DISTINCT
		// purpose_code,
		// employee_id,
		// due_date,
		// NAME,
		// departments.department_shortname AS department,
		// attend_date 
		// FROM
		// (
		// SELECT
		// general_attendances.purpose_code,
		// general_attendances.employee_id,
		// general_attendances.due_date,
		// employee_syncs.`name`,
		// employee_syncs.department,
		// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
		// FROM
		// general_attendances
		// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
		// WHERE
		// general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
		// SELECT
		// general_attendances.purpose_code,
		// general_attendances.employee_id,
		// general_attendances.due_date,
		// employee_syncs.`name`,
		// employee_syncs.department,
		// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
		// FROM
		// general_attendances
		// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
		// WHERE
		// DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
		// ) AS attendances 
		// LEFT JOIN
		// departments on departments.department_name = attendances.department
		// WHERE employee_id like 'PI%'
		// ORDER BY
		// attend_date DESC,
		// NAME ASC";

		// $attendance_lists = db::select($query); 

		$response = array(
			'status' => true,
			'attendance_lists' => $attendance_lists,
		);
		return Response::json($response);

	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}		
}

public function indexQueue($remark)
{
	if ($remark == 'mcu') {
		$title = "Medical Check Up Queue";
		$title_jp = "??";
	}

	return view('general.queue.index', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'remark' => $remark,
	));
}

public function fetchQueue($remark,Request $request)
{
	try {
		$auth = EmployeeSync::where('employee_syncs.employee_id',Auth::user()->username)->first();
		$now = date('Y-m-d');
		if ($remark == 'mcu') {
			$data_registrasi = DB::SELECT("SELECT
				meetings.id,
				SPLIT_STRING ( description, ' - ', 2 ) AS loc,
				meeting_details.*,
				employee_syncs.`name`,
				departments.department_shortname ,
				employee_syncs.section,
				shiftdaily_code
				FROM
				meetings
				JOIN meeting_details ON meeting_details.meeting_id = meetings.id 
				AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '".$now."'
				JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
				left JOIN departments ON departments.department_name = employee_syncs.department 
				left join sunfish_shift_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
				and shift_date = '".$now."'
				WHERE
				`subject` = 'Medical Check Up'
				and SPLIT_STRING ( description, ' - ', 2 ) = 'Registrasi'
				order By meeting_details.created_at asc
				");

			$data_clinic = DB::SELECT("SELECT
				meetings.id,
				SPLIT_STRING ( description, ' - ', 2 ) AS loc,
				meeting_details.*,
				employee_syncs.`name`,
				departments.department_shortname ,
				employee_syncs.section
				FROM
				meetings
				JOIN meeting_details ON meeting_details.meeting_id = meetings.id 
				AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '".$now."'
				JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
				left JOIN departments ON departments.department_name = employee_syncs.department  
				WHERE
				`subject` = 'Medical Check Up'
				and SPLIT_STRING ( description, ' - ', 2 ) = 'Darah'
				order By meeting_details.created_at asc
				");

			$data_thorax = DB::SELECT("SELECT
				meetings.id,
				SPLIT_STRING ( description, ' - ', 2 ) AS loc,
				meeting_details.*,
				employee_syncs.`name`,
				departments.department_shortname ,
				employee_syncs.section
				FROM
				meetings
				JOIN meeting_details ON meeting_details.meeting_id = meetings.id 
				AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '".$now."'
				JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
				left JOIN departments ON departments.department_name = employee_syncs.department  
				WHERE
				`subject` = 'Medical Check Up'
				and SPLIT_STRING ( description, ' - ', 2 ) = 'Thorax'
				order By meeting_details.created_at asc
				");

			$data_audiometri = DB::SELECT("SELECT
				meetings.id,
				SPLIT_STRING ( description, ' - ', 2 ) AS loc,
				meeting_details.*,
				employee_syncs.`name`,
				departments.department_shortname ,
				employee_syncs.section
				FROM
				meetings
				JOIN meeting_details ON meeting_details.meeting_id = meetings.id 
				AND meeting_details.STATUS = 0 and DATE(meeting_details.created_at) = '".$now."'
				JOIN employee_syncs ON employee_syncs.employee_id = meeting_details.employee_id
				left JOIN departments ON departments.department_name = employee_syncs.department  
				WHERE
				`subject` = 'Medical Check Up'
				and SPLIT_STRING ( description, ' - ', 2 ) = 'Audiometri'
				order By meeting_details.created_at asc
				");
		}

		if (count($auth) > 0) {
			$response = array(
				'status' => true,
				'data_thorax' => $data_thorax,
				'data_audiometri' => $data_audiometri,
				'data_clinic' => $data_clinic,
				'data_registrasi' => $data_registrasi,
				'section' => $auth->section,
				'now' => $now,
			);
		}else{
			$response = array(
				'status' => true,
				'data_thorax' => $data_thorax,
				'data_audiometri' => $data_audiometri,
				'data_clinic' => $data_clinic,
				'data_registrasi' => $data_registrasi,
				'section' => "",
				'now' => $now,
			);
		}
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

	//  -------------------------   OXYMETER ------------
public function indexOxymeterCheck()
{
	$title = "Oximeter Check";
	$title_jp = "オキシメーター検査";

		// $employees = EmployeeSync::orderBy('department', 'asc')->get();

	return view('general.oxymeter.index_check', array(
		'title' => $title,
		'title_jp' => $title_jp
	));
}

public function postOxymeterCheck(Request $request)
{
	try {
		$att_log = GeneralAttendanceLog::firstOrNew(array('employee_id' => $request->get('employee_id'), 'due_date' => date('Y-m-d'), 'purpose_code' => 'Oxymeter'));

		$att_log->attend_date = date('Y-m-d H:i:s');

		if ($request->get('ctg') == 'oxygen') {
			$att_log->remark = $request->get('value');
		} else {
			$att_log->remark2 = $request->get('value');
		}

		$att_log->created_by = Auth::user()->id;

		$att_log->save();

		$response = array(
			'status' => true,
			'message' => 'Success',
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

public function fetchOxymeterHistory(Request $request)
{
	// DB::connection()->enableQueryLog();
	$oxy_log = GeneralAttendanceLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
	// ->leftJoin('sunfish_shift_syncs', 'sunfish_shift_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
	->leftJoin('sunfish_shift_syncs', function ($join) {
		$join->on('sunfish_shift_syncs.employee_id', '=', 'general_attendance_logs.employee_id')
		->on('sunfish_shift_syncs.shift_date', '=', 'general_attendance_logs.due_date');
	})
	->where('purpose_code', '=', 'Oxymeter');

	if(strlen($request->get('username')) > 0 ){
		$dpt = EmployeeSync::where('employee_id', '=', $request->get('username'))->first();
		$oxy_log = $oxy_log->where('employee_syncs.department', '=', $dpt->department);
	}

	if(strlen($request->get('dt')) > 0 ){
		$oxy_log = $oxy_log->where('general_attendance_logs.due_date', '=', $request->get('dt'));
	}

	$oxy_log = $oxy_log->orderBy('updated_at', 'desc');

	if(strlen($request->get('limit')) > 0 ){
		$oxy_log = $oxy_log->limit($request->get('limit'));
	}

	$oxy_log = $oxy_log->select('general_attendance_logs.updated_at', 'general_attendance_logs.employee_id', 'employee_syncs.name', 'general_attendance_logs.remark', 'general_attendance_logs.remark2')->get();

	$response = array(
		'status' => true,
		'datas' => $oxy_log,
		// 'query' => DB::getQueryLog()
	);
	return Response::json($response);
}

public function indexOxymeterMonitoring()
{
	$title = "Oximeter Monitoring";
	$title_jp = "オキシメーターモニター";

	return view('general.oxymeter.index_monitoring', array(
		'title' => $title,
		'title_jp' => $title_jp
	));
}

public function fetchOxymeterMonitoring(Request $request)
{

	if ($request->get('dt')) {
		$dt = $request->get('dt');
	} else {
		$dt = date('Y-m-d');
	}

	$oxy_log = GeneralAttendanceLog::leftJoin('employees', 'employees.employee_id', '=', 'general_attendance_logs.employee_id')
	->where('purpose_code', '=', 'Oxymeter')
	->where('general_attendance_logs.due_date', '=', $dt)
	->select('general_attendance_logs.remark', db::raw('COUNT(general_attendance_logs.remark) as qty'))
	->groupBy('general_attendance_logs.remark')
	->get();

	$pulse_log = GeneralAttendanceLog::leftJoin('employees', 'employees.employee_id', '=', 'general_attendance_logs.employee_id')
	->where('purpose_code', '=', 'Oxymeter')
	->where('general_attendance_logs.due_date', '=', $dt)
	->select('general_attendance_logs.remark2', db::raw('COUNT(general_attendance_logs.remark2) as qty'))
	->groupBy('general_attendance_logs.remark2')
	->get();

	$shift_log = db::select("SELECT sunfish_shift_syncs.employee_id, employees.name, employees.remark, shiftdaily_code, attend_code, departments.department_shortname, section, `group`, oxymeter.remark as oxy, oxymeter.remark2 as pulse, oxymeter.attend_date as check_time from sunfish_shift_syncs
		left join employees on employees.employee_id = sunfish_shift_syncs.employee_id
		left join (select * from general_attendance_logs where purpose_code = 'Oxymeter' and due_date = '".$dt."') as oxymeter on oxymeter.employee_id = sunfish_shift_syncs.employee_id
		left join employee_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
		left join departments on employee_syncs.department = departments.department_name
		where shift_date = '".$dt."' and employees.remark <> 'Jps' and employees.employee_id NOT LIKE 'OS%'");
	
	$response = array(
		'status' => true,
		'oxy_datas' => $oxy_log,
		'pulse_datas' => $pulse_log,
		'shift' => $shift_log
	);
	return Response::json($response);
}

//  -----------  AIR VISUAL -----------
public function indexAirVisual()
{
	$title = "CO2 Monitor";
	$title_jp = "二酸化炭素モニター";

	return view('general.air_visual_map', array(
		'title' => $title,
		'title_jp' => $title_jp
	));
}

public function postAirVisual()
{
	$result = "";

	$arr_api = [
		'https://www.airvisual.com/api/v2/node/6051b1f079e60a367adc1a45',
		'https://www.airvisual.com/api/v2/node/606bb8c5efbeaf90b42b8e98',
		'https://www.airvisual.com/api/v2/node/606bbb0c97b9294b00b2b297',
		'https://www.airvisual.com/api/v2/node/606bbe7797b929136cb2b2a4',
		'https://www.airvisual.com/api/v2/node/6076926f61972e5d329d4207'
	];

	foreach ($arr_api as $api) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		curl_setopt($ch, CURLOPT_URL, $api);
		$result=curl_exec($ch);
		curl_close($ch);

		$arr = json_decode($result, true);
		$s = "";
		$true_date = "";

		for ($i=count($arr['historical']['instant'])-1; $i > 0; $i--) { 
			$s = str_replace('T', ' ', $arr['historical']['instant'][$i]['ts']);
			$times = substr(explode(' ', $s)[1], 0, -5);
			$dates = explode(' ', $s)[0];

			$ts = date_create($dates." ".$times);

			// $ts2 = date('Y-m-d H:i:s', strtotime($times) + 60*420);

			date_add($ts, date_interval_create_from_date_string('7 hours'));
			$true_date = date_format($ts, 'Y-m-d H:i:s');

			$air_log = GeneralAirVisualLog::firstOrNew(array('data_time' => $true_date));
			$air_log->get_at = date('Y-m-d H:i:00');
			$air_log->remark = $arr['historical']['instant'][$i]['ts'];
			$air_log->co = $arr['historical']['instant'][$i]['co'];
			$air_log->temperature = $arr['historical']['instant'][$i]['tp'];
			$air_log->humidity = $arr['historical']['instant'][$i]['hm'];
			$air_log->location = $arr['settings']['node_name'];
			$air_log->created_at = date('Y-m-d H:i:s');
			$air_log->updated_at = date('Y-m-d H:i:s');

			$air_log->save();
		}
	}

	$datas = GeneralAirVisualLog::whereRaw('DATE_FORMAT(data_time,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"')
	->select('location', 'data_time', 'co', 'temperature', 'humidity', db::raw('DATE_FORMAT(data_time, "%H:%i") as data_time2'))
	->orderBy('id', 'asc')
	->get();

	$last_data = db::select('SELECT id, location, co, temperature, humidity FROM general_air_visual_logs
		WHERE id IN (
		SELECT MAX(id)
		FROM general_air_visual_logs
		GROUP BY location
		) and DATE_FORMAT(created_at,"%Y-%m-%d %H:%i:%s") >= "'.date('Y-m-d 06:00:00').'"
		order by location asc');

	$response = array(
		'status' => true,
		'message' => '',
		'datas' => $datas,
		'time' => $true_date,
		'last_data' => $last_data
	);
	return Response::json($response);
}

public function excelOnlineTransportation(Request $request)
{
	$month = date('Y-m');
	if(strlen($request->get('monthfrom')) > 0){
		$month = date('Y-m', strtotime($request->get('monthfrom')));
	}

	$transportations = db::select("SELECT
		A.employee_id,
		employee_syncs.name,
		A.zona,
		A.att_in,
		A.att_out,
		A.remark_in,
		A.remark_out,
		A.id_in,
		A.id_out,
		A.grade,
		A.check_date,
		A.attend_code,
		A.attend_count,
		A.vehicle,
		A.highway_amount_in,
		A.highway_amount_out,
		A.distance_in,
		A.distance_out,
		A.origin_in,
		A.origin_out,
		A.destination_in,
		A.destination_out,
		A.highway_amount_total,
		A.distance_total,
		A.remark 
		FROM
		(
		SELECT
		attendance.zona,
		max( attendance.att_in ) AS att_in,
		max( attendance.att_out ) AS att_out,
		max( attendance.remark_in ) AS remark_in,
		max( attendance.remark_out ) AS remark_out,
		max( attendance.id_in ) AS id_in,
		max( attendance.id_out ) AS id_out,
		attendance.employee_id,
		attendance.grade,
		attendance.check_date,
		attendance.attend_code,
		max( attendance.attend_count ) AS attend_count,
		max( attendance.vehicle ) AS vehicle,
		max( attendance.highway_in ) AS highway_amount_in,
		max( attendance.highway_out ) AS highway_amount_out,
		max( attendance.distance_in ) AS distance_in,
		max( attendance.distance_out ) AS distance_out,
		max( attendance.origin_in ) AS origin_in,
		max( attendance.origin_out ) AS origin_out,
		max( attendance.destination_in ) AS destination_in,
		max( attendance.destination_out) AS destination_out,
		max( attendance.highway_in ) + max( attendance.highway_out ) AS highway_amount_total,
		max( attendance.distance_in ) + max( attendance.distance_out ) AS distance_total,
		min( attendance.remark ) AS remark 
		FROM
		(
		SELECT
		zona,
		highway_attachment AS att_in,
		0 AS att_out,
		remark AS remark_in,
		0 AS remark_out,
		id AS id_in,
		0 AS id_out,
		employee_id,
		grade,
		check_date,
		'hadir' AS attend_code,
		1 AS attend_count,
		vehicle,
		highway_amount AS highway_in,
		0 AS highway_out,
		distance AS distance_in,
		0 AS distance_out,
		origin AS origin_in,
		0 AS origin_out,
		destination AS destination_in,
		0 AS destination_out,
		remark 
		FROM
		`general_transportations` 
		WHERE
		DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
		AND remark = 1 
		AND attend_code = 'in' UNION ALL
		SELECT
		zona,
		0 AS att_in,
		highway_attachment AS att_out,
		0 AS remark_in,
		remark AS remark_out,
		0 AS id_in,
		id AS id_out,
		employee_id,
		grade,
		check_date,
		'hadir' AS attend_code,
		1 AS attend_count,
		vehicle,
		0 AS highway_in,
		highway_amount AS highway_out,
		0 AS distance_in,
		distance AS distance_out,
		0 AS origin_in,
		origin AS origin_out,
		0 AS destination_in,
		destination AS destination_out,
		remark 
		FROM
		`general_transportations` 
		WHERE
		DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
		AND remark = 1 
		AND attend_code = 'out' UNION ALL
		SELECT
		zona,
		0 AS att_in,
		0 AS att_out,
		remark AS remark_in,
		0 AS remark_out,
		id AS id_in,
		0 AS id_out,
		employee_id,
		grade,
		check_date,
		attend_code,
		0 AS attend_count,
		0 AS vehicle,
		0 AS highway_in,
		0 AS highway_out,
		0 AS distance_in,
		0 AS distance_out,
		0 AS origin_in,
		0 AS origin_out,
		0 AS destination_in,
		0 AS destination_out,
		remark 
		FROM
		`general_transportations` 
		WHERE
		DATE_FORMAT( check_date, '%Y-%m' ) = '".$month."' 
		AND remark = 1 
		AND attend_code <> 'out' 
		AND attend_code <> 'in' 
		) AS attendance 
		GROUP BY
		zona,
		employee_id,
		grade,
		check_date,
		attend_code 
		) AS A
		LEFT JOIN employee_syncs ON A.employee_id = employee_syncs.employee_id 
		ORDER BY
		A.employee_id ASC,
		A.check_date ASC");

	$datas = array();

	foreach ($transportations as $transportation) {
		$fuel = 0;
		$divider = 0;
		$multiplier = 0;
		$grade = "";

		if($transportation->grade != null){
			$grade = $transportation->grade;
		}

		if(substr($grade, 0, 1) == 'M'){
			$divider = 5;
			$multiplier = 7650;
		}
		else if(substr($grade, 0, 1) == 'L'){
			$divider = 7;
			$multiplier = 7650;
		}
		else{
			$divider = $transportation->distance_total;
			$multiplier = 0;
		}

		if($transportation->vehicle == 'car'){
			if($transportation->distance_total <= 150){
				$fuel = ($transportation->distance_total/$divider)*$multiplier;
			}
			else{
				$fuel = (150/$divider)*$multiplier;						
			}
		}

		if($transportation->vehicle == 'other'){
			if($transportation->zona == '1'){
				$fuel = 11000;
			}
			else if($transportation->zona == '2'){
				$fuel = 12400;
			}
			else{
				$fuel = 17000;
			}
		}

		if(substr($grade, 0, 1) == 'M' || substr($grade, 0, 1) == 'L'){
			$total_amount = $fuel+$transportation->highway_amount_total;
		}else{
			$total_amount = 0;
		}

		if (in_array($transportation->employee_id, $datas)) {

		}else{
			array_push($datas, 
				[
					"employee_id" => $transportation->employee_id,
					"name" => $transportation->name,
					"total_amount" => $total_amount
				]);
		}
	}

	$data = array(
		'datas' => $datas
	);

	ob_clean();
	Excel::create('Report Transportation', function($excel) use ($data){
		$excel->sheet('Payroll Component Upload', function($sheet) use ($data) {
			return $sheet->loadView('general.dropbox.excel_online_transportation', $data);
		});
	})->export('xlsx');

        // return view('general.dropbox.excel_online_transportation',$data);
}
}
