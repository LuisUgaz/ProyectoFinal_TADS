<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::all();

        if ($request->ajax()) {

            return DataTables::of($brands)

                ->addColumn('logo', function ($brand) {

                    if ($brand->logo) {
                        return '<img src="' . asset('storage/' . $brand->logo) . '"
                                width="50"
                                height="50"
                                style="object-fit:contain;">';
                    }

                    return '<img src="' . asset('img/no-image.png') . '"
                            width="50"
                            height="50">';
                })

                ->addColumn('edit', function ($brand) {
                    return '<button class="btn btn-sm btn-warning btn-editar"
                                id="' . $brand->id . '">
                                <i class="fas fa-pen"></i>
                            </button>';
                })

                ->addColumn('delete', function ($brand) {
                    return '<button type="button"
                                class="btn btn-sm btn-danger btn-delete"
                                data-url="' . route('admin.brands.destroy', $brand->id) . '">
                                <i class="fas fa-trash-alt"></i>
                            </button>';
                })

                ->rawColumns([
                    'logo',
                    'edit',
                    'delete'
                ])

                ->make(true);
        }

        return view('admin.brands.index');
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|unique:brands,name',
                'description' => 'required',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $logo = null;

            if ($request->hasFile('logo')) {

                $logo = $request->file('logo')
                    ->store('brands', 'public');
            }

            Brand::create([
                'name' => $request->name,
                'description' => $request->description,
                'logo' => $logo
            ]);

            return response()->json([
                'message' => 'Marca registrada correctamente'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);

        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, string $id)
    {
        try {

            $brand = Brand::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:brands,name,' . $id,
                'description' => 'required',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            if ($request->hasFile('logo')) {

                if ($brand->logo &&
                    Storage::disk('public')->exists($brand->logo)) {

                    Storage::disk('public')->delete($brand->logo);
                }

                $brand->logo = $request->file('logo')
                    ->store('brands', 'public');
            }

            $brand->name = $request->name;
            $brand->description = $request->description;

            $brand->save();

            return response()->json([
                'message' => 'Marca actualizada correctamente'
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

            $brand = Brand::findOrFail($id);

            if ($brand->logo &&
                Storage::disk('public')->exists($brand->logo)) {

                Storage::disk('public')->delete($brand->logo);
            }

            $brand->delete();

            return response()->json([
                'message' => 'Marca eliminada correctamente'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error en la eliminación: '
                    . $th->getMessage()
            ], 500);
        }
    }
}