<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

use App\Models\Lote;
use App\Models\Product;
use App\Models\Store;


class InventarioController extends Controller
{
    public function showInventarioInicialForm()
    {
        $stores = Store::all();
        $lotes = Lote::all();
        $productos = Product::all();

        return view('inventario-inicial', compact('stores', 'lotes', 'productos'));
    }

    public function registrarInicial(Request $request)
    {
        $validated = $request->validate([
            'inventarios' => 'required|array',
            'inventarios.*.lote_id' => 'required|exists:lotes,id',
            'inventarios.*.product_id' => 'required|exists:products,id',
            'inventarios.*.store_id' => 'required|exists:stores,id',
            'inventarios.*.cantidad_inicial' => 'required|numeric|min:0',
        ]);

        foreach ($validated['inventarios'] as $data) {
            Inventario::updateOrCreate(
                [
                    'lote_id' => $data['lote_id'],
                    'product_id' => $data['product_id'],
                    'store_id' => $data['store_id'],
                ],
                [
                    'cantidad_inicial' => $data['cantidad_inicial'],
                    'cantidad_actual' => $data['cantidad_inicial'],
                ]
            );
        }

        return response()->json([
            'status' => 1,
            'message' => 'Inventario inicial registrado correctamente.',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventario $inventario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventario $inventario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventario $inventario)
    {
        //
    }
}
