<?php

use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('levels')->insert(
            [
              'level_name' => 'Superman',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
      );
        DB::table('levels')->insert(
            [
              'level_name' => 'Operator',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
      );
        DB::table('levels')->insert(
            [
              'level_name' => 'Staff',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
      );
        DB::table('levels')->insert(
            [
              'level_name' => 'Leader',
              'created_by' => '1',
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
          ]
      );
    }
}
