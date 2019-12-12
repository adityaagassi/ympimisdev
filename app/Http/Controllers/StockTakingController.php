<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;
use Response;
use File;
use App\MaterialPlantDataList;
use App\BomOutput;
use App\StocktakingSilverList;
use App\StocktakingSilverLog;

class StockTakingController extends Controller
{
	public function __construct()
	{
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

		$this->base_unit = [
			'PC',
			'L',
			'SET',
			'KG',
			'G',
			'M',
			'SHT',
			'DS',
			'CAN',
			'CS',
			'BT',
			'DZ',
			'ROL',
			'BAG',
			'PAA'
		];
	}

	//Stock Taking Bulanan

	public function mpdl() {
		$title = 'Material Plant data List';
		$title_jp = '???';

		return view('stocktakings.mpdl', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'storage_locations' => $this->storage_location,
			'base_units' => $this->base_unit,
		))->with('page', 'Material Plant Data List')->with('head', 'MPDL');
	}

	public function fetchmpdl(Request $request){
		$material_plant_data_lists = MaterialPlantDataList::orderBy('material_plant_data_lists.material_number', 'asc');

		if($request->get('storage_location') != null){
			$material_plant_data_lists = $material_plant_data_lists->whereIn('material_plant_data_lists.storage_location', $request->get('storage_location'));
		}

		if($request->get('base_unit') != null){
			$material_plant_data_lists = $material_plant_data_lists->whereIn('material_plant_data_lists.bun', $request->get('base_unit'));
		}

		$material_plant_data_lists = $material_plant_data_lists->select('material_plant_data_lists.material_number', 'material_plant_data_lists.material_description', 'material_plant_data_lists.bun', 'material_plant_data_lists.spt', 'material_plant_data_lists.storage_location', 'material_plant_data_lists.valcl', 'material_plant_data_lists.standard_price')
		->get();

		return DataTables::of($material_plant_data_lists)->make(true);
	}

	public function bom_output(){
		$title = 'BOM Output';
		$title_jp = '???';

		return view('stocktakings.bomoutput', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'base_units' => $this->base_unit,
		))->with('page', 'BOM Output')->with('head', 'BOM');
	}
	
	public function fetch_bom_output(Request $request){
		$bom_outputs = BomOutput::orderBy('bom_outputs.id', 'asc');

		// if($request->get('base_unit') != null){
		// 	$bom_outputs = $bom_outputs->whereIn('bom_outputs.um', $request->get('base_unit'));
		// }

		$bom_outputs = $bom_outputs->select('bom_outputs.material_parent', 'bom_outputs.material_child', 'bom_outputs.usage', 'bom_outputs.um')
		->get();

		return DataTables::of($bom_outputs)->make(true);
	}


	//Stock Taking Silver

	public function indexSilver($id){
		if($id == 'fl_assembly'){
			$title = 'Silver Stock Taking (Flute Assembly)';
			$title_jp = 'FL組み立て職場の銀材棚卸';
			$location = 'FL ASSEMBLY';
		}

		if($id == 'fl_middle'){
			$title = 'Silver Stock Taking (Flute Middle)';
			$title_jp = 'FL中間工程の銀材棚卸';
			$location = 'FL MIDDLE';
		}

		if($id == 'fl_welding'){
			$title = 'Silver Stock Taking (Flute Welding)';
			$title_jp = 'FL溶接職場の銀材棚卸';
			$location = 'FL WELDING';

		}

		if($id == 'fl_bpro'){
			$title = 'Silver Stock Taking (Flute Body Process)';
			$title_jp = 'FL管体職場の銀材棚卸';
			$location = 'FL BODY PROCESS';
		}

		if($id == 'fl_mpro'){
			$title = 'Silver Stock Taking (Flute Material Process)';
			$title_jp = 'FL部品加工職場の銀材棚卸';
			$location = 'FL MATERIAL PROCESS';
		}

		return view('stocktakings.silver', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
		))->with('page', 'Stock Taking')->with('head', 'Silver');
	}

	public function indexSilverReport(){
		$title = 'Silver Stocktaking Report';
		$title_jp = '???';

		return view('stocktakings.silver_report', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Stock Taking')->with('head', 'Silver');
	}

	public function fetchSilverReport(Request $request){
		Carbon::setWeekStartsAt(Carbon::SUNDAY);
		Carbon::setWeekEndsAt(Carbon::SATURDAY);

		if($request->get('datefrom') != ""){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		else{
			$datefrom = date('Y-m-d', strtotime(Carbon::now()->subDays(14)));
		}

		if($request->get('dateto') != ""){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}
		else{
			$dateto = date('Y-m-d', strtotime(Carbon::now()->addDays(1)));
		}

		$query = "select stock_date as order_date, date_format(stock_date, '%d-%b-%Y') as stock_date, storage_location, sum(variance) as variance, sum(ok) as ok from
		(
		select material_number, material_description, storage_location, if(sum(pi)-sum(book) <> 0, 1, 0) as variance, if(sum(pi)-sum(book) <> 0, 0, 1) as ok, stock_date from
		(
		select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_silver_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_silver_lists) and storage_location_stocks.stock_date >= '".$datefrom."' and storage_location_stocks.stock_date <= '".$dateto."'

		union all

		select stocktaking_silver_logs.material_number, stocktaking_silver_logs.material_description, stocktaking_silver_logs.storage_location, 0 as book, stocktaking_silver_logs.quantity as pi, date(created_at) as stock_date from stocktaking_silver_logs where date(created_at) >= '".$datefrom."' and date(created_at) <= '".$dateto."') as variance group by material_number, material_description, storage_location, stock_date) as variance_count group by storage_location, stock_date order by order_date desc";

		$variances = db::select($query);

		$response = array(
			'status' => true,
			'variances' => $variances,
		);
		return Response::json($response);
	}

	public function fetchSilverReportModal(Request $request){
		$stock_date = date('Y-m-d', strtotime($request->get('date')));


		$loc = " having storage_location = '" . $request->get('loc') . "'";

		if( $request->get('loc') == 'all'){
			$loc = "";
		}

		$query = "select material_number, material_description, storage_location, sum(pi) as pi, sum(book) as book, sum(pi)-sum(book) as diff_qty, ABS(sum(pi)-sum(book)) as diff_abs from
		(
		select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_silver_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_silver_lists) and storage_location_stocks.stock_date = '".$stock_date."'

		union all

		select stocktaking_silver_logs.material_number, stocktaking_silver_logs.material_description, stocktaking_silver_logs.storage_location, 0 as book, stocktaking_silver_logs.quantity as pi, date(created_at) as stock_date from stocktaking_silver_logs where date(stocktaking_silver_logs.created_at) = '".$stock_date."') as variance 
		group by material_number, material_description, storage_location
		".$loc."
		order by diff_abs desc, diff_qty asc";

		$variance = DB::select($query);

		$response = array(
			'status' => true,
			'variance' => $variance,
		);
		return Response::json($response);
	}

	public function fetchSilverCount(Request $request){

		$count = StocktakingSilverList::where('stocktaking_silver_lists.id', '=', $request->get('id'))
		->select('stocktaking_silver_lists.material_number', 'stocktaking_silver_lists.category', 'stocktaking_silver_lists.id', 'stocktaking_silver_lists.material_description', 'stocktaking_silver_lists.quantity_check')
		->first();

		$response = array(
			'status' => true,
			'count' => $count,
		);
		return Response::json($response);
	}

	public function inputSilverCount(Request $request){
		try{

			$count = StocktakingSilverList::where('stocktaking_silver_lists.id', '=', $request->get('id'))
			->first();

			$count->quantity_check = $request->get('count');
			$count->save();

			$response = array(
				'status' => true,
				'message' => 'PI Count Confirmed',
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function inputSilverFinal(Request $request){
		try{
			$now = date('Y-m-d');
			$id = Auth::id();

			$lists = StocktakingSilverList::where('location', '=', $request->get('location'))
			->where('stocktaking_silver_lists.quantity_check', '>', 0)
			->get();

			if(count($lists) <= 0){
				$response = array(
					'status' => false,
					'message' => 'Resume stocktaking kosong.'
				);
				return Response::json($response);				
			}

			$zero_quantity_final = DB::table('stocktaking_silver_lists')
			->where('location', '=', $request->get('location'))
			->update([
				'quantity_final' => 0,
			]);

			$delete_logs = DB::delete('delete from stocktaking_silver_logs where location = "'.$request->get('location').'" and date(created_at) = "'.$now.'"');

			$update_final = DB::table('stocktaking_silver_lists')
			->where('location', '=', $request->get('location'))
			->where('stocktaking_silver_lists.quantity_check', '>', 0)
			->update([
				'quantity_final' => db::raw('quantity_check'),
				'quantity_check' => 0,
			]);

			$update_log_assy = DB::insert("insert into stocktaking_silver_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
				select location, material_child, material_child_description, storage_location, round(sum(quantity), 6) as quantity, '".$id."' as created_by, '".date('Y-m-d H:i:s')."' as created_at, '".date('Y-m-d H:i:s')."' as updated_at from
				(
				select stocktaking_silver_lists.location, stocktaking_silver_boms.material_child, stocktaking_silver_boms.material_child_description, stocktaking_silver_lists.storage_location, stocktaking_silver_lists.quantity_final*stocktaking_silver_boms.`usage` as quantity from stocktaking_silver_lists left join stocktaking_silver_boms on stocktaking_silver_boms.material_parent = stocktaking_silver_lists.material_number where stocktaking_silver_lists.quantity_final > 0 and stocktaking_silver_lists.location = '".$request->get('location')."' and stocktaking_silver_lists.category = 'ASSY'
			) as assy group by location, material_child, material_child_description, storage_location");

			$update_log_single = DB::insert("insert into stocktaking_silver_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
				select location, material_number, material_description, storage_location, quantity, '".$id."' as created_by, '".date('Y-m-d H:i:s')."' as created_at, '".date('Y-m-d H:i:s')."' as updated_at from
				(
				select stocktaking_silver_lists.location, stocktaking_silver_lists.material_number, stocktaking_silver_lists.material_description, stocktaking_silver_lists.storage_location, round(sum(stocktaking_silver_lists.quantity_final),6) as quantity from stocktaking_silver_lists where stocktaking_silver_lists.quantity_final > 0 and stocktaking_silver_lists.location = '".$request->get('location')."' and stocktaking_silver_lists.category = 'SINGLE' group by stocktaking_silver_lists.location, stocktaking_silver_lists.material_number, stocktaking_silver_lists.material_description, stocktaking_silver_lists.storage_location
			) as single");

			$response = array(
				'status' => true,
				'message' => 'PI Calculated',
				'lists' => $lists,
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchSilverResume(Request $request){

		$lists = StocktakingSilverList::where('location', '=', $request->get('location'))
		->where(db::raw('stocktaking_silver_lists.quantity_check+stocktaking_silver_lists.quantity_final'), '>', 0)
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}

	public function fetchSilverList(Request $request){

		$lists = StocktakingSilverList::where('location', '=', $request->get('location'))
		->select('stocktaking_silver_lists.material_number', 'stocktaking_silver_lists.category', 'stocktaking_silver_lists.id', 'stocktaking_silver_lists.material_description')
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}
}
