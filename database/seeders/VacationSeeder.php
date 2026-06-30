<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\Vacation;
use Illuminate\Database\Seeder;

class VacationSeeder extends Seeder
{
    public function run(): void
    {
        $juan = Personnel::where('dni', '12345678')->first();
        $pedro = Personnel::where('dni', '87654321')->first();

        if ($juan) {
            Vacation::updateOrCreate(
                [
                    'personnel_id' => $juan->id,
                    'start_date' => '2026-07-01',
                    'end_date' => '2026-07-15',
                ],
                [
                    'requested_days' => 15,
                    'status' => 'Aprobada',
                    'notes' => 'Vacaciones aprobadas del mes de julio.',
                ]
            );
        }

        if ($pedro) {
            Vacation::updateOrCreate(
                [
                    'personnel_id' => $pedro->id,
                    'start_date' => '2026-08-01',
                    'end_date' => '2026-08-20',
                ],
                [
                    'requested_days' => 20,
                    'status' => 'Pendiente',
                    'notes' => 'Solicitud de vacaciones pendiente del mes de agosto.',
                ]
            );
        }
    }
}