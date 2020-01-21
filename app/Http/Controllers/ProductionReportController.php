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
use App\UserActivityLog;

class ProductionReportController extends Controller
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
        $this->activity_type = ['Audit',
                        'Training',
                        'Laporan Aktivitas',
                        'Sampling Check',
                        'Pengecekan Foto',
                        'Interview',
                        'Pengecekan',
                        'Pemahaman Proses',
                        'Labelisasi',
                        'Cek Area',
                        'Jishu Hozen',
                        'Cek APD',
                        'Weekly Report'];
    }

    function index($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $activity =  new UserActivityLog([
            'activity' => 'Assembly (WI-A) Report',
            'created_by' => Auth::user()->id,
        ]);
        $activity->save();

        $role_code = Auth::user()->role_code;
        $queryActivity = "SELECT DISTINCT(activity_type) FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null";
    	$activityList = DB::select($queryActivity);
        $data = array('activity_list' => $activityList,
                      'role_code' => $role_code,
                      'id' => $id);
        return view('production_report.index', $data
          )->with('page', 'Leader Task Monitoring');
    }

    function activity($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        
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
            return redirect('/index/first_product_audit/list_proses/'.$id)->with('page', 'First Product Audit')->with('no', '6');
        }
        elseif($activity_type == "Pemahaman Proses"){
            return redirect('/index/audit_process/index/'.$id)->with('page', 'Audit Process')->with('no', '5');
        }
        elseif($activity_type == "Cek Area"){
            return redirect('/index/area_check/index/'.$id)->with('page', 'Area Check')->with('no', '10');
        }
        elseif($activity_type == "Jishu Hozen"){
            return redirect('/index/jishu_hozen/nama_pengecekan/'.$id)->with('page', 'Jishu Hozen')->with('no', '11');
        }
        elseif($activity_type == "Cek APD"){
            return redirect('/index/apd_check/index/'.$id)->with('page', 'APD Check')->with('no', '12');
        }
        elseif($activity_type == "Weekly Report"){
            return redirect('/index/weekly_report/index/'.$id)->with('page', 'Weekly Report')->with('no', '13');
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
        monthly.jumlah_training  + monthly.jumlah_laporan_aktivitas+ monthly.jumlah_labeling+ monthly.jumlah_interview+ monthly.jumlah_first_product_audit+monthly.jumlah_jishu_hozen as jumlah_monthly,
        COALESCE(((monthly.jumlah_training  + monthly.jumlah_laporan_aktivitas+ monthly.jumlah_labeling+ monthly.jumlah_interview+ monthly.jumlah_first_product_audit+monthly.jumlah_jishu_hozen )/monthly.jumlah_activity_monthly)*100,0) as persen_monthly,
        weekly.jumlah_activity_weekly as jumlah_activity_weekly,
        IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) < 4,0,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 4 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) < 8,1,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 8 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)  < 12,2,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 12 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 16,3,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 16 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 20,4,
                                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 20 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 24,5,
                                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 24 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 28,6,0)))))))
                                as jumlah_weekly,
        COALESCE((IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) < 4,0,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 4 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) < 8,1,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 8 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)  < 12,2,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 12 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 16,3,
                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 16 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 20,4,
                                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 20 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 24,5,
                                IF((weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report) >= 24 && (weekly.jumlah_sampling_kd+weekly.jumlah_sampling_fg+weekly.jumlah_audit+weekly.jumlah_audit_process+weekly.jumlah_apd_check+weekly.jumlah_weekly_report)< 28,6,0)))))))/(weekly.jumlah_activity_weekly))*100,0) as persen_weekly,
        (select count(week_date) from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."' and week_date not in (select tanggal from ftm.kalender))*daily.jumlah_activity_daily as jumlah_activity_daily,
        daily.jumlah_daily_check+daily.jumlah_area_check as jumlah_daily,
        COALESCE(((daily.jumlah_daily_check+daily.jumlah_area_check)/((select count(week_date) from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."' and week_date not in (select tanggal from ftm.kalender))*daily.jumlah_activity_daily))*100,0) as persen_daily,
        daily.jumlah_day,
        daily.cur_day,
        (daily.cur_day / daily.jumlah_day)*100 as persen_cur_day,
        prev.plan_prev as plan_prev,
        prev.aktual_prev as aktual_prev,
        prev.persen_prev as persen_prev
        from 
        (select count(activity_type) as jumlah_activity_monthly,
        leader_dept as leader_name,
        COALESCE((select count(DISTINCT(leader)) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Monthly'
                                and training_reports.leader = '".$dataleader."'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY training_reports.leader),0)
        as jumlah_training,
        COALESCE((select count(DISTINCT(leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and audit_report_activities.leader = '".$dataleader."'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY audit_report_activities.leader),0)
        as jumlah_laporan_aktivitas,
        COALESCE((select count(DISTINCT(leader)) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and labelings.leader = '".$dataleader."'
                                and labelings.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY labelings.leader),0)
        as jumlah_labeling,
        COALESCE((select count(DISTINCT(leader)) as jumlah_interview
                from interviews
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(interviews.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and interviews.leader = '".$dataleader."'
                                and interviews.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY interviews.leader),0)
        as jumlah_interview,
                COALESCE((select count(DISTINCT(leader)) as jumlah_first_product_audit
                from first_product_audit_details
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and first_product_audit_details.leader = '".$dataleader."'
                                and first_product_audit_details.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY first_product_audit_details.leader),0)
        as jumlah_first_product_audit,
        COALESCE((select count(DISTINCT(leader)) as jishu_hozens
                from jishu_hozens
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$bulan."'
                    and  actlist.frequency = 'Monthly'
                                and jishu_hozens.leader = '".$dataleader."'
                                and jishu_hozens.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY jishu_hozens.leader),0)
        as jumlah_jishu_hozen
        from activity_lists
        where deleted_at is null 
        and department_id = '".$id."'
        and leader_dept = '".$dataleader."'
        and activity_lists.frequency = 'Monthly'
        GROUP BY leader_dept) monthly,

        (select count(activity_type) as jumlah_activity_weekly,
        leader_dept as leader_name,
        COALESCE((select count(DISTINCT(sampling_checks.week_name)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and sampling_checks.leader = '".$dataleader."'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and actlist.activity_alias like '%FG%'
                                GROUP BY sampling_checks.leader),0)
        as jumlah_sampling_fg,
        COALESCE((select count(DISTINCT(sampling_checks.week_name)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and sampling_checks.leader = '".$dataleader."'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and actlist.activity_alias like '%KD%'
                                GROUP BY sampling_checks.leader),0)
        as jumlah_sampling_kd,
        COALESCE((select count(DISTINCT(audit_processes.week_name)) as jumlah_sampling
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and audit_processes.leader = '".$dataleader."'
                                and audit_processes.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                GROUP BY audit_processes.leader),0)
        as jumlah_audit_process,
        COALESCE((select count(DISTINCT(production_audits.week_name)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                                join point_check_audits as point_check on point_check.id = point_check_audit_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and point_check.leader = '".$dataleader."'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                GROUP BY point_check.leader),0)
        as jumlah_audit,
        COALESCE((select count(DISTINCT(apd_checks.week_name)) as jumlah_audit
                from apd_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and apd_checks.leader = '".$dataleader."'
                                and apd_checks.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                GROUP BY apd_checks.leader),0)
        as jumlah_apd_check,
        COALESCE((select count(DISTINCT(weekly_activity_reports.week_name)) as jumlah_weekly_report
                from weekly_activity_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and weekly_activity_reports.leader = '".$dataleader."'
                                and weekly_activity_reports.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                GROUP BY weekly_activity_reports.leader),0)
        as jumlah_weekly_report
        from activity_lists
        where deleted_at is null 
        and department_id = '".$id."'
        and leader_dept = '".$dataleader."'
        and activity_lists.frequency = 'Weekly'
        GROUP BY leader_dept) weekly,

        (select COALESCE(count(activity_type),0) as jumlah_activity_daily,
        COALESCE((select count(DISTINCT(daily_checks.production_date)) as jumlah_laporan
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and daily_checks.leader = '".$dataleader."'
                                and daily_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'),0)
        as jumlah_daily_check,
        COALESCE((select count(DISTINCT(area_checks.date)) as jumlah_area_check
                from area_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(area_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and area_checks.leader = '".$dataleader."'
                                and area_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'),0)
        as jumlah_area_check,
        (select count(week_date) as jumlah_day from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."' and week_date not in (select tanggal from ftm.kalender)) as jumlah_day,
        4 as jumlah_week,
        (SELECT IF(DATE_FORMAT(CURDATE(),'%Y-%m') != '".$bulan."',
            (select count(week_date) as jumlah_day from weekly_calendars where week_date between concat('".$bulan."','-01') AND LAST_DAY(concat('".$bulan."','-01')) and week_date not in (select tanggal from ftm.kalender)),
            (select count(week_date) as jumlah_day from weekly_calendars where week_date between concat(DATE_FORMAT(CURDATE(),'%Y-%m'),'-01') AND CURDATE() and week_date not in (select tanggal from ftm.kalender))) as jumlah_day)
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

    public function fetchDetailReportWeekly(Request $request,$id){
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
        $detail[] = null;
        $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");
        $date2 = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");

        foreach($date2 as $date2){
            $detail[] = db::select("SELECT detail.id_activity,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.week_name,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            4 as plan,
            '".$date2->week_name."' as week_name,
            IF(activity_type = 'Audit',
            (select count(production_audits.week_name) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and week_name = '".$date2->week_name."'
                                and actlist.id = id_activity
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Sampling Check',
            (select count(sampling_checks.week_name) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pemahaman Proses',
            (select count(audit_processes.week_name) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$week_date."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Cek APD',
            (select count(apd_checks.week_name) as jumlah_apd_check
                from apd_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$week_date."'
                                and apd_checks.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Weekly Report',
            (select count(weekly_activity_reports.week_name) as jumlah_weekly_report
                from weekly_activity_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$week_date."'
                                and weekly_activity_reports.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),0)))))
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and frequency = '".$frequency."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        }
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

    public function fetchDetailReportMonthly(Request $request,$id){
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

        $detail = db::select("SELECT detail.id_activity,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     IF(frequency = '".$frequency."', 1,4) as plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/IF(frequency = '".$frequency."', 1,4))*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,frequency,
            IF(activity_type = 'Audit',
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Training',
            (select count(DISTINCT(leader)) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(DISTINCT(leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Sampling Check',
            (select count(DISTINCT(sampling_checks.date)) as jumlah_sampling
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
            (select count(DISTINCT(leader)) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$week_date."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pemahaman Proses',
            (select count(DISTINCT(audit_processes.date)) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$week_date."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Interview',
            (select count(DISTINCT(interviews.leader)) as jumlah_interview
                from interviews
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(interviews.date,'%Y-%m') = '".$week_date."'
                                and interviews.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
                        IF(activity_type = 'Pengecekan',
            (select count(DISTINCT(first_product_audit_details.leader)) as jumlah_first_product_audit
                from first_product_audit_details
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$week_date."'
                                and first_product_audit_details.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Jishu Hozen',
            (select count(DISTINCT(jishu_hozens.leader)) as jumlah_jishu_hozen
                from jishu_hozens
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$week_date."'
                                and jishu_hozens.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),0))))))))))
            jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and frequency = '".$frequency."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, frequency,id,activity_name,leader_dept) detail");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailReportDaily(Request $request,$id){
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

        $date = db::select("select week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."' and week_date not in (select tanggal from ftm.kalender)");

        $act_name = DB::select("select activity_name from activity_lists where 
             leader_dept = '".$leader_name."'
            and activity_lists.department_id = '".$id."'
            and activity_lists.frequency = '".$frequency."'");

        $detail = db::select("select 
        weekly_calendars.week_date,
                (select count(week_date)
                from weekly_calendars
                where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'
                and week_date not in (select tanggal from ftm.kalender))
        as plan, 
                (select count(DISTINCT(production_date))
                from daily_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(production_date,'%Y-%m') = '".$week_date."'
                and leader = '".$leader_name."'
                and production_date = weekly_calendars.week_date
                and actlist.department_id = '".$id."'
                and actlist.frequency = '".$frequency."') as jumlah_daily_check,
                (select count(DISTINCT(date))
                from area_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(date,'%Y-%m') = '".$week_date."'
                and leader = '".$leader_name."'
                and date = weekly_calendars.week_date
                and actlist.department_id = '".$id."'
                and actlist.frequency = '".$frequency."') as jumlah_area_check
                        from weekly_calendars 
                        where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'
                        and weekly_calendars.week_date not in (select tanggal from ftm.kalender)");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'date' => $date,
            'act_name' => $act_name,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
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

    public function fetchDetailReportMonthly2(Request $request,$id){
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

    function approval($id)
    {
        $leader = DB::SELECT("SELECT DISTINCT(leader_dept) FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null");
        $data = array('leader' => $leader,
                      'id' => $id);
        return view('production_report.approval', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_list($id,$leader_name)
    {
        $month = date('Y-m');
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
             detail.activity_type,
             detail.activity_name,
             detail.jumlah_approval,
             detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                    IF(activity_type = 'Audit',
                        (SELECT count(*) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT count(*) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT count(*) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT count(*) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT count(*) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT count(*) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT count(*) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT count(*) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT count(*) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT count(*) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT count(*) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT count(*) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT count(*) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as jumlah_approval,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as link
                        from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                        and activity_name is not null
                        and deleted_at is null) detail");
        $monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'id' => $id);
        return view('production_report.approval_list', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_list_filter(Request $request,$id,$leader_name)
    {
        $month = $request->get('month');
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
             detail.activity_type,
             detail.activity_name,
             detail.jumlah_approval,
             detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                    IF(activity_type = 'Audit',
                        (SELECT count(*) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT count(*) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT count(*) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT count(*) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT count(*) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT count(*) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT count(*) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT count(*) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT count(*) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT count(*) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT count(*) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT count(*) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT count(*) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as jumlah_approval,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as link
                        from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                        and activity_name is not null
                        and deleted_at is null) detail");
        $monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'id' => $id);
        return view('production_report.approval_list', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_detail($activity_list_id,$month)
    {
        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $activity_type = $activityList->activity_type;
        $leader = $activityList->leader_dept;

        // $month = date('Y-m');

        if ($activity_type == 'Audit') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/production_audit/print_audit_email/',production_audits.activity_list_id,'/','".$month."','/',product,'/',proses)) as link,
                CONCAT(product,' - ',proses) as title
                FROM production_audits
                        join point_check_audits on production_audits.point_check_audit_id = point_check_audits.id
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and production_audits.activity_list_id = '".$activity_list_id."'
                        and approval is null
                        and production_audits.deleted_at is null");
        }
        else if ($activity_type == 'Pengecekan') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/first_product_audit/print_first_product_audit_email/',first_product_audit_details.activity_list_id,'/',first_product_audit_details.first_product_audit_id,'/','".$month."')) as link, CONCAT(proses,' - ',jenis) as title FROM first_product_audit_details
                    join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                    where send_status = 'Sent'
                    and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                    and first_product_audit_details.activity_list_id = '".$activity_list_id."'
                    and approval is null
                    and first_product_audit_details.deleted_at is null");
        }
        $data = array('detail' => $detail,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'id_departments' => $id_departments,
                      'activity_list_id' => $activity_list_id);
        return view('production_report.approval_detail', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function report_by_task($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }

        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();
        // $queryLeader2 = "select DISTINCT(employees.name), employees.employee_id
        //     from employees
        //     join mutation_logs on employees.employee_id= mutation_logs.employee_id
        //     join promotion_logs on employees.employee_id= promotion_logs.employee_id
        //     where (mutation_logs.department = '".$departments."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='foreman')";
        // $leader = DB::select($queryLeader2);
        // $leader2 = DB::select($queryLeader2);
        // $leader3 = DB::select($queryLeader2);

        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        return view('production_report.report_by_task',  array('title' => 'Leader Tasks',
            'title_jp' => '???',
            'id' => $id,
            // 'data' => $data,
            'activity_list' => $activityList,
            'activity_type' => $this->activity_type,
            // 'leader2' => $leader2,
            // 'leader3' => $leader3,
            // 'leader' => $leader,
            'departments' => $departments,
        ))->with('page', 'Leader Task Monitoring');
    }

    public function fetchReportByTask(Request $request)
    {
        if ($request->get('month') == null) {
            $month = date('Y-m');
        }
        else{
            $month = $request->get('month');
        }

        if ($request->get('activity_type') == null) {
            $activity_type = 'Audit';
        }
        else{
            $activity_type = $request->get('activity_type');
        }

        $monthTitle = date("F Y", strtotime($month));

        $week = DB::SELECT("SELECT DISTINCT(week_name) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$month."'");

        if ($activity_type == 'Audit') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from production_audits where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and production_audits.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Audit' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Training') {
            $data = DB::SELECT("select leader_dept,activity_name,(select count(*) from training_reports where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and training_reports.deleted_at is null) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Training' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Laporan Aktivitas') {
            $data = DB::SELECT("select activity_lists.id,leader_dept,activity_name,(select count(*) from audit_report_activities where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and audit_report_activities.deleted_at is null ) as hasil,(SELECT count(*) from audit_guidances where leader = leader_dept and month = '".$month."' and audit_guidances.deleted_at is null) as plan,frequency from activity_lists  where activity_lists.activity_type = 'Laporan Aktivitas' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Sampling Check') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from sampling_checks where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and sampling_checks.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Sampling Check' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        // $response = array(
        //     'status' => true,
        //     'activity_type' => $activity_type,
        //     'datas' => $audit,
        //     'monthTitle' => $monthTitle,
        //     'month' => $month
        //   );

        //   return Response::json($response);

          $response = array(
            'status' => true,
            'activity_type' => $activity_type,
            'datas' => $data,
            'week' => $week,
            'monthTitle' => $monthTitle,
            'month' => $month
          );

      return Response::json($response);
    }
}

