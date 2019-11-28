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

    function filter_first_product(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $month = $request->get('month');
            $first_product_audit = FirstProductAudit::where('activity_list_id',$id)
                ->where('month', '=', $month)
                ->orderBy('first_product_audits.id','desc')
                ->get();
        }
        else{
            $first_product_audit = FirstProductAudit::where('activity_list_id',$id)
            ->orderBy('first_product_audits.id','desc')->get();
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
                      'first_product_audit' => $first_product_audit,
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
}
