<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('upload:completions')->dailyAt('09:25');
        $schedule->command('upload:completions')->dailyAt('16:55');
        $schedule->command('upload:completions')->dailyAt('19:55');

        $schedule->command('upload:transfers')->dailyAt('09:25');
        $schedule->command('upload:transfers')->dailyAt('16:55');
        $schedule->command('upload:transfers')->dailyAt('19:55');
        // $schedule->command('inspire')
        //          ->hourly();
        //          
        // foreach (['08:45', '09:15', '09:45', '10:15'] as $time) {
        //     $schedule->command('command:name')->dailyAt($time);
        // }
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