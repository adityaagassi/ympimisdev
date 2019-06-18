<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\Libraries\ActMLEasyIf;
use App\LogProcess;
use App\PlcCounter;
use App\CodeGenerator;
use App\StampInventory;
use App\StampSchedule;
use App\Material;
use App\Process;

class ProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexLog(){
		return view('processes.assy_fl.log')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchLogTableFl(){
		$query = "select stamp_inventories.serial_number, stamp_inventories.model, max(if(log_processes.process_code = 1, log_processes.created_at, null)) as kariawase, max(if(log_processes.process_code = 2, log_processes.created_at, null)) as tanpoawase, max(if(log_processes.process_code = 3, log_processes.created_at, null)) as yuge, max(if(log_processes.process_code = 4, log_processes.created_at, null)) as chousei, if(stamp_inventories.status is null, 'InProcess', stamp_inventories.`status`) as `status` 
		from stamp_inventories 
		left join log_processes 
		on log_processes.serial_number = stamp_inventories.serial_number 
		where stamp_inventories.model like 'YFL%' 
		group by stamp_inventories.serial_number, stamp_inventories.model, stamp_inventories.status
		order by stamp_inventories.serial_number asc";

		$logs = DB::select($query);
		return DataTables::of($logs)->make(true);
	}

	public function indexRepairFl(){
		return view('processes.assy_fl.return')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

	}

	public function indexProcessAssyFL(){
		return view('processes.assy_fl.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::orderBy('created_at', 'desc')
		->get();
		return view('processes.assy_fl.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL0(){
		return view('processes.assy_fl.kariawase')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL2(){
		return view('processes.assy_fl.tanpoawase')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL3(){
		return view('processes.assy_fl.seasoning')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL4(){
		return view('processes.assy_fl.choseikanggo')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchReturnTableFl(){
		$stamp_inventories = StampInventory::where('origin_group_code', '=', '041')
		->where('status', '=', 'return')
		->orderBy('updated_at', 'desc')
		->get();

		return DataTables::of($stamp_inventories)
		->make(true);
	}

	public function scanSerialNumberReturnFl(Request $request){
		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
		->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

		$stamp_inventory->update(['status' => 'return']);

		$response = array(
			'status' => true,
			'message' => 'Return success',
		);
		return Response::json($response);
	}



	public function indexDisplay(){
		return view('processes.assy_fl.display', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexDisplayWipFL(){
		return view('processes.assy_fl.display_all', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');		
	}

	public function fetchwipflallstock(){
		$first = date('Y-m-01');

		if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(5)));
		}
		elseif(date('D')=='Sun'){
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
		}
		else{
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
		}

		$query = "
		select result1.model, result1.process_code, result1.process_name, if(result2.quantity is null, 0, result2.quantity) as quantity from
		(
		select distinct model, processes.process_code, processes.process_name from materials left join processes on processes.remark = CONCAT('YFL',materials.origin_group_code) where processes.remark = 'YFL041' and processes.process_code <> '5' and materials.category = 'FG') as result1
		left join
		(
		select stamp_inventories.model, stamp_inventories.process_code, sum(stamp_inventories.quantity) as quantity from stamp_inventories where stamp_inventories.status is null group by stamp_inventories.model, stamp_inventories.process_code
		) as result2 
		on result2.model = result1.model and result2.process_code = result1.process_code order by result1.model asc";

		$query2 = "
		select result1.model, if(result2.plan is null or result2.plan < 0, 0, result2.plan) as plan from
		(
		select distinct materials.model from materials where materials.origin_group_code = '041' and materials.category = 'FG'
		) as result1
		left join
		(
		select materials.model, sum(plan) as plan from
		(
		select material_number, sum(quantity) as plan from production_schedules where due_date >= '".$first."' and due_date <= '".$h4."' group by material_number
		union all
		select material_number, -(sum(quantity)) as plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$h4."' group by material_number
		) r
		left join materials on materials.material_number = r.material_number
		group by materials.model
		) as result2
		on result1.model = result2.model order by result1.model asc";

		$inventory = DB::select($query);
		$plan = DB::select($query2);

		$response = array(
			'status' => true,
			'inventory' => $inventory,
			'plan' => $plan,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLActualChart(Request $request){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$next_process = $request->get('processCode')+1;

		$query = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes 
		where process_code = '" . $next_process . "' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$chartData = DB::select($query);

		if(date('D')=='Fri'){
			if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
				$deduction = 600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 13:10:00')){
				$deduction = 4800;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 15:00:00')){
				$deduction = 5400;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:30:00')){
				$deduction = 5800;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
				$deduction = 7500;
			}
			else{
				$deduction = 0;
			}
		}
		else{
			if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
				$deduction = 600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 12:40:00')){
				$deduction = 3000;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 14:30:00')){
				$deduction = 3600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:00:00')){
				$deduction = 4200;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
				$deduction = 5700;
			}
			else{
				$deduction = 0;
			}
		}

		$query2 = "select date(log_processes.created_at) as due_date, sum(log_processes.quantity) as quantity, (select avg(manpower) from log_processes where log_processes.process_code = " . $request->get('processCode') . " and date(created_at) = '" . $now . "') as manpower, max(log_processes.created_at) as last_input,
		round(sum(log_processes.quantity*st_assemblies.st)*60) as std_time,
		(timestampdiff(second, '" . date('Y-m-d 07:05:00') . "', max(log_processes.created_at))-" . $deduction . ")*(select avg(manpower) from log_processes where log_processes.process_code = " . $request->get('processCode') . " and date(created_at) = '".$now."') as act_time 
		from log_processes 
		left join st_assemblies 
		on st_assemblies.model = log_processes.model 
		where log_processes.process_code = " . $next_process . " and st_assemblies.process_code = ".$request->get('processCode')." 
		and date(log_processes.created_at) = '" . $now . "' 
		group by date(log_processes.created_at)";

		$effData = DB::select($query2);

		$totalStock = StampInventory::where('process_code', '=', $request->get('processCode'))
		->whereNull('status')
		->sum('quantity');

		$response = array(
			'status' => true,
			'chartData' => $chartData,
			'effData' => $effData,
			'totalStock' => $totalStock
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLDisplayActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);


		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}

	public function inputProcessAssyFL(Request $request){
		$stamp = LogProcess::where('serial_number', '=', $request->get('serialNumber'))
		->where('model', 'like', 'YFL%')
		->first();

		try{
			$id = Auth::id();

			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => $request->get('processCode'), 
					'serial_number' => $request->get('serialNumber'),
					'origin_group_code' => $request->get('originGroupCode')
				],
				[
					'model' => $stamp->model,
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
			->where('origin_group_code', '=', '041')
			->first();

			$inventory->status = null;
			$inventory->process_code = $request->get('processCode');

			$inventory->save();
			$log_process->save();

			$response = array(
				'status' => true,
				'message' => 'Input success',
			);
			return Response::json($response);
		}
		catch (QueryException $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}


	// public function fetchStampPlan(){

	// 	$now = date('Y-m-d');

	// 	$query = "select model, sum(plan) as plan, sum(actual) as actual from
	// 	(
	// 	select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	// 	union all

	// 	select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
	// 	) as plan
	// 	group by model
	// 	having model like 'YFL%'";

	// 	$planData = DB::select($query);
	// 	$materials = DB::table('materials')->where('model', 'like', 'YFL%')->select('model')->distinct()->get();

	// 	$response = array(
	// 		'status' => true,
	// 		'planData' => $planData,
	// 		'model' => $materials,
	// 	);
	// 	return Response::json($response);
	// }

	public function fetchSerialNumber(Request $request){
		$code_generator = DB::table('code_generators')->where('note', '=', $request->get('originGroupCode'))->first();
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index);
		$number2 = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

		$lastCounter = $code_generator->prefix.$number;
		$nextCounter = $code_generator->prefix.$number2;

		$response = array(
			'status' => true,
			'lastCounter' => $lastCounter,
			'nextCounter' => $nextCounter,
		);
		return Response::json($response);
	}

	// public function fetchResult(){
	// 	$now = date('Y-m-d');
	// 	$log_processes = db::table('log_processes')
	// 	->where('process_code', '=', '1')
	// 	->where('model', 'like', 'YFL%')
	// 	->where(db::raw('date(created_at)'), '=', $now)
	// 	->orderBy('created_at', 'desc')
	// 	->get();

	// 	$response = array(
	// 		'status' => true,
	// 		'resultData' => $log_processes,
	// 	);
	// 	return Response::json($response);
	// }

	public function adjust(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$prefix = $code_generator->prefix;
		$lastIndex = $code_generator->index;

		$response = array(
			'status' => true,
			'prefix' => $prefix,
			'lastIndex' => $lastIndex,
		);
		return Response::json($response);
	}

	public function adjustUpdate(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$code_generator->index = $request->get('lastIndex');
		$code_generator->prefix = $request->get('prefix');
		$code_generator->save();

		$response = array(
			'status' => true,
			'message' => 'Serial number adjustment success',
		);
		return Response::json($response);
	}

	public function adjustSerial(Request $request){
		if($request->get('adjust') == 'minus'){
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index-1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted minus',
			);
			return Response::json($response);
		}
		else{
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted plus',
			);
			return Response::json($response);
		}
	}

	public function editStamp(Request $request){
		$log_process = LogProcess::find($request->get('id'));

		$response = array(
			'status' => true,
			'logProcess' => $log_process,
		);
		return Response::json($response);
	}

	public function destroyStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model);

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model);

		$log_process->forceDelete();
		$stamp_inventory->forceDelete();

		$response = array(
			'status' => true,
			'message' => 'Delete Success',
		);
		return Response::json($response);
	}

	public function updateStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model)
		->first();
		$log_process->model = $request->get('model');

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model)
		->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

		$stamp_inventory->update(['model' => $request->get('model')]);
		$log_process->save();

		$response = array(
			'status' => true,
			'message' => 'Update Success',
		);
		return Response::json($response);
	}


	public function reprint_stamp(Request $request)
	{
		$model = db::table('stamp_inventories')
		->where('model', 'like', 'YFL%')
		->where('serial_number', '=', $request->get('stamp_number_reprint'))
		->select ('model')
		->first();

		if ($request->get('stamp_number_reprint') != null){
			try {
				$code_generator = CodeGenerator::where('note', '=', '041')->first();
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$printer_name = 'SUPERMAN';

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setBarcodeWidth(2);
				$printer->setBarcodeHeight(64);
				$printer->barcode($request->get('stamp_number_reprint'), Printer::BARCODE_CODE39);
			// $printer->qrCode($request->get('serialNumber'));
				$printer->setTextSize(3, 1);
				$printer->text($request->get('stamp_number_reprint')."\n");
				$printer->feed(1);
				$printer->text($model->model."\n");
				$printer->setTextSize(1, 1);
				$printer->text(date("d-M-Y H:i:s")."\n");
				$printer->cut();
				$printer->close();

				return back()->with('status', 'Stamp has been reprinted.')->with('page', 'Assembly Process');
			}
			catch(\Exception $e){
				return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
			}
		}
		else{
			return back()->with('error', 'Serial number '. $request->get('stamp_number_reprint') . ' not found.');
		}
	}

	public function stamp(Request $request){
		try{
			if ($request->get('originGroupCode') =='041') {
				$plc = new ActMLEasyIf(0);
				$datas = $plc->read_data('D0', 5);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}else if ($request->get('originGroupCode') =='042') {
				$plc = new ActMLEasyIf(3);
				$datas = $plc->read_data('D0', 5);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}else if ($request->get('originGroupCode') =='043') {
				$plc = new ActMLEasyIf(2);
				$datas = $plc->read_data('D0', 5);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}
			$data = $datas[0];

			if($plc_counter->plc_counter != $data){

				if(Auth::user()->role_code == "OP-SubAssy-FL"){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $request->get('serialNumber'),
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
					$code_generator->index = $code_generator->index+1;

					if ($request->get('category')=='FG'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $request->get('serialNumber'),
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}

					$plc_counter->save();
					$code_generator->save();
					$log_process->save();

					// if ($request->get('category')=='FG'){
					$printer_name = 'SUPERMAN';

					$connector = new WindowsPrintConnector($printer_name);
					$printer = new Printer($connector);

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setBarcodeWidth(2);
					$printer->setBarcodeHeight(64);
					$printer->barcode($request->get('serialNumber'), Printer::BARCODE_CODE39);
					$printer->setTextSize(3, 1);
					$printer->text($request->get('serialNumber')."\n");
					$printer->feed(1);
					$printer->text($request->get('model')."\n");
					$printer->setTextSize(1, 1);
					$printer->text(date("d-M-Y H:i:s")."\n");
					$printer->cut();
					$printer->close();
					// }

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);
				}
				else if(Auth::user()->role_code == "OP-Body-CL"){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $request->get('serialNumber'),
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
					$code_generator->index = $code_generator->index+1;

					if ($request->get('category')=='FG'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $request->get('serialNumber'),
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}

					$plc_counter->save();
					$code_generator->save();
					$log_process->save();

					if ($request->get('category')=='FG'){
						//print

					}

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);

				}else if(Auth::user()->role_code == "OP-Handatsuke-SX"){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $request->get('serialNumber'),
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
					$code_generator->index = $code_generator->index+1;

					if ($request->get('category')=='FG'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $request->get('serialNumber'),
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}

					$plc_counter->save();
					$code_generator->save();
					$log_process->save();

					if ($request->get('category')=='FG'){
						//print

					}

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);

				}
			// else{
			// 	$response = array(
			// 		'status' => true,
			// 		'statusCode' => 'stamp',
			// 		'message' => 'Stamp success',
			// 		'role' => 'Guest'
			// 	);
			// 	return Response::json($response);
			// }
			}
			else{
				$response = array(
					'status' => true,
					'statusCode' => 'noStamp',
				);
				return Response::json($response);
			}
		}
		catch (\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function filter_stamp_detail(Request $request){
		$flo_detailsTable = DB::table('log_processes')
		->leftJoin('processes', 'processes.process_code', '=', 'log_processes.process_code')
		->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','processes.process_name', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('code')) > 0){
			$code = $request->get('code');
			$flo_detailsTable = $flo_detailsTable->where('log_processes.process_code','=', $code );
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->get();

		return DataTables::of($stamp_detail)
		->addColumn('action', function($stamp_detail){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

	public function fetchwipflallchart(Request $request){

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$target = DB::table('production_schedules')
		->leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
		->where('production_schedules.due_date', '=', $now)
		->where('materials.category', '=', 'FG')
		->where('materials.hpl', '=', 'FLFG');
		$stock = DB::table('stamp_inventories');

		$targetFL = $target->where('origin_group_code', '=', $request->get('originGroupCode'))->sum('production_schedules.quantity');
		$stockFL = $stock->where('origin_group_code', '=', $request->get('originGroupCode'))->whereNull('status')->sum('stamp_inventories.quantity');

		if($targetFL != 0){
			$dayFL = floor($stockFL/$targetFL);
			$addFL = ($stockFL/$targetFL)-$dayFL;
		}
		else{
			$dayFL = 2;
			$addFL = 1;
		}

		$last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

		$currStock = round($stockFL/$targetFL,1);

		if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+3)));
		}
		elseif(date('D')=='Sun'){
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
		}
		else{
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
		}

		$query = "select stamp_inventories.process_code, sum(stamp_inventories.quantity) as qty from stamp_inventories where stamp_inventories.status is null and stamp_inventories.origin_group_code = '041' group by stamp_inventories.process_code";

		$query2 = "select model, sum(plan) as plan, sum(stock) as stock, sum(max_plan) as max_plan from
		(
		select materials.model, sum(plan) as plan, 0 as stock, sum(max_plan) as max_plan from
		(
		select material_number, sum(quantity) as plan, 0 as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$hFL."' group by material_number

		union all

		select material_number, round(sum(quantity)*".$addFL.") as plan, 0 as max_plan from production_schedules where due_date = '".$aFL."' group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$last."' group by material_number

		union all

		select material_number, -(sum(quantity)) as plan, -(sum(quantity)) as max_plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$aFL."' group by material_number
		) result1
		left join materials on materials.material_number = result1.material_number
		group by materials.model

		union all

		select model, 0 as plan, sum(quantity) as stock, 0 as max_plan from stamp_inventories where status is null group by model
		) as result2
		group by model having model like 'YFL%' and plan > 0 or stock > 0 order by model asc";

		$stockData = DB::select($query);
		$efficiencyData = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'stockData' => $stockData,
			'currStock' => $currStock,
		);
		return Response::json($response);
	}

	//tambah ali stamp sax and cl

	public function indexProcessAssyFLCla1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::where('origin_group_code','=','042')->orderBy('created_at', 'desc')
		->get();
		return view('processes.assy_fl_cla.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFLSaxT1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
		->get();
		return view('processes.assy_fl_saxT.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}


	public function indexProcessStampSX(){
		return view('processes.assy_fl_saxT.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessStampCl(){
		return view('processes.assy_fl_cla.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexResumesCL(){


		return view('processes.assy_fl_cla.resumes')
		->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
	}



	public function fetchStampPlan($id){
		$id_all = $id."%";

		$now = date('Y-m-d');

		$query = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like '".$id_all."'";


		$query3 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like '".$id_all."'";



		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
		) as plan
		WHERE MODEL in ('INDONESIA','CHINA')
		group by model
		";




		if ($id =="YAS") {
			$materials = DB::table('materials')->where('model', 'like', 'YAS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'ASBODY')
			->where('category', '=', 'wip')->select('model')->distinct()->get();
		}

		if ($id =="YTS") {
			$materials = DB::table('materials')->where('model', 'like', 'YTS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'TSBODY')
			->where('category', '=', 'wip')->select('model')->distinct()->get();
		}

		if($id =="YCL"){
			$planData = DB::select($query2);
		}else{
			$planData = DB::select($query);
		}

		if ($id =="YFL") {
			$materials = DB::table('materials')->where('model', 'like', $id_all)->select('model')->distinct()->get();
		}

		$response = array(
			'status' => true,
			'planData' => $planData,
			'model' => $materials,

		);
		return Response::json($response);
	}


	public function fetchResult($id){
		$id_all = $id."%";
		$now = date('Y-m-d');
		if($id =="YCL"){
			$query="SELECT * FROM log_processes WHERE model IN ('INDONESIA','CHINA') ORDER BY created_at desc";
			$log_processes = db::select($query);

		}elseif($id =="YTS"){
			$query="SELECT * FROM (
			SELECT serial_number,model,created_at,id FROM log_processes WHERE model LIKE 'YTS%' and process_code ='1' 
			UNION ALL
			SELECT serial_number,model,created_at,id FROM log_processes WHERE model LIKE 'YAS%'  and process_code ='1'
		) A ORDER BY created_at DESC";
		$log_processes = db::select($query);
	}elseif($id =="YTS2"){
		$query="SELECT * FROM (
		SELECT serial_number,model,created_at,id FROM log_processes WHERE model LIKE 'YTS%' and process_code ='2' 
		UNION ALL
		SELECT serial_number,model,created_at,id FROM log_processes WHERE model LIKE 'YAS%'  and process_code ='2'
	) A ORDER BY created_at DESC";
	$log_processes = db::select($query);
}elseif($id =="YTS3"){
	$query="SELECT * FROM (
	SELECT * FROM stamp_inventories WHERE model LIKE 'YTS%' and process_code ='3' 
	UNION ALL
	SELECT * FROM stamp_inventories WHERE model LIKE 'YAS%'  and process_code ='3'
) A ORDER BY created_at DESC";
$log_processes = db::select($query);
}else{
	$log_processes = db::table('log_processes')
	->where('process_code', '=', '1')
	->where('model', 'like', $id_all)
	->where(db::raw('date(created_at)'), '=', $now)
	->orderBy('created_at', 'desc')
	->get();
}
$response = array(
	'status' => true,
	'resultData' => $log_processes,
);
return Response::json($response);
}




// print saxophone

public function getsnsax(Request $request)
{
	$sn = StampInventory::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->select('model', 'serial_number')
	->first();

	$sn2 = StampInventory::where('process_code', '=', '2')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->select('model', 'serial_number')
	->first();


	if ($sn != null) {
		$response = array(
			'status' => true,
			'message' => '1',
			'model' => $sn->model,
			'sn' => $sn->serial_number,
		);
		return Response::json($response);
	}elseif ($sn2 != null) {
		$response = array(
			'status' => true,
			'message' => '2',
			'model' => $sn2->model,
			'sn' => $sn2->serial_number,
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false,
			'message' => 'Serial Number not registered',
		);
		return Response::json($response);
	}
}


public function print_sax(Request $request){
	$stamp = LogProcess::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	$stamp2 = LogProcess::where('process_code', '=', '2')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();


	try{
		$id = Auth::id();
		if ($request->get('status') =="update") {
			if ($stamp != null) {
				$model = $stamp->model;
			}else{
				$model = $stamp2->model;
			}
			

			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => '2', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '2', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'model' => $model,
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('process_code', '=', $request->get('code'))
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			$inventory2 = StampInventory::where('process_code', '=', '2')
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			if ($inventory != null) {
				$inventory->status = null;
				$inventory->process_code = '2';
				$inventory->save();
			} elseif ($inventory2 != null) {
				$inventory2->status = null;
				$inventory2->process_code = '2';
				$inventory2->save();
			}

			

			
			$log_process->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('sn'), Printer::BARCODE_CODE39);
				// $printer->qrCode($request->get('sn'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('sn')."\n");
			$printer->feed(1);
			$printer->text($model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();

		}else{
			$invent = new StampInventory([
				'process_code' => '2', 
				'serial_number' => $request->get('sn'),
				'origin_group_code' => $request->get('origin'),
				'model' => $request->get('snmodel'),
				'quantity' => 1,
				'created_by' => $id,
				'created_at' => date('Y-m-d H:i:s')
			]);

			$log = new LogProcess([
				'process_code' => '2', 
				'serial_number' => $request->get('sn'),
				'origin_group_code' => $request->get('origin'),
				'model' => $request->get('snmodel'),
				'quantity' => 1,
				'created_by' => $id,
				'created_at' => date('Y-m-d H:i:s')
			]);
			$invent->save();
			$log->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('sn'), Printer::BARCODE_CODE39);
				// $printer->qrCode($request->get('sn'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('sn')."\n");
			$printer->feed(1);
			$printer->text($request->get('snmodel')."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();
		}

		$response = array(
			'status' => true,
			'message' => 'Print success',
		);
		return Response::json($response);
	}
	catch (QueryException $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

public function fetchStampPlansax2($id){

	$id_all = $id."%";

	$now = date('Y-m-d');	

	$query3 = "select model, sum(plan) as plan, sum(actual) as actual from
	(
	select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	union all

	select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
	) as plan
	group by model
	having model like '".$id_all."'";

	$planData = DB::select($query3);

	if ($id =="YAS") {
		$materials = DB::table('materials')->where('model', 'like', 'YAS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'ASBODY')
		->where('category', '=', 'wip')->select('model')->distinct()->get();
	}

	if ($id =="YTS") {
		$materials = DB::table('materials')->where('model', 'like', 'YTS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'TSBODY')
		->where('category', '=', 'wip')->select('model')->distinct()->get();
	}

	$response = array(
		'status' => true,
		'planData' => $planData,
		'model' => $materials,

	);
	return Response::json($response);
}

public function reprint_stamp2(Request $request)
{
	$model = db::table('stamp_inventories')	
	->where('serial_number', '=', $request->get('stamp_number_reprint'))
	->select ('model')
	->first();

	if ($request->get('stamp_number_reprint') != null){
		try {
			$code_generator = CodeGenerator::where('note', '=', '043')->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('stamp_number_reprint'), Printer::BARCODE_CODE39);
			// $printer->qrCode($request->get('serialNumber'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('stamp_number_reprint')."\n");
			$printer->feed(1);
			$printer->text($model->model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();

			return back()->with('status', 'Stamp has been reprinted.')->with('page', 'Assembly Process');
		}
		catch(\Exception $e){
			return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
		}
	}
	else{
		return back()->with('error', 'Serial number '. $request->get('stamp_number_reprint') . ' not found.');
	}
}


// end print saxophone

// print saxophone label
public function indexProcessAssyFLSaxT2(){
	$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
	->get();
	return view('processes.assy_fl_saxT.print',array(
		'model2' => $model2,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function indexProcessAssyFLSaxT3(){
	$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
	->get();
	return view('processes.assy_fl_saxT.print_label',array(
		'model2' => $model2,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function fetchStampPlansax3($id){
	
	$id_all = $id."%";

	$now = date('Y-m-d');	

	// $query3 = "select model, sum(plan) as plan, sum(actual) as actual from
	// (
	// select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	// union all

	// select model, 0 as plan, quantity as actual from log_processes where process_code = '3' and date(created_at) = '" . $now . "'
	// ) as plan
	// group by model
	// having model like '".$id_all."'";

	$query3 ="select model, COUNT(model) as actual from stamp_inventories where process_code='3' and origin_group_code='043' and model like '".$id_all."' and DATE_FORMAT(updated_at,'%Y-%m-%d') ='" . $now . "' GROUP BY model";

	$planData = DB::select($query3);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

public function getModel(Request $request)
{
	if ($request->get('log')==3) {
		$query ="select material_number,material_description,remark from materials
		LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		WHERE stamp_hierarchies.model in ( SELECT model from log_processes WHERE serial_number='".$request->get('sn')."' )
		";
	}else{
		$query ="select material_number,material_description,remark from materials
		LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		WHERE stamp_hierarchies.model in ( SELECT model from stamp_inventories WHERE serial_number='".$request->get('sn')."' )
		";	
	}

	$planData = DB::select($query);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

public function getsnsax2(Request $request)
{
	$sn = StampInventory::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->select('model', 'serial_number')
	->first();

	$sn2 = StampInventory::where('process_code', '=', '3')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->select('model', 'serial_number')
	->first();


	if ($sn != null) {
		$response = array(
			'status' => true,
			'message' => '1',
			'model' => $sn->model,
			'sn' => $sn->serial_number,
		);
		return Response::json($response);
	}
	elseif ($sn2 != null) {
		$response = array(
			'status' => true,
			'message' => '2',
			'model' => $sn2->model,
			'sn' => $sn2->serial_number,
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false,
			'message' => 'Serial Number not registered',
		);
		return Response::json($response);
	}
}

public function print_sax2(Request $request){
	$stamp = LogProcess::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	$stamp2 = LogProcess::where('process_code', '=', '3')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();


	try{
		$id = Auth::id();
		if ($request->get('status') =="update") {
			if ($stamp != null) {
				$model = $stamp->model;
			}else{
				$model = $stamp2->model;
			}
			
			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => '3', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '3', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'status' => $request->get('jpn'),
					'model' => $model,
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('process_code', '=', $request->get('code'))
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			$inventory2 = StampInventory::where('process_code', '=', '3')
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			if ($inventory != null) {
				$inventory->status = null;
				$inventory->process_code = '3';
				$inventory->model = $request->get('snmodel');
				$inventory->status =  $request->get('jpn');
				$inventory->save();
			} elseif ($inventory2 != null) {
				$inventory2->status = null;
				$inventory2->process_code = '3';
				$inventory2->model = $request->get('snmodel');
				$inventory->status =  $request->get('jpn');
				$inventory2->save();
			}
			
			$log_process->save();

		}

		$response = array(
			'status' => true,
			'message' => 'Print success',
		);
		return Response::json($response);
	}
	catch (QueryException $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}


public function label_besar($id,$gmc,$remark){

	$date = date('Y-m-d');
	if ($remark =="J") {
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="NJ"){
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="JR") {
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="NJR"){
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}elseif ($remark =="JRB") {
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="NJRB"){
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	$barcode = DB::select($query);

	$date = date('Y-m-d');
	$querydate = "SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."'";
	$date2 = DB::select($querydate);
	
	return view('processes.assy_fl_saxT.print_label_besar',array(
		'barcode' => $barcode,
		'date2' => $date2,

		'remark' => $remark,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_kecil($id,$remark){
	$sn = $id;
	$date = date('Y-m-d');
	// if ($remark =="RP") {
	// 	$query ="SELECT a.serial_number,b.date_code from stamp_inventories as a
	// 	INNER JOIN (
	// 	SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
	// 	on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
	// 	WHERE a.serial_number='".$id."'";
	// }else{
	// 	$query ="SELECT a.serial_number,b.date_code from stamp_inventories as a
	// 	INNER JOIN (
	// 	SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
	// 	on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
	// 	WHERE a.serial_number='".$id."'";
	// }

	$query = "SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."'";
	$barcode = DB::select($query);
	
	return view('processes.assy_fl_saxT.print_label_kecil',array(
		'barcode' => $barcode,
		'sn' => $sn
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_des($id){
	
	$query ="select model from stamp_inventories where process_code ='3' and serial_number='".$id."'";
	$barcode = DB::select($query);
	
	return view('processes.assy_fl_saxT.print_label_description',array(
		'barcode' => $barcode,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function editStampLabel(Request $request){
	$stamp = StampInventory::find($request->get('id'));

	$response = array(
		'status' => true,
		'stamp' => $stamp,
	);
	return Response::json($response);
}

public function updateStampLabel(Request $request){
	$stamp = StampInventory::where('serial_number',$request->get('id'))->get()->first();	

	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
	->where('stamp_inventories.model', '=', $stamp->model)
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update([
		'status' => $request->get('jpn'),
		'model' => $request->get('model')]);
	

	$response = array(
		'status' => true,
		'message' => 'Update Success',
	);
	return Response::json($response);
}

public function getModelReprintAll(Request $request)
{
	$query ="SELECT material_number,serial_number,status from materials 
	LEFT JOIN stamp_inventories 
	on materials.material_description = stamp_inventories.model
	where serial_number ='".$request->get('sn')."'";

	$reprint = DB::select($query);
	$response = array(
		'status' => true,
		'reprint' => $reprint,
	);
	return Response::json($response);
}

// print saxophone label

public function filter_stamp_detail_cl(Request $request){
	$flo_detailsTable = DB::table('log_processes')
	
	->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','log_processes.process_code', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

	if(strlen($request->get('datefrom')) > 0){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
	}


	if(strlen($request->get('dateto')) > 0){
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
	}

	$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->where('origin_group_code','=','042')->get();

	return DataTables::of($stamp_detail)
	->addColumn('action', function($stamp_detail){
		return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
	})
	->make(true);
}



public function indexResumesSX(){

	$code = Process::where('remark','=','043')->orderBy('process_code', 'asc')
	->get();


	return view('processes.assy_fl_saxT.resumes',array(
		'code' => $code,
	))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
}

public function indexResumes(){

	$code = Process::where('remark','=','YFL041')->orderBy('process_code', 'asc')
	->get();
	return view('processes.assy_fl.resumes',array(
		'code' => $code,
	))

	->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function filter_stamp_detail_sx(Request $request){
	$flo_detailsTable = DB::table('log_processes')
	
	->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','log_processes.process_code', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

	if(strlen($request->get('datefrom')) > 0){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
	}

	if(strlen($request->get('code')) > 0){
		$code = $request->get('code');
		$flo_detailsTable = $flo_detailsTable->where('log_processes.process_code','=', $code );
	}

	if(strlen($request->get('dateto')) > 0){
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
	}

	$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->where('origin_group_code','=','043')->get();

	return DataTables::of($stamp_detail)
	->addColumn('action', function($stamp_detail){
		return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
	})
	->make(true);
}

public function fetch_plan_labelsax(Request $request){
	
	$hpl = "where materials.category = 'FG' and materials.origin_group_code = '043'";		
	$first = date('Y-m-01');
	if(date('Y-m-d') != date('Y-m-01')){
		$last = date('Y-m-d', strtotime(Carbon::yesterday()));
	}
	else{
		$last = date('Y-m-d');
	}
	$now = date('Y-m-d');

	if($first != $now){
		$debt = "union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number";
	}
	else{
		$debt= "";
	}
	

	$query = " select a.model, a.debt,a.plan,COALESCE(b.act,0) as actual  from (
	select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
	(
	select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
	from production_schedules 
	where due_date = '". $now ."' 
	group by material_number

	union all

	select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
	from flo_details 
	where date(created_at) = '". $now ."'  
	group by material_number

	".$debt."

	) as result
	left join materials on materials.material_number = result.material_number
	". $hpl ."
	group by result.material_number, materials.material_description
	having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0 ) a
	
	LEFT JOIN (
	select model, count(MODEL)AS act from stamp_inventories where process_code='3' AND origin_group_code='043' and  date(updated_at) = '". $now ."' GROUP BY model) b
	on a.model = b.model";

	$tableData = DB::select($query);


	$response = array(
		'status' => true,
		'tableData' => $tableData,
		
	);
	return Response::json($response);
}
	//end tambah ali

}
