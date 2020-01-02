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
use App\FirstProductAuditDaily;
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

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department = '".$departments."'";
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

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

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
                      'operator2' => $operator2,
                      'id' => $id,
                      'first_product_audit_id' => $first_product_audit_id,
                      'id_departments' => $id_departments);
        return view('first_product_audit.index', $data
            )->with('page', 'First Product Audit');
    }

    function store_details(Request $request,$id,$first_product_audit_id)
    {
            try{

              $id_user = Auth::id();
              $first_product_audit_id = $request->get('first_product_audit_id');
              $activity_list_id = $request->get('activity_list_id');
                FirstProductAuditDetail::create([
                    'activity_list_id' => $request->get('activity_list_id'),
                    'first_product_audit_id' => $request->get('first_product_audit_id'),
                    'date' => $request->get('date'),
                    'month' => $request->get('inputmonth'),
                    'auditor' => $request->get('auditor'),
                    'foto_aktual' => $request->get('foto_aktual'),
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
              return redirect('/index/first_product_audit/details/'.$activity_list_id.'/'.$first_product_audit_id)->with('status', 'First Product Audit data has been created.')->with('page', 'First Product Audit');
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
                          'month' => $detail->month,
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

                  $first_product_audit_detail = FirstProductAuditDetail::find($first_product_audit_detail_id);
                  $first_product_audit_detail->date = $request->get('editdate');
                  $first_product_audit_detail->month = $request->get('editmonth');
                  $first_product_audit_detail->pic = $request->get('editpic');
                  $first_product_audit_detail->foto_aktual = $request->get('editfoto_aktual');
                  $first_product_audit_detail->note = $request->get('editnote');
                  $first_product_audit_detail->save();
                

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

    function print_first_product_audit(Request $request,$id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($request->get('month') != null){
            $month = $request->get('month');
            $queryfirst_product_audit = "select *, first_product_audits.id as id_first_product_audit
                from first_product_audit_details
                join activity_lists on activity_lists.id = first_product_audit_details.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                and first_product_audit_details.deleted_at is null";
            $first_product_audit = DB::select($queryfirst_product_audit);
            $first_product_audit2 = DB::select($queryfirst_product_audit);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($first_product_audit2 as $first_product_audit2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $first_product_audit2->date;
            $foreman = $first_product_audit2->foreman;
            $approval_leader = $first_product_audit2->approval_leader;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $subsection = $first_product_audit2->subsection;
            $leader = $first_product_audit2->leader;
            if ($first_product_audit2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($first_product_audit2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $first_product_audit2->approved_date;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $proses = $first_product_audit2->proses;
            $jenis = $first_product_audit2->jenis;
            $standar_kualitas = $first_product_audit2->standar_kualitas;
            $tool_check = $first_product_audit2->tool_check;
            $jumlah_cek = $first_product_audit2->jumlah_cek;
        }
        if($first_product_audit == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'proses' => $proses,
                          'jenis' => $jenis,
                          'standar_kualitas' => $standar_kualitas,
                          'jml_null' => $jml_null,
                          'tool_check' => $tool_check,
                          'jumlah_cek' => $jumlah_cek,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'first_product_audit' => $first_product_audit,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('first_product_audit.print', $data
                )->with('page', 'First Product Audit');
        }
    }

    function print_first_product_audit_email($id,$first_product_audit_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        // if($request->get('month') != null){
            // $month = $request->get('month');
            $queryfirst_product_audit = "select *, first_product_audits.id as id_first_product_audit,first_product_audit_details.id as id_first_product_audit_details
                from first_product_audit_details
                join activity_lists on activity_lists.id = first_product_audit_details.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                and first_product_audit_details.deleted_at is null";
            $first_product_audit = DB::select($queryfirst_product_audit);
            $first_product_audit2 = DB::select($queryfirst_product_audit);
        // }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($first_product_audit2 as $first_product_audit2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $first_product_audit2->date;
            $foreman = $first_product_audit2->foreman;
            $approval_leader = $first_product_audit2->approval_leader;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $subsection = $first_product_audit2->subsection;
            $leader = $first_product_audit2->leader;
            if ($first_product_audit2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($first_product_audit2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $first_product_audit2->approved_date;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $proses = $first_product_audit2->proses;
            $jenis = $first_product_audit2->jenis;
            $standar_kualitas = $first_product_audit2->standar_kualitas;
            $tool_check = $first_product_audit2->tool_check;
            $jumlah_cek = $first_product_audit2->jumlah_cek;
            $id_first_product_audit = $first_product_audit2->id_first_product_audit;
        }
        if($first_product_audit == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'role_code' => Auth::user()->role_code,
                          'month' => $month,
                          'proses' => $proses,
                          'jenis' => $jenis,
                          'standar_kualitas' => $standar_kualitas,
                          'jml_null' => $jml_null,
                          'tool_check' => $tool_check,
                          'jumlah_cek' => $jumlah_cek,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'first_product_audit' => $first_product_audit,
                          'id_first_product_audit' => $id_first_product_audit,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('first_product_audit.print_email', $data
                )->with('page', 'First Product Audit');
        }
    }

    public function sendemail(Request $request,$id,$first_product_audit_id)
      {
          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_first_product_audit = "select *, first_product_audit_details.id as id_first_product_audit_details,first_product_audits.id as id_first_product_audit
                from first_product_audit_details
                join activity_lists on activity_lists.id = first_product_audit_details.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                and first_product_audit_details.deleted_at is null";
          $first_product_audit = DB::select($query_first_product_audit);
          $first_product_audit2 = DB::select($query_first_product_audit);
          $first_product_audit3 = DB::select($query_first_product_audit);

          // var_dump($first_product_audit3);

          if($first_product_audit2 != null){
            foreach($first_product_audit2 as $first_product_audit2){
                $foreman = $first_product_audit2->foreman;
                $id_first_product_audit_details = $first_product_audit2->id_first_product_audit_details;
                $send_status = $first_product_audit2->send_status;
              }

              foreach ($first_product_audit3 as $first_product_audit3) {
                    $laktivitas = FirstProductAuditDetail::find($first_product_audit3->id_first_product_audit_details);
                    $laktivitas->send_status = "Sent";
                    $laktivitas->send_date = date('Y-m-d');
                    $laktivitas->approval_leader = "Approved";
                    $laktivitas->approved_date_leader = date('Y-m-d');
                    $laktivitas->save();
              }

              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/first_product_audit/details/'.$id.'/'.$first_product_audit_id)->with('error', 'Data tidak tersedia.')->with('page', 'First Product Audit');
          }

          if($send_status == "Sent"){
            return redirect('/index/first_product_audit/details/'.$id.'/'.$first_product_audit_id)->with('error', 'Data pernah dikirim.')->with('page', 'First Product Audit');
          }
          elseif($first_product_audit != null){
              Mail::to($mail_to)->send(new SendEmail($first_product_audit, 'first_product_audit'));
              return redirect('/index/first_product_audit/details/'.$id.'/'.$first_product_audit_id)->with('status', 'Your E-mail has been sent.')->with('page', 'First Product Audit');
          }
          else{
            return redirect('/index/first_product_audit/details/'.$id.'/'.$first_product_audit_id)->with('error', 'Data tidak tersedia.')->with('page', 'First Product Audit');
          }
      }

      public function approval(Request $request,$id,$first_product_audit_id,$month)
      {
          $approve = $request->get('approve');
          foreach($approve as $approve){
            $first_product_audit = FirstProductAuditDetail::find($approve);
            $subsection = $first_product_audit->subsection;
            $month = substr($first_product_audit->date,0,7);
            $date = $first_product_audit->date;
            $first_product_audit->approval = "Approved";
            $first_product_audit->approved_date = date('Y-m-d');
            $first_product_audit->save();
          }
          return redirect('/index/first_product_audit/print_first_product_audit_email/'.$id.'/'.$first_product_audit_id.'/'.$month)->with('status', 'Approved.')->with('page', 'First Product Audit');
      }

    function daily($id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $first_product_audit_daily = FirstProductAuditDaily::where('activity_list_id',$id)
            ->where('first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_dailies.id','desc')->get();

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

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $data = array( 'first_product_audit_daily' => $first_product_audit_daily,
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
        return view('first_product_audit.index_daily', $data
          )->with('page', 'First Product Audit Daily');
    }

    function filter_first_product_daily(Request $request,$id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $first_product_audit_daily = FirstProductAuditDaily::where('activity_list_id',$id)
                ->where('first_product_audit_dailies.first_product_audit_id',$first_product_audit_id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('first_product_audit_dailies.id','desc')
                ->get();
        }
        else{
            $first_product_audit_daily = FirstProductAuditDaily::where('activity_list_id',$id)
            ->where('first_product_audit_dailies.first_product_audit_id',$first_product_audit_id)
            ->orderBy('first_product_audit_dailies.id','desc')->get();
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

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $data = array(
                      'first_product_audit_daily' => $first_product_audit_daily,
                      'departments' => $departments,
                      'proses' => $proses,
                      'jenis' => $jenis,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'leader2' => $leader2,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'id' => $id,
                      'first_product_audit_id' => $first_product_audit_id,
                      'id_departments' => $id_departments);
        return view('first_product_audit.index_daily', $data
            )->with('page', 'First Product Audit');
    }

    function store_daily(Request $request,$id,$first_product_audit_id)
    {
            try{

              $id_user = Auth::id();
              $first_product_audit_id = $request->get('first_product_audit_id');
              $activity_list_id = $request->get('activity_list_id');
                FirstProductAuditDaily::create([
                    'activity_list_id' => $request->get('activity_list_id'),
                    'first_product_audit_id' => $request->get('first_product_audit_id'),
                    'date' => $request->get('date'),
                    'auditor' => $request->get('auditor'),
                    'judgement' => $request->get('judgement'),
                    'note' => $request->get('note'),
                    'pic' => $request->get('pic'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);

              return redirect('/index/first_product_audit/daily/'.$activity_list_id.'/'.$first_product_audit_id)->with('status', 'First Product Audit data has been created.')->with('page', 'First Product Audit');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function getdaily(Request $request)
    {
         try{
            $detail = FirstProductAuditDaily::find($request->get("id"));
            $data = array('first_product_audit_detail_id' => $detail->id,
                          'first_product_audit_id' => $detail->first_product_audit_id,
                          'date' => $detail->date,
                          'proses' => $detail->first_product_audit->proses,
                          'jenis' => $detail->first_product_audit->jenis,
                          'auditor' => $detail->auditor,
                          'judgement' => $detail->judgement,
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

    function update_daily(Request $request,$id,$first_product_audit_detail_id)
    {
      try{
                $first_product_audit_id = $request->get('editfirst_product_audit_id');
                $activity_list_id = $request->get('editactivity_list_id');

                  $first_product_audit_detail = FirstProductAuditDaily::find($first_product_audit_detail_id);
                  $first_product_audit_detail->date = $request->get('editdate');
                  $first_product_audit_detail->pic = $request->get('editpic');
                  $first_product_audit_detail->judgement = $request->get('editjudgement');
                  $first_product_audit_detail->note = $request->get('editnote');
                  $first_product_audit_detail->save();
                

            return redirect('index/first_product_audit/daily/'.$activity_list_id.'/'.$first_product_audit_id)
              ->with('page', 'First Product Audit Daily')->with('status', 'First Product Audit Daily has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'First Product Audit Daily already exist.')->with('page', 'First Product Audit Daily');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'First Product Audit Daily');
            }
          }
    }

    public function destroy_daily($id,$first_product_audit_detail_id)
    {
      $first_product_audit_details = FirstProductAuditDaily::find($first_product_audit_detail_id);
      $activity_list_id = $first_product_audit_details->activity_list_id;
      $first_product_audit_id = $first_product_audit_details->first_product_audit_id;
      $first_product_audit_details->delete();

      return redirect('index/first_product_audit/daily/'.$activity_list_id.'/'.$first_product_audit_id)
              ->with('page', 'First Product Audit Daily')->with('status', 'First Product Audit Daily has been deleted.');     
    }

    function print_first_product_audit_daily(Request $request,$id,$first_product_audit_id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($request->get('month') != null){
            $month = $request->get('month');
            $queryfirst_product_audit = "select *, first_product_audits.id as id_first_product_audit
                from first_product_audit_dailies
                join activity_lists on activity_lists.id = first_product_audit_dailies.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_dailies.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_dailies.date,'%Y-%m') = '".$month."'
                and first_product_audit_dailies.deleted_at is null";
            $first_product_audit = DB::select($queryfirst_product_audit);
            $first_product_audit2 = DB::select($queryfirst_product_audit);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($first_product_audit2 as $first_product_audit2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $first_product_audit2->date;
            $foreman = $first_product_audit2->foreman;
            $approval_leader = $first_product_audit2->approval_leader;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $subsection = $first_product_audit2->subsection;
            $leader = $first_product_audit2->leader;
            if ($first_product_audit2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($first_product_audit2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $first_product_audit2->approved_date;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $proses = $first_product_audit2->proses;
            $jenis = $first_product_audit2->jenis;
            $standar_kualitas = $first_product_audit2->standar_kualitas;
            $tool_check = $first_product_audit2->tool_check;
            $jumlah_cek = $first_product_audit2->jumlah_cek;
        }
        if($first_product_audit == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'proses' => $proses,
                          'jenis' => $jenis,
                          'standar_kualitas' => $standar_kualitas,
                          'jml_null' => $jml_null,
                          'tool_check' => $tool_check,
                          'jumlah_cek' => $jumlah_cek,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'first_product_audit' => $first_product_audit,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('first_product_audit.print_daily', $data
                )->with('page', 'First Product Audit');
        }
    }

    function print_first_product_audit_email_daily($id,$first_product_audit_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        // if($request->get('month') != null){
            // $month = $request->get('month');
            $queryfirst_product_audit = "select *, first_product_audits.id as id_first_product_audit,first_product_audit_dailies.id as id_first_product_audit_details
                from first_product_audit_dailies
                join activity_lists on activity_lists.id = first_product_audit_dailies.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_dailies.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_dailies.date,'%Y-%m') = '".$month."'
                and first_product_audit_dailies.deleted_at is null";
            $first_product_audit = DB::select($queryfirst_product_audit);
            $first_product_audit2 = DB::select($queryfirst_product_audit);
        // }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($first_product_audit2 as $first_product_audit2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $first_product_audit2->date;
            $foreman = $first_product_audit2->foreman;
            $approval_leader = $first_product_audit2->approval_leader;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $subsection = $first_product_audit2->subsection;
            $leader = $first_product_audit2->leader;
            if ($first_product_audit2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($first_product_audit2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $first_product_audit2->approved_date;
            $approved_date_leader = $first_product_audit2->approved_date_leader;
            $proses = $first_product_audit2->proses;
            $jenis = $first_product_audit2->jenis;
            $standar_kualitas = $first_product_audit2->standar_kualitas;
            $tool_check = $first_product_audit2->tool_check;
            $jumlah_cek = $first_product_audit2->jumlah_cek;
            $id_first_product_audit = $first_product_audit2->id_first_product_audit;
        }
        if($first_product_audit == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'month' => $month,
                          'proses' => $proses,
                          'jenis' => $jenis,
                          'standar_kualitas' => $standar_kualitas,
                          'jml_null' => $jml_null,
                          'tool_check' => $tool_check,
                          'jumlah_cek' => $jumlah_cek,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'first_product_audit' => $first_product_audit,
                          'id_first_product_audit' => $id_first_product_audit,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('first_product_audit.print_email_daily', $data
                )->with('page', 'First Product Audit');
        }
    }

    public function sendemail_daily(Request $request,$id,$first_product_audit_id)
      {
          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_first_product_audit = "select *, first_product_audit_dailies.id as id_first_product_audit_details,first_product_audits.id as id_first_product_audit
                from first_product_audit_dailies
                join activity_lists on activity_lists.id = first_product_audit_dailies.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                join first_product_audits on first_product_audits.id = first_product_audit_dailies.first_product_audit_id
                where activity_lists.id = '".$id."'
                and first_product_audits.id = '".$first_product_audit_id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(first_product_audit_dailies.date,'%Y-%m') = '".$month."'
                and first_product_audit_dailies.deleted_at is null";
          $first_product_audit = DB::select($query_first_product_audit);
          $first_product_audit2 = DB::select($query_first_product_audit);
          $first_product_audit3 = DB::select($query_first_product_audit);

          // var_dump($first_product_audit3);

          if($first_product_audit2 != null){
            foreach($first_product_audit2 as $first_product_audit2){
                $foreman = $first_product_audit2->foreman;
                $id_first_product_audit_details = $first_product_audit2->id_first_product_audit_details;
                $send_status = $first_product_audit2->send_status;
              }

              foreach ($first_product_audit3 as $first_product_audit3) {
                    $laktivitas = FirstProductAuditDaily::find($first_product_audit3->id_first_product_audit_details);
                    $laktivitas->send_status = "Sent";
                    $laktivitas->send_date = date('Y-m-d');
                    $laktivitas->approval_leader = "Approved";
                    $laktivitas->approved_date_leader = date('Y-m-d');
                    $laktivitas->save();
              }

              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/first_product_audit/daily/'.$id.'/'.$first_product_audit_id)->with('error', 'Data tidak tersedia.')->with('page', 'First Product Audit');
          }

          if($send_status == "Sent"){
            return redirect('/index/first_product_audit/daily/'.$id.'/'.$first_product_audit_id)->with('error', 'Data pernah dikirim.')->with('page', 'First Product Audit');
          }
          elseif($first_product_audit != null){
              Mail::to($mail_to)->send(new SendEmail($first_product_audit, 'first_product_audit_daily'));
              return redirect('/index/first_product_audit/daily/'.$id.'/'.$first_product_audit_id)->with('status', 'Your E-mail has been sent.')->with('page', 'First Product Audit');
          }
          else{
            return redirect('/index/first_product_audit/daily/'.$id.'/'.$first_product_audit_id)->with('error', 'Data tidak tersedia.')->with('page', 'First Product Audit');
          }
      }

      public function approval_daily(Request $request,$id,$first_product_audit_id,$month)
      {
          $approve = $request->get('approve');
          foreach($approve as $approve){
            $first_product_audit = FirstProductAuditDaily::find($approve);
            $subsection = $first_product_audit->subsection;
            $month = substr($first_product_audit->date,0,7);
            $date = $first_product_audit->date;
            $first_product_audit->approval = "Approved";
            $first_product_audit->approved_date = date('Y-m-d');
            $first_product_audit->save();
          }
          return redirect('/index/first_product_audit/print_first_product_audit_email_daily/'.$id.'/'.$first_product_audit_id.'/'.$month)->with('status', 'Approved.')->with('page', 'First Product Audit');
      }
}
