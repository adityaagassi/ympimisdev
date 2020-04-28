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
      if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
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
        $frequency = $activityList->frequency;

        $bulan = date('Y-m');

        $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }

    	$data = array('audit_guidance' => $audit_guidance,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'fy' => $fy,
    				  'id' => $id,
              'frequency' => $frequency,
                      'id_departments' => $id_departments);
    	return view('audit_guidance.index', $data
    		)->with('page', 'Schedule Audit');
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

        $bulan = date('Y-m');

        $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        // }
        $data = array(
                      'audit_guidance' => $audit_guidance,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'fy' => $fy,
                      'id' => $id,
                      'frequency' => $frequency,
                      'id_departments' => $id_departments);
        return view('audit_guidance.index', $data
            )->with('page', 'Schedule Audit');
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
            )->with('page', 'Schedule Audit');
    }

    public function destroy($id,$audit_guidance_id)
    {
      $audit_guidance = AuditGuidance::find($audit_guidance_id);
      $audit_guidance->delete();

      return redirect('/index/audit_guidance/index/'.$id)
        ->with('status', 'Schedule has been deleted.')
        ->with('page', 'Schedule Audit');        
    }

    function store(Request $request,$id)
    {
            try{
              $id_user = Auth::id();
                AuditGuidance::create([
                    'activity_list_id' => $id,
                    'nama_dokumen' => $request->get('inputnama_dokumen'),
                    'no_dokumen' => $request->get('inputno_dokumen'),
                    'auditor' => $request->get('auditor'),
                    'date' => date('Y-m-d'),
                    'month' => $request->get('inputmonth'),
                    'periode' => $request->get('inputperiode'),
                    'status' => 'Belum Dikerjakan',
                    'leader' => $request->get('inputleader'),
                    'foreman' => $request->get('inputforeman'),
                    'created_by' => $id_user
                ]);

              return redirect('/index/audit_guidance/index/'.$id)->with('status', 'Audit Guidance data has been created.')->with('page', 'Audit Guidance');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function getdetail(Request $request)
    {
         try{
            $detail = AuditGuidance::find($request->get("id"));
            $data = array('audit_guidance_id' => $detail->id,
                          'nama_dokumen' => $detail->nama_dokumen,
                          'no_dokumen' => $detail->no_dokumen,
                          'month' => $detail->month,
                          'periode' => $detail->periode,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $audit_guidance){
            $error_code = $audit_guidance->errorInfo[1];
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

    function update(Request $request,$id,$audit_guidance_id)
    {
      try{
                
                  $audit_guidance = AuditGuidance::find($audit_guidance_id);
                  $audit_guidance->no_dokumen = $request->get('editno_dokumen');
                  $audit_guidance->nama_dokumen = $request->get('editnama_dokumen');
                  $audit_guidance->month = $request->get('editmonth');
                  $audit_guidance->save();

            return redirect('index/audit_guidance/index/'.$id)
              ->with('page', 'Audit Guidance')->with('status', 'Audit Guidance has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Audit Guidance already exist.')->with('page', 'Audit Guidance');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Audit Guidance');
            }
          }
    }
}
