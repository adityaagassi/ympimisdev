<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;
use App\EmployeeSync;
use App\GreatdayAttendance;

class GreatdayAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:greatday_attendance';

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
        $tgl = date('Y-m-14');
        $list = DB::CONNECTION('sunfish')->select("SELECT
          * 
        FROM
          [dbo].[VIEW_AR_YMPI] AS A 
        WHERE
          format ( a.dateTime, 'yyyy-MM-dd' ) = '".$tgl."'");

        $lists = [];

        for ($i=0; $i < count($list); $i++) { 
          $listss = EmployeeSync::where('employee_id',$list[$i]->emp_no)->first();
          $latlong = json_decode($list[$i]->location);
          $mocks = null;
          if (ISSET($latlong->mock)) {
              $mocks = $latlong->mock;
          }

          $url = "https://locationiq.org/v1/reverse.php?key=pk.456ed0d079b6f646ad4db592aa541ba0&lat=".$latlong->latitude."&lon=".$latlong->longitude."&format=json";
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            $results = curl_exec($curlHandle);
            curl_close($curlHandle);

            $addrs = json_encode($results);
            $loc2 = explode('\"',$addrs);

            // All Village
            $keyVillage = array_search('village', $loc2);
            $keyResidential = array_search('residential', $loc2);
            $keyHamlet = array_search('hamlet', $loc2);
            $keyNeighbourhood = array_search('neighbourhood', $loc2);

            // All City
            $keyStateDistrict = array_search('state_district', $loc2);
            $keyCity = array_search('city', $loc2);
            $keyCounty = array_search('county', $loc2);

            //All Province
            $keyState = array_search('state', $loc2);
            $keyPostcode = array_search('postcode', $loc2);
            $keyCountry = array_search('country', $loc2);

            if ($keyVillage && $loc2[$keyVillage+2] != ":") {
                $village = $loc2[$keyVillage+2];
            }else if($keyResidential && $loc2[$keyResidential+2] != ":") {
                $village = $loc2[$keyResidential+2];
            }else if($keyHamlet && $loc2[$keyHamlet+2] != ":") {
                $village = $loc2[$keyHamlet+2];
            }else if($keyNeighbourhood && $loc2[$keyNeighbourhood+2] != ":") {
                $village = $loc2[$keyNeighbourhood+2];
            }else{  
                $village = "";
            }

            if ($keyStateDistrict && $loc2[$keyStateDistrict + 2] != ":") {
                $city = $loc2[$keyStateDistrict + 2];
            }else if($keyCity && $loc2[$keyCity + 2] != ":") {
                $city = $loc2[$keyCity + 2];
            }else if($keyCounty && $loc2[$keyCounty+2] != ":") {
                $city = $loc2[$keyCounty+2];
            }else{  
                $city = "";
            }

            if($keyState && $loc2[$keyState + 2] != ":"){
                $province = $loc2[$keyState + 2];
            }else{
                $province = "";
            }


            // if (ISSET($addrs->address->village)) {
            //       $village = $addrs->address->village;
            //   }elseif(ISSET($addrs->address->city)){
            //     $village = $addrs->address->city;
            //   }elseif(ISSET($addrs->address->suburb)){
            //     $village = $addrs->address->suburb;
            //   }elseif(ISSET($addrs->address->house_number)){
            //         $addrs->address->house_number;
            //     }
            //     elseif(ISSET($addrs->address->road)){
            //         $addrs->address->road;
            //     }
            //     elseif(ISSET($addrs->address->neighbourhood)){
            //         $addrs->address->neighbourhood;
            //     }
            //     elseif(ISSET($addrs->address->hamlet)){
            //         $addrs->address->hamlet;
            //     }
            //     elseif(ISSET($addrs->address->town)){
            //         $addrs->address->town;
            //     }
            //     elseif(ISSET($addrs->address->city_district)){
            //         $addrs->address->city_district;
            //     }
            //     elseif(ISSET($addrs->address->region)){
            //         $addrs->address->region;
            //     }

            //     if (ISSET($addrs->address->state_district)) {
            //         $state_district = $addrs->address->state_district;
            //     }else{
            //         $state_district = $addrs->address->county;
            //     }

          $menu = GreatdayAttendance::updateOrCreate(
                [
                    'date_in' => date('Y-m-d',strtotime($list[$i]->dateTime)),
                    'employee_id' => $listss->employee_id,
                ],
                [
                    'employee_id' => $listss->employee_id,
                   'name' => $listss->name,
                   'date_in' => date('Y-m-d',strtotime($list[$i]->dateTime)),
                   'time_in' => $list[$i]->dateTime,
                   'task' => $list[$i]->taskDesc,
                   'department' => $listss->department,
                   'section' => $listss->section,
                   'group' => $listss->group,
                   'latitude' => $latlong->latitude,
                   'longitude' => $latlong->longitude,
                   'mock' => $mocks,
                   'village' => $village,
                   'state_district' => $city,
                   'state' => $province,
                    'created_by' => '1929'
                ]
            );
            $menu->save();
        }
    }
}
