<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use FTP;
use App\ErrorLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\TransactionTransfer;
use File;

class UploadTransferKD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:transferKD';

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
        $date = date('Y-m-d H:i:s');
        $kdofilename = 'ympigm_upload_kdo_' . date('ymdhis') . '.txt';
        $kdofilepath = public_path() . "/uploads/sap/transfers/" . $kdofilename;
        $kdofiledestination = "ma/ympigm/" . $kdofilename;

        $transfers = TransactionTransfer::whereNull('reference_file')
        ->update(['reference_file' => $kdofilename]);

        $upload_transfers = TransactionTransfer::where('reference_file', '=', $kdofilename)
        ->select('plant', 'material_number', 'issue_plant', 'issue_location', 'receive_plant', 'receive_location', db::raw('sum(quantity) as quantity'), 'cost_center', 'gl_account', db::raw('date(created_at) as date', 'transcation_code', 'reason_code'))
        ->groupBy('plant', 'material_number', 'issue_plant', 'issue_location', 'receive_plant', 'receive_location', 'cost_center', 'gl_account', db::raw('date(created_at)'), 'transcation_code', 'reason_code')
        ->get();

        $upload_text = "";
        if(!$upload_transfers){
            echo 'false';
            exit;
        }

        foreach ($upload_transfers as $upload_transfer){
            $upload_text .= self::writeString($upload_transfer->plant, 15, " ");
            $upload_text .= self::writeString($upload_transfer->issue_plant, 4, " ");
            $upload_text .= self::writeString($upload_transfer->material_number, 18, " ");
            $upload_text .= self::writeString($upload_transfer->issue_location, 4, " ");
            $upload_text .= self::writeString($upload_transfer->receive_plant, 4, " ");
            $upload_text .= self::writeString($upload_transfer->receive_location, 4, " ");
            $upload_text .= self::writeDecimal($upload_transfer->quantity, 13, "0");
            $upload_text .= self::writeString($upload_transfer->cost_center, 10, " ");
            $upload_text .= self::writeString($upload_transfer->gl_account, 10, " ");
            $upload_text .= self::writeDate($upload_transfer->date, "transfer");
            $upload_text .= self::writeString($upload_transfer->transaction_code, 20, " ");
            $upload_text .= self::writeString($upload_transfer->movement_type, 3, " ");
            $upload_text .= self::writeString($upload_transfer->reason_code, 4, " ");
            $upload_text .= "\r\n";
        }

        try{
            File::put($kdofilepath, $upload_text);
            $success = self::uploadFTP($kdofilepath, $kdofiledestination);
        }
        catch(\Exception $e){
            $transaction_error = TransactionTransfer::where('reference_file', '=', $kdofilename)
            ->update(['reference_file', '=', $kdofilename]);

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => '1'
            ]);
            $error_log->save();
            echo 'false';
            exit;
        }

        function uploadFTP($from, $to) {
            $upload = FTP::connection()->uploadFile($from, $to);
            return $upload;
        }

        function writeString($text, $maxLength, $char) {
            if ($maxLength > 0) {
                $textLength = 0;
                if ($text != null) {
                    $textLength = strlen($text);
                }
                else {
                    $text = "";
                }
                for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                    $text .= $char;
                }
            }
            return strtoupper($text);
        }

        function writeDecimal($text, $maxLength, $char) {
            if ($maxLength > 0) {
                $textLength = 0;
                if ($text != null) {
                    if(fmod($text,1) > 0){
                        $decimal = self::decimal(fmod($text,1));
                        $decimalLength = strlen($decimal);

                        for ($j = 0; $j < (3- $decimalLength); $j++) {
                            $decimal = $decimal . $char;
                        }
                    }
                    else{
                        $decimal = $char . $char . $char;
                    }
                    $textLength = strlen(floor($text));
                    $text = floor($text);
                }
                else {
                    $text = "";
                }
                for ($i = 0; $i < (($maxLength - 4) - $textLength); $i++) {
                    $text = $char . $text;
                }
            }
            $text .= "." . $decimal;
            return $text;
        }

        function writeDate($created_at, $type) {
            $datetime = strtotime($created_at);
            if ($type == "completion") {
                $text = date("dmY", $datetime);
                return $text;
            }
            else {
                $text = date("Ymd", $datetime);
                return $text;
            }
        }

        function decimal($number){
            $num = explode('.', $number);
            return $num[1];
        }
    }
}
