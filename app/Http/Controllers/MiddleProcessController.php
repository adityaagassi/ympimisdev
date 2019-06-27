<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\LogProcessMiddle;
use App\LogNgMiddle;
use App\TagMaterial;
use App\MiddleInventory;
use App\BarrelQueue;
use App\Barrel;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;

class MiddleProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexProcessMiddleSX(){
		return view('processes.middle.index_sx')->with('page', 'Middle Process SX')->with('head', 'Middle Process');
	}

	public function indexProcessBarrelMachine(){
		$title = 'Saxophone Barrel Machine';
		
		return view('processes.middle.barrel_machine', array(
			'title' => $title,))->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
	}

	public function indexProcessBarrelBoard($id){

		if($id == 'barrel-sx'){
			$title = 'Saxophone Barrel Board';
			$mprc = 'S51';
			$hpl = 'ASKEY,TSKEY';
		}
		
		return view('processes.middle.barrel_board', array(
			'title' => $title,
			'mrpc' => $mprc,
			'hpl' => $hpl,
		))->with('page', 'Middle Process Barrel Board')->with('head', 'Middle Process');
	}

	public function indexProcessMiddleBarrel($id){
		if($id == 'barrel-sx'){
			$title = 'Saxophone Tumbling-Barrel';
			$mprc = 'S51';
			$hpl = 'ASKEY,TSKEY';
			$surface = 'LCQ';
		}

		return view('processes.middle.barrel', array(
			'title' => $title,
			'mrpc' => $mprc,
			'hpl' => $hpl,
			'surface' => $surface,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function indexProcessMiddleKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();
		$groups = DB::table('middle_groups')->where('location', '=', $id)->get();

		if($id == 'incoming-lcq'){
			$title = 'Incoming Check Saxophone Key Lcq';
		}
		if($id == 'incoming-lcq2'){
			$title = 'Incoming Check Saxophone Key After Treatment Lcq';
		}
		if($id == 'incoming-lcq-body'){
			$title = 'Incoming Check Saxophone Body Lcq';
		}
		if($id == 'incoming-plt-sx'){
			$title = 'Incoming Check Saxophone Plt';
		}
		if($id == 'kensa-lcq'){
			$title = 'Kensa Saxophone Lcq';
		}
		if($id == 'kensa-plt-sx'){
			$title = 'Kensa Saxophone Plt';
		}

		return view('processes.middle.kensa', array(
			'ng_lists' => $ng_lists,
			'groups' => $groups,
			'loc' => $id,
			'title' => $title,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function fetchMiddleBarrelBoard(Request $request){
		$barrel_queues =  DB::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->where('materials.hpl', '=', $request->get('hpl'))
		->select('barrel_queues.created_at', 'materials.model', 'materials.key')
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		$response = array(
			'status' => true,
			'barrel_queues' => $barrel_queues,
		);
		return Response::json($response);
	}

	public function fetchMiddleBarrel(Request $request){
		$queues = db::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->leftJoin('barrel_jigs', function($join)
		{
			$join->on('barrel_jigs.key', '=', 'materials.key');
			$join->on('barrel_jigs.hpl','=', 'materials.hpl');
		})
		->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->whereIn('materials.hpl', $request->get('hpl'))
		->select('barrel_queues.tag', 'barrel_queues.created_at', 'materials.model', 'materials.hpl', 'materials.material_number', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_jigs.spring', 'bom_components.material_child', 'bom_components.material_description', 'barrel_jigs.lot')
		->orderBy('barrel_queues.created_at', 'asc')
		->get();

		// $queues = db::table('barrel_queues')
		// ->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		// ->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
		// ->where('materials.category', '=', 'WIP')
		// ->where('materials.hpl', '=', $request->get('hpl'))
		// ->where('materials.mrpc', '=', $request->get('mrpc'))
		// ->select('materials.model', 'materials.key', 'materials.surface', 'barrel_queues.created_at', 'bom_components.material_child', 'bom_components.material_description', 'barrel_queues.tag')
		// ->orderBy('barrel_queues.created_at', 'asc')
		// ->get();

		// if(strpos($queues[0]->surface, 'PLT') !== false){
		// 	$jobs = array();
		// 	foreach ($queues as $queue) {
		// 		if(strpos($queue->surface, 'LCQ')){
		// 			break;
		// 		}
		// 		array_push($jobs, array(
		// 			'jig' => '-',
		// 			'key' => $queue->key,
		// 			'model' => $queue->model,
		// 			'surface' => $queue->surface,
		// 			'material_child' => $queue->material_child,
		// 			'material_description' => $queue->material_description,
		// 			'tag' => $queue->tag,
		// 		));
		// 	}
		// }
		// else{
		// 	$jobs = db::table('barrel_jigs')
		// 	->leftJoin(db::raw('(
		// 		select min(barrel_queues.id) as id, materials.key from barrel_queues left join materials on materials.material_number = barrel_queues.material_number where materials.surface like "%LCQ%" and materials.mrpc = "S51" group by materials.key
		// 	) as queue'), 'queue.key', '=', 'barrel_jigs.key')
		// 	->leftJoin('barrel_queues', 'barrel_queues.id', '=', 'queue.id')
		// 	->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		// 	// ->leftJoin('bom_components', 'bom_components.material_parent', '=', 'materials.material_number')
		// 	->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
		// 	->select('barrel_jigs.jig', 'barrel_jigs.key', 'barrel_queues.tag', 'materials.model', 'materials.surface', 'materials.material_number', 'materials.material_description', 'bom_components.material_child', 'bom_components.material_description')
		// 	->where('barrel_jigs.hpl', '=', $request->get('hpl'))
		// 	->orderBy('barrel_jigs.jig', 'asc')
		// 	->orderBy('barrel_jigs.key', 'asc')
		// 	->get();
		// }

		$response = array(
			'status' => true,
			'queues' => $queues,
		);
		return Response::json($response);
	}

	public function fetchMiddleBarrelMachine(Request $request){
		$queue = db::table('barrel_queues')
		->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		->where('materials.category', '=', 'WIP')
		->where('materials.hpl', '=', $request->get('hpl'))
		->where('materials.mrpc', '=', $request->get('mrpc'))
		->orderBy('barrel_queues.created_at', 'asc')
		->first();

		if($queue != null){
			if(strpos($queue->surface, 'PLT') !== false){
				$no_machine = 'Direct To Plating';
				$capacity = '0';
			}
			else{
				$machine = db::table('barrel_machines')->where('status', '=', 'idle')->orderBy('updated_at', 'asc')->first();

				if($machine != null){
					$no_machine = $machine->machine;
					$capacity = db::table('barrel_jigs')->where('hpl', '=', $request->get('hpl'))->count();
				}
				else{
					$no_machine = 'FULL';
					$capacity = '0';
				}
			}

			$response = array(
				'status' => true,
				'no_machine' => $no_machine,
				'capacity' => $capacity
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'no_machine' => 'No Queue',
				'capacity' => 0
			);
			return Response::json($response);
		}

	}

	public function printMiddleBarrel(Request $request){
		$id = Auth::id();
		$printer_name = 'Trial Printer';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if($request->get('surface') == 'LCQ'){
			$code_generator = CodeGenerator::where('note','=','barrel_machine')->first();
			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			$qr_machine = $code_generator->prefix . $number;
			try{
				$tags = $request->get('tag');

				foreach ($tags as $tag) {
					$barrel_queue = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
					->where('barrel_queues.tag', '=', $tag[0])
					->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.key', 'materials.model', 'materials.surface', 'materials.material_description')
					->first();

					$insert_jig = [
						'machine' => $request->get('no_machine'),
						'jig' => $tag[1],
						'key' => $barrel_queue->key,
						'tag' => $tag[0],
						'material_number' => $barrel_queue->material_number,
						'qty' => $barrel_queue->quantity,
						'status' => 'racking',
						'remark' => $qr_machine,
						'created_by' => $id
					];

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(4,4);
					$printer->setUnderline(true);
					$printer->text('LACQUERING'."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(5,2);
					$printer->text($barrel_queue->model." ".$barrel_queue->key."\n");
					$printer->text($barrel_queue->surface."\n\n");
					$printer->initialize();
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->qrCode($barrel_queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
					$printer->text($barrel_queue->tag."\n\n");
					$printer->initialize();
					$printer->setTextSize(1,1);
					$printer->text("GMC : ".$barrel_queue->material_number." MACHINE: ".$request->get('no_machine')." JIG: ".$tag[1]."\n");
					$printer->text("DESC: ".$barrel_queue->material_description."\n");	
					$printer->text("QTY : ".$barrel_queue->quantity." PC(S)                MACHINE: ".$request->get('no_machine')." JIG: ".$tag[1]."\n");			
					$printer->cut(Printer::CUT_PARTIAL, 50);
					$printer->close();

					$barrel = new Barrel($insert_jig);
					$delete_queue = BarrelQueue::where('tag', '=', $tag[0]);

					DB::transaction(function() use ($barrel, $delete_queue){
						$barrel->save();
						$delete_queue->forceDelete();
					});
				}

				$code_generator->index = $code_generator->index+1;
				$code_generator->save();
				
				$response = array(
					'status' => true,
					'message' => 'New kanban has been printed',
					'tes' => $tags,
				);
				return Response::json($response);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}
		else{

		}

		// $printer_name = 'Trial Printer';
		// $connector = new WindowsPrintConnector($printer_name);
		// $printer = new Printer($connector);

		// if($request->get('no_machine') == 'Direct To Plating'){
		// 	$queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		// 	->whereIn('barrel_queues.tag', $request->get('tag'))
		// 	->get();

		// 	$data_middle_inventory = array();

		// 	foreach ($queues as $queue) {
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->setTextSize(4,4);
		// 		$printer->setUnderline(true);
		// 		$printer->text('PLATING'."\n\n");
		// 		$printer->initialize();
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->setTextSize(5,2);
		// 		$printer->text($queue->model." ".$queue->key."\n");
		// 		$printer->text($queue->surface."\n\n");
		// 		$printer->initialize();
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->qrCode($queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		// 		$printer->text($queue->tag."\n\n");
		// 		$printer->initialize();
		// 		$printer->setTextSize(1,2);
		// 		$printer->text("GMC : ".$queue->material_number."\n");
		// 		$printer->text("DESC: ".$queue->material_description."\n");	
		// 		$printer->text("QTY : ".$queue->quantity." PC(S)\n");			
		// 		$printer->cut(Printer::CUT_PARTIAL, 50);
		// 		$printer->close();

		// 		$data_middle_inventory = [
		// 			'tag' => $queue->tag,
		// 			'material_number' => $queue->material_number,
		// 			'quantity' => $queue->quantity,
		// 			'location' => 'incoming-plating',
		// 		];

		// 		try{
		// 			$middle_inventory = MiddleInventory::firstOrCreate(
		// 				['tag' => $queue->tag],
		// 				$data_middle_inventory
		// 			);
		// 			$delete_queue = BarrelQueue::where('tag', $queue->tag)->forceDelete();
		// 		}
		// 		catch(\Exception $e){
		// 			$response = array(
		// 				'status' => false,
		// 				'message' => $e->getMessage(),
		// 			);
		// 			return Response::json($response);
		// 		}
		// 	}

		// 	$response = array(
		// 		'status' => true,
		// 		'message' => 'New kanban has been printed',
		// 	);
		// 	return Response::json($response);
		// }
		// elseif($request->get('no_machine') !== 'FULL'){
		// 	$machine = BarrelMachine::where('status', '=', 'idle')->orderBy('updated_at', 'asc')->first();

		// 	if(strlen($machine->machine) <= 0){
		// 		$response = array(
		// 			'status' => false,
		// 			'message' => 'No Machine Available',
		// 		);
		// 		return Response::json($response);
		// 	}

		// 	$queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
		// 	->whereIn('barrel_queues.tag', $request->get('tag'))
		// 	->get();

		// 	$data_barrel = array();
		// 	$data_middle_inventory = array();

		// 	foreach ($queues as $queue) {
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->setTextSize(4,4);
		// 		$printer->setUnderline(true);
		// 		$printer->text('LACQUERING'."\n\n");
		// 		$printer->initialize();
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->setTextSize(5,2);
		// 		$printer->text($queue->model." ".$queue->key."\n");
		// 		$printer->text($queue->surface."\n\n");
		// 		$printer->initialize();
		// 		$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 		$printer->qrCode($queue->tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		// 		$printer->text($queue->tag."\n\n");
		// 		$printer->initialize();
		// 		$printer->setTextSize(1,2);
		// 		$printer->text("GMC : ".$queue->material_number."\n");
		// 		$printer->text("DESC: ".$queue->material_description."\n");	
		// 		$printer->text("QTY : ".$queue->quantity." PC(S)\n");			
		// 		$printer->cut(Printer::CUT_PARTIAL, 50);
		// 		$printer->close();

		// 		$data_barrel = [
		// 			'machine' => $machine->machine,
		// 			'tag' => $queue->tag,
		// 			'key' => $queue->key,
		// 			'material_number' => $queue->material_number,
		// 			'qty' => $queue->quantity,
		// 			'status' => 'racking',
		// 			'created_by' => $id,
		// 		];

		// 		$data_middle_inventory = [
		// 			'tag' => $queue->tag,
		// 			'material_number' => $queue->material_number,
		// 			'quantity' => $queue->quantity,
		// 			'location' => 'barrel',
		// 		];

		// 		try{
		// 			try{
		// 				$barrel = new Barrel($data_barrel);
		// 				$middle_inventory = MiddleInventory::firstOrCreate(
		// 					['tag' => $queue->tag],
		// 					$data_middle_inventory
		// 				);
		// 				$barrel->save();
		// 				$delete_queue = BarrelQueue::where('tag', $queue->tag)->forceDelete();
		// 			}
		// 			catch(\Exception $e){
		// 				$response = array(
		// 					'status' => false,
		// 					'message' => $e->getMessage(),
		// 				);
		// 				return Response::json($response);
		// 			}
		// 		}
		// 		catch(\Exception $e){
		// 			$response = array(
		// 				'status' => false,
		// 				'message' => $e->getMessage(),
		// 			);
		// 			return Response::json($response);
		// 		}
		// 	}

		// 	$duration = date_create(date('Y-m-d H:i:s'))->format('U')-date_create(date('Y-m-d H:i:s', strtotime($machine->updated_at)))->format('U');
		// 	$input_log = new BarrelMachineLog([
		// 		'machine' => $machine->machine,
		// 		'status' => 'idle',
		// 		'created_by' => $id,
		// 		'duration' => $duration,
		// 	]);
		// 	$input_log->save();
		// 	$update_machine = BarrelMachine::where('machine', '=', $machine->machine)->update([
		// 		'status' => 'racking',
		// 	]);

		// 	$response = array(
		// 		'status' => true,
		// 		'message' => 'New kanban has been printed',
		// 	);
		// 	return Response::json($response);
		// }
		// else{
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => 'Undefined Error',
		// 	);
		// 	return Response::json($response);
		// }
	}

	public function scanMiddleBarrel(Request $request){	
		$id = Auth::id();
		$printer_name = 'Trial Printer';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		if(substr($request->get('qr'),0,3) == 'MCB'){
			$barrels = Barrel::where('remark', '=', $request->get('qr'))->get();
			$barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->first();

			if($barrels->count() > 0){
				if($barrel_machine->status == 'idle' && $barrels[0]->status == 'queue'){
					try{
						$update_barrel = Barrel::where('remark', '=', $request->get('qr'))->update([
							'status' => 'running',
						]);

						$duration_log = date_create(date('Y-m-d H:i:s'))->format('U')-date_create(date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)))->format('U');
						$update_log = new BarrelLog([
							'machine' => $barrels[0]->machine,
							'status' => 'idle',
							'created_by' => $id,
							'duration' => $duration_log,
						]);
						$update_log->save();

						$update_barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->update([
							'status' => 'running',
						]);

						// foreach ($barrels as $barrel) {
						// 	$duration = date_create(date('Y-m-d H:i:s'))->format('U')-date_create(date('Y-m-d H:i:s', strtotime($barrel->finish_racking)))->format('U');
						// 	$insert_log = [
						// 		'machine' => $barrel->machine,
						// 		'tag' => $barrel->tag,
						// 		'material' => $barrel->material_number,
						// 		'qty' => $barrel->qty,
						// 		'status' => 'queue',
						// 		'duration' => $duration,
						// 		'created_by' => $id,
						// 	];
						// 	$barrel_log = new BarrelLog($insert_log);
						// }

						$response = array(
							'status' => true,
							'message' => 'Machine running.',
						);
						return Response::json($response);

					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}
				elseif($barrel_machine->status == 'running' && $barrels[0]->status == 'running'){
					try{

					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}
				else{
					$response = array(
						'status' => false,
						'message' => 'Machine status invalid.',
					);
					return Response::json($response);
				}	
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Qr code cycle is done.',
				);
				return Response::json($response);
			}
		}
		else{
			$barrel = Barrel::where('tag', '=', $request->get('qr'))
			->where('status', '=', 'racking')
			->first();

			if($barrel != null){
				try{
					$barrel->finish_racking = date('Y-m-d H:i:s');
					$barrel->status = 'queue';

					$duration = date_create(date('Y-m-d H:i:s'))->format('U')-date_create(date('Y-m-d H:i:s', strtotime($barrel->updated_at)))->format('U');

					$insert_log = [
						'machine' => $barrel->machine,
						'tag' => $barrel->tag,
						'material' => $barrel->material_number,
						'qty' => $barrel->qty,
						'status' => 'racking',
						'duration' => $duration,
						'created_by' => $id,
					];

					$barrel_log = new BarrelLog($insert_log);

					DB::transaction(function() use ($barrel, $barrel_log){
						$barrel->save();
						$barrel_log->save();
					});

					$check_barrels = Barrel::where('remark', '=', $barrel->remark)->where('status', '<>', 'queue')->get();

					if($check_barrels->count() == 0){

						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setTextSize(4,4);
						$printer->text('BARREL'."\n");
						$printer->text("MACHINE ".$barrel->machine."\n");
						$printer->initialize();
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->qrCode($barrel->remark, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
						$printer->text($barrel->remark."\n\n");
						$printer->cut();
						$printer->close();

						$response = array(
							'status' => true,
							'message' => 'All material has been racked, printing machine label.',
						);
						return Response::json($response);
					}

					$response = array(
						'status' => true,
						'message' => 'Material has been racked',
					);
					return Response::json($response);

				}
				catch(\Exception $e){
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'QR code status invalid.',
				);
				return Response::json($response);
			}
		}

		// if(substr($request->get('qr'),0,3) == 'MCB'){
		// 	$barrels = Barrel::where('remark', '=', $request->get('qr'))->get();

		// 	foreach ($barrels as $barrel) {
		// 		# code...
		// 	}

		// }
		// else{
		// 	$barrel = Barrel::where('tag', '=', $request->get('qr'))->first();
		// 	if($barrel->status <> 'racking'){
		// 		$response = array(
		// 			'status' => false,
		// 			'message' => 'QR code status invalid.',
		// 		);
		// 		return Response::json($response);
		// 	}
		// 	if($barrel != null){
		// 		$barrel->finish_racking = date('Y-m-d H:i:s');
		// 		$barrel->status = 'running';
		// 		$barrel->save();

		// 		$check_barrels = Barrel::where('machine', '=', $barrel->machine)->where('status', '<>', 'running')->get();

		// 		if($check_barrels->count() == 0){
		// 			$printer_name = 'Trial Printer';
		// 			$connector = new WindowsPrintConnector($printer_name);
		// 			$printer = new Printer($connector);

		// 			$barrel_machine = BarrelMachine::where('machine', '=', $barrel->machine)->first();
		// 			$duration = date_create(date('Y-m-d H:i:s'))->format('U')-date_create(date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)))->format('U');

		// 			try{
		// 				$input_log = new BarrelMachineLog([
		// 					'machine' => $barrel->machine,
		// 					'status' => 'racking',
		// 					'created_by' => $id,
		// 					'duration' => $duration,
		// 				]);
		// 				$input_log->save();
		// 			}
		// 			catch(\Exception $e){
		// 				$response = array(
		// 					'status' => false,
		// 					'message' => $e->getMessage(),
		// 				);
		// 				return Response::json($response);
		// 			}

		// 			$update_barrel_machine = BarrelMachine::where('machine', '=', $barrel->machine)->update([
		// 				'status' => 'running',
		// 			]);

		// 			$code_generator = CodeGenerator::where('note','=','barrel_machine')->first();
		// 			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		// 			$qr_machine = $code_generator->prefix . $number;

		// 			$update_barrel = Barrel::where('machine', '=', $barrel->machine)->update([
		// 				'remark' => $qr_machine,
		// 			]);

		// 			$code_generator->index = $code_generator->index+1;
		// 			$code_generator->save();

		// 			$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 			$printer->setTextSize(4,4);
		// 			$printer->text('BARREL'."\n");
		// 			$printer->text("MACHINE ".$barrel->machine."\n");
		// 			$printer->initialize();
		// 			$printer->setJustification(Printer::JUSTIFY_CENTER);
		// 			$printer->qrCode($qr_machine, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		// 			$printer->text($qr_machine."\n\n");
		// 			$printer->cut();
		// 			$printer->close();
		// 		}
		// 	}
		// 	else{
		// 		$response = array(
		// 			'status' => false,
		// 			'message' => 'QR code not found',
		// 		);
		// 		return Response::json($response);
		// 	}
		// }

		// $response = array(
		// 	'status' => true,
		// 	'message' => substr($request->get('qr'),0,2),
		// );
		// return Response::json($response);
	}

	public function fetchResultMiddleKensa(Request $request){

		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');

		$queryResume = "select sum(total_ok) as ok, sum(total_ng) as ng, round(sum(total_ng)/sum(total_ok), 2) as rate from
		(
		select 0 as total_ok, sum(qty) as total_ng from log_ng_middles where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "'
		union all
		select sum(qty) as total_ok, 0 as total_ng from log_process_middles where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "') as a";

		$queryDetail = "select a.model, a.result, b.ng_name, coalesce(c.ng_qty, 0) as ng_qty from
		(
		select materials.model, sum(log_process_middles.qty) as result from log_process_middles left join materials on materials.material_number = log_process_middles.material_number where log_process_middles.prod_date = '" . $prodDate . "' and log_process_middles.group_code = '" . $request->get('group') . "' and log_process_middles.location = '" . $request->get('location') . "' group by materials.model
		) as a
		cross join
		(
		select ng_name from ng_lists where ng_lists.location = '" . $request->get('location') . "'
		) as b
		left join
		(
		select materials.model, log_ng_middles.ng_name, sum(qty) as ng_qty from log_ng_middles left join materials on materials.material_number = log_ng_middles.material_number where prod_date = '" . $prodDate . "' and group_code = '" . $request->get('group') . "' and location = '" . $request->get('location') . "' group by materials.model, log_ng_middles.ng_name
		) as c on c.model = a.model and c.ng_name = b.ng_name
		order by a.model asc, b.ng_name asc";

		$queryNg = "select a.ng_name, coalesce(ng_qty, 0) as ng_qty from
		(
		(select ng_lists.ng_name from ng_lists where ng_lists.location = '" . $request->get('location') . "') as a
		left join 
		(select log_ng_middles.ng_name, sum(log_ng_middles.qty) as ng_qty from log_ng_middles where log_ng_middles.location = '" . $request->get('location') . "' and log_ng_middles.prod_date = '" . $prodDate . "' and log_ng_middles.group_code = '" . $request->get('group') . "' group by ng_name) as b on a.ng_name = b.ng_name
		)
		order by a.ng_name asc";

		$resume = db::select($queryResume);
		$detail = db::select($queryDetail);
		$ng_lists = db::table('ng_lists')->where('location', '=', $request->get('location'))->select('ng_lists.ng_name')->orderBy('ng_lists.ng_name', 'asc')->get();
		$ng = db::select($queryNg);

		$response = array(
			'status' => true,
			'resume' => $resume,
			'detail' => $detail,
			'ng_lists' => $ng_lists,
			'ng' => $ng,
			'ng_count' => count($ng_lists),
		);
		return Response::json($response);
	}

	public function ScanMiddleKensa(Request $request){
		$id = Auth::id();
		$tag_material = db::table('tag_materials')->where('tag_materials.tag', '=', $request->get('tag'))
		->leftjoin('materials', 'materials.material_number', '=', 'tag_materials.material_number')
		->select('materials.model', 'materials.mrpc', 'materials.material_number', 'tag_materials.tag')
		->first();

		if($tag_material == null){
			$completion = db::connection('mysql2')->table('completions')
			->where('completions.barcode_number', '=', $request->get('tag'))
			->leftjoin('materials', 'materials.id', '=', 'completions.material_id')
			->where('completions.active', '=', '1')
			->where('materials.location', '=', $request->get('sLoc'))
			->select('completions.lot_completion', 'materials.material_number')
			->first();

			if($completion == null){
				$response = array(
					'status' => false,
					'message' => 'Tag material not registered or inactive.',
				);
				return Response::json($response);
			}

			$new_tag = new TagMaterial([
				'tag' => $request->get('tag'),
				'material_number' => $completion->material_number,
				'qty' => $completion->lot_completion ,
				'op_prod' => '-',
				'location' => $request->get('location'),
				'created_by'=> $id,
			]);
			$new_tag->save();

			$tag_material2 = db::table('tag_materials')->where('tag_materials.tag', '=', $request->get('tag'))
			->leftjoin('materials', 'materials.material_number', '=', 'tag_materials.material_number')
			->select('materials.model', 'materials.mrpc', 'materials.material_number', 'tag_materials.tag')
			->first();

			if($tag_material2 == null){
				$response = array(
					'status' => false,
					'message' => 'Material not registered in MIRAI.',
				);
				return Response::json($response);
			}

			$model = $tag_material2->model;
			$tag = $tag_material2->tag;
			$mrpc = $tag_material2->mrpc;
		}
		else{
			$model = $tag_material->model;
			$tag = $tag_material->tag;
			$mrpc = $tag_material->mrpc;
		}

		if($mrpc != $request->get('workCenter')){
			$response = array(
				'status' => false,
				'message' => 'Wrong location',
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'message' => 'Material found',
				'model' => $model,
				'tag' => $tag,
			);
			return Response::json($response);
		}
	}

	public function inputNgMiddleKensa(Request $request){

		$tag_material = TagMaterial::where('tag_materials.tag', '=', $request->get('tag'))
		->first();

		$ngName = $request->get('ng_name');
		$ngQty = $request->get('ng_qty');
		$count_text = $request->get('count_text');
		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');
		$id = Auth::id();

		for ($i=0; $i < count($ngName); $i++) {
			try{
				$log_ng_middle = new LogNgMiddle([
					'group_code' => $request->get('group'),
					'op_kensa' => $request->get('opKensa'),
					'prod_date' => $prodDate,
					'tag' => $request->get('tag'),
					'material_number' => $tag_material->material_number,
					'location' => $request->get('location'),
					'ng_name' => $ngName[$i],
					'qty' => $ngQty[$i],
					'op_prod' => $tag_material->op_prod,
					'created_by' => $id,
				]);
				$log_ng_middle->save();
				$success_count[] = $count_text[$i];
			}
			catch (QueryException $e){
				$fail_count[] = $count_text[$i];
			}
		}

		if(isset($fail_count)){
			$response = array(
				'status' => false,
				'fail_count' => $fail_count,
				'message' => 'Material NG has some errors',
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => true,
				'success_count' => $success_count,
				'message' => 'Material NG has been inputted',
			);
			return Response::json($response);
		}
	}

	public function inputResultMiddleKensa(Request $request){
		$tag_material = TagMaterial::where('tag_materials.tag', '=', $request->get('tag'))
		->first();

		$prodDate = \DateTime::createFromFormat('d/m/Y', $request->get('prodDate'))->format('Y-m-d');

		try{

			$id = Auth::id();
			$tag_material->location = $request->get('location');
			$log_process_middle = new LogProcessMiddle([
				'group_code' => $request->get('group'),
				'op_kensa' => $request->get('opKensa'),
				'prod_date' => $prodDate,
				'tag' => $request->get('tag'),
				'material_number' => $tag_material->material_number,
				'location' => $request->get('location'),
				'qty' => $tag_material->qty,
				'op_prod' => $tag_material->op_prod,
				'created_by' => $id,
			]);

			$inventory = MiddleInventory::firstOrNew(['location' => $request->get('location'), 'material_number' => $tag_material->material_number]);
			$inventory->quantity = ($inventory->quantity+$tag_material->qty);

			$tag_material->save();
			$log_process_middle->save();
			$inventory->save();

			$response = array(
				'status' => true,
				'message' => 'Material '. $tag_material->material_number .' inputted as production result.',
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

	public function fetchProcessBarrelMachine()
	{
		$data = DB::table('barrel_machines')->select('machine', 'status','updated_at',DB::raw('now() as now'),DB::raw('TIMEDIFF(now(),updated_at) as duration'),DB::raw('ROUND(TIME_TO_SEC(TIMEDIFF(now(),updated_at)) / 60 / 60, 1) as hour'))->get();

		$data2 = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.key' ,'barrels.machine',DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
		->get();

		$response = array(
			'status' => true,
			'datas' => $data,
			'contents' => $data2,
		);
		return Response::json($response);
	}

	public function fetchProcessBarrelMachineContent()
	{
		$data = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.key' ,'barrels.machine',DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
		->get();

		$response = array(
			'status' => true,
			'contents' => $data,
		);
		return Response::json($response);
	}

	public function fetchProcessBarrel()
	{
		$data = DB::table('barrels')
		->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
		->select('barrels.machine', 'barrels.jig', 'barrels.key', DB::raw('SUM(qty) as qty'), 'barrels.status', DB::raw('LEFT(materials.model, 1) as model'))
		->groupBy('barrels.machine', 'barrels.jig', 'barrels.key','barrels.status',DB::raw('LEFT(materials.model, 1)'))
		->orderBy('remark','asc')
		->orderBy('jig','asc')
		->get();

		$barrel_machine = DB::table('barrel_machines')		
		->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'),DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'))
		->get();

		$response = array(
			'status' => true,
			'datas' => $data,
			'machine_stat' => $barrel_machine
		);
		return Response::json($response);
	}

	public function fetchMachine()
	{
		$barrel_machine = DB::table('barrel_machines')		
		->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'), DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'))
		->get();

		$response = array(
			'status' => true,
			'machine_stat' => $barrel_machine
		);
		return Response::json($response);
	}
}