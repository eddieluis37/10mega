<?php

namespace App\Http\Controllers\inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\centros\Centrocosto;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Log;
use App\Models\Lote;



class inventarioController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $startDate = '2023-05-01';
        $endDate = '2023-05-08';

        /*  $category = Category::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9])->orderBy('name', 'asc')->get();
 */
        $centros = Centrocosto::Where('status', 1)->get();
        $stores = Store::orderBy('id', 'asc')->get();
        $lotes = Lote::orderBy('id', 'asc')->get();


        // llama al metodo para calcular el stock
        //   $this->totales(request());
        $response = $this->totales(request()); // Call the totales method
        $totalStock = $response->getData()->totalStock; // Retrieve the totalStock value from the response

        return view('inventario.cierre.index', compact('centros', 'stores', 'lotes', 'startDate', 'endDate', 'totalStock'));
    }

    public function showInvcierre(Request $request)
    {

        $storeId = $request->input('storeId', -1); // Valor por defecto -1 si no está definido
        $loteId = $request->input('loteId', -1);  // Valor por defecto -1 si no está definido

        // Log::info('storeId:', ['storeId' => $storeId]); // larvel.log
        // Log::info('loteId:', ['loteId' => $loteId]); // larvel.log

        DB::beginTransaction();

        try {

            // Obtener todos los inventarios activos con filtros de store y lote
            $inventarios = Inventario::with(['lote', 'product', 'store', 'store.centroCosto'])
                ->when($storeId, function ($query, $storeId) {
                    return $query->where('store_id', $storeId);
                })
                ->when($loteId, function ($query, $loteId) {
                    return $query->where('lote_id', $loteId);
                })
                ->get();

            $resultados = [];

            foreach ($inventarios as $inventario) {
                // Obtener los movimientos de inventario relacionados
                $movimientos = MovimientoInventario::where('lote_id', $inventario->lote_id)
                    ->where('product_id', $inventario->product_id)
                    ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
                    ->groupBy('tipo')
                    ->get();

                // Calcular cantidades por tipo de movimiento
                $compensadores = $movimientos->where('tipo', 'compensadores')->sum('cantidad_total');
                $desposteres = $movimientos->where('tipo', 'desposteres')->sum('cantidad_total');
                $products_terminados = $movimientos->where('tipo', 'products_terminados')->sum('cantidad_total');
                /*  $trasladoIngreso = $movimientos->where('tipo', 'traslado_ingreso')->sum('cantidad_total');
                $trasladoSalida = $movimientos->where('tipo', 'traslado_salida')->sum('cantidad_total');
                $venta = $movimientos->where('tipo', 'venta')->sum('cantidad_total');
                $ajuste = $movimientos->where('tipo', 'ajuste')->sum('cantidad_total');
 */
                // Calcular inventario final
                $cantidadFinal = $inventario->cantidad_inicial;

                // Calcular stock ideal
                $stockIdeal = $compensadores + $inventario->inventario_inicial;

                // Actualizar los campos del inventario
                /*   $inventario->update([
                    'cantidad_final' => $cantidadFinal,
                    'stock_ideal' => $stockIdeal,
                    'inventario_inicial' => $inventario->cantidad_inicial, // Actualizar inventario inicial si aplica
                ]);
 */
                // Guardar los resultados para visualización
                $resultados[] = [
                    'CategoriaNombre' => $inventario->product->category->name,
                    'ProductoNombre' => $inventario->product->name,
                    'CantidadInicial' => $inventario->inventario_inicial,
                    'compraLote' => $desposteres,
                    'ProductoTerminado' => $inventario->cantidad_final,
                    'alistamiento' => $inventario->lote->id,
                    //    'alistamiento' => $inventario->lote->codigo,
                    'compensados' => $compensadores,
                    'trasladoing' => $inventario->lote->id,
                    'trasladosal' => $inventario->product->id,

                    'venta' => $inventario->store->id,
                    'notacredito' => $inventario->store->name,
                    'notadebito' => $inventario->lote->id,
                    'venta_real' => $inventario->lote->id,

                    'stock' => $inventario->lote->id,
                    'fisico' => $inventario->lote->id,
                    //  'Venta' => $venta,
                    //  'Ajuste' => $ajuste,
                    // 'CantidadFinal' => $cantidadFinal,
                    // 'StockIdeal' => $stockIdeal,
                ];
            }

            //  Log::info('Inventarios:', ['inventarios' => $resultados]); // larvel.log


            DB::commit();
            // Devolver $resultados en formato Datatables
            return datatables()->of(collect($resultados))
                ->addIndexColumn() // Agregar un índice
                ->make(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'Error al realizar el cierre de inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllLotes()
    {
        // Obtener todos los lotes
        $lotes = Lote::all(['id', 'codigo']);

        // Retornar los lotes como una respuesta JSON
        return response()->json($lotes);
    }

    public function getLotes(Request $request)
    {
        // Validar la solicitud entrante
        $request->validate([
            'storeId' => 'required|exists:inventarios,store_id', // Verifica que el store_id exista en la tabla inventarios
        ]);

        // Recuperar el storeId de la solicitud
        $storeId = $request->input('storeId');

        // Obtener los lote_ids asociados a la tienda a través de la tabla inventarios
        $loteIds = Inventario::where('store_id', $storeId)->pluck('lote_id');

        // Obtener los lotes asociados a los lote_ids
        $lotes = Lote::whereIn('id', $loteIds)->get(['id', 'codigo']); // Ajusta los nombres de los campos según tu base de datos

        // Retornar los lotes como una respuesta JSON
        return response()->json($lotes);
    }

    public function totales(Request $request)
    {
        $centrocostoId = $request->input('centrocostoId');
        $categoriaId = $request->input('categoriaId');

        $data = DB::table('centro_costo_products as ccp')
            ->join('products as pro', 'pro.id', '=', 'ccp.products_id')
            ->join('centro_costo_store as ccs', 'ccs.centro_costo_id', '=', 'ccp.centrocosto_id')
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id')
            ->select(
                'cat.name as namecategoria',
                'pro.name as nameproducto',
                'ccp.invinicial as invinicial',
                'ccp.compralote as compraLote',
                'ccp.alistamiento',
                'ccp.compensados as compensados',
                'ccp.trasladoing as trasladoing',
                'ccp.trasladosal as trasladosal',
                'ccp.venta as venta',
                'ccp.notacredito as notacredito',
                'ccp.notadebito as notadebito',
                'ccp.venta_real as venta_real',
                'ccp.stock as stock',
                'ccp.fisico as fisico'
            )
            ->where('ccs.store_id', $centrocostoId)
            ->where(function ($query) {
                $query->where('ccp.tipoinventario', 'cerrado')
                    ->orWhere('ccp.tipoinventario', 'inicial');
            })
            ->where('pro.category_id', $categoriaId)
            ->where('pro.status', 1)
            ->get();

        $totalStock = 0;
        $totalInvInicial = 0;
        $totalCompraLote = 0;
        $totalAlistamiento = 0;
        $totalCompensados = 0;
        $totalTrasladoIng = 0;
        $totalVenta = 0;
        $totalTrasladoSal = 0;
        $totalIngresos = 0;
        $totalSalidas = 0;
        $totalConteoFisico = 0;

        $diferenciaKilos = 0;
        $porcMermaPermitida = 0;
        $difKilosPermitidos = 0;
        $difKilos = 0;
        $porcMerma = 0;
        $difPorcentajeMerma = 0;

        foreach ($data as $item) {

            $stock = ($item->invinicial + $item->compraLote + $item->alistamiento + $item->compensados + $item->trasladoing) - ($item->venta_real + $item->trasladosal);
            $item->stock = round($stock, 2);
            $totalStock += $stock;

            $ingresos = ($item->invinicial + $item->compraLote + $item->alistamiento + $item->compensados + $item->trasladoing);
            $item->ingresos = round($ingresos, 2);
            $totalIngresos += $ingresos;

            $ventas = ($item->venta - $item->notacredito) + $item->notadebito;
            $item->ventas = round($ventas, 2);

            $salidas =  $item->trasladosal + (($item->venta - $item->notacredito) + $item->notadebito);
            $item->salidas = round($salidas, 2);
            $totalSalidas += $salidas;

            $totalInvInicial += $item->invinicial;
            $totalCompraLote += $item->compraLote;
            $totalAlistamiento += $item->alistamiento;
            $totalCompensados += $item->compensados;
            $totalTrasladoIng += $item->trasladoing;
            $totalVenta += $item->ventas;
            $totalTrasladoSal += $item->trasladosal;

            $totalConteoFisico += $item->fisico;
            $diferenciaKilos = $totalConteoFisico - $totalStock;
        }

        if ($totalIngresos <= 0) {
            $totalIngresos = 1;
        }

        $porcMerma = $diferenciaKilos / $totalIngresos;

        $porcMermaPermitida = 0.005;
        $difKilosPermitidos = -1 * ($totalIngresos * $porcMermaPermitida);
        $difKilos = $diferenciaKilos - $difKilosPermitidos;


        $difPorcentajeMerma = $porcMerma + $porcMermaPermitida;

        return response()->json(
            [
                'totalStock' => number_format($totalStock, 2),

                'totalInvInicial' => number_format($totalInvInicial, 2),
                'totalCompraLote' => number_format($totalCompraLote, 2),
                'totalAlistamiento' => number_format($totalAlistamiento, 2),
                'totalCompensados' => number_format($totalCompensados, 2),
                'totalTrasladoing' => number_format($totalTrasladoIng, 2),

                'totalVenta' => number_format($totalVenta, 2),
                'totalTrasladoSal' => number_format($totalTrasladoSal, 2),

                'totalIngresos' => number_format($totalIngresos, 2),
                'totalSalidas' => number_format($totalSalidas, 2),

                'totalConteoFisico' => number_format($totalConteoFisico, 2),

                'diferenciaKilos' => number_format($diferenciaKilos, 2),
                'difKilosPermitidos' => number_format($difKilosPermitidos, 2),
                'porcMerma' => number_format($porcMerma * 100, 2),
                'porcMermaPermitida' => number_format($porcMermaPermitida * 100, 2),
                'difKilos' => number_format($difKilos, 2),
                'difPorcentajeMerma' => number_format($difPorcentajeMerma * 100, 2),

            ]
        );
    }
}
