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
            'name' => 'Clark Kent',
            'username' => 'superman',
            'email' => 'superman@gmail.com',
            'password' => bcrypt('superman'),
            'role_code' => 'S',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
 }
}
