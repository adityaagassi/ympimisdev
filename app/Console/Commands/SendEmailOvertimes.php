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

        $query = "
        SELECT DISTINCT
        ovr.nik,
        emp.name,
        jabatan.grade,
        pos.department,
        pos.section,
        helper.code,
        ovr.act,
        forecast_mp.fc_mp,
        budget.mp_budget
        FROM
        (
        SELECT
        nik,
        sum( IF ( STATUS = 0, jam, final ) ) AS act 
        FROM
        over_time
        LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot 
        WHERE
        DATE_FORMAT( tanggal, '%Y-%m-%d' ) >= '".$first."'
        AND DATE_FORMAT( tanggal, '%Y-%m-%d' ) <= '".$now."'
        AND deleted_at IS NULL 
        AND jam_aktual = 0 
        GROUP BY
        nik 
        ) ovr
        LEFT JOIN ympimis.employees AS emp ON emp.employee_id = ovr.nik
        LEFT JOIN (select employee_id, if(position <> '-', position, grade_name) as grade from ympimis.promotion_logs where valid_to is null) as jabatan on jabatan.employee_id = ovr.nik
        LEFT JOIN ( SELECT mutation_logs.employee_id, ympimis.cost_centers.department, mutation_logs.section, mutation_logs.`group`, mutation_logs.cost_center FROM ympimis.mutation_logs left join ympimis.cost_centers on ympimis.cost_centers.cost_center = mutation_logs.cost_center WHERE valid_to IS NULL ) AS pos ON ovr.nik = pos.employee_id
        LEFT JOIN ympimis.total_meeting_codes AS helper ON pos.`group` = helper.group_name
        LEFT JOIN (
        select forecast.cost_center, coalesce(floor(forecast.fc / emp_data.jml * 2 + 0.5 ) / 2,0) as fc_mp from (select cost_center, sum(hour) as fc from ympimis.forecasts where DATE_FORMAT( date, '%Y-%m-%d' ) >= '".$first."'
        AND DATE_FORMAT( date, '%Y-%m-%d' ) <= '".$now."'
        group by cost_center) as forecast
        left join (
        select count(emp.employee_id) as jml, cost_center from ympimis.employees as emp
        join (select employee_id, cost_center from ympimis.mutation_logs where valid_to is null) as cc on emp.employee_id = cc.employee_id
        group by cost_center
        ) as emp_data on emp_data.cost_center = forecast.cost_center
        ) as forecast_mp on forecast_mp.cost_center = pos.cost_center
        LEFT JOIN (
        select cost_center, floor(budget_mp / DAY(LAST_DAY('".$first."')) * DAY('".$now."') * 2  + 0.5) / 2 mp_budget from ympimis.budgets where DATE_FORMAT(period,'%Y-%m') = '".$mon."'
        ) as budget on budget.cost_center = pos.cost_center
        where department is not null
        and act > 0 
        and act > mp_budget
        ORDER BY ovr.act DESC";
        
        $datas = db::connection('mysql3')->select($query);

        $ofc = ['OFC'];
        $prd = ['WH', 'AP', 'EI', 'MTC', 'PP', 'PE', 'QA', 'WST'];
        $offices = array();
        $productions = array();
        $c_ofc = 1;
        $c_prd = 1;
        
        foreach ($datas as $data) {
            if(in_array($data->code, $ofc) && $c_ofc <= 20){
                array_push($offices, [
                    'period' => $mon,
                    'department' => strtoupper($data->department),
                    'section' => ucwords($data->section),
                    'grade' => ucwords($data->grade),
                    'employee_id' => strtoupper($data->nik),
                    'name' => ucwords($data->name),
                    'overtime' => $data->act,
                    'fq' => $data->fc_mp,
                    'budget' => $data->mp_budget
                ]);
                $c_ofc += 1;
            }
            if(in_array($data->code, $prd) && $c_prd <= 20){
                array_push($productions, [
                    'period' => $mon,
                    'department' => strtoupper($data->department),
                    'section' => ucwords($data->section),
                    'grade' => ucwords($data->grade),
                    'employee_id' => strtoupper($data->nik),
                    'name' => ucwords($data->name),
                    'overtime' => $data->act,
                    'fq' => $data->fc_mp,
                    'budget' => $data->mp_budget
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

        // dd($overtimes);
        // exit;
        
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        if($datas != null){
            Mail::to($mail_to)->send(new SendEmail($overtimes, 'overtime'));
        }

    }
}
