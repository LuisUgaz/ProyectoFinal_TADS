<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $attendances = Attendance::with('personnel')
                ->select('attendances.*');

            return DataTables::of($attendances)

                ->addColumn('personnel_dni', function ($attendance) {
                    return $attendance->personnel->dni ?? 'N/A';
                })

                ->addColumn('personnel_name', function ($attendance) {
                    return $attendance->personnel
                        ? $attendance->personnel->names . ' ' . $attendance->personnel->lastnames
                        : 'Sin personal';
                })

                ->addColumn('date', function ($attendance) {
                    return $attendance->date->format('d/m/Y');
                })

                ->addColumn('time', function ($attendance) {
                    return \Carbon\Carbon::parse($attendance->time)->format('H:i');
                })

                ->addColumn('type_badge', function ($attendance) {
                    if ($attendance->type == 'Ingreso') {
                        return '<span class="badge badge-success badge-custom">Ingreso</span>';
                    }

                    return '<span class="badge badge-info badge-custom">Salida</span>';
                })

                ->addColumn('status_badge', function ($attendance) {
                    if ($attendance->status == 'Presente') {
                        return '<span class="badge badge-success badge-custom">Presente</span>';
                    }

                    return '<span class="badge badge-danger badge-custom">Ausente</span>';
                })

                ->addColumn('notes', function ($attendance) {
                    return $attendance->notes ?: '—';
                })

                ->addColumn('edit', function ($attendance) {
                    return '<button class="btn btn-sm btn-warning btn-editar" id="' . $attendance->id . '">
                                <i class="fas fa-pen"></i>
                            </button>';
                })

                ->addColumn('delete', function ($attendance) {
                    return '<button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-url="' . route('admin.attendances.destroy', $attendance->id) . '">
                                <i class="fas fa-trash-alt"></i>
                            </button>';
                })

                ->rawColumns([
                    'type_badge',
                    'status_badge',
                    'edit',
                    'delete'
                ])

                ->make(true);
        }

        return view('admin.attendances.index');
    }

    public function create()
    {
        $personnels = Personnel::where('status', 'Activo')
            ->orderBy('lastnames')
            ->orderBy('names')
            ->get();

        return view('admin.attendances.create', compact('personnels'));
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'personnel_id' => 'required|exists:personnels,id',
                'date' => 'required|date',
                'time' => 'required',
                'status' => 'required|in:Presente,Ausente',
                'notes' => 'nullable',
            ], [
                'personnel_id.required' => 'Debe seleccionar al personal.',
                'personnel_id.exists' => 'El personal seleccionado no es válido.',
                'date.required' => 'La fecha es obligatoria.',
                'time.required' => 'La hora es obligatoria.',
                'status.required' => 'Debe seleccionar el estado de asistencia.',
            ]);

            $attendanceStatus = $this->getAttendanceStatus($request->personnel_id, $request->date);

            if (!$attendanceStatus['can_register']) {
                return response()->json([
                    'message' => $attendanceStatus['message']
                ], 422);
            }

            $type = $attendanceStatus['next_type'];

            Attendance::create([
                'personnel_id' => $request->personnel_id,
                'date' => $request->date,
                'time' => $request->time,
                'type' => $type,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Asistencia registrada correctamente'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $attendance = Attendance::findOrFail($id);

        $personnels = Personnel::where('status', 'Activo')
            ->orderBy('lastnames')
            ->orderBy('names')
            ->get();

        return view('admin.attendances.edit', compact('attendance', 'personnels'));
    }

    public function update(Request $request, string $id)
    {
        try {

            $attendance = Attendance::findOrFail($id);

            $request->validate([
                'personnel_id' => 'required|exists:personnels,id',
                'date' => 'required|date',
                'time' => 'required',
                'status' => 'required|in:Presente,Ausente',
                'notes' => 'nullable',
            ]);

            $attendance->update([
                'personnel_id' => $request->personnel_id,
                'date' => $request->date,
                'time' => $request->time,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Asistencia actualizada correctamente'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {

            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'message' => 'Asistencia eliminada correctamente'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error en la eliminación: ' . $th->getMessage()
            ], 500);
        }
    }

    private function getAttendanceStatus($personnelId, $date)
    {
        $records = Attendance::where('personnel_id', $personnelId)
            ->where('date', $date)
            ->orderBy('time')
            ->get();

        $hasIngreso = $records->where('type', 'Ingreso')->count() > 0;
        $hasSalida = $records->where('type', 'Salida')->count() > 0;

        if (!$hasIngreso) {
            return [
                'records' => $records,
                'next_type' => 'Ingreso',
                'can_register' => true,
                'message' => 'Corresponde registrar la ENTRADA del personal.'
            ];
        }

        if (!$hasSalida) {
            return [
                'records' => $records,
                'next_type' => 'Salida',
                'can_register' => true,
                'message' => 'La entrada ya fue registrada. Corresponde registrar la SALIDA del personal.'
            ];
        }

        return [
            'records' => $records,
            'next_type' => null,
            'can_register' => false,
            'message' => 'La asistencia del personal ya se encuentra registrada para la fecha seleccionada.'
        ];
    }

    public function personnelDayInfo(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:personnels,id',
            'date' => 'required|date',
        ]);

        $personnel = Personnel::findOrFail($request->personnel_id);

        $attendanceStatus = $this->getAttendanceStatus(
            $request->personnel_id,
            $request->date
        );

        $records = $attendanceStatus['records'];
        $nextType = $attendanceStatus['next_type'];
        $canRegister = $attendanceStatus['can_register'];
        $message = $attendanceStatus['message'];

        return response()->json([
            'personnel' => [
                'dni' => $personnel->dni,
                'names' => $personnel->names,
                'lastnames' => $personnel->lastnames,
                'email' => $personnel->email,
                'phone' => $personnel->phone,
            ],
            'records' => $records->map(function ($record) {
                return [
                    'type' => $record->type,
                    'time' => \Carbon\Carbon::parse($record->time)->format('H:i'),
                    'status' => $record->status,
                ];
            }),
            'next_type' => $nextType,
            'can_register' => $canRegister,
            'message' => $message,
        ]);
    }
}