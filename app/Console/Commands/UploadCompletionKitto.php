<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use FTP;
use App\ErrorLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use File;

class UploadCompletionKitto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:completionkitto';

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
        $kittofilename = 'ympi_upload_kitto_' . date('ymdhis') . '.txt';
        $kittofilepath = public_path() . "/uploads/sap/completions/" . $kittofilename;
        $kittofiledestination = "ma/ympi/prodordconf/" . $kittofilename;

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

        $not_uploads = db::connection('mysql2')->table('histories')
        ->leftJoin('materials', 'materials.id', '=', 'histories.completion_material_id')
        ->whereIn('histories.category', array('completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete'))
        ->where('histories.synced', '=', 0)
        ->whereNull('histories.deleted_at')
        ->select('materials.material_number', db::raw('sum(lot) as lot_minus'))
        ->groupBy('materials.material_number')
        ->having('lot_minus', '<', 0)
        ->get();

        $not_upload_materials = array();

        foreach($not_uploads as $not_upload){
            array_push($not_upload_materials, $not_upload->material_number);
        }

        $completions = db::connection('mysql2')->table('histories')
        ->leftJoin('materials', 'materials.id', '=', 'histories.completion_material_id')        
        ->whereIn('histories.category', array('completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete'))
        ->where('histories.synced', '=', 0)
        ->whereNull('histories.deleted_at')
        ->whereNotIn('materials.material_number', $not_upload_materials)
        ->update([
            'histories.reference_file' => $kittofilename,
            'histories.synced' => 1
        ]);

        $upload_completions = db::connection('mysql2')->table('histories')
        ->leftJoin('materials', 'materials.id', '=', 'histories.completion_material_id')
        ->whereIn('histories.category', array('completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete'))
        ->whereNull('histories.deleted_at')
        ->where('histories.reference_file', '=', $kittofilename)->select(
            'histories.completion_location',
            'materials.material_number',
            DB::raw('SUM(histories.lot) as lot')
        )
        ->groupBy('histories.completion_location', 'materials.material_number')
        ->get();

        $upload_text = "";
        if(!$upload_completions || count($upload_completions) <= 0){ 
            $error_log = new ErrorLog([
                'error_message' => 'UploadCompletionKitto (No Data)',
                'created_by' => '1'
            ]);
            $error_log->save();
            $title = "Kitto Upload Completion Tidak Ada Data";
            $body = "Count Upload = ".count($upload_completions)."\n\nKeterangan: Tidak ada data yang diupload";
            self::mailReport($title, $body, $mail_to);
            exit;
        }

        $row_count = 0;
        foreach ($upload_completions as $upload_completion){
            if($upload_completion->lot > 0){
                $row_count++;
                $upload_text .= self::writeString('8190', 4, " ");
                $upload_text .= self::writeString($upload_completion->completion_location, 4, " ");
                $upload_text .= self::writeString($upload_completion->material_number, 18, " ");
                $upload_text .= self::writeDecimal($upload_completion->lot, 14, "0");
                $upload_text .= self::writeDate(date('Y-m-d'), "completion");
                $upload_text .= self::writeString('', 16, " ");
                $upload_text .= "\r\n";                
            }
        }

        try{
            File::put($kittofilepath, $upload_text);
            $success = self::uploadFTP($kittofilepath, $kittofiledestination);

            $title = "Kitto Upload Completion Berhasil";
            $body = "Count Upload = ".count($row_count)."\n\nKeterangan: Upload Berhasil";
            self::mailReport($title, $body, $mail_to);
            exit;
        }
        catch(\Exception $e){
            $transfers = db::connection('mysql2')->table('histories')
            ->whereIn('histories.category', array('completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete'))
            ->whereNull('histories.deleted_at')
            ->where('histories.reference_file', '=', $kittofilename)
            ->update([
                'histories.synced' => 0
            ]);

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => '1'
            ]);
            $error_log->save();

            $title = "Kitto Upload Completion Gagal";
            $body = "Count Upload = ".count($upload_completions)."\n\nKeterangan: Upload Gagal (".$e->getMessage().")";
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
