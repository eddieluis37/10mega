<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandThird;
use App\Models\Third;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Se carga la marca y los proveedores asignados (relación many-to-many)
        $query = BrandThird::with('brand', 'thirds');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('brand', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('thirds', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $brandThirds = $query->get();

        return view('brands.index', compact('brandThirds', 'search'));
    }

    public function create()
    {
        $brands = Brand::all();
        $thirds = Third::all();
        return view('brands.create', compact('brands', 'thirds'));
    }

    public function store(Request $request)
    {
        // Validamos que 'third_id' sea un arreglo y que cada elemento exista en la tabla 'thirds'
        $validatedData = $request->validate([
            'name'       => 'required|string|max:255',
            'brand_id'   => 'required|exists:brands,id',
            'third_id'   => 'required|array',
            'third_id.*' => 'exists:thirds,id',
        ]);

        // Creamos el registro en brand_third sin los proveedores
        $brandThird = BrandThird::create([
            'name'     => $validatedData['name'],
            'brand_id' => $validatedData['brand_id'],
        ]);

        // Asociamos los proveedores mediante la tabla pivote
        $brandThird->thirds()->attach($validatedData['third_id']);

        return redirect()->route('brands.index')->with('success', 'Marca relacionada de forma exitosa.');
    }

    public function edit(BrandThird $brandThird)
    {
        $brands = Brand::all();
        $thirds = Third::all();
        return view('brands.edit', compact('brandThird', 'brands', 'thirds'));
    }

    public function update(Request $request, BrandThird $brandThird)
    {
        // Validamos los datos, incluyendo que 'third_id' sea un arreglo
        $validatedData = $request->validate([
            'name'       => 'required|string|max:255',
            'brand_id'   => 'required|exists:brands,id',
            'third_id'   => 'required|array',
            'third_id.*' => 'exists:thirds,id',
        ]);

        // Actualizamos el registro principal
        $brandThird->update([
            'name'     => $validatedData['name'],
            'brand_id' => $validatedData['brand_id'],
        ]);

        // Sincronizamos los proveedores asociados
        $brandThird->thirds()->sync($validatedData['third_id']);

        return redirect()->route('brands.index')->with('success', 'Marca actualizada exitosamente.');
    }

    public function destroy(BrandThird $brandThird)
    {
        $brandThird->delete();
        return redirect()->route('brands.index')->with('success', 'Marca eliminada.');
    }
}
