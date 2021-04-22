<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Driver;
use App\DriverList;
use App\DriverDetail;
use App\DriverLog;
use App\EmployeeSync;
use App\BentoQuota;
use App\User;
use App\Bento;
use App\BentoMenu;
use Carbon\Carbon;
use Response;

class GeneralAffairController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function indexBentoReport(){
		$title = "Japanese Food Order Report";
		$title_jp = "和食弁当の予約";

		$menus = BentoMenu::orderBy('due_date', 'desc')->where('due_date', '>=', date('Y-m-01'))->get();
		$quotas = BentoQuota::orderBy('due_date', 'desc')->where('due_date', '>=', date('Y-m-01'))->get();

		return view('general_affairs.bento_report', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('head', 'GA Control')->with('page', 'Japanese Food Order Report');
	}

	public function approveBento($id){
		$title = "Bento Message";
		$title_jp = "";
		$message = "";
		$message2 = "";
		$bento_lists = array();

		$bento_ids = explode('-', $id);

		foreach($bento_ids as $bento_id){
			if($bento_id != ""){
				$bento = Bento::where('id', '=', $bento_id)->first();

				if(strlen($bento->approver_id)>0){

					$message = 'Bento Request Already Confirmed';
					$message2 = "Can't approve order";

					return view('general_affairs.bento_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $message,
						'message2' => $message2
					))->with('head', 'Bento Request');
				}

				$message = "Bento Request Approved";
				$message2 = "";

				$bento->approver_id = Auth::user()->username;
				$bento->approver_name = Auth::user()->name;
				$bento->status = 'Approved';

				$bento->save();

				array_push($bento_lists, 
					[
						'id' => $bento->id,
						'order_by' => $bento->order_by,
						'order_by_name' => $bento->order_by_name,
						'charge_to' => $bento->charge_to,
						'charge_to_name' => $bento->charge_to_name,
						'due_date' => $bento->due_date,
						'employee_id' => $bento->employee_id,
						'employee_name' => $bento->employee_name,
						'department' => $bento->department,
						'section' => $bento->section,
						'status' => $bento->status,
						'created_by' => $bento->created_by,
						'approver_id' => $bento->approver_id,
						'approver_name' => $bento->approver_name
					]);
			}
		}


		$user = User::where('id', '=', $bento_lists[0]['created_by'])->first();

		if($user->role_code == 'YEMI'){
			Mail::to(['rianita.widiastuti@music.yamaha.com', $user->email])->cc(['putri.sukma.riyanti@music.yamaha.com', 'merlinda.dyah@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
		}
		else{
			Mail::to(['rianita.widiastuti@music.yamaha.com', $user->email])->cc(['putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
		}

		return view('general_affairs.bento_message', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'message' => $message,
			'message2' => $message2
		))->with('head', 'Bento Request');
	}

	public function rejectBento($id){
		$title = "Bento Message";
		$title_jp = "";
		$message = "";
		$message2 = "";
		$bento_lists = array();

		$bento_ids = explode('-', $id);

		foreach($bento_ids as $bento_id){
			if($bento_id != ""){
				$bento = Bento::where('id', '=', $bento_id)->first();

				if(strlen($bento->approver_id)>0){

					$message = "Bento Request Already Confirmed";
					$message2 = "Can't reject order";

					return view('general_affairs.bento_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $message,
						'message2' => $message2
					))->with('head', 'Bento Request');
				}

				$bento->approver_id = Auth::user()->username;
				$bento->approver_name = Auth::user()->name;
				$bento->status = 'Rejected';

				$bento->save();

				array_push($bento_lists, 
					[
						'id' => $bento->id,
						'order_by' => $bento->order_by,
						'order_by_name' => $bento->order_by_name,
						'charge_to' => $bento->charge_to,
						'charge_to_name' => $bento->charge_to_name,
						'due_date' => $bento->due_date,
						'employee_id' => $bento->employee_id,
						'employee_name' => $bento->employee_name,
						'department' => $bento->department,
						'section' => $bento->section,
						'status' => $bento->status,
						'created_by' => $bento->created_by,
						'approver_id' => $bento->approver_id,
						'approver_name' => $bento->approver_name
					]);
			}
		}

		$user = User::where('id', '=', $bento_lists[0]['created_by'])->first();

		if($user->role_code == 'YEMI'){
			Mail::to(['rianita.widiastuti@music.yamaha.com', $user->email])->cc(['putri.sukma.riyanti@music.yamaha.com', 'merlinda.dyah@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
		}
		else{
			Mail::to(['rianita.widiastuti@music.yamaha.com', $user->email])->cc(['putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
		}

		return view('general_affairs.bento_message', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'message' => $message,
			'message2' => $message2
		))->with('head', 'Bento Request');
	}

	public function indexBento(){

		$title = "Japanese Food Order";
		$title_jp = "和食弁当の予約";

		if(Auth::user()->role_code == 'YEMI'){
			$employees = User::where('role_code', '=', 'YEMI')
			->orderBy('name', 'asc')
			->select(db::raw('username as employee_id'), 'name')
			->get();

			$location = 'YEMI';
		}
		else{
			// $employees = db::select('SELECT
			// 	employee_id,
			// 	name 
			// 	FROM
			// 	employee_syncs 
			// 	WHERE
			// 	department = ( SELECT department FROM employee_syncs WHERE employee_id = "'.Auth::user()->username.'" ) 
			// 	AND end_date IS NULL 
			// 	ORDER BY
			// 	name ASC');

			$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
			->first();

			$employees = EmployeeSync::orderBy('name', 'asc')
			->whereNull('end_date')
			->where('department', '=', $employee->department)
			->select('employee_id', 'name')
			->get();

			$location = 'YMPI';		
		}

		$bentos = BentoMenu::orderBy('due_date', 'desc')
		->select(db::raw('date_format(due_date, "%b %Y") as period'), 'menu_image')
		->take(2)
		->get();

		return view('general_affairs.bento', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'location' => $location,
			'bentos' => $bentos
		))->with('head', 'GA Control')->with('page', 'Japanese Food Order');
	}

	public function indexDriverLog(){
		$title = "Driver Log";
		$title_jp = "";

		$driver_lists = DriverList::orderBy('name', 'asc')->get();
		$employees = EmployeeSync::orderBy('name', 'asc')->get();

		return view('general_affairs.driver_log', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'driver_lists' => $driver_lists,
			'employees' => $employees
		))->with('head', 'GA Control')->with('page', 'Driver Control');
	}

	public function indexDriver(){
		$title = "Driver Monitoring System";
		$title_jp = "ドライバー管理システム";

		$employees = EmployeeSync::orderBy('name', 'asc')->get();
		$driver_lists = DriverList::orderBy('name', 'asc')->get();

		return view('general_affairs.driver', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $employees,
			'driver_lists' => $driver_lists
		))->with('head', 'GA Control')->with('page', 'Driver Control');
	}

	public function fetchBentoQuota(Request $request){
		$due_date = date('Y-m-d', strtotime($request->get('due_date')));
		$bento_quota = BentoQuota::where('due_date', '=', $due_date)->first();

		$response = array(
			'status' => true,
			'bento_quota' => $bento_quota
		);
		return Response::json($response);
	}

	public function fetchBentoOrderList(){

		$now = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
		$last = date('Y-m-d', strtotime(carbon::now()->addDays(10)));

		if(Auth::user()->role_code == 'GA'){
			$unconfirmed = Bento::get();
		}
		else{
			$unconfirmed = Bento::where('created_by', '=', Auth::id())
			->get();
		}

		$quotas = BentoQuota::where('due_date', '>=', $now)
		->where('due_date', '<=', $last)
		->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
		->orderBy('due_date', 'asc')
		->get();

		$response = array(
			'status' => true,
			'unconfirmed' => $unconfirmed,
			'quotas' => $quotas
		);
		return Response::json($response);
	}

	public function editBentoOrder(Request $request){
		try{

			if($request->get('status') == 'edit'){
				$bento = Bento::where('id', '=', $request->get("id"))->first();

				if($request->get('location') != 'YEMI'){
					$bento_quota = BentoQuota::where('due_date', '=', $bento->due_date)->first();
					$bento_quota->serving_ordered = $bento_quota->serving_ordered-1;
					$bento_quota->save();
				}

				$bento->order_by = $request->get('order_by');
				$bento->order_by_name = $request->get('order_by_name');
				$bento->charge_to = $request->get('charge_to');
				$bento->charge_to_name = $request->get('charge_to_name');
				$bento->due_date = $request->get('due_date');

				$employee_id = explode('-', $request->get('employee_id'));
				$bento->employee_id = $employee_id[0];

				$employee = EmployeeSync::where('employee_id', '=', $employee_id[0])->first();

				$bento->employee_name = $employee->name;
				$bento->department = $employee->department;
				$bento->section = $employee->section;

				if($request->get('location') != 'YEMI'){
					$bento_quota = BentoQuota::where('due_date', '=', $request->get('due_date'))->first();
					$bento_quota->serving_ordered = $bento_quota->serving_ordered-1;
					$bento_quota->save();
				}

				$bento->save();

				$response = array(
					'status' => true,
					'message' => 'Your order has been edited, please wait for approval'
				);
				return Response::json($response);				
			}

			if($request->get('status') == 'delete'){

				$bento = Bento::where('id', '=', $request->get("id"))->first();

				if($request->get('location') != 'YEMI'){
					$bento_quota = BentoQuota::where('due_date', '=', $bento->due_date)->first();
					$bento_quota->serving_ordered = $bento_quota->serving_ordered-1;
					$bento_quota->save();
				}

				$bento->forceDelete();

				$response = array(
					'status' => true,
					'message' => 'Your order has been deleted'
				);
				return Response::json($response);				
			}


		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputBentoOrder(Request $request){
		try{
			$order_lists = $request->get('order_list');
			$order_by = User::where('username', '=', $request->get('order_by'))->first();
			$charge_to = User::where('username', '=', $request->get('charge_to'))->first();
			$bento_lists = array();

			foreach($order_lists as $order_list) {
				$order = explode("_", $order_list);
				$bento_quota = BentoQuota::where('due_date', '=', $order[1])->first();

				if($order_by->role_code == 'YEMI'){
					$employee = User::where('username', '=', $order[0]);
					$bento = new Bento([
						'order_by' => $order_by->username,
						'order_by_name' => $order_by->name,
						'charge_to' => $charge_to->username,
						'charge_to_name' => $charge_to_name->username,
						'due_date' => $order[1],
						'employee_id' => $order[0],
						'employee_name' => $employee->name,
						'department' => 'YEMI',
						'section' => 'YEMI',
						'status' => 'Waiting For Confirmation',
						'created_by' => Auth::id()
					]);
				}
				else{
					$employee = EmployeeSync::where('employee_id', '=', $order[0])->first();

					$bento = new Bento([
						'order_by' => $order_by->username,
						'order_by_name' => $order_by->name,
						'charge_to' => $charge_to->username,
						'charge_to_name' => $charge_to->name,
						'due_date' => $order[1],
						'employee_id' => $order[0],
						'employee_name' => $employee->name,
						'department' => $employee->department,
						'section' => $employee->section,
						'status' => 'Waiting For Confirmation',
						'created_by' => Auth::id()
					]);
					$bento_quota->serving_ordered = $bento_quota->serving_ordered+1;
					$bento_quota->save();
				}

				$bento->save();

				array_push($bento_lists, 
					[
						'id' => $bento->id,
						'order_by' => $bento->order_by,
						'order_by_name' => $bento->order_by_name,
						'charge_to' => $bento->charge_to,
						'charge_to_name' => $bento->charge_to_name,
						'due_date' => $bento->due_date,
						'employee_id' => $bento->employee_id,
						'employee_name' => $bento->employee_name,
						'department' => $bento->department,
						'section' => $bento->section,
						'status' => $bento->status,
						'created_by' => $bento->created_by
					]);

			}

			if($order_by->role_code == 'YEMI'){
				Mail::to(['rianita.widiastuti@music.yamaha.com', $order_by->email])->cc(['putri.sukma.riyanti@music.yamaha.com', 'merlinda.dyah@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));
			}
			else{
				Mail::to(['rianita.widiastuti@music.yamaha.com', $order_by->email])->cc(['putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));	
			}

			$response = array(
				'status' => true,
				'message' => 'Your order has been created, please wait for approval<br>予約完了です。ご確認をお待ちください。'
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

	public function fetchDriverLog(Request $request){
		$driver_logs = DriverLog::orderBy('driver_logs.driver_id');
		$drivers = Driver::orderBy('drivers.driver_id');

		if(count($request->get('driver_id'))>0){
			$driver_logs = $driver_logs->whereIn('driver_logs.driver_id', $request->get('driver_id'));
			$drivers = $drivers->whereIn('drivers.driver_id', $request->get('driver_id'));
		}
		if(count($request->get('status'))>0){
			$driver_logs = $driver_logs->whereIn('driver_logs.status', $request->get('status'));
			$drivers = $drivers->whereIn('drivers.remark', $request->get('status'));
		}
		if(strlen($request->get('datefrom'))>0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$driver_logs = $driver_logs->where(db::raw('date_format(driver_logs.date_from, "%Y-%m-%d")'), '>=', $date_from);
			$drivers = $drivers->where(db::raw('date_format(drivers.date_from, "%Y-%m-%d")'), '>=', $date_from);
		}
		if(strlen($request->get('dateto'))>0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$driver_logs = $driver_logs->where(db::raw('date_format(driver_logs.date_to, "%Y-%m-%d")'), '<=', $date_to);
			$drivers = $drivers->where(db::raw('date_format(drivers.date_to, "%Y-%m-%d")'), '<=', $date_to);
		}

		$driver_logs = $driver_logs->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'driver_logs.created_by')
		->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'driver_logs.approved_by')
		->leftJoin('employee_syncs as receive', 'receive.employee_id', '=', 'driver_logs.received_by')
		->select('driver_logs.request_id as id', 'driver_logs.driver_id', 'driver_logs.name', 'driver_logs.purpose', 'driver_logs.destination_city', 'driver_logs.date_from', 'driver_logs.date_to', 'request.employee_id as request_employee_id', 'request.name as request_name', 'approve.employee_id as approve_employee_id', 'approve.name as approve_name', 'receive.employee_id as receive_employee_id', 'receive.name as receive_name', 'driver_logs.status')
		->distinct()
		->get();

		$drivers = $drivers->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
		->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'drivers.approved_by')
		->leftJoin('employee_syncs as receive', 'receive.employee_id', '=', 'drivers.received_by')
		->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'request.employee_id as request_employee_id', 'request.name as request_name', 'approve.employee_id as approve_employee_id', 'approve.name as approve_name', 'receive.employee_id as receive_employee_id', 'receive.name as receive_name', 'drivers.remark as status')
		->get();

		$logs = array();

		if(count($driver_logs) > 0){
			foreach ($driver_logs as $driver_log) {
				array_push($logs, 
					[
						"id" => $driver_log->id,
						"driver_id" => $driver_log->driver_id,
						"name" => $driver_log->name,
						"purpose" => $driver_log->purpose,
						"destination_city" => $driver_log->destination_city,
						"date_from" => $driver_log->date_from,
						"date_to" => $driver_log->date_to,
						"request_employee_id" => $driver_log->request_employee_id,
						"request_name" => $driver_log->request_name,
						"approve_employee_id" => $driver_log->approve_employee_id,
						"approve_name" => $driver_log->approve_name,
						"receive_employee_id" => $driver_log->receive_employee_id,
						"receive_name" => $driver_log->receive_name,
						"status" => $driver_log->status
					]);
			}
		}

		if(count($drivers) > 0){
			foreach ($drivers as $driver) {
				array_push($logs, 
					[
						"id" => $driver->id,
						"driver_id" => $driver->driver_id,
						"name" => $driver->name,
						"purpose" => $driver->purpose,
						"destination_city" => $driver->destination_city,
						"date_from" => $driver->date_from,
						"date_to" => $driver->date_to,
						"request_employee_id" => $driver->request_employee_id,
						"request_name" => $driver->request_name,
						"approve_employee_id" => $driver->approve_employee_id,
						"approve_name" => $driver->approve_name,
						"receive_employee_id" => $driver->receive_employee_id,
						"receive_name" => $driver->receive_name,
						"status" => $driver->status
					]);
			}
		}

		$response = array(
			'status' => true,
			'logs' => $logs
		);
		return Response::json($response);
	}

	public function fetchDriverRequest(Request $request){
		$requests = Driver::leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
		->select('drivers.id', 'drivers.purpose', 'drivers.destination_city', 'request.name as requested_by', 'drivers.date_from', 'drivers.date_to')
		->whereIn('drivers.remark', ['requested','accepted'])
		->orderBy('drivers.created_at', 'asc')
		->get();

		$response = array(
			'status' => true,
			'requests' => $requests
		);
		return Response::json($response);
	}

	public function importDriverDuty(Request $request){
		if($request->hasFile('duty')){
			$username = Auth::user()->username;
			$file = $request->file('duty');
			$data = file_get_contents($file);

			$rows = explode("\r\n", $data);
			foreach ($rows as $row)
			{
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					if($row[0] != 'NIK'){
						$driver_list = DriverList::where('driver_id', '=', $row[0])->first();
						$driver = new Driver([
							'driver_id' => $row[0],
							'name' => $driver_list->name,
							'purpose' => $row[1],
							'destination_city' => $row[2],
							'date_from' => date('Y-m-d H:i:s', strtotime(str_replace('/','-',$row[3]))),
							'date_to' => date('Y-m-d H:i:s', strtotime(str_replace('/','-',$row[4]))),
							'created_by' => $username,
							'approved_by' => $username,
							'received_by' => $username,
							'remark' => 'received'
						]);
						$driver->save();
					}
				}
			}
			return redirect('/index/ga_control/driver')->with('status', 'Tugas driver berhasil di import.')->with('page', 'Material');
		}
		else
		{
			return redirect('/index/ga_control/driver')->with('error', 'Harus ada file.')->with('page', 'Material');
		}
	}

	public function acceptDriverRequest(Request $request){
		try{
			if($request->get('cat') == 'accept'){
				$driver = Driver::find($request->get('id'));
				$driver_list = DriverList::where('driver_id', '=', $request->get('driver_id'))->first();

				$driver->driver_id = $request->get('driver_id');
				$driver->name = $driver_list->name;
				$driver->purpose = $request->get('purpose');
				$driver->destination_city = $request->get('destination_city');
				$driver->date_from = $request->get('start_time');
				$driver->date_to = $request->get('end_time');
				$driver->received_by = Auth::user()->username;
				$driver->remark = 'received';
				$driver->save();

				$response = array(
					'status' => true,
					'message' => 'Driver request berhasil diterima',
					'tes' => $driver
				);
				return Response::json($response);
			}
			else if($request->get('cat') == 'reject'){
				$driver = Driver::where('id', '=', $request->get('id'))
				->first();

				$driver->received_by = Auth::user()->username;
				$driver->save();

				$drivers = Driver::where('drivers.id', '=', $request->get('id'))
				->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
				->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
				->get();

				foreach ($drivers as $driver) {
					$driver_log = new Driverlog([
						'request_id' => $driver->id,
						'driver_id' => $driver->driver_id,
						'name' => $driver->name,
						'purpose' => $driver->purpose,
						'destination_city' => $driver->destination_city,
						'date_from' => $driver->date_from,
						'date_to' => $driver->date_to,
						'created_by' => $driver->created_by,
						'approved_by' => $driver->approved_by,
						'received_by' => $driver->received_by,
						'status' => 'rejected',
						'remark' => $driver->remark,
						'category' => $driver->category
					]);
					$driver_log->save();
				}

				$driver->forceDelete();

				$delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
				->first();

				if(count($delete_driver_detail) > 0){
					$delete_driver_detail->forceDelete();	
				}

				$response = array(
					'status' => true,
					'message' => 'Driver request berhasil ditolak'
				);
				return Response::json($response);
			}

			else if($request->get('cat') == 'canceled'){
				$driver = Driver::where('id', '=', $request->get('id'))
				->first();

				$driver->received_by = Auth::user()->username;
				$driver->save();

				$drivers = Driver::where('drivers.id', '=', $request->get('id'))
				->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
				->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
				->get();

				foreach ($drivers as $driver) {
					$driver_log = new Driverlog([
						'request_id' => $driver->id,
						'driver_id' => $driver->driver_id,
						'name' => $driver->name,
						'purpose' => $driver->purpose,
						'destination_city' => $driver->destination_city,
						'date_from' => $driver->date_from,
						'date_to' => $driver->date_to,
						'created_by' => $driver->created_by,
						'approved_by' => $driver->approved_by,
						'received_by' => $driver->received_by,
						'status' => 'canceled',
						'remark' => $driver->remark,
						'category' => $driver->category
					]);
					$driver_log->save();
				}

				$driver->forceDelete();

				$delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
				->first();

				if(count($delete_driver_detail) > 0){
					$delete_driver_detail->forceDelete();	
				}

				$response = array(
					'status' => true,
					'message' => 'Driver request berhasil ditolak'
				);
				return Response::json($response);
			}
			else if($request->get('cat') == 'close'){
				$driver = Driver::where('id', '=', $request->get('id'))
				->first();

				$drivers = Driver::where('drivers.id', '=', $request->get('id'))
				->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
				->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
				->get();

				foreach ($drivers as $driver) {
					$driver_log = new Driverlog([
						'request_id' => $driver->id,
						'driver_id' => $driver->driver_id,
						'name' => $driver->name,
						'purpose' => $driver->purpose,
						'destination_city' => $driver->destination_city,
						'date_from' => $driver->date_from,
						'date_to' => $driver->date_to,
						'created_by' => $driver->created_by,
						'approved_by' => $driver->approved_by,
						'received_by' => $driver->received_by,
						'status' => 'received',
						'remark' => $driver->remark,
						'category' => $driver->category
					]);
					$driver_log->save();
				}

				$driver->forceDelete();

				$delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
				->first();

				if(count($delete_driver_detail) > 0){
					$delete_driver_detail->forceDelete();	
				}

				$response = array(
					'status' => true,
					'message' => 'Driver request berhasil ditutup'
				);
				return Response::json($response);
			}
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function createDriverDuty(Request $request){
		try{
			$id = Auth::user()->username;
			$driver_list = DriverList::where('driver_id', '=', $request->get('driver_id'))->first();
			$driver = new Driver([
				'purpose' => $request->get('purpose'),
				'destination_city' => $request->get('destination_city'),
				'date_from' => $request->get('start_time'),
				'date_to' => $request->get('end_time'),
				'driver_id' => $request->get('driver_id'),
				'name' => $driver_list->name,
				'approved_by' => $id,
				'received_by' => $id,
				'created_by' => $id,
				'remark' => 'received'
			]);
			$driver->save();

			$passengers = $request->get('passenger');
			$destinations = $request->get('destination');

			for ($i=0; $i < count($passengers); $i++) { 
				$passenger_detail = new DriverDetail([
					'driver_id' => $driver->id,
					'remark' => $passengers[$i],
					'category' => 'passenger'
				]);
				$passenger_detail->save();
			}

			for ($i=0; $i < count($destinations); $i++) { 
				$destination_detail = new DriverDetail([
					'driver_id' => $driver->id,
					'remark' => $destinations[$i],
					'category' => 'destination'		
				]);
				$destination_detail->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Tugas driver berhasil ditambahkan'
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

	public function createDriverRequest(Request $request){
		try{
			$id = Auth::user()->username;
			$driver = new Driver([
				'purpose' => $request->get('purpose'),
				'destination_city' => $request->get('destination_city'),
				'date_from' => $request->get('start_time'),
				'date_to' => $request->get('end_time'),
				'created_by' => $id,
				'remark' => 'requested'
			]);
			$driver->save();

			$passengers = $request->get('passenger');
			$destinations = $request->get('destination');

			for ($i=0; $i < count($passengers); $i++) { 
				$passenger_detail = new DriverDetail([
					'driver_id' => $driver->id,
					'remark' => $passengers[$i],
					'category' => 'passenger'
				]);
				$passenger_detail->save();
			}

			for ($i=0; $i < count($destinations); $i++) { 
				$destination_detail = new DriverDetail([
					'driver_id' => $driver->id,
					'remark' => $destinations[$i],
					'category' => 'destination'		
				]);
				$destination_detail->save();
			}


			$mail = EmployeeSync::leftJoin('send_emails', 'send_emails.remark', '=', 'employee_syncs.department')
			->where('employee_syncs.employee_id', '=', $id)
			->select('send_emails.email')
			->first();

			$data = [
				'driver' => $driver,
				'requested_by' => Auth::user()->name
			];

			Mail::to($mail->email)
			->bcc(['aditya.agassi@music.yamaha.com'])
			->send(new SendEmail($data, 'driver_request'));

			$response = array(
				'status' => true,
				'message' => 'Request Driver Berhasil Diajukan',
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

	public function approveRequest($id){
		try{
			$driver = Driver::where('drivers.id', '=', $id)->first();

			if($driver->remark == 'accepted'){
				$message = 'Approve driver request gagal';
				$message2 = 'Driver Request ID: '.$id.' sudah pernah disetujui';
				return view('general_affairs.driver_approval', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'Driver Approval');
			}
			if($driver->remark == 'received'){
				$approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
				$driver->approved_by = $approver->nik_manager;
				$driver->save();

				$data = [
					'driver' => $driver
				];

				Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])
				->bcc(['aditya.agassi@music.yamaha.com'])
				->send(new SendEmail($data, 'driver_approval_notification'));

				$message = 'Approval driver request berhasil';
				$message2 = 'Driver Request ID: '.$id.' berhasil disetujui';
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'WJO Approval');
			}
			if($driver->remark == 'requested'){
				$approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
				$driver->approved_by = $approver->nik_manager;
				$driver->remark = 'accepted';

				$driver->save();

				$data = [
					'driver' => $driver
				];

				Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])
				->bcc(['aditya.agassi@music.yamaha.com'])
				->send(new SendEmail($data, 'driver_approval_notification'));

				$message = 'Approval driver request berhasil';
				$message2 = 'Driver Request ID: '.$id.' berhasil disetujui';
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'WJO Approval');
			}

		}
		catch(\Exception $e){
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => 'ERROR!!!',
				'message2' => $e->getMessage(),
			))->with('page', 'WJO Approval');
		}
	}

	public function rejectRequest($id){
		try{
			$driver = Driver::where('drivers.id', '=', $id)->first();

			if($driver->remark == 'accepted' || $driver->remark == 'rejected'){
				$message = 'Reject driver request gagal';
				$message2 = 'Driver Request ID: '.$id.' sudah pernah disetujui/ditolak';
				return view('general_affairs.driver_approval', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'Driver Approval');
			}
			else{
				$approver = EmployeeSync::where('employee_id', '=', $driver->created_by)->first();
				$driver->approved_by = $approver->nik_manager;
				$driver->remark = 'rejected';
				$driver->save();

				$drivers = Driver::where('drivers.id', '=', $request->get('id'))
				->leftJoin('driver_details', 'drivers.id', '=', 'driver_details.driver_id')
				->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'drivers.created_by', 'drivers.approved_by', 'drivers.received_by', 'drivers.remark as status', 'driver_details.remark', 'driver_details.category')
				->get();

				foreach ($drivers as $driver) {
					$driver_log = new Driverlog([
						'request_id' => $driver->id,
						'driver_id' => $driver->driver_id,
						'name' => $driver->name,
						'purpose' => $driver->purpose,
						'destination_city' => $driver->destination_city,
						'date_from' => $driver->date_from,
						'date_to' => $driver->date_to,
						'created_by' => $driver->created_by,
						'approved_by' => $driver->approved_by,
						'received_by' => $driver->received_by,
						'status' => 'rejected',
						'remark' => $driver->remark,
						'category' => $driver->category
					]);
					$driver_log->save();
				}

				$driver->forceDelete();

				$delete_driver_detail = DriverDetail::where('driver_id', '=', $request->get('id'))
				->first();

				if(count($delete_driver_detail) > 0){
					$delete_driver_detail->forceDelete();	
				}

				$message = 'Reject driver request berhasil';
				$message2 = 'Driver Request ID: '.$id.' berhasil ditolak';
				return view('workshop.wjo_approval_message', array(
					'head' => $id,
					'message' => $message,
					'message2' => $message2,
				))->with('page', 'WJO Approval');
			}
		}
		catch(\Exception $e){
			return view('workshop.wjo_approval_message', array(
				'head' => $id,
				'message' => 'ERROR!!!',
				'message2' => $e->getMessage(),
			))->with('page', 'WJO Approval');
		}
	}

	public function editDriverEdit(Request $request){
		try{
			if($request->get('cat') == 'save'){
				$driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

				$driver_list->name = $request->get('name');
				$driver_list->phone_no = $request->get('no');
				$driver_list->plat_no = $request->get('plat');
				$driver_list->car = $request->get('car');
				$driver_list->category = $request->get('category');

				$driver_list->save();

				$response = array(
					'status' => true,
					'message' => 'Data driver berhasil diperbaharui'
				);
				return Response::json($response);				
			}
			else if($request->get('cat') == 'new'){
				$driver_list = new DriverList([
					'driver_id' => $request->get('id'),
					'name' => $request->get('name'),
					'phone_no' => $request->get('no'),
					'plat_no' => $request->get('plat'),
					'car' => $request->get('car'),
					'category' => $request->get('category'),
					'created_by' => Auth::id()
				]);

				$driver_list->save();

				$response = array(
					'status' => true,
					'message' => 'Driver baru berhasil ditambahkan'
				);
				return Response::json($response);
			}
			else if($request->get('cat') == 'delete'){
				$driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

				$driver_list->delete();

				$response = array(
					'status' => true,
					'message' => 'Driver berhasil dinonaktifkan'
				);
				return Response::json($response);	
			}
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchDriverEdit(Request $request){
		$driver_list = DriverList::where('driver_id', '=', $request->get('id'))->first();

		if($driver_list == null){
			$response = array(
				'status' => false,
				'message' => 'Klik pada NIK driver',
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Driver ditemukan',
			'driver_list' => $driver_list
		);
		return Response::json($response);

	}

	public function fetchDriverDuty(){
		$now = date('Y-m-d H:i:s');
		$drivers = Driver::leftJoin('employee_syncs', 'drivers.created_by', '=', 'employee_syncs.employee_id')
		->select('drivers.id', 'drivers.driver_id', 'drivers.name as driver_name', 'drivers.purpose', 'drivers.destination_city', 'employee_syncs.name', db::raw('date_format(drivers.date_from, "%H:%i") as date_from'), db::raw('date_format(drivers.date_to, "%H:%i") as date_to'), 'drivers.remark')
		->whereIn('drivers.remark', ['received'])
		->whereNotNull('drivers.driver_id')
		->where('drivers.date_from', '<=', $now)
		->where('drivers.date_to', '>=', $now)
		->orderBy('drivers.driver_id', 'asc')
		->get();

		$response = array(
			'status' => true,
			'drivers' => $drivers
		);
		return Response::json($response);
	}

	public function fetchDriver(){
		$driver_lists = DriverList::orderBy('driver_id', 'asc')
		->get();

		$drivers = Driver::leftJoin('employee_syncs', 'drivers.created_by', '=', 'employee_syncs.employee_id')
		->select('drivers.id', 'drivers.driver_id', 'drivers.name', 'drivers.destination_city', 'employee_syncs.name', 'drivers.date_from', 'drivers.date_to', 'drivers.remark')
		->whereIn('drivers.remark', ['requested', 'accepted', 'received', 'rejected'])
		->whereNotNull('drivers.driver_id')
		->orderBy('drivers.driver_id', 'asc')
		->get();

		$response = array(
			'status' => true,
			'drivers' => $drivers,
			'driver_lists' => $driver_lists
		);
		return Response::json($response);
	}

	public function fetchDriverDetail(Request $request){
		$driver = Driver::where('drivers.id', '=', $request->get('id'))
		->leftJoin('employee_syncs as request', 'request.employee_id', '=', 'drivers.created_by')
		->leftJoin('employee_syncs as approve', 'approve.employee_id', '=', 'drivers.approved_by')
		->leftJoin('employee_syncs as accept', 'accept.employee_id', '=', 'drivers.received_by')
		->select('drivers.id', 'drivers.purpose', 'drivers.destination_city', 'drivers.date_from', 'drivers.date_to', 'request.name as request_name', 'request.employee_id as request_id', 'approve.name as approve_name', 'approve.employee_id as approve_id', 'accept.name as accept_name', 'accept.employee_id as accept_id', 'drivers.driver_id')
		->first();

		$driver_lists = DriverList::orderBy('driver_lists.name', 'asc')->get();

		$passenger_detail = DriverDetail::where('driver_details.driver_id', '=', $request->get('id'))
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'driver_details.remark')
		->where('driver_details.category', '=', 'passenger')
		->get();

		$destination_detail = DriverDetail::where('driver_details.category', '=', 'destination')
		->where('driver_details.driver_id', '=', $request->get('id'))
		->get();

		$response = array(
			'status' => true,
			'driver' => $driver,
			'passenger_detail' => $passenger_detail,
			'destination_detail' => $destination_detail,
			'driver_lists' => $driver_lists
		);
		return Response::json($response); 
	}



    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //


	public function purchase_requisition()
	{
		$title = 'Purchase Requisition Canteen';
		$title_jp = '';

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department', 'section', 'group')
		->first();

		$staff = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
			where end_date is null and (position like '%Staff%')");

		$items = db::select("select kode_item, kategori, deskripsi from acc_item_canteens where deleted_at is null");
		$dept = $this->dept;

		return view('accounting_purchasing.purchase_requisition', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'items' => $items,
			'dept' => $dept,
			'staff' => $staff,
			'uom' => $this->uom
		))
		->with('page', 'Purchase Requisition')
		->with('head', 'PR');
	}

}
