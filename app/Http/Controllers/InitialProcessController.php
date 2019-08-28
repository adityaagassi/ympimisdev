<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;

class InitialProcessController extends Controller
{
	public function indexStockMonitoring($id){
		$title = 'Initial Process Stock Monitoring';
		$title_jp = '最初工程の在庫監視';

		if($id == 'mpro'){
			$location = "'FLA0','CLA0','SXA0','VNA0'";
			$locs = ["'FLA0'","'CLA0'","'SXA0'","'VNA0'"];
		}

		return view('processes.initial.display.stock_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
			'locs' => $locs
		))->with('head', 'Initial Process');
	}

	public function indexStockTrend($id){
		$title = 'Initial Process Stock Trend';
		$title_jp = '最初工程の在庫トレンド';

		if($id == 'mpro'){
			$location = "'FLA0','CLA0','SXA0','VNA0'";
			$locs = ["'FLA0'","'CLA0'","'SXA0'","'VNA0'"];
		}

		return view('processes.initial.display.stock_trend', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
			'locs' => $locs
		))->with('head', 'Initial Process');
	}

	public function fetchStockTrend(Request $request){
		$query = "select category, count(material_number) as material, date_stock from

		(
		select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, if(ceiling(inventories.act_stock/stocks.quantity)<9,ceiling(inventories.act_stock/stocks.quantity),9) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(ceiling(inventories.act_stock/stocks.quantity)=1, '<1Days', if(ceiling(inventories.act_stock/stocks.quantity)=2, '<2Days', if(ceiling(inventories.act_stock/stocks.quantity)=3, '<3Days', if(ceiling(inventories.act_stock/stocks.quantity)=4, '<4Days', if(ceiling(inventories.act_stock/stocks.quantity)=5, '<5Days', if(ceiling(inventories.act_stock/stocks.quantity)=6, '<6Days', if(ceiling(inventories.act_stock/stocks.quantity)=7, '<7Days', if(ceiling(inventories.act_stock/stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (".$request->get('location').") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories

		inner join 

		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number
		) as final
		group by category, date_stock
		";

		// $query = "select final_2.date_stock, final_2.category, final_2.material, round((final_2.material/total.material)*100, 2) as percentage from
		// (
		// select date_stock, category, count(material_number) as material from
		// (
		// select daily_stocks.material_number, date(daily_stocks.created_at) as date_stock, initial_safety_stocks.quantity as safety_stock, daily_stocks.quantity as actual_stock, round(daily_stocks.quantity/initial_safety_stocks.quantity,2) as stock, if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=0, '0Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=1, '<1Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=2, '<2Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=3, '<3Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=4, '<4Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=5, '<5Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=6, '<6Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=7, '<7Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category from daily_stocks inner join initial_safety_stocks on initial_safety_stocks.material_number = daily_stocks.material_number and DATE_FORMAT(initial_safety_stocks.valid_date, '%Y-%m') = DATE_FORMAT(daily_stocks.created_at, '%Y-%m') where initial_safety_stocks.quantity > 0 and daily_stocks.location in (".$request->get('location').")
		// ) as final group by date_stock, category
		// ) as final_2
		// left join
		// (
		// select date_stock, count(material_number) as material from
		// (
		// select daily_stocks.material_number, date(daily_stocks.created_at) as date_stock from daily_stocks inner join initial_safety_stocks on initial_safety_stocks.material_number = daily_stocks.material_number and DATE_FORMAT(initial_safety_stocks.valid_date, '%Y-%m') = DATE_FORMAT(daily_stocks.created_at, '%Y-%m') where initial_safety_stocks.quantity > 0 and daily_stocks.location in (".$request->get('location').")) as final group by date_stock) as total on total.date_stock = final_2.date_stock";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);

	}

	public function fetchStockTrendDetail(Request $request){

		$query = "select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, round((inventories.act_stock/stocks.quantity),2) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(ceiling(inventories.act_stock/stocks.quantity)=1, '<1Days', if(ceiling(inventories.act_stock/stocks.quantity)=2, '<2Days', if(ceiling(inventories.act_stock/stocks.quantity)=3, '<3Days', if(ceiling(inventories.act_stock/stocks.quantity)=4, '<4Days', if(ceiling(inventories.act_stock/stocks.quantity)=5, '<5Days', if(ceiling(inventories.act_stock/stocks.quantity)=6, '<6Days', if(ceiling(inventories.act_stock/stocks.quantity)=7, '<7Days', if(ceiling(inventories.act_stock/stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (".$request->get('location').") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories
		inner join 
		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number where inventories.date_stock = '".$request->get('date_stock')."' having category = '".$request->get('category')."' order by date_stock, stock asc	
		";

		// $query = "select daily_stocks.material_number, date(daily_stocks.created_at) as date_stock, initial_safety_stocks.quantity as safety_stock, daily_stocks.quantity as actual_stock, round(daily_stocks.quantity/initial_safety_stocks.quantity,2) as stock, if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=0, '0Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=1, '<1Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=2, '<2Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=3, '<3Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=4, '<4Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=5, '<5Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=6, '<6Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=7, '<7Days', if(ceiling(daily_stocks.quantity/initial_safety_stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category from daily_stocks inner join initial_safety_stocks on initial_safety_stocks.material_number = daily_stocks.material_number and DATE_FORMAT(initial_safety_stocks.valid_date, '%Y-%m') = DATE_FORMAT(daily_stocks.created_at, '%Y-%m') where initial_safety_stocks.quantity > 0 and daily_stocks.location in (".$request->get('location').") and date(daily_stocks.created_at) = '".$request->get('date_stock')."' having category = '".$request->get('category')."'";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);

	}

	public function fetchStockMonitoring(Request $request){
		$now = date('Y-m');
		$query = "select stock, category, count(material_number) as material from
		(
		select inventories.material_number, inventories.description, inventories.quantity, stocks.quantity as safety, if(ceiling(inventories.quantity/stocks.quantity)<9,ceiling(inventories.quantity/stocks.quantity),9) as stock, if(ceiling(inventories.quantity/stocks.quantity)=0, '0Days', if(ceiling(inventories.quantity/stocks.quantity)=1, '<1Days', if(ceiling(inventories.quantity/stocks.quantity)=2, '<2Days', if(ceiling(inventories.quantity/stocks.quantity)=3, '<3Days', if(ceiling(inventories.quantity/stocks.quantity)=4, '<4Days', if(ceiling(inventories.quantity/stocks.quantity)=5, '<5Days', if(ceiling(inventories.quantity/stocks.quantity)=6, '<6Days', if(ceiling(inventories.quantity/stocks.quantity)=7, '<7Days', if(ceiling(inventories.quantity/stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category from
		(
		select kitto.inventories.material_number, kitto.materials.description, sum(kitto.inventories.lot) as quantity from kitto.inventories left join kitto.materials on kitto.materials.material_number = kitto.inventories.material_number where kitto.materials.location in (".$request->get('location').") group by kitto.inventories.material_number, kitto.materials.description
		) as inventories
		inner join
		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity from initial_safety_stocks where DATE_FORMAT(valid_date, '%Y-%m') = '".$now."' and initial_safety_stocks.quantity > 0
		) as stocks on stocks.material_number = inventories.material_number)
		as final group by category, stock order by stock asc";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);
	}

	public function fetchStockMonitoringDetail(Request $request){
		$now = date('Y-m');
		$query = "select inventories.material_number, inventories.description, inventories.quantity, stocks.quantity as safety, if(ceiling(inventories.quantity/stocks.quantity)<9,ceiling(inventories.quantity/stocks.quantity),9) as stock, if(ceiling(inventories.quantity/stocks.quantity)=0, '0Days', if(ceiling(inventories.quantity/stocks.quantity)=1, '<1Days', if(ceiling(inventories.quantity/stocks.quantity)=2, '<2Days', if(ceiling(inventories.quantity/stocks.quantity)=3, '<3Days', if(ceiling(inventories.quantity/stocks.quantity)=4, '<4Days', if(ceiling(inventories.quantity/stocks.quantity)=5, '<5Days', if(ceiling(inventories.quantity/stocks.quantity)=6, '<6Days', if(ceiling(inventories.quantity/stocks.quantity)=7, '<7Days', if(ceiling(inventories.quantity/stocks.quantity)=8, '<8Days', '>8Days'))))))))) as category, inventories.quantity/stocks.quantity as days from
		(
		select kitto.inventories.material_number, kitto.materials.description, sum(kitto.inventories.lot) as quantity from kitto.inventories left join kitto.materials on kitto.materials.material_number = kitto.inventories.material_number where kitto.materials.location in (".$request->get('location').") group by kitto.inventories.material_number, kitto.materials.description
		) as inventories
		inner join
		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity from initial_safety_stocks where DATE_FORMAT(valid_date, '%Y-%m') = '".$now."' and initial_safety_stocks.quantity > 0) as stocks on stocks.material_number = inventories.material_number having category = '".$request->get('category')."' order by days asc";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);
	}
}
