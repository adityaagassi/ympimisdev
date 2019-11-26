<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditProcess;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AuditProcessController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->product = ['All',
      					'Saxophone',
                        'Flute',
                        'Clarinet',
                        'Venova',
                        'Recorder',
                        'Pianica'];
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$audit_process = AuditProcess::where('activity_list_id',$id)
            ->orderBy('audit_processes.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('audit_process' => $audit_process,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('audit_process.index', $data
    		)->with('page', 'Audit Process');
    }

    function filter_audit_process(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $audit_process = AuditProcess::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('audit_processes.id','desc')
                ->get();
        }
        else{
            $audit_process = AuditProcess::where('activity_list_id',$id)
            ->orderBy('audit_processes.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        // }
        $data = array(
                      'audit_process' => $audit_process,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('audit_process.index', $data
            )->with('page', 'Audit Process');
    }

    function show($id,$audit_process_id)
    {
        $activityList = ActivityList::find($id);
        $audit_process = AuditProcess::find($audit_process_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('audit_process' => $audit_process,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('audit_process.view', $data
            )->with('page', 'Audit Process');
    }

    public function destroy($id,$audit_process_id)
    {
      $audit_process = AuditProcess::find($audit_process_id);
      $audit_process->delete();

      return redirect('/index/audit_process/index/'.$id)
        ->with('status', 'Label has been deleted.')
        ->with('page', 'Audit Process');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $date = date('Y-m-d');

        $fyQuery = "SELECT DISTINCT(fiscal_year) FROM weekly_calendars where week_date = '".$date."'";
        $fyHasil = DB::select($fyQuery);

        foreach($fyHasil as $fyHasil){
        	$fy = $fyHasil->fiscal_year;
        }

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $queryAuditor = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where (mutation_logs.department = '".$departments."' and promotion_logs.position = 'Leader') or (mutation_logs.department = '".$departments."' and promotion_logs.position = 'Sub Leader')";
        $auditor = DB::select($queryAuditor);

        $data = array(
                      'product' => $this->product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'auditor' => $auditor,
                      'fy' => $fy,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('audit_process.create', $data
            )->with('page', 'Labeling');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();

            AuditProcess::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'product' => $request->input('product'),
                'periode' => $request->input('periode'),
                'date' => $request->input('date'),
                'proses' => $request->input('proses'),
                'operator' => $request->input('operator'),
                'auditor' => $request->input('auditor'),
                'cara_proses' => $request->input('cara_proses'),
                'kondisi_cara_proses' => $request->input('kondisi_cara_proses'),
                'pemahaman' => $request->input('pemahaman'),
                'kondisi_pemahaman' => $request->input('kondisi_pemahaman'),
                'keterangan' => $request->input('keterangan'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/audit_process/index/'.$id)
            ->with('page', 'Audit Process')->with('status', 'New Audit Process has been created.');
    }
}
