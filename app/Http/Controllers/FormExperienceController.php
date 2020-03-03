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

        $detail_table = $detail_table->orderBy('form_failures.id', 'ASC');
        $details = $detail_table->get();

        return DataTables::of($details)

        ->editColumn('tanggal',function($details){
            return date('d F Y', strtotime($details->tanggal));
          })

        ->addColumn('action', function($details){
          $id = $details->id;
          return '<a href="form_experience/edit/'.$id.'" class="btn btn-primary btn-xs">Edit</a>';
        })

        ->rawColumns(['action' => 'action'])
        ->make(true);
    }

    //Buat

    public function create()
    {
        $title = 'Form Permasalahan & Kegagalan';
        $title_jp = '問題・失敗のフォーム';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        return view('form.failure.create', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp
        ))->with('page', 'Form Permasalahan & Kegagalan');
    }

    public function post_form(Request $request)
    {
         try {
              $id_user = Auth::id();

              $forms = FormFailure::create([
                   'employee_id' => $request->get('employee_id'),
                   'employee_name' => $request->get('employee_name'),
                   'kategori' => $request->get('kategori'),
                   'section' => $request->get('section'),
                   'department' => $request->get('department'),
                   'tanggal' => $request->get('tanggal'),
                   'judul' => $request->get('judul'),
                   'penyebab' => $request->get('penyebab'),
                   'penanganan' => $request->get('penanganan'),
                   'tindakan' => $request->get('tindakan'),
                   'created_by' => $id_user
              ]);

              $forms->save();

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

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();

        return view('form.failure.edit', array(
            'employee' => $emp,
            'form_failures' => $form_failures
        ))->with('page', 'Form Permasalahan & Kegagalan');
    }

    public function update_form(Request $request)
    {
         try {
              $id_user = Auth::id();

              $forms = FormFailure::where('id',$request->get('id'))
              ->update([
                   'kategori' => $request->get('kategori'),
                   'judul' => $request->get('judul'),
                   'penyebab' => $request->get('penyebab'),
                   'penanganan' => $request->get('penanganan'),
                   'tindakan' => $request->get('tindakan'),
                   'created_by' => $id_user
              ]);

              // $forms->save();

         } catch (QueryException $e){
              $response = array(
                   'status' => false,
                   'datas' => $e->getMessage()
              );
              return Response::json($response);
         }
    }

}
