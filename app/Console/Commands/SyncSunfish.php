<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSunfish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:sunfish';

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
        $insert = array();
        $datas = db::connection('sunfish')->select("select * from dbo.view_ympi_emp_orgunit");
        
        foreach ($datas as $data) {
            $row = array();

            $row['employee_id'] = $data->Emp_no;
            $row['employee_name'] = $data->Full_name;
            $row['gender'] = $data->gender;
            $row['birth_place'] = $data->birthplace;
            $row['birth_date'] = $data->birthdate;
            $row['address'] = $data->address;
            $row['phone'] = $data->phone;
            $row['identity_number'] = $data->identity_no;
            $row['taxfile_number'] = $data->taxfilenumber;
            $row['JP'] = $data->JP;
            $row['BPJS'] = $data->BPJS;
            $row['hire_date'] = $data->start_date;
            $row['position_name'] = $data->pos_name_en;
            $row['grade_code'] = $data->grade_code;
            $row['grade_category'] = $data->gradecategory_name;
            $row['division'] = $data->Division;
            $row['department'] = $data->Department;
            $row['section'] = $data->Section;
            $row['group'] = $data->Group;
            $row['employment_status'] = $data->employ_code;
            $row['cost_center'] = $data->cost_center;
            $row['assignment'] = $data->Penugasan;

            $insert[] = $row;
        }
        
        DB::table('employee_syncs')->truncate();
        DB::table('employee_syncs')->insert($insert);
    }
}
