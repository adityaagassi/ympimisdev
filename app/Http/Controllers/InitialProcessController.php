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
		$title_jp = 'イニシアル工程の在庫監視';

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
