<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\WeeklyCalendar;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class EmailBento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:bento';

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

        $first = date('Y-m-01');
        $last = date('Y-m-t');
        $bento_lists = db::select("SELECT
            j.employee_id,
            j.employee_name,
            u.email,
            b.due_date,
            b.revise,
            b.status 
            FROM
            japaneses AS j
            LEFT JOIN ( SELECT * FROM bentos WHERE due_date >= '".$first."' AND due_date <= '".$last."' ) AS b ON b.employee_id = j.employee_id
            LEFT JOIN users AS u ON u.username = j.employee_id");

        $calendars = WeeklyCalendar::where('week_date', '>=', $first)
        ->where('week_date', '<=', $last)
        ->get();

        $mail_to = array();
        foreach($bento_lists as $bento_list){
            if(!in_array($bento_list->email, $mail_to)){
                array_push($mail_to, $bento_list->email);
            }
        }

        $bentos = [
            'bento_lists' => $bento_lists,
            'calendars' => $calendars
        ];

        Mail::to($mail_to)
        ->cc([
            'rianita.widiastuti@music.yamaha.com', 
            'putri.sukma.riyanti@music.yamaha.com', 
            'prawoto@music.yamaha.com',
            'budhi.apriyanto@music.yamaha.com', 
            'helmi.helmi@music.yamaha.com',
            'merlinda.dyah@music.yamaha.com', 
            'novita.siswindarti@music.yamaha.com'
        ])
        ->bcc([
            'aditya.agassi@music.yamaha.com', 
            'anton.budi.santoso@music.yamaha.com',
            'agus.yulianto@music.yamaha.com'
        ])
        ->send(new SendEmail($bentos, 'bento_approve'));
    }
}
