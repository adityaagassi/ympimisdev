<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\DB;

class HighestCovidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'highest:survey_covid';

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
        $datas = DB::CONNECTION('mobile')->SELECT("SELECT
                tanggal,
                survey_logs.employee_id,
                survey_logs.`name`,
                COALESCE ( ympimis.employee_syncs.department, '' ) AS department,
                COALESCE ( ympimis.employee_syncs.section, '' ) AS section,
                COALESCE ( ympimis.employee_syncs.`group`, '' ) AS `group`,
                COALESCE ( ympimis.employee_syncs.sub_group, '' ) AS sub_group,
                total AS nilai 
            FROM
                survey_logs
                LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = survey_logs.employee_id 
            WHERE
                total > 80 
            ORDER BY
            total DESC");

        $bcc = [];
        $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
        $bcc[1] = 'rio.irvansyah@music.yamaha.com';
        $bcc[2] = 'aditya.agassi@music.yamaha.com';

        // $cc = [];
        // $cc[0] = 'prawoto@music.yamaha.com';

        $mail_to = [];
        $mail_to[0] = 'dicky.kurniawan@music.yamaha.com';

        if (count($datas) > 0) {
            Mail::to($mail_to)->bcc($bcc,'BCC')->send(new SendEmail($datas, 'highest_covid'));
        }
    }
}
