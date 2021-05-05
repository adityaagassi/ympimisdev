<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Interview;
use Illuminate\Support\Facades\DB;

class InterviewPointingCallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interview:schedule';

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
        $int = DB::SELECT("SELECT DISTINCT
            ( section ),
            activity_lists.id,
            department_name AS department,
            '-' AS subsection,
            (
            SELECT
                fiscal_year 
            FROM
                weekly_calendars 
            WHERE
                week_date = DATE(
                NOW())) AS periode,
            DATE(
            NOW()) AS date,
            leader_dept,
            foreman_dept 
        FROM
            `activity_lists`
            JOIN departments ON departments.id = activity_lists.department_id
            LEFT JOIN employee_syncs ON employee_syncs.department = departments.department_name
            LEFT JOIN employees ON employees.employee_id = employee_syncs.employee_id 
        WHERE
            activity_lists.remark = 'OFC' 
            AND position != 'Manager' 
            AND employees.remark = 'OFC' 
        ORDER BY
            department_name");

        foreach ($int as $key) {
            Interview::create([
                'activity_list_id' => $key->id,
                'department' => $key->department,
                'section' => $key->section,
                'subsection' => $key->subsection,
                'date' => $key->date,
                'periode' => $key->periode,
                'leader' => $key->leader_dept,
                'foreman' => $key->foreman_dept,
                'created_by' => '1930'
            ]);
        }
    }
}
