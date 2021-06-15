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


class RawMaterialOverUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:raw_material_over';

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

        $start = date('Y-m-01');
        $end = date('Y-m-d');

        $now = WeeklyCalendar::where('week_date', $end)->first();

        if($now->remark != 'H'){
            $over = db::select("SELECT io.material_number, mpdl.material_description, mpdl.bun, COALESCE(mrp.`usage`, 0) AS `usage`, io.quantity FROM
                (SELECT material_number, SUM(quantity) AS quantity FROM material_in_outs
                WHERE entry_date BETWEEN '".$start."' AND '".$end."'
                AND movement_type IN ('9I3', '9I4')
                GROUP BY material_number) AS io
                LEFT JOIN
                (SELECT material_number, SUM(`usage`) AS `usage` FROM material_requirement_plans
                WHERE due_date BETWEEN '".$start."' AND '".$end."'
                GROUP BY material_number) AS mrp
                ON mrp.material_number = io.material_number
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = io.material_number
                HAVING io.quantity > `usage`
                AND `usage` > 0");


            if(count($over) > 0){
                $materials = "";
                for($x = 0; $x < count($over); $x++) {
                    $materials = $materials."'".$over[$x]->material_number."'";
                    if($x != count($over)-1){
                        $materials = $materials.",";
                    }
                }

                $detail = db::select("SELECT sl.department_group, io.material_number, SUM(io.quantity) AS quantity FROM material_in_outs io
                    LEFT JOIN storage_locations sl ON io.receive_location = sl.storage_location
                    WHERE io.entry_date BETWEEN '".$start."' AND '".$end."'
                    AND io.movement_type IN ('9I3', '9I4')
                    AND io.material_number IN (".$materials.")
                    GROUP BY sl.department_group, io.material_number");


                $to = [
                    'jihan.rusdi@music.yamaha.com'
                ];

                // $to = [
                //     'bagus.nur.hidayat@music.yamaha.com',
                //     'maruli.sapta.adi@music.yamaha.com',
                //     'bambang.wahyudi@music.yamaha.com',
                //     'santo.siswa@music.yamaha.com',
                //     'ipung.dwi.setiawan@music.yamaha.com',
                //     'anang.zahroni@music.yamaha.com'
                // ];

                // $cc = [
                //     'khoirul.umam@music.yamaha.com', 
                //     'yudi.abtadipa@music.yamaha.com', 
                //     'fatchur.rozi@music.yamaha.com', 
                //     'fattatul.mufidah@music.yamaha.com'
                // ];

                $bcc = [
                    // 'aditya.agassi@music.yamaha.com', 
                    'muhammad.ikhlas@music.yamaha.com'
                ];

                $data = [
                    'overs' => $over,
                    'details' => $detail,
                    'month_text' => date('F Y'),
                    'start_date' => date('d F Y', strtotime($start)),
                    'end_date' => date('d F Y')
                ];

                Mail::to($to)
                // ->cc($cc)
                ->bcc($bcc)
                ->send(new SendEmail($data, 'raw_material_over'));

            }
        }
    }
}


