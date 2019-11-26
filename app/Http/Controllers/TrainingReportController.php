<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\TrainingReport;
use App\TrainingPicture;
use App\TrainingParticipant;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;


class TrainingReportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$trainingReport = TrainingReport::where('activity_list_id',$id)
            ->orderBy('training_reports.id','desc')->get();

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);
        $product2 = DB::select($queryProduct);

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        // var_dump($productionAudit);
    	$data = array('training_report' => $trainingReport,
                      'product' => $product,
                      'product2' => $product2,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('training_report.index', $data
    		)->with('page', 'Training Report');
    }

    function filter_training(Request $request,$id)
    {
        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);
        $product2 = DB::select($queryProduct);

        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        if($request->get('product') != null && strlen($request->get('date')) != null){
            $origin_group = $request->get('product');
            $date = date('Y-m-d', strtotime($request->get('date')));
            $trainingReport = TrainingReport::where('activity_list_id',$id)
                ->where('product',$origin_group)
                ->where('date',$date)
                ->orderBy('training_reports.id','desc')
                ->get();
        }
        elseif (strlen($request->get('date')) > null && $request->get('product') == null) {
            $date = date('Y-m-d', strtotime($request->get('date')));
            $trainingReport = TrainingReport::where('activity_list_id',$id)
                ->where('date',$date)
                ->orderBy('training_reports.id','desc')
                ->get();
        }
        elseif($request->get('product') > null && strlen($request->get('date')) == null){
            $origin_group = $request->get('product');
            $trainingReport = TrainingReport::where('activity_list_id',$id)
                ->where('product',$origin_group)
                ->orderBy('training_reports.id','desc')
                ->get();
        }
        else{
            $trainingReport = TrainingReport::where('activity_list_id',$id)
            ->orderBy('training_reports.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        // }
        $data = array('product' => $product,
                      'product2' => $product2,
                      'training_report' => $trainingReport,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('training_report.index', $data
            )->with('page', 'Training Report');
    }

    function show($id,$training_id)
    {
        $activityList = ActivityList::find($id);
        $trainingReport = TrainingReport::find($training_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('training_report' => $trainingReport,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('training_report.view', $data
            )->with('page', 'Training Report');
    }

    public function destroy($id,$training_id)
    {
      $trainingReport = TrainingReport::find($training_id);
      $trainingReport->delete();

      return redirect('/index/training_report/index/'.$id)
        ->with('status', 'Training Report has been deleted.')
        ->with('page', 'Training Report');
        //
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader_dept = $activityList->leader_dept;
        $foreman_dept = $activityList->foreman_dept;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $queryTrainer = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
                        join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$departments."' and promotion_logs.`position`='sub leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='leader')";
        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);
        $trainer = DB::select($queryTrainer);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $queryPeriode = "select DISTINCT(weekly_calendars.fiscal_year) from weekly_calendars";
        $periode = DB::select($queryPeriode);

        $data = array('product' => $product,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'foreman_dept' => $foreman_dept,
                      'leader_dept' => $leader_dept,
                      'departments' => $departments,
                      'section' => $section,
                      'periode' => $periode,
                      'trainer' => $trainer,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('training_report.create', $data
            )->with('page', 'Training Report');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            TrainingReport::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'product' => $request->input('product'),
                'periode' => $request->input('periode'),
                'date' => $request->input('date'),
                'time' => $request->input('time'),
                'trainer' => $request->input('trainer'),
                'theme' => $request->input('theme'),
                'isi_training' => $request->input('isi_training'),
                'tujuan' => $request->input('tujuan'),
                'standard' => $request->input('standard'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'notes' => $request->input('notes'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/training_report/index/'.$id)
            ->with('page', 'Training Report')->with('status', 'New Training Report has been created.');
    }

    function edit($id,$training_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;

        $queryLeaderForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group` = 'leader')";
        $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$departments."' and mutation_logs.`group`='foreman')";
        $queryTrainer = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
                        join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$departments."' and promotion_logs.`position`='sub leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='leader')";
        $leaderForeman = DB::select($queryLeaderForeman);
        $foreman = DB::select($queryForeman);
        $trainer = DB::select($queryTrainer);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $queryPeriode = "select DISTINCT(weekly_calendars.fiscal_year) from weekly_calendars";
        $periode = DB::select($queryPeriode);        

        $trainingReport = TrainingReport::find($training_id);

        $data = array('product' => $product,
                      'leaderForeman' => $leaderForeman,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'periode' => $periode,
                      'trainer' => $trainer,
                      'activity_name' => $activity_name,
                      'training_report' => $trainingReport,
                      'id' => $id);
        return view('training_report.edit', $data
            )->with('page', 'Training Report');
    }

    function update(Request $request,$id,$training_id)
    {
        try{
                $training_report = TrainingReport::find($training_id);
                $training_report->activity_list_id = $id;
                $training_report->department = $request->get('department');
                $training_report->section = $request->get('section');
                $training_report->product = $request->get('product');
                $training_report->periode = $request->get('periode');
                $training_report->date = $request->get('date');
                $training_report->time = $request->get('time');
                $training_report->trainer = $request->get('trainer');
                $training_report->theme = $request->get('theme');
                $training_report->isi_training = $request->get('isi_training');
                $training_report->tujuan = $request->get('tujuan');
                $training_report->standard = $request->get('standard');
                $training_report->leader = $request->get('leader');
                $training_report->foreman = $request->get('foreman');
                $training_report->notes = $request->get('notes');
                $training_report->save();

            return redirect('/index/training_report/index/'.$id)->with('status', 'Training Report data has been updated.')->with('page', 'Training Report');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Training Report already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Training Report');
            }
          }
    }


    function details($id,$session_training)
    {
        // $activityList = ActivityList::find($id);
        $trainingReport = TrainingReport::find($id);

        $trainingPicture = TrainingPicture::where('training_id',$id)
            ->get();

        $trainingParticipant = TrainingParticipant::where('training_id',$id)
            ->get();

        $trainingParticipant2 = TrainingParticipant::where('training_id',$id)
            ->get();

        $jml_null = 0;
        foreach($trainingParticipant2 as $trainingParticipant2){
            if($trainingParticipant2->participant_absence == null){
                $jml_null = $jml_null + 1;
            }
        }

        $queryProduct = "select * from origin_groups";
        $product = DB::select($queryProduct);

        $activity_name = $trainingReport->activity_lists->activity_name;
        $departments = $trainingReport->activity_lists->departments->department_name;
        $id_departments = $trainingReport->activity_lists->departments->id;
        $activity_alias = $trainingReport->activity_lists->activity_alias;
        $activity_id = $trainingReport->activity_lists->id;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);
        // var_dump($productionAudit);
        $data = array('training_report' => $trainingReport,
                      'training_picture' => $trainingPicture,
                      'training_participant' => $trainingParticipant,
                      'product' => $product,
                      'jml_null' => $jml_null,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'activity_id' => $activity_id,
                      'session_training' => $session_training,
                      'id_departments' => $id_departments);
        return view('training_report.details', $data
            )->with('page', 'Training Report');
    }

    function insertpicture(Request $request, $id)
    {
            $id_user = Auth::id();
            $tujuan_upload = 'data_file/training';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $file->getClientOriginalName();
            $file->move($tujuan_upload,$file->getClientOriginalName());

            TrainingPicture::create([
                'training_id' => $id,
                'picture' => $nama_file,
                'created_by' => $id_user
            ]);
        

        return redirect('index/training_report/details/'.$id.'/view')
            ->with('page', 'Training Report')->with('status', 'New Pictrue has been created.');
    }

    function insertparticipant(Request $request, $id)
    {
            $id_user = Auth::id();

            TrainingParticipant::create([
                'training_id' => $id,
                'participant_id' => $request->input('participant_id'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/training_report/details/'.$id.'/view')
            ->with('page', 'Training Report')->with('status', 'New Participant has been created.');
    }

    public function destroypicture($id,$picture_id)
    {
      $trainingPicture = TrainingPicture::find($picture_id);
      $trainingPicture->delete();

      return redirect('/index/training_report/details/'.$id.'/view')
        ->with('status', 'Training Picture has been deleted.')
        ->with('page', 'Training Report');
        //
    }

    public function destroyparticipant($id,$participant_id)
    {
      $trainingParticipant = TrainingParticipant::find($participant_id);
      $trainingParticipant->delete();

      return redirect('/index/training_report/details/'.$id.'/view')
        ->with('status', 'Training Participant has been deleted.')
        ->with('page', 'Training Report');
        //
    }

    function editpicture(Request $request, $id,$picture_id)
    {
        try{
            $tujuan_upload = 'data_file/training';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $file->getClientOriginalName();
            $file->move($tujuan_upload,$file->getClientOriginalName());

            $training_picture = TrainingPicture::find($picture_id);
            $training_picture->picture = $nama_file;
            $training_picture->save();

            return redirect('/index/training_report/details/'.$id.'/view')->with('status', 'Training Picture data has been updated.')->with('page', 'Training Report');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Training Picture already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Training Report');
            }
          }
    }

    function editparticipant(Request $request, $id,$participant_id)
    {
        try{
            $training_participant = TrainingParticipant::find($participant_id);
            $training_participant->participant_id = $request->input('participant_name');
            $training_participant->save();

            return redirect('/index/training_report/details/'.$id.'/view')->with('status', 'Training Picture data has been updated.')->with('page', 'Training Report');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Training Participant already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Training Report');
            }
          }
    }

    function report_training($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('training_report.report_training',  array('title' => 'Report Training',
            'title_jp' => 'Report Training',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Training');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select week_date, count(*) as jumlah_training from weekly_calendars join training_reports on training_reports.date = weekly_calendars.week_date join activity_lists on activity_lists.id = training_reports.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."' and training_reports.deleted_at is null GROUP BY week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detailTraining(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *, training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id where department_id = '".$id."' and activity_type = 'Training' and date = '".$week_date."' and training_reports.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    function print_training($id)
    {
        $training = TrainingReport::find($id);
        $activity_list_id = $training->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $trainingPictureQuery = "select * from training_pictures where training_id = '".$id."' and deleted_at is null";
        $trainingPicture = DB::select($trainingPictureQuery);
        $trainingParticipantQuery = "select participant_id, training_id,training_participants.id,employees.name,participant_absence from training_participants join employees on employees.employee_id = training_participants.participant_id where training_participants.training_id = '".$id."' and training_participants.deleted_at is null";
        $trainingParticipant = DB::select($trainingParticipantQuery);
        if($training == null){
            return redirect('/index/training_report/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Training Report');
        }else{
            $data = array(
                          'training' => $training,
                          'trainingPicture' => $trainingPicture,
                          'trainingParticipant' => $trainingParticipant,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('training_report.print', $data
                )->with('page', 'Training Report');
        }
    }

    function print_training_email($id)
    {
        $training = TrainingReport::find($id);
        $activity_list_id = $training->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $trainingPictureQuery = "select * from training_pictures where training_id = '".$id."' and deleted_at is null";
        $trainingPicture = DB::select($trainingPictureQuery);
        $trainingParticipantQuery = "select participant_id, training_id,training_participants.id,employees.name,participant_absence from training_participants join employees on employees.employee_id = training_participants.participant_id where training_participants.training_id = '".$id."' and training_participants.deleted_at is null";
        $trainingParticipant = DB::select($trainingParticipantQuery);
        if($training == null){
            return redirect('/index/training_report/index/'.$id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Training Report');
        }else{
            $data = array(
                          'training' => $training,
                          'trainingPicture' => $trainingPicture,
                          'trainingParticipant' => $trainingParticipant,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('training_report.print_email', $data
                )->with('page', 'Training Report');
        }
    }

    function scan_employee($id)
    {
            $data = array(
                          'id' => $id);
            return view('training_report.scan_employee2', $data
                )->with('page', 'Training Report');
    }

    function cek_employee($nik,$id)
    {
        // $emp = DB::table('employees')->where('employees.employee_id',$nik)->paginate(1);
        // $data = array('employees' => $emp);
        // return view('materials.cek', $data);
        $id_user = Auth::id();

        // TrainingParticipant::create([
        //     'training_id' => '2',
        //     'participant_name' => $nik,
        //     'created_by' => $id_user
        // ]);

        $training = TrainingParticipant::where('participant_id',$nik)->where('training_id',$id)->get();
        foreach($training as $training){
            $training->participant_absence = 'Hadir';
        }
        $training->save();

        // DB::table('training_participants')->where('participant_name',$nik)->where('training_id',$id_training)->update([
        //     'participant_absence' => "Hadir"
        // ]);
        

        return redirect('index/training_report/details/'.$id.'/view')
            ->with('page', 'Training Report')->with('status', 'Participant has been attend.');
    }

    public function getparticipant(Request $request)
    {
         try{
            $participant = TrainingParticipant::find($request->get("id"));
            $participant_id = $participant->participant_id;
            // $name = $beacon->name;
            // $beacon->uuid = $request->get('uuid');
            // $beacon->name = $request->get('name');
           

            $response = array(
              'status' => true,
              'participant_id' => $participant_id
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

    public function sendemail($id)
      {
          $query_training = "select *,training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id join departments on activity_lists.department_id = departments.id where training_reports.id = '".$id."'";
          
          $training = DB::select($query_training);
          $training3 = DB::select($query_training);
          // $training2 = DB::select($query_training);

          if($training != null){
            foreach($training as $training){
              $foreman = $training->foreman;
              $send_status = $training->send_status;
              $activity_list_id = $training->activity_list_id;
              $training2 = TrainingReport::find($id);
              $training2->send_status = "Sent";
              $training2->send_date = date('Y-m-d');
              $training2->approval_leader = "Approved";
              $training2->approved_date_leader = date('Y-m-d');
              $training2->save();
              // var_dump($id);
            }
            $queryEmail = "select employees.employee_id,employees.name,email from users join employees on employees.employee_id = users.username where employees.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
              // var_dump($mail_to);
            }
          }
          else{
            return redirect('/index/training_report/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Training Report');
          }

          if($send_status == "Sent"){
            return redirect('/index/training_report/index/'.$activity_list_id)->with('error', 'Data pernah dikirim.')->with('page', 'Training Report');
          }
          
          elseif($training != null){
              Mail::to($mail_to)->send(new SendEmail($training3, 'training'));
              return redirect('/index/training_report/index/'.$activity_list_id)->with('status', 'Your E-mail has been sent.')->with('page', 'Training Report');
          }
          else{
            return redirect('/index/training_report/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Training Report');
          }
      }

      public function approval(Request $request,$id)
      {
          $approve = $request->get('approve');
          $approvecount = count($approve);
          if($approvecount < 10){
            // echo "<script>alert('Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')</script>";
            return redirect('/index/training_report/print_training_email/'.$id)->with('error', 'Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')->with('page', 'Training Report');
          }
          else{
                $training = TrainingReport::find($id);
                $training->approval = "Approved";
                $training->approved_date = date('Y-m-d');
                $training->save();
            return redirect('/index/training_report/print_training_email/'.$id)->with('status', 'Approved.')->with('page', 'Training Report');
          }
      }
}
