<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\ErrorLog;
use App\BomComponent;
use App\CodeGenerator;
use App\KnockDown;
use App\KnockDownDetail;
use App\KnockDownLog;
use App\Material;
use App\MouthpieceChecksheet;
use App\MouthpieceChecksheetDetail;
use App\TransactionCompletion;
use App\TransactionTransfer;
use App\ShipmentSchedule;
use App\EmployeeSync;
use App\Inventory;
use App\Destination;
use Carbon\Carbon;
use Response;
use DataTables;

class MouthpieceController extends Controller
{
	public function indexKdMouthpieceQaCheck(){
		$title = 'Mouthpiece QA Check';
		$title_jp = '';

		return view('kd.mouthpiece.qa_check', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'KD Mouthpiece')->with('page', 'MP QA Check');
	}

	public function scanKdMouthpieceQaCheck(Request $request){
		$checksheets = MouthpieceChecksheet::leftJoin('materials', 'materials.material_number', '=', 'mouthpiece_checksheets.material_number')
		->where('mouthpiece_checksheets.kd_number', '=', $request->get('kd_number'))
		->where('mouthpiece_checksheets.remark', '=', '1')
		->select(
			'mouthpiece_checksheets.kd_number', 
			'mouthpiece_checksheets.material_number', 
			'materials.issue_storage_location', 
			'mouthpiece_checksheets.material_description', 
			'mouthpiece_checksheets.quantity', 
			'mouthpiece_checksheets.actual_quantity',
			'mouthpiece_checksheets.shipment_schedule_id',
			'mouthpiece_checksheets.remark',
			'mouthpiece_checksheets.employee_id',
			'mouthpiece_checksheets.start_packing',
			'mouthpiece_checksheets.end_packing',
			'mouthpiece_checksheets.destination_shortname',
			'mouthpiece_checksheets.st_date',
			'mouthpiece_checksheets.packing_date',
			'mouthpiece_checksheets.created_by',
			'mouthpiece_checksheets.created_at',
			'mouthpiece_checksheets.updated_at'
		)
		->get();

		if(count($checksheets) <= 0){
			$response = array(
				'status' => false,
				'message' => "Checksheet tidak ditemukan"
			);
			return Response::json($response);			
		}

		$quantity = 0;
		$actual_quantity = 0;

		foreach ($checksheets as $checksheet) {
			$quantity += $checksheet->quantity;
			$actual_quantity += $checksheet->actual_quantity;
		}

		if($quantity != $actual_quantity){
			$response = array(
				'status' => false,
				'message' => "Proses packing mouthpiece belum selesai"
			);
			return Response::json($response);
		}

		foreach($checksheets as $checksheet){
			try{
				$knock_down_details = new KnockDownDetail([
					'kd_number' => $checksheet->kd_number,
					'material_number' => $checksheet->material_number,
					'quantity' => $checksheet->quantity,
					// 'shipment_schedule_id' => $checksheet->shipment_schedule_id,
					'storage_location' => $checksheet->issue_storage_location,
					'created_by' => Auth::id()
				]);
				$knock_down_details->save();

				$inventory = Inventory::where('plant', '=', '8190')
				->where('material_number', '=', $checksheet->material_number)
				->where('storage_location', '=', $checksheet->issue_storage_location)
				->first();

				if($inventory){
					$inventory->quantity = $inventory->quantity + $checksheet->quantity;
					$inventory->save();
				}else{	
					$inventory = new Inventory([
						'plant' => '8190',
						'material_number' => $checksheet->material_number,
						'storage_location' => $checksheet->issue_storage_location,
						'quantity' => $checksheet->quantity
					]);
					$inventory->save();
				}

				$transaction_completion = new TransactionCompletion([
					'serial_number' => $checksheet->kd_number,
					'material_number' => $checksheet->material_number,
					'issue_plant' => '8190',
					'issue_location' => $checksheet->issue_storage_location,
					'quantity' => $checksheet->quantity,
					'movement_type' => '101',
					'created_by' => Auth::id(),
				]);
				$transaction_completion->save();

				// $shipment_schedule = ShipmentSchedule::where('shipment_schedules.id', '=', $checksheet->shipment_schedule_id)
				// ->first();

				// $shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $checksheet->quantity;
				// $shipment_schedule->save();

				$mouthpiece_checksheet_log = db::table('mouthpiece_checksheet_logs')->insert([
					'kd_number' => $checksheet->kd_number,
					'material_number' => $checksheet->material_number,
					'material_description' => $checksheet->material_description,
					'quantity' => $checksheet->quantity,
					'actual_quantity' => $checksheet->actual_quantity,
					'remark' => $checksheet->remark,
					'employee_id' => $checksheet->employee_id,
					'start_packing' => $checksheet->start_packing,
					'end_packing' => $checksheet->end_packing,
					'print_status' => $checksheet->print_status,
					'destination_shortname' => $checksheet->destination_shortname,
					'st_date' => $checksheet->st_date,
					'st_date' => $checksheet->packing_date,
					'qa_check' => $request->get('employee_id'),
					'created_by' => $checksheet->created_by,
					'created_at' => $checksheet->created_at,
					'updated_at' => $checksheet->updated_at
				]);

			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => Auth::id()
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		try{
			$knock_down = new KnockDown([
				'kd_number' => $checksheets[0]->kd_number,
				'created_by' => Auth::id(),
				'max_count' => 100,
				'actual_count' => count($checksheets),
				'remark' => 'MP',
				'status' => 1
			]);
			$knock_down->save();

			$kd_log1 = KnockDownLog::updateOrCreate(
				['kd_number' => $checksheets[0]->kd_number, 'status' => 0],
				['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
			);
			$kd_log1->save();

			$kd_log2 = KnockDownLog::updateOrCreate(
				['kd_number' => $checksheets[0]->kd_number, 'status' => 1],
				['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
			);
			$kd_log2->save();

			$mouthpiece_checksheet_detail_logs = db::select("
				INSERT INTO mouthpiece_checksheet_detail_logs ( kd_number, material_number, material_description, quantity, actual_quantity, remark, end_picking, employee_id, created_by, deleted_at, created_at, updated_at ) SELECT
				mouthpiece_checksheet_details.kd_number,
				mouthpiece_checksheet_details.material_number,
				mouthpiece_checksheet_details.material_description,
				mouthpiece_checksheet_details.quantity,
				mouthpiece_checksheet_details.actual_quantity,
				mouthpiece_checksheet_details.remark,
				mouthpiece_checksheet_details.end_picking,
				mouthpiece_checksheet_details.employee_id,
				mouthpiece_checksheet_details.created_by,
				mouthpiece_checksheet_details.deleted_at,
				mouthpiece_checksheet_details.created_at,
				mouthpiece_checksheet_details.updated_at 
				FROM
				mouthpiece_checksheet_details
				WHERE
				mouthpiece_checksheet_details.kd_number = '".$checksheets[0]->kd_number."'
				");

			$del_checksheet = MouthpieceChecksheet::where('kd_number', '=', $checksheets[0]->kd_number)->forceDelete();
			$del_detail = MouthpieceChecksheetDetail::where('kd_number', '=', $checksheets[0]->kd_number)->forceDelete();

		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => Auth::id()
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Checksheet berhasil lolos QA Check',
		);
		return Response::json($response);

	}

	public function indexKdMouthpieceLog(){
		$title = 'Mouthpiece Checksheet Log';
		$title_jp = '';

		$materials = Material::where('hpl', '=', 'MP')
		->where('category', '=', 'KD')
		->orderBy('material_number', 'ASC')
		->get();

		$employees = EmployeeSync::orderBy('employee_id')
		->get();

		$destinations = Destination::orderBy('destination_shortname', 'ASC')
		->get();

		return view('kd.mouthpiece.log', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'employees' => $employees,
			'destinations' => $destinations
		))->with('head', 'KD Mouthpiece')->with('page', 'MP Log');
	}

	public function fetchKdMouthpieceLog(Request $request){

		$prodDate = "";
		$shipDate = "";
		$kd_number = "";
		$material = "";
		$employee = "";
		$destination = "";

		if(strlen($request->get('prodFrom'))>0 && strlen($request->get('prodTo'))<=0){
			$prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
			$prodDate = " AND date(m.created_at) >= '".$prodFrom."'";
		}

		if(strlen($request->get('prodFrom'))>0 && strlen($request->get('prodTo'))>0){
			$prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
			$prodTo = date('Y-m-d', strtotime($request->get('prodTo')));
			$prodDate = " AND date(m.created_at) >= '".$prodFrom."' AND date(m.created_at) <= '".$prodTo."'";
		}

		if(strlen($request->get('shipFrom'))>0 && strlen($request->get('shipTo'))<=0){
			$shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
			$shipDate = " AND date(m.st_date) >= '".$shipFrom."'";
		}

		if(strlen($request->get('shipFrom'))>0 && strlen($request->get('shipTo'))>0){
			$shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
			$shipTo = date('Y-m-d', strtotime($request->get('shipTo')));
			$shipDate = " AND date(m.st_date) >= '".$shipFrom."' AND date(m.st_date) <= '".$shipTo."'";
		}

		if(strlen($request->get('kd_number'))>0){
			$kd_number = " AND m.kd_number = '".$request->get('kd_number')."'";
		}

		if($request->get('material_number') != null){
			$material_numbers = $request->get('material_number');
			$material_number_length = count($material_numbers);
			$material_number = "";

			for($x = 0; $x < $material_number_length; $x++) {
				$material_number = $material_number."'".$material_numbers[$x]."'";
				if($x != $material_number_length-1){
					$material_number = $material_number.",";
				}
			}

			$material = " AND m.material_number in (".$material_number.") ";
		}

		if($request->get('employee_id') != null){
			$employee_ids = $request->get('employee_id');
			$employee_id_length = count($employee_ids);
			$employee_id = "";

			for($x = 0; $x < $employee_id_length; $x++) {
				$employee_id = $employee_id."'".$employee_ids[$x]."'";
				if($x != $employee_id_length-1){
					$employee_id = $employee_id.",";
				}
			}

			$employee = " AND m.employee_id in (".$employee_id.") ";
		}

		if($request->get('destination_shortname') != null){
			$destination_shortnames = $request->get('destination_shortname');
			$destination_shortname_length = count($destination_shortnames);
			$destination_shortname = "";

			for($x = 0; $x < $destination_shortname_length; $x++) {
				$destination_shortname = $destination_shortname."'".$destination_shortnames[$x]."'";
				if($x != $destination_shortname_length-1){
					$destination_shortname = $destination_shortname.",";
				}
			}

			$destination = " AND m.destination_shortname in (".$destination_shortname.") ";
		}

		$checksheets = db::select("SELECT
			date( m.created_at ) AS created_at,
			m.kd_number,
			m.material_number,
			m.material_description,
			m.quantity,
			m.st_date,
			m.destination_shortname,
			m.employee_id,
			e.`name`,
			TIMESTAMPDIFF( MINUTE, start_packing, end_packing ) AS packing 
			FROM
			mouthpiece_checksheet_logs m
			LEFT JOIN employee_syncs e ON m.employee_id = e.employee_id 
			WHERE
			m.deleted_at IS NULL
			".$prodDate."
			".$shipDate."
			".$kd_number."
			".$material."
			".$employee."
			".$destination."");

		$kd_numbers = array();

		foreach($checksheets as $checksheet){
			if(!in_array($checksheet->kd_number, $kd_numbers)){
				array_push($kd_numbers, $checksheet->kd_number);
			}
		}

		$kd_number_detail_length = count($kd_numbers);
		$kd_number_detail = "";

		for($x = 0; $x < $kd_number_detail_length; $x++) {
			$kd_number_detail = $kd_number_detail."'".$kd_numbers[$x]."'";
			if($x != $kd_number_detail_length-1){
				$kd_number_detail = $kd_number_detail.",";
			}
		}

		$kd = "";

		if(strlen($kd_number_detail)>0){
			$kd = " AND m.kd_number in (".$kd_number_detail.") ";
		}

		$checksheet_details = db::select("SELECT
			m.kd_number,
			m.material_number,
			m.material_description,
			m.quantity,
			m.end_picking,
			m.employee_id,
			e.`name` 
			FROM
			mouthpiece_checksheet_detail_logs AS m
			LEFT JOIN employee_syncs AS e ON m.employee_id = e.employee_id 
			WHERE
			m.deleted_at IS NULL
			".$kd."");

		$response = array(
			'status' => true,
			'checksheets' => $checksheets,
			'checksheet_details' => $checksheet_details,
			'message' => 'Data checksheet berhasil ditemukan'
		);
		return Response::json($response);
	}

	public function indexKdMouthpiecePacking(){
		$title = 'Mouthpiece Packing';
		$title_jp = '';

		return view('kd.mouthpiece.packing', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'KD Mouthpiece')->with('page', 'MP Packing');
	}

	public function checkKdMouthpieceChecksheet(Request $request){
		$checksheet = MouthpieceChecksheet::where('kd_number', '=', $request->get('id'))
		->select(db::raw('kd_number, st_date, destination_shortname, sum(quantity), sum(actual_quantity), remark'))
		->groupBy('kd_number', 'st_date', 'destination_shortname', 'remark')
		->first();

		if(!$checksheet){
			$response = array(
				'status' => false,
				'message' => "Data checksheet tidak ditemukan"
			);
			return Response::json($response);
		}

		if($checksheet->remark < $request->get('remark')){
			$response = array(
				'status' => false,
				'message' => "Proses picking material belum selesai"
			);
			return Response::json($response);
		}

		if($checksheet->remark > $request->get('remark')){
			$response = array(
				'status' => false,
				'message' => "Proses mouthpiece packing sudah selesai."
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'checksheet' => $checksheet,
			'message' => "Data checksheet ditemukan."
		);
		return Response::json($response);
	}

	public function fetchKdMouthpiecePacking(Request $request){
		$checksheets = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', '=', $request->get('id'))
		->select('mouthpiece_checksheets.id', 'mouthpiece_checksheets.kd_number', 'mouthpiece_checksheets.material_number', 'mouthpiece_checksheets.material_description', 'mouthpiece_checksheets.quantity', 'mouthpiece_checksheets.actual_quantity', 'mouthpiece_checksheets.print_status', 'mouthpiece_checksheets.destination_shortname', 'mouthpiece_checksheets.st_date', db::raw('mouthpiece_checksheets.actual_quantity-mouthpiece_checksheets.quantity as diff'))
		->get();

		return DataTables::of($checksheets)
		->addColumn('inner', function($checksheet){
			return '<button style="width:100%; font-size: 1.5vw; font-weight:bold;" class="btn btn-info btn-lg" id="'.$checksheet->id.'" onlick="printInner(id)">INNER</button>';
		})
		->addColumn('outer', function($checksheet){
			return '<button style="width:100%; font-size: 1.5vw; font-weight:bold;" class="btn btn-warning btn-lg" id="'.$checksheet->id.'" onlick="printOuter(id)">OUTER</button>';
		})
		->rawColumns([ 
			'inner' => 'inner',
			'outer' => 'outer'
		])
		->make(true);
	}

	public function scanKdMouthpiecePacking(Request $request){
		$checksheet = MouthpieceChecksheet::where('kd_number', '=', $request->get('kd_number'))
		->where('material_number', '=', $request->get('material_number'))
		->first();

		if($checksheet == ""){
			$response = array(
				'status' => false,
				'message' => "Mouthpiece tidak ada pada checksheet."
			);
			return Response::json($response);
		}

		if($checksheet->quantity <= $checksheet->actual_quantity){
			$response = array(
				'status' => false,
				'message' => "Jumlah mouthpiece pada checksheet sudah terpenuhi."
			);
			return Response::json($response);
		}

		try{
			$checksheet->actual_quantity = $checksheet->actual_quantity+1;
			$checksheet->employee_id = $request->get('employee_id');
			if($checksheet->start_packing == null){
				$checksheet->start_packing = date('Y-m-d H:i:s');
			}
			$checksheet->end_packing = date('Y-m-d H:i:s');
			$checksheet->save();

			$response = array(
				'status' => true,
				'message' => 'Packing mouthpiece berhasil.'
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => Auth::id()
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function indexKdMouthpiecePicking(){
		$title = 'Mouthpiece Material Picking';
		$title_jp = '';

		// $checksheets = db::select("SELECT
		// 	mouthpiece_checksheets.kd_number,
		// 	shipment_schedules.st_date,
		// 	destinations.destination_shortname,
		// 	group_concat(
		// 	CONCAT( materials.material_description, ' (', mouthpiece_checksheets.quantity, ')' )) AS item,
		// 	sum( mouthpiece_checksheets.quantity ) AS total 
		// 	FROM
		// 	mouthpiece_checksheets
		// 	LEFT JOIN materials ON materials.material_number = mouthpiece_checksheets.material_number
		// 	LEFT JOIN shipment_schedules ON shipment_schedules.id = mouthpiece_checksheets.shipment_schedule_id
		// 	LEFT JOIN destinations ON destinations.destination_code = shipment_schedules.destination_code 
		// 	WHERE 
		// 	mouthpiece_checksheets.remark = '0'
		// 	GROUP BY
		// 	mouthpiece_checksheets.kd_number,
		// 	shipment_schedules.st_date,
		// 	destinations.destination_shortname");

		return view('kd.mouthpiece.picking', array(
			'title' => $title,
			'title_jp' => $title_jp,
			// 'checksheets' => $checksheets
		))->with('head', 'KD Mouthpiece')->with('page', 'MP Picking Material');
	}

	public function createKdMouthpiecePicking(Request $request){
		try{
			$checksheets = MouthpieceChecksheet::where('kd_number', '=', $request->get('kd_number'))
			->update([
				'remark' => 1
			]);

			$response = array(
				'status' => true,
				'message' => "Picking telah selesai"
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => Auth::id()
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function scanKdMouthpiecePicking(Request $request){
		try{
			$checksheet_detail = MouthpieceChecksheetDetail::where('kd_number', '=', $request->get('kd_number'))
			->where('material_number', '=', $request->get('material_number'))
			->whereRaw('quantity > actual_quantity')
			->first();

			if($checksheet_detail == ""){
				$response = array(
					'status' => false,
					'message' => "Material tidak ada pada checklist atau Material sudah dipicking"
				);
				return Response::json($response);
			}

			$checksheet_detail->actual_quantity = $checksheet_detail->quantity;
			$checksheet_detail->employee_id = $request->get('employee_id');
			$checksheet_detail->end_picking = date('Y-m-d H:i:s');

			$checksheet_detail->save();

			$response = array(
				'status' => true,
				'message' => "Material berhasil dipicking"
			);
			return Response::json($response);

		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => Auth::id()
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchKdMouthpiecePicking(Request $request){

		$checksheets = MouthpieceChecksheet::where('kd_number', '=', $request->get('id'))
		->where('remark', '=', '0')
		->get();

		if(count($checksheets) <= 0){
			$response = array(
				'status' => false,
				'message' => 'Checksheet tidak ditemukan'
			);
			return Response::json($response);
		}

		$checksheet_details = MouthpieceChecksheetDetail::where('kd_number', '=', $request->get('id'))
		->orderBy('remark', 'ASC')
		->orderBy('material_number', 'ASC')
		->get();

		if(count($checksheet_details) <= 0){
			$response = array(
				'status' => false,
				'message' => 'Checksheet tidak ditemukan'
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'checksheet_details' => $checksheet_details
		);
		return Response::json($response);
	}

	public function indexKdMouthpieceChecksheet()
	{
		$title = 'Create Mouthpiece Packing Checksheet';
		$title_jp = '';

		$destinations= db::table('destinations')->orderBy('destination_shortname')
		->get();

		return view('kd.mouthpiece.checksheet', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'destinations' => $destinations
		))->with('head', 'KD Mouthpiece')->with('page', 'MP Create Checksheet');
	}

	public function fetchKdMouthpieceMaterial(){
		$materials = Material::where('category', '=', 'KD')
		->where('hpl', '=', 'MP')
		->where('kd_name', '=', 'SINGLE')
		->get();

		$response = array(
			'status' => true,
			'target' => $materials
		);
		return Response::json($response);

	}

	public function scanKdMouthpieceOperator(Request $request){

		$employee_id = substr($request->get('employee_id'), 0, 9);
		$employee_sync = EmployeeSync::where('employee_id', '=', $employee_id)->first();

		if($employee_sync == ""){
			$response = array(
				'status' => false,
				'message' => "ID karyawan tidak ditemukan"
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'employee' => $employee_sync
		);
		return Response::json($response);

	}

	public function fetchKdMouthpieceChecksheet(){
		$checksheets = db::select("SELECT
			mouthpiece_checksheets.kd_number,
			mouthpiece_checksheets.packing_date,
			mouthpiece_checksheets.st_date,
			mouthpiece_checksheets.destination_shortname,
			mouthpiece_checksheets.print_status,
			group_concat(
			CONCAT( mouthpiece_checksheets.material_description, ' (', mouthpiece_checksheets.quantity, ')' )) AS item,
			sum( mouthpiece_checksheets.quantity ) AS total 
			FROM
			mouthpiece_checksheets
			GROUP BY
			mouthpiece_checksheets.kd_number,
			mouthpiece_checksheets.packing_date,
			mouthpiece_checksheets.st_date,
			mouthpiece_checksheets.print_status,
			mouthpiece_checksheets.destination_shortname");

		$response = array(
			'status' => true,
			'checksheets' => $checksheets
		);
		return Response::json($response);
	}

	public function createKdMouthpieceChecksheet(Request $request)
	{

		$prefix_now = 'KD'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','kd')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$kd_number = $code_generator->prefix . $number;
		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		$st_date = '';
		$location = $request->get('location');

		foreach($request->get('item_list') as $list){
			try{
				$new_checksheet = new MouthpieceChecksheet([
					'kd_number' => $kd_number,
					'material_number' => $list['material_number'],
					'material_description' => $list['material_description'],
					'quantity' => $list['quantity'],
					'actual_quantity' => 0,
					'remark' => '0',
					'packing_date' => $list['packing_date'],
					'destination_shortname' => $list['destination'],
					'st_date' => $list['shipment_date'],
					'created_by' => Auth::id()
				]);
				$new_checksheet->save();

				$st_date = $list['shipment_date'];

			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => Auth::id()
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$checksheets = db::select("SELECT
			material_number,
			material_description,
			sum( `usage` ) AS quantity,
			remark 
			FROM
			(
			SELECT
			bom.material_child AS material_number,
			material_plant_data_lists.material_description,
			m.quantity * bom.`usage` AS `usage`,
			bom.remark 
			FROM
			mouthpiece_checksheets m
			LEFT JOIN bom_components bom ON bom.material_parent = m.material_number
			LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = bom.material_child 
			WHERE
			m.kd_number = '".$kd_number."' 
			) AS pick 
			GROUP BY
			material_number,
			material_description,
			remark 
			ORDER BY
			remark ASC");

		foreach($checksheets as $checksheet){
			try{
				if($checksheet->remark == 'label outer'){
					$new_detail = new MouthpieceChecksheetDetail([
						'kd_number' => $kd_number,
						'material_number' => $checksheet->material_number,
						'material_description' => $checksheet->material_description,
						'quantity' => count($request->get('item_list')),
						'remark' => $checksheet->remark,
						'created_by' => Auth::id()
					]);
				}
				else if($checksheet->remark == 'outer box'){
					$new_detail = new MouthpieceChecksheetDetail([
						'kd_number' => $kd_number,
						'material_number' => $checksheet->material_number,
						'material_description' => $checksheet->material_description,
						'quantity' => 1,
						'remark' => $checksheet->remark,
						'created_by' => Auth::id()
					]);
				}
				else{
					$new_detail = new MouthpieceChecksheetDetail([
						'kd_number' => $kd_number,
						'material_number' => $checksheet->material_number,
						'material_description' => $checksheet->material_description,
						'quantity' => $checksheet->quantity,
						'remark' => $checksheet->remark,
						'created_by' => Auth::id()
					]);
				}

				$new_detail->save();
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => Auth::id()
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		$details = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', $kd_number)
		->select(
			'mouthpiece_checksheets.kd_number',
			'mouthpiece_checksheets.material_number',
			'mouthpiece_checksheets.material_description',
			'mouthpiece_checksheets.destination_shortname',
			'mouthpiece_checksheets.quantity'
		)
		->get();

		// $this->printKDO($kd_number, $st_date, $details, $location, 'PRINT', $details[0]->destination_shortname);

		$response = array(
			'status' => true,
			'message' => "Checksheet berhasil dibuat",
		);
		return Response::json($response);
	}

	public function deleteKdMouthpieceChecksheet(Request $request)
	{
		try{
			$checksheet = db::select("DELETE 
				FROM
				mouthpiece_checksheets 
				WHERE
				kd_number = '".$request->get('id')."'");

			$checksheet_detail = db::select("DELETE 
				FROM
				mouthpiece_checksheet_details
				WHERE
				kd_number = '".$request->get('id')."'");

			$response = array(
				'status' => true,
				'message' => "Checksheet berhasil dihapus",
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => Auth::id()
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function reprintKdMouthpieceChecksheet(Request $request){

		$kd_number = $request->get('kd_number');
		$location = $request->get('location');

		$details = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', $kd_number)
		->select(
			'mouthpiece_checksheets.kd_number',
			'mouthpiece_checksheets.material_number',
			'mouthpiece_checksheets.material_description',
			'mouthpiece_checksheets.quantity',
			'mouthpiece_checksheets.destination_shortname',
			'mouthpiece_checksheets.st_date'
		)
		->get();

		$this->printKDO($kd_number, $details[0]->st_date, $details, $location, 'REPRINT', $details[0]->destination_shortname);

		$response = array(
			'status' => true,
			'message' => "Reprint KDO Berhasil",
		);
		return Response::json($response);

	}

	public function printKDO($kd_number, $st_date, $knock_down_details, $storage_location, $remark, $destination_shortname){

		if(Auth::user()->role_code == 'MIS'){
			$printer_name = 'MIS';
		}else if (Auth::user()->role_code == 'OP-WH-Exim') {
			$printer_name = 'FLO Printer LOG';
		}else{
			$printer_name = 'KDO MP';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if($remark == 'REPRINT'){
			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setReverseColors(true);
			$printer->setTextSize(2, 2);
			$printer->text(" REPRINT "."\n");
			$printer->feed(1);
		}

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Storage Location:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(3, 3);
		$printer->text(strtoupper($storage_location."\n"));
		$printer->initialize();

		$printer->setUnderline(true);
		$printer->text('KDO:');
		$printer->feed(1);
		$printer->setUnderline(false);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($kd_number."\n");

		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Destination:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(6, 3);
		$printer->text(strtoupper($destination_shortname."\n\n"));
		$printer->initialize();

		$printer->initialize();
		$printer->setUnderline(true);
		$printer->text('Shipment Date:');
		$printer->setUnderline(false);
		$printer->feed(1);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(4, 2);
		$printer->text(date('d-M-Y', strtotime($st_date))."\n\n");
		$printer->initialize();
		$printer->text("No |GMC     | Description                 | Qty ");
		$total_qty = 0;
		for ($i=0; $i < count($knock_down_details); $i++) {
			$number = $this->writeString($i+1, 2, ' ');
			$qty = $this->writeString($knock_down_details[$i]->quantity, 4, ' ');
			$material_description = substr($knock_down_details[$i]->material_description, 0,27);
			$material_description = $this->writeString($material_description, 27, ' ');
			$printer->text($number." |".$knock_down_details[$i]->material_number." | ".$material_description." | ".$qty);
			$total_qty += $knock_down_details[$i]->quantity;
		}
		$printer->feed(2);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Qty:             |Qty:            |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|Production       |Logistic        |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->text("Total Qty: ". $total_qty ."\n");
		$printer->feed(2);
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function writeString($text, $maxLength, $char) {
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

}