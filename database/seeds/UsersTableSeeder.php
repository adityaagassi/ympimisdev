<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     DB::table('users')->insert(
        [
            'name' => 'Superman',
            'username' => 'superman',
            'email' => 'superman@gmail.com',
            'password' => bcrypt('superman'),
            'level_id' => '1',
            'department_id' => '1',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Assy-FL',
            'username' => 'Assy-FL',
            'email' => 'Assy-FL@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'department_id' => '14',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Assy-CL',
            'username' => 'Assy-CL',
            'email' => 'Assy-CL@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'department_id' => '14',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Assy-SX',
            'username' => 'Assy-SX',
            'email' => 'Assy-SX@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'department_id' => '14',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Assy-EDIN',
            'username' => 'Assy-EDIN',
            'email' => 'Assy-EDIN@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'department_id' => '13',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'WH-Logistic',
            'username' => 'WH-Logistic',
            'email' => 'WH-Logistic@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'department_id' => '10',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
 }
}
