<?php

namespace App\Http\Controllers;

use Excel;
use File;
use DataTables;
use Response;
use DateTime;
use App\User;
use App\PresenceLog;
use App\Division;
use App\Department;
use App\Section;
use App\SubSection;
use App\Group;
use App\Grade;
use App\Position;
use App\CostCenter;
use App\PromotionLog;
use App\Mutationlog;
use App\HrQuestionLog;
use App\HrQuestionDetail;
use App\KaizenForm;
use App\KaizenScore;
use App\KaizenNote;
use App\Employee;
use App\EmployeeSync;
use App\EmploymentLog;
use App\OrganizationStructure;
use App\StandartCost;
use App\KaizenCalculation;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


class EmployeeController extends Controller
{
     public function __construct()
     {
          $this->middleware('auth');
          $this->keluarga = [
               'Tk',
               'K0',
               'K1',
               'K2',
               'K3',
               'Pk1',
               'Pk2',
               'Pk3',
               '0',
          ];

          $this->attend = [
               [ 'attend_code' => 'ABS', 'attend_name' =>  'Absent', 'attend_type' => '-'],
               [ 'attend_code' => 'CK1', 'attend_name' =>  'Keluarga Meninggal', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK10', 'attend_name' => 'Melahirkan', 'attend_type' =>  'Cuti'],
               [ 'attend_code' => 'CK11', 'attend_name' => 'Keguguran', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK12', 'attend_name' => 'Ibadah Haji / Ziarah Keagamaan', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK13', 'attend_name' => 'Musibah', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK15', 'attend_name' => 'Saudara Kandung Menikah', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK2', 'attend_name' =>  'Keluarga Serumah Meninggal', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK3', 'attend_name' =>  'Menikah', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK4', 'attend_name' =>  'Menikahkan', 'attend_type' =>  'Cuti'],
               [ 'attend_code' => 'CK5', 'attend_name' =>  'Menghitankan', 'attend_type' =>  'Cuti'],
               [ 'attend_code' => 'CK6', 'attend_name' =>  'Membaptiskan', 'attend_type' =>  'Cuti'],
               [ 'attend_code' => 'CK7', 'attend_name' =>  'Istri Keguguran / Melahirkan', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CK8', 'attend_name' =>  'Tugas Negara', 'attend_type' =>  'Cuti'],
               [ 'attend_code' => 'CK9', 'attend_name' =>  'Haid', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'CUTI', 'attend_name' => 'Cuti', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'IMP', 'attend_name' =>  'Izin Meninggalkan Pekerjaan', 'attend_type' => 'Izin Meninggalkan Pekerjaan'],
               [ 'attend_code' => 'Izin', 'attend_name' => 'Izin', 'attend_type' => 'Izin'],
               [ 'attend_code' => 'Mangkir', 'attend_name' =>   'Mangkir',  'attend_type' => 'Mangkir'],
               [ 'attend_code' => 'OFF', 'attend_name' =>  'OFF', 'attend_type' => '-'],
               [ 'attend_code' => 'PC',  'attend_name' =>  'Pulang Cepat', 'attend_type' =>  'Pulang Cepat'],
               [ 'attend_code' => 'SAKIT', 'attend_name' => 'Sakit Surat Dokter', 'attend_type' => 'Sakit'],
               [ 'attend_code' => 'UPL', 'attend_name' =>  'Cuti Tidak Di Bayar', 'attend_type' => 'Cuti'],
               [ 'attend_code' => 'EAI', 'attend_name' =>  'Early In', 'attend_type' => '-'],
               [ 'attend_code' => 'EAO', 'attend_name' =>  'Early Out', 'attend_type' => '-'],
               [ 'attend_code' => 'LTI', 'attend_name' =>  'Late In', 'attend_type' =>  'Terlambat'],
               [ 'attend_code' => 'NSI', 'attend_name' =>  'No Swipe In', 'attend_type' =>   'Tidak Ceklog Masuk'],
               [ 'attend_code' => 'NSO', 'attend_name' =>  'No Swipe Out',  'attend_type' =>  'Tidak Ceklog Pulang'],
               [ 'attend_code' => 'ODT', 'attend_name' =>  'Dinas',  'attend_type' =>   'Dinas Luar'],
               [ 'attend_code' => 'PRS', 'attend_name' =>  'Present', 'attend_type' =>  'Hadir'],
               [ 'attend_code' => 'PRSOFF', 'attend_name' =>    'PRSOFF', 'attend_type' =>   '-'],
               [ 'attend_code' => 'STSHIFT2', 'attend_name' =>  'Shift 2', 'attend_type' =>  '-'],
               [ 'attend_code' => 'STSHIFT3', 'attend_name' =>  'Shift 3', 'attend_type' =>  '-'],
               [ 'attend_code' => 'STSHIFTG', 'attend_name' =>  'Shift Group', 'attend_type' =>   '-'],
               [ 'attend_code' => 'TELAT', 'attend_name' =>     'Izin Telat Masuk', 'attend_type' =>  'Terlambat'],
               [ 'attend_code' => 'TRN', 'attend_name' =>  'Training', 'attend_type' => '-'],
               [ 'attend_code' => 'UNPR', 'attend_name' => 'Unproductive', 'attend_type' =>  '-']
          ];

          $this->status = [
               'Percobaan',
               'Kontrak 1',
               'Kontrak 2',
               'Tetap'
          ];

          $this->cat = [
               'Absensi', 'Lembur', 'BPJS Kes', 'BPJS TK', 'Cuti', "PKB", "Penggajian"
          ];

          $this->usr = "'PI1110001','PI0904007'";

          $this->wst = ['PI1808032', 'PI1809036', 'PI1505002'];

     }
// master emp
     public function index(){
          return view('employees.master.index',array(
               'status' => $this->status))->with('page', 'Master Employee')->with('head', 'Employees Data');
     }

     public function indexEmployeeResume(){
          $title = 'Employee Resume';
          $title_jp = '従業員のまとめ';

// $datas = db::table('employee_syncs')->select("select * from employee_syncs");

          $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

          $datas = db::select($q);

          return view('employees.report.employee_resume', array(
               'title' => $title,
               'title_jp' => $title_jp,
               'datas' => $datas
          ));
     }

     public function fetchEmployeeResume(Request $request){

          $tanggal = "";
          $addcostcenter = "";
          $adddepartment = "";
          $addsection = "";
          $addgrup = "";
          $addnik = "";

          if(strlen($request->get('datefrom')) > 0){
               $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
               $tanggal = "and tanggal >= '".$datefrom."' ";
               if(strlen($request->get('dateto')) > 0){
                    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                    $tanggal = $tanggal."and tanggal <= '".$dateto."' ";
               }
          }

          if($request->get('cost_center_code') != null) {
               $costcenter = implode(",", $request->get('cost_center_code'));
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

          if($request->get('employee_id') != null) {
               $niks = $request->get('employee_id');
               $niklen = count($niks);
               $nik = "";

               for($x = 0; $x < $niklen; $x++) {
                    $nik = $nik."'".$niks[$x]."'";
                    if($x != $niklen-1){
                         $nik = $nik.",";
                    }
               }
               $addnik = "and ovr.nik in (".$nik.") ";
          }

          $presences = db::connection('sunfish')->select("SELECT
               A.Emp_no,
               B.Full_name,
               B.Department,
               format ( A.shiftstarttime, 'yyyy-MM' ) AS orderer,
               format ( A.shiftstarttime, 'MMMM yyyy' ) AS periode,
               COUNT (
               IIF ( A.Attend_Code LIKE '%Mangkir%', 1, NULL )) AS mangkir,
               COUNT (
               IIF ( A.Attend_Code LIKE '%CK%' OR A.Attend_Code LIKE '%CUTI%' OR A.Attend_Code LIKE '%UPL%', 1, NULL )) AS cuti,
               COUNT (
               IIF ( A.Attend_Code LIKE '%Izin%', 1, NULL )) AS izin,
               COUNT (
               IIF ( A.Attend_Code LIKE '%SAKIT%', 1, NULL )) AS sakit,
               COUNT (
               IIF ( A.Attend_Code LIKE '%LTI%' OR A.Attend_Code LIKE '%TELAT%', 1, NULL )) AS terlambat,
               COUNT (
               IIF ( A.Attend_Code LIKE '%PC%', 1, NULL )) AS pulang_cepat,
               COUNT (
               IIF (
               A.Attend_Code LIKE '%ABS%' 
               OR A.Attend_Code LIKE '%CK10%' 
               OR A.Attend_Code LIKE '%CK11%' 
               OR A.Attend_Code LIKE '%CK12%' 
               OR A.Attend_Code LIKE '%CK8%' 
               OR A.Attend_Code LIKE '%Izin%' 
               OR A.Attend_Code LIKE '%Mangkir%' 
               OR A.Attend_Code LIKE '%PC%' 
               OR A.Attend_Code LIKE '%SAKIT%' 
               OR A.Attend_Code LIKE '%UPL%' 
               OR A.Attend_Code LIKE '%LTI%' 
               OR A.Attend_Code LIKE '%TELAT%',
               1,
               NULL 
               )) AS tunjangan,
               ISNULL(SUM (  A.total_ot / 60.0 ),0) AS overtime 
               FROM
               VIEW_YMPI_Emp_Attendance AS A
               left join VIEW_YMPI_Emp_OrgUnit as B on B.Emp_no = A.Emp_no
               WHERE
               YEAR(A.shiftstarttime) >= '2020'
               AND A.shiftstarttime <= '2020-04-30 23:59:59'
               GROUP BY
               format ( A.shiftstarttime, 'MMMM yyyy' ),
               format ( A.shiftstarttime, 'yyyy-MM' ),
               A.Emp_no,
               B.Full_name,
               B.Department
               ORDER BY
               A.Emp_no asc,
               orderer ASC");

          $response = array(
               'status' => true,
               'presences' => $presences,
          );
          return Response::json($response);
     }

     public function indexTotalMeeting()
     {
          $title_jp = "トータルミーティング";
          $title = "Total Meeting";
          return view('employees.report.total_meeting', array(
               'title' => $title,
               'title_jp' => $title_jp
          ))->with('page', 'Total Meeting');
     }

     public function indexTermination()
     {
          return view('employees.master.termination',array(
               'status' => $this->status))->with('page', 'Termination')->with('head', 'Employees Data');
     }

     public function indexEmployeeInformation()
     {
          return view('employees.index_employee_information');
     }

     public function indexKaizenAssessment($id)
     {
          return view('employees.service.kaizenDetail', array(
               'title' => 'e-Kaizen Verification',
               'title_jp' => '??'))->with('page', 'Kaizen');
     }

     public function indexKaizenData()
     {
          return view('employees.service.kaizenData', array(
               'title' => 'e-Kaizen Datas',
               'title_jp' => '??'))->with('page', 'Kaizen');
     }

     public function attendanceData()
     {
          $title = 'Attendance Data';
          $title_jp = '出席データ';
          $attend_codes = $this->attend;

          $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

          $datas = db::select($q);

          return view('employees.report.attendance_data', array(
               'title' => $title,
               'title_jp' => $title_jp,
               'datas' => $datas,
               'attend_codes' => $attend_codes
          ));
     }


     public function getNotif()
     {
          $ntf = HrQuestionLog::select(db::raw("SUM(remark) as ntf"))->first();
          return $ntf->ntf;
     }

     public function indexHRQA()
     {
          $q_question = "select category, count(id) as total_question, SUM(IF(remark = 1,1,0)) as unanswer from hr_question_logs group by category";
          $question = DB::select($q_question);

          return view('employees.master.hrquestion', array(
               'title' => 'HR Question & Answer',
               'title_jp' => '??',
               'all_question' => $question))->with('page', 'qna');
     }

     public function indexKaizen()
     {
          $username = Auth::user()->username;

          $emp = User::join('employee_syncs','employee_syncs.employee_id','=','users.username')
          ->where('employee_syncs.employee_id','=', $username)
          ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief", "Deputy Foreman") or role_code = "MIS" or username in ('.$this->usr.'))')
          ->select('position')
          ->first();

          $dd = [];

          $emp_usr = User::where('role_code','=','MIS')->select('username')->get();

          for($x = 0; $x < count($emp_usr); $x++) {
               array_push($dd, $emp_usr[$x]->username);
          }

          array_push($dd, 'PI0904007');

          $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

          $sc = db::select($sections);

          if ($emp) {
               return view('employees.service.indexKaizen', array(
                    'title' => 'e-Kaizen (Assessment List)',
                    'position' => $emp,
                    'section' => $sc,
                    'user' => $dd,
                    'title_jp' => 'e-改善（採点対象改善提案リスト）'))->with('page', 'Assess')->with('head','Kaizen');
          } else {
               return redirect()->back();
          }
     }

     public function indexKaizen2($section)
     {
          $username = Auth::user()->username;

          $emp = User::join('employee_syncs','employee_syncs.employee_id','=','users.username')
          ->where('employee_syncs.employee_id','=', $username)
          ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief") or role_code = "MIS")')
          ->select('position')
          ->first();

          $dd = str_replace("'","", $this->usr);
          $dd = explode(',', $dd);

          $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";


          $sc = db::select($sections);

          if ($emp) {
               return view('employees.service.indexKaizen', array(
                    'title' => 'e-Kaizen (Assessment List)',
                    'position' => $emp,
                    'section' => $sc,
                    'filter' => $section,
                    'user' => $dd,
                    'title_jp' => 'e-改善（採点対象改善提案リスト）'))->with('page', 'Assess')->with('head','Kaizen');
          } else {
               return redirect()->back();
          }
     }

     public function indexKaizenApplied()
     {
          $username = Auth::user()->username;

          $emp = User::leftJoin('employee_syncs','employee_syncs.employee_id','=','users.username')
          ->where('employee_syncs.employee_id','=', $username)
          ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief") or username in ("'.$this->usr.'"))')
          ->select('position')
          ->first();

          $dd = str_replace("'","", $this->usr);
          $dd = explode(',', $dd);

          $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

          $sc = db::select($sections);


          if ($emp) {
               return view('employees.service.indexKaizenApplied', array(
                    'title' => 'e-Kaizen (Applied list)',
                    'position' => $emp,
                    'section' => $sc,
                    'user' => $dd,
                    'title_jp' => '??'))->with('page', 'Applied')->with('head','Kaizen');
          } else {
               return redirect()->back();
          }

     }

     public function indexKaizenReport()
     {
          return view('employees.report.kaizen_rank', array(
               'title' => '',
               'title_jp' => ''))->with('page', 'Kaizen Report');
     }

     public function indexKaizenResume()
     {
          return view('employees.report.kaizen_resume', array(
               'title' => 'Report Kaizen Teian',
               'title_jp' => '改善提案の報告'))->with('page', 'Kaizen Resume');
     }

     public function indexKaizenApprovalResume()
     {
          $username = Auth::user()->username;

          $dd = str_replace("'","", $this->usr);
          $dd = explode(',', $dd);

// $get_department = Mutationlog::select('department')->whereNull('valid_to')->where("employee_id","=",Auth::user()->username)->first();

          $get_department = EmployeeSync::select('department')->where("employee_id","=",Auth::user()->username)->first();

          for ($i=0; $i < count($dd); $i++) {
               if ($username == $dd[$i] || Auth::user()->role_code == 'S' || Auth::user()->role_code == 'MIS') {
                    $d = "";
                    break;
               } else {
                    $d = "where department = '".$get_department->department."'";

                    if ($get_department->department == 'Maintenance') {
                         $d .= " or department = 'Production Engineering'";
                    }
               }
          }

          $q_data = "select bagian.*, IFNULL(kz.count,0) as count  from 
          (select fr.employee_id, `name`, position, fr.department, struktur.section from
          (select employee_id, `name`, position, department, section from employee_syncs where end_date is null and position in ('foreman', 'chief')) as fr
          left join 
          (select department, section from employee_syncs where department is not null and section is not null group by department, section) as struktur on fr.department = struktur.department) as bagian
          left join
          (select count(id) as count, area from kaizen_forms where `status` = -1 and deleted_at is null group by area) as kz
          on bagian.section = kz.area
          ".$d."
          order by `name` desc";

          $datas = db::select($q_data);

          return view('employees.service.kaizenAprovalResume', array(
               'title' => 'e-Kaizen Unverified Resume',
               'title_jp' => '',
               'datas' => $datas
          ))->with('page', 'Kaizen Aproval Resume');
     }

     public function indexUpdateKaizenDetail($id)
     {
          $data = KaizenForm::where('kaizen_forms.id','=', $id)
          ->leftJoin('kaizen_calculations','kaizen_forms.id','=','kaizen_calculations.id_kaizen')
          ->leftJoin('kaizen_notes','kaizen_forms.id','=','kaizen_notes.id_kaizen')
          ->select('kaizen_forms.id','kaizen_forms.employee_name','kaizen_forms.propose_date','kaizen_forms.section','kaizen_forms.leader','kaizen_forms.title','kaizen_forms.purpose', 'kaizen_forms.condition', 'kaizen_forms.improvement','kaizen_forms.area','kaizen_forms.employee_id','kaizen_calculations.id_cost', 'kaizen_calculations.cost','kaizen_notes.foreman_note','kaizen_notes.manager_note')
          ->get();

          $section = explode(" ~ ",$data[0]->section)[0];

          $ldr = "position = 'Leader'";
          if ($section == 'Assembly Process Control') {
               $ldr = "grade_name = 'Staff'";
          }

          $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and ".$ldr." and section = '".$section."' order by name asc";

// $q_subleader = "select employees.name, position, employees.employee_id from employees 
// left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
// left join mutation_logs on mutation_logs.employee_id = employees.employee_id
// where promotion_logs.valid_to is null and mutation_logs.valid_to is null and position = 'Leader'
// and end_date is null and section = '".$section."'
// order by name asc";

          $subleader = db::select($q_subleader);

          $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

          $sc = db::select($sections);

          return view('employees.service.ekaizenUpdate', array(
               'title' => 'e-Kaizen Update',
               'title_jp' => '',
               'subleaders' => $subleader,
               'sc' => $sc,
               'data' => $data
          ))->with('page', 'Kaizen Update');
     }

     public function indexUploadKaizenImage()
     {
          $username = Auth::user()->username;

          $mstr = EmployeeSync::where('employee_id','=', $username)->select('sub_section')->first();

          $datas = EmployeeSync::where('section','=', $mstr->sub_section)->select('employee_id','name')->get();

          return view('employees.service.ekaizenUpload', array(
               'title' => 'e-Kaizen Upload Images',
               'title_jp' => '',
               'employees' => $datas
          ))->with('page', 'Kaizen Upload Images');
     }

     public function indexKaizenReward()
     {
          $username = Auth::user()->username;

          $user = db::select("select username from users 
               left join employee_syncs on employee_syncs.employee_id = users.username
               where username = '".$username."' AND (role_code = 'MIS' OR username = 'PI0904007' OR position in ('Manager','foreman'))");

          if ($user) {
               return view('employees.report.report_kaizen_reward', array(
                    'title' => 'e-Kaizen Reward',
                    'title_jp' => '',
               ))->with('page', 'Kaizen Reward');  
          } else {
               return redirect()->back();
          }
     }

     public function makeKaizen($id, $name, $section, $group){
          $ldr = "position = 'Leader'";
          if ($section == 'Assembly Process Control') {
               $ldr = "grade_name = 'Staff'";
          }

// $q_subleader = "select employees.name, position, employees.employee_id from employees 
// left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
// left join mutation_logs on mutation_logs.employee_id = employees.employee_id
// where promotion_logs.valid_to is null and mutation_logs.valid_to is null and ".$ldr."
// and end_date is null and section = '".$section."'
// order by name asc";

          $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and ".$ldr." and section = '".$section."' order by name asc";


          $subleader = db::select($q_subleader);

          if (in_array($id , $this->wst)) {

          }

          $sections = "select section from employee_syncs where position in ('Leader', 'chief') group by section";

          $sc = db::select($sections);

          return view('employees.service.ekaizenForm', array(
               'title' => 'e-Kaizen',
               'emp_id' => $id,
               'name' => $name,
               'section' => $section,
               'group' => $group,
               'subleaders' => $subleader,
               'sc' => $sc,
               'title_jp' => ''));
     }

     public function makeKaizen2($id, $name, $section){
          $group = "";

          $ldr = "position = 'Leader'";
          if ($section == 'Assembly Process Control') {
               $ldr = "grade_name = 'Staff'";
          }

// $q_subleader = "select employees.name, position, employees.employee_id from employees 
// left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
// left join mutation_logs on mutation_logs.employee_id = employees.employee_id
// where promotion_logs.valid_to is null and mutation_logs.valid_to is null and ".$ldr."
// and end_date is null and section = '".$section."'
// order by name asc";

          $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and ".$ldr." and section = '".$section."' order by name asc";


          $subleader = db::select($q_subleader);

          if (in_array($id , $this->wst)) {

          }

          $sections = "select section from employee_syncs where position in ('Leader', 'chief') group by section";

          $sc = db::select($sections);

          return view('employees.service.ekaizenForm', array(
               'title' => 'e-Kaizen',
               'emp_id' => $id,
               'name' => $name,
               'section' => $section,
               'group' => $group,
               'subleaders' => $subleader,
               'sc' => $sc,
               'title_jp' => ''));
     }

     public function updateEmp($id){
          $keluarga = $this->keluarga;
          $emp = Employee::where('employee_id','=',$id)
          ->get();
          return view('employees.master.updateEmp', array(
               'emp' => $emp,
               'keluarga' => $keluarga))->with('page', 'Update Employee');
     }

     public function fetchTotalMeeting(Request $request){

          $now = date('Y-m-01', strtotime($request->get('period').'-01'));
          $period = date('Y-m', strtotime($request->get('period')));

          $first = date('Y-m-d', strtotime('-3 months', strtotime($now)));
          $last = date('Y-m-t', strtotime($now));
          $first_sunfish = date('Y-m-d', strtotime('-3 months', strtotime($now)));
          $last_mirai = date('Y-m-t', strtotime($now));

          $employees = db::select("select date_format(period, '%M %Y') as period, date_format(period, '%Y-%m') as period2, count(full_name) as total, sum(if(employ_code = 'OUTSOURCE', 1, 0)) as outsource, sum(if(employ_code = 'CONTRACT1', 1, 0)) as contract1, sum(if(employ_code = 'CONTRACT2', 1, 0)) as contract2, sum(if(employ_code = 'PERMANENT', 1, 0)) as permanent, sum(if(employ_code = 'PROBATION', 1, 0)) as probation, sum(if(gender = 'L', 1, 0)) as male, sum(if(gender = 'P', 1, 0)) as female, sum(if(`Labour_Union` = 'NONE' or `Labour_Union` is null, 1, 0)) as no_union, sum(if(`Labour_Union` = 'SPSI', 1, 0)) as spsi, sum(if(`Labour_Union` = 'sbm', 1, 0)) as sbm, sum(if(`Labour_Union` = 'spmi', 1, 0)) as spmi from employee_histories where end_date is null and date_format(period, '%Y-%m-%d') >= '".$first."' and date_format(period, '%Y-%m') <= '".$last."' group by date_format(period, '%Y-%m'), date_format(period, '%M %Y') order by period2 asc");

          $mirai_overtimes1 = array();
          $sunfish_overtimes1 = array();
          if($last >= '2020-01-01'){
               if($first <= '2020-01-01'){
                    $first_sunfish = '2020-01-01';
               }
               $sunfish_overtimes1 = db::connection('sunfish')->select("SELECT DISTINCT
                    X.orderer,
                    X.period,
                    VIEW_YMPI_Emp_OrgUnit.Department,
                    COALESCE ( Q.ot_person, 0 ) AS ot_person 
                    FROM
                    VIEW_YMPI_Emp_OrgUnit
                    CROSS JOIN (
                    SELECT DISTINCT
                    format ( ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    format ( ovtplanfrom, 'MMMM yyyy' ) AS period 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan 
                    WHERE
                    ovtplanfrom >= '".$first_sunfish." 00:00:00' 
                    AND ovtplanfrom <= '".$last." 00:00:00' 
                    ) X
                    LEFT JOIN (
                    SELECT
                    orderer,
                    period,
                    B.Department,
                    SUM ( ot ) / COUNT ( final.Emp_no ) AS ot_person 
                    FROM
                    (
                    SELECT
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                    SUM ( ROUND( A.total_ot / 60.0, 2 ) ) AS ot 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan A 
                    WHERE
                    A.ovtplanfrom >= '".$first_sunfish." 00:00:00' 
                    AND A.ovtplanfrom <= '".$last." 23:59:59' 
                    GROUP BY
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) 
                    ) AS final
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = final.Emp_no 
                    GROUP BY
                    orderer,
                    period,
                    B.Department 
                    ) AS Q ON Q.orderer = X.orderer 
                    AND Q.Department = VIEW_YMPI_Emp_OrgUnit.Department 
                    WHERE
                    VIEW_YMPI_Emp_OrgUnit.Department IS NOT NULL");
          }
          if($first <= '2020-01-01'){
               if($last_mirai >= '2020-01-01'){
                    $last_mirai = '2019-12-31';
               }
               $mirai_overtimes1 = db::select("select mon as period, department as Department, round(ot_hour / kar,2) as ot_person from 
                    (
                    select em.mon ,em.department, IFNULL(sum(ovr.final),0) ot_hour, sum(jml) as kar from
                    (
                    select emp.*, bagian.department, 1 as jml from 
                    (select employee_id, mon from 
                    (
                    select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
                    cross join (
                    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where week_date BETWEEN  '".$first."' and  '".$last_mirai."' group by date_format(week_date, '%Y-%m')) s
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
                    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where week_date BETWEEN  '".$first."' and '".$last_mirai."' group by date_format(week_date, '%Y-%m')
                    )
                    group by date_format(tanggal,'%Y-%m'), nik
                    ) ovr on em.employee_id = ovr.nik and em.mon = ovr.mon
                    group by department, em.mon
               ) as semua");
          }

          $overtimes1 = array();
          if($mirai_overtimes1 != null){
               foreach ($mirai_overtimes1 as $key) {
                    array_push($overtimes1, 
                         [
                              "period" => $key->period,
                              "Department" => $key->Department,
                              "ot_person" => $key->ot_person
                         ]);
               }
          }
          if($sunfish_overtimes1 != null){
               foreach ($sunfish_overtimes1 as $key) {
                    array_push($overtimes1, 
                         [
                              "period" => $key->orderer,
                              "Department" => $key->Department,
                              "ot_person" => $key->ot_person
                         ]);
               }
          }

          if($now >= '2020-01-01'){
               $overtimes2 = db::connection('sunfish')->select(" SELECT
                    orderer,
                    period,
                    Department,
                    SUM ( ot_3 ) AS ot_3,
                    SUM ( ot_14 ) AS ot_14,
                    IIF ( SUM ( ot_14 ) > 0 AND SUM ( ot_3 ) > 0, IIF(SUM ( ot_3 )>SUM ( ot_14 ), SUM ( ot_14 ), SUM ( ot_3 )), 0 ) AS ot_3_14,
                    SUM ( ot_56 ) AS ot_56 
                    FROM
                    (
                    SELECT
                    orderer,
                    period,
                    Department,
                    COUNT ( ot_3 ) AS ot_3,
                    0 AS ot_14,
                    0 AS ot_56 
                    FROM
                    (
                    SELECT
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                    B.Department,
                    SUM (
                    IIF(ROUND( A.total_ot / 60.0, 2 ) > 3, 1 , null)
                    ) AS ot_3 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan A
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no 
                    WHERE
                    A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '".$period."'
                    GROUP BY
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                    B.Department 
                    ) AS final 
                    GROUP BY
                    orderer,
                    period,
                    Department UNION ALL
                    SELECT
                    orderer,
                    period,
                    Department,
                    0 AS ot_3,
                    COUNT ( ot_14 ) AS ot_14,
                    0 AS ot_56 
                    FROM
                    (
                    SELECT
                    orderer,
                    period,
                    Emp_no,
                    Department,
                    SUM ( ot_14 ) AS ot_14 
                    FROM
                    (
                    SELECT
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                    A.Emp_no,
                    DATEPART( week, A.ovtplanfrom ) AS wk,
                    B.Department,
                    CASE

                    WHEN SUM (
                    ROUND( A.total_ot / 60.0, 2 ) 
                    ) > 14 THEN
                    1 ELSE NULL 
                    END AS ot_14 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan A
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no 
                    WHERE
                    A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '".$period."'
                    GROUP BY
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                    A.Emp_no,
                    DATEPART( week, A.ovtplanfrom ),
                    B.Department 
                    ) AS final 
                    GROUP BY
                    orderer,
                    period,
                    Emp_no,
                    Department 
                    ) AS final2 
                    GROUP BY
                    orderer,
                    period,
                    Department UNION ALL
                    SELECT
                    orderer,
                    period,
                    Department,
                    0 AS ot_3,
                    0 AS ot_14,
                    COUNT ( ot_56 ) AS ot_56 
                    FROM
                    (
                    SELECT
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                    A.Emp_no,
                    B.Department,
                    CASE

                    WHEN SUM (
                    ROUND( A.total_ot / 60.0, 2 )
                    ) > 56 THEN
                    1 ELSE NULL 
                    END AS ot_56 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan A
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no 
                    WHERE
                    A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '".$period."'
                    GROUP BY
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                    A.Emp_no,
                    B.Department 
                    ) AS final 
                    GROUP BY
                    orderer,
                    period,
                    Department 
                    ) AS ot_violation 
                    where Department is not null
                    GROUP BY
                    orderer,
                    period,
                    Department");
}
else{
     $overtimes2 = db::select("select kd.department as Department, '".$period."' as orderer, COALESCE(tiga.tiga_jam,0) as ot_3, COALESCE(patblas.emptblas_jam,0) as ot_14, COALESCE(tiga_patblas.tiga_patblas_jam,0) as ot_3_14, COALESCE(lima_nam.limanam_jam,0) as ot_56 from
          (select child_code as department from organization_structures where remark = 'department') kd
          left join
          ( select department, count(nik) tiga_jam from (
          select d.nik, karyawan.department from
          (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
          left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
          where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$period."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
          group by nik, tanggal) d 
          left join 
          (
          select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."' and id IN (
          SELECT MAX(id)
          FROM mutation_logs
          where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."'
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
          where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$period."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
          group by nik, tanggal) m
          group by nik, week_name) s
          left join 
          (
          select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."' and id IN (
          SELECT MAX(id)
          FROM mutation_logs
          where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."'
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
          where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$period."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
          group by nik, tanggal) d 
          where jam > 3
          group by d.nik ) z

          INNER JOIN

          (select s.nik from
          (select nik, sum(jam) jam, week_name from
          (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
          left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
          where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$period."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
          group by nik, tanggal) m
          group by nik, week_name) s
          where jam > 14
          group by s.nik) x on z.nik = x.nik
          ) c
          left join 
          (
          select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."' and id IN (
          SELECT MAX(id)
          FROM mutation_logs
          where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."'
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
          where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '".$period."' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
          group by nik, tanggal) d
          left join 
          (
          select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."' and id IN (
          SELECT MAX(id)
          FROM mutation_logs
          where DATE_FORMAT(valid_from,'%Y-%m') <= '".$period."'
          GROUP BY employee_id
          )
          ) employee on employee.employee_id = d.nik
          group by d.nik ) c
          where jam > 56
          group by department
     ) lima_nam on lima_nam.department = kd.department");
}


$response = array(
     'status' => true,
     'employees' => $employees,
     'overtimes1' => $overtimes1,
     'overtimes2' => $overtimes2,
     'period' => $period,
);
return Response::json($response);
}

public function insertEmp(){
     $dev = OrganizationStructure::where('status','LIKE','DIV%')->get();
     $dep = OrganizationStructure::where('status','LIKE','DEP%')->get();
     $sec = OrganizationStructure::where('status','LIKE','SEC%')->get();
     $sub = OrganizationStructure::where('status','LIKE','SSC%')->get();
     $grup = OrganizationStructure::where('status','LIKE','GRP%')->get();
     $kode =  DB::table('total_meeting_codes')->select('code')->groupBy('code')->get();
     $grade = Grade::orderBy('id', 'asc')->get();
     $position = Position::orderBy('id', 'asc')->get();
     $cc = CostCenter::get();

     return view('employees.master.insertEmp', array(
          'dev' => $dev,
          'dep' => $dep,
          'sec' => $sec,
          'sub' => $sub,
          'grup' => $grup,
          'grade' => $grade,
          'cc' => $cc,
          'kode' => $kode,
          'position' => $position, 
          'keluarga' => $this->keluarga ))->with('page', 'Master Employee');
}

public function fetchMasterEmp(Request $request){
     $where = "";

     if ($request->get("filter") != "") {
          if($request->get("filter") == "ofc") {
               $where = "where `remark` in ('0fc','Jps')";
          }
          else if($request->get("filter") == "prod") {
               $where = "where `remark` in ('WH', 'AP', 'EI', 'MTC', 'PP', 'PE', 'QA', 'WST')";
          }
     }

     $emp = "select employees.employee_id,name, department, section, DATE_FORMAT(hire_date,' %d %b %Y') hire_date, stat.status from employees
     LEFT JOIN (select employee_id, department, section, `group` from mutation_logs where valid_to is null group by employee_id, department, section, `group`) mutation_logs on employees.employee_id = mutation_logs.employee_id
     left join (
     select employee_id, status from employment_logs 
     WHERE id IN (
     SELECT MAX(id)
     FROM employment_logs
     GROUP BY employment_logs.employee_id
     )
     ) stat on stat.employee_id = employees.employee_id
     ".$where."
     ORDER BY employees.remark asc";
     $masteremp = DB::select($emp);

     return DataTables::of($masteremp)
     ->addColumn('action', function($masteremp){

          if ($masteremp->status != 'Tetap') {
               return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="detail(this.id)" id="' . $masteremp->employee_id . '">Details</a>
               <a href="'. url("index/updateEmp")."/".$masteremp->employee_id.'" class="btn btn-xs btn-warning"  id="' . $masteremp->employee_id . '">Update</a>
               <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Upgrade" onclick="modalUpgrade(\''.$masteremp->employee_id.'\', \''.$masteremp->name.'\',\''.$masteremp->status.'\')"><i class="fa fa-arrow-up"></i></button>';
          }
          else {
               return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="detail(this.id)" id="' . $masteremp->employee_id . '">Details</a>
               <a href="'. url("index/updateEmp")."/".$masteremp->employee_id.'" class="btn btn-xs btn-warning"  id="' . $masteremp->employee_id . '">Update</a>';
          }
     })

     ->rawColumns(['action' => 'action'])
     ->make(true);
}

public function fetchdetail(Request $request){

     $detail ="select employees.employee_id,employees.name,employees.avatar,employees.direct_superior,employees.birth_place, DATE_FORMAT(employees.birth_date,' %d %b %Y') birth_date,employees.gender,employees.address,employees.family_id, DATE_FORMAT(employees.hire_date,' %d %b %Y') hire_date,employees.remark,employees.phone,employees.account,employees.card_id,employees.npwp,employees.bpjstk,employees.jp,employees.bpjskes,mutation_logs.division,mutation_logs.department,mutation_logs.section,mutation_logs.sub_section,mutation_logs.group,promotion_logs.grade_code,promotion_logs.position,promotion_logs.grade_name from employees
     LEFT JOIN (select employee_id,cost_center, division, department, section, sub_section, `group` from mutation_logs where employee_id = '".$request->get('nik')."' and valid_to is null) mutation_logs on employees.employee_id = mutation_logs.employee_id 
     LEFT JOIN (select employee_id,grade_code, grade_name, position from promotion_logs where employee_id = '".$request->get('nik')."' and valid_to is null) promotion_logs on employees.employee_id = promotion_logs.employee_id
     where employees.employee_id ='".$request->get('nik')."'
     ORDER BY employees.remark asc";

     $detail2 = DB::select($detail);
     $response = array(
          'status' => true,
          'detail' => $detail2,
     );
     return Response::json($response);
}


public function empCreate(Request $request)
{
     $id = Auth::id();

     try{

          $hire_date = $request->get('tglM');

          if($request->hasFile('foto')){
               $files = $request->file('foto');
               foreach ($files as $file) 
               {
                    $number= $request->get('nik');
                    $data = file_get_contents($file);
                    $ext = $file->getClientOriginalExtension();
                    $photo_number = $number.".".$ext;
                    $filepath = public_path() . "/uploads/employee_photos/" . $photo_number;

                    $emp = new Employee([
                         'employee_id' => $request->get('nik'),
                         'name' => $request->get('nama'),
                         'gender' => $request->get('jk'),
                         'family_id' => $request->get('statusK'),
                         'birth_place' => $request->get('tmptL'),
                         'birth_date' => $request->get('tglL'),
                         'address' => $request->get('alamat'),
                         'phone' => $request->get('hp'),
                         'card_id' => $request->get('ktp'), 
                         'account' => $request->get('no_rek'),  
                         'bpjstk' => $request->get('bpjstk'),
                         'jp' => $request->get('jp'), 
                         'bpjskes' => $request->get('bpjskes'), 
                         'npwp' => $request->get('npwp'),                 
                         'direct_superior' => $request->get('leader'), 
                         'hire_date' => $hire_date, 
                         'avatar' => $photo_number, 
                         'remark' => $request->get('pin'), 
                         'created_by' => $id
                    ]);

                    $emp->save();
                    File::put($filepath, $data);
               }
          }else{
               $emp = new Employee([
                    'employee_id' => $request->get('nik'),
                    'name' => $request->get('nama'),
                    'gender' => $request->get('jk'),
                    'family_id' => $request->get('statusK'),
                    'birth_place' => $request->get('tmptL'),
                    'birth_date' => $request->get('tglL'),
                    'address' => $request->get('alamat'),
                    'phone' => $request->get('hp'),
                    'card_id' => $request->get('ktp'), 
                    'account' => $request->get('no_rek'),  
                    'bpjstk' => $request->get('bpjstk'), 
                    'jp' => $request->get('jp'), 
                    'bpjskes' => $request->get('bpjskes'), 
                    'npwp' => $request->get('npwp'),                 
                    'direct_superior' => $request->get('leader'), 
                    'hire_date' => $hire_date, 
                    'remark' => $request->get('pin'), 
                    'created_by' => $id
               ]);

               $emp->save();
          }

// --------------- Promotion Log insert

          $grade1 = $request->get('grade');
          $grade2 = explode("#", $grade1);
          $grade = new PromotionLog([
               'employee_id' => $request->get('nik'),
               'grade_code' => $grade2[0],
               'grade_name' => $grade2[1],
               'position' => $request->get('jabatan'),
               'valid_from' => $hire_date,
               'created_by' => $id

          ]);

          $grade->save();

// --------------- Mutation Log insert
          $jabatan = new Mutationlog ([
               'employee_id' => $request->get('nik'), 
               'cost_center' => $request->get('cs'),
               'division' => $request->get('devisi'), 
               'department' => $request->get('departemen'), 
               'section' => $request->get('section'), 
               'sub_section' => $request->get('subsection'), 
               'group' => $request->get('group'), 
               'valid_from' => $hire_date,
               'created_by' => $id
          ]);

          $jabatan->save();

// --------------- Employment Log insert

          $emp = new EmploymentLog ([
               'employee_id' => $request->get('nik'), 
               'status' => $request->get('statusKar'),
               'valid_from' => $hire_date,
               'created_by' => $id
          ]);

          $emp->save();

          return redirect('/index/insertEmp')->with('status', 'Input Employee success')->with('page', 'Master Employee');
     }
     catch (QueryException $e){
          return redirect('/index/insertEmp')->with('error', "Employee already exists")->with('page', 'Master Employee');
     }
}


public function getCostCenter(Request $request)
{
     $cc = CostCenter::select('cost_center')
     ->where('section','=',$request->get('section'))
     ->where('sub_sec','=',$request->get('subsection'))
     ->where('group','=',$request->get('group'))
     ->get();

     $response = array(
          'status' => true,
          'cost_center' => $cc,
     );
     return Response::json($response);

// select cost_center from cost_centers where section = 'Assembly Process' and sub_sec = 'CL BODY' and `group` = 'Leader'
}

public function updateEmpData(Request $request)
{
     $id = Auth::id();
     try{

          $idemp = $request->get('nik2');
          $emp = Employee::where('employee_id','=',$idemp)
          ->withTrashed()       
          ->first();

          if($request->hasFile('foto')){
               $files = $request->file('foto');
               foreach ($files as $file) 
               {
                    $number= $request->get('nik');
                    $data = file_get_contents($file);
                    $ext = $file->getClientOriginalExtension();
                    $photo_number = $number.".".$ext;
                    $filepath = public_path() . "/uploads/employee_photos/" . $photo_number;

                    $files = glob(public_path() . "/uploads/employee_photos/" .$number.".*");
                    foreach ($files as $file) {
                         unlink($file);
                    }

                    $emp->employee_id = $request->get('nik');
                    $emp->name = $request->get('nama');
                    $emp->gender = $request->get('jk');
                    $emp->family_id = $request->get('statusK');
                    $emp->birth_place = $request->get('tmptL');
                    $emp->birth_date = $request->get('tglL');
                    $emp->address = $request->get('alamat');
                    $emp->phone = $request->get('hp');
                    $emp->card_id = $request->get('ktp');
                    $emp->account = $request->get('no_rek');  
                    $emp->bpjstk = $request->get('bpjstk');
                    $emp->jp = $request->get('jp');
                    $emp->bpjskes = $request->get('bpjskes'); 
                    $emp->npwp = $request->get('npwp');                 
                    $emp->direct_superior = $request->get('leader');
                    $emp->hire_date = $request->get('tglM');
                    $emp->avatar = $photo_number; 
                    $emp->remark = $request->get('pin');
                    $emp->created_by = $id;        

                    $emp->save();
                    File::put($filepath, $data);
               }
          }else{

               $emp->employee_id = $request->get('nik');
               $emp->name = $request->get('nama');
               $emp->gender = $request->get('jk');
               $emp->family_id = $request->get('statusK');
               $emp->birth_place = $request->get('tmptL');
               $emp->birth_date = $request->get('tglL');
               $emp->address = $request->get('alamat');
               $emp->phone = $request->get('hp');
               $emp->card_id = $request->get('ktp');
               $emp->account = $request->get('no_rek');  
               $emp->bpjstk = $request->get('bpjstk');
               $emp->jp = $request->get('jp');
               $emp->bpjskes = $request->get('bpjskes'); 
               $emp->npwp = $request->get('npwp');                 
               $emp->direct_superior = $request->get('leader');
               $emp->hire_date = $request->get('tglM'); 
               $emp->remark = $request->get('pin');
               $emp->created_by = $id;
               $emp->save();   
          }

          $emp->category = $request->get('category');

          return redirect('/index/MasterKaryawan')->with('status', 'Update Employee Success')->with('page', 'Master Employee'); 
     }

     catch (QueryException $e){
          return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
     }

}

// end master emp

// absensi import

public function importEmp(Request $request)
{
     $id = Auth::id();
     try{
          $tanggal = [];

          if($request->hasFile('import')){
               $file = $request->file('import');
               $data = file_get_contents($file);
               $rows = explode("\r\n", $data);

               foreach ($rows as $row)
               {
                    if (strlen($row) > 0) {

                         $row = explode("\t", $row);
                         $tgl = date('Y-m-d',strtotime($row[2]));
                         $array = Arr::prepend($tanggal, $tgl);
// $array1 = Arr::collapse($array);
                         if ($row[3] =='  '){
                              $row[3] = '00:00';
                         }
                         if ($row[4] =='  '){
                              $row[4] = '00:00';
                         }
                         if ($row[5] ==''){
                              $row[5] = '-';
                         }

                         $detail =  PresenceLog::updateOrCreate([
                              'employee_id' => $row[1],
                              'presence_date' => date('Y-m-d',strtotime($row[2])),

                         ]
                         ,[          
                              'employee_id' => $row[1],
                              'presence_date' => date('Y-m-d',strtotime($row[2])),
                              'in_time' => $row[3],
                              'out_time' => $row[4],
                              'shift' => $row[5],
                              'remark' => $row[0],
                              'created_by' => $id,

                         ]);
                         $detail->save();
                    }
               }
          }
          return redirect('/index/MasterKaryawan')->with('status', 'Update Presence Employee Success'.$array[1])->with('page', 'Master Employee');
     }
     catch (QueryException $e){
          $emp = PresenceLog::where('presence_date','=',$tgl)
          ->forceDelete();
          return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
     }

}
// end absensi import

// master promotion_logs

public function indexpromotion(){
     return view('employees.master.promotion')->with('page', 'Promotion')->with('head', 'Employees Data');
}

public function fetchpromotion(Request $request)
{
     $emp_id = $request->get('emp_id');

     $promotion_logs = PromotionLog::leftJoin('employees', 'employees.employee_id', '=', 'promotion_logs.employee_id')
     ->select('promotion_logs.employee_id','employees.name', 'grade_code','grade_name', 'position', 'valid_from','valid_to')
     ->where('promotion_logs.employee_id','=', $emp_id)
     ->orderByRaw('promotion_logs.created_at desc')
     ->take(1)
     ->get();

     $pos = Position::orderBy('id', 'asc')->get();
     $grd = Grade::get();

     $response = array(
          'status' => true,
          'promotion_logs' => $promotion_logs[0],
          'positions' => $pos,
          'grades' => $grd
     );
     return Response::json($response);
}


public function changePromotion(Request $request)
{
     $grade = explode("#",$request->get('grade'));
     $emp_id = $request->get('emp_id');

     $data = PromotionLog::where('employee_id','=' , $emp_id)
     ->latest()
     ->first();
     $data->valid_to = $request->get('valid_to');
     $data->save();

     $promotion = new PromotionLog([
          'employee_id' => $emp_id,
          'grade_code' => $grade[0],
          'grade_name' => $grade[1],
          'valid_from' => $request->get('valid_from'),
          'position' => $request->get('position'),
          'created_by' => 1
     ]);

     $promotion->save();

     $response = array(
          'status' => true,
          'data' => $promotion,
     );
     return Response::json($response);
}

// end promotion_logs

// mutation log

public function indexMutation()
{
     return view('employees.master.mutation')->with('page', 'Mutation')->with('head', 'Employees Data');
}

public function fetchMutation(Request $request)
{
     $emp_id = $request->get('emp_id');

     $mutation_logs = MutationLog::leftJoin('employees', 'employees.employee_id', '=', 'mutation_logs.employee_id')
     ->select('mutation_logs.employee_id','name', 'cost_center', 'division','department', 'section', 'sub_section','group', 'valid_from', 'valid_to')
     ->where('mutation_logs.employee_id','=', $emp_id)
     ->orderByRaw('mutation_logs.created_at desc')
     ->take(1)
     ->get();

     $devision = OrganizationStructure::where('status','LIKE','DIV%')->get();
     $department = OrganizationStructure::where('status','LIKE','DEP%')->get();
     $section = OrganizationStructure::where('status','LIKE','SEC%')->get();
     $sub_section = OrganizationStructure::where('status','LIKE','SSC%')->get();
     $group = OrganizationStructure::where('status','LIKE','GRP%')->get();
     $cc = CostCenter::select('cost_center')->groupBy('cost_center')->get();

     $response = array(
          'status' => true,
          'mutation_logs' => $mutation_logs[0],
          'devision' => $devision,
          'department' => $department,
          'section' => $section,
          'sub_section' => $sub_section,
          'group' => $group,
          'cost_center' => $cc
     );
     return Response::json($response);
}

public function changeMutation(Request $request)
{
     $emp_id = $request->get('emp_id');

     $data = MutationLog::where('employee_id','=' , $emp_id)
     ->latest()
     ->first();
     $data->valid_to = $request->get('valid_to');
     $data->save();

     $mutation = new MutationLog([
          'employee_id' => $emp_id,
          'cost_center' => $request->get('cc'),
          'division' => $request->get('division'),
          'department' => $request->get('department'),
          'section' => $request->get('section'),
          'sub_section' => $request->get('subsection'),
          'group' => $request->get('group'),
          'reason' => $request->get('reason'),
          'valid_from' => $request->get('valid_from'),
          'created_by' => 1
     ]);

     $mutation->save();

     $response = array(
          'status' => true,
          'data' => $mutation,
     );
     return Response::json($response);
}

public function changeStatusEmployee(Request $request)
{
     $emp_id = $request->get('emp_id');

     $data = MutationLog::where('employee_id','=' , $emp_id)
     ->latest()
     ->first();
     $data->valid_to = $request->get('valid_to');
     $data->save();

     $mutation = new MutationLog([
          'employee_id' => $emp_id,
          'cost_center' => $request->get('cc'),
          'division' => $request->get('division'),
          'department' => $request->get('department'),
          'section' => $request->get('section'),
          'sub_section' => $request->get('subsection'),
          'group' => $request->get('group'),
          'reason' => $request->get('reason'),
          'valid_from' => $request->get('valid_from'),
          'created_by' => 1
     ]);

     $mutation->save();

     $response = array(
          'status' => true,
          'data' => $mutation,
     );
     return Response::json($response);
}

//end mutation_log

// --------------------- Total Meeting Report -------------------------

public function indexReportGender()
{
     return view('employees.report.manpower_by_gender',array(
          'title' => 'Report Employee by Gender',
          'title_jp' => '従業員報告 男女別'
     ))->with('page', 'Manpower by Gender');
}

public function fetchReportGender()
{
     $tgl = date('Y-m-d');
     $fiskal = "select fiscal_year from weekly_calendars WHERE week_date = '".$tgl."'";

     $get_fiskal = db::select($fiskal);

     $gender = "select mon, gender, sum(tot_karyawan) as tot_karyawan from
     (select mon, gender, count(if(if(date_format(a.hire_date, '%Y-%m') <= mon, 1, 0 ) - if(date_format(a.end_date, '%Y-%m') <= mon, 1, 0 ) = 0, null, 1)) as tot_karyawan from
     (
     select distinct fiscal_year, date_format(week_date, '%Y-%m') as mon
     from weekly_calendars
     ) as b
     join
     (
     select '".$get_fiskal[0]->fiscal_year."' as fy, end_date, hire_date, employee_id, gender
     from employees
     ) as a
     on a.fy = b.fiscal_year
     where mon <= date_format('".$tgl."','%Y-%m-%d') 
     group by mon, gender
     union all
     select mon, gender, count(if(if(date_format(a.entry_date, '%Y-%m') <= mon, 1, 0 ) - if(date_format(a.end_date, '%Y-%m') <= mon, 1, 0 ) = 0, null, 1)) as tot_karyawan from
     (
     select distinct fiscal_year, date_format(week_date, '%Y-%m') as mon
     from weekly_calendars
     ) as b
     join
     (
     select '".$get_fiskal[0]->fiscal_year."' as fy, end_date, entry_date, nik, gender
     from outsources
     ) as a
     on a.fy = b.fiscal_year
     where mon <= date_format('".$tgl."','%Y-%m-%d') 
     group by mon, gender) semua
     group by mon, gender";

     $get_manpower = db::select($gender);

     $response = array(
          'status' => true,
          'manpower_by_gender' => $get_manpower,
     );

     return Response::json($response); 
}

public function fetchReportGender2(Request $request)
{

     if(strlen($request->get('tgl')) > 0){
          $tgl = $request->get("tgl");
     }else{
          $tgl = date("Y-m");
     }
     $gender = "select gender, count(employee_id) as jml from employees where DATE_FORMAT(end_date,'%Y-%m') >= '".$tgl."' or end_date is null group by gender";

     $get_manpower = db::select($gender);
     $monthTitle = date("F Y", strtotime($tgl));

     $response = array(
          'status' => true,
          'manpower_by_gender' => $get_manpower,
          'monthTitle' => $monthTitle
     );

     return Response::json($response);
}


public function fetchReportStatus()
{
     $tanggal = date('Y-m');

     $fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '".$tanggal."' group by fiscal_year";

     $fy = db::select($fiskal);


     $statusS = "select count(c.employee_id) as emp, status, mon from
     (select * from 
     (
     select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
     cross join (
     select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
     ) m
     where hire_month <= mon and (mon < end_month OR end_month is null)
     ) as b
     left join
     (
     select id, employment_logs.employee_id, employment_logs.status, date_format(employment_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(employment_logs.valid_to, '%Y-%m'), date_format(now(), '%Y-%m')) as mon_to from employment_logs 
     WHERE id IN (
     SELECT MAX(id)
     FROM employment_logs
     GROUP BY employment_logs.employee_id, date_format(employment_logs.valid_from, '%Y-%m')
     )
     ) as c on b.employee_id = c.employee_id
     where mon_from <= mon and mon_to >= mon
     group by mon, status
     union all
     select count(name) as emp, 'OUTSOURCES' as status, mon from 
     (
     select name, date_format(entry_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from outsources
     cross join (
     select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
     ) m
     where hire_month <= mon and (mon < end_month OR end_month is null)
     group by mon";

     $get_manpower_status = db::select($statusS);

     $response = array(
          'status' => true,
          'manpower_by_status_stack' => $get_manpower_status,
     );

     return Response::json($response); 
}

public function reportSerikat()
{
     $tanggal = date('Y-m');

     $fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '".$tanggal."' group by fiscal_year";

     $fy = db::select($fiskal);


     $get_union = "select count(employee_id) as emp_tot, serikat, mon from
     ( select emp.employee_id, COALESCE(serikat,'NON UNION') serikat, mon, COALESCE(mon_from,mon) mon_from, COALESCE(mon_to,mon) mon_to from
     (select * from 
     (
     select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
     cross join (
     select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
     ) m
     where hire_month <= mon and (mon < end_month OR end_month is null)
     ) as emp
     join
     (
     select id, labor_union_logs.employee_id, labor_union_logs.`union` as serikat, date_format(labor_union_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(labor_union_logs.valid_to, '%Y-%m'), date_format(now(), '%Y-%m')) as mon_to from labor_union_logs 
     WHERE id IN (
     SELECT MAX(id)
     FROM labor_union_logs
     GROUP BY labor_union_logs.employee_id, date_format(labor_union_logs.valid_from, '%Y-%m')
     )
     ) uni on emp.employee_id = uni.employee_id
     ) semua
     where mon_from <= mon and mon_to >= mon
     group by mon, serikat
     union all
     select count(employee_id) as emp_tot, 'NON UNION' as serikat, mon from 
     (
     select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
     cross join (
     select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy[0]->fiscal_year."' and date_format(week_date, '%Y-%m') <= '".$tanggal."' group by date_format(week_date, '%Y-%m')) s
     ) m
     where hire_month <= mon and (mon < end_month OR end_month is null) and employee_id not in (select employee_id from labor_union_logs)
     group by mon
     order by mon asc, serikat desc";

     $union = db::select($get_union);

     $response = array(
          'status' => true,
          'manpower_by_serikat' => $union,
     );

     return Response::json($response); 

}

// --------------------- End Total Meeting Report ---------------------


// --------------------- Start Employement ---------------------
public function indexEmployment()
{
     return view('employees.master.indexEmployment')->with('page', 'Employement');
}
// --------------------- End Employement -----------------------


// -------------------------  Start Employee Service ------------------
public function indexEmployeeService(Request $request)
{
     $title = 'Employee Self Services';
     $title_jp = '従業員の情報サービス';
     $emp_id = Auth::user()->username;
     $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
     $now = date('Y-m-d');

     $profil = db::select("select employee_id, name, hire_date, phone, phone as wa_number, address, employment_status as `status`, division, department, section, `group`, sub_group, cost_center, grade_code, grade_name,position from employee_syncs where employee_id = '".$emp_id."'
          ");

     try{
          $presences = db::connection('sunfish')->select("SELECT
               Emp_no,
               format ( shiftstarttime, 'yyyy-MM' ) AS orderer,
               format ( shiftstarttime, 'MMMM yyyy' ) AS periode,
               COUNT (
               IIF ( Attend_Code LIKE '%ABS%', 1, NULL )) AS mangkir,
               COUNT (
               IIF ( Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%', 1, NULL )) AS cuti,
               COUNT (
               IIF ( Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%', 1, NULL )) AS izin,
               COUNT (
               IIF ( Attend_Code LIKE '%SAKIT%' OR Attend_Code LIKE '%SD%', 1, NULL )) AS sakit,
               COUNT (
               IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 1, NULL )) AS terlambat,
               COUNT (
               IIF ( Attend_Code LIKE '%PC%', 1, NULL )) AS pulang_cepat,
               COUNT (
               IIF (
               Attend_Code LIKE '%ABS%' 
               OR Attend_Code LIKE '%CK10%' 
               OR Attend_Code LIKE '%CK11%' 
               OR Attend_Code LIKE '%CK12%' 
               OR Attend_Code LIKE '%CK8%' 
               OR Attend_Code LIKE '%Izin%' 
               OR Attend_Code LIKE '%Mangkir%' 
               OR Attend_Code LIKE '%PC%' 
               OR Attend_Code LIKE '%SAKIT%' 
               OR Attend_Code LIKE '%UPL%' 
               OR Attend_Code LIKE '%LTI%' 
               OR Attend_Code LIKE '%TELAT%',
               1,
               NULL 
               )) AS tunjangan,
               SUM ( ROUND( total_ot / 60.0, 2 ) ) as overtime
               FROM
               VIEW_YMPI_Emp_Attendance 
               WHERE
               Emp_no = '".$emp_id."'
               AND YEAR ( shiftstarttime ) = '2020' 
               AND shiftstarttime <= '".$now."' 
               GROUP BY
               format ( shiftstarttime, 'MMMM yyyy' ),
               format ( shiftstarttime, 'yyyy-MM' ),
               Emp_no 
               ORDER BY
               orderer ASC");

          $employee = db::connection('sunfish')->select("SELECT
               VIEW_YMPI_LEAVE_BALANCE.remaining 
               FROM
               VIEW_YMPI_LEAVE_BALANCE
               WHERE
               VIEW_YMPI_LEAVE_BALANCE.emp_no = '".$emp_id."' 
               AND VIEW_YMPI_LEAVE_BALANCE.startvaliddate <= '".$now."'
               AND VIEW_YMPI_LEAVE_BALANCE.endvaliddate >= '".$now."'");

     }
     catch(\Exception $e){

     }

     if (ISSET($presences)) {
          return view('employees.service.indexEmploymentService', array(
               'status' => true,
               'title' => $title,
               'title_jp' => $title_jp,
               'emp_id' => $emp_id,
               'profil' => $profil,
               'presences' => $presences,
               'employee' => $employee,
          ))->with('page', 'Employment Services');
     }else{
          return view('employees.service.indexEmploymentService', array(
               'status' => true,
               'title' => $title,
               'title_jp' => $title_jp,
               'emp_id' => $emp_id,
               'profil' => $profil,
// 'presences' => $presences,
// 'employee' => $employee,
          ))->with('page', 'Employment Services');
     }
}

public function fetchChat(Request $request)
{
     $data = HrQuestionLog::leftJoin('hr_question_details','hr_question_details.message_id','=','hr_question_logs.id')
     ->where('hr_question_logs.created_by','=' , $request->get('employee_id'))
     ->select('hr_question_logs.id', 'hr_question_logs.message', 'hr_question_logs.category', 'hr_question_logs.created_at', db::raw('date_format(hr_question_logs.created_at, "%b %d, %H:%i") as created_at_new'), db::raw('hr_question_details.message as message_detail'), db::raw('hr_question_details.created_by as dari'), db::raw('hr_question_details.created_at as reply_date'), db::raw('SPLIT_STRING(IF(hr_question_details.created_by is null, hr_question_logs.created_by, hr_question_details.created_by) ,"_",1) as avatar'))
     ->orderBy('hr_question_logs.updated_at','desc')
     ->orderBy('hr_question_details.created_at','asc')
     ->get();

     $response = array(
          'status' => true,
          'chats' => $data,
// 'tes' => $obj,
          'base_avatar' => url('images/avatar/')
     );

     return Response::json($response); 
}

public function postChat(Request $request)
{
     $quest = new HrQuestionLog([
          'message' => $request->get('message'),
          'category' =>  $request->get('category'),
          'created_by' => $request->get('from'),
          'remark' => 1
     ]);

     $quest->save();

     $response = array(
          'status' => true
     );

     return Response::json($response); 
}

public function postComment(Request $request)
{
// $remark = 0;
     $id = $request->get('id');

     if($request->get("from") == "HR") {
          $remark = 0;
     } else {
          $remark = 1;
     }

     $questDetail = new HrQuestionDetail([
          'message' => $request->get('message'),
          'message_id' =>  $id,
          'created_by' => $request->get("from")
     ]);

     $questDetail->save();

     HrQuestionLog::where('id', $id)
     ->update(['remark' => $remark]);

     $response = array(
          'status' => true,
          'remark' => $remark
     );

     return Response::json($response); 
}
// -------------------------  End Employee Service --------------------

public function indexReportStatus()
{
     return view('employees.report.employee_status',  array(
          'title' => 'Report Employee by Status Kerja',
          'title_jp' => '従業員報告 ステータス別'
     ))->with('page', 'Manpower by Status Kerja');
}

public function indexReportManpower(){
     return view('employees.report.manpower',  array(
          'title' => 'Manpower Information',
          'title_jp' => '人工の情報'
     ))->with('page', 'Manpower Report');
}

public function indexReportGrade()
{
     return view('employees.report.employee_status',  array(
          'title' => 'Report Employee by Grade',
          'title_jp' => '従業員報告 グレード別'
     ))->with('page', 'Manpower by Grade');
}

public function indexReportDepartment()
{
     return view('employees.report.employee_status',  array(
          'title' => 'Report Employee by Department',
          'title_jp' => '従業員報告 部門別'
     ))->with('page', 'Manpower by Department');
}

public function indexReportJabatan()
{
     return view('employees.report.employee_status',  array(
          'title' => 'Report Employee by Jabatan',
          'title_jp' => '従業員報告 役職別'
     ))->with('page', 'Manpower by jabatan');
}

public function fetchReportManpower(){
     $manpowers = db::connection("sunfish")->select("SELECT
          Emp_no,
          Full_name,
          employ_code,
          Department,
          grade_code,
          pos_name_en,
          gender,
          CASE
          WHEN [Labour_Union] IS NULL THEN
          'NONE' 
          WHEN [Labour_Union] = '' THEN
          'NONE' 
          ELSE [Labour_Union] 
          END AS [union] 
          FROM
          [dbo].[VIEW_YMPI_Emp_OrgUnit] 
          WHERE
          end_date IS NULL");

     $response = array(
          'status' => true,
          'manpowers' => $manpowers,
     );
     return Response::json($response); 
}

public function fetchReportManpowerDetail(Request $request){

     $where = "";
     $where = "and ".$request->get('filter')." = '".$request->get('category')."'";

     $manpowers = db::connection("sunfish")->select("select Emp_no, Full_name, Division, Department, Section, Groups, Sub_Groups, convert(varchar, start_date, 105) as start_date, employ_code, grade_code, pos_name_en, gender, case when [Labour_Union] is null then 'NONE' else [Labour_Union] end as [union] FROM [dbo].[VIEW_YMPI_Emp_OrgUnit] where end_date is null ".$where." order by Emp_no asc");

     $response = array(
          'status' => true,
          'details' => $manpowers,
     );
     return Response::json($response); 
}

public function fetchReport(Request $request)
{

     if(strlen($request->get('tgl')) > 0){
          $tgl = $request->get("tgl");
     }else{
          $tgl = date("Y-m");
     }

     if ($request->get("ctg") == 'Report Employee by Status Kerja') {
          $emp = db::select("select count(emp.employee_id) jml, log.`status` from
               (select employee_id from employees
               WHERE DATE_FORMAT(end_date,'%Y-%m') >= '".$tgl."' or end_date is null) emp
               left join
               (SELECT id, employee_id, `status` FROM employment_logs
               WHERE id IN (SELECT MAX(id) FROM employment_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tgl."' GROUP BY employee_id)) log
               on emp.employee_id = log.employee_id
               GROUP BY log.`status`");
     } 
     else if ($request->get("ctg") == 'Report Employee by Grade') 
     {
          $emp = db::select("select count(emp.employee_id) jml, log.grade_code as `status` from
               (select employee_id from employees
               WHERE DATE_FORMAT(end_date,'%Y-%m') >= '".$tgl."' or end_date is null) emp
               left join
               (SELECT id, employee_id, grade_code FROM promotion_logs
               WHERE id IN (SELECT MAX(id) FROM promotion_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tgl."' GROUP BY employee_id)) log
               on emp.employee_id = log.employee_id
               GROUP BY log.grade_code
               Order By FIELD(status, '-', 'E0', 'E1', 'E2','E3', 'E4', 'E5','E6', 'E7', 'E8','L1', 'L2', 'L3','L4', 'M1', 'M2','M3', 'M4','D3')");
     }
     else if ($request->get("ctg") == 'Report Employee by Department') {
          $emp = db::select("select count(emp.employee_id) jml, log.department as status from
               (select employee_id from employees
               WHERE DATE_FORMAT(end_date,'%Y-%m') >= '".$tgl."' or end_date is null) emp
               left join
               (SELECT id, employee_id, department FROM mutation_logs
               WHERE id IN (SELECT MAX(id) FROM mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tgl."' GROUP BY employee_id)) log
               on emp.employee_id = log.employee_id
               GROUP BY log.department
               ORDER BY jml asc");
     } else if ($request->get("ctg") == 'Report Employee by Jabatan') {
          $emp = db::select("select count(emp.employee_id) jml, log.position as `status`, positions.position from
               (select employee_id from employees
               WHERE DATE_FORMAT(end_date,'%Y-%m') >= '".$tgl."' or end_date is null) emp
               left join
               (SELECT id, employee_id, position FROM promotion_logs
               WHERE id IN (SELECT MAX(id) FROM promotion_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '".$tgl."' and position <> '-' GROUP BY employee_id)) log
               on emp.employee_id = log.employee_id
               join positions on positions.position = log.position
               GROUP BY log.position, positions.position
               order by positions.id");
     }

     $monthTitle = date("F Y", strtotime($tgl));


     $response = array(
          'status' => true,
          'datas' => $emp,
          'ctg' => $request->get("ctg"),
          'monthTitle' => $monthTitle

     );

     return Response::json($response); 
}

public function detailReport(Request $request){
     $kondisi = $request->get("kondisi");

     if($request->get("by") == 'Report Employee by Status Kerja'){
          $query = "select employment_logs.employee_id, employees.`name`, mutation_logs.division, mutation_logs.department, mutation_logs.section, mutation_logs.sub_section, employees.hire_date, employment_logs.`status` from employment_logs
          LEFT JOIN mutation_logs ON employment_logs.employee_id = mutation_logs.employee_id
          LEFT JOIN employees ON employment_logs.employee_id = employees.employee_id
          where employees.end_date is null and employment_logs.valid_to is null and mutation_logs.valid_to is null and employment_logs.`status` = '".$kondisi."'";
     }elseif ($request->get("by") == 'Report Employee by Department') {
          $query = "select employment_logs.employee_id, employees.`name`, mutation_logs.division, mutation_logs.department, mutation_logs.section, mutation_logs.sub_section, employees.hire_date, employment_logs.`status` from employment_logs
          LEFT JOIN mutation_logs ON employment_logs.employee_id = mutation_logs.employee_id
          LEFT JOIN employees ON employment_logs.employee_id = employees.employee_id
          where employees.end_date is null and employment_logs.valid_to is null and mutation_logs.valid_to is null and mutation_logs.department = '".$kondisi."'";
     }elseif ($request->get("by") == 'Report Employee by Grade') {
          $query = "select employment_logs.employee_id, employees.`name`, mutation_logs.division, mutation_logs.department, mutation_logs.section, mutation_logs.sub_section, employees.hire_date, employment_logs.`status` from employment_logs
          LEFT JOIN mutation_logs ON employment_logs.employee_id = mutation_logs.employee_id
          LEFT JOIN employees ON employment_logs.employee_id = employees.employee_id
          LEFT JOIN promotion_logs ON employment_logs.employee_id = promotion_logs.employee_id
          where employees.end_date is null and employment_logs.valid_to is null and mutation_logs.valid_to is null and promotion_logs.valid_to is null and promotion_logs.grade_code = '".$kondisi."'";
     }elseif ($request->get("by") == 'Report Employee by Jabatan') {
          $query = "select employment_logs.employee_id, employees.`name`, mutation_logs.division, mutation_logs.department, mutation_logs.section, mutation_logs.sub_section, employees.hire_date, employment_logs.`status` from employment_logs
          LEFT JOIN mutation_logs ON employment_logs.employee_id = mutation_logs.employee_id
          LEFT JOIN employees ON employment_logs.employee_id = employees.employee_id
          LEFT JOIN promotion_logs ON employment_logs.employee_id = promotion_logs.employee_id
          where employees.end_date is null and employment_logs.valid_to is null and mutation_logs.valid_to is null and promotion_logs.valid_to is null and promotion_logs.position = '".$kondisi."'";
     }

     $detail = db::select($query);

     return DataTables::of($detail)->make(true);

}

public function exportBagian()
{
     $bagian = Mutationlog::select("employee_id", "cost_center", "division", "department", "section", "sub_section", "group")
// ->whereIn('id', db::raw(""))
     ->whereRaw('id in (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id)')
     ->get()
     ->toArray();

     $bagian_array[] = array('employee_id', 'cost_center','division','department','section','sub_section','group');

     foreach ($bagian as $key) {
          $bagian_array[] = array(
               'employee_id' => $key['employee_id'],
               'cost_center' => $key['cost_center'],
               'division' => $key['division'],
               'department' => $key['department'],
               'section' => $key['section'],
               'sub_section' => $key['sub_section'],
               'group' => $key['group']
          );
     }

     Excel::create('Bagian', function($excel) use ($bagian_array){
          $excel->setTitle('Bagian List');
          $excel->sheet('Employee Bagian Data', function($sheet) use ($bagian_array){
               $sheet->fromArray($bagian_array, null, 'A1', false, false);
          });
     })->download('xlsx');
}

public function importBagian(Request $request)
{
     $id = Auth::id();
     try{


          if($request->hasFile('importBagian')){
               $file = $request->file('importBagian');
               $data = file_get_contents($file);
               $rows = explode("\r\n", $data);

               foreach ($rows as $row)
               {
                    if (strlen($row) > 0) {
                         $row = explode("\t", $row);

                         $date_from = date("Y-m-d",strtotime($row[7]));
                         $date = DateTime::createFromFormat('d/m/Y', $row[7]);

                         $date_from = $date->format('Y-m-d');

                         date_sub($date, date_interval_create_from_date_string('1 days'));

                         $date_to = $date->format('Y-m-d');
                         Mutationlog::where('employee_id', $row[0])
                         ->orderBy('id','desc')
                         ->take(1)
                         ->update(['valid_to' => $date_to]);

                         $bagian = new Mutationlog([
                              'employee_id' => $row[0],
                              'cost_center' =>  $row[1],
                              'division' => $row[2],
                              'department' => $row[3],
                              'section' => $row[4],
                              'sub_section' => $row[5],
                              'group' => $row[6],
                              'valid_from' => $date_from,
                              'created_by' => $id
                         ]);

                         $bagian->save();
                    }
               }
          }
          return redirect('/index/MasterKaryawan')->with('status', 'Update Bagian Employee Success')->with('page', 'Master Employee');
     }
     catch (QueryException $e){
// $emp = PresenceLog::where('presence_date','=',$tgl)
// ->forceDelete();
          return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
     }

}

public function importKaryawan(Request $request)
{
     $id = Auth::id();
     try{
          if($request->hasFile('importEmployee')){
               $file = $request->file('importEmployee');
               $data = file_get_contents($file);
               $rows = explode("\r\n", $data);

               foreach ($rows as $row)
               {
                    if (strlen($row) > 0) {
                         $row = explode("\t", $row);

                         $date_from = date("Y-m-d",strtotime($row[7]));
                         $date = DateTime::createFromFormat('d/m/Y', $row[7]);

                         $date_from = $date->format('Y-m-d');

                         date_sub($date, date_interval_create_from_date_string('1 days'));

                         $date_to = $date->format('Y-m-d');
                         Mutationlog::where('employee_id', $row[0])
                         ->orderBy('id','desc')
                         ->take(1)
                         ->update(['valid_to' => $date_to]);

                         $bagian = new Mutationlog([
                              'employee_id' => $row[0],
                              'cost_center' =>  $row[1],
                              'division' => $row[2],
                              'department' => $row[3],
                              'section' => $row[4],
                              'sub_section' => $row[5],
                              'group' => $row[6],
                              'valid_from' => $date_from,
                              'created_by' => $id
                         ]);

                         $bagian->save();
                    }
               }
          }
          return redirect('/index/MasterKaryawan')->with('status', 'Update Bagian Employee Success')->with('page', 'Master Employee');
     }
     catch (QueryException $e){
// $emp = PresenceLog::where('presence_date','=',$tgl)
// ->forceDelete();
          return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
     }

}

//------------- Start DailyAttendance
public function indexDailyAttendance()
{
     return view('employees.report.daily_attendance',array(
          'title' => 'Attendance Rate',
          'title_jp' => '出勤率'))->with('page', 'Daily Attendance');
}

public function fetchDailyAttendance(Request $request){

     if(strlen($request->get('tgl')) > 0){
          $tgl = $request->get("tgl");
     }else{
          $tgl = date("m-Y");
     }

     $queryAttendance = "SELECT  DATE_FORMAT(hadir.tanggal,'%d %b %Y') as tanggal, hadir.jml as hadir, tdk.jml as tdk from (SELECT tanggal, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' GROUP BY tanggal ) as hadir LEFT JOIN (SELECT tanggal, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' GROUP BY tanggal) as tdk on hadir.tanggal = tdk.tanggal";

     $attendanceData = db::connection('mysql3')->select($queryAttendance);

     $tgl = '01-'.$tgl;
     $titleChart = date("F Y", strtotime($tgl));


     $response = array(
          'status' => true,
          'titleChart' => $titleChart,
          'attendanceData' => $attendanceData,

     );
     return Response::json($response);

}

public function detailDailyAttendance(Request $request){
     $tgl = date('d-m-Y', strtotime($request->get('tgl')));
     $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.masuk, presensi.keluar, presensi.shift from presensi 
     LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
     LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
     WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null ORDER BY presensi.nik";
     $detail = db::connection('mysql3')->select($query);

     return DataTables::of($detail)->make(true);
}
//------------- End DailyAttendance

//------------- Start Presence
public function indexPresence()
{
     return view('employees.report.presence', array(
          'title' => 'Presence',
          'title_jp' => '出勤'))->with('page', 'Presence Data');
}

public function fetchPresence(Request $request)
{
     if(strlen($request->get('tgl')) > 0){
          $tgl = date('d-m-Y',strtotime($request->get("tgl")));
     }else{
          $tgl = date("d-m-Y");
     }

     $query = "SELECT shift, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' GROUP BY shift";

     $presence = db::connection('mysql3')->select($query);
     $titleChart = date('j F Y',strtotime($tgl));

     $response = array(
          'status' => true,
          'presence' => $presence,
          'titleChart' => $titleChart,
          'tgl' => $tgl
     );
     return Response::json($response);
}

public function detailPresence(Request $request){
     $tgl = date('d-m-Y', strtotime($request->get('tgl')));
     $shift = $request->get('shift');

     $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.masuk, presensi.keluar, presensi.shift from presensi 
     LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
     LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
     WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null and shift = '".$shift."' ORDER BY presensi.nik";
     $detail = db::connection('mysql3')->select($query);

     return DataTables::of($detail)->make(true);
}
//------------- End Presence

//------------- Start Absence
public function indexAbsence()
{
     return view('employees.report.absence',array(
          'title' => 'Absence',
          'title_jp' => '欠勤',
          'absence_category' => $this->attend
     ))->with('page', 'Absence Data');
}

public function fetchAbsence(Request $request)
{
     if(strlen($request->get('tgl')) > 0){
          $tgl = date('d-m-Y',strtotime($request->get("tgl")));
     }else{
          $tgl = date("d-m-Y");
     }

     $absence = db::connection('sunfish')->select("
          select VIEW_YMPI_Emp_Attendance.emp_no, FORMAT (shiftstarttime, 'dd MMMM yyyy') as tanggal, official_name, Attend_Code, concat( Division,' / ', Department, ' / ', [Section]) as bagian from VIEW_YMPI_Emp_Attendance
          join VIEW_YMPI_Emp_OrgUnit on VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_Attendance.emp_no
          where FORMAT (shiftstarttime, 'dd-MM-yyyy') = '".$tgl."' and
          Attend_Code NOT LIKE '%PRS%' AND
          Attend_Code NOT LIKE '%PRSOFF%' AND
          Attend_Code NOT LIKE '%STSHIFT2%' AND
          Attend_Code NOT LIKE '%STSHIFT3%' AND
          Attend_Code NOT LIKE '%STSHIFTG%' AND
          Attend_Code NOT LIKE '%OFF%' AND
          Attend_Code NOT LIKE '%NSI%' AND
          Attend_Code NOT LIKE '%NSO%'
          ");

// $query = "SELECT shift, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' and shift <> 'OFF' and shift <> 'X' GROUP BY shift ORDER BY jml";

// $absence = db::connection('mysql3')->select($query);
     $titleChart = date('j F Y',strtotime($tgl));

     $response = array(
          'status' => true,
          'absence' => $absence,
          'titleChart' => $titleChart,
          'tgl' => $tgl
     );
     return Response::json($response);
}

public function detailAbsence(Request $request){
     $tgl = date('d-m-Y', strtotime($request->get('tgl')));
     $shift = $request->get('shift');

     $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.shift as absensi from presensi 
     LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
     LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
     WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null and shift = '".$shift."' ORDER BY presensi.nik";
     $detail = db::connection('mysql3')->select($query);

     return DataTables::of($detail)->make(true);
}
//------------- End Absence


public function fetchMasterQuestion(Request $request)
{
     $filter = $request->get("filter");
     $ctg = $request->get("ctg");

     $getQuestion = HrQuestionLog::leftJoin(db::raw('hr_question_logs as hr'),'hr.created_by' ,'=','hr_question_logs.created_by')
     ->select('hr_question_logs.message', db::raw('GROUP_CONCAT(hr.category) as category'), 'hr_question_logs.created_at', db::raw('date_format(hr_question_logs.created_at, "%b %d, %H:%i") as created_at_new'), 'hr_question_logs.created_by', db::raw('SUM(hr.remark) as notif'))
     ->whereRaw('hr_question_logs.id IN ( SELECT MAX(id) FROM hr_question_logs GROUP BY created_by )');

     if($filter != "") {
          $getQuestion = $getQuestion->whereRaw('hr_question_logs.created_by like "%'.$filter.'%"');
     }

     if($ctg != "") {
          $getQuestion = $getQuestion->whereRaw('hr.category = "'.$ctg.'"');
     }

     $getQuestion = $getQuestion->groupBy('hr_question_logs.created_by','hr_question_logs.message', 'hr_question_logs.created_at', 'hr_question_logs.created_by')
     ->orderBy('hr_question_logs.created_at', 'desc')
     ->get();

     $response = array(
          'status' => true,
          'question' => $getQuestion
     );
     return Response::json($response);
}

public function fetchDetailQuestion(Request $request)
{
     $getQuestionDetail = HrQuestionLog::select('message','category', 'created_at', 'created_by')
     ->where('created_by','=',$request->get('employee_id'))
     ->orderBy('created_at','desc')
     ->get();

     $response = array(
          'status' => true,
          'questionDetails' => $getQuestionDetail
     );
     return Response::json($response);
}

public function fetchAttendanceData(Request $request)
{

     $tanggal = "";
     $addcostcenter = "";
     $adddepartment = "";
     $addsection = "";
     $addgrup = "";
     $addnik = "";
     $addattend_code = "";

     if(strlen($request->get('datefrom')) > 0){
          $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
          $tanggal = "and A.shiftstarttime >= '".$datefrom." 00:00:00' ";
          if(strlen($request->get('dateto')) > 0){
               $dateto = date('Y-m-d', strtotime($request->get('dateto')));
               $tanggal = $tanggal."and A.shiftendtime <= '".$dateto." 23:59:59' ";
          }
     }

     if($request->get('cost_center_code') != null) {
          $costcenter = implode(",", $request->get('cost_center_code'));
          $addcostcenter = 'and B.cost_center_code in (\''.$costcenter.'\') ';
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
          $adddepartment = "and B.Department in (".$department.") ";
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
          $addsection = "and B.[Section] in (".$section.") ";
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
          $addgrup = "and B.Groups in (".$group.") ";
     }

     if($request->get('employee_id') != null) {
          $niks = $request->get('employee_id');
          $niklen = count($niks);
          $nik = "";

          for($x = 0; $x < $niklen; $x++) {
               $nik = $nik."'".$niks[$x]."'";
               if($x != $niklen-1){
                    $nik = $nik.",";
               }
          }
          $addnik = "and A.Emp_no in (".$nik.") ";
     }

     if($request->get('attend_code') != null) {
          $attend_codes = $request->get('attend_code');
          $attend_codelen = count($attend_codes);
          $attend_code = "";

          for($x = 0; $x < $attend_codelen; $x++) {
               $attend_code = $attend_code."A.attend_code like '%".$attend_codes[$x]."%'";
               if($x != $attend_codelen-1){
                    $attend_code = $attend_code." or ";
               }
          }
          $addattend_code = "and (".$attend_code.") ";
     }

     $qry = "SELECT
     format ( A.shiftstarttime, 'yyyy-MM-dd' ) AS tanggal,
     A.emp_no,
     B.Full_name,
     B.Department,
     B.section,
     B.groups,
     B.cost_center_code,
     A.shiftdaily_code,
     A.starttime,
     A.endtime,
     A.Attend_Code 
     FROM
     VIEW_YMPI_Emp_Attendance A
     LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON A.emp_no = B.emp_no 
     WHERE
     A.emp_no IS NOT NULL ".$tanggal."".$addcostcenter."".$adddepartment."".$addsection."".$addgrup."".$addnik."".$addattend_code."
     ORDER BY
     A.emp_no ASC";

     $attendances = db::connection('sunfish')->select($qry);

     return DataTables::of($attendances)->make(true);

// $response = array(
//      'status' => true,
//      'attendances' => $attendances,
//      'qry' => $qry
// );
// return Response::json($response);
}

public function editNumber(Request $request)
{
     try {
          $datas =  Employee::where('employee_id', $request->get('employee_id'))
          ->update(['phone' => $request->get('phone_number'), 'wa_number' => $request->get('wa_number')]);

          $response = array(
               'status' => true,
               'datas' => $datas
          );
          return Response::json($response);
     } catch (QueryException $e){
          $response = array(
               'status' => false,
               'datas' => $e->getMessage()
          );
          return Response::json($response);
     }

}

public function fetchKaizen(Request $request)
{
     $start = $request->get('bulanAwal');
     $end = $request->get('bulanAkhir');

     $kz = KaizenForm::leftJoin('kaizen_scores','kaizen_forms.id','=','kaizen_scores.id_kaizen')
     ->where('employee_id',$request->get('employee_id'))
     ->select('kaizen_forms.id','employee_id','propose_date','title','application','status','foreman_point_1', 'manager_point_1');
     if ($start != "" && $end != "") {
          $kz = $kz->where('propose_date','>=', $start)->where('propose_date','<=', $end)->get();
     }

     return DataTables::of($kz)
     ->addColumn('action', function($kz){
          if ($kz->status == '-1' || $kz->status == '3') {
               return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="cekDetail(this.id)" id="' . $kz->id . '"><i class="fa fa-eye"></i> Details</a>
               <a href="'. url("index/updateKaizen")."/".$kz->id.'" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> Ubah</a>
               <button onclick="openDeleteDialog('.$kz->id.',\''.$kz->title.'\', \''.$kz->propose_date.'\')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>';
          } else {
               return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="cekDetail(this.id)" id="' . $kz->id . '"><i class="fa fa-eye"></i> Details</a>';
          }

     })->addColumn('posisi', function($kz){
          if ($kz->foreman_point_1 != null && $kz->manager_point_1 == null) {
               return 'Sudah diverifikasi <b>Foreman</b>';
          } else if ($kz->foreman_point_1 != null && $kz->manager_point_1 != null) {
               return 'Sudah diverifikasi <b>Manager</b>';
          } else if ($kz->foreman_point_1 == null) {
               return 'Belum Verifikasi';
          }
     })->addColumn('stat', function($kz){
          if ($kz->status == '1') 
               return 'Kaizen';
          else if ($kz->status == '0') 
               return 'Bukan Kaizen';
          else if ($kz->status == '-1') 
               return 'Belum Verifikasi';
          else if ($kz->status == '3') 
               return '<font style="color:red">Terdapat Catatan</font>';
     })->addColumn('aplikasi', function($kz){
          if ($kz->application == '1') 
               return 'Telah di Aplikasikan';
          else if ($kz->application == '0') 
               return 'Tidak di Aplikasikan';
          else if ($kz->application == '') 
               return '';
     })

     ->rawColumns(['posisi' , 'action', 'stat'])
     ->make(true);
}

public function postKaizen(Request $request)
{
     try {
          $kz = new KaizenForm([
               'employee_id' => $request->get('employee_id'),
               'employee_name' => $request->get('employee_name'),
               'propose_date' => $request->get('propose_date'),
               'section' => $request->get('section'),
               'leader' => $request->get('leader'),
               'title' => $request->get('title'),
               'condition' => $request->get('condition'),
               'improvement' => $request->get('improvement'),
               'area' => $request->get('area_kz'),
               'purpose' => $request->get('purpose'),
               'status' => '-1'
          ]);

          $kz = KaizenForm::create([
               'employee_id' => $request->get('employee_id'),
               'employee_name' => $request->get('employee_name'),
               'propose_date' => $request->get('propose_date'),
               'section' => $request->get('section'),
               'leader' => $request->get('leader'),
               'title' => $request->get('title'),
               'condition' => $request->get('condition'),
               'improvement' => $request->get('improvement'),
               'area' => $request->get('area_kz'),
               'purpose' => $request->get('purpose'),
               'status' => '-1'
          ]);
          if(isset($kz->id))    
          {
               if ($request->get('estimasi')) {
                    foreach ($request->get('estimasi') as $est) {
                         $kc = new KaizenCalculation([
                              'id_kaizen' => $kz->id,
                              'id_cost' => $est[0],
                              'cost' => $est[1],
                              'created_by' => Auth::id(),
                              'created_at' => date('Y-m-d H:i:s'),
                         ]);

                         $kc->save();
                    }
               }

               $response = array(
                    'status' => true,
                    'datas' => 'Kaizen Berhasil ditambahkan'
               );
               return Response::json($response);
          }
          else
          {
//not inserted
          }

// $kz->save();

     } catch (QueryException $e){
          $response = array(
               'status' => false,
               'datas' => $e->getMessage()
          );
          return Response::json($response);
     }
}

public function fetchSubLeader()
{
     $ldr = Employee::leftJoin('promotion_logs','promotion_logs.employee_id','=','employees.employee_id')
     ->whereNull("end_date")
     ->whereNull("valid_to")
     ->get();

     return Response::json($ldr);
}

public function getKaizen(Request $request)
{
     $kzn = KaizenForm::where('id',$request->get('id'))->first();

     return Response::json($kzn);
}

public function fetchDataKaizen()
{
     $username = Auth::user()->username;
     for ($i=0; $i < count($_GET['user']); $i++) { 
          if ($username == $_GET['user'][$i]) {
               $d = 1;
               break;
          } else {
               $d = 0;
          }
     }

     if (Auth::user()->email == 'susilo.basri@music.yamaha.com' ) {
          $dprt = db::select("select distinct section from employee_syncs where (department = (select department from employee_syncs where employee_id = '".$username."') or department = 'Production Engineering') and section is not null");
     } else if (Auth::id() == 81) {
          $dprt = db::select("select distinct section from employee_syncs where section is not null");
     } else {
          $dprt = db::select("select distinct section from employee_syncs where department = (select department from employee_syncs where employee_id = '".$username."') and section is not null");
     }

     $kzn = KaizenForm::leftJoin('kaizen_scores','kaizen_forms.id','=','kaizen_scores.id_kaizen')
     ->select('kaizen_forms.id','employee_id','employee_name','title','area','section','propose_date','status','foreman_point_1','foreman_point_2', 'foreman_point_3', 'manager_point_1','manager_point_2', 'manager_point_3');
     if ($_GET['area'][0] != "") {
          $areas = implode("','", $_GET['area']);

          $kzn = $kzn->whereRaw('area in (\''.$areas.'\')');
     }

     if ($_GET['status'] != "") {
          if ($_GET['status'] == '1') {
               $kzn = $kzn->whereRaw('( status = -1 OR status = 3 )');
          } else if ($_GET['status'] == '2') {
               $kzn = $kzn->where('manager_point_1','=', '0');
               $kzn = $kzn->where('status','=', '1');
          } else if ($_GET['status'] == '3') {
               $kzn = $kzn->where('status','=', '1');
          } else if ($_GET['status'] == '4') {
               $kzn = $kzn->where('manager_point_1','<>', '0');
          } else if ($_GET['status'] == '5') {
               $kzn = $kzn->where('status','=', '2');
          } else if ($_GET['status'] == '6') {
               $kzn = $kzn->where('status','=', '0');
          }
     }

     $dprt2 = [];
     foreach ($dprt as $dpr) {
          array_push($dprt2, $dpr->section);
     }

     $dprt3 = implode("','", $dprt2);

     if ($_GET['filter'] != "") {
          $kzn = $kzn->where('area','=', $_GET['filter']);
          $kzn = $kzn->where('status','=', '-1');
     }

     if ($d == 0) {
          $kzn = $kzn->whereRaw('area in (\''.$dprt3.'\')');
     }

     $kzn->get();

     return DataTables::of($kzn)
     ->addColumn('fr_stat', function($kzn){
          if ($kzn->status == -1) {
               if ($_GET['position'] == 'Foreman' || $_GET['position'] == 'Manager' || $_GET['position'] == 'Chief'  || $_GET['position'] == 'Deputy General Manager' || $_GET['position'] == 'Deputy Foreman' || Auth::id() == 53 || Auth::id() == 80 || Auth::id() == 2580 || Auth::id() == 81) {
                    return '<a class="label bg-yellow btn" href="'.url("index/kaizen/detail/".$kzn->id."/foreman").'">Unverified</a>';
               } else {
                    return '<span class="label bg-yellow">Unverified</span>';
               }
          }
          else if ($kzn->status == 1){
               if ($kzn->foreman_point_1 != '' && $kzn->foreman_point_2 != '' && $kzn->foreman_point_3 != '') {
                    return '<span class="label bg-green"><i class="fa fa-check"></i> Verified</span>';
               } else {
                    return '<span class="label bg-yellow">Unverified</span>';
               }
          }
          else if ($kzn->status == 2) {
               return '<span class="label bg-green"><i class="fa fa-check"></i> Verified</span>';
          }
          else if ($kzn->status == 3) {
               return '<span class="label bg-blue"><i class="fa fa-envelope-o"></i>&nbsp; Noted</span>';
          }
          else {
               return '<span class="label bg-red"><i class="fa fa-close"></i> NOT Kaizen</span>';
          }

     })
     ->addColumn('action', function($kzn){
          return '<button onClick="cekDetail(\''.$kzn->id.'\')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> Details</button>';
     })
     ->addColumn('mg_stat', function($kzn){
          if ($kzn->foreman_point_1 != '' && $kzn->foreman_point_2 != '' && $kzn->foreman_point_3 != '') {
               if ($kzn->manager_point_1 != '' && $kzn->manager_point_2 != '' && $kzn->manager_point_3 != '') {
                    return '<span class="label bg-green"><i class="fa fa-check"></i> Verified</span>';
               } else {
                    if ($kzn->status == 2) {
                         return '<span class="label bg-red"><i class="fa fa-close"></i> NOT Kaizen</span>';
                    }
                    else if ($kzn->status == 3) {
                         return '<span class="label bg-blue"><i class="fa fa-envelope-o"></i>&nbsp; Noted</span>';
                    }
                    else {
                         if ($_GET['position'] == 'Manager' || $_GET['position'] == 'Deputy General Manager') {
                              return '<a class="label bg-yellow btn" href="'.url("index/kaizen/detail/".$kzn->id."/manager").'">Unverified</a>';     
                         } else {
                              return '<span class="label bg-yellow"><i class="fa fa-hourglass-half"></i>&nbsp; Unverified</span>'; 
                         }
                    }
               }
          } else {
               if ($kzn->status == 0) {
                    return '<span class="label bg-red"><i class="fa fa-close"></i> NOT Kaizen</span>';
               } else {
// return '<span class="label bg-yellow"><i class="fa fa-hourglass-half"></i>&nbsp; Unverified</span>';
               }
          }
     })
     ->addColumn('fr_point', function($kzn){
          return ($kzn->foreman_point_1 * 40) + ($kzn->foreman_point_2 * 30) + ($kzn->foreman_point_3 * 30);
     })
     ->addColumn('mg_point', function($kzn){
          return ($kzn->manager_point_1 * 40) + ($kzn->manager_point_2 * 30) + ($kzn->manager_point_3 * 30);
     })
     ->rawColumns(['fr_stat', 'mg_stat', 'fr_point', 'mg_point', 'action'])
     ->make(true);
}

public function inputKaizenDetailNote(Request $request){

     $kaizen_forms = KaizenForm::find($request->get('id'));
     $kaizen_forms->status = 3;

     $kaizen_notes = KaizenNote::firstOrNew(array('id_kaizen' => $request->get('id')));
     if ($request->get('from') == 'foreman') {
          $kaizen_notes->foreman_note = $request->get('catatan');
     }else if ($request->get('from') == 'manager') {
          $kaizen_notes->manager_note = $request->get('catatan');
     }
     $kaizen_notes->created_by = Auth::id();

     try{
          $kaizen_notes->save();
          $kaizen_forms->save();

          return response()->json([
               'status' => true,
               'message' => 'Note saved successfully'
          ]);

     }
     catch(\Exception $e){
          return response()->json([
               'status' => false,
               'message' => $e->getMessage(),
          ]);
     }
}

public function fetchDetailKaizen(Request $request)
{
     $data = KaizenForm::select("kaizen_forms.id","kaizen_forms.employee_id","employee_name", db::raw("date_format(propose_date,'%d-%b-%Y') as date"), "title", "condition", "improvement", "area", "leader", "purpose", "section", db::raw("name as leader_name"),'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3', 'kaizen_calculations.cost', 'standart_costs.cost_name', db::raw('kaizen_calculations.cost * standart_costs.cost as sub_total_cost'), 'frequency', 'unit',db::raw('standart_costs.cost as std_cost'), 'kaizen_forms.remark', 'kaizen_notes.foreman_note','kaizen_notes.manager_note')
     ->leftJoin('employees','employees.employee_id','=','kaizen_forms.leader')
     ->leftJoin('kaizen_calculations','kaizen_forms.id','=','kaizen_calculations.id_kaizen')
     ->leftJoin('standart_costs','standart_costs.id','=','kaizen_calculations.id_cost')
     ->leftJoin('kaizen_scores','kaizen_scores.id_kaizen','=','kaizen_forms.id')
     ->leftJoin('kaizen_notes','kaizen_forms.id','=','kaizen_notes.id_kaizen')
     ->where('kaizen_forms.id','=',$request->get('id'))
     ->get();

     $login = db::select("select username from users where '".Auth::user()->username."' in (select username from users where role_code = 'MIS' or username = 'PI0904007')");

     if (count($login) > 0) {
          $aksi = true;
     } else {
          $aksi = false;
     }

// return Response::json($data);
     return response()->json([
          'status' => true,
          'datas' => $data,
          'aksi' => $aksi
     ]);
}

public function assessKaizen(Request $request)
{
     $id = Auth::id();

     if ($request->get('category') == 'manager') { 
// --------------- JIKA inputor Manager ----
          if ($request->get('nilai1')) {
               try {
                    $data = KaizenScore::where('id_kaizen','=' , $request->get('id'))
                    ->first();

                    $data->manager_point_1 = $request->get('nilai1');
                    $data->manager_point_2 = $request->get('nilai2');
                    $data->manager_point_3 = $request->get('nilai3');
                    $data->save();

                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head','Kaizen');
// return ['status' => 'success', 'message' => 'Kaizen successfully assessed'];

               } catch (QueryException $e) {
// return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
               }
          } else {
// -------------- Jika Kaizen False -----------
               try {
                    $data = KaizenForm::where('id','=' , $request->get('id'))
                    ->first();

                    $data->status = 2;
                    $data->save();

// return ['status' => 'success', 'message' => 'Kaizen successfully assessed (NOT KAIZEN)'];
                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed (NOT KAIZEN)')->with('page', 'Assess')->with('head','Kaizen');
               } catch (QueryException $e) {
// return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
               }
          }
} else if ($request->get('category') == 'foreman') {    // --------------- JIKA inputor Foreman ----
     if ($request->get('nilai1')) {
// ----------------  JIKA KAIZEN true ------------
          try {
               $data = KaizenForm::where('id','=' , $request->get('id'))
               ->first();

               $data->status = 1;
               $data->save();

               $kz_nilai = new KaizenScore([
                    'id_kaizen' => $request->get('id'),
                    'foreman_point_1' => $request->get('nilai1'),
                    'foreman_point_2' => $request->get('nilai2'),
                    'foreman_point_3' => $request->get('nilai3'),
                    'created_by' => $id
               ]);

               $kz_nilai->save();

               $total_nilai = ($request->get('nilai1')*40)+($request->get('nilai2')*30)+($request->get('nilai1')*30);

               if($total_nilai <= 350){
                    $manager = KaizenScore::where('id_kaizen', '=', $request->get('id'))
                    ->update([
                         'manager_point_1' => $request->get('nilai1'),
                         'manager_point_2' => $request->get('nilai2'),
                         'manager_point_3' => $request->get('nilai3'),
                    ]);
               }

// return ['status' => 'success', 'message' => 'Kaizen successfully assessed'];
               return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head','Kaizen');

          } catch (QueryException $e) {
// return ['status' => 'error', 'message' => $e->getMessage()];
               return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
          }
     } else {
// ----------------  JIKA KAIZEN false ------------
          try {
               $data = KaizenForm::where('id','=' , $request->get('id'))
               ->first();

               $data->status = 0;
               $data->save();

// return ['status' => 'success', 'message' => 'Kaizen successfully assessed (NOT KAIZEN)'];

               return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed (NOT KAIZEN)')->with('page', 'Assess')->with('head','Kaizen');
          } catch (QueryException $e) {
// return ['status' => 'error', 'message' => $e->getMessage()];
               return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
          }
     }
}
}

public function fetchAppliedKaizen()
{
     $username = Auth::user()->username;
     for ($i=0; $i < count($_GET['user']); $i++) { 
          if ($username == $_GET['user'][$i]) {
               $d = 1;
               break;
          } else {
               $d = 0;
          }
     }

     $dprt = db::select("select distinct section from mutation_logs where valid_to is null and department = (select department from mutation_logs where employee_id = '".$username."' and valid_to is null)");

     $kzn = KaizenForm::Join('kaizen_scores','kaizen_forms.id','=','kaizen_scores.id_kaizen')
     ->select('kaizen_forms.id','employee_name','title','area','section','application','propose_date','status','foreman_point_1','foreman_point_2', 'foreman_point_3', 'manager_point_1','manager_point_2', 'manager_point_3')
     ->where('manager_point_1','<>','0');
     if ($_GET['area'][0] != "") {
          $areas = implode("','", $_GET['area']);

          $kzn = $kzn->whereRaw('area in (\''.$areas.'\')');
     }

     if ($_GET['status'] != "") {
          if ($_GET['status'] == '1') {
               $kzn = $kzn->whereNull('application');
          } else if ($_GET['status'] == '2') {
               $kzn = $kzn->where('application','=', '1');
          } else if ($_GET['status'] == '3') {
               $kzn = $kzn->where('application','=', '0');
          }
     }

     $dprt2 = [];
     foreach ($dprt as $dpr) {
          array_push($dprt2, $dpr->section);
     }

     $dprt3 = implode("','", $dprt2);
     if ($d == 0) {
          $kzn = $kzn->whereRaw('area in (\''.$dprt3.'\')');
     }

     $kzn->get();

     return DataTables::of($kzn)
     ->addColumn('app_stat', function($kzn){
          if ($kzn->application == '') {
               return '<button class="label bg-yellow btn" onclick="modal_apply('.$kzn->id.',\''.$kzn->title.'\')">UnApplied</a>';
          } else if($kzn->application == '1') {
               return '<span class="label bg-green"><i class="fa fa-check"></i> Applied</span>';
          } else if($kzn->application == '0') {
               return '<span class="label bg-red"><i class="fa fa-close"></i> NOT Applied</span>';
          }
     })
     ->addColumn('action', function($kzn){
          return '<button onClick="cekDetail(\''.$kzn->id.'\')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> Details</button>';
     })
     ->addColumn('fr_point', function($kzn){
          return ($kzn->foreman_point_1 * 40) + ($kzn->foreman_point_2 * 30) + ($kzn->foreman_point_3 * 30);
     })
     ->addColumn('mg_point', function($kzn){
          return ($kzn->manager_point_1 * 40) + ($kzn->manager_point_2 * 30) + ($kzn->manager_point_3 * 30);
     })
     ->rawColumns(['app_stat', 'fr_point', 'mg_point', 'action'])
     ->make(true);
}

public function fetchCost()
{
     $costL = StandartCost::get();

     return Response::json($costL);
}

public function fetchKaizenReport(Request $request)
{
     $date = date('Y-m');
     $dt2 = date('F');

     if ($request->get('tanggal') != "") {
          $date = $request->get('tanggal');
          $dt2 = date('F',strtotime($request->get('tanggal')));
     }

     $chart1 = "select count(kaizen_forms.employee_id) as kaizen , employee_syncs.department, employee_syncs.section from kaizen_forms 
     left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
     left join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
     where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m') = '".$date."' and kaizen_forms.`status` = 1
     group by employee_syncs.department, employee_syncs.section";

     $kz_total = db::select($chart1);

     $q_rank1 = "select kz.employee_id, employee_name, CONCAT(department,' - ', section,' - ', `group`) as bagian, mp1+mp2+mp3 as nilai from 
     (select employee_id, employee_name, SUM(manager_point_1 * 40) mp1, SUM(manager_point_2 * 30) mp2, SUM(manager_point_3 * 30) mp3 from kaizen_forms LEFT JOIN kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
     where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m') = '".$date."' and status = 1
     group by employee_id, employee_name
     ) as kz
     left join employee_syncs on kz.employee_id = employee_syncs.employee_id
     order by (mp1+mp2+mp3) desc
     limit 3";

     $kz_rank1 = db::select($q_rank1);

     $q_rank2 = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ', employee_syncs.section,' - ', `group`) as bagian , COUNT(kaizen_forms.employee_id) as count from kaizen_forms 
     left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
     left join kaizen_scores on kaizen_scores.id_kaizen = kaizen_forms.id
     where `status` = 1 and DATE_FORMAT(kaizen_scores.created_at,'%Y-%m') = '".$date."'
     group by kaizen_forms.employee_id, employee_name, department, employee_syncs.section, `group`
     order by count desc
     limit 10";

     $kz_rank2 = db::select($q_rank2);

     $q_excellent = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ',employee_syncs.section,' - ',`group`) as bagian, title, (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) as score, kaizen_forms.id from kaizen_forms 
     join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
     left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
     where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m') = '".$date."' and (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) > 450
     order by (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) desc";

     $kz_excellent = db::select($q_excellent);

     $q_a_excellent = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ',employee_syncs.section,' - ',`group`) as bagian, title, (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) as score, kaizen_forms.id from kaizen_forms 
     join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
     left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
     where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m') = '".$date."' and remark = 'excellent'
     order by (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) desc";

     $kz_after_excellent = db::select($q_a_excellent);

     $response = array(
          'status' => true,
          'charts' => $kz_total,
          'rank1' => $kz_rank1,
          'rank2' => $kz_rank2,
          'excellent' => $kz_excellent,
          'true_excellent' => $kz_after_excellent,
          'date' => $dt2
     );
     return Response::json($response);
}

public function applyKaizen(Request $request)
{
     try {
          KaizenForm::where('id', $request->get('id'))
          ->update(['application' => $request->get('status')]);
     } catch (QueryException $e) {
          $response = array(
               'status' => false,
               'message' => $e->getMessage()
          );
          return Response::json($response);
     }

     $response = array(
          'status' => true,
          'message' => 'e-Kaizen Updated Successfully'
     );
     return Response::json($response);
}

public function fetchKaizenResumeDetail(Request $request){
     $tanggal = date('Y-m-t');
     if(strlen($request->get('tanggal'))>0){
          $tanggal = date('Y-m-t', strtotime($request->get('tanggal').'-01'));
     }

     $fiscal = db::table('weekly_calendars')->where('week_date', '=', $tanggal)->first();

     $leader = explode('-', $request->get('leader'))[0];

     $q = "select kaizen_leaders.employee_id, employee_syncs.`name`, employee_syncs.position as grade, employee_syncs.`section`, employee_syncs.`group`, COALESCE(kz,0) as kz from kaizen_leaders 
     left join (select employee_id, count(id) as kz from kaizen_forms where propose_date in (select week_date from weekly_calendars where fiscal_year = '".$fiscal->fiscal_year."') and `status` = 1 group by employee_id) as kaizens on kaizens.employee_id = kaizen_leaders.employee_id
     inner join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
     where kaizen_leaders.leader_id = '".$leader."' and employee_syncs.end_date is null
     order by kz asc";

     $details = db::select($q);

     $response = array(
          'status' => true,
          'details' => $details,
     );
     return Response::json($response);
}

public function fetchKaizenResume(Request $request)
{
     $tanggal = date('Y-m-t');
     if(strlen($request->get('tanggal'))>0){
          $tanggal = date('Y-m-t', strtotime($request->get('tanggal').'-01'));
     }

     $fiscal = db::table('weekly_calendars')->where('week_date', '=', $tanggal)->select('fiscal_year')->first();

     try {

// $q = "select final.leader_id as leader, employee_syncs.`name`, count(final.employee_id) as total_operator, count(final.kaizen) as total_sudah, count(if(final.kaizen is null, 1, null)) as total_belum, 0 as total_kaizen from
// (
// select kaizen_leaders.leader_id, kaizen_leaders.employee_id as employee_id, kaizens.employee_id as kaizen from kaizen_leaders left join 
// (
// select employee_id from kaizen_forms left join weekly_calendars on kaizen_forms.propose_date = weekly_calendars.week_date where weekly_calendars.fiscal_year = '".$fiscal->fiscal_year."') as kaizens on kaizens.employee_id = kaizen_leaders.employee_id 
// inner join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
// where employee_syncs.end_date is null
// group by kaizen_leaders.leader_id, kaizens.employee_id, kaizen_leaders.employee_id) as final 
// inner join employee_syncs on employee_syncs.employee_id = final.leader_id where employee_syncs.end_date is null
// group by final.leader_id, employee_syncs.`name` order by total_belum desc";

          $q = "select kaizen_leaders.leader_id as leader, A.`name`, count(kz) as total_sudah, count(coalesce(kz, 1)) as total_operator, count(coalesce(kz, 1))-count(kz) total_belum from kaizen_leaders 
          left join (select employee_id, count(id) as kz from kaizen_forms where propose_date in (select week_date from weekly_calendars where fiscal_year = '".$fiscal->fiscal_year."') and `status` = '1' group by employee_id) as kaizens on kaizens.employee_id = kaizen_leaders.employee_id
          left join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
          left join employee_syncs A on A.employee_id = kaizen_leaders.leader_id
          where employee_syncs.end_date is null and A.end_date is null and A.employee_id is not null
          group by kaizen_leaders.leader_id, A.`name`
          order by total_belum desc";

          $datas = db::select($q);

     } catch (QueryException $e) {
          $response = array(
               'status' => false,
               'message' => $e->getMessage()
          );
          return Response::json($response);
     }

     $response = array(
          'status' => true,
          'datas' => $datas,
          'fiscal' => $fiscal->fiscal_year,
          'message' => 'Success'
     );
     return Response::json($response);
}

public function updateKaizen(Request $request)
{
     $stt_q = KaizenScore::where('id_kaizen','=',$request->get('id'))->first();

     if ($stt_q) {
          $stt = 1;
     } else {
          $stt = -1;
     }


     try {
          $kz = KaizenForm::where('id',$request->get('id'))
          ->update([
               'leader' => $request->get('leader'),
               'title' => $request->get('title'),
               'condition' => $request->get('condition'),
               'improvement' => $request->get('improvement'),
               'area' => $request->get('area_kz'),
               'purpose' => $request->get('purpose'),
               'status' => $stt
          ]);
          if ($request->get('estimasi')) {

               KaizenCalculation::where('id_kaizen',$request->get('id'))->forceDelete();

               foreach ($request->get('estimasi') as $est) {
                    $kc = new KaizenCalculation([
                         'id_kaizen' => $request->get('id'),
                         'id_cost' => $est[0],
                         'cost' => $est[1],
                         'created_by' => Auth::id(),
                         'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    $kc->save();
               }
          }

          $response = array(
               'status' => true,
               'datas' => 'Kaizen Berhasil diubah'
          );
          return Response::json($response);


     } catch (QueryException $e){
          $response = array(
               'status' => false,
               'datas' => $e->getMessage()
          );
          return Response::json($response);
     }
}

public function deleteKaizen(Request $request)
{
     KaizenForm::where('id',$request->get('id'))->delete();

     $response = array(
          'status' => true,
          'datas' => 'Data Berhasil dihapus'
     );
     return Response::json($response);
}


public function UploadKaizenImage(Request $request)
{
     $files = $request->file('fileupload');
     foreach ($files as $file) {
          $filename = $file->getClientOriginalName();

          if (!file_exists(public_path().'/kcfinderimages/'.$request->get('employee_id'))) {
               mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id'), 0777, true);
               mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', 0777, true);
          }

          $file->move(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', $filename);

// if (!file_exists(public_path().'/kcfinderimages/'.$request->get('employee_id'))) {
//   mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id'), 0777, true);
//   mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', 0777, true);
// }

// $file->move(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', $filename);
          return redirect('/index/upload_kaizen')->with('status', 'Upload Image Successfully');
     }
}

public function executeKaizenExcellent(Request $request)
{
     if ($request->get('status')) {
          $stat = 'excellent';
     } else {
          $stat = 'not excellent';
     }

     $kz = KaizenForm::where('id', $request->get('id'))
     ->update([
          'remark' => $stat,
     ]);

     $response = array(
          'status' => true,
          'message' => 'Kaizen Successfully Executed'
     );
     return Response::json($response);
}

public function getKaizenReward()
{
     $db = db::select("select DATE_FORMAT(CONCAT(mon,'-01'),'%M %Y') as  mons, doit, count(doit) as tot from 
          (select DATE_FORMAT(propose_date,'%Y-%m') as mon, IF(total < 300,2000,IF(total >= 300 AND total <= 350,5000,IF(total > 350 AND total <= 400,10000,IF(total > 400 AND total <= 450,25000,50000)))) as doit from
          (select propose_date, manager_point_1 * 40 m1, manager_point_2 * 30 m2, manager_point_3 * 30 m3, id_kaizen, (manager_point_1 * 40+ manager_point_2 * 30+ manager_point_3 * 30) as total from kaizen_scores 
          join kaizen_forms on kaizen_scores.id_kaizen = kaizen_forms.id
          where propose_date >= '2019-12-01'
          order by id_kaizen asc) as total
          ) as total2
          group by doit, mon
          order by mon asc, doit asc");

     $response = array(
          'status' => true,
          'datas' => $db
     );
     return Response::json($response);
}

public function fetchAbsenceEmployee(Request $request)
{
     $username = Auth::user()->username;

     $attend_code = "";

     if($request->get('attend_code') == 'Mangkir'){
          $attend_code = "Attend_code LIKE '%ABS%'";
     }

     if($request->get('attend_code') == 'Cuti'){
          $attend_code = "Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%'";
     }

     if($request->get('attend_code') == 'Izin'){
          $attend_code = "Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%'";
     }

     if($request->get('attend_code') == 'Sakit'){
          $attend_code = "Attend_Code LIKE '%SAKIT%' OR Attend_Code LIKE '%SD%'";
     }

     if($request->get('attend_code') == 'Terlambat'){
          $attend_code = "Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%'";
     }

     if($request->get('attend_code') == 'Pulang Cepat'){
          $attend_code = "Attend_Code LIKE '%PC%'";
     }

     $absence = db::connection('sunfish')->select("SELECT
          format ( shiftstarttime, 'dd MMM yyyy' ) AS tanggal,
          starttime,
          endtime,
          Attend_Code 
          FROM
          VIEW_YMPI_Emp_Attendance 
          WHERE
          Emp_no = '".$username."' 
          AND format ( shiftstarttime, 'MMMM yyyy' ) = '".$request->get('period')."' 
          AND ( ".$attend_code." )");

     if($request->get('attend_code') == 'Overtime'){
          $absence = "";

          $absence = db::connection('sunfish')->select("SELECT
               format ( shiftstarttime, 'dd MMM yyyy' ) AS tanggal,
               ovtactfrom AS starttime,
               ovtactto AS endtime,
               ovtrequest_no AS Attend_Code 
               FROM
               VIEW_YMPI_Emp_Attendance 
               WHERE
               ovtrequest_no IS NOT NULL
               AND Emp_no = '".$username."' 
               AND format ( shiftstarttime, 'MMMM yyyy' ) = '".$request->get('period')."'");
     }     

     $response = array(
          'status' => true,
          'datas' => $absence
     );
     return Response::json($response);
}

public function fetchDataKaizenAll(Request $request)
{
     $kzn = KaizenForm::join('kaizen_scores', 'kaizen_forms.id', '=', 'kaizen_scores.id_kaizen');

     if ($request->get('dari') != "") {
          $kzn = $kzn->where("kaizen_forms.propose_date", '>=', $request->get('dari'));
     }

     if ($request->get('sampai') != "") {
          $kzn = $kzn->where("kaizen_forms.propose_date", '<=', $request->get('sampai'));
     }

     $kzn = $kzn->whereNotNull('kaizen_scores.manager_point_1')
     ->select('kaizen_forms.id', 'kaizen_forms.propose_date', 'kaizen_forms.employee_id', 'kaizen_forms.employee_name', 'kaizen_forms.section', 'title', 'area', 'status', db::raw('(foreman_point_1 * 40) as FP1'), db::raw('(foreman_point_2 * 30) as FP2'), db::raw('(foreman_point_3 * 30) as FP3'), db::raw('(manager_point_1 * 40) as MP1'), db::raw('(manager_point_2 * 30) as MP2'), db::raw('(manager_point_3 * 30) as MP3'))
     ->orderBy('kaizen_forms.id', 'desc')
     ->get();

     return DataTables::of($kzn)
     ->addColumn('action', function($kzn){
          return '<button class="btn btn-primary" id="'.$kzn->id.'">details</button>';
     })
     ->rawColumns(['action'])
     ->make(true);
}

public function setSession(Request $request)
{
// Session::put('kz_filter', $request->input('filter'));

// Session::set('kz_filter', $request->input('filter')); 
     session(['kz_filter' => $request->input('filter'), 'kz_stat' => $request->input('filter2')]);
// session('kz_stat', $request->input('stat'));
     $data = [];
     foreach (Session::get('kz_filter') as $key) {
          $data[] = $key;
     }
     return Session::all();
}

}