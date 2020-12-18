<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class EmailUserDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:user_document';

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
    public function handle(){
        $mail_to = db::select("select u.email from user_documents d 
            left join users u on d.employee_id = u.username
            where (d.`condition` = 'At Risk' or  d.`condition` = 'Expired')
            and `status` = 'Active'");

        $safe = db::select("UPDATE user_documents
            SET `condition` = 'Safe'
            WHERE DATEDIFF(valid_to, NOW()) > reminder");

        $at_risk = db::select("UPDATE user_documents
            SET `condition` = 'At Risk'
            WHERE DATEDIFF(valid_to, NOW()) < reminder");

        $expired = db::select("UPDATE user_documents
            SET `condition` = 'Expired'
            WHERE now() > valid_to");

        $user_documents = db::select("select d.category, d.document_number, d.employee_id, u.`name`, d.valid_from, d.valid_to, d.`condition`, DATEDIFF(valid_to, NOW()) as diff from user_documents d
            left join users u on d.employee_id = u.username
            where (d.`condition` = 'At Risk' or  d.`condition` = 'Expired')
            and `status` = 'Active'
            order by diff asc");

        $data = [
            'user_documents' => $user_documents,
            'jml' => count($user_documents)
        ];

        if(count($user_documents) > 0){
            Mail::to(['eko.junaedi@music.yamaha.com', 'harjati.handajani@music.yamaha.com'])
            ->cc(['budhi.apriyanto@music.yamaha.com'])
            ->bcc(['aditya.agassi@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com'])
            ->send(new SendEmail($data, 'user_document'));
        }
    }
}
