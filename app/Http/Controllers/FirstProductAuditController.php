<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\FirstProductAudit;
use App\FirstProductAuditDetail;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class FirstProductAuditController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->proses = ['Pengerjaan Kunci Sub Assy',];
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$first_product_audit = FirstProductAudit::where('activity_list_id',$id)
            ->orderBy('first_product_audits.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('first_product_audit' => $first_product_audit,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('first_product_audit.index', $data
    		)->with('page', 'First Product Audit');
    }

    function list_proses($id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit = FirstProductAudit::where('activity_list_id',$id)
            ->orderBy('first_product_audits.id','desc')->get();

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $data = array('first_product_audit' => $first_product_audit,
                'departments' => $departments,
                'activity_name' => $activity_name,
                'activity_alias' => $activity_alias,
                'leader' => $leader,
                'foreman' => $foreman,
                'id' => $id,
                'id_departments' => $id_departments);
        return view('first_product_audit.list_proses', $data
          )->with('page', 'First Product Audit');
    }

    function show($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        $data = array('first_product_audit' => $first_product_audit,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('first_product_audit.view', $data
            )->with('page', 'First Product Audit');
    }

    public function destroy($id,$first_product_audit_id)
    {
      $first_product_audit = FirstProductAudit::find($first_product_audit_id);
      $first_product_audit->delete();

      return redirect('/index/first_product_audit/index/'.$id)
        ->with('status', 'First Product Audit has been deleted.')
        ->with('page', 'First Product Audit');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('first_product_audit.create', $data
            )->with('page', 'First Product Audit');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            FirstProductAudit::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'subsection' => $request->input('subsection'),
                'proses' => $request->input('proses'),
                'jenis' => $request->input('jenis'),
                'standar_kualitas' => $request->input('standar_kualitas'),
                'tool_check' => $request->input('tool_check'),
                'jumlah_cek' => $request->input('jumlah_cek'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/first_product_audit/list_proses/'.$id)
            ->with('page', 'First Product Audit')->with('status', 'New First Product Audit has been created.');
    }

    function edit($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'first_product_audit' => $first_product_audit,
                      'id' => $id);
        return view('first_product_audit.edit', $data
            )->with('page', 'First Product Audit');
    }

    function update(Request $request,$id,$first_product_audit_id)
    {
        try{
                $first_product_audit = FirstProductAudit::find($first_product_audit_id);
                $first_product_audit->activity_list_id = $id;
                $first_product_audit->department = $request->get('department');
                $first_product_audit->subsection = $request->get('subsection');
                $first_product_audit->proses = $request->get('proses');
                $first_product_audit->jenis = $request->get('jenis');
                $first_product_audit->standar_kualitas = $request->get('standar_kualitas');
                $first_product_audit->tool_check = $request->get('tool_check');
                $first_product_audit->jumlah_cek = $request->get('jumlah_cek');
                $first_product_audit->leader = $request->get('leader');
                $first_product_audit->foreman = $request->get('foreman');
                $first_product_audit->save();

            return redirect('/index/first_product_audit/list_proses/'.$id)->with('status', 'First Product Audit data has been updated.')->with('page', 'First Product Audit');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'First Product Audit already exist.')->with('page', 'First Product Audit');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'First Product Audit');
            }
          }
    }

    function details($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
            ->where('first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_details.id','desc')->get();

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        $proses = $first_product_audit->proses;
        $jenis = $first_product_audit->jenis;

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $leader2 = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $data = array( 'first_product_audit_details' => $first_product_audit_details,
                        'departments' => $departments,
                        'activity_name' => $activity_name,
                        'activity_alias' => $activity_alias,
                        'leader' => $leader,
                        'leader2' => $leader2,
                        'foreman' => $foreman,
                        'operator' => $operator,
                        'operator2' => $operator2,
                        'proses' => $proses,
                        'jenis' => $jenis,
                        'id' => $id,
                        'first_product_audit_id' => $first_product_audit_id,
                        'id_departments' => $id_departments);
        return view('first_product_audit.index', $data
          )->with('page', 'First Product Audit Detail');
    }

    function filter_first_product_detail(Request $request,$id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
                ->where('first_product_audit_details.first_product_audit_id',$first_product_audit_id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('first_product_audit_details.id','desc')
                ->get();
        }
        else{
            $first_product_audit_details = FirstProductAuditDetail::where('activity_list_id',$id)
            ->where('first_product_audit_details.first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_details.id','desc')->get();
        }

        $first_product_audit = FirstProductAudit::find($first_product_audit_id);
        $proses = $first_product_audit->proses;
        $jenis = $first_product_audit->jenis;

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $leader2 = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);

        $data = array(
                      'first_product_audit_details' => $first_product_audit_details,
                      'departments' => $departments,
                      'proses' => $proses,
                      'jenis' => $jenis,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'leader2' => $leader2,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'id' => $id,
                      'first_product_audit_id' => $first_product_audit_id,
                      'id_departments' => $id_departments);
        return view('first_product_audit.index', $data
            )->with('page', 'First Product Audit');
    }

    function store_details(Request $request,$id,$first_product_audit_id)
    {
            try{

              $tujuan_upload = 'data_file/cek_produk_pertama';
              $date = date('Y-m-d');

              $file = $request->file('foto_aktual');
              $nama_file = $file->getClientOriginalName();
              $file->getClientOriginalName();
              $file->move($tujuan_upload,$file->getClientOriginalName());

              $id_user = Auth::id();
              $first_product_audit_id = $request->get('first_product_audit_id');
              $activity_list_id = $request->get('activity_list_id');
                FirstProductAuditDetail::create([
                    'activity_list_id' => $request->get('activity_list_id'),
                    'first_product_audit_id' => $request->get('first_product_audit_id'),
                    'date' => $request->get('date'),
                    'auditor' => $request->get('auditor'),
                    'foto_aktual' => $nama_file,
                    'note' => $request->get('note'),
                    'pic' => $request->get('pic'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);

              // $response = array(
              //   'status' => true,
              // );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              // return Response::json($response);
              return redirect('/index/first_product_audit/details/'.$activity_list_id.'/'.$first_product_audit_id)->with('status', 'First Product Audit data has been updated.')->with('page', 'First Product Audit');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function getdetail(Request $request)
    {
         try{
            $detail = FirstProductAuditDetail::find($request->get("id"));
            $data = array('first_product_audit_detail_id' => $detail->id,
                          'first_product_audit_id' => $detail->first_product_audit_id,
                          'date' => $detail->date,
                          'proses' => $detail->first_product_audit->proses,
                          'jenis' => $detail->first_product_audit->jenis,
                          'auditor' => $detail->auditor,
                          'foto_aktual' => $detail->foto_aktual,
                          'note' => $detail->note,
                          'pic' => $detail->pic,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $first_product_audit){
            $error_code = $first_product_audit->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function update_details(Request $request,$id,$first_product_audit_detail_id)
    {
      try{
                $first_product_audit_id = $request->get('editfirst_product_audit_id');
                $activity_list_id = $request->get('editactivity_list_id');

                if($request->file('editfoto_aktual') != null){
                  $tujuan_upload = 'data_file/cek_produk_pertama/';
                  $date = date('Y-m-d');

                  $file = $request->file('editfoto_aktual');
                  $nama_file = $file->getClientOriginalName();
                  $file->getClientOriginalName();
                  $file->move($tujuan_upload,$file->getClientOriginalName());

                  $first_product_audit_detail = FirstProductAuditDetail::find($first_product_audit_detail_id);
                  $first_product_audit_detail->date = $request->get('editdate');
                  $first_product_audit_detail->pic = $request->get('editpic');
                  $first_product_audit_detail->foto_aktual = $nama_file;
                  $first_product_audit_detail->note = $request->get('editnote');
                  $first_product_audit_detail->save();
                }
                else{
                  $first_product_audit_detail = FirstProductAuditDetail::find($first_product_audit_detail_id);
                  $first_product_audit_detail->date = $request->get('editdate');
                  $first_product_audit_detail->pic = $request->get('editpic');
                  $first_product_audit_detail->foto_aktual = $request->get('foto_aktual_edit');
                  $first_product_audit_detail->note = $request->get('editnote');
                  $first_product_audit_detail->save();
                }

            return redirect('index/first_product_audit/details/'.$activity_list_id.'/'.$first_product_audit_id)
              ->with('page', 'First Product Audit Details')->with('status', 'First Product Audit Details has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'First Product Audit Details already exist.')->with('page', 'First Product Audit Details');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'First Product Audit Details');
            }
          }
    }

    public function destroy_details($id,$first_product_audit_detail_id)
    {
      $first_product_audit_details = FirstProductAuditDetail::find($first_product_audit_detail_id);
      $activity_list_id = $first_product_audit_details->activity_list_id;
      $first_product_audit_id = $first_product_audit_details->first_product_audit_id;
      $first_product_audit_details->delete();

      return redirect('index/first_product_audit/details/'.$activity_list_id.'/'.$first_product_audit_id)
              ->with('page', 'First Product Audit Details')->with('status', 'First Product Audit Details has been deleted.');     
    }
}
