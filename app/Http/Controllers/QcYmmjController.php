<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\MaterialPlantDataList;
use Response;
use DataTables;
use Carbon\Carbon;
use App\QcYmmj;
use File;
use PDF;

class QcYmmjController extends Controller
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
         return view('qc_ymmj.index',  
          array('title' => 'Form Ketidaksesuaian YMMJ', 
                'title_jp' => 'YMMJ不適合報告',)
          )->with('page', 'Form Ketidaksesuaian YMMJ');
    }

    public function filter(Request $request)
    {
        $qc_ymmj = QcYmmj::select('qc_ymmjs.*')
        ->orderBy('tgl_kejadian','desc')
        ->whereNull('qc_ymmjs.deleted_at')
        ->get();

        return DataTables::of($qc_ymmj)

          ->editColumn('tgl_kejadian',function($qc_ymmj){
            return date('d F Y', strtotime($qc_ymmj->tgl_kejadian));
          })

          ->editColumn('detail',function($qc_ymmj){
            return $qc_ymmj->detail;
          })

          ->editColumn('presentase_ng',function($qc_ymmj){
            return $qc_ymmj->presentase_ng. ' %';
          })

          ->editColumn('qty_cek',function($qc_ymmj){
            return $qc_ymmj->qty_cek. ' Pcs';
          })

          ->editColumn('qty_ng',function($qc_ymmj){
            return $qc_ymmj->qty_ng. ' Pcs';
          })

          ->editColumn('file', function($qc_ymmj){
            $fl = "";
            if ($qc_ymmj->file != null) {
              $data = json_decode($qc_ymmj->file);
              for ($i = 0; $i < count($data); $i++) {
                $data[$i];
                $fl .= '<a href="../files/'.$data[$i].'" target="_blank" class="fa fa-paperclip"></a>';
              }
              
            }
            else{
               $fl = '-';
            }

            return $fl;
            
          })

          ->addColumn('action', function($qc_ymmj){
          $id = $qc_ymmj->id;

            return '<a href="qa_ymmj/update/'.$id.'" style="width: 50%; height: 100%;" class="btn btn-xs btn-warning form-control"><span><i class="glyphicon glyphicon-edit"></i></span></a>';
          })

        ->rawColumns(['detail' => 'detail','tgl_kejadian' => 'tgl_kejadian','action' => 'action','file' => 'file'])
        ->make(true);
    }


    public function create()
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $materials = MaterialPlantDataList::select('material_plant_data_lists.material_number','material_plant_data_lists.material_description')
        ->orderBy('material_plant_data_lists.id','ASC')
        ->get();

        return view('qc_ymmj.create', array(
            'materials' =>  $materials
        ))->with('page', 'Form Ketidaksesuaian YMMJ');
    }

    public function create_action(request $request)
    {
      try{
          $files=array();
          $file = new QcYmmj();
          if ($request->file('files') != NULL) {
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

          $id_user = Auth::id();
          $tgl_kejadian = $request->get('tgl_kejadian');
          $date_kejadian = str_replace('/', '-', $tgl_kejadian);

          $ymmj = new QcYmmj([
            'nomor' => $request->get('nomor'),
            'judul' => $request->get('judul_komplain'),
            'lokasi' => $request->get('lokasi'),
            'tgl_kejadian' => date("Y-m-d", strtotime($date_kejadian)),
            'tgl_form' => date("Y-m-d"),
            'material_number' => $request->get('material_number'),
            'material_description' => $request->get('material_description'),
            'no_invoice' => $request->get('no_invoice'),
            'qty_cek' => $request->get('sample_qty'),
            'qty_ng' => $request->get('defect_qty'),
            'presentase_ng' => $request->get('defect_presentase'),
            'detail' => $request->get('detail'),
            'penanganan' => $request->get('penanganan'),
            'file' => $file->filename,
            'created_by' => $id_user
          ]);

          $ymmj->save();

          return redirect('/index/qa_ymmj')
          ->with('status', 'New Form has been created.')
          ->with('page', 'Form Ketidaksesuaian YMMJ');
      }
      catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Form already exist.')->with('page', 'Form Ketidaksesuaian YMMJ');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Form Ketidaksesuaian YMMJ');
            }
        }
    }

    public function update($id)
    {

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $ymmj = QcYmmj::find($id);

        $materials = MaterialPlantDataList::select('material_plant_data_lists.material_number','material_plant_data_lists.material_description')
        ->orderBy('material_plant_data_lists.id','ASC')
        ->get();

        return view('qc_ymmj.edit', array(
            'ymmj' => $ymmj,
            'materials' =>  $materials
        ))->with('page', 'Form Ketidaksesuaian YMMJ');
    }

    public function update_action(Request $request, $id)
    {
          try{

            $ymmj = QcYmmj::find($id);

            $id_user = Auth::id();
            $tgl_kejadian = $request->get('tgl_kejadian');
            $date_kejadian = str_replace('/', '-', $tgl_kejadian);

            $files=array();
            
            // $file = new QcForm Ketidaksesuaian YMMJ();
            if ($request->file('files') != NULL) {
              if($files=$request->file('files')) {
                foreach($files as $file){
                  $nama=$file->getClientOriginalName();
                  $file->move('files',$nama);
                  $data[]=$nama;              
                }
              }

              $ymmj->file=json_encode($data);           
            }

            $ymmj->nomor = $request->get('nomor');
            $ymmj->judul = $request->get('judul');
            $ymmj->lokasi = $request->get('lokasi');
            $ymmj->tgl_kejadian = date('Y-m-d', strtotime($date_kejadian));
            $ymmj->material_number = $request->get('material_number');
            $ymmj->material_description = $request->get('material_description');
            $ymmj->no_invoice = $request->get('no_invoice');
            $ymmj->qty_cek = $request->get('sample_qty');
            $ymmj->qty_ng = $request->get('defect_qty');
            $ymmj->presentase_ng = $request->get('defect_presentase');
            $ymmj->detail = $request->get('detail');
            $ymmj->penanganan = $request->get('penanganan');
            

            $ymmj->save();
            return redirect('/index/qa_ymmj/update/'.$ymmj->id)->with('status', 'Data has been updated.')->with('page', 'Form Ketidaksesuaian YMMJ');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Form Ketidaksesuaian YMMJ already exist.')->with('page', 'Form Ketidaksesuaian YMMJ');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Form Ketidaksesuaian YMMJ');
            }
          }
    }

    public function deletefiles(Request $request)
      {
        try{
          $ymmj = QcYmmj::find($request->get('id'));
          $namafile = json_decode($ymmj->file);
          $namafilebarubaru = "";

          foreach ($namafile as $key) {
            if ($key == $request->get('nama_file')) {
              File::delete('files/'.$key);
            }
            else{
                if (count($key) > 0) {
                  $namafilebarubaru = $key;
                  $namafilebaru[] = $namafilebarubaru;
                }
            }
          }
          
          if ($namafilebarubaru != "") {
            $ymmj->file = json_encode($namafilebaru);
            $ymmj->save();
          }
          else{
            $ymmj->file = "";
            $ymmj->save();
          }

          $response = array(
            'status' => true,
            'message' => 'Berhasil Hapus Data'
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


      //grafik CPAR

      public function grafik_ymmj(){
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
        $bulan = db::select("select DISTINCT MONTH(tgl_kejadian) as bulan, MONTHNAME(tgl_kejadian) as namabulan FROM qc_ymmjs order by bulan asc;");
        $tahun = db::select("select DISTINCT YEAR(tgl_kejadian) as tahun FROM qc_ymmjs order by tahun desc");
        
         return view('qc_ymmj.grafik',  
          array('title' => 'Report YMMJ By Month', 
                'title_jp' => '',
                'fys' => $fys,
                'bulans' => $bulan,
                'years' => $tahun, 
              )
          )->with('page', 'YMMJ Graph');
      }

    public function fetchGrafik(Request $request)
    {
      $tahun = date('Y');
      $tglfrom = $request->get('tglfrom');
      $tglto = $request->get('tglto');

      if ($tglfrom == "") {
          $tglfrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
      }

      if ($tglto == "") {
          $tglto = date('Y-m', strtotime(carbon::now()));
      }

      $dateTitle = date("M Y", strtotime($tglto));

      $data = db::select("select count(nomor) as jumlah, monthname(tgl_kejadian) as bulan, year(tgl_kejadian) as tahun from qc_ymmjs where qc_ymmjs.deleted_at is null and DATE_FORMAT(tgl_kejadian,'%Y-%m') between '".$tglfrom."' and '".$tglto."' GROUP BY bulan,tahun order by tahun, month(tgl_kejadian) ASC");


      $response = array(
        'status' => true,
        'datas' => $data,
        'tahun' => $tahun,
        'tglfrom' => $tglfrom,
        'tglto' => $tglto,
        'date' => $dateTitle
      );

      return Response::json($response); 
    }

    public function fetchTable(Request $request)
    {
      $data = db::select("select nomor, tgl_kejadian, lokasi, judul, no_invoice, qty_cek, qty_ng, penanganan, file from qc_ymmjs order by tgl_kejadian desc ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }

    public function detail(Request $request){

      $bulan = $request->get("bulan");
      $tglfrom = $request->get("tglfrom");
      $tglto = $request->get("tglto");

      $query = "select * from qc_ymmjs where qc_ymmjs.deleted_at is null and monthname(tgl_kejadian) = '".$bulan."' and DATE_FORMAT(tgl_kejadian,'%Y-%m') between '".$tglfrom."' and '".$tglto."'";

      $detail = db::select($query);

      return DataTables::of($detail)
        ->addColumn('action', function($detail){
          $id = $detail->id;
          return '<a href="print/'.$id.'" class="btn btn-warning btn-md" target="_blank">Report YMMJ</a>';
        })

        ->editColumn('penanganan',function($detail){
          if($detail->penanganan == "Repair") {
            return '<label class="label label-warning">'.$detail->penanganan. '</label>';
          }
          else if($detail->penanganan == "Confirm stock" || $detail->penanganan == "Confirm Stock") {
            return '<label class="label label-success">'.$detail->penanganan. '</label>';
          }
          else if($detail->penanganan == "Return") {
            return '<label class="label label-info">'.$detail->penanganan. '</label>';
          }
          else if($detail->penanganan == "Replacement") {
            return '<label class="label label-primary">'.$detail->penanganan. '</label>';
          }
          else if($detail->penanganan == "Special Acceptance") {
            return '<label class="label label-primary">'.$detail->penanganan. '</label>';
          }
        })

        ->editColumn('file', function($qc_ymmj){
            $fl = "";
            if ($qc_ymmj->file != null) {
              $data = json_decode($qc_ymmj->file);
              for ($i = 0; $i < count($data); $i++) {
                $data[$i];
                $fl .= '<a href="../../files/'.$data[$i].'" target="_blank" class="fa fa-paperclip"></a>';
              }
            }
            else{
              $fl = '-';
            }
            return $fl;
          })

      ->rawColumns(['action' => 'action', 'penanganan' => 'penanganan','file' => 'file'])
      ->make(true);
    }

    public function print_ymmj($id)
    {
      $ymmj = QcYmmj::find($id);
      
      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('Legal', 'potrait');
      $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
      
      $pdf->loadView('qc_ymmj.print', array(
        'ymmj' => $ymmj,
      ));
      
      $nomor = str_replace("/"," ",$ymmj->nomor);
      return $pdf->stream("Form YMMJ ".$nomor. ".pdf");


      // return view('qc_ymmj.print',  
      //     array('title' => 'Report YMMJ By Month', 
      //           'title_jp' => '',
      //           'ymmj' => $ymmj,
      //         )
      //     )->with('page', 'YMMJ Graph');
    }
}
