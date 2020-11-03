<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AreaCheckPoint;
use App\AreaCheck;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AreaCheckController extends Controller
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

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$area_check = AreaCheck::where('activity_list_id',$id)
            ->orderBy('area_checks.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $queryPointCheck = "select * from area_check_points where activity_list_id = '".$id."' and deleted_at is null";
        $point_check = DB::select($queryPointCheck);
        $point_check2 = DB::select($queryPointCheck);
        $point_check3 = DB::select($queryPointCheck);

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

    	$data = array('area_check' => $area_check,
            				  'departments' => $departments,
            				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'point_check' => $point_check,
                      'point_check2' => $point_check2,
                      'point_check3' => $point_check3,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
              'frequency' => $frequency,
                      'id_departments' => $id_departments);
    	return view('area_check.index', $data
    		)->with('page', 'Area Check');
    }

    function filter_area_check(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $area_check = AreaCheck::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('area_checks.id','desc')
                ->get();
        }
        else{
            $area_check = AreaCheck::where('activity_list_id',$id)
            ->orderBy('area_checks.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $queryPointCheck = "select * from area_check_points where activity_list_id = '".$id."' and deleted_at is null";
        $point_check = DB::select($queryPointCheck);
        $point_check2 = DB::select($queryPointCheck);
        $point_check3 = DB::select($queryPointCheck);
        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

        $data = array(
                      'area_check' => $area_check,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
					            'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'point_check' => $point_check,
                      'point_check2' => $point_check2,
                      'point_check3' => $point_check3,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'foreman' => $foreman,
                      'id' => $id,
                      'frequency' => $frequency,
                      'id_departments' => $id_departments);
        return view('area_check.index', $data
            )->with('page', 'Area Check');
    }

    public function destroy($id,$area_check_id)
    {
      $area_check = AreaCheck::find($area_check_id);
      $area_check->delete();

      return redirect('/index/area_check/index/'.$id)
        ->with('status', 'Area Check has been deleted.')
        ->with('page', 'Area Check');        
    }

    function store(Request $request,$id)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
                AreaCheck::create([
                    'activity_list_id' => $id,
                    'department' => $request->get('department'),
                    'subsection' => $request->get('subsection'),
                    'area_check_point_id' => $request->get('point_check'),
                    'date' => $request->get('date'),
                    'condition' => $request->get('condition'),
                    'pic' => $request->get('pic'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function getareacheck(Request $request)
    {
          try{
            $detail = AreaCheck::find($request->get("id"));
            $data = array('area_check_id' => $detail->id,
            			  'area_check_point_id' => $detail->area_check_point_id,
                          'department' => $detail->department,
                          'subsection' => $detail->subsection,
                          'date' => $detail->date,
                          'condition' => $detail->condition,
                          'pic' => $detail->pic);

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

    function update(Request $request,$id)
    {
        try{
                $interview = AreaCheck::find($id);
                $interview->department = $request->get('department');
                $interview->subsection = $request->get('subsection');
                $interview->date = $request->get('date');
                $interview->area_check_point_id = $request->get('area_check_point_id');
                $interview->condition = $request->get('condition');
                $interview->pic = $request->get('pic');
                $interview->save();

            // return redirect('index/interview/details/'.$interview_id)
            //   ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
               $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function print_area_check($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $date = db::select("select week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."' and week_date not in (select tanggal from ftm.kalender)");
        $countdate = count($date);

        $point_check = db::select("select * from area_check_points where activity_list_id = '".$id."'");

        $areaCheckQuery = "select * from area_checks
          JOIN activity_lists on activity_lists.id = area_checks.activity_list_id
          where DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and area_checks.deleted_at is null";
        $area_check = DB::select($areaCheckQuery);
        $area_check2 = DB::select($areaCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($area_check2 as $area_check2){
          $subsection = $area_check2->subsection;
          $leader = $area_check2->leader_dept;
          $foreman = $area_check2->foreman_dept;
          if($area_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($area_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $area_check2->approved_date;
          $approved_date_leader = $area_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($area_check == null){
            return redirect('/index/area_check/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Area Check');
        }else{
            // $data = array(
            //               'countdate' => $countdate,
            //               'date' => $date,
            //               'subsection' => $subsection,
            //               'point_check' => $point_check,
            //               'activityList' => $activityList,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'role_code' => Auth::user()->role_code,
            //               'id_departments' => $id_departments,
            //               'monthTitle' => $monthTitle,
            //               'month' => $month,
            //               'leader' => $leader,
            //               'jml_null' => $jml_null,
            //               'jml_null_leader' => $jml_null_leader,
            //               'approved_date' => $approved_date,
            //               'approved_date_leader' => $approved_date_leader,
            //               'foreman' => $foreman,);
            // return view('area_check.print', $data
            //     )->with('page', 'Area Check');
            $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'landscape');

           $pdf->loadView('area_check.print', array(
               'countdate' => $countdate,
                          'date' => $date,
                          'subsection' => $subsection,
                          'point_check' => $point_check,
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
                          'foreman' => $foreman,
           ));

           return $pdf->stream("Cek Area ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_area_check_email($id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $date = db::select("select week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."' and week_date not in (select tanggal from ftm.kalender)");
        $countdate = count($date);

        $point_check = db::select("select * from area_check_points where activity_list_id = '".$id."'");

        $areaCheckQuery = "select * from area_checks
          JOIN activity_lists on activity_lists.id = area_checks.activity_list_id
          where DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and area_checks.deleted_at is null";
        $area_check = DB::select($areaCheckQuery);
        $area_check2 = DB::select($areaCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($area_check2 as $area_check2){
          $subsection = $area_check2->subsection;
          $leader = $area_check2->leader_dept;
          $foreman = $area_check2->foreman_dept;
          if($area_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($area_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $area_check2->approved_date;
          $approved_date_leader = $area_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($area_check == null){
            return redirect('/index/area_check/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Area Check');
        }else{
            $data = array(
                          'countdate' => $countdate,
                          'date' => $date,
                          'subsection' => $subsection,
                          'point_check' => $point_check,
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
            return view('area_check.print_email', $data
                )->with('page', 'Area Check');
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

          $areaCheckQuery = "select *,area_checks.id as id_area_check,'".$month."' as month from area_checks
            JOIN activity_lists on activity_lists.id = area_checks.activity_list_id
            JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
            and area_checks.activity_list_id = '".$id."'
            and department_id = '".$id_departments."'
            and area_checks.deleted_at is null";
            
          $area_check = DB::select($areaCheckQuery);
          $area_check3 = DB::select($areaCheckQuery);
          $area_check2 = DB::select($areaCheckQuery);
          // $training2 = DB::select($query_training);

          if($area_check != null){
            foreach($area_check as $area_check){
              $foreman = $area_check->foreman;
              $send_status = $area_check->send_status;
              $subsection = $area_check->subsection;
              // var_dump($id);
            }

            foreach ($area_check2 as $area_check2) {
              $aCheck = AreaCheck::find($area_check2->id_area_check);
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
              // var_dump($mail_to);
            }
          }
          else{
            return redirect('/index/area_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Area Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/area_check/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Area Check');
          }
          
          elseif($area_check != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($area_check3, 'area_check'));
              return redirect('/index/area_check/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Area Check');
          }
          else{
            return redirect('/index/area_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Area Check');
          }
      }

    public function approval(Request $request,$id,$month)
    {
        $approve = $request->get('approve');
        if(count($approve) == 0){
          return redirect('/index/area_check/print_area_check_email/'.$id.'/'.$month)->with('error', 'Checklist the approval.')->with('page', 'Area Check');
        }
        else{
          foreach($approve as $approve){
                $dCheck = AreaCheck::find($approve);
                $dCheck->approval = "Approved";
                $dCheck->approved_date = date('Y-m-d');
                $dCheck->save();
              }
          return redirect('/index/area_check/print_area_check_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Area Check');
        }
    }
}
