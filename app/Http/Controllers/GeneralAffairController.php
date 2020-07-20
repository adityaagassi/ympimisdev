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
use Response;

class GeneralAffairController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
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

				Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'dicky.kurniawan@music.yamaha.com'])
				->cc(['aditya.agassi@music.yamaha.com'])
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

				Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com', 'dicky.kurniawan@music.yamaha.com'])
				->cc(['aditya.agassi@music.yamaha.com'])
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

	public function indexLiveCooking(){
		$title = "Live Cooking";
		$title_jp = "";

		return view('general_affairs.live_cooking', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'GA Control')->with('page', 'Live Cooking');
	}

	public function indexBento(){
		$title = "Bento";
		$title_jp = "";

		return view('general_affairs.bento', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'GA Control')->with('page', 'Bento');
	}
}
