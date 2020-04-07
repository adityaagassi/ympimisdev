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
            'created_by' => 1
        ]);
    }
}
}
