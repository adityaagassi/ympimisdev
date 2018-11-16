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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $batchs = BatchSetting::get();
        
        foreach($batchs as $batch){
            if($batch->upload == 1){
                $schedule->command('upload:completions')->dailyAt(date('H:i', strtotime($batch->batch_time)));
                $schedule->command('upload:transfers')->dailyAt(date('H:i', strtotime($batch->batch_time)));
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