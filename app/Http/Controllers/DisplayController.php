<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\OriginGroup;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisplayController extends Controller
{
	public function index_dp_production_result(){
		$origin_groups = OriginGroup::orderBy('origin_group_name', 'asc')->get();
		return view('displays.daily_production_result', array(
			'origin_groups' => $origin_groups,
		))->with('page', 'Display Production Result')->with('head', 'Display');
	}

	public function fetch_dp_production_result(Request $request){
		if($request->get('hpl') == 'all'){
			$hpl = "";
		}
		else{
			$hpl = "where materials.origin_group_code = '". $request->get('hpl') ."'";
		}

		$first = date('Y-m-01');
		if(date('Y-m-d') != date('Y-m-01')){
			$last = date('Y-m-d', strtotime(Carbon::yesterday()));
		}
		else{
			$last = date('Y-m-d');
		}
		$now = date('Y-m-d');

		$query = "select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
		(
		select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date = '". $now ."' 
		group by material_number

		union all

		select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
		from flo_details 
		where date(created_at) = '". $now ."'  
		group by material_number

		union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number

		) as result
		left join materials on materials.material_number = result.material_number
		". $hpl ."
		group by result.material_number, materials.material_description";

		$tableData = DB::select($query);

		$query2 = "select result.material_number, materials.material_description as model, sum(result.plan) as plan, sum(result.actual) as actual from
		(
		select material_number, sum(quantity) as plan, 0 as actual 
		from production_schedules 
		where due_date >= '". $first ."' and due_date <= '". $now ."' 
		group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as actual
		from flo_details
		where date(created_at) >= '". $first ."' and date(created_at) <= '". $now ."'
		group by material_number
		) as result
		left join materials on materials.material_number = result.material_number
		". $hpl ."
		group by result.material_number, materials.material_description";

		$chartData = DB::select($query2);

		// $totalPlan = DB::select();

		$response = array(
			'status' => true,
			'tableData' => $tableData,
			'chartData' => $chartData,
			// 'totalPlan' => $totalPlan,
		);
		return Response::json($response);
	}
}
