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

class SamplingCheckController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$samplingCheck = SamplingCheck::where('activity_list_id','0')
            ->get();


    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        // var_dump($productionAudit);
        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);

    	$data = array('sampling_check' => $samplingCheck,
                      'subsection' => $subsection,
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
}
