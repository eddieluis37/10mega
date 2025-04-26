<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Combo;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\Loss;
use App\Models\Product;
use Illuminate\Http\Request;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dishes = Dish::with('products')->get();
        return view('dishes.index', compact('dishes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dish = Dish::create($request->only('name','code','description','price','image','status'));
        $dish->products()->attach($request->input('ingredients', []));
        return redirect()->route('dishes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish)
    {
        return view('dishes.show', compact('dish'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dish $dish)
    {
        $products = Product::all();
        return view('dishes.edit', compact('dish','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dish $dish)
    {
        $dish->update($request->only('name','description','price','status'));
        $dish->products()->sync($request->input('ingredients', []));
        return redirect()->route('dishes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function  destroy(Dish $dish)
    {
        $dish->delete();
        return back();
    }
}
