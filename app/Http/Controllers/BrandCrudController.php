<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandCrudController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands_crud.index', compact('brands'));
    }

    public function create()
    {
        return view('brands_crud.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|boolean'
        ]);

        Brand::create($validatedData);

        return redirect()->route('brand-crud.index')->with('success', 'Marca creada exitosamente.');
    }

    public function show(Brand $brand)
    {
        return view('brands_crud.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('brands_crud.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|boolean'
        ]);

        $brand->update($validatedData);

        return redirect()->route('brand-crud.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('brand-crud.index')->with('success', 'Marca eliminada correctamente.');
    }
}
