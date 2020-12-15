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
					'lokasi' => $request->input('location'),
					'auditor_id' => $request->input('auditor_id'),
					'auditor_name' => $request->input('auditor_name'),
					'auditee_name' => $request->input('patrol_pic_'.$i),
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


	public function indexMonitoring(){

	    return view('audit.patrol_monitoring',  
	      array(
	          'title' => 'Patrol Monitoring', 
	          'title_jp' => '',
	        )
	      )->with('page', 'Audit Patrol');
	}

	public function fetchMonitoring(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = AuditAllResult::whereNull('status_ditangani')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->audit_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->audit_date));
          }
        }
      }


      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {
          $cat = json_encode($status);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and audit_all_results.status_ditangani in'.$kat;
      }else{
          $kate = '';
      }

      //per tgl
      $data = db::select("select tanggal, sum(case when status_ditangani is null then 1 else 0 end) as jumlah_belum, sum(case when status_ditangani is not null then 1 else 0 end) as jumlah_sudah from audit_all_results group by tanggal");
      $year = date('Y');

      $response = array(
        'status' => true,
        'datas' => $data,
        'year' => $year
      );

      return Response::json($response);
  }

  public function detailMonitoring(Request $request){

      $tgl = $request->get("tgl");

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

	      if ($status == "Belum Ditangani") {
	        
	      	$stat = 'and audit_all_results.status_ditangani is null';
	      }

	      if ($status == "Sudah Ditangani") {
	      	$stat = 'and audit_all_results.status_ditangani = "close"';
	      }

      
      } else{
          $stat = '';
      }

      $datefrom = $request->get('datefrom');
      $dateto = $request->get('dateto');

      if ($datefrom != null && $dateto != null) {
          $df = 'and audit_all_results.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
      }else{
          $df = '';
      }

      $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->editColumn('kategori', function($detail){
        $kategori = '';
	        
        if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        	$kategori = "Presdir";
        }else if ($detail->kategori == "5S Patrol GM"){
        	$kategori = "GM";
        }

        return $kategori;
      })

      ->editColumn('tanggal', function($detail){
        return date('d-M-Y', strtotime($detail->tanggal));
      })

      ->editColumn('foto', function($detail){
        return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="150">';
      })

      ->editColumn('penanganan', function($detail){
        return $detail->penanganan;
      })

      ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
      ->make(true);
  }

  public function fetchtable_audit(Request $request)
    {

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = AuditAllResult::whereNull('status_ditangani')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->audit_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->audit_date));
          }
        }
      }


      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {
          $cat = json_encode($status);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and audit_all_results.status_ditangani in'.$kat;
      }else{
          $kate = 'and audit_all_results.status_ditangani is null';
      }


      $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and tanggal between '".$datefrom."' and '".$dateto."' ".$kate." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }


    public function detailPenanganan(Request $request){
		$audit = db::select("SELECT
			* from audit_all_results where id = ". $request->get('id'));

		$response = array(
			'status' => true,
			'audit' => $audit,
		);
		return Response::json($response);
	}

	public function postPenanganan(Request $request)
    {
        try{
            $audit = AuditAllResult::find($request->get("id"));
            $audit->penanganan = $request->get('penanganan');
            $audit->tanggal_penanganan = date('Y-m-d');
            $audit->status_ditangani = 'close';
            $audit->save();

            $response = array(
              'status' => true,
              'datas' => "Berhasil",
          );
            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
               $response = array(
                  'status' => false,
                  'datas' => "Audit Already Exist",
              );
               return Response::json($response);
           }
           else{
               $response = array(
                  'status' => false,
                  'datas' => $e->getMessage(),
              );
               return Response::json($response);
           }
       }
   }

}
