<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\ApdCheck;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class ApdCheckController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->apd = [
                        'Masker',
                        'Celemek',
                        'Ear Muff',
                        'Ear Plug',
                        'Safety Shoes',
                        'Safety Glass',
                        'Safety Gloves',
                        'Safety Google',
                        'Safety Boots, Celemek Apron, Sarung Tangan Karet, Uvex Glass, Masker Koken',
                        'Masker Full Face',
                        'Hand Cover',
                        'Face Shield'];
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
        $now = date('Y-m-d');
        $one_month = date('Y-m-d', strtotime('-3 months', strtotime($now)));
    	$apdCheck = ApdCheck::where('activity_list_id',$id)->orderBy('apd_checks.id','desc')->whereBetween('date', [$now, $one_month])->get();

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        // var_dump($productionAudit);
        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

    	$data = array('apd_check' => $apdCheck,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
              'frequency' => $frequency,
    				  'apd' => $this->apd,
    				  'apd2' => $this->apd,
    				  'pic' => $pic,
                      'pic2' => $pic2,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id_departments' => $id_departments);
    	return view('apd_check.index', $data
    		)->with('page', 'APD Check');
    }

    function filter_apd_check(Request $request,$id)
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

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $apdCheck = ApdCheck::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('apd_checks.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $apdCheck = ApdCheck::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('apd_checks.id','desc')
                ->get();
        }
        elseif(strlen($request->get('month')) == null){
            $apdCheck = ApdCheck::where('activity_list_id',$id)
                ->orderBy('apd_checks.id','desc')
                ->get();
        }
        else{
            $apdCheck = ApdCheck::where('activity_list_id',$id)
                ->orderBy('apd_checks.id','desc')
                ->get();
        }
        $data = array(
                      'apd_check' => $apdCheck,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'apd' => $this->apd,
                      'apd2' => $this->apd,
                      'pic' => $pic,
                      'frequency' => $frequency,
                      'pic2' => $pic2,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id_departments' => $id_departments);
        return view('apd_check.index', $data
            )->with('page', 'APD Check');
    }

    public function destroy($id,$apd_check_id)
    {
      $apd_check = ApdCheck::find($apd_check_id);
      $apd_check->delete();

      return redirect('/index/apd_check/index/'.$id)
        ->with('status', 'APD Check has been deleted.')
        ->with('page', 'APD Check');        
    }

    function store(Request $request,$id)
    {
        	try{    
              $id_user = Auth::id();
              $date = $request->get('date');
              $week = WeeklyCalendar::where('week_date',$date)->get();
              foreach($week as $week){
                  $week_name = $week->week_name;
              }
              $jenis_apd = $request->get('jenis_apd');

              for ($i=0; $i < count($jenis_apd); $i++) { 
                ApdCheck::create([
                    'activity_list_id' => $id,
                    'department' => $request->get('department'),
                    'subsection' => $request->get('subsection'),
                    'date' => $request->get('date'),
                    'week_name' => $week_name,
                    'pic' => $request->get('pic'),
                    'proses' => $request->get('proses'),
                    'jenis_apd' => $jenis_apd[$i],
                    'kondisi' => $request->get('kondisi'),
                    'foto_aktual' => $request->get('foto_aktual'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);
              }

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

    function getapdcheck(Request $request)
    {
          try{
            $detail = ApdCheck::find($request->get("id"));
            $data = array('apd_check_id' => $detail->id,
                          'department' => $detail->department,
                          'subsection' => $detail->subsection,
                          'date' => $detail->date,
                          'kondisi' => $detail->kondisi,
                          'pic' => $detail->pic,
                          'proses' => $detail->proses,
                          'foto_aktual' => $detail->foto_aktual,
                          'jenis_apd' => $detail->jenis_apd);

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
                $apd_check = ApdCheck::find($id);
                $apd_check->department = $request->get('department');
                $apd_check->subsection = $request->get('subsection');
                $apd_check->date = $request->get('date');
                $apd_check->proses = $request->get('proses');
                $apd_check->jenis_apd = $request->get('jenis_apd');
                $apd_check->foto_aktual = $request->get('foto_aktual');
                $apd_check->kondisi = $request->get('kondisi');
                $apd_check->pic = $request->get('pic');
                $apd_check->save();

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

    function print_apd_check($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $apdCheckQuery = "select * from apd_checks
          JOIN activity_lists on activity_lists.id = apd_checks.activity_list_id
          where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and apd_checks.deleted_at is null";
        $apd_check = DB::select($apdCheckQuery);
        $apd_check2 = DB::select($apdCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($apd_check2 as $apd_check2){
          $subsection = $apd_check2->subsection;
          $leader = $apd_check2->leader_dept;
          $foreman = $apd_check2->foreman_dept;
          if($apd_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($apd_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $apd_check2->approved_date;
          $approved_date_leader = $apd_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($apd_check == null){
            return redirect('/index/apd_check/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'APD Check');
        }else{
            // $data = array(
            //               'subsection' => $subsection,
            //               'apd_check' => $apd_check,
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
            // return view('apd_check.print', $data
            //     )->with('page', 'APD Check');
          $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'potrait');

           $pdf->loadView('apd_check.print', array(
               'subsection' => $subsection,
              'apd_check' => $apd_check,
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

           return $pdf->stream("Cek APD ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_apd_check_email($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $apdCheckQuery = "select *,apd_checks.id as id_apd_check from apd_checks
          JOIN activity_lists on activity_lists.id = apd_checks.activity_list_id
          where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
          and activity_list_id = '".$id."'
          and department_id = '".$id_departments."'
          and apd_checks.deleted_at is null";
        $apd_check = DB::select($apdCheckQuery);
        $apd_check2 = DB::select($apdCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($apd_check2 as $apd_check2){
          $subsection = $apd_check2->subsection;
          $leader = $apd_check2->leader_dept;
          $foreman = $apd_check2->foreman_dept;
          if($apd_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($apd_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $apd_check2->approved_date;
          $approved_date_leader = $apd_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($apd_check == null){
            return redirect('/index/apd_check/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'APD Check');
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'apd_check' => $apd_check,
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
            return view('apd_check.print_email', $data
                )->with('page', 'APD Check');
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

          $apdCheckQuery = "
            select *,apd_checks.id as id_apd_check,'".$month."' as month from apd_checks
            JOIN activity_lists on activity_lists.id = apd_checks.activity_list_id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
            and apd_checks.activity_list_id = '".$id."'
            and department_id = '".$id_departments."'
            and apd_checks.deleted_at is null";
            
          $apd_check = DB::select($apdCheckQuery);
          $apd_check3 = DB::select($apdCheckQuery);
          $apd_check2 = DB::select($apdCheckQuery);

          if($apd_check != null){
            foreach($apd_check as $apd_check){
              $foreman = $apd_check->foreman;
              $send_status = $apd_check->send_status;
              $subsection = $apd_check->subsection;
            }

            foreach ($apd_check2 as $apd_check2) {
              $aCheck = ApdCheck::find($apd_check2->id_apd_check);
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
            return redirect('/index/apd_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'APD Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/apd_check/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'APD Check');
          }
          
          elseif($apd_check != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($apd_check3, 'apd_check'));
              return redirect('/index/apd_check/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'APD Check');
          }
          else{
            return redirect('/index/apd_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'APD Check');
          }
      }

    public function approval(Request $request,$id,$month)
    {
        $approve = $request->get('approve');
        if(count($approve) == 0){
          return redirect('/index/apd_check/print_apd_check_email/'.$id.'/'.$month)->with('error', 'Checklist the approval.')->with('page', 'APD Check');
        }
        else{
          foreach($approve as $approve){
                $apd_check = ApdCheck::find($approve);
                $apd_check->approval = "Approved";
                $apd_check->approved_date = date('Y-m-d');
                $apd_check->save();
              }
          return redirect('/index/apd_check/print_apd_check_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'APD Check');
        }
    }
}
