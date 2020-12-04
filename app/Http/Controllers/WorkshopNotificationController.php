<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\CodeGenerator;
use App\WorkshopJobOrder;
use App\WorkshopJobOrderLog;
use App\WorkshopMaterial;
use App\EmployeeSync;
use Carbon\Carbon;
use DataTables;
use Response;

class WorkshopNotificationController extends Controller{

	public function __construct(){
	}

	public function approveUrgent($id){
		$wjo = WorkshopJobOrder::where('order_no', '=', $id)->first();

		if($wjo->remark == 0){
			$wjo->remark = 1;

			$manager = EmployeeSync::where('position', '=', 'Manager')
			->where('department', 'like', '%Maintenance%')
			->first();

			$wjo_log = new WorkshopJobOrderLog([
				'order_no' => $id,
				'remark' => 1,
				'created_by' => $manager->employee_id
			]);

			try {
				DB::transaction(function() use ($wjo, $wjo_log){
					$wjo->save();
					$wjo_log->save();
				});

				$message = 'WJO dengan Order No. '.$id;
				$message2 ='Berhasil di approve sebagai WJO dengan prioritas urgent';
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'WJO Approval');

			} catch (Exception $e) {
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => 'Update Error',
					'message2' => $e->getMessage(),
				))->with('page', 'WJO Approval');
			}

		}else{
			$message = 'WJO dengan Order No. '.$id;
			$message2 ='Sudah di approve/reject';
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => $message,
				'message2' => $message2,
			))->with('page', 'WJO Approval');
		}
	}

	public function rejectUrgent($id){
		$wjo = WorkshopJobOrder::where('order_no', '=', $id)->first();

		if($wjo->remark == 0){
			$wjo->remark = 1;
			$wjo->priority = 'Normal';
			if($wjo->category == 'Equipment'){
				$wjo->target_date = date('Y-m-d', strtotime($wjo->target_date. ' + 7 days'));
			}else{
				$wjo->target_date = date('Y-m-d', strtotime($wjo->target_date. ' + 14 days'));
			}

			$manager = EmployeeSync::where('position', '=', 'Manager')
			->where('department', 'like', '%Maintenance%')
			->first();

			$wjo_log = new WorkshopJobOrderLog([
				'order_no' => $id,
				'remark' => 1,
				'created_by' => $manager->employee_id		
			]);		

			try {
				DB::transaction(function() use ($wjo, $wjo_log){
					$wjo->save();
					$wjo_log->save();
				});

				$message = 'Reject WJO Urgent berhasil';
				$message2 = $id.' berubah sebagai WJO dengan prioritas normal';
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'WJO Approval');

			} catch (Exception $e) {
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => 'Update Error',
					'message2' => $e->getMessage(),
				))->with('page', 'WJO Approval');
			}

		}else{
			$message = 'WJO dengan Order No. '.$id;
			$message2 ='Sudah di approve/reject';
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => $message,
				'message2' => $message2,
			))->with('page', 'WJO Approval');
		}
	}


}
