<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\PersonnelGroup;
use App\Models\PersonnelGroupDetail;
use App\Models\PersonnelType;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\PersonnelGroupWorkday;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PersonnelGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = PersonnelGroup::with([
            'zone',
            'shift',
            'vehicle',
            'driver',
            'helpers.personnel',
            'workdays'
        ])->get();

        if ($request->ajax()) {

            return DataTables::of($groups)

                ->addColumn('zone', function ($group) {

                    return $group->zone?->name;
                })

                ->addColumn('shift', function ($group) {

                    return $group->shift?->name;
                })

                ->addColumn('vehicle', function ($group) {

                    return $group->vehicle?->plate;
                })

                ->addColumn('driver', function ($group) {

                    if (!$group->driver) {
                        return '';
                    }

                    return $group->driver->names . ' ' .
                        $group->driver->lastnames;
                })

                ->addColumn('helpers', function ($group) {

                    return $group->helpers
                        ->map(function ($detail) {

                            return $detail->personnel->names . ' ' .
                                $detail->personnel->lastnames;
                        })
                        ->implode('<br>');
                })

                ->addColumn('workdays', function ($group) {

                    return $group->workdays
                        ->pluck('day')
                        ->implode(', ');
                })

                ->addColumn('status_badge', function ($group) {

                    return $group->status
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })

                ->addColumn('created_at_format', function ($group) {

                    return $group->created_at->format('d/m/Y H:i');
                })

                ->addColumn('updated_at_format', function ($group) {

                    return $group->updated_at->format('d/m/Y H:i');
                })

                ->addColumn('edit', function ($group) {

                    return '<button
                            class="btn btn-warning btn-sm btn-editar"
                            id="' . $group->id . '">

                            <i class="fas fa-edit"></i>

                        </button>';
                })

                ->addColumn('delete', function ($group) {

                    return '<button
                            type="button"
                            class="btn btn-danger btn-sm btn-delete"
                            data-url="' . route(
                        'admin.personnel-groups.destroy',
                        $group->id
                    ) . '">

                            <i class="fas fa-trash"></i>

                        </button>';
                })

                ->rawColumns([
                    'helpers',
                    'status_badge',
                    'edit',
                    'delete'
                ])

                ->make(true);
        }

        return view('admin.personnel-groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::where('status', true)->get();

        $shifts = Shift::orderBy('name')->get();

        $vehicles = Vehicle::where('status', 'Activo')->get();

        $driverType = PersonnelType::where('name', 'Conductor')->first();

        $helperType = PersonnelType::where('name', 'Ayudante')->first();

        $drivers = Personnel::where(
            'personnel_type_id',
            $driverType->id
        )->get();

        $helpers = Personnel::where(
            'personnel_type_id',
            $helperType->id
        )->get();

        return view(
            'admin.personnel-groups.create',
            compact(
                'zones',
                'shifts',
                'vehicles',
                'drivers',
                'helpers'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|max:255',
                'zone_id' => 'required|exists:zones,id',
                'shift_id' => 'required|exists:shifts,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'required|exists:personnels,id',
                'workdays' => 'required|array|min:1',
                'helpers' => 'required|array'
            ]);

            if ($request->filled('helpers')) {

                if (
                    count($request->helpers)
                    != count(array_unique($request->helpers))
                ) {

                    return response()->json([
                        'message' =>
                        'No puede seleccionar el mismo ayudante más de una vez.'
                    ], 422);
                }
            }

            $groupsDriver = PersonnelGroup::where(
                'driver_id',
                $request->driver_id
            )
                ->where(
                    'shift_id',
                    $request->shift_id
                )
                ->with('workdays')
                ->get();

            foreach ($groupsDriver as $groupDriver) {

                $groupDays = $groupDriver->workdays
                    ->pluck('day')
                    ->toArray();

                $repeatedDays = array_intersect(
                    $request->workdays,
                    $groupDays
                );

                if (!empty($repeatedDays)) {

                    return response()->json([
                        'message' =>
                        'El conductor ya está asignado a otro grupo en el turno seleccionado para los días: '
                            . implode(', ', $repeatedDays)
                    ], 422);
                }
            }

            if ($request->filled('helpers')) {

                foreach ($request->helpers as $helperId) {

                    $groupsHelper = PersonnelGroupDetail::where(
                        'personnel_id',
                        $helperId
                    )
                        ->with([
                            'group.workdays',
                            'group.shift'
                        ])
                        ->get();

                    foreach ($groupsHelper as $detail) {

                        if (
                            $detail->group &&
                            $detail->group->shift_id == $request->shift_id
                        ) {

                            $groupDays = $detail->group
                                ->workdays
                                ->pluck('day')
                                ->toArray();

                            $repeatedDays = array_intersect(
                                $request->workdays,
                                $groupDays
                            );

                            if (!empty($repeatedDays)) {

                                return response()->json([
                                    'message' =>
                                    'Uno de los ayudantes ya está asignado a otro grupo en el mismo turno para los días: '
                                        . implode(', ', $repeatedDays)
                                ], 422);
                            }
                        }
                    }
                }
            }

            $existingGroups = PersonnelGroup::where(
                'zone_id',
                $request->zone_id
            )
                ->where(
                    'shift_id',
                    $request->shift_id
                )
                ->where(
                    'vehicle_id',
                    $request->vehicle_id
                )
                ->with('workDays')
                ->get();

            foreach ($existingGroups as $group) {

                $groupDays = $group->workDays
                    ->pluck('day')
                    ->toArray();

                $repeatedDays = array_intersect(
                    $request->workdays,
                    $groupDays
                );

                if (!empty($repeatedDays)) {

                    return response()->json([
                        'message' =>
                        'El vehículo ya está asignado a otro grupo en la misma zona y turno para los días: '
                            . implode(', ', $repeatedDays)
                    ], 422);
                }
            }

            $group = PersonnelGroup::create([
                'name' => $request->name,
                'zone_id' => $request->zone_id,
                'shift_id' => $request->shift_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'status' => $request->status ?? true
            ]);

            if ($request->filled('helpers')) {

                foreach ($request->helpers as $helperId) {

                    PersonnelGroupDetail::create([
                        'personnel_group_id' => $group->id,
                        'personnel_id' => $helperId
                    ]);
                }
            }

            foreach ($request->workdays as $day) {

                PersonnelGroupWorkday::create([
                    'personnel_group_id' => $group->id,
                    'day' => $day
                ]);
            }

            return response()->json([
                'message' => 'Grupo registrado correctamente'
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $group = PersonnelGroup::with([
            'zone',
            'shift',
            'vehicle',
            'driver',
            'helpers.personnel',
            'workdays'
        ])->findOrFail($id);

        return response()->json($group);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = PersonnelGroup::with([
            'helpers',
            'workdays'
        ])->findOrFail($id);

        $zones = Zone::all();

        $shifts = Shift::all();

        $vehicles = Vehicle::all();

        $driverType = PersonnelType::where(
            'name',
            'Conductor'
        )->first();

        $helperType = PersonnelType::where(
            'name',
            'Ayudante'
        )->first();

        $drivers = Personnel::where(
            'personnel_type_id',
            $driverType->id
        )->get();

        $helpers = Personnel::where(
            'personnel_type_id',
            $helperType->id
        )->get();

        return view(
            'admin.personnel-groups.edit',
            compact(
                'group',
                'zones',
                'shifts',
                'vehicles',
                'drivers',
                'helpers'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $group = PersonnelGroup::findOrFail($id);

            $request->validate([
                'name' => 'required|max:255',
                'zone_id' => 'required|exists:zones,id',
                'shift_id' => 'required|exists:shifts,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'required|exists:personnels,id',
                'workdays' => 'required|array|min:1',
                'helpers' => 'required|array'
            ]);

            if ($request->filled('helpers')) {

                if (count($request->helpers) !== count(array_unique($request->helpers))) {

                    return response()->json([
                        'message' => 'No puede seleccionar el mismo ayudante más de una vez.'
                    ], 422);
                }
            }

            $driverGroups = PersonnelGroup::where('driver_id', $request->driver_id)
                ->where('shift_id', $request->shift_id)
                ->where('id', '!=', $group->id)
                ->with('workdays')
                ->get();

            foreach ($driverGroups as $driverGroup) {

                $groupDays = $driverGroup->workdays->pluck('day')->toArray();

                $repeatedDays = array_intersect($request->workdays, $groupDays);

                if (!empty($repeatedDays)) {

                    return response()->json([
                        'message' =>
                        'El conductor ya está asignado a otro grupo en el mismo turno para los días: '
                            . implode(', ', $repeatedDays)
                    ], 422);
                }
            }

            if ($request->filled('helpers')) {

                foreach ($request->helpers as $helperId) {

                    $helperGroups = PersonnelGroupDetail::where('personnel_id', $helperId)
                        ->whereHas('group', function ($q) use ($request, $group) {
                            $q->where('shift_id', $request->shift_id)
                                ->where('id', '!=', $group->id);
                        })
                        ->with('group.workdays')
                        ->get();

                    foreach ($helperGroups as $detail) {

                        $groupDays = $detail->group->workdays->pluck('day')->toArray();

                        $repeatedDays = array_intersect($request->workdays, $groupDays);

                        if (!empty($repeatedDays)) {

                            return response()->json([
                                'message' =>
                                'Uno de los ayudantes ya está asignado a otro grupo en el mismo turno para los días: '
                                    . implode(', ', $repeatedDays)
                            ], 422);
                        }
                    }
                }
            }

            $group->update([
                'name' => $request->name,
                'zone_id' => $request->zone_id,
                'shift_id' => $request->shift_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'status' => $request->status ?? true
            ]);

            PersonnelGroupDetail::where('personnel_group_id', $group->id)->delete();

            if ($request->filled('helpers')) {

                foreach ($request->helpers as $helperId) {

                    PersonnelGroupDetail::create([
                        'personnel_group_id' => $group->id,
                        'personnel_id' => $helperId
                    ]);
                }
            }

            PersonnelGroupWorkday::where('personnel_group_id', $group->id)->delete();

            foreach ($request->workdays as $day) {

                PersonnelGroupWorkday::create([
                    'personnel_group_id' => $group->id,
                    'day' => $day
                ]);
            }

            return response()->json([
                'message' => 'Grupo actualizado correctamente'
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $group = PersonnelGroup::findOrFail($id);

            PersonnelGroupDetail::where(
                'personnel_group_id',
                $group->id
            )->delete();

            $group->delete();

            return response()->json([
                'message' => 'Grupo eliminado correctamente'
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
