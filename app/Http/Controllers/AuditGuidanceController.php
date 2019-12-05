<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditGuidance;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AuditGuidanceController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$audit_guidance = AuditGuidance::where('activity_list_id',$id)
            ->orderBy('audit_guidances.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('audit_guidance' => $audit_guidance,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('audit_guidance.index', $data
    		)->with('page', 'Audit Guidance');
    }

    function filter_guidance(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $month = $request->get('month');
            $audit_guidance = AuditGuidance::where('activity_list_id',$id)
                ->where('month', '=', $month)
                ->orderBy('audit_guidances.id','desc')
                ->get();
        }
        else{
            $audit_guidance = AuditGuidance::where('activity_list_id',$id)
            ->orderBy('audit_guidances.id','desc')->get();
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
                      'audit_guidance' => $audit_guidance,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('audit_guidance.index', $data
            )->with('page', 'Audit Guidance');
    }

    function show($id,$audit_guidance_id)
    {
        $activityList = ActivityList::find($id);
        $audit_guidance = AuditGuidance::find($audit_guidance_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;
            $leader = $activityList->leader_dept;
        	$foreman = $activityList->foreman_dept;

        // }
        $data = array('audit_guidance' => $audit_guidance,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id);
        return view('audit_guidance.view', $data
            )->with('page', 'Audit Guidance');
    }

    public function destroy($id,$audit_guidance_id)
    {
      $audit_guidance = AuditGuidance::find($audit_guidance_id);
      $audit_guidance->delete();

      return redirect('/index/audit_guidance/index/'.$id)
        ->with('status', 'Label has been deleted.')
        ->with('page', 'Audit Guidance');        
    }
}
