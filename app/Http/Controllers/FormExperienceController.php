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
        $detail_table = DB::table('form_failures')
        ->leftjoin('employee_syncs','form_failures.employee_id','=','employee_syncs.employee_id')
        ->select('form_failures.*')
        ->whereNull('form_failures.deleted_at');

        if(strlen($request->get('department_id')) > 0){
          $detail_table = $detail_table->where('form_failures.department', '=', $request->get('department_id'));
        }

        $detail_table = $detail_table->orderBy('form_failures.id', 'DESC');
        $details = $detail_table->get();

        return DataTables::of($details)

        ->editColumn('tanggal_kejadian',function($details){
            return date('Y-m', strtotime($details->tanggal_kejadian));
          })

        ->editColumn('lokasi_kejadian',function($details){
            return str_replace('_', ' - ', $details->lokasi_kejadian);
          })

        ->editColumn('kerugian',function($details){
            if ($details->kerugian != null) {
              return "$ ".number_format($details->kerugian,0,"",".");
            }
            else {
              return $details->kerugian;
            }
          })

        ->addColumn('action', function($details){
          $id = $details->id;
          if ($details->created_by == Auth::id() || Auth::user()->role_code == "MIS" || Auth::id() == "13") {
            return '
              <a href="form_experience/edit/'.$id.'" class="btn btn-primary btn-xs">Edit</a>
              <a href="form_experience/print/'.$id.'" class="btn btn-warning btn-xs">Detail PDF</a>
            ';
          }
          else{
            return '
              <a href="form_experience/print/'.$id.'" class="btn btn-warning btn-xs">Detail PDF</a>
            ';
          }
        })

        ->rawColumns(['action' => 'action'])
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
      $detail = db::select("SELECT employee_syncs.department,COUNT(form_failures.id) as total FROM `form_failures` join employee_syncs on form_failures.employee_id = employee_syncs.employee_id group by employee_syncs.department");

      $response = array(
        'status' => true,
        'detail' => $detail
      );
      return Response::json($response);
    }

}
