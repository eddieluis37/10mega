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
        // $products = Product::all();

        $products = Product::WhereIn('category_id', [1, 2, 3])->get();
           

        return view('dishes.create', compact('products'));
    }

    public function store(Request $request)
    {
        // 1) Valida los campos si lo deseas…

        // 2) Crea el plato
        $dish = Dish::create($request->only('name', 'code', 'description', 'price', 'image', 'status'));

        // 3) Prepara el array para attach con quantity y unitofmeasure_id
        $attach = [];
        foreach ($request->input('ingredients', []) as $productId) {
            $attach[$productId] = [
                'quantity'          => $request->input("quantities.{$productId}", 1),
                'unitofmeasure_id'  => $request->input("units.{$productId}", 1),
            ];
        }

        // 4) Adjunta
        $dish->products()->attach($attach);

        return redirect()->route('dishes.index');
    }

    public function update(Request $request, Dish $dish)
    {
        // 1) Actualiza datos básicos
        $dish->update($request->only('name', 'code', 'description', 'price', 'image', 'status'));

        // 2) Prepara sync con pivot
        $sync = [];
        foreach ($request->input('ingredients', []) as $productId) {
            $sync[$productId] = [
                'quantity'          => $request->input("quantities.{$productId}", 1),
                'unitofmeasure_id'  => $request->input("units.{$productId}", 1),
            ];
        }

        // 3) Sincroniza
        $dish->products()->sync($sync);

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
        $products = Product::Where([
            ['category_id', 3],
            ['status', 1]
        ])->get();
        return view('dishes.edit', compact('dish', 'products'));
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
