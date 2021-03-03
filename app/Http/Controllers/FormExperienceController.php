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
use App\FormFailure;
use App\FormFailureAttendance;
use App\Department;
use App\EmployeeSync;

class FormExperienceController extends Controller
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

    //Get Data
    
    public function index()
    {	
    	$title = 'Form Permasalahan & Kegagalan';
		  $title_jp = '問題・失敗のフォーム';

      $departments = Department::select('departments.id', 'departments.department_name')->get();

		return view('form.failure.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
      'departments' => $departments,
		))->with('page', 'Form Permasalahan & Kegagalan');
    }

    function filter_form(Request $request)
    {
        // $detail_table = DB::table('form_failures')
        // ->leftjoin('employee_syncs','form_failures.employee_id','=','employee_syncs.employee_id')
        // ->leftjoin('form_failure_attendances','form_failures.id','=','form_failure_attendances.form_id')
        // ->select('form_failures.id','form_failures.employee_id','form_failures.employee_name','form_failures.tanggal_kejadian','form_failures.lokasi_kejadian','form_failures.equipment','form_failures.grup_kejadian','form_failures.kategori','form_failures.judul','form_failures.loss','form_failures.kerugian', DB::raw('count(form_failure_attendances.id) as jumlah'))
        // ->whereNull('form_failures.deleted_at')
        // ->groupBy('form_failures.id','form_failures.employee_id','form_failures.employee_name','form_failures.tanggal_kejadian','form_failures.lokasi_kejadian','form_failures.equipment','form_failures.grup_kejadian','form_failures.kategori','form_failures.judul','form_failures.loss','form_failures.kerugian');

        // if(strlen($request->get('department_id')) > 0){
        //   $detail_table = $detail_table->where('form_failures.department', '=', $request->get('department_id'));
        // }

        // $detail_table = $detail_table->orderBy('form_failures.id', 'DESC');
        // $details = $detail_table->get();

      $details = DB::SELECT('
        SELECT
        form_failures.id,
        form_failures.employee_id,
        form_failures.employee_name,
        form_failures.tanggal_kejadian,
        form_failures.lokasi_kejadian,
        form_failures.equipment,
        form_failures.grup_kejadian,
        form_failures.kategori,
        form_failures.judul,
        form_failures.loss,
        form_failures.kerugian,
        form_failures.file,
        target_sosialisasi.jml,
        count( form_failure_attendances.id ) AS jumlah 
      FROM
        form_failures
        LEFT JOIN form_failure_attendances ON form_failures.id = form_failure_attendances.form_id 
        LEFT JOIN (select `group` ,count(employee_id) as jml from employee_syncs where end_date is null and `group` is not null group by `group`) as target_sosialisasi on target_sosialisasi.group = SPLIT_STRING(lokasi_kejadian, "_", 2)
      GROUP BY
        form_failures.id,
        form_failures.employee_id,
        form_failures.employee_name,
        form_failures.tanggal_kejadian,
        form_failures.lokasi_kejadian,
        form_failures.equipment,
        form_failures.grup_kejadian,
        form_failures.kategori,
        form_failures.judul,
        form_failures.loss,
        form_failures.kerugian,
        form_failures.file,
        target_sosialisasi.jml
      ORDER by id desc
        '
        );

        return DataTables::of($details)

        ->editColumn('tanggal_kejadian',function($details){
            return date('Y-m', strtotime($details->tanggal_kejadian));
          })

        ->editColumn('section',function($details){
            $detail = explode("_", $details->lokasi_kejadian);

            return $detail[0];
          })

        ->addColumn('grup',function($details){
            $detail = explode("_", $details->lokasi_kejadian);

            return $detail[1];
        })

        ->editColumn('kerugian',function($details){
            return $details->kerugian;
          })

        ->addColumn('target_sosialisasi',function($details){
          if ($details->jml != null) {
            return $details->jml.' Orang';
          }else{
            return '-';
          }
        })

        ->addColumn('jumlah_sosialisasi',function($details){
          if ($details->jml != null) {
            if ($details->jumlah < $details->jml) {
              return '<span style="color:red">'.$details->jumlah.' Orang</span>';
            }
            else{
              return '<span style="color:green">'.$details->jumlah.' Orang</span>';
            }
          }else{
            return $details->jumlah.' Orang';
          }
        })

        ->addColumn('action', function($details){
          $id = $details->id;

          $user = strtoupper(Auth::user()->username);

          if (Auth::user()->role_code == "MIS" || $details->employee_id == $user) {
            return '
              <a href="form_experience/edit/'.$id.'" class="btn btn-primary btn-md"><i class="fa fa-pencil"></i></a>
              <a target="_blank" href="form_experience/print/'.$id.'" class="btn btn-warning btn-md"><i class="fa fa-file-pdf-o"></i></a>
            ';
          }
          else{
            return '
              <a href="form_experience/print/'.$id.'" class="btn btn-warning btn-md"><i class="fa fa-file-pdf-o"></i></a>
            ';
          }
        })

        ->addColumn('sosialisasi', function($details){
          $id = $details->id;

          $user = strtoupper(Auth::user()->username);

            return '
              <a href="javascript:void(0)" data-toggle="modal" class="btn btn-md btn-success" onClick="sosialisasi('.$id.')"><i class="fa fa-user"></i> <i class="fa fa-exchange"></i> <i class="fa fa-users"></i></a>
            ';
        })

        ->editColumn('file', function ($details)
        {
            $fl = "";

            if ($details->file != null)
            {
                $fl .= '<a href="../files/kegagalan/' . $details->file . '" target="_blank" class="fa fa-paperclip"></a>';
            }
            else
            {
                $fl = '-';
            }
            return $fl;
        })

        ->rawColumns(['action' => 'action','sosialisasi' => 'sosialisasi','jumlah_sosialisasi' => 'jumlah_sosialisasi','file' => 'file'])
        ->make(true);
    }

    //Buat

    public function create()
    {
        $title = 'Form Permasalahan & Kegagalan';
        $title_jp = '問題・失敗のフォーム';

        $sections = db::select("select DISTINCT department, section, `group` from employee_syncs
        where department is not null
        and section is not null
        and grade_code not like '%L%'
        order by department, section, `group` asc");

        $leader = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
        where end_date is null
        and (position like 'Leader%' or position like '%Staff%' or position like '%Chief%' or position like '%Foreman%')");

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        return view('form.failure.create', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
            'sections' => $sections,
            'leaders' => $leader
        ))->with('page', 'Form Permasalahan & Kegagalan');
    }

    public function post_form(Request $request)
    {
         try {
              $id_user = Auth::id();
              $file_name = "";

              // var_dump($request->file('file_datas'));die();

              if (count($request->file('file_datas')) > 0) {
                
                  $file = $request->file('file_datas');

                  $nama = $file->getClientOriginalName();
                  $filename = pathinfo($nama, PATHINFO_FILENAME);
                  $extension = pathinfo($nama, PATHINFO_EXTENSION);

                  $file_name = $filename.'_'.date('YmdHis').'.'.$extension;
                  $file->move('files/kegagalan/', $file_name);
              } 
              else {
                  $file_name = null;
              }

              $date_request = date('Y-m-01', strtotime($request->get('tanggal_kejadian')));

              $forms = FormFailure::create([
                   'employee_id' => $request->get('employee_id'),
                   'employee_name' => $request->get('employee_name'),
                   'kategori' => $request->get('kategori'),
                   'tanggal_kejadian' => $date_request,
                   'lokasi_kejadian' => $request->get('lokasi_kejadian'),
                   'equipment' => $request->get('equipment'),
                   'grup_kejadian' => $request->get('grup_kejadian'),
                   'judul' => $request->get('judul'),
                   'loss' => $request->get('loss'),
                   'kerugian' => $request->get('kerugian'),
                   'deskripsi' => $request->get('deskripsi'),
                   'penanganan' => $request->get('penanganan'),
                   'tindakan' => $request->get('tindakan'),
                   'file' => $file_name,
                   'created_by' => $id_user
              ]);

              $forms->save();

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

    public function update($id)
    {
        $form_failures = FormFailure::find($id);

        $sections = db::select("select DISTINCT department, section, `group` from employee_syncs
        where department is not null
        and section is not null
        and grade_code not like '%L%'
        order by department, section, `group` asc");

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $emp = EmployeeSync::where('employee_id', $form_failures->employee_id)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        return view('form.failure.edit', array(
            'employee' => $emp,
            'sections' => $sections,
            'form_failures' => $form_failures
        ))->with('page', 'Form Permasalahan & Kegagalan');
    }

    public function update_form(Request $request)
    {
         try {
              $id_user = Auth::id();

              $data_form = FormFailure::where('id',$request->get('id'))->first();

              $file_name = "";

              if (count($request->file('file_datas')) > 0) {
                
                  $file = $request->file('file_datas');

                  $nama = $file->getClientOriginalName();
                  $filename = pathinfo($nama, PATHINFO_FILENAME);
                  $extension = pathinfo($nama, PATHINFO_EXTENSION);

                  $file_name = $filename.'_'.date('YmdHis').'.'.$extension;
                  $file->move('files/kegagalan/', $file_name);
              } 
              else {
                  $file_name = $data_form->file;
              }

              $date_request = date('Y-m-01', strtotime($request->get('tanggal_kejadian')));

              $forms = FormFailure::where('id',$request->get('id'))
              ->update([
                   'kategori' => $request->get('kategori'),
                   'tanggal_kejadian' => $date_request,
                   'lokasi_kejadian' => $request->get('lokasi_kejadian'),
                   'equipment' => $request->get('equipment'),
                   'grup_kejadian' => $request->get('grup_kejadian'),
                   'judul' => $request->get('judul'),
                   'loss' => $request->get('loss'),
                   'kerugian' => $request->get('kerugian'),
                   'deskripsi' => $request->get('deskripsi'),
                   'penanganan' => $request->get('penanganan'),
                   'tindakan' => $request->get('tindakan'),
                   'file' => $file_name,
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

    public function print_form($id){

        $form_failures = FormFailure::find($id);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('Legal', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        
        $pdf->loadView('form.failure.print', array(
          'form_failures' => $form_failures,
        ));

        return $pdf->stream("Form ".$form_failures->judul.".pdf");
    }

    public function get_nik(Request $request)
    {
      $nik = $request->nik;
      $query = "SELECT name,section,department FROM employee_syncs where employee_id = '$nik'";
      $nama = DB::select($query);
      return json_encode($nama);
    }

    public function fetchChart(Request $request){
      $detail = db::select("SELECT
          employee_syncs.department AS department,
          COUNT( form_failures.id ) AS total 
        FROM
          `form_failures`
          LEFT JOIN employee_syncs ON form_failures.employee_id = employee_syncs.employee_id
          -- where employee_syncs.division in ('Production Division','Production Support Division') 
        GROUP BY
          department
      ");

      $response = array(
        'status' => true,
        'detail' => $detail
      );
      return Response::json($response);
    }

    public function fetchDetailChart(Request $request){
      $group = $request->get('group');

      $data = db::select("SELECT
        form_failures.id,
        form_failures.employee_id,
        form_failures.employee_name,
        form_failures.tanggal_kejadian,
        form_failures.lokasi_kejadian,
        form_failures.equipment,
        form_failures.grup_kejadian,
        form_failures.kategori,
        form_failures.judul,
        form_failures.loss,
        form_failures.kerugian
      FROM
        form_failures
        JOIN employee_syncs ON form_failures.employee_id = employee_syncs.employee_id
      WHERE
        employee_syncs.department = '".$group."'");

    $response = array(
      'status' => true,
      'data' => $data
    );
    return Response::json($response);
  }


  public function scanEmployee(Request $request){
    $id = Auth::id();
    $employee = db::table('employees')->where('tag', '=', $request->get('tag'))->first();

    if($employee == null){
      $response = array(
        'status' => false,
        'message' => 'ID Card not found'
      );
      return Response::json($response);
    }

    try{

      $form_detail = FormFailureAttendance::where('form_id', '=', $request->get('form_id'))
      ->where('employee_id', '=', $employee->employee_id)
      ->first();
      
      if($form_detail != null){
          $response = array(
            'status' => false,
            'message' => 'Already attended',
          );
          return Response::json($response);
      }
      else{

        $form_detail = new FormFailureAttendance([
          'form_id' => $request->get('form_id'),
          'employee_tag' => $employee->tag,
          'employee_id' => $employee->employee_id,
          'status' => 'ok',
          'attend_time' => date('Y-m-d H:i:s'),
          'created_by' => $id
        ]);
      }
      $form_detail->save();

    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }

    $response = array(
      'status' => true,
      'message' => 'Attendance success'
    );
    return Response::json($response);
  }


  public function fetchAttendance(Request $request){

    $form = FormFailure::where('form_failures.id', '=', $request->get('id'))
    ->select('form_failures.id', 'form_failures.kategori', 'form_failures.judul')
    ->first();

    $form_details = FormFailureAttendance::where('form_failure_attendances.form_id', '=', $request->get('id'))
    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'form_failure_attendances.employee_id')
    ->select('form_failure_attendances.id', 'form_failure_attendances.form_id', 'form_failure_attendances.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'form_failure_attendances.status')
    ->orderBy('form_failure_attendances.id', 'asc')
    ->get();

    $response = array(
      'status' => true,
      'form' => $form,
      'form_details' => $form_details
    );
    return Response::json($response);
  }

}
