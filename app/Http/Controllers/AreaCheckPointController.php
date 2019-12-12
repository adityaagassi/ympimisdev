<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use Illuminate\Support\Facades\DB;
use App\User;
use App\AreaCheckPoint;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AreaCheckPointController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$area_check_point = AreaCheckPoint::where('activity_list_id',$id)
            ->orderBy('area_check_points.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('area_check_point' => $area_check_point,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('area_check_point.index', $data
    		)->with('page', 'Area Check Point');
    }

    function store(Request $request,$id)
    {
            try{
              $id_user = Auth::id();
                AreaCheckPoint::create([
                    'activity_list_id' => $id,
                    'point_check' => $request->get('inputpoint_check'),
                    'leader' => $request->get('inputleader'),
                    'foreman' => $request->get('inputforeman'),
                    'created_by' => $id_user
                ]);

              return redirect('/index/area_check_point/index/'.$id)->with('status', 'Area Check Point data has been created.')->with('page', 'Area Check Point');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function show($id,$audit_guidance_id)
    {
        $activityList = ActivityList::find($id);
        $area_check_point = AreaCheckPoint::find($audit_guidance_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;
            $leader = $activityList->leader_dept;
        	$foreman = $activityList->foreman_dept;

        // }
        $data = array('area_check_point' => $area_check_point,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id);
        return view('area_check_point.view', $data
            )->with('page', 'Area Check Point');
    }

    public function destroy($id,$audit_guidance_id)
    {
      $area_check_point = AreaCheckPoint::find($audit_guidance_id);
      $area_check_point->delete();

      return redirect('/index/area_check_point/index/'.$id)
        ->with('status', 'Area Check Point has been deleted.')
        ->with('page', 'Area Check Point');        
    }

    public function getdetail(Request $request)
    {
         try{
            $detail = AreaCheckPoint::find($request->get("id"));
            $data = array('area_check_point_id' => $detail->id,
                          'point_check' => $detail->point_check,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $area_check_point){
            $error_code = $area_check_point->errorInfo[1];
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

    function update(Request $request,$id,$area_check_point_id)
    {
      try{
                
                  $area_check_point = AreaCheckPoint::find($area_check_point_id);
                  $area_check_point->point_check = $request->get('editpoint_check');
                  $area_check_point->save();

            return redirect('index/area_check_point/index/'.$id)
              ->with('page', 'Audit Guidance')->with('status', 'Area Check Point has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Area Check Point already exist.')->with('page', 'Area Check Point');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Area Check Point');
            }
          }
    }
}
