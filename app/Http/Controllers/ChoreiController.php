<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChoreiController extends Controller
{
	public function index_ch_daily_production_result(){
		return view('choreis.production_result')->with('page', 'Chorei Production Result')->with('head', 'Chorei');
	}

	public function fetch_daily_production_result_week(){
		$first = date('Y-m-d', strtotime(new Carbon('first day of this month')));
		$last = date('Y-m-d', strtotime(new Carbon('last day of this month')));

		$weeks = DB::table('weekly_calendars')->select('week_name')
		->where('week_date', '>=', $first)
		->where('week_date', '<=', $last)
		->distinct()
		->select(db::raw('concat("Week ", mid(week_name,2)) as week'), 'week_name')
		->orderBy(db::raw('convert(mid(week_name,2), unsigned integer)'), 'asc')
		->get();

		$response = array(
			'status' => true,
			'weekData' => $weeks,
		);
		return Response::json($response);
	}

	public function fetch_daily_production_result_date(Request $request){

		$dates = DB::table('weekly_calendars');

		if(strlen($request->get('week')) > 0){
			$dates = $dates->where('week_name', '=', $request->get('week'));
		}
		else{
			$week = DB::table('weekly_calendars')->where('week_date', '=', date('Y-m-d'))->select('week_name')->first();
			$dates = $dates->where('week_name', '=', $week->week_name);
		}

		$dates = $dates->select('week_date', db::raw('date_format(week_date, "%d %M %Y") as week_date_name'))
		->orderBy('week_date', 'asc')
		->get();

		$response = array(
			'status' => true,
			'dateData' => $dates,
		);
		return Response::json($response);
	}

	public function fetch_daily_production_result(Request $request){

		

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}	
}
