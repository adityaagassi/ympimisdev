<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\BatchSetting;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UploadCompletions::class,
        Commands\UploadTransfers::class,
        Commands\PlanStamps::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $batch_flos = BatchSetting::where('remark', '=', 'FLO')->get();
        $batch_plan_stamps = BatchSetting::where('remark', '=', 'PLANSTAMP')->get();
        
        foreach($batch_flos as $batch_flo){
            if($batch_flo->upload == 1){
                $schedule->command('upload:completions')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
                $schedule->command('upload:transfers')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
            }
        }

        foreach($batch_plan_stamps as $batch_plan_stamp){
            if($batch_plan_stamp->upload == 1){
                $schedule->command('plan:stamps')->dailyAt(date('H:i', strtotime($batch_plan_stamp->batch_time)));
            }
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}