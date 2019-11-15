<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\ProductionAudit;
use Response;
use DataTables;
use Excel;
use App\User;
use App\PresenceLog;
use App\Division;
use App\Department;
use App\Section;
use App\SubSection;
use App\Group;
use App\Grade;
use App\Position;
use App\CostCenter;
use App\PromotionLog;
use App\Mutationlog;
use App\HrQuestionLog;
use App\HrQuestionDetail;
use App\Employee;
use App\EmploymentLog;
use App\OrganizationStructure;
use File;
use DateTime;
use Illuminate\Support\Arr;

class ProductionReportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    function index($id)
    {
        $queryActivity = "SELECT DISTINCT(activity_type) FROM activity_lists where department_id = '".$id."'";
    	$activityList = DB::select($queryActivity);
        $data = array('activity_list' => $activityList,
                    'id' => $id);
        return view('production_report.index', $data
          )->with('page', 'Production Report');
    }

    function activity($id)
    {
    	$activityList = ActivityList::find($id);
    	// foreach ($activityList as $activity) {
    		$activity_type = $activityList->activity_type;
    	// }
    	if ($activity_type == "Audit") {
    		return redirect('/index/production_audit/details/'.$id)->with('page', 'Production Audit')->with('no', '1');
    	}
    	elseif($activity_type == "Training"){
            return redirect('/index/training_report/index/'.$id)->with('page', 'Training')->with('no', '2');
    	}
    	elseif($activity_type == "Laporan Aktivitas"){
            return redirect('/index/audit_report_activity/index/'.$id)->with('page', 'Laporan Aktivitas')->with('no', '4');
    	}
    	elseif($activity_type == "Sampling Check"){
            return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', '3');
    	}
    	elseif($activity_type == "Pengecekan Foto"){

    	}
        elseif($activity_type == "Interview"){
            return redirect('/index/interview/index/'.$id)->with('page', 'Interview')->with('no', '7');
        }
        elseif($activity_type == "Labelisasi"){

        }
        elseif($activity_type == "Pengecekan"){

        }
        elseif($activity_type == "Pemahaman Proses"){

        }
    }

    function report_all($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        return view('production_report.report_all',  array('title' => 'Production Report',
            'title_jp' => 'Production Report',
            'id' => $id,
            'data' => $data,
            'departments' => $departments,
        ))->with('page', 'Report All');
    }

    public function fetchReportDaily(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week_date, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            week_date,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."'
                        and activity_lists.frequency = 'Daily')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Daily'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Daily'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Daily'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportWeekly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            DISTINCT(week_name) as week,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."' 
                        and activity_lists.frequency = 'Weekly')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.week_name = week)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Weekly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Weekly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportMonthly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $tahun = $request->get('week_date');
      }
      else{
        $tahun = date('Y');
      }

      $data = db::select("select tot.month, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            DISTINCT(DATE_FORMAT(week_date,'%Y-%m')) as month,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."' 
                        and activity_lists.frequency = 'Monthly')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = month
                    and actlist.frequency = 'Monthly'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                        and  actlist.frequency = 'Monthly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                        and actlist.frequency = 'Monthly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y') = '".$tahun."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailMonthly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        count(activity_lists.activity_alias) as jumlah_plan,
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                and  actlist.frequency = 'Monthly'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                and  actlist.frequency = 'Monthly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Monthly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Monthly'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportConditional(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week_date, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            week_date,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                and production_audits.date = week_date)
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Conditional'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and training_reports.date = week_date)
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.date = week_date)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and audit_report_activities.date = week_date)
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Conditional'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Conditional'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailConditional(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $tanggal = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$tanggal."'
                and  actlist.frequency = 'Conditional'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$tanggal."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$tanggal."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.date = '".$tanggal."'
                and  actlist.frequency = 'Conditional'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and actlist.frequency = 'Conditional'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and  actlist.frequency = 'Conditional'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Conditional'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailWeekly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $week_name = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        count(activity_lists.activity_alias) as jumlah_plan,
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$week_name."'
                and  actlist.frequency = 'Weekly'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$week_name."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$week_name."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.week_name = '".$week_name."'
                and  actlist.frequency = 'Weekly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.frequency = 'Weekly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and  actlist.frequency = 'Weekly'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Weekly'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportAudit(Request $request,$id)
    {
      if($request->get('tgl') != null){
        $bulan = $request->get('tgl');
        $frequency = $request->get('frequency');
        $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
        $fynow = DB::select("select fiscal_year from weekly_calendars where CURDATE() = week_date");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }
      }

      $data = DB::select("select weekly_calendars.week_date,count(*) as jumlah_semua, sum(case when production_audits.kondisi = 'Good' then 1 else 0 end) as jumlah_good, sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) as jumlah_not_good from (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."' and fiscal_year='".$fy."') as weekly_calendars join production_audits on production_audits.date = weekly_calendars.week_date join activity_lists on activity_lists.id = production_audits.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and production_audits.deleted_at is null GROUP BY  weekly_calendars.week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function fetchReportTraining(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $frequency = $request->get('frequency');
        $bulan = $request->get('week_date');
      }
      else{
        $frequency = $request->get('frequency');
        $bulan = date('Y-m');
      }

      $data = DB::select("select CONCAT(year(date),'-',month(date)) as month, (select count(*) as jumlah_training from activity_lists where activity_type = 'Training' and frequency = '".$frequency."') as plan, count(*) as jumlah_training from weekly_calendars join training_reports on training_reports.date = weekly_calendars.week_date join activity_lists on activity_lists.id = training_reports.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null GROUP BY CONCAT(year(date),'-',month(date))");
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

    public function fetchReportSampling(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
        $frequency = $request->get('frequency');
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
      }

      $data = DB::select("select week_date, count(*) as jumlah_sampling_check from weekly_calendars join sampling_checks on sampling_checks.date = weekly_calendars.week_date join activity_lists on activity_lists.id = sampling_checks.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and sampling_checks.deleted_at is null GROUP BY week_date");
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

    public function fetchReportLaporanAktivitas(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
        $frequency = $request->get('frequency');
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
      }

      $data = DB::select("select week_date, count(*) as jumlah_laporan from weekly_calendars join audit_report_activities on audit_report_activities.date = weekly_calendars.week_date join activity_lists on activity_lists.id = audit_report_activities.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = 'Monthly' and audit_report_activities.deleted_at is null GROUP BY week_date");
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
      if($request->get('week_date') != null){
        $week_date = $request->get("week_date");
        $frequency = $request->get('frequency');
      }
      else{
        $week_date = date('Y-m');
        $frequency = $request->get('frequency');
      }
      if($frequency == 'Conditional'){
        $query = "select *, training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id where department_id = '".$id."' and activity_type = 'Training' and training_reports.date = '".$week_date."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null";
      }
      else{
        $query = "select *, training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id where department_id = '".$id."' and activity_type = 'Training' and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null";
      }

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    public function detailProductionReport(Request $request, $id){
      $activity_type = $request->get("activity_type");
        $query = "SELECT *, activity_lists.id as id_activity FROM `activity_lists` join departments on departments.id = activity_lists.department_id where activity_lists.activity_type = '".$activity_type."' and activity_lists.deleted_at is null and activity_lists.department_id = '".$id."'";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    public function detailSamplingCheck(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *,CONCAT(activity_lists.id, '/', sampling_checks.subsection, '/', DATE_FORMAT(sampling_checks.date,'%Y-%m')) as linkurl, sampling_checks.id as sampling_check_id from sampling_checks join activity_lists on activity_lists.id = sampling_checks.activity_list_id where department_id = '".$id."' and activity_type = 'Sampling Check' and week_name = '".$week_date."' and sampling_checks.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    public function fetchPlanReport(Request $request, $id){
      $frequency = $request->get("frequency");
        $query = "select * from activity_lists where frequency = '".$frequency."' and department_id = '".$id."'";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    function report_by_act_type($id,$activity_type)
    {
        // $activityList = ActivityList::find($id);
        // // foreach ($activityList as $activity) {
        //     $activity_type = $activityList->activity_type;
        // }
        if ($activity_type == "Audit") {
            return redirect('/index/production_audit/report_audit/'.$id)->with('page', 'Production Audit');
        }
        elseif($activity_type == "Training"){
            return redirect('/index/training_report/report_training/'.$id)->with('page', 'Training');
        }
        elseif($activity_type == "Laporan Aktivitas"){
            var_dump("halooo");
        }
        elseif($activity_type == "Sampling Check"){
            return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', '3');
        }
        elseif($activity_type == "Pengecekan Foto"){

        }
        elseif($activity_type == "Interview"){

        }
        elseif($activity_type == "Labelisasi"){

        }
        elseif($activity_type == "Pengecekan"){

        }
    }
}
