<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use App\Visitor;
use App\VisitorDetail;
use App\Employee;
use App\TelephoneList;
use App\VisitorId;
use App\PlcCounter;
use DataTables;
use File;
use Storage;
use App\BodyTemperature;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class VisitorController extends Controller
{
	public function __construct()
    {
      $this->middleware('auth');
      if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
    }

	public function index()
	{
		return view('visitors.index')->with('page', 'Visitor Index');
	}

//-------------registration   

	public function registration()
	{
		$employees = "SELECT
			*,employee_syncs.employee_id as empid
		FROM
			employee_syncs
			LEFT JOIN office_members ON office_members.employee_id = employee_syncs.employee_id 
		WHERE
			position NOT LIKE '%Operator%'";
		$employee = DB::select($employees);

		$companies = "SELECT
			DISTINCT(company)
		FROM
			visitors";
		$company = DB::select($companies);

		return view('visitors.registration', array(
			'employee' => $employee,
			'company' => $company,
		))->with('page', 'Visitor Registration');
	}


	public function simpanheader(Request $request)
	{
		$lop = $request->get('lop2');
		$lop_suhu = $request->get('lop_suhu2');
		// var_dump($id);
		try {
			//----insert visitor
			if ($request->get('company') == null) {
				$company = $request->get('company2');
				if ($request->get('pol') == null) {
					$visitor = new visitor([
						'company' => $request->get('company2'),
						'purpose' => $request->get('purpose'),
						'status' => $request->get('status'),
						'jumlah' => $request->get('jumlah'),
						'location' => "Security",
						'destination' => $request->get('destination'),
						'employee'=> $request->get('employee'),
						'transport'=>  $request->get('kendaraan'),
					]);
				}else{
					$visitor = new visitor([
						'company' => $request->get('company2'),
						'purpose' => $request->get('purpose'),
						'status' => $request->get('status'),
						'jumlah' => $request->get('jumlah'),
						'location' => "Security",
						'destination' => $request->get('destination'),
						'employee'=> $request->get('employee'),
						'transport'=>  $request->get('kendaraan'),
						'pol'=>  $request->get('pol'),
					]);
				}
			}else{
				$company = $request->get('company');
				if ($request->get('pol') == null) {
					$visitor = new visitor([
						'company' => $request->get('company'),
						'purpose' => $request->get('purpose'),
						'status' => $request->get('status'),
						'jumlah' => $request->get('jumlah'),
						'location' => "Security",
						'destination' => $request->get('destination'),
						'employee'=> $request->get('employee'),
						'transport'=>  $request->get('kendaraan'),
					]);
				}else{
					$visitor = new visitor([
						'company' => $request->get('company'),
						'purpose' => $request->get('purpose'),
						'status' => $request->get('status'),
						'jumlah' => $request->get('jumlah'),
						'location' => "Security",
						'destination' => $request->get('destination'),
						'employee'=> $request->get('employee'),
						'transport'=>  $request->get('kendaraan'),
						'pol'=>  $request->get('pol'),
					]);
				}
			}

			$visitor -> save();	
			$id = Visitor::orderby('created_at','desc')->first();

			for ($i=0; $i < $lop_suhu ; $i++) {

				$nama = "nama".$i;
				$suhu = "suhu".$i;
				$kota = "kota".$i;
				$namavis = $request->get($nama);
				$kotavis = $request->get($kota);
				$suhuvis = $request->get($suhu);
				if ($request->get('company') == null) {
					$suhunew = new BodyTemperature([
						'company' => $request->get('company2'),
						'name' => $request->get($nama),
						'kota' => $request->get($kota),
						'suhu' => $request->get($suhu),
						'created_by' => Auth::id()
					]);
				}else{
					$suhunew = new BodyTemperature([
						'company' => $request->get('company'),
						'name' => $request->get($nama),
						'kota' => $request->get($kota),
						'suhu' => $request->get($suhu),
						'created_by' => Auth::id()
					]);
				}
				$suhunew -> save();
			}

			if ($request->get('destination') == 'Office') {

				$emp_sync = DB::SELECT("SELECT * FROM employee_syncs WHERE employee_id = '".$request->get('employee')."'");

				if (count($emp_sync) > 0) {
					foreach ($emp_sync as $key) {
						$department = $key->department;
						$name = $key->name;
					}

					if ($department == null && $name == 'Budhi Apriyanto') {
						$department = 'Management Information System';
						$mail_to = DB::SELECT("SELECT
							email 
						FROM
							send_emails 
						WHERE
							remark = '".$department."'");

						$namamanager[] = [ 'employees' => $name,
		                    'department' => $department,
		                    'company' => $company,
		                    'nama' => $namavis,
		                    'kota' => $kotavis,
		                    'suhu' => $suhuvis,
		                    'id' => $id->id
		                ];
					}elseif ($department == null && $name == 'Arief Soekamto') {
						$department = 'Human Resources';
						$mail_to = DB::SELECT("SELECT
							email 
						FROM
							send_emails 
						WHERE
							remark = '".$department."'");

						$namamanager[] = [ 'employees' => $name,
		                    'department' => $department,
		                    'company' => $company,
		                    'nama' => $namavis,
		                    'kota' => $kotavis,
		                    'suhu' => $suhuvis,
		                    'id' => $id->id
		                ];
					}else{
						$mail_to = DB::SELECT("SELECT
							email 
						FROM
							send_emails 
						WHERE
							remark = '".$department."'");

						$namamanager[] = [ 'employees' => $name,
		                    'department' => $department,
		                    'company' => $company,
		                    'nama' => $namavis,
		                    'kota' => $kotavis,
		                    'suhu' => $suhuvis,
		                    'id' => $id->id
		                ];
					}
				}

				$contactList = [];
		        $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
		        $contactList[1] = 'aditya.agassi@music.yamaha.com';
				Mail::to($mail_to)->bcc($contactList,'Contact List')->send(new SendEmail($namamanager, 'incoming_visitor'));
			}

			//----insert detail
			for ($i=0; $i < $lop ; $i++) {

				$visitor_id = "visitor_id".$i;
				$visitor_name = "visitor_name".$i;
				$telp = "telp".$i;
				$VisitorDetail = new VisitorDetail([
					'id_number' => $request->get($visitor_id), 
					'id_visitor' => $id->id,
					'full_name' => $request->get($visitor_name),
					'telp' => $request->get($telp),
					'status' => $request->get('status')
				]);
				$VisitorDetail -> save();


				$tabelvisitorid =  VisitorId::updateOrCreate(
					[

						'ktp' => $request->get($visitor_id),

					],
					[
						'ktp' => $request->get($visitor_id),
						'full_name' => $request->get($visitor_name),
						'telp' => $request->get($telp),
					]
				);
			}

			

			return redirect('visitor_registration')->with('status', 'Input Visitor Registration success ')->with('page', 'Visitor Registration');
		}
		catch (QueryException $e){
			return redirect('visitor_registration')->with('error', $e->getMessage())->with('page', 'Visitor Registration');
		}
	}


	public function getdata(Request $request)
	{

		$id_list = VisitorId::where('ktp', '=', $request->get('ktp'))->get();

		$response = array(
			'status' => true,
			'id_list' => $id_list,      
		);
		return Response::json($response);
	}

	//-------------------- end registration

	//----------------- list 

	public function receive()
	{
		return view('visitors.receive')->with('page', 'Visitor Index');
	}


	public function filllist($nik)
	{
		$id = Auth::id();
		$tgl = date('Y-m-d');
		$kurang = date('Y-m-d',strtotime('-14 days'));
		
		if ($nik !="") {			
			// $where = "where employee = '".$nik."'";
			$where=" WHERE
						a.created_at2 >= '".$kurang."'
						AND a.created_at2 <= '".$tgl."'
						AND employee IN (
						SELECT
							employee_id 
						FROM
							employee_syncs 
						WHERE
							employee_id = '".$nik."')";
		}

		if($nik =="asd"){
			$where="WHERE a.created_at2 >='".$kurang."' and a.created_at2<='".$tgl."' ";
		}

		$op="SELECT
				*,
				count(
				DISTINCT ( total1 )) AS total 
			FROM
				(
				SELECT
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitors.employee,
					visitors.id,
					company,
					visitor_details.full_name,
					visitor_details.id_number AS total1,
					purpose,
					visitors.status,
					employee_syncs.name,
					employee_syncs.department,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
				) a 
			".$where."
			GROUP BY
				a.id 
			ORDER BY
				created_at DESC";
	$ops = DB::select($op);
	return DataTables::of($ops)

	// confirm pertag
	// ->addColumn('edit', function($ops){
	// 	return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
	// })

	->addColumn('edit', function($ops){
		return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
	})
	->rawColumns(['edit' => 'edit'])

	->make(true);
}



public function editlist(Request $request)
{
	$id = $request->get('id');
	$id_list = VisitorDetail::where('id_visitor', '=', $request->get('id'))->get();
	$header_lists = "SELECT DISTINCT
						( visitors.id ),
						company,
						name,
						employee_syncs.department,
						employee_syncs.department AS shortname 
					FROM
						visitors
						LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
					WHERE
					visitors.id='".$id."'";
	$header_list = DB::select($header_lists);
	$response = array(
		'status' => true,
		'id_list' => $id_list,
		'header_list'  =>$header_list,        
	);
	return Response::json($response);
}

public function inputtag(Request $request){

	try {
		$id = $request->get('id');
		$intime = date('H:i:s');
		$visitordetail = VisitorDetail::where('id','=', str_replace("V","",$id))
		->withTrashed()       
		->first();

		$visitordetail->tag = $request->get('idtag');
		$visitordetail->in_time =$intime;
		$visitordetail->save();


		$response = array(
			'status' => true,
			'message' => 'Input Success'
		);
		return Response::json($response);
	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}

}

//-------------------- end list

//-------------------- at lobby

	public function scanVisitorLobby(Request $request){

		$tag_visitor = $request->get('tag_visitor');

		if(strlen($tag_visitor) > 9){
			$tag_visitor = substr($tag_visitor,0,9);
		}

		$visitorDetail = VisitorDetail::where('tag', 'like', '%'.$tag_visitor.'%')->orderby('id','desc')->first();

		if(count($visitorDetail) > 0 ){
			$id_visitor = $visitorDetail->id_visitor;

			$visitor = Visitor::find($id_visitor);
			$visitor->location = 'Lobby';
			$visitor->save();

			$visitor2 = Visitor::join('employee_syncs','employee_syncs.employee_id','=','visitors.employee')->where('visitors.id',$id_visitor)->first();

			$plc = PlcCounter::where('origin_group_code','visitor_lobby')->first();
	    	$counter = $plc->plc_counter;
	    	$id_plc = $plc->id;

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = 0;
			$plccounter->save();

			$plc2 = PlcCounter::where('origin_group_code','visitor_lobby2')->first();
	    	$counter2 = $plc2->plc_counter;
	    	$id_plc2 = $plc2->id;

			$plccounter2 = PlcCounter::find($id_plc2);
			$plccounter2->plc_counter = 0;
			$plccounter2->save();

			$plc_sec = PlcCounter::where('origin_group_code','visitor')->first();
	    	$counter_sec = $plc_sec->plc_counter;
	    	$id_plc_sec = $plc_sec->id;

			$plccounter_sec = PlcCounter::find($id_plc_sec);
			$plccounter_sec->plc_counter = $counter_sec - 1;
			$plccounter_sec->save();

			$plc_sec2 = PlcCounter::where('origin_group_code','visitor2')->first();
	    	$counter_sec2 = $plc_sec2->plc_counter;
	    	$id_plc_sec2 = $plc_sec2->id;

			$plccounter_sec2 = PlcCounter::find($id_plc_sec2);
			$plccounter_sec2->plc_counter = $counter_sec2 - 1;
			$plccounter_sec2->save();

			$response = array(
				'status' => true,
				'message' => 'Scan Success',
				'visitor' => $visitor2
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Tag Invalid'
			);
			return Response::json($response);
		}
	}

//-------------------- end at lobby


//--------------------- confirmation

public function confirmation()
{
	$division = DB::SELECT("SELECT DISTINCT
								( division ) 
							FROM
								telephone_lists");

	$japan = DB::SELECT("SELECT *
							FROM
								telephone_lists
							WHERE
								division = 'JAPAN STAFF'
							ORDER BY
								id ASC");

	$hrga = DB::SELECT("SELECT
								* 
							FROM
								telephone_lists 
							WHERE
								division = 'HR&GA' 
							ORDER BY
								id ASC");

	$production = DB::SELECT("SELECT *
							FROM
								telephone_lists
							WHERE
								division = 'PRODUCTION'
							ORDER BY
								id ASC");

	$finance = DB::SELECT("SELECT *
							FROM
								telephone_lists
							WHERE
								division = 'FINANCE'
							ORDER BY
								id ASC");

	$ps = DB::SELECT("SELECT *
							FROM
								telephone_lists
							WHERE
								division = 'PRODUCTION SUPPORT'
							ORDER BY
								id ASC");

	$room = DB::SELECT("SELECT *
							FROM
								telephone_lists
							WHERE
								division = 'ROOM'
							ORDER BY
								id ASC");

	return view('visitors.confirmation')
	->with('page', 'Visitor Confirmation')
	->with('japan',$japan)
	->with('hrga',$hrga)
	->with('production',$production)
	->with('finance',$finance)
	->with('ps',$ps)
	->with('room',$room)
	->with('division',$division);
}

public function confirmation2()
{
	return view('visitors.confirmationSatpam')->with('page', 'Visitor Confirmation');
}

public function updateremark(Request $request){

	try {
		$id = $request->get('id');
		$tag = $request->get('idtag');
		$intime = date('H:i:s');
		$visitordetail = VisitorDetail::where('id_visitor','=', $id)
		->where('tag','=',$tag)
		->withTrashed()       
		->first();

		$visitordetail->remark = 'Confirmed';		
		$visitordetail->save();


		$visitor = Visitor::where('id','=', $id)		     
		->first();
		$visitor->remark = 'Confirmed';
		$visitor->save();

		$response = array(
			'status' => true,
			'message' => 'Confirm Visitors Success'
		);
		return Response::json($response);
	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}

}

public function updateremarkall(Request $request){

	try {
		$id = $request->get('id');
		if ($request->get('remark') != null) {
		    $remark = $request->get('remark');
		    $intime = date('H:i:s');
			$visitordetail = VisitorDetail::where('id_visitor','=', $id)		
			->withTrashed()
			->update(['remark' => $remark]);

			$visitor = Visitor::where('id','=', $id)		     
			->first();
			$visitor->remark = $remark;
			$visitor->save();
		// $tag = $request->get('idtag');
			
		}else{
			$intime = date('H:i:s');
			$visitordetail = VisitorDetail::where('id_visitor','=', $id)		
			->withTrashed()
			->update(['remark' => 'Confirmed']);

			$visitor = Visitor::where('id','=', $id)		     
			->first();
			$visitor->remark = 'Confirmed';
			$visitor->save();
		}
		

		

		$response = array(
			'status' => true,
			'message' => 'Confirm Visitors Success'
		);
		return Response::json($response);
	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}

}

public function confirm_manager($id){

	try {
	    $intime = date('H:i:s');
		$visitordetail = VisitorDetail::where('id_visitor','=', $id)		
		->withTrashed()
		->update(['remark' => 'Sudah Ditemui']);

		$visitor = Visitor::where('id','=', $id)		     
		->first();
		$visitor->remark = 'Sudah Ditemui';
		$visitor->save();

		$datavisitor = Visitor::join('employee_syncs','employee_syncs.employee_id','=','visitors.employee')->where('visitors.id',$id)->first();

		$name = $datavisitor->name;
		$department = $datavisitor->department;
		$company = $datavisitor->company;

		$message = $name.'('.$department.') telah terkonfirmasi menemui '.$company;
		return view('visitors.visitor_confirm_manager', array(
			'head' => $id,
			'message' => $message,
		))->with('page', 'Visitor Confirmation');
	}
	catch(\Exception $e){
		$message = $name.'('.$department.') telah terkonfirmasi menemui '.$company;
		return view('visitors.visitor_confirm_manager', array(
			'head' => $id,
			'message' => $message,
		))->with('page', 'Visitor Confirmation');
	}

}

public function telpon()
{		

	$telpon="select person, dept, nomor from telephone_lists";
	$telpons = DB::select($telpon);
	return DataTables::of($telpons)
	->make(true);
}

public function getNotifVisitor()
 {
 	if (Auth::user() !== null) {
 		$manager = Auth::user()->username;
 		$role = Auth::user()->role_code;
		$emp_sync = DB::SELECT("SELECT * FROM `employee_syncs` where employee_id = '".$manager."'");

		if (count($emp_sync) > 0) {
			foreach ($emp_sync as $key) {
				$position = $key->position;
				$name = $key->name;
				$department = $key->department;
			}

			if ($role == 'MIS') {
				if ($department == null && $name == 'Budhi Apriyanto') {
					$lists = DB::SELECT("SELECT
								count( visitors.id ) AS notif 
							FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
							WHERE
								(
								visitors.remark IS NULL 
								AND employee_syncs.department = 'Management Information System')
								OR (
								visitors.remark IS NULL 
								AND employee_syncs.name = 'Budhi Apriyanto')");
				}elseif($name == 'Romy Agung Kurniawan'){
					$lists = DB::SELECT("SELECT
							count( visitors.id ) AS notif 
						FROM
							visitors
							LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
							LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
						WHERE
							visitors.remark IS NULL 
							AND employee_syncs.department = '".$department."'");
				}else{
					$lists = DB::SELECT("SELECT
							count( visitors.id ) AS notif 
						FROM
							visitors
							LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
							LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
						WHERE
							visitors.remark IS NULL");
				}
			}else{
				if ($department == null && $name == 'Arief Soekamto') {
					$lists = DB::SELECT("SELECT
								count( visitors.id ) AS notif 
							FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
							WHERE
								( visitors.remark IS NULL AND employee_syncs.department = 'Human Resources' ) 
								OR (
								visitors.remark IS NULL 
								AND employee_syncs.department = 'General Affairs')
								OR (
								visitors.remark IS NULL 
								AND employee_syncs.name = 'Arief Soekamto')");
				}elseif ($name == 'Susilo Basri Prasetyo') {
					$lists = DB::SELECT("SELECT
								count( visitors.id ) AS notif 
							FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
							WHERE
								( visitors.remark IS NULL AND employee_syncs.department = 'Maintenance' ) 
								OR (
								visitors.remark IS NULL 
								AND employee_syncs.department = 'Production Engineering')");
				}else{
					$lists = DB::SELECT("SELECT
							count( visitors.id ) AS notif 
						FROM
							visitors
							LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
							LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
						WHERE
							visitors.remark IS NULL 
							AND employee_syncs.department = '".$department."'");
				}
			}

			foreach ($lists as $val) {
				$notif = $val->notif;
			}
		    if (count($notif) > 0) {
		    	return $notif;
		    }
		}
 	}
 }

public function confirmation_manager()
{
	$manager = Auth::user()->username;
	$role = Auth::user()->role_code;
	$emp_sync = DB::SELECT("SELECT
					*,
				IF
					(
						position LIKE '%Manager%',
						TRUE,
					IF
						(
							position LIKE '%Director%',
							TRUE,
						IF
						( position LIKE '%Foreman%', TRUE, IF
						( position LIKE '%Leader%', TRUE, FALSE ) ))) position_bolean,
						IF(department = 'Logistic',true,false) as is_logistic,
						IF(position = 'Foreman',true,false) as is_foreman
				FROM
					`employee_syncs` 
				WHERE
					employee_id = '".$manager."'");

	if (count($emp_sync) > 0) {
		foreach ($emp_sync as $key) {
			$position = $key->position;
			$position_bolean = $key->position_bolean;
			$is_logistic = $key->is_logistic;
			$is_foreman = $key->is_foreman;
			$department = $key->department;
		}

		if ($role == 'MIS') {
			return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
		}else{
			if ($position_bolean == 1 && $is_foreman == 1 && $is_logistic == 1) {
				return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
			}elseif ($position_bolean == 1 && $is_logistic == 0 && $is_foreman == 0) {
				return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
			}elseif ($position_bolean == 1 && $is_logistic == 1 && $is_foreman == 0) {
				return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
			}else{
				return redirect('home');
			}
		}
	}else{
		return redirect('home');
	}
}

public function fetchVisitorByManager()
{
	$manager = Auth::user()->username;
	$manager_name = Auth::user()->name;
	$role = Auth::user()->role_code;

	$emp_sync = DB::SELECT("SELECT * FROM `employee_syncs` where employee_id = '".$manager."'");

	if (count($emp_sync) > 0) {
		foreach ($emp_sync as $key) {
			$name = $key->name;
			$department = $key->department;
		}
		if ($role == 'MIS') {
			if ($department == null && $name == 'Budhi Apriyanto') {
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					( visitors.remark IS NULL AND employee_syncs.department = 'Management Information System' )
					OR ( visitors.remark IS NULL AND employee_syncs.name = 'Budhi Apriyanto' ) 
				ORDER BY
					id DESC");
			}elseif($name == 'Romy Agung Kurniawan'){
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					visitors.remark IS NULL AND employee_syncs.department = '".$department."'
				ORDER BY
					id DESC");
			}else{
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					visitors.remark IS NULL
				ORDER BY
					id DESC");
			}
		}else{
			if ($department == null && $name == 'Arief Soekamto') {
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					( visitors.remark IS NULL AND employee_syncs.department = 'Human Resources' ) 
					OR ( visitors.remark IS NULL AND employee_syncs.department = 'General Affairs' )
					OR ( visitors.remark IS NULL AND employee_syncs.name = 'Arief Soekamto' ) 
				ORDER BY
					id DESC");
			}elseif ($name == 'Susilo Basri Prasetyo') {
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					( visitors.remark IS NULL AND employee_syncs.department = 'Maintenance' ) 
					OR ( visitors.remark IS NULL AND employee_syncs.department = 'Production Engineering' )
				ORDER BY
					id DESC");
			}else{
				$lists = DB::SELECT("SELECT
					visitors.id,
					name,
					department,
					company,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
					visitors.created_at,
					visitor_details.full_name,
					visitors.jumlah AS total1,
					purpose,
					visitors.status,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				WHERE
					visitors.remark IS NULL AND employee_syncs.department = '".$department."'
				ORDER BY
					id DESC");
			}
		}

		$response = array(
			'status' => true,
			'lists' => $lists,
			'manager_name' => $manager_name,
			'name' => $name,
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false
		);
		return Response::json($response);
	}
}


///----------------------- KELUAR

public function leave()
{
	return view('visitors.leave')->with('page', 'Visitor Leave');
}


public function getvisit(Request $request)

{
	$id = $request->get('id');
	$op = "SELECT DISTINCT( visitor_details.tag),visitors.company,visitors.remark, visitor_details.id_number,visitor_details.full_name, visitor_details.in_time, employees.name, mutation_logs.department from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	left join employees on visitors.employee = employees.employee_id 
	LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id

	where visitor_details.tag='".$id."' and visitor_details.out_time ='' limit 1";

	$ops = DB::select($op);

	$response = array(
		'status' => true,
		'ops' => $ops,
		'message' => 'Confirm Visitors Leave Success'      
	);
	return Response::json($response);
}

public function out(Request $request){

	try {
		// $id = $request->get('id');
		$tag = $request->get('idtag');
		$reason = $request->get('reason');
		$time = date('H:i:s');

		$visitordetail = VisitorDetail::where('tag','=', $tag)
		->where('out_time','=','')		      
		->first();
		$visitorhead = Visitor::where('id','=', $visitordetail->id_visitor)		      
		->first();

		if($reason ==""){
			$visitordetail->out_time = $time;		
			$visitordetail->save();
		}else{
			$visitordetail->out_time = $time;		
			$visitordetail->save();
			$visitorhead->reason = $reason;
			$visitorhead->save();
		}


		$response = array(
			'status' => true,
			'message' => 'Confirm Visitors Leave Success'
		);
		return Response::json($response);
	}
	catch(\Exception $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}

}

//-------------------display

public function display()
{
	return view('visitors.list')->with('page', 'Visitor display');
}

public function filldisplay($nik, Request $request)
{

	$kurang = date('Y-m-d',strtotime('-14 days'));
	$tgl = date('m-Y');
	$tgl2 = date('Y-m-d');

	if($request->get('date') > 0){
		$date = $request->get('date');
	}else{
		$date = date('Y-m-d');

	}


	if ($nik !="") {
		$where = "where employee = '".$nik."'";
	}

	if($nik =="asd"){
		$where="WHERE DATE_FORMAT(created_at,'%m-%Y') = '".$tgl."' ";
	}

	if($nik =="display"){

		$where=" WHERE DATE_FORMAT(created_at,'%Y-%m-%d') ='".$date."' ";
	}

	$op = "SELECT
				*,
				count(
				DISTINCT ( total1 )) AS total 
			FROM
				(
				SELECT
					visitors.reason,
					visitors.employee,
					visitors.id,
					company,
					visitor_details.full_name,
					visitor_details.id_number AS total1,
					purpose,
					visitors.status,
					employee_syncs.name,
					employee_syncs.department,
					visitor_details.in_time,
					visitor_details.out_time,
					visitors.remark,
					visitors.created_at,
					DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) AS tgl 
				FROM
					visitors
					LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
					LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
				) a 
			".$where."
			GROUP BY
				a.id 
			ORDER BY
				a.id DESC";

	$ops = DB::select($op);

	return DataTables::of($ops)->make(true);
}

public function getchart(Request $request)

{
	$kurang = date('Y-m-d',strtotime('-30 days'));
	$bln = date('m-Y');
	$tgl = date('Y-m-d');
	$id = $request->get('id');
	$oplama = "select tglok, COALESCE(d.vendor,0) as vendor, COALESCE(d.visitor,0) as visitor  from (
	SELECT  b.tgl, sum(b.vendor) as vendor, sum(b.visitor) as visitor  from (
	select final.status, final.tgl, sum(final.total_vendor) as vendor, sum(final.total_visitor) as visitor from
	(
	SELECT Status, tgl ,count(total1) as total_vendor, 0 as total_visitor from (
	select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	) a WHERE DATE_FORMAT(a.tgl,'%m-%Y') = '04-2019' and a.status = 'Vendor' GROUP BY a.tgl,a.Status
	
	union 
	
	SELECT Status, tgl, 0 as total_vendor, count(total1) as total_visitor from (
	select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	) a WHERE DATE_FORMAT(a.tgl,'%m-%Y') = '".$bln."' and a.status = 'visitor' GROUP BY a.tgl,a.Status
	) as final
	group by final.status, final.tgl
	) b GROUP BY b.tgl
	) d
	
	RIGHT JOIN (	
	select week_date as tglok, 0 as vendor, 0 as visitor from weekly_calendars 
	WHERE DATE_FORMAT(week_date,'%d-%m-%Y')<='".$tgl."' and DATE_FORMAT(week_date,'%m-%Y')='".$bln."' 
	
	) c on d.tgl = c.tglok
	ORDER BY tglok asc
	
	";

	$op="select DATE_FORMAT(tglok,'%d %b %y') as tglok, COALESCE(d.vendor,0) as vendor, COALESCE(d.visitor,0) as visitor  from (
	SELECT  b.tgl, sum(b.vendor) as vendor, sum(b.visitor) as visitor  from (
	select final.status, final.tgl, sum(final.total_vendor) as vendor, sum(final.total_visitor) as visitor from
	(
	SELECT Status, tgl ,count(total1) as total_vendor, 0 as total_visitor from (
	select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	) a WHERE a.status = 'Vendor' GROUP BY a.tgl,a.Status
	
	union 
	
	SELECT Status, tgl, 0 as total_vendor, count(total1) as total_visitor from (
	select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	) a WHERE a.status = 'visitor' GROUP BY a.tgl,a.Status
	) as final
	group by final.status, final.tgl
	) b GROUP BY b.tgl
	) d
	
	RIGHT JOIN (	
	select week_date as tglok, 0 as vendor, 0 as visitor from weekly_calendars 
	WHERE week_date >='".$kurang."' and week_date<='".$tgl."'
	
	) c on d.tgl = c.tglok
	ORDER BY   DATE_FORMAT(tglok,'%Y-%m-%d') asc";

	$ops = DB::select($op);

	$response = array(
		'status' => true,
		'ops' => $ops,
		'message' => $kurang    
	);
	return Response::json($response);
}
}
