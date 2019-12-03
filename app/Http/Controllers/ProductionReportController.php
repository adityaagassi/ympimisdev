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
        $queryActivity = "SELECT DISTINCT(activity_type) FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null";
    	$activityList = DB::select($queryActivity);
        $data = array('activity_list' => $activityList,
                    'id' => $id);
        return view('production_report.index', $data
          )->with('page', 'Leader Task Monitoring');
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
            return redirect('/index/daily_check_fg/product/'.$id)->with('page', 'Daily Check FG')->with('no', '8');
    	}
        elseif($activity_type == "Interview"){
            return redirect('/index/interview/index/'.$id)->with('page', 'Interview')->with('no', '7');
        }
        elseif($activity_type == "Labelisasi"){
            return redirect('/index/labeling/index/'.$id)->with('page', 'Labeling')->with('no', '9');
        }
        elseif($activity_type == "Pengecekan"){
            return redirect('/index/first_product_audit/index/'.$id)->with('page', 'First Product Audit')->with('no', '6');
        }
        elseif($activity_type == "Pemahaman Proses"){
            return redirect('/index/audit_process/index/'.$id)->with('page', 'Audit Process')->with('no', '8');
        }
    }

    function report_all($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }

        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();
        $queryLeader2 = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$departments."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='foreman')";
        $leader = DB::select($queryLeader2);
        $leader2 = DB::select($queryLeader2);
        $leader3 = DB::select($queryLeader2);

        $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        return view('production_report.report_all2',  array('title' => 'Leader Task Monitoring',
            'title_jp' => '職長業務管理',
            'id' => $id,
            'data' => $data,
            'activity_list' => $activityList,
            'leader2' => $leader2,
            'leader3' => $leader3,
            'leader' => $leader,
            'departments' => $departments,
        ))->with('page', 'Leader Task Monitoring');
    }

    public function fetchReportByLeader(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $date = db::select("select week_name,week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."'");

      $queryPMonth = "select DATE_FORMAT(date_sub(concat('".$bulan."','-01'), INTERVAL 1 MONTH),'%Y-%m') as last_month";
      $pMonth = DB::select($queryPMonth);

      foreach($pMonth as $pMonth){
        $prevMonth = $pMonth->last_month;
      }

      $queryLeader = "select DISTINCT(leader_dept) from activity_lists where department_id = '".$id."'";
      $leaderrr = DB::select($queryLeader);

      $data[] = null;
      $dataleader[] = null;

      $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."'");

      foreach($leaderrr as $leader){
        $dataleader = $leader->leader_dept; 
        $data[] = db::select("select monthly.leader_name,
        monthly.jumlah_activity_monthly,
        monthly.jumlah_training + monthly.jumlah_sampling + monthly.jumlah_laporan_aktivitas+ monthly.jumlah_labeling as jumlah_monthly,
        COALESCE(((monthly.jumlah_training + monthly.jumlah_sampling + monthly.jumlah_laporan_aktivitas+ monthly.jumlah_labeling)/monthly.jumlah_activity_monthly)*100,0) as persen_monthly,
        weekly.jumlah_activity_weekly as jumlah_activity_weekly,
        weekly.jumlah_sampling+weekly.jumlah_audit+weekly.jumlah_audit_process as jumlah_weekly,
        COALESCE(((weekly.jumlah_sampling + weekly.jumlah_audit_process + weekly.jumlah_audit)/(weekly.jumlah_activity_weekly))*100,0) as persen_weekly,
        daily.jumlah_activity_daily as jumlah_activity_daily,
        daily.jumlah_daily_check as jumlah_daily,
        COALESCE(((daily.jumlah_daily_check)/(daily.jumlah_activity_daily))*100,0) as persen_daily,
        daily.jumlah_day,
        daily.cur_day,
        (daily.cur_day / daily.jumlah_day)*100 as persen_cur_day,
        prev.plan_prev as plan_prev,
        prev.aktual_prev as aktual_prev,
        prev.persen_prev as persen_prev
        from 
        (select sum(plan_item) as jumlah_activity_monthly,
        leader_dept as leader_name,
        COALESCE((select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Monthly'
                                and training_reports.leader = '".$dataleader."'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY training_reports.leader),0)
        as jumlah_training,
        COALESCE((select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and sampling_checks.leader = '".$dataleader."'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY sampling_checks.leader),0)
        as jumlah_sampling,
        COALESCE((select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and audit_report_activities.leader = '".$dataleader."'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY audit_report_activities.leader),0)
        as jumlah_laporan_aktivitas,
        COALESCE((select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and labelings.leader = '".$dataleader."'
                                and labelings.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY labelings.leader),0)
        as jumlah_labeling
        from activity_lists
        where deleted_at is null 
        and department_id = '".$id."'
        and leader_dept = '".$dataleader."'
        and activity_lists.frequency = 'Monthly'
        GROUP BY leader_dept) monthly,

        (select sum(plan_item) as jumlah_activity_weekly,
        leader_dept as leader_name,
        COALESCE((select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and sampling_checks.leader = '".$dataleader."'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY sampling_checks.leader),0)
        as jumlah_sampling,
        COALESCE((select count(*) as jumlah_sampling
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and audit_processes.leader = '".$dataleader."'
                                and audit_processes.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY audit_processes.leader),0)
        as jumlah_audit_process,
        COALESCE((select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                                join point_check_audits as point_check on point_check.id = point_check_audit_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and point_check.leader = '".$dataleader."'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                GROUP BY point_check.leader),0)
        as jumlah_audit
        from activity_lists
        where deleted_at is null 
        and department_id = '".$id."'
        and leader_dept = '".$dataleader."'
        and activity_lists.frequency = 'Weekly'
        GROUP BY leader_dept) weekly,

        (select COALESCE(sum(plan_item),0) as jumlah_activity_daily,
        COALESCE((select count(*) as jumlah_laporan
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and daily_checks.leader = '".$dataleader."'
                                and daily_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'),0)
        as jumlah_daily_check,
        (select count(week_date) as jumlah_day from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') as jumlah_day,
        4 as jumlah_week,
        (SELECT IF(DATE_FORMAT(CURDATE(),'%Y-%m') != '".$bulan."',
            (select count(week_date) as jumlah_day from weekly_calendars where week_date between concat('".$bulan."','-01') AND LAST_DAY(concat('".$bulan."','-01'))),
            (select count(week_date) as jumlah_day from weekly_calendars where week_date between concat(DATE_FORMAT(CURDATE(),'%Y-%m'),'-01') AND CURDATE())) as jumlah_day)
        as cur_day,
        (select count(DISTINCT(week_name)) as jumlah_week from weekly_calendars WHERE week_date between concat(left(curdate(),7),'-01') AND CURDATE()) as cur_week
        from activity_lists
        where deleted_at is null 
        and department_id = '".$id."'
        and leader_dept = '".$dataleader."'
        and activity_lists.frequency = 'Daily'
        GROUP BY leader_dept) daily,
        (SELECT 
            SUM(detail.plan) as plan_prev,
            SUM(detail.jumlah_aktual) as aktual_prev,
            (SUM(detail.jumlah_aktual)/SUM(detail.plan))*100 as persen_prev
        from 
        (select activity_lists.id as id_activity,
            sum(plan_item) as plan,
            IF(activity_type = 'Audit',
            (select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$dataleader."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    ),
            IF(activity_type = 'Training',
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Sampling Check',
            (select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Labelisasi',
            (select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$prevMonth."'
                                and labelings.leader = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Pemahaman Proses',
            (select count(*) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$prevMonth."'
                                and audit_processes.leader = '".$dataleader."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),0))))))) 
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$dataleader."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail) prev");
      }
      $monthTitle = date("F Y", strtotime($bulan));


      $response = array(
        'status' => true,
        'leaderrr' => $leaderrr,
        'datas' => $data,
        'date' => $date,
        'id' => $id,
        'dataleader' => $dataleader,
        'monthTitle' => $monthTitle
      );

      return Response::json($response);
    }

    public function fetchDetailReport(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = date('Y-m');
        }

        $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");

        $detail = db::select("SELECT detail.id_activity,
                     COALESCE(point_check_audit_id,0) as point_check_audit_id,
                     production_audits.week_name as audit_week,
                     sampling_checks.week_name as sampling_week,
                     detail.link,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            sum(plan_item) as plan,
            IF(activity_type = 'Audit',CONCAT('index/production_audit/details/',activity_lists.id),
            IF(activity_type = 'Training',CONCAT('index/training_report/index/',activity_lists.id),
            IF(activity_type = 'Laporan Aktivitas',CONCAT('index/audit_report_activity/index/',activity_lists.id),
            IF(activity_type = 'Sampling Check',CONCAT('index/sampling_check/index/',activity_lists.id),
            IF(activity_type = 'Pengecekan Foto',CONCAT('index/daily_check_fg/index/',activity_lists.id,'/',(select DISTINCT(product) from daily_checks where activity_list_id = activity_lists.id)),
            IF(activity_type = 'Labelisasi',CONCAT('index/labeling/index/',activity_lists.id),
            IF(activity_type = 'Pemahaman Proses',CONCAT('index/audit_process/index/',activity_lists.id),0)))))))
            as link,
            IF(activity_type = 'Audit',
            (select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Training',
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Sampling Check',
            (select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Labelisasi',
            (select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$week_date."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pemahaman Proses',
            (select count(*) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$week_date."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),0))))))) 
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and frequency = '".$frequency."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail
            left join production_audits on production_audits.activity_list_id =  detail.id_activity
            left join audit_processes on audit_processes.activity_list_id =  detail.id_activity
            left join sampling_checks on sampling_checks.activity_list_id =  detail.id_activity");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'date' => $date,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailReportByActType(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = $request->get('week_date');
            $id_activity = $request->get('id_activity');
            $id_point_check = $request->get('id_point_check');
            $week_name = $request->get('week_name');
        }
        else{
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = date('Y-m');
        }

        $date = db::select("select week_name,week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");
        $detail = db::select("select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '2019-10'
                                and leader_dept = 'Arie Gunawan'
                                and actlist.department_id = '".$id."'
                                and actlist.id = '".$id_activity."'
                                and point_check_audit_id = '".$id_point_check."'
                                and week_name = '".$week_name."'
                    and actlist.frequency = '".$frequency."'");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'date' => $date,
            'detail' => $detail,
            'id_activity' => $id_activity,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);
    }

    public function fetchPointCheck(Request $request,$id){
        if($request->get('id_point_check') != Null){
            $leader_name = $request->get('leader_name');
            $id_activity = $request->get('id_activity');
            $id_point_check = $request->get('id_point_check');
        }
        else{
            $leader_name = $request->get('leader_name');
        }

        $point_check = db::select("select *
                from point_check_audits
                where point_check_audits.id = '".$id_point_check."' LIMIT 1");

        $response = array(
            'status' => true,
            'point_check' => $point_check,
            'id_activity' => $id_activity,
            'leader_name' => $leader_name,
        );
        return Response::json($response);

    }

    public function fetchDetailReportPrev(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $week_date = date('Y-m');
        }

        $queryPMonth = "select DATE_FORMAT(date_sub(concat('".$week_date."','-01'), INTERVAL 1 MONTH),'%Y-%m') as last_month";
        $pMonth = DB::select($queryPMonth);

        foreach($pMonth as $pMonth){
            $prevMonth = $pMonth->last_month;
        }

        $detail = db::select("SELECT detail.id_activity,
                     detail.link,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            sum(plan_item) as plan,
            IF(activity_type = 'Audit',CONCAT('index/production_audit/details/',activity_lists.id),
            IF(activity_type = 'Training',CONCAT('index/training_report/index/',activity_lists.id),
            IF(activity_type = 'Laporan Aktivitas',CONCAT('index/audit_report_activity/index/',activity_lists.id),
            IF(activity_type = 'Sampling Check',CONCAT('index/sampling_check/index/',activity_lists.id),
            IF(activity_type = 'Pengecekan Foto',CONCAT('index/daily_check_fg/index/',activity_lists.id,'/',(select DISTINCT(product) from daily_checks where activity_list_id = activity_lists.id)),
            IF(activity_type = 'Labelisasi',CONCAT('index/labeling/index/',activity_lists.id),
            IF(activity_type = 'Pemahaman Proses',CONCAT('index/audit_process/index/',activity_lists.id),0)))))))
            as link,
            IF(activity_type = 'Audit',
            (select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    ),
            IF(activity_type = 'Training',
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Sampling Check',
            (select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Labelisasi',
            (select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$prevMonth."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id ='".$id."'
                    ),
            IF(activity_type = 'Pemahaman Proses',
            (select count(*) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') ='".$prevMonth."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),0))))))) 
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        $monthTitle = date("F Y", strtotime($prevMonth));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'leader_name' => $leader_name,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

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
                              and actlist.department_id = '".$id."'
                              and production_audits.date = week_date)
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Daily'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and training_reports.date = week_date)
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.date = week_date)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
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
                        and  actlist.frequency = 'Daily'
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
                        and actlist.frequency = 'Daily'
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
        (tot.jumlah_audit-tot.jumlah_not_good)+
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
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and production_audits.kondisi = 'Not Good'
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
        ((select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly') - (select count(DISTINCT(production_audits.date)) as jumlah_audit
                        from production_audits
                                join activity_lists as actlist on actlist.id = activity_list_id
                                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                                and actlist.department_id = '".$id."'
                                and  actlist.frequency = 'Monthly'
                                and production_audits.kondisi = 'Not Good'
                                and production_audits.deleted_at is null)),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Monthly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.department_id = '".$id."'
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
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Conditional'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and actlist.department_id = '".$id."'
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
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.week_name = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Weekly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
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

    public function fetchReportDetailDaily(Request $request,$id)
    {
      if($request->get('date') != null){
        $week_name = $request->get('date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Daily'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and  actlist.frequency = 'Daily'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Daily'
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
      if($frequency == 'Conditional' || $frequency == 'Daily'){
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
