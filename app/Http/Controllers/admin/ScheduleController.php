<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\PersonnelGroup;
use App\Models\Personnel;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\Holiday;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $schedules = Schedule::with(['personnelGroup', 'zone', 'shift', 'vehicle', 'driver', 'helpers'])
                ->select('schedules.*');

            return DataTables::of($schedules)
                ->addColumn('group_name', fn($s) => $s->personnelGroup?->name)
                ->addColumn('zone_name', fn($s) => $s->zone?->name)
                ->addColumn('shift_name', fn($s) => $s->shift?->name)
                ->addColumn('vehicle_plate', fn($s) => $s->vehicle?->plate)
                ->addColumn('driver_name', fn($s) => $s->driver?->names . ' ' . $s->driver?->lastnames)
                ->addColumn('helpers_names', function($s) {
                    return $s->helpers->map(fn($h) => $h->names . ' ' . $h->lastnames)->implode('<br>');
                })
                ->addColumn('date_range', fn($s) => $s->start_date->format('d/m/Y') . ' - ' . $s->end_date->format('d/m/Y'))
                ->addColumn('status_badge', function($s) {
                    $badges = [
                        'scheduled' => '<span class="badge badge-primary">Programada</span>',
                        'in_progress' => '<span class="badge badge-info">En Curso</span>',
                        'completed' => '<span class="badge badge-success">Finalizada</span>',
                        'cancelled' => '<span class="badge badge-danger">Cancelada</span>',
                    ];
                    return $badges[$s->status] ?? $s->status;
                })
                ->addColumn('actions', function($s) {
                    return '
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info btn-history" data-id="'.$s->id.'" title="Ver Historial"><i class="fas fa-history"></i></button>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="'.$s->id.'" title="Modificar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="'.$s->id.'" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </div>
                    ';
                })
                ->rawColumns(['status_badge', 'actions', 'helpers_names'])
                ->make(true);
        }

        $groups = PersonnelGroup::where('status', true)->get();
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $personnels = Personnel::all();

        return view('admin.schedules.index', compact('groups', 'zones', 'shifts', 'vehicles', 'personnels'));
    }

    public function create()
    {
        $groups = PersonnelGroup::where('status', true)->get();
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $personnels = Personnel::all(); // En un futuro filtrar por disponibilidad inicial

        return view('admin.schedules.create', compact('groups', 'zones', 'shifts', 'vehicles', 'personnels'));
    }

    /**
     * Valida la disponibilidad de conductores, ayudantes y vehículos.
     * Invocado vía AJAX antes de guardar.
     */
    public function validateAvailability(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $driverId = $request->driver_id;
        $helperIds = $request->helper_ids ?? [];
        $vehicleId = $request->vehicle_id;
        $workdays = $request->workdays ?? []; // Días de la semana seleccionados

        $inconsistencies = [];

        // 1. Validar Feriados (Si la programación cae en un feriado activo)
        $holidays = Holiday::where('status', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        foreach ($holidays as $holiday) {
            // Podríamos ser más específicos según los días de la semana, pero por ahora notificamos
            $inconsistencies[] = "El periodo incluye un feriado: " . $holiday->description . " (" . $holiday->date->format('d/m/Y') . ")";
        }

        // 2. Validar Vacaciones del Conductor y Ayudantes
        $allPersonnelIds = array_merge([$driverId], $helperIds);
        foreach ($allPersonnelIds as $pId) {
            $personnel = Personnel::find($pId);
            if (!$personnel) continue;

            $vacations = Vacation::where('personnel_id', $pId)
                ->where('status', 'Aprobada') // Coincide con el enum en español
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
                })->first();

            if ($vacations) {
                $inconsistencies[] = "El personal {$personnel->names} {$personnel->lastnames} tiene vacaciones en este periodo.";
            }
        }

        // 3. Validar otras programaciones activas (Cruces de horario/días)
        // Por ahora una validación simple de fechas, se puede refinar con los 'workdays'
        $existingSchedules = Schedule::whereIn('status', ['scheduled', 'in_progress'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate]);
            })->get();

        foreach ($existingSchedules as $es) {
            if ($es->driver_id == $driverId) {
                $inconsistencies[] = "El conductor ya está asignado a otra programación en este periodo.";
            }
            if ($es->vehicle_id == $vehicleId) {
                $inconsistencies[] = "El vehículo ya está asignado a otra programación en este periodo.";
            }
            // Validar ayudantes
            foreach ($helperIds as $hId) {
                if ($es->helpers()->where('personnel_id', $hId)->exists()) {
                    $h = Personnel::find($hId);
                    $inconsistencies[] = "El ayudante {$h->names} ya está asignado a otra programación en este periodo.";
                }
            }
        }

        return response()->json([
            'valid' => count($inconsistencies) === 0,
            'inconsistencies' => $inconsistencies
        ]);
    }

    public function store(Request $request)
    {
        // Validación de datos básica
        $validated = $request->validate([
            'personnel_group_id' => 'required|exists:personnel_groups,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'zone_id' => 'required',
            'shift_id' => 'required',
            'vehicle_id' => 'required',
            'driver_id' => 'required',
        ]);

        $schedule = Schedule::create($request->all());

        if ($request->has('helper_ids')) {
            $schedule->helpers()->attach($request->helper_ids);
        }

        if ($request->has('workdays')) {
            foreach ($request->workdays as $day) {
                $schedule->workdays()->create(['day' => $day]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Programación creada correctamente.');
    }

    public function show($id)
    {
        $schedule = Schedule::with(['helpers', 'workdays'])->findOrFail($id);
        return response()->json($schedule);
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $validated = $request->validate([
            'personnel_group_id' => 'required|exists:personnel_groups,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'zone_id' => 'required',
            'shift_id' => 'required',
            'vehicle_id' => 'required',
            'driver_id' => 'required',
        ]);

        $schedule->update($request->all());

        // Actualizar ayudantes
        if ($request->has('helper_ids')) {
            $schedule->helpers()->sync($request->helper_ids);
        } else {
            $schedule->helpers()->detach();
        }

        // Actualizar días
        $schedule->workdays()->delete();
        if ($request->has('workdays')) {
            foreach ($request->workdays as $day) {
                $schedule->workdays()->create(['day' => $day]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}
