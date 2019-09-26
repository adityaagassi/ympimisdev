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

		$flo_datas = FloDetail::where('serial_number','=',$serial_number)->where('origin_group_code', '=', '041')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at')
		->first();

		if(count($flo_datas) > 0){
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

		$flo_data = FloRepair::where('serial_number','=',$serialNumber)
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at')
		->get();

		if(count($flo_data) > 0){
			try{
				$update_flo_repair = FloRepair::where('serial_number', '=', $serialNumber)->update([
					'status' => 'selesai repair'
				]);
				foreach ($flo_data as $data) {
					$log = new FloRepairLog([
						'serial_number' => $data->serial_number,
						'material_number' => $data->material_number,
						'origin_group_code' => $data->origin_group_code,
						'flo_number' => $data->flo_number,
						'quantity' => $data->quantity,
						'status' => 'selesai repair',
						'packed_at' => $data->created_at
					]);
					$log->save();
				}

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
		}else{
			$response = array(
				'status' => false,
				'message' => 'Serial Number not found',
			);
			return Response::json($response);
		}

	}

	public function scanKembali(Request $request){

		if($request->get("serialNumber") != null){
			$serialNumber = $request->get("serialNumber");

			$flo_data = FloRepair::where('serial_number','=',$serialNumber)
			->where('status','=','selesai repair')
			->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at','status')
			->get();
		}else if($request->get("floNumber") != null){
			$floNumber = $request->get("floNumber");

			$row = FloRepair::where('flo_number','=',$floNumber)
			->where('status','=','repair')
			->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at','status')
			->get()->count();

			if($row == 0){
				$flo_data = FloRepair::where('flo_number','=',$floNumber)
				->where('status','=','selesai repair')
				->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at','status')
				->get();
			}else{
				$response = array(
					'status' => false,
					'message' => 'FLFG is still being repaired',
				);
				return Response::json($response);
			}
		}


		if(count($flo_data) > 0){
			try{

				if($request->get("serialNumber") != null){
					$update_flo_repair = FloRepair::where('serial_number', '=', $serialNumber)->update([
						'status' => 'kembali ke warehouse'
					]);
					foreach ($flo_data as $data) {
						$log = new FloRepairLog([
							'serial_number' => $data->serial_number,
							'material_number' => $data->material_number,
							'origin_group_code' => $data->origin_group_code,
							'flo_number' => $data->flo_number,
							'quantity' => $data->quantity,
							'status' => 'kembali ke warehouse',
							'packed_at' => $data->created_at
						]);
						$log->save();
					}

				}else if($request->get("floNumber") != null){
					$update_flo_repair = FloRepair::where('flo_number', '=', $floNumber)->update([
						'status' => 'kembali ke warehouse'
					]);
					foreach ($flo_data as $data) {
						$log = new FloRepairLog([
							'serial_number' => $data->serial_number,
							'material_number' => $data->material_number,
							'origin_group_code' => $data->origin_group_code,
							'flo_number' => $data->flo_number,
							'quantity' => $data->quantity,
							'status' => 'kembali ke warehouse',
							'packed_at' => $data->created_at
						]);
						$log->save();
					}

				}




				
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
		}else{
			$response = array(
				'status' => false,
				'message' => 'Serial or FLO Number invalid',
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






}
