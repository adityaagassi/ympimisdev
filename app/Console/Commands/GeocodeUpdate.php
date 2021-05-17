<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\GreatdayAttendance;

class GeocodeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:geocode';

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
        $attendances = GreatdayAttendance::whereNull('state')->get();

        foreach ($attendances as $attendance) {
            $greatday = GreatdayAttendance::where('id', '=', $attendance->id)->first();
            $url = "https://locationiq.org/v1/reverse.php?key=pk.456ed0d079b6f646ad4db592aa541ba0&lat=".$attendance->latitude."&lon=".$attendance->longitude."&format=json";
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

            $keyVillage = array_search('village', $loc2);
            $keyResidential = array_search('residential', $loc2);
            $keyHamlet = array_search('hamlet', $loc2);
            $keyNeighbourhood = array_search('neighbourhood', $loc2);

            $keyStateDistrict = array_search('state_district', $loc2);
            $keyCity = array_search('city', $loc2);
            $keyCounty = array_search('county', $loc2);

            $keyState = array_search('state', $loc2);
            $keyPostcode = array_search('postcode', $loc2);
            $keyCountry = array_search('country', $loc2);

            if($keyVillage && $loc2[$keyVillage+2] != ":"){
                $village = $loc2[$keyVillage+2];
            }
            else if($keyResidential && $loc2[$keyResidential+2] != ":") {
                $village = $loc2[$keyResidential+2];
            }
            else if($keyHamlet && $loc2[$keyHamlet+2] != ":") {
                $village = $loc2[$keyHamlet+2];
            }
            else if($keyNeighbourhood && $loc2[$keyNeighbourhood+2] != ":") {
                $village = $loc2[$keyNeighbourhood+2];
            }
            else{  
                $village = null;
            }

            if ($keyStateDistrict && $loc2[$keyStateDistrict + 2] != ":") {
                $city = $loc2[$keyStateDistrict + 2];
            }
            else if($keyCity && $loc2[$keyCity + 2] != ":") {
                $city = $loc2[$keyCity + 2];
            }
            else if($keyCounty && $loc2[$keyCounty+2] != ":") {
                $city = $loc2[$keyCounty+2];
            }
            else{  
                $city = null;
            }

            if($keyState && $loc2[$keyState + 2] != ":"){
                $province = $loc2[$keyState + 2];
            }
            else{
                $province = null;
            }

            $greatday->state = $province;
            $greatday->state_district = $city;
            $greatday->village = $village;

            $greatday->save();
        }
    }
}
