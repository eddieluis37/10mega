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
            $rules = [
                'loteId' => 'required',
                'lote' => 'required',
                'fecha_vencimiento' => 'required',
            ];

            $messages = [
                'loteId.required' => 'El es requerido',
                'lote.required' => 'Lote es requerido',
                'fecha_vencimiento.required' => 'Fecha de vencimiento requerida',

            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getReg = Lote::firstWhere('id', $request->loteId);
            if ($getReg == null) {
                $Lote = new Lote();
                $Lote->codigo = $request->lote;
                $Lote->fecha_vencimiento = $request->fecha_vencimiento;
                $Lote->save();

                return response()->json([
                    'status' => 1,
                    'message' => "Lote: " . $Lote->codigo . ' ' . 'Creado con ID: ' . $Lote->id,
                    "registroId" => $Lote->id
                ]);
            } else {
                $updateLote = Lote::firstWhere('id', $request->loteId);
                $updateLote->codigo = $request->lote;
                $updateLote->fecha_vencimiento = $request->fecha_vencimiento;
                $updateLote->save();

                return response()->json([
                    "status" => 1,
                    "message" => "Lote: " . $updateLote->codigo . ' ' . 'Editado con ID: ' . $updateLote->id,
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
}
