<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Yajra\DataTables\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\EmployeeSync;
use App\MaterialPlantDataList;
use App\StorageLocation;
use App\BomOutput;
use App\StocktakingCalendar;
use App\StocktakingList;
use App\StocktakingOutput;
use App\StocktakingInquiryLog;
use App\StocktakingOutputLog;
use App\StocktakingSilverList;
use App\StocktakingSilverLog;
use App\TransactionTransfer;
use App\ErrorLog;
use Carbon\Carbon;
use DataTables;
use Response;
use Excel;
use DateTime;
use File;
use FTP;
use PDF;


class StockTakingController extends Controller{

	private $assy_output = array();
	private $cek = array();
	private $temp = array();

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
	public function indexMonthlyStocktaking(){
		$title = 'Monthly Stocktaking';
		$title_jp = '月次棚卸';

		return view('stocktakings.monthly.index', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
	}

	public function indexManageStore(){
		$title = 'Manage Store';
		$title_jp = 'ストアー管理';

		$groups = StorageLocation::select('area')
		->whereNotNull('area')
		->distinct()
		->orderBy('area', 'ASC')
		->get();

		$locations = StocktakingList::select('stocktaking_lists.location')
		->distinct()
		->orderBy('stocktaking_lists.location', 'ASC')
		->get();

		$stores = StocktakingList::select('stocktaking_lists.store')
		->distinct()
		->orderBy('stocktaking_lists.store', 'ASC')
		->get();

		$materials = MaterialPlantDataList::select('material_number', 'material_description')->get();

		return view('stocktakings.monthly.manage_store', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'groups' => $groups,
			'locations' => $locations,
			'stores' => $stores,
			'materials' => $materials
		))->with('page', 'Manage Store')->with('head', 'Stocktaking');
	}


	public function indexRevise(){
		$title = 'Revision';
		$title_jp = '改定';

		return view('stocktakings.monthly.revise', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Revise')->with('head', 'Stocktaking');
	}

	public function indexUnmatch($month){
		$title = 'Unmatch';
		$title_jp = 'チェック不適合';

		return view('stocktakings.monthly.unmatch', array(
			'month' => $month,
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Unmatch')->with('head', 'Stocktaking');
	}

	public function indexNoUse(){
		$title = 'No Use';
		$title_jp = '使用しない';

		return view('stocktakings.monthly.no_use', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'No Use')->with('head', 'Stocktaking');
	}

	public function indexCount(){
		$title = 'Monthly Stocktaking';
		$title_jp = '月次棚卸';

		$employees = EmployeeSync::get();

		return view('stocktakings.monthly.count', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees
		))->with('page', 'Monthly Stock Taking Count')->with('head', 'Stocktaking');
	}

	public function indexAudit($id){
		$title = 'Audit '. $id;
		$title_jp = '監査 '. $id;

		if($id == 1){
			return view('stocktakings.monthly.audit_1', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'Monthly Stock Audit 1')->with('head', 'Stocktaking');

		}else if($id == 2){
			$auditors = EmployeeSync::orwhere('position', 'like', '%Staff%')
			->orWhere('position', '=', 'Chief')
			->orWhere('position', '=', 'Foreman')
			->orWhere('position', '=', 'Coordinator')
			->WhereNotNull('end_date')
			->get();

			return view('stocktakings.monthly.audit_2', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'auditors' => $auditors
			))->with('page', 'Monthly Stock Audit 2')->with('head', 'Stocktaking');
		}	
	}

	public function indexSummaryOfCounting(){
		$title = 'Summary of Counting';
		$title_jp = '計算まとめ';

		return view('stocktakings.monthly.summary_of_counting', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Summary Of Counting')->with('head', 'Stocktaking');
	}

	public function indexCountPI(){

		$data = db::select("SELECT DISTINCT process FROM stocktaking_lists");

		if(count($data) > 1){
			$response = array(
				'status' => false,
				'message' => 'Proses tidak sesuai urutan',
			);
			return Response::json($response);
		}else{
			if($data[0]->process < 3){
				$response = array(
					'status' => false,
					'message' => 'Proses tidak sesuai urutan',
				);
				return Response::json($response);
			}
		}

		try{
			DB::transaction(function() {
				StocktakingOutput::truncate();
				$update = StocktakingList::where('process', 3)->update(['process' => 4]);
			});

			$this->countPISingle();
			$this->countPIAssy();

			$response = array(
				'status' => true,
				'message' => 'Count PI Berhasil'
			);
			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function countPISingle(){
		$single = StocktakingList::where('category', 'SINGLE')->get();

		for ($i=0; $i < count($single); $i++) {

			$insert = new StocktakingOutput([
				'material_number' => $single[$i]->material_number,
				'store' => $single[$i]->store,
				'location' => $single[$i]->location,
				'quantity' => $single[$i]->final_count
			]);
			$insert->save();
		}
	}

	public function countPIAssy(){

		$assy = db::select("SELECT s.material_number, s.store, s.location, s.final_count, b.material_child, b.`usage`, b.divider, m.spt, (s.final_count*(b.`usage`/b.divider)) as quantity FROM stocktaking_lists s
			left join bom_outputs b on s.material_number = b.material_parent
			left join material_plant_data_lists m on m.material_number = b.material_child 
			where s.category = 'ASSY'");


		for ($i=0; $i < count($assy); $i++) {

			if($assy[$i]->spt == 50){
				$row = array();
				$row['material_number'] = $assy[$i]->material_child;
				$row['store'] = $assy[$i]->store;
				$row['location'] = $assy[$i]->location;
				$row['quantity'] = $assy[$i]->quantity;
				$row['created_at'] = Carbon::now();
				$row['updated_at'] = Carbon::now();

				$this->cek[] = $row;
			}else{
				$row = array();
				$row['material_number'] = $assy[$i]->material_child;
				$row['store'] = $assy[$i]->store;
				$row['location'] = $assy[$i]->location;
				$row['quantity'] = $assy[$i]->quantity;
				$row['created_at'] = Carbon::now();
				$row['updated_at'] = Carbon::now();

				$this->assy_output[] = $row;

			}
		}

		while(count($this->cek) > 0) {
			$this->breakdown();
		}

		foreach (array_chunk($this->assy_output,1000) as $t) {
			$output = StocktakingOutput::insert($t);
		}


	}

	public function breakdown(){

		$this->temp = array();

		for ($i=0; $i < count($this->cek); $i++) {
			$breakdown = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, m.spt
				FROM bom_outputs b
				LEFT JOIN material_plant_data_lists m ON m.material_number = b.material_child 
				WHERE b.material_parent = '".$this->cek[$i]['material_number']."'");

			for ($j=0; $j < count($breakdown); $j++) {

				if($breakdown[$j]->spt == 50){
					$row = array();
					$row['material_number'] = $breakdown[$j]->material_child;
					$row['store'] = $this->cek[$i]['store'];
					$row['location'] = $this->cek[$i]['location'];
					$row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider) ;
					$row['created_at'] = Carbon::now();
					$row['updated_at'] = Carbon::now();
					$this->temp[] = $row;
				}else{
					$row = array();
					$row['material_number'] = $breakdown[$j]->material_child;
					$row['store'] = $this->cek[$i]['store'];
					$row['location'] = $this->cek[$i]['location'];
					$row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider) ;
					$row['created_at'] = Carbon::now();
					$row['updated_at'] = Carbon::now();
					$this->assy_output[] = $row;
				}
			}
		}

		$this->cek = array();
		$this->cek = $this->temp;

	}

	public function printSummaryOfCounting(Request $request){

		$store = '';
		if(strlen($request->get('store')) > 0){
			$stores = explode(',', $request->get('store'));
			for ($i=0; $i < count($stores); $i++) {
				$store = $store."'".$stores[$i]."'";
				if($i != (count($stores)-1)){
					$store = $store.',';
				}
			}
			$store = " WHERE s.store in (".$store.") ";
		}

		try {
			$lists = db::select("SELECT
				s.id,
				s.store, 
				s.category,
				s.material_number,
				mpdl.material_description,
				m.`key`,
				m.model,
				m.surface,
				mpdl.bun,
				s.location,
				mpdl.storage_location,
				v.lot_completion,
				v.lot_transfer,
				IF
				( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
				FROM
				stocktaking_lists s
				LEFT JOIN materials m ON m.material_number = s.material_number
				LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
				LEFT JOIN material_volumes v ON v.material_number = s.material_number"
				.$store.
				"ORDER BY s.store, s.id ASC");

			$number = 0;
			$store = '';
			foreach ($lists as $list) {

				if($list->store == $store){
					$number++;
				}else{
					$store = $list->store;
					$number = 1;
				}

				$this->printSummary($list, $number);
			}


			$response = array(
				'status' => true,
				'message' => 'Print Successful'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function reprintStoreSoc(Request $request){
		$store = $request->get('store');

		try {
			$lists = db::select("SELECT
				s.id,
				s.store, 
				s.category,
				s.material_number,
				mpdl.material_description,
				m.`key`,
				m.model,
				m.surface,
				mpdl.bun,
				s.location,
				mpdl.storage_location,
				v.lot_completion,
				v.lot_transfer,
				IF
				( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
				FROM
				stocktaking_lists s
				LEFT JOIN materials m ON m.material_number = s.material_number
				LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
				LEFT JOIN material_volumes v ON v.material_number = s.material_number
				WHERE s.store = '".$store."'
				ORDER BY s.store, s.id ASC");

			$number = 0;

			foreach ($lists as $list) {
				$number++;
				$this->printSummary($list, $number);
			}


			$response = array(
				'status' => true,
				'message' => 'Print Successful'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function reprintIdSoc(Request $request){
		$id = $request->get('id');

		try {
			$lists = db::select("SELECT
				s.id,
				s.store, 
				s.category,
				s.material_number,
				mpdl.material_description,
				m.`key`,
				m.model,
				m.surface,
				mpdl.bun,
				s.location,
				mpdl.storage_location,
				v.lot_completion,
				v.lot_transfer,
				IF
				( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
				FROM
				stocktaking_lists s
				LEFT JOIN materials m ON m.material_number = s.material_number
				LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
				LEFT JOIN material_volumes v ON v.material_number = s.material_number
				WHERE s.id = ".$id."
				ORDER BY s.store, s.id ASC");

			$stores = StocktakingList::where('store', $lists[0]->store)->get();

			$number = 0;
			foreach ($stores as $store) {
				$number++;
				if($store->id == $lists[0]->id){
					break;
				}
			}

			foreach ($lists as $list) {
				$this->printSummary($list, $number);
			}


			$response = array(
				'status' => true,
				'message' => 'Print Successful'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}


	}

	public function printSummary($list, $number){
		$printer_name = 'MIS';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$id = $list->id;
		$store = $list->store;
		$category = '('.$list->category.')';
		$material_number = $list->material_number;
		$sloc = $list->location;
		$description = $list->material_description;
		$key = $list->key;
		$model = $list->model;
		$surface = $list->surface;
		$uom = $list->bun;
		$lot = $list->lot;

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("  Summary of Counting  "."\n");
		$printer->initialize();
		$printer->setTextSize(3, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($store."\n");
		if($list->category == 'ASSY'){
			$printer->setReverseColors(true);			
		}
		
		$printer->text($category."\n");
		$printer->feed(1);
		$printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material_number."\n");

		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(3, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($sloc."\n\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->text($model."-".$key."-".$surface."\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);

		$printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
		$printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
		$printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
		$printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
		$printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
		$printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
		$printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");

		$printer->feed(1);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_RIGHT);
		$printer->text("(".$number.")".str_repeat(" ", 22).Carbon::now()."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function printSummaryBackup($list){
		$printer_name = 'MIS';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		// $id = '136';
		// $store = 'SUBASSY-CL-2B';
		// $category = '(ASSY)';
		// $material_number = 'W528860';
		// $sloc = 'CL91';
		// $description = 'CL-250N 7 ASSY CORK&PAD PACKED(YMPI) J';
		// $key = '7';
		// $model = 'CL250';
		// $surface = 'NICKEL';
		// $uom = 'PC';
		// $lot = '';

		$id = $list->id;
		$store = $list->store;
		$category = '('.$list->category.')';
		$material_number = $list->material_number;
		$sloc = $list->location;
		$description = $list->material_description;
		$key = $list->key;
		$model = $list->model;
		$surface = $list->surface;
		$uom = $list->bun;
		$lot = $list->lot;

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("  Summary of Counting  "."\n");
		$printer->initialize();
		$printer->setTextSize(3, 3);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($store."\n");
		if($list->category == 'ASSY'){
			$printer->setReverseColors(true);			
		}
		$printer->text($category."\n");
		$printer->feed(1);
		$printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material_number."\n");
		$printer->text($sloc."\n\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->text($model."-".$key."-".$surface."\n");
		if(strlen($lot) == 0){
			$printer->text("Lot: \xDB\xDB ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat("\xDB", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		else{
			$printer->text("Lot: ".$lot." ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat(" ", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
		$printer->textRaw("\xb3Z1 :".str_repeat(" ", 18)."\xb3\n");
		$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		$printer->feed(1);
		$printer->setTextSize(1, 1);
		$printer->setJustification(Printer::JUSTIFY_RIGHT);
		$printer->text(Carbon::now()."\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function exportOfficailVariance(Request $request){

		$month = $request->get('month_official_variance');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar->status == 'finished'){
			$variances = db::select("SELECT plnt, `group`, location, sum(pi_amt) AS sumof_pi_amt, sum(book_amt) AS sumof_book_amt, sum(diff_amt) AS sumof_diff_amt, sum(var_amt_min) AS sumof_var_amt_min, sum(var_amt_plus) AS sumof_var_amt_plus, sum(var_amt_abs) AS sumof_var_amt_abs, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 AS book FROM stocktaking_output_logs
				WHERE stocktaking_date = '".$calendar->date."'
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area IS NOT NULL
				AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')
				) AS official_variance
				GROUP BY plnt, `group`, location");
		}else{
			$variances = db::select("SELECT plnt, `group`, location, sum(pi_amt) AS sumof_pi_amt, sum(book_amt) AS sumof_book_amt, sum(diff_amt) AS sumof_diff_amt, sum(var_amt_min) AS sumof_var_amt_min, sum(var_amt_plus) AS sumof_var_amt_plus, sum(var_amt_abs) AS sumof_var_amt_abs, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area IS NOT NULL
				AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')
				) AS official_variance
				GROUP BY plnt, `group`, location");
		}

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'potrait');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('stocktakings.monthly.report.official_variance_pdf', array(
			'variances' => $variances
		));

		// $pdf = PDF::loadview('qc_report.print_cpar',['cpars'=>$cpars,'parts'=>$parts]);
		return $pdf->stream("OFFICIAL_VARIANCE_STOCKTAKING " +$month+ ".pdf");

		
	}

	public function exportInquiry(Request $request){

		$month = $request->get('month_inquiry');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar->status == 'finished'){
			$inquiries = db::select("SELECT
				stocktaking_inquiry_logs.id,
				stocktaking_inquiry_logs.location,
				storage_locations.area AS `group`,
				stocktaking_inquiry_logs.store,
				stocktaking_inquiry_logs.material_number,
				material_plant_data_lists.material_description,
				stocktaking_inquiry_logs.category,
				material_plant_data_lists.bun,
				stocktaking_inquiry_logs.quantity as final_count,
				date_format( stocktaking_inquiry_logs.updated_at, '%d-%M-%y' ) AS updated_at 
				FROM
				stocktaking_inquiry_logs
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_inquiry_logs.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_inquiry_logs.location
				WHERE stocktaking_inquiry_logs.stocktaking_date = '".$calendar->date."'
				ORDER BY storage_locations.area, stocktaking_inquiry_logs.location, stocktaking_inquiry_logs.material_number ASC");
		}else{
			$inquiries = db::select("SELECT
				stocktaking_lists.id,
				stocktaking_lists.location,
				storage_locations.area AS `group`,
				stocktaking_lists.store,
				stocktaking_lists.material_number,
				material_plant_data_lists.material_description,
				stocktaking_lists.category,
				material_plant_data_lists.bun,
				stocktaking_lists.final_count,
				date_format( stocktaking_lists.updated_at, '%d-%M-%y' ) AS updated_at 
				FROM
				stocktaking_lists
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_lists.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_lists.location
				ORDER BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.material_number ASC");
		}

		$title = 'Inquiry'.str_replace('-', '' ,$month).'_('.date('ymd H.i').')';

		$data = array(
			'inquiries' => $inquiries
		);

		ob_clean();
		Excel::create($title, function($excel) use ($data){
			$excel->sheet('Inquiry', function($sheet) use ($data) {
				return $sheet->loadView('stocktakings.monthly.report.inquiry_excel', $data);
			});
		})->export('xlsx');

	}

	public function exportVariance(Request $request){

		$month = $request->get('month_variance');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar->status == 'finished'){
			$variances = db::select("SELECT
				storage_locations.area AS `group`,
				storage_locations.plnt,
				material_plant_data_lists.valcl,
				pi_book.material_number,
				material_plant_data_lists.material_description,
				pi_book.location,
				storage_locations.location AS location_name,
				material_plant_data_lists.bun AS uom,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs,
				stocktaking_material_notes.note
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 AS book FROM stocktaking_output_logs
				WHERE stocktaking_date = '".$calendar->date."'
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				LEFT JOIN stocktaking_material_notes ON stocktaking_material_notes.material_number = pi_book.material_number
				WHERE storage_locations.area = 'Assembly'
				ORDER BY
				storage_locations.area,
				pi_book.location,
				pi_book.material_number ASC");

		}else{
			$variances = db::select("
				SELECT
				storage_locations.area AS `group`,
				storage_locations.plnt,
				material_plant_data_lists.valcl,
				pi_book.material_number,
				material_plant_data_lists.material_description,
				pi_book.location,
				storage_locations.location AS location_name,
				material_plant_data_lists.bun AS uom,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
				if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs,
				stocktaking_material_notes.note
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				LEFT JOIN stocktaking_material_notes ON stocktaking_material_notes.material_number = pi_book.material_number
				WHERE storage_locations.area = 'Assembly'
				ORDER BY
				storage_locations.area,
				pi_book.location,
				pi_book.material_number ASC");

		}


		$title = 'VarianceReport'.str_replace('-', '' ,$month).'_('.date('ymd H.i').')';		

		$data = array(
			'variances' => $variances
		);

		ob_clean();
		Excel::create($title, function($excel) use ($data){
			$excel->sheet('Variance', function($sheet) use ($data) {
				return $sheet->loadView('stocktakings.monthly.report.variance_excel', $data);
			});
		})->export('xlsx');
	}

	public function exportUploadSAP(Request $request){
		$month = $request->get('month');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar){
			$filename = 'ympipi_upload_' . str_replace('-', '', $calendar->date) . '.txt';
			$filepath = public_path() . "/uploads/sap/stocktaking/" . $filename;
			// $filepath = public_path('files') . $filename;
			$filedestination = "ma/ympipi/" . $filename;

			$datas = db::select("SELECT pi.plnt, pi.location, pi.cost_center, pi.material_number, pi.pi, book.book, (COALESCE(pi.pi,0) - COALESCE(book.book,0)) AS div_qty, ROUND(ABS((COALESCE(pi.pi,0) - COALESCE(book.book,0))), 3) AS div_abs, pi.date, if((COALESCE(pi.pi,0) - COALESCE(book.book,0)) > 0, '9671003', '9681003') AS type FROM
				(SELECT storage_locations.plnt, stocktaking_outputs.location, storage_locations.cost_center, stocktaking_outputs.material_number, date(stocktaking_outputs.created_at) AS date, sum( stocktaking_outputs.quantity ) AS pi
				FROM stocktaking_outputs
				LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_outputs.location 
				GROUP BY storage_locations.plnt, stocktaking_outputs.location, storage_locations.cost_center, stocktaking_outputs.material_number, date(stocktaking_outputs.created_at)) AS pi
				LEFT JOIN
				(SELECT storage_location, material_number, sum( unrestricted ) AS book FROM storage_location_stocks 
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS book
				ON pi.location = book.storage_location and pi.material_number = book.material_number
				HAVING div_abs > 0");


			$upload_text = "";
			foreach ($datas as $data){
				$upload_text .= $this->writeString($data->plnt, 15, " ");
				$upload_text .= $this->writeString($data->plnt, 4, " ");
				$upload_text .= $this->writeString($data->material_number, 18, " ");
				$upload_text .= $this->writeString($data->location, 4, " ");
				$upload_text .= $this->writeString($data->plnt, 4, " ");
				$upload_text .= $this->writeString($data->location, 4, " ");
				$upload_text .= $this->writeDecimal($data->div_abs, 13, "0");
				$upload_text .= $this->writeStringReserve($data->cost_center, 10, "0");
				$upload_text .= $this->writeString('', 10, " ");
				$upload_text .= $this->writeDate($data->date, "transfer");
				$upload_text .= $this->writeString('MB1C', 20, " ");
				$upload_text .= $this->writeString($data->type, 20, " ");
				$upload_text .= "\r\n";
			}

			try{
				File::put($filepath, $upload_text);
				// $success = self::uploadFTP($filepath, $filedestination);
				
				$response = array(
					'status' => true
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				// $transaction_error = TransactionTransfer::where('reference_file', '=', $filename)
				// ->update(['reference_file', '=', $filename]);

				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => '1'
				]);
				$error_log->save();
				

				$response = array(
					'status' => false
				);
				return Response::json($response);
			}
		}

	}

	public function exportLog(Request $request){
		$month = $request->get('month');

		try {
			$calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)
			->update([
				'status' => 'finished'
			]);

			$lists = StocktakingList::get();
			$outputs = StocktakingOutput::select('material_number', 'store', 'location', db::raw('sum(quantity) as quantity'))
			->groupBy('material_number', 'store', 'location')
			->get();

			$calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)->first();

			$insert_list = array();
			foreach ($lists as $list){
				$row = array();

				$row['store'] = $list['store'];
				$row['category'] = $list['category'];
				$row['material_number'] = $list['material_number'];
				$row['location'] = $list['location'];
				$row['quantity'] = $list['quantity'];
				$row['stocktaking_date'] = $calendar->date;
				$row['created_at'] = Carbon::now();
				$row['updated_at'] = Carbon::now();

				$insert_list[] = $row;
			}
			foreach ($insert_list as $t){			
				$insert = StocktakingInquiryLog::updateOrCreate(
					['store' => $t['store'], 'category' => $t['category'], 'material_number' => $t['material_number'], 'stocktaking_date' => $t['stocktaking_date']],
					['location' => $t['location'], 'quantity' => $t['quantity'], 'updated_at' => Carbon::now()]
				);
			}


			$insert_output = array();
			foreach ($outputs as $output){
				$row = array();

				$row['material_number'] = $output['material_number'];
				$row['store'] = $output['store'];	
				$row['location'] = $output['location'];
				$row['quantity'] = $output['quantity'];
				$row['stocktaking_date'] = $calendar->date;
				$row['created_at'] = Carbon::now();
				$row['updated_at'] = Carbon::now();

				$insert_output[] = $row;
			}		
			foreach ($insert_output as $t){
				$insert = StocktakingOutputLog::updateOrCreate(
					['store' => $t['store'], 'material_number' => $t['material_number'], 'stocktaking_date' => $t['stocktaking_date']],
					['location' => $t['location'], 'quantity' => $t['quantity'], 'updated_at' => Carbon::now()]
				);
			}


			StocktakingOutput::truncate();

			$list = StocktakingList::where('created_by', 1)
			->update([
				'process' => 0,
				'quantity' => null,
				'audit1' => null,
				'audit2' => null,
				'final_count' => null,
				'inputed_by' => null,
				'audit1_by' => null,
				'audit2_by' => null,
			]);



			$response = array(
				'status' => true
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false
			);
			return Response::json($response);
		}

	}

	public function fetchCheckMaterial(Request $request){
		$material = MaterialPlantDataList::where('material_number', $request->get('material'))->first();

		$response = array(
			'status' => true,
			'material' => $material
		);
		return Response::json($response);
	}

	public function fetchGetStorageLocation(Request $request){
		$group = $request->get('group');

		$getStorageLocation = StorageLocation::where('storage_locations.area', $group)
		->select('storage_locations.area', 'storage_locations.storage_location')
		->orderBy('storage_locations.area', 'ASC')
		->orderBy('storage_locations.storage_location', 'ASC')
		->get();

		echo '<option value=""></option>';
		for($i=0; $i < count($getStorageLocation); $i++) {
			echo '<option value="'.$getStorageLocation[$i]['storage_location'].'">'.$getStorageLocation[$i]['area'].' - '.$getStorageLocation[$i]['storage_location'].'</option>';
		}

	}

	public function fetchGetStore(Request $request){
		$location = $request->get('location');

		$getStore = StocktakingList::leftJoin('storage_locations', 'stocktaking_lists.location', '=', 'storage_locations.storage_location')
		->where('stocktaking_lists.location', $location)
		->distinct()
		->select('storage_locations.area', 'stocktaking_lists.location', 'stocktaking_lists.store')
		->orderBy('storage_locations.area', 'ASC')
		->orderBy('stocktaking_lists.location', 'ASC')
		->orderBy('stocktaking_lists.store', 'ASC')
		->get();		


		echo '<option value=""></option>';
		for($i=0; $i < count($getStore); $i++) {
			echo '<option value="'.$getStore[$i]['store'].'">'.$getStore[$i]['area'].' - '.$getStore[$i]['location'].' - '.$getStore[$i]['store'].'</option>';
		}
		echo '<option value="LAINNYA">LAINNYA</option>';

	}

	public function fetchCheckMonth(Request $request){

		$month = $request->get('month');
		$data = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if(!$data){
			$response = array(
				'status' => false,
				'message' => "Stocktaking Data Not Found"
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
		
	}

	public function fetchStore(Request $request){

		$area = '';
		if($request->get('area') != null){
			$areas =  $request->get('area');
			for ($i=0; $i < count($areas); $i++) {
				$area = $area."'".$areas[$i]."'";
				if($i != (count($areas)-1)){
					$area = $area.',';
				}
			}
			$area = "storage_locations.area IN (".$area.") ";
		}

		$location = '';
		if($request->get('location') != null){
			$locations =  $request->get('location');
			for ($i=0; $i < count($locations); $i++) {
				$location = $location."'".$locations[$i]."'";
				if($i != (count($locations)-1)){
					$location = $location.',';
				}
			}
			$location = "stocktaking_lists.location IN (".$location.") ";
		}

		$store = '';
		if($request->get('store') != null){
			$stores =  $request->get('store');
			for ($i=0; $i < count($stores); $i++) {
				$store = $store."'".$stores[$i]."'";
				if($i != (count($stores)-1)){
					$store = $store.',';
				}
			}
			$store = "stocktaking_lists.store IN (".$store.") ";
		}

		$condition = '';
		$and = false;
		if($area != '' || $location != '' || $store != ''){
			$condition = 'WHERE';
		}

		if($area != ''){
			$and = true;
			$condition = $condition. ' ' .$area;
		}

		if($location != ''){
			if($and){
				$condition =  $condition.' OR ';
			}

			$condition = $condition. ' ' .$location;
		}

		if($store != ''){
			if($and){
				$condition =  $condition.' OR ';
			}

			$condition = $condition. ' ' .$store;
		}


		$data = db::select("SELECT storage_locations.area AS `group`, stocktaking_lists.location, stocktaking_lists.store, count( stocktaking_lists.id ) AS quantity FROM stocktaking_lists
			LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_lists.location
			".$condition."
			GROUP BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.store
			ORDER BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.store ASC");

		return DataTables::of($data)
		->addColumn('delete', function($data){
			return '<button style="width: 50%; height: 100%;" onclick="deleteStore(\''.$data->store.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
		})
		->addColumn('reprint', function($data){
			return '<button style="width: 50%; height: 100%;" onclick="reprintStore(\''.$data->store.'\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-print"></i></span></button>';
		})
		->rawColumns([ 
			'reprint' => 'reprint',
			'delete' => 'delete'
		])
		->make(true);
	}


	public function fetchStoreDetail(Request $request){

		$area = '';
		if($request->get('area') != null){
			$areas =  $request->get('area');
			for ($i=0; $i < count($areas); $i++) {
				$area = $area."'".$areas[$i]."'";
				if($i != (count($areas)-1)){
					$area = $area.',';
				}
			}
			$area = "sl.area IN (".$area.") ";
		}

		$location = '';
		if($request->get('location') != null){
			$locations =  $request->get('location');
			for ($i=0; $i < count($locations); $i++) {
				$location = $location."'".$locations[$i]."'";
				if($i != (count($locations)-1)){
					$location = $location.',';
				}
			}
			$location = "s.location IN (".$location.") ";
		}

		$store = '';
		if($request->get('store') != null){
			$stores =  $request->get('store');
			for ($i=0; $i < count($stores); $i++) {
				$store = $store."'".$stores[$i]."'";
				if($i != (count($stores)-1)){
					$store = $store.',';
				}
			}
			$store = "s.store IN (".$store.") ";
		}

		$condition = '';
		$and = false;
		if($area != '' || $location != '' || $store != ''){
			$condition = 'WHERE';
		}

		if($area != ''){
			$and = true;
			$condition = $condition. ' ' .$area;
		}

		if($location != ''){
			if($and){
				$condition =  $condition.' OR ';
			}

			$condition = $condition. ' ' .$location;
		}

		if($store != ''){
			if($and){
				$condition =  $condition.' OR ';
			}

			$condition = $condition. ' ' .$store;
		}

		$data = db::select("SELECT s.id, sl.area AS `group`, s.location, s.store, s.category, s.material_number, mpdl.material_description, mpdl.bun AS uom FROM stocktaking_lists s
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			".$condition." 
			ORDER BY sl.area, s.location, s.store, s.category, s.material_number ASC");

		return DataTables::of($data)
		->addColumn('delete', function($data){
			return '<button style="width: 50%; height: 100%;" onclick="deleteMaterial(\''.$data->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
		})
		->addColumn('reprint', function($data){
			return '<button style="width: 50%; height: 100%;" onclick="reprintID(\''.$data->id.'\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-print"></i></span></button>';
		})
		->rawColumns([
			'reprint' => 'reprint',
			'delete' => 'delete'
		])
		->make(true);
	}

	public function fetchPiVsBook(Request $request){
		$month = $request->get('month');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar){
			$data = db::select("SELECT storage_locations.area AS `group`, pi_kitto.location, pi_kitto.material_number, material_plant_data_lists.material_description, pi_kitto.pi FROM
				(SELECT pi.location, pi.material_number, pi.qty AS pi, book.qty AS book FROM
				(SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
				GROUP BY location, material_number) AS pi
				LEFT JOIN
				(SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS book
				ON pi.location = book.location 	
				AND pi.material_number = book.material_number) AS pi_kitto
				LEFT JOIN material_plant_data_lists ON pi_kitto.material_number = material_plant_data_lists.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_kitto.location
				WHERE pi_kitto.book is null
				AND pi_kitto.pi > 0
				AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')
				AND storage_locations.area = 'ASSEMBLY'
				ORDER BY storage_locations.area, pi_kitto.location, pi_kitto.material_number");

			return DataTables::of($data)->make(true);
		}
		
	}

	public function fetchBookVsPi(Request $request){
		$month = $request->get('month');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar){
			$data = db::select("SELECT storage_locations.area AS `group`, pi_kitto.location, pi_kitto.material_number, material_plant_data_lists.material_description, pi_kitto.book FROM
				(SELECT book.location, book.material_number, pi.qty AS pi, book.qty AS book FROM	
				(SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS book
				LEFT JOIN
				(SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
				GROUP BY location, material_number) AS pi
				ON pi.location = book.location 	
				AND pi.material_number = book.material_number) AS pi_kitto
				LEFT JOIN material_plant_data_lists ON pi_kitto.material_number = material_plant_data_lists.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_kitto.location
				WHERE pi_kitto.pi is null
				AND pi_kitto.book > 0
				AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')
				AND storage_locations.area = 'ASSEMBLY'
				ORDER BY storage_locations.area, pi_kitto.location, pi_kitto.material_number");

			return DataTables::of($data)->make(true);
		}

	}

	public function fetchKittoVsPi(){
		$data = db::select("SELECT storage_locations.area AS `group`, kitto_pi.location, kitto_pi.material_number, material_plant_data_lists.material_description, kitto_pi.kitto, kitto_pi.pi FROM
			(SELECT	inventory.location, inventory.material_number, inventory.qty AS kitto,	COALESCE ( pi.qty, 0 ) AS pi FROM
			(SELECT issue_location AS location, material_number, sum( lot ) AS qty FROM kitto.inventories
			GROUP BY issue_location, material_number) AS inventory
			LEFT JOIN
			(SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
			GROUP BY location, material_number) AS pi
			ON inventory.location = pi.location 
			AND inventory.material_number = pi.material_number) AS kitto_pi
			LEFT JOIN material_plant_data_lists ON kitto_pi.material_number = material_plant_data_lists.material_number
			LEFT JOIN storage_locations ON storage_locations.storage_location = kitto_pi.location
			WHERE kitto_pi.kitto <> kitto_pi.pi
			AND storage_locations.area = 'ASSEMBLY'
			ORDER BY storage_locations.area, kitto_pi.location, kitto_pi.material_number");

		return DataTables::of($data)->make(true);
	}

	public function fetchKittoVsBook(Request $request){
		$month = $request->get('month');
		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if($calendar){
			$data = db::select("SELECT storage_locations.area AS `group`, kitto_book.location, kitto_book.material_number, material_plant_data_lists.material_description, kitto_book.kitto, kitto_book.book FROM
				(SELECT inventory.location, inventory.material_number, inventory.qty AS kitto, COALESCE ( book.qty, 0 ) AS book FROM
				(SELECT issue_location AS location, material_number, sum( lot ) AS qty FROM kitto.inventories
				GROUP BY issue_location, material_number) AS inventory
				LEFT JOIN
				(SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS book
				ON inventory.location = book.location 
				AND inventory.material_number = book.material_number) AS kitto_book
				LEFT JOIN material_plant_data_lists ON kitto_book.material_number = material_plant_data_lists.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = kitto_book.location
				WHERE kitto_book.kitto <> kitto_book.book
				AND storage_locations.area = 'ASSEMBLY'
				ORDER BY storage_locations.area, kitto_book.location, kitto_book.material_number");

			return DataTables::of($data)->make(true);
		}

	}

	public function fetchPiVsLot(){
		$data = db::select("SELECT storage_locations.area AS `group`, inventory.location, inventory.material_number, material_plant_data_lists.material_description, inventory.quantity, material_volumes.lot_completion FROM
			(SELECT pi.location, lot.material_number, pi.quantity FROM
			(SELECT location, material_number, sum(quantity) AS quantity FROM stocktaking_outputs
			GROUP BY location, material_number) AS pi
			JOIN
			(SELECT issue_location AS location, material_number, sum( lot ) AS qty FROM kitto.inventories
			GROUP BY issue_location, material_number) AS lot
			ON pi.location = lot.location AND pi.material_number = lot.material_number) AS inventory
			LEFT JOIN material_volumes ON inventory.material_number = material_volumes.material_number
			LEFT JOIN material_plant_data_lists ON inventory.material_number = material_plant_data_lists.material_number
			LEFT JOIN storage_locations ON inventory.location = storage_locations.storage_location
			WHERE (inventory.quantity % material_volumes.lot_completion) <> 0
			AND storage_locations.area = 'ASSEMBLY'");

		return DataTables::of($data)->make(true);
	}

	public function fetchVariance(Request $request){
		$month = $request->get('month');

		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
		if(!$calendar){
			$response = array(
				'status' => false,
				'message' => "Stocktaking Data Not Found"
			);
			return Response::json($response);
		}

		if($calendar->status != 'finished'){
			$variance = db::select("SELECT plnt, `group`, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area IS NOT NULL
				AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')) AS official_variance
				GROUP BY plnt, `group`");
		}else{
			$variance = db::select("SELECT plnt, `group`, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_output_logs
				WHERE stocktaking_date = '".$calendar->date."'
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '".$calendar->date."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area IS NOT NULL
				AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')) AS official_variance
				GROUP BY plnt, `group`");
		}

		$response = array(
			'status' => true,
			'variance' => $variance
		);
		return Response::json($response);
	}

	public function fetchVarianceDetail(Request $request){
		$month = $request->get('month');
		$location = $request->get('location');

		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
		if(!$calendar){
			$response = array(
				'status' => false,
				'message' => "Stocktaking Data Not Found"
			);
			return Response::json($response);
		}

		if($calendar->status != 'finished'){
			$variance_detail = db::select("SELECT plnt, `group`, location, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '". $calendar->date ."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area = '". $location ."') AS official_variance
				GROUP BY plnt, `group`, location");
		}else{
			$variance_detail = db::select("SELECT plnt, `group`, location, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
				(SELECT storage_locations.area AS `group`,
				storage_locations.plnt,
				pi_book.material_number,
				pi_book.location,
				ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
				pi_book.pi AS pi,
				pi_book.book AS book,
				(pi_book.pi - pi_book.book) AS diff_qty,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
				(ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
				ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
				FROM
				(SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
				(SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_output_logs
				WHERE stocktaking_date = '".$calendar->date."'
				GROUP BY location, material_number
				UNION ALL
				SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
				WHERE stock_date = '". $calendar->date ."'
				GROUP BY storage_location, material_number) AS union_pi_book
				GROUP BY location, material_number) AS pi_book
				LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
				LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
				WHERE storage_locations.area = '". $location ."') AS official_variance
				GROUP BY plnt, `group`, location");
		}		

		$response = array(
			'status' => true,
			'variance_detail' => $variance_detail
		);
		return Response::json($response);

	}

	public function fetchfilledList(Request $request){
		$month = $request->get('month');

		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

		if(!$calendar){
			$response = array(
				'status' => false,
				'message' => "Stocktaking Data Not Found"
			);
			return Response::json($response);
		}

		if($calendar->status != 'finished'){
			$data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
				(SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_lists s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				GROUP BY sl.area, s.location
				UNION ALL
				SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_lists s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				WHERE s.quantity IS NOT NULL 
				GROUP BY sl.area, s.location) AS list 
				GROUP BY area, location
				ORDER BY area");
		}else{
			$data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
				(SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_inquiry_logs s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				WHERE s.stocktaking_date = '".$calendar->date."'
				GROUP BY sl.area, s.location
				UNION ALL
				SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_inquiry_logs s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				WHERE s.stocktaking_date = '".$calendar->date."'
				AND s.quantity IS NOT NULL
				GROUP BY sl.area, s.location) AS list 
				GROUP BY area, location
				ORDER BY area");
		}


		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);

	}

	public function fetchfilledListDetail(Request $request){
		$group = $request->get('group');
		$month = $request->get('month');
		$quantity = '';
		if($request->get('series') == 'Empty'){
			$quantity = 's.quantity IS NULL';
		}else if ($request->get('series') == 'Filled') {
			$quantity = 's.quantity IS NOT NULL';
		}

		$calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
		if(!$calendar){
			$response = array(
				'status' => false,
				'message' => "Stocktaking Data Not Found"
			);
			return Response::json($response);
		}

		if($calendar->status != 'finished'){
			$input_detail = db::select("SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, s.quantity, s.audit1, s.audit2, s.final_count FROM stocktaking_lists s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
				WHERE s.location = '".$group."'
				AND ".$quantity."
				ORDER BY sl.area, s.location, s.store, s.material_number ASC");
		}else{
			$input_detail = db::select("
				SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, NULL AS quantity, NULL AS audit1, NULL AS audit2, s.quantity AS final_count FROM stocktaking_inquiry_logs s
				LEFT JOIN storage_locations sl on sl.storage_location = s.location
				LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
				WHERE s.location = '".$group."'
				AND ".$quantity."
				AND s.stocktaking_date = '".$calendar->date."'
				ORDER BY sl.area, s.location, s.store, s.material_number ASC;");
		}

		$response = array(
			'status' => true,
			'input_detail' => $input_detail
		);
		return Response::json($response);
	}

	public function fetchCheckAudit(Request $request, $audit){

		$minimum = 0;
		if($audit == 'audit1'){
			$minimum = 10;
		}else if($audit == 'audit2'){
			$minimum = 5;
		}

		$actual = db::select("SELECT
			( SELECT count( id ) AS total FROM stocktaking_lists
			WHERE remark = 'USE'
			AND store = '".$request->get('store')."'
			AND ".$audit." IS NOT NULL )
			/
			( SELECT count( id ) AS total
			FROM stocktaking_lists
			WHERE remark = 'USE'
			AND store = '".$request->get('store')."' )
			* 100
			AS percentage");


		if($actual[0]->percentage >= $minimum){
			$response = array(
				'status' => true,
				'actual' => $actual[0]->percentage,
				'minimum' => $minimum
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false,
				'actual' => $actual[0]->percentage,
				'minimum' => $minimum
			);
			return Response::json($response);
		}

	}

	public function fetchSummaryOfCounting(Request $request){

		$store = '';
		if(strlen($request->get('store')) > 0){
			$stores = explode(',', $request->get('store'));
			for ($i=0; $i < count($stores); $i++) {
				$store = $store."'".$stores[$i]."'";
				if($i != (count($stores)-1)){
					$store = $store.',';
				}
			}
			$store = " WHERE s.store in (".$store.") ";
		}

		$summary = db::select("SELECT
			s.id,
			sl.area,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			".$store."
			ORDER BY s.store ASC");


		return DataTables::of($summary)->make(true);
	}

	public function fetchMaterialDetail(Request $request){
		$material = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			s.process,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			sl.area,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
			s.quantity,
			s.remark
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			WHERE
			s.id = ". $request->get('id'));

		$response = array(
			'status' => true,
			'material' => $material,
		);
		return Response::json($response);
	}

	public function fetchStoreList(Request $request){
		$store = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			sl.area,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
			s.quantity,
			s.remark,
			s.audit1,
			s.audit2
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			WHERE
			s.store = '". $request->get('store'). "'
			ORDER BY
			s.id ASC");

			// ORDER BY
			// s.remark DESC,
			// s.category ASC,
			// s.material_number ASC

		$response = array(
			'status' => true,
			'store' => $store,
		);
		return Response::json($response);
	}

	public function fetchReviseId(Request $request){

		$process = $request->get('process');
		$current = StocktakingList::where('id', $request->get('id'))->first();

		//Cek Store
		if($current == null){
			$response = array(
				'status' => false,
				'message' => 'Data tidak ditemukan',
			);
			return Response::json($response);
		}

		$null = StocktakingList::where('store', $current->store)
		->whereNull('final_count')
		->get();

		//Cek qty sudah terisi ?
		if(count($null) > 0){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		//Cek proses saat ini
		if($process > $current->process){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		$store = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
			s.remark,
			s.process,
			s.quantity,
			s.audit1,
			s.audit2,
			s.final_count
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number 
			WHERE
			s.store = '". $current->store. "'
			ORDER BY
			s.id ASC");

		$response = array(
			'status' => true,
			'store' => $store,
			'store_name' => $current->store
		);
		return Response::json($response);
	}

	public function fetchRevise(Request $request){

		$process = $request->get('process');
		$current = StocktakingList::where('store', $request->get('store'))->first();

		$null = StocktakingList::where('store', $request->get('store'))
		->whereNull('final_count')
		->get();

		//Cek Store
		if($current == null){
			$response = array(
				'status' => false,
				'message' => 'Store tidak ditemukan',
			);
			return Response::json($response);
		}

		//Cek qty sudah terisi ?
		if(count($null) > 0){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		//Cek proses saat ini
		if($process > $current->process){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		$store = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
			s.remark,
			s.process,
			s.quantity,
			s.audit1,
			s.audit2,
			s.final_count
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number 
			WHERE
			s.store = '". $request->get('store'). "'
			ORDER BY
			s.id ASC");

		$response = array(
			'status' => true,
			'store' => $store,
		);
		return Response::json($response);
	}


	public function fetchAuditStoreList(Request $request){

		$process = $request->get('process');
		$current = StocktakingList::where('store', $request->get('store'))->orderBy('process', 'ASC')->first();

		$null = StocktakingList::where('store', $request->get('store'))
		->whereNull('quantity')
		->get();

		//Cek Store
		if($current == null){
			$response = array(
				'status' => false,
				'message' => 'Store tidak ditemukan',
			);
			return Response::json($response);
		}

		//Cek qty sudah terisi ?
		if(count($null) > 0){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		//Cek proses saat ini
		if($process > $current->process){
			$response = array(
				'status' => false,
				'message' => 'Proses sebelumnya belum selesai',
			);
			return Response::json($response);
		}

		$store = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
			s.remark,
			s.process,
			s.quantity,
			s.audit1,
			s.audit2
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number 
			WHERE
			s.store = '". $request->get('store'). "'
			ORDER BY s.id");

		// ORDER BY
		// s.category,
		// s.material_number

		$response = array(
			'status' => true,
			'store' => $store,
		);
		return Response::json($response);
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

	public function fetch_bom_output(Request $request){
		$bom_outputs = BomOutput::orderBy('bom_outputs.id', 'asc');
		$bom_outputs = $bom_outputs->select('bom_outputs.material_parent', 'bom_outputs.material_child', 'bom_outputs.usage', 'bom_outputs.uom')
		->get();

		return DataTables::of($bom_outputs)->make(true);
	}

	public function addMaterial(Request $request){
		$store = $request->get('store');
		$category = $request->get('category');
		$material = $request->get('material');
		$location = $request->get('location');

		try {
			$add = new StocktakingList([
				'store' => $store,
				'category' => $category,
				'material_number' => $material,
				'location' => $location,
				'remark' => 'USE',
				'process' => 0,
				'created_by' => Auth::id()
			]);
			$add->save();


			$response = array(
				'status' => true,
				'message' => 'Add New Material Successful'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function updateNoUse(Request $request){
		try {

			$update = StocktakingList::whereIn('id', $request->get('id'))
			->update([
				'remark' => 'NO USE',
				'process' => 1,
				'quantity' => 0,
				'inputed_by' => Auth::user()->username
			]);

			$response = array(
				'status' => true,
				'message' => 'Update No Use Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function byPassAudit(){

		$store = StocktakingList::get();

		for ($i = 0; $i < count($store); $i++) {
			$final = 0;
			if($store[$i]->audit2 > 0){
				$final = $store[$i]->audit2;
			}else if($store[$i]->audit1 > 0){
				$final = $store[$i]->audit1;
			}else{
				$final = $store[$i]->quantity;
			}
			$updateStore = StocktakingList::where('id', $store[$i]->id)
			->update([
				'process' => 3,
				'final_count' => $final
			]);
		}

	}

	public function updateProcessAudit(Request $request, $audit){

		if($audit == 'audit1'){
			$process = 2;
		}else if($audit == 'audit2'){
			$process = 3;
		}

		try {
			$updateStore = StocktakingList::where('store', $request->get('store'))
			->update([
				'process' => $process
			]);

			if($audit == 'audit2'){
				$store = StocktakingList::where('store', $request->get('store'))->get();

				for ($i = 0; $i < count($store); $i++) {
					$final = 0;
					if($store[$i]->audit2 > 0){
						$final = $store[$i]->audit2;
					}else if($store[$i]->audit1 > 0){
						$final = $store[$i]->audit1;
					}else{
						$final = $store[$i]->quantity;
					}
					$updateStore = StocktakingList::where('id', $store[$i]->id)
					->update([
						'final_count' => $final
					]);
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Audit Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function updateAudit(Request $request, $audit){
		$id = $request->get('id');
		$quantity = $request->get('quantity');
		
		$auditor = $request->get('auditor');
		if($auditor == null){
			$auditor = Auth::user()->username;
		}

		$field = '';
		if($audit == 'audit1'){
			$field = 'audit1_by';
		}else if($audit == 'audit2'){
			$field = 'audit2_by';
		}

		try {

			$update = StocktakingList::where('id', $id)
			->update([
				$audit => $quantity,
				$field => $auditor
			]);

			$response = array(
				'status' => true,
				'message' => 'Update Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function updateRevise(Request $request){
		$id = $request->get('id');
		$quantity = $request->get('quantity');
		$reason = $request->get('reason');

		$remark = '';
		if($quantity > 0){
			$remark = 'USE';
		}else{
			$remark = 'NO USE';
		}

		try {

			$update = StocktakingList::where('id', $id)
			->update([
				'remark' => $remark,
				'final_count' => $quantity,
				'reason' => $reason
			]);

			$response = array(
				'status' => true,
				'message' => 'Update Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}


	public function updateCount(Request $request){

		$id = $request->get('id');
		$quantity = $request->get('quantity');
		$inputor = $request->get('inputor');

		try {

			$update = StocktakingList::where('id', $id)
			->update([
				'process' => 1,
				'quantity' => $quantity,
				'inputed_by' => $inputor
			]);

			// $store = StocktakingList::where('id', $id)->first();
			// $updateStore = StocktakingList::where('store', $store->store)
			// ->update([
			// 	'process' => 1
			// ]);

			$response = array(
				'status' => true,
				'message' => 'PI Berhasil Disimpan'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function deleteStore(Request $request){
		$store = $request->get('store');

		try {
			$delete = StocktakingList::where('store', $store)->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Delete Store Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}	
	}

	public function deleteMaterial(Request $request){
		$id = $request->get('id');

		try {
			$delete = StocktakingList::where('id', $id)->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Delete Material Berhasil'
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function bom_output(){
		$title = 'BOM Output';
		$title_jp = '';

		return view('stocktakings.bomoutput', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'base_units' => $this->base_unit,
		))->with('page', 'BOM Output')->with('head', 'BOM');
	}

	public function mpdl() {
		$title = 'Material Plant Data List';
		$title_jp = '';

		return view('stocktakings.mpdl', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'storage_locations' => $this->storage_location,
			'base_units' => $this->base_unit,
		))->with('page', 'Material Plant Data List')->with('head', 'MPDL');
	}

	function uploadFTP($from, $to) {
		$upload = FTP::connection()->uploadFile($from, $to);
		return $upload;
	}

	function writeString($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$text .= $char;
			}
		}
		return strtoupper($text);
	}

	function writeStringReserve($text, $maxLength, $char) {

		$output = '';

		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$output = "";
			}

			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$output .= $char;
			}

			if ($text != null) {
				$output .= $text;
			}
		}
		return strtoupper($output);
	}

	function writeDecimal($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				if(fmod($text,1) > 0){
					$decimal = $this->decimal(fmod($text,1));
					$decimalLength = strlen($decimal);

					for ($j = 0; $j < (3- $decimalLength); $j++) {
						$decimal = $decimal . $char;
					}
				}
				else{
					$decimal = $char . $char . $char;
				}
				$textLength = strlen(floor($text));
				$text = floor($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < (($maxLength - 4) - $textLength); $i++) {
				$text = $char . $text;
			}
		}
		$text .= "." . $decimal;
		return $text;
	}

	function writeDate($created_at, $type) {
		$datetime = strtotime($created_at);
		if ($type == "completion") {
			$text = date("dmY", $datetime);
			return $text;
		}
		else {
			$text = date("Ymd", $datetime);
			return $text;
		}
	}

	function decimal($number){
		$num = explode('.', $number);
		return $num[1];
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
		$title_jp = '銀材棚卸報告';

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
