<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Material;
use App\CodeGenerator;
use App\MaterialVolume;
use App\Flo;
use App\FloDetail;
use App\FloLog;
use App\ContainerSchedule;
use App\ContainerAttachment;
use App\User;
use App\Inventory;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use DataTables;
use Yajra\DataTables\Exception;
use Response;
use File;
use Storage;
use Carbon\Carbon;
use App\StampInventory;
use App\LogProcess;
use App\LogTransaction;
use App\ErrorLog;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class FloController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function index_bi(){
		$flos = Flo::orderBy('flo_number', 'asc')
		->where('flos.status', '=', '0')
		->get();
		return view('flos.flo_bi', array(
			'flos' => $flos,
		))->with('page', 'FLO Band Instrument');
	}

	public function index_ei(){
		$flos = Flo::orderBy('flo_number', 'asc')
		->whereIn('status', ['0', '1'])
		->get();
		return view('flos.flo_ei', array(
			'flos' => $flos
		))->with('page', 'FLO Educational Instrument');
	}

	public function index_delivery(){
		$flos = Flo::orderBy('flo_number', 'asc')
		->whereIn('status', ['1', '2'])
		->get();
		return view('flos.flo_delivery', array(
			'flos' => $flos
		))->with('page', 'FLO Delivery');
	}

	public function index_stuffing(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$flos = Flo::orderBy('flo_number', 'asc')
		->where('status', '=', '2')
		->get();

		$container_schedules = ContainerSchedule::orderBy('shipment_date', 'asc')
		->where('shipment_date', '>=', $first)
		->where('shipment_date', '<=', $now)
        // ->where('shipment_date', '>=', DB::raw('DATE_FORMAT(now(), "%Y-%m-%d")'))
        // ->where('shipment_date', '<=', DB::raw('last_day(now())'))
		->get();

		return view('flos.flo_stuffing', array(
			'flos' => $flos,
			'container_schedules' => $container_schedules,
		))->with('page', 'FLO Stuffing');
	}

	public function index_shipment(){
		return view('flos.flo_shipment')->with('page', 'FLO Shipment');
	}

	public function index_lading(){
		$invoices = Flo::orderBy('invoice_number', 'asc')
		->whereNotNull('invoice_number')
		->whereNull('bl_date')
		->select('invoice_number')
		->distinct()
		->get();

		return view('flos.flo_lading', array(
			'invoices' => $invoices,
		))->with('page', 'FLO Lading');
	}

	public function index_deletion(){
		return view('flos.flo_deletion')->with('page', 'FLO Deletion');
	}

	public function index_detail(){

		$materials = DB::table('materials')->select('material_number', 'material_description')->get();
		$origin_groups = DB::table('origin_groups')->select('origin_groups.origin_group_code', 'origin_groups.origin_group_name')->get();
		$flos = DB::table('flos')->whereIn('flos.status', ['0', '1', 'M', '2'])->select('flos.flo_number')->distinct()->get();
		$statuses = DB::table('statuses')->select('statuses.status_code', 'statuses.status_name')->get();
        // $serial_numbers = DB::table('flo_details')->select('flo_details.serial_number')->distinct()->get();

		return view('flos.flo_detail', array(
			'materials' => $materials,
			'origin_groups' => $origin_groups,
			'flos' => $flos,
			'statuses' => $statuses,
            // 'serial_numbers' => $serial_numbers,
		))->with('page', 'FLO Detail');
	}

	public function destroy_flo_attachment(Request $request){
		$container_attachment = ContainerAttachment::where('file_name', '=', $request->get('id'))->first();
		$filepath = public_path() . $container_attachment->file_path . $container_attachment->file_name;
		File::delete($filepath);
		$container_attachment->forceDelete();

		$response = array(
			'status' => true,
			'message' => 'Photo has been deleted.'
		);
		return Response::json($response);
	}

	public function index_flo_invoice(){
		$invoices = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->whereNotNull('flos.bl_date')
		->select('flos.invoice_number', 'shipment_schedules.st_date', 'shipment_schedules.destination_code', 'destinations.destination_name', 'shipment_schedules.bl_date as plan_bl', 'flos.bl_date as actual_bl')
		->groupBy('flos.invoice_number', 'shipment_schedules.st_date', 'shipment_schedules.destination_code', 'destinations.destination_name', 'shipment_schedules.bl_date', 'flos.bl_date')
		->orderBy('flos.bl_date', 'desc')
		->get();

		return DataTables::of($invoices)
		->addColumn('action', function($invoices){
			return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editConfirmation(id)" id="' . $invoices->invoice_number . '"><i class="fa fa-edit"></i></a>';
		})
		->make(true);
	}

	public function filter_flo_detail(Request $request){
		$flo_detailsTable = DB::table('flo_details')
		->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
		->leftJoin('statuses', 'statuses.status_code', '=', 'flos.status')
		->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
		->leftJoin('materials', 'flos.material_number', '=', 'materials.material_number')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->select('flo_details.id', 'shipment_schedules.sales_order', 'flo_details.flo_number', db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'), 'destinations.destination_shortname', 'materials.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.quantity', 'flo_details.created_at', 'statuses.status_name');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if(strlen($request->get('origin_group')) > 0){
			$flo_detailsTable = $flo_detailsTable->where('materials.origin_group_code', '=', $request->get('origin_group'));
		}

		if(strlen($request->get('material_number')) > 0){
			$flo_detailsTable = $flo_detailsTable->where('shipment_schedules.material_number', '=', $request->get('material_number'));
		}

		if(strlen($request->get('serial_number')) > 0){
			$flo_detailsTable = $flo_detailsTable->where('shipment_schedules.serial_number', '=', $request->get('serial_number'));
		}

		if(strlen($request->get('flo_number')) > 0){
			$flo_detailsTable = $flo_detailsTable->where('flo_details.flo_number', '=', $request->get('flo_number'));
		}

		if(strlen($request->get('status')) > 0){
			$flo_detailsTable = $flo_detailsTable->where('flos.status', '=', $request->get('status'));
		}

		$flo_details = $flo_detailsTable->orderBy('flo_details.created_at', 'desc')->get();

		return DataTables::of($flo_details)
		->addColumn('action', function($flo_details){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

	public function index_flo_detail(Request $request){
		$flo_details = DB::table('flo_details')
		->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
		->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
		->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
		->where('flo_details.flo_number', '=', $request->get('flo_number'))
		->where('flos.status', '=', '0')
		->select('shipment_schedules.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id', 'flo_details.quantity')
		->orderBy('flo_details.id', 'DESC')
		->get();

		return DataTables::of($flo_details)
		->addColumn('action', function($flo_details){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

	public function index_flo(Request $request){
		$flos = DB::table('flos')
		->leftJoin('shipment_schedules', 'flos.shipment_schedule_id','=', 'shipment_schedules.id')
		->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
		->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flos.flo_number')
		->where('flos.status', '=', $request->get('status'))
		->where('flo_logs.status_code', '=', $request->get('status'))
		->whereNull('flos.bl_date');

		if(!empty($request->get('originGroup'))){
			$flos = $flos->whereIn('materials.origin_group_code', $request->get('originGroup'));
		}

		$flos = $flos->select('flos.flo_number', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'materials.material_number', 'materials.material_description', 'flos.actual', 'flos.id', 'flos.invoice_number', 'flos.invoice_number', 'flos.container_id', 'flo_logs.updated_at')
		->get();

		return DataTables::of($flos)
		->addColumn('action', function($flos){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="cancelConfirmation(id)" id="' . $flos->id . '"><i class="glyphicon glyphicon-remove-sign"></i></a>';
		})
		->make(true);
	}

	public function index_flo_container(Request $request){
		// $level = Auth::user()->level_id;
		// $invoices = DB::table('flos')
		// ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		// ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		// ->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
		// ->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'flos.container_id')
		// ->whereNotNull('flos.invoice_number')
		// ->select('container_schedules.container_id', 'container_schedules.container_code', 'destinations.destination_shortname', db::raw('shipment_schedules.st_date as shipment_date'), 'shipment_conditions.shipment_condition_name', 'container_schedules.container_number')
		// ->distinct()
		// ->orderBy('shipment_schedules.st_date', 'desc')
		// ->get();

		$invoices = DB::table('shipment_schedules')
		->leftJoin('flos', 'flos.shipment_schedule_id', 'shipment_schedules.id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'flos.container_id')
		// ->whereNull('flos.bl_date')
		// ->whereRaw('shipment_schedules.id not in (select flos.shipment_schedule_id from flos where flos.container_id is null group by flos.shipment_schedule_id)')
		->select('shipment_schedules.st_date', 'destinations.destination_shortname', 'flos.container_id', 'container_schedules.container_code', 'container_schedules.container_number')
		->whereNotNull('flos.invoice_number')
		->groupBy('shipment_schedules.st_date', 'destinations.destination_shortname', 'flos.container_id', 'container_schedules.container_code', 'container_schedules.container_number')
		->orderBy('shipment_schedules.st_date', 'desc')
		->get();

		// $response = array(
		// 	'status' => true,
		// 	'tes' => $invoices,
		// );
		// return Response::json($response);

		return DataTables::of($invoices)
		->addColumn('action', function($invoices){return '<center><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" onClick="updateConfirmation(id)" id="' . $invoices->container_id . '"><i class="fa fa-upload"></i></a></center>';})
		->make(true);
	}

	public function fetch_flo_lading(Request $request){
		$invoice_number = $request->input('id');
		$flo = Flo::where('invoice_number', '=', $invoice_number)->first();
		$bl_date= date('m/d/Y', strtotime($flo->bl_date));
		$response = array(
			'status' => true,
			'invoice_number' => $invoice_number,
			'bl_date' => $bl_date,
		);
		return Response::json($response);
	}

	public function input_flo_lading(Request $request){
		$bl_date =  date('Y-m-d', strtotime($request->get('bl_date')));
		$flos = Flo::where('invoice_number', '=', $request->get('invoice_number'))->update(['bl_date' => $bl_date, 'status' => 4]);

		$response = array(
			'status' => true,
			'message' => 'BL date for invoice number "' . $request->get('invoice_number') . '" has been updated',
		);
		return Response::json($response);
	}

	public function fetch_flo_container(Request $request){
		$container_id = $request->input('id');
		$container_schedule = ContainerSchedule::where('container_id', '=', $container_id)->first();
		$before_attachments = ContainerAttachment::where('container_id', '=', $container_id)
		->where('file_path', 'like', '%before%')
		->get();
		$process_attachments = ContainerAttachment::where('container_id', '=', $container_id)
		->where('file_path', 'like', '%process%')
		->get();
		$after_attachments = ContainerAttachment::where('container_id', '=', $container_id)
		->where('file_path', 'like', '%after%')
		->get();
		$file_before[] = "";
		$file_process[] = "";
		$file_after[] = "";
		foreach ($before_attachments as $before_attachment) {
			$file_before[] = asset($before_attachment->file_path . $before_attachment->file_name);
		}
		foreach ($process_attachments as $process_attachment) {
			$file_process[] = asset($process_attachment->file_path . $process_attachment->file_name);
		}
		foreach ($after_attachments as $after_attachment) {
			$file_after[] = asset($after_attachment->file_path . $after_attachment->file_name);
		}

		$response = array(
			'status' => true,
			'container_id' => $container_schedule->container_id,
			'container_number' => $container_schedule->container_number,
			'file_before' => $file_before,
			'file_process' => $file_process,
			'file_after' => $file_after,
		);
		return Response::json($response);
	}

	public function update_flo_container(Request $request){

		$id = Auth::id();

		$checks = db::table('flos')->whereRaw('shipment_schedule_id in (select shipment_schedule_id from flos where container_id = "' . $request->get('container_id') . '")')
		->whereNull('container_id')
		->select('flos.flo_number')
		->groupBy('flos.flo_number')
		->get();

		if(count($checks) > 0 ){
			$message = '';
			foreach ($checks as $check) {
				$message .= $check->flo_number.'<br>';
			}
			return response()->json([
				'status' => false,
				'message' => 'This FLO is not loaded:<br>'.$message
			]);
		}

		if($request->get('container_number') != ""){
			$container_schedule = ContainerSchedule::where('container_id', '=', $request->get('container_id'))->first();
			$container_number = $container_schedule->container_number;
			$container_schedule->container_number = $request->get('container_number');
			$container_schedule->save();
			if($container_number == null){
				$check2 = db::table('flos')->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')->where('flos.container_id', '=', $request->get('container_id'))->first();
				self::sendMail($check2->st_date);
			}
		}

		if($request->hasFile('container_before')){
			$files = $request->file('container_before');
			foreach ($files as $file) 
			{
				$data = file_get_contents($file);
				$code_generator = CodeGenerator::where('note','=','container_att')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index)+1;
				$photo_number = "B" . $number;
				$ext = $file->getClientOriginalExtension();
				$filepath = public_path() . "/uploads/containers/before/" . $photo_number . "." . $ext;
				$attachment = new ContainerAttachment([
					'container_id' => $request->get('container_id'),
					'file_name' =>  $photo_number . "." . $ext,
					'file_path' => "/uploads/containers/before/",
					'created_by' => $id,
				]);
				$attachment->save();
				File::put($filepath, $data);
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();
			} 
		}

		if($request->hasFile('container_process')){
			$files = $request->file('container_process');
			foreach ($files as $file) 
			{
				$data = file_get_contents($file);
				$code_generator = CodeGenerator::where('note','=','container_att')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index)+1;
				$photo_number = "P" . $number;
				$ext = $file->getClientOriginalExtension();
				$filepath = public_path() . "/uploads/containers/process/" . $photo_number . "." . $ext;
				$attachment = new ContainerAttachment([
					'container_id' => $request->get('container_id'),
					'file_name' =>  $photo_number . "." . $ext,
					'file_path' => "/uploads/containers/process/",
					'created_by' => $id,
				]);
				$attachment->save();
				File::put($filepath, $data);
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();
			}
		}

		if($request->hasFile('container_after')){
			$files = $request->file('container_after');
			foreach ($files as $file) 
			{
				$data = file_get_contents($file);
				$code_generator = CodeGenerator::where('note','=','container_att')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index)+1;
				$photo_number = "A" . $number;
				$ext = $file->getClientOriginalExtension();
				$filepath = public_path() . "/uploads/containers/after/" . $photo_number . "." . $ext;
				$attachment = new ContainerAttachment([
					'container_id' => $request->get('container_id'),
					'file_name' =>  $photo_number . "." . $ext,
					'file_path' => "/uploads/containers/after/",
					'created_by' => $id,
				]);
				$attachment->save();
				File::put($filepath, $data);
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();
			}
		}

		return response()->json([
			'status' => true,
			'message' => 'Container data has been updated',
		]);
	}

	public function scan_material_number(Request $request){

		$maedaoshi_check = FLo::where('flo_number', '=', 'Maedaoshi' . $request->get('material_number'))
		->where('actual', '>', 0)
		->first();

		if($maedaoshi_check != ""){
			$response = array(
				'status' => false,
				'message' => "There is maedaoshi items, please use menu after maedaoshi if shipment schedule available."
			);
			return Response::json($response);
		}

		if($request->get('ymj') == 'true'){
			$flo = DB::table('flos')
			->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
			->where('shipment_schedules.material_number', '=', $request->get('material_number'))
			->where('shipment_schedules.destination_code', '=', 'Y1000YJ')
			->where('flos.status', '=', '0')
			->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
			->select('flos.flo_number')
			->first();
		}
		else{
			$flo = DB::table('flos')
			->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
			->where('shipment_schedules.material_number', '=', $request->get('material_number'))
			->where('flos.status', '=', '0')
			->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
			->select('flos.flo_number')
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
					'status_code' => 1001,
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
				'status_code' => 1000
			); 
			return Response::json($response);
		}
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

	public function reprint_flo(Request $request)
	{
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
		elseif(Auth::user()->role_code == "S"){
			$printer_name = 'SUPERMAN';
		}
		elseif(Auth::user()->role_code == "OP-WH-Exim"){
			$printer_name = 'FLO Printer LOG';
		}
		elseif(Auth::user()->role_code == "MIS"){
			$printer_name = 'FLO Printer MIS';
		}
		else{
			$response = array(
				'status' => false,
				'message' => "You don't have permission to print FLO"
			);
			return Response::json($response);
		}

		$flo = DB::table('flos')
		->leftJoin('shipment_schedules', 'flos.shipment_schedule_id' , '=', 'shipment_schedules.id')
		->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
		->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
		->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
		->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
		->where('flos.flo_number', '=', $request->get('flo_number_reprint'))
		->whereNull('flos.bl_date')
		->select('flos.flo_number', 'flos.quantity', 'flos.actual', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'shipment_schedules.material_number', 'materials.material_description', 'flos.status')
		->first();

		if($flo != null){
			if($flo->status == '0'){
				try {

					$connector = new WindowsPrintConnector($printer_name);
					$printer = new Printer($connector);

					$printer->feed(2);
					$printer->setUnderline(true);
					$printer->text('FLO:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->barcode($flo->flo_number, Printer::BARCODE_CODE39);
					$printer->setTextSize(3, 1);
					$printer->text($flo->flo_number."\n\n");
					$printer->initialize();

					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->setUnderline(true);
					$printer->text('Destination:');
					$printer->setUnderline(false);
					$printer->feed(1);

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(6, 3);
					$printer->text(strtoupper($flo->destination_shortname."\n\n"));
					$printer->initialize();

					$printer->setUnderline(true);
					$printer->text('Shipment Date:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(4, 2);
					$printer->text(date('d-M-Y', strtotime($flo->st_date))."\n\n");
					$printer->initialize();

					$printer->setUnderline(true);
					$printer->text('By:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(4, 2);
					$printer->text(strtoupper($flo->shipment_condition_name)."\n\n");

					$printer->initialize();
					$printer->setTextSize(2, 2);
					$printer->text("   ".strtoupper($flo->material_number)."\n");
					$printer->text("   ".strtoupper($flo->material_description)."\n");

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
					$printer->text("Max Qty:".$flo->quantity."\n");
					$printer->cut();
					$printer->close();

					return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Band Instrument');
				} 
				catch(\Exception $e){
					return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
				}
			}
			else{

				$flo_details = DB::table('flo_details')->where('flo_number', '=', $request->get('flo_number_reprint'))->select('serial_number')->get();

				foreach ($flo_details as $flo_detail) {
					if($flo_detail->serial_number<>''){
						$lists[] = $flo_detail->serial_number;
					}
				}
				$list = implode(', ', $lists);

				try {

					$connector = new WindowsPrintConnector($printer_name);
					$printer = new Printer($connector);

					$printer->feed(2);
					$printer->setUnderline(true);
					$printer->text('FLO:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->barcode($flo->flo_number, Printer::BARCODE_CODE39);
					$printer->setTextSize(3, 1);
					$printer->text($flo->flo_number."\n\n");
					$printer->initialize();

					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->setUnderline(true);
					$printer->text('Destination:');
					$printer->setUnderline(false);
					$printer->feed(1);

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(6, 3);
					$printer->text(strtoupper($flo->destination_shortname."\n\n"));
					$printer->initialize();

					$printer->setUnderline(true);
					$printer->text('Shipment Date:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(4, 2);
					$printer->text(date('d-M-Y', strtotime($flo->st_date))."\n\n");
					$printer->initialize();

					$printer->setUnderline(true);
					$printer->text('By:');
					$printer->setUnderline(false);
					$printer->feed(1);
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setTextSize(4, 2);
					$printer->text(strtoupper($flo->shipment_condition_name)."\n\n");

					$printer->initialize();
					$printer->setTextSize(2, 2);
					$printer->text("   ".strtoupper($flo->material_number)."\n");
					$printer->text("   ".strtoupper($flo->material_description)."\n");

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
					$printer->text("Max Qty:".$flo->quantity."\n");
					$printer->text("Actual Qty:".$flo->actual."\n");
					$printer->text($list."\n");
					$printer->cut();
					$printer->close();

					return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Band Instrument');
				} 
				catch(\Exception $e){
					return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
				}
			}
		}
		else{
			return back()->with('error', 'FLO number '. $request->get('flo_number') . 'not found.');
		}        
	}

	public function destroy_serial_number(Request $request)
	{
		$flo_detail = FloDetail::find($request->get('id'));
		if($flo_detail->completion == null){
			$flo = Flo::where('flo_number', '=', $flo_detail->flo_number)->first();
			$actual = DB::table('flo_details')
			->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
			->leftJoin('shipment_schedules', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
			->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'shipment_schedules.material_number')
			->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
			->where('flo_details.id', '=', $request->get('id'))
			->select('material_volumes.lot_completion', 'materials.material_number', 'materials.issue_storage_location')
			->first();

			$flo->actual = $flo->actual-$actual->lot_completion;
			$flo->save();

			$flo_detail->forceDelete();

			$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $actual->material_number, 'storage_location' => $actual->issue_storage_location]);
			$inventory->quantity = ($inventory->quantity-$actual->lot_completion);
			$inventory->save();

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

	public function cancel_flo_settlement(Request $request)
	{

		$id = Auth::id();
		$status = $request->get('status')-1;
		$flo = Flo::where('id', '=', $request->get('id'))
		->where('status', '=', $request->get('status'))
		->first();

		if($flo != null){

			$flo->status = $status;

			if($request->get('status') == '2'){
				$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
				$inventoryFSTK->quantity = ($inventoryFSTK->quantity-$flo->actual);
				$inventoryFSTK->save();

				$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => $flo->shipmentschedule->material->issue_storage_location]);
				$inventoryWIP->quantity = ($inventoryWIP->quantity+$flo->actual);
				$inventoryWIP->save();

				// if($flo->transfer != null){
				// 	$log_transaction = new LogTransaction([
				// 		'material_number' => $flo->shipmentschedule->material_number,
				// 		'issue_plant' => '8190',
				// 		'issue_storage_location' => $flo->shipmentschedule->material->issue_storage_location,
				// 		'receive_plant' => '8191',
				// 		'receive_storage_location' => 'FSTK',
				// 		'transaction_code' => 'MB1B',
				// 		'mvt' => '9P2',
				// 		'transaction_date' => date('Y-m-d H:i:s'),
				// 		'qty' => $flo->actual,
				// 		'created_by' => $id
				// 	]);
				// 	$log_transaction->save();
				// 	$flo->transfer = null;
				// }
			}

			if($request->get('status') == '3'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
				$inventory->quantity = ($inventory->quantity+$flo->actual);
				$flo->invoice_number = null;
				$flo->container_id = null;
				$flo->bl_date = null;
				$inventory->save();
			}

			$flo->save();

			$response = array(
				'status' => true,
				'message' => "FLO " . $request->get('flo_number') . " settlement has been canceled.",
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => "FLO " . $request->get('flo_number') . " not found or FLO " . $request->get('flo_number') . " status is invalid.",
			);
			return Response::json($response);
		}
	}

	public function flo_settlement(Request $request)
	{
		$id = Auth::id();
		$status = $request->get('status')-1;
		$flo = Flo::where('flo_number', '=', $request->get('flo_number'))
		->where('status', '=', $status)
		->first();

		$closure = Flo::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
		->leftJoin('materials', 'materials.material_number', '=', 'flos.material_number')
		->where('flo_number', '=', $request->get('flo_number'))
		->where('status', '=', $status)
		->select('materials.material_number', 'flos.flo_number', 'destinations.destination_shortname', 'shipment_conditions.shipment_condition_name', 'materials.material_description', 'flos.actual', 'shipment_schedules.st_date')
		->first();

		if($flo != null){

			$flo->status = $request->get('status');

			if($request->get('status') == '3'){
				$flo->invoice_number = $request->get('invoice_number');
				$flo->container_id = $request->get('container_id');
			}
			$flo->save();

			if($request->get('status') == '2'){
				$inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->material_number, 'storage_location' => 'FSTK']);
				$inventoryFSTK->quantity = ($inventoryFSTK->quantity+$flo->actual);
				$inventoryFSTK->save();

				$inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->material_number, 'storage_location' => $flo->material->issue_storage_location]);
				$inventoryWIP->quantity = ($inventoryWIP->quantity-$flo->actual);
				$inventoryWIP->save();
			}

			if($request->get('status') == '3'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->material_number, 'storage_location' => 'FSTK']);
				$inventory->quantity = ($inventory->quantity-$flo->actual);
				$inventory->save();
			}

			$flo_log = FloLog::updateOrCreate(
				['flo_number' => $request->get('flo_number'), 'status_code' => $request->get('status')],
				['created_by' => $id, 'status_code' => $request->get('status'), 'updated_at' => Carbon::now()]
			);

			if($request->get('type') == 'bi'){
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
				elseif(Auth::user()->role_code == "S"){
					$printer_name = 'SUPERMAN';
				}
				elseif(Auth::user()->role_code == "MIS"){
					$printer_name = 'FLO Printer MIS';
				}
				elseif(Auth::user()->role_code == "OP-WH-Exim"){
					$printer_name = 'FLO Printer LOG';
				}				
				$flo_details = DB::table('flo_details')->where('flo_number', '=', $request->get('flo_number'))->select('serial_number')->get();

				foreach ($flo_details as $flo_detail) {
					if($flo_detail->serial_number<>''){
						$lists[] = $flo_detail->serial_number;
					}
				}
				$list = implode(', ', $lists);

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->feed(2);
				$printer->setUnderline(true);
				$printer->text('FLO:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->barcode(intVal($closure->flo_number), Printer::BARCODE_CODE39);
				$printer->setTextSize(3, 1);
				$printer->text($closure->flo_number."\n\n");
				$printer->initialize();

				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->setUnderline(true);
				$printer->text('Destination:');
				$printer->setUnderline(false);
				$printer->feed(1);

				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(6, 3);
				$printer->text(strtoupper($closure->destination_shortname."\n\n"));
				$printer->initialize();

				$printer->setUnderline(true);
				$printer->text('Shipment Date:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(4, 2);
				$printer->text(date('d-M-Y', strtotime($closure->st_date))."\n\n");
				$printer->initialize();

				$printer->setUnderline(true);
				$printer->text('By:');
				$printer->setUnderline(false);
				$printer->feed(1);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(4, 2);
				$printer->text(strtoupper($closure->shipment_condition_name)."\n\n");

				$printer->initialize();
				$printer->setTextSize(2, 2);
				$printer->text("   ".strtoupper($closure->material_number)."\n");
				$printer->text("   ".strtoupper($closure->material_description)."\n");

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
				$printer->text("Qty:".$closure->actual."\n");
				$printer->text($list."\n");
				$printer->initialize();                   
				$printer->cut();
				$printer->close();
			}

			$response = array(
				'status' => true,
				'message' => "FLO " . $request->get('flo_number') . " has been settled.",
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => "FLO " . $request->get('flo_number') . " not found or FLO " . $request->get('flo_number') . " status is invalid.",
			);
			return Response::json($response);
		}
	}

	public function destroy_flo_deletion(Request $request){
		$flo_detail = FloDetail::find($request->get('id'));
		$material = Material::where('material_number', '=', $flo_detail->material_number)->first();

		$id = Auth::id();

		if($flo_detail->completion != null){
			$log_transaction = new LogTransaction([
				'material_number' => $flo_detail->material_number,
				'issue_plant' => '8190',
				'issue_storage_location' => $material->issue_storage_location,
				'transaction_code' => 'MB1B',
				'mvt' => '102',
				'transaction_date' => date('Y-m-d H:i:s'),
				'qty' => $flo_detail->quantity,
				'created_by' => $id
			]);
			$log_transaction->save();
		}

		if($flo_detail->transfer != null){
			$log_transaction = new LogTransaction([
				'material_number' => $flo_detail->material_number,
				'issue_plant' => '8190',
				'issue_storage_location' => $material->issue_storage_location,
				'receive_plant' => '8191',
				'receive_storage_location' => 'FSTK',
				'transaction_code' => 'MB1B',
				'mvt' => '9P2',
				'transaction_date' => date('Y-m-d H:i:s'),
				'qty' => $flo_detail->quantity,
				'created_by' => $id
			]);
			$log_transaction->save();
		}

		$flo = Flo::where('flo_number', '=', $flo_detail->flo_number)->first();
		if($flo != null){
			if(($flo->actual-$flo_detail->quantity) <= 0){
				$flo->forceDelete();
			}
			else{
				$flo->actual = $flo->actual-$flo_detail->quantity;
				if($flo->status == '1'){
					$flo->quantity = $flo->quantity-$flo_detail->quantity;
				}
				$flo->save();
			}
			$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo_detail->material_number, 'storage_location' => $material->issue_storage_location]);
			$inventory->quantity = ($inventory->quantity-$flo_detail->quantity);
			$inventory->save();
			$flo_detail->forceDelete();
		}
		else{
			$flo_detail->forceDelete();
		}

		$response = array(
			'status' => true,
			'message' => "Item has been deleted.",
		);
		return Response::json($response);
	}

	public function fetch_flo_deletion(){
		$flo_details = FloDetail::leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
		->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        // ->leftJoin('statuses', 'statuses.status_code', '=', 'flos.status')
		->whereIn('flos.status', ['M', '0', '1'])
		->orWhereNull('flos.status')
		->select(
			'flo_details.id',
			'flo_details.flo_number',
			'flo_details.serial_number',
			'materials.material_number',
			'materials.material_description',
			'flo_details.quantity',
			db::raw('if(flo_details.completion is not null, "Uploaded", "-") as completion'),
			db::raw('if(flo_details.transfer is not null, "Uploaded", "-") as transfer'),
			db::raw('if(flos.status is not null, flos.status, "error") as status'),
			'flo_details.created_at'
		)
		->get();

		return DataTables::of($flo_details)
		->addColumn('action', function($flo_details){
			return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="fa fa-trash"></i></a>';
		})
		->make(true);
	}

	public function sendMail($st_date){

		$mail_to = db::table('send_emails')
		->where('remark', '=', 'stuffing')
		->WhereNull('deleted_at')
		->orWhere('remark', '=', 'superman')
		->WhereNull('deleted_at')
		->select('email')
		->get();

		if($st_date == date('Y-m-d')){
			$query = "select shipment_schedules.st_date, stuffings.container_id, destinations.destination_shortname, stuffings.container_number, stuffings.container_name, coalesce(sum(shipment_schedules.quantity),0) as plan, coalesce(if(stuffings.container_number is not null, sum(shipment_schedules.quantity), sum(stuffings.actual)),0) as actual, max(stuffings.created_at) as finished_at from shipment_schedules left join
			(

			select flos.shipment_schedule_id, flos.container_id, container_schedules.container_number, containers.container_name, sum(flos.actual) as actual, max(logs.created_at) as created_at from flos left join container_schedules on container_schedules.container_id = flos.container_id left join containers on containers.container_code = container_schedules.container_code 
			left join 
			(select flo_logs.flo_number, flo_logs.created_at from flo_logs where flo_logs.status_code = 3) as logs on logs.flo_number = flos.flo_number where flos.`status` in (3,4)
			group by flos.shipment_schedule_id, flos.container_id, containers.container_name, container_schedules.container_number

			) as stuffings on stuffings.shipment_schedule_id = shipment_schedules.id
			left join destinations on destinations.destination_code = shipment_schedules.destination_code
			where shipment_schedules.st_date = date(now())
			group by shipment_schedules.st_date, stuffings.container_id, stuffings.container_number, destinations.destination_shortname, stuffings.container_name
			order by finished_at desc";

			$stuffings = db::select($query);

			if($stuffings != null){
				Mail::to($mail_to)->send(new SendEmail($stuffings, 'stuffing'));
			}
		}
	}
}