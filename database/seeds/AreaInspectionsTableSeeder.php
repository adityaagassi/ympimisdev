<?php

use Illuminate\Database\Seeder;

class AreaInspectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('area_inspections')->insert(
        [
          'id' => '1',
          'area' => 'Front Wall Condition (Dinding Depan)', 
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '2',
          'area' => 'Left Side Wall Condition (Dinding Sebelah Kiri)', 
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '3',
          'area' => 'Right Side Wall Condition (Dinding Sebelah Kanan)',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '4',
          'area' => 'Floor Condition (Lantai)',  
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '5',
          'area' => 'Ceiling/ Roof Condition (Atap)',  
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '6',
          'area' => 'Inside/Outside Door Condition (Pintu Dalam/Luar)',  
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

       DB::table('area_inspections')->insert(
        [
          'id' => '7',
          'area' => 'Outside/Undercarriage Condition (Dinding/Lantai Bawah)', 
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

          DB::table('area_inspections')->insert(
        [
          'id' => '8',
          'area' => 'Door Rubber Sheet (Segel Karet pada Pintu Kontainer)',   
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );

             DB::table('area_inspections')->insert(
        [
          'id' => '9',
          'area' => 'Wheel Condition (Kondisi Ban Kontainer)',   
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
    }
}
