<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmployeeHistory extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'employee:history';

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
    $employees = db::connection('sunfish')->select("select * from dbo.VIEW_YMPI_Emp_OrgUnit");
    $datas = json_decode(json_encode($employees), true);

    foreach ($datas as $data) {
        $row = array();

        $row['period'] = date("Y-m-t");
        $row['Emp_no'] = $data['Emp_no'];
        $row['Full_name'] = $data['Full_name'];
        $row['grade_code'] = $data['grade_code'];
        $row['start_date'] = $data['start_date'];
        $row['end_date'] = $data['end_date'];
        $row['position_id'] = $data['position_id'];
        $row['dept_id'] = $data['dept_id'];
        $row['pos_name_en'] = $data['pos_name_en'];
        $row['pos_code'] = $data['pos_code'];
        $row['parent_path'] = $data['parent_path'];
        $row['BOD'] = $data['BOD'];
        $row['Division'] = $data['Division'];
        $row['Department'] = $data['Department'];
        $row['Section'] = $data['Section'];
        $row['Group'] = $data['Groups'];
        $row['Sub-Group'] = $data['Sub_Groups'];
        $row['status'] = $data['status'];
        $row['employ_code'] = $data['employ_code'];
        $row['photo'] = $data['photo'];
        $row['gender'] = $data['gender'];
        $row['birthplace'] = $data['birthplace'];
        $row['birthdate'] = $data['birthdate'];
        $row['address'] = $data['address'];
        $row['phone'] = $data['phone'];
        $row['identity_no'] = $data['identity_no'];
        $row['taxfilenumber'] = $data['taxfilenumber'];
        $row['JP'] = $data['JP'];
        $row['BPJS'] = $data['BPJS'];
        $row['cost_center_name'] = $data['cost_center_name'];
        $row['cost_center_code'] = $data['cost_center_code'];
        $row['gradecategory_name'] = $data['gradecategory_name'];
        $row['Penugasan'] = $data['Penugasan'];
        $row['Labour_Union'] = $data['Labour_Union'];
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');

        $insert[] = $row;
    }

    foreach (array_chunk($insert,1000) as $t)  
    {
        DB::table('employee_histories')->insert($t);
    }
}
}
