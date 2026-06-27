<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleChange;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Schedule;
use App\Models\ScheduleDaily;
use App\Models\Reason;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Personnel;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
class ScheduleChangeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $changes = ScheduleChange::with([
                'schedule.zone',
                'reason',
                'user'
            ])->select('schedule_changes.*');

            if ($request->filled('start_date')) {
                $changes->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $changes->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('change_type')) {
                $changes->where('change_type', $request->change_type);
            }

            return DataTables::of($changes)
                ->addColumn('date_format', function ($change) {
                    return $change->created_at->format('d/m/Y H:i');
                })

                ->addColumn('type_badge', function ($change) {
                    $colors = [
                        'Turno' => 'warning',
                        'Vehículo' => 'info',
                        'Conductor' => 'primary',
                        'Ayudantes' => 'success',
                    ];

                    $color = $colors[$change->change_type] ?? 'secondary';

                    return '<span class="badge badge-' . $color . ' badge-custom">'
                        . ($change->change_type ?? 'Cambio') .
                    '</span>';
                })

                ->addColumn('previous_value_format', function ($change) {
                    return $change->previous_value ?? 'Sin registro';
                })

                ->addColumn('new_value_format', function ($change) {
                    return $change->new_value ?? 'Sin registro';
                })

                ->addColumn('user_name', function ($change) {
                    return $change->user?->name ?? 'Administrador';
                })

                ->addColumn('schedule_info', function ($change) {
                    return 'Prog. #' . $change->schedule_id .
                        '<br><small class="text-muted">' .
                        ($change->schedule?->zone?->name ?? 'Sin zona') .
                        '</small>';
                })

                ->addColumn('actions', function ($change) {
                    $show = '<button
                                class="btn btn-sm btn-info btn-show"
                                id="' . $change->id . '"
                                title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>';

                    $delete = '<button
                                type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-url="' . route('admin.changes.destroy', $change->id) . '"
                                title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>';

                    return $show . ' ' . $delete;
                })

                ->rawColumns([
                    'type_badge',
                    'schedule_info',
                    'actions'
                ])

                ->make(true);
        }

        return view('admin.changes.index');
    }

    public function show($id)
    {
        $change = ScheduleChange::with([
            'schedule.zone',
            'reason',
            'user'
        ])->findOrFail($id);

        return view('admin.changes.show', compact('change'));
    }

    public function createMass()
    {
        $zones = Zone::where('status', true)->orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'Activo')->orderBy('plate')->get();
        $reasons = Reason::orderBy('name')->get();

        $drivers = Personnel::whereHas('type', function ($q) {
                $q->where('name', 'Conductor');
            })
            ->where('status', 'Activo')
            ->orderBy('names')
            ->get();

        $helpers = Personnel::whereHas('type', function ($q) {
                $q->where('name', 'Ayudante');
            })
            ->where('status', 'Activo')
            ->orderBy('names')
            ->get();

        return view(
            'admin.changes.mass',
            compact(
                'zones',
                'shifts',
                'vehicles',
                'drivers',
                'helpers',
                'reasons'
            )
        );
    }

    public function storeMass(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'change_type' => 'required|in:Turno,Vehículo,Conductor,Ayudantes',
            'reason_id' => 'required|exists:reasons,id',
            'previous_value_id' => 'required',
            'new_value_id' => 'required',
            'description' => 'nullable|max:500',
        ]);

        return DB::transaction(function () use ($request) {

            $query = ScheduleDaily::with([
                'schedule',
                'shift',
                'vehicle',
                'driver',
                'helpers'
            ])
                ->whereBetween('date', [$request->start_date, $request->end_date]);

            if ($request->filled('zone_id')) {
                $query->whereHas('schedule', function ($q) use ($request) {
                    $q->where('zone_id', $request->zone_id);
                });
            }

            $dailies = $query->get();

            $affected = 0;

            foreach ($dailies as $daily) {

                if ($request->change_type === 'Turno') {
                    if ($daily->shift_id != $request->previous_value_id) {
                        continue;
                    }

                    $old = Shift::find($daily->shift_id);
                    $new = Shift::find($request->new_value_id);

                    $daily->update([
                        'shift_id' => $request->new_value_id,
                        'status' => 'reprogramado',
                    ]);

                    $daily->schedule?->update([
                        'status' => 'reprogramado',
                        'shift_id' => $request->new_value_id,
                    ]);

                    $this->saveMassChange($daily, $request, $old?->name, $new?->name);
                    $affected++;
                }

                if ($request->change_type === 'Vehículo') {
                    if ($daily->vehicle_id != $request->previous_value_id) {
                        continue;
                    }

                    $old = Vehicle::find($daily->vehicle_id);
                    $new = Vehicle::find($request->new_value_id);

                    $daily->update([
                        'vehicle_id' => $request->new_value_id,
                        'status' => 'reprogramado',
                    ]);

                    $daily->schedule?->update([
                        'status' => 'reprogramado',
                        'vehicle_id' => $request->new_value_id,
                    ]);

                    $this->saveMassChange($daily, $request, $old?->plate, $new?->plate);
                    $affected++;
                }

                if ($request->change_type === 'Conductor') {
                    if ($daily->driver_id != $request->previous_value_id) {
                        continue;
                    }

                    $old = Personnel::find($daily->driver_id);
                    $new = Personnel::find($request->new_value_id);

                    $daily->update([
                        'driver_id' => $request->new_value_id,
                        'status' => 'reprogramado',
                    ]);

                    $daily->schedule?->update([
                        'status' => 'reprogramado',
                        'driver_id' => $request->new_value_id,
                    ]);

                    $this->saveMassChange(
                        $daily,
                        $request,
                        $old ? $old->names . ' ' . $old->lastnames : null,
                        $new ? $new->names . ' ' . $new->lastnames : null
                    );

                    $affected++;
                }

                if ($request->change_type === 'Ayudantes') {
                    if (!$daily->helpers->contains('id', $request->previous_value_id)) {
                        continue;
                    }

                    $old = Personnel::find($request->previous_value_id);
                    $new = Personnel::find($request->new_value_id);

                    $helperIds = $daily->helpers->pluck('id')->toArray();

                    $helperIds = array_map(function ($id) use ($request) {
                        return $id == $request->previous_value_id
                            ? $request->new_value_id
                            : $id;
                    }, $helperIds);

                    $daily->helpers()->sync($helperIds);

                    if ($daily->schedule) {
                        $daily->schedule->helpers()->sync($helperIds);
                        $daily->schedule->update([
                            'status' => 'reprogramado',
                        ]);
                    }

                    $daily->update([
                        'status' => 'reprogramado',
                    ]);

                    $this->saveMassChange(
                        $daily,
                        $request,
                        $old ? $old->names . ' ' . $old->lastnames : null,
                        $new ? $new->names . ' ' . $new->lastnames : null
                    );

                    $affected++;
                }
            }

            if ($affected === 0) {
                return response()->json([
                    'message' => 'No se encontraron programaciones que coincidan con el cambio solicitado.'
                ], 422);
            }

            return response()->json([
                'message' => 'Cambio masivo aplicado correctamente. Registros afectados: ' . $affected
            ]);
        });
    }

    private function saveMassChange($daily, $request, $previousValue, $newValue)
    {
        ScheduleChange::create([
            'schedule_id' => $daily->schedule_id,
            'reason_id' => $request->reason_id,
            'user_id' => auth()->id(),
            'change_type' => $request->change_type,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'description' => $request->description,

            'old_shift' => $request->change_type === 'Turno' ? $previousValue : null,
            'new_shift' => $request->change_type === 'Turno' ? $newValue : null,

            'old_vehicle' => $request->change_type === 'Vehículo' ? $previousValue : null,
            'new_vehicle' => $request->change_type === 'Vehículo' ? $newValue : null,

            'old_driver' => $request->change_type === 'Conductor' ? $previousValue : null,
            'new_driver' => $request->change_type === 'Conductor' ? $newValue : null,

            'old_helpers' => $request->change_type === 'Ayudantes' ? $previousValue : null,
            'new_helpers' => $request->change_type === 'Ayudantes' ? $newValue : null,
        ]);
    }

    public function destroy($id)
    {
        try {
            $change = ScheduleChange::findOrFail($id);
            $change->delete();

            return response()->json([
                'message' => 'Cambio eliminado correctamente.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}