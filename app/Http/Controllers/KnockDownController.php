<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ShipmentSchedule;
use App\KnockDownLog;
use App\KnockDownDetail;
use App\KnockDown;
use App\CodeGenerator;
use App\Inventory;
use App\TransactionCompletion;
use App\TransactionTransfer;
use App\StorageLocation;
use App\ErrorLog;
use Carbon\Carbon;
use DataTables;
use Response;


class KnockDownController extends Controller{

	public function __construct(){
		$this->middleware('auth');
	}

	public function indexKD($id){
		if($id == 'z-pro'){
			$title = 'KD Z-PRO';
			$title_jp = '??';
		}else if($id == 'sub-assy-sx'){
			$title = 'KD Sub Assy SX';
			$title_jp = '??';
		}else if($id == 'sub-assy-fl'){
			$title = 'KD Sub Assy FL';
			$title_jp = '??';
		}else if($id == 'sub-assy-cl'){
			$title = 'KD Sub Assy CL';
			$title_jp = '??';
		}

		return view('kd.index_kd', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $id,
		))->with('page', 'KD Delivery')->with('head', $title);
	}

	public function scanKdDelivery(Request $request){

		$id = Auth::id();

		$status = $request->get('status');

		$knock_down = KnockDown::where('kd_number', '=', $request->get('kd_number'))
		->where('status', '=', ($status-1))
		->first();

		if(!$knock_down){
			$response = array(
				'status' => false,
				'message' => 'Nomor KDO tidak ditemukan'
			);
			return Response::json($response);
		}

		$knock_down_details = KnockDownDetail::where('kd_number', '=', $request->get('kd_number'))->get();

		$knock_down->status = $status;
		foreach ($knock_down_details as $knock_down_detail) {

			$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $knock_down_detail->material_number, 'storage_location' => $knock_down_detail->storage_location]);
			$inventoryWIP->quantity = ($inventoryWIP->quantity-$knock_down_detail->quantity);

			$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $knock_down_detail->material_number, 'storage_location' => 'FSTK']);
			$inventoryFSTK->quantity = ($inventoryFSTK->quantity+$knock_down_detail->quantity);

			$transaction_transfer = new TransactionTransfer([
				'plant' => '8190',
				'serial_number' => $knock_down_detail->kd_number,
				'material_number' => $knock_down_detail->material_number,
				'issue_plant' => '8190',
				'issue_location' => $knock_down_detail->storage_location,
				'receive_plant' => '8191',
				'receive_location' => 'FSTK',
				'transaction_code' => 'MB1B',
				'movement_type' => '9P1',
				'quantity' => $knock_down_detail->quantity,
				'created_by' => $id
			]);

			try{
				DB::transaction(function() use ($inventoryWIP, $inventoryFSTK, $transaction_transfer, $knock_down){
					$inventoryWIP->save();
					$inventoryFSTK->save();
					$transaction_transfer->save();
					$knock_down->save();
				});	
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
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
			$kd_log = KnockDownLog::updateOrCreate(
				['kd_number' => $request->get('kd_number'), 'status' => $status],
				['created_by' => $id, 'status' => $status, 'updated_at' => Carbon::now()]
			);
			$kd_log->save();
		}
		catch(\Exception $e){
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
		
		$response = array(
			'status' => true,
			'message' => 'KDO berhasil ditransfer ke FSTK.',
		);
		return Response::json($response);
	}

	public function fetchKDO(Request $request){
		$status = $request->get('status');

		$knock_downs = KnockDown::leftJoin('knock_down_logs', 'knock_down_logs.kd_number', '=', 'knock_downs.kd_number')
		->where('knock_down_logs.status', '=', $status)
		->where('knock_downs.status', '=', $status)
		->orderBy('knock_down_logs.updated_at', 'desc')
		->select('knock_downs.kd_number', 'knock_downs.actual_count', 'knock_down_logs.updated_at')
		->get();

		return DataTables::of($knock_downs)
		->addColumn('detailKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-primary" onClick="detailKDO(id)" id="' . $knock_downs->kd_number . '"><i class="fa fa-eye"></i></a>';
		})
		->addColumn('deleteKDO', function($knock_downs){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteKDO(id)" id="' . $knock_downs->kd_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->rawColumns([ 'detailKDO' => 'detailKDO', 'deleteKDO' => 'deleteKDO'])
		->make(true);
	}

	public function fetchKD($id){
		$datefrom = date('Y-m-01');
		$dateto = date('Y-m-d');

		$storage = '';
		if($id == 'z-pro'){
			$storage = 'ZPRO';
		}else if($id == 'sub-assy-sx'){
			$storage = 'SX91';
		}else if($id == 'sub-assy-fl'){
			$storage = 'FL91';
		}else if($id == 'sub-assy-cl'){
			$storage = 'CL91';
		}

		$target = db::select("select target.material_number, target.material_description, sum(target.quantity ) as target from
			(select s.material_number, m.material_description, sum(quantity) as quantity from production_schedules s
			left join materials m on m.material_number = s.material_number
			where date(s.due_date) >= '".$datefrom."'
			and date(s.due_date) <= '".$dateto."'
			and m.category = 'KD'
			and m.hpl = '".$storage."'
			group by s.material_number, m.material_description
			union all
			select d.material_number, m.material_description, sum(-quantity) as quantity from knock_down_details d
			left join materials m on m.material_number = d.material_number
			where date(d.created_at) >= '".$datefrom."'
			and date(d.created_at) <= '".$dateto."'
			and m.hpl = '".$storage."'
			group by d.material_number, m.material_description) target
			group by target.material_number, target.material_description
			having target > 0
			order by target desc");

		$response = array(
			'status' => true,
			'target' => $target,
		);
		return Response::json($response);

	}

	public function indexKdDelivery(){
		return view('kd.kd_delivery')->with('page', 'KD Delivery');
	}

	public function fetchKdDetail(Request $request){
		$location = $request->get('location');

		$detail = db::select("select m.material_number, m.material_description, v.lot_completion from materials m
			left join material_volumes v on v.material_number = m.material_number
			where m.material_number = '".$request->get('material_number')."'");

		$knock_down = KnockDown::where('remark','=',$location)
		->where('status','=',0)
		->orderBy('kd_number','desc')
		->first();

		$actual_count = 0;
		if($knock_down){
			$actual_count = $knock_down->actual_count;
		}

		$response = array(
			'status' => true,
			'detail' => $detail,
			'actual_count' => $actual_count,
		);
		return Response::json($response);
	}

	public function forcePrintLabel(Request $request){
		$id = Auth::id();
		$location = $request->get('location');
		$storage_location = '';
		if($location = 'z-pro'){
			$storage_location = 'ZPA0';
		}

		$knock_down = KnockDown::where('remark','=',$location)
		->where('status','=', 0)
		->orderBy('kd_number', 'desc')
		->first();
		$knock_down->status = 1;

		$kd_number = $knock_down->kd_number;

		$knock_down_log = KnockDownLog::updateOrCreate(
			['kd_number' => $kd_number, 'status' => 1],
			['created_by' => $id, 'status' => 1, 'updated_at' => Carbon::now()]
		);

		try{
			DB::transaction(function() use ($knock_down, $knock_down_log){
				$knock_down->save();
				$knock_down_log->save();
			});

			$knock_down_details = KnockDownDetail::leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description', db::raw('sum(knock_down_details.quantity) as quantity'))
			->groupBy('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description')
			->get();

			$st_date = KnockDownDetail::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
			->where('knock_down_details.kd_number','=',$kd_number)
			->select('knock_down_details.kd_number','knock_down_details.material_number','shipment_schedules.st_date')
			->orderBy('shipment_schedules.st_date','asc')
			->first();

			$storage_location = StorageLocation::where('storage_location', '=', $storage_location)->first();

			$this->printKDO($kd_number, $st_date->st_date, $knock_down_details, $storage_location->location);

			$response = array(
				'status' => true,
				'message' => 'Print Label Sukses',
				'actual_count' => 0,
			);
			return Response::json($response);
		}catch(Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}	

	}

	public function printLabel(Request $request){
		$prefix_now = 'KD'.date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','kd')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		$material_number = $request->get('material_number');
		$quantity = $request->get('quantity');
		$material_description = $request->get('material_description');
		$location = $request->get('location');
		$max_count = 0;

		if($location = 'z-pro'){
			// $max_count = 10;
			$max_count = 99;

		}

		$storage_location = '';
		if($location = 'z-pro'){
			$storage_location = 'ZPA0';
		}


		$shipment_schedule = ShipmentSchedule::where('material_number', '=', $material_number)
		->where('actual_quantity','<',db::raw('quantity'))
		->orderBy('id', 'asc')
		->orderBy('st_date', 'asc')
		->first();
		
		if($shipment_schedule){
			$knock_down = KnockDown::where('remark','=',$location)
			->where('status','=', 0)
			->orderBy('kd_number', 'desc')
			->first();

			$kd_number = '';
			if($knock_down){
				if($knock_down->actual_count < $knock_down->max_count){
					$kd_number = $knock_down->kd_number;
					$knock_down->actual_count = $knock_down->actual_count + 1;

				}else{
					$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
					$kd_number = $code_generator->prefix . $number;
					$code_generator->index = $code_generator->index+1;
					$code_generator->save();

					$knock_down = new KnockDown([
						'kd_number' => $kd_number,
						'created_by' => Auth::id(),
						'max_count' => $max_count,
						'actual_count' => 1,
						'remark' => $location,
						'status' => 0,
					]);

				}
			}else{
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$kd_number = $code_generator->prefix . $number;
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$knock_down = new KnockDown([
					'kd_number' => $kd_number,
					'created_by' => Auth::id(),
					'max_count' => $max_count,
					'actual_count' => 1,
					'remark' => $location,
					'status' => 0,
				]);

			}

			$knock_down_log;
			if(($knock_down->actual_count + 1) == $max_count){
				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 1],
					['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
				);
			}else{
				$knock_down_log = KnockDownLog::updateOrCreate(
					['kd_number' => $kd_number, 'status' => 0],
					['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
				);
			}

			$knock_down_detail = new KnockDownDetail([
				'kd_number' => $kd_number,
				'material_number' => $material_number,
				'quantity' => $quantity,
				'shipment_schedule_id' => $shipment_schedule->id,
				'storage_location' => $storage_location,
				'created_by' => Auth::id(),
			]);

			

			$inventory = Inventory::where('plant','=','8190')
			->where('material_number','=',$material_number)
			->where('storage_location','=',$storage_location)
			->first();

			if($inventory){
				$inventory->quantity = $inventory->quantity + $quantity;
			}else{	
				$inventory = new Inventory([
					'plant' => '8190',
					'material_number' => $material_number,
					'storage_location' => $storage_location,
					'quantity' => $quantity,
				]);
			}

			$transaction_completion = new TransactionCompletion([
				'serial_number' => $kd_number,
				'material_number' => $material_number,
				'issue_plant' => '8190',
				'issue_location' => $storage_location,
				'quantity' => $quantity,
				'movement_type' => '101',
				'created_by' => Auth::id(),
			]);

			$shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $quantity;

			try{
				DB::transaction(function() use ($knock_down, $knock_down_detail, $shipment_schedule, $inventory, $transaction_completion, $knock_down_log){
					$knock_down->save();
					$knock_down_detail->save();
					$shipment_schedule->save();
					$inventory->save();
					$transaction_completion->save();
					$knock_down_log->save();
				});

				$knock_down = KnockDown::where('remark','=',$location)
				->where('status','=',0)
				->orderBy('kd_number', 'desc')
				->first();

				if($knock_down->actual_count == $max_count){
					$knock_down_details = KnockDownDetail::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
					->leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
					->where('knock_down_details.kd_number','=',$kd_number)
					->select('knock_down_details.kd_number','knock_down_details.material_number', 'materials.material_description', 'knock_down_details.quantity','shipment_schedules.st_date')
					->get();

					$knock_down = KnockDown::where('kd_number', '=', $kd_number)->update(['status' => '1']);

					$storage_location = StorageLocation::where('storage_location', '=', $storage_location)->first();
					
					$this->printKDO($kd_number, $knock_down_details[9]->st_date, $knock_down_details, $storage_location->location);
				}

				$knock_down = KnockDown::where('kd_number', '=', $kd_number)->first();

				$response = array(
					'status' => true,
					'message' => 'Print Label Sukses',
					'actual_count' => $knock_down->actual_count,
				);
				return Response::json($response);
			}catch(Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}		
		}else{
			$response = array(
				'status' => false,
				'message' => 'Tidak ada Skedul Pengiriman',
			);
			return Response::json($response);
		}
	}

	public function printKDO($kd_number, $st_date, $knock_down_details, $storage_location){
		
		if(Auth::user()->role_code == 'op-zpro'){
			$printer_name = 'KDO ZPRO';
		}
		else{
			$printer_name = 'TESTPRINTER';
		}

		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);
		
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

