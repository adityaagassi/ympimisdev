<?php

use Illuminate\Database\Seeder;

class CodeGeneratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('code_generators')->insert(
            [
              'prefix' => '201809',
              'length' => '6',
              'index' => '0',
              'note' => 'flo',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        DB::table('code_generators')->insert(
            [
              'prefix' => '20180901',
              'length' => '4',
              'index' => '0',
              'note' => 'pd',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        DB::table('code_generators')->insert(
            [
              'prefix' => '201809',
              'length' => '2',
              'index' => '0',
              'note' => 'container',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
    }
}
