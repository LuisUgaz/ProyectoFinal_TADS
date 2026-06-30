<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Madrugada',
                'description' => 'Turno madrugada',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
            ],
            [
                'name' => 'Mañana',
                'description' => 'Turno matutino',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
            ],
            [
                'name' => 'Tarde',
                'description' => 'Turno vespertino',
                'start_time' => '14:00:00',
                'end_time' => '18:00:00',
            ],
            [
                'name' => 'Noche',
                'description' => 'Turno nocturno',
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::updateOrCreate(
                ['name' => $shift['name']],
                [
                    'description' => $shift['description'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                ]
            );
        }
    }
}