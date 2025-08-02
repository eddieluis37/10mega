<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\Centro_costo_product;
use App\Models\Listaprecio;
use App\Models\Listapreciodetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class AsignarPreciosProdController extends Controller
{
    public function index()
    {
        /* $category = Category::whereIn('id', [1, 2, 3, 4])->orderBy('id', 'asc')->get(); */
        $category = Category::orderBy('name', 'asc')->get();
        $centros = Centrocosto::Where('status', 1)->get();
        $listaPrecioDetalles = Listapreciodetalle::all();
        $listaPrecio = Listaprecio::all();


        $newToken = Crypt::encrypt(csrf_token());

        return view("asignarPreciosProd.asignar_precios_prod", compact('category', 'centros', 'listaPrecioDetalles', 'listaPrecio'));

        // return view('hola');
        //  return view('inventory.diary');
    }

    public function show(Request $request)
    {
        $centrocostoId = $request->input('centrocostoId');
        $listaprecioId = $request->input('listaprecioId');
        $categoriaId = $request->input('categoriaId');

        //     $costoVariable = $request->input('costo_variable'); // Recibir el valor de costo_variable

        $costoVariable = 0; // 1357 Recibir el valor de costo_variable
        $costoFijo = 0; // 1389 valor de costo_fijo

        $data = DB::table('listapreciodetalles as lpd')
            ->join('listaprecios as lp', 'lp.id', '=', 'lpd.listaprecio_id')
            ->join('products as pro', 'pro.id', '=', 'lpd.product_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            ->select(
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'pro.id as productId',
                'pro.cost as costo',
                'pro.iva as porc_imp_iva',
                'pro.otro_impuesto as porc_imp_ultra_pro',
                'pro.impoconsumo as porc_imp_consumo',
                DB::raw('(pro.cost + ' . $costoVariable . ') as costo_total'), // Sumar costo_variable a pro.cost
                'lpd.porc_util_proyectada as porc_util_proyectada',
                DB::raw('(pro.cost + ' . $costoVariable . ') / (1 - (porc_util_proyectada / 100) ) as precio_proyectado '),
                'lpd.precio as precio',
                'lpd.porc_descuento as porc_descuento',
                DB::raw('((precio - (pro.cost + ' . $costoVariable . ')) - (porc_descuento / 100) * precio) as utilidad '),
                'lpd.utilidad as porc_utilidad',
                DB::raw('((precio - (pro.cost + ' . $costoVariable . ')) - (porc_descuento / 100) * precio) - ' . $costoFijo . ' as contribucion'),
                //  NUEVO campo precio_venta:
                DB::raw('
                (
                  lpd.precio * (1 - (lpd.porc_descuento / 100))
                  * (
                      1 + (
                        (pro.iva + pro.otro_impuesto + pro.impoconsumo) / 100
                      )
                  )
                ) 
                as precio_venta
            '),
                'lpd.status as status',
            )
            ->where('lpd.listaprecio_id', $listaprecioId)
            ->where('pro.category_id', $categoriaId)
            /*       ->where('pro.status', 1)
          /*   ->where('pro.level_product_id', 1) */
            ->get();

        // return response()->json(['data' => $data]);
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function updateAPPSwitch()
    {
        $listaprecioId       = request('listaprecioId');
        $productId           = request('productId');
        $precio              = request('precio');
        $porc_util_proyectada = request('porc_util_proyectada');
        $porc_descuento      = request('porc_descuento');
        $status              = request('status');

        // Cargamos el detalle con su producto:
        $detalle = \App\Models\Listapreciodetalle::with('product')
            ->where('listaprecio_id', $listaprecioId)
            ->where('product_id', $productId)
            ->first();

        if (!$detalle) {
            return response()->json(['error' => 'No encontrado'], 404);
        }

        // Actualizamos los campos que vienen
        if (!is_null($precio)) {
            $detalle->precio = $precio;
        }
        if (!is_null($porc_util_proyectada)) {
            $detalle->porc_util_proyectada = $porc_util_proyectada;
        }
        if (!is_null($porc_descuento)) {
            $detalle->porc_descuento = $porc_descuento;
        }
        if (!is_null($status)) {
            $detalle->status = $status;
        }

        // Recalculamos precio_venta según la fórmula:
        $p       = $detalle->precio;
        $d       = $detalle->porc_descuento / 100;
        $iva     = $detalle->product->iva;
        $ultra   = $detalle->product->otro_impuesto;
        $consumo = $detalle->product->impoconsumo;
        $detalle->precio_venta = ($p * (1 - $d)) * (1 + (($iva + $ultra + $consumo) / 100));

        $detalle->save();

        return response()->json(['success' => true]);
    }
}
