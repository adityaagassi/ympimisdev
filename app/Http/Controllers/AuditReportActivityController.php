<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditReportActivity;
use App\AuditGuidance;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AuditReportActivityController extends Controller
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
    	$auditReportActivity = AuditReportActivity::where('activity_list_id',$id)->orderBy('audit_report_activities.id','desc')
            ->get();


    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($productionAudit);
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

    	$data = array('audit_report_activity' => $auditReportActivity,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
    				  'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('audit_report_activity.index', $data
    		)->with('page', 'Laporan Aktivitas Audit');
    }

    function filter_audit_report(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $auditReportActivity = AuditReportActivity::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $auditReportActivity = AuditReportActivity::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $auditReportActivity = AuditReportActivity::where('activity_list_id',$id)
                ->where('subsection',$subsection)
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        else{
            $auditReportActivity = AuditReportActivity::where('activity_list_id',$id)
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        $data = array(
                      'audit_report_activity' => $auditReportActivity,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('audit_report_activity.index', $data
            )->with('page', 'Laporan Aktivitas Audit');
    }

    function show($id,$audit_report_id)
    {
        $activityList = ActivityList::find($id);
        $auditReportActivity = AuditReportActivity::find($audit_report_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $data = array('audit_report_activity' => $auditReportActivity,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('audit_report_activity.view', $data
            )->with('page', 'Laporan Aktivitas Audit');
    }

    public function destroy($id,$audit_report_id)
    {
      $auditReportActivity = AuditReportActivity::find($audit_report_id);
      $auditReportActivity->delete();

      return redirect('/index/audit_report_activity/index/'.$id)
        ->with('status', 'Laporan Aktivitas Audit has been deleted.')
        ->with('page', 'Laporan Aktivitas Audit');        
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

        $bulan = date('Y-m');

        $guidance = DB::SELECT("SELECT * FROM audit_guidances where activity_list_id = '".$id."' and deleted_at is null and status = 'Belum Dikerjakan'");

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department LIKE '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'guidance' => $guidance,
                      'operator' => $operator,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('audit_report_activity.create', $data
            )->with('page', 'Audit Report Activity');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            $audit_guidance_id = explode('_', $request->input('audit_guidance_id'));
            AuditReportActivity::create([
                'activity_list_id' => $id,
                'audit_guidance_id' => $audit_guidance_id[0],
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'date' => date('Y-m-d'),
                'nama_dokumen' => $request->input('nama_dokumen'),
                'no_dokumen' => $request->input('no_dokumen'),
                'kesesuaian_aktual_proses' => $request->input('kesesuaian_aktual_proses'),
                'tindakan_perbaikan' => $request->input('tindakan_perbaikan'),
                'target' => $request->input('target'),
                'kelengkapan_point_safety' => $request->input('kelengkapan_point_safety'),
                'kesesuaian_qc_kouteihyo' => $request->input('kesesuaian_qc_kouteihyo'),
                'condition' => $request->input('condition'),
                'handling' => $request->input('handling'),
                'operator' => $request->input('operator'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);

            $audit_guidance_id = $audit_guidance_id[0];
            $audit_guidance = AuditGuidance::find($audit_guidance_id);
            $audit_guidance->status = 'Sudah Dikerjakan';
            $audit_guidance->save();

        return redirect('index/audit_report_activity/index/'.$id)
            ->with('page', 'Audit Report Activity')->with('status', 'New Audit Report Activity has been created.');
    }

    function edit($id,$audit_report_id)
    {
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department LIKE '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $bulan = date('Y-m');

        $guidance = DB::SELECT("SELECT * FROM audit_guidances where activity_list_id = '".$id."' ");

        $audit_report_activity = AuditReportActivity::find($audit_report_id);

        $data = array('leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'guidance' => $guidance,
                      'operator' => $operator,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'audit_report_activity' => $audit_report_activity,
                      'id' => $id);
        return view('audit_report_activity.edit', $data
            )->with('page', 'Audit Report Activity');
    }

    function update(Request $request,$id,$audit_report_id)
    {
        try{
                $audit_report_activity = AuditReportActivity::find($audit_report_id);
                $audit_report_activity->activity_list_id = $id;
                $audit_report_activity->audit_guidance_id = $request->get('audit_guidance_id');
                $audit_report_activity->department = $request->get('department');
                $audit_report_activity->section = $request->get('section');
                $audit_report_activity->subsection = $request->get('subsection');
                $audit_report_activity->date = date('Y-m-d');
                $audit_report_activity->nama_dokumen = $request->get('nama_dokumen');
                $audit_report_activity->no_dokumen = $request->get('no_dokumen');
                $audit_report_activity->kesesuaian_aktual_proses = $request->get('kesesuaian_aktual_proses');
                $audit_report_activity->tindakan_perbaikan = $request->get('tindakan_perbaikan');
                $audit_report_activity->target = $request->get('target');
                $audit_report_activity->kelengkapan_point_safety = $request->get('kelengkapan_point_safety');
                $audit_report_activity->kesesuaian_qc_kouteihyo = $request->get('kesesuaian_qc_kouteihyo');
                $audit_report_activity->condition = $request->get('condition');
                $audit_report_activity->handling = $request->get('handling');
                $audit_report_activity->operator = $request->get('operator');
                $audit_report_activity->leader = $request->get('leader');
                $audit_report_activity->foreman = $request->get('foreman');
                $audit_report_activity->save();

            return redirect('/index/audit_report_activity/index/'.$id)->with('status', 'Audit Report Activity data has been updated.')->with('page', 'Audit Report Activity');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Audit Report Activity already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Audit Report Activity');
            }
          }
    }

    function report_audit_activity($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('audit_report_activity.report_audit_activity',  array('title' => 'Report Audit Activity',
            'title_jp' => 'Report Audit Activity',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Audit Activity');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select count(*) as jumlah_laporan, date
                from audit_report_activities
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where actlist.department_id = '".$id."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                        and audit_report_activities.deleted_at is null GROUP BY date");
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

    public function detail_laporan_aktivitas(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *,CONCAT(activity_lists.id, '/', audit_report_activities.subsection, '/', DATE_FORMAT(audit_report_activities.date,'%Y-%m')) as linkurl, audit_report_activities.id as laporan_aktivitas_id from audit_report_activities join activity_lists on activity_lists.id = audit_report_activities.activity_list_id where department_id = '".$id."' and activity_type = 'Laporan Aktivitas' and date = '".$week_date."' and audit_report_activities.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    function print_audit_report($id,$month)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            // $month = $request->get('month');
            $queryLaporanAktivitas = "select *, audit_report_activities.id as id_audit_report
                from audit_report_activities
                join activity_lists on activity_lists.id = audit_report_activities.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."' 
                and audit_report_activities.deleted_at is null";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            // $data = array(
            //               'subsection' => $subsection,
            //               'leader' => $leader,
            //               'foreman' => $foreman,
            //               'section' => $section,
            //               'monthTitle' => $monthTitle,
            //               'date' => $date,
            //               'jml_null' => $jml_null,
            //               'approved_date' => $approved_date,
            //               'approval_leader' => $approval_leader,
            //               'approved_date_leader' => $approved_date_leader,
            //               'laporanAktivitas' => $laporanAktivitas,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'id_departments' => $id_departments);
            // return view('audit_report_activity.print', $data
            //     )->with('page', 'Laporan Aktivitas Audit');

            $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'landscape');

           $pdf->loadView('audit_report_activity.print', array(
                'subsection' => $subsection,
                  'leader' => $leader,
                  'foreman' => $foreman,
                  'section' => $section,
                  'monthTitle' => $monthTitle,
                  'date' => $date,
                  'jml_null' => $jml_null,
                  'month' => $month,
                  'approved_date' => $approved_date,
                  'approval_leader' => $approval_leader,
                  'approved_date_leader' => $approved_date_leader,
                  'laporanAktivitas' => $laporanAktivitas,
                  'departments' => $departments,
                  'activity_name' => $activity_name,
                  'activity_alias' => $activity_alias,
                  'id' => $id,
                  'id_departments' => $id_departments
           ));

           return $pdf->stream("Audit IK ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_audit_report_chart($id,$subsection,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($subsection != null && $month != null){
            $queryLaporanAktivitas = "select *, audit_report_activities.id as id_audit_report
                from audit_report_activities
                join activity_lists on activity_lists.id = audit_report_activities.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and audit_report_activities.subsection = '".$subsection."' 
                and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."' 
                and audit_report_activities.deleted_at is null";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'laporanAktivitas' => $laporanAktivitas,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_report_activity.print_chart', $data
                )->with('page', 'Laporan Aktivitas Audit');
        }
    }

    function print_audit_report_email($id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            $queryLaporanAktivitas = "select *, audit_report_activities.id as id_audit_report
                from audit_report_activities
                join activity_lists on activity_lists.id = audit_report_activities.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."' 
                and audit_report_activities.deleted_at is null";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'role_code' => Auth::user()->role_code,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'monthTitle' => $monthTitle,
                          'date' => $date,
                          'month' => $month,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'laporanAktivitas' => $laporanAktivitas,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_report_activity.print_email', $data
                )->with('page', 'Laporan Aktivitas Audit');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $subsection = $request->get('subsection');
          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_laporan_aktivitas = "select *, audit_report_activities.id as id_audit_report
                from audit_report_activities
                join activity_lists on activity_lists.id = audit_report_activities.activity_list_id
                join departments on departments.id =  activity_lists.department_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and audit_report_activities.subsection = '".$subsection."'
                and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                and audit_report_activities.deleted_at is null";
          $laporan_aktivitas = DB::select($query_laporan_aktivitas);
          $laporan_aktivitas2 = DB::select($query_laporan_aktivitas);
          $laporan_aktivitas3 = DB::select($query_laporan_aktivitas);

          // var_dump($sampling_check2);

          if($laporan_aktivitas2 != null){
            foreach($laporan_aktivitas2 as $laporan_aktivitas2){
                $foreman = $laporan_aktivitas2->foreman;
                $id_audit_report = $laporan_aktivitas2->id_audit_report;
                $send_status = $laporan_aktivitas2->send_status;
              }

              foreach ($laporan_aktivitas3 as $laporan_aktivitas3) {
                    $laktivitas = AuditReportActivity::find($laporan_aktivitas3->id_audit_report);
                    $laktivitas->send_status = "Sent";
                    $laktivitas->send_date = date('Y-m-d');
                    $laktivitas->approval_leader = "Approved";
                    $laktivitas->approved_date_leader = date('Y-m-d');
                    $laktivitas->save();
              }

              // $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              // var_dump($foreman);
              // var_dump($email);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/sampling_check/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Sampling Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/audit_report_activity/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Laporan Aktivitas Audit');
          }
          elseif($laporan_aktivitas != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($laporan_aktivitas, 'laporan_aktivitas'));
              return redirect('/index/audit_report_activity/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Laporan Aktivitas Audit');
          }
          else{
            return redirect('/index/audit_report_activity/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Laporan Aktivitas Audit');
          }
      }

      public function approval(Request $request,$id)
      {
          $approve = $request->get('approve');
          foreach($approve as $approve){
            $audit_report_activity = AuditReportActivity::find($approve);
            $subsection = $audit_report_activity->subsection;
            $month = substr($audit_report_activity->date,0,7);
            $date = $audit_report_activity->date;
            $audit_report_activity->approval = "Approved";
            $audit_report_activity->approved_date = date('Y-m-d');
            $audit_report_activity->save();
          }
          return redirect('/index/audit_report_activity/print_audit_report_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Laporan Aktivitas Audit');
      }

      public function getemployee(Request $request){

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where employee_id = '".$request->get('employee_id')."'";
        $employee = DB::select($queryOperator);
        foreach ($employee as $key) {
            $name = $key->name;
        }

        $response = array(
            'status' => true,
            'lists' => $employee,
            'name' => $name,
        );
        return Response::json($response);
    }

    public function scanEmployee(Request $request)
    {
      $nik = $request->get('employee_id');

      if(strlen($nik) > 9){
          $nik = substr($nik,0,9);
      }

      $employee = db::table('employees')->where('tag', 'like', '%'.$nik.'%')->first();

      if(count($employee) > 0){
        $response = array(
            'status' => true,
            'message' => 'Scan Peserta Berhasil',
            'employee' => $employee
        );
        return Response::json($response);
      }
      else{
          $response = array(
              'status' => false,
              'message' => 'Employee ID Invalid'
          );
          return Response::json($response);
      }
    }
}
