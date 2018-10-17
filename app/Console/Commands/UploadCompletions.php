<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Status;
use App\FloDetail;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Auth;
use Response;
use FTP;

class UploadCompletions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:completions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload completion to SAP';

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
        $flofilename = 'ympi_upload_flo_' . date('ymdhis') . '.txt';
        $flofilepath = public_path() . "/uploads/sap/completions/" . $flofilename;
        $flofiledestination = "ma/ympi/prodordconf/" . $flofilename;

        $flo_details = FloDetail::whereNull('flo_details.completion')
        ->where('flo_details.created_at', '<=', $date);

        $flo_completions = DB::table('flo_details')
        ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        ->whereNull('flo_details.completion')
        ->where('flo_details.created_at', '<=', $date)
        ->select(
            'materials.issue_storage_location', 
            'flo_details.material_number',
            DB::raw('date(flo_details.created_at) as date'),
            DB::raw('sum(flo_details.quantity) as qty')
        )
        ->groupBy(
            'materials.issue_storage_location', 
            DB::raw('date(flo_details.created_at)'),
            'flo_details.material_number'
        )
        ->having(DB::raw('sum(flo_details.quantity)'), '>', 0)
        ->get();

        $flo_text = "";
        if(count($flo_completions) > 0){
            foreach ($flo_completions as $flo_completion) {
                $flo_text .= self::writeString('8190', 4, " "); //plant ympi
                $flo_text .= self::writeString($flo_completion->issue_storage_location, 4, " "); //sloc
                $flo_text .= self::writeString($flo_completion->material_number, 18, " "); //gmc
                $flo_text .= self::writeDecimal($flo_completion->qty, 14, "0"); //qty
                $flo_text .= self::writeDate($flo_completion->date, "completion"); //date
                $flo_text .= self::writeString('', 16, " "); //reference number
                $flo_text .= "\r\n";
            }
            File::put($flofilepath, $flo_text);

            // $success = self::uploadFTP($flofilepath, $flofiledestination);

            // if($success){
            //     $flo_details->update(['completion' => $flofilename]);
            // }
            // else{
            //     echo 'false';
            // }
        }
        else{
            echo 'false';
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