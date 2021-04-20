<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SurveyCovid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:survey_covid';

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

        $insert_data = db::select("
            INSERT INTO miraimobile.survey_covid_logs (survey_code, tanggal, employee_id, name, department, question, answer, poin, total, keterangan, deleted_at, created_at, updated_at)
            SELECT survey_code, tanggal, employee_id, name, department, question, answer, poin, total, keterangan, deleted_at, created_at, updated_at FROM miraimobile.survey_logs
        ");


        $delete_data = db::select("DELETE FROM miraimobile.survey_logs");
    }
}
