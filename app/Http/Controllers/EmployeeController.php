<?php

namespace App\Http\Controllers;

use Excel;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Employee;
use App\EmploymentLog;
use App\OrganizationStructure;
use File;
use DataTables;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;


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

  public function fetchMasterEmp(){
    $emp = "select employees.employee_id,name,division, department,DATE_FORMAT(hire_date,' %d %b %Y') hire_date, stat.status from employees
    LEFT JOIN (select employee_id, division, department from mutation_logs where valid_to is null group by employee_id, division, department) mutation_logs on employees.employee_id = mutation_logs.employee_id
    left join (
    select employee_id, status from employment_logs 
    WHERE id IN (
    SELECT MAX(id)
    FROM employment_logs
    GROUP BY employment_logs.employee_id
    )
    ) stat on stat.employee_id = employees.employee_id
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

  $pos = Position::get();
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
  $cc = CostCenter::get();
  
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
    'title_jp' => '??'
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

public function fetchReportGender2()
{
  $gender = "select gender, count(employee_id) as jml from employees where end_date is null group by gender";

  $get_manpower = db::select($gender);

  $response = array(
    'status' => true,
    'manpower_by_gender' => $get_manpower,
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
public function indexEmployeeService()
{
  $title = 'Employment Services';
  $title_jp = '???';
  $emp_id = Auth::user()->username;
  $status = true;
  
  $query = "select employees.employee_id, employees.name,  DATE_FORMAT(employees.hire_date, '%d %M %Y') hire_date, employees.direct_superior, emp_log.`status`, mut_log.division, mut_log.department, mut_log.section, mut_log.sub_section, mut_log.`group`, mut_log.cost_center, promot_log.grade_code, promot_log.grade_name, promot_log.position from employees 
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

  $absence = "select nik, tanggal, sum(if(shift = 1,1,0)) as shift1, sum(if(shift = "-",1,0)) as kosong from ftm.presensi where nik = '19014987'
  group by DATE_FORMAT(tanggal,'%Y-%m')";

  $absences = db::select($absence);

  $datas = db::select($query);

  if ($datas == null) {
   return view('home', array(
    'employee_service' => 'Belum Terdaftar'
  ))->with('page', 'Dashboard');
 }

 return view('employees.service.indexEmploymentService', array(
  'status' => $status,
  'title' => $title,
  'title_jp' => $title_jp,
  'emp_id' => $emp_id,
  'profil' => $datas
))->with('page', 'Employment Service');
}
// -------------------------  End Employee Service --------------------

public function indexReportStatus()
{
  return view('employees.report.employee_status',  array(
    'title' => 'Report Employee by Status Kerja',
    'title_jp' => '??'
  ))->with('page', 'Manpower by Status Kerja');
}

public function indexReportGrade()
{
  return view('employees.report.employee_status',  array(
    'title' => 'Report Employee by Grade',
    'title_jp' => '??'
  ))->with('page', 'Manpower by Grade');
}

public function indexReportDepartment()
{
  return view('employees.report.employee_status',  array(
    'title' => 'Report Employee by Department',
    'title_jp' => '??'
  ))->with('page', 'Manpower by Department');
}

public function indexReportJabatan()
{
  return view('employees.report.employee_status',  array(
    'title' => 'Report Employee by Jabatan',
    'title_jp' => '??'
  ))->with('page', 'Manpower by jabatan');
}

public function fetchReport(Request $request)
{
  if ($request->get("ctg") == 'Report Employee by Status Kerja') {
   $emp = Employee::leftJoin(db::raw("(select employee_id, status from employment_logs where valid_to is null) as emp_log"),"emp_log.employee_id","=","employees.employee_id")
   ->whereNull("end_date")
   ->select("status", db::raw("count(employees.employee_id) as jml"))
   ->groupBy("emp_log.status")
   ->get();
 } 
 else if ($request->get("ctg") == 'Report Employee by Grade') 
 {
  $emp = Employee::leftJoin(db::raw("(select employee_id, grade_code as status from promotion_logs where valid_to is null) as emp_log"),"emp_log.employee_id","=","employees.employee_id")
  ->whereNull("end_date")
  ->select("status", db::raw("count(employees.employee_id) as jml"))
  ->groupBy("emp_log.status")
  ->orderBy(db::raw('FIELD(emp_log.status, "E0", "E1", "E2","E3", "E4", "E5","E6", "E7", "E8","L1", "L2", "L3","L4", "M1", "M2","M3", "M4","D3")'))
  ->get();
}
else if ($request->get("ctg") == 'Report Employee by Department') {
  $emp = Employee::leftJoin(db::raw("(select employee_id, department as status from mutation_logs where valid_to is null) as emp_log"),"emp_log.employee_id","=","employees.employee_id")
  ->whereNull("end_date")
  ->select("status", db::raw("count(employees.employee_id) as jml"))
  ->groupBy("emp_log.status")
  ->orderBy("jml","asc")
  ->get();
} else if ($request->get("ctg") == 'Report Employee by Jabatan') {
  $emp = Employee::leftJoin(db::raw("(select employee_id, position as status from promotion_logs where valid_to is null and position <> '-') as emp_log"),"emp_log.employee_id","=","employees.employee_id")
  ->whereNull("end_date")
  ->whereNotNull("status")
  ->select("status", db::raw("count(employees.employee_id) as jml"))
  ->groupBy("emp_log.status")
  ->orderBy("jml","asc")
  ->get();
}

$response = array(
  'status' => true,
  'datas' => $emp,
);

return Response::json($response); 
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
      
      $date_to = date("Y-m-d",strtotime('-1 day', strtotime($row[7])));
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

}
