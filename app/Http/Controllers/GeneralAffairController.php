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
use App\KaizenLeader;
use Carbon\Carbon;
use App\GeneralAttendance;
use App\WeeklyCalendar;
use App\CodeGenerator;
use App\CanteenPurchaseRequisition;
use App\CanteenPurchaseRequisitionItem;
use App\CanteenItem;
use App\CanteenItemCategory;
use App\AccBudget;
use App\Employee;
use App\CanteenBudgetHistory;
use App\AccExchangeRate;

class GeneralAffairController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');

		$this->uom = ['bag', 'bar', 'batang', 'belt', 'botol', 'bottle', 'box', 'Btg', 'Btl', 'btng', 'buah', 'buku', 'Can', 'Case', 'container', 'cps', 'day', 'days', 'dos', 'doz', 'Drum', 'dus', 'dz', 'dzn', 'EA', 'G', 'galon', 'gr', 'hari', 'hour', 'job', 'JRG', 'kaleng', 'ken', 'Kg', 'kgm', 'klg', 'L', 'Lbr', 'lbs', 'lembar', 'License', 'lisence', 'lisensi', 'lmbr', 'lonjor', 'Lot', 'ls', 'ltr', 'lubang', 'lusin', 'm', 'm2', 'm²', 'm3', 'malam', 'meter', 'ml', 'month', 'Mtr', 'night', 'OH', 'Ons', 'orang', 'OT', 'Pac', 'Pack', 'package', 'pad', 'pail', 'pair', 'pairs', 'pak', 'Pasang', 'pc', 'Pca', 'Pce', 'Pck', 'Pcs', 'Person', 'pick up', 'pil', 'ply', 'point', 'pot', 'prs', 'prsn', 'psc', 'PSG', 'psn', 'Rim', 'rol', 'roll', 'rolls', 'sak', 'sampel', 'sample', 'Set', 'Set', 'Sets', 'sheet', 'shoot', 'slop', 'sum', 'tank', 'tbg', 'time', 'titik', 'ton', 'tube', 'Um', 'Unit', 'user', 'VA', 'yard', 'zak'
	];

	$this->dgm = 'PI0109004';
	$this->gm = 'PI1206001';
	$this->gm_acc = 'PI1712018';
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

public function inputBentoMenu(Request $request){
	try{
		$bento_menu = BentoQuota::where('due_date', '=', $request->get('date'))
		->update([
			'serving_quota' => $request->get('quota'),
			'menu' => $request->get('menu')
		]);

		$response = array(
			'status' => true,
			'message' => 'Menu updated',
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

public function indexBentoApprove(){
	$title = 'Bento Approval';
	$title_jp = '';

	return view('general_affairs.bento_approve', array(
		'title' => $title,
		'title_jp' => $title_jp
	))->with('head', 'Bento Request');
}

public function approveBento(Request $request){
	try{

		$ids = array();


		if(count($request->get('rejected'))>0){
			foreach ($request->get('rejected') as $reject) {
				array_push($ids, $reject);
			}

			$rejected = Bento::whereIn('id', $request->get('rejected'))
			->update([
				'status' => 'Rejected',
				'approver_id' => Auth::user()->username,
				'approver_name' => Auth::user()->name
			]);	
		}

		if(count($request->get('approved'))>0){
			foreach ($request->get('approved') as $approve) {
				array_push($ids, $approve);
			}

			$approved = Bento::whereIn('id', $request->get('approved'))
			->update([
				'status' => 'Approved',
				'approver_id' => Auth::user()->username,
				'approver_name' => Auth::user()->name
			]);
		}

		$list_jp = Bento::whereIn('id', $ids)
		->where('grade_code', '=', 'J0-')
		->select(
			db::raw('min(due_date) as min_date'),
			db::raw('max(due_date) as max_date')
		)
		->first();

		$lists = Bento::whereIn('bentos.id', $ids)
		->where('bentos.grade_code', '!=', 'J0-')
		->leftJoin('users', 'users.username', '=', 'bentos.order_by')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'bentos.order_by')
		->select(
			'users.email',
			'employee_syncs.phone',
			db::raw('min(bentos.due_date) as min_date'),
			db::raw('max(bentos.due_date) as max_date')		
		)
		->groupBy(
			'users.email',
			'employee_syncs.phone'
		)
		->get();
		
		if(strlen($list_jp->max_date) > 0){
			$first = date('Y-m-01', strtotime($list_jp->max_date));
			$last = date('Y-m-t', strtotime($list_jp->max_date));
			$bento_lists = db::select("SELECT
				j.employee_id,
				j.employee_name,
				u.email,
				b.due_date,
				b.revise,
				b.status 
				FROM
				japaneses AS j
				LEFT JOIN ( SELECT * FROM bentos WHERE due_date >= '".$first."' AND due_date <= '".$last."' ) AS b ON b.employee_id = j.employee_id
				LEFT JOIN users AS u ON u.username = j.employee_id");

			$calendars = WeeklyCalendar::where('week_date', '>=', $first)
			->where('week_date', '<=', $last)
			->get();

			$mail_to = array();
			foreach($bento_lists as $bento_list){
				if(!in_array($bento_list->email, $mail_to)){
					array_push($mail_to, $bento_list->email);
				}
			}

			$bentos = [
				'bento_lists' => $bento_lists,
				'calendars' => $calendars
			];

			Mail::to($mail_to)
			->cc([
				'rianita.widiastuti@music.yamaha.com', 
				'putri.sukma.riyanti@music.yamaha.com', 
				'prawoto@music.yamaha.com',
				'budhi.apriyanto@music.yamaha.com', 
				'helmi.helmi@music.yamaha.com',
				'merlinda.dyah@music.yamaha.com', 
				'novita.siswindarti@music.yamaha.com'
			])
			->bcc([
				'aditya.agassi@music.yamaha.com', 
				'anton.budi.santoso@music.yamaha.com',
				'agus.yulianto@music.yamaha.com'
			])
			->send(new SendEmail($bentos, 'bento_approve'));
		}

		if(count($lists) > 0){
			foreach($lists as $list){
				$in_id = "";

				for($x = 0; $x < count($ids); $x++) {
					$in_id = $in_id."'".$ids[$x]."'";
					if($x != count($ids)-1){
						$in_id = $in_id.",";
					}
				}

				$bento_lists = db::select("SELECT
					b.employee_id,
					b.employee_name,
					b.due_date,
					b.revise,
					b.status,
					b.email,
					es.grade_code 
					FROM
					bentos AS b
					LEFT JOIN employee_syncs AS es ON b.employee_id = es.employee_id 
					LEFT JOIN users AS u ON b.order_by = u.username 
					WHERE
					b.id in (".$in_id.")
					AND u.email = '".$list->email."'");

				$calendars = WeeklyCalendar::where('week_date', '>=', $list->min_date)
				->where('week_date', '<=', $list->max_date)
				->get();

				$bentos = [
					'bento_lists' => $bento_lists,
					'calendars' => $calendars
				];

				foreach ($bento_lists as $bento_list) {
					if($bento_list->status == 'Rejected'){
						$quota = BentoQuota::where('due_date', '=', $bento_list->due_date)->first();
						$quota->serving_ordered = $quota->serving_ordered-1;
						$quota->save();
					}
					else{
						$attendance = new GeneralAttendance([
							'purpose_code' => 'Bento',
							'due_date' => $bento_list->due_date,
							'employee_id' => $bento_list->employee_id,
							'created_by' => Auth::id()
						]);

						$attendance->save();
					}
				}

				if(strpos($list->email, '@music.yamaha.com') == false){
					$curl = curl_init();

					if(substr($list->phone, 0, 1) == '+' ){
						$phone = substr($list->phone, 1, 15);
					}
					else if(substr($list->phone, 0, 1) == '0'){
						$phone = "62".substr($list->phone, 1, 15);
					}
					else{
						$phone = $list->phone;
					}

					curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://app.whatspie.com/api/messages',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						// CURLOPT_POSTFIELDS => 'receiver=6282244167224&device=628113669871&message=Order%20bento%20anda%20tanggal%202021-05-06.%20Telah%20dikonfirmasi.%0ASilahkan%20cek%20pada%20MIRAI.%0A%0AYMPI%20GA%20Dept.&type=chat',
						CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=628113669871&message=Order%20bento%20anda%20telan%20dikofirmasi.%0ASilahkan%20cek%20kembali%20di%20MIRAI.%0A%0A-YMPI%20GA%20Dept.-&type=chat',
						CURLOPT_HTTPHEADER => array(
							'Accept: application/json',
							'Content-Type: application/x-www-form-urlencoded',
							'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
						),
					));

					curl_exec($curl);
				}
				else{
					Mail::to($list->email)->cc([
						'rianita.widiastuti@music.yamaha.com', 
						'putri.sukma.riyanti@music.yamaha.com'
					])
					->bcc([
						'aditya.agassi@music.yamaha.com', 
						'anton.budi.santoso@music.yamaha.com'
					])
					->send(new SendEmail($bentos, 'bento_approve'));
				}
			}
		}
		$response = array(
			'status' => true,
			'message' => 'Order Has Been Confirmed'
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
		Mail::to([$user->email])->cc([
			'rianita.widiastuti@music.yamaha.com', 
			'putri.sukma.riyanti@music.yamaha.com', 
			'merlinda.dyah@music.yamaha.com', 
			'hiroshi.ura@music.yamaha.com',
			'prawoto@music.yamaha.com'])
		->bcc([
			'aditya.agassi@music.yamaha.com', 
			'anton.budi.santoso@music.yamaha.com'
		])
		->send(new SendEmail($bento_lists, 'bento_confirm'));
	}
	else{
		Mail::to([$user->email])->cc([
			'rianita.widiastuti@music.yamaha.com', 
			'putri.sukma.riyanti@music.yamaha.com'
		])
		->bcc([
			'aditya.agassi@music.yamaha.com', 
			'anton.budi.santoso@music.yamaha.com'
		])
		->send(new SendEmail($bento_lists, 'bento_confirm'));
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
		->select(db::raw('username as employee_id'), 'name', db::raw('"J0-" as grade_code'))
		->get();

		$location = 'YEMI';
	}
	else{
		if(Auth::user()->role_code == 'GA' || Auth::user()->role_code == 'MIS'){
			$yemi = User::where('role_code', '=', 'YEMI')
			->orderBy('name', 'asc')
			->select(db::raw('username as employee_id'), 'name', db::raw('"J0-" as grade_code'));

			$employees = EmployeeSync::orderBy('name', 'asc')
			->whereNull('end_date')
			->select('employee_id', 'name', 'grade_code')
			->union($yemi)
			->get();
		}
		else{
			$employees = EmployeeSync::orderBy('name', 'asc')
			->whereNull('end_date')
			->where('grade_code', '!=', 'J0-')
			->select('employee_id', 'name', 'grade_code')
			->get();
		}

		// $employees = db::select("SELECT
		// 	* 
		// 	FROM
		// 	(
		// 	SELECT
		// 	employee_id,
		// 	name,
		// 	grade_code 
		// 	FROM
		// 	`employee_syncs` 
		// 	WHERE
		// 	end_date IS NULL UNION ALL
		// 	SELECT
		// 	username AS employee_id,
		// 	name,
		// 	'J0-' AS grade_code 
		// 	FROM
		// 	users 
		// 	WHERE
		// 	role_code = 'YEMI' 
		// 	ORDER BY
		// 	employee_id ASC 
		// ) AS e");

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

public function fetchBentoOrderLog(Request $request){
	$bentos = Bento::whereNull('deleted_at');

	if(strlen($request->get('dateFrom') > 0)){
		$bentos = $bentos->where('due_date', '>=', $request->get('dateFrom'));
	}

	if(strlen($request->get('dateTo') > 0)){
		$bentos = $bentos->where('due_date', '<=', $request->get('dateTo'));
	}

	if($request->get('status') != null){
		$bentos = $bentos->whereIn('status', $request->get('status'));

	}

	$bentos = $bentos->get();

	$response = array(
		'status' => true,
		'bentos' => $bentos
	);
	return Response::json($response);
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

public function fetchBentoOrderCount(){
	if(Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'GA'){
		$count = Bento::where('status', '=', 'Waiting')->count('id');

		$response = array(
			'status' => true,
			'count' => $count
		);
		return Response::json($response);
	}
	else{
		$response = array(
			'status' => false
		);
		return Response::json($response);
	}
}

public function fetchBentoOrderEdit(Request $request){
	$bento = Bento::where('due_date', '=', $request->get('due_date'))
	->where('employee_name', '=', $request->get('employee_name'))
	->first();

	$response = array(
		'status' => true,
		'bento' => $bento
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
		if(Auth::user()->role_code == 'GA' || Auth::user()->role_code == 'HR' || Auth::user()->role_code == 'MIS'){
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
				AND status = 'Approved'
				AND deleted_at IS NULL
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
			$response = array(
				'status' => false,
				'message' => 'You do not have permission to access this data'
			);
			return Response::json($response);
		}
	}
	else{
		$now = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
		$last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

		if(Auth::user()->role_code == 'GA'){
			$unconfirmed = Bento::get();
		}
		else{
			// $unconfirmed = Bento::where('created_by', '=', Auth::id())
			// ->get();

			$unconfirmed = db::select("SELECT
				* 
				FROM
				bentos 
				WHERE
				created_by = '".Auth::id()."' 
				OR employee_id = '".Auth::user()->username."' 
				OR order_by = '".Auth::user()->username."'");
		}

		// $quotas = BentoQuota::where('due_date', '>=', $now)
		// ->where('due_date', '<=', $last)
		// ->where('remark', '!=', 'H')
		// ->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
		// ->get();

		$menus = BentoQuota::whereNull('deleted_at')->get();

		$response = array(
			'status' => true,
			'unconfirmed' => $unconfirmed,
			// 'quotas' => $quotas,
			'menus' => $menus
		);
		return Response::json($response);
	}
}

public function editBentoOrder(Request $request){
	try{

		if($request->get('status') == 'edit'){
			$bento = Bento::where('id', '=', $request->get("id"))->first();
			$employee_id = explode('_', $request->get('employee_id'));
			$bento_quota_old = BentoQuota::where('due_date', '=', $bento->due_date)->first();
			$bento_quota_new = BentoQuota::where('due_date', '=', $request->get('due_date'))->first();
			$now = date('Y-m-d H:i:s');
			$limit = date('Y-m-d 09:00:00', strtotime($bento->due_date));

			$date_old = date('Y-m-d', strtotime($bento->due_date));
			if(
				$bento->order_by == $request->get('order_by') &&
				$bento->order_by_name == $request->get('order_by_name') &&
				$bento->charge_to == $request->get('charge_to') &&
				$bento->charge_to_name == $request->get('charge_to_name') &&
				$bento->due_date == $request->get('due_date') &&
				$bento->employee_id == $employee_id[0]
			){
				$response = array(
					'status' => false,
					'message' => 'There is no change in your order<br>ご注文に変更なし'
				);
				return Response::json($response);	
			}

			if(strlen($bento_quota_new->menu) <= 0){
				$response = array(
					'status' => false,
					'message' => 'There is order(s) without menu or in holiday.'
				);
				return Response::json($response);
			}

			if($now > $limit){
				$response = array(
					'status' => false,
					'message' => 'Can not edit order, time limit reached. Max change request on day before 09:00',
				);
				return Response::json($response);
			}

			// $diff = date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($bento->due_date))));
			// if($diff->format("%R%a") <= 0){
			// 	$response = array(
			// 		'status' => false,
			// 		'message' => 'Your order exceeded time limit. Max order change day before'
			// 	);
			// 	return Response::json($response);
			// }

			if($employee_id[2] != 'J0-'){
				if($bento_quota_new->serving_quota-$bento_quota_new->serving_ordered <= 0){
					$response = array(
						'status' => false,
						'message' => 'Maximum quota reached, please check your order.'
					);
					return Response::json($response);
				}
				$bento_quota_old->serving_ordered = $bento_quota_old->serving_ordered-1;
				$bento_quota_old->save();

				if($bento->status == 'Approved'){
					$attendance = GeneralAttendance::where('due_date', '=', $date_old)
					->where('employee_id', '=', $bento->employee_id)
					->first();
					$attendance->forceDelete();
				}
			}

			$bento->order_by = $request->get('order_by');
			$bento->order_by_name = $request->get('order_by_name');
			$bento->charge_to = $request->get('charge_to');
			$bento->charge_to_name = $request->get('charge_to_name');
			$bento->due_date = $request->get('due_date');
			$bento->revise = $bento->revise+1;

			$bento->employee_id = $employee_id[0];

			$employee = EmployeeSync::where('employee_id', '=', $employee_id[0])->first();


			if($employee != ""){
				$bento->employee_name = $employee->name;
				$bento->department = $employee->department;
				$bento->section = $employee->section;
			}
			else{
				$bento->employee_name = $employee_id[1];
				$bento->department = 'YEMI';
				$bento->section = 'YEMI';
			}

			if($employee_id[2] != 'J0-'){
				$bento_quota = BentoQuota::where('due_date', '=', $request->get('due_date'))->first();
				$bento_quota->serving_ordered = $bento_quota->serving_ordered+1;
				$bento_quota->save();

				if($bento->status == 'Approved'){
					if($date_old == $request->get('due_date')){
						$attendance = new GeneralAttendance([
							'purpose_code' => 'Bento',
							'due_date' => $request->get('due_date'),
							'employee_id' => $employee_id[0],
							'created_by' => Auth::id()
						]);

						$attendance->save();
					}
				}
			}

			if($date_old != $request->get('due_date')){
				$bento->status = 'Waiting';
				$bento->approver_id = null;
				$bento->approver_name = null;
			}

			$bento->save();

			$response = array(
				'status' => true,
				'message' => 'Your order has been edited, please wait for approval'
			);
			return Response::json($response);				
		}

		if($request->get('status') == 'cancel'){

			$bento = Bento::where('id', '=', $request->get('id'))->first();

			$now = date('Y-m-d H:i:s');
			$limit = date('Y-m-d 09:00:00', strtotime($bento->due_date));

			if($now > $limit){
				$response = array(
					'status' => false,
					'message' => 'Can not cancel order, time limit reached. Max change on day 09:00',
				);
				return Response::json($response);
			}

			if($request->get('location') != 'YEMI'){
				$bento_quota = BentoQuota::where('due_date', '=', $bento->due_date)->first();
				$bento_quota->serving_ordered = $bento_quota->serving_ordered-1;
				$bento_quota->save();
				if($bento->status == 'Approved'){
					$attendance = GeneralAttendance::where('due_date', '=', $bento->due_date)
					->where('employee_id', '=', $bento->employee_id)
					->first();
					$attendance->forceDelete();
				}
			}

			$bento->status = 'Cancelled';
			$bento->revise = $bento->revise+1;

			$bento->save();

			$user = User::where('username', '=', $bento->order_by)->first();
			$bento_lists = Bento::where('id', '=', $request->get('id'))->get();

			// if($request->get('location') != 'YEMI'){
			// 	Mail::to([$user->email])->cc([
			// 		'rianita.widiastuti@music.yamaha.com', 
			// 		'putri.sukma.riyanti@music.yamaha.com'
			// 	])
			// 	->bcc([
			// 		'aditya.agassi@music.yamaha.com', 
			// 		'anton.budi.santoso@music.yamaha.com'
			// 	])
			// 	->send(new SendEmail($bento_lists, 'bento_confirm'));
			// }
			// else{
			// 	Mail::to([$user->email])->cc([
			// 		'rianita.widiastuti@music.yamaha.com', 
			// 		'putri.sukma.riyanti@music.yamaha.com', 
			// 		'merlinda.dyah@music.yamaha.com', 
			// 		'prawoto@music.yamaha.com'])
			// 	->bcc([
			// 		'aditya.agassi@music.yamaha.com', 
			// 		'anton.budi.santoso@music.yamaha.com'
			// 	])
			// 	->send(new SendEmail($bento_lists, 'bento_confirm'));
			// }

			$response = array(
				'status' => true,
				'message' => 'Your order has been cancelled'
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

		$code_generator = CodeGenerator::where('note','=','bento')->first();
		if($code_generator->prefix != date('ym')){
			$code_generator->prefix = date('ym');
			$code_generator->index = '0';
			$code_generator->save();
		}
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
		$order_id = $code_generator->prefix . $number;

		$check_quota = array();
		$check_menu = array();

		foreach($order_lists as $order_list) {
			$order = explode("_", $order_list);
			if($order[2] != 'J0-'){
				array_push($check_quota, $order[1]);	
			}
			array_push($check_menu, $order[1]);
		}

		$check = array_count_values($check_quota);
		$check2 = array_count_values($check_menu);

		if(count($check) > 0){
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
		}

		foreach ($check2 as $key => $val) {
			$bento_quota = BentoQuota::where('due_date', '=', $key)->first();
			$diff = date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($key))));
			if($diff->format("%R%a") <= 0){
				$response = array(
					'status' => false,
					'message' => 'Your order exceeded time limit. Max order one day before'
				);
				return Response::json($response);
			}
			if(strlen($bento_quota->menu) <= 0){
				$response = array(
					'status' => false,
					'message' => 'There is order(s) with no menu yet or in holiday.'
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
					'order_id' => $order_id,
					'order_by' => $order_by->username,
					'order_by_name' => $order_by->name,
					'charge_to' => $order[0],
					'charge_to_name' => $employee->name,
					'due_date' => $order[1],
					'employee_id' => $order[0],
					'employee_name' => $employee->name,
					'grade_code' => $order[2],
					'email' => $employee->email,
					'department' => 'YEMI',
					'section' => 'YEMI',
					'status' => 'Waiting',
					'created_by' => Auth::id()
				]);
			}
			else{
				$employee = EmployeeSync::where('employee_id', '=', $order[0])
				->leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
				->select('employee_syncs.name', 'employee_syncs.department', 'employee_syncs.section', 'users.email')
				->first();

				if($employee->grade_code == 'J0-'){
					$bento = new Bento([
						'order_id' => $order_id,
						'order_by' => $order_by->username,
						'order_by_name' => $order_by->name,
						'charge_to' => $order[0],
						'charge_to_name' => $employee->name,
						'due_date' => $order[1],
						'employee_id' => $order[0],
						'employee_name' => $employee->name,
						'email' => $employee->email,
						'department' => $employee->department,
						'section' => $employee->section,
						'status' => 'Waiting',
						'created_by' => Auth::id()
					]);
				}
				else{
					$bento = new Bento([
						'order_id' => $order_id,
						'order_by' => $order_by->username,
						'order_by_name' => $order_by->name,
						'charge_to' => $charge_to->username,
						'charge_to_name' => $charge_to->name,
						'due_date' => $order[1],
						'employee_id' => $order[0],
						'employee_name' => $employee->name,
						'email' => $employee->email,
						'department' => $employee->department,
						'section' => $employee->section,
						'status' => 'Waiting',
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
					'order_id' => $bento->order_id,
					'order_by' => $bento->order_by,
					'order_by_name' => $bento->order_by_name,
					'charge_to' => $bento->charge_to,
					'charge_to_name' => $bento->charge_to_name,
					'due_date' => $bento->due_date,
					'employee_id' => $bento->employee_id,
					'employee_name' => $bento->employee_name,
					'email' => $bento->email,
					'department' => $bento->department,
					'section' => $bento->section,
					'status' => $bento->status,
					'created_by' => $bento->created_by
				]);
		}

		$code_generator->index = $code_generator->index+1;
		$code_generator->save();

		// if($order_by->role_code == 'YEMI'){
		// 	Mail::to(['rianita.widiastuti@music.yamaha.com'])->cc(['putri.sukma.riyanti@music.yamaha.com', 'prawoto@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));
		// }
		// else{
		// 	Mail::to(['rianita.widiastuti@music.yamaha.com'])->cc(['putri.sukma.riyanti@music.yamaha.com'])->bcc(['aditya.agassi@music.yamaha.com', 'anton.budi.santoso@music.yamaha.com'])->send(new SendEmail($bento_lists, 'bento_request'));
		// }

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

	$menus = CanteenLiveCookingMenu::where('periode',date('Y-m'))->get();
	$today = CanteenLiveCookingMenu::where('due_date',date('Y-m-d'))->first();
	$dateOrder = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-01'))));

	$t1 = strtotime(date('Y-m-d'));
	$t2 = strtotime(date('Y-'.date('m',strtotime('first day of +1 month')).'-01'));

	$interval = $t2 - $t1;
	$total_sec = abs($t2-$t1);
	$total_min = floor($total_sec/60);
	$total_hour = floor($total_min/60);
	$total_day = floor($total_hour/24);
	$month_now = date('Y-m');
	return view('general_affairs.live_cooking', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'menus' => $menus,
		'user' => $user,
		'today' => $today,
		'total_day' => 6,
		'monthTitle' => date('F Y'),
		'month_now' => $month_now
	))->with('head', 'GA Control')->with('page', 'Live Cooking Order');
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

		$now = date('Y-m-d', strtotime(carbon::now()));
		$last = date('Y-m-d', strtotime(carbon::now()->addDays(20)));

		$periode = date('Y-m');
		$nextPeriode = date('Y-'.date('m',strtotime('first day of +1 month')));

		// $roles = CanteenLiveCookingAdmin::where('employee_id',Auth::user()->username)->first();

		// if ($roles->live_cooking_role == 'ga') {
		// $resumes = DB::SELECT("SELECT
		// 	canteen_live_cookings.id AS id_live,
		// 	order_by,
		// 	emp_by.`name` AS name_by,
		// 	order_for,
		// 	emp_for.`name` AS name_for,
		// 	due_date,
		// 	`status`,
		// 	remark,
		// 	emp_by.department,
		// 	emp_by.section
		// 	FROM
		// 	canteen_live_cookings
		// 	LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
		// 	LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
		// 	where DATE_FORMAT(due_date,'%Y-%m') = '".$periode."'
		// 	order by due_date");

		// 	$quotas = CanteenLiveCookingMenu::where('due_date', '>=', $now)
		// 	->where('due_date', '<=', $last)
		// 	->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
		// 	->get();
		// }else if($roles->live_cooking_role == 'all'){
		// 	$resumes = DB::SELECT("SELECT
		// 		canteen_live_cookings.id AS id_live,
		// 		order_by,
		// 		emp_by.`name` AS name_by,
		// 		order_for,
		// 		emp_for.`name` AS name_for,
		// 		due_date,
		// 		`status`,
		// 		remark,
		// 		emp_by.department,
		// 		emp_by.section
		// 		FROM
		// 		canteen_live_cookings
		// 		LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
		// 		LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
		// 		where DATE_FORMAT(due_date,'%Y-%m') = '".$periode."'
		// 		order by due_date");
		// 	$quotas = CanteenLiveCookingMenu::where('due_date', '>=', $now)
		// 	->where('due_date', '<=', $last)
		// 	->select(db::raw('date_format(due_date, "%a, %d %b %Y") as due_date'), 'serving_quota', 'serving_ordered')
		// 	->get();
		// }else if($roles->live_cooking_role == 'prod'){
		$resumes = DB::SELECT("SELECT
			canteen_live_cookings.id AS id_live,
			order_by,
			emp_by.`name` AS name_by,
			order_for,
			emp_for.`name` AS name_for,
			due_date,
			`status`,
			remark,
			emp_by.department,
			emp_by.section
			FROM
			canteen_live_cookings
			LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
			LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
			WHERE
			order_by = '".Auth::user()->username."'
			and DATE_FORMAT(due_date,'%Y-%m') = '".$periode."'
			order by due_date ");

		$quotas = DB::SELECT("SELECT
			date_format( due_date, '%a, %d %b %Y' ) AS due_date,
			due_date as due_dates,
			serving_quota,serving_ordered 
			FROM
			canteen_live_cooking_menus 
			WHERE
			due_date >= '".$now."' 
			AND due_date <= '".$last."' 
			GROUP BY
			due_date");

		$menus = DB::SELECT("SELECT
			a.* 
			FROM
			(
			SELECT
			week_date,
			weekly_calendars.remark,
			( SELECT menu_name FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS menu_name,
			( SELECT serving_quota FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_quota,
			( SELECT serving_ordered FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered 
			FROM
			weekly_calendars 
			WHERE
			DATE_FORMAT( week_date, '%Y-%m' ) = '".$periode."' UNION ALL
			SELECT
			week_date,
			weekly_calendars.remark,
			( SELECT menu_name FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS menu_name,
			( SELECT serving_quota FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_quota,
			( SELECT serving_ordered FROM canteen_live_cooking_menus WHERE due_date = week_date ) AS serving_ordered 
			FROM
			weekly_calendars 
			WHERE
			DATE_FORMAT( week_date, '%Y-%m' ) = '".$nextPeriode."' 
		) a");
		// }

		$calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '=', $periode)
		->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
		->get();

		$response = array(
			'status' => true,
			'resumes' => $resumes,
			'quota' => $quotas,
			'calendars' => $calendars,
			'menus' => $menus,
			'now' => $now
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

public function fetchLiveCookingEmployees(Request $request)
{
	try {
		// if ($request->get('roles') == 'prod') {
		// 	$emp = DB::SELECT("SELECT
		// 		* 
		// 		FROM
		// 		employee_syncs
		// 		JOIN employees ON employees.employee_id = employee_syncs.employee_id 
		// 		WHERE
		// 		( employee_syncs.end_date IS NULL AND remark != 'OFC' AND department = '".$request->get('department')."' AND section = '".$request->get('section')."' ) 
		// 		OR (
		// 		employee_syncs.end_date IS NULL 
		// 		AND remark IS NULL 
		// 		AND department = '".$request->get('department')."' 
		// 		AND section = '".$request->get('section')."')");
		// }else if($request->get('roles') == 'ga'){
		// 	$emp = EmployeeSync::select('employee_id','name')->where('employee_syncs.end_date',null)->get();
		// }else if($request->get('roles') == 'ofc'){
		// 	$dept = '';
		// 	if($roles->department != null){
		// 		$depts =  explode(",", $roles->department);
		// 		for ($i=0; $i < count($depts); $i++) {
		// 			$dept = $dept."'".$depts[$i]."'";
		// 			if($i != (count($depts)-1)){
		// 				$dept = $dept.',';
		// 			}
		// 		}
		// 		$deptin = " and `department` in (".$dept.") ";
		// 	}
		// 	else{
		// 		$deptin = "";
		// 	}
		// 	$emp = DB::SELECT("SELECT
		// 		* 
		// 		FROM
		// 		employee_syncs
		// 		JOIN employees ON employees.employee_id = employee_syncs.employee_id 
		// 		WHERE
		// 		employee_syncs.end_date IS NULL 
		// 		".$deptin."
		// 		AND remark = 'OFC'");
		// }else if($request->get('roles') == 'all'){
		// 	$emp = DB::SELECT("SELECT
		// 		* 
		// 		FROM
		// 		employee_syncs
		// 		JOIN employees ON employees.employee_id = employee_syncs.employee_id 
		// 		WHERE
		// 		employee_syncs.end_date IS NULL");
		// }

		$emp = DB::SELECT("SELECT
			canteen_live_cookings.id AS id_live,
			order_by,
			emp_by.`name` AS name_by,
			order_for,
			emp_for.`name` AS name_for,
			due_date,
			`status`,
			remark,
			emp_by.department,
			emp_by.section
			FROM
			canteen_live_cookings
			LEFT JOIN employee_syncs emp_by ON emp_by.employee_id = canteen_live_cookings.order_by
			LEFT JOIN employee_syncs emp_for ON emp_for.employee_id = canteen_live_cookings.order_for
			WHERE
			emp_for.name = '".$request->get('for_name')."'
			and due_date = '".$request->get('due_date')."'
			order by due_date");

		$response = array(
			'status' => true,
			'employees' => $emp,
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

public function inputLiveCookingOrder(Request $request)
{
	try{
		$quotas = CanteenLiveCookingMenu::where('due_date',$request->get('due_date'))->first();
		if ($quotas->serving_ordered < $quotas->serving_ordered ) {
			$live_cooking = CanteenLiveCooking::create(
				[
					'order_by' => strtoupper($request->get('order_by')),
					'due_date' => $request->get('date'),
					'order_for' => strtoupper($request->get('order_by')),
					'status' => 'Confirmed',
					'created_by' => Auth::id()
				]
			);
			$live_cooking->save();
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Order Anda pada tanggal '.$request->get('quota').' telah melebihi kuota.',
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

public function randomLiveCooking(Request $request)
{
	try {
		$periode = $request->get('menuDateRandom');
		$dateFrom = $request->get('dateFromRandom');
		$dateTo = $request->get('dateToRandom');

		$menus = CanteenLiveCookingMenu::where('periode',$periode)->where('due_date','>=',$dateFrom)->where('due_date','<=',$dateTo)->get();
		foreach ($menus as $val) { 
			$emp = DB::SELECT("SELECT
				employees.id,
				employee_syncs.employee_id,
				employee_syncs.`name`,
				sunfish_shift_syncs.shiftdaily_code 
				FROM
				employee_syncs
				LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id
				LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id 
				WHERE
				employees.live_cooking = 0 
				AND employee_syncs.end_date IS NULL 
				AND sunfish_shift_syncs.shift_date = '".$val->due_date."' 
				AND shiftdaily_code LIKE '%Shift_1%' 
				ORDER BY
				RAND()
				LIMIT ".$val->serving_quota."");
			$datanow = CanteenLiveCooking::where('due_date',$val->due_date)->forceDelete();
			// if (count($datanow) > 0) {
			// 	$datanow->forceDelete();
			// }
			foreach ($emp as $key) {
				$live_cooking = CanteenLiveCooking::create(
					[
						'order_by' => $key->employee_id,
						'due_date' => $val->due_date,
						'order_for' => $key->employee_id,
						'status' => 'Confirmed',
						'created_by' => Auth::id()
					]
				);
				$live_cooking->save();

				$empys = Employee::where('id',$key->id)->first();
				$empys->live_cooking = 1;
				$empys->save();
			}
		}
		// $emp = EmployeeSync::where('')
		$response = array(
			'status' => true,
			'message' => 'Success Randomize'
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

public function editLiveCookingOrder(Request $request)
{
	try {
		if ($request->get('status') == 'edit') {
			$orders = CanteenLiveCooking::where('due_date',$request->get('due_date'))->get();
			$quota = CanteenLiveCookingMenu::where('due_date',$request->get('due_date'))->first();
			$live_cooking = CanteenLiveCooking::where('id',$request->get('id'))->first();
			$check = CanteenLiveCooking::where('due_date',$request->get('due_date'))->where('order_for',$request->get('order_for'))->first();
			if ($live_cooking->due_date == $request->get('due_date')) {
				$status = true;
				$message = 'Update Data Berhasil';
			}else{
				if ($quota->serving_ordered < $quota->serving_quota) {
					$live_cooking->due_date = $request->get('due_date');
					$live_cooking->save();
					$status = true;
					$message = 'Update Data Berhasil';
				}else{
					$status = false;
					$message = 'Kuota pada tanggal '.date('d F Y',strtotime($request->get('due_date'))).' telah penuh.';
				}
			}
		}else{
			$live_cooking = CanteenLiveCooking::where('id',$request->get('id'))->first();
			$live_cooking->forceDelete();
			$status = true;
			$message = 'Delete Data Berhasil';
		}
		$response = array(
			'status' => $status,
			'message' => $message,
		);
		return Response::json($response);
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
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


	$qry = "SELECT  * FROM canteen_purchase_requisitions A WHERE A.deleted_at IS NULL " . $tanggal . "" . $restrict_dept. " order by A.id DESC";

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

public function get_nomor_pr(Request $request)
{
	$datenow = date('Y-m-d');
	$tahun = date('y');
	$bulan = date('m');

	$query = "SELECT no_pr FROM `canteen_purchase_requisitions` where DATE_FORMAT(submission_date, '%y') = '$tahun' and month(submission_date) = '$bulan' order by id DESC LIMIT 1";
	$nomorurut = DB::select($query);

	if ($nomorurut != null)
	{
		$nomor = substr($nomorurut[0]->no_pr, -3);
		$nomor = $nomor + 1;
		$nomor = sprintf('%03d', $nomor);
	}
	else
	{
		$nomor = "001";
	}

	$result['tahun'] = $tahun;
	$result['bulan'] = $bulan;
	$result['dept'] = 'CA';
	$result['no_urut'] = $nomor;

	return json_encode($result);
}

public function create_purchase_requisition(Request $request)
{
	$id = Auth::id();

	$lop = $request->get('lop');

	try
	{
		$staff = null;
		$manager = null;
		$manager_name = null;
		$posisi = null;
		$gm = null;

	            //Jika GA pak arief
		if($request->get('department') == "General Affairs Department")
		{
			$manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position = 'manager'");
		}

		else
		{
	                // Get Manager
			$manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '" . $request->get('department') . "' and position = 'manager'");
		}

		if ($manag != null)
		{
			$posisi = "user";

			foreach ($manag as $mg)
			{
				$manager = $mg->employee_id;
				$manager_name = $mg->name;
			}
		}

		else
		{
			$posisi = "user";
		}

	            //Cek File
		$files = array();
		$file = new CanteenPurchaseRequisition();
		if ($request->file('reportAttachment') != NULL)
		{
			if ($files = $request->file('reportAttachment'))
			{
				foreach ($files as $file)
				{
					$nama = $file->getClientOriginalName();
					$file->move('files/pr', $nama);
					$data[] = $nama;
				}
			}
			$file->filename = json_encode($data);
		}
		else
		{
			$file->filename = NULL;
		}

		if($request->get('department') == "Human Resources Department" || $request->get('department') == "General Affairs Department"){
	                //GM Pak Arief
			$getgm = EmployeeSync::select('employee_id', 'name', 'position')
			->where('employee_id','=','PI9709001')
			->first();

			$gm = $getgm->employee_id;
		}
	            //if accounting maka GM Pak IDA
		else if($request->get('department') == "Accounting Department"){
			$gm = $this->gm_acc;
		}
	            //Selain Itu GM Pak Budhi
		else{
			$gm = $this->dgm;
		}


		$data = new CanteenPurchaseRequisition([
			'no_pr' => $request->get('no_pr') , 
			'emp_id' => $request->get('emp_id') , 
			'emp_name' => $request->get('emp_name') , 
			'department' => $request->get('department') , 
			'section' => $request->get('section') , 
			'submission_date' => $request->get('submission_date'), 
			'note' => $request->get('note') , 
			'file' => $file->filename, 
			'file_pdf' => 'PR'.$request->get('no_pr').'.pdf', 
			'posisi' => $posisi, 
			'status' => 'approval', 
			'no_budget' => $request->get('budget_no'), 
			'staff' => $staff,
			'manager' => $manager,
			'manager_name' => $manager_name,
			'gm' => $gm, 
			'created_by' => $id
		]);

		$data->save();

		for ($i = 1;$i <= $lop;$i++)
		{
			$item_code = "item_code" . $i;
			$item_desc = "item_desc" . $i;
			$item_request_date = "req_date" . $i;
			$item_currency = "item_currency" . $i;
			$item_currency_text = "item_currency_text" . $i;
			$item_price = "item_price" . $i;
			$item_qty = "qty" . $i;
			$item_uom = "uom" . $i;
			$item_amount = "amount" . $i;
			$status = "";
			if ($request->get($item_code) == "kosong")
			{
				$request->get($item_code) == "";
			}

			if ($request->get($item_code) != null)
			{
				$status = "fixed";
			}
			else
			{
				$status = "sementara";
			}

			if ($request->get($item_currency) != "")
			{
				$current = $request->get($item_currency);
			}
			else if ($request->get($item_currency_text) != "")
			{
				$current = $request->get($item_currency_text);
			}

			$data2 = new CanteenPurchaseRequisitionItem([
				'no_pr' => $request->get('no_pr') , 
				'item_code' => $request->get($item_code) ,
				'item_desc' => $request->get($item_desc) , 
				'item_request_date' => $request->get($item_request_date), 
				'item_currency' => $current, 
				'item_price' => $request->get($item_price), 
				'item_qty' => $request->get($item_qty),
				'item_uom' => $request->get($item_uom),
				'item_amount' => $request->get($item_amount),
				'created_by' => $id
			]);

			$data2->save();

			$dollar = "konversi_dollar" . $i;

			$getbulan = AccBudget::select('budget_no', 'periode')
			->where('budget_no', $request->get('budget_no'))
			->first();

			if ($getbulan->periode == "FY198") {
				$month = strtolower(date('M'));
			}
			else{
				$month = "apr";
			}

			$data3 = new CanteenBudgetHistory([
				'budget' => $request->get('budget_no'),
				'budget_month' => $month,
				'budget_date' => date('Y-m-d'),
				'category_number' => $request->get('no_pr'),
				'no_item' => $request->get($item_desc),
				'beg_bal' => $request->get('budget'),
				'amount' => $request->get($dollar),
				'status' => 'PR',
				'created_by' => $id
			]);

			$data3->save();
		}

		$totalPembelian = $request->get('TotalPembelian');
		if ($totalPembelian != null) {
			$getbulan = AccBudget::select('budget_no', 'periode')
			->where('budget_no', $request->get('budget_no'))
			->first();

			if ($getbulan->periode == "FY198") {
				$bulan = strtolower(date('M'));
				$fiscal = "FY198";
			}
			else{
				$bulan = "apr";
				$fiscal = "FY199";
			}

			$sisa_bulan = $bulan.'_sisa_budget';                    
	                //get Data Budget Based On Periode Dan Nomor
			$budget = AccBudget::where('budget_no','=',$request->get('budget_no'))->first();
	                //perhitungan 
			$total = $budget->$sisa_bulan - $totalPembelian;

			if ($total < 0 ) {
				return false;
			}

			$dataupdate = AccBudget::where('budget_no',$request->get('budget_no'))->update([
				$sisa_bulan => $total
			]);
		}

		$detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*','canteen_purchase_requisition_items.*','canteen_budget_histories.beg_bal','canteen_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
		->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
		->join('canteen_budget_histories', function($join) {
			$join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
			$join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
		})
		->where('canteen_purchase_requisitions.id', '=', $data->id)
		->distinct()
		->get();

		$exchange_rate = AccExchangeRate::select('*')
		->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
		->where('currency','!=','USD')
		->orderBy('currency','ASC')
		->get();

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'potrait');

		$pdf->loadView('general_affairs.report.report_pr', array(
			'pr' => $detail_pr,
			'rate' => $exchange_rate
		));

		$pdf->save(public_path() . "/kantin/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

		return redirect('/canteen/purchase_requisition')->with('status', 'PR Berhasil Dibuat')
		->with('page', 'Purchase Requisition');
	}
	catch(QueryException $e)
	{
		return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
		->with('page', 'Purchase Requisition');
	}
}

public function edit_purchase_requisition(Request $request)
{
	$purchase_requistion = CanteenPurchaseRequisition::find($request->get('id'));
	$purchase_requistion_item = CanteenPurchaseRequisition::select('canteen_purchase_requisition_items.*','canteen_budget_histories.budget', 'canteen_budget_histories.budget_month', 'canteen_budget_histories.budget_date', 'canteen_budget_histories.category_number','canteen_budget_histories.no_item','canteen_budget_histories.amount','canteen_budget_histories.beg_bal')
	->join('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
	->join('canteen_budget_histories', function($join) {
		$join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
		$join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
	})
	->where('canteen_purchase_requisitions.id', '=', $request->get('id'))
	->whereNull('canteen_purchase_requisition_items.sudah_po')
	->get();

	$response = array(
		'status' => true,
		'purchase_requisition' => $purchase_requistion,
		'purchase_requisition_item' => $purchase_requistion_item
	);
	return Response::json($response);
}

public function update_purchase_requisition(Request $request)
{
	$id = Auth::id();
	$lop2 = $request->get('lop2');
	$lop = explode(',', $request->get('looping'));
	try
	{
		foreach ($lop as $lp)
		{
			$item_code = "item_code_edit" . $lp;
			$item_desc = "item_desc_edit" . $lp;
			$item_uom = "uom_edit" . $lp;
			$item_req = "req_date_edit" . $lp;
			$item_qty = "qty_edit" . $lp;
			$item_price = "item_price_edit" . $lp;
			$item_amount = "amount_edit" . $lp;

                // $amount = preg_replace('/[^0-9]/', '', $request->get($item_amount));

			$data2 = CanteenPurchaseRequisitionItem::where('id', $lp)->update([
				'item_code' => $request->get($item_code), 
				'item_desc' => $request->get($item_desc), 
				'item_uom' => $request->get($item_uom), 
				'item_request_date' => $request->get($item_req), 
				'item_qty' => $request->get($item_qty),
				'item_price' => $request->get($item_price),
				'item_amount' => $request->get($item_amount),
				'created_by' => $id
			]);

		}

		for ($i = 2;$i <= $lop2;$i++)
		{

			$item_code = "item_code" . $i;
			$item_desc = "item_desc" . $i;
			$item_req = "req_date" . $i;
			$item_currency = "item_currency" . $i;
			$item_currency_text = "item_currency_text" . $i;
			$item_price = "item_price" . $i;
			$item_qty = "qty" . $i;
			$item_uom = "uom" . $i;
			$item_amount = "amount" . $i;
			$dollar = "konversi_dollar" . $i;
			$status = "";

                //Jika ada value kosong
			if ($request->get($item_code) == "kosong")
			{
				$request->get($item_code) == "";
			}

                //Jika item kosong
			if ($request->get($item_code) != null)
			{
				$status = "fixed";
			}
			else
			{
				$status = "sementara";
			}

			if ($request->get($item_currency) != "")
			{
				$current = $request->get($item_currency);
			}
			else if ($request->get($item_currency_text) != "")
			{
				$current = $request->get($item_currency_text);
			}


			$data2 = new CanteenPurchaseRequisitionItem([
				'no_pr' => $request->get('no_pr_edit') , 
				'item_code' => $request->get($item_code) , 
				'item_desc' => $request->get($item_desc) , 
				'item_request_date' => $request->get($item_req) , 
				'item_currency' => $current,
				'item_price' => $request->get($item_price),
				'item_qty' => $request->get($item_qty) , 
				'item_uom' => $request->get($item_uom) , 
				'item_amount' => $request->get($item_amount), 
				'status' => $status, 
				'created_by' => $id
			]);

			$data2->save();

			$getbulan = AccBudget::select('budget_no', 'periode')
			->where('budget_no', $request->get('no_budget_edit'))
			->first();

			if ($getbulan->periode == "FY198") {
				$bulan = strtolower(date('M'));
			}
			else{
				$bulan = "apr";
			}

			$sisa_bulan = $bulan.'_sisa_budget';

                 //get Data Budget Based On Periode Dan Nomor
			$budgetdata = AccBudget::where('budget_no','=',$request->get('no_budget_edit'))->first();

                //Get Amount Di PO
			$total_dollar = $request->get($dollar);

			$totalminusPO = $budgetdata->$sisa_bulan - $total_dollar;

                // Setelah itu update data budgetnya dengan yang actual
			$dataupdate = AccBudget::where('budget_no',$request->get('no_budget_edit'))
			->update([
				$sisa_bulan => $totalminusPO
			]);

                // $month = strtolower(date("M",strtotime($request->get('tgl_pengajuan_edit'))));
			$begbal = $request->get('SisaBudgetEdit') + $request->get('TotalPembelianEdit');

			$getbulan = AccBudget::select('budget_no', 'periode')
			->where('budget_no', $request->get('no_budget_edit'))
			->first();

			if ($getbulan->periode == "FY198") {
				$month = strtolower(date('M'));
			}
			else{
				$month = "apr";
			}

			$data3 = new CanteenBudgetHistory([
				'budget' => $request->get('no_budget_edit'),
				'budget_month' => $month,
				'budget_date' => date('Y-m-d'),
				'category_number' => $request->get('no_pr_edit'),
				'beg_bal' => $begbal,
				'no_item' => $request->get($item_desc),
				'amount' => $request->get($dollar),
				'status' => 'PR',
				'created_by' => $id
			]);

			$data3->save();
		}

		$detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*','canteen_purchase_requisition_items.*','canteen_budget_histories.beg_bal','canteen_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
		->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
		->join('canteen_budget_histories', function($join) {
			$join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
			$join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
		})
		->where('canteen_purchase_requisitions.id', '=', $request->get('id_edit_pr'))
		->distinct()
		->get();

		$exchange_rate = AccExchangeRate::select('*')
		->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
		->where('currency','!=','USD')
		->orderBy('currency','ASC')
		->get();

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('A4', 'potrait');

		$pdf->loadView('general_affairs.report.report_pr', array(
			'pr' => $detail_pr,
			'rate' => $exchange_rate
		));

		$pdf->save(public_path() . "/kantin/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

		return redirect('/canteen/purchase_requisition')
		->with('status', 'Purchase Requisition Berhasil Dirubah')
		->with('page', 'Purchase Requisition');
	}
	catch(QueryException $e)
	{
		return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
		->with('page', 'Purchase Requisition');
	}
}

public function delete_purchase_requisition(Request $request)
{
	try
	{
		$pr = CanteenPurchaseRequisition::find($request->get('id'));

		$budget_log = CanteenBudgetHistory::where('category_number', '=', $pr->no_pr)
		->get();

		foreach ($budget_log as $log) {
			$sisa_bulan = $log->budget_month.'_sisa_budget';
			$budget = AccBudget::where('budget_no', $log->budget)->first();

                $total = $budget->$sisa_bulan + $log->amount; //add total
                $dataupdate = AccBudget::where('budget_no', $log->budget)->update([
                	$sisa_bulan => $total
                ]);
            }

            $delete_budget_log = CanteenBudgetHistory::where('category_number', '=', $pr->no_pr)->delete();
            $delete_pr_item = CanteenPurchaseRequisitionItem::where('no_pr', '=', $pr->no_pr)->delete();
            $delete_pr = CanteenPurchaseRequisition::where('no_pr', '=', $pr->no_pr)->delete();

            $response = array(
            	'status' => true,
            );

            return Response::json($response);
        }
        catch(QueryException $e)
        {
        	return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
        	->with('page', 'Purchase Requisition');
        }
    }

    public function delete_item_pr(Request $request)
    {
    	try
    	{
    		$master_item = CanteenPurchaseRequisitionItem::find($request->get('id'));

    		$budget_log = CanteenBudgetHistory::where('no_item', '=', $master_item->item_desc)
    		->where('category_number', '=', $master_item->no_pr)
    		->first();

    		$sisa_bulan = $budget_log->budget_month.'_sisa_budget';

    		$budget = AccBudget::where('budget_no', $budget_log->budget)->first();

            $total = $budget->$sisa_bulan + $budget_log->amount; //add total

            $dataupdate = AccBudget::where('budget_no', $budget_log->budget)->update([
            	$sisa_bulan => $total
            ]);

            $delete_budget_log = CanteenBudgetHistory::where('no_item', '=', $master_item->item_desc)
            ->where('category_number', '=', $master_item->no_pr)
            ->delete();

            $delete_item = CanteenPurchaseRequisitionItem::where('id', '=', $request->get('id'))->delete();

            $response = array(
            	'status' => true,
            );

            return Response::json($response);

        }
        catch(QueryException $e)
        {
        	return redirect('/canteen/purchase_requisition')->with('error', $e->getMessage())
        	->with('page', 'Purchase Requisition');
        }

    }

    //==================================//
    //          Report PR               //
    //==================================//
    public function report_purchase_requisition($id){

    	$detail_pr = CanteenPurchaseRequisition::select('canteen_purchase_requisitions.*','canteen_purchase_requisition_items.*','canteen_budget_histories.beg_bal','canteen_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = canteen_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
    	->leftJoin('canteen_purchase_requisition_items', 'canteen_purchase_requisitions.no_pr', '=', 'canteen_purchase_requisition_items.no_pr')
    	->join('canteen_budget_histories', function($join) {
    		$join->on('canteen_budget_histories.category_number', '=', 'canteen_purchase_requisition_items.no_pr');
    		$join->on('canteen_budget_histories.no_item','=', 'canteen_purchase_requisition_items.item_desc');
    	})
    	->where('canteen_purchase_requisitions.id', '=', $id)
    	->distinct()
    	->get();

    	$exchange_rate = AccExchangeRate::select('*')
    	->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
    	->where('currency','!=','USD')
    	->orderBy('currency','ASC')
    	->get();

    	$pdf = \App::make('dompdf.wrapper');
    	$pdf->getDomPDF()->set_option("enable_php", true);
    	$pdf->setPaper('A4', 'potrait');

    	$pdf->loadView('general_affairs.report.report_pr', array(
    		'pr' => $detail_pr,
    		'rate' => $exchange_rate
    	));

    	$path = "kantin/pr_list/" . $detail_pr[0]->no_pr . ".pdf";
    	return $pdf->stream("PR".$detail_pr[0]->no_pr. ".pdf");

        // return view('general_affairs.report.report_pr', array(
        //  'pr' => $detail_pr,
        // ))->with('page', 'PR')->with('head', 'PR List');
    }

    public function pr_send_email(Request $request){
    	$pr = CanteenPurchaseRequisition::find($request->get('id'));

    	try{
    		if ($pr->posisi == "user")
    		{
                //ke manager
    			$mails = "select distinct email from canteen_purchase_requisitions join users on canteen_purchase_requisitions.manager = users.username where canteen_purchase_requisitions.id = ".$request->get('id');
    			$mailtoo = DB::select($mails);
    			$pr->posisi = "manager";
    		}

    		$pr->save();

    		$isimail = "select canteen_purchase_requisitions.*,canteen_purchase_requisition_items.item_stock, canteen_purchase_requisition_items.item_desc, canteen_purchase_requisition_items.kebutuhan, canteen_purchase_requisition_items.peruntukan, canteen_purchase_requisition_items.item_qty, canteen_purchase_requisition_items.item_uom FROM canteen_purchase_requisitions join canteen_purchase_requisition_items on canteen_purchase_requisitions.no_pr = canteen_purchase_requisition_items.no_pr where canteen_purchase_requisitions.id = ".$request->get('id');
    		$purchaserequisition = db::select($isimail);

    		Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com','aditya.agassi@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

    		$response = array(
    			'status' => true,
    			'datas' => "Berhasil"
    		);

    		return Response::json($response);
    	} 
    	catch (Exception $e) {
    		$response = array(
    			'status' => false,
    			'datas' => "Gagal"
    		);
    		return Response::json($response);
    	}
    }

}
