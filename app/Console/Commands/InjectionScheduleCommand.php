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

        // $inventory = InjectionInventory::get();
        // foreach ($inventory as $key) {
        //     $invent = InjectionInventory::find($key->id);
        //     $invent->forceDelete();
        // }
        InjectionScheduleTemp::truncate();
        InjectionScheduleLog::truncate();
        $j = 30;
        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        foreach ($weekly_calendars as $key) {
            if ($key->week_date == $nextdayplus1) {
                if ($key->remark == 'H') {
                    $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                }
            }
        }
        if (date('D')=='Fri' || date('D')=='Sat') {
            $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        }

        // $nextdayplus1 = date('Y-m-t');

        $first = date('Y-m-01');
        $now = date('Y-m-d');

        $tomorrow = date('Y-m-d', strtotime(carbon::now()->addDays(1)));

        $data = DB::SELECT("SELECT
            c.material_number,
            c.material_description,
            c.part_code,
            c.color,
            SUM( c.stock ) AS stock,
            SUM( c.plan ) AS plan,
            SUM( c.stock ) - SUM( c.plan ) AS diff,
        IF
            (
                SUM( c.stock ) - SUM( c.plan ) >= 0,
                0,-(
                SUM( c.stock ) - SUM( c.plan ))) AS debt 
        FROM
            (
            SELECT
                gmc as material_number,
                part_name as material_description,
                color,
                part_code,
                COALESCE (( SELECT SUM(quantity) FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc ), 0 ) AS stock,
                0 AS plan 
            FROM
                injection_parts 
            WHERE
                remark = 'injection' 
                and deleted_at is null

        UNION ALL

        SELECT
                a.material_number,
                a.material_description,
                a.color,
                a.part_code,
                0 AS stock,
                sum( a.plan )- sum( a.stamp ) AS plan 
            FROM
                (
                SELECT
                    injection_part_details.gmc as material_number,
                    injection_part_details.part as material_description,
                    injection_part_details.color,
                    injection_part_details.part_code,
                    SUM( quantity ) AS plan,
                    0 AS stamp 
                FROM
                    production_schedules
                    LEFT JOIN materials ON materials.material_number = production_schedules.material_number
                    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                WHERE
                    materials.category = 'FG' 
                    AND materials.origin_group_code = '072' 
                    AND production_schedules.due_date BETWEEN '".$first."' 
                    AND '".$nextdayplus1."' 
                GROUP BY
                    material_number,part,color,part_code
                
                UNION ALL
                
                SELECT
                    injection_part_details.gmc as material_number,
                    injection_part_details.part as material_description,
                    injection_part_details.color,
                    injection_part_details.part_code,
                    0 AS plan,
                    SUM( quantity ) AS stamp 
                FROM
                    flo_details
                    LEFT JOIN materials ON materials.material_number = flo_details.material_number
                    LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
                WHERE
                    materials.category = 'FG' 
                    AND materials.origin_group_code = '072' 
                    AND DATE( flo_details.created_at ) BETWEEN '".$first."' 
                    AND '".$nextdayplus1."'
                GROUP BY
                    material_number,
                    part,color,part_code
                ) a GROUP BY
                a.material_number,a.material_description,a.color,a.part_code
            ) c 
        GROUP BY
            c.material_number,c.material_description,c.color,c.part_code
        ORDER BY
        material_number");

        foreach ($data as $key) {
            if ($key->debt != 0) {
                // $partpart = explode(' ',$key->part_code);
                // $colorcolor = explode(')',$key->color);

                $schedule = InjectionScheduleTemp::firstOrNew([
                    'material_number' => $key->material_number,
                    'date' => date('Y-m-d')
                ]);
                $schedule->date = date('Y-m-d');
                $schedule->due_date = $tomorrow;
                $schedule->material_description = $key->material_description;
                $schedule->part = $key->part_code;
                $schedule->color = $key->color;
                $schedule->stock = $key->stock;
                $schedule->plan = $key->plan;
                $schedule->diff = $key->diff;
                $schedule->debt = $key->debt;
                $schedule->created_by = '1930';
                $schedule->save();
            }
        }

        // $debttoday = DB::SELECT("SELECT
        //         date,
        //         due_date,
        //         material_number,
        //         material_description,
        //         b.part,
        //         b.color,
        //         stock,
        //         plan,
        //         diff,
        //         debt,
        //         model,
        //         cycle,
        //         shoot,
        //         qty,
        //         qty_hako,
        //         machine,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3,
        //     IF
        //         ((
        //             SELECT
        //                 machine 
        //             FROM
        //                 injection_schedule_logs 
        //             WHERE
        //                 machine = b.mesin1 
        //                 AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date 
        //                 ) IS NULL,
        //             b.mesin1,
        //         IF
        //             ((
        //                 SELECT
        //                     machine 
        //                 FROM
        //                     injection_schedule_logs 
        //                 WHERE
        //                     machine = b.mesin2 
        //                     AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date  
        //                     ) IS NULL,
        //             IF
        //                 ( b.mesin2 = 'Mesin ', b.mesin1, b.mesin2 ),
        //             IF
        //                 ((
        //                     SELECT
        //                         machine 
        //                     FROM
        //                         injection_schedule_logs 
        //                     WHERE
        //                         machine = b.mesin3 
        //                         AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date  
        //                         ) IS NULL,
        //                 IF
        //                     ( b.mesin3 = 'Mesin ', b.mesin1, b.mesin3 ),
        //                     b.mesin1 
        //                 ) 
        //             )) AS machine_now,
        //             (
        //             SELECT
        //                 machine 
        //             FROM
        //                 injection_schedule_logs 
        //             WHERE
        //                 machine = b.mesin1 
        //                 AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date  
        //                 ) as mesin1work,
        //                 (
        //             SELECT
        //                 machine 
        //             FROM
        //                 injection_schedule_logs 
        //             WHERE
        //                 machine = b.mesin2 
        //                 AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date  
        //                 ) as mesin2work,
        //                 (
        //             SELECT
        //                 machine 
        //             FROM
        //                 injection_schedule_logs 
        //             WHERE
        //                 machine = b.mesin3 
        //                 AND DATE( end_time ) BETWEEN DATE(NOW()) AND b.due_date  
        //                 ) as mesin3work,
        //         ROUND(((( b.debt / b.shoot )* b.cycle )/ 60 )/ 60 ) AS jam,
        //         ROUND((( b.debt / b.shoot )* b.cycle )/ 60 ) AS menit,
        //         ROUND(( b.debt / b.shoot )* b.cycle ) AS detik
        //     FROM
        //         (
        //         SELECT
        //             date,
        //             due_date,
        //             material_number,
        //             material_description,
        //             a.part,
        //             a.color,
        //             stock,
        //             plan,
        //             diff,
        //             debt,
        //             model,
        //             cycle,
        //             shoot,
        //             qty,
        //             qty_hako,
        //             machine,
        //             CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
        //             CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
        //             CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3 
        //         FROM
        //             injection_schedule_temps AS a
        //             LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = a.part 
        //             AND injection_machine_cycle_times.color = a.color 
        //         WHERE
        //         date = DATE(
        //         NOW())) b");

        $debttoday = DB::SELECT("SELECT
            date,
            due_date,
            material_number,
            material_description,
            b.part,
            b.color,
            stock,
            plan,
            diff,
            debt,
            model,
            cycle,
            shoot,
            qty,
            qty_hako,
            machine,
            CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
            CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
            CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3,
            ROUND(((( b.debt / b.shoot )* b.cycle )/ 60 )/ 60 ) AS jam,
            ROUND((( b.debt / b.shoot )* b.cycle )/ 60 ) AS menit,
            ROUND(( b.debt / b.shoot )* b.cycle ) AS detik 
        FROM
            (
            SELECT
                date,
                due_date,
                material_number,
                material_description,
                a.part,
                a.color,
                stock,
                plan,
                diff,
                debt,
                model,
                cycle,
                shoot,
                qty,
                qty_hako,
                machine,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3 
            FROM
                injection_schedule_temps AS a
                LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = a.part 
                AND injection_machine_cycle_times.color = a.color 
            WHERE
            date = DATE(
            NOW())) b");

        $mesin = DB::SELECT("SELECT
            mesin 
        FROM
            injection_machine_masters");

        $schedules = [];

        $count = 0;

        foreach ($debttoday as $key) {
            $schedules[] = array(
                'material_number' => $key->material_number,
                'material_description' => $key->material_description,
                'part' => $key->part,
                'color' => $key->color,
                'qty' => $key->debt,
                'start_time' => date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))),
                'end_time' => date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$key->detik),
                'machine' => $key->mesin1,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
        }

        for ($i=0; $i < count($schedules); $i++) {
            DB::table('injection_schedule_logs')->insert([
                $schedules[$i]
            ]);
        }

        $mesinsama = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
        FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
        ORDER BY
            injection_schedule_logs.machine,
        IF
        ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama) > 0) {
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama); $i++) { 
                    if ($mesinsama[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama[$i]);
                    }
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    if ($mesins[$j]->machine_2 != 0) {
                        $log = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                        $log->machine = 'Mesin '.$mesins[$j]->machine_2;
                        $log->save();
                    }
                }

                $dandori = 0;
                $dandori_time = 0;

                for ($m=0; $m < count($mesins); $m++) {
                    var_dump($dandori);
                    if ($mesins[$m]->start_time == date('Y-m-d 07:00:00')) {
                        if ($dandori % 2 == 0) {
                            $dandori_time = $dandori_time + 14400;
                        }
                        $log = InjectionScheduleLog::where('id',$mesins[$m]->id)->first();
                        $ts1 = strtotime($log->start_time);
                        $ts2 = strtotime($log->end_time);
                        $seconds_diff = $ts2 - $ts1;
                        $secondall = $seconds_diff+$dandori_time;
                        $log->start_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$dandori_time);
                        $log->end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$secondall);
                        $log->save();
                        $dandori++;
                    }
                }
            }
        }

        $mesinsama2 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
        FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
        ORDER BY
            injection_schedule_logs.machine,
        IF
        ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama2) > 0) {
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama2); $i++) { 
                    if ($mesinsama2[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama2[$i]);
                    }
                }

                for ($j=1; $j < count($mesins); $j++) { 
                    if ($mesins[$j]->machine_2 == 0) {
                        $log = InjectionScheduleLog::where('machine',$mesins[$j]->machine)->get();
                        $end = $log[0]->end_time;
                        if (count($log) > 1) {
                            for ($k=1; $k < count($log); $k++) {
                                $log2 = InjectionScheduleLog::where('id',$log[$k]->id)->first();
                                $ts1 = strtotime($log2->start_time);
                                $ts2 = strtotime($log2->end_time);
                                $seconds_diff = $ts2 - $ts1;
                                $secondall = $seconds_diff+14400;
                                $log2->start_time = date("Y-m-d H:i:s",strtotime($end)+14400);
                                $end_time = date("Y-m-d H:i:s",strtotime($end)+$secondall);
                                $log2->end_time = $end_time;
                                $log2->save();
                                $end = $end_time;
                            }
                        }
                    }else {
                        $log = InjectionScheduleLog::where('machine',$mesins[$j]->machine)->get();
                        $end = $log[0]->end_time;
                        if (count($log) > 1) {
                            for ($l=1; $l < count($log); $l++) { 
                                $log2 = InjectionScheduleLog::where('id',$log[$l]->id)->first();
                                $ts1 = strtotime($log2->start_time);
                                $ts2 = strtotime($log2->end_time);
                                $seconds_diff = $ts2 - $ts1;
                                $secondall = $seconds_diff+14400;
                                $log2->start_time = date("Y-m-d H:i:s",strtotime($end)+14400);
                                $end_time = date("Y-m-d H:i:s",strtotime($end)+$secondall);
                                $log2->end_time = $end_time;
                                $log2->save();
                                $end = $end_time;
                            }
                        }
                    }
                }
            }
        }

        $mesinsama3 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
        FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            and IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) != 0
        ORDER BY
            injection_schedule_logs.machine,
        IF
        ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama3) > 0) {
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama3); $i++) { 
                    if ($mesinsama3[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama3[$i]);
                    }
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    $log = InjectionScheduleLog::where('machine','Mesin '.$mesins[$j]->machine_1)->orderBy('id','desc')->first();
                    if (count($log) > 0) {
                        $end = $log->end_time;
                        if ($mesins[$j]->start_time > $end) {
                            $log2 = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                            $log2->machine = 'Mesin '.$mesins[$j]->machine_1;
                            $log2->save();
                        }
                    }
                }
            }
        }

        $mesinsama4 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
        IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
        FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            and IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) != 0
        ORDER BY
            injection_schedule_logs.machine,
        IF
        ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama4) > 0) {
            foreach ($mesin as $key) {
                $end = "";
                $mesins = [];
                for ($i=0; $i < count($mesinsama4); $i++) { 
                    if ($mesinsama4[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama4[$i]);
                    }
                }
                if (count($mesins) > 0) {
                    $end = $mesins[0]->end_time;
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    $log = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                    if (count($log) > 0) {
                        $ts1 = strtotime($end);
                        $ts2 = strtotime($log->start_time);
                        $seconds_diff = $ts2 - $ts1;
                        if ($seconds_diff > 14400 || $seconds_diff < 14400) {
                            $ts1 = strtotime($log->start_time);
                            $ts2 = strtotime($log->end_time);
                            $seconds_diff = $ts2 - $ts1;
                            $secondall = $seconds_diff+14400;
                            $log->start_time = date("Y-m-d H:i:s",strtotime($end)+14400);
                            $end_time = date("Y-m-d H:i:s",strtotime($end)+$secondall);
                            $log->end_time = $end_time;
                            $log->save();
                        }else{
                            $end_time = $mesins[$j]->end_time;
                        }
                        $end = $end_time;
                    }
                }
            }
        }
    }
}
