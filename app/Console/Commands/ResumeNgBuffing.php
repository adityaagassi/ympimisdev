<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\MiddleBuffingMonthlyResume;
use App\MiddleBuffingWeeklyResume;
use App\MiddleBuffingDailyResume;
use App\MiddleBuffingCheckLog;
use App\MiddleBuffingNgLog;
use App\MiddleBuffingMonthlyNgResume;


class ResumeNgBuffing extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resume:buffing';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $datetime = date('Y-m-d H:i:s');

        $resume_cek_monthly = db::select("SELECT w.fiscal_year, DATE_FORMAT(w.week_date,'%Y-%m') AS `month`, w.week_name, date(c.created_at) AS date, c.location, m.hpl, SUM(c.quantity) AS cek FROM middle_buffing_check_logs c
            LEFT JOIN weekly_calendars w ON w.week_date = date(c.created_at)
            LEFT JOIN materials m ON m.material_number = c.material_number
            WHERE c.created_at < '".$datetime."'
            AND c.sync = 0
            GROUP BY w.fiscal_year, `month`, w.week_name, date, c.location, m.hpl");


        for ($i=0; $i < count($resume_cek_monthly); $i++) {
            $cekMonth = MiddleBuffingMonthlyResume::where('fiscal_year', $resume_cek_monthly[$i]->fiscal_year)
            ->where('month', $resume_cek_monthly[$i]->month)
            ->where('location', $resume_cek_monthly[$i]->location)
            ->first();

            if($cekMonth){
                $cekMonth->check = $cekMonth->check + $resume_cek_monthly[$i]->cek;
                $cekMonth->save();
            }else{
                $insert = new MiddleBuffingMonthlyResume([
                    'fiscal_year' => $resume_cek_monthly[$i]->fiscal_year,
                    'month' => $resume_cek_monthly[$i]->month,
                    'location' => $resume_cek_monthly[$i]->location,
                    'check' => $resume_cek_monthly[$i]->cek
                ]);
                $insert->save();
            }


            $cekWeek = MiddleBuffingWeeklyResume::where('fiscal_year', $resume_cek_monthly[$i]->fiscal_year)
            ->where('month', $resume_cek_monthly[$i]->month)
            ->where('week', $resume_cek_monthly[$i]->week_name)
            ->where('location', $resume_cek_monthly[$i]->location)
            ->first();

            if($cekWeek){
                $cekWeek->check = $cekWeek->check + $resume_cek_monthly[$i]->cek;
                $cekWeek->save();
            }else{
                $insert = new MiddleBuffingWeeklyResume([
                    'fiscal_year' => $resume_cek_monthly[$i]->fiscal_year,
                    'month' => $resume_cek_monthly[$i]->month,
                    'week' => $resume_cek_monthly[$i]->week_name,
                    'location' => $resume_cek_monthly[$i]->location,
                    'check' => $resume_cek_monthly[$i]->cek
                ]);
                $insert->save();
            }

            $cekDate = MiddleBuffingDailyResume::where('date', $resume_cek_monthly[$i]->date)
            ->where('remark', $resume_cek_monthly[$i]->hpl)
            ->first();

            if($cekDate){
                $cekDate->check = $cekDate->check + $resume_cek_monthly[$i]->cek;
                $cekDate->save();
            }else{
                $insert = new MiddleBuffingDailyResume([
                    'date' => $resume_cek_monthly[$i]->date,
                    'remark' => $resume_cek_monthly[$i]->hpl,
                    'location' => $resume_cek_monthly[$i]->location,
                    'check' => $resume_cek_monthly[$i]->cek
                ]);
                $insert->save();
            }
        }

        $resume_ng_monthly = db::select("SELECT w.fiscal_year, DATE_FORMAT(w.week_date,'%Y-%m') AS `month`, w.week_name, date(ng.created_at) AS date, ng.location, m.hpl, SUM(ng.quantity) AS ng FROM middle_buffing_ng_logs ng
            LEFT JOIN weekly_calendars w ON w.week_date = date(ng.created_at)
            LEFT JOIN materials m ON m.material_number = ng.material_number
            WHERE ng.created_at < '".$datetime."'
            AND ng.sync = 0
            GROUP BY w.fiscal_year, `month`, w.week_name, date, ng.location, m.hpl");

        for ($i=0; $i < count($resume_ng_monthly); $i++) {
            $ngMonth = MiddleBuffingMonthlyResume::where('fiscal_year', $resume_ng_monthly[$i]->fiscal_year)
            ->where('month', $resume_ng_monthly[$i]->month)
            ->where('location', $resume_ng_monthly[$i]->location)
            ->first();

            if($ngMonth){
                $ngMonth->ng = $ngMonth->ng + $resume_ng_monthly[$i]->ng;
                $ngMonth->save();
            }else{
                $insert = new MiddleBuffingMonthlyResume([
                    'fiscal_year' => $resume_ng_monthly[$i]->fiscal_year,
                    'month' => $resume_ng_monthly[$i]->month,
                    'location' => $resume_ng_monthly[$i]->location,
                    'ng' => $resume_ng_monthly[$i]->ng
                ]);
                $insert->save();
            }


            $ngWeek = MiddleBuffingWeeklyResume::where('fiscal_year', $resume_ng_monthly[$i]->fiscal_year)
            ->where('month', $resume_ng_monthly[$i]->month)
            ->where('week', $resume_ng_monthly[$i]->week_name)
            ->where('location', $resume_ng_monthly[$i]->location)
            ->first();

            if($ngWeek){
                $ngWeek->ng = $ngWeek->ng + $resume_ng_monthly[$i]->ng;
                $ngWeek->save();
            }else{
                $insert = new MiddleBuffingWeeklyResume([
                    'fiscal_year' => $resume_ng_monthly[$i]->fiscal_year,
                    'month' => $resume_ng_monthly[$i]->month,
                    'week' => $resume_ng_monthly[$i]->week_name,
                    'location' => $resume_ng_monthly[$i]->location,
                    'ng' => $resume_ng_monthly[$i]->ng
                ]);
                $insert->save();
            }

            $cekDate = MiddleBuffingDailyResume::where('date', $resume_ng_monthly[$i]->date)
            ->where('remark', $resume_ng_monthly[$i]->hpl)
            ->first();

            if($cekDate){
                $cekDate->ng = $cekDate->ng + $resume_ng_monthly[$i]->ng;
                $cekDate->save();
            }else{
                $insert = new MiddleBuffingDailyResume([
                    'date' => $resume_ng_monthly[$i]->date,
                    'remark' => $resume_ng_monthly[$i]->hpl,
                    'location' => $resume_ng_monthly[$i]->location,
                    'ng' => $resume_ng_monthly[$i]->ng
                ]);
                $insert->save();
            }
        }


        $resume_ng_key =db::select("SELECT w.fiscal_year, DATE_FORMAT(w.week_date,'%Y-%m') AS `month`, ng.location, m.hpl, m.`key`, ng.ng_name, SUM(ng.quantity) AS ng FROM middle_buffing_ng_logs ng
            LEFT JOIN weekly_calendars w ON w.week_date = date(ng.created_at)
            LEFT JOIN materials m ON m.material_number = ng.material_number
            WHERE ng.created_at < '".$datetime."'
            AND ng.sync = 0
            GROUP BY w.fiscal_year, `month`, ng.location, m.hpl, m.`key`, ng.ng_name");

        for ($i=0; $i < count($resume_ng_key); $i++) {
            $ngKey = MiddleBuffingMonthlyNgResume::where('fiscal_year', $resume_ng_key[$i]->fiscal_year)
            ->where('month', $resume_ng_key[$i]->month)
            ->where('location', $resume_ng_key[$i]->location)
            ->where('hpl', $resume_ng_key[$i]->hpl)
            ->where('key', $resume_ng_key[$i]->key)
            ->where('ng_name', $resume_ng_key[$i]->ng_name)
            ->first();

            if($ngKey){
                $ngKey->ng = $ngKey->ng + $resume_ng_key[$i]->ng;
                $ngKey->save();
            }else{
                $insert = new MiddleBuffingMonthlyNgResume([
                    'fiscal_year' => $resume_ng_key[$i]->fiscal_year,
                    'month' => $resume_ng_key[$i]->month,
                    'location' => $resume_ng_key[$i]->location,
                    'hpl' => $resume_ng_key[$i]->hpl,
                    'key' => $resume_ng_key[$i]->key,
                    'ng_name' => $resume_ng_key[$i]->ng_name,
                    'ng' => $resume_ng_key[$i]->ng
                ]);
                $insert->save();
            }
        }



        $updateCheck = MiddleBuffingCheckLog::where('created_at', '<', $datetime)
        ->update([
            'sync' => 1
        ]);

        $updateNg = MiddleBuffingNgLog::where('created_at', '<', $datetime)
        ->update([
            'sync' => 1
        ]);

    }
}
