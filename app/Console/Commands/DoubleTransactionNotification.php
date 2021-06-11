<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;

class DoubleTransactionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:double_transaction';

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

        $date = date('Y-m-d', strtotime('-1 days'));

        $resume = db::connection('mysql2')->select("SELECT resume.category, materials.material_number, resume.barcode, resume.description, resume.issue, resume.receive, resume.lot, resume.created_at, SUM(resume.qty) AS duplicates FROM
            (SELECT category,
            IF(category LIKE '%transfer%', transfer_material_id, completion_material_id) AS material_id,
            IF(category LIKE '%transfer%', transfer_barcode_number, completion_barcode_number) AS barcode,
            completion_description as description,
            IF(category LIKE '%transfer%', transfer_issue_location, completion_location) AS issue,
            IF(category LIKE '%transfer%', transfer_receive_location, '-') AS receive,
            lot,
            created_at,
            1 AS qty
            FROM `histories`
            WHERE date(created_at) = '".$date."'
            AND category IN ('completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'transfer', 'transfer_adjustment', 'transfer_adjustment_excel', 'transfer_adjustment_manual', 'transfer_cancel')
            ) AS resume
            LEFT JOIN materials ON materials.id = resume.material_id
            GROUP BY resume.category, materials.material_number, resume.barcode, resume.description, resume.issue, resume.receive, resume.lot, resume.created_at
            HAVING duplicates > 1");

        if(count($resume) > 0){
            // $to = [
            //     'yusli.erwandi@music.yamaha.com',
            //     'silvy.firliani@music.yamaha.com',
            //     'istiqomah@music.yamaha.com',
            //     'fathor.rahman@music.yamaha.com',
            //     'ade.laksmana.putra@music.yamaha.com'
            // ];

            // $bcc = [
            //     'aditya.agassi@music.yamaha.com', 
            //     'muhammad.ikhlas@music.yamaha.com'
            // ];

            $to = [
                'muhammad.ikhlas@music.yamaha.com'
            ];

            $data = [
                'resume' => $resume,
                'date_text' => date('l, d M Y')
            ];

            Mail::to($to)
            // ->bcc($bcc)
            ->send(new SendEmail($data, 'double_transaction_notification'));


        }


        
    }
}
