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
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Aditya Agassi',
            'username' => 'adityaagassi',
            'email' => 'adityaagassi@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '2',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Much. Buyung',
            'username' => 'buyung',
            'email' => 'buyung@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '3',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Anton Budi',
            'username' => 'anton',
            'email' => 'anton@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '4',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Agus Yulianto',
            'username' => 'agus',
            'email' => 'agus@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '5',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Romy Agung',
            'username' => 'romy',
            'email' => 'romy@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '6',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
     DB::table('users')->insert(
        [
            'name' => 'Trial',
            'username' => 'trial',
            'email' => 'trial@gmail.com',
            'password' => bcrypt('1234'),
            'level_id' => '7',
            'created_by' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
 }
}
