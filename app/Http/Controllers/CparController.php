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
use App\CparDepartment;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\MaterialPlantDataList;
use App\CparItem;

class CparController extends Controller
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
    }

    // index Data table

    public function index()
    {
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      return view('cpar.index', array(
       'emp' => $emp_id,
       'employee' => $employee
     ))->with('page', 'CPAR Antar Departemen');
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

      $query = "SELECT * FROM cpar_departments ".$tgl." ".$secfrom." ".$secto." order by id desc";

      $detail = db::select($query);

      $response = array(
        'status' => true,
        'lists' => $detail,
      );
      return Response::json($response);
    }



    // Buat CPAR

    public function create()
    {
      $secfrom = db::select("select DISTINCT department, section, `group` from employee_syncs
        where employee_id = '".Auth::user()->username."'
        ");

      $sections = db::select("select DISTINCT department, section, `group` from employee_syncs
        where department is not null
        and section is not null
        and grade_code not like '%L%'
        order by department, section, `group` asc");

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

      return view('cpar.create', array(
        'secfrom' => $secfrom,
        'sections' => $sections,
        'employee' => $emp
      ))->with('page', 'CPAR Antar Departemen');
    }

    public function post_create(request $request)
    {

     try{
      $id_user = Auth::id();

      //get chief foreman manager from secfrom

      //$get section
      $sec = explode("_", $request->get('secfrom'));

      //get departemen
      $dept = EmployeeSync::where('section','=', $sec[1])
      ->select('department')->first();

      //get chief foreman manager from departemen

      $cfm = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position in ('chief','foreman','manager')");

      // Jika ada Chief Foreman Manager

      $chief = null;
      $foreman = null;
      $manager = null;
      $chiefcount = 0;
      $foremancount = 0;

      if ($cfm != null) {
        foreach ($cfm as $position) {

          $pos = $position->position;

          // Manager 
          if ($pos == "Manager") {
            $manager = $position->employee_id;
          }

          // Chief
          if ($pos == "Chief") {
            if ($chiefcount == 0) {
              $chief = $position->employee_id;
              $chiefcount = 1;
            }
            else{
              $cf = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Chief' and section='".$sec[1]."'");

              $chief = $cf[0]->employee_id;
            }
          }

          // Foreman
          if ($pos == "Foreman") {
            if ($foremancount == 0) {
              $foreman = $position->employee_id;
              $foremancount = 1;
            } else {
              $f = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Foreman' and section='".$sec[1]."'");

              $foreman = $f[0]->employee_id;
            }

          }

        }
      }

      if (strpos($request->get('position'), 'Staff') !== false) {        
        $gr = "Staff";
      }
      else if (strpos($request->get('position'), 'Leader') !== false) {        
        $gr = "Leader";
      }
      else {
        $gr = "Staff";
      }

      $cpar = new CparDepartment([
        'tanggal' => $request->get('tanggal'),
        'kategori' => $request->get('kategori'),
        'judul' => $request->get('judul'),
        'section_from' => $request->get('secfrom'),
        'section_to' => $request->get('secto'),
        'pelapor' => $request->get('employee_id'),
        'grade' => $gr,
        'chief' => $chief,
        'foreman' => $foreman,
        'manager' => $manager,
        'posisi' => 'sl',
        'created_by' => $id_user
      ]);

      $cpar->save();

      $response = array(
       'status' => true,
       'datas' => $cpar->id
     );
      return Response::json($response);
    }
    catch (QueryException $e){
      $response = array(
       'status' => false,
       'datas' => $e->getMessage()
     );
      return Response::json($response);
    }
  }

  public function detail($id)
  {
    $emp_id = Auth::user()->username;
    $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

    $cpar = CparDepartment::find($id);

    $emp = EmployeeSync::where('employee_id', $cpar->pelapor)
    ->select('employee_id', 'name')->first();

    $sections = db::select("select DISTINCT department, section, `group` from employee_syncs
      where department is not null
      and section is not null
      and grade_code not like '%L%'
      order by department, section, `group` asc");

    $materials = MaterialPlantDataList::select('material_plant_data_lists.id','material_plant_data_lists.material_number','material_plant_data_lists.material_description')
    ->orderBy('material_plant_data_lists.id','ASC')
    ->get();

    return view('cpar.detail', array(
      'cpar' => $cpar,
      'emp' => $emp,
      'sections' => $sections,
      'materials' => $materials
    ))->with('page', 'CPAR Antar Departemen');
  }

      //GMC

  public function fetch_item($id)
  {
    $cpar = CparDepartment::find($id);

    $cpar_item = CparItem::leftJoin("cpar_departments","cpar_items.id_cpar","=","cpar_departments.id")
    ->select('cpar_items.*')
    ->where('cpar_items.id_cpar','=',$cpar->id)
    ->get();

    return DataTables::of($cpar_item)

    ->editColumn('detail', function($cpar_item){
      return $cpar_item->detail;
    })

    ->editColumn('jml_cek', function($cpar_item){
      return $cpar_item->jml_cek. ' Pcs';
    })

    ->editColumn('jml_ng', function($cpar_item){
      return $cpar_item->jml_ng. ' Pcs';
    })

    ->editColumn('presentase_ng', function($cpar_item){
      return $cpar_item->presentase_ng. ' %';
    })

    ->addColumn('action', function($cpar_item){
      return '
      <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$cpar_item->id.')">Edit</button>
      <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$cpar_item->id.')">Delete</button>';
    })

    ->rawColumns(['detail' => 'detail', 'presentase_ng' => 'presentase_ng', 'action' => 'action'])
    ->make(true);
  }


  /* ---------------------- Add Update Delete Item */

  public function create_item(Request $request)
  {
    try
    {
      $id_user = Auth::id();

      $items = new CparItem([
        'id_cpar' => $request->get('id_cpar'),
        'item' => $request->get('item'),
        'item_desc' => $request->get('item_desc'),
                  // 'supplier' => $request->get('supplier'),
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
    $items = CparItem::find($request->get("id"));

    $response = array(
      'status' => true,
      'datas' => $items,
    );
    return Response::json($response);
  }

  public function edit_item(Request $request)
  {
    try{

      $items = CparItem::find($request->get("id"));
      $items->item = $request->get('item');
      $items->item_desc = $request->get('item_desc');
      // $items->supplier = $request->get('supplier');
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

  public function delete_item(Request $request)
  {
    $request_item = CparItem::find($request->get("id"));
    $request_item->forceDelete();

    $response = array(
      'status' => true
    );
    return Response::json($response);
  }

  /* Add Update Delete Item ------------------------------ */

  public function update_detail(Request $request, $id)
  {
    try{
      $req = CparDepartment::find($id);
      $req->target = $request->get('target');
      $req->jumlah = $request->get('jumlah');
      $req->waktu = $request->get('waktu');
      $req->aksi = $request->get('aksi');
      $req->save();
      return redirect('/index/cpar/detail/'.$req->id)->with('status', 'Data has been updated.')->with('page', 'CPAR Antar Departemen');
    }
    catch (QueryException $e){
      $error_code = $e->errorInfo[1];
      if($error_code == 1062){
        return back()->with('error', 'Already exist.')->with('page', 'Request CPAR');
      }
      else{
        return back()->with('error', $e->getMessage())->with('page', 'Request CPAR');
      }
    }
  }

      // Print PDF

  public function print_report($id)
  {
    $cpar = CparDepartment::find($id);

    if($cpar->grade == "Staff"){
      
      //apakah chiefnya ada
      if ($cpar->chief != null) {
        $pos = "chief";
      }

      // apakah foremannya ada
      else if($cpar->foreman != null) {
        $pos = "foreman";
      }

      // apakah manager ada
      else{
        $pos = "manager";
      }

    }
    else if($cpar->grade == "Leader"){
      //foreman ada
      if($cpar->foreman != null) {
        $pos = "foreman";
      }

      else if ($cpar->chief != null) {
        $pos = "chief";
      }

      else{
        $pos = "manager";
      }

    }

    $qa = CparDepartment::select('cpar_departments.*','sl.name as slname','cf.name as cfname','m.name as mname')
    ->leftjoin('employee_syncs as sl','cpar_departments.pelapor','=','sl.employee_id')
    ->leftjoin('employee_syncs as cf','cpar_departments.'.$pos,'=','cf.employee_id')
    ->leftjoin('employee_syncs as m','cpar_departments.manager','=','m.employee_id')
    ->where('cpar_departments.id','=',$id)
    ->get();

    $items = CparItem::select('cpar_items.*')
    ->join('cpar_departments','cpar_departments.id','=','cpar_items.id_cpar')
    ->where('cpar_departments.id','=',$id)
    ->get();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');
    $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    $pdf->loadView('cpar.print', array(
      'qa' => $qa,
      'items' => $items,
      'sl' => $qa[0]->slname,
      'cf' => $qa[0]->cfname,
      'm' => $qa[0]->mname,
    ));

    return $pdf->stream("Form ".$qa[0]->judul.".pdf");
  }

  // Verifikasi CPAR

  public function verifikasicpar($id){
    $cpar = CparDepartment::find($id);

    if($cpar->grade == "Staff"){
      
      //apakah chiefnya ada
      if ($cpar->chief != null) {
        $pos = "chief";
      }

      // apakah foremannya ada
      else if($cpar->foreman != null) {
        $pos = "foreman";
      }

      // apakah manager ada
      else{
        $pos = "manager";
      }

    }
    else if($cpar->grade == "Leader"){
      //foreman ada
      if($cpar->foreman != null) {
        $pos = "foreman";
      }

      else if ($cpar->chief != null) {
        $pos = "chief";
      }

      else{
        $pos = "manager";
      }

    }

    $qa = CparDepartment::select('cpar_departments.*','sl.name as slname','cf.name as cfname','m.name as mname')
    ->leftjoin('employee_syncs as sl','cpar_departments.pelapor','=','sl.employee_id')
    ->leftjoin('employee_syncs as cf','cpar_departments.'.$pos,'=','cf.employee_id')
    ->leftjoin('employee_syncs as m','cpar_departments.manager','=','m.employee_id')
    ->where('cpar_departments.id','=',$id)
    ->get();

    $items = CparItem::select('cpar_items.*')
    ->join('cpar_departments','cpar_items.id_cpar','=','cpar_departments.id')
        // ->join('material_plant_data_lists','cpar_items.item','=','material_plant_data_lists.material_number')
    ->where('cpar_departments.id','=',$id)
    ->get();

    return view('cpar.verifikasi_cpar', array(
      'cpar' => $cpar,
      'items' => $items,
      'sl' => $qa[0]->slname,
      'cf' => $qa[0]->cfname,
      'm' => $qa[0]->mname,
    ))->with('page', 'CPAR Departemen');
  }


  public function approval(Request $request,$id)
  {
    $approve = $request->get('approve');

    if(count($approve) == 3){
      $cpar = CparDepartment::find($id);

      // if ($cpar->posisi == "sl") {

      //   if ($cpar->chief == null && $cpar->foreman == null) {
      //       $cpar->posisi = "m";
      //       $cpar->approvalcf = "Approved";
      //       $cpar->approvalm = "Approved";
      //       $cpar->datem = date('Y-m-d H:i:s');
      //   }
      //   else{
      //       $cpar->posisi = "cf";
      //       $cpar->approvalcf = "Approved";
      //       $cpar->datecf = date('Y-m-d H:i:s');
      //   }


      //   if($cpar->grade == "Staff"){
      
      //     //apakah chiefnya ada
      //     if ($cpar->chief != null) {
      //       $pos = "chief";
      //     }

      //     // apakah foremannya ada
      //     else if($cpar->foreman != null) {
      //       $pos = "foreman";
      //     }

      //     // apakah manager ada
      //     else{
      //       $pos = "manager";
      //     }

      //   }
        
      //   else if($cpar->grade == "Leader"){
      //     //foreman ada
      //     if($cpar->foreman != null) {
      //       $pos = "foreman";
      //     }

      //     else if ($cpar->chief != null) {
      //       $pos = "chief";
      //     }

      //     else{
      //       $pos = "manager";
      //     }

      //   }

      //   $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.".$pos." = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
      //   $mails = DB::select($mailto);
      // }

      if ($cpar->posisi == "cf") {
        $cpar->posisi = "m";
        $cpar->approvalcf = "Approved";
        $cpar->datecf = date('Y-m-d H:i:s');

        $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.manager = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
        $mails = DB::select($mailto);

        foreach($mails as $mail){
          $mailtoo = $mail->email;
          $number = $mail->phone;
        }
      }
      else if ($cpar->posisi == "m") {
        $cpar->approvalm = "Approved";
        $cpar->datem = date('Y-m-d H:i:s');

        $mailtoo = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.manager = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
        $mails = DB::select($mailto);
      }

      $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;

      $cpar_dept = db::select($isimail);

      Mail::to($mailtoo)->send(new SendEmail($cpar_dept, 'cpar_dept'));

      $cpar->save();

      return redirect('/index/cpar/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');
    }
    else{
      return redirect('/index/cpar/verifikasicpar/'.$id)->with('error', 'CPAR Not Approved')->with('page', 'CPAR');
    }          
  }


  public function sendemail(Request $request,$id)
      {
          $id_user = Auth::id();
          $cpar = CparDepartment::find($id);

          if ($cpar->posisi == "sl") {
            if ($cpar->chief == null && $cpar->foreman == null) {
                $cpar->posisi = "m";
                $cpar->approvalcf = "Approved";
                $cpar->datecf = date('Y-m-d H:i:s');
                // $cpar->approvalm = "Approved";
            }
            else{
                $cpar->posisi = "cf";
            }

            if($cpar->grade == "Staff"){
          
              //apakah chiefnya ada
              if ($cpar->chief != null) {
                $pos = "chief";
              }
              // apakah foremannya ada
              else if($cpar->foreman != null) {
                $pos = "foreman";
              }
              // apakah manager ada
              else{
                $pos = "manager";
              }
            }
            
            else if($cpar->grade == "Leader"){
              //foreman ada
              if($cpar->foreman != null) {
                $pos = "foreman";
              }
              else if ($cpar->chief != null) {
                $pos = "chief";
              }
              else{
                $pos = "manager";
              }

            }

            $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.".$pos." = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
            $mails = DB::select($mailto);
          }

          foreach($mails as $mail){
            $mailtoo = $mail->email;
            $number = $mail->phone;
          }

          $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;
          $cpar_dept = db::select($isimail);

          Mail::to($mailtoo)->send(new SendEmail($cpar_dept, 'cpar_dept'));
          $cpar->save();      
    }


    // CPAR Reject

    public function notapprove(Request $request,$id)
      {
          $alasan = $request->get('alasan');

          $cpar = CparDepartment::find($id);
          
          $cpar->alasan = $alasan;
          $cpar->datereject = date('Y-m-d H:i:s');

          if ($cpar->posisi == "cf") {
            $cpar->posisi = "sl";              
          }
          else if ($cpar->posisi == "m") {
            $cpar->posisi = "sl";
            $cpar->approvalcf = null;
            $cpar->datecf = null;
          }

          $cpar->save();

          $query = "select * from cpar_departments where cpar_departments.id='".$id."'";
          $tolak = db::select($query);

          $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.pelapor = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
            $number = $mail->phone;
          }

          Mail::to($mailtoo)->send(new SendEmail($tolak, 'rejectcpar_dept'));
          return redirect('/index/cpar/verifikasicpar/'.$id)->with('status', 'CPAR Not Approved')->with('page', 'CPAR Antar Departemen');
      }

}
