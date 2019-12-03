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
use App\Employee;
use App\EmployeeSync;
use App\EmploymentLog;
use App\OrganizationStructure;
use App\StandartCost;
use App\KaizenCalculation;
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

    $this->status = [
      'Percobaan',
      'Kontrak 1',
      'Kontrak 2',
      'Tetap'
    ];

    $this->cat = [
      'Absensi', 'Lembur', 'BPJS Kes', 'BPJS TK', 'Cuti', "PKB", "Penggajian"
    ];

    $this->usr = "'19014987','19014986','E01090823','R14122906','M09041335'";

    $this->wst = ['18084786', '18094874', 'S15053064'];

  }
// master emp
  public function index(){
    return view('employees.master.index',array(
      'status' => $this->status))->with('page', 'Master Employee')->with('head', 'Employees Data');
  }

  public function indexTotalMeeting()
  {
    return view('employees.report.total_meeting')->with('page', 'Total Meeting');
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

  public function attendanceData()
  {
    return view('employees.report.attendance_data'); 
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

    $emp = User::join('promotion_logs','promotion_logs.employee_id','=','users.username')
    ->where('promotion_logs.employee_id','=', $username)
    ->whereNull('valid_to')
    ->whereRaw('(promotion_logs.position in ("Foreman","Manager","Chief") or role_code = "MIS")')
    ->select('position')
    ->first();

    $dd = [];

    $emp_usr = User::where('role_code','=','MIS')->select('username')->get();

    for($x = 0; $x < count($emp_usr); $x++) {
      array_push($dd, $emp_usr[$x]->username);
    }

    array_push($dd, 'O11101710');

    // $dd = implode("','", $emp_usr);

    // $dd = str_replace("'","", $this->usr);
    // $dd = "'".$dd."'";

    $sections = "select section from
    (select employee_id, position from promotion_logs where valid_to is null and position in ('Leader', 'Chief')) d
    left join employees on d.employee_id = employees.employee_id
    left join
    (select employee_id, section from mutation_logs where valid_to is null) s on s.employee_id = d.employee_id
    group by section
    order by section";

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

    $emp = User::join('promotion_logs','promotion_logs.employee_id','=','users.username')
    ->where('promotion_logs.employee_id','=', $username)
    ->whereNull('valid_to')
    ->whereRaw('(promotion_logs.position in ("Foreman","Manager","Chief") or role_code = "MIS")')
    ->select('position')
    ->first();

    $dd = str_replace("'","", $this->usr);
    $dd = explode(',', $dd);

    $sections = "select section from
    (select employee_id, position from promotion_logs where valid_to is null and position in ('Leader', 'Chief')) d
    left join employees on d.employee_id = employees.employee_id
    left join
    (select employee_id, section from mutation_logs where valid_to is null) s on s.employee_id = d.employee_id
    group by section
    order by section";

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

    $emp = User::join('promotion_logs','promotion_logs.employee_id','=','users.username')
    ->where('promotion_logs.employee_id','=', $username)
    ->whereNull('valid_to')
    ->whereRaw('(promotion_logs.position in ("Foreman","Manager","Chief") or username in ('.$this->usr.'))')
    ->select('position')
    ->first();

    $dd = str_replace("'","", $this->usr);
    $dd = explode(',', $dd);

    $sections = "select section from
    (select employee_id, position from promotion_logs where valid_to is null and position in ('Leader', 'Chief')) d
    left join employees on d.employee_id = employees.employee_id
    left join
    (select employee_id, section from mutation_logs where valid_to is null) s on s.employee_id = d.employee_id
    group by section
    order by section";

    $sc = db::select($sections);

    return view('employees.service.indexKaizenApplied', array(
      'title' => 'e-Kaizen (Applied list)',
      'position' => $emp,
      'section' => $sc,
      'user' => $dd,
      'title_jp' => '??'))->with('page', 'Applied')->with('head','Kaizen');
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

    $get_department = Mutationlog::select('department')->whereNull('valid_to')->where("employee_id","=",Auth::user()->username)->first();

    for ($i=0; $i < count($dd); $i++) {
      if ($username == $dd[$i] || Auth::user()->role_code == 'S' || Auth::user()->role_code == 'MIS') {
        $d = "";
        break;
      } else {
        $d = "where department = '".$get_department->department."'";
      }
    }

    $q_data = "select bagian.*, IFNULL(kz.count,0) as count  from 
    (select fr.employee_id, `name`, position, fr.department, struktur.section from
    (select employees.employee_id, `name`, position, department, section from employees left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
    left join mutation_logs on mutation_logs.employee_id = employees.employee_id
    where end_date is null and promotion_logs.valid_to is null and mutation_logs.valid_to is null and position in ('foreman','chief')) as fr
    left join 
    (select organization_structures.child_code, organization_structures.`status` as dep, os.parent_name, os.child_code as section from organization_structures 
    join organization_structures as os on organization_structures.`status` = os.parent_name
    where organization_structures.remark = 'department') as struktur on fr.department = struktur.child_code) as bagian
    left join
    (select count(id) as count, area from kaizen_forms where `status` = -1 group by area) as kz
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
    ->select('kaizen_forms.id','kaizen_forms.employee_name','kaizen_forms.propose_date','kaizen_forms.section','kaizen_forms.leader','kaizen_forms.title','kaizen_forms.purpose', 'kaizen_forms.condition', 'kaizen_forms.improvement','kaizen_forms.area','kaizen_forms.employee_id','kaizen_calculations.id_cost', 'kaizen_calculations.cost')
    ->get();

    $section = explode(" ~ ",$data[0]->section)[0];

    $q_subleader = "select employees.name, position, employees.employee_id from employees 
    left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
    left join mutation_logs on mutation_logs.employee_id = employees.employee_id
    where promotion_logs.valid_to is null and mutation_logs.valid_to is null and position = 'Leader'
    and end_date is null and section = '".$section."'
    order by name asc";

    $subleader = db::select($q_subleader);

    $sections = "select section from
    (select employee_id, position from promotion_logs where valid_to is null and position in ('Leader', 'chief')) d
    left join employees on d.employee_id = employees.employee_id
    left join
    (select employee_id, section from mutation_logs where valid_to is null) s on s.employee_id = d.employee_id
    group by section
    order by section";

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

  public function makeKaizen($id, $name, $section, $group){
    $ldr = "position = 'Leader'";
    if ($section == 'Assembly Process Control') {
      $ldr = "grade_name = 'Staff'";
    }

    $q_subleader = "select employees.name, position, employees.employee_id from employees 
    left join promotion_logs on employees.employee_id = promotion_logs.employee_id 
    left join mutation_logs on mutation_logs.employee_id = employees.employee_id
    where promotion_logs.valid_to is null and mutation_logs.valid_to is null and ".$ldr."
    and end_date is null and section = '".$section."'
    order by name asc";


    $subleader = db::select($q_subleader);

    if (in_array($id , $this->wst)) {

    }
    
    $sections = "select section from
    (select employee_id, position from promotion_logs where valid_to is null and position in ('Leader', 'chief')) d
    left join employees on d.employee_id = employees.employee_id
    left join
    (select employee_id, section from mutation_logs where valid_to is null) s on s.employee_id = d.employee_id
    group by section
    order by section";

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

// if (!file_exists(public_path().'/kcfinderimages/'.$emp_id)) {
//   mkdir(public_path().'/kcfinderimages/'.$emp_id, 0777, true);
//   mkdir(public_path().'/kcfinderimages/'.$emp_id.'/files', 0777, true);
// }

  $query = "select employees.employee_id, employees.name,  DATE_FORMAT(employees.hire_date, '%d %M %Y') hire_date, phone, wa_number, address, employees.direct_superior, emp_log.`status`, mut_log.division, mut_log.department, mut_log.section, mut_log.sub_section, mut_log.`group`, mut_log.cost_center, promot_log.grade_code, promot_log.grade_name, promot_log.position from employees 
  left join 
  (
  SELECT employee_id, `status` FROM employment_logs
  WHERE id IN ( SELECT MAX(id) FROM employment_logs where employee_id = '".$emp_id."' GROUP BY employee_id)
  ) as emp_log on employees.employee_id = emp_log.employee_id
  left join
  (
  select employee_id ,division, department, section, sub_section, `group`, cost_center from mutation_logs
  WHERE id IN (SELECT MAX(id) FROM mutation_logs where employee_id = '".$emp_id."' GROUP BY employee_id)
  ) as mut_log on employees.employee_id = mut_log.employee_id
  left join
  (
  select employee_id ,grade_code, grade_name, position from promotion_logs
  WHERE id IN (SELECT MAX(id) FROM promotion_logs where employee_id = '".$emp_id."' GROUP BY employee_id)
  ) as promot_log on employees.employee_id = promot_log.employee_id
  where employees.employee_id = '".$emp_id."'";

  $absence = "select abs.*, COALESCE(jam,0) overtime, IF(absent > 0 OR permit > 0 OR sick > 0 OR pc > 0 OR late > 0, 1, 0) as dicipline from 
  (select DATE_FORMAT(tanggal,'%b %Y') as period, sum(if(shift = 'A',1,0)) as absent, sum(if(shift = 'I',1,0)) as permit, sum(if(shift = 'SD',1,0)) as sick, sum(if(shift = 'CT',1,0)) as personal_leave, sum(if(shift = 'T',1,0)) as late, sum(if(shift = 'PC',1,0)) as pc from ftm.presensi where nik = '".$emp_id."'
  group by DATE_FORMAT(tanggal,'%b %Y') 
  order by tanggal asc) abs
  left join (
  select DATE_FORMAT(tanggal,'%b %Y') as period, SUM(IF(status = 0, jam, final)) as jam from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and jam_aktual = 0 and nik = '".$emp_id."'
  group by DATE_FORMAT(tanggal,'%b %Y')
) ovr on ovr.period = abs.period";

$absences = db::connection('mysql3')->select($absence);

$ct = db::connection('mysql3')->select("
  select leave_quota, leave_quota - (select shift + (select count(tanggal) as mass_leave from kalender where deskripsi = 'Mass Leave' and tanggal >= 
  IF(DATE_FORMAT(hire_date,'%m-%d') > DATE_FORMAT(now(),'%m-%d'), 
  DATE_FORMAT(hire_date, CONCAT(YEAR(now() - INTERVAL 1 YEAR),'-%m-01')),
  DATE_FORMAT(hire_date, CONCAT(YEAR(now()),'-%m-01')))) as cuti from ympimis.employees left join
  (select count(shift) as shift, '".$emp_id."' as nik from presensi join ympimis.employees on employees.employee_id = presensi.nik
  where shift in ('S','I','A','CT') and nik = '".$emp_id."'
  and DATE_FORMAT(tanggal,'%Y-%m-%d') >= 
  IF(DATE_FORMAT(hire_date,'%m-%d') > DATE_FORMAT(now(),'%m-%d'), 
  DATE_FORMAT(hire_date, CONCAT(YEAR(now() - INTERVAL 1 YEAR),'-%m-01')),
  DATE_FORMAT(hire_date, CONCAT(YEAR(now()),'-%m-01')))
  ) presensi on employees.employee_id = presensi.nik where nik = '".$emp_id."') as sisa_cuti from 
  (select YEAR(now()) - YEAR(hire_date)
  - (DATE_FORMAT(now(), '%m%d') < DATE_FORMAT(hire_date, '%m%d')) as employeed, 0 as cuti from ympimis.employees where employee_id = '".$emp_id."') as emp
  join ympimis.leave_quotas on leave_quotas.employeed = emp.employeed");

$datas = db::select($query);

if($datas) {
  return view('employees.service.indexEmploymentService', array(
    'status' => true,
    'title' => $title,
    'title_jp' => $title_jp,
    'emp_id' => $emp_id,
    'profil' => $datas,
    'absences' => $absences,
    'sisa_cuti' => $ct
  ))->with('page', 'Employment Services');
} else {
  return view('home')->with('page', 'Dashboard');
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
    'title_jp' => '欠勤'))->with('page', 'Absence Data');
}

public function fetchAbsence(Request $request)
{
  if(strlen($request->get('tgl')) > 0){
    $tgl = date('d-m-Y',strtotime($request->get("tgl")));
  }else{
    $tgl = date("d-m-Y");
  }
  
  $query = "SELECT shift, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='".$tgl."' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' and shift <> 'OFF' and shift <> 'X' GROUP BY shift ORDER BY jml";

  $absence = db::connection('mysql3')->select($query);
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
//------------- End Absence ------

public function fetchAttendanceData(Request $request)
{
  $datas = "select employee_id, `name` from employees where (end_date is null or end_date >= '2019-09-01') and hire_date <= '2019-09-01'";

  $response = array(
    'status' => true,
    'datas' => $datas
  );
  return Response::json($response);
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
    if ($kz->status == '-1') {
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

  $dprt = db::select("select distinct section from mutation_logs where valid_to is null and department = (select department from mutation_logs where employee_id = '".$username."' and valid_to is null)");

  $kzn = KaizenForm::leftJoin('kaizen_scores','kaizen_forms.id','=','kaizen_scores.id_kaizen')
  ->select('kaizen_forms.id','employee_id','employee_name','title','area','section','propose_date','status','foreman_point_1','foreman_point_2', 'foreman_point_3', 'manager_point_1','manager_point_2', 'manager_point_3');
  if ($_GET['area'][0] != "") {
    $areas = implode("','", $_GET['area']);

    $kzn = $kzn->whereRaw('area in (\''.$areas.'\')');
  }

  if ($_GET['status'] != "") {
    if ($_GET['status'] == '1') {
      $kzn = $kzn->where('status','=', '-1');
    } else if ($_GET['status'] == '2') {
      $kzn = $kzn->where('manager_point_1','=', '0');
    } else if ($_GET['status'] == '3') {
      $kzn = $kzn->where('status','=', '1');
    } else if ($_GET['status'] == '4') {
      $kzn = $kzn->where('manager_point_1','<>', '0');
    } else if ($_GET['status'] == '5') {
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
      if ($_GET['position'] == 'Foreman' || $_GET['position'] == 'Manager' || $_GET['position'] == 'Chief'  || $_GET['position'] == 'Deputy General Manager' || Auth::id() == 53) {
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
    } else {
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
        if ($_GET['position'] == 'Manager' || $_GET['position'] == 'Deputy General Manager') {
          return '<a class="label bg-yellow btn" href="'.url("index/kaizen/detail/".$kzn->id."/manager").'">Unverified</a>';     
        } else {
          return '<span class="label bg-yellow"><i class="fa fa-hourglass-half"></i>&nbsp; Unverified</span>'; 
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

public function fetchDetailKaizen(Request $request)
{
  $data = KaizenForm::select("kaizen_forms.employee_id","employee_name", db::raw("date_format(propose_date,'%d-%b-%Y') as date"), "title", "condition", "improvement", "area", "leader", "purpose", "section", db::raw("name as leader_name"),'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3', 'kaizen_calculations.cost', 'standart_costs.cost_name', db::raw('kaizen_calculations.cost * standart_costs.cost as sub_total_cost'), 'frequency', 'unit',db::raw('standart_costs.cost as std_cost'))
  ->leftJoin('employees','employees.employee_id','=','kaizen_forms.leader')
  ->leftJoin('kaizen_calculations','kaizen_forms.id','=','kaizen_calculations.id_kaizen')
  ->leftJoin('standart_costs','standart_costs.id','=','kaizen_calculations.id_cost')
  ->leftJoin('kaizen_scores','kaizen_scores.id_kaizen','=','kaizen_forms.id')
  ->where('kaizen_forms.id','=',$request->get('id'))
  ->get();

  return Response::json($data);
}

public function assessKaizen(Request $request)
{
  $id = Auth::id();

  if ($request->get('category') == 'manager') { // --------------- JIKA inputor Manager ----
    try {
      $data = KaizenScore::where('id_kaizen','=' , $request->get('id'))
      ->first();
      
      $data->manager_point_1 = $request->get('nilai1');
      $data->manager_point_2 = $request->get('nilai2');
      $data->manager_point_3 = $request->get('nilai3');
      $data->save();

      return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head','Kaizen');

    } catch (QueryException $e) {
      return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
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

        return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head','Kaizen');

      } catch (QueryException $e) {
        return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head','Kaizen');
      }
    } else {
      // ----------------  JIKA KAIZEN false ------------
      try {
        $data = KaizenForm::where('id','=' , $request->get('id'))
        ->first();

        $data->status = 0;
        $data->save();

        return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed (NOT KAIZEN)')->with('page', 'Assess')->with('head','Kaizen');
      } catch (QueryException $e) {
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
  DB::enableQueryLog(); // Enable query log

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
    return ($kzn->foreman_point_1 * 40) + ($kzn->foreman_point_2 * 20) + ($kzn->foreman_point_3 * 20);
  })
  ->addColumn('mg_point', function($kzn){
    return ($kzn->manager_point_1 * 40) + ($kzn->manager_point_2 * 20) + ($kzn->manager_point_3 * 20);
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

  $chart1 = "select count(kaizen_forms.employee_id) as kaizen , mutation_logs.department, mutation_logs.section from kaizen_forms 
  left join mutation_logs on kaizen_forms.employee_id = mutation_logs.employee_id 
  where valid_to is null and DATE_FORMAT(propose_date,'%Y-%m') = '".$date."'
  group by mutation_logs.department, mutation_logs.section";

  $kz_total = db::select($chart1);

  $q_rank1 = "select kz.employee_id, employee_name, CONCAT(department,' - ', section,' - ', `group`) as bagian, mp1+mp2+mp3 as nilai from 
  (select employee_id, employee_name, SUM(manager_point_1 * 40) mp1, SUM(manager_point_2 * 30) mp2, SUM(manager_point_3 * 30) mp3 from kaizen_forms LEFT JOIN kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
  where DATE_FORMAT(propose_date,'%Y-%m') = '".$date."' and status = 1
  group by employee_id, employee_name
  ) as kz
  left join mutation_logs on kz.employee_id = mutation_logs.employee_id
  where valid_to is null
  order by (mp1+mp2+mp3) desc
  limit 3";

  $kz_rank1 = db::select($q_rank1);

  $q_rank2 = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ', mutation_logs.section,' - ', `group`) as bagian , COUNT(kaizen_forms.employee_id) as count from kaizen_forms left join mutation_logs on kaizen_forms.employee_id = mutation_logs.employee_id
  where `status` = 1 and valid_to is null and DATE_FORMAT(propose_date,'%Y-%m') = '".$date."'
  group by kaizen_forms.employee_id, employee_name, department, mutation_logs.section, `group`
  order by count desc
  limit 10";

  $kz_rank2 = db::select($q_rank2);

  $q_excellent = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ',mutation_logs.section,' - ',`group`) as bagian, title, (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) as  score from kaizen_forms 
  join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
  left join mutation_logs on kaizen_forms.employee_id = mutation_logs.employee_id
  where DATE_FORMAT(propose_date,'%Y-%m') = '".$date."' and (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) > 450
  and valid_to is null
  order by (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) desc";

  $kz_excellent = db::select($q_excellent);

  $response = array(
    'status' => true,
    'charts' => $kz_total,
    'rank1' => $kz_rank1,
    'rank2' => $kz_rank2,
    'excellent' => $kz_excellent,
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

public function fetchKaizenResume(Request $request)
{
  try {
    $q = "select leader, `name`, SUM(tot) as total_operator, SUM(kz_tot) as total_sudah, SUM(belum) as total_belum, SUM(kaizen_count) as total_kaizen from 
    (select leader.leader, count(leader.employee_id) as tot, count(kz.employee_id) as kz_tot, SUM(IF(kz.employee_id is null, 1, 0)) as belum, 0 as kaizen_count from
    (select employees.employee_id, emp.employee_id as leader from employees join employees emp on employees.direct_superior = emp.employee_id
    where employees.direct_superior is not null) leader left join
    (select employee_id, leader from kaizen_forms join 
    (select MIN(week_date) as min , MAX(week_date) as max from weekly_calendars where fiscal_year = 'FY196') dt on kaizen_forms.propose_date >= dt.min and kaizen_forms.propose_date <= dt.max
    group by employee_id, leader) kz on leader.employee_id = kz.employee_id 
    and leader.leader = kz.leader
    group by leader.leader

    union all

    select leader, 0 as tot, 0 as kz_tot, 0 as belum, count(employee_id) as kaizen_count from kaizen_forms join 
    (select MIN(week_date) as min , MAX(week_date) as max from weekly_calendars where fiscal_year = 'FY196') dt on kaizen_forms.propose_date >= dt.min and kaizen_forms.propose_date <= dt.max
    group by leader) as alls
    join employees on alls.leader = employees.employee_id
    group by leader, `name`";

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
    'message' => 'Success'
  );
  return Response::json($response);
}

public function updateKaizen(Request $request)
{
  try {
    $kz = KaizenForm::where('id',$request->get('id'))
    ->update([
      'leader' => $request->get('leader'),
      'title' => $request->get('title'),
      'condition' => $request->get('condition'),
      'improvement' => $request->get('improvement'),
      'area' => $request->get('area_kz'),
      'purpose' => $request->get('purpose')
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

public function deleteKaizen()
{
  KaizenForm::where('id',$request->get('id'))->delete();

  $response = array(
    'status' => true,
    'datas' => 'Data Berhasil dihapus'
  );
  return Response::json($response);
}

// public function fetchEmployee(Request $request)
// {
//   $username = Auth::user()->username;

//   $mstr = EmployeeSync::where('employee_id','=', $username)->select('sub_section')->first();

//   $datas = EmployeeSync::where('section','=', $mstr->sub_section)->select('employee_id','name')->get();

//   $response = array(
//     'status' => true,
//     'employees' => $datas,
//     'master' => $mstr
//   );
//   return Response::json($response);
// }

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
}
}

}