<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Labeling;
use App\WeeklyCalendar;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class LabelingController extends Controller
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
      $this->product = ['All',
      					'Saxophone',
                        'Flute',
                        'Clarinet',
                        'Venova',
                        'Recorder',
                        'Pianica'];
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	$labeling = Labeling::where('activity_list_id',$id)
            ->orderBy('labelings.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

    	$data = array('labeling' => $labeling,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
                      'frequency' => $frequency,
                      'id_departments' => $id_departments);
    	return view('labeling.index', $data
    		)->with('page', 'Labeling');
    }

    function filter_labeling(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $labeling = Labeling::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('labelings.id','desc')
                ->get();
        }
        else{
            $labeling = Labeling::where('activity_list_id',$id)
            ->orderBy('labelings.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;
        // }
        $data = array(
                      'labeling' => $labeling,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'frequency' => $frequency,
                      'id_departments' => $id_departments);
        return view('labeling.index', $data
            )->with('page', 'Labeling');
    }

    function show($id,$labeling_id)
    {
        $activityList = ActivityList::find($id);
        $labeling = Labeling::find($labeling_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        // }
        $data = array('labeling' => $labeling,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('labeling.view', $data
            )->with('page', 'Labeling');
    }

    public function destroy($id,$labeling_id)
    {
      $labeling = Labeling::find($labeling_id);
      $labeling->delete();

      return redirect('/index/labeling/index/'.$id)
        ->with('status', 'Label has been deleted.')
        ->with('page', 'Labeling');        
    }

    function create($id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $date = date('Y-m-d');

        $fyQuery = "SELECT DISTINCT(fiscal_year) FROM weekly_calendars where week_date = '".$date."'";
        $fyHasil = DB::select($fyQuery);

        foreach($fyHasil as $fyHasil){
        	$fy = $fyHasil->fiscal_year;
        }

        $data = array(
                      'product' => $this->product,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'fy' => $fy,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('labeling.create', $data
            )->with('page', 'Labeling');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            $tujuan_upload = 'data_file/labeling';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);

            $file2 = $request->file('file2');
            $nama_file2 = $file2->getClientOriginalName();
            $filename2 = md5(date("dmYhis-A")).'.'.$file2->getClientOriginalExtension();
            $file2->move($tujuan_upload,$filename2);

            Labeling::create([
                'activity_list_id' => $id,
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'product' => $request->input('product'),
                'periode' => $request->input('periode'),
                'date' => $request->input('date'),
                'nama_mesin' => $request->input('nama_mesin'),
                'foto_arah_putaran' => $filename,
                'foto_sisa_putaran' => $filename2,
                'keterangan' => $request->input('keterangan'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
        

        return redirect('index/labeling/index/'.$id)
            ->with('page', 'Labeling')->with('status', 'New Label has been created.');
    }

    function edit($id,$labeling_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $date = date('Y-m-d');

        $fyQuery = "SELECT DISTINCT(fiscal_year) FROM weekly_calendars where week_date = '".$date."'";
        $fyHasil = DB::select($fyQuery);

        foreach($fyHasil as $fyHasil){
        	$fy = $fyHasil->fiscal_year;
        }

        $labeling = Labeling::find($labeling_id);

        $data = array(
                      'product' => $this->product,
                      'foreman' => $foreman,
                      'leader' => $leader,
                      'labeling' => $labeling,
                      'fy' => $fy,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('labeling.edit', $data
            )->with('page', 'Labeling');
    }

    function update(Request $request,$id,$labeling_id)
    {
        try{
            $tujuan_upload = 'data_file/labeling';
            $date = date('Y-m-d');

            if($request->file('file') != null && $request->file('file2') != null){
                $file = $request->file('file');
                $nama_file = $file->getClientOriginalName();
                $file->getClientOriginalName();
                $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();
                $file->move($tujuan_upload,$filename);

                $file2 = $request->file('file2');
                $nama_file2 = $file2->getClientOriginalName();
                $file2->getClientOriginalName();
                $filename2 = md5(date("dmYhis-A")).'.'.$file2->getClientOriginalExtension();
                $file2->move($tujuan_upload,$filename2);

                $labeling = Labeling::find($labeling_id);
                $labeling->activity_list_id = $id;
                $labeling->department = $request->get('department');
                $labeling->section = $request->get('section');
                $labeling->product = $request->get('product');
                $labeling->periode = $request->get('periode');
                $labeling->date = $request->get('date');
                $labeling->nama_mesin = $request->get('nama_mesin');
                $labeling->foto_arah_putaran = $filename;
                $labeling->foto_sisa_putaran = $filename2;
                $labeling->keterangan = $request->get('keterangan');
                $labeling->leader = $request->get('leader');
                $labeling->foreman = $request->get('foreman');
                $labeling->save();
            }
            elseif($request->file('file') != null && $request->file('file2') == null){
                $file = $request->file('file');
                $nama_file = $file->getClientOriginalName();
                $file->getClientOriginalName();
                $file->move($tujuan_upload,$file->getClientOriginalName());

                $labeling = Labeling::find($labeling_id);
                $labeling->activity_list_id = $id;
                $labeling->department = $request->get('department');
                $labeling->section = $request->get('section');
                $labeling->product = $request->get('product');
                $labeling->periode = $request->get('periode');
                $labeling->date = $request->get('date');
                $labeling->nama_mesin = $request->get('nama_mesin');
                $labeling->foto_arah_putaran = $nama_file;
                $labeling->foto_sisa_putaran = $request->get('foto_sisa_putaran');
                $labeling->keterangan = $request->get('keterangan');
                $labeling->leader = $request->get('leader');
                $labeling->foreman = $request->get('foreman');
                $labeling->save();
            }
            elseif($request->file('file') == null && $request->file('file2') != null){
            	$file2 = $request->file('file2');
                $nama_file2 = $file2->getClientOriginalName();
                $file2->getClientOriginalName();
                $file2->move($tujuan_upload,$file2->getClientOriginalName());

                $labeling = Labeling::find($labeling_id);
                $labeling->activity_list_id = $id;
                $labeling->department = $request->get('department');
                $labeling->section = $request->get('section');
                $labeling->product = $request->get('product');
                $labeling->periode = $request->get('periode');
                $labeling->date = $request->get('date');
                $labeling->nama_mesin = $request->get('nama_mesin');
                $labeling->foto_arah_putaran = $request->get('foto_arah_putaran');
                $labeling->foto_sisa_putaran = $nama_file2;
                $labeling->keterangan = $request->get('keterangan');
                $labeling->leader = $request->get('leader');
                $labeling->foreman = $request->get('foreman');
                $labeling->save();
            }
            else{
            	$labeling = Labeling::find($labeling_id);
                $labeling->activity_list_id = $id;
                $labeling->department = $request->get('department');
                $labeling->section = $request->get('section');
                $labeling->product = $request->get('product');
                $labeling->periode = $request->get('periode');
                $labeling->date = $request->get('date');
                $labeling->nama_mesin = $request->get('nama_mesin');
                $labeling->foto_arah_putaran = $request->get('foto_arah_putaran');
                $labeling->foto_sisa_putaran = $request->get('foto_sisa_putaran');
                $labeling->keterangan = $request->get('keterangan');
                $labeling->leader = $request->get('leader');
                $labeling->foreman = $request->get('foreman');
                $labeling->save();
            }

            return redirect('/index/labeling/index/'.$id)->with('status', 'Labeling data has been updated.')->with('page', 'Labeling');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Label already exist.')->with('page', 'Labeling');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Labeling');
            }
          }
    }

    function print_labeling(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        if($request->get('month') != null){
            $date = $request->get('month');
            $queryLabeling = "select *
					from labelings
					join activity_lists on activity_lists.id = activity_list_id
					join departments on departments.id =  activity_lists.department_id
					where DATE_FORMAT(labelings.date,'%Y-%m') = '".$date."'
                    and activity_list_id = '".$id."'
					and department_id = '".$id_departments."'
					and labelings.deleted_at is null";
            $labeling = DB::select($queryLabeling);
            $labeling2 = DB::select($queryLabeling);
        }        
        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($labeling as $labeling){
            $product = $labeling->product;
            $section = $labeling->section;
            $fy = WeeklyCalendar::where('week_date',$labeling->date)->first();
            $periode = $fy->fiscal_year;
            $foreman = $labeling->foreman;
            $leader = $labeling->leader;
            if ($labeling->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($labeling->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $labeling->approved_date;
            $approved_date_leader = $labeling->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($date));

        if($labeling == null){
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'product' => $product,
                          'foreman' => $foreman,
                          'leader' => $leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'section' => $section,
                          'periode' => $periode,
                          'monthTitle' => $monthTitle,
                          'labeling' => $labeling,
                          'labeling2' => $labeling2,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('labeling.print', $data
                )->with('page', 'Labeling');
        }
    }

    function print_labeling_email(Request $request,$id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

            $queryLabeling = "select *,labelings.id as id_labeling
					from labelings
					join activity_lists on activity_lists.id = activity_list_id
					join departments on departments.id =  activity_lists.department_id
					where DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                    and activity_list_id = '".$id."'
					and department_id = '".$id_departments."'
					and labelings.deleted_at is null";
            $labeling = DB::select($queryLabeling);
            $labeling2 = DB::select($queryLabeling);

        $jml_null = 0;
        $jml_null_leader = 0;

        foreach($labeling as $labeling){
            $product = $labeling->product;
            $section = $labeling->section;
            $periode = $labeling->periode;
            $foreman = $labeling->foreman;
            $leader = $labeling->leader;
            if ($labeling->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($labeling->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $labeling->approved_date;
            $approved_date_leader = $labeling->approved_date_leader;
        }

        $monthTitle = date("F Y", strtotime($month));

        if($labeling == null){
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'product' => $product,
                          'foreman' => $foreman,
                          'leader' => $leader,
                          'approved_date' => $approved_date,
                          'approved_date_leader' => $approved_date_leader,
                          'jml_null' => $jml_null,
                          'jml_null_leader' => $jml_null_leader,
                          'section' => $section,
                          'periode' => $periode,
                          'monthTitle' => $monthTitle,
                          'labeling' => $labeling,
                          'labeling2' => $labeling2,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'month' => $month,
                          'id_departments' => $id_departments);
            return view('labeling.print_email', $data
                )->with('page', 'Labeling');
        }
    }

    public function sendemail(Request $request,$id)
      {
      	 $activityList = ActivityList::find($id);
         $activity_name = $activityList->activity_name;
         $departments = $activityList->departments->department_name;
         $activity_alias = $activityList->activity_alias;
         $id_departments = $activityList->departments->id;

          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $queryLabeling = "select *,labelings.id as id_labeling,DATE_FORMAT(labelings.date,'%Y-%m') as month
					from labelings
					join activity_lists on activity_lists.id = activity_list_id
					join departments on departments.id =  activity_lists.department_id
					where DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
					and department_id = '".$id_departments."'
					and labelings.deleted_at is null";
          $labeling = DB::select($queryLabeling);
          $labeling2 = DB::select($queryLabeling);
          $labeling3 = DB::select($queryLabeling);

          // var_dump($sampling_check2);

          if($labeling2 != null){
            foreach($labeling2 as $labeling2){
                $foreman = $labeling2->foreman;
                $leader = $labeling2->leader;
                $id_labeling = $labeling2->id_labeling;
                $send_status = $labeling2->send_status;
              }

              foreach ($labeling3 as $labeling3) {
                    $label = Labeling::find($labeling3->id_labeling);
                    $label->send_status = "Sent";
                    $label->send_date = date('Y-m-d');
                    $label->approval_leader = "Approved";
                    $label->approved_date_leader = date('Y-m-d');
                    $label->save();
              }

              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/labeling/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Labeling');
          }

          if($send_status == "Sent"){
            return redirect('/index/labeling/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Labeling');
          }
          elseif($labeling != null){
              Mail::to($mail_to)->send(new SendEmail($labeling, 'labeling'));
              return redirect('/index/labeling/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Labeling');
          }
          else{
            return redirect('/index/labeling/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Labeling');
          }
      }

      public function approval(Request $request,$id,$month)
      {
          $approve = $request->get('approve');
          if(count($approve) > 0){
            foreach($approve as $approve){
                $labeling = Labeling::find($approve);
                $month = substr($labeling->date,0,7);
                $date = $labeling->date;
                $labeling->approval = "Approved";
                $labeling->approved_date = date('Y-m-d');
                $labeling->save();
              }
              return redirect('/index/labeling/print_labeling_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Labeling');
          }
          else{
            return redirect('/index/labeling/print_labeling_email/'.$id.'/'.$month)->with('error', 'Not Approved.')->with('page', 'Labeling');
          }
      }
}
