<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Response;
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

	public function fetchTarik(){
		$tarik = FloRepair::where('status','=','repair')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status')
		->get();

		return DataTables::of($tarik)->make(true);
	}

	public function fetchSelesai(){
		$selesai = FloRepair::where('status','=','selesai repair')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status')
		->get();

		return DataTables::of($selesai)->make(true);
	}

	public function fetchKembali(){
		$selesai = FloRepair::where('status','=','kembali')
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','packed_at','status')
		->get();

		return DataTables::of($selesai)->make(true);
	}

	public function scanTarik(Request $request){
		$flo_number = $request->get("flo_number");

		$flo_datas = FloDetail::where('flo_number','=',$flo_number)
		->select('serial_number','material_number','origin_group_code','flo_number','quantity','created_at')
		->get();

		if(count($flo_datas) > 0){
			try{
				foreach ($flo_datas as $flo_data) {
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
				}
				$response = array(
					'status' => true,
					'message' => 'Input material successfull.',
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
				'message' => 'FLO Number not found',
			);
			return Response::json($response);

		}
	}






}
