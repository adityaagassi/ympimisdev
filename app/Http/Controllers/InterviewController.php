<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Interview;
use App\InterviewDetail;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$interview = Interview::where('activity_list_id',$id)
            ->orderBy('interviews.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);
        $subsection4 = DB::select($querySubSection);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        // var_dump($productionAudit);
    	$data = array('interview' => $interview,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
    				  'departments' => $departments,
    				  'leader' => $leader,
                      'foreman' => $foreman,
                      'section' => $section,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('interview.index', $data
    		)->with('page', 'Interview');
    }

    function filter_interview(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);
        $subsection4 = DB::select($querySubSection);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            // $date = date('Y-m',$request->get('month'));
            // $year = date_format($date, 'Y');
            // $month = date_format($date, 'm');
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $interview = Interview::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $interview = Interview::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $interview = Interview::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        else{
            $interview = Interview::where('activity_list_id',$id)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        $data = array(
                      'interview' => $interview,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
                      'departments' => $departments,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'section' => $section,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('interview.index', $data
            )->with('page', 'Interview');
    }

    function show($id,$interview_id)
    {
        $activityList = ActivityList::find($id);
        $interview = Interview::find($interview_id);
        
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        $data = array('interview' => $interview,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.view', $data
            )->with('page', 'Interview');
    }

    public function destroy($id,$interview_id)
    {
      $interview = Interview::find($interview_id);
      $interview->delete();

      return redirect('/index/interview/index/'.$id)
        ->with('status', 'Interview has been deleted.')
        ->with('page', 'Interview');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryFY = "select DISTINCT(fiscal_year)from weekly_calendars";
        $fy = DB::select($queryFY);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'fy' => $fy,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.create', $data
            )->with('page', 'Interview');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            Interview::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'date' => $request->input('date'),
                'periode' => $request->input('periode'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/interview/index/'.$id)
            ->with('page', 'Interview')->with('status', 'New Interview has been created.');
    }

    function edit($id,$interview_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryFY = "select DISTINCT(fiscal_year)from weekly_calendars";
        $fy = DB::select($queryFY);

        $interview = Interview::find($interview_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'fy' => $fy,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'interview' => $interview,
                      'id' => $id);
        return view('interview.edit', $data
            )->with('page', 'Interview');
    }

    function update(Request $request,$id,$interview_id)
    {
        try{
                $month = date("m",strtotime($request->get('date')));
                $interview = Interview::find($interview_id);
                $interview->activity_list_id = $id;
                $interview->department = $request->get('department');
                $interview->section = $request->get('section');
                $interview->subsection = $request->get('subsection');
                $interview->date = $request->get('date');
                $interview->periode = $request->get('periode');
                $interview->leader = $request->get('leader');
                $interview->foreman = $request->get('foreman');
                $interview->save();

            return redirect('/index/interview/index/'.$id)->with('status', 'Interview data has been updated.')->with('page', 'Interview');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Interview already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    function details($interview_id)
    {
        $interview_detail = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_detail2 = InterviewDetail::where('interview_id',$interview_id)
            ->get();

        $interview = Interview::find($interview_id);

        $activity_name = $interview->activity_lists->activity_name;
        $departments = $interview->activity_lists->departments->department_name;
        $id_departments = $interview->activity_lists->departments->id;
        $activity_alias = $interview->activity_lists->activity_alias;
        $activity_id = $interview->activity_lists->id;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $data = array('interview_detail' => $interview_detail,
        			  'interview_detail2' => $interview_detail2,
                      'interview' => $interview,
                      'departments' => $departments,
                      'operator' => $operator,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'interview_id' => $interview_id,
                      'activity_id' => $activity_id,
                      'id_departments' => $id_departments);
        return view('interview.details', $data
            )->with('page', 'Interview Details');
    }

    function create_participant(Request $request,$interview_id)
    {
            $id_user = Auth::id();
            InterviewDetail::create([
                'interview_id' => $interview_id,
                'nik' => $request->input('nik'),
                'filosofi_yamaha' => $request->input('filosofi_yamaha'),
                'aturan_k3' => $request->input('aturan_k3'),
                'komitmen_berkendara' => $request->input('komitmen_berkendara'),
                'kebijakan_mutu' => $request->input('kebijakan_mutu'),
                'dasar_tindakan_bekerja' => $request->input('dasar_tindakan_bekerja'),
                'enam_pasal_keselamatan' => $request->input('enam_pasal_keselamatan'),
                'budaya_kerja' => $request->input('budaya_kerja'),
                'budaya_5s' => $request->input('budaya_5s'),
                'komitmen_hotel_konsep' => $request->input('komitmen_hotel_konsep'),
                'janji_tindakan_dasar' => $request->input('janji_tindakan_dasar'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/interview/details/'.$interview_id)
            ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
    }
}
