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
        Commands\Leaves::class,
        Commands\SendEmailShipments::class,
        Commands\SendEmailOvertimes::class,
        Commands\RecordDailyStocks::class,
        Commands\EmailMiddleKanban::class,
        Commands\EmailConfirmationOvertimes::class,
        Commands\EmailUserDocument::class,        
        Commands\InjectionSchedule::class,
        Commands\InjectionScheduleTrial::class,
        Commands\CallRoute::class,
        Commands\UploadCompletionKD::class,
        Commands\UploadTransferKD::class,
        Commands\SyncSunfish::class,
        Commands\SendEmailKaizen::class,
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
                $schedule->command('upload:completionKD')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
                $schedule->command('upload:transferKD')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
            }
        }

        foreach($batch_plan_stamps as $batch_plan_stamp){
            if($batch_plan_stamp->upload == 1){
                $schedule->command('plan:stamps')->dailyAt(date('H:i', strtotime($batch_plan_stamp->batch_time)));
            }
        }

        $schedule->command('plan:leaves')->monthlyOn(1, '01:00');

        $schedule->command('record:daily_stocks')->dailyAt('07:00');

        $schedule->command('email:shipment')->weekdays()->dailyAt('08:40');
        $schedule->command('email:overtime')->weekdays()->dailyAt('08:42');
        $schedule->command('email:shipment')->weekends()->dailyAt('13:00');
        $schedule->command('email:overtime')->weekends()->dailyAt('13:02');
        $schedule->command('email:middle_kanban')->weekdays()->dailyAt('07:00');
        // $schedule->command('email:confirmation_overtime')->weekdays()->dailyAt('06:55');

        $schedule->command('plan:injections')->weekdays()->dailyAt('08:40');
        $schedule->command('sync:sunfish')->weekdays()->dailyAt('03:01');
        $schedule->command('email:kaizen')->weekdays()->dailyAt('08:45');

        // $schedule->command('email:user_document')->weekdays()->dailyAt('07:00');


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