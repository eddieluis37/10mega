<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Dish;
use App\Models\Product;
use App\Models\Restaurantorder;
use Illuminate\Http\Request;

class RestaurantOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Restaurantorder::with('items')->latest()->get();
        return view('restaurant_orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dishes = Dish::all();
        $combos = Combo::all();
        $products = Product::all();
        return view('restaurant_orders.create', compact('dishes', 'combos', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = RestaurantOrder::create($request->only('sale_id', 'table_number', 'waiter_id', 'status'));
        foreach ($request->input('items', []) as $item) {
            $order->items()->create([
                'item_type'  => $item['type'],
                'item_id'    => $item['id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }
        return redirect()->route('restaurant-orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->load('items');
        return view('restaurant_orders.show', compact('restaurantOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestaurantOrder $restaurantOrder)
    {
        $restaurantOrder->delete();
        return back();
    }
}
