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
        $mgr = DB::SELECT("SELECT * FROM `employee_syncs` where position like '%Manager%' or name = 'Dwi Misnanto' or name = 'Nurul Hidayat'");
        $mail_to = DB::SELECT("SELECT email FROM `send_emails` where remark = 'visitor_confirmation'");
        $namamanager = [];

        foreach ($mgr as $key) {
            if ($key->department == null && $key->name == 'Budhi Apriyanto') {
                $visitor = DB::SELECT("SELECT
                        visitors.id,
                        name,
                        department,
                        company,
                        DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                        visitors.created_at,
                        visitor_details.full_name,
                        visitor_details.id_number AS total1,
                        purpose,
                        visitors.status,
                        visitor_details.in_time,
                        visitor_details.out_time,
                        visitors.remark 
                    FROM
                        visitors
                        LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
                        LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
                    WHERE
                        ( visitors.remark IS NULL AND employee_syncs.name = 'Budhi Apriyanto' ) 
                        OR (
                        visitors.remark IS NULL 
                        AND employee_syncs.department = 'Management Information System Department')");
            }elseif ($key->name == 'Susilo Basri Prasetyo') {
                $visitor = DB::SELECT("SELECT
                        visitors.id,
                        name,
                        department,
                        company,
                        DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                        visitors.created_at,
                        visitor_details.full_name,
                        visitor_details.id_number AS total1,
                        purpose,
                        visitors.status,
                        visitor_details.in_time,
                        visitor_details.out_time,
                        visitors.remark 
                    FROM
                        visitors
                        LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
                        LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
                    WHERE
                        ( visitors.remark IS NULL AND employee_syncs.department = 'Production Engineering Department' ) 
                        OR (
                        visitors.remark IS NULL 
                        AND employee_syncs.department = 'Maintenance Department')");
            }elseif ($key->name == 'Prawoto') {
                $visitor = DB::SELECT("SELECT
                        visitors.id,
                        name,
                        department,
                        company,
                        DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                        visitors.created_at,
                        visitor_details.full_name,
                        visitor_details.id_number AS total1,
                        purpose,
                        visitors.status,
                        visitor_details.in_time,
                        visitor_details.out_time,
                        visitors.remark 
                    FROM
                        visitors
                        LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
                        LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
                    WHERE
                        ( visitors.remark IS NULL AND employee_syncs.department = 'Human Resources Department' ) 
                        OR (
                        visitors.remark IS NULL 
                        AND employee_syncs.department = 'General Affairs Department')
                        OR (
                        visitors.remark IS NULL 
                        AND employee_syncs.name = 'Arief Soekamto')");
            }elseif ($key->name == 'Imron Faizal') {
                $visitor = DB::SELECT("SELECT
                        visitors.id,
                        name,
                        department,
                        company,
                        DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                        visitors.created_at,
                        visitor_details.full_name,
                        visitor_details.id_number AS total1,
                        purpose,
                        visitors.status,
                        visitor_details.in_time,
                        visitor_details.out_time,
                        visitors.remark 
                    FROM
                        visitors
                        LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
                        LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
                    WHERE
                        ( visitors.remark IS NULL AND employee_syncs.department = 'Procurement Department' ) 
                        OR (
                        visitors.remark IS NULL 
                        AND employee_syncs.department = 'Purchasing Control Department')");
            }elseif($key->name != 'Takashi Ohkubo'){
                $visitor = DB::SELECT("SELECT
                        visitors.id,
                        name,
                        department,
                        company,
                        DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                        visitors.created_at,
                        visitor_details.full_name,
                        visitor_details.id_number AS total1,
                        purpose,
                        visitors.status,
                        visitor_details.in_time,
                        visitor_details.out_time,
                        visitors.remark 
                    FROM
                        visitors
                        LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
                        LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
                    WHERE
                        visitors.remark IS NULL 
                        AND employee_syncs.department = '".$key->department."'");
            }
            if (count($visitor) > 0) {
                if ($key->name == 'Dwi Misnanto') {
                    $namamanager[] = [ 'manager_name' => $key->name.' (Foreman '.$key->department.')',
                        'jumlah_visitor' => count($visitor)
                    ];
                }elseif ($key->name == 'Nurul Hidayat') {
                    $namamanager[] = [ 'manager_name' => $key->name.' (Leader '.$key->department.')',
                        'jumlah_visitor' => count($visitor)
                    ];
                }else{
                    $namamanager[] = [ 'manager_name' => $key->name,
                        'jumlah_visitor' => count($visitor)
                    ];
                }
            }
        }
        $contactList = [];
        $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
        $contactList[1] = 'aditya.agassi@music.yamaha.com';
        if(count($namamanager) > 0){
            Mail::to($mail_to)->bcc($contactList,'Contact List')->send(new SendEmail($namamanager, 'visitor_confirmation'));
        }
    }
}
