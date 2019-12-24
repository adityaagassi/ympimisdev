<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
		];

	}

	public function indexClinicMonitoring(){
		$title = 'Clinic Visit Monitoring';
		$title_jp = '??';

		return view('clinic.display.clinic_visit_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', $title)->with('head','Clinic');
	}

	public function indexClinicVisit(){
		$title = 'Clinic Visit';
		$title_jp = '??';

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

		$visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.employee_name, e.hire_date, e.section  from patient_list p
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
		$visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.employee_name, e.hire_date, e.section, d.purpose from patient_list p
			left join ympimis.employee_syncs e on e.employee_id = p.employee_id
			left join ympimis.clinic_patient_details d on d.id = p.`status`
			where p.note is null
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

		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$date = "WHERE week_date BETWEEN '".$datefrom."'";
			$date_log = "WHERE tanggal BETWEEN '".$datefrom."'";

			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));

				$date = $date." AND '".$dateto."'";
				$date_log = $date_log." AND '".$dateto."'";
			}else{
				$date = $date." AND '". date('Y-m-d') ."'";
				$date_log = $date_log." AND '". date('Y-m-d') ."'";
			}
		}else{
			$date = "WHERE week_date BETWEEN '".date('Y-m-d',strtotime('-1 month'))."' AND '".date('Y-m-d')."'";
			$date_log = "WHERE tanggal BETWEEN '".date('Y-m-d',strtotime('-1 month'))."' AND '".date('Y-m-d')."'";
		}

		$clinic_visit = db::connection("clinic")->select("select DATE_FORMAT(date.week_date,'%d %b %Y') as week_date, COALESCE(log.sum,0) as visit from
			(select week_date, DATE_FORMAT(week_date,'%a') as `day` from ympimis.weekly_calendars
			".$date." and DATE_FORMAT(week_date,'%a') != 'Sun' and DATE_FORMAT(week_date,'%a') != 'Sat') as date
			left join 
			(select tanggal, count(employee_id) as sum from patient_logs
			".$date_log." group by tanggal) as log
			on date.week_date = log.tanggal");

		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
		);
		return Response::json($response);
	}

	public function fetchClinicVisit(Request $request){
		$date_log = "";

		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$date_log = "WHERE tanggal BETWEEN '".$datefrom."'";

			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));
				$date_log = $date_log." AND '".$dateto."'";
			}else{
				$date_log = $date_log." AND '". date('Y-m-d') ."'";
			}
		}else{
			$date_log = "WHERE tanggal BETWEEN '".date('Y-m-d',strtotime('-1 month'))."' AND '".date('Y-m-d')."'";
		}

		$clinic_visit = db::connection("clinic")->select("select e.cost_center, count(p.employee_id) as qty from patient_logs p
			left join ympimis.employee_syncs e on e.employee_id = p.employee_id ".$date_log."
			group by e.cost_center
			order by qty desc");
		$response = array(
			'status' => true,
			'clinic_visit' => $clinic_visit,
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

		$diagnose = "";
		if($request->get('diagnose') != null) {
			$diagnoses = $request->get('diagnose');
			for($x = 0; $x < count($diagnoses); $x++) {
				$diagnose = $diagnose.$diagnoses[$x];
				if($x != count($diagnoses)-1){
					$diagnose = $diagnose.", ";
				}
			}
		}

		$paramedic = $request->get('paramedic');
		$doctor = $request->get('doctor');
		$family = $request->get('family');
		$family_name = $request->get('family_name');
		$visited_at = $request->get('date');

		try{
			//Input Patient Diagnose
			$clinic_patient_detail = new ClinicPatientDetail([
				'employee_id' => $employee_id,
				'purpose' => $purpose,
				'diagnose' => $diagnose,
				'paramedic' => Auth::user()->name,
				'doctor' => $doctor,
				'family' => $family,
				'family_name' => $family_name,
				'visited_at' => $visited_at,
			]);
			$clinic_patient_detail->save();	

			//Input Medicine
			if($request->get('medicine') != null) {
				$medicines = $request->get('medicine');
				$clinic_medicine_log = [];
				for($x = 0; $x < count($medicines); $x++) {
					$clinic_medicine_log[$x] = new ClinicMedicineLog([
						'medicine_name' => $medicines[$x]['medicine_name'],
						'status' => 'out',
						'clinic_patient_detail' => $clinic_patient_detail->id,
						'quantity' => $medicines[$x]['quantity'],
					]);
					DB::transaction(function() use ($clinic_patient_detail, $idx, $clinic_medicine_log, $x){
						$clinic_patient_detail->save();
						$clinic_medicine_log[$x]->save();

						$clinic_patient = db::connection('clinic')->table('patient_list')
						->where('idx', '=', $idx)
						->update([
							'status' => $clinic_patient_detail->id
						]);
					});
				}
			}else{
				DB::transaction(function() use ($clinic_patient_detail, $idx){
					$clinic_patient_detail->save();

					$clinic_patient = db::connection('clinic')->table('patient_list')
					->where('idx', '=', $idx)
					->update([
						'status' => $clinic_patient_detail->id
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
