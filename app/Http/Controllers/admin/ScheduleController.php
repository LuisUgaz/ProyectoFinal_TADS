<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleDaily;
use App\Models\PersonnelGroup;
use App\Models\Personnel;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\Holiday;
use App\Models\Vacation;
use App\Models\ScheduleChange;
use App\Models\Reason;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dailies = ScheduleDaily::with([
                'schedule.zone',
                'schedule.personnelGroup',
                'shift',
                'vehicle',
                'driver',
                'helpers'
            ])->select('schedule_dailies.*');

            return DataTables::of($dailies)
                ->addColumn('date_format', fn($d) => $d->date->format('d/m/Y'))

                ->addColumn('group_name', fn($d) => $d->schedule?->personnelGroup?->name ?? 'N/A')

                ->addColumn('zone_name', fn($d) => $d->schedule?->zone?->name ?? 'N/A')

                ->addColumn('shift_name', fn($d) => $d->shift?->name ?? 'N/A')

                ->addColumn('vehicle_plate', fn($d) => $d->vehicle?->plate ?? 'N/A')

                ->addColumn('driver_name', function ($d) {
                    return $d->driver
                        ? $d->driver->names . ' ' . $d->driver->lastnames
                        : 'N/A';
                })

                ->addColumn('helpers_names', function ($d) {
                    return $d->helpers
                        ->map(fn($h) => $h->names . ' ' . $h->lastnames)
                        ->implode('<br>');
                })

                ->addColumn('status_badge', function ($d) {
                    $badges = [
                        'pendiente' => '<span class="badge badge-secondary badge-custom">Pendiente</span>',
                        'completado' => '<span class="badge badge-success badge-custom">Completado</span>',
                        'reprogramado' => '<span class="badge badge-warning badge-custom">Reprogramado</span>',
                        'cancelado' => '<span class="badge badge-danger badge-custom">Cancelado</span>',
                    ];

                    return $badges[$d->status] ?? '<span class="badge badge-info badge-custom">' . $d->status . '</span>';
                })

                ->addColumn('actions', function ($d) {

                    $history = '
                        <button class="btn btn-sm btn-secondary btn-history" data-id="'.$d->schedule_id.'" title="Ver historial">
                            <i class="fas fa-history"></i>
                        </button>
                    ';

                    if ($d->status === 'completado') {
                        return '<div class="btn-group">' . $history . '</div>';
                    }

                    $edit = '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="'.$d->schedule_id.'" title="Modificar programación">
                            <i class="fas fa-pen"></i>
                        </button>
                    ';

                    $finish = '
                        <button class="btn btn-sm btn-success btn-finish-daily" data-id="'.$d->id.'" title="Finalizar programación">
                            <i class="fas fa-check"></i>
                        </button>
                    ';

                    $delete = '
                        <button class="btn btn-sm btn-danger btn-delete-daily" data-id="'.$d->id.'" title="Eliminar programación">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';

                    return '<div class="btn-group">' . $history . $edit . $finish . $delete . '</div>';
                })

                ->rawColumns(['helpers_names', 'status_badge', 'actions'])
                ->make(true);
        }

        $groups = PersonnelGroup::where('status', true)->get();
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $personnels = Personnel::all();
        $reasons = Reason::orderBy('name')->get();

        return view(
            'admin.schedules.index',
            compact(
                'groups',
                'zones',
                'shifts',
                'vehicles',
                'personnels',
                'reasons'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'personnel_group_id' => 'required|exists:personnel_groups,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'zone_id' => 'required',
            'shift_id' => 'required',
            'vehicle_id' => 'required',
            'driver_id' => 'required',
        ]);

        $availability = $this->checkAvailability($request);
        if (!$availability['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Existen conflictos bloqueantes en la programación:',
                'errors' => $availability['errors']
            ], 422);
        }

        return DB::transaction(function() use ($request) {
            $schedule = Schedule::create($request->all());

            if ($request->has('helper_ids')) {
                $schedule->helpers()->attach($request->helper_ids);
            }

            if ($request->has('workdays')) {
                foreach ($request->workdays as $day) {
                    $schedule->workdays()->create(['day' => $day]);
                }
            }

            $this->generateDailyRecords($schedule);

            return response()->json(['success' => true]);
        });
    }

    public function previewMass(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $shiftId = $request->shift_id;

        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $groups = PersonnelGroup::where('status', true)
            ->when($shiftId, fn($q) => $q->where('shift_id', $shiftId))
            ->with(['zone', 'shift', 'vehicle', 'driver', 'helpers.personnel', 'workdays'])
            ->get();

        $preview = [];

        $personnels = Personnel::where('status', 'Activo')
            ->whereHas('contracts', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        foreach ($groups as $group) {
            $tempRequest = new Request([
                'personnel_group_id' => $group->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'zone_id' => $group->zone_id,
                'shift_id' => $group->shift_id,
                'vehicle_id' => $group->vehicle_id,
                'driver_id' => $group->driver_id,
                'helper_ids' => $group->helpers->pluck('personnel_id')->toArray(),
            ]);

            $availability = $this->checkAvailability($tempRequest);

            $preview[] = [
                'group' => $group,
                'availability' => $availability
            ];
        }

        return response()->json([
            'holidays' => $holidays,
            'preview' => $preview,
            'personnels' => $personnels
        ]);
    }

    public function storeMass(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'groups' => 'required|array|min:1',
        ]);

        $data = $request->groups;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return DB::transaction(function () use ($data, $startDate, $endDate) {

            foreach ($data as $item) {

                $tempRequest = new Request([
                    'personnel_group_id' => $item['group_id'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'zone_id' => $item['zone_id'],
                    'shift_id' => $item['shift_id'],
                    'vehicle_id' => $item['vehicle_id'],
                    'driver_id' => $item['driver_id'],
                    'helper_ids' => $item['helper_ids'] ?? [],
                ]);

                $availability = $this->checkAvailability($tempRequest);

                if (!$availability['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Existen conflictos en uno o más grupos seleccionados.',
                        'errors' => $availability['errors']
                    ], 422);
                }

                $schedule = Schedule::create([
                    'personnel_group_id' => $item['group_id'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'zone_id' => $item['zone_id'],
                    'shift_id' => $item['shift_id'],
                    'vehicle_id' => $item['vehicle_id'],
                    'driver_id' => $item['driver_id'],
                    'status' => 'scheduled'
                ]);

                if (!empty($item['helper_ids'])) {
                    $schedule->helpers()->attach($item['helper_ids']);
                }

                $group = PersonnelGroup::with('workdays')->find($item['group_id']);

                if ($group) {
                    foreach ($group->workdays as $workday) {
                        $schedule->workdays()->create([
                            'day' => $workday->day
                        ]);
                    }
                }

                $this->generateDailyRecords($schedule);
            }

            return response()->json(['success' => true]);
        });
    }

    private function generateDailyRecords(Schedule $schedule)
    {
        $startDate = $schedule->start_date;
        $endDate = $schedule->end_date;
        $currentDate = $startDate->copy();

        $workdays = $schedule->workdays()
            ->pluck('day')
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

        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        while ($currentDate->lte($endDate)) {

            $dayName = $currentDate->format('l');
            $shortDay = $dayMap[$dayName] ?? null;

            if (
                in_array($shortDay, $workdays) &&
                !in_array($currentDate->format('Y-m-d'), $holidays)
            ) {
                $daily = $schedule->dailies()->create([
                    'date' => $currentDate->format('Y-m-d'),
                    'shift_id' => $schedule->shift_id,
                    'vehicle_id' => $schedule->vehicle_id,
                    'driver_id' => $schedule->driver_id,
                    'status' => 'pendiente'
                ]);

                $daily->helpers()->attach($schedule->helpers->pluck('id'));
            }

            $currentDate->addDay();
        }
    }

    public function show($id)
    {
        $schedule = Schedule::with(['helpers', 'workdays', 'dailies.shift', 'dailies.vehicle', 'dailies.driver', 'dailies.helpers'])->findOrFail($id);
        return response()->json($schedule);
    }

    public function edit($id)
    {
        $schedule = Schedule::with(['helpers', 'workdays', 'shift', 'vehicle', 'driver'])->findOrFail($id);
        
        $eligibleDrivers = $this->getEligiblePersonnelByRole('Conductor');
        $eligibleHelpers = $this->getEligiblePersonnelByRole('Ayudante');
        
        return response()->json([
            'schedule' => $schedule,
            'current_shift_name' => $schedule->shift?->name,
            'current_vehicle_plate' => $schedule->vehicle?->plate,
            'eligibleDrivers' => $eligibleDrivers,
            'eligibleHelpers' => $eligibleHelpers,
            'current_driver' => $schedule->driver,
            'current_helpers' => $schedule->helpers
        ]);
    }

    private function getEligiblePersonnelByRole($roleName)
    {
        return Personnel::whereHas('type', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            })
            ->where('status', 'Activo')
            ->whereHas('contracts', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('names')
            ->get();
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::with(['shift', 'vehicle', 'driver', 'helpers'])
            ->findOrFail($id);

        $request->validate([
            'shift_id' => 'required',
            'vehicle_id' => 'required',
            'driver_id' => 'required',
            'reason_id' => 'required|exists:reasons,id',
            'reason' => 'nullable|max:500'
        ]);

        return DB::transaction(function () use ($request, $schedule) {

            $reason = Reason::findOrFail($request->reason_id);

            $oldShift = $schedule->shift?->name;
            $newShift = Shift::find($request->shift_id)?->name;

            $oldVehicle = $schedule->vehicle?->plate;
            $newVehicle = Vehicle::find($request->vehicle_id)?->plate;

            $oldDriver = $schedule->driver
                ? $schedule->driver->names . ' ' . $schedule->driver->lastnames
                : null;

            $newDriverModel = Personnel::find($request->driver_id);

            $newDriver = $newDriverModel
                ? $newDriverModel->names . ' ' . $newDriverModel->lastnames
                : null;

            $oldHelpers = $schedule->helpers
                ->map(fn($helper) => $helper->names . ' ' . $helper->lastnames)
                ->implode(', ');

            $newHelperIds = array_filter($request->helper_ids ?? []);

            $newHelpers = Personnel::whereIn('id', $newHelperIds)
                ->get()
                ->map(fn($helper) => $helper->names . ' ' . $helper->lastnames)
                ->implode(', ');

            $baseData = [
                'schedule_id' => $schedule->id,
                'reason_id' => $reason->id,
                'user_id' => auth()->id(),
                'description' => $request->reason,
            ];

            if ($oldShift !== $newShift) {
                ScheduleChange::create(array_merge($baseData, [
                    'change_type' => 'Turno',
                    'previous_value' => $oldShift,
                    'new_value' => $newShift,
                    'old_shift' => $oldShift,
                    'new_shift' => $newShift,
                ]));
            }

            if ($oldVehicle !== $newVehicle) {
                ScheduleChange::create(array_merge($baseData, [
                    'change_type' => 'Vehículo',
                    'previous_value' => $oldVehicle,
                    'new_value' => $newVehicle,
                    'old_vehicle' => $oldVehicle,
                    'new_vehicle' => $newVehicle,
                ]));
            }

            if ($oldDriver !== $newDriver) {
                ScheduleChange::create(array_merge($baseData, [
                    'change_type' => 'Conductor',
                    'previous_value' => $oldDriver,
                    'new_value' => $newDriver,
                    'old_driver' => $oldDriver,
                    'new_driver' => $newDriver,
                ]));
            }

            if ($oldHelpers !== $newHelpers) {
                ScheduleChange::create(array_merge($baseData, [
                    'change_type' => 'Ayudantes',
                    'previous_value' => $oldHelpers,
                    'new_value' => $newHelpers,
                    'old_helpers' => $oldHelpers,
                    'new_helpers' => $newHelpers,
                ]));
            }

            $schedule->update([
                'shift_id' => $request->shift_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'status' => 'reprogramado',
                'notes' => $request->reason
            ]);

            $schedule->helpers()->sync($newHelperIds);

            $futureDailies = $schedule->dailies()
                ->whereIn('status', ['pendiente', 'reprogramado'])
                ->where('date', '>=', now()->toDateString())
                ->get();

            foreach ($futureDailies as $daily) {
                $daily->update([
                    'shift_id' => $request->shift_id,
                    'vehicle_id' => $request->vehicle_id,
                    'driver_id' => $request->driver_id,
                    'status' => 'reprogramado'
                ]);

                $daily->helpers()->sync($newHelperIds);
            }

            return response()->json(['success' => true]);
        });
    }

    public function updateDailyStatus(Request $request, $id)
    {
        $daily = ScheduleDaily::findOrFail($id);
        $daily->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }

    public function updateDailyRecord(Request $request, $id)
    {
        $daily = ScheduleDaily::findOrFail($id);
        $daily->update($request->only(['shift_id', 'vehicle_id', 'driver_id', 'notes']));
        
        if ($request->has('helper_ids')) {
            $daily->helpers()->sync($request->helper_ids);
        }

        return response()->json(['success' => true]);
    }

    private function checkAvailability(Request $request, $excludeId = null)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $groupId = $request->personnel_group_id;
        $driverId = $request->driver_id;
        $helperIds = array_filter($request->helper_ids ?? []);
        $vehicleId = $request->vehicle_id;

        $errors = [];
        $warnings = [];

        if (!$request->filled('workdays')) {
            $group = PersonnelGroup::with('workdays')->find($groupId);
            $workdays = $group ? $group->workdays->pluck('day')->toArray() : [];
        } else {
            $workdays = $request->workdays;
        }

        if (empty($workdays)) {
            $errors[] = 'Debe seleccionar al menos un día de trabajo.';
        }

        if (count($helperIds) !== count(array_unique($helperIds))) {
            $errors[] = 'No puede seleccionar el mismo ayudante más de una vez.';
        }

        if (in_array($driverId, $helperIds)) {
            $errors[] = 'El conductor no puede estar registrado también como ayudante.';
        }

        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach ($holidays as $holiday) {
            $warnings[] = 'El periodo incluye un feriado activo: '
                . $holiday->description . ' (' . $holiday->date->format('d/m/Y') . ').';
        }

        $allPersonnelIds = array_filter(array_unique(array_merge([$driverId], $helperIds)));

        foreach ($allPersonnelIds as $personnelId) {
            $personnel = Personnel::with('type')->find($personnelId);

            if (!$personnel) {
                $errors[] = 'Uno de los trabajadores seleccionados no existe.';
                continue;
            }

            if ($personnel->status !== 'Activo') {
                $errors[] = "El personal {$personnel->names} {$personnel->lastnames} no está activo.";
            }

            $hasActiveContract = $personnel->contracts()
                ->where('is_active', true)
                ->whereDate('start_date', '<=', $startDate)
                ->where(function ($q) use ($endDate) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $endDate);
                })
                ->exists();

            if (!$hasActiveContract) {
                $errors[] = "El personal {$personnel->names} {$personnel->lastnames} no tiene contrato activo para el rango seleccionado.";
            }

            $vacation = Vacation::where('personnel_id', $personnelId)
                ->where('status', 'Aprobada')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($sub) use ($startDate, $endDate) {
                            $sub->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->first();

            if ($vacation) {
                $errors[] = "El personal {$personnel->names} {$personnel->lastnames} tiene vacaciones aprobadas del "
                    . Carbon::parse($vacation->start_date)->format('d/m/Y')
                    . ' al '
                    . Carbon::parse($vacation->end_date)->format('d/m/Y') . '.';
            }
        }

        $activeScheduleIds = Schedule::whereIn('status', ['scheduled', 'in_progress', 'reprogramado'])
            ->when($excludeId, function ($q) use ($excludeId) {
                $q->where('id', '!=', $excludeId);
            })
            ->pluck('id');

        $existingDailies = ScheduleDaily::with(['schedule', 'helpers'])
            ->whereIn('schedule_id', $activeScheduleIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach ($existingDailies as $daily) {
            $schedule = $daily->schedule;

            if (!$schedule) {
                continue;
            }

            if ($schedule->zone_id == $request->zone_id && $daily->shift_id == $request->shift_id) {
                $errors[] = 'La zona y el turno ya tienen una programación registrada en el rango seleccionado.';
                break;
            }
        }

        foreach ($existingDailies as $daily) {
            if ($daily->vehicle_id == $vehicleId) {
                $errors[] = 'El vehículo seleccionado ya está asignado en una programación dentro del rango.';
                break;
            }
        }

        foreach ($existingDailies as $daily) {
            if ($daily->driver_id == $driverId) {
                $errors[] = 'El conductor seleccionado ya está asignado en una programación dentro del rango.';
                break;
            }

            $dailyHelperIds = $daily->helpers->pluck('id')->toArray();

            foreach ($helperIds as $helperId) {
                if (in_array($helperId, $dailyHelperIds)) {
                    $errors[] = 'Uno de los ayudantes seleccionados ya está asignado en una programación dentro del rango.';
                    break 2;
                }
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => array_values(array_unique($errors)),
            'warnings' => $warnings
        ];
    }

    public function validateAvailability(Request $request)
    {
        $result = $this->checkAvailability($request, $request->id);
        return response()->json($result);
    }

    public function history($id)
    {
        $schedule = Schedule::with([
            'changes.reason',
            'changes.user'
        ])->findOrFail($id);

        return view('admin.schedules.history', compact('schedule'));
    }

    public function finishDaily($id)
    {
        try {
            $daily = ScheduleDaily::findOrFail($id);

            $daily->update([
                'status' => 'completado'
            ]);

            $schedule = $daily->schedule;

            if ($schedule) {
                $hasPending = $schedule->dailies()
                    ->whereIn('status', ['pendiente', 'reprogramado'])
                    ->exists();

                if (!$hasPending) {
                    $schedule->update([
                        'status' => 'completed'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Día finalizado correctamente.'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function finish($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            $schedule->update([
                'status' => 'completed'
            ]);

            $schedule->dailies()
                ->whereIn('status', ['pendiente', 'reprogramado'])
                ->update([
                    'status' => 'completado'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Programación finalizada correctamente.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroyDaily($id)
    {
        try {
            $daily = ScheduleDaily::findOrFail($id);
            $schedule = $daily->schedule;

            $daily->helpers()->detach();
            $daily->delete();

            if ($schedule && $schedule->dailies()->count() === 0) {
                $schedule->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Programación eliminada correctamente.'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
