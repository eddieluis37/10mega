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
use Illuminate\Support\Facades\Auth;

class siporcentrocostoController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function SiIndex()
    {
        $startDate = '2023-05-01';
        $endDate = '2023-05-08';

        $centros = Centrocosto::Where('status', 1)->get();
        $stores = Store::orderBy('id', 'asc')->get();
        // $categorias = Category::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9])->orderBy('name', 'asc')->get();
        $categorias = Category::orderBy('name', 'asc')->get();

        // llama al metodo para calcular el stock
        //   $this->totales(request());
        $response = $this->totales(request()); // Call the totales method
        $totalStock = $response->getData()->totalStock; // Retrieve the totalStock value from the response

        return view('inventario.si_por_centro_costo.index', compact('centros', 'stores', 'categorias', 'startDate', 'endDate', 'totalStock'));
    }

    public function SishowPorCentroCosto(Request $request)
    {
        $centroId    = $request->input('centroId',   -1);
        $storeId     = $request->input('storeId',    -1);
        $loteId      = $request->input('loteId',     -1);
        $categoriaId = $request->input('categoriaId', -1);

        DB::beginTransaction();
        try {
            $inventarios = Inventario::with(['lote', 'product.category', 'store'])
                ->when($storeId != -1, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->when($centroId != -1, function ($q) use ($centroId) {
                    return $q->whereHas('store', fn($q2) => $q2->where('centrocosto_id', $centroId));
                })
                ->when($loteId != -1, function ($q) use ($loteId) {
                    return $q->where('lote_id', $loteId);
                })
                ->when($categoriaId != -1, function ($q) use ($categoriaId) {
                    return $q->whereHas('product', fn($q2) => $q2->where('category_id', $categoriaId));
                })
                ->get();

            $resultados = [];

            foreach ($inventarios as $inventario) {
                // Movimientos asociados a ingresos (incluye traslado_ingreso y otros movimientos que se registran en store_destino_id)
                $movimientosIngreso = MovimientoInventario::where('lote_id', $inventario->lote_id)
                    ->where('store_destino_id', $inventario->store_id)
                    ->where('product_id', $inventario->product_id)
                    // ->where('status', 1) // Descomenta si necesitas filtrar por movimientos activos
                    ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
                    ->groupBy('tipo')
                    ->get();

                // Movimientos asociados a salidas (traslado_salida se registra en store_origen_id)
                $movimientosSalida = MovimientoInventario::where('lote_id', $inventario->lote_id)
                    ->where('store_origen_id', $inventario->store_id)
                    ->where('product_id', $inventario->product_id)
                    // ->where('status', 1) // Descomenta si necesitas filtrar por movimientos activos
                    ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
                    ->groupBy('tipo')
                    ->get();

                // Sumar otros movimientos que se encuentran en la consulta de ingresos
                $desposteres    = $movimientosIngreso->where('tipo', 'desposteres')->sum('cantidad_total');
                $despostecerdos = $movimientosIngreso->where('tipo', 'despostecerdos')->sum('cantidad_total');
                $enlistments    = $movimientosIngreso->where('tipo', 'enlistments')->sum('cantidad_total');
                $compensadores  = $movimientosIngreso->where('tipo', 'compensadores')->sum('cantidad_total');

                // Para los traslados, se toman de cada consulta según corresponda:
                $trasladoIngreso = $movimientosIngreso->where('tipo', 'traslado_ingreso')->sum('cantidad_total');
                $trasladoSalida  = $movimientosSalida->where('tipo', 'traslado_salida')->sum('cantidad_total');

                $totalVenta  = $movimientosSalida->where('tipo', 'venta')->sum('cantidad_total');
                $totalNotaCredito  = $movimientosSalida->where('tipo', 'notacredito')->sum('cantidad_total');

                // Calcular stock ideal:
                $stockIdeal = ($inventario->cantidad_inventario_inicial
                    + $desposteres
                    + $despostecerdos
                    + $enlistments
                    + $compensadores
                    + $inventario->cantidad_prod_term
                    + $trasladoIngreso) - $trasladoSalida - ($totalVenta - $totalNotaCredito);

                // 1) filtra filas con stockIdeal == 0, las saltamos:
                if ($stockIdeal == 0) {
                    continue;
                }

                // 2) Tomamos el stock físico actual del inventario
                //    Asumimos que stock_fisico ya está cargado en la BD o bien viene en la petición.
                $stockFisico = $inventario->stock_fisico;

                // Actualizar el inventario con el stock ideal calculado
                $inventario->update([
                    'stock_ideal' => $stockIdeal,
                ]);

                // Preparar el resultado para visualización
                $resultados[] = [
                    'StoreNombre'           => $inventario->store->name,
                    'codigoLote'            => $inventario->lote->codigo,
                    'fechaVencimientoLote'  => $inventario->lote->fecha_vencimiento,
                    'CategoriaNombre'       => $inventario->product->category->name,
                    'ProductoCode'          => $inventario->product->code,
                    'ProductoNombre'        => $inventario->product->name,
                    'StockIdeal'            => $inventario->stock_ideal,
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

    public function getAllStores()
    {
        // Todos los stores (para cuando no hay centro seleccionado)
        $stores = Store::all(['id', 'name']);
        return response()->json($stores);
    }

    public function getStores(Request $request)
    {
        $centroId = $request->input('centroId');
        if ($centroId) {
            $stores = Store::where('centrocosto_id', $centroId)
                ->get(['id', 'name']);
        } else {
            $stores = Store::all(['id', 'name']);
        }
        return response()->json($stores);
    }

    public function getLotes(Request $request)
    {
        $storeId = $request->input('storeId');
        $lotes = $storeId
            ? Lote::whereHas('inventarios', fn($q) => $q->where('store_id', $storeId))
            ->get(['id', 'codigo'])
            : Lote::all(['id', 'codigo']);
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
