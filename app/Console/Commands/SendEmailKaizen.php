<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use Artisan;

class SendEmailKaizen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:kaizen';

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
        // DB::connection()->enableQueryLog();
        $mail_to = db::table('employees')
        ->leftJoin('promotion_logs','promotion_logs.employee_id','=','employees.employee_id')
        ->leftJoin('mutation_logs','mutation_logs.employee_id','=','employees.employee_id')
        ->leftJoin('users','users.username','=','employees.employee_id')
        ->whereRaw('end_date is null')
        ->whereRaw('promotion_logs.valid_to is null')
        ->whereRaw('mutation_logs.valid_to is null')
        ->whereRaw("position in ('foreman','chief','manager')")
        ->whereNotNull('email')
        ->select('employees.employee_id','employees.name','position', 'department','email')
        ->get();

        $mail_to2 = db::table('send_emails')
        ->Where('remark', '=', 'superman')
        ->WhereNull('deleted_at')
        ->select('email')
        ->get();

        // $tes = DB::getQueryLog();

        $query_cf = "SELECT
        child_code,
        area,
        SUM( unv_frm ) AS frm,
        SUM( unv_mngr ) AS mngr 
        FROM
        (
        SELECT
        child_code,
        area,
        count AS unv_frm,
        0 AS unv_mngr 
        FROM
        (
        SELECT
        organization_structures.child_code,
        os.child_code AS section 
        FROM
        organization_structures
        JOIN organization_structures AS os ON organization_structures.`status` = os.parent_name 
        WHERE
        organization_structures.remark = 'department' 
        ) AS bagian
        LEFT JOIN ( SELECT count( id ) AS count, area FROM kaizen_forms WHERE `status` = - 1 AND propose_date >= '2019-12-01'
        and kaizen_forms.deleted_at is null GROUP BY area ) AS kz ON bagian.section = kz.area 
        WHERE
        area IS NOT NULL UNION ALL
        SELECT
        child_code,
        area,
        0 AS unv_frm,
        count( kaizen_forms.id ) AS unv_mngr 
        FROM
        kaizen_forms
        LEFT JOIN kaizen_scores ON kaizen_forms.id = kaizen_scores.id_kaizen
        RIGHT JOIN (
        SELECT
        organization_structures.child_code,
        os.child_code AS section 
        FROM
        organization_structures
        JOIN organization_structures AS os ON organization_structures.`status` = os.parent_name 
        WHERE
        organization_structures.remark = 'department' 
        ) AS bagian ON bagian.section = kaizen_forms.area 
        WHERE
        `status` = 1 
        and kaizen_forms.deleted_at is null
        AND ( manager_point_1 IS NULL OR manager_point_1 = 0 ) 
        GROUP BY
        area 
        ) alls 
        GROUP BY
        child_code,
        area";
        
        $kzn = db::select($query_cf);

        $kzs = array();
        $cf_fr = array();
        $mngr = array();
        $mail_tos = array();

        foreach ($mail_to2 as $data2) {
            array_push($mail_tos ,$data2->email);
        }

        foreach ($mail_to as $data) {
            if ($data->position == 'Chief' || $data->position == 'Foreman') {
                array_push($cf_fr, [
                    'employee_id' => $data->employee_id,
                    'name' => $data->name,
                    'position' => $data->position,
                    'department' => $data->department,
                    'email' => $data->email,
                ]);
            } else {
                array_push($mngr, [
                    'employee_id' => $data->employee_id,
                    'name' => $data->name,
                    'position' => $data->position,
                    'department' => $data->department,
                    'email' => $data->email,
                ]);
            }            
        }

        foreach ($cf_fr as $cf) {
            foreach($kzn as $data_cf) {
                if (strcasecmp($cf['department'], $data_cf->child_code) == 0 ) {
                  if (!in_array($cf['email'], $mail_tos)) {
                    array_push($mail_tos,$cf['email']);
                }
            }
        }
    }

    foreach ($mngr as $mngr) {
        foreach($kzn as $data_cf) {
            if (strcasecmp($mngr['department'], $data_cf->child_code) == 0 ) {
              if (!in_array($mngr['email'], $mail_tos)) {
                array_push($mail_tos,$mngr['email']);
            }
        }
    }
}

array_push($mail_tos,'eko.prasetyo.wicaksono@music.yamaha.com');
// print_r($mail_tos);

$kaizens = [
    'kaizens' => $kzn
];

Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('config:cache');

if($kzn != null){
    Mail::to($mail_tos)->send(new SendEmail($kaizens, 'kaizen'));
}
}
}
