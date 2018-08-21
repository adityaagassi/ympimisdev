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
    	    DB::table('users')->insert([
            'name' => 'administrator',
            'username' => 'administrator',
            'email' => 'administrator@gmail.com',
            'password' => bcrypt('administrator'),
            'level_id' => '1',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        //
    }
}
