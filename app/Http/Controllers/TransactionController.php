<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\StorageLocation;
use Response;
use App\ReturnList;
use App\User;
use App\ReturnLog;
use App\ReturnAdditional;
use DataTables;


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
			'CL51',
			'FL51',
			'SX51',
			'VN51',
			'CL21',
			'FL21',
			'SX21',
			'VN21'
		];
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
		));
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

				$material = db::connection('mysql2')->table('materials')
				->where('material_number', '=', $return->material_number)
				->first();

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
		$log = ReturnLog::leftJoin(db::raw('(select id, name from users) as returner'), 'returner.id', '=', 'return_logs.returned_by')
		->leftJoin(db::raw('(select id, name from users) as creator'), 'creator.id', '=', 'return_logs.created_by');

		if(strlen($request->get('datefrom')) > 0 ){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$log = $log->where(db::raw('date(return_logs.slip_created)'), '>=', $datefrom);
		}
		if(strlen($request->get('dateto')) > 0 ){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
			$log = $log->where(db::raw('date(return_logs.slip_created)'), '<=', $dateto);
		}
		if($request->get('issue') != null){
			$log = $log->whereIn('return_logs.issue_location', $request->get('issue'));
		}
		if($request->get('receive') != null){
			$log = $log->whereIn('return_logs.receive_location', $request->get('receive'));
		}
		if($request->get('material') != null){
			$log = $log->whereIn('return_logs.material_number', $request->get('material'));
		}
		if($request->get('remark') != null){
			$log = $log->whereIn('return_logs.remark', $request->get('remark'));
		}

		$log = $log->orderBy('return_logs.slip_created', 'asc')
		->select(
			'return_logs.slip_created',
			'return_logs.material_number',
			'return_logs.material_description',
			'return_logs.issue_location',
			'return_logs.receive_location',
			db::raw('concat( SPLIT_STRING ( returner.name, " ", 1 ), " ", SPLIT_STRING ( returner.name, " ", 2 ) ) AS returner'),
			db::raw('concat( SPLIT_STRING ( creator.name, " ", 1 ), " ", SPLIT_STRING ( creator.name, " ", 2 ) ) AS creator'),
			'return_logs.remark',
			'return_logs.quantity',
			db::raw('if(return_logs.remark = "received", return_logs.created_at, "-") as receive_at'),
			db::raw('if(return_logs.remark = "rejected", return_logs.created_at, "-") as reject_at'),
			db::raw('if(return_logs.remark = "deleted", return_logs.created_at, "-") as delete_at')
		)
		->get();

		// $response = array(
		// 	'status' => true,
		// 	'log' => $log,
		// );
		// return Response::json($response);

		return DataTables::of($log)->make(true);


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

		if(Auth::user()->role == 'MIS' || Auth::user()->role == 'S'){
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
			else if($receive == 'SX51' || $receive == 'CL51' || $receive == 'FL51' || $receive == 'VN51' || $receive == 'VN91'){
				$printer_name = 'Stockroom-Printer';			
			}
			else if($receive == 'SX21' || $receive == 'CL21' || $receive == 'FL21' || $receive == 'VN21'){
				$printer_name = 'Welding-Printer';			
			}
			else{
				$printer_name = 'MIS';
			}
		}
			// dd($printer_name);
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

		if(Auth::user()->role == 'MIS' || Auth::user()->role == 'S'){
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
			else if($receive == 'SX51' || $receive == 'CL51' || $receive == 'FL51' || $receive == 'VN51' || $receive == 'VN91'){
				$printer_name = 'Stockroom-Printer';			
			}
			else if($receive == 'SX21' || $receive == 'CL21' || $receive == 'FL21' || $receive == 'VN21'){
				$printer_name = 'Welding-Printer';			
			}
			else{
				$printer_name = 'MIS';
			}
		}
			// dd($printer_name);
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
}
