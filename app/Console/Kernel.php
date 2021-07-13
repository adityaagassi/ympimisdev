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
        Commands\EmailMiddleKanban::class,
        Commands\EmailConfirmationOvertimes::class,
        Commands\EmailUserDocument::class,        
        Commands\EmailClinicVisit::class,        
        Commands\InjectionSchedule::class,
        Commands\InjectionScheduleTrial::class,
        Commands\CallRoute::class,
        Commands\UploadCompletionKD::class,
        Commands\UploadTransferKD::class,
        Commands\SyncSunfish::class,
        Commands\SendEmailKaizen::class,
        Commands\EmployeeHistory::class,
        Commands\EmailVisitorConfirmation::class,
        // Commands\SendMachineNotification::class,
        Commands\EmailHrq::class,
        Commands\RoomTemperatureLog::class,
        Commands\SurveyCovid::class,
        Commands\UpdateAddress::class,
        Commands\HighestCovidCommand::class,
        // Commands\APARAutoPR::class,
        // Commands\SchedulingChemical::class,
        // Commands\SendEmailChemicalNotInput::class,
        // Commands\SendEmailChemicalUnpicked::class,
        Commands\InterviewPointingCallCommand::class,
        Commands\UpdatePointingCall::class,
        Commands\SkillUnfulfilledLogCommand::class,
        Commands\CostCenterHistoryCommand::class,
        Commands\InjectionScheduleCommand::class,
        Commands\KDShipment::class,
        Commands\SendEmailSPKNotification::class,
        Commands\SendEmailSPK::class,
        Commands\EmailAgreement::class,
        Commands\LiveCookingCommand::class,
        Commands\GreatdayAttendanceCommand::class,
        Commands\GeocodeUpdate::class,
        Commands\SyncShiftSunfish::class,

        Commands\ResumeNgBuffing::class,
        Commands\ResumeNgLacquering::class,
        Commands\ResumeNgPlating::class,
        Commands\RawMaterialReminder::class,
        Commands\EmailBento::class,
        Commands\DoubleTransactionNotification::class,
        Commands\RawMaterialOverUsage::class,
        Commands\ResetOperatorLocation::class,


        //KITTO
        Commands\UploadTransferKitto::class,
        Commands\UploadCompletionKitto::class,
        Commands\RecordDailyStocks::class,
        Commands\RecordStockMiddle::class,

        //WH
        // Commands\UpdateStatusOperator::class,

        
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
                $schedule->command('upload:completionkitto')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
                $schedule->command('upload:transferkitto')->dailyAt(date('H:i', strtotime($batch_flo->batch_time)));
            }
        }

        foreach($batch_plan_stamps as $batch_plan_stamp){
            if($batch_plan_stamp->upload == 1){
                $schedule->command('plan:stamps')->dailyAt(date('H:i', strtotime($batch_plan_stamp->batch_time)));
            }
        }

        // $chedule->command('email:bento')->weeklyOn(1, '07:00');;

        // $schedule->command('plan:leaves')->monthlyOn(1, '01:00');

        $schedule->command('record:daily_stocks')->dailyAt('07:00');
        $schedule->command('record:stock_middle')->dailyAt('07:00');
        $schedule->command('record:stock_middle')->dailyAt('16:00');

        $schedule->command('email:shipment')->weekdays()->dailyAt('08:40');
        $schedule->command('email:overtime')->weekdays()->dailyAt('08:42');
        $schedule->command('email:shipment')->weekends()->dailyAt('13:00');
        // $schedule->command('email:overtime')->weekends()->dailyAt('13:02');
        $schedule->command('email:middle_kanban')->weekdays()->dailyAt('07:15');
        $schedule->command('email:visitor_confirmation')->weekdays()->dailyAt('07:00');
        // $schedule->command('email:visitor_confirmation')->everyMinute();
        // $schedule->command('email:confirmation_overtime')->weekdays()->dailyAt('06:55');

        // $schedule->command('plan:injections')->weekdays()->dailyAt('08:40');
        $schedule->command('sync:sunfish')->weekdays()->dailyAt('03:01');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('05:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('06:00');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('06:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('06:45');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('07:00');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('08:00');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('09:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('12:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('14:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('16:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('17:00');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('19:30');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('01:15');
        $schedule->command('sync:shift_sunfish')->weekdays()->dailyAt('01:45');
        $schedule->command('email:kaizen')->weekdays()->dailyAt('08:45');
        $schedule->command('email:hrq')->weekdays()->dailyAt('07:45');
        $schedule->command('log:room_temperature')->everyThirtyMinutes();
        $schedule->command('log:survey_covid')->weekly()->saturdays()->at('12:00');
        $schedule->command('highest:survey_covid')->weekly()->sundays()->at('18:30');
        
        // $schedule->command('log:survey_covid')->weekends()->dailyAt('21:00');
        // $schedule->command('notif:machine')->dailyAt('07:00');
        // $schedule->command('email:kaizen')->everyMinute();
        // $schedule->command('employee:history')->monthlyOn(date('t'), '20:01');

        $schedule->command('email:user_document')->weekdays()->dailyAt('07:00');
        // $schedule->command('email:clinic_visit')->weekdays()->dailyAt('08:00');
        
        $schedule->command('update:address')->dailyAt('08:00');
        $schedule->command('update:address')->dailyAt('18:00');
        $schedule->command('email:agreement')->dailyAt('07:50');
        
        // $schedule->command('scheduling:chemical')->dailyAt('07:20');
        // $schedule->command('scheduling:chemical')->dailyAt('13:20');
        // $schedule->command('scheduling:chemical')->dailyAt('16:20');
        // $schedule->command('scheduling:chemical')->dailyAt('21:20');
        // $schedule->command('email:controlling_chart')->dailyAt('06:00');
        // $schedule->command('email:chemical_unpicked')->dailyAt('06:00');

        $schedule->command('update:pointing_calls')->dailyAt('01:00');
        $schedule->command('skill:unfulfilled_log')->dailyAt('01:00');
        $schedule->command('costcenter:history')->dailyAt('01:00');
        $schedule->command('injection:schedule')->monthlyOn(1, '04:00');
        $schedule->command('interview:schedule')->monthlyOn(1, '02:00');


        $schedule->command('kd:shipment')->everyTenMinutes();
        $schedule->command('spk:notify')->everyTenMinutes();
        $schedule->command('email:raw_material_reminder')->dailyAt('11:00');
        $schedule->command('email:raw_material_over')->dailyAt('11:00');
        $schedule->command('email:double_transaction')->dailyAt('08:00');


        $schedule->command('resume:buffing')->hourlyAt(10);
        $schedule->command('resume:lacquering')->hourlyAt(10);
        $schedule->command('resume:plating')->hourlyAt(10);

        //Warehouse
        // $schedule->command('update:operator_internal')->dailyAt('07:00');
        // $schedule->command('update:operator_internal')->dailyAt('16:00');
        // $schedule->command('update:operator_internal')->dailyAt('16:00');

        $schedule->command('sync:greatday_attendance')->dailyAt('08:00');
        $schedule->command('update:geocode')->dailyAt('08:30');
        $schedule->command('sync:greatday_attendance')->dailyAt('17:00');
        $schedule->command('update:geocode')->dailyAt('18:00');

        $schedule->command('generate:live_cooking')->dailyAt('07:00');

        $schedule->command('mtc:op_reset 1')->weekdays()->dailyAt('07:00');
        $schedule->command('mtc:op_reset 2')->weekdays()->dailyAt('16:30');
        $schedule->command('mtc:op_reset 3')->weekdays()->dailyAt('00:30');

    }

    /**l
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}