<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\EmployeeSync;
use App\Inventory;
use App\ReedInjectionOrder;
use App\ReedInjectionOrderList;
use App\ReedInjectionOrderLog;

use App\ReedLaserOrder;
use App\ReedLaserOrderList;
use App\ReedLaserOrderLog;



use App\ReedMasterChecksheet;

use Carbon\Carbon;
use Response;
use DataTables;

class ReedSyntheticController extends Controller{


	public function __construct(){
		$this->middleware('auth');
	}

	public function indexReed(){
		$title = "Injection Reed";
		$title_jp = "??";

		return view('reed_synthetic.index_reed', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Injection Reed')->with('head', 'Injection Reed');
	}

	public function indexInjectionVerification(){
		$title = "Injection Verification";
		$title_jp = "??";

		return view('reed_synthetic.injection.injection_verification', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Injection Reed')->with('head', 'Injection Reed');
	}

	public function indexMoldingVerification(){
		$title = "Setup Molding Verification";
		$title_jp = "??";

		return view('reed_synthetic.molding.molding_verification', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Setup Molding Verification')->with('head', 'Setup Molding Verification');
	}

	public function indexInjectionDelivery(){
		$title = "After Injection Delivery";
		$title_jp = "??";

		return view('reed_synthetic.injection.after_injection_delivery', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Setup Molding Verification')->with('head', 'Setup Molding Verification');
	}

	public function indexLaserVerification(){
		$title = "Laser Marking Verification";
		$title_jp = "??";

		return view('reed_synthetic.laser.laser_verification', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Laser Marking Verification')->with('head', 'Laser Marking Verification');
	}

	public function indexTrimmingVerification(){
		$title = "Annealing Verification";
		$title_jp = "??";

		return view('reed_synthetic.laser.trimming_verification', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Annealing Verification')->with('head', 'Annealing Verification');
	}

	public function indexAnnealingVerification(){
		$title = "Annealing Verification";
		$title_jp = "??";

		return view('reed_synthetic.laser.annealing_verification', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Annealing Verification')->with('head', 'Annealing Verification');
	}

	public function fetchInjectionPickingList(Request $request){

		$kanban = $request->get('kanban');
		$location = $request->get('location');
		$process = $request->get('proses');

		$order = ReedInjectionOrder::where('kanban', $kanban)->first();

		if($order){
			if(strtoupper($location) == 'MOLDING'){
				if($order->setup_molding == 1){
					$response = array(
						'status' => false,
						'message' => 'Molding telah di setup'				
					);
					return Response::json($response);	
				}
			}

			if($order->remark == '0'){
				$data = ReedInjectionOrderList::where('order_id', $order->id)
				->where('location', strtoupper($location))
				->get();

				$response = array(
					'status' => true,
					'order' => $order,
					'data' => $data
				);
				return Response::json($response);	

			}elseif ($order->remark == '1') {
				$response = array(
					'status' => false,
					'message' => 'Proses injekse telah dilakukan'
				);
				return Response::json($response);

			}elseif ($order->remark == '2') {
				$material_number = substr($kanban, 4, 7);

				$checksheet = ReedMasterChecksheet::where('material_number', $material_number)
				->where('process', $process)
				->get();

				if(count($checksheet) > 0){

					$last_id;
					try {
						$new_order = new ReedInjectionOrder([
							'kanban' => $kanban,
							'material_number' => $material_number,
							'material_description' => $checksheet[0]->material_description,
							'quantity' => $checksheet[0]->lot_kanban,
							'hako' => ceil($checksheet[0]->lot_kanban / $checksheet[0]->lot_hako),
							'created_by' => Auth::id()
						]);
						$new_order->save();
						$last_id = $new_order->id;

					} catch (Exception $e) {
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}

					for ($i=0; $i < count($checksheet); $i++) { 
						try {
							$order_list = new ReedInjectionOrderList([
								'order_id' => $last_id,
								'kanban' => $kanban,
								'material_number' => $checksheet[$i]->material_number,
								'material_description' => $checksheet[$i]->material_description,
								'picking_list' => $checksheet[$i]->picking_list,
								'picking_description' => $checksheet[$i]->picking_description,
								'location' => $checksheet[$i]->location,
								'quantity' => $checksheet[$i]->quantity,
								'created_by' => Auth::id()
							]);
							$order_list->save();

						} catch (Exception $e) {
							$response = array(
								'status' => false,
								'message' => $e->getMessage(),
							);
							return Response::json($response);
						}
					}

					$data = ReedInjectionOrderList::where('order_id', $last_id)
					->where('location', strtoupper($location))
					->get();

					$response = array(
						'status' => true,
						'order' => $new_order,
						'data' => $data
					);
					return Response::json($response);	
				}else{
					$response = array(
						'status' => false,
						'message' => 'Master kanban tidak ditemukan'
					);
					return Response::json($response);
				}	
			}

			
		}else{
			$material_number = substr($kanban, 4, 7);

			$checksheet = ReedMasterChecksheet::where('material_number', $material_number)
			->where('process', $process)
			->get();

			if(count($checksheet) > 0){

				$last_id;
				try {
					$new_order = new ReedInjectionOrder([
						'kanban' => $kanban,
						'material_number' => $material_number,
						'material_description' => $checksheet[0]->material_description,
						'quantity' => $checksheet[0]->lot_kanban,
						'hako' => ceil($checksheet[0]->lot_kanban / $checksheet[0]->lot_hako),
						'created_by' => Auth::id()
					]);
					$new_order->save();
					$last_id = $new_order->id;

				} catch (Exception $e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}

				for ($i=0; $i < count($checksheet); $i++) { 
					try {
						$order_list = new ReedInjectionOrderList([
							'order_id' => $last_id,
							'kanban' => $kanban,
							'material_number' => $checksheet[$i]->material_number,
							'material_description' => $checksheet[$i]->material_description,
							'picking_list' => $checksheet[$i]->picking_list,
							'picking_description' => $checksheet[$i]->picking_description,
							'location' => $checksheet[$i]->location,
							'quantity' => $checksheet[$i]->quantity,
							'created_by' => Auth::id()
						]);
						$order_list->save();
						
					} catch (Exception $e) {
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}

				$data = ReedInjectionOrderList::where('order_id', $last_id)
				->where('location', strtoupper($location))
				->get();

				$response = array(
					'status' => true,
					'order' => $new_order,
					'data' => $data
				);
				return Response::json($response);	
			}else{
				$response = array(
					'status' => false,
					'message' => 'Master kanban tidak ditemukan'
				);
				return Response::json($response);
			}	
		}
	}

	public function fetchLaserPickingList(Request $request){
		
		$kanban = $request->get('kanban');
		$location = $request->get('location');
		$process = $request->get('proses');

		$order = ReedLaserOrder::where('kanban', $kanban)->first();

		if($order){

			if($order->remark == '0'){
				$data = ReedLaserOrderList::where('order_id', $order->id)
				->where('location', strtoupper($location))
				->get();

				$response = array(
					'status' => true,
					'order' => $order,
					'data' => $data
				);
				return Response::json($response);
			}elseif ($order->remark == '1') {
				$material_number = substr($kanban, 4, 7);

				$checksheet = ReedMasterChecksheet::where('material_number', $material_number)
				->where('process', $process)
				->get();

				if(count($checksheet) > 0){

					$last_id;
					try {
						$new_order = new ReedLaserOrder([
							'kanban' => $kanban,
							'material_number' => $material_number,
							'material_description' => $checksheet[0]->material_description,
							'quantity' => $checksheet[0]->lot_kanban,
							'hako' => ceil($checksheet[0]->lot_kanban / $checksheet[0]->lot_hako),
							'created_by' => Auth::id()
						]);
						$new_order->save();
						$last_id = $new_order->id;

					} catch (Exception $e) {
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}

					for ($i=0; $i < count($checksheet); $i++) { 
						try {
							$order_list = new ReedLaserOrderList([
								'order_id' => $last_id,
								'kanban' => $kanban,
								'material_number' => $checksheet[$i]->material_picking,
								'material_description' => $checksheet[$i]->material_description,
								'picking_list' => $checksheet[$i]->picking_list,
								'picking_description' => $checksheet[$i]->picking_description,
								'location' => $checksheet[$i]->location,
								'quantity' => $checksheet[$i]->quantity,
								'created_by' => Auth::id()
							]);
							$order_list->save();

						} catch (Exception $e) {
							$response = array(
								'status' => false,
								'message' => $e->getMessage(),
							);
							return Response::json($response);
						}
					}

					$data = ReedLaserOrderList::where('order_id', $last_id)
					->where('location', strtoupper($location))
					->get();

					$response = array(
						'status' => true,
						'order' => $new_order,
						'data' => $data
					);
					return Response::json($response);	
				}else{
					$response = array(
						'status' => false,
						'message' => 'Master kanban tidak ditemukan'
					);
					return Response::json($response);
				}	
			}

			// else{
			// 	$response = array(
			// 		'status' => false,
			// 		'message' => 'Proses laser telah dilakukan'
			// 	);
			// 	return Response::json($response);
			// }



		}else{
			$material_number = substr($kanban, 4, 7);
			$checksheet = ReedMasterChecksheet::where('material_number', $material_number)->get();

			if(count($checksheet) > 0){

				$last_id;
				try {
					$new_order = new ReedLaserOrder([
						'kanban' => $kanban,
						'material_number' => $material_number,
						'material_description' => $checksheet[0]->material_description,
						'quantity' => $checksheet[0]->lot_kanban,
						'hako' => ceil($checksheet[0]->lot_kanban / $checksheet[0]->lot_hako),
						'created_by' => Auth::id()
					]);
					$new_order->save();
					$last_id = $new_order->id;

				} catch (Exception $e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}

				for ($i=0; $i < count($checksheet); $i++) { 
					try {
						$order_list = new ReedLaserOrderList([
							'order_id' => $last_id,
							'kanban' => $kanban,
							'material_number' => $checksheet[$i]->material_picking,
							'material_description' => $checksheet[$i]->material_description,
							'picking_list' => $checksheet[$i]->picking_list,
							'picking_description' => $checksheet[$i]->picking_description,
							'location' => $checksheet[$i]->location,
							'quantity' => $checksheet[$i]->quantity,
							'created_by' => Auth::id()
						]);
						$order_list->save();
						
					} catch (Exception $e) {
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}

				$data = ReedLaserOrderList::where('order_id', $last_id)
				->where('location', strtoupper($location))
				->get();

				$response = array(
					'status' => true,
					'order' => $new_order,
					'data' => $data
				);
				return Response::json($response);	
			}else{
				$response = array(
					'status' => false,
					'message' => 'Master kanban tidak ditemukan'
				);
				return Response::json($response);
			}	
		}
	}

	public function fetchInjectionDelivery(Request $request){

		$kanban = $request->get('kanban');
		$location = $request->get('location');

		$storage_location = substr($kanban, 0, 4);
		$material_number = substr($kanban, 4, 7);

		$order = ReedInjectionOrder::where('kanban', $kanban)
		->where('remark', 1)
		->first();

		if($order){
			$inventory = Inventory::where('plant', '8090')
			->where('material_number', $material_number)
			->where('storage_location', $storage_location)
			->first();

			$response = array(
				'status' => true,
				'order' => $order,
				'inventory' => $inventory,
				'storage_location' => $storage_location
			);
			return Response::json($response);	
		}else{
			$response = array(
				'status' => false,
				'message' => 'Kanban finish injection tidak ditemukan'
			);
			return Response::json($response);
		}

	}

	public function fetchUpdateInjectionDelivery(Request $request){

		$kanban = $request->get('kanban');
		$location = $request->get('location');
		$order_id = $request->get('order_id');

		$storage_location = substr($kanban, 0, 4);
		$material_number = substr($kanban, 4, 7);

		$order = ReedInjectionOrder::where('id', $order_id)->first();

		if($order){
			$inventory = Inventory::where('plant', '8190')
			->where('material_number', $material_number)
			->where('storage_location', $storage_location)
			->first();

			$response = array(
				'status' => true,
				'order' => $order,
				'inventory' => $inventory,
				'storage_location' => $storage_location
			);
			return Response::json($response);	
		}else{
			$response = array(
				'status' => false,
				'message' => 'Kanban finish injection tidak ditemukan'
			);
			return Response::json($response);
		}

	}

	public function scanReedOperator(Request $request){

		$employee_id = $request->get('employee_id');
		$employee_sync = EmployeeSync::where('employee_id', '=', $employee_id)->first();

		if($employee_sync == ""){
			$response = array(
				'status' => false,
				'message' => "ID karyawan tidak ditemukan"
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'employee' => $employee_sync
		);
		return Response::json($response);
	}

	public function scanInjectionPicking(Request $request){
		$qr_item = $request->get('qr_item');
		$order_id = $request->get('order_id');
		$location = $request->get('location');
		$employee_id = $request->get('employee_id');


		if(str_contains(strtoupper($qr_item), 'HAKO') || str_contains(strtoupper($qr_item), 'MOLDING')){
			$data = explode('-', $qr_item);

			$picking_list = $data[0];
			$material_number = $data[1];

			$order_list = ReedInjectionOrderList::where('order_id', $order_id)
			->where('picking_list', strtoupper($picking_list))
			->where('material_number', strtoupper($material_number))
			->where('location', strtoupper($location))
			->first();

			if($order_list){

				if($order_list->actual_quantity == $order_list->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quanity sudah terpenuhi'
					);
					return Response::json($response);
				}else{
					$order_list->actual_quantity = $order_list->actual_quantity + 1;
					$order_list->picked_by = strtoupper($employee_id);
					$order_list->picked_at = date('Y-m-d H:i:s');

					$log = new ReedInjectionOrderLog([
						'order_id' => $order_list->order_id,
						'kanban' => $order_list->kanban,
						'material_number' => $order_list->material_number,
						'material_description' => $order_list->material_description,
						'picking_list' => $order_list->picking_list,
						'picking_description' => $order_list->picking_description,
						'location' => $order_list->location,
						'quantity' => 1,
						'picked_by' => strtoupper($employee_id),
						'picked_at' => date('Y-m-d H:i:s'),
						'created_by' => Auth::id()
					]);

					try {
						DB::transaction(function() use ($order_list, $log){
							$order_list->save();
							$log->save();
						});	
						
						$response = array(
							'status' => true,
							'message' => 'Verifikasi Berhasil'
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
			}else{
				$response = array(
					'status' => false,
					'message' => ucwords('Pengambilan '.$picking_list.' Salah')
				);
				return Response::json($response);	
			}

		}else{
			$remark = null;
			if(str_contains(strtoupper($qr_item), 'INJEKSI')){
				$remark = $qr_item;
				$qr_item = 'MESIN INJEKSI';
			}

			$order_list = ReedInjectionOrderList::where('order_id', $order_id)
			->where('picking_list', strtoupper($qr_item))
			->where('location', strtoupper($location))
			->first();

			if($order_list){

				if($order_list->actual_quantity == $order_list->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quanity sudah terpenuhi'
					);
					return Response::json($response);
				}else{
					$order_list->actual_quantity = $order_list->actual_quantity + 1;
					$order_list->picked_by = strtoupper($employee_id);
					$order_list->picked_at = date('Y-m-d H:i:s');
					$order_list->remark = $remark;

					$log = new ReedInjectionOrderLog([
						'order_id' => $order_list->order_id,
						'kanban' => $order_list->kanban,
						'material_number' => $order_list->material_number,
						'material_description' => $order_list->material_description,
						'picking_list' => $order_list->picking_list,
						'picking_description' => $order_list->picking_description,
						'location' => $order_list->location,
						'quantity' => 1,
						'remark' => $remark,
						'picked_by' => strtoupper($employee_id),
						'picked_at' => date('Y-m-d H:i:s'),
						'created_by' => Auth::id()
					]);

					try {
						DB::transaction(function() use ($order_list, $log){
							$order_list->save();
							$log->save();
						});	

						$response = array(
							'status' => true,
							'message' => 'Verifikasi Berhasil'
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
			}else{
				$response = array(
					'status' => false,
					'message' => 'Pengambilan Salah'
				);
				return Response::json($response);	
			}

		}		
	}

	public function scanLaserPicking(Request $request){
		$qr_item = $request->get('qr_item');
		$order_id = $request->get('order_id');
		$location = $request->get('location');
		$employee_id = $request->get('employee_id');


		if(str_contains(strtoupper($qr_item), 'HAKO') || str_contains(strtoupper($qr_item), 'LASER' || str_contains(strtoupper($qr_item), 'MOLDING')){
			$data = explode('-', $qr_item);

			$picking_list = $data[0];
			$material_number = $data[1];

			if(strtoupper($picking_list) == 'LASER'){
				$picking_list = 'APLIKASI LASER';
			}

			$order_list = ReedLaserOrderList::where('order_id', $order_id)
			->where('picking_list', strtoupper($picking_list))
			->where('material_number', strtoupper($material_number))
			->where('location', strtoupper($location))
			->first();

			// dd($order_id.'#'.$picking_list.'#'.$material_number.'#'.$location);

			if($order_list){

				if($order_list->actual_quantity == $order_list->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quanity sudah terpenuhi'
					);
					return Response::json($response);
				}else{
					$order_list->actual_quantity = $order_list->actual_quantity + 1;
					$order_list->picked_by = strtoupper($employee_id);
					$order_list->picked_at = date('Y-m-d H:i:s');

					$log = new ReedLaserOrderLog([
						'order_id' => $order_list->order_id,
						'kanban' => $order_list->kanban,
						'material_number' => $order_list->material_number,
						'material_description' => $order_list->material_description,
						'picking_list' => $order_list->picking_list,
						'picking_description' => $order_list->picking_description,
						'location' => $order_list->location,
						'quantity' => 1,
						'picked_by' => strtoupper($employee_id),
						'picked_at' => date('Y-m-d H:i:s'),
						'created_by' => Auth::id()
					]);

					try {
						DB::transaction(function() use ($order_list, $log){
							$order_list->save();
							$log->save();
						});	
						
						$response = array(
							'status' => true,
							'message' => 'Verifikasi Berhasil'
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
			}else{
				$response = array(
					'status' => false,
					'message' => ucwords('Pengambilan '.$picking_list.' Salah')
				);
				return Response::json($response);	
			}

		}else{
			$remark = null;
			if(str_contains(strtoupper($qr_item), 'INJEKSI')){
				$remark = $qr_item;
				$qr_item = 'MESIN INJEKSI';
			}

			$order_list = ReedLaserOrderList::where('order_id', $order_id)
			->where('picking_list', strtoupper($qr_item))
			->where('location', strtoupper($location))
			->first();

			if($order_list){

				if($order_list->actual_quantity == $order_list->quantity){
					$response = array(
						'status' => false,
						'message' => 'Quanity sudah terpenuhi'
					);
					return Response::json($response);
				}else{
					$order_list->actual_quantity = $order_list->actual_quantity + 1;
					$order_list->picked_by = strtoupper($employee_id);
					$order_list->picked_at = date('Y-m-d H:i:s');
					$order_list->remark = $remark;

					$log = new ReedLaserOrderLog([
						'order_id' => $order_list->order_id,
						'kanban' => $order_list->kanban,
						'material_number' => $order_list->material_number,
						'material_description' => $order_list->material_description,
						'picking_list' => $order_list->picking_list,
						'picking_description' => $order_list->picking_description,
						'location' => $order_list->location,
						'quantity' => 1,
						'remark' => $remark,
						'picked_by' => strtoupper($employee_id),
						'picked_at' => date('Y-m-d H:i:s'),
						'created_by' => Auth::id()
					]);

					try {
						DB::transaction(function() use ($order_list, $log){
							$order_list->save();
							$log->save();
						});	

						$response = array(
							'status' => true,
							'message' => 'Verifikasi Berhasil'
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
			}else{
				$response = array(
					'status' => false,
					'message' => 'Pengambilan Salah'
				);
				return Response::json($response);	
			}

		}		
	}

	public function fetchStartInjection(Request $request){
		$id = $request->get('order_id');
		$employee_id = $request->get('employee_id');

		try {
			$order = ReedInjectionOrder::where('id', $id)
			->update([
				'operator_injection_id' => strtoupper($employee_id),
				'start_injection' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Proses Injeksi berhasil dimulai',
				'start' => date('Y-m-d H:i:s')
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

	public function fetchFinishInjection(Request $request){
		$id = $request->get('order_id');
		$employee_id = $request->get('employee_id');

		try {
			$order = ReedInjectionOrder::where('id', $id)
			->update([
				'operator_injection_id' => strtoupper($employee_id),
				'finish_injection' => date('Y-m-d H:i:s'),
				'remark' => 1
			]);

			$response = array(
				'status' => true,
				'message' => 'Proses Injeksi berhasil diakhiri'
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

	public function fetchStartLaser(Request $request){
		$id = $request->get('order_id');
		$employee_id = $request->get('employee_id');

		try {
			$order = ReedLaserOrder::where('id', $id)
			->update([
				'operator_laser_id' => strtoupper($employee_id),
				'start_laser' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Proses Laser berhasil dimulai',
				'start' => date('Y-m-d H:i:s')
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

	public function fetchFinishLaser(Request $request){
		$id = $request->get('order_id');
		$employee_id = $request->get('employee_id');

		try {
			$order = ReedLaserOrder::where('id', $id)
			->update([
				'operator_laser_id' => strtoupper($employee_id),
				'finish_laser' => date('Y-m-d H:i:s'),
				'remark' => 1
			]);

			$response = array(
				'status' => true,
				'message' => 'Proses Laser berhasil diakhiri'
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

	public function fetchFinishMolding(Request $request){
		$id = $request->get('order_id');
		$employee_id = $request->get('employee_id');

		try {
			$order = ReedInjectionOrder::where('id', $id)
			->update([
				'operator_molding_id' => strtoupper($employee_id),
				'setup_molding' => 1
			]);

			$response = array(
				'status' => true,
				'message' => 'Proses Injeksi berhasil diakhiri'
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

	public function scanInjectionDelivery(Request $request){
		$kanban = $request->get('kanban');
		$employee_id = $request->get('employee_id');

		$storage_location = substr($kanban, 0, 4);
		$material_number = substr($kanban, 4, 7);

		$order = ReedInjectionOrder::where('kanban', $kanban)
		->where('remark', 1)
		->first();

		DB::beginTransaction();
		if($order){
			if($order->hako == $order->hako_delivered){
				$response = array(
					'status' => false,
					'message' => 'Delivery sudah dilakukan'
				);
				return Response::json($response);
			}

			$order->hako_delivered = $order->hako_delivered + 1; 
			$order->delivered_by = $employee_id; 
			$order->delivered_at = date('Y-m-d H:i:s'); 

			if($order->hako == $order->hako_delivered){
				$order->remark = 2;

				$log = new ReedInjectionOrderLog([
					'order_id' => $order->id,
					'kanban' => $kanban,
					'material_number' => $order->material_number,
					'material_description' => $order->material_description,
					'location' => 'AFTER INJECTION',
					'quantity' => $order->quantity,
					'picked_by' => strtoupper($employee_id),
					'picked_at' => date('Y-m-d H:i:s'),
					'created_by' => Auth::id()
				]);


				$inventory = Inventory::where('plant', '8190')
				->where('material_number', $material_number)
				->where('storage_location', $storage_location)
				->first();

				if($inventory){
					$inventory->quantity = $inventory->quantity + $order->quantity;
				}else{
					$inventory = new Inventory([
						'plant' => '8190',
						'material_number' => $material_number,
						'storage_location' => $storage_location,
						'quantity' => $order->quantity
					]);
				}

				try {
					DB::transaction(function() use ($log, $inventory){
						$log->save();
						$inventory->save();
					});

				}catch (Exception $e) {
					DB::rollback();

					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			try {
				$order->save();
				DB::commit();

				$response = array(
					'status' => true,
					'message' => 'Delivery berhasil'
				);
				return Response::json($response);

			}catch (Exception $e) {
				DB::rollback();

				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}else{
			DB::rollback();

			$response = array(
				'status' => false,
				'message' => 'Kanban finish injection tidak ditemukan'
			);
			return Response::json($response);
		}
	}

}
