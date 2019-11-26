<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Interview;
use App\InterviewDetail;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
      	$interview = Interview::where('activity_list_id',$id)
              ->orderBy('interviews.id','desc')->get();

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
        $subsection4 = DB::select($querySubSection);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        // var_dump($productionAudit);
    	$data = array('interview' => $interview,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
    				  'departments' => $departments,
    				  'leader' => $leader,
                      'foreman' => $foreman,
                      'section' => $section,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('interview.index', $data
    		)->with('page', 'Interview');
    }

    function filter_interview(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySubSection = "select sub_section_name,section_name from sub_sections join sections on sections.id =  sub_sections.id_section join departments on sections.id_department = departments.id where departments.department_name = '".$departments."'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);
        $subsection4 = DB::select($querySubSection);

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            // $date = date('Y-m',$request->get('month'));
            // $year = date_format($date, 'Y');
            // $month = date_format($date, 'm');
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $interview = Interview::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $interview = Interview::where('activity_list_id',$id)
                // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $interview = Interview::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        else{
            $interview = Interview::where('activity_list_id',$id)
                ->orderBy('interviews.id','desc')
                ->get();
        }
        $data = array(
                      'interview' => $interview,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
                      'departments' => $departments,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'section' => $section,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('interview.index', $data
            )->with('page', 'Interview');
    }

    function show($id,$interview_id)
    {
        $activityList = ActivityList::find($id);
        $interview = Interview::find($interview_id);
        
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        $data = array('interview' => $interview,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.view', $data
            )->with('page', 'Interview');
    }

    public function destroy($id,$interview_id)
    {
      $interview = Interview::find($interview_id);
      $interview->delete();

      return redirect('/index/interview/index/'.$id)
        ->with('status', 'Interview has been deleted.')
        ->with('page', 'Interview');        
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

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryFY = "select DISTINCT(fiscal_year)from weekly_calendars";
        $fy = DB::select($queryFY);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'fy' => $fy,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.create', $data
            )->with('page', 'Interview');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            Interview::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'date' => $request->input('date'),
                'periode' => $request->input('periode'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/interview/index/'.$id)
            ->with('page', 'Interview')->with('status', 'New Interview has been created.');
    }

    function edit($id,$interview_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "select * from sections where id_department = '".$id_departments."'";
        $section = DB::select($querySection);

        $querySubSection = "select sub_section_name from sub_sections join sections on sections.id = sub_sections.id_section where sections.id_department = '".$id_departments."'";
        $subsection = DB::select($querySubSection);

        $queryFY = "select DISTINCT(fiscal_year)from weekly_calendars";
        $fy = DB::select($queryFY);

        $interview = Interview::find($interview_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'fy' => $fy,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'interview' => $interview,
                      'id' => $id);
        return view('interview.edit', $data
            )->with('page', 'Interview');
    }

    function update(Request $request,$id,$interview_id)
    {
        try{
                $month = date("m",strtotime($request->get('date')));
                $interview = Interview::find($interview_id);
                $interview->activity_list_id = $id;
                $interview->department = $request->get('department');
                $interview->section = $request->get('section');
                $interview->subsection = $request->get('subsection');
                $interview->date = $request->get('date');
                $interview->periode = $request->get('periode');
                $interview->leader = $request->get('leader');
                $interview->foreman = $request->get('foreman');
                $interview->save();

            return redirect('/index/interview/index/'.$id)->with('status', 'Interview data has been updated.')->with('page', 'Interview');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Interview already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    function details($interview_id)
    {
        $interview_detail = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_detail2 = InterviewDetail::where('interview_id',$interview_id)
            ->get();

        $interview = Interview::find($interview_id);

        $activity_name = $interview->activity_lists->activity_name;
        $departments = $interview->activity_lists->departments->department_name;
        $id_departments = $interview->activity_lists->departments->id;
        $activity_alias = $interview->activity_lists->activity_alias;
        $activity_id = $interview->activity_lists->id;

        $queryOperator = "select DISTINCT(employees.name),employees.employee_id from mutation_logs join employees on employees.employee_id = mutation_logs.employee_id where mutation_logs.department = '".$departments."'";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $data = array('interview_detail' => $interview_detail,
        			  'interview_detail2' => $interview_detail2,
                      'interview' => $interview,
                      'departments' => $departments,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'interview_id' => $interview_id,
                      'activity_id' => $activity_id,
                      'id_departments' => $id_departments);
        return view('interview.details', $data
            )->with('page', 'Interview Details');
    }

    function create_participant(Request $request)
    {
            try{    
              $id_user = Auth::id();
              $interview_id = $request->get('interview_id');
              if($request->input('pesertascan') == Null){
                InterviewDetail::create([
                    'interview_id' => $interview_id,
                    'nik' => $request->get('nik'),
                    'filosofi_yamaha' => $request->get('filosofi_yamaha'),
                    'aturan_k3' => $request->get('aturan_k3'),
                    'komitmen_berkendara' => $request->get('komitmen_berkendara'),
                    'kebijakan_mutu' => $request->get('kebijakan_mutu'),
                    'dasar_tindakan_bekerja' => $request->get('dasar_tindakan_bekerja'),
                    'enam_pasal_keselamatan' => $request->get('enam_pasal_keselamatan'),
                    'budaya_kerja' => $request->get('budaya_kerja'),
                    'budaya_5s' => $request->get('budaya_5s'),
                    'komitmen_hotel_konsep' => $request->get('komitmen_hotel_konsep'),
                    'janji_tindakan_dasar' => $request->get('janji_tindakan_dasar'),
                    'created_by' => $id_user
                ]);
              }else{
                InterviewDetail::create([
                    'interview_id' => $interview_id,
                    'nik' => $request->get('pesertascan'),
                    'filosofi_yamaha' => $request->get('filosofi_yamaha'),
                    'aturan_k3' => $request->get('aturan_k3'),
                    'komitmen_berkendara' => $request->get('komitmen_berkendara'),
                    'kebijakan_mutu' => $request->get('kebijakan_mutu'),
                    'dasar_tindakan_bekerja' => $request->get('dasar_tindakan_bekerja'),
                    'enam_pasal_keselamatan' => $request->get('enam_pasal_keselamatan'),
                    'budaya_kerja' => $request->get('budaya_kerja'),
                    'budaya_5s' => $request->get('budaya_5s'),
                    'komitmen_hotel_konsep' => $request->get('komitmen_hotel_konsep'),
                    'janji_tindakan_dasar' => $request->get('janji_tindakan_dasar'),
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

    public function getdetail(Request $request)
    {
         try{
            $detail = InterviewDetail::find($request->get("id"));
            $data = array('detail_id' => $detail->id,
                          'interview_id' => $detail->interview_id,
                          'nik' => $detail->nik,
                          'name' => $detail->participants->name,
                          'filosofi_yamaha' => $detail->filosofi_yamaha,
                          'aturan_k3' => $detail->aturan_k3,
                          'komitmen_berkendara' => $detail->komitmen_berkendara,
                          'kebijakan_mutu' => $detail->kebijakan_mutu,
                          'dasar_tindakan_bekerja' => $detail->dasar_tindakan_bekerja,
                          'enam_pasal_keselamatan' => $detail->enam_pasal_keselamatan,
                          'budaya_kerja' => $detail->budaya_kerja,
                          'budaya_5s' => $detail->budaya_5s,
                          'komitmen_hotel_konsep' => $detail->komitmen_hotel_konsep,
                          'janji_tindakan_dasar' => $detail->janji_tindakan_dasar);
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

    function edit_participant(Request $request,$interview_id,$detail_id)
    {
        try{
                $interview = InterviewDetail::find($detail_id);
                $interview->nik = $request->get('nik');
                $interview->filosofi_yamaha = $request->get('filosofi_yamaha');
                $interview->aturan_k3 = $request->get('aturan_k3');
                $interview->komitmen_berkendara = $request->get('komitmen_berkendara');
                $interview->kebijakan_mutu = $request->get('kebijakan_mutu');
                $interview->dasar_tindakan_bekerja = $request->get('dasar_tindakan_bekerja');
                $interview->enam_pasal_keselamatan = $request->get('enam_pasal_keselamatan');
                $interview->budaya_kerja = $request->get('budaya_kerja');
                $interview->budaya_5s = $request->get('budaya_5s');
                $interview->komitmen_hotel_konsep = $request->get('komitmen_hotel_konsep');
                $interview->janji_tindakan_dasar = $request->get('janji_tindakan_dasar');
                $interview->save();

            return redirect('index/interview/details/'.$interview_id)
              ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Participant already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    public function destroy_participant($interview_id,$detail_id)
    {
      $interview = InterviewDetail::find($detail_id);
      $interview->delete();

      return redirect('index/interview/details/'.$interview_id)
              ->with('page', 'Interview Details')->with('status', 'Participant has been deleted.');
    }

    function print_interview($interview_id)
    {
        $interview = Interview::find($interview_id);
        $activity_list_id = $interview->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $interviewDetailQuery = "select * from interview_details join employees on interview_details.nik = employees.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);

        if($interview == null){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Interview');
        }else{
            $data = array(
                          'interview' => $interview,
                          'interviewDetail' => $interviewDetail,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'interview_id' => $interview_id,
                          'id_departments' => $id_departments);
            return view('interview.print', $data
                )->with('page', 'Interview');
        }
    }

    function print_email($interview_id)
    {
        $interview = Interview::find($interview_id);
        $activity_list_id = $interview->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $interviewDetailQuery = "select * from interview_details join employees on interview_details.nik = employees.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);

        if($interview == null){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Interview');
        }else{
            $data = array(
                          'interview' => $interview,
                          'interviewDetail' => $interviewDetail,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'interview_id' => $interview_id,
                          'id_departments' => $id_departments);
            return view('interview.print_email', $data
                )->with('page', 'Interview');
        }
    }

    public function sendemail($interview_id)
      {
          $query_interview = "select *,interviews.id as interview_id from interviews join activity_lists on activity_lists.id = interviews.activity_list_id join departments on activity_lists.department_id = departments.id where interviews.id = '".$interview_id."' and interviews.deleted_at is null";
          
          $interview = DB::select($query_interview);
          $interview3 = DB::select($query_interview);
          // $training2 = DB::select($query_training);

          if($interview != null){
            foreach($interview as $interview){
              $foreman = $interview->foreman;
              $send_status = $interview->send_status;
              $activity_list_id = $interview->activity_list_id;
              $interview2 = Interview::find($interview_id);
              $interview2->send_status = "Sent";
              $interview2->send_date = date('Y-m-d');
              $interview2->approval_leader = "Approved";
              $interview2->approved_date_leader = date('Y-m-d');
              $interview2->save();
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
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
          }

          if($send_status == "Sent"){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data pernah dikirim.')->with('page', 'Interview');
          }
          
          elseif($interview != null){
              Mail::to($mail_to)->send(new SendEmail($interview3, 'interview'));
              return redirect('/index/interview/index/'.$activity_list_id)->with('status', 'Your E-mail has been sent.')->with('page', 'Interview');
          }
          else{
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
          }
      }

    public function approval(Request $request,$interview_id)
    {
        $approve = $request->get('approve');
        $interviewDetailQuery = "select * from interview_details join employees on interview_details.nik = employees.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);
        $jumlahDetail = count($interviewDetail);
        $approvecount = count($approve);
        if($approvecount < $jumlahDetail){
          return redirect('/index/interview/print_email/'.$interview_id)->with('error', 'Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')->with('page', 'Interview');
        }
        else{
            $interview = Interview::find($interview_id);
            $interview->approval = "Approved";
            $interview->approved_date = date('Y-m-d');
            $interview->save();
            return redirect('/index/interview/print_email/'.$interview_id)->with('status', 'Approved.')->with('page', 'Interview');
        }
    }
}
