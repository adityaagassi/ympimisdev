<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Response;
use App\FloRepairLog;
use App\FloRepair;
use App\FloDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdditionalController extends Controller
{

	public function indexFluteRepair(){
		return view('additional.flute_repair.index_flute_rpr')->with('page', 'Flute Repair');
	}

	public function indexTarik(){
		return view('additional.flute_repair.tarik')->with('page', 'Flute Repair');
	}

	public function indexSelesai(){
		return view('additional.flute_repair.selesai')->with('page', 'Flute Repair');
	}

	public function indexKembali(){
		return view('additional.flute_repair.kembali')->with('page', 'Flute Repair');		
	}

	public function indexResume(){
		return view('additional.flute_repair.resume', array(
			'title' => 'Flute Repair Resume',
		))->with('page', 'Flute Repair Resume');
	}

	public function fetchTarik(){
		$tarik = FloRepair::where('status','=','repair')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','created_at')
		->get();
		return DataTables::of($tarik)->make(true);
	}

	public function fetchSelesai(){
		$selesai = FloRepair::where('status','=','selesai repair')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','updated_at')
		->get();
		return DataTables::of($selesai)->make(true);
	}

	public function fetchKembali(){
		$selesai = FloRepair::where('status','=','kembali ke warehouse')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status','updated_at')
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
		$status = db::select("select `status`, sum(quantity) as jml from flo_repairs
			GROUP BY `status`");
		$response = array(
			'status' => true,
			'status' => $status,
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
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."' order by model");

		$datas = db::select("select a.model, a.`status`, COALESCE(b.jml,0) as jml from
			(select model.model, `status`.`status` from
			(select distinct model from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
			) model
			cross join
			(select distinct `status` from flo_repair_logs) `status`) a
			left join
			(select m.model, f.`status`, sum(f.quantity) as jml from flo_repair_logs f
			left join materials m on m.material_number = f.material_number
			where DATE_FORMAT(f.created_at,'%Y-%m-%d') = '".$date."'
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
			(select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f) tgl
			cross join
			(select distinct `status` from flo_repair_logs f) `status`) a
			left join
			(select DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl, f.`status`, sum(f.quantity) as jml
			from flo_repair_logs f GROUP BY tgl, f.`status`) b
			on a.tgl = b.tgl and a.`status` = b.`status` 
			ORDER BY a.tgl");

		$tgl = db::select("select distinct DATE_FORMAT(f.created_at,'%Y-%m-%d') as tgl from flo_repair_logs f");

		$response = array(
			'status' => true,
			'datas' => $datas,
			'tgl' => $tgl,
		);
		return Response::json($response);


	}



}
