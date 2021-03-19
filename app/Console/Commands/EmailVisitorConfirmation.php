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
        $visitor_department = [];

        $dept = DB::SELECT("SELECT
            department_name 
        FROM
            departments 
        WHERE
            departments.department_name <> 'japan staff'");

        $mgr = DB::SELECT("select * from employee_syncs where department is null");

        // $confirmer = '';
        // for ($i=0; $i < count($dept); $i++) {
        //   $confirmer = $confirmer."'".$dept[$i]->department_name."'";
        //   if($i != (count($dept)-1)){
        //     $confirmer = $confirmer.',';
        //   }
        // }
        // $confirmerin = " AND employee_syncs.department in (".$confirmer.") ";
        // if (preg_match('/Management Information System Department/i', $confirmerin)) {
        //     $visitor = DB::SELECT("SELECT
        //             visitors.id,
        //             name,
        //             department,
        //             company,
        //             DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
        //             visitors.created_at,
        //             visitor_details.full_name,
        //             visitors.jumlah AS total1,
        //             purpose,
        //             visitors.status,
        //             visitor_details.in_time,
        //             visitor_details.out_time,
        //             visitors.remark 
        //         FROM
        //             visitors
        //             LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
        //             LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
        //         WHERE
        //             (visitors.remark IS NULL 
        //             ".$confirmerin.")
        //             OR
        //             (visitors.remark IS NULL 
        //             and employee_syncs.employee_id = '".$manager."')
        //             OR
        //             (visitors.remark IS NULL 
        //             and employee_syncs.employee_id = 'PI0109004')
        //         ORDER BY
        //             id DESC");
        // }else if(preg_match('/Human Resources Department/i', $confirmerin)){
        //     $visitor = DB::SELECT("SELECT
        //             visitors.id,
        //             name,
        //             department,
        //             company,
        //             DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
        //             visitors.created_at,
        //             visitor_details.full_name,
        //             visitors.jumlah AS total1,
        //             purpose,
        //             visitors.status,
        //             visitor_details.in_time,
        //             visitor_details.out_time,
        //             visitors.remark 
        //         FROM
        //             visitors
        //             LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
        //             LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
        //         WHERE
        //             (visitors.remark IS NULL 
        //             ".$confirmerin.")
        //             OR
        //             (visitors.remark IS NULL 
        //             and employee_syncs.employee_id = '".$manager."')
        //             OR
        //             (visitors.remark IS NULL 
        //             and employee_syncs.employee_id = 'PI9709001')
        //         ORDER BY
        //             id DESC");
        // }else{
        //     $visitor = DB::SELECT("SELECT
        //             visitors.id,
        //             name,
        //             department,
        //             company,
        //             DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
        //             visitors.created_at,
        //             visitor_details.full_name,
        //             visitors.jumlah AS total1,
        //             purpose,
        //             visitors.status,
        //             visitor_details.in_time,
        //             visitor_details.out_time,
        //             visitors.remark 
        //         FROM
        //             visitors
        //             LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
        //             LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id 
        //         WHERE
        //             (visitors.remark IS NULL 
        //             ".$confirmerin.")
        //             OR
        //             (visitors.remark IS NULL 
        //             and employee_syncs.employee_id = '".$manager."')
        //         ORDER BY
        //             id DESC");
        // }
            // foreach ($visitor as $key) {
            //     array_push($visitor_department, $key->department);
            // }

        foreach ($dept as $depts) {
            if ($depts->department_name == 'Management Information System Department') {
                $visitor = DB::SELECT("SELECT
                     visitors.id,
                     name,
                     department,
                     company,
                     DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                     visitors.created_at,
                     visitor_details.full_name,
                     visitors.jumlah AS total1,
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
                     (visitors.remark IS NULL 
                     and employee_syncs.department = '".$depts->department_name."')
                     OR
                     (visitors.remark IS NULL 
                     and employee_syncs.employee_id = 'PI0109004')
                 ORDER BY
                     id DESC");
            }else if($depts->department_name == 'Human Resources Department'){
                $visitor = DB::SELECT("SELECT
                     visitors.id,
                     name,
                     department,
                     company,
                     DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                     visitors.created_at,
                     visitor_details.full_name,
                     visitors.jumlah AS total1,
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
                     (visitors.remark IS NULL 
                     and employee_syncs.department = '".$depts->department_name."')
                     OR
                     (visitors.remark IS NULL 
                     and employee_syncs.employee_id = 'PI9709001')
                 ORDER BY
                     id DESC");
            }else{
                $visitor = DB::SELECT("SELECT
                     visitors.id,
                     name,
                     department,
                     company,
                     DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
                     visitors.created_at,
                     visitor_details.full_name,
                     visitors.jumlah AS total1,
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
                     (visitors.remark IS NULL 
                     and employee_syncs.department = '".$depts->department_name."')
                 ORDER BY
                     id DESC");
            }
            if (count($visitor) > 0) {
                $visitor_departments[] = [ 'department' => $depts->department_name,
                    'jumlah_visitor' => count($visitor)
                ];
            }
        }

        $depts = '';
        for ($i=0; $i < count($visitor_departments); $i++) {
          if ($visitor_departments[$i]['jumlah_visitor'] != 0) {
              $depts = $depts."'".$visitor_departments[$i]['department']."'";
              if($i != (count($visitor_departments)-1)){
                $depts = $depts.',';
              }
          }
        }
        // $depts = substr($depts, 0, -1);

        $dept2 = " WHERE department in (".$depts.") ";

        $mail_to = DB::SELECT("select email from visitor_confirmers ".$dept2);
        $contactList = [];
        $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
        $contactList[1] = 'aditya.agassi@music.yamaha.com';
        if(count($visitor_departments) > 0){
            Mail::to($mail_to)->bcc($contactList,'Contact List')->send(new SendEmail($visitor_departments, 'visitor_confirmation'));
        }
    }
}
