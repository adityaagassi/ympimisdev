<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\WeeklyActivityReport;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class WeeklyActivityReportController extends Controller
{

	public function __construct()
    {
      $this->middleware('auth');
      $this->report_type = [
                        'Man',
                        'Material',
                        'Methode',
                        'Machine'];
    
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$weekly_report = WeeklyActivityReport::where('activity_list_id',$id)->orderBy('weekly_activity_reports.id','desc')
            ->get();

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        // var_dump($productionAudit);
        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

    	$data = array('weekly_report' => $weekly_report,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
            				  'departments' => $departments,
            				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'report_type' => $this->report_type,
                      'report_type2' => $this->report_type,
            				  'id' => $id,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id_departments' => $id_departments);
    	return view('weekly_report.index', $data
    		)->with('page', 'Weekly Activity Report');
    }

    function filter_weekly_report(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $weekly_report = WeeklyActivityReport::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('weekly_activity_reports.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $weekly_report = WeeklyActivityReport::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('weekly_activity_reports.id','desc')
                ->get();
        }
        elseif(strlen($request->get('month')) == null){
            $weekly_report = WeeklyActivityReport::where('activity_list_id',$id)
                ->orderBy('weekly_activity_reports.id','desc')
                ->get();
        }
        else{
            $weekly_report = WeeklyActivityReport::where('activity_list_id',$id)
                ->orderBy('weekly_activity_reports.id','desc')
                ->get();
        }
        $data = array(
                      'weekly_report' => $weekly_report,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'report_type' => $this->report_type,
                      'report_type2' => $this->report_type,
                      'id' => $id,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id_departments' => $id_departments);
        return view('weekly_report.index', $data
            )->with('page', 'Weekly Report');
    }

    public function destroy($id,$weekly_report_id)
    {
      $weekly_report = WeeklyActivityReport::find($weekly_report_id);
      $weekly_report->delete();

      return redirect('/index/weekly_report/index/'.$id)
        ->with('status', 'Weekly Report has been deleted.')
        ->with('page', 'Weekly Report');        
    }

    function store(Request $request,$id)
    {
        	try{    
              $id_user = Auth::id();
              $date = $request->get('date');
              $week = WeeklyCalendar::where('week_date',$date)->get();
              foreach($week as $week){
                  $week_name = $week->week_name;
              }

                WeeklyActivityReport::create([
                    'activity_list_id' => $id,
                    'department' => $request->get('department'),
                    'subsection' => $request->get('subsection'),
                    'date' => $request->get('date'),
                    'week_name' => $week_name,
                    'report_type' => $request->get('report_type'),
                    'problem' => $request->get('problem'),
                    'action' => $request->get('action'),
                    'kondisi' => $request->get('kondisi'),
                    'foto_aktual' => $request->get('foto_aktual'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function getweeklyreport(Request $request)
    {
          try{
            $detail = WeeklyActivityReport::find($request->get("id"));
            $data = array('weekly_report_id' => $detail->id,
                          'department' => $detail->department,
                          'subsection' => $detail->subsection,
                          'date' => $detail->date,
                          'week_name' => $detail->week_name,
                          'report_type' => $detail->report_type,
                          'problem' => $detail->problem,
                          'action' => $detail->action,
                          'foto_aktual' => $detail->foto_aktual);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function update(Request $request,$id)
    {
        try{
                $weekly_report = WeeklyActivityReport::find($id);
                $weekly_report->department = $request->get('department');
                $weekly_report->subsection = $request->get('subsection');
                $weekly_report->date = $request->get('date');
                $weekly_report->report_type = $request->get('report_type');
                $weekly_report->problem = $request->get('problem');
                $weekly_report->foto_aktual = $request->get('foto_aktual');
                $weekly_report->action = $request->get('action');
                $weekly_report->save();

            // return redirect('index/interview/details/'.$interview_id)
            //   ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
               $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function print_weekly_report($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $weekly_report_query = "
        select * from weekly_activity_reports
          JOIN activity_lists on activity_lists.id = weekly_activity_reports.activity_list_id
          where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and weekly_activity_reports.deleted_at is null";
        $weekly_report = DB::select($weekly_report_query);
        $weekly_report2 = DB::select($weekly_report_query);

        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($weekly_report2 as $weekly_report2){
          $subsection = $weekly_report2->subsection;
          $leader = $weekly_report2->leader_dept;
          $foreman = $weekly_report2->foreman_dept;
          if($weekly_report2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($weekly_report2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $weekly_report2->approved_date;
          $approved_date_leader = $weekly_report2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($weekly_report == null){
            return redirect('/index/weekly_report/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Weekly Activity Report');
        }else{
            $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'landscape');

           $pdf->loadView('weekly_report.print', array(
               'subsection' => $subsection,
                'weekly_report' => $weekly_report,
                'activityList' => $activityList,
                'departments' => $departments,
                'activity_name' => $activity_name,
                'activity_alias' => $activity_alias,
                'id' => $id,
                'role_code' => Auth::user()->role_code,
                'id_departments' => $id_departments,
                'monthTitle' => $monthTitle,
                'month' => $month,
                'leader' => $leader,
                'jml_null' => $jml_null,
                'jml_null_leader' => $jml_null_leader,
                'approved_date' => $approved_date,
                'approved_date_leader' => $approved_date_leader,
                'foreman' => $foreman,
           ));

           return $pdf->stream("Weekly Report ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_weekly_report_email($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $weekly_report_query = "
        select *,weekly_activity_reports.id as id_weekly_report from weekly_activity_reports
          JOIN activity_lists on activity_lists.id = weekly_activity_reports.activity_list_id
          where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and weekly_activity_reports.deleted_at is null";
        $weekly_report = DB::select($weekly_report_query);
        $weekly_report2 = DB::select($weekly_report_query);

        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($weekly_report2 as $weekly_report2){
          $subsection = $weekly_report2->subsection;
          $leader = $weekly_report2->leader_dept;
          $foreman = $weekly_report2->foreman_dept;
          if($weekly_report2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($weekly_report2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $weekly_report2->approved_date;
          $approved_date_leader = $weekly_report2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($weekly_report == null){
            return redirect('/index/weekly_report/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Weekly Activity Report');
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'weekly_report' => $weekly_report,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'role_code' => Auth::user()->role_code,
                          'id_departments' => $id_departments,
                          'monthTitle' => $monthTitle,
                          'month' => $month,
                          'leader' => $leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'foreman' => $foreman,);
            return view('weekly_report.print_email', $data
                )->with('page', 'Weekly Activity Report');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $month = $request->get('month');

          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $weeklyReportQuery = "
          	select *,weekly_activity_reports.id as id_weekly_report,'".$month."' as month from weekly_activity_reports
            JOIN activity_lists on activity_lists.id = weekly_activity_reports.activity_list_id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
            and weekly_activity_reports.activity_list_id = '".$id."'
            and department_id = '".$id_departments."'
            and weekly_activity_reports.deleted_at is null";
            
          $weekly_report = DB::select($weeklyReportQuery);
          $weekly_report3 = DB::select($weeklyReportQuery);
          $weekly_report2 = DB::select($weeklyReportQuery);

          if($weekly_report != null){
            foreach($weekly_report as $weekly_report){
              $foreman = $weekly_report->foreman;
              $send_status = $weekly_report->send_status;
              $subsection = $weekly_report->subsection;
            }

            foreach ($weekly_report2 as $weekly_report2) {
              $aCheck = WeeklyActivityReport::find($weekly_report2->id_weekly_report);
              $aCheck->send_status = "Sent";
              $aCheck->send_date = date('Y-m-d');
              $aCheck->approval_leader = "Approved";
              $aCheck->approved_date_leader = date('Y-m-d');
              $aCheck->save();
            }

            $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
            }
          }
          else{
            return redirect('/index/weekly_report/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Weekly Activity Report');
          }

          if($send_status == "Sent"){
            return redirect('/index/weekly_report/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Weekly Activity Report');
          }
          
          elseif($weekly_report != null){
              Mail::to($mail_to)->send(new SendEmail($weekly_report3, 'weekly_report'));
              return redirect('/index/weekly_report/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Weekly Activity Report');
          }
          else{
            return redirect('/index/weekly_report/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Weekly Activity Report');
          }
      }

    public function approval(Request $request,$id,$month)
    {
        $approve = $request->get('approve');
        if(count($approve) == 0){
          return redirect('/index/weekly_report/print_weekly_report_email/'.$id.'/'.$month)->with('error', 'Checklist the approval.')->with('page', 'Weekly Activity Report');
        }
        else{
          foreach($approve as $approve){
                $weekly_report = WeeklyActivityReport::find($approve);
                $weekly_report->approval = "Approved";
                $weekly_report->approved_date = date('Y-m-d');
                $weekly_report->save();
              }
          return redirect('/index/weekly_report/print_weekly_report_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Weekly Activity Report');
        }
    }
}
