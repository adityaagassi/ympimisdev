<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Response;
use App\FloRepairLog;
use App\FloRepair;
use App\FloDetail;
use App\MaterialVolume;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdditionalController extends Controller
{

	//Flute Repair
	public function indexFluteRepair(){
		$title = 'Flute Repair';
		return view('additional.flute_repair.index_flute_rpr',  array(
			'title' => $title,
		))->with('page', 'Flute Repair');
	}

	public function indexTarik(){
		$title = 'Flute Repair';
		return view('additional.flute_repair.tarik',  array(
			'title' => $title,
		))->with('page', 'Flute Repair');
	}

	public function indexSelesai(){
		$title = 'Flute Repair';
		return view('additional.flute_repair.selesai',  array(
			'title' => $title,
		))->with('page', 'Flute Repair');
	}

	public function indexKembali(){
		$title = 'Flute Repair';
		return view('additional.flute_repair.kembali',  array(
			'title' => $title,
		))->with('page', 'Flute Repair');		
	}

	public function indexResume(){
		return view('additional.flute_repair.resume', array(
			'title' => 'Flute Repair Resume',
		))->with('page', 'Flute Repair Resume');
	}

	public function fetchTarik(){
		$tarik = FloRepairLog::where('status','=','repair')
		->where('origin_group_code','=','072')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($tarik)->make(true);
	}

	public function fetchSelesai(){
		$selesai = FloRepairLog::where('status','=','selesai repair')
		->where('origin_group_code','=','072')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($selesai)->make(true);
	}

	public function fetchKembali(){
		$selesai = FloRepairLog::where('status','=','kembali ke warehouse')
		->where('origin_group_code','=','072')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($selesai)->make(true);
	}

	public function scanTarik(Request $request){
		$serial_number = $request->get("serialNumber");

		$flo_data = FloDetail::where('serial_number','=',$serial_number)->where('origin_group_code', '=', '041')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at')
		->first();

		if(count($flo_data) > 0){
			try{

				$flo_repair = new FloRepair([
					'serial_number' => $flo_data->serial_number,
					'material_number' => $flo_data->material_number,
					'origin_group_code' => $flo_data->origin_group_code,
					'flo_number' => $flo_data->flo_number,
					'quantity' => $flo_data->quantity,
					'status' => 'repair',
					'packed_at' => $flo_data->created_at
				]);
				$flo_repair->save();

				$log = new FloRepairLog([
					'serial_number' => $flo_data->serial_number,
					'material_number' => $flo_data->material_number,
					'origin_group_code' => $flo_data->origin_group_code,
					'flo_number' => $flo_data->flo_number,
					'quantity' => $flo_data->quantity,
					'status' => 'repair',
					'packed_at' => $flo_data->created_at
				]);
				$log->save();

				$response = array(
					'status' => true,
					'message' => 'Input successfull.',
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
				'message' => 'Serial Number not found',
			);
			return Response::json($response);
		}
	}


	public function scanSelesai(Request $request){
		$serialNumber = $request->get("serialNumber");

		$flo_repair = FloRepair::where('serial_number', '=', $serialNumber)->first();

		try{
			if($flo_repair->status == 'repair'){
				$flo_repair->status = 'selesai repair';
				$flo_repair->save();
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Status invalid.',
				);
				return Response::json($response);
			}

			$log = new FloRepairLog([
				'serial_number' => $flo_repair->serial_number,
				'material_number' => $flo_repair->material_number,
				'origin_group_code' => $flo_repair->origin_group_code,
				'flo_number' => $flo_repair->flo_number,
				'quantity' => $flo_repair->quantity,
				'status' => 'selesai repair',
				'packed_at' => $flo_repair->created_at
			]);
			$log->save();

			$response = array(
				'status' => true,
				'message' => 'Update status successfull.',
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

	public function scanKembali(Request $request){
		$serialNumber = $request->get("serialNumber");
		$flo_repair = FloRepair::where('serial_number', '=', $serialNumber)->first();

		try{
			if($flo_repair->status == 'selesai repair'){
				$flo_repair->status = 'kembali ke warehouse';
				$flo_repair->save();
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Status invalid.',
				);
				return Response::json($response);
			}

			$log = new FloRepairLog([
				'serial_number' => $flo_repair->serial_number,
				'material_number' => $flo_repair->material_number,
				'origin_group_code' => $flo_repair->origin_group_code,
				'flo_number' => $flo_repair->flo_number,
				'quantity' => $flo_repair->quantity,
				'status' => 'kembali ke warehouse',
				'packed_at' => $flo_repair->created_at
			]);
			$log->save();

			$response = array(
				'status' => true,
				'message' => 'Update status successfull.',
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


	public function fetchByStatus(){
		$status = db::select("select `status`, sum(quantity) as jml from flo_repair_logs
			where origin_group_code = '041'
			GROUP BY `status`");

		$sedang = db::select("select `status`, sum(quantity) as jml from flo_repairs
			where `status` = 'repair'
			and origin_group_code = '041'
			GROUP BY `status`");

		$response = array(
			'status' => true,
			'status' => $status,
			'sedang' => $sedang,
		);
		return Response::json($response);

	}

	public function fetchByModel(Request $request){

		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$model = db::select("select distinct model from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '041'
			order by model");

		$datas = db::select("select a.model, a.`status`, COALESCE(b.jml,0) as jml from
			(select model.model, `status`.`status` from
			(select distinct model from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '041'
			) model
			cross join
			(select distinct `status` from flo_repair_logs) `status`) a
			left join
			(select m.model, f.`status`, sum(f.quantity) as jml from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '041'
			GROUP BY m.model, f.`status`) b
			on a.model = b.model and a.`status` = b.`status`
			order by model, `status`");

		$response = array(
			'status' => true,
			'date' => date("d M Y", strtotime($date)),
			'model' => $model,
			'datas' => $datas,
		);
		return Response::json($response);
	}


	public function fetchByDate(){

		$datas = db::select("select a.tgl, a.`status`, COALESCE(b.jml,0) as jml  from
			(select * from
			(select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f
			where f.origin_group_code = '041') tgl
			cross join
			(select distinct `status` from flo_repair_logs f) `status`) a
			left join
			(select DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl, f.`status`, sum(f.quantity) as jml
			from flo_repair_logs f
			where f.origin_group_code = '041'
			GROUP BY tgl, f.`status`) b
			on a.tgl = b.tgl and a.`status` = b.`status` 
			ORDER BY a.tgl");

		$tgl = db::select("select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f
			where f.origin_group_code = '041'");

		$response = array(
			'status' => true,
			'datas' => $datas,
			'tgl' => $tgl,
		);
		return Response::json($response);

	}


	//Recorder Repair
	public function indexRecorderRepair(){
		$title = 'Recorder Repair';

		return view('additional.recorder_repair.index_recorder_rpr',  array(
			'title' => $title,
		))->with('page', 'Recorder Repair');
	}

	public function indexRecorderTarik(){
		$title = 'Recorder Repair';
		return view('additional.recorder_repair.tarik',  array(
			'title' => $title,
		))->with('page', 'Recorder Repair');
	}

	public function indexRecorderSelesai(){
		$title = 'Recorder Repair';
		return view('additional.recorder_repair.selesai',  array(
			'title' => $title,
		))->with('page', 'Recorder Repair');
	}

	public function indexRecorderKembali(){
		$title = 'Recorder Repair';
		return view('additional.recorder_repair.kembali',  array(
			'title' => $title,
		))->with('page', 'Recorder Repair');		
	}

	public function indexRecorderResume(){
		return view('additional.recorder_repair.resume', array(
			'title' => 'Recorder Repair Resume',
		))->with('page', 'Recorder Repair Resume');
	}

	public function fetchRecorderTarik(){
		$tarik = FloRepairLog::where('status','=','repair')
		->where('origin_group_code','=','072')
		->select('material_number','origin_group_code','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($tarik)->make(true);
	}

	public function fetchRecorderSelesai(){
		$tarik = FloRepairLog::where('status','=','selesai repair')
		->where('origin_group_code','=','072')
		->select('material_number','origin_group_code','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($tarik)->make(true);
	}

	public function fetchRecorderKembali(){
		$tarik = FloRepairLog::where('status','=','kembali ke warehouse')
		->where('origin_group_code','=','072')
		->select('material_number','origin_group_code','quantity','packed_at','status','created_at')
		->orderBy('created_at','DESC')
		->get();
		return DataTables::of($tarik)->make(true);
	}

	public function scanRecorderTarik(Request $request){
		$material_number = $request->get("materialNumber");
		$material_volume = MaterialVolume::where('material_number', '=', $material_number)->first();
		
		try{
			$flo_repair = new FloRepair([
				'serial_number' => date("y").date("m").date("d").date("h").date("i").date("s"),
				'material_number' => $material_number,
				'origin_group_code' => '072',
				'flo_number' => '-',
				'quantity' => $material_volume->lot_completion,
				'status' => 'repair',
			]);
			$flo_repair->save();

			$log = new FloRepairLog([
				'serial_number' => date("y").date("m").date("d").date("h").date("i").date("s"),
				'material_number' => $material_number,
				'origin_group_code' => '072',
				'flo_number' => '-',
				'quantity' => $material_volume->lot_completion,
				'status' => 'repair',
			]);
			$log->save();

			$response = array(
				'status' => true,
				'message' => 'Input successfull.',
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

	public function scanRecorderSelesai(Request $request){
		$materialNumber = $request->get("materialNumber");
		$recorder_repair = FloRepair::where('material_number', '=', $materialNumber)->first();

		try{
			if($recorder_repair->status == 'repair'){
				$recorder_repair->status = 'selesai repair';
				$recorder_repair->save();
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Status invalid.',
				);
				return Response::json($response);
			}

			$log = new FloRepairLog([
				'serial_number' => $recorder_repair->serial_number,
				'material_number' => $recorder_repair->material_number,
				'origin_group_code' => $recorder_repair->origin_group_code,
				'flo_number' => $recorder_repair->flo_number,
				'quantity' => $recorder_repair->quantity,
				'status' => 'selesai repair',
			]);
			$log->save();

			$response = array(
				'status' => true,
				'message' => 'Update status successfull.',
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

	public function scanRecorderKembali(Request $request){
		$materialNumber = $request->get("materialNumber");
		$recorder_repair = FloRepair::where('material_number', '=', $materialNumber)->first();

		try{
			if($recorder_repair->status == 'selesai repair'){
				$recorder_repair->status = 'kembali ke warehouse';
				$recorder_repair->save();
			}
			else{
				$response = array(
					'status' => false,
					'message' => 'Status invalid.',
				);
				return Response::json($response);
			}

			$log = new FloRepairLog([
				'serial_number' => $recorder_repair->serial_number,
				'material_number' => $recorder_repair->material_number,
				'origin_group_code' => $recorder_repair->origin_group_code,
				'flo_number' => $recorder_repair->flo_number,
				'quantity' => $recorder_repair->quantity,
				'status' => 'kembali ke warehouse',
			]);
			$log->save();

			$response = array(
				'status' => true,
				'message' => 'Update status successfull.',
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

	public function fetchRecorderByStatus(){
		$status = db::select("select `status`, sum(quantity) as jml from flo_repair_logs
			where origin_group_code = '072'
			GROUP BY `status`");

		$sedang = db::select("select `status`, sum(quantity) as jml from flo_repairs
			where `status` = 'repair'
			and origin_group_code = '072'
			GROUP BY `status`");

		$response = array(
			'status' => true,
			'status' => $status,
			'sedang' => $sedang,
		);
		return Response::json($response);

	}

	public function fetchRecorderByModel(Request $request){

		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$model = db::select("select distinct model from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '072'
			order by model");

		$datas = db::select("select a.model, a.`status`, COALESCE(b.jml,0) as jml from
			(select model.model, `status`.`status` from
			(select distinct model from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '072'
			) model
			cross join
			(select distinct `status` from flo_repair_logs) `status`) a
			left join
			(select m.model, f.`status`, sum(f.quantity) as jml from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			and f.origin_group_code = '072'
			GROUP BY m.model, f.`status`) b
			on a.model = b.model and a.`status` = b.`status`
			order by model, `status`");

		$response = array(
			'status' => true,
			'date' => date("d M Y", strtotime($date)),
			'model' => $model,
			'datas' => $datas,
		);
		return Response::json($response);
	}


	public function fetchRecorderByDate(){

		$datas = db::select("select a.tgl, a.`status`, COALESCE(b.jml,0) as jml  from
			(select * from
			(select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f
			where f.origin_group_code = '072') tgl
			cross join
			(select distinct `status` from flo_repair_logs f) `status`) a
			left join
			(select DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl, f.`status`, sum(f.quantity) as jml
			from flo_repair_logs f
			where f.origin_group_code = '072'
			GROUP BY tgl, f.`status`) b
			on a.tgl = b.tgl and a.`status` = b.`status` 
			ORDER BY a.tgl");

		$tgl = db::select("select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f
			where f.origin_group_code = '072'");

		$response = array(
			'status' => true,
			'datas' => $datas,
			'tgl' => $tgl,
		);
		return Response::json($response);

	}




}
