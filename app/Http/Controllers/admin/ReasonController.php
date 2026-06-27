<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $reasons = Reason::query();

            return DataTables::of($reasons)

                ->addColumn('created_at_format', function ($reason) {
                    return $reason->created_at->format('d/m/Y H:i');
                })

                ->addColumn('updated_at_format', function ($reason) {
                    return $reason->updated_at->format('d/m/Y H:i');
                })

                ->addColumn('actions', function ($reason) {

                    $edit = '<button
                                class="btn btn-sm btn-warning btn-editar"
                                id="' . $reason->id . '">
                                <i class="fas fa-pen"></i>
                            </button>';

                    $delete = '<button
                                type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-url="' . route('admin.reasons.destroy', $reason->id) . '">
                                <i class="fas fa-trash-alt"></i>
                            </button>';

                    return $edit . ' ' . $delete;
                })

                ->rawColumns([
                    'actions'
                ])

                ->make(true);
        }

        return view('admin.reasons.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.reasons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|max:255|unique:reasons,name',
                'description' => 'nullable|max:500'
            ]);

            Reason::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json([
                'message' => 'Motivo registrado correctamente.'
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reason $reason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reason = Reason::findOrFail($id);

        return view(
            'admin.reasons.edit',
            compact('reason')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $reason = Reason::findOrFail($id);

            $request->validate([
                'name' => 'required|max:255|unique:reasons,name,' . $reason->id,
                'description' => 'nullable|max:500'
            ]);

            $reason->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json([
                'message' => 'Motivo actualizado correctamente.'
            ], 200);
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

            $reason = Reason::findOrFail($id);

            $reason->delete();

            return response()->json([
                'message' => 'Motivo eliminado correctamente.'
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
