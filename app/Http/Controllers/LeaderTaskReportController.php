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

class LeaderTaskReportController extends Controller
{
    function __construct()
    {
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
        $leader = DB::SELECT("SELECT DISTINCT(leader_dept),department_name FROM activity_lists join departments on activity_lists.department_id = departments.id where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null");

        foreach ($leader as $key) {
            $dept = $key->department_name;
        }
        $data = array('leader' => $leader,
                      'id' => $id
                  );
        return view('leader_task_report.index', $data
          )->with('page', 'Leader Task Report')->with('dept', strtoupper($dept));
    }

    function leader_task_list($id,$leader_name)
    {
        $role_code = Auth::user()->role_code;
        $name = Auth::user()->name;
        $month = date('Y-m');
        // $activity_list = DB::SELECT("SELECT detail.id_activity_list,
        //                                     detail.activity_type,
        //                                     detail.activity_name,
        //                                     detail.frequency,
        //                                     detail.jumlah,
        //                                     detail.link
        //         from
        //         (select activity_type, activity_lists.id as id_activity_list, activity_name,frequency,
        //         IF(activity_type = 'Audit',
        //                 (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM production_audits
        //                 where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Training',
        //                 (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Sampling Check',
        //                 (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan Foto',
        //                 (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Laporan Aktivitas',
        //                 (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pemahaman Proses',
        //                 (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan',
        //                 (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Interview',
        //                 (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Labelisasi',
        //                 (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek Area',
        //                 (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Jishu Hozen',
        //                 (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek APD',
        //                 (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Weekly Report',
        //                 (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Temuan NG',
        //                 (SELECT DISTINCT(CONCAT('/index/ng_finding/print_ng_finding_email/',id_activity_list,'/','".$month."')) FROM ng_findings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),null))))))))))))))
        //         as link,
        //         IF(activity_type = 'Audit',
        //                 (SELECT count(DISTINCT(week_name)) FROM production_audits
        //                 where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Training',
        //                 (SELECT count(DISTINCT(leader)) FROM training_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Sampling Check',
        //                 (SELECT count(DISTINCT(week_name)) FROM sampling_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan Foto',
        //                 (SELECT count(id) FROM daily_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Laporan Aktivitas',
        //                 (SELECT count(DISTINCT(leader)) FROM audit_report_activities
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pemahaman Proses',
        //                 (SELECT count(DISTINCT(week_name)) FROM audit_processes
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan',
        //                 (SELECT count(DISTINCT(leader)) FROM first_product_audit_details
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Interview',
        //                 (SELECT COUNT(DISTINCT(leader)) FROM interviews
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Labelisasi',
        //                 (SELECT count(DISTINCT(leader)) FROM labelings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek Area',
        //                 (SELECT count(DISTINCT(id)) FROM area_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Jishu Hozen',
        //                 (SELECT count(DISTINCT(leader)) FROM jishu_hozens
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek APD',
        //                 (SELECT count(DISTINCT(week_name)) FROM apd_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Weekly Report',
        //                 (SELECT count(DISTINCT(week_name)) FROM weekly_activity_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Temuan NG',
        //                 (SELECT count(DISTINCT(id)) FROM ng_findings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),null))))))))))))))
        //         as jumlah
        //                 from activity_lists
        //                 where leader_dept = '".$leader_name."'
        //                 and department_id = '".$id."'
        //                 and activity_name is not null
        //                 and deleted_at is null) detail");

        $activity_list = ActivityList::where('department_id',$id)->where('leader_dept',$leader_name)->where('deleted_at', null)->get();
		$monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'month' => $month,
                      'role_code' => $role_code,
                      'name' => $name,
                      'id' => $id);
        return view('leader_task_report.leader_task_list', $data
          )->with('page', 'Leader Task Report');
    }

    function filter_leader_task(Request $request,$id,$leader_name)
    {
        if($request->get('date') != null){
        	$month = $request->get('date');
        }
        else{
        	$month = date('Y-m');
        }
        $role_code = Auth::user()->role_code;
        $name = Auth::user()->name;
        // $activity_list = DB::SELECT("SELECT detail.id_activity_list,
        //                                     detail.activity_type,
        //                                     detail.activity_name,
        //                                     detail.frequency,
        //                                     detail.jumlah,
        //                                     detail.link
        //         from
        //         (select activity_type, activity_lists.id as id_activity_list, activity_name,frequency,
        //         IF(activity_type = 'Audit',
        //                 (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM production_audits
        //                 where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Training',
        //                 (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Sampling Check',
        //                 (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan Foto',
        //                 (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Laporan Aktivitas',
        //                 (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pemahaman Proses',
        //                 (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan',
        //                 (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Interview',
        //                 (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Labelisasi',
        //                 (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek Area',
        //                 (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Jishu Hozen',
        //                 (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek APD',
        //                 (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Weekly Report',
        //                 (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Temuan NG',
        //                 (SELECT DISTINCT(CONCAT('/index/ng_finding/print_ng_finding_email/',id_activity_list,'/','".$month."')) FROM ng_findings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),null))))))))))))))
        //         as link,
        //         IF(activity_type = 'Audit',
        //                 (SELECT count(DISTINCT(week_name)) FROM production_audits
        //                 where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Training',
        //                 (SELECT count(DISTINCT(leader)) FROM training_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Sampling Check',
        //                 (SELECT count(DISTINCT(week_name)) FROM sampling_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan Foto',
        //                 (SELECT count(id) FROM daily_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Laporan Aktivitas',
        //                 (SELECT count(DISTINCT(leader)) FROM audit_report_activities
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pemahaman Proses',
        //                 (SELECT count(DISTINCT(week_name)) FROM audit_processes
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Pengecekan',
        //                 (SELECT count(DISTINCT(leader)) FROM first_product_audit_details
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Interview',
        //                 (SELECT COUNT(DISTINCT(leader)) FROM interviews
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Labelisasi',
        //                 (SELECT count(DISTINCT(leader)) FROM labelings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek Area',
        //                 (SELECT count(DISTINCT(id)) FROM area_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Jishu Hozen',
        //                 (SELECT count(DISTINCT(leader)) FROM jishu_hozens
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Cek APD',
        //                 (SELECT count(DISTINCT(week_name)) FROM apd_checks
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Weekly Report',
        //                 (SELECT count(DISTINCT(week_name)) FROM weekly_activity_reports
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),
        //             IF(activity_type = 'Temuan NG',
        //                 (SELECT count(DISTINCT(id)) FROM ng_findings
        //                 where leader = '".$leader_name."'
        //                 and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
        //                 and activity_list_id = id_activity_list
        //                 and deleted_at is null),null))))))))))))))
        //         as jumlah
        //                 from activity_lists
        //                 where leader_dept = '".$leader_name."'
        //                 and department_id = '".$id."'
        //                 and activity_name is not null
        //                 and deleted_at is null) detail");
        $activity_list = ActivityList::where('department_id',$id)->where('leader_dept',$leader_name)->where('deleted_at', null)->get();
		$monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'month' => $month,
                      'role_code' => $role_code,
                      'name' => $name,
                      'id' => $id);
        return view('leader_task_report.leader_task_list', $data
          )->with('page', 'Leader Task Report');
    }

    function leader_task_detail($activity_list_id,$month)
    {
        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $activity_type = $activityList->activity_type;
        $leader = $activityList->leader_dept;

        if ($activity_type == 'Audit') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/production_audit/print_audit/',production_audits.activity_list_id,'/','".$month."','/',product,'/',proses)) as link,
                CONCAT(product,' - ',proses) as title
                FROM production_audits
                        join point_check_audits on production_audits.point_check_audit_id = point_check_audits.id
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and production_audits.activity_list_id = '".$activity_list_id."'
                        and production_audits.deleted_at is null");
        }
        else if ($activity_type == 'Pengecekan') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/first_product_audit/print_first_product_audit/',first_product_audit_details.activity_list_id,'/',first_product_audit_details.first_product_audit_id,'/','".$month."')) as link, CONCAT(proses,' - ',jenis) as title FROM first_product_audit_details
                    join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                    and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                    and first_product_audit_details.activity_list_id = '".$activity_list_id."'
                    and first_product_audit_details.deleted_at is null");
        }
        $data = array('detail' => $detail,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'id_departments' => $id_departments,
                      'activity_list_id' => $activity_list_id);
        return view('leader_task_report.leader_task_detail', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    public function fetchReport(Request $request)
    {
        try {
            $activity_type = $request->get('activity_type');
            $id = $request->get('id');
            $activity_list_id = $request->get('activity_list_id');
            $month = $request->get('month');

            if ($month == "") {
                $month = date('Y-m');
            }

            if ($activity_type == 'Audit') {
              $activity = DB::SELECT('SELECT *,
                    CONCAT("/index/leader_task_report/leader_task_detail/",activity_list_id,"/","'.$month.'") as link
                FROM
                    production_audits 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( production_audits.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND production_audits.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Training') {
              $activity = DB::SELECT('SELECT
                    * 
                FROM
                    training_reports 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( training_reports.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND training_reports.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Sampling Check') {
              $act_name = 'Sampling Check FG / KD';
            }
            elseif ($activity_type == 'Laporan Aktivitas') {
              $act_name = 'Laporan Audit IK';
            }
            elseif ($activity_type == 'Pemahaman Proses') {
              $act_name = 'Audit Pemahaman Proses';
            }
            elseif ($activity_type == 'Pengecekan') {
              $act_name = 'Cek Produk Pertama';
            }
            elseif ($activity_type == 'Interview') {
              $act_name = 'Interview Pointing Call';
            }
            elseif ($activity_type == 'Pengecekan Foto') {
              $act_name = 'Cek FG / KD Harian';
            }
            elseif ($activity_type == 'Labelisasi') {
              $act_name = 'Audit Label Safety';
            }
            elseif ($activity_type == 'Cek Area') {
              $act_name = 'Cek Safety Area Kerja';
            }
            elseif ($activity_type == 'Jishu Hozen') {
              $act_name = 'Audit Jishu Hozen';
            }
            elseif ($activity_type == 'Cek APD') {
              $act_name = 'Cek APD';
            }
            elseif ($activity_type = 'Weekly Report') {
              $act_name = 'Weekly Report';
            }
            elseif ($activity_type = 'Temuan NG') {
              $act_name = 'Temuan NG';
            }
          $response = array(
            'status' => true,
            'message' => 'Success Get Data',
            'activity_list' => $activity,
          );
          return Response::json($response);
        } catch (\Exception $e) {
          $response = array(
            'status' => false,
            'message' => $e->getMessage(),
          );
          return Response::json($response);
        }
    }

    public function filter(Request $request)
    {
        try {
            $leader = $request->get('leader');
            $id = $request->get('id');

            $activity = ActivityList::where('leader_dept',$leader)->where('department_id',$id)->get();

            $response = array(
                'status' => true,
                'activity' => $activity,
                'id' => $id,
                'leader' => $leader
              );
              return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
        }
    }

    public function filter_detail(Request $request)
    {
        try {
            $activity_type = $request->get('activity_type');
            $id = $request->get('id');
            $activity_list_id = $request->get('activity_list_id');
            $month = $request->get('month');

            if ($month == "") {
                $month = date('Y-m');
            }

            if ($activity_type == 'Audit') {
              $activity = DB::SELECT('SELECT
                    DISTINCT(activity_list_id),activity_lists.activity_name,activity_lists.leader_dept,activity_lists.activity_type,frequency,
                    CONCAT( "/index/leader_task_report/leader_task_detail/", activity_list_id, "/", "'.$month.'" ) AS link 
                FROM
                    production_audits
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( production_audits.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND production_audits.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Training') {
              $activity = DB::SELECT('SELECT DISTINCT(CONCAT("/index/training_report/print/",training_reports.id)) as link,
                    training_reports.*,activity_lists.*
                FROM
                    training_reports 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( training_reports.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND training_reports.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Sampling Check') {
              $activity = DB::SELECT('SELECT DISTINCT
                    ( activity_list_id ),
                    CONCAT( "/index/sampling_check/print_sampling/", activity_list_id, "/", "'.$month.'" ) AS link,
                    activity_lists.activity_name,
                    activity_lists.leader_dept,
                    activity_lists.frequency
                FROM
                    sampling_checks 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( sampling_checks.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND sampling_checks.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Laporan Aktivitas') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/audit_report_activity/print_audit_report/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        audit_report_activities
                    audit_report_activities 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( audit_report_activities.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND audit_report_activities.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Pemahaman Proses') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/audit_process/print_audit_process/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        audit_processes
                    audit_processes 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( audit_processes.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND audit_processes.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Pengecekan') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/leader_task_report/leader_task_detail/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        first_product_audit_details
                    first_product_audit_details 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( first_product_audit_details.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND first_product_audit_details.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Interview') {
              $activity = DB::SELECT('SELECT DISTINCT(CONCAT("/index/interview/print_interview/",interviews.id)) as link,
                    interviews.*,activity_lists.*
                FROM
                    interviews 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( interviews.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND interviews.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Pengecekan Foto') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/daily_check_fg/print_daily_check/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        daily_checks
                    daily_checks 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( daily_checks.check_date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND daily_checks.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Labelisasi') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/labeling/print_labeling/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        labelings
                    labelings 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( labelings.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND labelings.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Cek Area') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/area_check/print_area_check/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        area_checks
                    area_checks 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( area_checks.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND area_checks.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Jishu Hozen') {
              $activity = DB::SELECT('SELECT DISTINCT
                (jishu_hozen_point_id),
                CONCAT( "/index/jishu_hozen/print_jishu_hozen/", jishu_hozens.activity_list_id, "/",jishu_hozens.id, "/", "'.$month.'" ) AS link,
                activity_lists.activity_name,
                CONCAT(activity_lists.activity_name," - ",jishu_hozen_points.nama_pengecekan) as activity_name_detail,
                activity_lists.leader_dept,
                activity_lists.frequency 
            FROM
                jishu_hozens
                JOIN activity_lists ON activity_list_id = activity_lists.id 
                join jishu_hozen_points on jishu_hozen_point_id = jishu_hozen_points.id
            WHERE
                DATE_FORMAT( jishu_hozens.date, "%Y-%m" ) = "'.$month.'" 
                AND jishu_hozens.activity_list_id = "'.$activity_list_id.'" 
                AND jishu_hozens.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Cek APD') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/apd_check/print_apd_check/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        apd_checks
                    apd_checks 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( apd_checks.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND apd_checks.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Weekly Report') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/weekly_report/print_weekly_report/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        weekly_activity_reports
                    weekly_activity_reports 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( weekly_activity_reports.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND weekly_activity_reports.deleted_at IS NULL');
            }
            elseif ($activity_type == 'Temuan NG') {
              $activity = DB::SELECT('SELECT DISTINCT
                        ( activity_list_id ),
                        CONCAT( "/index/ng_finding/print_ng_finding/", activity_list_id, "/", "'.$month.'" ) AS link,
                        activity_lists.activity_name,activity_lists.leader_dept,activity_lists.frequency
                    FROM
                        ng_findings
                    ng_findings 
                    JOIN activity_lists ON activity_list_id = activity_lists.id 
                WHERE
                    DATE_FORMAT( ng_findings.date, "%Y-%m" ) = "'.$month.'" 
                    AND activity_list_id = "'.$activity_list_id.'" 
                    AND ng_findings.deleted_at IS NULL');
            }

            $monthTitle = date("F Y", strtotime($month));

            $response = array(
                'status' => true,
                'activity' => $activity,
                'monthTitle' => $monthTitle,
                'id' => $id,
              );
              return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
        }
    }
}
