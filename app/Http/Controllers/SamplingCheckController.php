<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\SamplingCheck;
use App\SamplingCheckDetail;
use Response;
use DataTables;
use Excel;

class SamplingCheckController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$samplingCheck = SamplingCheck::where('activity_list_id',$id)
            ->get();


    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        // var_dump($productionAudit);
        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

    	$data = array('sampling_check' => $samplingCheck,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('sampling_check.index', $data
    		)->with('page', 'Sampling Check');
    }

    function filter_sampling(Request $request,$id)
    {
        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $sub_section = DB::select($querySubSection);

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            $month = $request->get('month');
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->where('month',$month)
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $month = $request->get('month');
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->where('month',$month)
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->get();
        }
        else{
            $samplingCheck = SamplingCheck::where('activity_list_id',$id)
                ->get();
        }
        $data = array(
                      'sampling_check' => $samplingCheck,
                      'subsection' => $sub_section,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('sampling_check.index', $data
            )->with('page', 'Sampling Check');
    }

    function show($id,$sampling_id)
    {
        $activityList = ActivityList::find($id);
        $samplingCheck = SamplingCheck::find($sampling_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('sampling_check' => $samplingCheck,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('sampling_check.view', $data
            )->with('page', 'Sampling Check');
    }

    public function destroy($id,$sampling_id)
    {
      $samplingCheck = SamplingCheck::find($sampling_id);
      $samplingCheck->delete();

      return redirect('/index/sampling_check/index/'.$id)
        ->with('status', 'Sampling Check has been deleted.')
        ->with('page', 'Sampling Check');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $data = array('product' => $product,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('sampling_check.create', $data
            )->with('page', 'Training Report');
    }

    function store(Request $request,$id)
    {
            $month = date("m",strtotime($request->input('date')));
            $id_user = Auth::id();
            SamplingCheck::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'month' => $month,
                'date' => $request->input('date'),
                'product' => $request->input('product'),
                'no_seri_part' => $request->input('no_seri_part'),
                'jumlah_cek' => $request->input('jumlah_cek'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/sampling_check/index/'.$id)
            ->with('page', 'Sampling Check')->with('status', 'New Sampling Check has been created.');
    }

    function edit($id,$sampling_check_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $sampling_check = SamplingCheck::find($sampling_check_id);

        $data = array('product' => $product,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_check' => $sampling_check,
                      'id' => $id);
        return view('sampling_check.edit', $data
            )->with('page', 'Sampling Check');
    }

    function update(Request $request,$id,$sampling_check_id)
    {
        try{
                $month = date("m",strtotime($request->get('date')));
                $sampling_check = SamplingCheck::find($sampling_check_id);
                $sampling_check->activity_list_id = $id;
                $sampling_check->department = $request->get('department');
                $sampling_check->section = $request->get('section');
                $sampling_check->product = $request->get('product');
                $sampling_check->month = $month;
                $sampling_check->subsection = $request->get('subsection');
                $sampling_check->date = $request->get('date');
                $sampling_check->no_seri_part = $request->get('no_seri_part');
                $sampling_check->jumlah_cek = $request->get('jumlah_cek');
                $sampling_check->leader = $request->get('leader');
                $sampling_check->foreman = $request->get('foreman');
                $sampling_check->save();

            return redirect('/index/sampling_check/index/'.$id)->with('status', 'Sampling Check data has been updated.')->with('page', 'Sampling Check');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Sampling Check already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Sampling Check');
            }
          }
    }

    function details($sampling_id)
    {
        $samplingCheckDetails = SamplingCheckDetail::where('sampling_check_id',$sampling_id)
            ->get();

        $samplingCheck = SamplingCheck::find($sampling_id);

        $activity_name = $samplingCheck->activity_lists->activity_name;
        $departments = $samplingCheck->activity_lists->departments->department_name;
        $id_departments = $samplingCheck->activity_lists->departments->id;
        $activity_alias = $samplingCheck->activity_lists->activity_alias;
        $activity_id = $samplingCheck->activity_lists->id;

        $data = array('sampling_check_details' => $samplingCheckDetails,
                      'sampling_check' => $samplingCheck,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'sampling_id' => $sampling_id,
                      'activity_id' => $activity_id,
                      'id_departments' => $id_departments);
        return view('sampling_check.details', $data
            )->with('page', 'Sampling Check Details');
    }

    public function destroydetails($id,$sampling_check_details_id)
    {
      $SamplingCheckDetail = SamplingCheckDetail::find($sampling_check_details_id);
      $SamplingCheckDetail->delete();

      return redirect('/index/sampling_check/details/'.$id)
        ->with('status', 'Sampling Check Details has been deleted.')
        ->with('page', 'Sampling Check Details');
        //
    }

    function createdetails($sampling_id)
    {
        $samplingCheck = SamplingCheck::find($sampling_id);
        // var_dump($samplingCheck);
        // $activityList = ActivityList::find($id);

        $activity_name = $samplingCheck->activity_lists->activity_name;
        $departments = $samplingCheck->activity_lists->departments->department_name;
        $id_departments = $samplingCheck->activity_lists->departments->id;
        $activity_alias = $samplingCheck->activity_lists->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $data = array(
                      'operator' => $operator,
                      'samplingCheck' => $samplingCheck,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_id' => $sampling_id);
        return view('sampling_check.createdetails', $data
            )->with('page', 'Sampling Check');
    }

    function storedetails(Request $request,$sampling_id)
    {
            // $month = date("m",strtotime($request->input('date')));
            $tujuan_upload = 'data_file/sampling_check';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $file->getClientOriginalName();
            $file->move($tujuan_upload,$file->getClientOriginalName());

            $id_user = Auth::id();
            SamplingCheckDetail::create([
                'sampling_check_id' => $sampling_id,
                'point_check' => $request->input('point_check'),
                'hasil_check' => $request->input('hasil_check'),
                'picture_check' => $nama_file,
                'pic_check' => $request->input('pic_check'),
                'sampling_by' => $request->input('sampling_by'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/sampling_check/details/'.$sampling_id)
            ->with('page', 'Sampling Check')->with('status', 'New Sampling Check Details has been created.');
    }

    function editdetails($id,$sampling_check_details_id)
    {
        $sampling_check = SamplingCheck::find($id);
        // var_dump($samplingCheck);
        // $activityList = ActivityList::find($id);

        $activity_name = $sampling_check->activity_lists->activity_name;
        $departments = $sampling_check->activity_lists->departments->department_name;
        $id_departments = $sampling_check->activity_lists->departments->id;
        $activity_alias = $sampling_check->activity_lists->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";

        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $samplingCheckDetails = SamplingCheckDetail::find($sampling_check_details_id);

        $data = array(
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'operator' => $operator,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'sampling_check' => $sampling_check,
                      'sampling_check_details' => $samplingCheckDetails,
                      'sampling_check_details_id' => $sampling_check_details_id,
                      'id' => $id);
        return view('sampling_check.editdetails', $data
            )->with('page', 'Sampling Check');
    }

    function updatedetails(Request $request,$id,$sampling_check_details_id)
    {
        try{
            $tujuan_upload = 'data_file/sampling_check';
            $date = date('Y-m-d');

            if ($request->file('file') != null) {
                $file = $request->file('file');
                $nama_file = $file->getClientOriginalName();
                $file->getClientOriginalName();
                $file->move($tujuan_upload,$file->getClientOriginalName());

                $sampling_check_details = SamplingCheckDetail::find($sampling_check_details_id);
                $sampling_check_details->sampling_check_id = $id;
                $sampling_check_details->point_check = $request->get('point_check');
                $sampling_check_details->hasil_check = $request->get('hasil_check');
                $sampling_check_details->picture_check = $nama_file;
                $sampling_check_details->pic_check = $request->get('pic_check');
                $sampling_check_details->sampling_by = $request->get('sampling_by');
                $sampling_check_details->save();
            }
            else{
                $sampling_check_details = SamplingCheckDetail::find($sampling_check_details_id);
                $sampling_check_details->sampling_check_id = $id;
                $sampling_check_details->point_check = $request->get('point_check');
                $sampling_check_details->hasil_check = $request->get('hasil_check');
                $sampling_check_details->picture_check = $request->get('picture_check');;
                $sampling_check_details->pic_check = $request->get('pic_check');
                $sampling_check_details->sampling_by = $request->get('sampling_by');
                $sampling_check_details->save();
            }

            return redirect('/index/sampling_check/details/'.$id)->with('status', 'Sampling Check Details has been updated.')->with('page', 'Sampling Check');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Sampling Check Details already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Sampling Check');
            }
          }
    }

    function report_sampling_check($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('sampling_check.report_sampling_check',  array('title' => 'Report Sampling Check',
            'title_jp' => 'Report Sampling Check',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Sampling Check');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select week_date, count(*) as jumlah_sampling_check from weekly_calendars join sampling_checks on sampling_checks.date = weekly_calendars.week_date join activity_lists on activity_lists.id = sampling_checks.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."' and sampling_checks.deleted_at is null GROUP BY week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detail_sampling_check(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *, sampling_checks.id as sampling_check_id from sampling_checks join activity_lists on activity_lists.id = sampling_checks.activity_list_id where department_id = '".$id."' and activity_type = 'Sampling Check' and date = '".$week_date."' and sampling_checks.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    function print_sampling(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($request->get('subsection') != null && $request->get('month') != null){
            $subsection = $request->get('subsection');
            $month = $request->get('month');
            $querySamplingCheck = "select *
                from sampling_checks
                join sampling_check_details on sampling_checks.id = sampling_check_details.sampling_check_id
                join activity_lists on activity_lists.id = sampling_checks.activity_list_id
                where activity_lists.department_id = '".$id_departments."'
                and sampling_checks.subsection = '".$subsection."' 
                and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."' 
                and sampling_checks.deleted_at is null";
            $samplingCheck = DB::select($querySamplingCheck);
            $samplingCheck2 = DB::select($querySamplingCheck);
        }
        // var_dump($subsection);

        foreach($samplingCheck2 as $samplingCheck2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $samplingCheck2->date;
            $foreman = $samplingCheck2->foreman;
            $section = $samplingCheck2->section;
            $subsection = $samplingCheck2->subsection;
            $month = $samplingCheck2->month;
            $leader = $samplingCheck2->leader;
        }
        if($samplingCheck == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'month' => $month,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'subsection' => $subsection,
                          'month' => $month,
                          'date' => $date,
                          'samplingCheck' => $samplingCheck,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('sampling_check.print', $data
                )->with('page', 'Sampling Check');
        }
    }
}
