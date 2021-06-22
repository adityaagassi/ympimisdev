<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditKanban;
use App\AuditKanbanPointCheck;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class AuditKanbanController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index($id){

        $activityList = ActivityList::find($id);

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        $remark = $activityList->remark;

        $point_check = AuditKanbanPointCheck::orderBy('audit_kanban_point_checks.point_check_index')->get();

        $data = array(
            'departments' => $departments,
            'activity_name' => $activity_name,
            'activity_alias' => $activity_alias,
            'id' => $id,
            'frequency' => $frequency,
            'remark' => $remark,
            'point_check' => $point_check,
            'point_check2' => $point_check,
            'leader' => $leader,
            'foreman' => $foreman,
            'id_departments' => $id_departments
        );
        return view('audit_kanban.index', $data)->with('page', 'Audit Kanban');
    }

    public function fetchAuditKanban(Request $request){
        try {
            $id = $request->get('id');
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                }else{
                    $first = date('Y-m-d');
                    $last = "'".$date_to."'";
                }
            }else{
                if ($date_to == "") {
                    $first = "'".$date_from."'";
                    $last = date('Y-m-d');
                }else{
                    $first = "'".$date_from."'";
                    $last = "'".$date_to."'";
                }
            }
            $audit_kanban = AuditKanban::select('audit_kanbans.*','audit_kanban_point_checks.*','audit_kanbans.id as id_audit_kanban','activity_lists.remark')
            ->leftjoin('audit_kanban_point_checks','audit_kanban_point_checks.id','audit_kanbans.point_check_id')
            ->leftjoin('activity_lists','activity_lists.id','audit_kanbans.activity_list_id')
            ->where('audit_kanbans.check_date','>=',$first)
            ->where('audit_kanbans.check_date','<=',$last)
            ->where('audit_kanbans.activity_list_id','=',$id)
            ->get();

            $response = array(
                'status' => true,
                'audit_kanban' => $audit_kanban,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }


    public function printAuditKanban($activity_list, $month){

        $weekly_calendar = WeeklyCalendar::where(db::raw("DATE_FORMAT(week_date, '%Y-%m')"), $month)->get();

        $activity = ActivityList::where('id', $activity_list)->first();

        $audit_kanban = AuditKanban::where('activity_list_id', $activity_list)
        ->where(db::raw("DATE_FORMAT(check_date, '%Y-%m')"), $month)
        ->get();

        $point_check = AuditKanbanPointCheck::orderBy('point_check_index', 'ASC')->get();

        $percentage = DB::SELECT("SELECT point.week_date, point.remark, point.`check`, COALESCE(audit.audit,0) AS audit, (COALESCE(audit.audit,0)/point.`check`*100) AS percentage FROM
            (SELECT w.week_date, w.remark, SUM(IF(p.point_check_index IS NOT NULL && w.remark <> 'H', 1, 0)) AS `check` FROM weekly_calendars w
            CROSS JOIN audit_kanban_point_checks p
            WHERE DATE_FORMAT(w.week_date,'%Y-%m') = '".$month."'
            GROUP BY w.week_date, w.remark) AS point
            LEFT JOIN
            (SELECT a.check_date,  SUM(IF(a.`condition` = 'OK', 1,0)) AS audit FROM audit_kanbans a
            WHERE DATE_FORMAT(a.check_date,'%Y-%m') = '".$month."'
            AND a.activity_list_id = '".$activity_list."'
            GROUP BY a.check_date) AS audit
            ON audit.check_date = point.week_date
            ORDER BY point.week_date ASC");

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        return view('audit_kanban.audit_kanban_pdf', array(
            'month' => $month,
            'weekly_calendar' => $weekly_calendar,
            'activity' => $activity,
            'audit_kanban' => $audit_kanban,
            'point_check' => $point_check,
            'percentage' => $percentage
        ));
        
    }

    public function inputAuditKanban(Request $request)
    {
    	try {
    		$condition = $request->get('condition');
    		$point_check_id = $request->get('point_check_id');
    		$count_point = $request->get('count_point');
    		for ($i=0; $i < $count_point; $i++) { 
    			AuditKanban::create([
                    'activity_list_id' => $request->get('id'),
                    'point_check_id' => $point_check_id[$i],
                    'department' => $request->get('department'),
                    'check_date' => $request->get('check_date'),
                    'condition' => $condition[$i],
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => Auth::user()->id
                ]);
    		}

    		
    		$response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (\Exception $e) {
          $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
          return Response::json($response);
      }
  }

  public function emailAuditKanban(Request $request)
  {
     try {
      $audit_kanban = AuditKanban::select('audit_kanbans.*','audit_kanban_point_checks.*','audit_kanbans.id as id_audit_kanban')
      ->leftjoin('audit_kanban_point_checks','audit_kanban_point_checks.id','audit_kanbans.point_check_id')
      ->leftjoin('activity_lists','activity_lists.id','audit_kanbans.activity_list_id')
      ->where('check_date',$request->get('check_date'))
      ->where('audit_kanbans.activity_list_id','=',$request->get('id'))
      ->get();

      if (count($audit_kanban) > 0) {
         $foreman = $audit_kanban[0]->foreman;
         $mail_to = User::select('email')->where('name',$foreman)->first();

         Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($audit_kanban, 'audit_kanban'));

         for ($i=0; $i < count($audit_kanban); $i++) { 
          $audit = AuditKanban::where('id',$audit_kanban[$i]->id_audit_kanban)->first();
          $audit->send_status = 'Sent';
          $audit->send_date = date('Y-m-d');
          $audit->approval_leader = 'Approved';
          $audit->approval_date_leader = date('Y-m-d');
          $audit->save();
      }

      $response = array(
         'status' => true,
     );
      return Response::json($response);
  }else{
   $response = array(
       'status' => false,
       'message' => 'Data tidak tersedia / Sudah pernah dikirim.',
   );
   return Response::json($response);
}
} catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
  );
    return Response::json($response);
}
}

public function approvalAuditKanban($activity_list_id,$month)
{
 $audit_kanban = AuditKanban::select('audit_kanbans.*','audit_kanban_point_checks.*','audit_kanbans.id as id_audit_kanban')
 ->leftjoin('audit_kanban_point_checks','audit_kanban_point_checks.id','audit_kanbans.point_check_id')
 ->leftjoin('activity_lists','activity_lists.id','audit_kanbans.activity_list_id')
 ->where(DB::RAW('DATE_FORMAT(audit_kanbans.check_date,"%Y-%m")'),$month)
 ->where('audit_kanbans.activity_list_id','=',$activity_list_id)
 ->get();

 $leader = $audit_kanban[0]->leader;

 if (count($audit_kanban) > 0) {
   for ($i=0; $i < count($audit_kanban); $i++) { 
    $audit = AuditKanban::where('id',$audit_kanban[$i]->id_audit_kanban)->first();
    $audit->approval = 'Approved';
    $audit->approval_date = date('Y-m-d');
    $audit->save();
}
}

return view('audit_kanban.audit_kanban_message')->with('head','Audit Kanban')->with('message','Audit Kanban of '.$leader.' has been approved.')->with('activity_list_id',$activity_list_id)->with('month',$month)->with('page','Audit Kanban');
}


public function editAuditKanban(Request $request)
{
  try {
     $audit = AuditKanban::where('id',$request->get('id_audit_kanban'))->first();

     $audit_kanban = AuditKanban::where(DB::RAW('DATE_FORMAT(check_date,"%Y-%m")'),date('Y-m',strtotime($audit->check_date)))->get();

     $response = array(
         'status' => true,
         'audit_kanban' => $audit_kanban,
     );
     return Response::json($response);
 } catch (\Exception $e) {
     $response = array(
         'status' => false,
         'message' => $e->getMessage(),
     );
     return Response::json($response);
 }
}

public function updateAuditKanban(Request $request)
{
  try {
    $condition = $request->get('condition');
	$edit_id = $request->get('edit_id');
	$count_point = $request->get('count_point');
	for ($i=0; $i < $count_point; $i++) { 
		$audit = AuditKanban::where('id',$edit_id[$i])->first();
		$audit->condition = $condition[$i];
		$audit->save();
	}

     $response = array(
         'status' => true,
     );
     return Response::json($response);
 } catch (\Exception $e) {
     $response = array(
         'status' => false,
         'message' => $e->getMessage(),
     );
     return Response::json($response);
 }
}


}
