<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Personnel;
use App\Models\PersonnelGroup;
use App\Models\Schedule;
use App\Models\ScheduleDaily;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DashboardScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $date = '2026-06-30';

        $this->seedUserSchedules();

        $scenarios = [
            [
                'group' => 'Grupo Norte A',
                'status' => 'pendiente',
                'attendance' => [
                    '12345678' => 'Presente',
                    '87654321' => 'Presente',
                    '66778899' => 'Presente',
                ],
            ],
            [
                'group' => 'Grupo Oeste D',
                'status' => 'pendiente',
                'attendance' => [
                    '44556688' => 'Presente',
                    '20223344' => 'Presente',
                    '30334455' => 'Ausente',
                ],
            ],
            [
                'group' => 'Grupo Sur C',
                'status' => 'pendiente',
                'attendance' => [
                    '33445566' => 'Ausente',
                    '99001122' => 'Presente',
                    '10112233' => 'Presente',
                ],
            ],
        ];

        foreach ($scenarios as $scenario) {
            $group = PersonnelGroup::with(['helpers.personnel', 'workdays'])
                ->where('name', $scenario['group'])
                ->first();

            if (!$group || !$group->zone_id || !$group->shift_id || !$group->vehicle_id || !$group->driver_id) {
                continue;
            }

            $schedule = Schedule::updateOrCreate(
                [
                    'personnel_group_id' => $group->id,
                    'start_date' => $date,
                    'end_date' => $date,
                ],
                [
                    'zone_id' => $group->zone_id,
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'driver_id' => $group->driver_id,
                    'status' => 'scheduled',
                    'notes' => 'Programacion generada para pruebas del dashboard.',
                ]
            );

            $helperIds = $group->helpers
                ->pluck('personnel_id')
                ->filter()
                ->values()
                ->all();

            $schedule->helpers()->sync($helperIds);

            $schedule->workdays()->delete();

            foreach ($group->workdays as $workday) {
                $schedule->workdays()->create([
                    'day' => $workday->day,
                ]);
            }

            $daily = ScheduleDaily::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date' => $date,
                ],
                [
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'driver_id' => $group->driver_id,
                    'status' => $scenario['status'],
                    'notes' => 'Registro diario generado para pruebas del dashboard.',
                ]
            );

            $daily->helpers()->sync($helperIds);

            $this->seedAttendances($scenario['attendance'], $date, $group->shift_id);
        }

        $dashboardDates = $this->seedDashboardAttendancesForExistingDailies($date);

        $this->seedAvailableReplacements($dashboardDates);
    }

    private function seedUserSchedules(): void
    {
        $schedules = [
            [
                'group' => 'Grupo Sur C',
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-31',
            ],
            [
                'group' => 'Grupo Centro B',
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-31',
            ],
            [
                'group' => 'Grupo Centro B',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-30',
            ],
        ];

        foreach ($schedules as $data) {
            $group = PersonnelGroup::with(['helpers.personnel', 'workdays'])
                ->where('name', $data['group'])
                ->first();

            if (!$group || !$group->zone_id || !$group->shift_id || !$group->vehicle_id || !$group->driver_id) {
                continue;
            }

            $schedule = Schedule::updateOrCreate(
                [
                    'personnel_group_id' => $group->id,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                ],
                [
                    'zone_id' => $group->zone_id,
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'driver_id' => $group->driver_id,
                    'status' => 'scheduled',
                    'notes' => null,
                ]
            );

            $helperIds = $group->helpers
                ->pluck('personnel_id')
                ->filter()
                ->values()
                ->all();

            $schedule->helpers()->sync($helperIds);

            $schedule->workdays()->delete();

            foreach ($group->workdays as $workday) {
                $schedule->workdays()->create([
                    'day' => $workday->day,
                ]);
            }

            $this->generateDailiesFromGroup($schedule, $group, $helperIds);
        }
    }

    private function generateDailiesFromGroup(Schedule $schedule, PersonnelGroup $group, array $helperIds): void
    {
        $currentDate = $schedule->start_date->copy();
        $workdays = $group->workdays->pluck('day')->toArray();
        $holidays = \App\Models\Holiday::where('status', true)
            ->whereBetween('date', [$schedule->start_date, $schedule->end_date])
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->toArray();

        $dayMap = [
            'Monday' => 'Lu',
            'Tuesday' => 'Ma',
            'Wednesday' => 'Mi',
            'Thursday' => 'Ju',
            'Friday' => 'Vi',
            'Saturday' => 'Sá',
            'Sunday' => 'Do',
        ];

        while ($currentDate->lte($schedule->end_date)) {
            $dayKey = $dayMap[$currentDate->format('l')] ?? null;
            $date = $currentDate->format('Y-m-d');

            if (in_array($dayKey, $workdays, true) && !in_array($date, $holidays, true)) {
                $daily = ScheduleDaily::updateOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'date' => $date,
                    ],
                    [
                        'shift_id' => $group->shift_id,
                        'vehicle_id' => $group->vehicle_id,
                        'driver_id' => $group->driver_id,
                        'status' => 'pendiente',
                        'notes' => null,
                    ]
                );

                $daily->helpers()->sync($helperIds);
            }

            $currentDate->addDay();
        }
    }

    private function seedAttendances(array $attendanceByDni, string $date, int $shiftId): void
    {
        foreach ($attendanceByDni as $dni => $status) {
            $person = Personnel::where('dni', $dni)->first();

            if (!$person) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'personnel_id' => $person->id,
                    'date' => $date,
                    'shift_id' => $shiftId,
                    'type' => 'Ingreso',
                ],
                [
                    'time' => $this->attendanceTime($shiftId),
                    'status' => $status,
                    'notes' => 'Asistencia generada para pruebas del dashboard.',
                ]
            );
        }
    }

    private function seedDashboardAttendancesForExistingDailies(string $fromDate): array
    {
        $dailies = ScheduleDaily::with(['driver', 'helpers'])
            ->whereDate('date', '>=', $fromDate)
            ->orderBy('date')
            ->orderBy('id')
            ->limit(8)
            ->get();

        $dashboardDates = [];

        foreach ($dailies as $index => $daily) {
            $date = $daily->date->toDateString();
            $dashboardDates[$date . '-' . $daily->shift_id] = [
                'date' => $date,
                'shift_id' => $daily->shift_id,
            ];

            $assigned = collect([$daily->driver])
                ->filter()
                ->merge($daily->helpers)
                ->unique('id')
                ->values();

            foreach ($assigned as $personIndex => $person) {
                $status = ($index % 3 !== 0 && $personIndex === $assigned->count() - 1)
                    ? 'Ausente'
                    : 'Presente';

                Attendance::updateOrCreate(
                    [
                        'personnel_id' => $person->id,
                        'date' => $date,
                        'shift_id' => $daily->shift_id,
                        'type' => 'Ingreso',
                    ],
                    [
                        'time' => $this->attendanceTime($daily->shift_id),
                        'status' => $status,
                        'notes' => 'Asistencia validada para pruebas del dashboard.',
                    ]
                );
            }
        }

        return array_values($dashboardDates);
    }

    private function seedAvailableReplacements(array $dashboardDates): void
    {
        $replacementDnis = [
            '55667788',
            '88990011',
            '40445566',
        ];

        foreach ($dashboardDates as $dashboardDate) {
            foreach ($replacementDnis as $dni) {
                $person = Personnel::where('dni', $dni)->first();

                if (!$person) {
                    continue;
                }

                Attendance::updateOrCreate(
                    [
                        'personnel_id' => $person->id,
                        'date' => $dashboardDate['date'],
                        'shift_id' => $dashboardDate['shift_id'],
                        'type' => 'Ingreso',
                    ],
                    [
                        'time' => $this->attendanceTime($dashboardDate['shift_id']),
                        'status' => 'Presente',
                        'notes' => 'Personal disponible para reemplazos del dashboard.',
                    ]
                );
            }
        }
    }

    private function attendanceTime(int $shiftId): string
    {
        $shift = \App\Models\Shift::find($shiftId);

        if (!$shift) {
            return Carbon::parse('08:00:00')->format('H:i:s');
        }

        return Carbon::parse($shift->start_time)
            ->addMinutes(10)
            ->format('H:i:s');
    }
}
