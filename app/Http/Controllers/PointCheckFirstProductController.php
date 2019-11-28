<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use Illuminate\Support\Facades\DB;
use App\User;
use App\PointCheckFirstProduct;

class PointCheckFirstProductController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->proses = ['Pengerjaan Kunci Sub Assy',];
    }

    function index($id)
    {
    	$activityList = ActivityList::find($id);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

    	$pointCheckFirstProduct = PointCheckFirstProduct::where('activity_list_id',$id)
            ->orderBy('point_check_first_products.id','desc')->get();

    	$data = array('pointCheckFirstProduct' => $pointCheckFirstProduct,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
    				  'proses' => $this->proses,
                      'id_departments' => $id_departments);
    	return view('point_check_first_product.index', $data
    		)->with('page', 'Point Check First Product');
    }

    function show($id,$point_check_id)
    {
        $activityList = ActivityList::find($id);
        $pointCheckFirstProduct = PointCheckFirstProduct::find($point_check_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;
        // }
        $data = array('pointCheckFirstProduct' => $pointCheckFirstProduct,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'proses' => $this->proses,
                      'id' => $id);
        return view('point_check_first_product.view', $data
            )->with('page', 'Point Check First Product');
    }

    public function destroy($id,$point_check_id)
    {
      $pointCheckFirstProduct = PointCheckFirstProduct::find($point_check_id);
      $pointCheckFirstProduct->delete();

      return redirect('/index/point_check_first_product/index/'.$id)
        ->with('status', 'Point Check has been deleted.')
        ->with('page', 'Point Check First Product');
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $data = array('proses' => $this->proses,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('point_check_first_product.create', $data
            )->with('page', 'Point Check First Product');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();

            PointCheckFirstProduct::create([
                'activity_list_id' => $id,
                'proses' => $request->input('proses'),
                'point_check' => $request->input('point_check'),
                'standar' => $request->input('standar'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/point_check_first_product/index/'.$id)
            ->with('page', 'Point Check First Product')->with('status', 'New Point Check has been created.');
    }

    function edit($id,$point_check_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $pointCheckFirstProduct = PointCheckFirstProduct::find($point_check_id);

        $data = array('pointCheckFirstProduct' => $pointCheckFirstProduct,
                      'proses' => $this->proses,
                      'departments' => $departments,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('point_check_first_product.edit', $data
            )->with('page', 'Point Check First Product');
    }

    function update(Request $request,$id,$point_check_id)
    {
        try{
                $pointCheckFirstProduct = PointCheckFirstProduct::find($point_check_id);
                $pointCheckFirstProduct->activity_list_id = $id;
                $pointCheckFirstProduct->proses = $request->get('proses');
                $pointCheckFirstProduct->point_check = $request->get('point_check');
                $pointCheckFirstProduct->standar = $request->get('standar');
                $pointCheckFirstProduct->save();

            return redirect('/index/point_check_first_product/index/'.$id)->with('status', 'Point Check data has been updated.')->with('page', 'Point Check First Product');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Point Check First Product already exist.')->with('page', 'Point Check First Product');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Point Check First Product');
            }
          }
    }
}
