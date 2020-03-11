<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class EmailVisitorConfirmation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:visitor_confirmation';

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
        $mgr = DB::SELECT("SELECT * FROM `employee_syncs` where position like '%Manager%'");
        $mail_to = DB::SELECT("SELECT email FROM `send_emails` where remark = 'visitor_confirmation'");
        $namamanager = [];

        foreach ($mgr as $key) {
            $visitor = DB::SELECT("select 
            visitors.id,name,department,company,DATE_FORMAT(visitors.created_at,'%Y-%m-%d') created_at2,visitors.created_at,visitor_details.full_name, visitor_details.id_number as total1 ,purpose, visitors.status,visitor_details.in_time, visitor_details.out_time, visitors.remark 
            from visitors 
            LEFT JOIN visitor_details on visitors.id = visitor_details.id_visitor 
            LEFT JOIN employee_syncs on visitors.employee = employee_syncs.employee_id
            where visitors.remark is null and employee_syncs.nik_manager = '".$key->employee_id."'");
            if (count($visitor) > 0) {
                $namamanager[] = [ 'manager_name' => $key->name,
                'jumlah_visitor' => count($visitor)
                ];
            }
        }
        if(count($namamanager) > 0){
            Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com','Mokhamad Khamdan Khabibi')->send(new SendEmail($namamanager, 'visitor_confirmation'));
        }
    }
}
