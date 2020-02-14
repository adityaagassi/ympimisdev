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
use DataTables;
use File;
use Storage;

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
		$employees = "SELECT DISTINCT( employees.employee_id) as employee_id,employees.`name`,mutation_logs.department, cost_centers.department as shortname from employees
		LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id
		LEFT JOIN cost_centers on mutation_logs.cost_center = cost_centers.cost_center
		WHERE mutation_logs.valid_to is null and employees.end_date is null  ORDER BY mutation_logs.department asc";
		$employee = DB::select($employees);
		return view('visitors.registration', array(
			'employee' => $employee,
		))->with('page', 'Visitor Registration');
	}


	public function simpanheader(Request $request)
	{
		$lop = $request->get('lop2');
		// var_dump($id);
		try {
			//----insert visitor
			$visitor = new visitor([
				'company' => $request->get('company'),
				'purpose' => $request->get('purpose'),
				'status' => $request->get('status'),
				'employee'=> $request->get('employee'),
				'transport'=>  $request->get('kendaraan'),
				'pol'=>  $request->get('pol'),
			]);	
			$visitor -> save();	
			$id = Visitor::orderby('created_at','desc')->first();
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
			$where=" where a.created_at2 >='".$kurang."' and a.created_at2<='".$tgl."' and employee in ( SELECT employee_id from mutation_logs WHERE department in ( SELECT department from mutation_logs WHERE employee_id ='".$nik."' and valid_to is null ) and valid_to is null)";
		}

		if($nik =="asd"){
			$where="WHERE a.created_at2 >='".$kurang."' and a.created_at2<='".$tgl."' ";
		}

		$op="SELECT *,count(DISTINCT(total1)) as total from (
		select DATE_FORMAT(visitors.created_at,'%Y-%m-%d') created_at2,visitors.created_at,visitors.employee, visitors.id, company, visitor_details.full_name, visitor_details.id_number as total1 ,purpose, visitors.status, employees.name, mutation_logs.department, visitor_details.in_time, visitor_details.out_time, visitors.remark from visitors
		left join visitor_details on visitors.id = visitor_details.id_visitor
		LEFT JOIN employees on visitors.employee = employees.employee_id
		LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id

	) a ".$where." GROUP BY a.id order by created_at desc";
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
	$header_lists = "select DISTINCT( visitors.id),company,name,mutation_logs.department,cost_centers.department as shortname from visitors LEFT JOIN employees on visitors.employee = employees.employee_id 
	LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id
	LEFT JOIN cost_centers on mutation_logs.cost_center = cost_centers.cost_center
	where visitors.id='".$id."'";
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

//-------------------- end lis


//--------------------- confirmation

public function confirmation()
{
	return view('visitors.confirmation')->with('page', 'Visitor Confirmation');
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

public function telpon()
{		

	$telpon="select person, dept, nomor from telephone_lists";
	$telpons = DB::select($telpon);
	return DataTables::of($telpons)
	->make(true);
}

public function confirmation_manager()
{
	// var_dump(stream_get_contents(fopen("\\\\172.17.128.87\\MIS\\Book1.csv", "r")));
	// foreach (glob(public_path('*.csv')) as $filename) {
	//       // echo "$filename size " . filesize($filename) . "\n";
	//     // var_dump();
	//     $data = substr($filename,-9,5);
	//     $file = File::get($filename);
	//     $filepecah = explode(' ', $file);
	//     // var_dump(substr($filepecah[3], 7,2));
	//     // var_dump($file);
	//   }
	$manager = Auth::user()->username;
	$emp_sync = DB::SELECT("SELECT * FROM `employee_syncs` where employee_id = '".$manager."'");

	foreach ($emp_sync as $key) {
		$position = $key->position;
	}

	if (strpos($position, 'Manager') === false) {
		return redirect('home');
	}else{
		return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
	}
}

public function fetchVisitorByManager()
{
	$manager = Auth::user()->username;
	$manager_name = Auth::user()->name;

	$emp_sync = DB::SELECT("SELECT * FROM `employee_syncs` where employee_id = '".$manager."'");

	foreach ($emp_sync as $key) {
		$name = $key->name;
	}
	$lists = DB::SELECT("select 
		visitors.id,name,department,company,DATE_FORMAT(visitors.created_at,'%Y-%m-%d') created_at2,visitors.created_at,visitor_details.full_name, visitor_details.id_number as total1 ,purpose, visitors.status,visitor_details.in_time, visitor_details.out_time, visitors.remark 
		from visitors 
		LEFT JOIN visitor_details on visitors.id = visitor_details.id_visitor 
		LEFT JOIN employee_syncs on visitors.employee = employee_syncs.employee_id
		where visitors.remark is null and employee_syncs.nik_manager = '".$manager."'");

	$response = array(
		'status' => true,
		'lists' => $lists,
		'manager_name' => $manager_name,
		'name' => $name,
	);
	return Response::json($response);
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

	where visitor_details.tag='".$id."' and visitor_details.out_time ='' ";

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

	$op = "SELECT *,count(DISTINCT(total1)) as total from (
	select visitors.reason,visitors.employee, visitors.id, company, visitor_details.full_name, visitor_details.id_number as total1 ,purpose, visitors.status, employees.name, mutation_logs.department, visitor_details.in_time, visitor_details.out_time, visitors.remark, visitors.created_at, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
	left join visitor_details on visitors.id = visitor_details.id_visitor
	LEFT JOIN employees on visitors.employee = employees.employee_id
	LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id) a ".$where."
	GROUP BY a.id  order by a.id desc";

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
