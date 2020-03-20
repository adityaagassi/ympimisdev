<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\EmployeeSync;
use DateTime;

class SendMachineNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:machine';

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
        $this->all = [
            [85,'WCut#1', 'Parts Process (WI-PP)'],
            [84,'WCut#2', 'Parts Process (WI-PP)'],
            [98,'WCut#3', 'Parts Process (WI-PP)'],
            [89,'Shinogi', 'Parts Process (WI-PP)'],
            [94,'MC1st#1', 'Parts Process (WI-PP)'],
            [86,'MC1st#2', 'Parts Process (WI-PP)'],
            [95,'MC2nd#1', 'Parts Process (WI-PP)'],
            [1,'MC 1st#6', 'Parts Process (WI-PP)'],
            [2,'MC 1st#4', 'Parts Process (WI-PP)'],
            [3,'MC 1st#3', 'Parts Process (WI-PP)'],
            [4,'MC 1st#5', 'Parts Process (WI-PP)'],
            [5,'MC 1st#1', 'Parts Process (WI-PP)'],
            [6,'MC 1st#2', 'Parts Process (WI-PP)'],
            [7,'MC 1st#7', 'Parts Process (WI-PP)'],
            [8,'MC 1st#9', 'Parts Process (WI-PP)'],
            [9,'MC 1st#8', 'Parts Process (WI-PP)'],
            [10,'MC 1st#10', 'Parts Process (WI-PP)'],
            [11,'MC 1st#11', 'Parts Process (WI-PP)'],
            [12,'MC 1st#12', 'Parts Process (WI-PP)'],
            [13,'MC 2nd#1', 'Parts Process (WI-PP)'],
            [14,'MC 2nd#2', 'Parts Process (WI-PP)'],
            [15,'MC 2nd#3', 'Parts Process (WI-PP)'],
            [16,'MC 2nd#4', 'Parts Process (WI-PP)'],
            [17,'MC 2nd#5', 'Parts Process (WI-PP)'],
            [18,'MC 2nd#6', 'Parts Process (WI-PP)'],
            [19,'MC 2nd#7', 'Parts Process (WI-PP)'],
            [20,'MC 2nd#8', 'Parts Process (WI-PP)'],
            [21,'MC 2nd#9', 'Parts Process (WI-PP)'],
            [22,'MC 2nd#10', 'Parts Process (WI-PP)'],
            [100, 'K Mkp', 'Parts Process (WI-PP)'],
            [99, 'K Nkp', 'Parts Process (WI-PP)'],
            [63, 'K Nuki', 'Parts Process (WI-PP)'],
            [75, 'Kom#1', 'Parts Process (WI-PP)'],
            [76, 'Kom#2', 'Parts Process (WI-PP)'],
            [77, 'Kom#3', 'Parts Process (WI-PP)'],
            [78, 'Kom#4', 'Parts Process (WI-PP)'],
            [79, 'Kom#5', 'Parts Process (WI-PP)'],
            [81, 'Amd PC', 'Parts Process (WI-PP)'],
            [80, 'Amd#1', 'Parts Process (WI-PP)'],
            [69, 'Amd#2', 'Parts Process (WI-PP)'],
            [70, 'Amd#3', 'Parts Process (WI-PP)'],
            [71, 'Amd#4', 'Parts Process (WI-PP)'],
            [72, 'Amd#5', 'Parts Process (WI-PP)'],
            [73, 'Amd#6', 'Parts Process (WI-PP)'],
            [74, 'Amd#7', 'Parts Process (WI-PP)'],
            [50,'LT#1', 'Parts Process (WI-PP)'],
            [29,'LT#2', 'Parts Process (WI-PP)'],
            [30,'LT#3', 'Parts Process (WI-PP)'],
            [31,'LT#4', 'Parts Process (WI-PP)'],
            [32,'LT#5', 'Parts Process (WI-PP)'],
            [51,'LT#6', 'Parts Process (WI-PP)'],
            [53,'LT#7', 'Parts Process (WI-PP)'],
            [54,'LT#8', 'Parts Process (WI-PP)'],
            [55,'LT#9', 'Parts Process (WI-PP)'],
            [56,'LT#10', 'Parts Process (WI-PP)'],
            [52,'LT#11', 'Parts Process (WI-PP)'],
            [48,'LT#12', 'Parts Process (WI-PP)'],
            [49,'LT#13', 'Parts Process (WI-PP)'],
            [45,'LT#14', 'Parts Process (WI-PP)'],
            [46,'LT#15', 'Parts Process (WI-PP)'],
            [43,'LT#16', 'Parts Process (WI-PP)'],
            [44,'LT#17', 'Parts Process (WI-PP)'],
            [33,'LT#18', 'Parts Process (WI-PP)'],
            [34,'LT#19', 'Parts Process (WI-PP)'],
            [35,'LT#20', 'Parts Process (WI-PP)'],
            [36,'LT#21', 'Parts Process (WI-PP)'],
            [37,'LT#22', 'Parts Process (WI-PP)'],
            [38,'LT#23', 'Parts Process (WI-PP)'],
            [39,'LT#24', 'Parts Process (WI-PP)'],
            [40,'LT#25', 'Parts Process (WI-PP)'],
            [41,'LT#26', 'Parts Process (WI-PP)'],
            [42,'LT#27', 'Parts Process (WI-PP)'],
            [26,'LT#28', 'Parts Process (WI-PP)'],
            [25,'LT#29', 'Parts Process (WI-PP)'],
            [24,'LT#30', 'Parts Process (WI-PP)'],
            [23,'LT#31', 'Parts Process (WI-PP)'],
            [47,'Inj#1', 'Educational Instrument (EI)'],
            [82,'Inj#2', 'Educational Instrument (EI)'],
            [57,'Inj#3', 'Educational Instrument (EI)'],
            [58,'Inj#4', 'Educational Instrument (EI)'],
            [59,'Inj#5', 'Educational Instrument (EI)'],
            [60,'Inj#6', 'Educational Instrument (EI)'],
            [61,'Inj#7', 'Educational Instrument (EI)'],
            [83,'Inj#8', 'Educational Instrument (EI)'],
            [62,'Inj#9', 'Educational Instrument (EI)'],
            [91,'Inj#10', 'Educational Instrument (EI)'],
            [64,'Inj#11', 'Educational Instrument (EI)'],
            [92,'Inj#12', 'Educational Instrument (EI)'],
            [67,'Inj#13', 'Educational Instrument (EI)'],
            [68,'Inj#14', 'Educational Instrument (EI)'],
            [65,'Inj#15', 'Educational Instrument (EI)'],
            [93,'Inj#16', 'Educational Instrument (EI)'],
            [96,'Inj#17', 'Educational Instrument (EI)'],
            [66,'Inj#18', 'Educational Instrument (EI)'],
            [97,'Inj#19', 'Educational Instrument (EI)']
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mail_tos = [];
        $sms_tos = [];

        // print_r($get_mail);

        // print_r($sms);
        // exit;

        // while (true) {
            $temp_all = [];
            $json = file_get_contents('http://172.17.128.204/zed/dashboard/getData');
            $temp = explode("(ime)", $json);
            unset($temp[count($temp) -1]);

            $json_z = file_get_contents('http://172.17.128.204/zed/dashboard/getDataSystem');
            $tempz = explode("(ime)", $json_z);
            unset($tempz[count($tempz) -1]);

            foreach ($temp as $mc) {
                array_push($temp_all, [explode("#", $mc)[0], explode("#", $mc)[1]]);
            }

            foreach ($tempz as $zpro) {
               array_push($temp_all, [explode("#", $zpro)[0], explode("#", $zpro)[1]]);   
           }

           foreach ($temp_all as $key) {
            foreach ($this->all as $ctg) {
                if ($key[0] == $ctg[0]) {
                    $temp = db::table('maintenance_machine_logs')->where('machine_code','=', $key[0])->first();

                    if ($key[1] == '0') {

                        $get_mail = EmployeeSync::leftJoin('users' ,'employee_syncs.employee_id', '=', 'users.username')
                        ->whereIn('position', ['chief','foreman','manager'])
                        ->whereRaw("(department in ('Maintenance', '".$ctg[2]."'))")
                        ->where('division', '=', 'Production')
                        ->select('employee_id', 'email', 'department','phone', 'position')
                        ->get()
                        ->toArray();

                        foreach ($get_mail as $mail) {
                            if ($mail['position'] == 'Chief' || $mail['position'] == 'Foreman') {

                                $mailnumber = '62'.substr($mail['phone'], 1);

                                array_push($sms_tos, $mailnumber);
                            } else {
                                array_push($mail_tos, $mail['email']);
                            }
                        }

                        array_push($mail_tos, 'nadif@music.yamaha.com');
                        array_push($mail_tos, 'duta.narendratama@music.yamaha.com');

                        $sms = implode (",", $sms_tos);

                        if (!$temp) {
                            DB::table('maintenance_machine_logs')->insert(
                                [
                                    'machine_code' => $key[0],
                                    'machine_name' => $ctg[1],
                                    'status_cf' => "",
                                    'status_gm' => "",
                                    'started_at' => date('Y-m-d H:i:s')
                                ]
                            );
                        } else {
                            $datetime1 = new DateTime($temp->started_at);
                            $datetime2 = new DateTime();

                            $dateDiff  = $datetime1->diff($datetime2);

                            $machine = [
                                "machine" => $ctg[1]
                            ];

                            if ($dateDiff->i >= 15 && $temp->status_cf == "") {
                                DB::table('maintenance_machine_logs')
                                ->where('machine_code','=', $key[0])
                                ->update(['status_cf' => 'Notified']);

                                $query_string = "api.aspx?apiusername=API3Y9RTZ5R6Y&apipassword=API3Y9RTZ5R6Y3Y9RT";
                                $query_string .= "&senderid=".rawurlencode("PT YMPI")."&mobileno=".rawurlencode($sms);
                                $query_string .= "&message=".rawurlencode(stripslashes("Informasi Error Mesin :\nTelah terjadi error selama 15 menit untuk Mesin ".$ctg[1].".\n\nTerimakasih")) . "&languagetype=1";        
                                $url = "http://gateway.onewaysms.co.id:10002/".$query_string;    
                                $fd = @implode('', file($url));

                                $machine['time'] = "15 minutes";
                                print_r($sms);


                                Mail::to($mail_tos)->cc('aditya.agassi@music.yamaha.com')->send(new SendEmail($machine, 'machine'));
                            } else if ($dateDiff->h >= 1 && $temp->status_m == "") {
                                DB::table('maintenance_machine_logs')
                                ->where('machine_code','=', $key[0])
                                ->update(['status_m' => 'Notified']);

                                $machine['time'] = "an hour";

                                Mail::to(['budhi.apriyanto@music.yamaha.com','takashiohkubo@yamaha.com'])->cc('aditya.agassi@music.yamaha.com')->send(new SendEmail($machine, 'machine'));
                            } else if ($dateDiff->h >= 2 && $temp->status_gm == "") {
                                DB::table('maintenance_machine_logs')
                                ->where('machine_code','=', $key[0])
                                ->update(['status_gm' => 'Notified']);

                                $machine['time'] = "two hours";

                                Mail::to('yukitaka.hayakawa@music.yamaha.com')->cc('aditya.agassi@music.yamaha.com')->send(new SendEmail($machine, 'machine'));
                            } 
                        }
                    } else {
                        if ($temp) {
                            DB::table('maintenance_machine_logs')->where('machine_code', '=', $key[0])->delete();
                        }
                    }
                }
            }
        // }
        // sleep(2);
    }
}
}
