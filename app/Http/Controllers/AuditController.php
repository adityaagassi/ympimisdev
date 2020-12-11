<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use PDF;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AuditAllResult;
use App\EmployeeSync;

class AuditController extends Controller
{

	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
			if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
			{
				die();
			}
		}      
		$this->middleware('auth');

		$this->location = ['Assembly','Accounting','Body Process','Exim','Material Process','Surface Treatment','Educational Instrument','Standardization','QA Process','Chemical Process Control','Human Resources','General Affairs','Workshop and Maintenance Molding','Production Engineering','Maintenance','Procurement','Production Control','Warehouse','Welding Process'];
	}

	public function index()
	{
		$title = "Audit Patrol";
		$title_jp = "";

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department')->first();

		$auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
			where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

		return view('audit.patrol', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'auditee' => $auditee,
			'location' => $this->location
		))->with('page', 'Audit Patrol');
	}


	public function post_audit(Request $request)
	{
		$audit = $request->get("audit");
		$datas = [];

		for ($i=0; $i < count($request->get('patrol_lokasi')); $i++) { 
			$patrol = new AuditAllResult;
			$patrol->tanggal = date('Y-m-d');
			$patrol->kategori = $request->get('category');
			$patrol->auditor_id = $request->get('auditor_id') ;
			$patrol->auditor_name = $request->get('auditor_name');
			$patrol->lokasi = $request->get('patrol_lokasi')[$i];
			$patrol->auditee_name = $request->get('patrol_pic')[$i];
			$patrol->point_judul = $request->get('patrol_detail')[$i];
			$patrol->note = $request->get('note')[$i];
			$patrol->created_by = Auth::id();
			$patrol->save();
		}

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function post_audit_file(Request $request)
	{
		try {
			$id_user = Auth::id();
			$tujuan_upload = 'files/patrol';

            // dd($request);

			for ($i=0; $i < $request->input('jumlah'); $i++) { 

				$file = $request->file('file_datas_'.$i);
				$nama = $file->getClientOriginalName();

				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);

				$filename = md5($filename.date('YmdHisa')).'.'.$extension;

				$file->move($tujuan_upload,$filename);

				AuditAllResult::create([
					'tanggal' => date('Y-m-d'),
					'kategori' => $request->input('category'),
					'auditor_id' => $request->input('auditor_id'),
					'auditor_name' => $request->input('auditor_name'),
					'auditee_name' => $request->input('patrol_pic_'.$i),
					'lokasi' => $request->input('patrol_lokasi_'.$i),
					'point_judul' => $request->input('patrol_detail_'.$i),
					'note' => $request->input('note_'.$i),
					'foto' => $filename,
					'created_by' => $id_user
				]);
			}


			$response = array(
				'status' => true,
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




	public function fetch_audit(Request $request)
	{
		try {

			$kategori = $request->get("category");

			$query = 'SELECT * FROM standarisasi_audit_checklists where point_question is not null and deleted_at is null and kategori = "'.$kategori.'" order by id asc';
			$detail = db::select($query);

			$response = array(
				'status' => true,
				'lists' => $detail
			);

			return Response::json($response);

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message'=> $e->getMessage()
			);

			return Response::json($response); 
		}
	}

}
