<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\FirstProductAudit;
use App\FirstProductAuditDetail;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class FirstProductAuditController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->proses = ['Pengerjaan Kunci Sub Assy',];
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$first_product_audit = FirstProductAudit::where('activity_list_id',$id)
            ->orderBy('first_product_audits.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('first_product_audit' => $first_product_audit,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('first_product_audit.index', $data
    		)->with('page', 'First Product Audit');
    }

    function list_proses($id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit = FirstProductAudit::where('activity_list_id',$id)
            ->orderBy('first_product_audits.id','desc')->get();

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $data = array('first_product_audit' => $first_product_audit,
                'departments' => $departments,
                'activity_name' => $activity_name,
                'activity_alias' => $activity_alias,
                'leader' => $leader,
                'foreman' => $foreman,
                'id' => $id,
                'id_departments' => $id_departments);
        return view('first_product_audit.list_proses', $data
          )->with('page', 'First Product Audit');
    }

    function show($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        $data = array('first_product_audit' => $first_product_audit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('first_product_audit.view', $data
            )->with('page', 'First Product Audit');
    }

    public function destroy($id,$first_product_audit_id)
    {
      $first_product_audit = FirstProductAudit::find($first_product_audit_id);
      $first_product_audit->delete();

      return redirect('/index/first_product_audit/index/'.$id)
        ->with('status', 'First Product Audit has been deleted.')
        ->with('page', 'First Product Audit');        
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

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('first_product_audit.create', $data
            )->with('page', 'First Product Audit');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            FirstProductAudit::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'subsection' => $request->input('subsection'),
                'proses' => $request->input('proses'),
                'jenis' => $request->input('jenis'),
                'standar_kualitas' => $request->input('standar_kualitas'),
                'tool_check' => $request->input('tool_check'),
                'jumlah_cek' => $request->input('jumlah_cek'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/first_product_audit/list_proses/'.$id)
            ->with('page', 'First Product Audit')->with('status', 'New First Product Audit has been created.');
    }

    function edit($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'first_product_audit' => $first_product_audit,
                      'id' => $id);
        return view('first_product_audit.edit', $data
            )->with('page', 'First Product Audit');
    }

    function update(Request $request,$id,$first_product_audit_id)
    {
        try{
                $first_product_audit = FirstProductAudit::find($first_product_audit_id);
                $first_product_audit->activity_list_id = $id;
                $first_product_audit->department = $request->get('department');
                $first_product_audit->subsection = $request->get('subsection');
                $first_product_audit->proses = $request->get('proses');
                $first_product_audit->jenis = $request->get('jenis');
                $first_product_audit->standar_kualitas = $request->get('standar_kualitas');
                $first_product_audit->tool_check = $request->get('tool_check');
                $first_product_audit->jumlah_cek = $request->get('jumlah_cek');
                $first_product_audit->leader = $request->get('leader');
                $first_product_audit->foreman = $request->get('foreman');
                $first_product_audit->save();

            return redirect('/index/first_product_audit/list_proses/'.$id)->with('status', 'First Product Audit data has been updated.')->with('page', 'First Product Audit');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'First Product Audit already exist.')->with('page', 'First Product Audit');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'First Product Audit');
            }
          }
    }

    function details($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
            ->where('first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_details.id','desc')->get();

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        $proses = $first_product_audit->proses;
        $jenis = $first_product_audit->jenis;

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $data = array( 'first_product_audit_details' => $first_product_audit_details,
                        'departments' => $departments,
                        'activity_name' => $activity_name,
                        'activity_alias' => $activity_alias,
                        'leader' => $leader,
                        'foreman' => $foreman,
                        'operator' => $operator,
                        'proses' => $proses,
                        'jenis' => $jenis,
                        'id' => $id,
                        'first_product_audit_id' => $first_product_audit_id,
                        'id_departments' => $id_departments);
        return view('first_product_audit.index', $data
          )->with('page', 'First Product Audit Detail');
    }

    function filter_first_product_detail(Request $request,$id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
                ->where('first_product_audit_details.first_product_audit_id',$first_product_audit_id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('first_product_audit_details.id','desc')
                ->get();
        }
        else{
            $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
            ->where('first_product_audit_details.first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_details.id','desc')->get();
        }

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        $proses = $first_product_audit->proses;
        $jenis = $first_product_audit->jenis;

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $data = array(
                      'first_product_audit_details' => $first_product_audit_details,
                      'departments' => $departments,
                      'proses' => $proses,
                      'jenis' => $jenis,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'id' => $id,
                      'first_product_audit_id' => $first_product_audit_id,
                      'id_departments' => $id_departments);
        return view('first_product_audit.index', $data
            )->with('page', 'First Product Audit');
    }

    function create_details($id)
    {
        $first_product_audit = FirstProductAudit::find($id);
        $proses = $first_product_audit->proses;
        $activity_list_id = $first_product_audit->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'first_product_audit' => $first_product_audit,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('first_product_audit.create_details', $data
            )->with('page', 'First Product Audit');
    }

    function store_details(Request $request,$id)
    {
            $month = date("m",strtotime($request->input('date')));
            $week = WeeklyCalendar::where('week_date',$request->get('date'))->get();
            foreach($week as $week){
                $week_name = $week->week_name;
            }
            $id_user = Auth::id();
            SamplingCheck::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'month' => $month,
                'date' => $request->input('date'),
                'week_name' => $week_name,
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
}
