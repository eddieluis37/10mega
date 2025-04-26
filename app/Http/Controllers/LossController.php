<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use App\Models\Product;
use Illuminate\Http\Request;

class LossController extends Controller
{
    public function index()
    {
        $losses = Loss::with('product', 'reporter')->get();
        return view('losses.index', compact('losses'));
    }

    public function create()
    {
        $products = Product::all();
        return view('losses.create', compact('products'));
    }

    public function store(Request $request)
    {
        Loss::create($request->only('store_id', 'product_id', 'quantity', 'reason', 'reported_by'));
        return redirect()->route('losses.index');
    }
}
