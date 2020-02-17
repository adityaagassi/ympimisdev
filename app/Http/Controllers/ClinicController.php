<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\CodeGenerator;
use App\ClinicPatient;
use App\ClinicPatientDetail;
use App\ClinicMedicine;
use App\ClinicMedicineLog;
use Response;
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
			'Ichatus Solikha',
			'Nanang Sugianto',
		];
		$this->purpose = [
			'Pemeriksaan Kesehatan',
			'Konsultasi Kesehatan',
			'Laktasi',
			'Istirahat',
			'Medical Check Up',
			'Mengantar Karyawan Sakit',
			'Mengantar Medical Check Up',
		];

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

	public function indexDiagnose(){
		$title = 'Patient Diagnosis';
		$title_jp = '??';
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
			left join ympimis.clinic_patient_details d on d.id = p.`status`
			order by p.in_time asc");

		$response = array(
			'status' => true,
			'visitor' => $visitor,
		);
		return Response::json($response);
	}

	public function fetchDailyClinicVisit(Request $request){
		$date = "";
		$date_log = "";
		$month = "";

		if(strlen($request->get('month')) > 0){
			$date = "WHERE DATE_FORMAT(week_date,'%Y-%m') ='".$request->get('month')."'";
			$date_log = "where DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
			$month = $request->get('month');

		}else{
			$date = "WHERE DATE_FORMAT(week_date,'%Y-%m') ='".date('Y-m')."'";
			$date_log = "WHERE DATE_FORMAT(tanggal,'%Y-%m') = '".date('Y-m')."'";
			$month = date('Y-m');

		}

		$clinic_visit = db::connection("clinic")->select("select DATE_FORMAT(date.week_date,'%d %b %Y') as week_date, COALESCE(log.sum,0) as visit from
			(select week_date, DATE_FORMAT(week_date,'%a') as `day` from ympimis.weekly_calendars
			".$date." and remark <> 'H'
			order by week_date asc) as date
			left join 
			(select tanggal, count(employee_id) as sum from patient_logs
			".$date_log." group by tanggal) as log
			on date.week_date = log.tanggal");

		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
			'month' => $month,
		);
		return Response::json($response);
	}

	public function fetchClinicVisit(Request $request){
		$month = "";

		if(strlen($request->get('month')) > 0){
			$month = $request->get('month');
		}else{
			$month = date('Y-m');
		}

		$clinic_visit = db::select("select e.department, count(c.employee_id) as qty from clinic_patient_details c
			left join employee_syncs e on c.employee_id = e.employee_id
			where DATE_FORMAT(c.created_at,'%Y-%m') = '".$month."'
			and e.department is not null
			and c.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat')
			group by e.department
			order by qty desc");

		$department = db::select("select department, count(employee_id) as qty from employee_syncs
			where department is not null
			group by department");

		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
			'department' => $department,
			'month' => $month,
		);
		return Response::json($response);
	}

	public function fetchClinicVisitDetail(Request $request){

		// $detail =  db::connection("clinic")->select("select p.employee_id, e.name, d.paramedic, p.in_time, p.out_time, d.purpose from patient_logs p
		// 	left join ympimis.employee_syncs e on e.employee_id = p.employee_id
		// 	left join ympimis.clinic_patient_details d on d.id = p.status
		// 	where DATE_FORMAT(p.in_time,'%Y-%m') = '".$request->get('month')."'
		// 	and e.department like '%".$request->get('department')."%'");

		$detail =  db::select("select d.employee_id, e.`name`, d.paramedic, d.visited_at, d.purpose  from clinic_patient_details d
			left join ympimis.employee_syncs e on e.employee_id = d.employee_id
			where DATE_FORMAT(d.visited_at,'%Y-%m') = '".$request->get('month')."'
			and d.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat')
			and e.department like '%".$request->get('department')."%'");

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
					DB::transaction(function() use ($idx, $clinic_medicine_log, $x, $bed){
						$clinic_medicine_log[$x]->save();

						$clinic_patient = db::connection('clinic')->table('patient_list')
						->where('idx', '=', $idx)
						->update([
							'status' => 'Yes',
							'note' => $bed
						]);
					});
				}
			}else{
				DB::transaction(function() use ($clinic_patient_detail, $idx, $bed){
					$clinic_patient_detail->save();

					$clinic_patient = db::connection('clinic')->table('patient_list')
					->where('idx', '=', $idx)
					->update([
						'status' => $clinic_patient_detail->id,
						'note' => $bed
					]);
				});
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


}
