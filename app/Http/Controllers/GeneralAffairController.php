<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DataTables;
use File;
use PDF;
use Excel;
use Response;
use App\Driver;
use App\DriverList;
use App\DriverDetail;
use App\DriverLog;
use App\EmployeeSync;
use App\BentoQuota;
use App\User;
use App\Bento;
use App\BentoMenu;
use App\CanteenLiveCooking;
use App\CanteenLiveCookingMenu;
use App\CanteenLiveCookingAdmin;
use App\KaizenLeader;
use Carbon\Carbon;
use App\GeneralAttendance;
use App\WeeklyCalendar;

use App\CanteenItem;
use App\CanteenItemCategory;

class GeneralAffairController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');

		$this->uom = ['bag', 'bar', 'batang', 'belt', 'botol', 'bottle', 'box', 'Btg', 'Btl', 'btng', 'buah', 'buku', 'Can', 'Case', 'container', 'cps', 'day', 'days', 'dos', 'doz', 'Drum', 'dus', 'dz', 'dzn', 'EA', 'G', 'galon', 'gr', 'hari', 'hour', 'job', 'JRG', 'kaleng', 'ken', 'Kg', 'kgm', 'klg', 'L', 'Lbr', 'lbs', 'lembar', 'License', 'lisence', 'lisensi', 'lmbr', 'lonjor', 'Lot', 'ls', 'ltr', 'lubang', 'lusin', 'm', 'm2', 'm²', 'm3', 'malam', 'meter', 'ml', 'month', 'Mtr', 'night', 'OH', 'Ons', 'orang', 'OT', 'Pac', 'Pack', 'package', 'pad', 'pail', 'pair', 'pairs', 'pak', 'Pasang', 'pc', 'Pca', 'Pce', 'Pck', 'Pcs', 'Person', 'pick up', 'pil', 'ply', 'point', 'pot', 'prs', 'prsn', 'psc', 'PSG', 'psn', 'Rim', 'rol', 'roll', 'rolls', 'sak', 'sampel', 'sample', 'Set', 'Set', 'Sets', 'sheet', 'shoot', 'slop', 'sum', 'tank', 'tbg', 'time', 'titik', 'ton', 'tube', 'Um', 'Unit', 'user', 'VA', 'yard', 'zak'
	];
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

public function uploadBentoMenu(Request $request){
	$filename = "";
	$file_destination = 'images/bento_menu';

	if (count($request->file('newAttachment')) > 0) {
		try{
			$file = $request->file('newAttachment');
			$filename = date('Y-m-01', strtotime($request->get('menuDate'))).'.'.$request->input('extension');
			$file->move($file_destination, $filename);

			$menu = BentoMenu::updateOrCreate(
				[
					'due_date' => date('Y-m-01', strtotime($request->get('menuDate')))
				],
				[
					'due_date' => date('Y-m-01', strtotime($request->get('menuDate'))),
					'menu_image' => $file_destination.'/'.$filename,
					'created_by' => Auth::id()
				]
			);
			$menu->save();

			$response = array(
				'status' => true,
				'message' => 'Menu image succesfully uploaded'
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
	else{
		$response = array(
			'status' => false,
			'message' => 'Please select file to attach'
		);
		return Response::json($response);
	}
}

public function approveBento($id){
	$title = "Bento Message";
	$title_jp = "";
	$message = "Bento Request Approved";
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

			$bento->approver_id = Auth::user()->username;
			$bento->approver_name = Auth::user()->name;
			$bento->status = 'Approved';

			$bento->save();

			if($bento->department != 'YEMI'){
				$attendance = new GeneralAttendance([
					'purpose_code' => 'Bento',
					'due_date' => $bento->due_date,
					'employee_id' => $bento->employee_id,
					'created_by' => Auth::id()
				]);

				$attendance->save();
			}

			$department = $bento->department;

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

	if($department == 'YEMI'){

		$list = Bento::whereIn('id', $bento_ids)
		->select(
			db::raw('min(due_date) as min_date'),
			db::raw('max(due_date) as max_date')
		)
		->first();

		$bento_lists = Bento::leftJoin('users', 'users.username', '=', 'bentos.employee_id')
		->where('department', '=', 'YEMI')
		->where('due_date', '>=', $list->min_date)
		->where('due_date', '<=', $list->max_date)
		->select(
			'bentos.order_by',
			'bentos.order_by_name',
			'bentos.due_date',
			'bentos.employee_id',
			'bentos.employee_name',
			'users.email'
		)
		->get();

		$mail_to = array();

		foreach($bento_lists as $bento_list){
			if(!in_array($bento_list->email, $mail_to)){
				array_push($mail_to, $bento_list->email);
			}
		}

		$calendars = WeeklyCalendar::where('week_date', '>=', $list->min_date)
		->where('week_date', '<=', $list->max_date)
		->get();

		$bentos = [
			'bento_lists' => $bento_lists,
			'calendars' => $calendars
		];

		Mail::to($mail_to)->cc(['rianita.widiastuti@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com', 'merlinda.dyah@music.yamaha.com', 'novita.siswindarti@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bentos, 'bento_approve'));
	}
	else{
		$user = User::where('id', '=', $bento_lists[0]['created_by'])->first();
		Mail::to([$user->email])->cc(['rianita.widiastuti@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
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
	$message = "Bento Request Rejected";
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

			$quota = BentoQuota::where('due_date', '=', $bento->due_date)->first();
			$quota->serving_ordered = $quota->serving_ordered-1;

			$quota->save();
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
		Mail::to([$user->email])->cc(['rianita.widiastuti@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com', 'merlinda.dyah@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
	}
	else{
		Mail::to([$user->email])->cc(['rianita.widiastuti@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_confirm'));
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

public function fetchBentoOrderList(Request $request){
	if($request->get('date')){

		$now = date('Y-m-d', strtotime($request->get('date')));

		$bentos = Bento::where('due_date', '=', $now)->get();

		$response = array(
			'status' => true,
			'bentos' => $bentos
		);
		return Response::json($response);
	}
	else if($request->get('resume')){
		$period = date('Y-m', strtotime($request->get('resume')));

		$calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '=', $period)
		->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
		->get();

		// $bentos = Bento::where(db::raw('date_format(due_date, "%Y-%m")'), '=', $period)
		// ->select('order_by', 'order_by_name', 'employee_id', 'employee_name', 'due_date', db::raw('date_format(due_date, "%d") as header'))
		// ->get();

		$bentos = db::select("SELECT
			charge_to AS employee_id,
			charge_to_name AS employee_name,
			due_date,
			count( id ) AS qty 
			FROM
			`bentos` 
			WHERE
			date_format( due_date, '%Y-%m' ) = '".$period."' 
			GROUP BY
			charge_to,
			charge_to_name,
			due_date");

		$response = array(
			'status' => true,
			'bentos' => $bentos,
			'calendars' => $calendars
		);
		return Response::json($response);
	}
	else{
		$now = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
		$last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

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
		->get();

		$response = array(
			'status' => true,
			'unconfirmed' => $unconfirmed,
			'quotas' => $quotas
		);
		return Response::json($response);
	}
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

		$check_quota = array();

		foreach($order_lists as $order_list) {
			$order = explode("_", $order_list);

			array_push($check_quota, $order[1]);
		}

		$check = array_count_values($check_quota);

		foreach ($check as $key => $val) {
			$bento_quota = BentoQuota::where('due_date', '=', $key)->first();
			if($bento_quota->serving_quota-$bento_quota->serving_ordered < $val){
				$response = array(
					'status' => false,
					'message' => 'Maximum quota reached, please check your order.'
				);
				return Response::json($response);
			}
		}

		foreach($order_lists as $order_list) {
			$order = explode("_", $order_list);
			$bento_quota = BentoQuota::where('due_date', '=', $order[1])->first();

			if($order_by->role_code == 'YEMI'){
				$employee = User::where('username', '=', $order[0])->first();
				$bento = new Bento([
					'order_by' => $order_by->username,
					'order_by_name' => $order_by->name,
					'charge_to' => $order[0],
					'charge_to_name' => $employee->name,
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

				if($employee->grade_code == 'J0-'){
					$bento = new Bento([
						'order_by' => $order_by->username,
						'order_by_name' => $order_by->name,
						'charge_to' => $order[0],
						'charge_to_name' => $employee->name,
						'due_date' => $order[1],
						'employee_id' => $order[0],
						'employee_name' => $employee->name,
						'department' => $employee->department,
						'section' => $employee->section,
						'status' => 'Waiting For Confirmation',
						'created_by' => Auth::id()
					]);
				}
				else{
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
			Mail::to(['rianita.widiastuti@music.yamaha.com'])->cc(['putri.sukma.riyanti@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));
		}
		else{
			Mail::to(['rianita.widiastuti@music.yamaha.com'])->cc(['putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));
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

	//LIVE COOKINGS

public function indexLiveCooking()
{
	$title = "Live Cooking Order";
	$title_jp = "ライブクッキングの予約";

	$user = Auth::user()->username;

	$roles = CanteenLiveCookingAdmin::where('employee_id',$user)->first();
	if (count($roles) > 0) {
		if ($roles->live_cooking_role == 'leader') {
			$emp = KaizenLeader::select('employee_syncs.employee_id','employee_syncs.name')->where('leader_id',$user)->join('employee_syncs','employee_syncs.employee_id','kaizen_leaders.employee_id')->get();
		}else if($roles->live_cooking_role == 'ga'){
			$emp = EmployeeSync::select('employee_id','name')->where('employee_syncs.end_date',null)->get();
		}else if($roles->live_cooking_role == 'ofc'){
			$dept = '';
			if($roles->department != null){
				$depts =  explode(",", $roles->department);
				for ($i=0; $i < count($depts); $i++) {
					$dept = $dept."'".$depts[$i]."'";
					if($i != (count($depts)-1)){
						$dept = $dept.',';
					}
				}
				$deptin = " and `department` in (".$dept.") ";
			}
			else{
				$deptin = "";
			}
			$emp = DB::SELECT("SELECT
				* 
				FROM
				employee_syncs
				JOIN employees ON employees.employee_id = employee_syncs.employee_id 
				WHERE
				employee_syncs.end_date IS NULL 
				".$deptin."
				AND remark = 'OFC'");
		}else if($roles->live_cooking_role == 'all'){
			$emp = DB::SELECT("SELECT
				* 
				FROM
				employee_syncs
				JOIN employees ON employees.employee_id = employee_syncs.employee_id 
				WHERE
				employee_syncs.end_date IS NULL");
		}

		$live_cookings = DB::SELECT('SELECT DISTINCT
			( periode ),
			date_format( due_date, "%b %Y" ) AS period 
			FROM
			canteen_live_cooking_menus 
			WHERE
			DATE_FORMAT( NOW(), "%Y-%m" ) = periode UNION ALL
			SELECT
			DATE_FORMAT(
			DATE_SUB(
			LAST_DAY( DATE_ADD( NOW(), INTERVAL 1 MONTH ) ),
			INTERVAL DAY ( LAST_DAY( DATE_ADD( NOW(), INTERVAL 1 MONTH ) ) )- 1 DAY 
			),
			"%Y-%m"
			),
			DATE_FORMAT(
			DATE_SUB(
			LAST_DAY( DATE_ADD( NOW(), INTERVAL 1 MONTH ) ),
			INTERVAL DAY ( LAST_DAY( DATE_ADD( NOW(), INTERVAL 1 MONTH ) ) )- 1 DAY 
			),
			"%b %Y"
		) AS period');

		
		return view('general_affairs.live_cooking', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employees' => $emp,
			'roles' => $roles,
			'live_cookings' => $live_cookings,
		))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
	}else{
		return view('404');
	}
}

public function fetchLiveCookingMenu($periode)
{
	$live_cookings = DB::SELECT("SELECT
		DATE_FORMAT(due_date,'%d %M %Y') as due_date,menu_name
		FROM
		canteen_live_cooking_menus 
		WHERE
		periode = '".$periode."'");

	$monthTitle = date("F Y",strtotime($periode));
	if (count($live_cookings) > 0) {
		return view('general_affairs.live_cooking_menu', array(
			'live_cookings' => $live_cookings,
			'monthTitle' => $monthTitle,
		))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
	}else{
		return redirect('index/ga_control/live_cooking')->with('error', 'Data Belum Tersedia');
	}
}

public function downloadFileExcelLiveCooking()
{
	$file_path = public_path('data_file/TemplateMenuLiveCooking.xlsx');
	return response()->download($file_path);
}

public function uploadLiveCookingMenu(Request $request)
{
	$filename = "";
	$file_destination = 'data_file/live_cooking';

	if (count($request->file('newAttachment')) > 0) {
		try{
			$file = $request->file('newAttachment');
			$filename = 'live_cooking_'.date('YmdHisa').'.'.$request->input('extension');
			$file->move($file_destination, $filename);

			$excel = 'data_file/live_cooking/' . $filename;
			$rows = Excel::load($excel, function($reader) {
				$reader->noHeading();
				$reader->skipRows(1);

				$reader->each(function($row) {
				});
			})->toObject();

			for ($i=0; $i < count($rows); $i++) {

				$menu = CanteenLiveCookingMenu::updateOrCreate(
					[
						'due_date' => date('Y-m-d', strtotime($rows[$i][0]))
					],
					[
						'periode' => $request->get('menuDate'),
						'due_date' => date('Y-m-d', strtotime($rows[$i][0])),
						'menu_name' => $rows[$i][2],
						'serving_quota' => $rows[$i][3],
						'serving_ordered' => 0,
						'created_by' => Auth::id()
					]
				);
				$menu->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Menu succesfully uploaded'
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
	else{
		$response = array(
			'status' => false,
			'message' => 'Please select file to attach'
		);
		return Response::json($response);
	}
}

public function fetchLiveCookingOrderList(Request $request)
{
	try {

		$now = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
		$last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

		if(Auth::user()->role_code == 'GA'){
			$unconfirmed = Bento::get();
		}
		else{
			$unconfirmed = Bento::where('created_by', '=', Auth::id())
			->get();
		}

		$quotas = CanteenLiveCookingMenu::where('due_date', '>=', $now)
		->where('due_date', '<=', $last)
		->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
		->get();

		$response = array(
			'status' => true,
			'quota' => $quotas
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}
}



    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //
    // CANTEEN ORDER -> PR PO RECEIVE //


public function canteen_purchase_requisition()
{
	$title = 'Purchase Requisition Canteen';
	$title_jp = '食堂の購入依頼';

	$emp = EmployeeSync::where('employee_id', Auth::user()->username)
	->select('employee_id', 'name', 'position', 'department', 'section', 'group')
	->first();

	$staff = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
		where end_date is null and (position like '%Staff%')");

	$items = db::select("select kode_item, kategori, deskripsi from canteen_items where deleted_at is null");
	$dept = db::select("select department_name from departments where deleted_at is null");

	return view('general_affairs.report.canteen_purchase_requisition', array(
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

public function fetch_canteen_purchase_requisition(Request $request)
{
	$tanggal = "";
	$adddepartment = "";
	$restrict_dept = "";

	if (strlen($request->get('datefrom')) > 0)
	{
		$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		$tanggal = "and A.submission_date >= '" . $datefrom . " 00:00:00' ";
		if (strlen($request->get('dateto')) > 0)
		{
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
			$tanggal = $tanggal . "and A.submission_date  <= '" . $dateto . " 23:59:59' ";
		}
	}

	if ($request->get('department') != null)
	{
		$departments = $request->get('department');
		$deptlength = count($departments);
		$department = "";

		for ($x = 0;$x < $deptlength;$x++)
		{
			$department = $department . "'" . $departments[$x] . "'";
			if ($x != $deptlength - 1)
			{
				$department = $department . ",";
			}
		}
		$adddepartment = "and A.department in (" . $department . ") ";
	}


	        //Get Employee Department
	$emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
	->select('department')
	->first();

	if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || strpos($emp_dept->department, 'Procurement Department') !== false || strpos($emp_dept->department, 'Purchasing Control Department') !== false) {
		$restrict_dept = "";
	}
	else{
		$restrict_dept = "and department like '%".$emp_dept->department."%'";
	}


	$qry = "SELECT  * FROM canteen_purchase_requisitions A WHERE A.deleted_at IS NULL " . $tanggal . "" . $adddepartment . "" . $restrict_dept. " order by A.id DESC";

	$pr = DB::select($qry);

	return DataTables::of($pr)
	->editColumn('submission_date', function ($pr)
	{
		return $pr->submission_date;
	})
	->editColumn('note', function ($pr)
	{
		$note = "";
		if ($pr->note != null)
		{
			$note = $pr->note;
		}
		else
		{
			$note = '-';
		}

		return $note;
	})
	->editColumn('status', function ($pr)
	{
		$id = $pr->id;

		if ($pr->posisi == "user" && $pr->status == "approval")
		{
			return '<label class="label label-danger">Not Sent</a>';
		}
		else if($pr->posisi == "manager" && $pr->status == "approval")
		{
			return '<label class="label label-warning">Approval Manager</a>';
		}
		else if($pr->posisi == "dgm" && $pr->status == "approval")
		{
			return '<label class="label label-warning">Approval DGM</a>';
		}
		else if($pr->posisi == "gm" && $pr->status == "approval")
		{
			return '<label class="label label-warning">Approval GM</a>';
		}
		else if ($pr->status == "approval_acc")
		{
			return '<label class="label label-info">Diverifikasi Purchasing</a>';
		}
		else if ($pr->status == "received")
		{
			return '<label class="label label-success">Diterima Purchasing</a>';
		}

	})
	->addColumn('action', function ($pr)
	{
		$id = $pr->id;
		if ($pr->posisi == "user" && $pr->status == "approval") {
			return '
			<button class="btn btn-xs btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail('.$id.')"><i class="fa fa-envelope"></i> Send Email</button>
			<a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="editPR(' . $id . ')" data-toggle="tooltip" title="Edit PR"><i class="fa fa-edit"></i> Edit PR</a>
			<a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
			<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="deleteConfirmationPR('.$id.')" data-toggle="modal" data-target="#modalDeletePR"  title="Delete PR"><i class="fa fa-trash"></i> Delete PR</a>
			';
		}
		else{
			return '
			<a href="purchase_requisition/report/' . $id . '" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i> Report</a>
			';
		}


	})
	->editColumn('file', function ($pr)
	{

		$data = json_decode($pr->file);

		$fl = "";

		if ($pr->file != null)
		{
			for ($i = 0;$i < count($data);$i++)
			{
				$fl .= '<a href="files/pr/' . $data[$i] . '" target="_blank" class="fa fa-paperclip"></a>';
			}
		}
		else
		{
			$fl = '-';
		}

		return $fl;
	})
	->rawColumns(['status' => 'status', 'action' => 'action', 'file' => 'file'])
	->make(true);
}

public function fetch_item_list(Request $request)
{
	$items = CanteenItem::select('kode_item', 'deskripsi')
	->get();

	$response = array(
		'status' => true,
		'item' => $items
	);
	return Response::json($response);
}

public function prgetitemdesc(Request $request)
{
	$html = array();
	$kode_item = CanteenItem::where('kode_item', $request->kode_item)
	->get();
	foreach ($kode_item as $item)
	{
		$html = array(
			'deskripsi' => $item->deskripsi,
			'uom' => $item->uom,
			'price' => $item->harga,
			'currency' => $item->currency,
			'moq' => $item->moq
		);

	}

	return json_encode($html);
}

	//==================================//
    //            Master Item           //
    //==================================//
public function master_item()
{
	$title = 'Food Item';
	$title_jp = '';

	$item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
	->get();

	return view('general_affairs.report.canteen_purchase_item', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'uom' => $this->uom,
		'item_category' => $item_categories,
	))->with('page', 'Food Item')
	->with('head', 'Food Item');
}

public function fetch_item(Request $request)
{
	$items = CanteenItem::select('canteen_items.id', 'canteen_items.kode_item', 'canteen_items.kategori', 'canteen_items.deskripsi', 'canteen_items.uom', 'canteen_items.harga','canteen_items.currency');

	if ($request->get('keyword') != null)
	{
		$items = $items->where('deskripsi', 'like', '%' . $request->get('keyword') . '%');
	}

	if ($request->get('category') != null)
	{
		$items = $items->where('canteen_items.kategori', $request->get('category'));
	}

	if ($request->get('uom') != null)
	{
		$items = $items->whereIn('canteen_items.uom', $request->get('uom'));
	}

	$items = $items->orderBy('canteen_items.id', 'ASC')
	->get();

	return DataTables::of($items)
	->addColumn('action', function ($items)
	{
		$id = $items->id;

		if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") {
			return ' 
			<a href="purchase_item/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a> 
			<a href="purchase_item/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
			';
		}else{
			return '-';                
		}
	})
	->addColumn('image', function ($items)
	{
		$item_code = $items->kode_item;

		if (file_exists(public_path() .'/images/purchase_item/'.$item_code.'.jpg')) {
			return '<img src="'.url('images/purchase_item').'/'.$item_code.'.jpg" width="250">';
		}
		else if (file_exists(public_path() .'/images/purchase_item/'.$item_code.'.png')) {
			return '<img src="'.url('images/purchase_item').'/'.$item_code.'.png" width="250">';
		}
		else{
			return '-';
		}

	})
	->rawColumns(['action' => 'action','image' => 'image'])
	->make(true);
}

public function create_item()
{
	$title = 'Create Item';
	$title_jp = '購入アイテムを作成';

	$emp = EmployeeSync::where('employee_id', Auth::user()->username)
	->select('employee_id', 'name', 'position', 'department', 'section', 'group')
	->first();

	$item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
	->get();

	return view('general_affairs.report.canteen_create_purchase_item', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'employee' => $emp,
		'item_category' => $item_categories,
		'uom' => $this->uom
	))
	->with('page', 'Purchase Item');
}

public function create_item_post(Request $request)
{
	try
	{
		$id_user = Auth::id();

		$item = CanteenItem::create([
			'kode_item' => $request->get('item_code') , 
			'kategori' => $request->get('item_category') , 
			'deskripsi' => $request->get('item_desc') , 
			'uom' => $request->get('item_uom') , 
			'harga' => $request->get('item_price') , 
			'currency' => $request->get('item_currency') , 
			'created_by' => $id_user
		]);

		$item->save();

		$response = array(
			'status' => true,
			'datas' => "Berhasil",
			'id' => $item->id
		);
		return Response::json($response);

	}
	catch(QueryException $e)
	{
		$response = array(
			'status' => false,
			'datas' => $e->getMessage()
		);
		return Response::json($response);
	}
}

public function update_item($id)
{
	$title = 'Edit Food Item';
	$title_jp = '購入アイテムを編集';

	$item = CanteenItem::find($id);

	$emp = EmployeeSync::where('employee_id', Auth::user()->username)
	->select('employee_id', 'name', 'position', 'department', 'section', 'group')
	->first();

	$item_categories = CanteenItemCategory::select('canteen_item_categories.*')->whereNull('canteen_item_categories.deleted_at')
	->get();

	return view('general_affairs.report.canteen_edit_purchase_item', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'item' => $item,
		'employee' => $emp,
		'item_category' => $item_categories,
		'uom' => $this->uom
	))
	->with('page', 'Purchase Item');
}

public function update_item_post(Request $request)
{
	try
	{
		$id_user = Auth::id();

		$inv = CanteenItem::where('id', $request->get('id'))
		->update([
			'kode_item' => $request->get('item_code') , 
			'kategori' => $request->get('item_category') , 
			'deskripsi' => $request->get('item_desc') , 
			'uom' => $request->get('item_uom') , 
			'harga' => $request->get('item_price') , 
			'currency' => $request->get('item_currency') , 
			'created_by' => $id_user
		]);

		$response = array(
			'status' => true,
			'datas' => "Berhasil"
		);
		return Response::json($response);

	}
	catch(QueryException $e)
	{
		$response = array(
			'status' => false,
			'datas' => $e->getMessage()
		);
		return Response::json($response);
	}
}

public function delete_item($id)
{
	$items = CanteenItem::find($id);
	$items->delete();

	return redirect('/canteen/purchase_item')
	->with('status', 'Food Item has been deleted.')
	->with('page', 'Food Item');
}

public function get_kode_item(Request $request)
{
	$kategori = $request->kategori;

	$query = "SELECT kode_item FROM `canteen_items` where kategori='$kategori' order by kode_item DESC LIMIT 1";
	$nomorurut = DB::select($query);

	if ($nomorurut != null)
	{
		$nomor = substr($nomorurut[0]->kode_item, -3);
		$nomor = $nomor + 1;
		$nomor = sprintf('%03d', $nomor);

	}
	else
	{
		$nomor = "001";
	}

	$result['no_urut'] = $nomor;

	return json_encode($result);
}

    //==================================//
    //       Create Item Category       //
    //==================================//
public function create_item_category()
{
	$title = 'Create Item Category';
	$title_jp = '購入アイテムの種類を作成';

	return view('general_affairs.report.canteen_create_category_item', array(
		'title' => $title,
		'title_jp' => $title_jp
	))->with('page', 'Food Item');
}

public function create_item_category_post(Request $request)
{
	try
	{
		$id_user = Auth::id();

		$item_category = CanteenItemCategory::create([
			'category_id' => $request->get('category_id') , 
			'category_name' => $request->get('category_name') , 
			'created_by' => $id_user
		]);

		$item_category->save();

		$response = array(
			'status' => true,
			'datas' => "Berhasil"
		);
		return Response::json($response);
	}
	catch(QueryException $e)
	{
		$response = array(
			'status' => false,
			'datas' => $e->getMessage()
		);
		return Response::json($response);
	}
}
}
