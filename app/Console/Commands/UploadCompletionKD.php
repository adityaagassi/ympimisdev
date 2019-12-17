<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use FTP;
use App\ErrorLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\TransactionCompletion;
use File;

class UploadCompletionKD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:completionKD';

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
        $kdofilename = 'ympi_upload_kdo_' . date('ymdhis') . '.txt';
        $kdofilepath = public_path() . "/uploads/sap/completions/" . $kdofilename;
        $kdofiledestination = "ma/ympi/prodordconf/" . $kdofilename;

        $completions = TransactionCompletion::whereNull('reference_file')
        ->where('movement_type', '=', '101')
        ->update(['reference_file' => $kdofilename]);

        $upload_completions = TransactionCompletion::where('reference_file', '=', $kdofilename)
        ->select('material_number', 'issue_location', db::raw('sum(quantity) as quantity'), db::raw('date(created_at) as date'))
        ->groupBy('material_number', 'issue_location', db::raw('date(created_at)'))
        ->get();

        $upload_text = "";
        if(!$upload_completions){
            echo 'false';
            exit;
        }

        foreach ($upload_completions as $upload_completion){
            $upload_text .= self::writeString('8190', 4, " ");
            $upload_text .= self::writeString($upload_completion->issue_location, 4, " ");
            $upload_text .= self::writeString($upload_completion->material_number, 18, " ");
            $upload_text .= self::writeDecimal($upload_completion->quantity, 14, "0");
            $upload_text .= self::writeDate($upload_completion->date, "completion");
            $upload_text .= self::writeString('', 16, " ");
            $upload_text .= "\r\n";
        }

        try{
            File::put($kdofilepath, $upload_text);
            $success = self::uploadFTP($kdofilepath, $kdofiledestination);
        }
        catch(\Exception $e){
            $transaction_error = TransactionCompletion::where('reference_file', '=', $kdofilename)
            ->update(['reference_file', '=', $kdofilename]);

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => '1'
            ]);
            $error_log->save();
            echo 'false';
            exit;
        }
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
