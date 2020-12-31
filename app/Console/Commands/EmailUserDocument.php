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
        $user_reminder = db::select("SELECT DISTINCT d.employee_id, u.email FROM user_documents d 
            LEFT JOIN users u ON d.employee_id = u.username
            WHERE (d.`condition` = 'At Risk' OR  d.`condition` = 'Expired')
            AND d.`status` = 'Active'
            AND u.email like '%music.yamaha.com%'");

        $safe = db::select("UPDATE user_documents
            SET `condition` = 'Safe'
            WHERE DATEDIFF(valid_to, NOW()) > reminder");

        $at_risk = db::select("UPDATE user_documents
            SET `condition` = 'At Risk'
            WHERE DATEDIFF(valid_to, NOW()) < reminder");

        $expired = db::select("UPDATE user_documents
            SET `condition` = 'Expired'
            WHERE now() > valid_to");


        for ($x=0; $x < count($user_reminder) ; $x++) {
            $user_documents = db::select("SELECT d.category,
                d.document_number,
                d.employee_id,
                u.`name`,
                d.valid_from,
                d.valid_to,
                d.`condition`,
                DATEDIFF(valid_to, NOW()) as diff
                FROM user_documents d
                LEFT JOIN users u ON d.employee_id = u.username
                WHERE (d.`condition` = 'At Risk' OR  d.`condition` = 'Expired')
                AND d.`status` = 'Active'
                AND d.employee_id = '".$user_reminder[$x]->employee_id."'
                ORDER BY diff ASC");

            $data = [
                'user_documents' => $user_documents,
                'jml' => count($user_documents),
                'type' => 'user'
            ];

            if(count($user_documents) > 0){
                Mail::to([$user_reminder[$x]->email])
                ->bcc(['aditya.agassi@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com', 'agus.yulianto@music.yamaha.com'])
                ->send(new SendEmail($data, 'user_document'));
            }
            
        }



        $resume = db::select("SELECT d.category,
            d.document_number,
            d.employee_id,
            u.`name`,
            d.valid_from,
            d.valid_to,
            d.`condition`,
            DATEDIFF(valid_to, NOW()) as diff
            FROM user_documents d
            LEFT JOIN users u ON d.employee_id = u.username
            WHERE (d.`condition` = 'At Risk' OR  d.`condition` = 'Expired')
            AND d.`status` = 'Active'
            ORDER BY diff ASC");

        $data = [
            'user_documents' => $resume,
            'jml' => count($resume),
            'type' => 'resume'
        ];

        if(count($user_documents) > 0){
            Mail::to(['eko.junaedi@music.yamaha.com', 'harjati.handajani@music.yamaha.com'])
            ->cc(['budhi.apriyanto@music.yamaha.com'])
            ->bcc(['aditya.agassi@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com', 'agus.yulianto@music.yamaha.com'])
            ->send(new SendEmail($data, 'user_document'));
        }
    }
}
