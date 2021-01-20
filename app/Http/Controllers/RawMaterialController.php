<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\StorageLocationStock;
use App\MaterialListByModel;
use App\RawMaterialStock;
use App\MaterialUsage;
use App\StocktakingCalendar;
use App\StocktakingLocationStock;
use App\StocktakingMaterialForecast;
use Carbon\Carbon;
use DataTables;
use Response;
use File;


class RawMaterialController extends Controller{

	private $storage_location;

	public function __construct(){

		$this->middleware('auth');
		$this->storage_location = [
			'203',
			'208',
			'214',
			'216',
			'217',
			'218',
			'401',
			'CL21',
			'CL51',
			'CL61',
			'CL91',
			'CLA0',
			'CLA2',
			'CLB9',
			'CS91',
			'FA0R',
			'FA1R',
			'FL21',
			'FL51',
			'FL61',
			'FL91',
			'FLA0',
			'FLA1',
			'FLA2',
			'FLT9',
			'FSTK',
			'LA0R',
			'MINS',
			'MMJR',
			'MNCF',
			'MS11',
			'MSCR',
			'MSTK',
			'OTHR',
			'PN91',
			'PNR4',
			'PNR9',
			'RC11',
			'RC91',
			'SA0R',
			'SA1R',
			'SX21',
			'SX51',
			'SX61',
			'SX91',
			'SXA0',
			'SXA1',
			'SXA2',
			'SXBR',
			'SXT9',
			'SXWH',
			'VA0R',
			'VN11',
			'VN21',
			'VN51',
			'VN91',
			'VNA0',
			'WCL',
			'WCS',
			'WFL',
			'WFTP',
			'WHST',
			'WLST',
			'WPCS',
			'WPN',
			'WPPN',
			'WPRC',
			'WPRS',
			'WRC',
			'WSCR',
			'WSTP',
			'WSX',
			'YCJP',
			'YCJR',
			'ZPA0'
		];
	}

	public function indexUsage(){
		$storage_locations = $this->storage_location;
		return view('raw_materials.material_usage', array(
			'storage_locations' => $storage_locations,
		))->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
	}

	public function indexStorage(){
		$storage_locations = $this->storage_location;
		return view('raw_materials.storage_location_stock', array(
			'storage_locations' => $storage_locations,
		))->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
	}

	public function indexSmbmr(){
		return view('raw_materials.material_list_by_model')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
	}

	public function fetchStorage(Request $request){
		$storage_location_stocks = StorageLocationStock::orderBy('storage_location_stocks.material_number', 'asc');

		if(strlen($request->get('dateFrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
			$storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '>=', $date_from);
		}

		if(strlen($request->get('dateTo')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateTo')));
			$storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '<=', $date_to);
		}

		if($request->get('storage_location') != null){
			$storage_location_stocks = $storage_location_stocks->whereIn('storage_location_stocks.storage_location', $request->get('storage_location'));
		}

		if(strlen($request->get('dateFrom')) <= 0 && strlen($request->get('dateTo')) <= 0 && $request->get('storage_location') == null){
			$storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '=', date("Y-m-d"));
		}

		$storage_location_stocks = $storage_location_stocks->select('storage_location_stocks.material_number', 'storage_location_stocks.material_description', 'storage_location_stocks.storage_location', 'storage_location_stocks.unrestricted', 'storage_location_stocks.download_date', 'storage_location_stocks.download_time', 'storage_location_stocks.stock_date')
		->get();

		return DataTables::of($storage_location_stocks)->make(true);
	}

	public function fetchSmbmr(Request $request){
		$material_list_by_models = MaterialListByModel::orderBy('material_list_by_models.material_parent', 'asc');

		if(strlen($request->get('material_parent')) > 0){
			$material_parent = explode(",", $request->get('material_parent'));
			$material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.material_parent', $material_parent);
		}

		if(strlen($request->get('material_child')) > 0){
			$material_child = explode(",", $request->get('material_child'));
			$material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.material_child', $material_child);
		}

		if(strlen($request->get('vendor')) > 0){
			$vendor = explode(",", $request->get('vendor'));
			$material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.vendor', $vendor);
		}

		$material_list_by_models = $material_list_by_models->select('material_list_by_models.material_parent', 'material_list_by_models.material_parent_description', 'material_list_by_models.material_child', 'material_list_by_models.material_child_description', 'material_list_by_models.uom', 'material_list_by_models.purg', 'material_list_by_models.usage', 'material_list_by_models.vendor')
		->get();

		return DataTables::of($material_list_by_models)->make(true);
	}

	public function fetchUsage(Request $request){
		$material_usages = MaterialUsage::orderBy('material_usages.material_number', 'asc');

		if(strlen($request->get('material_number')) > 0){
			$material_number = explode(",", $request->get('material_number'));
			$material_usages = $material_usages->whereIn('material_usages.material_number', $material_number);
		}

		if(strlen($request->get('dateFrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
			$material_usages = $material_usages->where('material_usages.due_date', '>=', $date_from);
		}

		if(strlen($request->get('dateTo')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateTo')));
			$material_usages = $material_usages->where('material_usages.due_date', '<=', $date_to);
		}

		$material_usages = $material_usages->select('material_usages.material_number', 'material_usages.material_description', 'material_usages.due_date', 'material_usages.usage')
		->get();

		return DataTables::of($material_usages)->make(true);
	}

	public function calculateUsage(Request $request){
		if(strlen($request->get('validMonth')) > 0){
			try{
				$delete_usage = MaterialUsage::where(db::raw('date_format(material_usages.due_date, "%m-%Y")'), '=', $request->get('validMonth'))->forceDelete();
				$id = Auth::id();
				$now = date("Y-m-d H:i:s");

				DB::insert("insert into material_usages(material_number, material_description, due_date, `usage`, created_by, created_at, updated_at)
					select material_child as material_number, material_child_description as material_description, due_date, round(sum(`usage`),6) as `usage`, '".$id."' as created_by, '".$now."' as created_at, '".$now."' as updated_at from
					(
					select material_list_by_models.material_child, material_list_by_models.material_child_description, production_schedules.due_date, round(production_schedules.quantity*material_list_by_models.`usage`, 6) as `usage` from production_schedules inner join material_list_by_models on production_schedules.material_number = material_list_by_models.material_parent where date_format(production_schedules.due_date, '%m-%Y') = '".$request->get('validMonth')."'
				) as materials group by material_child, material_child_description, due_date");

				return redirect('/index/material/usage')->with('success', 'Material usage have been calculated')->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
			}
			catch(\Exception $e){
				return redirect('/index/material/usage')->with('error', $e->getMessage())->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
			}
		}
		else{
			return redirect('/index/material/usage')->with('error', 'Please select a Month')->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
		}
	}

	public function importStorage(Request $request){
		if($request->hasFile('storage_location_stock') && strlen($request->get('date_stock')) > 0){
			try{
				$stock_date = date('Y-m-d', strtotime($request->get('date_stock')));
				$delete_storage = StorageLocationStock::where('storage_location_stocks.stock_date', '=', $stock_date)->forceDelete();
				$raw_material = RawMaterialStock::where('raw_material_stocks.stock_date', '=', $stock_date)->forceDelete();
				$forecast = StocktakingMaterialForecast::where('stocktaking_material_forecasts.created_by', '=', 1)->forceDelete();
				
				$id = Auth::id();

				$file = $request->file('storage_location_stock');
				$data = file_get_contents($file);

				$calendar = StocktakingCalendar::where('date', $stock_date)->first();
				$insert_st_location_stock = false;
				if($calendar){
					if($calendar->status != 'finished'){
						StocktakingLocationStock::truncate();
						$insert_st_location_stock = true;
					}
				}

				$month = date('Y-m', strtotime($request->get('date_stock')));
				$calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)->first();
				
				$insert_st_forecast = false;
				if($calendar){
					$yesterday_st = date('Y-m-d', strtotime('yesterday', strtotime($calendar->date)));

					if($stock_date == $yesterday_st){
						$insert_st_forecast = true;
					}
				}

				$rows = explode("\r\n", $data);
				foreach ($rows as $row){
					if(strlen($row) > 0){
						$row = explode("\t", $row);
						if($row[0] != 'Material' && str_replace('"','',str_replace(',','',$row[3])) > 0){
							if(strlen($row[0]) == 6){
								$material_number = "0" . $row[0];
							}
							elseif(strlen($row[0]) == 5){
								$material_number = "00" . $row[0];
							}
							else{
								$material_number = $row[0];
							}

							$storage_location_stock = new StorageLocationStock([
								'material_number' => $material_number,
								'material_description' => $row[1],
								'storage_location' => $row[2],
								'unrestricted' => str_replace('"','',str_replace(',','',$row[3])),
								'download_date' => date('Y-m-d', strtotime($row[4])),
								'download_time' => date('H:i:s', strtotime(str_replace('/','-',$row[5]))),
								'stock_date' => $stock_date,
								'created_by' => $id,
							]);
							$storage_location_stock->save();


							if($insert_st_location_stock){
								$st_location_stock = new StocktakingLocationStock([
									'material_number' => $material_number,
									'material_description' => $row[1],
									'storage_location' => $row[2],
									'unrestricted' => str_replace('"','',str_replace(',','',$row[3])),
									'download_date' => date('Y-m-d', strtotime($row[4])),
									'download_time' => date('H:i:s', strtotime(str_replace('/','-',$row[5]))),
									'stock_date' => $stock_date,
									'created_by' => $id,
								]);
								$st_location_stock->save(); 
							}

							if($insert_st_forecast){

								$ins_or_upd = StocktakingMaterialForecast::updateOrCreate(
									['material_number' => $material_number],
									['created_by' => 1, 'updated_at' => Carbon::now()]
								);
								$ins_or_upd->save();

							}
						}
					}
				}

				$now = date("Y-m-d H:i:s");

				// DB::insert("
				// 	insert into raw_material_stocks(material_number, material_description, storage_location, quantity, stock_date, created_by, created_at, updated_at)
				// 	select material_number, material_description, storage_location, round(sum(quantity),6) as quantity, stock_date, '".$id."' as created_by, '".$now."' as created_at, '".$now."' as updated_at from 
				// 	(
				// 	select if(material_list_by_models.material_parent is null, storage_location_stocks.material_number, material_list_by_models.material_child) as material_number, if(material_list_by_models.material_parent is null, storage_location_stocks.material_description, material_list_by_models.material_child_description) as material_description, storage_location_stocks.storage_location, if(material_list_by_models.material_parent is null, storage_location_stocks.unrestricted, storage_location_stocks.unrestricted*material_list_by_models.usage) as quantity, storage_location_stocks.stock_date from storage_location_stocks left join material_list_by_models on material_list_by_models.material_parent = storage_location_stocks.material_number where storage_location_stocks.stock_date = '".$stock_date."'
				// 	) as raw_material_stocks group by material_number, material_description, storage_location, stock_date
				// 	");

				return redirect('/index/material/storage')->with('success', 'Storage Location Stock Uploaded')->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
			}
			catch(\Exception $e){
				return redirect('/index/material/storage')->with('error', $e->getMessage())->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
			}

		}
		else{
			return redirect('/index/material/storage')->with('error', 'Stock Date and File must be selected.')->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
		}
	}

	public function importSmbmr(Request $request){
		if($request->hasFile('smbmr')){
			try{
				MaterialListByModel::truncate();
				$id = Auth::id();

				$file = $request->file('smbmr');
				$data = file_get_contents($file);

				$rows = explode("\r\n", $data);
				foreach ($rows as $row){
					if(strlen($row) > 0){
						$row = explode("\t", $row);
						if($row[0] != 'Plant' && strlen($row[0] > 0)){
							if(strlen($row[5]) == 6){
								$material_child = "0" . $row[5];
							}
							elseif(strlen($row[5]) == 5){
								$material_child = "00" . $row[5];
							}
							else{
								$material_child = $row[5];
							}

							$material_list_by_models = new MaterialListByModel([
								'material_parent' => $row[1],
								'material_parent_description' => $row[2],
								'material_child' => $material_child,
								'material_child_description' => $row[6],
								'uom' => $row[7],
								'purg' => $row[8],
								'usage' => str_replace('"','',str_replace(',','',$row[17])),
								'vendor' => $row[32],
								'created_by' => $id,
							]);
							$material_list_by_models->save();
						}
					}
				}

				return redirect('/index/material/smbmr')->with('success', 'Material List By Model Uploaded')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
			}
			catch(\Exception $e){
				return redirect('/index/material/smbmr')->with('error', $e->getMessage())->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
			}

		}
		else{
			return redirect('/index/material/smbmr')->with('error', 'Please select a file.')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
		}
	}
}
