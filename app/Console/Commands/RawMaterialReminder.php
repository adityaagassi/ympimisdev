<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use App\User;
use App\WeeklyCalendar;

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

        $period = date('2021-04');
        $due_date = date('2021-04-26');

        $now = WeeklyCalendar::where('week_date', $due_date)->first();

        if($now->remark != 'H'){
            $pic = db::select("SELECT DISTINCT pic FROM `material_controls`");

            for ($i=0; $i < count($pic); $i++) {
                $user = User::where('username', $pic[$i]->pic)->first();

                $material = db::select("SELECT msp.period, msp.material_number, msp.material_description, '".$due_date."' AS stock_date, COALESCE ( s.stock_total, 0 ) AS stock, msp.policy, ROUND( COALESCE ( s.stock_total, 0 ) / msp.policy * 100 , 2) AS percentage
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
                    LEFT JOIN material_controls mc on mc.material_number = msp.material_number
                    WHERE msp.policy > 0
                    AND msp.material_number in (SELECT material_number FROM material_controls)
                    AND date_format( msp.period, '%Y-%m' ) = '".$period."'
                    AND mc.pic = '".$pic[$i]->pic."'
                    HAVING percentage < 75
                    ORDER BY percentage ASC");

                $cc = array();
                array_push($cc, 'adianto.heru@music.yamaha.com');

                $bcc = array();
                array_push($bcc, 'muhammad.ikhlas@music.yamaha.com');

                if(count($material) > 0){
                    $data = [
                        'material' => $material,
                        'user' => $user,
                        'date' => date('l, d M Y')
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
