<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\BrandModel;
use App\Models\VehicleType;
use App\Models\VehicleColor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vehicles = Vehicle::with(['model.brand', 'type', 'color'])->get();

            return DataTables::of($vehicles)
                ->addColumn('full_model', function ($vehicle) {
                    return ($vehicle->model && $vehicle->model->brand) 
                        ? $vehicle->model->brand->name . ' ' . $vehicle->model->name 
                        : 'N/A';
                })
                ->addColumn('type_name', function ($vehicle) {
                    return $vehicle->type ? $vehicle->type->name : 'N/A';
                })
                ->addColumn('color_info', function ($vehicle) {
                    if (!$vehicle->color) return 'N/A';
                    return '<div class="d-flex align-items-center">
                                <div style="width:20px;height:20px;border-radius:50%;background:' . $vehicle->color->code . ';margin-right:8px;border:1px solid #ccc;"></div>' 
                                . $vehicle->color->name . 
                           '</div>';
                })
                ->addColumn('edit', function ($vehicle) {
                    return '<button class="btn btn-sm btn-warning btn-editar" id="' . $vehicle->id . '">
                                <i class="fas fa-pen"></i>
                            </button>';
                })
                ->addColumn('delete', function ($vehicle) {
                    return '<button type="button" 
                                class="btn btn-sm btn-danger btn-delete"
                                data-url="' . route('admin.vehicles.destroy', $vehicle->id) . '">
                                <i class="fas fa-trash-alt"></i>
                            </button>';
                })
                ->rawColumns(['color_info', 'edit', 'delete'])
                ->make(true);
        }

        return view('admin.vehicles.index');
    }

    public function create()
    {
        $models = BrandModel::with('brand')->get();
        $types = VehicleType::all();
        $colors = VehicleColor::all();
        
        return view('admin.vehicles.create', compact('models', 'types', 'colors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'plate' => 'required|unique:vehicles,plate',
                'brand_model_id' => 'required|exists:brand_models,id',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'vehicle_color_id' => 'required|exists:vehicle_colors,id',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'mileage' => 'required|integer|min:0',
                'status' => 'required'
            ], [
                'plate.required' => 'La placa es obligatoria.',
                'plate.unique' => 'Esta placa ya está registrada.',
                'brand_model_id.required' => 'Debe seleccionar un modelo.',
                'vehicle_type_id.required' => 'Debe seleccionar un tipo.',
                'vehicle_color_id.required' => 'Debe seleccionar un color.',
                'year.required' => 'El año es obligatorio.'
            ]);

            Vehicle::create($request->all());

            return response()->json(['message' => 'Vehículo registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    public function edit(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $models = BrandModel::with('brand')->get();
        $types = VehicleType::all();
        $colors = VehicleColor::all();

        return view('admin.vehicles.edit', compact('vehicle', 'models', 'types', 'colors'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            $request->validate([
                'plate' => 'required|unique:vehicles,plate,' . $id,
                'brand_model_id' => 'required|exists:brand_models,id',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'vehicle_color_id' => 'required|exists:vehicle_colors,id',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'mileage' => 'required|integer|min:0',
                'status' => 'required'
            ], [
                'plate.required' => 'La placa es obligatoria.',
                'plate.unique' => 'Esta placa ya está registrada.'
            ]);

            $vehicle->update($request->all());

            return response()->json(['message' => 'Vehículo actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            return response()->json(['message' => 'Vehículo eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la eliminación: ' . $th->getMessage()], 500);
        }
    }
}
