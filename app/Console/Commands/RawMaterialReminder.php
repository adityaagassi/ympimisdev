<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use App\User;
use App\WeeklyCalendar;
use App\MaterialControl;

class RawMaterialReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:raw_material_reminder';

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
    public function handle(){

        $period = date('Y-m');
        $due_date = date('Y-m-d');

        $now = WeeklyCalendar::where('week_date', $due_date)->first();

        if($now->remark != 'H'){
            $pic = db::select("SELECT DISTINCT pic FROM `material_controls`");

            for ($i=0; $i < count($pic); $i++) {
                $user = User::where('username', $pic[$i]->pic)->first();
                $cc_user = MaterialControl::where('pic', $pic[$i]->pic)->first();
                $cc_email = User::where('username', $cc_user->control)->first();

                $material = db::select("SELECT msp.period,
                    msp.material_number,
                    msp.material_description,
                    mc.vendor_code,
                    mc.vendor_name,
                    '".$due_date."' AS stock_date,
                    COALESCE ( s.stock_total, 0 ) AS stock,
                    plan.plan,
                    msp.day,
                    ROUND(msp.policy, 3) AS policy,
                    ROUND( COALESCE ( s.stock_total, 0 ) / msp.policy * 100 , 2) AS percentage
                    FROM material_stock_policies AS msp
                    LEFT JOIN
                    (SELECT sls.material_number, sls.stock_date,
                    sum(IF( sl.category = 'MSTK', sls.unrestricted, 0 )) AS stock_mstk,
                    sum(IF( sl.category = 'WIP', sls.unrestricted, 0 )) AS stock_wip,
                    sum( sls.unrestricted ) AS stock_total
                    FROM storage_location_stocks AS sls
                    LEFT JOIN storage_locations AS sl ON sls.storage_location = sl.storage_location 
                    WHERE sls.stock_date = '".$due_date."'
                    AND sls.material_number IN ( SELECT material_number FROM material_controls WHERE deleted_at IS NULL ) 
                    GROUP BY sls.material_number, sls.stock_date 
                    ORDER BY sls.material_number ASC, sls.stock_date ASC) AS s
                    ON s.material_number = msp.material_number
                    LEFT JOIN material_controls mc ON mc.material_number = msp.material_number
                    LEFT JOIN (SELECT material_number, MIN(due_date) AS plan FROM material_plan_deliveries
                    WHERE due_date >= '".$due_date."'
                    GROUP BY material_number) AS plan
                    ON plan.material_number = msp.material_number
                    WHERE msp.policy > 0
                    AND msp.material_number in (SELECT material_number FROM material_controls)
                    AND date_format( msp.period, '%Y-%m' ) = '".$period."'
                    AND mc.pic = '".$pic[$i]->pic."'
                    HAVING percentage < 75
                    ORDER BY percentage ASC");

                $resume = array();

                for ($j=0; $j < count($material); $j++) { 

                    $plan_usage = db::select("SELECT material_number, due_date, `usage` FROM material_requirement_plans
                        WHERE material_number = '". $material[$j]->material_number ."'
                        AND due_date >= '".$due_date."'
                        ORDER BY due_date ASC");

                    $sum = 0;
                    $stock_out_date = date('Y-m-d');
                    for ($k=0; $k < count($plan_usage); $k++) { 
                        $sum += $plan_usage[$k]->usage;

                        if($material[$j]->stock >= $sum){
                            $stock_out_date = $plan_usage[$k]->due_date;
                        }else{
                            break;
                        }
                    }

                    $adjustment = 'Re-schedule in';

                    if(is_null($material[$j]->plan)){
                        if($material[$j]->plan < $stock_out_date){
                            $adjustment = 'Re-schedule out';
                        }
                    }
                    
                    $row = array();
                    $row['material_number'] = $material[$j]->material_number;
                    $row['material_description'] = $material[$j]->material_description;
                    $row['vendor_code'] = $material[$j]->vendor_code;
                    $row['vendor_name'] = $material[$j]->vendor_name;
                    $row['stock_date'] = $material[$j]->stock_date;
                    $row['stock'] = $material[$j]->stock;
                    $row['stock_out_date'] = $stock_out_date;
                    $row['plan_delivery'] = $material[$j]->plan;
                    $row['adjustment'] = $adjustment;
                    $row['day'] = $material[$j]->day;
                    $row['policy'] = $material[$j]->policy;
                    $row['percentage'] = $material[$j]->percentage;
                    $resume[] = $row;
                }

                $cc = array();
                array_push($cc, 'adianto.heru@music.yamaha.com', $cc_email->email);

                $bcc = array();
                array_push($bcc, 'muhammad.ikhlas@music.yamaha.com');

                if(count($resume) > 0){
                    $data = [
                        'material' => $resume,
                        'user' => $user,
                        'date' => date('Y-m-d'),
                        'date_text' => date('l, d M Y')
                    ];

                    Mail::to([$user->email])
                    ->cc($cc)
                    ->bcc($bcc)
                    ->send(new SendEmail($data, 'raw_material_reminder'));
                }
            }
        }
    }
}
