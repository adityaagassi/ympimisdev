<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class EmailHrq extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:hrq';

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
        $qry = "select category, SUM(IF(remark = 1,1,0)) as unanswer from hr_question_logs group by category having unanswer > 0";

        $data = db::select($qry);


        if($data != null){
            Mail::to(['m.hamzah@music.yamaha.com', 'prawoto@music.yamaha.com', 'putri.sukma.riyanti@music.yamaha.com', 'budhi.apriyanto@music.yamaha.com', 'aditya.agassi@music.yamaha.com'])->send(new SendEmail($data, 'hrq'));
        }
    }
}
