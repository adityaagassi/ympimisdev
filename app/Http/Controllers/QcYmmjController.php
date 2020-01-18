<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\MaterialPlantDataList;
use Response;
use DataTables;
use Carbon\Carbon;
use App\QcYmmj;

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
                'title_jp' => '?????????????',)
          )->with('page', 'Form Ketidaksesuaian YMMJ');
    }

    public function filter(Request $request)
    {
        $qc_ymmj = QcYmmj::select('qc_ymmjs.*')
        ->whereNull('qc_ymmjs.deleted_at')
        ->get();

        return DataTables::of($qc_ymmj)

          ->editColumn('tgl_kejadian',function($qc_ymmj){
            return date('d F Y', strtotime($qc_ymmj->tgl_kejadian));
          })

          ->editColumn('detail',function($qc_ymmj){
            return $qc_ymmj->detail;
          })

        ->rawColumns(['detail' => 'detail','tgl_kejadian' => 'tgl_kejadian'])
        ->make(true);
    }


    public function create()
    {
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


}
