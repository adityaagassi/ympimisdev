<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class EmailClinicVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:clinic_visit';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $now = date('Y-m-d');
        $past = date('Y-m-d', strtotime('-30 day'));
        $report_date;

        $calendar = db::select("SELECT * FROM weekly_calendars
            WHERE week_date < '".$now."'
            AND week_date > '".$past."'
            AND remark <> 'H'
            ORDER BY week_date DESC;");

        for ($i=0; $i < count($calendar); $i++) { 
            $report_date = $calendar[$i]->week_date;
            break;
        }

        $to = db::select("SELECT users.email FROM employee_syncs
            LEFT JOIN users ON users.username = employee_syncs.employee_id
            WHERE employee_syncs.division = 'Human Resources & General Affairs Division'
            AND employee_syncs.position IN ('Chief', 'Manager')");

        $cc = db::select("SELECT DISTINCT u.email FROM clinic_patient_details cl
            LEFT JOIN employee_syncs e ON e.employee_id = cl.employee_id
            LEFT JOIN users u ON u.username = e.nik_manager
            WHERE cl.purpose IN ('Pemeriksaan Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja', 'Pulang (Sakit)')
            AND date(visited_at) = '".$report_date."'
            AND e.division <> 'Human Resources & General Affairs Division'
            AND e.nik_manager IS NOT NULL");

        $bcc = array();
        array_push($bcc, 'anton.budi.santoso@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com');


        $resume = db::select("SELECT cl.employee_id, e.`name`, e.department, cl.purpose, cl.paramedic, GROUP_CONCAT(' ', cl.diagnose) AS diagnose, MAX(cl.visited_at) AS visited_at FROM clinic_patient_details cl
            LEFT JOIN employee_syncs e ON e.employee_id = cl.employee_id
            WHERE cl.purpose IN ('Pemeriksaan Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja', 'Pulang (Sakit)')
            AND date(visited_at) = '".$report_date."'
            GROUP BY cl.employee_id, e.`name`, e.department, cl.purpose, cl.paramedic
            ORDER BY visited_at ASC");


        $data = [
            'resume' => $resume,
            'date' => $report_date
        ];


        if(count($resume) > 0){
            Mail::to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->send(new SendEmail($data, 'clinic_visit'));
        }
        
    }
}
