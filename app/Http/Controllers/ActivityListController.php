<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use Illuminate\Support\Facades\DB;
use App\User;

class ActivityListController extends Controller
{

	public function __construct()
    {
      $this->middleware('auth');
      $this->activity_type = ['Audit',
                        'Training',
                        'Laporan Aktivitas',
                        'Sampling Check',
                        'Pengecekan Foto',
                        'Interview',
                        'Pengecekan',
                        'Pemahaman Proses',
                        'Labelisasi'];
    }

    function index()
    {
    	$activityList = ActivityList::get();
    	$data = array('activity_list' => $activityList);
    	return view('activity_list.index', $data
    		)->with('page', 'Activity List');
    }

    function filter($id,$no)
    {
      $queryDepartments = "SELECT * FROM departments where id='".$id."'";
      $department = DB::select($queryDepartments);
      foreach ($department as $department) {
          $dept_name = $department->department_name;
      }
      if ($no == 1) {
        $activity_type = 'Audit';
      }
      elseif ($no == 2) {
        $activity_type = 'Training';
      }
      elseif ($no == 3) {
        $activity_type = 'Sampling Check';
      }
      elseif ($no == 4) {
        $activity_type = 'Laporan Aktivitas';
      }
      elseif ($no == 5) {
        $activity_type = 'Pemahaman Proses';
      }
      elseif ($no == 6) {
        $activity_type = 'Pengecekan';
      }
      elseif ($no == 7) {
        $activity_type = 'Interview';
      }
      elseif ($no == 8) {
        $activity_type = 'Pengecekan Foto';
      }
      elseif ($no == 9) {
        $activity_type = 'Labelisasi';
      }
      $activityList = ActivityList::where('department_id',$id)->where('activity_type',$activity_type)->get();
      $data = array('activity_list' => $activityList,
                    'department' => $department,
                    'dept_name' => $dept_name,
                    'id' => $id,
                    'no' => $no,);
      return view('activity_list.filter', $data
        )->with('page', 'Activity List');
    }

    function create()
    {
    	$queryDepartments = "SELECT * FROM departments where id_division=5";
    	$department = DB::select($queryDepartments);
    	$data = array('department' => $department,
    				  'activity_type' => $this->activity_type,
              'id' => 0,
              'dept_name' => null);
    	return view('activity_list.create', $data
    		)->with('page', 'Activity List');
    }

    function create_by_department($id,$no)
    {
      $queryDepartments2 = "SELECT * FROM departments where id='".$id."'";
      $department_by_id = DB::select($queryDepartments2);
      foreach ($department_by_id as $department_by_id) {
          $dept_name = $department_by_id->department_name;
      }

      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);
      $data = array('department' => $department,
                    'activity_type' => $this->activity_type,
                    'dept_name' => $dept_name,
                    'id' => $id,
                    'no' => $no);
      return view('activity_list.create', $data
        )->with('page', 'Activity List');
    }

    public function store(request $request)
    {
      try{
          $id = Auth::id();
          $activity_list = new ActivityList([
            'activity_name' => $request->get('activity_name'),
            'activity_alias' => $request->get('activity_alias'),
            'frequency' => $request->get('frequency'),
            'department_id' => $request->get('department_id'),
            'activity_type' => $request->get('activity_type'),
            'created_by' => $id
          ]);

          $activity_list->save();
          return redirect('/index/activity_list')->with('status', 'New Activity has been created.')->with('page', 'Activity List');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Activity List');
        }
      }
    }

    public function store_by_department(request $request,$id,$no)
    {
      try{
          $id_user = Auth::id();
          $activity_list = new ActivityList([
            'activity_name' => $request->get('activity_name'),
            'activity_alias' => $request->get('activity_alias'),
            'frequency' => $request->get('frequency'),
            'department_id' => $request->get('department_id'),
            'activity_type' => $request->get('activity_type'),
            'created_by' => $id_user
          ]);

          $activity_list->save();
          return redirect('/index/activity_list/filter/'.$id.'/'.$no)->with('status', 'New Activity has been created.')->with('page', 'Activity List');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Activity List');
        }
      }
    }

    public function show($id)
    {
      $activity_list = ActivityList::find($id);
      $data = array('activity_list' => $activity_list);
    	return view('activity_list.view', $data
    		)->with('page', 'Activity List');
    }

    public function edit($id)
    {
      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);
      $activity_list = ActivityList::find($id);
      $data = array(
              'id_department' => 0,
              'department' => $department,
      				'activity_list' => $activity_list,
  					  'activity_type' => $this->activity_type);
    	return view('activity_list.edit', $data
    		)->with('page', 'Activity List');
    }

    public function edit_by_department($id,$department_id,$no)
    {
      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);
      $activity_list = ActivityList::find($id);
      $id_department = $activity_list->department_id;
      $data = array(
              'id_department' => $id_department,
              'department' => $department,
              'no' => $no,
              'activity_list' => $activity_list,
              'activity_type' => $this->activity_type);
      return view('activity_list.edit', $data
        )->with('page', 'Activity List');
    }

    public function update(Request $request, $id)
    {
          try{
          	$activity_list = ActivityList::find($id);
            $activity_list->activity_name = $request->get('activity_name');
            $activity_list->activity_alias = $request->get('activity_alias');
            $activity_list->frequency = $request->get('frequency');
            $activity_list->department_id = $request->get('department_id');
            $activity_list->activity_type = $request->get('activity_type');
            $activity_list->save();
            return redirect('/index/activity_list')->with('status', 'Activity data has been updated.')->with('page', 'Activity List');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Activity List');
            }
          }
    }

    public function update_by_department(Request $request, $id,$id_department,$no)
    {
          try{
            $activity_list = ActivityList::find($id);
            $activity_list->activity_name = $request->get('activity_name');
            $activity_list->activity_alias = $request->get('activity_alias');
            $activity_list->frequency = $request->get('frequency');
            $activity_list->department_id = $request->get('department_id');
            $activity_list->activity_type = $request->get('activity_type');
            $activity_list->save();
            return redirect('/index/activity_list/filter/'.$id_department.'/'.$no)->with('status', 'Activity data has been updated.')->with('page', 'Activity List');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Activity List');
            }
          }
    }

    public function destroy($id)
    {
      $activity_list = ActivityList::find($id);
      $activity_list->delete();

      return redirect('/index/activity_list')->with('status', 'Activity has been deleted.')->with('page', 'Activity List');
        //
    }

    public function destroy_by_department($id,$department_id,$no)
    {
      $activity_list = ActivityList::find($id);
      $activity_list->delete();

      return redirect('/index/activity_list/filter/'.$department_id.'/'.$no)->with('status', 'Activity has been deleted.')->with('page', 'Activity List');
        //
    }
}
