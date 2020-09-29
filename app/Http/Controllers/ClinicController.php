<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\CodeGenerator;
use App\EmployeeSync;
use App\ClinicPatient;
use App\ClinicPatientDetail;
use App\ClinicMedicine;
use App\ClinicMedicineLog;
use Response;
use Excel;
use DataTables;


class ClinicController extends Controller{

	public function __construct(){
		$this->middleware('auth');
		$this->diagnose = [
			'Abces',
			'Alergi',
			'Anemia',
			'Artritis',
			'Astma Bronchial',
			'Atralgia',
			'Bronkhitis',
			'Caries Dentis',
			'Cephalgia',
			'Chest Pain',
			'Colic Abdomen',
			'Combustio',
			'Commond Cold',
			'Conjungtivitis',
			'Contusio Musc',
			'Corpus Alienum',
			'Dermatitis Alergi',
			'Dermatitis Infecti',
			'Dermatomikosis',
			'Disfagia',
			'DKA (Dermatitis Kontak Alergi)',
			'Dysentri',
			'Dysmenorhae',
			'Dyspepsia',
			'Dyspneu',
			'Epistaxis',
			'Faringitis',
			'Flu',
			'Fluor Albus',
			'Furunkel',
			'Gastritis',
			'Gea',
			'Ginggivitis',
			'Gout',
			'Gravida',
			'Haemoroid',
			'Hematochizia',
			'Herpez Zoster',
			'Hiperemesis Gravidarum',
			'Hipertensi',
			'Hipertermi',
			'Hipotensi',
			'Hordeolum',
			'Hypertiroidisme',
			'Influenza',
			'Insect Bite',
			'Iritasi',
			'Isk',
			'Ispa',
			'Konstipasi',
			'Lbp',
			'Lethargi',
			'Leukore',
			'Limfadenopaty Submandibula',
			'Limphadenitis',
			'Menometroragi',
			'Metrorargia',
			'Migraen',
			'Morbili',
			'Myalgia',
			'Neuritis',
			'Obs.Tyfoid',
			'Observasi Febris',
			'Observasi Vomiting',
			'Oma',
			'Paronikia',
			'Parotitis',
			'Piodermi',
			'Pruritus',
			'Psikosomatik',
			'Ptirigium',
			'Pulpitis',
			'Rinitis',
			'Scabies',
			'Spasme Muscolorum',
			'Stomatitis',
			'Susp. Fam',
			'Suspec Abortus Iminen',
			'Suspec Apendixitis',
			'Suspec Thypoid',
			'Tension Headache',
			'Tinea Cruris',
			'Tinea Versicolor',
			'Tonsilitis',
			'Tonsilo Faringitis',
			'Trauma Kll',
			'Uri',
			'Urticaria',
			'Varicella',
			'Vertigo',
			'Vulnus Abratio',
			'Vulnus Infection',
			'Vulnus Laceratum',
		];
		$this->doctor = [
			'Taliffia Setya, dr',
		];
		$this->paramedic = [
			'Elis Kurniawati',
			'Ahmad Fanani',
			'Nanang Sugianto',
		];
		$this->purpose = [
			'Petugas Cek Suhu',
			'Pemeriksaan Kesehatan',
			'Konsultasi Kesehatan',
			'Istirahat Sakit',
			'Laktasi',			
			'Kecelakaan Kerja',
			'Medical Check Up',
			'Mengantar Karyawan Sakit',
			'Mengantar Medical Check Up'			
		];

	}

	public function indexMaskLog(){
		$title = "Surgical Mask Log";
		$title_jp = '';

		return view('clinic.mask_log', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');
	}

	public function indexClinicDisease(){
		$title = "Clinic Diagnostic Data";
		$title_jp = 'クリニック見立てデータ';

		return view('clinic.display.clinic_disease', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');
	}

	public function indexClinicMonitoring(){
		$title = 'Clinic Monitoring';
		$title_jp = 'クリニック監視';

		return view('clinic.display.clinic_visit_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');
	}

	public function indexClinicVisit(){
		$title = 'Clinic Visit';
		$title_jp = 'クリニック訪問';

		return view('clinic.display.clinic_visit', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');
	}

	public function indexMedicines(){
		$title = "Clinic Medicines Data";
		$title_jp = 'クリニック薬品データ';

		return view('clinic.medicines', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');	
	}

	public function indexDiagnose(){
		$title = 'Patient Diagnosis';
		$title_jp = '患者見立て';
		$medicines = ClinicMedicine::select('medicine_name')->get();

		return view('clinic.diagnose', array(
			'diagnoses' => $this->diagnose,
			'doctors' => $this->doctor,
			'paramedics' => $this->paramedic,
			'purposes' => $this->purpose,
			'medicines' => $medicines,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Diagnose')->with('head','Clinic');
	}

	public function indexVisitLog(){
		$title = 'Clinic Visit Logs';
		$title_jp = 'クリニック訪問記録';
		$employees = EmployeeSync::select('employee_id', 'name')->get();
		$medicines = ClinicMedicine::select('medicine_name')->get();
		$departments = db::select("SELECT DISTINCT
			department 
			FROM
			employee_syncs 
			WHERE
			department IS NOT NULL 
			ORDER BY
			department ASC");


		return view('clinic.visit_logs', array(
			'diagnoses' => $this->diagnose,
			'doctors' => $this->doctor,
			'paramedics' => $this->paramedic,
			'purposes' => $this->purpose,
			'employees' => $employees,
			'departments' => $departments,
			'medicines' => $medicines,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Visit Logs')->with('head','Clinic');
	}

	public function fetchMedicines(){
		$medicine = ClinicMedicine::get();

		return DataTables::of($medicine)
		->addColumn('button', function($medicine){
			return '<button style="padding: 3%;" class="btn btn-md btn-success" id="'.$medicine->id.'#'.$medicine->medicine_name.'#'.$medicine->quantity.'" onclick="addStock(this)">Edit Stock</button>';
		})
		->rawColumns([ 'button' => 'button'])
		->make(true);

	}

	public function fetchVisitEdit(Request $request){

		$patient = db::select("SELECT c.visited_at, c.employee_id, e.`name`, e.department, c.purpose, c.paramedic, c.doctor, GROUP_CONCAT(c.diagnose) as diagnose FROM clinic_patient_details c
			left join employee_syncs e on e.employee_id = c.employee_id
			where c.patient_list_id = '".$request->get('id')."'
			group by c.visited_at, c.employee_id, e.`name`, e.department, c.purpose, c.paramedic, c.doctor");

		$medicines = db::select("select * from clinic_medicine_logs
			where clinic_patient_detail = '".$request->get('id')."'");

		$response = array(
			'status' => true,
			'patient' => $patient,
			'medicines' => $medicines,
		);
		return Response::json($response);		
	}

	public function fetchVisitLogExcel(Request $request){
		$clinic_visit_logs = ClinicPatientDetail::leftJoin(db::raw('(select * from employee_syncs) as patient'), 'patient.employee_id', '=', 'clinic_patient_details.employee_id');
		if(strlen($request->get('visitFrom')) > 0 ){
			$visitFrom = date('Y-m-d', strtotime($request->get('visitFrom')));
			$clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '>=', $visitFrom);
		}
		if(strlen($request->get('visitTo')) > 0 ){
			$visitTo = date('Y-m-d', strtotime($request->get('visitTo')));
			$clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '<=', $visitTo);
		}
		if($request->get('employee_id') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('patient.employee_id', $request->get('employee_id'));
		}
		if($request->get('department') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('patient.department', $request->get('department'));
		}
		if($request->get('purpose') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.purpose', $request->get('purpose'));
		}
		if($request->get('paramedic') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.paramedic', $request->get('paramedic'));
		}
		if($request->get('diagnose') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.diagnose', $request->get('diagnose'));
		}
		$clinic_visit_logs = $clinic_visit_logs->groupBy('clinic_patient_details.visited_at',
			'clinic_patient_details.patient_list_id',
			'clinic_patient_details.employee_id',
			'patient.name',
			'patient.department',
			'clinic_patient_details.paramedic',
			'clinic_patient_details.purpose');
		$clinic_visit_logs = $clinic_visit_logs->orderBy('clinic_patient_details.visited_at', 'asc')
		->select(
			'clinic_patient_details.visited_at',
			'clinic_patient_details.patient_list_id',
			'clinic_patient_details.employee_id',
			db::raw('concat(SPLIT_STRING(patient.name, " ", 1)," ",SPLIT_STRING(patient.name, " ", 2)) as name'),
			'patient.department',
			'clinic_patient_details.paramedic',
			'clinic_patient_details.purpose',
			db::raw('group_concat(diagnose) as diagnose')
		)->get();

		return $request;

		$data = array(
			'clinic_visit_logs' => $clinic_visit_logs
		);
		ob_clean();
		
		Excel::create('Clinic Visit Logs', function($excel) use ($data){
			$excel->sheet('Visit Logs', function($sheet) use ($data) {
				return $sheet->loadView('clinic.visit_log_excel', $data);
			});
		})->export('xlsx');
	}

	public function fetchVisitLog(Request $request){
		$clinic_visit_logs = ClinicPatientDetail::leftJoin(db::raw('(select * from employee_syncs) as patient'), 'patient.employee_id', '=', 'clinic_patient_details.employee_id')
		->leftJoin('ympi_klinik.patient_logs as logs', 'logs.idx', '=', 'clinic_patient_details.patient_list_id');

		if(strlen($request->get('visitFrom')) > 0 ){
			$visitFrom = date('Y-m-d', strtotime($request->get('visitFrom')));
			$clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '>=', $visitFrom);
		}
		if(strlen($request->get('visitTo')) > 0 ){
			$visitTo = date('Y-m-d', strtotime($request->get('visitTo')));
			$clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '<=', $visitTo);
		}
		if($request->get('employee_id') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('patient.employee_id', $request->get('employee_id'));
		}
		if($request->get('department') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('patient.department', $request->get('department'));
		}
		if($request->get('purpose') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.purpose', $request->get('purpose'));
		}
		if($request->get('paramedic') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.paramedic', $request->get('paramedic'));
		}
		if($request->get('diagnose') != 0 ){
			$clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.diagnose', $request->get('diagnose'));
		}
		$clinic_visit_logs = $clinic_visit_logs->groupBy('clinic_patient_details.visited_at',
			'logs.in_time',
			'logs.out_time',
			'clinic_patient_details.patient_list_id',
			'clinic_patient_details.employee_id',
			'patient.name',
			'patient.department',
			'clinic_patient_details.paramedic',
			'clinic_patient_details.purpose');
		$clinic_visit_logs = $clinic_visit_logs->orderBy('clinic_patient_details.visited_at', 'desc')
		->select(
			'clinic_patient_details.visited_at',
			'logs.in_time',
			'logs.out_time',
			'clinic_patient_details.patient_list_id',
			'clinic_patient_details.employee_id',
			'patient.name',
			'patient.department',
			'clinic_patient_details.paramedic',
			'clinic_patient_details.purpose',
			db::raw('group_concat(diagnose) as diagnose')
		)->get();

		
		$response = array(
			'status' => true,
			'logs' => $clinic_visit_logs,
		);
		return Response::json($response);
	}

	public function fetchDiagnose(Request $request){
		$id = '';
		if($request->get('id') != null){
			$id = 'and p.idx = '. $request->get('id');
		}

		$visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.name, e.hire_date, e.section  from patient_list p
			left join ympimis.employee_syncs e on e.employee_id = p.employee_id
			where p.`status` is null
			and p.note is null ".$id."
			order by p.in_time asc");

		$response = array(
			'status' => true,
			'visitor' => $visitor,
		);
		return Response::json($response);
	}

	public function fetchPatient(){
		$visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.name, e.hire_date, e.section, d.purpose, p.note as bed from patient_list p
			left join ympimis.employee_syncs e on e.employee_id = p.employee_id
			left join ympimis.clinic_patient_details d on d.patient_list_id = p.idx
			order by p.in_time asc");

		$response = array(
			'status' => true,
			'visitor' => $visitor,
		);
		return Response::json($response);
	}

	public function fetchDailyClinicVisit(Request $request){

		$datefrom = date('Y-m-01');
		$dateto = date('Y-m-d');

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}

		$clinic_visit = db::select("SELECT DATE_FORMAT( date.week_date, '%d %b %Y' ) AS week_date, COALESCE ( log.sum, 0 ) AS visit FROM
			(SELECT week_date, DATE_FORMAT( week_date, '%a' ) AS `day` FROM weekly_calendars
			WHERE	DATE_FORMAT( week_date, '%Y-%m-%d' ) >= '".$datefrom."'
			AND DATE_FORMAT( week_date, '%Y-%m-%d' ) <= '".$dateto."'
			AND remark <> 'H' 
			ORDER BY week_date ASC) AS date
			LEFT JOIN
			(SELECT log.tanggal AS tanggal, count( log.patient_list_id ) AS sum FROM
			(SELECT DISTINCT date( visited_at ) as tanggal, patient_list_id FROM clinic_patient_details
			WHERE DATE_FORMAT( visited_at, '%Y-%m-%d' ) >= '".$datefrom."'
			AND DATE_FORMAT( visited_at, '%Y-%m-%d' ) <= '".$dateto."'
			AND purpose IN ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja')) AS log
			GROUP BY tanggal) AS log
			ON date.week_date = log.tanggal");

		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
			'datefrom' => date_format(date_create($datefrom), "d M Y"),
			'dateto' => date_format(date_create($dateto), "d M Y")
		);
		return Response::json($response);
	}

	public function fetchClinicVisit(Request $request){
		$datefrom = date('Y-m-01');
		$dateto = date('Y-m-d');

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}

		$clinic_visit = db::select("select e.department, count(visit.employee_id) as qty from
			(select distinct c.employee_id, c.patient_list_id from clinic_patient_details c
			where DATE_FORMAT(c.created_at,'%Y-%m-%d') >= '".$datefrom."'
			and DATE_FORMAT(c.created_at,'%Y-%m-%d') <= '".$dateto."'
			and c.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja')) visit
			left join employee_syncs e on visit.employee_id = e.employee_id
			where e.department is not null
			group by e.department
			order by qty desc");

		$clinic_visit_detail = db::select("select dept.department, dept.purpose, COALESCE(qty.qty,0) as qty from
			(select dept.department, purpose.purpose from
			(select distinct department from employee_syncs
			where department is not null
			order by department asc) as dept
			cross join
			(SELECT 'Pemeriksaan Kesehatan' AS purpose
			UNION ALL
			SELECT 'Konsultasi Kesehatan' AS purpose
			UNION ALL
			SELECT 'Istirahat Sakit' AS purpose
			UNION ALL
			SELECT 'Kecelakaan Kerja' AS purpose) as purpose) as dept
			left join
			(SELECT e.department, visit.purpose, count( visit.employee_id ) AS qty FROM
			(SELECT DISTINCT c.employee_id, c.patient_list_id, c.purpose FROM clinic_patient_details c 
			WHERE	DATE_FORMAT( c.created_at, '%Y-%m-%d' ) >= '".$datefrom."'
			AND	DATE_FORMAT( c.created_at, '%Y-%m-%d' ) <= '".$dateto."'
			AND c.purpose IN ( 'Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja' ) 
			) visit
			LEFT JOIN employee_syncs e ON visit.employee_id = e.employee_id 
			WHERE e.department IS NOT NULL 
			GROUP BY e.department, visit.purpose) as qty
			on dept.department = qty.department and dept.purpose = qty. purpose");

		$tot_emp = db::select("select department ,count(employee_id) as emp from employee_syncs where end_date is null and department is not null GROUP BY department");

		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
			'clinic_visit_detail' => $clinic_visit_detail,
			'employees' => $tot_emp,
			'datefrom' => date_format(date_create($datefrom), "d M Y"),
			'dateto' => date_format(date_create($dateto), "d M Y")
		);
		return Response::json($response);
	}

	public function fetchClinicVisitDetail(Request $request){
		$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		$dateto = date('Y-m-d', strtotime($request->get('dateto')));

		$detail =  db::select("select distinct d.patient_list_id, d.employee_id, e.`name`, d.paramedic, d.visited_at, d.purpose  from clinic_patient_details d
			left join ympimis.employee_syncs e on e.employee_id = d.employee_id
			where DATE_FORMAT(d.visited_at,'%Y-%m-%d') >= '".$datefrom."'
			and DATE_FORMAT(d.visited_at,'%Y-%m-%d') <= '".$dateto."'
			and d.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja')
			and e.department like '%".$request->get('department')."%'");

		$response = array(
			'status' => true,
			'detail' => $detail,
		);
		return Response::json($response);		
	}

	public function fetchClinicMasker(Request $request){
		$datefrom = date('Y-m-01');
		$dateto = date('Y-m-d');

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}

		$masker = db::select("SELECT medicine.department, SUM(medicine.quantity) AS quantity FROM
			(SELECT DISTINCT m.id, e.department, m.quantity FROM clinic_medicine_logs m
			LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
			LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id 
			WHERE m.medicine_name = 'Surgical Masker'
			AND m.`status` = 'out' 
			AND DATE_FORMAT( m.created_at, '%Y-%m-%d' ) >= '".$datefrom."' 
			AND DATE_FORMAT( m.created_at, '%Y-%m-%d' ) <= '".$dateto."') medicine
			GROUP BY medicine.department 
			ORDER BY quantity DESC");

		$response = array(
			'status' => true,
			'masker' => $masker,
			'datefrom' => date_format(date_create($datefrom), "d M Y"),
			'dateto' => date_format(date_create($dateto), "d M Y")
		);
		return Response::json($response);
	}

	public function fetchMaskLog(Request $request){
		$datefrom = date('Y-m-d', strtotime($request->get('visitFrom')));
		$dateto = date('Y-m-d', strtotime($request->get('visitTo')));

		$logs =  db::select("SELECT DISTINCT m.id, p.visited_at, p.employee_id, e.`name`, p.paramedic, p.purpose, m.quantity FROM clinic_medicine_logs m
			LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
			LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id 
			WHERE m.medicine_name = 'Surgical Masker'
			AND m.`status` = 'out' 
			AND DATE_FORMAT(m.created_at, '%Y-%m-%d') >= '".$datefrom."'
			AND DATE_FORMAT(m.created_at, '%Y-%m-%d') <= '".$dateto."'
			ORDER BY p.visited_at ASC");

		$response = array(
			'status' => true,
			'logs' => $logs,
		);
		return Response::json($response);
	}

	public function fetchClinicMaskerDetail(Request $request){
		$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		$dateto = date('Y-m-d', strtotime($request->get('dateto')));

		$detail =  db::select("SELECT DISTINCT m.id, p.visited_at, p.employee_id, e.`name`, p.paramedic, p.purpose, m.quantity FROM clinic_medicine_logs m
			LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
			LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id 
			WHERE m.medicine_name = 'Surgical Masker'
			AND m.`status` = 'out' 
			AND DATE_FORMAT(m.created_at, '%Y-%m-%d') >= '".$datefrom."'
			AND DATE_FORMAT(m.created_at, '%Y-%m-%d') <= '".$dateto."'
			AND e.department like '%".$request->get('department')."%'
			ORDER BY p.visited_at ASC");

		$response = array(
			'status' => true,
			'detail' => $detail,
		);
		return Response::json($response);
	}


	public function fetchDisease(Request $request){
		$date_log = "";
		$month = "";

		if(strlen($request->get('month')) > 0){
			$date_log = "where DATE_FORMAT(visited_at,'%Y-%m') = '".$request->get('month')."'";
			$month = $request->get('month');
		}else{
			$date_log = "WHERE DATE_FORMAT(visited_at,'%Y-%m') = '".date('Y-m')."'";
			$month = date('Y-m');
		}

		$disease = db::select("select diagnose, count(employee_id) qty from clinic_patient_details ".$date_log."
			and diagnose is not null
			and diagnose <> ''
			group by diagnose
			order by qty desc");

		$response = array(
			'status' => true,
			'disease' => $disease,
			'month' => $month,
		);
		return Response::json($response);
	}

	public function fetchDiseaseDetail(Request $request){

		$detail = db::select("select p.diagnose, p.employee_id, e.name, p.paramedic, p.visited_at from clinic_patient_details p
			left join employee_syncs e on e.employee_id = p.employee_id
			where DATE_FORMAT(p.visited_at,'%Y-%m') = '".$request->get('month')."'
			and p.diagnose like '%".$request->get('disease')."%'
			order by p.visited_at asc");

		$response = array(
			'status' => true,
			'detail' => $detail,
		);
		return Response::json($response);
	}

	public function deleteVisitor(Request $request){
		$id = $request->get('id');

		try{
			$patient = db::connection('clinic')->table('patient_list')
			->where('idx', '=', $id)
			->update([
				'note' => 'delete',
			]);

			$response = array(
				'status' => true,
				'message' => 'patient was successfully deleted',
			);
			return Response::json($response);

		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}	
	}

	public function editDiagnose(Request $request){
		$idx = $request->get('id');
		$employee_id = $request->get('employee_id');
		$purpose = $request->get('purpose');
		$paramedic = $request->get('paramedic');
		$doctor = $request->get('doctor');
		$visited_at = $request->get('visited_at');

		try{

			//Input Patient Diagnose
			$delete = ClinicPatientDetail::where('patient_list_id', $idx)->forceDelete();
			
			if($request->get('diagnose') != null) {
				$diagnoses = $request->get('diagnose');
				for($x = 0; $x < count($diagnoses); $x++) {
					$diagnose = 
					$clinic_patient_detail = new ClinicPatientDetail([
						'employee_id' => $employee_id,
						'patient_list_id' => $idx,
						'purpose' => $purpose,
						'diagnose' => $diagnoses[$x],
						'paramedic' => $paramedic,
						'doctor' => $doctor,
						'visited_at' => $visited_at,
					]);
					$clinic_patient_detail->save();	
				}
			}else{
				$clinic_patient_detail = new ClinicPatientDetail([
					'employee_id' => $employee_id,
					'patient_list_id' => $idx,
					'purpose' => $purpose,
					'diagnose' => $diagnose,
					'paramedic' => $paramedic,
					'doctor' => $doctor,
					'visited_at' => $visited_at,
				]);
				$clinic_patient_detail->save();
			}

			//Input Medicine
			$delete = ClinicMedicineLog::where('clinic_patient_detail', $idx)->forceDelete();
			
			if($request->get('medicine') != null) {
				$medicines = $request->get('medicine');
				$clinic_medicine_log = [];
				for($x = 0; $x < count($medicines); $x++) {
					$clinic_medicine_log[$x] = new ClinicMedicineLog([
						'medicine_name' => $medicines[$x]['medicine_name'],
						'status' => 'out',
						'clinic_patient_detail' => $idx,
						'quantity' => $medicines[$x]['quantity'],
					]);
					
					DB::transaction(function() use ($clinic_medicine_log, $x){
						$clinic_medicine_log[$x]->save();
					});
				}
			}			

			$response = array(
				'status' => true,
				'message' => 'Patient Data`s successfully saved'
			);
			return Response::json($response);

		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

	}

	public function inputDiagnose(Request $request){
		$idx = $request->get('id');
		$employee_id = $request->get('nik');
		$purpose = $request->get('purpose');
		$bed = $request->get('bed');

		$diagnose = null;

		$paramedic = $request->get('paramedic');
		$doctor = $request->get('doctor');
		$family = $request->get('family');
		$family_name = $request->get('family_name');
		$visited_at = $request->get('date');

		$masker = $request->get('masker');
		$glove = $request->get('glove');

		try{
			//Input Patient Diagnose
			
			if($request->get('diagnose') != null) {
				$diagnoses = $request->get('diagnose');
				for($x = 0; $x < count($diagnoses); $x++) {
					$diagnose = 
					$clinic_patient_detail = new ClinicPatientDetail([
						'employee_id' => $employee_id,
						'patient_list_id' => $idx,
						'purpose' => $purpose,
						'diagnose' => $diagnoses[$x],
						'paramedic' => Auth::user()->name,
						'doctor' => $doctor,
						'family' => $family,
						'family_name' => $family_name,
						'visited_at' => $visited_at,
					]);
					$clinic_patient_detail->save();
					
				}
			}else{
				$clinic_patient_detail = new ClinicPatientDetail([
					'employee_id' => $employee_id,
					'patient_list_id' => $idx,
					'purpose' => $purpose,
					'diagnose' => $diagnose,
					'paramedic' => Auth::user()->name,
					'doctor' => $doctor,
					'family' => $family,
					'family_name' => $family_name,
					'visited_at' => $visited_at,
				]);
				$clinic_patient_detail->save();


				if($purpose == 'Petugas Cek Suhu'){
					if($masker > 0){
						$clinic_medicine = ClinicMedicine::where('medicine_name', 'Surgical Masker')->first();
						$clinic_medicine->quantity = $clinic_medicine->quantity - $masker;
						$clinic_medicine->save();

						$medicine_log = new ClinicMedicineLog([
							'medicine_name' => 'Surgical Masker',
							'status' => 'out',
							'clinic_patient_detail' => $idx,
							'quantity' => $masker,
						]);
						$medicine_log->save();
					}

					if($glove > 0){
						$clinic_medicine = ClinicMedicine::where('medicine_name', 'Latex Glove')->first();
						$clinic_medicine->quantity = $clinic_medicine->quantity - $glove;
						$clinic_medicine->save();

						$medicine_log = new ClinicMedicineLog([
							'medicine_name' => 'Latex Glove',
							'status' => 'out',
							'clinic_patient_detail' => $idx,
							'quantity' => $glove,
						]);
						$medicine_log->save();
					}
				}
			}


			//Input Medicine
			if($request->get('medicine') != null) {
				$medicines = $request->get('medicine');
				$clinic_medicine_log = [];
				for($x = 0; $x < count($medicines); $x++) {
					$clinic_medicine_log[$x] = new ClinicMedicineLog([
						'medicine_name' => $medicines[$x]['medicine_name'],
						'status' => 'out',
						'clinic_patient_detail' => $idx,
						'quantity' => $medicines[$x]['quantity'],
					]);


					$clinic_medicine[$x] = ClinicMedicine::where('medicine_name', $medicines[$x]['medicine_name'])->first();
					$clinic_medicine[$x]->quantity = $clinic_medicine[$x]->quantity - $medicines[$x]['quantity'];
					
					DB::transaction(function() use ($idx, $clinic_medicine_log, $clinic_medicine, $x, $bed){
						$clinic_medicine_log[$x]->save();
						$clinic_medicine[$x]->save();

						$clinic_patient = db::connection('clinic')->table('patient_list')
						->where('idx', '=', $idx)
						->update([
							'status' => 'Yes',
							'note' => $bed
						]);
					});
				}
			}else{
				$clinic_patient = db::connection('clinic')->table('patient_list')
				->where('idx', '=', $idx)
				->update([
					'status' => 'Yes',
					'note' => $bed
				]);
			}		

			$response = array(
				'status' => true,
				'message' => 'Patient Data`s successfully saved'
			);
			return Response::json($response);

		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function editMedicineStock(Request $request){
		$id = $request->get('id');
		$quantity = $request->get('quantity');
		
		try{
			$medicine = ClinicMedicine::where('id', $id)->first();
			$medicine->quantity = $medicine->quantity + $quantity;
			$medicine->save();


			$medicine_log = new ClinicMedicineLog([
				'medicine_name' => $medicine->medicine_name,
				'status' => 'in',
				'quantity' => $quantity,
			]);
			$medicine_log->save();	

			$response = array(
				'status' => true
			);
			return Response::json($response);

		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}


}
