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
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AuditAllResult;
use App\StandarisasiAuditIso;
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

    $this->point_sup = ['Jalan - Lantai - Tempat Kerja - Tembok - Atap', 'Kontrol Lemari Dokumen, Jig, Penyimpanan, Alat Kebersihan', 'Meja Kerja - Meja Office', 'Oa Perkakas Mesin - Telepon', 'Mesin - Equipment','Pencegahan Kebakaran - Pencegahan Bencana - Barang Berbahaya - Barang Beracun','Tempat Istirahat, Meeting Room, Lobby, Di Dalam Ruangan, Kantin','Kedisiplinan'];

    $this->point_1 = ['Jalan', 'Kebersihan', 'Barang tidak diperlukan', 'Informasi Papan'];
    
    $this->point_2 = ['Jalan 2', 'Kebersihan 3', 'Barang tidak diperlukan 2', 'Informasi Papan 2'];
	}

  public function index()
  {
    $title = "YMPI Internal Patrol";
    $title_jp = "内部パトロール";

    return view('audit.index_patrol', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPI Patrol'); 
  }

  public function index_audit()
  {
    $title = "YMPI Internal Audit";
    $title_jp = "内部監査";

    return view('audit.index_audit', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPI Patrol'); 
  }

	public function index_patrol()
	{
		$title = "5S Patrol GM & Presdir";
		$title_jp = "";

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department')->first();

		$auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
			where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

		return view('audit.patrol', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'auditee' => $auditee,
			'location' => $this->location,
      'poin' => $this->point_sup,
      'point_1' => $this->point_1,
      'point_2' => $this->point_2
		))->with('page', 'Audit Patrol');
	}

  public function fetch_patrol(Request $request){


    $data_all = db::select("
      SELECT
        kategori,
        sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
        audit_all_results 
      WHERE jenis = 'Patrol'
      GROUP BY
        kategori
      ORDER BY jumlah_belum ASC
    ");

    $data_type_all = db::select("
      SELECT
        point_judul,
        sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
        audit_all_results 
      WHERE point_judul is not null
      and jenis = 'Patrol'
      GROUP BY
        point_judul
      ORDER BY point_judul ASC
    ");

    $response = array(
      'status' => true,
      'data_all' => $data_all,
      'data_type_all' => $data_type_all
    );

    return Response::json($response);
  }

  public function index_mis()
  {
    $title = "Audit MIS";
    $title_jp = "";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_mis', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Audit Patrol MIS');
  }

  public function index_std()
  {
    $title = "EHS & 5S Monthly Patrol";
    $title_jp = "";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_std', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'EHS dan 5S Bulanan');
  }

  public function index_audit_stocktaking()
  {
    $title = "Audit Stocktaking";
    $title_jp = "";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.audit_stocktaking', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Audit Stocktaking');
  }


	public function post_audit(Request $request)
	{
		$audit = $request->get("audit");
		$datas = [];

		for ($i=0; $i < count($request->get('patrol_lokasi')); $i++) { 
			$patrol = new AuditAllResult;
			$patrol->tanggal = date('Y-m-d');
      $patrol->jenis = 'Patrol';
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

				$audit_all = AuditAllResult::create([
          'jenis' => 'Patrol',
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

        $id = $audit_all->id;

        $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
        $mailtoo = DB::select($mails);

        $isimail = "select * from audit_all_results where id = ".$id;

        $auditdata = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
			}

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		} 

    catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

  public function post_audit_stocktaking(Request $request)
  {
    try {
      $id_user = Auth::id();
      $tujuan_upload = 'files/patrol';

      for ($i=0; $i < $request->input('jumlah'); $i++) { 

        $file = $request->file('file_datas_'.$i);
        $nama = $file->getClientOriginalName();

        $filename = pathinfo($nama, PATHINFO_FILENAME);
        $extension = pathinfo($nama, PATHINFO_EXTENSION);

        $filename = md5($filename.date('YmdHisa')).'.'.$extension;

        $file->move($tujuan_upload,$filename);

        $audit_all = AuditAllResult::create([
          'jenis' => 'Audit',
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

        $id = $audit_all->id;

        $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
        $mailtoo = DB::select($mails);

        $isimail = "select * from audit_all_results where id = ".$id;

        $auditdata = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
      }

      $response = array(
        'status' => true,
      );
      return Response::json($response);
    } 

    catch (\Exception $e) {
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
       'title_jp' => 'パトロール監視',
     )
   )->with('page', 'Audit Patrol');
 }

 public function fetchMonitoring(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $first = date("Y-m-d", strtotime('-30 days'));

  $last = AuditAllResult::whereNull('status_ditangani')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
      }
    }
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $data = db::select("SELECT
    date_format(tanggal, '%a, %d %b %Y') AS tanggal,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir 
    FROM
    audit_all_results 
    WHERE
    tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
    and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    GROUP BY
    tanggal");

  $data_kategori = db::select("
  SELECT
    kategori,
    sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
  FROM
    audit_all_results 
  WHERE
    kategori IN ( 'S-Up And EHS Patrol Presdir', '5S Patrol GM' ) 
  GROUP BY
    kategori");

  $data_bulan = db::select("
    SELECT
    MONTHNAME(tanggal) as bulan,
    year(tanggal) as tahun,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir 
    FROM
    audit_all_results 
    WHERE
    kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    GROUP BY
    tahun,monthname(tanggal)
    order by tahun, month(tanggal) ASC"
  );

  $year = date('Y');

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_kategori' => $data_kategori,
    'data_bulan' => $data_bulan,
    'year' => $year
  );

  return Response::json($response);
}

public function detailMonitoring(Request $request){

    $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

      if ($status == "Temuan GM Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Open"){
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
      }
      else if ($status == "Temuan GM Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
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
      return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('penanganan', function($detail){
      return $detail->penanganan;
    })

    ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}


public function detailMonitoringCategory(Request $request){

    $kategori = $request->get('kategori');

    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan Belum Ditangani") {
        $stat = 'and audit_all_results.status_ditangani is null';
      }
      else if ($status == "Temuan Sudah Ditangani"){
        $stat = 'and audit_all_results.status_ditangani is not null';
      }

    } else{
      $stat = '';
    }

    if ($kategori == "EHS 5S Monthly Patrol") {
      $kategori = "EHS & 5S Patrol";
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and kategori = '".$kategori."' ".$stat."";

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
      return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('penanganan', function($detail){
      return $detail->penanganan;
    })

    ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}

public function detailMonitoringBulan(Request $request){

    $bulan = $request->get('bulan');
    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan GM Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Open"){
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
      }
      else if ($status == "Temuan GM Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat."";

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
      return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('penanganan', function($detail){
      return $detail->penanganan;
    })

    ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}

public function detailMonitoringType(Request $request){

    $type = $request->get('type');
    $status = $request->get('status');

    if ($status != null) {

     if ($status == "Temuan Belum Ditangani") {
        $stat = 'and audit_all_results.status_ditangani is null';
      }
      else if ($status == "Temuan Sudah Ditangani"){
        $stat = 'and audit_all_results.status_ditangani is not null';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and point_judul = '".$type."' ".$stat."";

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
      return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
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


  $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM') and tanggal between '".$datefrom."' and '".$dateto."' ".$kate." ");

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

  public function editAudit(Request $request)
  {
    try{
      $audit = AuditAllResult::find($request->get("id"));
      $audit->note = $request->get('note');
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

  public function exportPatrol(Request $request){
      $time = date('d-m-Y H;i;s');

      $tanggal = "";
      $status = "";

      if (strlen($request->get('date')) > 0)
      {
          $date = date('Y-m-d', strtotime($request->get('date')));
          $tanggal = "and tanggal = '" . $date . "'";
      }

      if (strlen($request->get('status')) > 0)
      {
          if($request->get('status') == 'Temuan GM Close') {
            $status = "and kategori = '5S Patrol GM' and status_ditangani is not null";
          }
          else if ($request->get('status') == 'Temuan GM Open') {
            $status = "and kategori = '5S Patrol GM' and status_ditangani is null";
          }
          else if ($request->get('status') == 'Temuan Presdir Close') {
            $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani is not null";
          }
          else if ($request->get('status') == 'Temuan Presdir Open') {
            $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani is null";
          }
      }

      $detail = db::select(
          "SELECT DISTINCT audit_all_results.* from audit_all_results WHERE audit_all_results.deleted_at IS NULL ".$tanggal." ".$status." order by id ASC");

      $data = array(
          'detail' => $detail
      );

      ob_clean();

      Excel::create('Audit List '.$time, function($excel) use ($data){
          $excel->sheet('Data', function($sheet) use ($data) {
            return $sheet->loadView('audit.audit_excel', $data);
        });
      })->export('xlsx');
    }




    // Audit & Patrol Monitoring All

    public function indexMonitoringAll($id){

      return view('audit.patrol_monitoring_all',  
         array(
           'title' => 'Audit & Patrol Monitoring', 
           'title_jp' => '',
           'category' => $id
         )
       )->with('page', 'Audit Patrol Monitoring');
     }

    public function fetchMonitoringAll(Request $request){

      $first = date("Y-m-d", strtotime('-30 days'));

      $check = AuditAllResult::where('status_ditangani', '=', 'close')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      if($first > date("Y-m-d", strtotime($check->tanggal))){
        $first = date("Y-m-d", strtotime($check->tanggal));
      }

      if ($request->get('category') == "monthly_patrol") {
        $category = "EHS & 5S Patrol";
      }else if ($request->get('category') == "stocktaking") {
        $category = "Audit Stocktaking";
      }

      $data = db::select("SELECT
        date_format(tanggal, '%a, %d %b %Y') AS tanggal,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        tanggal >= '".$first."'
        and kategori in ('".$category."')
        GROUP BY
        tanggal");

      $data_bulan = db::select("
        SELECT
        MONTHNAME(tanggal) as bulan,
        year(tanggal) as tahun,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        kategori in ('".$category."')
        GROUP BY
        tahun,monthname(tanggal)
        order by tahun, month(tanggal) ASC"
      );

      $response = array(
        'status' => true,
        'datas' => $data,
        'data_bulan' => $data_bulan,
        'category' => $category
      );

      return Response::json($response);
    }

    public function fetchTableAuditAll(Request $request)
    {
      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = AuditAllResult::whereNull('status_ditangani')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      $status = $request->get('status');

      if ($status != null) {
        $cat = json_encode($status);
        $kat = str_replace(array("[","]"),array("(",")"),$cat);

        $kate = 'and audit_all_results.status_ditangani in'.$kat;
      }else{
        $kate = 'and audit_all_results.status_ditangani is null';
      }


      if ($request->get('category') == "monthly_patrol") {
        $category = "EHS & 5S Patrol";
      }else if ($request->get('category') == "stocktaking") {
        $category = "Audit Stocktaking";
      }


      $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('".$category."') ".$kate." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }

    public function detailMonitoringAll(Request $request){

      $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      $status = $request->get('status');

      if ($status != null) {

      if ($status == "Temuan Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
      }
      else if ($status == "Temuan Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat."";

    $detail = db::select($query);

    return DataTables::of($detail)

    ->editColumn('tanggal', function($detail){
      return date('d-M-Y', strtotime($detail->tanggal));
    })

    ->editColumn('foto', function($detail){
      return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('penanganan', function($detail){
      return $detail->penanganan;
    })

    ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
  }

  public function detailMonitoringBulanAll(Request $request){

    $bulan = $request->get('bulan');
    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
      }
      else if ($status == "Temuan Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
      }

    } else{
      $stat = '';
    }

      $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->editColumn('tanggal', function($detail){
        return date('d-M-Y', strtotime($detail->tanggal));
      })

      ->editColumn('foto', function($detail){
        return '<img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
      })

      ->editColumn('penanganan', function($detail){
        return $detail->penanganan;
      })

      ->rawColumns(['tanggal' => 'tanggal', 'foto' => 'foto','penanganan' => 'penanganan'])
      ->make(true);
  }


  public function packing_documentation()
  {
    $title = "Packing Documentation";
    $title_jp = "";

    return view('documentation.index_packing_documentation', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'Packing Documentation'); 
  }
}
