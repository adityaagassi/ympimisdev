<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePointingCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pointing_calls';

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
        $calendar = db::table('weekly_calendars')
        ->where('week_date', '=', date("Y-m-d"))
        ->first();

        if($calendar->remark != 'H'){
            $locations = db::table('pointing_calls')
            ->select('location')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();

            foreach($locations as $location){
                $point_titles = db::table('pointing_calls')
                ->select('point_title')
                ->where('location', '=', $location->location)
                ->whereNull('deleted_at')
                ->distinct()
                ->get();

                foreach($point_titles as $point_title){
                    $max_point = db::table('pointing_calls')
                    ->where('location', '=', $location->location)
                    ->where('point_title', '=', $point_title->point_title)
                    ->whereNull('deleted_at')
                    ->select(db::raw('max(point_no) as point_no'))
                    ->first();

                    $current_point = db::table('pointing_calls')
                    ->where('location', '=', $location->location)
                    ->where('point_title', '=', $point_title->point_title)
                    ->whereNull('deleted_at')
                    ->where('remark', '=', '1')
                    ->select('point_no')
                    ->first();

                    if($max_point->point_no > $current_point->point_no){
                        $reset_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->update([
                            'remark' => '0'
                        ]);

                        $update_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->where('point_no', '=', $current_point->point_no+1)
                        ->update([
                            'remark' => '1'
                        ]);
                    }
                    else{
                        $reset_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->update([
                            'remark' => '0'
                        ]);

                        $update_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->where('point_no', '=', 1)
                        ->update([
                            'remark' => '1'
                        ]);
                    }
                }

            }
        }
        
    }
}
