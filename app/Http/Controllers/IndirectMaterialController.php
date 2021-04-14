<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Yajra\DataTables\Exception;
use Carbon\Carbon;
use App\Mail\SendEmail;
use App\CodeGenerator;
use App\EmployeeSync;
use App\IndirectMaterial;
use App\IndirectMaterialCostCenter;
use App\IndirectMaterialLog;
use App\IndirectMaterialOut;
use App\IndirectMaterialStock;
use App\IndirectMaterialSchedule;
use App\IndirectMaterialPick;
use App\IndirectMaterialPickingLog;
use App\MaterialPlantDataList;
use App\Inventory;
use App\ChemicalSolution;
use App\ChemicalSolutionComposer;
use App\ChemicalControlLog;
use App\ChemicalConvertion;
use DataTables;
use DateTime;
use Response;
use PDF;
use Excel;


class IndirectMaterialController extends Controller{

	public function __construct(){
		$this->middleware('auth');

		$this->subtitle = 'Chemical';
		$this->subtitle_jp = '??';
	}

	public function indexIndirectMaterialMonitoring(){
		$title = 'Chemical Monitoring';

		return view('indirect_material.monitoring', array(
			'title' => $title,
			'title_jp' => ''
		))->with('head', 'Indirect Material')->with('page', 'Monitoring');
	}

	public function indexLarutan(){
		$title = 'Larutan';

		return view('indirect_material.chemical.larutan', array(
			'title' => $title
		))->with('head', 'Chemical')->with('page', 'Larutan');
	}

	public function indexSolutionControl(){
		$title = 'Chemical Solution Control';
		$title_jp = '??';

		$username = Auth::user()->username;
		$solutions;

		if((!str_contains(strtoupper($username), 'PI')) || (Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'CHM')){
			$solutions = db::select("SELECT cs.id, cc.section, UPPER(cc.location) AS location, cs.solution_name FROM chemical_solutions cs
				LEFT JOIN indirect_material_cost_centers cc
				ON cc.id = cs.cost_center_id
				ORDER BY cc.section, cc.location, cs.solution_name ASC");
		}else{
			$emp = EmployeeSync::where('employee_id', strtoupper($username))->first();

			$solutions = db::select("SELECT cs.id, cc.section, UPPER(cc.location) AS location, cs.solution_name FROM chemical_solutions cs
				LEFT JOIN indirect_material_cost_centers cc
				ON cc.id = cs.cost_center_id
				WHERE cc.department = '".$emp->department."'
				ORDER BY cc.section, cc.location, cs.solution_name ASC");
		}

		$convertions = db::select("SELECT * FROM chemical_convertions ORDER BY material ASC");

		return view('indirect_material.chemical.solution_control', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'solutions' => $solutions,
			'convertions' => $convertions
		))->with('head', 'Chemical')->with('page', 'Chemical Solution Control');
	}

	public function indexPickingSchedule(){
		$title = 'Chemical Picking Schedule';
		$title_jp = '??';

		$larutans = db::select("SELECT chemical_solutions.id, chemical_solutions.solution_name, indirect_material_cost_centers.section, indirect_material_cost_centers.location from chemical_solutions
			LEFT JOIN indirect_material_cost_centers ON indirect_material_cost_centers.id = chemical_solutions.cost_center_id");

		$materials = ChemicalSolutionComposer::distinct()
		->select('material_number', 'material_description')
		->orderBy('material_description', 'ASC')
		->get();

		$new_materials = db::select("SELECT chemical_solutions.id, chemical_solutions.solution_name, indirect_material_cost_centers.section, indirect_material_cost_centers.location from chemical_solutions
			LEFT JOIN indirect_material_cost_centers ON indirect_material_cost_centers.id = chemical_solutions.cost_center_id");

		$sections = IndirectMaterialCostCenter::select('id', 'section', 'location')
		->orderBy('section', 'ASC')
		->orderBy('location', 'ASC')
		->get();

		$addition_materials = db::select("SELECT DISTINCT c.solution_id, c.solution_name, cc.section, cc.location FROM chemical_solution_composers c
			LEFT JOIN chemical_solutions l ON l.id = c.solution_id
			LEFT JOIN indirect_material_cost_centers cc ON l.cost_center_id = cc.id 
			WHERE c.addition = 1");

		return view('indirect_material.chemical.schedule', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'larutans' => $larutans,
			'materials' => $materials,
			'sections' => $sections,
			'addition_materials' => $addition_materials,
			'new_materials' => $new_materials	
		))->with('head', 'Chemical')->with('page', 'Chemical Picking Schedule');
	}

	public function indexIndirectMaterialLog(){
		$title = 'Indirect Material Logs';
		$title_jp = '??';

		$materials = IndirectMaterial::select('material_number', 'material_description')->get();

		return view('indirect_material.logs', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'subtitle' => $this->subtitle,
			'subtitle_jp' => $this->subtitle_jp,
			'materials' => $materials
		))->with('head', 'Indirect Material')->with('page', 'Request');
	}

	public function indexRequest(){
		$title = 'Indirect Material Request';
		$title_jp = '??';

		$locations = db::select("SELECT id, section, location FROM indirect_material_cost_centers
			WHERE id in (SELECT DISTINCT cost_center_id FROM chemical_solutions)
			ORDER BY section, location ASC");

		return view('indirect_material.chemical.request', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'subtitle' => $this->subtitle,
			'subtitle_jp' => $this->subtitle_jp,
			'locations' => $locations
		))->with('head', 'Chemical')->with('page', 'Request');
	}

	public function indexStock(){
		$title = 'Indirect Material Stock';
		$title_jp = '??';

		$materials = IndirectMaterial::select('material_number', 'material_description')->get();

		return view('indirect_material.stock', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'subtitle' => $this->subtitle,
			'subtitle_jp' => $this->subtitle_jp,
			'materials' => $materials
		))->with('head', 'Indirect Material')->with('page', 'Stock');
	}

	public function importStock(Request $request){
		if($request->hasFile('upload_file')) {
			try{				
				$file = $request->file('upload_file');
				$file_name = 'import_chemical_stock'.'('. date("ymd_h.i") .')'.'.'.$file->getClientOriginalExtension();
				$file->move(public_path('uploads/indirect_material_chm/'), $file_name);


				$excel = public_path('uploads/indirect_material_chm/') . $file_name;
				$rows = Excel::load($excel, function($reader) {
					$reader->noHeading();
					//Skip Header
					$reader->skipRows(1);
				})->get();
				$rows = $rows->toArray();


				$notChm = array();

				$in_date = $request->get('upload_date');

				for ($i=0; $i < count($rows); $i++) {
					$material_number = $rows[$i][0];
					$quantity = $rows[$i][1];

					$isChm = IndirectMaterial::where('material_number','=',$material_number)->first();

					if($isChm){
						$inventory = Inventory::where('plant','=','8190')
						->where('material_number','=',$material_number)
						->where('storage_location','=','MSTK')
						->first();

						if($inventory){
							$inventory->quantity = $inventory->quantity + $quantity;
							$inventory->updated_at = Carbon::now();
						}else{	
							$inventory = new Inventory([
								'plant' => '8190',
								'material_number' => $material_number,
								'storage_location' => 'MSTK',
								'quantity' => $quantity
							]);
						}
						$inventory->save();

						for ($j=0; $j < $quantity; $j++) {
							$prefix_now = 'INDM'.date("ymd");
							$code_generator = CodeGenerator::where('note','=','indirect-material')->first();
							if ($prefix_now != $code_generator->prefix){
								$code_generator->prefix = $prefix_now;
								$code_generator->index = '0';
								$code_generator->save();
							}

							$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
							$qr_code = $code_generator->prefix . $number;
							$code_generator->index = $code_generator->index+1;
							$code_generator->save();

							$log = new IndirectMaterialLog([
								'in_date' => $in_date,
								'qr_code' => $qr_code,
								'material_number' => $material_number,
								'remark' => 'in',
								'quantity' => 1,
								'created_by' => Auth::id()
							]);
							$log->save();

							$stock = new IndirectMaterialStock([
								'qr_code' => $qr_code,
								'material_number' => $material_number,
								'print_status' => 0,
								'created_by' => Auth::id()
							]);
							$stock->save();
						}
					}else{
						array_push($notChm, $material_number);
					}
				}

				$notInsert = MaterialPlantDataList::whereIn('material_number', $notChm)
				->select('material_number', 'material_description')
				->get();

				$response = array(
					'status' => true,
					'message' => 'Upload file success',
					'notInsert' => $notInsert
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

	public function deleteChmOut(Request $request){
		$qr = $request->get('qr');

		try {
			$out = IndirectMaterialOut::where('qr_code', $qr)->first();

			$log = new IndirectMaterialLog([
				'qr_code' => $out->qr_code,
				'material_number' => $out->material_number,
				'cost_center_id' => $out->cost_center_id,
				'in_date' => $out->in_date,
				'exp_date' => $out->exp_date,
				'remark' => 'empty',
				'quantity' => 1,
				'created_by' => Auth::id()
			]);
			$log->save();

			$out->delete();

			$response = array(
				'status' => true,
				'message' => 'Chemical dihapus'
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
	
	public function deleteChmPicked(Request $request){
		$id = $request->get('id');

		try {
			$pick = IndirectMaterialPick::where('id', $id)->first();

			if($pick->remark == 'new'){
				$new = new IndirectMaterialStock([
					'qr_code' => $pick->qr_code,
					'material_number' => $pick->material_number,
					'print_status' => 1,
					'in_date' => $pick->in_date,
					'exp_date' => $pick->exp_date,
					'created_by' => Auth::id()
				]);
				$new->save();

			}
			// elseif ($pick->remark == 'out') {
			// 	$out = new IndirectMaterialOut([
			// 		'qr_code' => $pick->qr_code,
			// 		'material_number' => $pick->material_number,
			// 		'cost_center_id' => $pick->cost_center_id,
			// 		'created_by' => Auth::id()
			// 	]);
			// 	$out->save();
			// }

			$pick->delete();


			$response = array(
				'status' => true,
				'message' => 'Item terpilih telah dihapus'
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

	public function updateLarutan(Request $request){
		$id = $request->get('id');
		$category = $request->get('category');
		$target_warning = $request->get('target_warning');
		$target_max = $request->get('target_max');

		try {

			if($category == 'CONTROLLING CHART'){
				$larutan = ChemicalSolution::where('id', $id)
				->update([
					'category' => $category,
					'target_warning' => $target_warning,
					'target_max' => $target_max,
					'created_by' => Auth::id()
				]);
			}else{
				$larutan = ChemicalSolution::where('id', $id)
				->update([
					'category' => $category,
					'target_warning' => null,
					'target_max' => null,
					'created_by' => Auth::id()
				]);
			}		


			$response = array(
				'status' => true,
				'message' => 'Data sukses diperbarui'
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

	public function inputChmPicked(Request $request){
		$location = $request->get('location');
		$schedule_id = $request->get('schedule_id');

		$schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();

		try {
			$pick = IndirectMaterialPick::where('cost_center_id', $location)
			->where('schedule_id', $schedule_id)
			->get();

			for ($i=0; $i < count($pick); $i++) { 
				$log = new IndirectMaterialPickingLog([
					'schedule_id' => $schedule_id,
					'qr_code' => $pick[$i]->qr_code,
					'material_number' => $pick[$i]->material_number,
					'cost_center_id' => $pick[$i]->cost_center_id,
					'remark' => $pick[$i]->remark,
					'quantity' => $schedule->quantity,
					'bun' => $schedule->bun,
					'in_date' => $pick[$i]->in_date,
					'exp_date' => $pick[$i]->exp_date,
					'created_by' => Auth::id()
				]);
				$log->save();

				if($pick[$i]->remark == 'new'){
					$out = new IndirectMaterialOut([
						'qr_code' => $pick[$i]->qr_code,
						'material_number' => $pick[$i]->material_number,
						'cost_center_id' => $pick[$i]->cost_center_id,
						'in_date' => $pick[$i]->in_date,
						'exp_date' => $pick[$i]->exp_date,
						'created_by' => Auth::id()
					]);
					$out->save();

					$idm_log = new IndirectMaterialLog([
						'qr_code' => $pick[$i]->qr_code,
						'material_number' => $pick[$i]->material_number,
						'cost_center_id' => $pick[$i]->cost_center_id,
						'remark' => 'out',
						'in_date' => $pick[$i]->in_date,
						'exp_date' => $pick[$i]->exp_date,
						'quantity' => 1,
						'created_by' => Auth::id()
					]);
					$idm_log->save();

					$inventory = Inventory::where('storage_location', 'MSTK')
					->where('material_number', $pick[$i]->material_number)
					->first();
					$inventory->quantity = $inventory->quantity - 1;
					$inventory->save();
				}				
			}

			$schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();
			$schedule->picked_by = Auth::id();
			$schedule->picked_time = date('Y-m-d H:i:s');
			$schedule->save();


			// $chemical_solution = ChemicalSolution::where('id', $schedule->solution_id)
			// ->update([
			// 	'is_add_schedule' => 1,
			// 	'actual_quantity' => 0
			// ]);


			$pick = IndirectMaterialPick::where('cost_center_id', $location)
			->where('schedule_id', $schedule_id)
			->delete();

			$response = array(
				'status' => true,
				'message' => 'Pengambilan chemical sukses'
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

	public function inputChmNew(Request $request){
		$date = $request->get('date');
		$shift = $request->get('shift');
		$solution_id = $request->get('solution_id');
		$note = $request->get('note');

		$shift1 = '07:00:01';
		$shift2 = '16:00:01';
		$shift3 = '00:00:01';

		$schedule_date;
		if ($shift == 1){
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift1));
		}elseif ($shift == 2) {
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift2));
		}elseif ($shift == 3) {
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift3));
		}

		$chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
		->where('solution_id', $solution_id)
		->select(
			'chemical_solution_composers.solution_name',
			'chemical_solution_composers.solution_id',
			'chemical_solution_composers.material_number',
			'chemical_solution_composers.storage_location',
			'chemical_solution_composers.quantity',
			'chemical_solution_composers.bun',
			'chemical_solutions.cost_center_id'
		)
		->get();

		try {
			for ($i=0; $i < count($chm_composer); $i++) {

				$schedule = new IndirectMaterialSchedule([
					'schedule_date' => $schedule_date,
					'schedule_shift' => $shift,
					'category' => 'Pembuatan Baru',
					'solution_id' => $solution_id,
					'material_number' => $chm_composer[$i]->material_number,
					'cost_center_id' => $chm_composer[$i]->cost_center_id,
					'storage_location' => $chm_composer[$i]->storage_location,
					'quantity' => $chm_composer[$i]->quantity,
					'bun' => $chm_composer[$i]->bun,
					'note' => $note,
					'created_by' => Auth::id()
				]);
				$schedule->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Input schedule success',
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

	public function inputChmAddition(Request $request){
		$date = $request->get('date');
		$shift = $request->get('shift');
		$solution_id = $request->get('solution_id');
		$composer = $request->get('composer');

		$shift1 = '07:00:01';
		$shift2 = '16:00:01';
		$shift3 = '00:00:01';

		$schedule_date;
		if ($shift == 1){
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift1));
		}elseif ($shift == 2) {
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift2));
		}elseif ($shift == 3) {
			$schedule_date = date('Y-m-d H:i:s', strtotime($date.' '.$shift3));
		}

		try {
			for ($i=0; $i < count($composer); $i++) {
				$chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
				->where('solution_id', $solution_id)
				->where('material_number', $composer[$i][0])
				->select(
					'chemical_solution_composers.solution_name',
					'chemical_solution_composers.solution_id',
					'chemical_solution_composers.material_number',
					'chemical_solution_composers.storage_location',
					'chemical_solution_composers.bun',
					'chemical_solutions.cost_center_id'
				)
				->first();

				$schedule = new IndirectMaterialSchedule([
					'schedule_date' => $schedule_date,
					'schedule_shift' => $shift,
					'category' => 'Penambahan Chemical',
					'solution_id' => $solution_id,
					'material_number' => $composer[$i][0],
					'cost_center_id' => $chm_composer->cost_center_id,
					'storage_location' => $chm_composer->storage_location,
					'quantity' => $composer[$i][1],
					'bun' => $chm_composer->bun,
					'created_by' => Auth::id()
				]);
				$schedule->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Input schedule success',
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

	public function inputStock(Request $request){
		$in_date = $request->get('in_date');
		$exp_date = $request->get('exp_date');
		$material_number = $request->get('material_number');
		$quantity = $request->get('quantity');

		if($in_date >= $exp_date){
			$response = array(
				'status' => false,
				'message' => 'In Date lebih lama dari Exp Date',
			);
			return Response::json($response);
		}

		try{
			$inventory = Inventory::where('plant','=','8190')
			->where('material_number','=',$material_number)
			->where('storage_location','=','MSTK')
			->first();

			if($inventory){
				$inventory->quantity = $inventory->quantity + $quantity;
				$inventory->updated_at = Carbon::now();
			}else{	
				$inventory = new Inventory([
					'plant' => '8190',
					'material_number' => $material_number,
					'storage_location' => 'MSTK',
					'quantity' => $quantity
				]);
			}
			$inventory->save();

			for ($i=0; $i < $quantity; $i++) {
				$prefix_now = 'INDM'.date("ymd");
				$code_generator = CodeGenerator::where('note','=','indirect-material')->first();
				if ($prefix_now != $code_generator->prefix){
					$code_generator->prefix = $prefix_now;
					$code_generator->index = '0';
					$code_generator->save();
				}

				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$qr_code = $code_generator->prefix . $number;
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$log = new IndirectMaterialLog([
					'in_date' => $in_date,
					'exp_date' => $exp_date,
					'qr_code' => $qr_code,
					'material_number' => $material_number,
					'remark' => 'in',
					'quantity' => 1,
					'created_by' => Auth::id()
				]);
				$log->save();

				$stock = new IndirectMaterialStock([
					'in_date' => $in_date,
					'exp_date' => $exp_date,
					'qr_code' => $qr_code,
					'material_number' => $material_number,
					'print_status' => 0,
					'created_by' => Auth::id()
				]);
				$stock->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Input file success',
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

	public function inputResult($id, $material, $quantity, $convertion, $date){

		$solution = ChemicalSolution::where('id', $id)->first();

		try {		

			if($solution->category == 'CONTROLLING CHART'){
				if($solution->target_uom == 'DM2'){
					$solution->actual_quantity = $solution->actual_quantity + ($quantity * $convertion);
					$solution->updated_at = Carbon::now();
					$solution->save();				

					$log = new ChemicalControlLog([
						'date' => $date,
						'solution_name' => $solution->solution_name,
						'cost_center_id' => $solution->cost_center_id,
						'target_max' => $solution->target_max,
						'target_warning' => $solution->target_warning,
						'note' => $material,
						'quantity' => $quantity * $convertion,
						'accumulative' => $solution->actual_quantity,
						'created_by' => Auth::id()
					]);
					$log->save();
				}else{
					$solution->actual_quantity = $solution->actual_quantity + $quantity;
					$solution->updated_at = Carbon::now();
					$solution->save();

					$log = new ChemicalControlLog([
						'date' => $date,
						'solution_name' => $solution->solution_name,
						'cost_center_id' => $solution->cost_center_id,
						'target_max' => $solution->target_max,
						'target_warning' => $solution->target_warning,
						'note' => $material,
						'quantity' => $quantity,
						'accumulative' => $solution->actual_quantity,
						'created_by' => Auth::id()
					]);
					$log->save();
				}

				if($solution->actual_quantity > $solution->target_warning){
					if($solution->is_add_schedule == 1){
						$this->inputChmControl($solution->id);

						$solution->is_add_schedule = 0;
						$solution->save();
					}
				}
			}		
			
			
		} catch (Exception $e) {
			$error_log = new ErrorLog([
				'error_message' => $e->getMessage(),
				'created_by' => $id
			]);
			$error_log->save();
		}

		
	}

	public function inputStrikeNickel($material, $quantity, $convertion, $date){

		//STRIKE NICKEL
		// $solution = ChemicalSolution::where('id', 36)->first();
		$this->inputResult(36, $material, $quantity, $convertion, $date);

		//ULTRASONIC CLEANER
		// $solution = ChemicalSolution::where('id', 30)->first();
		$this->inputResult(30, $material, $quantity, $convertion, $date);
		
		//ALKALI DEGREASING
		// $solution = ChemicalSolution::where('id', 31)->first();
		$this->inputResult(31, $material, $quantity, $convertion, $date);
		
		//ELECTRO ALKALI DEGREASING
		// $solution = ChemicalSolution::where('id', 32)->first();
		$this->inputResult(32, $material, $quantity, $convertion, $date);
		
		//ACID ACTIVATION
		// $solution = ChemicalSolution::where('id', 33)->first();
		$this->inputResult(33, $material, $quantity, $convertion, $date);
	}

	public function inputStrikeSilver($material, $quantity, $convertion, $date){

		//STRIKE SILVER
		// $solution = ChemicalSolution::where('id', 39)->first();
		$this->inputResult(39, $material, $quantity, $convertion, $date);

		//ULTRASONIC CLEANER
		// $solution = ChemicalSolution::where('id', 30)->first();
		$this->inputResult(30, $material, $quantity, $convertion, $date);
		
		//ALKALI DEGREASING
		// $solution = ChemicalSolution::where('id', 31)->first();
		$this->inputResult(31, $material, $quantity, $convertion, $date);
		
		//ELECTRO ALKALI DEGREASING
		// $solution = ChemicalSolution::where('id', 32)->first();
		$this->inputResult(32, $material, $quantity, $convertion, $date);
		
		//ACID ACTIVATION
		// $solution = ChemicalSolution::where('id', 33)->first();
		$this->inputResult(33, $material, $quantity, $convertion, $date);

		//ALKALI DIPPING
		// $solution = ChemicalSolution::where('id', 34)->first();
		$this->inputResult(34, $material, $quantity, $convertion, $date);
		
		//NETRALISASI
		// $solution = ChemicalSolution::where('id', 35)->first();
		$this->inputResult(35, $material, $quantity, $convertion, $date);

	}

	public function inputProductionResult(Request $request){
		$date = $request->get('date');
		$larutan = $request->get('larutan');
		$material = $request->get('materials');

		try{
			for ($i=0; $i < count($material); $i++) {
				$convertion = ChemicalConvertion::where('id', $material[$i][0])->first();

				$solution = ChemicalSolution::where('id', $larutan)->first();

				if (strpos($solution->solution_name, 'GLOSSY NI') !== false) {
					$this->inputStrikeNickel($material[$i][1], $material[$i][2], $convertion->dm2, $date);
				}else if (strpos($solution->solution_name, 'GLOSSY SILVER') !== false){
					$this->inputStrikeSilver($material[$i][1], $material[$i][2], $convertion->dm2, $date);
				}

				if($solution->category == 'CONTROLLING CHART'){
					if($solution->target_uom == 'DM2'){
						$solution->actual_quantity = $solution->actual_quantity + ($material[$i][2] * $convertion->dm2);
						$solution->updated_at = Carbon::now();
						$solution->save();

						$log = new ChemicalControlLog([
							'date' => $date,
							'solution_name' => $solution->solution_name,
							'cost_center_id' => $solution->cost_center_id,
							'target_max' => $solution->target_max,
							'target_warning' => $solution->target_warning,
							'note' => $material[$i][1],
							'quantity' => $material[$i][2] * $convertion->dm2,
							'accumulative' => $solution->actual_quantity,
							'created_by' => Auth::id()
						]);
						$log->save();
					}else{
						$solution->actual_quantity = $solution->actual_quantity + $material[$i][2];
						$solution->updated_at = Carbon::now();
						$solution->save();

						$log = new ChemicalControlLog([
							'date' => $date,
							'solution_name' => $solution->solution_name,
							'cost_center_id' => $solution->cost_center_id,
							'target_max' => $solution->target_max,
							'target_warning' => $solution->target_warning,
							'note' => $material[$i][1],
							'quantity' => $material[$i][2],
							'accumulative' => $solution->actual_quantity,
							'created_by' => Auth::id()
						]);
						$log->save();
					}

					if($solution->actual_quantity > $solution->target_warning){
						if($solution->is_add_schedule == 1){
							$this->inputChmControl($solution->id);

							$solution->is_add_schedule = 0;
							$solution->save();
						}
					}
				}else{
					$log = new ChemicalControlLog([
						'date' => $date,
						'solution_name' => $solution->solution_name,
						'cost_center_id' => $solution->cost_center_id,
						'target_max' => $solution->target_max,
						'target_warning' => $solution->target_warning,
						'note' => $material[$i][1],
						'quantity' => $material[$i][2],
						'accumulative' => $solution->actual_quantity,
						'created_by' => Auth::id()
					]);
					$log->save();
				}
			}		

			$response = array(
				'status' => true,
				'message' => 'Input Production Result Success',
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

	public function inputChmControl($solution_id){
		$date = date('Y-m-d', strtotime("+1 day"));
		$schedule_date = date('Y-m-d H:i:s', strtotime($date.' 07:00:01'));
		$shift = 1;

		$chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
		->where('solution_id', $solution_id)
		->select(
			'chemical_solution_composers.solution_name',
			'chemical_solution_composers.solution_id',
			'chemical_solution_composers.material_number',
			'chemical_solution_composers.storage_location',
			'chemical_solution_composers.quantity',
			'chemical_solution_composers.bun',
			'chemical_solutions.cost_center_id'
		)
		->get();

		try {
			for ($i=0; $i < count($chm_composer); $i++) {

				$schedule = new IndirectMaterialSchedule([
					'schedule_date' => $schedule_date,
					'schedule_shift' => $shift,
					'category' => 'Pembuatan Baru',
					'solution_id' => $solution_id,
					'material_number' => $chm_composer[$i]->material_number,
					'cost_center_id' => $chm_composer[$i]->cost_center_id,
					'storage_location' => $chm_composer[$i]->storage_location,
					'quantity' => $chm_composer[$i]->quantity,
					'bun' => $chm_composer[$i]->bun,
					'created_by' => 1
				]);
				$schedule->save();
			}

		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteSchedule(Request $request) {
		$id = $request->get('id');

		try {
			$schedule = IndirectMaterialSchedule::where('id', $id)->first();

			$larutan = ChemicalSolution::where('id', $schedule->solution_id)
			->update([
				'is_add_schedule' => 1
			]);

			$delete = IndirectMaterialSchedule::where('schedule_date', $schedule->schedule_date)
			->where('solution_id', $schedule->solution_id)
			->where('cost_center_id', $schedule->cost_center_id)
			->delete();


			$response = array(
				'status' => true,
				'message' => 'Delete schedule success'
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

	public function changeSchedule(Request $request) {
		$id = $request->get('id');

		try {
			$schedule = IndirectMaterialSchedule::where('id', $id)->first();

			$larutan = ChemicalSolution::where('id', $schedule->solution_id)->first();
			$larutan->is_add_schedule = 1;
			$larutan->save();

			if($schedule->category == 'Pembuatan Baru'){
				$larutan = ChemicalSolution::where('id', $schedule->solution_id)
				->update([
					'actual_quantity' => 0 
				]);

				$log = new ChemicalControlLog([
					'date' => date('Y-m-d'),
					'solution_name' => $solution->solution_name,
					'cost_center_id' => $solution->cost_center_id,
					'target_max' => $solution->target_max,
					'target_warning' => $solution->target_warning,
					'note' => '-',
					'quantity' => 0,
					'accumulative' => 0,
					'created_by' => Auth::id()
				]);
				$log->save();
			}

			$change = IndirectMaterialSchedule::where('schedule_date', $schedule->schedule_date)
			->where('solution_id', $schedule->solution_id)
			->where('cost_center_id', $schedule->cost_center_id)
			->update([
				'changed_by' => Auth::id(),
				'changed_time' => date('Y-m-d H:i:s')
			]);


			$response = array(
				'status' => true,
				'message' => 'Penggantian larutan success'
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

	public function fetchIndirectMaterialMonitoring(){
		$data = db::select("SELECT sum,
			SUM(exp) AS exp,
			SUM(one_month) AS one_month,
			SUM(three_month) AS three_month,
			SUM(six_month) AS six_month,
			SUM(nine_month) AS nine_month,
			SUM(twelve_month) AS twelve_month,
			SUM(more_year) AS more_year
			FROM
			(SELECT 'SUM' AS sum,
			IF(DATEDIFF(exp_date, NOW()) < 0, 1, 0) AS exp,
			IF(DATEDIFF(exp_date, NOW()) >= 0 AND DATEDIFF(exp_date, NOW()) <= 30, 1, 0) AS one_month,
			IF(DATEDIFF(exp_date, NOW()) > 30 AND DATEDIFF(exp_date, NOW()) <= 90, 1, 0) AS three_month,
			IF(DATEDIFF(exp_date, NOW()) > 90 AND DATEDIFF(exp_date, NOW()) <= 180, 1, 0) AS six_month,
			IF(DATEDIFF(exp_date, NOW()) > 180 AND DATEDIFF(exp_date, NOW()) <= 270, 1, 0) AS nine_month,
			IF(DATEDIFF(exp_date, NOW()) > 270 AND DATEDIFF(exp_date, NOW()) <= 365, 1, 0) AS twelve_month,
			IF(DATEDIFF(exp_date, NOW()) > 365, 1, 0) AS more_year,
			DATEDIFF(exp_date, NOW()) AS diff FROM indirect_material_stocks) AS resume
			GROUP BY sum");

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
	}

	public function fetchIndirectMaterialMonitoringDetail(Request $request){
		$category = $request->get('category');
		$condition = '';

		if($category == 'Expired'){
			$condition = 'DATEDIFF(s.exp_date, NOW()) < 0';
		}elseif ($category == '< 30 Days') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) >= 0 AND DATEDIFF(s.exp_date, NOW()) <= 30';
		}elseif ($category == '< 90 Days') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) > 30 AND DATEDIFF(s.exp_date, NOW()) <= 90';
		}elseif ($category == '< 180 Days') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) > 90 AND DATEDIFF(s.exp_date, NOW()) <= 180';
		}elseif ($category == '< 270 Days') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) > 180 AND DATEDIFF(s.exp_date, NOW()) <= 270';
		}elseif ($category == '< 1 Year') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) > 270 AND DATEDIFF(s.exp_date, NOW()) <= 356';
		}elseif ($category == '> 1 Year') {
			$condition = 'DATEDIFF(s.exp_date, NOW()) > 365';
		}else{
			$response = array(
				'status' => false
			);
			return Response::json($response);
		}		

		$data = db::select("SELECT s.material_number, chm.material_description, chm.storage_location, s.in_date, s.exp_date, COUNT(s.material_number) AS qty FROM indirect_material_stocks s
			LEFT JOIN (SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm
			ON chm.material_number = s.material_number
			WHERE ".$condition."
			GROUP BY s.material_number, chm.material_description, chm.storage_location, s.in_date, s.exp_date
			ORDER BY s.exp_date ASC");

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);

	}

	public function fetchcheckResult(Request $request){
		$id = $request->get('id');
		$data = ChemicalSolution::where('chemical_solutions.id', $id)->first();

		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
	}

	public function fetchLarutanDetail(Request $request){
		$id = $request->get('id');

		$data = ChemicalSolution::leftJoin('indirect_material_cost_centers', 'indirect_material_cost_centers.id', '=', 'chemical_solutions.cost_center_id')
		->where('chemical_solutions.id', $id)
		->select(
			'chemical_solutions.id',
			'chemical_solutions.solution_name',
			db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
			'chemical_solutions.category',
			'chemical_solutions.target_warning',
			'chemical_solutions.target_max',
			'chemical_solutions.actual_quantity'
		)
		->first();


		$response = array(
			'status' => true,
			'data' => $data
		);
		return Response::json($response);
	}

	public function fetchLarutan(){
		$data = ChemicalSolution::leftJoin('indirect_material_cost_centers', 'indirect_material_cost_centers.id', '=', 'chemical_solutions.cost_center_id')
		->select(
			'chemical_solutions.id',
			'chemical_solutions.solution_name',
			db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
			'chemical_solutions.category',
			'chemical_solutions.target_warning',
			'chemical_solutions.target_max',
			'chemical_solutions.actual_quantity'
		)
		->get();

		return DataTables::of($data)
		->addColumn('edit', function($data){
			if(Auth::user()->role_code == 'CHM' ||Auth::user()->role_code == 'MIS'){
				return '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onClick="editSolution(id)" id="' . $data->id . '"><i class="fa fa-pencil"></i></a>';
			}else{
				return '-';
			}
		})
		->rawColumns([
			'edit' => 'edit'
		])
		->make(true);
	}

	public function fetchCheckOut(Request $request){
		$qr = $request->get('qr');
		$location = $request->get('location');

		$out = IndirectMaterialOut::leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'indirect_material_outs.material_number')
		->where('qr_code', $qr)
		->where('cost_center_id', $location)
		->select(
			'indirect_material_outs.qr_code',
			'indirect_material_outs.material_number',
			'chm.material_description'
		)
		->first();

		if($out){
			$response = array(
				'status' => true,
				'data' => $out,
				'message' => 'QR code ditemukan'
			);
			return Response::json($response);

		}else{
			$response = array(
				'status' => false,
				'message' => 'QR code tidak ditemukan'
			);
			return Response::json($response);

		}

	}

	public function fetchCheckQr(Request $request){
		$now = date('Y-m-d');

		$qr = $request->get('qr');
		$material_number = $request->get('material_number');
		$location = $request->get('location');
		$schedule_id = $request->get('schedule_id');

		$out = IndirectMaterialOut::where('material_number', $material_number)
		->where('cost_center_id', $location)
		->first();

		if($out){
			// CEK OUT
			if($out->qr_code != $qr){
				$response = array(
					'status' => false,
					'message' => 'Scan chemical status out dahulu'
				);
				return Response::json($response);
			}

			// CEK EXPIRED
			if($now >= $stock->exp_date){
				$response = array(
					'status' => false,
					'message' => 'Chemical Expired'
				);
				return Response::json($response);
			}

			$pick_out = IndirectMaterialOut::where('qr_code', $qr)
			->where('cost_center_id', $location)
			->first();

			// CEK QR CODE SAMA
			$picked = IndirectMaterialPick::where('qr_code', $qr)->first();
			if($picked){
				$response = array(
					'status' => false,
					'message' => 'Chemical telah di scan'
				);
				return Response::json($response);
			}

			// CEK KESESUAIN MATERIAL
			if($pick_out->material_number != $material_number){
				$response = array(
					'status' => false,
					'message' => 'Material chemical salah'
				);
				return Response::json($response);
			}

			try {				
				$pick = new IndirectMaterialPick([
					'qr_code' => $pick_out->qr_code,
					'schedule_id' => $schedule_id,
					'material_number' => $pick_out->material_number,
					'cost_center_id' => $pick_out->cost_center_id,
					'in_date' => $pick_out->in_date,
					'exp_date' => $pick_out->exp_date,
					'remark' => 'out',
					'created_by' => Auth::id()
				]);
				$pick->save();

				// $delete_out = IndirectMaterialOut::where('qr_code', $qr)->delete();

				$response = array(
					'status' => true,
					'message' => 'Chemical telah ditambahkan'
				);
				return Response::json($response);
			}catch (Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}	
		}






		$stock = IndirectMaterialStock::where('qr_code', $qr)->first();
		

		// CHEMICAL TIDAK KADALUARSA
		$first_chemical = IndirectMaterialStock::where('material_number', $material_number)
		->where('exp_date', '>=', $now)
		->orderBy('in_date', 'ASC')
		->first();


		if($stock){
			if($stock->material_number != $material_number){
				$response = array(
					'status' => false,
					'message' => 'Material chemical salah'
				);
				return Response::json($response);
			}

			// CEK EXPIRED
			if($now >= $stock->exp_date){
				$response = array(
					'status' => false,
					'message' => 'Chemical Expired'
				);
				return Response::json($response);
			}

			// CEK FIFO
			// if($stock->in_date > $first_chemical->in_date){
			// 	$response = array(
			// 		'status' => false,
			// 		'message' => 'Pengambilan harus FIFO, Ambil chemical '.$stock->material_number.' dengan tanggal masuk ' . date('d-m-Y', strtotime($first_chemical->in_date)) . ' terlebih dahulu'
			// 	);
			// 	return Response::json($response);
			// }

			try {				
				$pick = new IndirectMaterialPick([
					'qr_code' => $stock->qr_code,
					'schedule_id' => $schedule_id,
					'material_number' => $stock->material_number,
					'cost_center_id' => $location,
					'in_date' => $stock->in_date,
					'exp_date' => $stock->exp_date,
					'remark' => 'new',
					'created_by' => Auth::id()
				]);
				$pick->save();

				$delete_stock = IndirectMaterialStock::where('qr_code', $qr)->delete();

				$response = array(
					'status' => true,
					'message' => 'Chemical telah ditambahkan'
				);
				return Response::json($response);
			}catch (Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}	
		}else{

			// CEK KESESUAIN MATERIAL
			$response = array(
				'status' => false,
				'message' => 'Material chemical salah'
			);
			return Response::json($response);
		}
	}

	public function fetchChmPicked(Request $request){

		$location = $request->get('location');
		$schedule_id = $request->get('schedule_id');

		$data = IndirectMaterialPick::leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'indirect_material_picks.material_number')
		->where('indirect_material_picks.cost_center_id', $location)
		->where('indirect_material_picks.schedule_id', $schedule_id)
		->select(
			'indirect_material_picks.id',
			'indirect_material_picks.qr_code',
			'indirect_material_picks.material_number',
			'chm.material_description',
			'indirect_material_picks.remark'
		)
		->get();

		return DataTables::of($data)
		->addColumn('delete', function($data){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deletePicked(id)" id="' . $data->id . '"><i class="fa fa-close"></i></a>';
		})
		->rawColumns([
			'delete' => 'delete'
		])
		->make(true);

	}

	public function fetchAdditionChm(Request $request){
		$solution_id = $request->get('solution_id');

		$getAdditionChm = ChemicalSolutionComposer::where('solution_id', $solution_id)
		->where('addition', 1)
		->select('solution_name', 'material_number', 'material_description', 'storage_location', 'bun')
		->get();

		echo '<option value=""></option>';
		for($i=0; $i < count($getAdditionChm); $i++) {
			echo '<option value="'.$getAdditionChm[$i]['material_number'].'(ime)'.$getAdditionChm[$i]['material_description'].'(ime)'.$getAdditionChm[$i]['storage_location'].'(ime)'.$getAdditionChm[$i]['bun'].'">'.$getAdditionChm[$i]['material_number'].' - '.$getAdditionChm[$i]['material_description'].'</option>';
		}
	}

	public function fetchPickingScheduleDetail(Request $request){
		$data = IndirectMaterialSchedule::leftJoin('chemical_solution_composers', function($join){
			$join->on('indirect_material_schedules.solution_id', '=', 'chemical_solution_composers.solution_id');
			$join->on('indirect_material_schedules.material_number', '=', 'chemical_solution_composers.material_number');	
		})
		->leftJoin('indirect_material_cost_centers', 'indirect_material_schedules.cost_center_id', '=', 'indirect_material_cost_centers.id')
		->leftJoin('users', 'indirect_material_schedules.picked_by', '=', 'users.id')
		->where('indirect_material_schedules.id', $request->get('id'))
		->select(
			'indirect_material_schedules.id',
			'indirect_material_schedules.schedule_date',
			'indirect_material_schedules.schedule_shift',
			'indirect_material_schedules.category',
			'chemical_solution_composers.solution_name',
			'indirect_material_schedules.cost_center_id',
			db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
			'chemical_solution_composers.material_number',
			'chemical_solution_composers.material_description',
			'chemical_solution_composers.material_bun',
			'chemical_solution_composers.storage_location',
			'chemical_solution_composers.quantity',
			'chemical_solution_composers.bun',
			db::raw('IF(users.name is null, "-", users.name) AS name'),
			db::raw('IF(indirect_material_schedules.picked_time is null, "-", indirect_material_schedules.picked_time) AS picked_time')
		)
		->first();

		$inventory = Inventory::where('inventories.material_number', $data->material_number)
		->where('inventories.storage_location', 'MSTK')
		->select('inventories.material_number', 'inventories.quantity')
		->first();

		$out = IndirectMaterialOut::where('material_number', $data->material_number)
		->where('cost_center_id', $data->cost_center_id)
		->get();

		$response = array(
			'status' => true,
			'data' => $data,
			'inventory' => $inventory,
			'out' => $out
		);
		return Response::json($response);

	}

	public function fetchPickingSchedule(Request $request){

		$data = IndirectMaterialSchedule::leftJoin('chemical_solution_composers', function($join){
			$join->on('indirect_material_schedules.solution_id', '=', 'chemical_solution_composers.solution_id');
			$join->on('indirect_material_schedules.material_number', '=', 'chemical_solution_composers.material_number');	
		})
		->leftJoin('indirect_material_cost_centers', 'indirect_material_schedules.cost_center_id', '=', 'indirect_material_cost_centers.id')
		->leftJoin('users AS pick', 'indirect_material_schedules.picked_by', '=', 'pick.id')
		->leftJoin('users AS change', 'indirect_material_schedules.changed_by', '=', 'change.id');

		$username = Auth::user()->username;
		if((!str_contains(strtoupper($username), 'PI')) || (Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'CHM')){

		}else{
			$emp = EmployeeSync::where('employee_id', strtoupper($username))->first();
			$data = $data->where('indirect_material_cost_centers.department', $emp->department);

		}

		if(strlen($request->get('datefrom')) > 0 ){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '>=', $datefrom);
		}
		if(strlen($request->get('dateto')) > 0 ){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
			$data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '<=', $dateto);
		}
		if($request->get('section') != null){
			$data = $data->whereIn('indirect_material_schedules.cost_center_id', $request->get('section'));
		}
		if($request->get('status') != null){
			if($request->get('status') == 'Picked'){
				$data = $data->whereNotNull('indirect_material_schedules.picked_by');
			}elseif ($request->get('status') == 'Scheduled') {
				$data = $data->whereNull('indirect_material_schedules.picked_by');			
			}
		}
		if($request->get('larutan') != null){
			$data = $data->whereIn('indirect_material_schedules.solution_id', $request->get('larutan'));
		}
		if($request->get('material') != null){
			$data = $data->whereIn('indirect_material_schedules.material_number', $request->get('material'));
		}


		if($request->get('request') != null){
			$dateto = date('Y-m-d H:i:s');
			$data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '<=', $dateto);
			$data = $data->whereNull('indirect_material_schedules.picked_time');
		}
		if($request->get('location') != null){
			$data = $data->where('indirect_material_schedules.cost_center_id', $request->get('location'));
		}

		$data = $data->select(
			'indirect_material_schedules.id',
			'indirect_material_schedules.schedule_date',
			'indirect_material_schedules.schedule_shift',
			'indirect_material_schedules.category',
			'chemical_solution_composers.solution_name',
			db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
			'chemical_solution_composers.material_number',
			'chemical_solution_composers.material_description',
			'chemical_solution_composers.storage_location',
			'indirect_material_schedules.quantity',
			'indirect_material_schedules.bun',
			db::raw('IF(pick.name is null, "-", pick.name) AS picked_name'),
			db::raw('IF(indirect_material_schedules.picked_time is null, "-", indirect_material_schedules.picked_time) AS picked_time'),
			db::raw('IF(change.name is null, "-", change.name) AS changed_name'),
			db::raw('IF(indirect_material_schedules.changed_time is null, "-", indirect_material_schedules.changed_time) AS changed_time')
		);


		if($request->get('request') != null){
			$data = $data->orderBy('indirect_material_schedules.schedule_date', 'asc')
			->get();

			$response = array(
				'status' => true,
				'data' => $data
			);
			return Response::json($response);
		}


		$data = $data->orderBy('indirect_material_schedules.schedule_date', 'desc')
		->orderBy('indirect_material_schedules.solution_id', 'desc')
		->limit(500)
		->get();

		return DataTables::of($data)
		->addColumn('delete', function($data){

			$emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

			if($emp && $data->picked_time == '-'){
				if(Auth::user()->role_code == 'CHM' ||Auth::user()->role_code == 'MIS' || stripos($emp->position, 'Foreman') !== false){
					return '<button style="width: 50%; height: 100%;" onclick="deleteSchedule(\''.$data->id.'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
				}else{
					return '-';
				}
			}else{
				return '-';

			}
		})
		->addColumn('change', function($data){

			$emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

			if(($data->picked_time != '-') && ($data->changed_time == '-')){
				return '<button style="width: 50%; height: 100%;" onclick="change(\''.$data->id.'\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-refresh"></i></span></button>';
			}else{
				return '-';

			}
		})
		->rawColumns([ 
			'delete' => 'delete',
			'change' => 'change'
		])
		->make(true);

	}

	public function fetchStock(Request $request){

		$data = Inventory::leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'inventories.material_number')
		->leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'inventories.material_number')
		->where('inventories.storage_location', 'MSTK')
		->where('inventories.quantity', '>', 0);

		if($request->get('material_number') != null){
			$data = $data->whereIn('inventories.material_number', $request->get('material_number'));
		}

		$data = $data->select(
			'inventories.material_number',
			'material_plant_data_lists.material_description',
			'material_plant_data_lists.bun',
			'chm.storage_location',
			'inventories.quantity',
			'inventories.updated_at'
		)
		->orderBy('inventories.updated_at', 'desc')
		->get();

		return DataTables::of($data)->make(true);	
	}

	public function fetchNew(Request $request){
		$data = IndirectMaterialStock::leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'indirect_material_stocks.material_number')
		->leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'indirect_material_stocks.material_number');

		if($request->get('material_number') != null){
			$data = $data->whereIn('indirect_material_stocks.material_number', $request->get('material_number'));
		}

		$data = $data->select(
			'indirect_material_stocks.qr_code',
			'indirect_material_stocks.material_number',
			'material_plant_data_lists.material_description',
			'material_plant_data_lists.bun',
			'chm.storage_location',
			'indirect_material_stocks.in_date',
			'indirect_material_stocks.exp_date',
			'indirect_material_stocks.print_status',
			'indirect_material_stocks.created_at'
		)
		->orderBy('indirect_material_stocks.qr_code', 'desc')
		->orderBy('indirect_material_stocks.created_at', 'desc')
		->get();

		return DataTables::of($data)
		->addColumn('print', function($data){
			if($data->print_status == 1){
				return '<button style="width: 50%; height: 100%;" onclick="print(\''.$data->qr_code.'\')" class="btn btn-xs btn-info form-control"><span><i class="fa fa-print"></i></span> Reprint</button>';
			}else{
				return '<button style="width: 50%; height: 100%;" onclick="print(\''.$data->qr_code.'\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-print"></i></span> Print</button>';
			}
		})
		->addColumn('check', function($data){
			return '<input type="checkbox" id="'.$data->qr_code.'" onclick="showSelected(this)">';
		})
		->rawColumns([ 
			'reprint' => 'print',
			'check' => 'check'
		])
		->make(true);
	}

	public function fetchOut(Request $request){

		$data = IndirectMaterialOut::leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'indirect_material_outs.material_number')
		->leftJoin('indirect_material_cost_centers', 'indirect_material_cost_centers.id', '=', 'indirect_material_outs.cost_center_id')
		->leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'indirect_material_outs.material_number');

		if($request->get('material_number') != null){
			$data = $data->whereIn('indirect_material_outs.material_number', $request->get('material_number'));
		}

		$data = $data->select(
			db::raw("
				concat(indirect_material_cost_centers.section,' - ',indirect_material_cost_centers.location) AS location"),
			'material_plant_data_lists.bun',
			'chm.storage_location',
			'indirect_material_outs.material_number',
			'material_plant_data_lists.material_description',
			'material_plant_data_lists.bun',
			'indirect_material_outs.created_at',
			'indirect_material_outs.qr_code'
		)
		->orderBy('indirect_material_outs.created_at', 'desc')
		->get();

		return DataTables::of($data)
		->addColumn('print', function($data){
			return '<button style="width: 50%; height: 100%;" onclick="print(\''.$data->qr_code.'\')" class="btn btn-xs btn-info form-control"><span><i class="fa fa-print"></i></span> Reprint</button>';
		})
		->rawColumns([ 
			'reprint' => 'print'
		])
		->make(true);
	}

	public function fetchIndirectMaterialLog(Request $request){

		$data = IndirectMaterialLog::leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'indirect_material_logs.material_number')
		->leftJoin('users', 'users.id', '=', 'indirect_material_logs.created_by')
		->leftJoin(db::raw('(SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm'), 'chm.material_number', '=', 'indirect_material_logs.material_number');

		if(strlen($request->get('datefrom')) > 0 ){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
			$data = $data->where(db::raw('date(indirect_material_logs.created_at)'), '>=', $datefrom);
		}
		if(strlen($request->get('dateto')) > 0 ){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
			$data = $data->where(db::raw('date(indirect_material_logs.created_at)'), '<=', $dateto);
		}
		if($request->get('material_number') != null){
			$data = $data->whereIn('indirect_material_logs.material_number', $request->get('material_number'));
		}


		$data = $data->select(
			'indirect_material_logs.qr_code',
			'indirect_material_logs.material_number',
			'material_plant_data_lists.material_description',
			'material_plant_data_lists.bun',
			'indirect_material_logs.remark',
			'indirect_material_logs.quantity',
			'chm.storage_location',
			'users.name',
			'indirect_material_logs.created_at')
		->orderBy('indirect_material_logs.qr_code', 'desc')
		->limit(500)
		->get();

		return DataTables::of($data)->make(true);
	}

	public function fetchSolutionControl(Request $request){
		$larutan_id = $request->get('larutan');

		if(strlen($request->get('datefrom')) > 0 ){
			$datefrom = $request->get('datefrom');
		}else{
			$datefrom = date('Y-m-01');
		}

		if(strlen($request->get('datefrom')) > 0 ){
			$dateto = $request->get('dateto');
		}else{
			$dateto = date('Y-m-d');
		}

		$larutan = ChemicalSolution::leftJoin('indirect_material_cost_centers','indirect_material_cost_centers.id','=','chemical_solutions.cost_center_id')
		->where('chemical_solutions.id', $larutan_id)
		->select('chemical_solutions.id', 'chemical_solutions.solution_name', 'chemical_solutions.category', 'chemical_solutions.cost_center_id', 'indirect_material_cost_centers.department', 'indirect_material_cost_centers.location')
		->first();

		if($larutan->category == 'CONTROLLING CHART'){
			$data = db::select("SELECT date.week_date AS date, accumulative, target_max, target_warning FROM
				(SELECT week_date FROM weekly_calendars
				WHERE week_date >= '".$datefrom."'
				AND week_date <= '".$dateto."'
				) date
				LEFT JOIN
				(SELECT date, target_max, target_warning, MAX(accumulative) as accumulative from chemical_control_logs
				WHERE date >= '".$datefrom."'
				AND date <= '".$dateto."'
				AND solution_name = '".$larutan->solution_name."'
				AND cost_center_id = ".$larutan->cost_center_id."
				GROUP BY date, target_max, target_warning
				) chm
				ON date.week_date = chm.date
				ORDER BY date.week_date ASC");

			$response = array(
				'status' => true,
				'data' => $data,
				'location' => $larutan
			);
			return Response::json($response);
		}else{
			$response = array(
				'status' => false
			);
			return Response::json($response);
		}

	}

	public function printLabel($param){

		$qr_code = explode(",", $param);

		$update = IndirectMaterialStock::whereIn('qr_code', $qr_code)
		->update([
			'print_status' => 1
		]);

		$data = IndirectMaterialStock::leftJoin('indirect_materials', 'indirect_material_stocks.material_number', '=', 'indirect_materials.material_number')
		->whereIn('qr_code', $qr_code)
		->select(
			'indirect_material_stocks.qr_code',
			'indirect_material_stocks.material_number',
			'indirect_materials.material_description',
			'indirect_materials.label',
			db::raw('date_format(indirect_material_stocks.in_date, "%d-%m-%Y") AS masuk'),
			db::raw('date_format(indirect_material_stocks.exp_date, "%d-%m-%Y") AS exp'),
			db::raw('date_format(indirect_material_stocks.in_date, "%M") AS month')
		)
		->orderBy('indirect_material_stocks.qr_code', 'desc')
		->get();

		if(count($data) == 0){
			$data = IndirectMaterialOut::leftJoin('indirect_materials', 'indirect_material_outs.material_number', '=', 'indirect_materials.material_number')
			->whereIn('qr_code', $qr_code)
			->select(
				'indirect_material_outs.qr_code',
				'indirect_material_outs.material_number',
				'indirect_materials.material_description',
				'indirect_materials.label',
				db::raw('date_format(indirect_material_outs.in_date, "%d-%m-%Y") AS masuk'),
				db::raw('date_format(indirect_material_outs.exp_date, "%d-%m-%Y") AS exp'),
				db::raw('date_format(indirect_material_outs.in_date, "%M") AS month')
			)
			->orderBy('indirect_material_outs.qr_code', 'desc')
			->get();
		}

		// dd($data);

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'potrait');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('indirect_material.chemical.label_pdf', array(
			'data' => $data
		));
		return $pdf->stream("Print_label.pdf");




	}

}
