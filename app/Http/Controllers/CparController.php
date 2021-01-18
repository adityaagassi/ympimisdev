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
use App\MaterialQaComplaint;
use App\CparItem;
use App\StandarisasiAudit;
use App\StandarisasiAuditChecklist;
use App\StandarisasiAuditIso;
use App\AuditAll;

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
     ))->with('page', 'Form Laporan Ketidaksesuaian');
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

      $query = "SELECT * FROM cpar_departments ".$tgl." ".$secfrom." ".$secto." where deleted_at is null order by id desc";

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
      // $secfrom = db::select("select DISTINCT department, section, `group` from employee_syncs
      //   where employee_id = '".Auth::user()->username."'");
      $secfrom = db::select("select DISTINCT department, section,`group` from employee_syncs where section = (SELECT section FROM employee_syncs where employee_id = '".Auth::user()->username."')");

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
      ))->with('page', 'Form Laporan Ketidaksesuaian');
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

      if ($dept->department == 'Educational Instrument (EI) Department') {
  
        $cfm = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Educational Instrument (EI) Department' and employee_id in ('PI1110001','PI9906002')");
  
      }else{
        $cfm = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position in ('chief','foreman','manager')");

      }



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

              if ($dept->department == "Woodwind Instrument - Surface Treatment (WI-ST) Department") {
                
                if ($sec[1] == "Body Buffing-Barrel Process Section") {
                  $sec[1] == "Buffing Key Process Section";
                }

                $f = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Foreman' and section='".$sec[1]."'");
              }
              else{
                $f = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Foreman'");
              }



              $foreman = $f[0]->employee_id;
            }
          }

          if ($pos == "Senior Staff") {
            $foreman = $position->employee_id;
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
        'judul' => ucwords($request->get('judul')),
        'section_from' => $request->get('secfrom'),
        'section_to' => $request->get('secto'),
        'pelapor' => $request->get('employee_id'),
        'grade' => $gr,
        'chief' => $chief,
        'foreman' => $foreman,
        'manager' => $manager,
        'posisi' => 'sl',
        'status' => 'cpar',
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

    $cpar_item = CparDepartment::join('cpar_items', 'cpar_departments.id', '=', 'cpar_items.id_cpar')->where('cpar_departments.id', '=', $id)
    ->get();

    $sections = db::select("select DISTINCT department, section, `group` from employee_syncs
      where department is not null
      and section is not null
      and grade_code not like '%L%'
      order by department, section, `group` asc");

    $materials = MaterialQaComplaint::select('material_qa_complaints.id','material_qa_complaints.material_number','material_qa_complaints.material_description')
    ->orderBy('material_qa_complaints.id','ASC')
    ->get();

    return view('cpar.detail', array(
      'cpar' => $cpar,
      'emp' => $emp,
      'sections' => $sections,
      'materials' => $materials,
      'cpar_item' => $cpar_item
    ))->with('page', 'Form Laporan Ketidaksesuaian');
  }

  //Delete

  public function delete_form($id)
    {
        $cpar = CparDepartment::find($id);
        $cpar->delete();

        return redirect('/index/form_ketidaksesuaian')
        ->with('status', 'Form has been deleted.')
        ->with('page', 'Form Laporan Ketidaksesuaian');
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
      return redirect('/index/form_ketidaksesuaian/detail/'.$req->id)->with('status', 'Data has been updated.')->with('page', 'Form Laporan Ketidaksesuaian');
    }
    catch (QueryException $e){
      $error_code = $e->errorInfo[1];
      if($error_code == 1062){
        return back()->with('error', 'Already exist.')->with('page', 'Form Ketidaksesuaian');
      }
      else{
        return back()->with('error', $e->getMessage())->with('page', 'Form Ketidaksesuaian');
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

      if ($cpar->chief_car != null) {
        $pos2 = "chief_car";
      }

      // apakah foremannya ada
      else if($cpar->foreman_car != null) {
        $pos2 = "foreman_car";
      }

      // apakah manager ada
      else{
        $pos2 = "manager_car";
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

      //foreman ada
      if($cpar->foreman_car != null) {
        $pos2 = "foreman_car";
      }

      else if ($cpar->chief_car != null) {
        $pos2 = "chief_car";
      }

      else{
        $pos2 = "manager_car";
      }
    }

    $qa = CparDepartment::select('cpar_departments.*','sl.name as slname','cf.name as cfname','m.name as mname','pic.name as picname','cfcar.name as cfcarname','mcar.name as mcarname')
    ->leftjoin('employee_syncs as sl','cpar_departments.pelapor','=','sl.employee_id')
    ->leftjoin('employee_syncs as cf','cpar_departments.'.$pos,'=','cf.employee_id')
    ->leftjoin('employee_syncs as m','cpar_departments.manager','=','m.employee_id')
    ->leftjoin('employee_syncs as pic','cpar_departments.pic_car','=','pic.employee_id')
    ->leftjoin('employee_syncs as cfcar','cpar_departments.'.$pos2,'=','cfcar.employee_id')
    ->leftjoin('employee_syncs as mcar','cpar_departments.manager_car','=','mcar.employee_id')
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
      'pic' => $qa[0]->picname,
      'cfcar' => $qa[0]->cfcarname,
      'mcar' => $qa[0]->mcarname,
      'cpar' => $cpar
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
        // ->join('material_qa_complaints','cpar_items.item','=','material_qa_complaints.material_number')
    ->where('cpar_departments.id','=',$id)
    ->get();

    if ($cpar->posisi == 'sl' || $cpar->posisi == 'cf' || $cpar->posisi == 'm' || $cpar->posisi == 'dept') {
      return view('cpar.verifikasi_cpar', array(
        'cpar' => $cpar,
        'items' => $items,
        'sl' => $qa[0]->slname,
        'cf' => $qa[0]->cfname,
        'm' => $qa[0]->mname,
      ))->with('page', 'Form Ketidaksesuaian');
    }
    else{
      return redirect('index/form_ketidaksesuaian');
    }

    
  }


  public function approval(Request $request,$id)
  {
    $approve = $request->get('approve');

    if(count($approve) == 3){
      $cpar = CparDepartment::find($id);

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

      else if ($cpar->posisi == "m" ) {

        $cpar->approvalm = "Approved";
        $cpar->datem = date('Y-m-d H:i:s');

        // if ($cpar->kategori == "Kualitas_Spec" || $cpar->kategori == "Kualitas_Part" || $cpar->kategori == "Kualitas_Fungsi" || $cpar->kategori == "Kualitas_Luka" || $cpar->kategori == "Kualitas_Recheck") {
          
        //   $cpar->posisi = "qa";
          
        //   $cpar->save();

        //   $getchief = "SELECT employee_id, email FROM `employee_syncs` join users on employee_syncs.employee_id = users.username where section = 'QA Process Control' and position = 'Chief'";

        //   // $getchief = "SELECT employee_id, email FROM `employee_syncs` join users on employee_syncs.employee_id = users.username where employee_id='PI1108002'";

        //   $chief = DB::select($getchief);
          
        //   if ($chief != null) {
        //     foreach ($chief as $cf) {
        //       $mailtoo = $cf->email;
        //     }             
        //   }

        //   $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;
        //   $cpar_dept = db::select($isimail);

        //   // Mail::to($cfh)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpar_dept, 'cpar_dept'));
        // }
        // else{


        $cpar->status = "car";

        $sec = explode("_", $cpar->section_to);
        $secfrom = explode("_", $cpar->section_from);
        //get departemen
        $dept = EmployeeSync::where('section','=', $sec[1])
        ->select('department')->first();

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = '".$dept->department."' and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%') and (section is null or section != '".$secfrom[1]."')";

        $mailtoo = DB::select($mails);

        $cpar->tanggal_car = date('Y-m-d H:i:s');

        //get chief foreman manager from departemen TUJUAN

        $cfm2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position in ('chief','foreman','manager')");


        $chief2 = null;
        $foreman2 = null;
        $manager2 = null;
        $chiefcount2 = 0;
        $foremancount2 = 0;

        if ($cfm2 != null) {
          foreach ($cfm2 as $position) {

            $pos = $position->position;

            // Manager 
            if ($pos == "Manager") {
              $manager2 = $position->employee_id;
              $cpar->manager_car = $manager2;
            }

            // Chief
            if ($pos == "Chief") {
              if ($chiefcount2 == 0) {
                $chief2 = $position->employee_id;
                $chiefcount2 = 1;

                $cpar->chief_car = $chief2;
              }
              else{
                $cf2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Chief' and section='".$sec[1]."'");

                $chief2 = $cf2[0]->employee_id;

                $cpar->chief_car = $chief2;
              }
            }

            // Foreman
            if ($pos == "Foreman") {
              if ($foremancount2 == 0) {
                $foreman2 = $position->employee_id;
                $foremancount2 = 1;

                $cpar->foreman_car = $foreman2;
              } else {
                $f2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Foreman' and section='".$sec[1]."'");

                $foreman2 = $f2[0]->employee_id;
                $cpar->foreman_car = $foreman2;
              }

            }

          }
        }

        $cpar->posisi = 'dept';

      }

      $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;

      $cpar_dept = db::select($isimail);

      Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpar_dept, 'cpar_dept'));

      $cpar->save();
      return redirect('/index/form_ketidaksesuaian/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'Form Ketidaksesuian');
    }
    else{
      return redirect('/index/form_ketidaksesuaian/verifikasicpar/'.$id)->with('error', 'CPAR Not Approved')->with('page', 'Form Ketidaksesuian');
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

          Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpar_dept, 'cpar_dept'));
          $cpar->save();      
    }

    public function sendemailqa(Request $request,$id)
      {
          $id_user = Auth::id();
          $cpar = CparDepartment::find($id);

          $cpar->posisi = "qa";
          
          $cpar->save();

          $mailtoo = array();

          //getQA

          $getqa = "SELECT employee_id, email FROM `employee_syncs` join users on employee_syncs.employee_id = users.username where employee_id='PI1108002' or employee_id = 'PI9707001'";

          $chief = DB::select($getqa);

          foreach ($chief as $cf) {
            array_push($mailtoo,$cf->email);
          }

          $getproduksi= "SELECT employee_id, email FROM `employee_syncs` join users on employee_syncs.employee_id = users.username where employee_id='".$cpar->foreman."' or employee_id = '".$cpar->manager."'";

          $produksi = DB::select($getproduksi);

          foreach ($produksi as $pd) {
            array_push($mailtoo,$pd->email);
          }

          //Get Foreman Manager

          $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;
          $cpar_dept = db::select($isimail);

          Mail::to($mailtoo)->cc('ratri.sulistyorini@music.yamaha.com')->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpar_dept, 'cpar_dept'));
          
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
          return redirect('/index/form_ketidaksesuaian/verifikasicpar/'.$id)->with('status', 'CPAR Not Approved')->with('page', 'Form Laporan Ketidaksesuaian');
      }

      // Form Ketidaksesuaian

      public function response($id){
          $emp_id = Auth::user()->username;
          $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

          $cpar = CparDepartment::find($id);

          $employee = EmployeeSync::where('employee_id', Auth::user()->username)
          ->select('employee_id', 'name', 'position')->first();

          if ($cpar->posisi != 'sl' && $cpar->posisi != 'cf' && $cpar->posisi != 'm') {
            return view('cpar.detail_car', array(
              'cpar' => $cpar,
              'employee' => $employee,
            ))->with('page', 'Form Ketidaksesuaian');
          }
          else{
            return redirect('index/form_ketidaksesuaian');
          }
      }

      public function update_car(Request $request, $id)
      {
        try{
          $car = CparDepartment::find($id);
          $car->pic_car = $request->get('pic_car');
          $car->deskripsi_car = $request->get('deskripsi_car');
          $car->penanganan_car = $request->get('penanganan_car');
          $car->save();

          return redirect('/index/form_ketidaksesuaian/response/'.$car->id)->with('status', 'Data has been updated.')->with('page', 'Form Ketidaksesuaian');
        }
        catch (QueryException $e){
          $error_code = $e->errorInfo[1];
          if($error_code == 1062){
            return back()->with('error', 'Already exist.')->with('page', 'Form Ketidaksesuaian');
          }
          else{
            return back()->with('error', $e->getMessage())->with('page', 'Form Ketidaksesuaian');
          }
        }
      }

      public function sendemailcar(Request $request,$id)
      {
          $id_user = Auth::id();

          $cpar = CparDepartment::find($id);

          if ($cpar->posisi == "dept") {
            if ($cpar->chief_car == null && $cpar->foreman_car == null) {
                $cpar->posisi = "deptm";
                $cpar->approvalcf_car = "Approved";
                $cpar->datecf_car = date('Y-m-d H:i:s');
            }
            else{
                $cpar->posisi = "deptcf";
            }

            if($cpar->grade == "Staff"){
          
              //apakah chiefnya ada
              if ($cpar->chief_car != null) {
                $posi = "chief_car";
              }
              // apakah foremannya ada
              else if($cpar->foreman_car != null) {
                $posi = "foreman_car";
              }
              // apakah manager ada
              else{
                $posi = "manager_car";
              }
            }
            
            else if($cpar->grade == "Leader"){
              //foreman ada
              if($cpar->foreman_car != null) {
                $posi = "foreman_car";
              }
              else if ($cpar->chief_car != null) {
                $posi = "chief_car";
              }
              else{
                $posi = "manager_car";
              }

            }

            $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.".$posi." = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$cpar->id."'";
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


    public function verifikasicar($id){
      $car = CparDepartment::find($id);

      if($car->grade == "Staff"){
        
        //apakah chiefnya ada
        if ($car->chief_car != null) {
          $pos = "chief_car";
        }

        // apakah foremannya ada
        else if($car->foreman_car != null) {
          $pos = "foreman_car";
        }

        // apakah manager ada
        else{
          $pos = "manager_car";
        }

    }
    else if($car->grade == "Leader"){
      //foreman ada
      if($car->foreman_car != null) {
        $pos = "foreman_car";
      }

      else if ($car->chief_car != null) {
        $pos = "chief_car";
      }

      else{
        $pos = "manager_car";
      }

    }

    $qa = CparDepartment::select('cpar_departments.*','pic.name as picname','cfcar.name as cfname','mcar.name as mname')
    ->leftjoin('employee_syncs as pic','cpar_departments.pic_car','=','pic.employee_id')
    ->leftjoin('employee_syncs as cfcar','cpar_departments.'.$pos,'=','cfcar.employee_id')
    ->leftjoin('employee_syncs as mcar','cpar_departments.manager_car','=','mcar.employee_id')
    ->where('cpar_departments.id','=',$id)
    ->get();

    $items = CparItem::select('cpar_items.*')
    ->join('cpar_departments','cpar_items.id_cpar','=','cpar_departments.id')
        // ->join('material_qa_complaints','cpar_items.item','=','material_qa_complaints.material_number')
    ->where('cpar_departments.id','=',$id)
    ->get();

    if ($car->posisi == 'dept' || $car->posisi == 'deptcf' || $car->posisi == 'deptm' || $car->posisi == 'verif') {
      return view('cpar.verifikasi_car', array(
        'car' => $car,
        'items' => $items,
        'pic' => $qa[0]->picname,
        'cfcar' => $qa[0]->cfname,
        'mcar' => $qa[0]->mname,
      ))->with('page', 'Penanganan Form Laporan Ketidaksesuaian');
    }
    else{
      return redirect('index/form_ketidaksesuaian');
    }

 
  }


  public function approvalcar(Request $request,$id)
  {
    $approve = $request->get('approve');

    if(count($approve) == 3){
      $car = CparDepartment::find($id);

      if ($car->posisi == "deptcf") {
        $car->posisi = "deptm";
        $car->approvalcf_car = "Approved";
        $car->datecf_car = date('Y-m-d H:i:s');

        $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.manager_car = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$car->id."'";
        $mails = DB::select($mailto);

        foreach($mails as $mail){
          $mailtoo = $mail->email;
          $number = $mail->phone;
        }

        $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$car->id;

        $car_dept = db::select($isimail);

        Mail::to($mailtoo)->send(new SendEmail($car_dept, 'cpar_dept'));
      }

      else if ($car->posisi == "deptm") {
        $car->posisi = "verif";
        $car->status = "verification";
        $car->approvalm_car = "Approved";
        $car->datem_car = date('Y-m-d H:i:s');

        $sec = explode("_", $car->section_to);
        $secfrom = explode("_", $car->section_from);
        
        //get departemen
        
        $dept = EmployeeSync::where('section','=', $secfrom[1])
        ->select('department')->first();

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = '".$dept->department."' and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%') and (section is null or section != '".$sec[1]."')";

        $mailtoo = DB::select($mails);

        $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$car->id;

        $car_dept = db::select($isimail);

        Mail::to($mailtoo)->send(new SendEmail($car_dept, 'cpar_dept'));
      }

      

      $car->save();

      return redirect('/index/form_ketidaksesuaian/verifikasicar/'.$id)->with('status', 'Form Approved')->with('page', 'Form Ketidaksesuaian');
    }
    else{
      return redirect('/index/form_ketidaksesuaian/verifikasicar/'.$id)->with('error', 'Form Not Approved')->with('page', 'Form Ketidaksesuaian');
    }          
  }


  public function notapprovecar(Request $request,$id)
    {
      $alasan = $request->get('alasan_car');

      $car = CparDepartment::find($id);
      
      $car->alasan_car = $alasan;
      $car->datereject_car = date('Y-m-d H:i:s');

      if ($car->posisi == "deptcf") {
        $car->posisi = "dept";              
      }

      else if ($car->posisi == "deptm") {
        $car->posisi = "dept";
        $car->approvalcf_car = null;
        $car->datecf_car = null;
      }

      $car->save();

      $query = "select * from cpar_departments where cpar_departments.id='".$id."'";
      $tolak = db::select($query);

      $mailto = "select distinct employees.name,email,phone from cpar_departments join employees on cpar_departments.pic_car = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$car->id."'";
      $mails = DB::select($mailto);

      foreach($mails as $mail){
        $mailtoo = $mail->email;
        $number = $mail->phone;
      }

      Mail::to($mailtoo)->send(new SendEmail($tolak, 'rejectcpar_dept'));
      return redirect('/index/form_ketidaksesuaian/verifikasicar/'.$id)->with('status', 'CAR Not Approved')->with('page', 'Form Laporan Ketidaksesuaian');
    }

    public function verifikasibagian($id){
      $verifikasi = CparDepartment::find($id);

      if($verifikasi->grade == "Staff"){
        
        //apakah chiefnya ada
        if ($verifikasi->chief_car != null) {
          $pos = "chief_car";
        }

        // apakah foremannya ada
        else if($verifikasi->foreman_car != null) {
          $pos = "foreman_car";
        }

        // apakah manager ada
        else{
          $pos = "manager_car";
        }
    }

    else if($verifikasi->grade == "Leader"){
      //foreman ada
      if($verifikasi->foreman_car != null) {
        $pos = "foreman_car";
      }

      else if ($verifikasi->chief_car != null) {
        $pos = "chief_car";
      }

      else{
        $pos = "manager_car";
      }

    }

    $qa = CparDepartment::select('cpar_departments.*','pic.name as picname','cfcar.name as cfname','mcar.name as mname')
    ->leftjoin('employee_syncs as pic','cpar_departments.pic_car','=','pic.employee_id')
    ->leftjoin('employee_syncs as cfcar','cpar_departments.'.$pos,'=','cfcar.employee_id')
    ->leftjoin('employee_syncs as mcar','cpar_departments.manager_car','=','mcar.employee_id')
    ->where('cpar_departments.id','=',$id)
    ->get();

    $items = CparItem::select('cpar_items.*')
    ->join('cpar_departments','cpar_items.id_cpar','=','cpar_departments.id')
    ->where('cpar_departments.id','=',$id)
    ->get();

    if ($verifikasi->posisi == 'verif' || $verifikasi->posisi == 'close' || $verifikasi->posisi == 'dept') {
      return view('cpar.verifikasi_bagian', array(
        'verifikasi' => $verifikasi,
        'items' => $items,
        'pic' => $qa[0]->picname,
        'cfcar' => $qa[0]->cfname,
        'mcar' => $qa[0]->mname,
      ))->with('page', 'Verifikasi Bagian');
    }
    else{
      return redirect('index/form_ketidaksesuaian');
    }

    
  }

  public function closecar(Request $request){
    try {
      $data = CparDepartment::where('id','=' , $request->get('id'))
      ->first();

      $data->posisi = "close";
      $data->status = "close";
      $data->save();

      $response = array(
        'status' => true,
        'message' => 'Success Close CPAR'
      );
      return Response::json($response);

      } catch (QueryException $e) {
          $response = array(
            'status' => false,
            'message' => 'error'
          );
          return Response::json($response);
      }
  }

  public function rejectcar(Request $request){
    try {
        $dataa = CparDepartment::where('id','=' , $request->get('id'))
        ->first();
        
        $dataa->status = "car";
        $dataa->reject_all = $request->get('catatan');

        $dataa->approvalcf_car = null;
        $dataa->approvalm_car = null;        
        $dataa->datecf_car = null;
        $dataa->datem_car = null;

        $sec = explode("_", $dataa->section_to);
        $secfrom = explode("_", $dataa->section_from);

        //get departemen
        $dept = EmployeeSync::where('section','=', $sec[1])
        ->select('department')->first();

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = '".$dept->department."' and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%') and (section is null or section != '".$secfrom[1]."')";

        $mailtoo = DB::select($mails);

        // $mailto = "select distinct employees.name,email from cpar_departments join employees on cpar_departments.pic_car = employees.employee_id join users on employees.employee_id = users.username where cpar_departments.id = '".$request->get('id')."'";
        // $mails = DB::select($mailto);

        // foreach($mails as $mail){
        //   $mailtoo = $mail->email;
        // }

        $dataa->save();

        $query = "select * from cpar_departments where cpar_departments.id='".$request->get('id')."'";
        $tolak = db::select($query);

        Mail::to($mailtoo)->send(new SendEmail($tolak, 'rejectcpar_dept'));

        $data2 = CparDepartment::where('id','=' , $request->get('id'))->first();
        $data2->posisi = "dept";
        $data2->save();
        
        $response = array(
          'status' => true,
          'message' => 'Success Reject Data'
        );
        return Response::json($response);

      } 

      catch (QueryException $e) {
        $response = array(
          'status' => false,
          'message' => 'error'
        );
      
        return Response::json($response);
      }
  }


  // Display Monitoring


  public function monitoring(){

    $sec = db::select("select DISTINCT section from employee_syncs");

    return view('cpar.monitoring',  
      array(
          'title' => 'Monitoring Form Laporan Ketidaksesuaian', 
          'title_jp' => '不適合報告フォームの管理',
          'sections' => $sec
        )
      )->with('page', 'Form Laporan Ketidaksesuaian');
  }

  public function fetchMonitoring(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = CparDepartment::where('status', '<>', 'close')
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

      $status = $request->get('status');

      if ($status != null) {
          $cat = json_encode($status);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and cpar_departments.status in'.$kat;
      }else{
          $kate = '';
      }

      $section_from = $request->get('section_from');

      if ($section_from != null) {
          $secfff = json_encode($section_from);
          $secff = str_replace(array("[","]"),array("",""),$secfff);
          $secf = str_replace(",","|",$secff);
          $secf = str_replace('"',"",$secf);

          $secf = 'and cpar_departments.section_from REGEXP "'.$secf.'"';
      } else {
          $secf = '';
      }

      $section_to = $request->get('section_to');

      if ($section_to != null) {
          $secttt = json_encode($section_to);
          $sectt = str_replace(array("[","]"),array("",""),$secttt);
          $sect = str_replace(",","|",$sectt);
          $sect = str_replace('"',"",$sect);

          $sect = 'and cpar_departments.section_to REGEXP "'.$sect.'"';
      } else {
          $sect = '';
      }

      //per month

      // $data = db::select("select count(id) as kasus, monthname(tanggal) as bulan, year(tanggal) as tahun, sum(case when cpar_departments.`status` = 'cpar' then 1 else 0 end) as cpar, sum(case when cpar_departments.`status` = 'car' then 1 else 0 end) as car, sum(case when cpar_departments.`status` = 'verification' then 1 else 0 end) as verif, sum(case when cpar_departments.`status` = 'close' then 1 else 0 end) as close from cpar_departments where cpar_departments.deleted_at is null group by bulan, tahun order by tahun, bulan ASC");


      //per tgl
      $data = db::select("
        select date.week_date, coalesce(cpar.qty, 0) as cpar, coalesce(car.qty, 0) as car, coalesce(verification.qty, 0) as verification, coalesce(`close`.qty, 0) as close from 
        (select week_date from weekly_calendars 
        where date(week_date) >= '".$datefrom."'
        and date(week_date) <= '".$dateto."') date
        left join
        (select date(tanggal) as date, count(id) as qty from cpar_departments
        where date(tanggal) >= '".$datefrom."' and date(tanggal) <= '".$dateto."' and `status` = 'cpar' and cpar_departments.deleted_at is null ".$kate." ".$secf." ".$sect."
        group by date(tanggal)) cpar
        on date.week_date = cpar.date
        left join
        (select date(tanggal) as date, count(id) as qty from cpar_departments
        where date(tanggal) >= '".$datefrom."' and date(tanggal) <= '".$dateto."' and `status` = 'car' and cpar_departments.deleted_at is null ".$kate." ".$secf." ".$sect."
        group by date(tanggal)) car
        on date.week_date = car.date
        left join
        (select date(tanggal) as date, count(id) as qty from cpar_departments
        where date(tanggal) >= '".$datefrom."' and date(tanggal) <= '".$dateto."' and `status` = 'verification' and cpar_departments.deleted_at is null ".$kate." ".$secf." ".$sect."
        group by date(tanggal)) verification
        on date.week_date = verification.date
        left join
        (select date(tanggal) as date, count(id) as qty from cpar_departments
        where date(tanggal) >= '".$datefrom."' and date(tanggal) <= '".$dateto."' and `status` = 'close' and cpar_departments.deleted_at is null ".$kate." ".$secf." ".$sect."
        group by date(tanggal)) close
        on date.week_date = `close`.date
        order by week_date asc");


      $year = date('Y');

      $response = array(
        'status' => true,
        'datas' => $data,
        'year' => $year,
        'section_from' => $secf,
        'section_to' => $sect
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

          if ($status == "CPAR") {
            $status = "cpar";
          }

          if ($status == "Penanganan") {
            $status = "car";
          }

          if ($status == "Verifikasi") {
            $status = "verification";
          }

          $stat = 'and cpar_departments.status = "'.$status.'"';
      }else{
          $stat = '';
      }

      $datefrom = $request->get('datefrom');
      $dateto = $request->get('dateto');

      if ($datefrom != null && $dateto != null) {
          $df = 'and cpar_departments.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
      }else{
          $df = '';
      }

      $section_from = $request->get('sf');

      if ($section_from != null) {
          $sf = $section_from;
      }else{
          $sf = '';
      }

      $section_to = $request->get('st');

      if ($section_to != null) {
          $st = $section_to;
      }else{
          $st = '';
      }

      $query = "select cpar_departments.* FROM cpar_departments where cpar_departments.deleted_at is null and tanggal = '".$tgl."' ".$stat." ".$df." ".$sf." ".$st."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->addColumn('action', function($detail){
        $id = $detail->id;
        return '<a href="print/'.$id.'" class="btn btn-warning btn-xs" target="_blank">Report</a>';
      })

      ->editColumn('tanggal', function($detail){
        return date('d F Y', strtotime($detail->tanggal));
      })

      ->editColumn('section_from', function($detail){
        $secf = $detail->section_from;
        $sf = explode("_", $secf);
        return $sf[0]." - ".$sf[1];
      })

      ->editColumn('section_to', function($detail){
        $sect = $detail->section_to;
        $st = explode("_", $sect);
        return $st[0]." - ".$st[1];
      })

      ->editColumn('status', function($detail){
        if($detail->status == "cpar") {
            return '<label class="label label-danger"> Pembuatan CPAR </label>';
          }
          else if($detail->status == "car"){
            return '<label class="label label-warning"> Pembuatan Penanganan </label>';
          }
          else if($detail->status == "verification"){
            return '<label class="label label-primary"> Proses Verifikasi </label>';
          }
          else if($detail->status == "close"){
            return '<label class="label label-success"> Closed </label>';
          }
      })


      ->rawColumns(['action' => 'action','status' => 'status'])
      ->make(true);
  }


  public function fetchtable(Request $request)
    {

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = CparDepartment::where('status', '<>', 'close')
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

      $status = $request->get('status');

      if ($status != null) {
          $cat = json_encode($status);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and cpar_departments.status in'.$kat;
      }else{
          $kate = 'and cpar_departments.status not in ("close")';
      }

      $section_from = $request->get('section_from');

      if ($section_from != null) {
          $secfff = json_encode($section_from);
          $secff = str_replace(array("[","]"),array("",""),$secfff);
          $secf = str_replace(",","|",$secff);
          $secf = str_replace('"',"",$secf);

          $secf = 'and cpar_departments.section_from REGEXP "'.$secf.'"';
      } else {
          $secf = '';
      }

      $section_to = $request->get('section_to');

      if ($section_to != null) {
          $secttt = json_encode($section_to);
          $sectt = str_replace(array("[","]"),array("",""),$secttt);
          $sect = str_replace(",","|",$sectt);
          $sect = str_replace('"',"",$sect);

          $sect = 'and cpar_departments.section_to REGEXP "'.$sect.'"';
      } else {
          $sect = '';
      }

      $data = db::select("select id, kategori, `status`, judul, tanggal, reject_all, section_from, section_to, posisi, (select name from employee_syncs where employee_id = cpar_departments.pelapor) as pelapor, 
        (CASE 
        WHEN cpar_departments.chief is null and cpar_departments.foreman is null THEN 'Tidak Ada'
        WHEN cpar_departments.grade = 'Staff' THEN 
        (IF(cpar_departments.chief is not null,(select name from employee_syncs where employee_id = cpar_departments.chief),(select name from employees where employee_id = cpar_departments.foreman)))
        WHEN cpar_departments.grade = 'Leader' THEN
        (IF(cpar_departments.foreman is not null,(select name from employee_syncs where employee_id = cpar_departments.foreman),(select name from employees where employee_id = cpar_departments.chief)))
        END) 
        as namacf,
        approvalcf, datecf,
        (select name from employee_syncs where employee_id = cpar_departments.manager) as namam,
        approvalm, datem, tanggal_car,
        (select name from employee_syncs where employee_id = cpar_departments.pic_car) as namapiccar,
        (CASE 
        WHEN cpar_departments.chief_car is null and cpar_departments.foreman_car is null THEN 'Tidak Ada'
        WHEN cpar_departments.grade = 'Staff' THEN 
        (IF(cpar_departments.chief_car is not null,(select name from employee_syncs where employee_id = cpar_departments.chief_car),(select name from employees where employee_id = cpar_departments.foreman_car)))
        WHEN cpar_departments.grade = 'Leader' THEN
        (IF(cpar_departments.foreman_car is not null,(select name from employee_syncs where employee_id = cpar_departments.foreman_car),(select name from employees where employee_id = cpar_departments.chief_car)))
        END) 
        as namacfcar,
        approvalcf_car,datecf_car,
        (select name from employee_syncs where employee_id = cpar_departments.manager_car) as namamcar,
        approvalm_car,datem_car
        from cpar_departments where cpar_departments.deleted_at is null and tanggal between '".$datefrom."' and '".$dateto."' ".$kate." ".$secf." ".$sect." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }




    //QA

    public function approveqa($id){
        $cpar = CparDepartment::find($id);
        $cpar->kategori = "Kualitas";
        $cpar->status = "close";

        $cpar->save();

        $message = 'Kasus dengan Judul '.$cpar->judul;
        $message2 ='Berhasil di setujui untuk diterbikan CPAR';
        return view('cpar.approval_message', array(
          'stat' => 'Approved',
          'judul' => $cpar->judul,
          'message' => $message,
          'message2' => $message2,
        ))->with('page', 'CPAR Approval');

    }

    public function rejectqa($id){

        $cpar = CparDepartment::find($id);

        $data2 = CparDepartment::where('id','=' , $cpar->id)->first();
        $data2->posisi = "m";
        $data2->save();

        $cpar->approvalcf = "Approved";
        $cpar->datecf = date('Y-m-d H:i:s');
        $cpar->approvalm = "Approved";
        $cpar->datem = date('Y-m-d H:i:s');
        $cpar->status = "car";

        $sec = explode("_", $cpar->section_to);
        $secfrom = explode("_", $cpar->section_from);
        //get departemen
        $dept = EmployeeSync::where('section','=', $sec[1])
        ->select('department')->first();

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.department = '".$dept->department."' and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%') and (section is null or section != '".$secfrom[1]."')";

        $mailtoo = DB::select($mails);

        $cpar->tanggal_car = date('Y-m-d H:i:s');

        //get chief foreman manager from departemen TUJUAN

        $cfm2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position in ('chief','foreman','manager')");


        $chief2 = null;
        $foreman2 = null;
        $manager2 = null;
        $chiefcount2 = 0;
        $foremancount2 = 0;

        if ($cfm2 != null) {
          foreach ($cfm2 as $position) {

            $pos = $position->position;

            // Manager 
            if ($pos == "Manager") {
              $manager2 = $position->employee_id;
              $cpar->manager_car = $manager2;
            }

            // Chief
            if ($pos == "Chief") {
              if ($chiefcount2 == 0) {
                $chief2 = $position->employee_id;
                $chiefcount2 = 1;

                $cpar->chief_car = $chief2;
              }
              else{
                $cf2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Chief' and section='".$sec[1]."'");

                $chief2 = $cf2[0]->employee_id;

                $cpar->chief_car = $chief2;
              }
            }

            // Foreman
            if ($pos == "Foreman") {
              if ($foremancount2 == 0) {
                $foreman2 = $position->employee_id;
                $foremancount2 = 1;

                $cpar->foreman_car = $foreman2;
              } else {
                $f2 = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '".$dept->department."' and position = 'Foreman' and section='".$sec[1]."'");

                $foreman2 = $f2[0]->employee_id;
                $cpar->foreman_car = $foreman2;
              }

            }

          }
        }

        $cpar->posisi = 'dept';

        $isimail = "select * FROM cpar_departments where cpar_departments.id = ".$cpar->id;

        $cpar_dept = db::select($isimail);

        //cc

        // $dept = EmployeeSync::where('section','=', $sec[1])
        // ->select('department')->first();

        Mail::to($mailtoo)->send(new SendEmail($cpar_dept, 'cpar_dept'));

        $cpar->save();

        $message = 'Kasus dengan Judul '.$cpar->judul;
        $message2 ='Berhasil di reject dan dikirimkan ke departemen terkait untuk ditangani';
        return view('cpar.approval_message', array(
          'stat' => 'Reject',
          'judul' => $cpar->judul,
          'message' => $message,
          'message2' => $message2,
        ))->with('page', 'CPAR Approval');
    }



    // AUDIT ISO STANDARISASI

    public function audit() {
     
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      return view('cpar.audit_index', array(
       'emp' => $emp_id,
       'employee' => $employee
      ))->with('page', 'Audit ISO');
    }

    public function fetchDataAudit(Request $request)
    {
      $tanggal = $request->get("tanggal");

      if ($tanggal == null) {
        $tgl = '';
      }
      else{
        $tgl = "and auditor_date = '".$tanggal."'"; 
      }

      $query = "SELECT * FROM standarisasi_audits where deleted_at is null ".$tgl." order by id desc";
      $detail = db::select($query);

      $response = array(
        'status' => true,
        'lists' => $detail,
      );
      return Response::json($response);
    }


    // Buat Audit

    public function audit_create()
    {
      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      $leader = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

      return view('cpar.audit_create', array(
        'employee' => $emp,
        'leaders' => $leader
      ))->with('page', 'Form Audit ISO');
    }

    public function audit_post_create(Request $request)
    {
      try {
        $id_user = Auth::id();


        $syarat = null;
        if ($request->get('auditor_persyaratan') == null || $request->get('auditor_persyaratan') == "") {
            $syarat = "0";
        }
        else{
            $syarat = $request->get('auditor_persyaratan');
        }

        $audits = StandarisasiAudit::create([
           'auditor' => $request->get('auditor'),
           'auditor_name' => $request->get('auditor_name'),
           'auditor_date' => $request->get('auditor_date'),
           'auditor_jenis' => $request->get('auditor_jenis'),
           'auditor_lokasi' => $request->get('auditor_lokasi'),
           'auditor_kategori' => $request->get('auditor_kategori'),
           'auditor_persyaratan' => $syarat,
           'auditor_permasalahan' => $request->get('auditor_permasalahan'),
           'auditor_penyebab' => $request->get('auditor_penyebab'),
           'auditor_bukti' => $request->get('auditor_bukti'),
           'auditee' => $request->get('auditee'),
           'auditee_name' => $request->get('auditee_name'),
           'auditee_due_date' => $request->get('auditee_due_date'),
           'audit_no' => $request->get('audit_no'),
           'posisi' => 'std',
           'status' => 'cpar',
           'created_by' => $id_user
        ]);

        $audits->save();

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.`group` = 'Standardization Group'";
        $mailtoo = DB::select($mails);

        $isimail = "select * FROM standarisasi_audits where standarisasi_audits.id = ".$audits->id;
        $auditiso = db::select($isimail);

        Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($auditiso, 'std_audit'));

        $response = array(
          'status' => true,
          'datas' => "Berhasil",
          'id' => $audits->id
        );

        return Response::json($response);

     } catch (QueryException $e){
        $response = array(
             'status' => false,
             'datas' => $e->getMessage()
        );
        return Response::json($response);
       }
    }

    public function audit_create_checklist($id)
    {
      $audit = StandarisasiAuditIso::find($id);

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      $leader = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

      return view('cpar.audit_create_checklist', array(
        'audit' => $audit,
        'employee' => $emp,
        'leaders' => $leader
      ))->with('page', 'Form Audit ISO');
    }

    public function audit_post_create_checklist(Request $request)
    {
      try {
        $id_user = Auth::id();

        $syarat = null;
        if ($request->get('auditor_persyaratan') == null || $request->get('auditor_persyaratan') == "") {
            $syarat = "0";
        }
        else{
            $syarat = $request->get('auditor_persyaratan');
        }

        $audits = StandarisasiAudit::create([
           'auditor' => $request->get('auditor'),
           'auditor_name' => $request->get('auditor_name'),
           'auditor_date' => $request->get('auditor_date'),
           'auditor_jenis' => $request->get('auditor_jenis'),
           'auditor_lokasi' => $request->get('auditor_lokasi'),
           'auditor_kategori' => $request->get('auditor_kategori'),
           'auditor_persyaratan' => $syarat,
           'auditor_permasalahan' => $request->get('auditor_permasalahan'),
           'auditor_bukti' => $request->get('auditor_bukti'),
           'auditee' => $request->get('auditee'),
           'auditee_name' => $request->get('auditee_name'),
           'auditee_due_date' => $request->get('auditee_due_date'),
           'audit_no' => $request->get('audit_no'),
           'posisi' => 'std',
           'status' => 'cpar',
           'created_by' => $id_user
        ]);

        $audits->save();

        $data3 = StandarisasiAuditIso::where('id', $request->get('id_checklist'))
        ->update(['status_ditangani' => 'true']);

        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.`group` = 'Standardization Group'";
        $mailtoo = DB::select($mails);

        $isimail = "select * FROM standarisasi_audits where standarisasi_audits.id = ".$audits->id;
        $auditiso = db::select($isimail);

        Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($auditiso, 'std_audit'));

        $response = array(
          'status' => true,
          'datas' => "Berhasil",
          'id' => $audits->id
        );

        return Response::json($response);

     } catch (QueryException $e){
        $response = array(
             'status' => false,
             'datas' => $e->getMessage()
        );
        return Response::json($response);
       }
    }


    public function audit_get_nama(Request $request){
      $auditee = $request->auditee;
      $query = "SELECT name,section,department FROM employee_syncs where employee_id = '$auditee'";
      $nama = DB::select($query);
      return json_encode($nama);
    }

    public function audit_get_nomor(Request $request)
    {
      $datenow = date('Y-m-d');
      $nomor = '';
      $kategori = $request->kategori;

      $query = "SELECT * FROM `standarisasi_audits` where auditor_kategori = '$kategori' ORDER BY id DESC LIMIT 1";
      $nomordepan = DB::select($query);

      if ($nomordepan != null) {
        foreach ($nomordepan as $nomors) {
          $nomor = $nomors->audit_no;
        }
      }
      else{
        $nomor = 0;
      }

      return json_encode($nomor);
    }

    public function audit_detail($id)
    {
        $audits = StandarisasiAudit::find($id);

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $emp = EmployeeSync::where('employee_id', $audits->employee_id)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        $leader = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like 'Leader%' or position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

        return view('cpar.audit_detail', array(
            'audits' => $audits,
            'employee' => $emp,
            'leaders' => $leader
        ))->with('page', 'Form Audit ISO');
    }

    public function audit_post_detail(Request $request)
    {
         try {
              $id_user = Auth::id();

              $forms = StandarisasiAudit::where('id',$request->get('id'))
              ->update([
                   'auditor_kategori' => $request->get('auditor_kategori'), 
                   'auditor_persyaratan' => $request->get('auditor_persyaratan'),
                   'auditor_permasalahan' => $request->get('auditor_permasalahan'),
                   'auditor_penyebab' => $request->get('auditor_penyebab'),
                   'auditor_bukti' => $request->get('auditor_bukti'),
                   'auditee' => $request->get('auditee'),
                   'auditee_name' => $request->get('auditee_name'),
                   'auditee_due_date' => $request->get('auditee_due_date'),
                   'created_by' => $id_user
              ]);

              $response = array(
                'status' => true,
                'datas' => "Berhasil",
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }

    // Verifikasi oleh STD

  public function verifikasistd($id){
    $audit = StandarisasiAudit::find($id);

    if ($audit->posisi == 'std' || $audit->posisi == 'auditor' || $audit->posisi == 'auditee') {
      return view('cpar.verifikasi_std', array(
        'audit' => $audit,
      ))->with('page', 'CPAR Audit Internal');
    }
    else{
      return redirect('index/audit_iso');
    }
  }

  public function std_approval(Request $request,$id)
  {
    $approve = $request->get('approve');

    if(count($approve) == 4){
      $audit = StandarisasiAudit::find($id);

      if ($audit->posisi == "std") {
        $audit->posisi = "auditee";
        $audit->status = "car";
        $audit->approval_std = "Approved";
        $audit->approval_date = date('Y-m-d H:i:s');

        $audit->save();

        $mailto = "select distinct employees.name,email from standarisasi_audits join employees on standarisasi_audits.auditee = employees.employee_id join users on employees.employee_id = users.username where standarisasi_audits.id = '".$audit->id."'";
        $mails = DB::select($mailto);

        foreach($mails as $mail){
          $mailtoo = $mail->email;
        }
      
        $isimail = "select * FROM standarisasi_audits where standarisasi_audits.id = ".$audit->id;
        $audits = db::select($isimail);

        Mail::to($mailtoo)->send(new SendEmail($audits, 'std_audit'));

      }


      return redirect('/index/audit_iso/verifikasistd/'.$id)->with('status', 'Audit Approved')->with('page', 'Audit ISO');
    }
    else{
      return redirect('/index/audit_iso/verifikasistd/'.$id)->with('error', 'Audit Not Approved')->with('page', 'Audit ISO');
    }          
  }

  public function std_comment(Request $request,$id)
  {
      $alasan = $request->get('alasan');
      $audit = StandarisasiAudit::find($id);

      $audit->alasan = $alasan;
      $audit->approval_date = date('Y-m-d H:i:s');

      if ($audit->posisi == "std") {
        $audit->posisi = "auditor";
        $audit->status = "commended";
      }

      $audit->save();

      $query = "select * from standarisasi_audits where standarisasi_audits.id='".$id."'";
      $komentar = db::select($query);

      $mailto = "select distinct employees.name,email from standarisasi_audits join employees on standarisasi_audits.auditor = employees.employee_id join users on employees.employee_id = users.username where standarisasi_audits.id = '".$audit->id."'";
      $mails = DB::select($mailto);

      foreach($mails as $mail){
        $mailtoo = $mail->email;
      }

      Mail::to($mailtoo)->send(new SendEmail($komentar, 'reject_std_audit'));
      return redirect('/index/audit_iso/verifikasistd/'.$id)->with('status', 'Audit Not Approved')->with('page', 'Audit ISO');
  }

  public function std_reject(Request $request,$id)
  {
      $alasan = $request->get('alasan');
      $audit = StandarisasiAudit::find($id);

      $audit->alasan = $alasan;
      $audit->approval_date = date('Y-m-d H:i:s');

      if ($audit->posisi == "std") {
        $audit->posisi = "std";
        $audit->status = "rejected";
      }

      $audit->save();

      $query = "select * from standarisasi_audits where standarisasi_audits.id='".$id."'";
      $reject = db::select($query);

      $mailto = "select distinct employees.name,email from standarisasi_audits join employees on standarisasi_audits.auditor = employees.employee_id join users on employees.employee_id = users.username where standarisasi_audits.id = '".$audit->id."'";
      $mails = DB::select($mailto);

      foreach($mails as $mail){
        $mailtoo = $mail->email;
      }

      Mail::to($mailtoo)->send(new SendEmail($reject, 'reject_std_audit'));
      return redirect('/index/audit_iso/verifikasistd/'.$id)->with('status', 'Audit Not Approved')->with('page', 'Audit ISO');
  }

  public function std_response($id){
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $std = StandarisasiAudit::find($id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      if ($std->posisi == 'auditee' || $std->posisi == 'auditor_final') {
        return view('cpar.audit_response', array(
          'std' => $std,
          'employee' => $employee,
        ))->with('page', 'Audit ISO');
      }
      else{
        return redirect('index/audit_iso');
      }
  }

  public function update_response(Request $request, $id)
  {
    try {
      $audit = StandarisasiAudit::find($id);
      $audit->auditor_penyebab = $request->get('auditor_penyebab');
      $audit->auditee_perbaikan = $request->get('auditee_perbaikan');
      $audit->auditee_pencegahan = $request->get('auditee_pencegahan');
      $audit->auditee_biaya = $request->get('auditee_biaya');
      $audit->auditee_date = date('Y-m-d');
      $audit->save();

      return redirect('/index/audit_iso/response/'.$audit->id)->with('status', 'Data has been updated.')->with('page', 'Audit ISO');
    }
    catch (QueryException $e){
      $error_code = $e->errorInfo[1];
      if($error_code == 1062){
        return back()->with('error', 'Already exist.')->with('page', 'Audit ISO');
      }
      else{
        return back()->with('error', $e->getMessage())->with('page', 'Audit ISO');
      }
    }
  }

  public function send_email_audit(Request $request){
        $audit = StandarisasiAudit::find($request->get('id'));

        try{
            if ($audit->posisi == "auditor")
            {

            	$audit->posisi = "std";
        		$audit->status = "cpar";
        		$audit->save();
		                
		        $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.`group` = 'Standardization Group'";
		        $mailtoo = DB::select($mails);

		        $isimail = "select * FROM standarisasi_audits where standarisasi_audits.id = ".$audit->id;
		        $auditiso = db::select($isimail);

		        Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($auditiso, 'std_audit'));

		        $response = array(
		          'status' => true,
		          'datas' => "Berhasil"
		        );

		        return Response::json($response);
            }

        } catch (Exception $e) {
            $response = array(
		          'status' => false,
		          'datas' => "Gagal"
		        );

		    return Response::json($response);
        }
    }

  public function sendemailpenanganan(Request $request,$id)
      {
          $id_user = Auth::id();
          $auditee = StandarisasiAudit::find($id);

          if ($auditee->posisi == "auditee") {
            $auditee->posisi = "auditor_final";
            $auditee->status = "verif";          
            $auditee->save();      

            $mailto = "select distinct employees.name,email from standarisasi_audits join employees on standarisasi_audits.auditor = employees.employee_id join users on employees.employee_id = users.username where standarisasi_audits.id = '".$auditee->id."'";
            $mails = DB::select($mailto);

            $mailtostd = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where end_date is null and employee_syncs.`group` = 'Standardization Group'";
            $mailstd = DB::select($mailtostd);

          }

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }

          $isimail = "select * FROM standarisasi_audits where standarisasi_audits.id = ".$auditee->id;
          $audits = db::select($isimail);

          Mail::to($mailtoo)->send(new SendEmail($audits, 'std_audit'));

          Mail::to($mailstd)->send(new SendEmail($audits, 'std_audit'));
          return redirect('/index/audit_iso/response/'.$id)->with('status', 'Audit Has Been Sent')->with('page', 'Audit ISO');

    }

    public function audit_post_detail_last(Request $request)
    {
         try {
              $id_user = Auth::id();

              $forms = StandarisasiAudit::where('id',$request->get('id'))
              ->update([
                   'auditor_catatan' => $request->get('auditor_catatan'),
                   'auditor_manfaat' => $request->get('auditor_manfaat'),
                   'status' => 'close',
                   'created_by' => $id_user
              ]);

              $response = array(
                'status' => true,
                'datas' => "Berhasil",
              );
              return Response::json($response);

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }


  public function print_audit($id)
  {
    $audit = StandarisasiAudit::find($id);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');
    $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    $pdf->loadView('cpar.print_audit', array(
      'audit' => $audit,
    ));

    return $pdf->stream("Form ".$audit->audit_no.".pdf");
  }


  // Monitoring Audit

  public function monitoring_audit(){

    // $sec = db::select("select DISTINCT section from employee_syncs");

    return view('cpar.monitoring_audit',  
      array(
          'title' => 'Monitoring Audit ISO E-IRCA 45001', 
          'title_jp' => 'ISO内部監査の監視',
        )
      )->with('page', 'Audit ISO');
  }

  public function fetchMonitoring_audit(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = StandarisasiAudit::where('status', '<>', 'close')
      ->orderBy('auditor_date', 'asc')
      ->select(db::raw('date(auditor_date) as auditor_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->auditor_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

          $kate = 'and standarisasi_audits.status in'.$kat;
      }else{
          $kate = '';
      }

      //per tgl
      // $data = db::select("
      //   select date.week_date, coalesce(cpar.qty, 0) as cpar, coalesce(car.qty, 0) as penanganan, coalesce(verification.qty, 0) as verification, coalesce(`close`.qty, 0) as close, coalesce(rejected.qty, 0) as rejected, coalesce(revised.qty, 0) as revised  from 
      //   (select week_date from weekly_calendars 
      //   where date(week_date) >= '".$datefrom."'
      //   and date(week_date) <= '".$dateto."') date
      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'cpar' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) cpar
      //   on date.week_date = cpar.date
      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'car' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) car
      //   on date.week_date = car.date
      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'verif' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) verification
      //   on date.week_date = verification.date
      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'close' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) close
      //   on date.week_date = close.date
      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'rejected' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) rejected
      //   on date.week_date = rejected.date

      //   left join
      //   (select date(auditor_date) as date, count(id) as qty from standarisasi_audits
      //   where date(auditor_date) >= '".$datefrom."' and date(auditor_date) <= '".$dateto."' and `status` = 'commended' and standarisasi_audits.deleted_at is null ".$kate."
      //   group by date(auditor_date)) revised
      //   on date.week_date = revised.date

      //   order by week_date asc");

      $data = DB::select("SELECT
      date_format(auditor_date, '%a, %d %b %Y') AS tanggal,
      sum( CASE WHEN deleted_at is null AND `status` = 'cpar' THEN 1 ELSE 0 END ) AS cpar,
      sum( CASE WHEN deleted_at IS null AND `status` = 'car' THEN 1 ELSE 0 END ) AS car,
      sum( CASE WHEN deleted_at IS null AND `status` = 'verif' THEN 1 ELSE 0 END ) AS verification,
      sum( CASE WHEN deleted_at IS null AND `status` = 'close' THEN 1 ELSE 0 END ) AS `close`,
      sum( CASE WHEN deleted_at IS null AND `status` = 'rejected' THEN 1 ELSE 0 END ) AS `rejected`,
      sum( CASE WHEN deleted_at IS null AND `status` = 'commended' THEN 1 ELSE 0 END ) AS `revised`
      FROM
      standarisasi_audits
      WHERE 
      auditor_kategori = 'ISO 45001' and deleted_at is null and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate."
      GROUP BY
      auditor_date
      
      ");

      $data_klausul = DB::select("SELECT
        klausul,
        sum( CASE WHEN `status` = 'Good' THEN 1 ELSE 0 END ) AS good,
        sum( CASE WHEN `status` = 'None' THEN 1 ELSE 0 END ) AS `none`,
        sum( CASE WHEN `status` = 'Not Good' THEN 1 ELSE 0 END ) AS `not_good`
        FROM
        standarisasi_audit_isos
        WHERE 
        kategori = 'ISO 45001' and deleted_at is null 
        GROUP BY
        klausul
      ");

      $year = date('Y');

      $response = array(
        'status' => true,
        'datas' => $data,
        'data_klausul' => $data_klausul,
        'year' => $year
      );

      return Response::json($response);
  }

  public function detailMonitoring_audit(Request $request){

      $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

          if ($status == "cpar") {
            $status = "cpar";
          }

          if ($status == "car") {
            $status = "penanganan";
          }

          if ($status == "Verifikasi") {
            $status = "verif";
          }

          if ($status == "Revised") {
            $status = "commended";
          }

          if ($status == "Reject") {
            $status = "rejected";
          }

          $stat = 'and standarisasi_audits.status = "'.$status.'"';
      }else{
          $stat = '';
      }

      $datefrom = $request->get('datefrom');
      $dateto = $request->get('dateto');

      if ($datefrom != null && $dateto != null) {
          $df = 'and standarisasi_audits.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
      }else{
          $df = '';
      }

      $query = "select standarisasi_audits.* FROM standarisasi_audits where standarisasi_audits.deleted_at is null and auditor_kategori = 'ISO 45001' and auditor_date = '".$tgl."' ".$stat." ".$df."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->addColumn('action', function($detail){
        $id = $detail->id;
        return '<a href="print/'.$id.'" class="btn btn-warning btn-xs" target="_blank">Report</a>';
      })

      ->editColumn('auditor_date', function($detail){
        return date('d F Y', strtotime($detail->auditor_date));
      })

      ->editColumn('status', function($detail){
        if($detail->status == "cpar") {
            return '<label class="label label-danger"> Verifikasi STD </label>';
          }
          else if($detail->status == "car"){
            return '<label class="label label-warning"> Pembuatan Penanganan </label>';
          }
          else if($detail->status == "verif"){
            return '<label class="label label-primary"> Verifikasi Auditor </label>';
          }
          else if($detail->status == "close"){
            return '<label class="label label-success"> Closed </label>';
          }
          else if($detail->status == "rejected"){
            return '<label class="label label-danger"> Rejected </label>';
          }
          else if($detail->status == "commended"){
            return '<label class="label label-warning"> Revised </label>';
          }
      })


      ->rawColumns(['action' => 'action','status' => 'status'])
      ->make(true);
  }


  public function fetchtable_audit(Request $request)
    {

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = StandarisasiAudit::where('status', '<>', 'close')
      ->orderBy('auditor_date', 'asc')
      ->select(db::raw('date(auditor_date) as auditor_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->auditor_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

          $kate = 'and standarisasi_audits.status in'.$kat;
      }else{
          $kate = 'and standarisasi_audits.status not in ("close","rejected")';
      }


      $data = db::select("select * from standarisasi_audits where standarisasi_audits.deleted_at is null and auditor_kategori = 'ISO 45001' and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }


    public function monitoring_audit2(){

    // $sec = db::select("select DISTINCT section from employee_syncs");

    return view('cpar.monitoring_audit2',  
      array(
          'title' => 'Monitoring Audit ISO E-IRCA 14001', 
          'title_jp' => 'ISO内部監査の監視',
        )
      )->with('page', 'Audit ISO');
  }

  public function fetchMonitoring_audit2(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = StandarisasiAudit::where('status', '<>', 'close')
      ->orderBy('auditor_date', 'asc')
      ->select(db::raw('date(auditor_date) as auditor_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->auditor_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

          $kate = 'and standarisasi_audits.status in'.$kat;
      }else{
          $kate = '';
      }

      $data = DB::select("SELECT
      date_format(auditor_date, '%a, %d %b %Y') AS tanggal,
      sum( CASE WHEN deleted_at is null AND `status` = 'cpar' THEN 1 ELSE 0 END ) AS cpar,
      sum( CASE WHEN deleted_at IS null AND `status` = 'car' THEN 1 ELSE 0 END ) AS car,
      sum( CASE WHEN deleted_at IS null AND `status` = 'verif' THEN 1 ELSE 0 END ) AS verification,
      sum( CASE WHEN deleted_at IS null AND `status` = 'close' THEN 1 ELSE 0 END ) AS `close`,
      sum( CASE WHEN deleted_at IS null AND `status` = 'rejected' THEN 1 ELSE 0 END ) AS `rejected`,
      sum( CASE WHEN deleted_at IS null AND `status` = 'commended' THEN 1 ELSE 0 END ) AS `revised`
      FROM
      standarisasi_audits
      WHERE 
      auditor_kategori = 'ISO 14001' and deleted_at is null and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate."
      GROUP BY
      auditor_date
      ");

      $data_klausul = DB::select("SELECT
        klausul,
        sum( CASE WHEN `status` = 'Good' THEN 1 ELSE 0 END ) AS good,
        sum( CASE WHEN `status` = 'None' THEN 1 ELSE 0 END ) AS `none`,
        sum( CASE WHEN `status` = 'Not Good' THEN 1 ELSE 0 END ) AS `not_good`
        FROM
        standarisasi_audit_isos
        WHERE 
        kategori = 'ISO 14001' and deleted_at is null 
        GROUP BY
        klausul
      ");

      $year = date('Y');

      $response = array(
        'status' => true,
        'datas' => $data,
        'data_klausul' => $data_klausul,
        'year' => $year
      );

      return Response::json($response);
  }

  public function detailMonitoring_audit2(Request $request){

      $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

          if ($status == "cpar") {
            $status = "cpar";
          }

          if ($status == "car") {
            $status = "penanganan";
          }

          if ($status == "Verifikasi") {
            $status = "verif";
          }

          if ($status == "Revised") {
            $status = "commended";
          }

          if ($status == "Reject") {
            $status = "rejected";
          }

          $stat = 'and standarisasi_audits.status = "'.$status.'"';
      }else{
          $stat = '';
      }

      $datefrom = $request->get('datefrom');
      $dateto = $request->get('dateto');

      if ($datefrom != null && $dateto != null) {
          $df = 'and standarisasi_audits.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
      }else{
          $df = '';
      }

      $query = "select standarisasi_audits.* FROM standarisasi_audits where standarisasi_audits.deleted_at is null and auditor_kategori = 'ISO 14001' and auditor_date = '".$tgl."' ".$stat." ".$df."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->addColumn('action', function($detail){
        $id = $detail->id;
        return '<a href="print/'.$id.'" class="btn btn-warning btn-xs" target="_blank">Report</a>';
      })

      ->editColumn('auditor_date', function($detail){
        return date('d F Y', strtotime($detail->auditor_date));
      })

      ->editColumn('status', function($detail){
        if($detail->status == "cpar") {
            return '<label class="label label-danger"> Verifikasi STD </label>';
          }
          else if($detail->status == "car"){
            return '<label class="label label-warning"> Pembuatan Penanganan </label>';
          }
          else if($detail->status == "verif"){
            return '<label class="label label-primary"> Verifikasi Auditor </label>';
          }
          else if($detail->status == "close"){
            return '<label class="label label-success"> Closed </label>';
          }
          else if($detail->status == "rejected"){
            return '<label class="label label-danger"> Rejected </label>';
          }
          else if($detail->status == "commended"){
            return '<label class="label label-warning"> Revised </label>';
          }
      })


      ->rawColumns(['action' => 'action','status' => 'status'])
      ->make(true);
  }


  public function fetchtable_audit2(Request $request)
    {

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = StandarisasiAudit::where('status', '<>', 'close')
      ->orderBy('auditor_date', 'asc')
      ->select(db::raw('date(auditor_date) as auditor_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->auditor_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

          $kate = 'and standarisasi_audits.status in'.$kat;
      }else{
          $kate = 'and standarisasi_audits.status not in ("close","rejected")';
      }


      $data = db::select("select * from standarisasi_audits where standarisasi_audits.deleted_at is null and auditor_kategori = 'ISO 14001' and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }


    public function check_audit() {
     
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      return view('cpar.check_audit_iso', array(
       'emp' => $emp_id,
       'employee' => $employee
      ))->with('page', 'Audit ISO');
    }

    public function indexPointCheck($kategori,$lokasi)
    {
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      return view('cpar.point_check_audit_iso', array(
        'kategori' => $kategori,
        'lokasi' => $lokasi
      ))->with('page', 'Point Question Audit ISO');
    }

    public function fetchKategoriLokasi(Request $request)
    {
      try {
        $audit = DB::SELECT('SELECT DISTINCT(kategori),lokasi FROM `standarisasi_audit_checklists` where standarisasi_audit_checklists.deleted_at is null');

        $response = array(
          'status' => true,
          'lists' => $audit
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

    public function fetchHasilAudit(Request $request)
    {
      try {
        $audit = DB::SELECT('SELECT DISTINCT tanggal, kategori, auditor_id, auditor_name, lokasi, auditee, auditee_name from `standarisasi_audit_isos` where standarisasi_audit_isos.deleted_at is null');

        $response = array(
          'status' => true,
          'lists' => $audit
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

    public function check_audit_create()
    {
      $title = "Audit Create";
      $title_jp = "";

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      $location = StandarisasiAuditChecklist::orderBy('lokasi', 'asc')
      ->select('lokasi')
      ->whereIn('kategori',array('ISO 45001','ISO 14001','ISO 9001'))
      ->distinct()
      ->get();

      $category = StandarisasiAuditChecklist::orderBy('kategori', 'asc')
      ->select('kategori')
      ->whereIn('kategori',array('ISO 45001','ISO 14001','ISO 9001'))
      ->distinct()
      ->get();

      $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

      return view('cpar.create_check_audit_iso', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee' => $emp,
        'location' => $location,
        'category' => $category,
        'auditee' => $auditee
      ))->with('page', 'Audit ISO');
    }

    public function fetch_audit_create(Request $request)
    {
      try {
      
        $lokasi = $request->get("location");
        $kategori = $request->get("category");

        $query = 'SELECT * FROM standarisasi_audit_checklists where point_question is not null and deleted_at is null and kategori = "'.$kategori.'" and lokasi = "'.$lokasi.'" order by id asc';
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

    public function check_audit_report()
    {
      $title = "Audit Report";
      $title_jp = "監査報告";

      $location = StandarisasiAuditChecklist::orderBy('lokasi', 'asc')
      ->select('lokasi')
      ->distinct()
      ->get();

      $category = StandarisasiAuditChecklist::orderBy('kategori', 'asc')
      ->select('kategori')
      ->distinct()
      ->get();

      $auditor = StandarisasiAuditIso::orderBy('auditor_name', 'asc')
      ->select('auditor_name')
      ->distinct()
      ->get();

      return view('cpar.report_check_audit_iso', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $location,
        'category' => $category,
        'auditor' => $auditor

      ))->with('page', 'Audit ISO');
    }

    public function check_audit_report_new($kategori, $lokasi, $auditor, $tanggal)
    {
      $title = "Audit Report";
      $title_jp = "監査報告";

      return view('cpar.report_check_audit_iso_new', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $lokasi,
        'category' => $kategori,
        'auditor' => $auditor,
        'audit_date' => $tanggal

      ))->with('page', 'Audit ISO');
    }

    public function fetch_audit_report(Request $request)
    {
      try {
      
        $lokasi = $request->get("location");
        $kategori = $request->get("category");
        $auditor = $request->get("auditor");
        $tanggal = $request->get("date");

        $query = 'SELECT * FROM standarisasi_audit_isos where deleted_at is null and kategori = "'.$kategori.'" and lokasi = "'.$lokasi.'" and auditor_name = "'.$auditor.'" and tanggal = "'.$tanggal.'" order by status desc, id asc';
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

    public function fetchPointAudit(Request $request)
    {
      try {
        $audit = DB::SELECT('SELECT * FROM `standarisasi_audit_checklists` where kategori = "'.$request->get('kategori').'" and lokasi = "'.$request->get('lokasi').'" and standarisasi_audit_checklists.deleted_at is null');

        $response = array(
          'status' => true,
          'lists' => $audit
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

    public function destroyPointCheck($id,$kategori,$lokasi)
    {
        $audit = StandarisasiAuditChecklist::find($id);
        $audit->delete();

        if ($audit) {
          return redirect('/index/audit_iso/point_check/'.$kategori.'/'.$lokasi)
          ->with('status', 'Point Question has been deleted.')
          ->with('page', 'Point Question Audit ISO');
        }else{
          return redirect('/index/audit_iso/point_check/'.$kategori.'/'.$lokasi)
          ->with('error', 'Point Question failed to delete.')
          ->with('page', 'Point Question Audit ISO');
        }
    }

    public function inputPointCheck(Request $request)
    {
      try {

        $id_user = Auth::id();

        $point_check = StandarisasiAuditChecklist::create([
          'kategori' => $request->get('kategori'),
          'lokasi' => $request->get('lokasi'),
          'klausul' => $request->get('klausul'),
          'point_judul' => $request->get('point_judul'),
          'point_question' => $request->get('point_question'),
          'created_by' => $id_user
        ]);

        $response = array(
          'status' => true,
          'message'=> 'Success Input Data'
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

    public function getPointCheck(Request $request)
    {
      try {

        $point_check = StandarisasiAuditChecklist::find($request->get('id'));

        $response = array(
          'status' => true,
          'lists' => $point_check
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

    public function updatePointCheck(Request $request)
    {
      try {

        $id_user = Auth::id();

        $point_check = StandarisasiAuditChecklist::find($request->get('id'));
        $point_check->klausul = $request->get('klausul');
        $point_check->point_judul = $request->get('point_judul');
        $point_check->point_question = $request->get('point_question');
        $point_check->save();

        $response = array(
          'status' => true,
          'message'=> 'Success Update Data'
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

    public function inputAuditIso(Request $request)
    {
      try {
        $id_user = Auth::id();
            $tujuan_upload = 'files/audit_iso';
            if (count($request->file('fileData')) > 0) {
              $file = $request->file('fileData');
              $filename = md5($request->input('id_point').$request->input('auditor_id').date('YmdHisa')).'.'.$request->input('extension');
              $file->move($tujuan_upload,$filename);

              StandarisasiAuditIso::create([
                  'tanggal' => $request->input('tanggal'),
                  'kategori' => $request->input('kategori'),
                  'auditor_id' => $request->input('auditor_id'),
                  'auditor_name' => $request->input('auditor_name'),
                  'auditee' => $request->input('auditee'),
                  'auditee_name' => $request->input('auditee_name'),
                  'lokasi' => $request->input('lokasi'),
                  'klausul' => $request->input('klausul'),
                  'point_judul' => $request->input('point_judul'),
                  'point_question' => $request->input('point_question'),
                  'status' => $request->input('status'),
                  'note' => $request->input('note'),
                  'foto' => $filename,
                  'created_by' => $id_user
              ]);
            }else{

              StandarisasiAuditIso::create([
                  'tanggal' => $request->input('tanggal'),
                  'kategori' => $request->input('kategori'),
                  'auditor_id' => $request->input('auditor_id'),
                  'auditor_name' => $request->input('auditor_name'),
                  'auditee' => $request->input('auditee'),
                  'auditee_name' => $request->input('auditee_name'),
                  'lokasi' => $request->input('lokasi'),
                  'klausul' => $request->input('klausul'),
                  'point_judul' => $request->input('point_judul'),
                  'point_question' => $request->input('point_question'),
                  'status' => $request->input('status'),
                  'note' => $request->input('note'),
                  'created_by' => $id_user
              ]);
            }

            $response = array(
                'status' => true
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

    // AUDIT ISO STANDARISASI

    public function audit_data() {
     
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      return view('audit.audit_data', array(
       'emp' => $emp_id,
       'employee' => $employee
      ))->with('page', 'Audit Data');
    }

    public function fetch_audit_all(Request $request)
    {
      $tanggal = $request->get("tanggal");
      $category = $request->get('category');

      if ($tanggal == null) {
        $tgl = '';
      }
      else{
        $tgl = "and auditor_date = '".$tanggal."'"; 
      }

      $query = "SELECT * FROM audit_alls where deleted_at is null AND auditor_jenis = '".$category."' ".$tgl." order by id desc";
      $detail = db::select($query);

      $response = array(
        'status' => true,
        'lists' => $detail,
      );
      return Response::json($response);
    }


    public function audit_kanban()
    {
      $title = "Audit Kanban";
      $title_jp = "かんばん監査";

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      $location = StandarisasiAuditChecklist::orderBy('lokasi', 'asc')
      ->select('lokasi')
      ->distinct()
      ->where('kategori','=','kanban')
      ->get();

      $category = StandarisasiAuditChecklist::orderBy('kategori', 'asc')
      ->select('kategori')
      ->distinct()
      ->where('kategori','=','kanban')
      ->get();

      $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

      return view('audit.create_audit', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee' => $emp,
        'location' => $location,
        'category' => $category,
        'auditee' => $auditee
      ))->with('page', 'Audit Kanban');
    }

    public function audit_mis()
    {
      $title = "Audit MIS";
      $title_jp = "かんばん監査";

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      $location = StandarisasiAuditChecklist::orderBy('lokasi', 'asc')
      ->select('lokasi')
      ->distinct()
      ->where('kategori','=','Audit_MIS')
      ->get();

      $category = StandarisasiAuditChecklist::orderBy('kategori', 'asc')
      ->select('kategori')
      ->distinct()
      ->where('kategori','=','Audit_MIS')
      ->get();

      $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like 'Manager%')");

      return view('audit.create_audit_mis', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee' => $emp,
        'location' => $location,
        'category' => $category,
        'auditee' => $auditee
      ))->with('page', 'Audit MIS');
    }

    public function audit_point_check() {
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      return view('audit.audit_point_check', array(
       'emp' => $emp_id,
       'employee' => $employee
      ))->with('page', 'Audit');
    }

    public function fetchKategoriLokasiAudit(Request $request)
    {
      try {

        $category = $request->get('category');

        $audit = DB::SELECT("SELECT DISTINCT(kategori),lokasi FROM `standarisasi_audit_checklists` where standarisasi_audit_checklists.deleted_at is null AND kategori = '".$category."'");

        $response = array(
          'status' => true,
          'lists' => $audit
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

    public function fetchHasilAuditAll(Request $request)
    {
      try {

        $category = $request->get('category');


        $audit = DB::SELECT("SELECT DISTINCT tanggal, kategori, auditor_id, auditor_name, lokasi, auditee, auditee_name from `standarisasi_audit_isos` where standarisasi_audit_isos.deleted_at is null and kategori  = '".$category."'");

        $response = array(
          'status' => true,
          'lists' => $audit
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

    public function check_audit_report_all()
    {
      $title = "Audit Report";
      $title_jp = "監査報告";

      $cat = $_GET['category'];

      $location = StandarisasiAuditChecklist::orderBy('lokasi', 'asc')
      ->select('lokasi')
      ->distinct()
      ->where('kategori','=',$cat)
      ->get();

      $category = StandarisasiAuditChecklist::orderBy('kategori', 'asc')
      ->select('kategori')
      ->distinct()
      ->where('kategori','=',$cat)
      ->get();

      $auditor = StandarisasiAuditIso::orderBy('auditor_name', 'asc')
      ->select('auditor_name')
      ->distinct()
      ->where('kategori','=',$cat)
      ->get();

      return view('audit.report_audit', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $location,
        'category' => $category,
        'auditor' => $auditor

      ))->with('page', 'Audit');
    }

    public function check_audit_report_new_all($kategori, $lokasi, $auditor, $tanggal)
    {
      $title = "Audit Report";
      $title_jp = "監査報告";

      return view('cpar.report_check_audit_iso_new', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $lokasi,
        'category' => $kategori,
        'auditor' => $auditor,
        'audit_date' => $tanggal

      ))->with('page', 'Audit');
    }

    public function fetch_audit_report_all(Request $request)
    {
      try {
      
        $lokasi = $request->get("location");
        $kategori = $request->get("category");
        $auditor = $request->get("auditor");
        $tanggal = $request->get("date");

        $query = 'SELECT * FROM standarisasi_audit_isos where deleted_at is null and kategori = "'.$kategori.'" and lokasi = "'.$lokasi.'" and auditor_name = "'.$auditor.'" and tanggal = "'.$tanggal.'" order by status desc, id asc';
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


    public function audit_create_checklist_all($id)
    {
      $audit = StandarisasiAuditIso::find($id);

      $emp = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position', 'department')->first();

      return view('audit.audit_laporan', array(
        'audit' => $audit,
        'employee' => $emp
      ))->with('page', 'Form Audit');
    }

    public function audit_post_create_checklist_all(Request $request)
    {
      try {
        $id_user = Auth::id();

        $audits = AuditAll::create([
           'auditor' => $request->get('auditor'),
           'auditor_name' => $request->get('auditor_name'),
           'auditor_date' => $request->get('auditor_date'),
           'auditor_jenis' => $request->get('auditor_jenis'),
           'auditor_lokasi' => $request->get('auditor_lokasi'),
           'auditor_permasalahan' => $request->get('auditor_permasalahan'),
           'auditor_bukti' => $request->get('auditor_bukti'),
           'auditee' => $request->get('auditee'),
           'auditee_name' => $request->get('auditee_name'),
           'auditee_due_date' => $request->get('auditee_due_date'),
           'posisi' => 'auditee',
           'created_by' => $id_user
        ]);

        $audits->save(); 

        $data3 = StandarisasiAuditIso::where('id', $request->get('id_checklist'))
        ->update(['status_ditangani' => 'true']);

        $mailto = "select distinct employees.name,email from audit_alls join employees on audit_alls.auditee = employees.employee_id join users on employees.employee_id = users.username where audit_alls.id = '".$audits->id."'";
        $mails = DB::select($mailto);

        foreach($mails as $mail){
          $mailtoo = $mail->email;
        }
      
        $isimail = "select * FROM audit_alls where audit_alls.id = ".$audits->id;
        $audits = db::select($isimail);

        Mail::to($mailtoo)->cc(['aditya.agassi@music.yamaha.com'])->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($audits, 'audit_all'));

        $response = array(
          'status' => true,
          'datas' => "Berhasil"
        );

        return Response::json($response);

     } catch (QueryException $e){
        $response = array(
             'status' => false,
             'datas' => $e->getMessage()
        );
        return Response::json($response);
       }
    }

    public function audit_response_all($id){
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

      $audit = AuditAll::find($id);

      $employee = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'name', 'position')->first();

      if ($audit->posisi == 'auditee' || $audit->posisi == 'auditor_final') {
        return view('audit.audit_response', array(
          'audit' => $audit,
          'employee' => $employee,
        ))->with('page', 'Audit');
      }
      else{
        return redirect('index/audit_all');
      }
    }

    public function update_response_all(Request $request, $id)
    {
      try {
        $audit = AuditAll::find($id);
        $audit->auditor_penyebab = $request->get('auditor_penyebab');
        $audit->auditee_perbaikan = $request->get('auditee_perbaikan');
        $audit->auditee_pencegahan = $request->get('auditee_pencegahan');
        $audit->auditee_biaya = $request->get('auditee_biaya');
        $audit->auditee_date = date('Y-m-d');
        $audit->save();

        return redirect('/index/audit/response/'.$audit->id)->with('status', 'Data has been updated.')->with('page', 'Audit All');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Already exist.')->with('page', 'Audit All');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Audit All');
        }
      }
    }


    public function sendemailpenanganan_all(Request $request,$id)
    {
      $id_user = Auth::id();
      $auditee = AuditAll::find($id);

      if ($auditee->posisi == "auditee") {
        $auditee->posisi = "auditor_final";    
        $auditee->save();      

        $mailto = "select distinct employees.name,email from audit_alls join employees on audit_alls.auditor = employees.employee_id join users on employees.employee_id = users.username where audit_alls.id = '".$auditee->id."'";
        $mails = DB::select($mailto);
      }

      foreach($mails as $mail){
        $mailtoo = $mail->email;
      }

      $isimail = "select * FROM audit_alls where audit_alls.id = ".$auditee->id;
      $audits = db::select($isimail);

      Mail::to($mailtoo)->cc(['ali.murdani@music.yamaha.com'])->send(new SendEmail($audits, 'audit_all'));
      return redirect('/index/audit/response/'.$id)->with('status', 'Audit Has Been Sent')->with('page', 'Audit');
    }

    public function print_audit_all($id)
    {
      $audit = AuditAll::find($id);

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'potrait');
      $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

      $pdf->loadView('audit.print_audit', array(
        'audit' => $audit,
      ));

      return $pdf->stream("Form Audit.pdf");
    }


    //Monitoring Audit Kanban

    public function monitoring_audit_kanban(){

      return view('cpar.monitoring_audit_kanban',  
        array(
            'title' => 'Monitoring Audit Kanban', 
            'title_jp' => '監査かんばんに対する監視',
          )
        )->with('page', 'Audit Kanban');
    }

    public function fetch_monitoring_audit_kanban(Request $request){

        $datefrom = date("Y-m-d",  strtotime('-30 days'));
        $dateto = date("Y-m-d");

        $last = AuditAll::where('posisi', '<>', 'auditor_final')
        ->orderBy('auditor_date', 'asc')
        ->select(db::raw('date(auditor_date) as auditor_date'))
        ->first();

        if(strlen($request->get('datefrom')) > 0){
          $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }else{
          if($last){
            $tanggal = date_create($last->auditor_date);
            $now = date_create(date('Y-m-d'));
            $interval = $now->diff($tanggal);
            $diff = $interval->format('%a%');

            if($diff > 30){
              $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

            $kate = 'and audit_all.posisi in'.$kat;
        }else{
            $kate = '';
        }

        $data = DB::select("SELECT
          date_format(auditor_date, '%a, %d %b %Y') AS tanggal,
          sum( CASE WHEN deleted_at IS null AND `posisi` = 'auditee' THEN 1 ELSE 0 END ) AS `penanganan`,
          sum( CASE WHEN deleted_at IS null AND `posisi` = 'auditor_final' THEN 1 ELSE 0 END ) AS `close`
          FROM
          audit_alls
          WHERE 
          deleted_at is null and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate."
          GROUP BY
          auditor_date
        ");

        $year = date('Y');

        $response = array(
          'status' => true,
          'datas' => $data,
          'year' => $year
        );

        return Response::json($response);
    }

    public function detail_monitoring_audit_kanban(Request $request){

      $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      $datefrom = null;
      $dateto = null;

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      if ($datefrom != null && $dateto != null) {
          $df = 'and audit_alls.auditor_date between "'.$datefrom.'" and "'.$dateto.'"';
      }else{
          $df = '';
      }

      $status = $request->get('status');

      if ($status != null) {

          if ($status == "Close") {
            $status = "auditor_final";
          }

          if ($status == "Penanganan") {
            $status = "auditee";
          }

          $stat = 'and audit_alls.posisi = "'.$status.'"';

      }else{
          $stat = '';
      }


      $query = "select audit_alls.* FROM audit_alls where audit_alls.deleted_at is null and auditor_date = '".$tgl."' ".$stat." ".$df."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->addColumn('action', function($detail){
        $id = $detail->id;
        return '<a href="../audit/print/'.$id.'" class="btn btn-warning btn-xs" target="_blank">Report</a>';
      })

      ->editColumn('auditor_date', function($detail){
        return date('d F Y', strtotime($detail->auditor_date));
      })

      ->editColumn('posisi', function($detail){
          if($detail->posisi == "auditee"){
            return '<label class="label label-warning"> Pembuatan Penanganan </label>';
          }
          else if($detail->posisi == "auditor_final"){
            return '<label class="label label-success"> Closed </label>';
          }
      })


      ->rawColumns(['action' => 'action','posisi' => 'posisi'])
      ->make(true);
  }


  public function fetch_table_audit_kanban(Request $request)
    {

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = AuditAll::where('posisi', '<>', 'auditor_final')
      ->orderBy('auditor_date', 'asc')
      ->select(db::raw('date(auditor_date) as auditor_date'))
      ->first();

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }else{
        if($last){
          $tanggal = date_create($last->auditor_date);
          $now = date_create(date('Y-m-d'));
          $interval = $now->diff($tanggal);
          $diff = $interval->format('%a%');

          if($diff > 30){
            $datefrom = date('Y-m-d', strtotime($last->auditor_date));
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

          $kate = 'and audit_alls.posisi in'.$kat;
      }else{
          $kate = 'and audit_alls.posisi not in ("auditor_final")';
      }

      $data = db::select("select * from audit_alls where audit_alls.deleted_at is null and auditor_date between '".$datefrom."' and '".$dateto."' ".$kate." ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }

}