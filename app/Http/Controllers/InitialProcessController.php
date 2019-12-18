<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use App\OriginGroup;
use App\Material;
use Carbon\Carbon;
use App\InitialSafetyStock;
use Illuminate\Support\Facades\Auth;

class InitialProcessController extends Controller
{
	public function index($id){
		if($id == 'bpro_fl'){
			$title = 'Body Process Flute';
			$title_jp = '??';
			return view('processes.initial.index_bpro_fl', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Body Process FL');
		}
		if($id == 'mpro_fl'){
			$title = 'Material Process Flute';
			$title_jp = '??';
			return view('processes.initial.index_mpro_fl', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process FL');
		}
		if($id == 'mpro_cl'){
			$title = 'Material Process Flute';
			$title_jp = '??';
			return view('processes.initial.index_mpro_cl', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process CL');
		}
		if($id == 'mpro_sx'){
			$title = 'Material Process Flute';
			$title_jp = '??';
			return view('processes.initial.index_mpro_sx', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process SAX');
		}
	}

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

	public function indexStockMaster()
	{
		$title = 'Initial Process Stock';
		$title_jp = '?';

		$materials = Material::orderBy('material_number', 'ASC')->get();
		$origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

		return view('initial_safety.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'origin_groups' => $origin_groups
		))->with('page', 'Safety Stock');
	}

	public function fetchStockTrend(Request $request){
		$query = "select category, count(material_number) as material, date_stock from

		(
		select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, 0, if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, 0.5, if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, 1, if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, 1.5, if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, 2, null))))) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, '<0.5Days', if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, '<1Days', if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, '<1.5Days', if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, '<2Days', null))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (".$request->get('location').") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories

		inner join 

		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number
		) as final
		group by category, date_stock having category is not null order by date_stock, stock asc
		";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);

	}

	public function fetchStockTrendDetail(Request $request){

		$query = "select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, 0, if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, 0.5, if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, 1, if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, 1.5, if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, 2, null))))) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, '<0.5Days', if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, '<1Days', if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, '<1.5Days', if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, '<2Days', null))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (".$request->get('location').") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories
		inner join 
		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number where inventories.date_stock = '".$request->get('date_stock')."' having category = '".$request->get('category')."' order by date_stock, stock asc	
		";

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
		select inventories.material_number, inventories.description, inventories.quantity, stocks.quantity as safety, 
		if(ceiling(inventories.quantity/stocks.quantity)=0, 0, if(inventories.quantity/stocks.quantity>0 and inventories.quantity/stocks.quantity <= 0.5, 0.5, if(inventories.quantity/stocks.quantity > 0.5 and inventories.quantity/stocks.quantity <= 1, 1, if(inventories.quantity/stocks.quantity>1 and inventories.quantity/stocks.quantity<=1.5, 1.5, if(inventories.quantity/stocks.quantity>1.5 and inventories.quantity/stocks.quantity<=2, 2, if(inventories.quantity/stocks.quantity>2 and inventories.quantity/stocks.quantity<=2.5, 2.5, if(inventories.quantity/stocks.quantity>2.5 and inventories.quantity/stocks.quantity<=3, 3, if(inventories.quantity/stocks.quantity>3 and inventories.quantity/stocks.quantity<=3.5, 3.5, if(inventories.quantity/stocks.quantity>3.5 and inventories.quantity/stocks.quantity<=4, 4, if(inventories.quantity/stocks.quantity>4 and inventories.quantity/stocks.quantity<=4.5, 4.5, if(inventories.quantity/stocks.quantity>4.5, 4.6, 4.6))))))))))) as stock, if(ceiling(inventories.quantity/stocks.quantity)=0, '0Days', if(inventories.quantity/stocks.quantity>0 and inventories.quantity/stocks.quantity <= 0.5, '<0.5Days', if(inventories.quantity/stocks.quantity > 0.5 and inventories.quantity/stocks.quantity <= 1, '<1Days', if(inventories.quantity/stocks.quantity>1 and inventories.quantity/stocks.quantity<=1.5, '<1.5Days', if(inventories.quantity/stocks.quantity>1.5 and inventories.quantity/stocks.quantity<=2, '<2Days', if(inventories.quantity/stocks.quantity>2 and inventories.quantity/stocks.quantity<=2.5, '<2.5Days', if(inventories.quantity/stocks.quantity>2.5 and inventories.quantity/stocks.quantity<=3, '<3Days', if(inventories.quantity/stocks.quantity>3 and inventories.quantity/stocks.quantity<=3.5, '<3.5Days', if(inventories.quantity/stocks.quantity>3.5 and inventories.quantity/stocks.quantity<=4, '<4Days', if(inventories.quantity/stocks.quantity>4 and inventories.quantity/stocks.quantity<=4.5, '<4.5Days', if(inventories.quantity/stocks.quantity>4.5, '>4.5Days', '>4.5Days'))))))))))) as category from
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
		$query = "select inventories.material_number, inventories.description, inventories.quantity, stocks.quantity as safety, 
		if(ceiling(inventories.quantity/stocks.quantity)=0, 0, if(inventories.quantity/stocks.quantity>0 and inventories.quantity/stocks.quantity <= 0.5, 0.5, if(inventories.quantity/stocks.quantity > 0.5 and inventories.quantity/stocks.quantity <= 1, 1, if(inventories.quantity/stocks.quantity>1 and inventories.quantity/stocks.quantity<=1.5, 1.5, if(inventories.quantity/stocks.quantity>1.5 and inventories.quantity/stocks.quantity<=2, 2, if(inventories.quantity/stocks.quantity>2 and inventories.quantity/stocks.quantity<=2.5, 2.5, if(inventories.quantity/stocks.quantity>2.5 and inventories.quantity/stocks.quantity<=3, 3, if(inventories.quantity/stocks.quantity>3 and inventories.quantity/stocks.quantity<=3.5, 3.5, if(inventories.quantity/stocks.quantity>3.5 and inventories.quantity/stocks.quantity<=4, 4, if(inventories.quantity/stocks.quantity>4 and inventories.quantity/stocks.quantity<=4.5, 4.5, if(inventories.quantity/stocks.quantity>4.5, 4.6, 4.6))))))))))) as stock, if(ceiling(inventories.quantity/stocks.quantity)=0, '0Days', if(inventories.quantity/stocks.quantity>0 and inventories.quantity/stocks.quantity <= 0.5, '<0.5Days', if(inventories.quantity/stocks.quantity > 0.5 and inventories.quantity/stocks.quantity <= 1, '<1Days', if(inventories.quantity/stocks.quantity>1 and inventories.quantity/stocks.quantity<=1.5, '<1.5Days', if(inventories.quantity/stocks.quantity>1.5 and inventories.quantity/stocks.quantity<=2, '<2Days', if(inventories.quantity/stocks.quantity>2 and inventories.quantity/stocks.quantity<=2.5, '<2.5Days', if(inventories.quantity/stocks.quantity>2.5 and inventories.quantity/stocks.quantity<=3, '<3Days', if(inventories.quantity/stocks.quantity>3 and inventories.quantity/stocks.quantity<=3.5, '<3.5Days', if(inventories.quantity/stocks.quantity>3.5 and inventories.quantity/stocks.quantity<=4, '<4Days', if(inventories.quantity/stocks.quantity>4 and inventories.quantity/stocks.quantity<=4.5, '<4.5Days', if(inventories.quantity/stocks.quantity>4.5, '>4.5Days', '>4.5Days'))))))))))) as category, inventories.quantity/stocks.quantity as days from
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

	public function fetchStockMaster()
	{
		$initial_safety = InitialSafetyStock::leftJoin("materials","materials.material_number","=","initial_safety_stocks.material_number")
		->leftJoin("origin_groups","origin_groups.origin_group_code","=","materials.origin_group_code")
		->select('initial_safety_stocks.id','initial_safety_stocks.material_number','initial_safety_stocks.valid_date','initial_safety_stocks.quantity','materials.material_description','origin_groups.origin_group_name')
		->orderByRaw('valid_date DESC', 'initial_safety_stocks.material_number ASC')
		->get();

		return DataTables::of($initial_safety)
		->addColumn('action', function($initial_safety){
			return '
			<button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$initial_safety->id.')">View</button>
			<button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$initial_safety->id.')">Edit</button>
			<button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$initial_safety->id.',\''.$initial_safety->material_number.'\',\''.$initial_safety->valid_date.'\')">Delete</button>';
		})

		->rawColumns(['action' => 'action'])
		->make(true);
	}

	public function view(Request $request)
	{
		$query = "select initial_stock.material_number, initial_stock.valid_date, initial_stock.quantity, users.`name`, material_description, origin_group_name, initial_stock.created_at, initial_stock.updated_at from
		(select material_number, valid_date, quantity, created_by, created_at, updated_at from initial_safety_stocks where id = "
		.$request->get('id').") as initial_stock
		left join materials on materials.material_number = initial_stock.material_number
		left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
		left join users on initial_stock.created_by = users.id";

		$intial_stock = DB::select($query);

		$response = array(
			'status' => true,
			'datas' => $intial_stock
		);

		return Response::json($response);
	}

	public function fetchEdit(Request $request)
	{
		$intial_stock = InitialSafetyStock::where('id', '=', $request->get("id"))
		->first();

		$response = array(
			'status' => true,
			'datas' => $intial_stock
		);

		return Response::json($response);
	}

	public function edit(Request $request)
	{
		$head = InitialSafetyStock::where('id', '=', $request->get('id'))
		->first();

		$head->quantity = $request->get('quantity');
		$head->save();

		$response = array(
			'status' => true
		);

		return Response::json($response);
	}

	public function import(Request $request)
	{
		try{
			if($request->hasFile('intial_stock')){
                // ProductionSchedule::truncate();

				$id = Auth::id();

				$file = $request->file('intial_stock');
				$data = file_get_contents($file);

				$rows = explode("\r\n", $data);

				$first = explode("\t", $rows[0]);
				$date =  date('Y-m-d' , strtotime(str_replace('/','-',$first[1])));

				$delete = InitialSafetyStock::where('valid_date', '=', $date)
				->forceDelete();

				foreach ($rows as $row)
				{
					if (strlen($row) > 0) {
						$row = explode("\t", $row);
						$intial_stock = new InitialSafetyStock([
							'material_number' => $row[0],
							'valid_date' => date('Y-m-d', strtotime(str_replace('/','-',$row[1]))),
							'quantity' => $row[2],
							'created_by' => $id,
						]);

						$intial_stock->save();
					}
				}
				return redirect('/index/safety_stock')->with('status', 'New Initial Safety Stock has been imported.')->with('page', 'Safety Stock');
			}
			else
			{
				return redirect('/index/safety_stock')->with('error', 'Please select a file.')->with('page', 'Safety Stock');
			}
		}

		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				return back()->with('error', 'Initial Safety Stock with preferred due date already exist.')->with('page', 'Safety Stock');
			}
			else{
				return back()->with('error', $e->getMessage())->with('page', 'Safety Stock');
			}

		}
	}

	public function createInitial(Request $request)
	{
		$valid_date = date('Y-m-d', strtotime(str_replace('/','-', $request->get('valid_date'))));

		try
		{
			$id = Auth::id();
			$intial_stock = new InitialSafetyStock([
				'material_number' => $request->get('material_number'),
				'valid_date' => $valid_date,
				'quantity' => $request->get('quantity'),
				'created_by' => $id
			]);

			$intial_stock->save();  

			$response = array(
				'status' => true
			);
			return Response::json($response);
		}
		catch (QueryException $e){
			$error_code = $e->errorInfo[1];
			if($error_code == 1062){
				$response = array(
					'status' => false,
					'Message'=> 'already exist'
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false
				);
				return Response::json($response);
			}
		}
	}

	public function delete(Request $request)
	{
		$intial_stock = InitialSafetyStock::where('id', '=', $request->get("id"))
		->forceDelete();

		$response = array(
			'status' => true
		);

		return Response::json($response);
	}

	public function destroy(Request $request)
	{
		$valid_date = date('Y-m-d', strtotime('01-'.$request->get('valid_date2')));

		$materials = Material::whereIn('origin_group_code', $request->get('origin_group2'))
		->select('material_number')->get();

		$intial_stock = InitialSafetyStock::where('valid_date', '=', $valid_date)
		->whereIn('material_number', $materials)
		->forceDelete();

		return redirect('/index/safety_stock')
		->with('status', 'Initial Safety Stock has been deleted.')
		->with('page', 'Safety Stock');
	}
}
