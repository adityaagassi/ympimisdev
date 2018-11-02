<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisplayController extends Controller
{
	public function index_dp_production_result(){
		$hpls = DB::table('production_schedules')->select('hpl')->distinct()->get();
		return view('displays.daily_production_result', array(
			'hpls' => $hpls,
		))->with('page', 'Display Production Result')->with('head', 'Display');
	}

	public function fetch_dp_production_result(Request $request){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$plan_now = db::table('production_schedules')->where('due_date', '=', $now)
		->where('hpl', '=', $request->get('hpl'))->select('material_number', 'quantity');

		$plan2 = db::table('production_schedules')->where('due_date', '=', $now)
		->where('hpl', '=', $request->get('hpl'))->select('material_number', 'quantity')->unionAll($plan_now)
		->select('material_number', db::raw('sum(quantity) as quantity'))
		->groupBy('material_number')
		->get();


		// $production_schedules = DB::table('production_schedules')
		// ->where('production_schedules.due_date', '>=', $first)
		// ->where('production_schedules.due_date', '<=', $now)
		// ->where('production_schedules.hpl', '=', $request->get('hpl'))
		// ->leftJoin(db::raw("(select flo_details.material_number, sum(flo_details.quantity) as actual from flo_details where date(flo_details.created_at) >= '". $first ."' and date(flo_details.created_at) <= '". $now ."' group by flo_details.material_number) as flo_details"), 'flo_details.material_number', '=', 'production_schedules.material_number')
		// ->select('production_schedules.model', db::raw('sum(production_schedules.quantity) as plan'), db::raw('sum(flo_details.actual) as actual'))
		// ->groupBy('production_schedules.model')
		// ->get();

		$response = array(
			'status' => true,
			'chartData' => $plan2,
		);
		return Response::json($response);
	}
}
