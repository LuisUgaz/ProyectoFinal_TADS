<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Personnel;
use App\Models\ScheduleChange;
use App\Models\ScheduleDaily;
use App\Models\Shift;
use App\Models\Vacation;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $shiftId = $request->input('shift_id');
        $shifts = Shift::orderBy('name')->get();

        $dashboard = $this->buildDashboard($date, $shiftId);

        return view('admin.index', compact('dashboard', 'date', 'shiftId', 'shifts'));
    }

    public function showDaily(ScheduleDaily $daily)
    {
        $daily->load([
            'schedule.zone',
            'schedule.personnelGroup',
            'shift',
            'vehicle',
            'driver.type',
            'helpers.type',
        ]);

        $date = $daily->date->toDateString();
        $currentHelpers = $daily->helpers->values();

        return response()->json([
            'daily' => [
                'id' => $daily->id,
                'date' => $daily->date->format('d/m/Y'),
                'zone' => $daily->schedule?->zone?->name ?? 'Sin zona',
                'group' => $daily->schedule?->personnelGroup?->name ?? 'Sin grupo',
                'shift_id' => $daily->shift_id,
                'vehicle_id' => $daily->vehicle_id,
                'driver_id' => $daily->driver_id,
            ],
            'shifts' => Shift::orderBy('name')->get(['id', 'name']),
            'vehicles' => $this->availableVehicles($date, $daily->shift_id, $daily->id, $daily->vehicle_id),
            'driver' => $this->personnelPayload($daily->driver),
            'helpers' => $currentHelpers->map(fn ($helper) => $this->personnelPayload($helper))->values(),
            'available_drivers' => $this->availablePersonnel('Conductor', $date, $daily->shift_id, $daily->id),
            'available_helpers' => $this->availablePersonnel('Ayudante', $date, $daily->shift_id, $daily->id),
        ]);
    }

    public function updateDaily(Request $request, ScheduleDaily $daily)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:personnels,id',
            'helper_ids' => 'array',
            'helper_ids.*' => 'nullable|exists:personnels,id',
        ]);

        return DB::transaction(function () use ($request, $daily) {
            $daily->load(['shift', 'vehicle', 'driver', 'helpers']);

            $oldShift = $daily->shift?->name;
            $newShift = Shift::find($request->shift_id)?->name;

            $oldVehicle = $daily->vehicle?->plate;
            $newVehicle = Vehicle::find($request->vehicle_id)?->plate;

            $oldDriver = $daily->driver
                ? $daily->driver->names . ' ' . $daily->driver->lastnames
                : null;

            $newDriverModel = Personnel::find($request->driver_id);
            $newDriver = $newDriverModel
                ? $newDriverModel->names . ' ' . $newDriverModel->lastnames
                : null;

            $oldHelpers = $daily->helpers
                ->map(fn ($helper) => $helper->names . ' ' . $helper->lastnames)
                ->implode(', ');

            $helperIds = collect($request->input('helper_ids', []))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (in_array((string) $request->driver_id, array_map('strval', $helperIds), true)) {
                return response()->json([
                    'message' => 'El conductor no puede estar registrado tambien como ayudante.',
                ], 422);
            }

            $newHelpers = Personnel::whereIn('id', $helperIds)
                ->get()
                ->map(fn ($helper) => $helper->names . ' ' . $helper->lastnames)
                ->implode(', ');

            $daily->update([
                'shift_id' => $request->shift_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'status' => 'reprogramado',
            ]);

            $daily->helpers()->sync($helperIds);

            $baseData = [
                'schedule_id' => $daily->schedule_id,
                'reason_id' => null,
                'user_id' => auth()->id(),
                'description' => 'Cambio realizado desde el dashboard diario.',
            ];

            if ($oldShift !== $newShift) {
                $this->saveChange($baseData, 'Turno', $oldShift, $newShift, 'old_shift', 'new_shift');
            }

            if ($oldVehicle !== $newVehicle) {
                $this->saveChange($baseData, 'Vehiculo', $oldVehicle, $newVehicle, 'old_vehicle', 'new_vehicle');
            }

            if ($oldDriver !== $newDriver) {
                $this->saveChange($baseData, 'Conductor', $oldDriver, $newDriver, 'old_driver', 'new_driver');
            }

            if ($oldHelpers !== $newHelpers) {
                $this->saveChange($baseData, 'Ayudantes', $oldHelpers, $newHelpers, 'old_helpers', 'new_helpers');
            }

            return response()->json([
                'message' => 'Programacion diaria actualizada correctamente.',
            ]);
        });
    }

    private function buildDashboard(string $date, $shiftId): array
    {
        $dailies = ScheduleDaily::with([
            'schedule.zone',
            'schedule.personnelGroup',
            'shift',
            'vehicle',
            'driver',
            'helpers',
        ])
            ->whereDate('date', $date)
            ->when($shiftId, fn ($query) => $query->where('shift_id', $shiftId))
            ->orderBy('date')
            ->get();

        $cards = $dailies->map(function (ScheduleDaily $daily) use ($date) {
            $assigned = collect([$daily->driver])
                ->filter()
                ->merge($daily->helpers)
                ->unique('id')
                ->values();

            $assignedIds = $assigned->pluck('id')->all();

            $presentIds = Attendance::whereDate('date', $date)
                ->where('shift_id', $daily->shift_id)
                ->where('status', 'Presente')
                ->whereIn('personnel_id', $assignedIds)
                ->pluck('personnel_id')
                ->unique()
                ->all();

            $missing = $assigned
                ->reject(fn ($person) => in_array($person->id, $presentIds))
                ->values();

            $presentCount = count($presentIds);
            $missingCount = $missing->count();
            $isComplete = $assigned->count() > 0 && $missingCount === 0;

            return [
                'id' => $daily->id,
                'zone' => $daily->schedule?->zone?->name ?? 'Sin zona',
                'group' => $daily->schedule?->personnelGroup?->name ?? 'Sin grupo',
                'shift' => $daily->shift?->name ?? 'Sin turno',
                'vehicle' => $daily->vehicle?->plate ?? 'Sin vehiculo',
                'present_count' => $presentCount,
                'missing_count' => $missingCount,
                'assigned_count' => $assigned->count(),
                'is_complete' => $isComplete,
                'missing_names' => $missing
                    ->map(fn ($person) => $person->names . ' ' . $person->lastnames)
                    ->implode(', '),
            ];
        })->values();

        return [
            'total' => $cards->count(),
            'completed' => $cards->where('is_complete', true)->count(),
            'incompleted' => $cards->where('is_complete', false)->count(),
            'missing_personnel' => $cards->sum('missing_count'),
            'cards' => $cards,
        ];
    }

    private function availablePersonnel(string $roleName, string $date, int $shiftId, int $currentDailyId)
    {
        $assignedIds = ScheduleDaily::whereDate('date', $date)
            ->where('shift_id', $shiftId)
            ->where('id', '!=', $currentDailyId)
            ->with('helpers')
            ->get()
            ->flatMap(function (ScheduleDaily $daily) {
                return collect([$daily->driver_id])->merge($daily->helpers->pluck('id'));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return Personnel::whereHas('type', fn ($query) => $query->where('name', $roleName))
            ->where('status', 'Activo')
            ->whereNotIn('id', $assignedIds)
            ->whereHas('contracts', function ($query) use ($date) {
                $query->where('is_active', true)
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($subQuery) use ($date) {
                        $subQuery->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            })
            ->whereHas('attendances', function ($query) use ($date, $shiftId) {
                $query->whereDate('date', $date)
                    ->where('shift_id', $shiftId)
                    ->where('status', 'Presente');
            })
            ->whereDoesntHave('vacations', function ($query) use ($date) {
                $query->where('status', 'Aprobada')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
            })
            ->orderBy('names')
            ->get()
            ->map(fn ($person) => $this->personnelPayload($person))
            ->values();
    }

    private function availableVehicles(string $date, int $shiftId, int $currentDailyId, int $currentVehicleId)
    {
        $busyVehicleIds = ScheduleDaily::whereDate('date', $date)
            ->where('shift_id', $shiftId)
            ->where('id', '!=', $currentDailyId)
            ->pluck('vehicle_id')
            ->filter()
            ->unique()
            ->all();

        return Vehicle::where('status', 'Activo')
            ->where(function ($query) use ($busyVehicleIds, $currentVehicleId) {
                $query->whereNotIn('id', $busyVehicleIds)
                    ->orWhere('id', $currentVehicleId);
            })
            ->orderBy('plate')
            ->get(['id', 'plate', 'name'])
            ->map(fn ($vehicle) => [
                'id' => $vehicle->id,
                'name' => trim($vehicle->plate . ' - ' . ($vehicle->name ?? 'Vehiculo')),
            ])
            ->values();
    }

    private function personnelPayload(?Personnel $person): ?array
    {
        if (!$person) {
            return null;
        }

        return [
            'id' => $person->id,
            'name' => $person->names . ' ' . $person->lastnames,
            'role' => $person->type?->name ?? 'Personal',
        ];
    }

    private function saveChange(
        array $baseData,
        string $type,
        ?string $previous,
        ?string $new,
        string $oldColumn,
        string $newColumn
    ): void {
        ScheduleChange::create(array_merge($baseData, [
            'change_type' => $type,
            'previous_value' => $previous,
            'new_value' => $new,
            $oldColumn => $previous,
            $newColumn => $new,
        ]));
    }
}
