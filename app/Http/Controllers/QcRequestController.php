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
use App\QcRequest;
use App\QcRequestItem;
use App\MaterialPlantDataList;
use Illuminate\Support\Facades\Mail;

class QcRequestController extends Controller
{

	public function __construct()
    {
      $this->middleware('auth');

      $this->sec_from = [
       'Sub Assy Saxophone',
       'CL Body',
       'Case',
       'Buffing',
       'Soldering',
       'Handatsuke',
       'Plating',
       'Lacquering',
       'Pianica',
       'Recorder',
       'Venova',
       'Reed Plate',
       'B-Pro',
       'C-Pro',
     ];

     $this->sec_to = [
       'Sanding',
       'Soldering',
       'Handatsuke',
       'Buffing',
       'Plating',
       'Lacquering',
       'Purchasing',
       'Assy / Sub Assy',
       'CL Body',
       'Warehouse',
       'Quality Assurance',
       'Case',
       'Injection Recorder',
       'Assy / Sub Assy Recorder',
       'Pianica Final',
       'Pianica Initial',
       'Reed Plate',
       'Warehouse',
       'Quality Control',
       'Purchasing',
       'Mouthpiece',
     ];
    }
    
      //Form Request ke QA

	public function index()
	{
	    return view('qc_request.index', array(
	        'sec_from' => $this->sec_from,
	        'sec_to' => $this->sec_to
	    ))->with('page', 'Request CPAR');
	}

	public function create()
	{
		return view('qc_request.create', array(
            'sec_from' => $this->sec_from,
	        'sec_to' => $this->sec_to
        ))->with('page', 'Request CPAR');
	}

	public function create_action(request $request)
	{

		 try{
          $id_user = Auth::id();
          
          $requestqa = new QcRequest([
            'tanggal' => $request->get('tgl'),
            'subject' => $request->get('subject'),
            'judul' => $request->get('judul'),
            'section_from' => $request->get('section_from'),
            'section_to' => $request->get('section_to'),
            'created_by' => $id_user
          ]);

          $requestqa->save();

          return redirect('/index/request_qa/detail/'.$requestqa->id)
          ->with('status', 'New Complain has been created.')
          ->with('page', 'Request CPAR');
      }
      catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Complain already exist.')->with('page', 'Request CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Request CPAR');
            }
        }
	 }

   public function update_action(request $request, $id)
    {
      try{
        $req = QcRequest::find($id);
        $req->judul = $request->get('judul');

        $req->save();
        return redirect('/index/request_qa/detail/'.$req->id)->with('status', 'Form has been updated.')->with('page', 'Request CPAR');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Request CPAR already exist.')->with('page', 'Request CPAR');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Request CPAR');
        }
      }
    }


	 public function detail($id)
    {

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $requestqa = QcRequest::find($id);
        
        $materials = MaterialPlantDataList::select('material_plant_data_lists.id','material_plant_data_lists.material_number','material_plant_data_lists.material_description')
        ->orderBy('material_plant_data_lists.id','ASC')
        ->get();

        return view('qc_request.Detail', array(
          'qa' => $requestqa,
          'materials' =>  $materials,
          'sec_from' => $this->sec_from,
	        'sec_to' => $this->sec_to
        ))->with('page', 'Request CPAR');
    }

    public function fetchDataTable(Request $request)
    {
      $tanggal = $request->get("tanggal");
      $section_from = $request->get("section_from");
      $section_to = $request->get("sec_to");

      if ($tanggal == null) {
        if ($section_from == null) {
          if ($section_to == null) {
            $tgl = '';
            $secfrom = '';
            $secto = '';
          }
          else{
            $tgl = '';
            $secfrom = '';
            $secto = "where section_to = '".$section_to."'";
          }
        }
        else{
          if ($section_to == null) {
            $tgl = '';
            $secfrom = "where section_from = '".$section_from."'";
            $secto = "";
          }
          else{
            $tgl = '';
            $secfrom = "where section_from = '".$section_from."'";
            $secto = "and section_to = '".$section_to."'";
          }
        }
      }
      else{
        if ($section_from == null) {
          if ($section_to == null) {
            $tgl = "where tanggal = '".$tanggal."'";
            $secfrom = '';
            $secto = '';
          }
          else{
            $tgl = "where tanggal = '".$tanggal."'";
            $secfrom = '';
            $secto = "and section_to = '".$section_to."'";
          }
        }
        else{
          if ($section_to == null) {
            $tgl = "where tanggal = '".$tanggal."'";
            $secfrom = "and section_from = '".$section_from."'";
            $secto = '';
          }
          else{
            $tgl = "where tanggal = '".$tanggal."'";
            $secfrom = "and section_from = '".$section_from."'";
            $secto = "and section_to = '".$section_to."'";
          }
        }
      }

      $query = "SELECT * FROM qc_requests ".$tgl." ".$secfrom." ".$secto." ";

      $detail = db::select($query);

      $response = array(
        'status' => true,
        'lists' => $detail,
      );
      return Response::json($response);
    }


    public function fetch_item($id)
    {
        $request = QcRequest::find($id);

        $request_item = QcRequestItem::leftJoin("qc_requests","qc_request_items.id_request","=","qc_requests.id")
        ->select('qc_request_items.*')
        ->where('qc_request_items.id_request','=',$request->id)
        ->get();

        return DataTables::of($request_item)

          ->editColumn('detail', function($request_item){
            return $request_item->detail;
          })

          ->editColumn('presentase_ng', function($request_item){
            return $request_item->presentase_ng. ' %';
          })
          
          ->addColumn('action', function($request_item){
            return '
            
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$request_item->id.')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$request_item->id.')">Delete</button>';
          })

      ->rawColumns(['detail' => 'detail', 'presentase_ng' => 'presentase_ng', 'action' => 'action'])
      ->make(true);
    }

    public function create_item(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $items = new QcRequestItem([
                'id_request' => $request->get('id_request'),
                'item' => $request->get('item'),
                'item_desc' => $request->get('item_desc'),
                'supplier' => $request->get('supplier'),
                'detail' => $request->get('detail'),
                'jml_cek' => $request->get('jml_cek'),
                'jml_ng' => $request->get('jml_ng'),
                'presentase_ng' => $request->get('presentase_ng'),
                'created_by' => $id_user
            ]);

            $items->save();

            $response = array(
              'status' => true,
              'items' => $items
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'items' => "Item already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'items' => "Item not created."
            );
             return Response::json($response);
           }
        }
    }

    public function fetch_item_edit(Request $request)
    {
      $items = QcRequestItem::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $items,
      );
      return Response::json($response);
    }

    public function edit_item(Request $request)
    {
        try{
            $items = QcRequestItem::find($request->get("id"));
            $items->item = $request->get('item');
            $items->item_desc = $request->get('item_desc');
            $items->supplier = $request->get('supplier');
            $items->detail = $request->get('detail');
            $items->jml_cek = $request->get('jml_cek');
            $items->jml_ng = $request->get('jml_ng');
            $items->presentase_ng = $request->get('presentase_ng');
            $items->save();

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
              'datas' => "Item already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update Item Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function update_detail(Request $request, $id)
    {
      try{
        $req = QcRequest::find($id);
        $req->target = $request->get('target');
        $req->jumlah = $request->get('jumlah');
        $req->waktu = $request->get('waktu');
        $req->aksi = $request->get('aksi');
        $req->save();
        return redirect('/index/request_qa/detail/'.$req->id)->with('status', 'Data has been updated.')->with('page', 'Request CPAR');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Request CPAR already exist.')->with('page', 'Request CPAR');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Request CPAR');
        }
      }
    }

    public function delete_item(Request $request)
    {
      $request_item = QcRequestItem::find($request->get("id"));
      $request_item->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }



    public function print_report($id)
    {
        $qa = QcRequest::select('qc_requests.*')
        ->where('qc_requests.id','=',$id)
        ->get();

        $items = QcRequestItem::select('qc_request_items.*')
        ->join('qc_requests','qc_requests.id','=','qc_request_items.id_request')
        ->where('qc_requests.id','=',$id)
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('Legal', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        
        $pdf->loadView('qc_request.print', array(
          'qa' => $qa,
          'items' => $items
        ));
        
        return $pdf->stream("Form Ketidaksesuaian.pdf");
      }

      public function verifikasi($id){
          $reqid = QcRequest::find($id);

          $req = QcRequest::select('qc_requests.*')
          ->where('qc_requests.id','=',$id)
          ->get();

          $items = QcRequestItem::select('qc_request_items.*')
          ->join('qc_requests','qc_requests.id','=','qc_request_items.id_request')
          ->where('qc_requests.id','=',$id)
          ->get();

          return view('qc_request.verifikasi', array(
            'reqid' => $reqid,
            'req' => $req,
            'items' => $items
          ))->with('page', 'CPAR');
      }

      public function approval(Request $request,$id)
      {


          $approve = $request->get('approve');

          if(count($approve) == 3){
            $req = QcRequest::find($id);
            $req->approval = "approved";              
            $req->save();
            return redirect('/index/request_qa/verifikasi/'.$id)->with('status', 'Request CPAR Approved')->with('page', 'Request CPAR');
          }
          else{
            return redirect('/index/request_qa/verifikasi/'.$id)->with('error', 'Request CPAR Not Approved')->with('page', 'Request CPAR');
          }          
      }

}
