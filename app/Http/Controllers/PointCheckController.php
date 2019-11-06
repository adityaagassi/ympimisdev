<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use Illuminate\Support\Facades\DB;
use App\User;
use App\PointCheckAudit;

class PointCheckController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
    	$activityList = ActivityList::find($id);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

    	$pointCheckAudit = PointCheckAudit::where('activity_list_id',$id)
            ->orderBy('point_check_audits.id','desc')->get();

    	$data = array('pointCheckAudit' => $pointCheckAudit,
    				  'product' => $product,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('point_check_audit.index', $data
    		)->with('page', 'Point Check Audit');
    }

    function filter_point_check(Request $request,$id)
    {
    	$queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        if($request->get('product') != null){
            $origin_group = $request->get('product');
            // $date = date('Y-m-d', strtotime($request->get('date')));
            $pointCheckAudit = PointCheckAudit::where('activity_list_id',$id)
                ->where('product',$origin_group)
                ->orderBy('point_check_audits.id','desc')
                ->get();
        }
        // elseif (strlen($request->get('date')) > null && $request->get('product') == null) {
        //     $date = date('Y-m-d', strtotime($request->get('date')));
        //     $productionAudit = ProductionAudit::where('activity_list_id',$id)
        //         ->where('date',$date)
        //         ->get();
        // }
        // elseif($request->get('product') > null && strlen($request->get('date')) == null){
        //     $origin_group = $request->get('product');
        //     $productionAudit = ProductionAudit::where('activity_list_id',$id)
        //         ->where('product',$origin_group)
        //         ->get();
        // }
        else{
            $pointCheckAudit = PointCheckAudit::where('activity_list_id',$id)
            ->orderBy('point_check_audits.id','desc')->get();
        }
        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        // }
        $data = array('product' => $product,
                      'pointCheckAudit' => $pointCheckAudit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('point_check_audit.index', $data
    		)->with('page', 'Point Check Audit');
    }

    function show($id,$point_check_audit_id)
    {
        $activityList = ActivityList::find($id);
        $pointCheckAudit = PointCheckAudit::find($point_check_audit_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;
        // }
        $data = array('pointCheckAudit' => $pointCheckAudit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('point_check_audit.view', $data
            )->with('page', 'Point Check Audit');
    }

    function show2($point_check_audit_id)
    {
        // $activityList = ActivityList::find($id);
        $pointCheckAudit = PointCheckAudit::find($point_check_audit_id);
        // foreach ($activityList as $activityList) {
            // $activity_name = $activityList->activity_name;
            // $departments = $activityList->departments->department_name;
            // $activity_alias = $activityList->activity_alias;
        // }
        $data = array('pointCheckAudit' => $pointCheckAudit,
                      // 'departments' => $departments,
                      );
        return view('point_check_audit.view2', $data
            )->with('page', 'Point Check Audit');
    }

    public function destroy($id,$point_check_audit_id)
    {
      $pointCheckAudit = PointCheckAudit::find($point_check_audit_id);
      $pointCheckAudit->delete();

      return redirect('/index/point_check_audit/index/'.$id)
        ->with('status', 'Point Check has been deleted.')
        ->with('page', 'Point Check Audit');
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $foreman = DB::select($queryForeman);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $data = array('product' => $product,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('point_check_audit.create', $data
            )->with('page', 'Point Check Audit');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();

            $messages = ['required' => ':attributes tidak boleh kosong.'];
            $this->validate($request,['point_check' => 'required',
                'cara_cek' => 'required',
                'proses' => 'required'],$messages);

            PointCheckAudit::create([
                'activity_list_id' => $id,
                'product' => $request->input('product'),
                'proses' => $request->input('proses'),
                'point_check' => $request->input('point_check'),
                'cara_cek' => $request->input('cara_cek'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/point_check_audit/index/'.$id)
            ->with('page', 'Point Check Audit')->with('status', 'New Point Check has been created.');
    }

    function edit($id,$point_check_audit_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $foreman = DB::select($queryForeman);

        $pointCheckAudit = PointCheckAudit::find($point_check_audit_id);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $data = array('pointCheckAudit' => $pointCheckAudit,
                      'product' => $product,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('point_check_audit.edit', $data
            )->with('page', 'Point Check Audit');
    }

    function update(Request $request,$id,$point_check_audit_id)
    {
        try{
                $pointCheckAudit = PointCheckAudit::find($point_check_audit_id);
                $pointCheckAudit->activity_list_id = $id;
                $pointCheckAudit->product = $request->get('product');
                $pointCheckAudit->proses = $request->get('proses');
                $pointCheckAudit->point_check = $request->get('point_check');
                $pointCheckAudit->cara_cek = $request->get('cara_cek');
                $pointCheckAudit->foreman = $request->get('foreman');
                $pointCheckAudit->save();

            return redirect('/index/point_check_audit/index/'.$id)->with('status', 'Point Check data has been updated.')->with('page', 'Point Check Audit');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Point Check already exist.')->with('page', 'Production Audit');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Point Check Audit');
            }
          }
    }
}
