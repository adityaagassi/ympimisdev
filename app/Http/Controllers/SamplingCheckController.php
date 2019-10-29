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
        //
    }
}
