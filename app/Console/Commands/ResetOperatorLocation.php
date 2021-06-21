<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\EmployeeSync;
use App\MaintenanceOperatorLocation;
use App\MaintenanceOperatorLocationLog;

class ResetOperatorLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mtc:op_reset {shift}';

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
        $mtc_op = MaintenanceOperatorLocation::get();

        foreach ($mtc_op as $loc_op) {
            if ($loc_op->qr_code) {
                $mtc_op_log = new MaintenanceOperatorLocationLog;
                $mtc_op_log->employee_id = $loc_op->employee_id;
                $mtc_op_log->employee_name = $loc_op->employee_name;
                $mtc_op_log->qr_code = $loc_op->qr_code;
                $mtc_op_log->machine_id = $loc_op->machine_id;
                $mtc_op_log->description = $loc_op->description;
                $mtc_op_log->location = $loc_op->location;
                $mtc_op_log->remark = $loc_op->remark;
                $mtc_op_log->logged_in_at = $loc_op->created_at;
                $mtc_op_log->logged_out_at = date('Y-m-d H:i:s');
                $mtc_op_log->created_by = $loc_op->employee_id;
                $mtc_op_log->save();
            }            
        }

        MaintenanceOperatorLocation::truncate();

        $emp = EmployeeSync::leftJoin('sunfish_shift_syncs', 'sunfish_shift_syncs.employee_id', '=', 'employee_syncs.employee_id')
        ->where('shift_date', '=', date('Y-m-d'))
        ->where('group', '=', 'Maintenance Group')
        ->whereNull('end_date')
        ->where('shiftdaily_code', 'LIKE', '%'.$this->argument('shift').'%')
        ->get();

        foreach ($emp as $op_mtc) {
            $mtc_op = new MaintenanceOperatorLocation;
            $mtc_op->employee_id = $op_mtc->employee_id;
            $mtc_op->employee_name = $op_mtc->name;
            $mtc_op->qr_code = 'AMTCR01';
            $mtc_op->machine_id = 'AMTCR01';
            $mtc_op->description = 'Maintenance';
            $mtc_op->location = 'Maintenance';
            $mtc_op->remark = 'job';
            $mtc_op->created_by = $op_mtc->employee_id;
            $mtc_op->save();
        }

    }
}
