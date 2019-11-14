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
use App\QcCpar;
use App\QcCar;
use App\QcCparItem;
use App\Department;
use App\Employee;
use App\Material;
use App\Status;
use App\WeeklyCalendar;
use App\Destination;
use App\Vendor;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
// use App\QcTtdCoba;


class QcReportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->staff = 'O11081664';
      $this->chief = 'G03110980';
      $this->manager = 'A97100056';
      $this->dgm = 'E01090823';
      $this->gm = 'P12061848';
    }

    public function index()
    {	
        $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->orderBy('qc_cpars.id','DESC')
        ->get();
        
        $departments = Department::select('departments.id', 'departments.department_name')->get();

        // $materials = Material::select('materials.material_number', 'materials.material_description')->get();
        $statuses = DB::table('statuses')->select('statuses.status_code', 'statuses.status_name')->offset(0)->limit(2)->get();

        return view('qc_report.index', array(
            'cpars' => $cpars,
            'departments' => $departments,
            'statuses' => $statuses
        ))->with('page', 'CPAR');
    }

    function filter_cpar(Request $request)
    {
        $cpar_detailsTable = DB::table('qc_cpars')
        ->leftjoin('departments','qc_cpars.department_id','=','departments.id')
        ->leftjoin('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->leftjoin('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->select('qc_cpars.id','qc_cpars.cpar_no','qc_cpars.kategori', 'employees.name', 'qc_cpars.lokasi', 'qc_cpars.tgl_permintaan', 'qc_cpars.tgl_balas', 'qc_cpars.via_komplain', 'qc_cpars.email_status', 'departments.department_name', 'qc_cpars.sumber_komplain', 'qc_cpars.status_code', 'statuses.status_name', 'qc_cpars.created_at')
        ->whereNull('qc_cpars.deleted_at');

        if(strlen($request->get('tgl_permintaan')) > 0){
          $tgl_permintaan = $request->get('tgl_permintaan');
          $date_permintaan = str_replace('/', '-', $tgl_permintaan);

          // $date_request = date('Y-m-d', strtotime($request->get('tgl_permintaan')));
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.tgl_permintaan', '=', date("Y-m-d", strtotime($date_permintaan)));
        }

        if(strlen($request->get('kategori')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.kategori', '=', $request->get('kategori'));
        }


        if(strlen($request->get('tgl_balas')) > 0){
          $tgl_balas = $request->get('tgl_balas');
          $date_balas = str_replace('/', '-', $tgl_balas);
          // $date_reply = date('Y-m-d', strtotime($request->get('tgl_balas')));
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.tgl_balas', '=', date("Y-m-d", strtotime($date_balas)));
        }

        if(strlen($request->get('department_id')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.department_id', '=', $request->get('department_id'));
        }
        
        if(strlen($request->get('status_code')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.status_code', '=', $request->get('status_code'));
        }
         $cpar_detailsTable = $cpar_detailsTable->orderBy('qc_cpars.id', 'DESC');
        $cpar_details = $cpar_detailsTable->get();

        return DataTables::of($cpar_details)

        ->editColumn('status_name',function($cpar_details){
            if($cpar_details->status_name == "Open") {
              return '<label class="label label-success">'.$cpar_details->status_name. '</label>';
            }
            else if($cpar_details->status_name == "Closed"){
              return '<label class="label label-danger">'.$cpar_details->status_name. '</label>';
            }
          })

        ->addColumn('action', function($cpar_details){
          $idcpar = $cpar_details->id;
          $no_cpar = $cpar_details->cpar_no;
          
          // if($cpar_details->email_status != "Sent") {
          //     return '<a href="qc_report/update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
          //         <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('.$idcpar.');">Delete</a>
          //         <a href="qc_report/sendemail/'.$idcpar.'" class="btn btn-warning btn-xs">Send Email</a>';
          // }
          return '<a href="qc_report/update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
                  <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('.$idcpar.');">Delete</a>';        
        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action'])
        ->make(true);
    }

    public function create()
    {
        $managers = Employee::select('employees.employee_id','employees.name','promotion_logs.position','mutation_logs.department')
        ->join('promotion_logs','employees.employee_id','=','promotion_logs.employee_id')
        ->join('mutation_logs','employees.employee_id','=','mutation_logs.employee_id')
        // ->join('departments','depar.employee_id','=','mutation_logs.employee_id')
        ->whereNull('promotion_logs.valid_to')
        ->whereNull('mutation_logs.valid_to')
        ->whereNull('employees.end_date')
        ->where('promotion_logs.position','manager')
        ->distinct()
        ->get();

        $productions = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','=','5')
        ->where('departments.id','<>','11')
        ->get();

        $procurements = Department::select('departments.*')
        ->where('id','=','7')
        ->get();

        $others = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','<>','5')
        ->whereNotIn('departments.id',['1','2','3','4','7'])
        ->get();

        $destinations = Destination::select('destinations.*')->get();

        $vendors = "select id, vendor, name from vendors";
        $vendor = DB::select($vendors);

        return view('qc_report.create', array(
            'managers' => $managers,
            'productions'  => $productions,
            'procurements' => $procurements,
            'others' =>  $others,
            'destinations' => $destinations,
            'vendors' => $vendor
        ))->with('page', 'CPAR');
    }

    public function create_action(request $request)
    {
      try{
          
          $files=array();
          $file = new QcCpar();
          if ($request->file('files') != NULL) {
            // var_dump($request->file('files'));die();
            if($files=$request->file('files')) {
              foreach($files as $file){
                $nama=$file->getClientOriginalName();
                $file->move('files',$nama);
                $data[]=$nama;              
              }
            }            
            $file->filename=json_encode($data);           
          }
          else {
            $file->filename=NULL;
          }


          // $file = $request->file('file');
          // if ($file != NULL) {
          //   $tujuan_upload = 'files';
          //   $file->move($tujuan_upload,$file->getClientOriginalName());
          //   $file = $file->getClientOriginalName();            
          // }
          // else{
          //   $file == "";
          // }

          $id_user = Auth::id();

          $tgl_permintaan = $request->get('tgl_permintaan');
          $tgl_balas = $request->get('tgl_balas');

          $date_permintaan = str_replace('/', '-', $tgl_permintaan);
          $date_balas = str_replace('/', '-', $tgl_balas);

          $cpars = new QcCpar([
            'cpar_no' => $request->get('cpar_no'),
            'kategori' => $request->get('kategori'),
            'employee_id' => $request->get('employee_id'),
            'lokasi' => $request->get('lokasi'),
            'department_id' => $request->get('department_id'),
            'tgl_permintaan' => date("Y-m-d", strtotime($date_permintaan)),
            'tgl_balas' => date("Y-m-d", strtotime($date_balas)),
            'file' => $file->filename,
            'via_komplain' => $request->get('via_komplain'),
            'sumber_komplain' => $request->get('sumber_komplain'),
            'destination_code' => $request->get('customer'),
            'vendor' => $request->get('supplier'),
            'staff' => $this->staff,
            'chief' => $this->chief,
            'manager' => $this->manager,
            'dgm' => $this->dgm,
            'gm' => $this->gm,
            'posisi' => 'staff',
            'created_by' => $id_user
          ]);

          // $cpar_detailsTable = DB::table('qc_cpars')->where('qc_cpars.id');
          // $id = QcCpar::select('qc_cpars.id')

          $cpars->save();

          return redirect('/index/qc_report/update/'.$cpars->id)
          ->with('status', 'New CPAR has been created.')
          ->with('page', 'CPAR List');
      }
      catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'CPAR');
            }
        }
    }

    public function update($id)
    {
        $managers = Employee::select('employees.*','promotion_logs.position','mutation_logs.department')
        ->join('promotion_logs','employees.employee_id','=','promotion_logs.employee_id')
        ->join('mutation_logs','employees.employee_id','=','mutation_logs.employee_id')
        ->whereNull('promotion_logs.valid_to')
        ->whereNull('employees.end_date')
        ->where('promotion_logs.position','manager')
        ->distinct()
        ->get();

        $productions = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','=','5')
        ->where('departments.id','<>','11')
        ->get();

        $procurements = Department::select('departments.*')
        ->where('id','=','7')
        ->get();

        $others = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','<>','5')
        ->whereNotIn('departments.id',['1','2','3','4','7'])
        ->get();

        $cpars = QcCpar::find($id);

        $destinations = Destination::select('destinations.*')->get();

        $vendors = "select id, vendor, name from vendors";
        $vendor = DB::select($vendors);

        $parts = QcCparItem::select('qc_cpar_items.*')
        ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
        // ->where('qc_cpar_items.cpar_no','=',$cpars->cpar_no)
        ->get();

        $materials = Material::select('materials.id','materials.material_number','materials.material_description')
        ->orderBy('materials.id','ASC')
        ->get();

        return view('qc_report.edit', array(
            'cpars' => $cpars,
            'managers' => $managers,
            'productions'  => $productions,
            'procurements' => $procurements,
            'others' =>  $others,
            'destinations' => $destinations,
            'vendors' => $vendor,
            'parts' => $parts,
            'materials' =>  $materials
        ))->with('page', 'CPAR');
    }

    public function update_action(Request $request, $id)
    {
          try{

            $tgl_permintaan = $request->get('tgl_permintaan');
            $tgl_balas = $request->get('tgl_balas');

            $date_permintaan = str_replace('/', '-', $tgl_permintaan);
            $date_balas = str_replace('/', '-', $tgl_balas);

            $cpars = QcCpar::find($id);

            $files=array();
            
            // $file = new QcCpar();
            if ($request->file('files') != NULL) {
              if($files=$request->file('files')) {
                foreach($files as $file){
                  $nama=$file->getClientOriginalName();
                  $file->move('files',$nama);
                  $data[]=$nama;              
                }
              }

              $cpars->file=json_encode($data);           
            }

            // var_dump($cpars->filename);die();
            // $file = $request->file('file');

            // if($file != NULL){
            //     $tujuan_upload = 'files';
            //     $file->move($tujuan_upload,$file->getClientOriginalName());
            //     $cpars->file = $file->getClientOriginalName();
            // }
            
            $cpars->cpar_no = $request->get('cpar_no');
            $cpars->employee_id = $request->get('employee_id');
            $cpars->lokasi = $request->get('lokasi');
            $cpars->department_id = $request->get('department_id');
            $cpars->tgl_permintaan = date('Y-m-d', strtotime($date_permintaan));
            $cpars->tgl_balas = date("Y-m-d", strtotime($date_balas));
            $cpars->via_komplain = $request->get('via_komplain');
            $cpars->sumber_komplain = $request->get('sumber_komplain');
            $cpars->destination_code = $request->get('customer');
            $cpars->vendor = $request->get('supplier');

            $cpars->save();
            return redirect('/index/qc_report/update/'.$cpars->id)->with('status', 'CPAR data has been updated.')->with('page', 'CPAR');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'CPAR');
            }
          }
    }

    public function delete($id)
    {
        $cpars = QcCpar::find($id);
        $cpars->delete();

        return redirect('/index/qc_report')
        ->with('status', 'CPAR has been deleted.')
        ->with('page', 'CPAR');
    }

    //part item

    public function fetch_item($id)
    {

        $cpars = QcCpar::find($id);

        $qc_cpar_items = QcCparItem::leftJoin("qc_cpars","qc_cpar_items.cpar_no","=","qc_cpars.cpar_no")
        ->select('qc_cpar_items.*')
        ->where('qc_cpar_items.cpar_no','=',$cpars->cpar_no)
        ->get();

        return DataTables::of($qc_cpar_items)

          ->editColumn('detail_problem',function($qc_cpar_items){
            return $qc_cpar_items->detail_problem;
          })

          ->editColumn('defect_presentase',function($qc_cpar_items){
            return $qc_cpar_items->defect_presentase. ' %';
          })
          
          ->addColumn('action', function($qc_cpar_items){
            return '
            
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$qc_cpar_items->id.')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$qc_cpar_items->id.',\''.$qc_cpar_items->cpar_no.'\')">Delete</button>';
          })

      ->rawColumns(['detail_problem' => 'detail_problem','action' => 'action'])
      ->make(true);
    }

    public function create_item(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $parts = new QcCparItem([
                'cpar_no' => $request->get('cpar_no'),
                'part_item' => $request->get('part_item'),
                'no_invoice' => $request->get('no_invoice'),
                'lot_qty' => $request->get('lot_qty'),
                'sample_qty' => $request->get('sample_qty'),
                'detail_problem' => $request->get('detail_problem'),
                'defect_qty' => $request->get('defect_qty'),
                'defect_presentase' => $request->get('defect_presentase'),
                'created_by' => $id_user
            ]);

            $parts->save();

            $response = array(
              'status' => true,
              'parts' => $parts
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'parts' => "Material already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'parts' => "Material not created."
            );
             return Response::json($response);
           }
        }
    }

    public function fetch_item_edit(Request $request)
    {
      $qc_cpar_items = QcCparItem::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $qc_cpar_items,
      );
      return Response::json($response);
    }

    public function edit_item(Request $request)
    {
        try{
            $qc_cpar_items = QcCparItem::find($request->get("id"));
            $qc_cpar_items->part_item = $request->get('part_item');
            $qc_cpar_items->no_invoice = $request->get('no_invoice');
            $qc_cpar_items->lot_qty = $request->get('lot_qty');
            $qc_cpar_items->sample_qty = $request->get('sample_qty');
            $qc_cpar_items->detail_problem = $request->get('detail_problem');
            $qc_cpar_items->defect_qty = $request->get('defect_qty');
            $qc_cpar_items->defect_presentase = $request->get('defect_presentase');
            $qc_cpar_items->save();

            $response = array(
              'status' => true
            );
            return Response::json($response);

          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Material already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update Material Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function view_item(Request $request)
    {
      $query = "select qc_cpar_items.cpar_no, qc_cpar_items.part_item, qc_cpar_items.no_invoice, qc_cpar_items.lot_qty, qc_cpar_items.sample_qty, qc_cpar_items.detail_problem, qc_cpar_items.defect_qty, qc_cpar_items.defect_presentase, qc_cpar_items.created_at, qc_cpar_items.updated_at from qc_cpar_items 
        left join qc_cpars on qc_cpar_items.cpar_no = qc_cpars.cpar_no
        where qc_cpar_items.id = ".$request->get('id')."";

      $qc_cpar_items = DB::select($query);

      $response = array(
        'status' => true,
        'datas' => $qc_cpar_items
      );
      return Response::json($response);
    }

    public function delete_item(Request $request)
    {
      $qc_cpars_items = QcCparItem::find($request->get("id"));
      $qc_cpars_items->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }

    public function get_fiscal(Request $request)
    {
      $datenow = date('Y-m-d');
      $query = "SELECT fiscal_year FROM `weekly_calendars` where week_date = '$datenow'";
      $tahun = DB::select($query);
      foreach ($tahun as $year) {
        $html = $year->fiscal_year;
      }

      return json_encode($html);
    }

    public function get_nomor_depan(Request $request)
    {
      $kategori = $request->kategori;
      $query = "SELECT * FROM `qc_cpars` where kategori = '$kategori' ORDER BY id DESC LIMIT 1";
      $nomordepan = DB::select($query);
      $nomor = '';
      foreach ($nomordepan as $nomors) {
        $nomor = $nomors->cpar_no;
      }
      return json_encode($nomor);
    }

    //grafik CPAR

    public function grafik_cpar(){
      $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      $bulan = db::select("select DISTINCT MONTH(tgl_permintaan) as bulan, MONTHNAME(tgl_permintaan) as namabulan FROM qc_cpars order by bulan asc;");
      $tahun = db::select("select DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
      $dept = db::select("select id, department_name from departments where departments.id not in (1,2,3,4,11)");
       return view('qc_report.grafik',  
        array('title' => 'QC Report', 
              'title_jp' => 'QC Report',
              'fys' => $fys,
              'bulans' => $bulan,
              'years' => $tahun, 
              'departemens' => $dept
            )
        )->with('page', 'CPAR Graph');
    }

    public function fetchReport(Request $request)
    {
      // if(strlen($request->get('tgl')) > 0){
      //   $tgl = $request->get("tgl");
      // }else{
      //   $tgl = date("Y-m-d");
      // }

      // if($request->get('tgl') != null){
      //   $bulan = $request->get('tgl');
      //   $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
      //   foreach($fynow as $fynow){
      //       $fy = $fynow->fiscal_year;
      //   }
      // }
      // else{
      //   $bulan = date('Y-m');
      //   $fynow = DB::select("select fiscal_year from weekly_calendars where CURDATE() = week_date");
      //   foreach($fynow as $fynow){
      //       $fy = $fynow->fiscal_year;
      //   }
      // }        
      $tahun = date('Y');
      $tglfrom = $request->get('tglfrom');
      $tglto = $request->get('tglto');

      if ($tglfrom == "") {
          $tglfrom = $tahun."-01";
      }

      if ($tglto == "") {
          $tglto = $tahun."-12";
      }

      // $files=array();

      $kategori = $request->get('kategori');

      if ($kategori != null) {
          $cat = json_encode($kategori);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and kategori in'.$kat;
      }else{
          $kate = '';
      }

      $departemen = $request->get('departemen');

      if ($departemen != null) {
          $deptt = json_encode($departemen);
          $dept = str_replace(array("[","]"),array("(",")"),$deptt);

          $dep = 'and department_id in'.$dept;
      } else {
          $dep = '';
      }      

      $data = db::select("select count(cpar_no) as jumlah, monthname(tgl_permintaan) as bulan, sum(case when qc_cpars.status_code = '0' then 1 else 0 end) as open, sum(case when qc_cpars.status_code = '1' then 1 else 0 end) as close from qc_cpars LEFT JOIN statuses on statuses.status_code = qc_cpars.status_code where DATE_FORMAT(tgl_permintaan,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$kate." ".$dep."  GROUP BY bulan order by month(tgl_permintaan) ASC");

      // $tahun = date('Y');
      // $monthTitle = date("Y", strtotime($bulan));

      $response = array(
        'status' => true,
        'datas' => $data,
        'tahun' => $tahun,
        'tglfrom' => $tglfrom,
        'tglto' => $tglto,
        'kategori' =>  $kate,
        'departemen' => $dep
      );

      return Response::json($response); 
    }

    public function fetchDept(Request $request)
    {   
      $tahun = date('Y');
      $tglfrom = $request->get('tglfrom');
      $tglto = $request->get('tglto');

      if ($tglfrom == "") {
          $tglfrom = $tahun."-01";
      }

      if ($tglto == "") {
          $tglto = $tahun."-12";
      }

      // $files=array();

      $kategori = $request->get('kategori');

      if ($kategori != null) {
          $cat = json_encode($kategori);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and kategori in'.$kat;
      }else{
          $kate = '';
      }

      $departemen = $request->get('departemen');

      if ($departemen != null) {
          $deptt = json_encode($departemen);
          $dept = str_replace(array("[","]"),array("(",")"),$deptt);

          $dep = 'and department_id in'.$dept;
      }else{
          $dep = '';
      }      

      $data = db::select("SELECT count(cpar_no) as jumlah, department_name,sum(case when qc_cpars.status_code = '0' then 1 else 0 end) as open, sum(case when qc_cpars.status_code = '1' then 1 else 0 end) as close from departments 
        LEFT JOIN qc_cpars on qc_cpars.department_id = departments.id 
        LEFT JOIN statuses on statuses.status_code = qc_cpars.status_code 
        where departments.id not in (1,2,3,4,5,11)
        GROUP BY departments.department_name ORDER BY departments.id;");

      // $tahun = date('Y');

      // $monthTitle = date("Y", strtotime($bulan));

      $response = array(
        'status' => true,
        'datas' => $data,
        'tahun' => $tahun,
        'tglfrom' => $tglfrom,
        'tglto' => $tglto,
        'kategori' =>  $kate,
        'departemen' => $dep
      );

      return Response::json($response); 
    }

    public function detail_cpar(Request $request){
      $bulan = $request->get("bulan");
      $status = $request->get("status");
      $tglfrom = $request->get("tglfrom");
      $tglto = $request->get("status");
      $kategori = $request->get("kategori");
      $departemen = $request->get("departemen");

      $query = "select qc_cpars.*,monthname(tgl_permintaan) as bulan,departments.department_name, employees.name,statuses.status_name FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code where qc_cpars.deleted_at is null and monthname(tgl_permintaan) = '".$bulan."' and statuses.status_name ='".$status."' and DATE_FORMAT(tgl_permintaan,'%Y-%m') between '".$tglfrom."' and '".$tglto."'".$kategori." ".$departemen."";

      $detail = db::select($query);

      return DataTables::of($detail)

        ->editColumn('status_name',function($detail){
          if($detail->status_name == "Open") {
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Closed"){
            return '<label class="label label-danger">'.$detail->status_name. '</label>';
          }
          })

        ->addColumn('action', function($detail){
          $idcpar = $detail->id;
          return '
                  <a href="update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
                  <a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report</a>
          ';
        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action'])
        ->make(true);
    }

    public function detail_cpar_dept(Request $request){
      $departemen = $request->get("departemen");
      $status = $request->get("status");

      $query = "select qc_cpars.*,departments.department_name, employees.name,statuses.status_name FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code where qc_cpars.deleted_at is null and departments.department_name = '".$departemen."' and statuses.status_name ='".$status."'";

      $detail = db::select($query);

      return DataTables::of($detail)

        ->editColumn('status_name',function($detail){
          if($detail->status_name == "Open") {
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Closed"){
            return '<label class="label label-danger">'.$detail->status_name. '</label>';
          }
          })

        ->addColumn('action', function($detail){
          $idcpar = $detail->id;
          return '
                  <a href="update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
                  <a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report</a>
          ';
        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action'])
        ->make(true);
    }

    //cetak PDF

    public function print_cpar($id)
    {

      $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','staff.name as staffname','chief.name as chiefname','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->join('employees as staff','qc_cpars.staff','=','staff.employee_id')
        ->join('employees as chief','qc_cpars.chief','=','chief.employee_id')
        ->join('employees as manager','qc_cpars.manager','=','manager.employee_id')
        ->join('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
        ->join('employees as gm','qc_cpars.gm','=','gm.employee_id')
        // ->join('qc_cpars_items','qc_cpars.cpar_no','=')
        ->where('qc_cpars.id','=',$id)
        ->get();

      $parts = QcCparItem::select('qc_cpar_items.*','materials.material_description')
      ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
      ->join('materials','qc_cpar_items.part_item','=','materials.material_number')
      ->where('qc_cpars.id','=',$id)
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'potrait');
      $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
      $pdf->loadView('qc_report.print_cpar', array(
        'cpars'=>$cpars,
        'parts'=>$parts
      ));
      
      $cpar = str_replace("/"," ",$cpars[0]->cpar_no);
      // $pdf = PDF::loadview('qc_report.print_cpar',['cpars'=>$cpars,'parts'=>$parts]);
      return $pdf->stream("CPAR ".$cpar. ".pdf");
    }

     public function coba_print($id)
      {

       $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name')
          ->join('departments','qc_cpars.department_id','=','departments.id')
          ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
          ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
          // ->join('qc_cpars_items','qc_cpars.cpar_no','=')
          ->where('qc_cpars.id','=',$id)
          ->get();

        $parts = QcCparItem::select('qc_cpar_items.*')
        ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
        ->where('qc_cpars.id','=',$id)
        ->get();

        return view('qc_report.print_cpar', array(
            'cpars' => $cpars,
            'parts' => $parts
        ))->with('page', 'CPAR');

      }

      public function sign()
      {
          return view('qc_report.signature')->with('page', 'CPARS');
      }

      public function save_sign(Request $request)
      {
          $result = array();
          $imagedata = base64_decode( $request->get('img_data'));
          $filename = md5(date("dmYhisA"));
          //Location to where you want to created sign image
          $file_name = './images/'.$filename.'.png';
          file_put_contents($file_name,$imagedata);
          $result['status'] = 1;
          $result['file_name'] = $file_name;
          echo json_encode($result);

          $ttd = new QcTtdCoba([
            'ttd' => $result['file_name']
          ]);

          $ttd->save();
      }

      //------ Email ------

      public function sendemail(Request $request, $id,$posisi)
      {

          $id_user = Auth::id();

          $query = "select qc_cpars.*,departments.department_name,employees.name,statuses.status_name FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code where qc_cpars.id='".$id."'";
          $cpars = db::select($query);

          if ($posisi == "staff") {
              $to = "chief";
          } elseif ($posisi == "chief") {
              $to = "manager";
          } elseif ($posisi == "manager") {
              $to = "dgm";
          } elseif ($posisi == "dgm") {
              $to = "gm";
          } elseif ($posisi == "gm") {
              $to = "employee_id"; //manager departemen
          }

          $mailto = "select distinct employees.name,email from qc_cpars join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
            $mails = DB::select($mailto);

            foreach($mails as $mail){
              $mailtoo = $mail->email;
              // var_dump($mailtoo);die();
            }

          $qc_cpars = QcCpar::find($id);

          if($cpars != null){

          // var_dump($cpars);die();

              if ($qc_cpars->email_status == NULL && $qc_cpars->posisi == "staff") {
                $qc_cpars->email_status = "SentChief";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "chief";
                $qc_cpars->save();
                Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke Chief berhasil terkirim')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentChief" && $qc_cpars->posisi == "chief"){
                $qc_cpars->email_status = "SentManager";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "manager";
                $qc_cpars->save();
                Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentManager" && $qc_cpars->posisi == "manager"){
                $qc_cpars->email_status = "SentDGM";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "dgm";
                $qc_cpars->save();
                Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke DGM berhasil terkirim')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentDGM" && $qc_cpars->posisi == "dgm"){
                $qc_cpars->email_status = "SentGM";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "gm";
                $qc_cpars->save();
                Mail::to('yukitaka.hayakawa@music.yamaha.com')->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke GM berhasil terkirim')->with('page', 'CPAR');
              }
              else if($qc_cpars->email_status == "SentGM" && $qc_cpars->posisi == "gm"){
                $qc_cpars->email_status = "SentBagian";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "bagian";
                $qc_cpars->received_manager = "Received";
                
                $qc_cpars->save();

                $cars = new QcCar([
                  'cpar_no' => $qc_cpars->cpar_no,
                  'created_by' => $id_user
                ]);

                $cars->save();

                $query2 = "select qc_cpars.*,departments.department_name,employees.name,statuses.status_name, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no where qc_cpars.id='".$id."'";
                $cpars2 = db::select($query2);

                Mail::to($mailtoo)->send(new SendEmail($cpars2, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail has Been Sent To Department')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentChief" || $qc_cpars->email_status == "SentManager" || $qc_cpars->email_status == "SentDGM" || $qc_cpars->email_status == "SentGM" || $qc_cpars->email_status == "SentBagian"){
                return redirect('/index/qc_report')->with('error', 'Email pernah dikirim')->with('page', 'CPAR');
              }
          }

          else{
            return redirect('/index/qc_report')->with('error', 'Data tidak tersedia.')->with('page', 'CPAR');
          }
      }

      public function getmaterialsbymaterialsnumber(Request $request)
      {
          $html = array();
          $materials_number = Material::where('material_number',$request->materials_number)->get();
          foreach ($materials_number as $material) {
              $html = array(
                'material_description' => $material->material_description,
                'hpl' => $material->hpl,
              );

          }

          return json_encode($html);
      }


      //verifikasi

      public function statuscpar($id){
          $cpars = QcCpar::select('qc_cpars.*')
          ->where('qc_cpars.id',$id)
          ->get();

          return view('qc_report.status_cpar', array(
            'cpars' => $cpars
          ))->with('page', 'CPAR');
      }

      public function verifikasicpar($id){
          $cpar = QcCpar::find($id);

          if ($cpar->posisi == "chief") {
              $from = "staff";
          }
          else if ($cpar->posisi == "manager") {
              $from = "chief";
          }
          else if ($cpar->posisi == "dgm") {
              $from = "manager";
          }
          else if ($cpar->posisi == "gm") {
              $from = "dgm";
          }
          else {
              $from = "staff";
          }

          $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name','users.name as emplo')
          ->join('departments','qc_cpars.department_id','=','departments.id')
          ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
          ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
          ->join('users','qc_cpars.'.$from,'=','users.username')
          ->where('qc_cpars.id',$id)
          ->get();

          $parts = QcCparItem::select('qc_cpar_items.*','materials.material_description','materials.hpl')
          ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
          ->join('materials','qc_cpar_items.part_item','=','materials.material_number')
          ->where('qc_cpars.id','=',$id)
          ->get();

          return view('qc_report.verifikasi_cpar', array(
            'cpars' => $cpars,
            'parts' => $parts
          ))->with('page', 'CPAR');
      }

      public function checked(Request $request,$id)
      {
          $checked = $request->get('checked');
          // var_dump(count($checked));die();
          if(count($checked) == 13){
            $cpars = QcCpar::find($id);
            if ($cpars->posisi == "chief") {
              $cpars->checked_chief = "Checked";              
            }
            else if ($cpars->posisi == "manager") {
              $cpars->checked_manager = "Checked";              
            }
            else if ($cpars->posisi == "dgm") {
              $cpars->approved_dgm = "Checked";              
            }
            else if ($cpars->posisi == "gm") {
              $cpars->approved_gm = "Checked"; 
            }
            $cpars->save();
            return redirect('/index/qc_report/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');
          }
          else{
            return redirect('/index/qc_report/verifikasicpar/'.$id)->with('error', 'CPAR Not Approved')->with('page', 'CPAR');
          }
          
      }
}
