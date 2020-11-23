<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use Artisan;

class SendEmailOvertimes extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'email:overtime';

/**
* The console command description.
*
* @var string
*/
protected $description = 'Command description';

/**
* Create a new command instance.
*
* @return void
*/
public function __construct()
{
    parent::__construct();
}

/**
* Execute the console command.
*
* @return mixed
*/
public function handle()
{
    $mail_to = db::table('send_emails')
    ->where('remark', '=', 'overtime')
    ->WhereNull('deleted_at')
    ->orWhere('remark', '=', 'superman')
    ->WhereNull('deleted_at')
    ->select('email')
    ->get();

    $first = date('Y-m-01');
    $now = date('Y-m-d');
    $mon = date('Y-m');
    if($now == $first){
        $first = date('Y-m-d', strtotime(Carbon::now()->subMonth(1)));
        $now = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
        $mon = date('Y-m', strtotime(Carbon::now()->subDays(1)));
    }

    // $datas = db::connection('sunfish')->select("select VIEW_YMPI_Emp_OrgUnit.cost_center_code, VIEW_YMPI_Emp_OrgUnit.Department, ot.emp_no as nik, VIEW_YMPI_Emp_OrgUnit.Full_name as name, VIEW_YMPI_Emp_OrgUnit.pos_name_en, ot.jam from
    //     (
    //     select VIEW_YMPI_Emp_OvertimePlan.emp_no,
    //     sum(
    //     CASE
    //     WHEN VIEW_YMPI_Emp_OvertimePlan.total_ot > 0 THEN
    //     floor((VIEW_YMPI_Emp_OvertimePlan.total_ot / 60.0) * 2  + 0.5) / 2
    //     ELSE
    //     floor((VIEW_YMPI_Emp_OvertimePlan.TOTAL_OVT_PLAN / 60.0) * 2  + 0.5) / 2
    //     END) as jam
    //     from VIEW_YMPI_Emp_OvertimePlan
    //     where VIEW_YMPI_Emp_OvertimePlan.emp_no <> 'SUNFISH' and VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom >= '".$first." 00:00:00' and VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom <= '".$now." 23:59:59'
    //     group by VIEW_YMPI_Emp_OvertimePlan.emp_no
    //     ) as ot left join VIEW_YMPI_Emp_OrgUnit on VIEW_YMPI_Emp_OrgUnit.Emp_no = ot.emp_no where jam > 0 order by ot.jam desc
    //     ");

    $datas = db::connection('sunfish')->select("SELECT
        e.cost_center_code,
        e.Department,
        ot.emp_no AS nik,
        e.Full_name AS name,
        e.pos_name_en,
        ot.jam 
        FROM
        (
        SELECT
        ot.emp_no,
        SUM (
        CASE

        WHEN ot.total_ot > 0 THEN
        floor( ( ot.total_ot / 60.0 ) * 2 + 0.5 ) / 2 ELSE floor( ( ot.TOTAL_OVT_PLAN / 60.0 ) * 2 + 0.5 ) / 2 
        END 
        ) AS jam 
        FROM
        VIEW_YMPI_Emp_OvertimePlan AS ot 
        WHERE
        ot.emp_no <> 'SUNFISH' 
        AND ot.ovtplanfrom >= '".$first." 00:00:00' 
        AND ot.ovtplanfrom <= '".$now." 23:59:59' 
        GROUP BY
        ot.emp_no 
        ) AS ot
        LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS e ON ot.emp_no = e.Emp_no 
        WHERE
        jam > 0
        ORDER BY
        ot.jam DESC");

    $ofc_1 = db::table('office_members')->get();

    $ofc = array();
    $drv = array();
    foreach ($ofc_1 as $of) {
        if($of->remark == 'office'){
            array_push($ofc, $of->employee_id);
        }
        if($of->remark == 'driver' || $of->remark == 'security'){
            array_push($drv, $of->employee_id);
        }
    }

    $offices = array();
    $productions = array();
    $c_ofc = 1;
    $c_prd = 1;

    foreach ($datas as $data) {
        if(in_array($data->nik, $ofc) && $c_ofc <= 20){
            array_push($offices, [
                'period' => $mon,
                'department' => strtoupper($data->Department),
                'grade' => ucwords($data->pos_name_en),
                'employee_id' => strtoupper($data->nik),
                'name' => ucwords($data->name),
                'overtime' => $data->jam
            ]);
            $c_ofc += 1;
        }
        else if(!in_array($data->nik, $drv) && $c_prd <= 20){
            array_push($productions, [
                'period' => $mon,
                'department' => strtoupper($data->Department),
                'grade' => ucwords($data->pos_name_en),
                'employee_id' => strtoupper($data->nik),
                'name' => ucwords($data->name),
                'overtime' => $data->jam
            ]);
            $c_prd += 1;
        }
        if($c_ofc == 20 && $c_prd == 20){
            break;
        }
    }

    $overtimes = [
        'offices' => $offices,
        'productions' => $productions,
        'first' => $first,
    ];

    if($datas != null){
        Mail::to($mail_to)->send(new SendEmail($overtimes, 'overtime'));
    }
}
}
