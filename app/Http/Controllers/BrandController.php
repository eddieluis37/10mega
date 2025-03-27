<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandThird;
use App\Models\Third;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brandThirds = BrandThird::with('brand', 'third')->get();
        return view('brands.index', compact('brandThirds'));
    }

    public function create()
    {
        $brands = Brand::all();
        $thirds = Third::all();
        return view('brands.create', compact('brands', 'thirds'));
    }

    public function store(Request $request)
    {
        // Validamos que 'third_id' sea un arreglo y cada elemento exista en la tabla 'thirds'
        $validatedData = $request->validate([
            'name'       => 'required|string|max:255',
            'brand_id'   => 'required|exists:brands,id',
            'third_id'   => 'required|array',
            'third_id.*' => 'exists:thirds,id',
        ]);

        // Creamos el registro sin los proveedores
        $brandThird = BrandThird::create([
            'name'     => $validatedData['name'],
            'brand_id' => $validatedData['brand_id'],
        ]);

        // Asociamos los proveedores (muchos a muchos)
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
        // Validamos los datos, incluyendo el arreglo de proveedores
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
