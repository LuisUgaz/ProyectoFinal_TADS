<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personnel;
use App\Models\PersonnelType;
use App\Models\Shift;
use App\Models\Zone;
use App\Models\Vehicle;
use App\Models\PersonnelGroup;
use App\Models\PersonnelGroupWorkday;
use App\Models\PersonnelGroupDetail;
use App\Models\Schedule;
use App\Models\ScheduleWorkday;
use App\Models\Holiday;
use App\Models\Attendance;
use App\Models\Vacation;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. FERIADO DE PRUEBA (Cerca de la fecha actual)
        $today = Carbon::now();
        Holiday::updateOrCreate(
            ['date' => $today->copy()->addDays(2)->format('Y-m-d')],
            ['description' => 'Feriado de Prueba (Validación)', 'status' => true]
        );

        // 2. Obtener dependencias existentes
        $shift = Shift::where('name', 'Mañana')->first();
        $zone = Zone::where('name', 'Centro')->first();
        $vehicle = Vehicle::first();
        
        $driver = Personnel::where('dni', '12345678')->first(); // Juan Alberto Ramos
        $helper1 = Personnel::where('dni', '87654321')->first(); // Pedro Fernando Montenegro
        $helper2 = Personnel::where('dni', '11223344')->first(); // Maria Elena Paz

        if ($driver && $zone && $vehicle && $shift) {
            
            // 3. VACACIONES DE PRUEBA (Para el Conductor)
            // Corregido: requested_days, status = 'Aprobada', notes en lugar de reason
            Vacation::updateOrCreate(
                [
                    'personnel_id' => $driver->id,
                    'start_date' => $today->copy()->subDays(2)->format('Y-m-d'),
                    'end_date' => $today->copy()->addDays(5)->format('Y-m-d'),
                ],
                [
                    'notes' => 'Vacaciones de prueba para validación',
                    'status' => 'Aprobada', 
                    'requested_days' => 7
                ]
            );

            // 4. GRUPO DE PRUEBA
            $group = PersonnelGroup::updateOrCreate(
                ['name' => 'Grupo A1 - Sector Centro'],
                [
                    'zone_id' => $zone->id,
                    'shift_id' => $shift->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'status' => true
                ]
            );

            PersonnelGroupWorkday::updateOrCreate(['personnel_group_id' => $group->id, 'day' => 'Lunes']);
            PersonnelGroupWorkday::updateOrCreate(['personnel_group_id' => $group->id, 'day' => 'Miércoles']);
            PersonnelGroupWorkday::updateOrCreate(['personnel_group_id' => $group->id, 'day' => 'Viernes']);

            if ($helper1) {
                PersonnelGroupDetail::updateOrCreate(['personnel_group_id' => $group->id, 'personnel_id' => $helper1->id]);
            }
            if ($helper2) {
                PersonnelGroupDetail::updateOrCreate(['personnel_group_id' => $group->id, 'personnel_id' => $helper2->id]);
            }

            // 5. PROGRAMACIÓN EXISTENTE (Conflicto de Vehículo y Personal)
            $existingSchedule = Schedule::updateOrCreate(
                [
                    'personnel_group_id' => $group->id,
                    'start_date' => $today->copy()->subDays(10)->format('Y-m-d'),
                    'end_date' => $today->copy()->addDays(10)->format('Y-m-d'),
                ],
                [
                    'zone_id' => $group->zone_id,
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'status' => 'scheduled',
                    'notes' => 'Esta programación ya ocupa los recursos'
                ]
            );

            // 6. ASISTENCIAS
            Attendance::updateOrCreate(
                ['personnel_id' => $driver->id, 'date' => $today->format('Y-m-d'), 'type' => 'Ingreso'],
                ['time' => '06:05:00', 'shift_id' => $shift->id, 'status' => 'Presente', 'notes' => 'Prueba']
            );
        }
    }
}
