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
        $datas = db::connection('sunfish')->select("SELECT DISTINCT * FROM VIEW_YMPI_Emp_OrgUnit");
        $datas2 = json_decode(json_encode($datas), true);
        
        foreach ($datas2 as $data) {
            $row = array();

            $row['employee_id'] = $data['Emp_no'];
            $row['name'] = $data['Full_name'];
            $row['gender'] = $data['gender'];
            $row['birth_place'] = $data['birthplace'];
            $row['birth_date'] = $data['birthdate'];
            $row['address'] = $data['address'];
            $row['phone'] = $data['phone'];
            $row['card_id'] = $data['identity_no'];
            $row['npwp'] = $data['taxfilenumber'];
            $row['JP'] = $data['JP'];
            $row['BPJS'] = $data['BPJS'];
            $row['hire_date'] = $data['start_date'];
            $row['position'] = $data['pos_name_en'];
            $row['grade_code'] = $data['grade_code'];
            $row['grade_name'] = $data['gradecategory_name'];
            $row['division'] = $data['Division'];
            $row['department'] = $data['Department'];
            $row['section'] = $data['Section'];
            $row['group'] = $data['Group'];
            $row['sub_group'] = $data['Sub-Group'];
            $row['group'] = $data['Group'];
            $row['employment_status'] = $data['employ_code'];
            $row['cost_center'] = $data['cost_center_code'];
            $row['assignment'] = $data['Penugasan'];
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');

            $insert[] = $row;
        }
        
        DB::table('employee_syncs')->truncate();
        foreach (array_chunk($insert,1000) as $t)  
        {
            DB::table('employee_syncs')->insert($t);
        }
    }
}
