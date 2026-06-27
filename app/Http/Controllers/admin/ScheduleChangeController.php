<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleChange;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ScheduleChangeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $changes = ScheduleChange::with([
                'schedule.zone',
                'schedule.shift',
                'schedule.vehicle',
                'schedule.driver',
                'reason',
                'user'
            ])->select('schedule_changes.*');

            return DataTables::of($changes)

                ->addColumn('date_format', function ($change) {
                    return $change->created_at->format('d/m/Y H:i');
                })

                ->addColumn('schedule_info', function ($change) {
                    return 'Programación #' . $change->schedule_id;
                })

                ->addColumn('zone_name', function ($change) {
                    return $change->schedule?->zone?->name ?? 'N/A';
                })

                ->addColumn('reason_name', function ($change) {
                    return $change->reason?->name ?? 'Reprogramación';
                })

                ->addColumn('user_name', function ($change) {
                    return $change->user?->name ?? 'Administrador';
                })

                ->addColumn('actions', function ($change) {
                    return '<button
                                class="btn btn-sm btn-info btn-show"
                                id="' . $change->id . '">
                                <i class="fas fa-eye"></i>
                            </button>';
                })

                ->rawColumns(['actions'])

                ->make(true);
        }

        return view('admin.changes.index');
    }

    public function show($id)
    {
        $change = ScheduleChange::with([
            'schedule.zone',
            'schedule.shift',
            'schedule.vehicle',
            'schedule.driver',
            'reason',
            'user'
        ])->findOrFail($id);

        return view('admin.changes.show', compact('change'));
    }
}