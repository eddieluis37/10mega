<?php

namespace App\Http\Controllers\inventory;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Lote;
use App\Models\centros\Centrocosto;
use App\Models\Centro_costo_product;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductLote;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class CargueProductTerminadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::whereIn('id', [1])->orderBy('name', 'asc')->get();
        /*  $category = Category::orderBy('name', 'asc')->get(); */
        //  $centros = Centrocosto::whereIn('id', [1])->orderBy('name', 'asc')->get();
        // $centroCostoProductos = Centro_costo_product::all();
        $bodegas = Store::whereIn('id', [1])->orderBy('name', 'asc')->get();
        $lote = Lote::orderBy('id', 'desc')->get();
        $prod = Product::Where('category_id', 1)->get();

        $newToken = Crypt::encrypt(csrf_token());

        return view("inventory.cargue_products_terminados.index", compact('category', 'bodegas', 'lote', 'prod'));

        // return view('hola');
        //  return view('inventory.diary');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function storelote(Request $request)
    {
        try {
            // Reglas de validación
            $rules = [

                'lote' => 'required|unique:lotes,codigo,' . $request->loteId . ',id', // Verifica unicidad, excluyendo el lote actual al editar
                'fecha_vencimiento' => 'required|date', // Aseguramos que sea una fecha válida
            ];

            // Mensajes personalizados
            $messages = [
                'lote.required' => 'El código del lote es requerido.',
                'lote.unique' => 'El código del lote ya existe. Por favor, use un código diferente.',
                'fecha_vencimiento.required' => 'La fecha de vencimiento es requerida.',
                'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser válida.',

            ];

            // Validación de entrada
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar lote existente o crear uno nuevo
            $getReg = Lote::find($request->loteId);

            if ($getReg == null) {
                // Crear un nuevo lote
                $Lote = new Lote();
                $Lote->codigo = $request->lote;
                $Lote->fecha_vencimiento = $request->fecha_vencimiento;
                $Lote->save();

                return response()->json([
                    'status' => 1,
                    'message' => "Lote: " . $Lote->codigo . ' creado con ID: ' . $Lote->id,
                    "registroId" => $Lote->id
                ]);
            } else {
                // Editar lote existente
                $getReg->codigo = $request->lote;
                $getReg->fecha_vencimiento = $request->fecha_vencimiento;
                $getReg->save();

                return response()->json([
                    "status" => 1,
                    "message" => "Lote: " . $getReg->codigo . ' editado con ID: ' . $getReg->id,
                    "registroId" => 0
                ]);
            }
        } catch (\Throwable $th) {
            // Manejo de excepciones
            return response()->json([
                'status' => 0,
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function getLoteData()
    {
        $lotes = Lote::orderBy('id', 'desc')->get();
        return response()->json($lotes);
    }

    public function productlote(Request $request)
    {
        try {
            $rules = [
                'productloteId' => 'required',
                'producto' => 'required|exists:products,id', // Ensure the product exists
                'loteProd' => 'required',
                'quantity' => 'required',
                'store_id' => 'required|exists:stores,id', // Ensure the store exists
            ];
            $messages = [
                'productloteId.required' => 'El id es requerido',
                'producto.required' => 'Producto es requerido',
                'producto.exists' => 'El producto no existe', // Custom message for product existence
                'loteProd.required' => 'Lote es requerido',
                'quantity.required' => 'Cantidad es requerida',
                'store_id.required' => 'El ID de la tienda es requerido',
                'store_id.exists' => 'La tienda no existe', // Custom message for store existence
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if ProductLote exists
            $getReg = ProductLote::firstWhere('id', $request->productloteId);
            if ($getReg == null) {
                // Create new ProductLote
                $productLote = new ProductLote();
                $productLote->product_id = $request->producto;
                $productLote->lote_id = $request->loteProd;
                $productLote->quantity = $request->quantity;
                $productLote->save();

                // Cargue el Producto para adjuntarlo a las tiendas
                $product = Product::find($request->producto);
                if ($product) {
                    $product->stores()->attach($request->store_id);
                }

                return response()->json([
                    'status' => 1,
                    'message' => "productLote: " . $productLote->product_id . ' ' . 'Creado con ID: ' . $productLote->id,
                    "registroId" => $productLote->id
                ]);
            } else {
                // Update existing ProductLote
                $updateLote = ProductLote::firstWhere('id', $request->productloteId);
                $updateLote->product_id = $request->producto;
                $updateLote->lote_id = $request->loteProd;
                $updateLote->quantity = $request->quantity;
                $updateLote->save();

                // Cargue el Producto para sincronizarlo con las tiendas
                $product = Product::find($request->producto);
                if ($product) {
                    $product->stores()->sync([$request->store_id]);
                }

                return response()->json([
                    "status" => 1,
                    "message" => "ProductLote: " . $updateLote->product_id . ' ' . 'Editado con ID: ' . $updateLote->id,
                    "registroId" => 0
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /* public function show(Request $request)
    {
        $centrocostoId = $request->input('centrocostoId');
        $categoriaId = $request->input('categoriaId');
        $data = DB::table('centro_costo_Loteucts as ccp')
            ->join('products as pro', 'pro.id', '=', 'ccp.products_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            ->select(
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'ccp.invinicial as invinicial',              
                'ccp.fisico as fisico'
            )
            ->where('ccp.centrocosto_id', $centrocostoId)
            ->where('ccp.tipoinventario', 'inicial')
            ->where('pro.category_id', $categoriaId)
            ->where('pro.status', 1)
            ->get();
       

        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    } */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $storeId = $request->input('storeId');
        $categoriaId = $request->input('categoriaId');
        $loteId = $request->input('loteId');


        $data = DB::table('product_lote as pl')
            ->join('products as pro', 'pro.id', '=', 'pl.product_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            //    ->join('product_store as ps', 'ps.product_id', '=', 'pl.product_id')
            ->join('lotes as l', 'l.id',  '=', 'pl.lote_id')
            //  ->join('product_lote as pl', 'pl.lote_id', '=', 'l.id')

            ->select(
                'pl.id as productoLoteId',
                'pro.id as productId',
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'l.codigo as codigolote',
                'l.fecha_vencimiento as fechavence',
                'pl.quantity as quantity',
                //   'pl.lote as lote',
            )
            ->where('pro.category_id', $categoriaId)
            ->where('pl.lote_id', $loteId)
            //    ->where('ps.store_id', $storeId)
            ->where('pro.status', 1)
            ->orderBy('pl.id', 'desc')
            ->get();

        // return response()->json(['data' => $data]);
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateCptInventory(Request $request)
    {
        $productoLoteId = request('productoLoteId');
        $productId = request('productId');
        $loteId = request('loteId'); // Get loteId from the dropdown
        $quantity = request('quantity');

        DB::table('product_lote')
            ->where('id', $productoLoteId)
            ->where('product_id', $productId)
            ->where('lote_id', $loteId)
            ->update([
                'quantity' => $quantity
            ]);

        return response()->json(['success' => 'true']);
    }

    public function destroy($id)
    {
        // Delete the record from the product_lote table
        $deleted = DB::table('product_lote')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function sincronizarProductLote()
    {
        DB::beginTransaction();

        try {
            // Obtener todos los lotes
            $lotes = Lote::all();

            foreach ($lotes as $lote) {
                // Obtener los detalles de los productos asociados al lote
                $detalles = $lote->productLotes;

                foreach ($detalles as $detalle) {
                    // Sincronizar únicamente si el peso (quantity) es mayor a 0
                    if ($detalle->quantity > 0) {
                        // 1. Actualizar o crear en product_lote (sobrescribir cantidad)
                        ProductLote::updateOrCreate(
                            [
                                'product_id' => $detalle->product_id,
                                'lote_id' => $lote->id,
                            ],
                            [
                                'quantity' => $detalle->quantity,
                            ]
                        );

                        // 2. Crear o actualizar inventario
                        $inventario = Inventario::firstOrCreate(
                            [
                                'product_id' => $detalle->product_id,
                                'lote_id' => $lote->id,
                                'store_id' => 1,
                            ],
                            [
                                'cantidad_inicial' => 0,
                                'cantidad_final' => 0,
                                'costo_unitario' => $detalle->product->cost,
                                'costo_total' => 0,
                            ]
                        );

                        // Actualizar inventario (sobrescribir cantidad final y costo total)
                        $inventario->cantidad_final = $detalle->quantity;
                        $inventario->costo_total = $inventario->cantidad_final * $detalle->product->cost;
                        $inventario->save();

                        // 3. Registrar movimiento de inventario
                        MovimientoInventario::create([
                            'tipo' => 'products_terminados',
                            'store_origen_id' => null,
                            'store_destino_id' => 1,
                            'lote_id' => $lote->id,
                            'product_id' => $detalle->product_id,
                            'cantidad' => $detalle->quantity,
                            'costo_unitario' => $detalle->product->cost,
                            'total' => $detalle->quantity * $detalle->product->cost,
                            'fecha' => Carbon::now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Sincronización completada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}
