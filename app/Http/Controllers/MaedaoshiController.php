<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;
use App\FloDetail;
use App\Flo;
use App\MaterialVolume;
use App\Material;
use App\Inventory;
use App\FloLog;
use App\CodeGenerator;
use Carbon\Carbon;
use App\StampInventory;
use App\LogProcess;

class MaedaoshiController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function index_bi(){
		$flos = Flo::where('flos.status', '=', 'M')->get();
		return view('flos.maedaoshi_bi', array(
			'flos' => $flos,
		))
		->with('page', 'FLO Maedaoshi BI');
	}

	public function index_ei(){
		$flos = Flo::where('flos.status', '=', 'M')->get();
		return view('flos.maedaoshi_ei', array(
			'flos' => $flos,
		))
		->with('page', 'FLO Maedaoshi EI');
	}

	public function index_after_bi(){
		return view('flos.maedaoshi_after_bi')->with('page', 'FLO Maedaoshi BI');
	}

	public function index_after_ei(){
		return view('flos.maedaoshi_after_ei')->with('page', 'FLO Maedaoshi EI');
	}

	public function scan_after_maedaoshi_material(Request $request){
		if($request->get('ymj') == 'true'){
			$flo = DB::table('flos')
			->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
			->where('shipment_schedules.material_number', '=', $request->get('material_number'))
			->where('shipment_schedules.destination_code', '=', 'Y1000YJ')
			->where('flos.status', '=', '0')
			->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
			->first();
		}
		else{
			$flo = DB::table('flos')
			->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
			->where('shipment_schedules.material_number', '=', $request->get('material_number'))
			->where('flos.status', '=', '0')
			->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
			->first();
		}

		if($flo == null ){
			if($request->get('type') == 'pd' || Auth::user()->role_code == "OP-Assy-FL"){
				$shipment_schedule = DB::table('shipment_schedules')
				->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
				->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
				->where('shipment_schedules.material_number', '=', $request->get('material_number'))
				->orderBy('shipment_schedules.st_date', 'ASC')
				->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
				->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
				->having('flo_quantity' , '>', 0)
				->first();
			}
			else{
				if($request->get('ymj') == 'true'){
					$shipment_schedule = DB::table('shipment_schedules')
					->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
					->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
					->where('shipment_schedules.material_number', '=', $request->get('material_number'))
					->where('shipment_schedules.destination_code', '=', 'Y1000YJ')
					->orderBy('shipment_schedules.st_date', 'ASC')
					->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
					->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
					->having('flo_quantity' , '>', 0)
					->first();
				}
				else{
					$shipment_schedule = DB::table('shipment_schedules')
					->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
					->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
					->where('shipment_schedules.material_number', '=', $request->get('material_number'))
					->where('shipment_schedules.destination_code', '<>', 'Y1000YJ')
					->orderBy('shipment_schedules.st_date', 'ASC')
					->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
					->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
					->having('flo_quantity' , '>', 0)
					->first();
				}
			}

			if($shipment_schedule != null){
				$response = array(
					'status' => true,
					'message' => 'Shipment schedule available',
					'flo_number' => '',
					'status_code' => 'new',
				);
				return Response::json($response);
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'There is no shipment schedule for '. $request->get('material_number') . ' yet.',
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => true,
				'message' => 'Open FLO available',
				'flo_number' => $flo->flo_number,
				'status_code' => 'open'
			); 
			return Response::json($response);
		}
	}

	public function scan_maedaoshi_material(Request $request){
		
		$material = DB::table('materials')
		->where('materials.material_number', '=', $request->get('material'))
		->first();

		$first = date('Y-m-01');
		$last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

		$query = "select material_number, sum(plan) as plan, sum(actual) as actual from
		(
			select material_number, quantity as plan, 0 as actual from production_schedules where due_date >= '".$first."' and due_date <= '".$last."'

			union all

			select material_number, 0 as plan, quantity as actual from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$last."'
		) as result
		group by result.material_number
		having plan <= actual and material_number = '".$request->get('material')."'";

		$productionPlan = DB::select($query);

		if(!empty($productionPlan)){
			$response = array(
				'status' => false,
				'message' => 'There is no production schedule for material '. $request->get('material') .'.',
			);
			return Response::json($response);
		}

		if($material != null){

			$flo = DB::table('flos')
			->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
			->where('shipment_schedules.material_number', '=', $request->get('material'))
			->where('flos.status', '=', '0')
			->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
			->first();

			if($flo == null){

				$shipment_schedule = DB::table('shipment_schedules')
				->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
				->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
				->where('shipment_schedules.material_number', '=', $request->get('material'))
				->orderBy('shipment_schedules.st_date', 'ASC')
				->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
				->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
				->having('flo_quantity' , '>', 0)
				->first();

				if($shipment_schedule == null){

					$flo2 = DB::table('flos')
					->where('flos.flo_number', '=', 'Maedaoshi'.$request->get("material"))
					->where('flos.status', '=', 'M')
					->first();

					if($flo2 == null){
						$response = array(
							'status' => true,
							'message' => 'New maedaoshi will be created',
							'maedaoshi' => '',
						); 
						return Response::json($response);
					}
					else{
						$response = array(
							'status' => true,
							'message' => 'Open maedaoshi available',
							'maedaoshi' => $flo2->flo_number,
						); 
						return Response::json($response);
					}
				}
				else{
					$response = array(
						'status' => false,
						'message' => 'There is shipment schedule for material '. $request->get('material') .'.',
					);
					return Response::json($response);
				}
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'There is open FLO for material '. $request->get('material') .'.',
				);
				return Response::json($response);
			}
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Material '. $request->get('material') .' is invalid.',
			);
			return Response::json($response);
		}		
	}


	public function scan_after_maedaoshi_serial(Request $request){

		$material_volume = MaterialVolume::where('material_number', '=', $request->get('material_number'))->first();
		$material = Material::where('material_number', '=', $request->get('material_number'))->first();
		$actual = $material_volume->lot_completion;

		$maedaoshi = FloDetail::where('flo_details.material_number', '=', $request->get('material_number'))
		->where('flo_details.flo_number', 'like', 'Maedaoshi%');

		if($request->get('type') == 'pd'){
			$maedaoshi = $maedaoshi->first();
		}
		else{
			$maedaoshi = $maedaoshi->where('flo_details.serial_number', '=', $request->get('serial_number'))->first();
		}

		if($maedaoshi == null){
			$response = array(
				'status' => false,
				'message' => "Material ". $request->get('material_number') ." is not maedaoshi",
			);
			return Response::json($response);
		}
		else{

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
				$number_pd = sprintf("%'.0" . $code_generator_pd->length . "d", $code_generator_pd->index+1);
				$serial_number = $code_generator_pd->prefix . $number_pd;
			}

			$material_number = $request->get('material_number');

			if($request->get('flo_number') == ""){
				if($request->get('type') == 'pd'){
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
					$prefix_now = date("y").date("m");
					$code_generator = CodeGenerator::where('note','=','flo')->first();
					if ($prefix_now != $code_generator->prefix){
						$code_generator->prefix = $prefix_now;
						$code_generator->index = '0';
						$code_generator->save();
					}
					$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
					$flo_number = $code_generator->prefix . $number;

					$code_generator->index = $code_generator->index+1;
					$code_generator->save();

					$flo_maedaoshi = Flo::where('flo_number', '=', $maedaoshi->flo_number)
					->first();

					if(($flo_maedaoshi->actual-$actual) <= 0){
						$flo_maedaoshi->forceDelete();
					}
					else{
						$flo_maedaoshi->actual = ($flo_maedaoshi->actual-$actual);
						$flo_maedaoshi->save();
					}

					$maedaoshi->flo_number = $flo_number;
					$maedaoshi->save();

					$flo = new Flo([
						'flo_number' => $flo_number,
						'shipment_schedule_id' => $shipment_schedule->id,
						'material_number' => $material->material_number,
						'quantity' => $shipment_schedule->flo_quantity,
						'actual' => $actual,
						'created_by' => $id
					]);
					$flo->save();

					$flo_log = FloLog::updateOrCreate(
						['flo_number' => $flo_number, 'status_code' => '0'],
						['flo_number' => $flo_number, 'created_by' => $id, 'status_code' => '0', 'updated_at' => Carbon::now()]
					);

					if(Auth::user()->role_code == "OP-Assy-FL"){
						$printer_name = 'FLO Printer 101';
					}
					elseif(Auth::user()->role_code == "OP-Assy-CL"){
						$printer_name = 'FLO Printer 102';
					}
					elseif(Auth::user()->role_code == "OP-Assy-SX"){
						$printer_name = 'FLO Printer 103';
					}
					elseif(Auth::user()->role_code == "OP-Assy-PN"){
						$printer_name = 'FLO Printer 104';
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
					$printer->barcode($flo_number, Printer::BARCODE_CODE39);
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

					$response = array(
						'status' => true,
						'message' => 'New FLO has been printed',
						'flo_number' => $flo_number,
						'status_code' => 'new',
					); 
					return Response::json($response);

				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => "Couldn't print to this printer " . $e->getMessage() . "\n.",
					);
					return Response::json($response);
				}
			}
			else{
				try{
					$flo_maedaoshi = Flo::where('flo_number', '=', $maedaoshi->flo_number)
					->first();

					if(($flo_maedaoshi->actual-$actual) <= 0){
						$flo_maedaoshi->forceDelete();
					}
					else{
						$flo_maedaoshi->actual = ($flo_maedaoshi->actual-$actual);
						$flo_maedaoshi->save();
					}

					$maedaoshi->flo_number = $request->get('flo_number');
					$maedaoshi->save();

					$flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
					$flo->actual = $flo->actual+$actual;
					$flo->save();

					$response = array(
						'status' => true,
						'message' => 'FLO fulfillment from maedaoshi success.',
						'status_code' => 'open',
					); 
					return Response::json($response);
				}
				catch (QueryException $e){
					$error_code = $e->errorInfo[2];
					$response = array(
						'status' => false,
						'message' => $error_code,
					);
					return Response::json($response);
				}
			}
		}		
	}

	public function scan_maedaoshi_serial(Request $request){
		$material_volume = MaterialVolume::where('material_number', '=', $request->get('material'))->first();
		$material = Material::where('material_number', '=', $request->get('material'))->first();
		$actual = $material_volume->lot_completion;

		$id = Auth::id();

		if(Auth::user()->role_code == "OP-Assy-FL"){
			$printer_name = 'FLO Printer 101';
		}
		elseif(Auth::user()->role_code == "OP-Assy-CL"){
			$printer_name = 'FLO Printer 102';
		}
		elseif(Auth::user()->role_code == "OP-Assy-SX"){
			$printer_name = 'FLO Printer 103';
		}
		elseif(Auth::user()->role_code == "OP-Assy-PN"){
			$printer_name = 'FLO Printer 104';
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

		if($request->get('serial')){
			$serial_number = $request->get('serial');
		}
		else{
			$prefix_now_pd = date("y").date("m").date("d");
			$code_generator_pd = CodeGenerator::where('note','=','pd')->first();
			if ($prefix_now_pd != $code_generator_pd->prefix){
				$code_generator_pd->prefix = $prefix_now_pd;
				$code_generator_pd->index = '0';
				$code_generator_pd->save();
			}
			$number_pd = sprintf("%'.0" . $code_generator_pd->length . "d", $code_generator_pd->index+1);
			$serial_number = $code_generator_pd->prefix . $number_pd;
		}

		if($request->get('maedaoshi') != ""){
			try{
				$flo_detail = new FloDetail([
					'serial_number' =>  $serial_number,
					'material_number' => $request->get('material'),
					'origin_group_code' => $material->origin_group_code,
					'flo_number' => $request->get('maedaoshi'),
					'quantity' => $actual,
					'created_by' => $id
				]);
				$flo_detail->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				$inventory->save();

				$flo = Flo::where('flo_number', '=', $request->get('maedaoshi'))->first();
				$flo->actual = $flo->actual+$actual;
				$flo->save();

				if($request->get('type') == 'pd'){
					$code_generator_pd->index = $code_generator_pd->index+1;
					$code_generator_pd->save();
				}

				$log_process = LogProcess::firstOrNew([
					'process_code' => '5',
					'serial_number' => $serial_number,
					'model' => $material->model,
					'manpower' => 2,
					'quantity' => $actual,
					'created_by' => $id
				]);
				$log_process->save();

				$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
				->where('model', '=', $material->model)
				->first();
				if($inventory_stamp != null){
					$inventory_stamp->forceDelete();
				}

				$response = array(
					'status' => true,
					'message' => 'Maedaoshi fulfillment success.',
					'status_code' => 'open',
					'maedaoshi' => $request->get('maedaoshi'),
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
			}
		}
		else{
			try{
				$flo_detail = new FloDetail([
					'serial_number' =>  $serial_number,
					'material_number' => $request->get('material'),
					'origin_group_code' => $material->origin_group_code,
					'flo_number' => 'Maedaoshi'.$request->get('material'),
					'quantity' => $actual,
					'created_by' => $id
				]);
				$flo_detail->save();

				$flo = new Flo([
					'flo_number' => 'Maedaoshi'.$request->get('material'),
					'shipment_schedule_id' => 0,
					'material_number' => $request->get('material'),
					'quantity' => 0,
					'status' => 'M',
					'actual' => $actual,
					'created_by' => $id
				]);
				$flo->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				$inventory->save();

				$flo_log = FloLog::updateOrCreate(
					['flo_number' => 'Maedaoshi'.$request->get('material'), 'status_code' => 'M'],
					['flo_number' => 'Maedaoshi'.$request->get('material'), 'created_by' => $id, 'status_code' => 'M', 'updated_at' => Carbon::now()]
				);

				if($request->get('type') == 'pd'){
					$code_generator_pd->index = $code_generator_pd->index+1;
					$code_generator_pd->save(); 
				}

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->feed(2);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(5, 7);
				$printer->text('MAEDAOSHI'."\n\n");
				$printer->feed(2);

				$printer->setTextSize(4, 4);
				$printer->text('No Shipment'."\n");
				$printer->text('Schedule'."\n\n");
				$printer->feed(2);

				$printer->setTextSize(2, 2);
				$printer->text($material->material_number."\n");
				$printer->text($material->material_description."\n");

				$printer->feed(2);
				$printer->cut();
				$printer->close();

				$log_process = LogProcess::firstOrNew([
					'process_code' => '5',
					'serial_number' => $serial_number,
					'model' => $material->model,
					'manpower' => 2,
					'quantity' => $actual,
					'created_by' => $id
				]);
				$log_process->save();

				$inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
				->where('model', '=', $material->model)
				->first();
				if($inventory_stamp != null){
					$inventory_stamp->forceDelete();
				}

				$response = array(
					'status' => true,
					'message' => 'New maedaoshi has been printed.',
					'status_code' => 'new',
					'maedaoshi' => 'Maedaoshi'.$request->get('material'),
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$error_code = $e->errorInfo[1];
				if($error_code == 1062){
					$message = "Serial number already exist.";
				}
				else{
					$message = "Couldn't print to this printer " . $e->getMessage() . "\n.";
				}
				$response = array(
					'status' => false,
					'message' => $message,
				);
				return Response::json($response);
			}
		}
	}

	public function fetch_maedaoshi(Request $request){
		$flo_details = DB::table('flo_details')
		->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
		->leftJoin('materials', 'flo_details.material_number', '=', 'materials.material_number')
		->where('flo_details.flo_number', '=', $request->get('maedaoshi'))
		->where('flos.status', '=', 'M')
		->select('materials.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id', 'flo_details.quantity')
		->orderBy('flo_details.id', 'DESC')
		->get();

		return DataTables::of($flo_details)
		->addColumn('action', function($flo_details){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

	public function reprint_maedaoshi(Request $request){
		if(Auth::user()->role_code == "OP-Assy-FL"){
			$printer_name = 'FLO Printer 101';
		}
		elseif(Auth::user()->role_code == "OP-Assy-CL"){
			$printer_name = 'FLO Printer 102';
		}
		elseif(Auth::user()->role_code == "OP-Assy-SX"){
			$printer_name = 'FLO Printer 103';
		}
		elseif(Auth::user()->role_code == "OP-Assy-PN"){
			$printer_name = 'FLO Printer 104';
		}
		elseif(Auth::user()->role_code == "OP-Assy-RC"){
			$printer_name = 'FLO Printer RC';
		}
		elseif(Auth::user()->role_code == "OP-Assy-VN"){
			$printer_name = 'FLO Printer VN';
		}
		elseif(Auth::user()->role_code == "S"){
			$printer_name = 'SUPERMAN';
		}
		elseif(Auth::user()->role_code == "MIS"){
			$printer_name = 'FLO Printer MIS';
		}
		elseif(Auth::user()->role_code == "OP-WH-Exim"){
			$printer_name = 'FLO Printer LOG';
		}
		else{
			$response = array(
				'status' => false,
				'message' => "You don't have permission to print FLO"
			);
			return Response::json($response);
		}

		$flo = DB::table('flos')
		->leftJoin('flo_details', 'flo_details.flo_number', '=', 'flos.flo_number')
		->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
		->where('flos.flo_number', '=', $request->get('maedaoshiReprint'))
		->select('flo_details.material_number', 'materials.material_description')
		->distinct()
		->first();

		if($flo != null){
			try{
				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->feed(2);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(5, 7);
				$printer->text('MAEDAOSHI'."\n\n");
				$printer->feed(2);

				$printer->setTextSize(4, 4);
				$printer->text('No Shipment'."\n");
				$printer->text('Schedule'."\n\n");
				$printer->feed(2);

				$printer->setTextSize(2, 2);
				$printer->text($flo->material_number."\n");
				$printer->text($flo->material_description."\n");

				$printer->feed(2);
				$printer->cut();
				$printer->close();

				return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Band Instrument');
			}
			catch(\Exception $e){
				return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
			}
		}
		else{
			return back()->with('error', 'FLO number '. $request->get('flo_number') . 'not found.');
		}
	}

	function destroy_maedaoshi(Request $request){
		$flo_detail = FloDetail::find($request->get('id'));
		if($flo_detail->completion == null){
			$material_volume = DB::table('material_volumes')
			->where('material_volumes.material_number', '=', $flo_detail->material_number)
			->first();
			$flo = DB::table('flos')
			->where('flo_number', '=', $request->get('maedaoshi'))
			->first();
			$material = DB::table('materials')
			->where('materials.material_number', '=', $flo_detail->material_number)
			->first();

			if(($flo->actual-$material_volume->lot_completion) <= 0){
				$flo_delete = Flo::where('flo_number', '=', $request->get('maedaoshi'));
				$flo_delete->forceDelete();
			}
			else{
				$flo->actual = ($flo->actual-$material_volume->lot_completion);
				$flo->save();
			}

			$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
			$inventory->quantity = ($inventory->quantity-$material_volume->lot_completion);
			$inventory->save();

			$flo_detail->forceDelete();

			$response = array(
				'status' => true,
				'message' => "Data has been deleted.",
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => "Data cannot be deleted, because data has been uploaded to SAP.",
			);
			return Response::json($response);
		}
	}
}