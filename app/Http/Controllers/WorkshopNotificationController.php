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
			$wjo->save();

			$message = 'WJO dengan Order No. '.$id;
			$message2 ='Berhasil di approve sebagai WJO dengan prioritas urgent';
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => $message,
				'message2' => $message2,
			))->with('page', 'WJO Approval');

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
			$wjo->priority = 'normal';
			$wjo->save();

			$message = 'WJO dengan Order No. '.$id;
			$message2 = 'berubah menjadi WJO dengan prioritas normal';
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => $message,
				'message2' => $message2,
			))->with('page', 'WJO Approval');

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
