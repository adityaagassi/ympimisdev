<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CanteenLiveCooking;
use App\GeneralAttendance;
use App\Employee;
use Illuminate\Support\Facades\DB;

class LiveCookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:live_cooking';

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

        $live_cooking_attendance = DB::SELECT("SELECT
            *,
            canteen_live_cookings.id AS id_live 
        FROM
            `canteen_live_cookings`
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = canteen_live_cookings.order_for
            LEFT JOIN canteen_live_cooking_menus ON canteen_live_cooking_menus.due_date = canteen_live_cookings.due_date 
        WHERE
            canteen_live_cookings.due_date = DATE(NOW())
            AND attendance_generate_status = 0");

        for ($i=0; $i < count($live_cooking_attendance); $i++) { 
            $general_attendance = GeneralAttendance::create(
                [
                    'purpose_code' => 'Live Cooking',
                    'due_date' => $live_cooking_attendance[$i]->due_date,
                    'employee_id' => $live_cooking_attendance[$i]->order_for,
                    'created_by' => '1930'
                ]
            );
            $general_attendance->save();

            $liveupdate = CanteenLiveCooking::where('id',$key->id_live)->first();
            $liveupdate->attendance_generate_status = 1;
            $liveupdate->save();
        }

        $now = date('Y-m-d');

        // if ($now == date('Y-m-01')) {
        //     $emp = Employee::where('end_date',null)->where('live_cooking',1)->get();
        //     if (count($emp) > 0) {
        //         foreach ($emp as $key) {
        //             $emp_edit = Employee::where('id',$key->id)->first();
        //             $emp_edit->live_cooking = 0;
        //             $emp_edit->save();
        //         }
        //     }
        // }

        $live_cooking = DB::SELECT("SELECT
          *,
          canteen_live_cookings.id AS id_live 
        FROM
          `canteen_live_cookings`
          LEFT JOIN employee_syncs ON employee_syncs.employee_id = canteen_live_cookings.order_for
          LEFT JOIN canteen_live_cooking_menus ON canteen_live_cooking_menus.due_date = canteen_live_cookings.due_date 
        WHERE
          whatsapp_status = 0 
          AND canteen_live_cookings.due_date = DATE(
          NOW()) + INTERVAL 1 DAY");

        if (count($live_cooking) > 0) {

            foreach ($live_cooking as $key) {
                $due_date = date('d F Y',strtotime($key->due_date));

                $due_date_replace = str_replace(" ","%20",$due_date);
                $menu_name = str_replace(" ","%20",$key->menu_name);

                if(substr($key->phone, 0, 1) == '+' ){
                 $phone = substr($key->phone, 1, 15);
                }
                else if(substr($key->phone, 0, 1) == '0'){
                 $phone = "62".substr($key->phone, 1, 15);
                }
                else{
                 $phone = $key->phone;
                }

                // $phone = '6285645896741';

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=628113669871&message=Anda%20telah%20terjadwal%20Live%20Cooking%20pada%20tanggal%20'.$due_date_replace.'.%0ASilahkan%20cek%20di%20MIRAI.%0A%0A-YMPI%20GA%20Dept.-&type=chat',
                  CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
                  ),
                  // CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6282334197238&message=Anda%20telah%20terjadwal%20Live%20Cooking%20pada%20tanggal%20'.$due_date_replace.'.%0ASilahkan%20cek%20di%20MIRAI.%0A%0A-YMPI%20GA%20Dept.-&type=chat',
                  // CURLOPT_HTTPHEADER => array(
                  //   'Accept: application/json',
                  //   'Content-Type: application/x-www-form-urlencoded',
                  //   'Authorization: Bearer OPd8jOytcihnTxoh3WIPLcgdjNAqZgEOjxRbIBb8JnsN7heixZ'
                  // ),
                ));
                curl_exec($curl);

                $liveupdate = CanteenLiveCooking::where('id',$key->id_live)->first();
                $liveupdate->whatsapp_status = 1;
                $liveupdate->save();
            }
        }


    }
}
