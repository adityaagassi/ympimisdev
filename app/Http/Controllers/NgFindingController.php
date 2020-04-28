<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\NgFinding;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\MaterialPlantDataList;

class NgFindingController extends Controller
{
	public function __construct()
    {
      $this->middleware('auth');
    
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$ng_finding = NgFinding::select('*','ng_findings.id as ng_finding_id')->join('material_plant_data_lists', 'ng_findings.material_number', '=', 'material_plant_data_lists.material_number')->where('activity_list_id',$id)->orderBy('ng_findings.id','desc')
            ->get();

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $mpdl = MaterialPlantDataList::get();
        $mpdl2 = MaterialPlantDataList::get();

    	$data = array('ng_finding' => $ng_finding,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
              'frequency' => $frequency,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'mpdl' => $mpdl,
                      'mpdl2' => $mpdl2,
                      'id_departments' => $id_departments);
    	return view('ng_finding.index', $data
    		)->with('page', 'Temuan NG');
    }

    function filter_ng_finding(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $ng_finding = NgFinding::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('ng_findings.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $ng_finding = NgFinding::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('ng_findings.id','desc')
                ->get();
        }
        elseif(strlen($request->get('month')) == null){
            $ng_finding = NgFinding::where('activity_list_id',$id)
                ->orderBy('ng_findings.id','desc')
                ->get();
        }
        else{
            $ng_finding = NgFinding::where('activity_list_id',$id)
                ->orderBy('ng_findings.id','desc')
                ->get();
        }

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $data = array(
                      'ng_finding' => $ng_finding,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'id_departments' => $id_departments);
        return view('ng_finding.index', $data
            )->with('page', 'Temuan NG');
    }

    public function destroy($id,$ng_finding_id)
    {
      $ng_finding = NgFinding::find($ng_finding_id);
      $ng_finding->delete();

      return redirect('/index/ng_finding/index/'.$id)
        ->with('status', 'Temuan NG has been deleted.')
        ->with('page', 'Temuan NG');        
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            $tujuan_upload = 'data_file/ng_finding';
            $date = date('Y-m-d');

            $file = $request->file('inputfile');
            $nama_file = $file->getClientOriginalName();
            $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();

            $file->move($tujuan_upload,$filename);

            NgFinding::create([
                'activity_list_id' => $id,
                'department' => $request->input('inputdepartment'),
                'date' => $request->input('inputdate'),
                'material_number' => $request->input('inputmaterialnumber'),
                'quantity' => $request->input('inputquantity'),
                'finder' => $request->input('inputfinder'),
                'picture' => $filename,
                'defect' => $request->input('inputdefect'),
                'checked_qa' => $request->input('inputcheckedqa'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/ng_finding/index/'.$id)
            ->with('page', 'Temuan NG')->with('status', 'New Temuan NG has been created.');
    }

    function getngfinding(Request $request)
    {
          try{
            $detail = NgFinding::find($request->get("id"));
            $data = array('ng_finding_id' => $detail->id,
                          'department' => $detail->department,
                          'date' => $detail->date,
                          'material_number' => $detail->material_number,
                          'quantity' => $detail->quantity,
                          'finder' => $detail->finder,
                          'picture' => $detail->picture,
                          'checked_qa' => $detail->checked_qa,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman,
                          'defect' => $detail->defect);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
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

    function update(Request $request,$id,$ng_finding_id)
    {
        try{

        	$tujuan_upload = 'data_file/ng_finding';

          	if ($request->file('editfile') == null) {
                $ng_finding = NgFinding::find($ng_finding_id);
                $ng_finding->department = $request->get('editdepartment');
                $ng_finding->date = $request->get('editdate');
                $ng_finding->material_number = $request->get('editmaterialnumber');
                $ng_finding->quantity = $request->get('editquantity');
                $ng_finding->finder = $request->get('editfinder');
                $ng_finding->defect = $request->get('editdefect');
                $ng_finding->checked_qa = $request->get('editcheckedqa');
                $ng_finding->save();
            }else{
            	$file = $request->file('editfile');
	            $nama_file = $file->getClientOriginalName();
	            $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();

	            $file->move($tujuan_upload,$filename);

            	$ng_finding = NgFinding::find($ng_finding_id);
                $ng_finding->department = $request->get('editdepartment');
                $ng_finding->date = $request->get('editdate');
                $ng_finding->material_number = $request->get('editmaterialnumber');
                $ng_finding->quantity = $request->get('editquantity');
                $ng_finding->finder = $request->get('editfinder');
                $ng_finding->picture = $filename;
                $ng_finding->defect = $request->get('editdefect');
                $ng_finding->checked_qa = $request->get('editcheckedqa');
                $ng_finding->save();
            }

            return redirect('index/ng_finding/index/'.$id)
              ->with('page', 'Temuan NG')->with('status', 'Temuan NG has been updated.');

            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function print_ng_finding(Request $request,$id)
    {
        $month = $request->get('month');

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $ng_finding_query = "
        SELECT
			* 
		FROM
			ng_findings
			JOIN activity_lists ON activity_lists.id = ng_findings.activity_list_id 
			JOIN material_plant_data_lists ON material_plant_data_lists.material_number = ng_findings.material_number
		WHERE
			DATE_FORMAT( ng_findings.date, '%Y-%m' ) = '".$month."' 
			AND activity_list_id = '".$id."' 
			AND department_id = '".$id_departments."' 
			AND ng_findings.deleted_at IS NULL";
        $ng_finding = DB::select($ng_finding_query);
        $ng_finding2 = DB::select($ng_finding_query);

        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($ng_finding2 as $ng_finding2){
          $leader = $ng_finding2->leader_dept;
          $foreman = $ng_finding2->foreman_dept;
          if($ng_finding2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($ng_finding2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $ng_finding2->approved_date;
          $approved_date_leader = $ng_finding2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($ng_finding == null){
        	echo "<script type='text/javascript'>alert('Data Tidak Tersedia');</script>";
        	echo "<script type='text/javascript'>window.close();</script>";
            // return redirect('/index/ng_finding/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Temuan NG');
        }else{
            $data = array(
                          'ng_finding' => $ng_finding,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'role_code' => Auth::user()->role_code,
                          'id_departments' => $id_departments,
                          'monthTitle' => $monthTitle,
                          'month' => $month,
                          'leader' => $leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'foreman' => $foreman,);
            return view('ng_finding.print', $data
                )->with('page', 'Temuan NG');
        }
    }

    function print_ng_finding_email($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $ng_finding_query = "
        SELECT
			*, ng_findings.id as id_ng_finding
		FROM
			ng_findings
			JOIN activity_lists ON activity_lists.id = ng_findings.activity_list_id 
			JOIN material_plant_data_lists ON material_plant_data_lists.material_number = ng_findings.material_number
		WHERE
			DATE_FORMAT( ng_findings.date, '%Y-%m' ) = '".$month."' 
			AND activity_list_id = '".$id."' 
			AND department_id = '".$id_departments."' 
			AND ng_findings.deleted_at IS NULL";
        $ng_finding = DB::select($ng_finding_query);
        $ng_finding2 = DB::select($ng_finding_query);

        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($ng_finding2 as $ng_finding2){
          $leader = $ng_finding2->leader_dept;
          $foreman = $ng_finding2->foreman_dept;
          if($ng_finding2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($ng_finding2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $ng_finding2->approved_date;
          $approved_date_leader = $ng_finding2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($ng_finding == null){
            // return redirect('/index/ng_finding/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Temuan NG');
            echo "<script type='text/javascript'>alert('Data Tidak Tersedia');</script>";
        	echo "<script type='text/javascript'>window.close();</script>";
        }else{
            $data = array(
                          'ng_finding' => $ng_finding,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'role_code' => Auth::user()->role_code,
                          'id_departments' => $id_departments,
                          'monthTitle' => $monthTitle,
                          'month' => $month,
                          'leader' => $leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'foreman' => $foreman,);
            return view('ng_finding.print_email', $data
                )->with('page', 'Temuan NG');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $month = $request->get('month');

          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $ng_findingQuery = "
          	SELECT
				*,
				ng_findings.id AS id_ng_finding,
				'2020-03' AS month 
			FROM
				ng_findings
				JOIN activity_lists ON activity_lists.id = ng_findings.activity_list_id
				JOIN departments ON activity_lists.department_id = departments.id 
				JOIN material_plant_data_lists ON material_plant_data_lists.material_number = ng_findings.material_number
			WHERE
				DATE_FORMAT( ng_findings.date, '%Y-%m' ) = '".$month."' 
				AND ng_findings.activity_list_id = '".$id."' 
				AND department_id = '".$id_departments."' 
				AND ng_findings.deleted_at IS NULL";
            
          $ng_finding = DB::select($ng_findingQuery);
          $ng_finding3 = DB::select($ng_findingQuery);
          $ng_finding2 = DB::select($ng_findingQuery);

          if($ng_finding != null){
            foreach($ng_finding as $ng_finding){
              $foreman = $ng_finding->foreman;
              $send_status = $ng_finding->send_status;
            }

            foreach ($ng_finding2 as $ng_finding2) {
              $aCheck = NgFinding::find($ng_finding2->id_ng_finding);
              $aCheck->send_status = "Sent";
              $aCheck->send_date = date('Y-m-d');
              $aCheck->approval_leader = "Approved";
              $aCheck->approved_date_leader = date('Y-m-d');
              $aCheck->save();
            }

            $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
            }
          }
          else{
            return redirect('/index/ng_finding/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Temuan NG');
          }

          if($send_status == "Sent"){
            return redirect('/index/ng_finding/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Temuan NG');
          }
          
          elseif($ng_finding != null){
              Mail::to($mail_to)->send(new SendEmail($ng_finding3, 'ng_finding'));
              return redirect('/index/ng_finding/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Temuan NG');
          }
          else{
            return redirect('/index/ng_finding/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Temuan NG');
          }
      }

    public function approval(Request $request,$id,$month)
    {
        $approve = $request->get('approve');
        if(count($approve) == 0){
          return redirect('/index/ng_finding/print_ng_finding_email/'.$id.'/'.$month)->with('error', 'Checklist the approval.')->with('page', 'Temuan NG');
        }
        else{
          foreach($approve as $approve){
                $ng_finding = NgFinding::find($approve);
                $ng_finding->approval = "Approved";
                $ng_finding->approved_date = date('Y-m-d');
                $ng_finding->save();
              }
          return redirect('/index/ng_finding/print_ng_finding_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Temuan NG');
        }
    }
}
