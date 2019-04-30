<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\Leave;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Leaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make leave quota to employees';

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
        $month = date('m');
        $now = date('Y-m-01');

        // $employees = Employee::where(db::raw('date_format(employees.hire_date, "%m")'), '=', $month)
        // ->whereNull('employees.end_date')
        // ->leftJoin('leave_quotas', 'leave_quotas.employeed', '=', db::raw('year(now())-year(employees.hire_date)'))
        // ->select('employees.employee_id', db::raw('year(now())-year(employees.hire_date) as employed'), db::raw('date_add(date_add(date_format(now(), "%Y-%m-01"), INTERVAL 1 YEAR), INTERVAL -1 DAY) as valid_to'), 'leave_quotas.leave_quota')
        // ->get();

        $employees = DB::connection('mysql3')->table(DB::raw('ftm.karyawan as kar'))
        ->where(db::raw('date_format(kar.tanggalMasuk, "%m")'),'=', $month)
        ->whereNull(db::raw('kar.tanggalKeluar'))
        ->leftJoin(db::raw('ympimis.leave_quotas as leave_quotas'), db::raw('leave_quotas.employeed'), '=', db::raw('year(now())-year(kar.tanggalMasuk)'))
        ->select(db::raw('kar.nik as employee_id'), db::raw('year(now())-year(kar.tanggalMasuk) as employed'), db::raw('date_add(date_add(date_format(now(), "%Y-%m-01"), INTERVAL 1 YEAR), INTERVAL -1 DAY) as valid_to'), 'leave_quotas.leave_quota')
        ->get();

        foreach ($employees as $employee) {
            $now = date('Y-m-01');
            $leave = Leave::firstOrCreate(
                ['employee_id' => $employee->employee_id, 'valid_from' => $now],
                [
                    'employee_id' => $employee->employee_id,
                    'leave_quota' => $employee->leave_quota,
                    'leave_left' => $employee->leave_quota,
                    'valid_from' => $now,
                    'valid_to' => $employee->valid_to,
                    'created_by' => 1,
                ]
            );
        }
    }
}