<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\PersonnelGroup;
use App\Models\PersonnelGroupDetail;
use App\Models\PersonnelGroupWorkday;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class PersonnelGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Grupo Norte A',
                'zone' => 'Norte',
                'shift' => 'Mañana',
                'vehicle' => 'V1C-789',
                'driver_dni' => '12345678',
                'helpers_dni' => ['87654321', '66778899'],
                'days' => ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            ],
            [
                'name' => 'Grupo Centro B',
                'zone' => 'Centro',
                'shift' => 'Tarde',
                'vehicle' => 'A5T-456',
                'driver_dni' => '22334455',
                'helpers_dni' => ['77889900', '88990011'],
                'days' => ['Lu', 'Mi', 'Vi'],
            ],
            [
                'name' => 'Grupo Sur C',
                'zone' => 'Sur',
                'shift' => 'Noche',
                'vehicle' => 'M9B-123',
                'driver_dni' => '33445566',
                'helpers_dni' => ['99001122', '10112233'],
                'days' => ['Ma', 'Ju', 'Sá'],
            ],
            [
                'name' => 'Grupo Oeste D',
                'zone' => 'Oeste',
                'shift' => 'Madrugada',
                'vehicle' => 'X4D-001',
                'driver_dni' => '44556688',
                'helpers_dni' => ['20223344', '30334455'],
                'days' => ['Lu', 'Ma', 'Mi', 'Ju', 'Vi'],
            ],
        ];

        foreach ($groups as $data) {
            $zone = Zone::where('name', $data['zone'])->first();
            $shift = Shift::where('name', $data['shift'])->first();
            $vehicle = Vehicle::where('plate', $data['vehicle'])->first();
            $driver = Personnel::where('dni', $data['driver_dni'])->first();

            if (!$zone || !$shift || !$vehicle || !$driver) {
                continue;
            }

            $group = PersonnelGroup::updateOrCreate(
                ['name' => $data['name']],
                [
                    'zone_id' => $zone->id,
                    'shift_id' => $shift->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'status' => true,
                ]
            );

            PersonnelGroupDetail::where('personnel_group_id', $group->id)->delete();
            PersonnelGroupWorkday::where('personnel_group_id', $group->id)->delete();

            foreach ($data['helpers_dni'] as $helperDni) {
                $helper = Personnel::where('dni', $helperDni)->first();

                if ($helper) {
                    PersonnelGroupDetail::create([
                        'personnel_group_id' => $group->id,
                        'personnel_id' => $helper->id,
                    ]);
                }
            }

            foreach ($data['days'] as $day) {
                PersonnelGroupWorkday::create([
                    'personnel_group_id' => $group->id,
                    'day' => $day,
                ]);
            }
        }
    }
}