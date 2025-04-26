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
        $combo = Combo::create($request->only('name', 'code', 'description', 'price', 'status'));
        $combo->products()->attach($request->input('products', []));
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
        $products = Product::all();
        return view('combos.edit', compact('combo', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Combo $combo)
    {
        $combo->update($request->only('name', 'description', 'price', 'status'));
        $combo->products()->sync($request->input('products', []));
        return redirect()->route('combos.index');
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
