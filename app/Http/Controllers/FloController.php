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
// use App\StampInventory;
// use App\LogProcess;

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

		return view('flos.flo_deletion', array(
			'flo_details' => $flo_details,
		))->with('page', 'FLO Deletion');
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
		$level = Auth::user()->level_id;
		$invoices = DB::table('flos')
		->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
		->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'flos.container_id')
		->whereNotNull('flos.invoice_number')
		->select('container_schedules.container_id', 'container_schedules.container_code', 'destinations.destination_shortname', 'container_schedules.shipment_date', 'shipment_conditions.shipment_condition_name', 'container_schedules.container_number')
		->distinct()
		->get();

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

		if($request->get('container_number') != ""){
			$container_schedule = ContainerSchedule::where('container_id', '=', $request->get('container_id'))->first();
			$container_schedule->container_number = $request->get('container_number');
			$container_schedule->save();
		}

		if($request->hasFile('container_before')){
			$files = $request->file('container_before');
			foreach ($files as $file) 
			{
				$data = file_get_contents($file);
				$code_generator = CodeGenerator::where('note','=','container')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
				$photo_number = $code_generator->prefix . $number+1;
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
				$code_generator = CodeGenerator::where('note','=','container')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
				$photo_number = $code_generator->prefix . $number+1;
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
				$code_generator = CodeGenerator::where('note','=','container')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index)+1;
				$photo_number = $code_generator->prefix . $number;
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
			'message' => 'Container data has been updated',
		]);

	}

	public function scan_material_number(Request $request){
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

		if($request->get('type') == 'pd'){
			$maedaoshi_check = FLoDetail::where('material_number', '=', $request->get('material_number'))
			->where('flo_number', 'like', 'Maedaoshi%')
			->first();
			if($maedaoshi_check != ""){
				$response = array(
					'status' => false,
					'message' => "This item is maedaoshi, please use menu after maedaoshi.",
					'TES' => $maedaoshi_check
				);
				return Response::json($response);
			}
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
				// $log_process = new LogProcess([
				// 	'process_code' => '5',
				// 	'serial_number' => $request->get('serialNumber'),
				// 	'model' => $material->model,
				// 	'manpower' => 2,
				// 	'quantity' => 1,
				// 	'created_by' => $id
				// ]);
				// $log_process->save();

				// $inventory_stamp = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
				// ->where('model', '=', $material->model)
				// ->first();
				// $inventory_stamp->forceDelete();

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
				$flo_detail->save();

				$flo = new Flo([
					'flo_number' => $flo_number,
					'shipment_schedule_id' => $shipment_schedule->id,
					'material_number' => $request->get('material_number'),
					'quantity' => $shipment_schedule->flo_quantity,
					'actual' => $actual,
					'created_by' => $id
				]);
				$flo->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				$inventory->save();

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
				$flo_detail = new FloDetail([
					'serial_number' =>  $serial_number,
					'material_number' => $request->get('material_number'),
					'origin_group_code' => $material->origin_group_code,
					'flo_number' => $request->get('flo_number'),
					'quantity' => $actual,
					'created_by' => $id
				]);
				$flo_detail->save();

				$flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
				$flo->actual = $flo->actual+$actual;
				$flo->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$actual);
				$inventory->save();

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
		->select('flos.flo_number', 'flos.quantity', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'shipment_schedules.material_number', 'materials.material_description')
		->first();

		if($flo != null){
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
		$status = $request->get('status')-1;
		$flo = Flo::where('id', '=', $request->get('id'))
		->where('status', '=', $request->get('status'))
		->first();

		if($flo != null){

			$flo->status = $status;
			$flo->save();

			if($request->get('status') == '2'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
				$inventory->quantity = ($inventory->quantity-$flo->actual);
				$inventory->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => $flo->shipmentschedule->material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity+$flo->actual);
				$inventory->save();
			}

			if($request->get('status') == '3'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
				$inventory->quantity = ($inventory->quantity+$flo->actual);
				$inventory->save();
			}

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
		$flo = Flo::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
		->leftJoin('materials', 'materials.material_number', '=', 'flos.material_number')
		->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
		->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
		->where('flo_number', '=', $request->get('flo_number'))
		->where('status', '=', $status)
		->first();

		if($flo != null){

			$flo->status = $request->get('status');

			if($request->get('status') == '3'){
				$flo->invoice_number = $request->get('invoice_number');
				$flo->container_id = $request->get('container_id');
			}
			$flo->save();

			if($request->get('status') == '2'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
				$inventory->quantity = ($inventory->quantity+$flo->actual);
				$inventory->save();

				$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => $flo->shipmentschedule->material->issue_storage_location]);
				$inventory->quantity = ($inventory->quantity-$flo->actual);
				$inventory->save();
			}

			if($request->get('status') == '3'){
				$inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
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
				$printer->barcode(intVal($request->get('flo_number')), Printer::BARCODE_CODE39);
				$printer->setTextSize(3, 1);
				$printer->text($request->get('flo_number')."\n\n");
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
				$printer->text("Qty Actual:".$flo->actual."\n"); 
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
				$printer->text($list."\n"); 
				$printer->feed(2);
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

	public function destroy_flo_deletion($id){
		$flo_detail = FloDetail::find($id);
		$material = Material::where('material_number', '=', $flo_detail->material_number)->first();

		$flo = Flo::where('flo_number', '=', $flo_detail->flo_number)->first();
		if($flo != null){
			$flo->actual = $flo->actual-$flo_detail->quantity;
			$flo->save();
		}

		$inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo_detail->material_number, 'storage_location' => $material->issue_storage_location]);
		$inventory->quantity = ($inventory->quantity-$flo_detail->quantity);
		$inventory->save();

		$flo_detail->forceDelete();
		return redirect('/index/flo_view/deletion')->with('status', 'Material has been deleted.');
	}
}