<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\InjectionScheduleTemp;
use App\InjectionScheduleLog;
use App\InjectionMachineCycleTime;
use App\InjectionMachineMaster;
use App\InjectionInventory;
use DateTime;
use DateInterval;

class InjectionScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'injection:schedule';

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

        $inventory = InjectionInventory::get();
        foreach ($inventory as $key) {
            $invent = InjectionInventory::find($key->id);
            $invent->forceDelete();
        }
        // $j = 2;
        // $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        // foreach ($weekly_calendars as $key) {
        //     if ($key->week_date == $nextdayplus1) {
        //         if ($key->remark == 'H') {
        //             $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
        //         }
        //     }
        // }
        // if (date('D')=='Fri' || date('D')=='Sat') {
        //     $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // }

        // $first = date('Y-m-01');
        // $now = date('Y-m-d');

        // $tomorrow = date('Y-m-d', strtotime(carbon::now()->addDays(1)));

        // $data = DB::SELECT("SELECT
        //     c.material_number,
        //     c.material_description,
        //     c.part_code,
        //     c.color,
        //     SUM( c.stock ) AS stock,
        //     SUM( c.plan ) AS plan,
        //     SUM( c.stock ) - SUM( c.plan ) AS diff,
        // IF
        //     (
        //         SUM( c.stock ) - SUM( c.plan ) >= 0,
        //         0,-(
        //         SUM( c.stock ) - SUM( c.plan ))) AS debt 
        // FROM
        //     (
        //     SELECT
        //         gmc as material_number,
        //         part_name as material_description,
        //         color,
        //         part_code,
        //         COALESCE (( SELECT quantity FROM injection_inventories WHERE location = 'RC91' AND material_number = gmc ), 0 ) AS stock,
        //         0 AS plan 
        //     FROM
        //         injection_parts 
        //     WHERE
        //         remark = 'injection' 

        // UNION ALL

        // SELECT
        //         a.material_number,
        //         a.material_description,
        //         a.color,
        //         a.part_code,
        //         0 AS stock,
        //         sum( a.plan )- sum( a.stamp ) AS plan 
        //     FROM
        //         (
        //         SELECT
        //             injection_part_details.gmc as material_number,
        //             injection_part_details.part as material_description,
        //             injection_part_details.color,
        //             injection_part_details.part_code,
        //             SUM( quantity ) AS plan,
        //             0 AS stamp 
        //         FROM
        //             production_schedules
        //             LEFT JOIN materials ON materials.material_number = production_schedules.material_number
        //             LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
        //         WHERE
        //             materials.category = 'FG' 
        //             AND materials.origin_group_code = '072' 
        //             AND production_schedules.due_date BETWEEN '".$first."' 
        //             AND '".$nextdayplus1."' 
        //         GROUP BY
        //             material_number,part,color,part_code
                
        //         UNION ALL
                
        //         SELECT
        //             injection_part_details.gmc as material_number,
        //             injection_part_details.part as material_description,
        //             injection_part_details.color,
        //             injection_part_details.part_code,
        //             0 AS plan,
        //             SUM( quantity ) AS stamp 
        //         FROM
        //             flo_details
        //             LEFT JOIN materials ON materials.material_number = flo_details.material_number
        //             LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
        //         WHERE
        //             materials.category = 'FG' 
        //             AND materials.origin_group_code = '072' 
        //             AND DATE( flo_details.created_at ) BETWEEN '".$first."' 
        //             AND '".$now."'
        //         GROUP BY
        //             material_number,
        //             part,color,part_code
        //         ) a GROUP BY
        //         a.material_number,a.material_description,a.color,a.part_code
        //     ) c 
        // GROUP BY
        //     c.material_number,c.material_description,c.color,c.part_code
        // ORDER BY
        // material_number");

        // foreach ($data as $key) {
        //     if ($key->debt != 0) {
        //         $partpart = explode(' ',$key->part_code);
        //         $colorcolor = explode(')',$key->color);

        //         $schedule = InjectionScheduleTemp::firstOrNew([
        //             'material_number' => $key->material_number,
        //             'date' => date('Y-m-d')
        //         ]);
        //         $schedule->date = date('Y-m-d');
        //         $schedule->due_date = $tomorrow;
        //         $schedule->material_description = $key->material_description;
        //         $schedule->part = $partpart[0];
        //         $schedule->color = $colorcolor[0];
        //         $schedule->stock = $key->stock;
        //         $schedule->plan = $key->plan;
        //         $schedule->diff = $key->diff;
        //         $schedule->debt = $key->debt;
        //         $schedule->created_by = '1930';
        //         $schedule->save();
        //     }
        // }

        // $debttoday = DB::SELECT('SELECT
        //     *,
        //     SPLIT_STRING ( machine, ",", 1 ) AS mesin1,
        //     SPLIT_STRING ( machine, ",", 2 ) AS mesin2,
        //     SPLIT_STRING ( machine, ",", 3 ) AS mesin3 
        // FROM
        //     injection_schedule_temps
        //     LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_temps.part 
        //     AND injection_machine_cycle_times.color = injection_schedule_temps.color 
        // WHERE
        //     date = DATE(
        //     NOW())');

        // $schedules = [];

        // $count = 0;

        // foreach ($debttoday as $val) {
        //     $shot = $val->debt/$val->shoot;
        //     $time = $shot*$val->cycle;
        //     $minute = $time / 60;
        //     $hour = $minute / 60;
        //     $day = $hour / 24;
        //     $mesin = '';
        //     $start_time = '';
        //     $end_time = '';

        //     $count = 0;

        //     if ($val->mesin1 != "") {
        //         $machineavl = $this->checkMachine($val->mesin1,$val->due_date);
        //         if ($machineavl != "") {
        //             $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //             $start_time = $machineavl;
        //             $mesin = 'Mesin '.$val->mesin1;
        //             $count++;
        //         }else{
        //             // $machineavl = $this->checkMachine($val->mesin1,$now);
        //             // if (date('Y-m-d',strtotime($machineavl)) != $now) {
        //             //     $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //             //     $start_time = $machineavl;
        //             //     $mesin = 'Mesin '.$val->mesin2;
        //             //     $count++;
        //             // }else{
        //                 $end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s'))+$time);
        //                 $start_time = date('Y-m-d H:i:s');
        //                 $mesin = 'Mesin '.$val->mesin2;
        //                 $count++;
        //             // }
        //         }
        //     }
        //     if ($val->mesin2 != "") {
        //         if ($count == 0) {
        //             $machineavl = $this->checkMachine($val->mesin2,$val->due_date);
        //             if ($machineavl != "") {
        //                 $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //                 $start_time = $machineavl;
        //                 $mesin = 'Mesin '.$val->mesin2;
        //                 $count++;
        //             }else{
        //                 // $machineavl = $this->checkMachine($val->mesin2,$now);
        //                 // if (date('Y-m-d',strtotime($machineavl)) != $now) {
        //                 //     $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //                 //     $start_time = $machineavl;
        //                 //     $mesin = 'Mesin '.$val->mesin3;
        //                 //     $count++;
        //                 // }else{
        //                     $end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s'))+$time);
        //                     $start_time = date('Y-m-d H:i:s');
        //                     $mesin = 'Mesin '.$val->mesin3;
        //                     $count++;
        //                 // }
        //             }
        //         }
        //     }
        //     if ($val->mesin3 != "") {
        //         if ($count == 0) {
        //             $machineavl = $this->checkMachine($val->mesin3,$val->due_date);
        //             if ($machineavl != "") {
        //                 $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //                 $start_time = $key->end_time;
        //                 $mesin = 'Mesin '.$val->mesin3;
        //                 $count++;
        //             }else{
        //                 // $machineavl = $this->checkMachine($val->mesin3,$now);
        //                 // if (date('Y-m-d',strtotime($machineavl)) != $now) {
        //                 //     $end_time = date("Y-m-d H:i:s",strtotime(date($machineavl))+$time);
        //                 //     $start_time = $machineavl;
        //                 //     $mesin = 'Mesin '.$val->mesin3;
        //                 //     $count++;
        //                 // }else{
        //                     $end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s'))+$time);
        //                     $start_time = date('Y-m-d H:i:s');
        //                     $mesin = 'Mesin '.$val->mesin3;
        //                     $count++;
        //                 // }
        //             }
        //         }
        //     }
        //     $schedules[] = array(
        //         'material_number' => $val->material_number,
        //         'material_description' => $val->material_description,
        //         'part' => $val->part,
        //         'color' => $val->color,
        //         'qty' => $val->debt,
        //         'start_time' => $start_time,
        //         'end_time' => $end_time,
        //         'machine' => $mesin,
        //         'created_by' => 1,
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updated_at' => date('Y-m-d H:i:s'),
        //     );
        //     // $cycle =DB::SELECT("SELECT * FROM injection_machine_cycle_times where part = '".$val->part."' and color = '".$val->color."'");
        //     // foreach ($cycle as $vul) {
        //     //     $shot = $val->debt/$vul->shoot;
        //     //     $time = $shot*$vul->cycle;
        //     //     $minute = $time / 60;
        //     //     $hour = $minute / 60;
        //     //     $day = $hour / 24;
        //     //     $machine = explode(',', $vul->machine);
        //     //     $mesin = '';
        //     //     $start_time = '';
        //     //     $end_time = '';
        //     //     for ($i = 0;$i < count($machine);$i++) {
        //     //         $machineavl = DB::SELECT("SELECT * FROM injection_schedule_logs where machine = 'Mesin ".$machine[$i]."' ORDER BY id desc");
        //     //         if (count($machineavl) > 0) {
        //     //             foreach ($machineavl as $vol) {
        //     //                 if (count($vol->end_time) > 0) {
        //     //                     if (date('Y-m-d', strtotime($vol->end_time)) <= $nextdayplus1) {
        //     //                         $end_time = date("Y-m-d H:i:s",strtotime(date($vol->end_time))+$time);
        //     //                         $start_time = $vol->end_time;
        //     //                         $mesin = $vol->machine;

        //     //                         $schedules[] = array(
        //     //                             'material_number' => $val->material_number,
        //     //                             'material_description' => $val->material_description,
        //     //                             'part' => $val->part,
        //     //                             'color' => $val->color,
        //     //                             'qty' => $val->debt,
        //     //                             'start_time' => $start_time,
        //     //                             'end_time' => $end_time,
        //     //                             'machine' => $mesin,
        //     //                             'created_by' => 1,
        //     //                             'created_at' => date('Y-m-d H:i:s'),
        //     //                             'updated_at' => date('Y-m-d H:i:s'),
        //     //                         );
        //     //                         break;
        //     //                     }
        //     //                 }else{
        //     //                     $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))+$time);
        //     //                     $start_time = date('Y-m-d H:i:s');
        //     //                     $mesin = 'Mesin '.$machine[$i];
        //     //                 }
        //     //             }
        //     //         }else{
        //     //             // if ($schedules[]->part != $val->part) {
        //     //                 $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))+$time);
        //     //                 $start_time = date('Y-m-d H:i:s');
        //     //                 $mesin = 'Mesin '.$machine[$i];

        //     //                 $schedules[] = array(
        //     //                     'material_number' => $val->material_number,
        //     //                     'material_description' => $val->material_description,
        //     //                     'part' => $val->part,
        //     //                     'color' => $val->color,
        //     //                     'qty' => $val->debt,
        //     //                     'start_time' => $start_time,
        //     //                     'end_time' => $end_time,
        //     //                     'machine' => $mesin,
        //     //                     'created_by' => 1,
        //     //                     'created_at' => date('Y-m-d H:i:s'),
        //     //                     'updated_at' => date('Y-m-d H:i:s'),
        //     //                 );
        //     //                 break;
        //     //             // }
        //     //         }
        //     //     }
        //     // }
        // }

        // //var_dump($schedules);

        // // $partinject = '';
        // // $colorinject = '';
        // for ($i=0; $i < count($schedules); $i++) {
        //     DB::table('injection_schedule_logs')->insert([
        //         $schedules[$i]
        //     ]);
        // }
    }

    // public function checkMachine($mesin,$due_date)
    // {
    //     $machineavl = DB::SELECT("SELECT * FROM injection_schedule_logs where machine = 'Mesin ".$mesin."' and DATE(end_time) < '".$due_date."' ORDER BY id desc");

    //     $end_time = "";

    //     if (count($machineavl) > 0) {
    //         foreach ($machineavl as $key) {
    //             $end_time = $key->end_time;
    //         }
    //     }

    //     return $end_time;
    // }
}
