<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;
use App\EmployeeSync;
use App\GreatdayAttendance;

class GreatdayAttendanceCommand extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'sync:greatday_attendance';

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

    $now = date('Y-m-d');

    $attendances = db::connection('sunfish')->select("SELECT * 
        FROM
        VIEW_AR_YMPI
        WHERE format(dateTime, 'yyyy-MM-dd') = '".$now."'
        ");

    foreach ($attendances as $attendance) {
        $employee = EmployeeSync::where('employee_id', '=', $attendance->emp_no)->first();
        $latlong = json_decode($attendance->location);
        $mock = null;
        if (ISSET($latlong->mock)) {
            $mock = $latlong->mock;
        }
        // pk.2a8bbcfe2d56fd3f61c2aeb2dd556e69

        // pk.c876df7db9f4dc1f1ec939df75a98ae7

        $insert = GreatdayAttendance::updateOrCreate(
            [
                'date_in' => date('Y-m-d', strtotime($attendance->dateTime)),
                'employee_id' => $attendance->emp_no
            ],
            [
                'employee_id' => $attendance->emp_no,
                'name' => $employee->name,
                'date_in' => date('Y-m-d', strtotime($attendance->dateTime)),
                'time_in' => $attendance->dateTime,
                'task' => $attendance->taskDesc,
                'department' => $employee->department,
                'section' => $employee->section,
                'group' => $employee->group,
                'latitude' => $latlong->latitude,
                'longitude' => $latlong->longitude,
                'mock' => $mock               
            ]
        );

        $insert->save();

    }
}
}
