<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;

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
    $datas = db::connection('sunfish')->select("SELECT DISTINCT * FROM VIEW_YMPI_Emp_OrgUnit WHERE Emp_no <> 'sunfish'");
    $datas2 = json_decode(json_encode($datas), true);

    foreach ($datas2 as $data) {
        $row = array();

        $row['employee_id'] = $data['Emp_no'];
        $row['name'] = $data['Full_name'];
        $row['gender'] = $data['gender'];
        $row['birth_place'] = $data['birthplace'];
        $row['birth_date'] = $data['birthdate'];
        $row['address'] = $data['Current_Address'];
        $row['phone'] = $data['phone'];
        $row['card_id'] = $data['identity_no'];
        $row['npwp'] = $data['taxfilenumber'];
        $row['JP'] = $data['JP'];
        $row['BPJS'] = $data['BPJS'];
        $row['hire_date'] = $data['start_date'];
        $row['end_date'] = $data['end_date'];
        $row['position'] = $data['pos_name_en'];
        $row['position_code'] = $data['pos_code'];
        $row['grade_code'] = $data['grade_code'];
        $row['grade_name'] = $data['gradecategory_name'];
        $row['division'] = $data['Division'];
        $row['department'] = $data['Department'];
        $row['section'] = $data['Section'];
        $row['group'] = $data['Groups'];
        $row['sub_group'] = $data['Sub_Groups'];
        $row['employment_status'] = $data['employ_code'];
        $row['cost_center'] = $data['cost_center_code'];
        $row['assignment'] = $data['Penugasan'];
        $row['union'] = $data['Labour_Union'];
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');
        $row['nik_manager'] = $data['NIK_Manager'];
        $row['zona'] = $data['Zona'];

        $insert[] = $row;
    }

    DB::table('employee_syncs')->truncate();
    foreach (array_chunk($insert,1000) as $t)  
    {
        DB::table('employee_syncs')->insert($t);
    }

    $users = DB::table('users')->get();

    $usernames = array();
    foreach ($users as $user) {
        array_push($usernames, strtoupper($user->username));
    }

    foreach ($datas as $data) {     
        if(!in_array($data->Emp_no, $usernames)){
            $insert_user = New User([
                'name' => ucwords($data->Full_name),
                'email' => strtolower($data->Emp_no).'@gmail.com',
                'password' => bcrypt('123456'),
                'username' => strtolower($data->Emp_no),
                'role_code' => 'emp-srv',
                'avatar' => strtolower($data->Emp_no.'jpg'),
                'created_by' => '1'
            ]);
            $insert_user->save();
        }
    }

    $employees = DB::table('employees')->get();
    $employee_ids = array();
    foreach ($employees as $employee) {
        array_push($employee_ids, strtoupper($employee->employee_id));
    }
    foreach ($datas as $data) {
        if(!in_array($data->Emp_no, $employee_ids)){
            $insert_employee = New Employee([
                'employee_id' => strtoupper($data->Emp_no),
                'name' => ucwords($data->Full_name),
                'gender' => strtoupper($data->gender),
                'birth_place' => ucwords($data->birthplace),
                'birth_date' => $data->birthdate,
                'address' => ucwords($data->Current_Address),
                'card_id' => $data->identity_no,
                'hire_date' => $data->start_date,
                'created_by' => '1'
            ]);
            $insert_employee->save();
        }else{
            if(!is_null($data->end_date)){
                $update_employee = Employee::where('employee_id', $data->Emp_no)
                ->update([
                    'end_date' => $data->end_date
                ]);
            }

            $update_name = Employee::where('employee_id', $data->Emp_no)
            ->update([
                'name' => ucwords($data->Full_name)
            ]);
        }
    }




}
}
