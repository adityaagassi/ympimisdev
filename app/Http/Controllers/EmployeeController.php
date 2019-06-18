<?php

namespace App\Http\Controllers;

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


class EmployeeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->keluarga = [
      '0',
      'K0',
      'K1',
      'K2',
      'K3',
      'Pk1',
      'Pk2',
      'Pk3',
      'Tk',
    ];

    $this->status = [
      'Percobaan',
      'PKWTT1',
      'PKWTT2',
      'PKWT'
    ];

  }
// master emp
  public function index(){
    return view('employees.master.index',array(
      'status' => $this->status ))->with('page', 'Master Employee');
  }

  public function indexTotalMeeting()
  {
    return view('employees.report.total_meeting')->with('page', 'Total Meeting');
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
      'position' => $position, 
      'keluarga' => $this->keluarga ))->with('page', 'Master Employee');
  }

  public function fetchMasterEmp(){
    $emp = "select employees.employee_id,name,division, department,DATE_FORMAT(hire_date,' %d %b %Y') hire_date, stat.status from employees
    LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id
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

      if ($masteremp->status != 'PKWT') {
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

    $detail ="select  employees.avatar,employees.direct_superior,employees.birth_place, DATE_FORMAT(employees.birth_date,' %d %b %Y') birth_date,employees.gender,employees.address,employees.family_id, DATE_FORMAT(employees.hire_date,' %d %b %Y') hire_date,employees.remark,employees.phone,employees.account,employees.card_id,employees.npwp,employees.bpjstk,employees.jp,employees.bpjskes,mutation_logs.division,mutation_logs.department,mutation_logs.section,mutation_logs.sub_section,mutation_logs.group,promotion_logs.grade_code,promotion_logs.position,promotion_logs.grade_name from employees
    LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id 
    LEFT JOIN promotion_logs on  employees.employee_id = promotion_logs.employee_id
    where mutation_logs.valid_to is null
    and employees.employee_id ='".$request->get('nik')."'
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

      if($request->hasFile('foto')){
        $files = $request->file('foto');
        foreach ($files as $file) 
        {
          $number= $request->get('nik');
          $data = file_get_contents($file);
          $photo_number = $number . $file->getClientOriginalName() ;
          $ext = $file->getClientOriginalExtension();
          $filepath = public_path() . "/uploads/employee_avatar/" . $photo_number;

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
            'hire_date' => $request->get('tglM'), 
            'avatar' => "/uploads/employee_avatar/".$photo_number, 
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
        'hire_date' => $request->get('tglM'), 
        'remark' => $request->get('pin'), 
        'created_by' => $id
      ]);

       $emp->save();
     }

       // --------------- Promotion Log insert

     $date = date('Y-m-d');
     $grade1 = $request->get('grade');
     $grade2 = explode("#", $grade1);
     $grade = new PromotionLog([
      'employee_id' => $request->get('nik'),
      'grade_code' => $grade2[0],
      'grade_name' => $grade2[1],
      'position' => $request->get('jabatan'),
      'valid_from' => $date,
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
       'valid_from' => $date,
       'created_by' => $id
     ]);

     $jabatan->save();

     return redirect('/index/insertEmp')->with('status', 'Input Employee success')->with('page', 'Master Employee');
   }
   catch (QueryException $e){
    return redirect('/index/insertEmp')->with('error', $e->getMessage())->with('page', 'Master Employee');
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
      $photo_number = $number . $file->getClientOriginalName() ;
      $ext = $file->getClientOriginalExtension();
      $filepath = public_path() . "/uploads/employee_avatar/" . $photo_number;

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
      $emp->avatar = "/uploads/employee_avatar/".$photo_number; 
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
  return view('employees.master.promotion')->with('page', 'Promotion');
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
  return view('employees.master.mutation')->with('page', 'Mutation');
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

    //end mutation_log

 // --------------------- Total Meeting Report -------------------------

public function indexReportGender()
{
  return view('employees.report.manpower_by_gender')->with('page', 'Manpower by Gender');
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

}
