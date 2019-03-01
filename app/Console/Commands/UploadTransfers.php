<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Status;
use App\FloDetail;
use App\LogTransaction;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Auth;
use Response;
use FTP;

class UploadTransfers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:transfers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload transfer to SAP';

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
        $flofilename = 'ympigm_upload_flo_' . date('ymdhis') . '.txt';
        $flofilepath = public_path() . "/uploads/sap/transfers/" . $flofilename;
        $flofiledestination = "ma/ympigm/" . $flofilename;

        $flo_details = DB::table('flo_details')
        ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
        ->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flo_details.flo_number')
        ->where('flo_logs.status_code', '=', '2')
        ->whereIn('flos.status', ['2','3','4'])
        ->whereNull('flo_details.transfer')
        ->where('flo_logs.created_at', '<=', $date);

        $flo_transfers = DB::table('flo_details')
        ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
        ->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flo_details.flo_number')
        ->where('flo_logs.status_code', '=', '2')
        ->whereIn('flos.status', ['2','3','4'])
        ->whereNull('flo_details.transfer')
        ->where('flo_logs.created_at', '<=', $date)
        ->select(
            'materials.issue_storage_location', 
            'flo_details.material_number',
            'flo_details.flo_number',
            DB::raw('date(flo_logs.updated_at) as date'),
            DB::raw('sum(flo_details.quantity) as qty')
        )
        ->groupBy('materials.issue_storage_location', 'flo_details.material_number', 
            'flo_details.flo_number', DB::raw('date(flo_logs.updated_at)'))
        ->having(DB::raw('sum(flo_details.quantity)'), '>', 0)
        ->get();

        $flo_text = "";
        if(count($flo_transfers) > 0){
            foreach ($flo_transfers as $flo_transfer) {
                $flo_text .= self::writeString('8190', 15, " "); //plant ympi
                $flo_text .= self::writeString('8190', 4, " "); //plant issue
                $flo_text .= self::writeString($flo_transfer->material_number, 18, " "); //gmc
                $flo_text .= self::writeString($flo_transfer->issue_storage_location, 4, " "); //sloc issue
                $flo_text .= self::writeString('8191', 4, " "); //plant receive
                $flo_text .= self::writeString('FSTK', 4, " "); // sloc receive
                $flo_text .= self::writeDecimal($flo_transfer->qty, 13, "0"); //qty
                $flo_text .= self::writeString('', 10, " "); //cost center
                $flo_text .= self::writeString('', 10, " "); //gl account
                $flo_text .= self::writeDate($flo_transfer->date, "transfer"); //date
                $flo_text .= self::writeString('MB1B', 20, " "); //transaction code
                $flo_text .= self::writeString('9P1', 3, " "); //mvt
                $flo_text .= self::writeString('', 4, " "); //reason code
                $flo_text .= "\r\n";
            }
            File::put($flofilepath, $flo_text);

            $success = self::uploadFTP($flofilepath, $flofiledestination);

            if($success){
                $flo_details->update(['transfer' => $flofilename]);
                foreach ($flo_transfers as $flo_transfer) {
                    $log_transaction = new LogTransaction([
                        'material_number' => $flo_transfer->material_number,
                        'issue_plant' => '8190',
                        'issue_storage_location' => $flo_transfer->issue_storage_location,
                        'receive_plant' => '8191',
                        'receive_storage_location' => 'FSTK',
                        'transaction_code' => 'MB1B',
                        'mvt' => '9P1',
                        'transaction_date' => $flo_transfer->date,
                        'qty' => $flo_transfer->qty,
                        'created_by' => 1
                    ]);
                    $log_transaction->save();
                }
            }
            else{
                echo 'false';
            }
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
