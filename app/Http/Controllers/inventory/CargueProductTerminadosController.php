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
                $Lote->name = $request->lote;
                $Lote->fecha_vencimiento = $request->fecha_vencimiento;
                $Lote->save();

                return response()->json([
                    'status' => 1,
                    'message' => "Lote: " . $Lote->name . ' ' . 'Creado con ID: ' . $Lote->id,
                    "registroId" => $Lote->id
                ]);
            } else {
                $updateLote = Lote::firstWhere('id', $request->loteId);
                $updateLote->name = $request->lote;
                $updateLote->fecha_vencimiento = $request->fecha_vencimiento;
                $updateLote->save();

                return response()->json([
                    "status" => 1,
                    "message" => "Lote: " . $updateLote->name . ' ' . 'Editado con ID: ' . $updateLote->id,
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
        $centrocostoId = $request->input('centrocostoId');
        $categoriaId = $request->input('categoriaId');
        $loteId = $request->input('loteId');


        $data = DB::table('product_store as ccp')
            ->join('products as pro', 'pro.id', '=', 'ccp.product_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            ->join('product_lote as prolote', 'prolote.product_id', '=', 'ccp.product_id')
            ->join('lotes as l', 'l.id',  '=', 'prolote.lote_id')
            //  ->join('product_lote as pl', 'pl.lote_id', '=', 'l.id')

            ->select(
                'pro.id as productId',
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'l.name as namelote',
                'l.fecha_vencimiento as fechavence',
                'prolote.quantity as quantity',
                //   'ccp.lote as lote',
            )
            ->where('ccp.store_id', $centrocostoId)
            ->where('pro.category_id', $categoriaId)
            ->where('prolote.lote_id', $loteId)
            ->where('pro.status', 1)
            ->get();

        // return response()->json(['data' => $data]);
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateCptInventory()
    {
        $productId = request('productId');
        $centrocostoId = request('centrocostoId');
        $fisico = request('fisico');
        $lote = request('lote');
        $fecha_vencimiento = request('fecha_vencimiento');

        DB::table('centro_costo_products')
            ->where('products_id', $productId)
            ->where('centrocosto_id', $centrocostoId)
            ->update([
                'fisico' => $fisico,
                'lote' => $lote,
                'fecha_vencimiento' => $fecha_vencimiento
            ]);

        return response()->json(['success' => 'true']);
    }
}
