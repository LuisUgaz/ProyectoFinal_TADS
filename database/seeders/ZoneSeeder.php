<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'Lambayeque')->first();

        if (!$department) {
            return;
        }

        $province = Province::where('name', 'Chiclayo')
            ->where('department_id', $department->id)
            ->first();

        if (!$province) {
            return;
        }

        $district = District::where('name', 'José Leonardo Ortiz')
            ->where('province_id', $province->id)
            ->first();

        if (!$district) {
            return;
        }

        $zones = [
            [
                'name' => 'Centro',
                'description' => 'Sector centro de JLO',
                'coordinates' => [
                    ['lat' => -6.756392, 'lng' => -79.833667],
                    ['lat' => -6.756401, 'lng' => -79.838865],
                    ['lat' => -6.762681, 'lng' => -79.841187],
                    ['lat' => -6.763225, 'lng' => -79.834664],
                ],
            ],
            [
                'name' => 'Norte',
                'description' => 'Sector norte de JLO',
                'coordinates' => [
                    ['lat' => -6.750900, 'lng' => -79.835900],
                    ['lat' => -6.750900, 'lng' => -79.829900],
                    ['lat' => -6.755500, 'lng' => -79.829900],
                    ['lat' => -6.755500, 'lng' => -79.835900],
                ],
            ],
            [
                'name' => 'Sur',
                'description' => 'Sector sur de JLO',
                'coordinates' => [
                    ['lat' => -6.765000, 'lng' => -79.835900],
                    ['lat' => -6.765000, 'lng' => -79.829900],
                    ['lat' => -6.770000, 'lng' => -79.829900],
                    ['lat' => -6.770000, 'lng' => -79.835900],
                ],
            ],
            [
                'name' => 'Oeste',
                'description' => 'Sector oeste de JLO',
                'coordinates' => [
                    ['lat' => -6.756392, 'lng' => -79.842000],
                    ['lat' => -6.756401, 'lng' => -79.847000],
                    ['lat' => -6.762681, 'lng' => -79.847000],
                    ['lat' => -6.763225, 'lng' => -79.842000],
                ],
            ],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                ['name' => $zone['name']],
                [
                    'department_id' => $department->id,
                    'province_id' => $province->id,
                    'district_id' => $district->id,
                    'description' => $zone['description'],
                    'status' => true,
                    'coordinates' => $zone['coordinates'],
                ]
            );
        }
    }
}