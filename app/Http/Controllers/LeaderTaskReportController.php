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
    function index($id)
    {
        $leader = DB::SELECT("SELECT DISTINCT(leader_dept) FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null");
        $data = array('leader' => $leader,
                      'id' => $id);
        return view('leader_task_report.index', $data
          )->with('page', 'Leader Task Report');
    }

    function leader_task_list($id,$leader_name)
    {
        $month = date('Y-m');
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
                                            detail.activity_type,
                                            detail.activity_name,
                                            detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),null))))))))))
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
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
                                            detail.activity_type,
                                            detail.activity_name,
                                            detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/leader_task_report/leader_task_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and deleted_at is null),null))))))))))
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
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/production_audit/print_audit_email/',production_audits.activity_list_id,'/','".$month."','/',product,'/',proses)) as link,
                CONCAT(product,' - ',proses) as title
                FROM production_audits
                        join point_check_audits on production_audits.point_check_audit_id = point_check_audits.id
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and production_audits.activity_list_id = '".$activity_list_id."'
                        and production_audits.deleted_at is null");
        }
        else if ($activity_type == 'Pengecekan') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/first_product_audit/print_first_product_audit_email/',first_product_audit_details.activity_list_id,'/',first_product_audit_details.first_product_audit_id,'/','".$month."')) as link, CONCAT(proses,' - ',jenis) as title FROM first_product_audit_details
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
}
