<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Exception;
use Carbon\Carbon;
use App\StorageLocation;
use App\ReturnList;
use App\User;
use App\ReturnLog;
use App\ReturnAdditional;
use App\SapCompletion;
use DataTables;
use DateTime;
use Response;
use PDF;
use Excel;


class TransactionController extends Controller
{

	private $storage_location;
	public function __construct()
	{
		$this->storage_location = [
			'CL91',
			'CLB9',
			'FL91',
			'SX91',
			'VN91',
			'CL51',
			'FL51',
			'SX51',
			'VN51',
			'CL21',
			'FL21',
			'SX21',
			'VN21',
			'VNA0'
		];
	}

	public function indexUploadSapData(){
		$title = "Upload SAP Data";
		$title_jp = "";

		$cost_center_names = StorageLocation::whereNotNull('cost_center_name')
		->select('cost_center_name')
		->distinct()
		->orderBy('cost_center_name', 'asc')
		->get();

		return view('general.sap.upload_data', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'cost_center_names' => $cost_center_names
		))->with('page', 'SAP Data');
	}

	public function indexReturnLogs(){

		$storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
		->orderBy('location', 'asc')
		->get();

		$materials = db::table('return_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location', 'receive_location')
		->orderBy('issue_location', 'ASC')
		->orderBy('material_number', 'ASC')
		->get();


		return view('return.return_logs', array(
			'title' => 'Return Logs',
			'title_jp' => '??',
			'storage_locations' => $storage_locations,
			'materials' => $materials
		))->with('page', 'Return Logs');
	}

	public function indexReturn(){
		// $storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
		// ->orderBy('location', 'asc')
		// ->get();

		return view('return.index', array(
			'title' => 'Return Material',
			'title_jp' => '??',
			'storage_locations' => $this->storage_location
		))->with('page', 'Return');
	}

	public function indexReturnData(){
		$storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
		->orderBy('location', 'asc')
		->get();

		return view('return.list', array(
			'title' => 'Data Return Material',
			'title_jp' => '??',
			'storage_locations' => $storage_locations
		))->with('page', 'Return');
	}

	public function cancelReturn(Request $request){

		try {

			$return = ReturnLog::where('id', '=', $request->get('id'))->first();

			$material = db::connection('mysql2')->table('materials')
			->where('material_number', '=', $return->material_number)
			->first();

			$return_log = new ReturnLog([
				'return_id' => $return->return_id,
				'material_number' => $return->material_number,
				'material_description' => $return->material_description,
				'issue_location' => $return->issue_location,
				'receive_location' => $return->receive_location,
				'quantity' => $return->quantity,
				'returned_by' => $return->returned_by,
				'created_by' => Auth::id(),
				'slip_created' => $return->slip_created,
				'remark' => 'canceled'
			]);
			$return_log->save();

			$return_completion = db::connection('mysql2')->table('histories')->insert([
				"category" => "completion_adjustment",
				"completion_barcode_number" => "",
				"completion_description" => "",
				"completion_location" => $return->issue_location,
				"completion_issue_plant" => "8190",
				"completion_material_id" => $material->id,
				"completion_reference_number" => "",
				"lot" => $return->quantity,
				"synced" => 0,
				'user_id' => "1",
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);

			$return_transfer = db::connection('mysql2')->table('histories')->insert([
				"category" => "transfer_adjustment",
				"transfer_barcode_number" => "",
				"transfer_document_number" => "8190",
				"transfer_material_id" => $material->id,
				"transfer_issue_location" => $return->issue_location,
				"transfer_issue_plant" => "8190",
				"transfer_receive_plant" => "8190",
				"transfer_receive_location" => $return->receive_location,
				"transfer_cost_center" => "",
				"transfer_gl_account" => "",
				"transfer_transaction_code" => "MB1B",
				"transfer_movement_type" => "9I3",
				"transfer_reason_code" => "",
				"lot" => $return->quantity,
				"synced" => 0,
				'user_id' => "1",
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);


			$response = array(
				'status' => true,
				'message' => 'Return berhasil dicancel',
			);
			return Response::json($response);

			
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}	
	}

	public function deleteReturn(Request $request){
		$auth_id = Auth::id();
		$return = ReturnList::where('id', '=', $request->get('id'))->first();
		$receive = $return->receive_location;
		try{
			$return_log = new ReturnLog([
				'return_id' => $return->id,
				'material_number' => $return->material_number,
				'material_description' => $return->material_description,
				'issue_location' => $return->issue_location,
				'receive_location' => $return->receive_location,
				'quantity' => $return->quantity,
				'returned_by' => $return->created_by,
				'created_by' => $auth_id,
				'slip_created' => $return->created_at,
				'remark' => 'deleted'
			]);
			$return_log->save();
			$return->forceDelete();
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
		$response = array(
			'status' => true,
			'receive' => $receive,
			'message' => 'Slip return berhasil didelete',
		);
		return Response::json($response);
	}

	function confirmReturn(Request $request){
		$id = explode('+', $request->get('id'));
		$auth_id = Auth::id();
		$return = ReturnList::where('id', '=', $id[1])->first();

		if($return == null){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}


		if($id[0] == 'receive'){
			try{

				$material = db::connection('mysql2')->table('materials')
				->where('material_number', '=', $return->material_number)
				->first();


				$return_log = new ReturnLog([
					'return_id' => $return->id,
					'material_number' => $return->material_number,
					'material_description' => $return->material_description,
					'issue_location' => $return->issue_location,
					'receive_location' => $return->receive_location,
					'quantity' => $return->quantity,
					'returned_by' => $return->created_by,
					'created_by' => $auth_id,
					'slip_created' => $return->created_at,
					'remark' => 'received'
				]);
				$return_log->save();
				

				$return_data = db::connection('mysql2')->table('transfers_return')->insert([
					'material_id' => $material->id,
					'issue_location' => $return->issue_location,
					'issue_plant' => "8190",
					'receive_location' => $return->receive_location,
					'receive_plant' => "8190",
					'movement_type' => "9I4",
					'transaction_code' => "MB1B",
					'document_number' => "",
					'lot' => $return->quantity,
					'cost_center' => "",
					'gl_account' => "",
					'reason_code' => "",
					'user_id' => "1",
					'active' => "1",
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				]);

				$return_completion = db::connection('mysql2')->table('histories')->insert([
					"category" => "completion_return",
					"completion_barcode_number" => "",
					"completion_description" => "",
					"completion_location" => $return->issue_location,
					"completion_issue_plant" => "8190",
					"completion_material_id" => $material->id,
					"completion_reference_number" => "",
					"lot" => $return->quantity*-1,
					"synced" => 0,
					'user_id' => "1",
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				]);
				
				$return_transfer = db::connection('mysql2')->table('histories')->insert([
					"category" => "transfer_return",
					"transfer_barcode_number" => "",
					"transfer_document_number" => "8190",
					"transfer_material_id" => $material->id,
					"transfer_issue_location" => $return->issue_location,
					"transfer_issue_plant" => "8190",
					"transfer_receive_plant" => "8190",
					"transfer_receive_location" => $return->receive_location,
					"transfer_cost_center" => "",
					"transfer_gl_account" => "",
					"transfer_transaction_code" => "MB1B",
					"transfer_movement_type" => "9I4",
					"transfer_reason_code" => "",
					"lot" => $return->quantity,
					"synced" => 0,
					'user_id' => "1",
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				]);
				$return->forceDelete();
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}

			$response = array(
				'status' => true,
				'message' => 'Slip return berhasil dikonfirmasi',
			);
			return Response::json($response);
		}
		else{
			try{
				$return_log = new ReturnLog([
					'return_id' => $return->id,
					'material_number' => $return->material_number,
					'material_description' => $return->material_description,
					'issue_location' => $return->issue_location,
					'receive_location' => $return->receive_location,
					'quantity' => $return->quantity,
					'returned_by' => $return->created_by,
					'created_by' => $auth_id,
					'slip_created' => $return->created_at,
					'remark' => 'rejected'
				]);
				$return_log->save();
				$return->forceDelete();
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}

			$response = array(
				'status' => true,
				'message' => 'Slip return berhasil ditolak',
			);
			return Response::json($response);
		}
	}

	public function fetchReturnLogs(Request $request){

		$date = '';
		if(strlen($request->get('datefrom')) > 0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$date = "AND date(slip_created) >= '".$datefrom."' ";
			if(strlen($request->get('dateto')) > 0){
				$dateto = date('Y-m-d', strtotime($request->get('dateto')));
				$date = $date . "AND date(slip_created) <= '".$dateto."' ";
			}
		}

		$issue = '';
		if($request->get('issue') != null){
			$issues =  $request->get('issue');
			for ($i=0; $i < count($issues); $i++) {
				$issue = $issue."'".$issues[$i]."'";
				if($i != (count($issues)-1)){
					$issue = $issue.',';
				}
			}
			$issue = " AND issue_location IN (".$issue.") ";
		}

		$receive = '';
		if($request->get('receive') != null){
			$receives =  $request->get('receive');
			for ($i=0; $i < count($receives); $i++) {
				$receive = $receive."'".$receives[$i]."'";
				if($i != (count($receives)-1)){
					$receive = $receive.',';
				}
			}
			$receive = " AND receive_location IN (".$receive.") ";
		}

		$material = '';
		if($request->get('material') != null){
			$materials =  $request->get('material');
			for ($i=0; $i < count($materials); $i++) {
				$material = $material."'".$materials[$i]."'";
				if($i != (count($materials)-1)){
					$material = $material.',';
				}
			}
			$material = " AND material_number IN (".$material.") ";
		}

		$remark = '';
		if($request->get('remark') != null){
			$remarks =  $request->get('remark');
			for ($i=0; $i < count($remarks); $i++) {
				$remark = $remark."'".$remarks[$i]."'";
				if($i != (count($remarks)-1)){
					$remark = $remark.',';
				}
			}
			$remark = " AND remark IN (".$remark.") ";
		}

		$condition = $date . $issue . $receive . $material . $remark;

		$log = db::select("SELECT
			non.id,
			non.return_id,
			non.material_number,
			non.issue_location,
			non.receive_location,
			non.material_description,
			non.quantity,
			IF(cancel.remark is null, non.remark, cancel.remark) AS remark,
			non.slip_created AS printed_at,
			return_user.`name` AS printed_by,
			IF(non.remark = 'received', non.created_at, '-') AS received_at,
			IF(non.remark = 'received', non_user.`name`, '-') AS received_by,
			IF(non.remark = 'rejected', non.created_at, '-') AS rejected_at,
			IF(non.remark = 'rejected', non_user.`name`, '-') AS rejected_by,
			IF(non.remark = 'deleted', non.created_at, '-') AS deleted_at,
			IF(non.remark = 'deleted', non_user.`name`, '-') AS deleted_by,
			COALESCE(cancel.created_at, '-') AS canceled_at,
			COALESCE(cancel_user.`name`, '-') AS canceled_by
			FROM
			(SELECT id, return_id, material_number, material_description, issue_location, receive_location, quantity, remark, slip_created, returned_by, created_at, created_by FROM `return_logs`
			where remark <> 'canceled' ".$condition." ) AS non
			LEFT JOIN
			(SELECT id, return_id, remark, created_at, created_by FROM `return_logs`
			where remark = 'canceled' ".$condition." ) AS cancel
			ON non.return_id = cancel.return_id
			LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS return_user ON return_user.id = non.returned_by
			LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS non_user ON non_user.id = non.created_by
			LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS cancel_user ON cancel_user.id = cancel.created_by
			ORDER BY non.slip_created");

		
		return DataTables::of($log)
		->addColumn('cancel', function($data){
			if($data->remark == 'received'){
				if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PROD"){
					return '<button style="width: 50%; height: 100%;" onclick="cancelReturn(\''.$data->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
				}else{
					return '-';
				}
			}else{
				return '-';
			}		
		})
		->rawColumns([ 'cancel' => 'cancel'])
		->make(true);
	}

	function fetchReturn(Request $request){
		$id = substr($request->get('id'), 2);
		$return = ReturnList::where('return_lists.id', '=', $id)
		->leftJoin('users', 'users.id', '=', 'return_lists.created_by')
		->select('return_lists.id', 'return_lists.material_number', 'return_lists.material_description', 'return_lists.issue_location', 'return_lists.receive_location', 'return_lists.quantity', 'users.name', 'return_lists.created_at', 'return_lists.created_by')
		->first();

		if($return == null){
			$response = array(
				'status' => false,
				'message' => "QRcode return tidak ditemnukan.",
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'return' => $return,
		);
		return Response::json($response);
	}

	public function fetchReturnList(Request $request){
		// $lists = db::connection('mysql2')->table('transfers')
		// ->leftJoin('materials', 'materials.id', '=', 'transfers.material_id')
		// ->leftJoin('completions', 'completions.id', '=', 'transfers.completion_id')
		// ->select('materials.material_number', 'materials.description', 'transfers.issue_location', 'transfers.receive_location')
		// ->where('transfers.receive_location', '=', $request->get('loc'))
		// ->where('completions.active', '=', 0)
		// ->orderBy('transfers.issue_location', 'asc')
		// ->orderBy('materials.material_number', 'asc')
		// ->distinct()
		// ->get();

		$lists = db::table('return_materials')
		->whereNull('deleted_at')
		->select('material_number', 'material_description as description', 'issue_location', 'receive_location')
		->where('receive_location', '=', $request->get('loc'))
		->orderBy('issue_location', 'ASC')
		->orderBy('material_number', 'ASC')
		->get();

		// if(count($lists) == 0){
		// 	$lists = ReturnAdditional::select('material_number', 'description', 'issue_location', 'receive_location')
		// 	->where('receive_location', '=', $request->get('loc'))
		// 	->orderBy('issue_location', 'asc')
		// 	->orderBy('material_number', 'asc')
		// 	->distinct()
		// 	->get();
		// }

		if(count($lists) == 0){
			$response = array(
				'status' => false,
				'message' => 'Lokasi terpilih tidak memiliki list material'
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'lists' => $lists,
			'message' => 'Lokasi berhasil dipilih'
		);
		return Response::json($response);
	}

	public function fetchReturnResume(Request $request){

		$resumes = ReturnList::where('receive_location', '=', $request->get('loc'))
		->orderBy('issue_location', 'asc')
		->orderBy('material_number', 'asc')
		->leftJoin('users', 'users.id', '=', 'return_lists.created_by')
		->select('return_lists.id', 'return_lists.material_number', 'return_lists.material_description', 'return_lists.issue_location', 'return_lists.receive_location', 'return_lists.quantity', 'users.name', 'return_lists.created_at', 'return_lists.created_by')
		->orderBy('return_lists.created_at', 'asc')
		->get();

		$response = array(
			'status' => true,
			'resumes' => $resumes
		);
		return Response::json($response);
	}

	public function reprintReturn(Request $request){
		try{
			$return = ReturnList::where('id', '=', $request->get('id'))->first();
			self::returnSlip($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by);
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
		$response = array(
			'status' => true,
			'message' => 'Cetak ulang slip return berhasil'
		);
		return Response::json($response);
	}

	public function returnSlipCopy($id, $material, $description, $issue, $receive, $quantity, $created_by){
		$user = User::where('id', '=', $created_by)->first();

		if(Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'S'){
			$printer_name = 'MIS';
		}
		else{
			if($receive == 'CL91' || $receive == 'CLB9'){
				$printer_name = 'FLO Printer 102';
			}
			else if($receive == 'SX91'){
				$printer_name = 'FLO Printer 103';
			}
			else if($receive == 'FL91'){
				$printer_name = 'FLO Printer 101';			
			}
			else if($receive == 'SX51' || $receive == 'CL51' || $receive == 'FL51' || $receive == 'VN51'){
				$printer_name = 'Stockroom-Printer';			
			}
			else if($receive == 'SX21' || $receive == 'CL21' || $receive == 'FL21' || $receive == 'VN21'){
				$printer_name = 'Welding-Printer';			
			}
			else if($receive == 'VN91' || $receive == 'VNA0'){
				$printer_name = 'FLO Printer VN';
			}
			else if($receive == 'RC91'){
				$printer_name = 'FLO Printer RC';
			}
			else{
				$printer_name = 'MIS';
			}
		}
		
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text(" SLIP RETURN COPY"."\n");
		// $printer->feed(1);
		$printer->qrCode('RE'.$id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		// $printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->text($receive." -> ".$issue."\n");
		// $printer->feed(1);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->setReverseColors(true);
		$printer->setTextSize(4, 4);
		$printer->text(" ".$quantity." PC(s) \n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// $printer->textRaw("\xda".str_repeat("\xc4", 46)."\xbf\n");
		$printer->textRaw($user->name." (".date("d-M-Y H:i:s").")\n");
		// $printer->textRaw("\xc0".str_repeat("\xc4", 46)."\xd9\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function returnSlip($id, $material, $description, $issue, $receive, $quantity, $created_by){
		$user = User::where('id', '=', $created_by)->first();

		if(Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'S'){
			$printer_name = 'MIS';
		}
		else{
			if($receive == 'CL91' || $receive == 'CLB9'){
				$printer_name = 'FLO Printer 102';
			}
			else if($receive == 'SX91'){
				$printer_name = 'FLO Printer 103';
			}
			else if($receive == 'FL91'){
				$printer_name = 'FLO Printer 101';			
			}
			else if($receive == 'SX51' || $receive == 'CL51' || $receive == 'FL51' || $receive == 'VN51'){
				$printer_name = 'Stockroom-Printer';			
			}
			else if($receive == 'SX21' || $receive == 'CL21' || $receive == 'FL21' || $receive == 'VN21'){
				$printer_name = 'Welding-Printer';			
			}
			else if($receive == 'VN91' || $receive == 'VNA0'){
				$printer_name = 'FLO Printer VN';
			}
			else if($receive == 'RC91'){
				$printer_name = 'FLO Printer RC';
			}
			else{
				$printer_name = 'MIS';
			}
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(3, 3);
		$printer->text(" SLIP RETURN "."\n");
		// $printer->feed(1);
		$printer->qrCode('RE'.$id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		// $printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material."\n");
		$printer->text($receive." -> ".$issue."\n");
		// $printer->feed(1);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->setReverseColors(true);
		$printer->setTextSize(4, 4);
		$printer->text(" ".$quantity." PC(s) \n");
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// $printer->textRaw("\xda".str_repeat("\xc4", 46)."\xbf\n");
		$printer->textRaw($user->name." (".date("d-M-Y H:i:s").")\n");
		// $printer->textRaw("\xc0".str_repeat("\xc4", 46)."\xd9\n");
		$printer->feed(1);
		$printer->cut();
		$printer->close();
	}

	public function printReturn(Request $request){
		$id = Auth::id();
		try{
			$return = new ReturnList([
				'material_number' => $request->get('material'),
				'material_description' => $request->get('description'),
				'issue_location' => $request->get('issue'),
				'receive_location' => $request->get('receive'),
				'quantity' => $request->get('quantity'),
				'created_by' => $id		
			]);

			$return->save();

			self::returnSlip($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by);

			if($return->receive_location == 'CL91' || $return->receive_location == 'SX91'){
				self::returnSlipCopy($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by);
			}
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Cetak slip return berhasil'
		);
		return Response::json($response);
	}

	public function importCompletion(Request $request){
		if($request->hasFile('completion')) {
			try{				
				$file = $request->file('completion');
				$file_name = 'import_cs_'.Auth::id().'('. date("y-m-d") .')'.'.'.$file->getClientOriginalExtension();
				$file->move(public_path('import/completion/'), $file_name);


				$excel = public_path('import/completion/') . $file_name;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					$reader->skipRows(1);
				})->get();
				$rows = $rows->toArray();


				// DB::beginTransaction();
				$month = $request->get('date_completion');
				$cc = $request->get('cc');
				$cost_center_name = explode(",",$cc);


				$existing = SapCompletion::leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'sap_completions.storage_location')
				->where(db::raw('DATE_FORMAT(sap_completions.posting_date, "%Y-%m")'), $month)
				->whereIn('storage_locations.cost_center_name', $cost_center_name)
				->delete();


				for ($i=0; $i < count($rows); $i++) {
					$entry_date = $rows[$i][0]->format('Y-m-d');
					$posting_date = $rows[$i][3]->format('Y-m-d');
					$movement_type = $rows[$i][4];
					$material_number = $rows[$i][5];
					$quantity = $rows[$i][8];
					$storage_location = $rows[$i][12];
					$reference = $rows[$i][15];

					$log = new SapCompletion([
						'entry_date' => $entry_date,
						'posting_date' => $posting_date,
						'movement_type' => $movement_type,
						'material_number' => $material_number,
						'quantity' => $quantity,
						'storage_location' => $storage_location,
						'reference' => $reference,
						'created_by' => Auth::id()
					]);
					$log->save();

				}

				// DB::rollback();

				
				// DB::commit();

				$response = array(
					'status' => true,
					'message' => 'Upload file success'
				);
				return Response::json($response);

			}catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}else{
			$response = array(
				'status' => false,
				'message' => 'Upload failed, File not found',
			);
			return Response::json($response);
		}
	}
}
