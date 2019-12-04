<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\TagMaterial;
use App\MiddleInventory;
use App\BarrelQueue;
use File;
use App\Barrel;
use App\BarrelQueueInactive;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;
use App\MiddleNgLog;
use App\MiddleLog;
use App\ErrorLog;
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use App\RfidBuffingInventory;
use Illuminate\Support\Facades\Mail;

class TrialController extends Controller
{

	public function uploadtrial(Request $request){

		try{
			db::table('tmp')->insert([
				'image' => $request->get('image')
			]);	
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' =>  $e->getMessage()
			);
			return Response::json($response);
		}

		$response = array(
			'status' => false,
			'message' => $request->get('image'),
		);
		return Response::json($response);
	}

	public function scan_serial_number(Request $request)
	{
		$material_volume = MaterialVolume::where('material_number', '=', $request->get('material_number'))->first();
		$material = Material::where('material_number', '=', $request->get('material_number'))->first();
		$actual = $material_volume->lot_completion;

		$id = Auth::id();

		if($request->get('serial_number')){
			$serial_number = $request->get('serial_number');
		}
		else{
			$prefix_now_pd = date("y").date("m").date("d");
			$code_generator_pd = CodeGenerator::where('note','=','pd')->first();
			if ($prefix_now_pd != $code_generator_pd->prefix){
				$code_generator_pd->prefix = $prefix_now_pd;
				$code_generator_pd->index = '0';
				$code_generator_pd->save();
			}
			$number_pd = sprintf("%'.0" . $code_generator_pd->length . "d\n", $code_generator_pd->index);
			$serial_number = $code_generator_pd->prefix . $number_pd+1;
		}

		$material_number = $request->get('material_number');
		$prefix_now = date("y").date("m");
		$code_generator = CodeGenerator::where('note','=','flo')->first();
		if ($prefix_now != $code_generator->prefix){
			$code_generator->prefix = $prefix_now;
			$code_generator->index = '0';
			$code_generator->save();
		}

		if($request->get('flo_number') == ""){
			if($request->get('type') == 'pd' || Auth::user()->role_code == "OP-Assy-FL"){
				$shipment_schedule = DB::table('shipment_schedules')
				->leftJoin('flos', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
				->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
				->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
				->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
				->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
				->where('shipment_schedules.material_number', '=' , $request->get('material_number'))
				->orderBy('shipment_schedules.st_date', 'asc')
				->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
				->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description')
				->having('flo_quantity', '>' , '0')
				->first();
			}
			else{
				if($request->get('ymj') == 'true'){
					$shipment_schedule = DB::table('shipment_schedules')
					->leftJoin('flos', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
					->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
					->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
					->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
					->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
					->where('shipment_schedules.material_number', '=' , $request->get('material_number'))
					->where('shipment_schedules.destination_code', '=', 'Y1000YJ')
					->orderBy('shipment_schedules.st_date', 'asc')
					->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
					->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description')
					->having('flo_quantity', '>' , '0')
					->first();
				}
				else{
					$shipment_schedule = DB::table('shipment_schedules')
					->leftJoin('flos', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
					->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
					->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
					->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
					->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
					->where('shipment_schedules.material_number', '=' , $request->get('material_number'))
					->where('shipment_schedules.destination_code', '<>', 'Y1000YJ')
					->orderBy('shipment_schedules.st_date', 'asc')
					->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'))
					->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description')
					->having('flo_quantity', '>' , '0')
					->first();
				}
			}
			try{
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$flo_number = $code_generator->prefix . $number;

				$flo_number_check = Flo::where('flo_number', '=', $flo_number)->first();

				if($flo_number_check != ""){
					$response = array(
						'status' => false,
						'message' => "Double FLO",
					);
					return Response::json($response);
				}

				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$flo_detail = new FloDetail([
					'serial_number' =>  $serial_number,
					'material_number' => $request->get('material_number'),
					'origin_group_code' => $material->origin_group_code,
					'flo_number' => $flo_number,
					'quantity' => $actual,
					'created_by' => $id
				]);
				// $flo_detail->save();

				$flo = new Flo([
					'flo_number' => $flo_number,
					'shipment_schedule_id' => $shipment_schedule->id,
					'material_number' => $request->get('material_number'),
					'quantity' => $shipment_schedule->flo_quantity,
					'actual' => $actual,
					'created_by' => $id
				]);
				// $flo->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				// $inventory->save();

				DB::transaction(function() use ($flo_detail, $inventory, $flo){
					$flo_detail->save();
					$flo->save();
					$inventory->save();
				});

				$flo_log = FloLog::updateOrCreate(
					['flo_number' => $flo_number, 'status_code' => '0'],
					['flo_number' => $flo_number, 'created_by' => $id, 'status_code' => '0', 'updated_at' => Carbon::now()]
				);

				if($request->get('type') == 'pd'){
					$code_generator_pd->index = $code_generator_pd->index+1;
					$code_generator_pd->save(); 
				}

				if(Auth::user()->role_code == "OP-Assy-FL"){
					$printer_name = 'FLO Printer 101';
				}
				elseif(Auth::user()->role_code == "OP-Assy-CL"){
					$printer_name = 'FLO Printer 102';
				}
				elseif(Auth::user()->role_code == "OP-Assy-SX"){
					$printer_name = 'FLO Printer 103';
				}
				elseif(Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn"){
					$printer_name = 'FLO Printer 104';
				}
				elseif(Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn-2"){
					$printer_name = 'FLO Printer 105';
				}
				elseif(Auth::user()->role_code == "OP-Assy-RC"){
					$printer_name = 'FLO Printer RC';
				}
				elseif(Auth::user()->role_code == "OP-Assy-VN"){
					$printer_name = 'FLO Printer VN';
				}
				elseif(Auth::user()->role_code == "OP-WH-Exim"){
					$printer_name = 'FLO Printer LOG';
				}
				elseif(Auth::user()->role_code == "MIS"){
					$printer_name = 'FLO Printer MIS';
				}				
				elseif(Auth::user()->role_code == "S"){
					$printer_name = 'SUPERMAN';
				}
				else{
					$response = array(
						'status' => false,
						'message' => "You don't have permission to print FLO"
					);
					return Response::json($response);
				}

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->feed(2);
				$printer->setUnderline(true);
				$printer->text('FLO:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->barcode(intVal($flo_number), Printer::BARCODE_CODE39);
				$printer->setTextSize(3, 1);
				$printer->text($flo_number."\n\n");
				$printer->initialize();

				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->setUnderline(true);
				$printer->text('Destination:');
				$printer->setUnderline(false);
				$printer->feed(1);

				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(6, 3);
				$printer->text(strtoupper($shipment_schedule->destination_shortname."\n\n"));
				$printer->initialize();

				$printer->setUnderline(true);
				$printer->text('Shipment Date:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(4, 2);
				$printer->text(date('d-M-Y', strtotime($shipment_schedule->st_date))."\n\n");
				$printer->initialize();

				$printer->setUnderline(true);
				$printer->text('By:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(4, 2);
				$printer->text(strtoupper($shipment_schedule->shipment_condition_name)."\n\n");

				$printer->initialize();
				$printer->setTextSize(2, 2);
				$printer->text("   ".strtoupper($shipment_schedule->material_number)."\n");
				$printer->text("   ".strtoupper($shipment_schedule->material_description)."\n");

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
				$printer->text("Max Qty:".$shipment_schedule->flo_quantity."\n");                    
				$printer->cut();
				$printer->close();			

				// $log_process = LogProcess::firstOrNew([
				// 	'process_code' => '5',
				// 	'origin_group_code' => $material->origin_group_code,
				// 	'serial_number' => $serial_number,
				// 	'model' => $material->model,
				// 	'manpower' => 2,
				// 	'quantity' => 1,
				// 	'created_by' => $id
				// ]);
				// $log_process->save();

				if($material->origin_group_code == '041'){
					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => '5', 
							'serial_number' => $serial_number,
							'origin_group_code' => $material->origin_group_code
						],
						[
							'model' => $material->model,
							'manpower' => 2,
							'quantity' => $actual,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
					->where('origin_group_code', '=', $material->origin_group_code)
					->first();
					if($inventory_stamp != null){
						$inventory_stamp->forceDelete();
					}
				}

				if($material->origin_group_code == '043'){
					$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
					->where('origin_group_code', '=', $material->origin_group_code)
					->first();
					if($inventory_stamp != null){
						$inventory_stamp->forceDelete();
					}
				}

				$response = array(
					'status' => true,
					'message' => 'New FLO has been printed',
					'flo_number' => $flo_number,
					'status_code' => 'new',
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$error_log = new ErrorLog([
					'error_message' => $e->getMessage(),
					'created_by' => $id
				]);
				$error_log->save();
				$response = array(
					'status' => false,
					'message' => "Couldn't print to this printer " . $e->getMessage() . "\n.",
				);
				return Response::json($response);
			}
		}
		else{
			try{

				$flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
				$flo->actual = $flo->actual+$actual;
				// $flo->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				// $inventory->save();

				$flo_detail = new FloDetail([
					'serial_number' =>  $serial_number,
					'material_number' => $request->get('material_number'),
					'origin_group_code' => $material->origin_group_code,
					'flo_number' => $request->get('flo_number'),
					'quantity' => $actual,
					'created_by' => $id
				]);
				// $flo_detail->save();


				// $log_process = LogProcess::firstOrNew([
				// 	'process_code' => '5',
				// 	'origin_group_code' => $material->origin_group_code,
				// 	'serial_number' => $serial_number,
				// 	'model' => $material->model,
				// 	'manpower' => 2,
				// 	'quantity' => 1,
				// 	'created_by' => $id
				// ]);
				// $log_process->save();
				
				DB::transaction(function() use ($flo_detail, $inventory, $flo){
					$flo_detail->save();
					$inventory->save();
					$flo->save();
				});

				if($material->origin_group_code == '041'){
					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => '5', 
							'serial_number' => $serial_number,
							'origin_group_code' => $material->origin_group_code
						],
						[
							'model' => $material->model,
							'manpower' => 2,
							'quantity' => $actual,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
					->where('origin_group_code', '=', $material->origin_group_code)
					->first();
					if($inventory_stamp != null){
						$inventory_stamp->forceDelete();
					}
				}

				if($material->origin_group_code == '043'){
					$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
					->where('origin_group_code', '=', $material->origin_group_code)
					->first();
					if($inventory_stamp != null){
						$inventory_stamp->forceDelete();
					}
				}

				if($request->get('type') == 'pd'){
					$code_generator_pd->index = $code_generator_pd->index+1;
					$code_generator_pd->save();
				}

				$response = array(
					'status' => true,
					'message' => 'FLO fulfillment success.',
					'status_code' => 'open',
				); 
				return Response::json($response);
			}
			catch (QueryException $e){
				$error_code = $e->errorInfo[1];
				if($error_code == 1062){
					$response = array(
						'status' => false,
						'message' => "Serial number already exist.",
					);
					return Response::json($response);
				}
				else{
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
		}
	}

	public function trialmail(){
		$mail_to = db::table('send_emails')
		->where('remark', '=', 'upload')
		->WhereNull('deleted_at')
		->orWhere('remark', '=', 'superman')
		->WhereNull('deleted_at')
		->select('email')
		->get();

		Mail::raw('Hi, welcome user!', function ($message) {
			$message->to(['asd@gmail.com', '123@gmail.com'])->subject('tess');
		});
	}

	public function tes(){

		$title = 'Saxophone Buffing Work Order';
		$title_jp = 'asdasd';
		$json = file_get_contents('https://spreadsheets.google.com/feeds/cells/1OwxR50gpboRGoSnBt3aMxeslbEqWye7JxgylmQFAF84/1/public/full?alt=json');
		$obj = json_decode($json, TRUE);
		

		return view('trial', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'dd' => $obj
		));
	}


	public function tes2()
	{
		$title = 'Pemahaman Informasi YMPI-YEMI Celebration 2019';
		$title_jp = '';
		$json = file_get_contents('https://spreadsheets.google.com/feeds/cells/1OwxR50gpboRGoSnBt3aMxeslbEqWye7JxgylmQFAF84/1/public/full?alt=json');
		$obj = json_decode($json, TRUE);

		return view('emergencies.other_response', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'dd' => $obj
		));
	}

	public function fetch_data(Request $request)
	{
		$emp_q = "select count(employees.employee_id) as total, department from employees join
		(SELECT employee_id, department
		FROM mutation_logs
		WHERE id IN (
		SELECT MAX(id)
		FROM mutation_logs
		GROUP BY employee_id
		)) mut on mut.employee_id = employees.employee_id
		where end_date is null
		group by department
		";

		$emp_bagian = db::select("SELECT employee_id, department FROM mutation_logs
			WHERE id IN (
			SELECT MAX(id)
			FROM mutation_logs
			where employee_id in (".$request->get('nik').")
			GROUP BY employee_id
		)");

		$emp = db::select($emp_q);

		$response = array(
			'status' => true,
			'emp_datas' => $emp,
			'emp_bagian' => $emp_bagian
		);
		return Response::json($response);
	}

}
