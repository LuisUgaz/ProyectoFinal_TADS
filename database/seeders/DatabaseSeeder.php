<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UbigeoSeeder::class,
            HolidaySeeder::class,
            VehicleCatalogSeeder::class,
            VehicleSeeder::class,
            PersonnelCatalogSeeder::class,
            PersonnelSeeder::class,
            VacationSeeder::class,
            ShiftSeeder::class,
            ZoneSeeder::class,
            ReasonSeeder::class,
            PersonnelGroupSeeder::class,
        ]);
    }
}