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
use App\WeeklyCalendar;
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
            $week = WeeklyCalendar::where('week_date',$request->get('date'))->get();
            foreach($week as $week){
                $week_name = $week->week_name;
            }

            AuditProcess::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'product' => $request->input('product'),
                'periode' => $request->input('periode'),
                'date' => $request->input('date'),
                'week_name' => $week_name,
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

    function edit($id,$audit_process_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
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

        $audit_process = AuditProcess::find($audit_process_id);

        $data = array(
                       'product' => $this->product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'auditor' => $auditor,
                      'fy' => $fy,
                      'activity_name' => $activity_name,
                      'audit_process' => $audit_process,
                      'id' => $id);
        return view('audit_process.edit', $data
            )->with('page', 'Audit Process');
    }

    function update(Request $request,$id,$audit_process_id)
    {
        try{
                $audit_process = AuditProcess::find($audit_process_id);
                $audit_process->activity_list_id = $id;
                $audit_process->department = $request->get('department');
                $audit_process->section = $request->get('section');
                $audit_process->product = $request->get('product');
                $audit_process->periode = $request->get('periode');
                $audit_process->proses = $request->get('proses');
                $audit_process->operator = $request->get('operator');
                $audit_process->auditor = $request->get('auditor');
                $audit_process->cara_proses = $request->get('cara_proses');
                $audit_process->kondisi_cara_proses = $request->get('kondisi_cara_proses');
                $audit_process->pemahaman = $request->get('pemahaman');
                $audit_process->kondisi_pemahaman = $request->get('kondisi_pemahaman');
                $audit_process->keterangan = $request->get('keterangan');
                $audit_process->leader = $request->get('leader');
                $audit_process->foreman = $request->get('foreman');
                $audit_process->save();

            return redirect('/index/audit_process/index/'.$id)->with('status', 'Audit Process data has been updated.')->with('page', 'Audit Process');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Audit Process already exist.')->with('page', 'Audit Process');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Audit Process');
            }
          }
    }

    function print_audit_process(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        if($request->get('month') != null){
            $month = $request->get('month');
            $queryaudit_process = "select *, audit_processes.id as id_audit_process
                from audit_processes
                join activity_lists on activity_lists.id = audit_processes.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."' 
                and audit_processes.deleted_at is null";
            $audit_process = DB::select($queryaudit_process);
            $audit_process2 = DB::select($queryaudit_process);
        }
        $monthTitle = date("F Y", strtotime($month));
        $jml_null = 0;
        foreach($audit_process2 as $audit_process2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $audit_process2->date;
            $foreman = $audit_process2->foreman;
            $section = $audit_process2->section;
            $product = $audit_process2->product;
            $periode = $audit_process2->periode;
            $leader = $audit_process2->leader;
            if ($audit_process2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $audit_process2->approved_date;
        }
        if($audit_process == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'monthTitle' => $monthTitle,
                          'month' => $month,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'product' => $product,
                          'periode' => $periode,
                          'date' => $date,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'audit_process' => $audit_process,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_process.print', $data
                )->with('page', 'Audit Process');
        }
    }

    function print_audit_process_email($id,$month)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

            $queryaudit_process = "select *, audit_processes.id as id_audit_process
                from audit_processes
                join activity_lists on activity_lists.id = audit_processes.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."' 
                and audit_processes.deleted_at is null";
            $audit_process = DB::select($queryaudit_process);
            $audit_process2 = DB::select($queryaudit_process);
        $monthTitle = date("F Y", strtotime($month));
        $jml_null = 0;
        foreach($audit_process2 as $audit_process2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $audit_process2->date;
            $foreman = $audit_process2->foreman;
            $section = $audit_process2->section;
            $product = $audit_process2->product;
            $periode = $audit_process2->periode;
            $leader = $audit_process2->leader;
            if ($audit_process2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $audit_process2->approved_date;
        }
        if($audit_process == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'month' => $month,
                          'monthTitle' => $monthTitle,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'product' => $product,
                          'role_code' => Auth::user()->role_code,
                          'periode' => $periode,
                          'date' => $date,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'audit_process' => $audit_process,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_process.print_email', $data
                )->with('page', 'Audit Process');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_audit_process = "select *, audit_processes.id as id_audit_process,DATE_FORMAT(audit_processes.date,'%Y-%m') as month
            from audit_processes
            join activity_lists on activity_lists.id =  audit_processes.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
            and activity_list_id = '".$id."'
            and audit_processes.deleted_at is null";
          $audit_process = DB::select($query_audit_process);
          $audit_process2 = DB::select($query_audit_process);
          $audit_process3 = DB::select($query_audit_process);

          // var_dump($sampling_check2);

          if($audit_process2 != null){
            foreach($audit_process2 as $audit_process2){
                $foreman = $audit_process2->foreman;
                $id_audit_process = $audit_process2->id_audit_process;
                $send_status = $audit_process2->send_status;
              }

              foreach ($audit_process3 as $audit_process3) {
                    $auditprocess = AuditProcess::find($audit_process3->id_audit_process);
                    $auditprocess->send_status = "Sent";
                    $auditprocess->send_date = date('Y-m-d');
                    $auditprocess->save();
              }

              $queryEmail = "select employees.employee_id,employees.name,email from users join employees on employees.employee_id = users.username where employees.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/audit_process/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Audit Process');
          }

          if($send_status == "Sent"){
            return redirect('/index/audit_process/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Audit Process');
          }
          elseif($audit_process != null){
              Mail::to($mail_to)->send(new SendEmail($audit_process, 'audit_process'));
              return redirect('/index/audit_process/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Audit Process');
          }
          else{
            return redirect('/index/audit_process/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Audit Process');
          }
      }

      public function approval(Request $request,$id,$month)
      {
          $approve = $request->get('approve');
          if(count($approve) > 0){
            foreach($approve as $approve){
                $audit_process = AuditProcess::find($approve);
                $date = $audit_process->date;
                $audit_process->approval = "Approved";
                $audit_process->approved_date = date('Y-m-d');
                $audit_process->save();
              }
              return redirect('/index/audit_process/print_audit_process_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Audit Process');
          }
          else{
            return redirect('/index/audit_process/print_audit_process_email/'.$id.'/'.$month)->with('error', 'Not Approved.')->with('page', 'Audit Process');
          }
      }
}
