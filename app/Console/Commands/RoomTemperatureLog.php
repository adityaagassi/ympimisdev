<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Plc;
use App\Libraries\ActMLEasyIf;
use Illuminate\Support\Facades\DB;

class RoomTemperatureLog extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'log:room_temperature';

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
    $plcs = Plc::orderBy('location', 'asc')->get();
    $lists = array();
    $date = date('Y-m-d H:i:s');

    foreach ($plcs as $plc) {
        $cpu = new ActMLEasyIf($plc->station);
        $datas = $cpu->read_data($plc->address, 10);
        $data = $datas[$plc->arr];

        $log = db::table('temperature_room_logs')
        ->insert([
            'location' => $plc->location,
            'remark' => $plc->remark,
            'value' => $data,
            'upper_limit' => $plc->upper_limit,
            'lower_limit' => $plc->lower_limit,
            'created_by' => 1,
            'created_at' => $date
        ]);
    }

    $q = "delete from patient_list where TIMESTAMPDIFF(minute,in_time,now()) > 150 and employee_id not like 'PR%'";
    $delete_klinik = db::connection('clinic')->select($q);


    $delete_stamp_sx = db::select("DELETE FROM stamp_inventories 
        WHERE serial_number IN ( SELECT serial_number FROM flo_details WHERE origin_group_code = '043' ) 
        AND origin_group_code = '043'");


}
}
