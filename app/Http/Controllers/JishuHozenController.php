<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\JishuHozenPoint;
use App\JishuHozen;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class JishuHozenController extends Controller
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

    function nama_pengecekan($id)
    {
    	$activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $frequency = $activityList->frequency;

        $query_jishu_hozen_point = "select DISTINCT(nama_pengecekan),id from jishu_hozen_points where activity_list_id='".$id."' and leader = '".$leader."'";
        $jishu_hozen_point = DB::select($query_jishu_hozen_point);

        $data = array('jishu_hozen_point' => $jishu_hozen_point,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'id_departments' => $id_departments);
        return view('jishu_hozen.point', $data
            )->with('page', 'Jishu Hozen');
    }

    function index($id,$jishu_hozen_point_id)
    {
        $activityList = ActivityList::find($id);
        $jishu_hozen = JishuHozen::where('activity_list_id',$id)
              ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
              ->orderBy('jishu_hozens.id','desc')->get();

        $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

      $data = array('jishu_hozen' => $jishu_hozen,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'jishu_hozen_point' => $jishu_hozen_point,
                      'jishu_hozen_point_id' => $jishu_hozen_point_id,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
      return view('jishu_hozen.index', $data
        )->with('page', 'Jishu Hozen');
    }

    function filter_jishu_hozen(Request $request,$id,$jishu_hozen_point_id)
    {
        $activityList = ActivityList::find($id);
        $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);

        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $jishu_hozen = JishuHozen::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
                ->orderBy('jishu_hozens.id','desc')
                ->get();
        }
        else{
            $jishu_hozen = JishuHozen::where('activity_list_id',$id)
            ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
            ->orderBy('jishu_hozens.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department = '".$departments."'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

        $data = array(
                      'jishu_hozen' => $jishu_hozen,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'jishu_hozen_point' => $jishu_hozen_point,
                      'jishu_hozen_point_id' => $jishu_hozen_point_id,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('jishu_hozen.index', $data
            )->with('page', 'Jishu Hozen');
    }

    public function destroy($id,$jishu_hozen_point_id,$jishu_hozen_id)
    {
      $jishu_hozen = JishuHozen::find($jishu_hozen_id);
      $jishu_hozen->delete();

      return redirect('/index/jishu_hozen/index/'.$id.'/'.$jishu_hozen_point_id)
        ->with('status', 'Jishu Hozen has been deleted.')
        ->with('page', 'Jishu Hozen');        
    }

    function store(Request $request,$id,$jishu_hozen_point_id)
    {
            try{

              $id_user = Auth::id();
                JishuHozen::create([
                    'activity_list_id' => $id,
                    'jishu_hozen_point_id' => $jishu_hozen_point_id,
                    'department' => $request->get('department'),
                    'subsection' => $request->get('subsection'),
                    'date' => $request->get('date'),
                    'month' => $request->get('month'),
                    'foto_aktual' => $request->get('foto_aktual'),
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
              // return redirect('/index/jishu_hozen/index/'.$id.'/'.$jishu_hozen_point_id)->with('status', 'Jishu Hozen data has been created.')->with('page', 'Jishu Hozen');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function getjishuhozen(Request $request)
    {
         try{
            $detail = JishuHozen::find($request->get("id"));
            $data = array('jishu_hozen_id' => $detail->id,
                          'jishu_hozen_point_id' => $detail->jishu_hozen_point_id,
                          'department' => $detail->department,
                          'subsection' => $detail->subsection,
                          'date' => $detail->date,
                          'month' => $detail->month,
                          'foto_aktual' => $detail->foto_aktual,
                          'pic' => $detail->pic,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $jishu_hozen){
            $error_code = $jishu_hozen->errorInfo[1];
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

    function update(Request $request,$id,$jishu_hozen_point_id,$jishu_hozen_id)
    {
        try{
                $jishu_hozen = JishuHozen::find($jishu_hozen_id);
                $jishu_hozen->department = $request->get('department');
                $jishu_hozen->subsection = $request->get('subsection');
                $jishu_hozen->month = $request->get('month');
                $jishu_hozen->foto_aktual = $request->get('foto_aktual');
                $jishu_hozen->pic = $request->get('pic');
                $jishu_hozen->save();

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

    function print_jishu_hozen($id,$jishu_hozen_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $id_departments = $activityList->departments->id;

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            where activity_lists.id = '".$id."'
            and jishu_hozens.id = '".$jishu_hozen_id."'
            and activity_lists.department_id = '".$id_departments."'
            and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
            and jishu_hozens.deleted_at is null";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            // $data = array(
            //               'subsection' => $subsection,
            //               'leader' => $leader,
            //               'foreman' => $foreman,
            //               'monthTitle' => $monthTitle,
            //               'subsection' => $subsection,
            //               'date' => $date,
            //               'jml_null' => $jml_null,
            //               'jml_null_leader' => $jml_null_leader,
            //               'approved_date' => $approved_date,
            //               'approval_leader' => $approval_leader,
            //               'approved_date_leader' => $approved_date_leader,
            //               'jishu_hozen' => $jishu_hozen,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'id_departments' => $id_departments);
            // return view('jishu_hozen.print', $data
            //     )->with('page', 'Jishu Hozen');

             $pdf = \App::make('dompdf.wrapper');
             $pdf->getDomPDF()->set_option("enable_php", true);
             $pdf->setPaper('A4', 'landscape');

             $pdf->loadView('jishu_hozen.print', array(
                  'subsection' => $subsection,
                  'leader' => $leader,
                  'foreman' => $foreman,
                  'monthTitle' => $monthTitle,
                  'subsection' => $subsection,
                  'date' => $date,
                  'jml_null' => $jml_null,
                  'jml_null_leader' => $jml_null_leader,
                  'approved_date' => $approved_date,
                  'approval_leader' => $approval_leader,
                  'approved_date_leader' => $approved_date_leader,
                  'jishu_hozen' => $jishu_hozen,
                  'departments' => $departments,
                  'activity_name' => $activity_name,
                  'activity_alias' => $activity_alias,
                  'id' => $id,
                  'id_departments' => $id_departments
             ));

             return $pdf->stream("Audit Jishu Hozen ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_jishu_hozen_email($id,$jishu_hozen_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            where activity_lists.id = '".$id."'
            and jishu_hozens.id = '".$jishu_hozen_id."'
            and activity_lists.department_id = '".$id_departments."'
            and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
            and jishu_hozens.deleted_at is null";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            $approval = $jishu_hozen2->approval;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
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
                          'approval' => $approval,
                          'jml_null' => $jml_null,
                          'role_code' => Auth::user()->role_code,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'jishu_hozen' => $jishu_hozen,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'jishu_hozen_id' => $jishu_hozen_id,
                          'month' => $month,
                          'id_departments' => $id_departments);
            return view('jishu_hozen.print_email', $data
                )->with('page', 'Jishu Hozen');
        }
    }

    function print_jishu_hozen_approval($activity_list_id,$month)
    {
        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            where activity_lists.id = '".$activity_list_id."'
            and activity_lists.department_id = '".$id_departments."'
            and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
            and jishu_hozens.deleted_at is null limit 1";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));
        $id = $activity_list_id;

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $jishu_hozen_id = $jishu_hozen2->id_jishu_hozen;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            $approval = $jishu_hozen2->approval;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
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
                          'approval' => $approval,
                          'jml_null' => $jml_null,
                          'role_code' => Auth::user()->role_code,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'jishu_hozen' => $jishu_hozen,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'activity_list_id' => $activity_list_id,
                          'id' => $id,
                          'jishu_hozen_id' => $jishu_hozen_id,
                          'month' => $month,
                          'id_departments' => $id_departments);
            return view('jishu_hozen.print_email', $data
                )->with('page', 'Jishu Hozen');
        }
    }

    public function sendemail($id,$jishu_hozen_point_id)
      {
          $query_jishu_hozen = "select *,jishu_hozens.id as jishu_hozen_id from jishu_hozens join activity_lists on activity_lists.id = jishu_hozens.activity_list_id join departments on activity_lists.department_id = departments.id where jishu_hozens.id = '".$id."'";
          
          $jishu_hozen = DB::select($query_jishu_hozen);
          $jishu_hozen3 = DB::select($query_jishu_hozen);
          // $training2 = DB::select($query_training);

          if($jishu_hozen != null){
            foreach($jishu_hozen as $jishu_hozen){
              $foreman = $jishu_hozen->foreman;
              $send_status = $jishu_hozen->send_status;
              $activity_list_id = $jishu_hozen->activity_list_id;
              $jishu_hozen2 = JishuHozen::find($id);
              $jishu_hozen2->send_status = "Sent";
              $jishu_hozen2->send_date = date('Y-m-d');
              $jishu_hozen2->approval_leader = "Approved";
              $jishu_hozen2->approved_date_leader = date('Y-m-d');
              $jishu_hozen2->save();
              // var_dump($id);
            }
            $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
              // var_dump($mail_to);
            }
          }
          else{
            return redirect('/index/jishu_hozen/index/'.$activity_list_id.'/'.$jishu_hozen_point_id)->with('error', 'Data tidak tersedia.')->with('page', 'Jishu Hozen');
          }

          if($send_status == "Sent"){
            return redirect('/index/jishu_hozen/index/'.$activity_list_id.'/'.$jishu_hozen_point_id)->with('error', 'Data pernah dikirim.')->with('page', 'Jishu Hozen');
          }
          
          elseif($jishu_hozen != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($jishu_hozen3, 'jishu_hozen'));
              return redirect('/index/jishu_hozen/index/'.$activity_list_id.'/'.$jishu_hozen_point_id)->with('status', 'Your E-mail has been sent.')->with('page', 'Jishu Hozen');
          }
          else{
            return redirect('/index/jishu_hozen/index/'.$activity_list_id.'/'.$jishu_hozen_point_id)->with('error', 'Data tidak tersedia.')->with('page', 'Jishu Hozen');
          }
      }

      public function approval(Request $request,$id,$jishu_hozen_id,$month)
      {
          $approve = $request->get('approve');
          $approvecount = count($approve);
          if($approvecount == 0){
            // echo "<script>alert('Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')</script>";
            return redirect('/index/jishu_hozen/print_jishu_hozen_email/'.$id.'/'.$jishu_hozen_id.'/'.$month)->with('error', 'Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')->with('page', 'Jishu Hozen');
          }
          else{
                $jishu_hozen = JishuHozen::find($jishu_hozen_id);
                $jishu_hozen->approval = "Approved";
                $jishu_hozen->approved_date = date('Y-m-d');
                $jishu_hozen->save();
            return redirect('/index/jishu_hozen/print_jishu_hozen_email/'.$id.'/'.$jishu_hozen_id.'/'.$month)->with('status', 'Approved.')->with('page', 'Jishu Hozen');
          }
      }
}
