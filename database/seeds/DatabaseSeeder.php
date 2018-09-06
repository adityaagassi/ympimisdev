<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(LevelsTableSeeder::class);
        $this->call(ContainersTableSeeder::class);
        $this->call(DestinationsTableSeeder::class);
        $this->call(ShipmentConditionsTableSeeder::class);
        $this->call(OriginGroupsTableSeeder::class);
        $this->call(MaterialsTableSeeder::class);
        $this->call(MaterialVolumesTableSeeder::class);
        $this->call(ContainerSchedulesTableSeeder::class);
        $this->call(ProductionSchedulesTableSeeder::class);
        $this->call(WeeklyCalendarsTableSeeder::class);
    }
}