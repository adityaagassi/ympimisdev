<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class SendEmailSPKNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spk:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SPK Urgent notification to Manager';

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
        $mail_to = db::table('send_emails')
        ->where('remark', '=', 'spk')
        ->WhereNull('deleted_at')
        ->select('email')
        ->get();

        $datas = db::select("SELECT mjo.order_no, department_shortname as department, priority, description, date_format(mjo.created_at, '%Y-%m-%d') as req_date, start_actual, process_name from maintenance_job_orders as mjo
            left join departments on departments.department_name = SUBSTRING_INDEX(mjo.section,'_',1)
            left join (select process_code, process_name from processes where remark = 'Maintenance') as prs on prs.process_code = mjo.remark
            left join maintenance_job_processes as mjp on mjo.order_no = mjp.order_no
            where priority = 'Urgent' and mjo.remark <> 6");

        $data = [
            'datas' => $datas,
        ];

        if($data != null){
            Mail::to($mail_to)->send(new SendEmail($data, 'spk_urgent'));
        }
    }
}
