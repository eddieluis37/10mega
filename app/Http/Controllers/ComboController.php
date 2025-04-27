<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Dish;
use App\Models\Loss;
use App\Models\Product;
use App\Models\Restaurantorder;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $combos = Combo::with('products')->get();
        return view('combos.index', compact('combos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('combos.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1) Crea el combo
        $combo = Combo::create($request->only('name', 'code', 'description', 'price', 'status'));

        // 2) Prepara los datos pivot con quantity
        $attachData = [];
        foreach ($request->input('products', []) as $productId) {
            // Suponiendo que en tu formulario envías un arreglo 'quantities[<id>]'
            $quantity = $request->input("quantities.{$productId}", 1);
            $attachData[$productId] = ['quantity' => $quantity];
        }

        // 3) Adjunta con cantidad
        $combo->products()->attach($attachData);

        return redirect()->route('combos.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Combo $combo)
    {
        return view('combos.show', compact('combo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Combo $combo)
    {
        // todos los productos para la lista de checkboxes
        $products = Product::all();
        return view('combos.edit', compact('combo', 'products'));
    }

    public function update(Request $request, Combo $combo)
    {
        // valida campos según necesites...
        $combo->update($request->only('name', 'code', 'description', 'price', 'status'));

        // sincroniza pivot con cantidades
        $attachData = [];
        foreach ($request->input('products', []) as $productId) {
            $attachData[$productId] = [
                'quantity' => $request->input("quantities.{$productId}", 1)
            ];
        }
        $combo->products()->sync($attachData);

        return redirect()->route('combos.index')
            ->with('success', 'Combo actualizado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Combo $combo)
    {
        $combo->delete();
        return back();
    }
}
