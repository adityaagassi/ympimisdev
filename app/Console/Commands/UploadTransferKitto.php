<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use FTP;
use App\ErrorLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use File;

class UploadTransferKitto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:transferkitto';

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
        $kittofilename = 'ympigm_upload_kitto_' . date('ymdHis') . '.txt';
        $kittofilepath = public_path() . "/uploads/sap/transfers/" . $kittofilename;
        $kittofiledestination = "ma/ympigm/" . $kittofilename;

        $title = "";
        $body = "";
        $mail_to = [
            'aditya.agassi@music.yamaha.com', 
            'muhammad.ikhlas@music.yamaha.com',
            'mei.rahayu@music.yamaha.com',
            'istiqomah@music.yamaha.com',
            'silvy.firliani@music.yamaha.com',
            'fathor.rahman@music.yamaha.com'
        ];

        $transfers = db::connection('mysql2')->table('histories')
        ->whereIn('histories.category', array('transfer', 'transfer_adjustment', 'transfer_adjustment_excel', 'transfer_adjustment_manual', 'transfer_cancel', 'transfer_error', 'transfer_return', 'transfer_repair', 'transfer_after_repair'))
        ->where('histories.synced', '=', 0)
        ->whereNull('histories.deleted_at')
        ->update([
            'histories.reference_file' => $kittofilename,
            'histories.synced' => 1
        ]);

        $upload_transfers = db::connection('mysql2')->table('histories')
        ->leftJoin('materials', 'materials.id', '=', 'histories.transfer_material_id')
        ->whereIn('histories.category', array('transfer', 'transfer_adjustment', 'transfer_adjustment_excel', 'transfer_adjustment_manual', 'transfer_cancel', 'transfer_error', 'transfer_return', 'transfer_repair', 'transfer_after_repair'))
        ->where('histories.reference_file', '=', $kittofilename)
        ->whereNull('histories.deleted_at')
        ->select(
            'histories.transfer_barcode_number', 
            'histories.transfer_document_number', 
            'histories.transfer_issue_location', 
            'histories.transfer_issue_plant', 
            'histories.transfer_receive_location', 
            'histories.transfer_receive_plant', 
            'histories.transfer_material_id',
            'histories.transfer_cost_center', 
            'histories.transfer_gl_account',
            'histories.transfer_transaction_code',
            'histories.transfer_movement_type',
            'histories.transfer_reason_code',
            'materials.material_number',
            'histories.lot', 
            'histories.synced', 
            db::raw('date(histories.created_at) as date')
        )
        ->get();

        $upload_text = "";
        if(!$upload_transfers || count($upload_transfers) <= 0){ 
            $error_log = new ErrorLog([
                'error_message' => 'UploadTransferKitto (No Data)',
                'created_by' => '1'
            ]);
            $error_log->save();
            $title = "Kitto Upload Transfer Tidak Ada Data";
            $body = "Count Upload = ".count($upload_transfers)."\n\nKeterangan: Tidak ada data yang diupload";
            self::mailReport($title, $body, $mail_to);
            exit;
        }

        foreach ($upload_transfers as $upload_transfer){
            $upload_text .= self::writeString("8190", 15, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_issue_plant, 4, " ");
            $upload_text .= self::writeString($upload_transfer->material_number, 18, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_issue_location, 4, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_receive_plant, 4, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_receive_location, 4, " ");
            $upload_text .= self::writeDecimal($upload_transfer->lot, 13, "0");
            $upload_text .= self::writeString($upload_transfer->transfer_cost_center, 10, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_gl_account, 10, " ");
            $upload_text .= self::writeDate($upload_transfer->date, "transfer");
            $upload_text .= self::writeString($upload_transfer->transfer_transaction_code, 20, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_movement_type, 3, " ");
            $upload_text .= self::writeString($upload_transfer->transfer_reason_code, 4, " ");
            $upload_text .= "\r\n";
        }

        try{
            File::put($kittofilepath, $upload_text);
            $success = self::uploadFTP($kittofilepath, $kittofiledestination);

            $title = "Kitto Upload Transfer Berhasil";
            $body = "Count Upload = ".count($upload_transfers)."\n\nKeterangan: Upload Berhasil";
            self::mailReport($title, $body, $mail_to);
            exit;
        }
        catch(\Exception $e){
            $transfers = db::connection('mysql2')->table('histories')
            ->whereIn('histories.category', array('transfer', 'transfer_adjustment', 'transfer_adjustment_excel', 'transfer_adjustment_manual', 'transfer_cancel', 'transfer_error', 'transfer_return', 'transfer_repair', 'transfer_after_repair'))
            ->where('histories.reference_file', '=', $kittofilename)
            ->whereNull('histories.deleted_at')
            ->update([
                'histories.synced' => 0
            ]);

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => '1'
            ]);
            $error_log->save();

            $title = "Kitto Upload Transfer Gagal";
            $body = "Count Upload = ".count($upload_transfers)."\n\nKeterangan: Upload Gagal (".$e->getMessage().")";
            self::mailReport($title, $body, $mail_to);
            exit;
        }

    }

    function mailReport($title, $body, $mail_to){
        Mail::raw([], function($message) use($title, $body, $mail_to){
          $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
          $message->to($mail_to);
          $message->subject($title);
          $message->setBody($body, 'text/plain');}
      ); 
    }

    function uploadFTP($from, $to) {
        $upload = FTP::connection()->uploadFile($from, $to, FTP_BINARY);
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
