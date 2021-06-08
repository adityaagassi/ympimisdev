<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

use App\MaintenanceJobOrder;


class SendEmailSPK extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spk:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command menotifikasi apabila ada spk masuk 30 menit belum distribusi';

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
        $datas = db::select("SELECT mjo.order_no, mjo.priority, mjo.section, mjo.target_date, mjo.type, mjo.danger, mjo.category, mjo.machine_condition, mjo.description, mjo.safety_note, mjo.machine_name as machine_temp, maintenance_plan_items.description as machine_desc, mjo.machine_remark, floor(TIME_TO_SEC(TIMEDIFF(now(),mjo.updated_at)) / 60) as diff, u.name as pemohon FROM `maintenance_job_orders` as mjo
            left join employee_syncs u on mjo.created_by = u.employee_id
            left join maintenance_plan_items on maintenance_plan_items.machine_id = mjo.machine_name
            where mjo.deleted_at is null 
            and mjo.remark in (0,2) 
            and mjo.machine_condition = 'Berhenti'
            and floor(TIME_TO_SEC(TIMEDIFF(now(),mjo.updated_at)) / 60) >= 30
            and mjo.notif is null");


        if($datas != null){
            Mail::to(['susilo.basri@music.yamaha.com','bambang.supriyadi@music.yamaha.com', 'nadif@music.yamaha.com', 'priyo.jatmiko@music.yamaha.com', 'duta.narendratama@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($datas, 'spk_machine_stop'));

            foreach ($datas as $data) {
                MaintenanceJobOrder::where('order_no', '=', $data->order_no)
                ->update(['notif' => 1]);
            }
        }
    }
}
