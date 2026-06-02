<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contracts = Contract::with('personnel')->select('contracts.*');

            return DataTables::of($contracts)
                ->addColumn('personnel_name', function ($contract) {
                    return $contract->personnel->names . ' ' . $contract->personnel->lastnames;
                })
                ->addColumn('personnel_dni', function ($contract) {
                    return $contract->personnel->dni;
                })
                ->addColumn('start_date', function ($contract) {
                    return $contract->start_date->format('d/m/Y');
                })
                ->addColumn('end_date', function ($contract) {
                    return $contract->end_date ? $contract->end_date->format('d/m/Y') : 'N/A';
                })
                ->addColumn('status', function ($contract) {
                    $badgeClass = $contract->is_active ? 'badge-success' : 'badge-danger';
                    $badgeText = $contract->is_active ? 'Activo' : 'Inactivo';
                    return '<span class="badge ' . $badgeClass . '">' . $badgeText . '</span>';
                })
                ->addColumn('edit', function ($contract) {
                    return '<button class="btn btn-sm btn-warning btn-editar" id="' . $contract->id . '">
                                <i class="fas fa-pen"></i>
                            </button>';
                })
                ->addColumn('delete', function ($contract) {
                    return '<button type="button" class="btn btn-sm btn-danger btn-delete" data-url="' . route('admin.contracts.destroy', $contract->id) . '">
                                <i class="fas fa-trash-alt"></i>
                            </button>';
                })
                ->rawColumns(['status', 'edit', 'delete'])
                ->make(true);
        }

        return view('admin.contracts.index');
    }

    public function create()
    {
        $personnels = Personnel::all();
        return view('admin.contracts.create', compact('personnels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'personnel_id' => 'required|exists:personnels,id',
                'type' => 'required|in:Permanente,Nombrado,Temporal',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'salary' => 'required|numeric|min:0',
                'probation_period' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            // Desactivar otros contratos activos del mismo personal si se marca como activo
            if ($request->has('is_active') && $request->is_active) {
                Contract::where('personnel_id', $request->personnel_id)
                    ->update(['is_active' => false]);
            }

            Contract::create($request->all());

            return response()->json([
                'message' => 'Contrato registrado correctamente'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $contract = Contract::findOrFail($id);
        $personnels = Personnel::all();
        return view('admin.contracts.edit', compact('contract', 'personnels'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $contract = Contract::findOrFail($id);

            $request->validate([
                'personnel_id' => 'required|exists:personnels,id',
                'type' => 'required|in:Permanente,Nombrado,Temporal',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'salary' => 'required|numeric|min:0',
                'probation_period' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            // Desactivar otros contratos si este se marca como activo
            if ($request->is_active) {
                Contract::where('personnel_id', $request->personnel_id)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);
            }

            $contract->update($request->all());

            return response()->json([
                'message' => 'Contrato actualizado correctamente'
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
            $contract = Contract::findOrFail($id);
            $contract->delete();

            return response()->json([
                'message' => 'Contrato eliminado correctamente'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en la eliminación: ' . $th->getMessage()
            ], 500);
        }
    }
}
