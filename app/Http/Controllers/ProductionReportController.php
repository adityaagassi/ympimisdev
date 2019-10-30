<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\ProductionAudit;
use Response;
use DataTables;
use Excel;
use App\User;
use App\PresenceLog;
use App\Division;
use App\Department;
use App\Section;
use App\SubSection;
use App\Group;
use App\Grade;
use App\Position;
use App\CostCenter;
use App\PromotionLog;
use App\Mutationlog;
use App\HrQuestionLog;
use App\HrQuestionDetail;
use App\Employee;
use App\EmploymentLog;
use App\OrganizationStructure;
use File;
use DateTime;
use Illuminate\Support\Arr;

class ProductionReportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $queryActivity = "SELECT DISTINCT(activity_type) FROM activity_lists where department_id = '".$id."'";
    	$activityList = DB::select($queryActivity);
        $data = array('activity_list' => $activityList,
                    'id' => $id);
        return view('production_report.index', $data
          )->with('page', 'Production Report');
    }

    function activity($id)
    {
    	$activityList = ActivityList::find($id);
    	// foreach ($activityList as $activity) {
    		$activity_type = $activityList->activity_type;
    	// }
    	if ($activity_type == "Audit") {
    		return redirect('/index/production_audit/details/'.$id)->with('page', 'Production Audit')->with('no', '1');
    	}
    	elseif($activity_type == "Training"){
            return redirect('/index/training_report/index/'.$id)->with('page', 'Training')->with('no', '2');
    	}
    	elseif($activity_type == "Laporan Aktivitas"){

    	}
    	elseif($activity_type == "Sampling Check"){
            return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', '3');
    	}
    	elseif($activity_type == "Pengecekan Foto"){

    	}
        elseif($activity_type == "Interview"){

        }
        elseif($activity_type == "Labelisasi"){

        }
        elseif($activity_type == "Pengecekan"){

        }
    }

    // function report_all($id)
    // {
    //     $queryDepartments = "SELECT * FROM departments where id='".$id."'";
    //     $department = DB::select($queryDepartments);
    //     foreach($department as $department){
    //         $departments = $department->department_name;
    //     }
    //     $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
    //     return view('production_report.report_all',  array('title' => 'Production Report',
    //         'title_jp' => 'Production Report',
    //         'id' => $id,
    //         'data' => $data,
    //         'departments' => $departments,
    //     ))->with('page', 'Report All');
    // }

    // public function fetchReport(Request $request,$id)
    // {
    //   $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
    //   // $monthTitle = date("F Y", strtotime($tgl));


    //   $response = array(
    //     'status' => true,
    //     'datas' => $data,
    //     'ctg' => $request->get("ctg"),
    //     // 'monthTitle' => $monthTitle

    //   );

    //   return Response::json($response); 
    // }

    // public function detailProductionReport(Request $request, $id){
    //   $activity_type = $request->get("activity_type");
    //     $query = "SELECT *, activity_lists.id as id_activity FROM `activity_lists` join departments on departments.id = activity_lists.department_id where activity_lists.activity_type = '".$activity_type."' and activity_lists.deleted_at is null and activity_lists.department_id = '".$id."'";

    //   $detail = db::select($query);

    //   return DataTables::of($detail)->make(true);

    // }

    // function report_by_act_type($id,$activity_type)
    // {
    //     // $activityList = ActivityList::find($id);
    //     // // foreach ($activityList as $activity) {
    //     //     $activity_type = $activityList->activity_type;
    //     // }
    //     if ($activity_type == "Audit") {
    //         return redirect('/index/production_audit/report_audit/'.$id)->with('page', 'Production Audit');
    //     }
    //     elseif($activity_type == "Training"){
    //         return redirect('/index/training_report/report_training/'.$id)->with('page', 'Training');
    //     }
    //     elseif($activity_type == "Laporan Aktivitas"){
    //         var_dump("halooo");
    //     }
    //     elseif($activity_type == "Sampling Check"){
    //         return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', '3');
    //     }
    //     elseif($activity_type == "Pengecekan Foto"){

    //     }
    //     elseif($activity_type == "Interview"){

    //     }
    //     elseif($activity_type == "Labelisasi"){

    //     }
    //     elseif($activity_type == "Pengecekan"){

    //     }
    // }
}
