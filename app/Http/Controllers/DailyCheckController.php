<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\DailyCheck;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class DailyCheckController extends Controller
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
      $this->product = ['Saxophone',
                        'Flute',
                        'Clarinet'];
    }

    function index($id,$product)
    {
        $activityList = ActivityList::find($id);
    	$daily_check = DailyCheck::where('activity_list_id',$id)
    		->where('product',$product)
            ->orderBy('daily_checks.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

    	$data = array('daily_check' => $daily_check,
                      'products' => $this->product,
                      'product' => $product,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('daily_check.index', $data
    		)->with('page', 'Daily Check');
    }

    function product($id)
    {
        $activityList = ActivityList::find($id);
    	$dailyCheck = DailyCheck::where('activity_list_id',$id)
            ->orderBy('daily_checks.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        
    	$data = array('dailyCheck' => $dailyCheck,
                      'product' => $this->product,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
              'frequency' => $frequency,
                      'id_departments' => $id_departments);
    	return view('daily_check.product', $data
    		)->with('page', 'Daily Check');
    }

    function filter_daily_check(Request $request,$id,$product)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $daily_check = DailyCheck::where('activity_list_id',$id)
                ->whereYear('check_date', '=', $year)
                ->whereMonth('check_date', '=', $month)
                ->where('product',$product)
                ->orderBy('daily_checks.id','desc')
                ->get();
        }
        else{
            $daily_check = DailyCheck::where('activity_list_id',$id)
            ->where('product',$product)
            ->orderBy('daily_checks.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        // }
        $data = array('product' => $product,
                      'products' => $this->product,
                      'daily_check' => $daily_check,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('daily_check.index', $data
            )->with('page', 'Daily Check');
    }

    function show($id,$daily_check_id)
    {
        $activityList = ActivityList::find($id);
        $daily_check = DailyCheck::find($daily_check_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('daily_check' => $daily_check,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'product' => $daily_check->product,
                      'id' => $id);
        return view('daily_check.view', $data
            )->with('page', 'Daily Check');
    }

    public function destroy($id,$daily_check_id)
    {
      $daily_check = DailyCheck::find($daily_check_id);
      $product = $daily_check->product;
      $daily_check->delete();

      return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)
        ->with('status', 'Daily Check has been deleted.')
        ->with('page', 'Daily Check');        
    }

    function create($id,$product)
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

        $data = array('product' => $product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('daily_check.create', $data
            )->with('page', 'Daily Check');
    }

    function store(Request $request,$id,$product)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
                DailyCheck::create([
                    'activity_list_id' => $id,
                    'department' => $request->get('department'),
                    'product' => $request->get('product'),
                    'production_date' => $request->get('production_date'),
                    'check_date' => $request->get('check_date'),
                    'serial_number' => $request->get('serial_number'),
                    'condition' => $request->get('condition'),
                    'keterangan' => $request->get('keterangan'),
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

    function getdetail(Request $request)
    {
          try{
            $detail = DailyCheck::find($request->get("id"));
            $data = array('daily_check_id' => $detail->id,
                          'department' => $detail->department,
                          'product' => $detail->product,
                          'production_date' => $detail->production_date,
                          'check_date' => $detail->check_date,
                          'serial_number' => $detail->serial_number,
                          'condition' => $detail->condition,
                          'keterangan' => $detail->keterangan,);
            // $name = $beacon->name;
            // $beacon->uuid = $request->get('uuid');
            // $beacon->name = $request->get('name');
           

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

    function update(Request $request,$id,$product)
    {
        try{
                $interview = DailyCheck::find($id);
                $interview->department = $request->get('department');
                $interview->product = $request->get('product');
                $interview->production_date = $request->get('production_date');
                $interview->check_date = $request->get('check_date');
                $interview->serial_number = $request->get('serial_number');
                $interview->condition = $request->get('condition');
                $interview->keterangan = $request->get('keterangan');
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

    function print_daily_check($id,$month)
    {
        // $month = $request->get('month');

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $dailyCheckQuery = "select *
            from daily_checks
            join activity_lists on activity_list_id = activity_lists.id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$month."'
            and activity_lists.department_id = '".$id_departments."'
            and activity_lists.id = '".$id."'
            and daily_checks.deleted_at is null";
        $daily_check = DB::select($dailyCheckQuery);
        $daily_check2 = DB::select($dailyCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($daily_check2 as $daily_check2){
          $product = $daily_check2->product;
          $leader = $daily_check2->leader;
          $foreman = $daily_check2->foreman;
          if($daily_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($daily_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $daily_check2->approved_date;
          $approved_date_leader = $daily_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($daily_check == null){
            return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('error', 'Data Tidak Tersedia.')->with('page', 'Daily Check');
        }else{
            // $data = array(
            //               'daily_check' => $daily_check,
            //               'activityList' => $activityList,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'id_departments' => $id_departments,
            //               'monthTitle' => $monthTitle,
            //               'leader' => $leader,
            //               'jml_null' => $jml_null,
            //               'jml_null_leader' => $jml_null_leader,
            //               'approved_date' => $approved_date,
            //               'approved_date_leader' => $approved_date_leader,
            //               'foreman' => $foreman,
            //               'product' => $product);
            // return view('daily_check.print', $data
            //     )->with('page', 'Daily Check');
          $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'potrait');

           $pdf->loadView('daily_check.print', array(
                'daily_check' => $daily_check,
                'activityList' => $activityList,
                'departments' => $departments,
                'activity_name' => $activity_name,
                'activity_alias' => $activity_alias,
                'id' => $id,
                'id_departments' => $id_departments,
                'monthTitle' => $monthTitle,
                'leader' => $leader,
                'jml_null' => $jml_null,
                'jml_null_leader' => $jml_null_leader,
                'approved_date' => $approved_date,
                'approved_date_leader' => $approved_date_leader,
                'foreman' => $foreman,
                'product' => $product
           ));

           return $pdf->stream("Cek FG / KD ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_daily_check_email($id,$month)
    {

        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $dailyCheckQuery = "select *,daily_checks.id as id_daily_check
            from daily_checks
            join activity_lists on activity_list_id = activity_lists.id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$month."'
            and activity_lists.department_id = '".$id_departments."'
            and activity_lists.id = '".$id."'
            and daily_checks.deleted_at is null";
        $daily_check = DB::select($dailyCheckQuery);
        $daily_check2 = DB::select($dailyCheckQuery);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($daily_check2 as $daily_check2){
          $product = $daily_check2->product;
          $leader = $daily_check2->leader;
          $foreman = $daily_check2->foreman;
          if($daily_check2->approval == Null){
            $jml_null = $jml_null + 1;
          }
          if($daily_check2->approval_leader == Null){
            $jml_null_leader = $jml_null_leader + 1;
          }
          $approved_date = $daily_check2->approved_date;
          $approved_date_leader = $daily_check2->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($daily_check == null){
            return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('error', 'Data Tidak Tersedia.')->with('page', 'Daily Check');
        }else{
            $data = array(
                          'daily_check' => $daily_check,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'role_code' => Auth::user()->role_code,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments,
                          'monthTitle' => $monthTitle,
                          'month' => $month,
                          'leader' => $leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'foreman' => $foreman,
                          'product' => $product);
            return view('daily_check.print_email', $data
                )->with('page', 'Daily Check');
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

          $dailyCheckQuery = "select *,daily_checks.id as id_daily_check,DATE_FORMAT(daily_checks.check_date,'%Y-%m') as month
            from daily_checks
            join activity_lists on activity_list_id = activity_lists.id
            join departments on activity_lists.department_id = departments.id
            where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$month."'
            and activity_lists.department_id = '".$id_departments."'
            and activity_lists.id = '".$id."'
            and daily_checks.deleted_at is null";
          $daily_check = DB::select($dailyCheckQuery);
          $daily_check3 = DB::select($dailyCheckQuery);
          $daily_check2 = DB::select($dailyCheckQuery);
          // $training2 = DB::select($query_training);

          if($daily_check != null){
            foreach($daily_check as $daily_check){
              $foreman = $daily_check->foreman;
              $send_status = $daily_check->send_status;
              $product = $daily_check->product;
              // var_dump($id);
            }

            foreach ($daily_check2 as $daily_check2) {
              $dCheck = DailyCheck::find($daily_check2->id_daily_check);
              $dCheck->send_status = "Sent";
              $dCheck->send_date = date('Y-m-d');
              $dCheck->approval_leader = "Approved";
              $dCheck->approved_date_leader = date('Y-m-d');
              $dCheck->save();
            }

            $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
              // var_dump($mail_to);
            }
          }
          else{
            return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('error', 'Data tidak tersedia.')->with('page', 'Daily Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('error', 'Data pernah dikirim.')->with('page', 'Daily Check');
          }
          
          elseif($daily_check != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($daily_check3, 'daily_check'));
              return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('status', 'Your E-mail has been sent.')->with('page', 'Daily Check');
          }
          else{
            return redirect('/index/daily_check_fg/index/'.$id.'/'.$product)->with('error', 'Data tidak tersedia.')->with('page', 'Daily Check');
          }
      }

    public function approval(Request $request,$id,$month)
    {
        $approve = $request->get('approve');
        if(count($approve) == 0){
          return redirect('/index/daily_check_fg/print_daily_check_email/'.$id.'/'.$month)->with('error', 'Checklist the approval.')->with('page', 'Daily Check');
        }
        else{
          foreach($approve as $approve){
                $dCheck = DailyCheck::find($approve);
                $dCheck->approval = "Approved";
                $dCheck->approved_date = date('Y-m-d');
                $dCheck->save();
              }
          return redirect('/index/daily_check_fg/print_daily_check_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Daily Check');
        }
    }
}
