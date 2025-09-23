<?php

namespace App\Http\Controllers\sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Third;
use App\Models\centros\Centrocosto;
use App\Models\compensado\Compensadores;
use App\Models\compensado\Compensadores_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\caja\Caja;
use App\Models\CuentaPorCobrar;
use App\Models\Formapago;
use App\Models\Inventario;
use App\Models\Product;
use App\Models\Listapreciodetalle;
use App\Models\Lote;
use App\Models\MovimientoInventario;
use App\Models\Notacredito;
use App\Models\NotaCreditoDetalle;
use App\Models\Productcomposition;
use App\Models\PromotionDetail;
use App\Models\Sale;
use App\Models\SaleCaja;
use App\Models\SaleDetail;
use App\Models\Store;
use App\Models\Subcentrocosto;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class saleController extends Controller
{

    public function getDireccionesByClienteSale($cliente_id)
    {
        $direcciones = Third::where('id', $cliente_id)->orderBy('id', 'desc')->get(); // despliega las mas reciente
        return response()->json($direcciones);
    }

    public function index()
    {
        $direccion = Third::where(function ($query) {
            $query->whereNotNull('direccion')
                ->orWhereNotNull('direccion1')
                ->orWhereNotNull('direccion2')
                ->orWhereNotNull('direccion3')
                ->orWhereNotNull('direccion4')
                ->orWhereNotNull('direccion5')
                ->orWhereNotNull('direccion6')
                ->orWhereNotNull('direccion7')
                ->orWhereNotNull('direccion8')
                ->orWhereNotNull('direccion9');
        })
            ->select('direccion', 'direccion1', 'direccion2', 'direccion3', 'direccion4', 'direccion5', 'direccion6', 'direccion7', 'direccion8', 'direccion9')
            ->get();

        $ventas = Sale::get();
        //   $centros = Centrocosto::WhereIn('id', [1])->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // Selecciona el primer centro de costo como valor por defecto (si existe)
        $defaultCentro = $centros->first();

        $clientes = Third::Where('cliente', 1)->get();
        $vendedores = Third::Where('vendedor', 1)->get();
        $domiciliarios = Third::Where('domiciliario', 1)->get();
        $subcentrodecostos = Subcentrocosto::get();

        return view('sale.index', compact('ventas', 'direccion', 'centros', 'defaultCentro', 'clientes', 'vendedores', 'domiciliarios', 'subcentrodecostos'));
    }

    public function index_parrilla()
    {
        $direccion = Third::where(function ($query) {
            $query->whereNotNull('direccion')
                ->orWhereNotNull('direccion1')
                ->orWhereNotNull('direccion2')
                ->orWhereNotNull('direccion3')
                ->orWhereNotNull('direccion4')
                ->orWhereNotNull('direccion5')
                ->orWhereNotNull('direccion6')
                ->orWhereNotNull('direccion7')
                ->orWhereNotNull('direccion8')
                ->orWhereNotNull('direccion9');
        })
            ->select('direccion', 'direccion1', 'direccion2', 'direccion3', 'direccion4', 'direccion5', 'direccion6', 'direccion7', 'direccion8', 'direccion9')
            ->get();

        $ventas = Sale::get();
        //   $centros = Centrocosto::WhereIn('id', [1])->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // Selecciona el primer centro de costo como valor por defecto (si existe)
        $defaultCentro = $centros->first();

        $clientes = Third::Where('cliente', 1)->get();
        $vendedores = Third::Where('vendedor', 1)->get();
        $domiciliarios = Third::Where('domiciliario', 1)->get();
        $subcentrodecostos = Subcentrocosto::get();

        return view('sale_parrilla.index', compact('ventas', 'direccion', 'centros', 'defaultCentro', 'clientes', 'vendedores', 'domiciliarios', 'subcentrodecostos'));
    }

    public function show()
    {
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $userCentrocostos = Auth::user()->stores->pluck('centrocosto_id')->unique()->toArray();

        $data = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select([
                'sa.*',
                'tird.name as namethird',
                'c.name as namecentrocosto',
                // Sub-select para detectar si hay detalles de tipo combo o receta
                DB::raw("(SELECT COUNT(*) 
                  FROM sale_details sd 
                  JOIN products p ON sd.product_id = p.id
                  WHERE sd.sale_id = sa.id
                    AND p.type IN ('combo','receta')
                ) > 0 as has_comanda")
            ])
            ->whereIn('c.id', $userCentrocostos)
            ->whereYear('sa.fecha_venta', Carbon::now()->year)
            ->whereMonth('sa.fecha_venta', Carbon::now()->month)
            ->get();


        return Datatables::of($data)->addIndexColumn()
            ->addColumn('status', function ($data) {
                $statusText = '';
                switch ($data->status) {
                    case 0:
                        $statusText = '<span class="badge bg-info">Open</span>';
                        break;
                    case 1:
                        $statusText = '<span class="badge bg-success">Close</span>';
                        break;
                    case 2:
                        $statusText = '<span class="badge bg-danger">Annulled</span>';
                        break;
                    case 3:
                        // Mostrar estado de devolución con el contador de notas de crédito
                        $creditNotesInfo = isset($data->credit_notes_count) && $data->credit_notes_count > 0
                            ? ' (' . $data->credit_notes_count . '/2)'
                            : '';
                        $statusText = '<span class="badge bg-warning">Returned' . $creditNotesInfo . '</span>';
                        break;
                    default:
                        $statusText = '<span class="badge bg-secondary">Unknown</span>';
                        break;
                }
                return $statusText;
            })
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->created_at);
                return $date->format('M-d. H:i');
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="text-center">';

                if ($data->has_comanda) {
                    $btn .= '<a href="sale/showComanda/' . $data->id . '" 
                     class="btn btn-primary" 
                     title="Ver Comanda" 
                     target="_blank">
                    <i class="fas fa-receipt"></i>
                 </a>';
                }

                // Si el campo tipo es '1', se muestran los botones de Despacho y Remisión
                if ($data->tipo == '1') {
                    $btn .= '<a href="sale/showDespacho/' . $data->id . '" class="btn btn-warning" title="Ver Despacho" target="_blank">
                                D
                             </a>';
                    $btn .= '<a href="sale/showRemision/' . $data->id . '" class="btn btn-success" title="Ver Remisión" target="_blank">
                                R
                             </a>';
                }

                // Botón para ver la factura (siempre visible)
                $btn .= '<a href="sale/showFactura/' . $data->id . '" class="btn btn-dark" title="Ver Factura" target="_blank">
                            <i class="far fa-file-pdf"></i>
                         </a>';


                // Según el estado de la venta se muestran otras acciones:
                if ($data->status == 0) {
                    $btn .= '<a href="sale/create/' . $data->id . '" class="btn btn-dark" title="Detalles">
                                <i class="fas fa-directions"></i>
                             </a>';
                } elseif ($data->status == 1) {
                    // Venta cerrada: mostrar botón de devolución parcial y anulación total.
                    // Verificar si ya se alcanzó el límite de notas de crédito (máximo 2)
                    $creditNotesCount = isset($data->credit_notes_count) ? $data->credit_notes_count : 0;
                    if ($creditNotesCount < 2) {
                        $btn .= '<a href="#" class="btn btn-info" title="Devolución parcial (' . $creditNotesCount . '/2)" onclick="confirmPartialReturn(' . $data->id . ')">
                                   <i class="fas fa-undo-alt"></i>
                                 </a>';
                    }
                    /*  // Mostrar botón de anulación solo si no hay notas de crédito o hay exactamente 1
                    if ($creditNotesCount == 0 || $creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    } */
                } elseif ($data->status == 2) {
                    $btn .= '<button class="btn btn-dark" title="Venta cancelada" disabled>
                                <i class="fas fa-ban"></i>
                             </button>';
                } elseif ($data->status == 3) {
                    // Venta con devolución parcial: verificar si todavía se pueden hacer más devoluciones
                    $creditNotesCount = isset($data->credit_notes_count) ? $data->credit_notes_count : 0;
                    if ($creditNotesCount < 2) {
                        $btn .= '<a href="#" class="btn btn-info" title="Devolución parcial (' . $creditNotesCount . '/2)" onclick="confirmPartialReturn(' . $data->id . ')">
                                   <i class="fas fa-undo-alt"></i>
                                 </a>';
                    } else {
                        $btn .= '<button class="btn btn-dark" title="Máximo de devoluciones alcanzado" disabled>
                                    <i class="fas fa-undo"></i>
                                 </button>';
                    }
                    /*  if ($creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    } */
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'date', 'action'])
            ->make(true);
    }

    public function showParrilla()
    {
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $userCentrocostos = Auth::user()->stores->pluck('centrocosto_id')->unique()->toArray();

        $data = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select([
                'sa.*',
                'tird.name as namethird',
                'c.name as namecentrocosto',
                // Sub-select para detectar si hay detalles de tipo combo o receta
                DB::raw("(SELECT COUNT(*) 
                  FROM sale_details sd 
                  JOIN products p ON sd.product_id = p.id
                  WHERE sd.sale_id = sa.id                    
                    AND p.type IN ('combo','receta')
                ) > 0 as has_comanda")
            ])
            ->whereIn('sa.tipo', ['2', '3'])
            ->whereIn('c.id', $userCentrocostos)
            ->whereYear('sa.fecha_venta', Carbon::now()->year)
            ->whereMonth('sa.fecha_venta', Carbon::now()->month)
            ->get();


        return Datatables::of($data)->addIndexColumn()
            ->addColumn('status', function ($data) {
                $statusText = '';
                switch ($data->status) {
                    case 0:
                        $statusText = '<span class="badge bg-info">Open</span>';
                        break;
                    case 1:
                        $statusText = '<span class="badge bg-success">Close</span>';
                        break;
                    case 2:
                        $statusText = '<span class="badge bg-danger">Annulled</span>';
                        break;
                    case 3:
                        // Mostrar estado de devolución con el contador de notas de crédito
                        $creditNotesInfo = isset($data->credit_notes_count) && $data->credit_notes_count > 0
                            ? ' (' . $data->credit_notes_count . '/2)'
                            : '';
                        $statusText = '<span class="badge bg-warning">Returned' . $creditNotesInfo . '</span>';
                        break;
                    default:
                        $statusText = '<span class="badge bg-secondary">Unknown</span>';
                        break;
                }
                return $statusText;
            })
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->created_at);
                return $date->format('M-d. H:i');
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="text-center">';

                if ($data->has_comanda) {
                    $btn .= '<a href="sale/showComanda/' . $data->id . '" 
                     class="btn btn-primary" 
                     title="Ver Comanda" 
                     target="_blank">
                    <i class="fas fa-receipt"></i>
                 </a>';
                }

                // Si el campo tipo es '3', se muestran los botones de Despacho y Remisión
                if ($data->tipo == '3') {
                    $btn .= '<a href="sale/showDespacho/' . $data->id . '" class="btn btn-warning" title="Ver Despacho" target="_blank">
                                D
                             </a>';
                    $btn .= '<a href="sale/showRemision/' . $data->id . '" class="btn btn-success" title="Ver Remisión" target="_blank">
                                R
                             </a>';
                }

                // Botón para ver la factura (siempre visible)
                $btn .= '<a href="sale/showFactura/' . $data->id . '" class="btn btn-dark" title="Ver Factura" target="_blank">
                            <i class="far fa-file-pdf"></i>
                         </a>';


                // Según el estado de la venta se muestran otras acciones:
                if ($data->status == 0) {
                    $btn .= '<a href="sale_parrilla/create/' . $data->id . '" class="btn btn-dark" title="Detalles">
                                <i class="fas fa-directions"></i>
                             </a>';
                } elseif ($data->status == 1) {
                    // Venta cerrada: mostrar botón de devolución parcial y anulación total.
                    // Verificar si ya se alcanzó el límite de notas de crédito (máximo 2)
                    $creditNotesCount = isset($data->credit_notes_count) ? $data->credit_notes_count : 0;
                    if ($creditNotesCount < 2) {
                        $btn .= '<a href="#" class="btn btn-info" title="Devolución parcial (' . $creditNotesCount . '/2)" onclick="confirmPartialReturn(' . $data->id . ')">
                                   <i class="fas fa-undo-alt"></i>
                                 </a>';
                    }
                    /* // Mostrar botón de anulación solo si no hay notas de crédito o hay exactamente 1
                    if ($creditNotesCount == 0 || $creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    } */
                } elseif ($data->status == 2) {
                    $btn .= '<button class="btn btn-dark" title="Venta cancelada" disabled>
                                <i class="fas fa-ban"></i>
                             </button>';
                } elseif ($data->status == 3) {
                    // Venta con devolución parcial: verificar si todavía se pueden hacer más devoluciones
                    $creditNotesCount = isset($data->credit_notes_count) ? $data->credit_notes_count : 0;
                    if ($creditNotesCount < 2) {
                        $btn .= '<a href="#" class="btn btn-info" title="Devolución parcial (' . $creditNotesCount . '/2)" onclick="confirmPartialReturn(' . $data->id . ')">
                                   <i class="fas fa-undo-alt"></i>
                                 </a>';
                    } else {
                        $btn .= '<button class="btn btn-dark" title="Máximo de devoluciones alcanzado" disabled>
                                    <i class="fas fa-undo"></i>
                                 </button>';
                    }
                    /*  if ($creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    } */
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'date', 'action'])
            ->make(true);
    }

    public $valorCambio;

    public function storeRegistroPago(Request $request, $ventaId)
    {
        // Obtener y sanitizar los valores del request
        $valor_a_pagar_efectivo = str_replace(['.', ',', '$', '#'], '', $request->input('valor_a_pagar_efectivo'));
        $forma_pago_tarjeta_id  = $request->input('forma_pago_tarjeta_id');
        $forma_pago_otros_id    = $request->input('forma_pago_otros_id');
        $forma_pago_credito_id  = $request->input('forma_pago_credito_id');

        $codigo_pago_tarjeta    = $request->input('codigo_pago_tarjeta');
        $codigo_pago_otros      = $request->input('codigo_pago_otros');
        $codigo_pago_credito    = $request->input('codigo_pago_credito');

        $valor_a_pagar_tarjeta  = str_replace(['.', ',', '$', '#'], '', $request->input('valor_a_pagar_tarjeta'));
        $valor_a_pagar_otros    = str_replace(['.', ',', '$', '#'], '', $request->input('valor_a_pagar_otros'));

        $valor_a_pagar_credito  = $request->input('valor_a_pagar_credito');
        if (is_null($valor_a_pagar_credito)) {
            $valor_a_pagar_credito = 0;
        }
        $valor_a_pagar_credito  = str_replace(['.', ',', '$', '#'], '', $valor_a_pagar_credito);

        $valor_pagado           = str_replace(['.', ',', '$', '#'], '', $request->input('valor_pagado'));
        $cambio                 = str_replace(['.', ',', '$', '#'], '', $request->input('cambio'));

        $status = '0'; // Estado pendiente (1 = pagado)

        try {
            // Obtener el usuario autenticado (cajero)
            $cajeroId = $request->user()->id;

            // Buscar la caja en estado "open" asociada al cajero
            $caja = Caja::where('cajero_id', $cajeroId)
                ->where('estado', 'open')
                ->latest()
                ->first();

            if (!$caja) {
                return redirect()->route('sale.index')
                    ->with('error', 'No se encontró una caja abierta para el cajero actual.');
            }

            // Crear un registro en SaleCaja
            $saleCaja = new SaleCaja();
            $saleCaja->sale_id = $ventaId;
            $saleCaja->caja_id = $caja->id;
            $saleCaja->save();

            $currentDateTime = Carbon::now();
            $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));

            // Actualizar la venta
            $venta = Sale::findOrFail($ventaId);
            $venta->user_id                  = $request->user()->id;
            $venta->forma_pago_tarjeta_id    = $forma_pago_tarjeta_id;
            $venta->forma_pago_otros_id      = $forma_pago_otros_id;
            $venta->forma_pago_credito_id    = $forma_pago_credito_id;
            $venta->codigo_pago_tarjeta      = $codigo_pago_tarjeta;
            $venta->codigo_pago_otros        = $codigo_pago_otros;
            $venta->codigo_pago_credito      = $codigo_pago_credito;
            $venta->valor_a_pagar_tarjeta    = $valor_a_pagar_tarjeta;
            $venta->valor_a_pagar_efectivo   = $valor_a_pagar_efectivo;
            $venta->valor_a_pagar_otros      = $valor_a_pagar_otros;
            $venta->valor_a_pagar_credito    = $valor_a_pagar_credito;
            $venta->valor_pagado             = $valor_pagado;
            $venta->cambio                   = $cambio;
            $venta->status                   = $status;
            $venta->fecha_cierre = $currentDateFormat;
            $venta->save();

            // Llamar al método para cargar el inventario
            $this->cargarInventariocr($ventaId);

            // Regenerar la sesión si es necesario
            session()->regenerate();

            // Obtener el campo 'tipo' para la vista
            $tipoVenta = $venta->tipo;

            // Pasamos tipoVenta para controlar la redirección
            return view('sale.redirectAndInvoice', compact('ventaId', 'tipoVenta'))
                ->with('success', 'Guardado correctamente y cargado al inventario.');
        } catch (\Throwable $th) {
            return redirect()->route('sale.index')
                ->with('error', 'Error al procesar el pago: ' . $th->getMessage());
        }
    }

    /**
     * Busca PromotionDetail aplicable.
     *
     * @param int $productId
     * @param int|null $storeId
     * @param float $quantity
     * @param int|null $loteId
     * @param int|null $inventarioId
     * @return PromotionDetail|null
     */
    private function getApplicablePromotionDetail($productId, $storeId, $quantity, $loteId = null, $inventarioId = null)
    {
        $now = Carbon::now();
        $dateNow = $now->toDateString();
        $timeNow = $now->format('H:i:s');

        $q = PromotionDetail::query()
            ->whereHas('promotion', fn($qq) => $qq->where('status', '1')) // promotion activo
            // cantidad minima requerida <= cantidad proporcionada
            //  ->where('quantity', '<=', $quantity)
            // fecha válida (si tus campos permiten null, ajustar)
            ->whereDate('fecha_inicio', '<=', $dateNow)
            ->whereDate('fecha_final', '>=', $dateNow)
            // hora válida: si hora_inicio/hora_final son null -> todo el día; sino validar rango
            /*  ->where(function ($qq) use ($timeNow) {
                $qq->where(function ($t) {
                    $t->whereNull('hora_inicio')->whereNull('hora_final');
                })->orWhere(function ($t) use ($timeNow) {
                    $t->whereTime('hora_inicio', '<=', $timeNow)
                        ->whereTime('hora_final', '>=', $timeNow);
                });
            }) */
            // coincidencias posibles
            ->where(function ($w) use ($productId, $loteId, $inventarioId, $storeId) {
                $w->where('product_id', $productId);

                if (!is_null($loteId)) {
                    $w->Where('lote_id', $loteId);
                }
                if (!is_null($inventarioId)) {
                    $w->Where('inventario_id', $inventarioId);
                }
                if (!is_null($storeId)) {
                    $w->Where('store_id', $storeId);
                }
            });

        // Priorizar por coincidencia exacta (product first, luego lote, inventario, store),
        // y dentro de cada grupo elegir mayor porc_desc
        $prioritySql = "CASE 
        WHEN product_id = ? THEN 1 
        WHEN lote_id = ? THEN 2 
        WHEN inventario_id = ? THEN 3 
        WHEN store_id = ? THEN 4 
        ELSE 5 END";

        $q->orderByRaw($prioritySql, [
            $productId,
            $loteId ?? 0,
            $inventarioId ?? 0,
            $storeId ?? 0
        ])->orderByDesc('porc_desc');

        return $q->first();
    }




    public function cuentasPorCobrar($ventaId)
    {
        $venta = Sale::find($ventaId);
        $clienteId = $venta->third_id;
        $formaPagoCreditoId =  $venta->forma_pago_credito_id;
        $formaPagos = Formapago::find($formaPagoCreditoId);
        $diasCredito = $formaPagos->diascredito;
        $cXc = new CuentaPorCobrar();
        $cXc->sale_id = $ventaId;
        $cXc->third_id = $clienteId;
        $cXc->deuda_inicial = $venta->valor_a_pagar_credito;
        $cXc->deuda_x_cobrar = $venta->valor_a_pagar_credito;
        $cXc->fecha_inicial = now();
        $cXc->fecha_vencimiento = now()->addDays($diasCredito);
        $cXc->save();
    }

    public function create($id)
    {
        $venta = Sale::find($id);

        $storeIds = [0];

        // Se obtienen los productos que tengan inventarios en las bodegas seleccionadas con stock_ideal > 0
        $productsQuery = Product::query();
        $productsQuery->whereHas('inventarios', function ($q) use ($storeIds) {
            $q->whereIn('store_id', $storeIds)
                ->where('stock_ideal', '>', 0);
        });
        $products = $productsQuery->get();

        // Se obtienen todos los inventarios que cumplan la condición, cargando las relaciones 'store' y 'lote'
        $inventarios = Inventario::with('store', 'lote')
            ->whereIn('store_id', $storeIds)
            ->where('stock_ideal', '>', 0)
            ->get();

        $results = [];

        foreach ($products as $prod) {
            // Filtrar todos los inventarios que correspondan al producto actual
            $inventariosProducto = $inventarios->where('product_id', $prod->id);

            foreach ($inventariosProducto as $inventario) {
                // Validar la fecha de vencimiento del lote
                if ($inventario->lote && \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->gte(\Carbon\Carbon::now())) {
                    $text = "Bg: " . ($inventario->store ? $inventario->store->name : 'N/A')
                        . " - " . ($inventario->lote ? $inventario->lote->codigo : 'Sin código')
                        . " - " . \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->format('d/m/Y')
                        . " - " . $prod->name
                        . " - Stk: " . $inventario->stock_ideal;

                    $results[] = [
                        // Se utiliza el id del inventario para que cada registro sea único
                        'id'             => $inventario->id,
                        'text'           => $text,
                        'lote_id'        => $inventario->lote ? $inventario->lote->id : null,
                        'inventario_id'  => $inventario->id,
                        'stock_ideal'    => $inventario->stock_ideal,
                        'store_id'       => $inventario->store ? $inventario->store->id : null,
                        'store_name'     => $inventario->store ? $inventario->store->name : null,
                        'barcode'        => $prod->barcode,
                        // Se conserva el id del producto en otra propiedad para otros usos
                        'product_id'     => $prod->id,
                    ];
                }
            }
        }

        $ventasdetalle = $this->getventasdetalle($id, $venta->centrocosto_id);
        $arrayTotales = $this->sumTotales($id);

        $datacompensado = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select('sa.*', 'tird.name as namethird', 'c.name as namecentrocosto', 'tird.porc_descuento as porc_descuento_cliente')
            ->where('sa.id', $id)
            ->get();
        $status = '';
        $estadoVenta = ($datacompensado[0]->status);

        if ($estadoVenta) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($estadoVenta) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }

        $statusInventory = "";
        if ($datacompensado[0]->status == "true") {
            $statusInventory = "true";
        } else {
            $statusInventory = "false";
        }

        $display = "";
        if ($status == "false" || $statusInventory == "true") {
            $display = "display:none;";
        }

        $detalleVenta = $this->getventasdetail($id);

        return view('sale.create', compact('datacompensado', 'results', 'id', 'detalleVenta', 'ventasdetalle', 'arrayTotales', 'status', 'statusInventory', 'display'));
    }

    public function create_parrilla($id)
    {
        $venta = Sale::find($id);

        $storeIds = [0];

        // Se obtienen los productos que tengan inventarios en las bodegas seleccionadas con stock_ideal > 0
        $productsQuery = Product::query();
        $productsQuery->whereHas('inventarios', function ($q) use ($storeIds) {
            $q->whereIn('store_id', $storeIds)
                ->where('stock_ideal', '>', 0);
        });
        $products = $productsQuery->get();

        // Se obtienen todos los inventarios que cumplan la condición, cargando las relaciones 'store' y 'lote'
        $inventarios = Inventario::with('store', 'lote')
            ->whereIn('store_id', $storeIds)
            ->where('stock_ideal', '>', 0)
            ->get();

        $results = [];

        foreach ($products as $prod) {
            // Filtrar todos los inventarios que correspondan al producto actual
            $inventariosProducto = $inventarios->where('product_id', $prod->id);

            foreach ($inventariosProducto as $inventario) {
                // Validar la fecha de vencimiento del lote
                if ($inventario->lote && \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->gte(\Carbon\Carbon::now())) {
                    $text = "Bg: " . ($inventario->store ? $inventario->store->name : 'N/A')
                        . " - " . ($inventario->lote ? $inventario->lote->codigo : 'Sin código')
                        . " - " . \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->format('d/m/Y')
                        . " - " . $prod->name
                        . " - Stk: " . $inventario->stock_ideal;

                    $results[] = [
                        // Se utiliza el id del inventario para que cada registro sea único
                        'id'             => $inventario->id,
                        'text'           => $text,
                        'lote_id'        => $inventario->lote ? $inventario->lote->id : null,
                        'inventario_id'  => $inventario->id,
                        'stock_ideal'    => $inventario->stock_ideal,
                        'store_id'       => $inventario->store ? $inventario->store->id : null,
                        'store_name'     => $inventario->store ? $inventario->store->name : null,
                        'barcode'        => $prod->barcode,
                        // Se conserva el id del producto en otra propiedad para otros usos
                        'product_id'     => $prod->id,
                    ];
                }
            }
        }

        $ventasdetalle = $this->getventasdetalle($id, $venta->centrocosto_id);
        $arrayTotales = $this->sumTotales($id);

        $datacompensado = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select('sa.*', 'tird.name as namethird', 'c.name as namecentrocosto', 'tird.porc_descuento as porc_descuento_cliente')
            ->where('sa.id', $id)
            ->get();
        $status = '';
        $estadoVenta = ($datacompensado[0]->status);

        if ($estadoVenta) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($estadoVenta) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }

        $statusInventory = "";
        if ($datacompensado[0]->status == "true") {
            $statusInventory = "true";
        } else {
            $statusInventory = "false";
        }

        $display = "";
        if ($status == "false" || $statusInventory == "true") {
            $display = "display:none;";
        }

        $detalleVenta = $this->getventasdetail($id);

        return view('sale_parrilla.create', compact('datacompensado', 'results', 'id', 'detalleVenta', 'ventasdetalle', 'arrayTotales', 'status', 'statusInventory', 'display'));
    }

    public function getventasdetalle($ventaId, $centrocostoId)
    {
        $detail = DB::table('sale_details as sd')
            ->join('products as pro', 'sd.product_id', '=', 'pro.id')
            ->join('inventarios as i', 'pro.id', '=', 'i.product_id')
            ->select('sd.*', 'pro.name as nameprod', 'pro.code',  'i.stock_ideal as stock')
            /*  ->selectRaw('i.invinicial + i.compraLote + i.alistamiento +
            i.compensados + i.trasladoing - (i.venta + i.trasladosal) stock') */
            ->where([
                ['i.store_id', $centrocostoId],
                ['sd.sale_id', $ventaId],
            ])->orderBy('sd.id', 'DESC')->get();

        return $detail;
    }

    public function search(Request $request)
    {
        $term = $request->input('q', '');
        $saleId = $request->input('sale_id');
        $sale = Sale::findOrFail($saleId);

        // --- NORMALIZAR TÉRMINO ---
        $originalTerm = $term;
        // quitamos 's08' si aparece al inicio (no sensible a mayúsc/minúsc)
        $cleanTerm = preg_replace('/^S08/i', '', $originalTerm);
        $cleanTerm = trim($cleanTerm);

        // candidatos para coincidencia exacta de barcode (13 dígitos)
        $barcodeCandidates = [];
        if (preg_match('/^\d{13}$/', $originalTerm)) {
            $barcodeCandidates[] = $originalTerm;
        }
        if ($cleanTerm !== $originalTerm && preg_match('/^\d{13}$/', $cleanTerm)) {
            $barcodeCandidates[] = $cleanTerm;
        }
        // --- /NORMALIZAR TÉRMINO ---

        // stores del centro de costo
        $storeIds = Store::where('centrocosto_id', $sale->centrocosto_id)
            ->pluck('id')
            ->toArray();

        // Query de productos con inventario > 0 en esas bodegas
        $prodQ = Product::query()
            ->whereHas('inventarios', function ($q) use ($storeIds) {
                $q->whereIn('store_id', $storeIds)
                    ->where('stock_ideal', '>', 0);
            });

        if ($originalTerm) {
            // Si tenemos candidatos válidos de barcode (13 dígitos), buscar por ellos
            if (!empty($barcodeCandidates)) {
                // evita duplicados
                $barcodeCandidates = array_values(array_unique($barcodeCandidates));
                $prodQ->whereIn('barcode', $barcodeCandidates);
            } else {
                // búsqueda por nombre / code / lote usando el término original (no limpio)
                $prodQ->where(function ($q) use ($originalTerm) {
                    $q->where('name', 'LIKE', "%{$originalTerm}%")
                        ->orWhere('code', 'LIKE', "%{$originalTerm}%")
                        ->orWhereHas('lotes', function ($q2) use ($originalTerm) {
                            $q2->where('codigo', 'LIKE', "%{$originalTerm}%");
                        });
                });
            }
        }

        $products = $prodQ->get();
        $productIds = $products->pluck('id')->toArray();

        // Inventarios válidos
        $inventarios = Inventario::with(['store', 'lote'])
            ->whereIn('store_id', $storeIds)
            ->whereIn('product_id', $productIds)
            ->where('stock_ideal', '>', 0)
            ->whereHas('lote', function ($q) {
                $q->where('fecha_vencimiento', '>=', Carbon::now());
            })
            ->get();

        $results = [];

        foreach ($products as $prod) {
            $invItems = $inventarios->where('product_id', $prod->id);
            foreach ($invItems as $inv) {
                // Buscar promoción aplicable considerando cantidad = 1 (búsqueda)
                $appliedPromotion = $this->getApplicablePromotionDetail(
                    $prod->id,
                    $inv->store_id,
                    1,
                    $inv->lote_id,
                    $inv->id
                );

                $promo_percent = 0;
                $promo_min_quantity = null;
                $promo_applies = false;
                $applied_promotion_id = null;

                if ($appliedPromotion) {
                    $promo_percent = floatval($appliedPromotion->porc_desc);
                    $promo_min_quantity = floatval($appliedPromotion->quantity);
                    $applied_promotion_id = $appliedPromotion->id;
                    $promo_applies = ($appliedPromotion->quantity <= 1);
                }

                $results[] = [
                    'id'                  => $inv->id,
                    'text'                => sprintf(
                        "Bg: %s - %s - %s - %s - Stk: %d",
                        $inv->store->name,
                        $inv->lote->codigo,
                        Carbon::parse($inv->lote->fecha_vencimiento)->format('d/m/Y'),
                        $prod->name,
                        $inv->stock_ideal
                    ),
                    'lote_id'             => $inv->lote->id,
                    'inventario_id'       => $inv->id,
                    'stock_ideal'         => $inv->stock_ideal,
                    'store_id'            => $inv->store->id,
                    'store_name'          => $inv->store->name,
                    'barcode'             => $prod->barcode,
                    // barcode limpio (si tenía prefijo s08, se lo quitamos)
                    'barcode_clean'       => preg_replace('/^s08/i', '', $prod->barcode),
                    'product_id'          => $prod->id,
                    'promo_percent'       => $promo_percent,
                    'promo_min_quantity'  => $promo_min_quantity,
                    'promo_applies'       => $promo_applies,
                    'applied_promotion_id' => $applied_promotion_id,
                ];
            }
        }

        return response()->json($results);
    }


    public function searchOk(Request $request)
    {
        $term   = $request->input('q', '');
        $saleId = $request->input('sale_id');
        $sale   = Sale::findOrFail($saleId);

        // stores del centro de costo
        $storeIds = Store::where('centrocosto_id', $sale->centrocosto_id)
            ->pluck('id')
            ->toArray();

        // Query de productos con inventario > 0 en esas bodegas
        $prodQ = Product::query()
            ->whereHas('inventarios', function ($q) use ($storeIds) {
                $q->whereIn('store_id', $storeIds)
                    ->where('stock_ideal', '>', 0);
            });

        if ($term) {
            if (preg_match('/^\d{13}$/', $term)) {
                $prodQ->where('barcode', $term);
            } else {
                $prodQ->where(function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%")
                        ->orWhere('code', 'LIKE', "%{$term}%")
                        ->orWhereHas('lotes', function ($q2) use ($term) {
                            $q2->where('codigo', 'LIKE', "%{$term}%");
                        });
                });
            }
        }

        $products   = $prodQ->get();
        $productIds = $products->pluck('id')->toArray();

        // Inventarios válidos
        $inventarios = Inventario::with(['store', 'lote'])
            ->whereIn('store_id', $storeIds)
            ->whereIn('product_id', $productIds)
            ->where('stock_ideal', '>', 0)
            ->whereHas('lote', function ($q) {
                $q->where('fecha_vencimiento', '>=', Carbon::now());
            })
            ->get();

        $results = [];

        foreach ($products as $prod) {
            $invItems = $inventarios->where('product_id', $prod->id);
            foreach ($invItems as $inv) {
                // Buscar promoción aplicable considerando cantidad = 1 (búsqueda)
                $appliedPromotion = $this->getApplicablePromotionDetail(
                    $prod->id,
                    $inv->store_id,
                    1,               // asumimos qty 1 para indicar si aplica con cantidad 1
                    $inv->lote_id,
                    $inv->id
                );

                $promo_percent = 0;
                $promo_min_quantity = null;
                $promo_applies = false;
                $applied_promotion_id = null;

                if ($appliedPromotion) {
                    $promo_percent = floatval($appliedPromotion->porc_desc);
                    $promo_min_quantity = floatval($appliedPromotion->quantity);
                    $applied_promotion_id = $appliedPromotion->id;
                    // si la promoción requiere <= 1 (es decir quantity <= 1) entonces aplica a qty 1
                    $promo_applies = ($appliedPromotion->quantity <= 1);
                }

                $results[] = [
                    'id'               => $inv->id,
                    'text'             => sprintf(
                        "Bg: %s - %s - %s - %s - Stk: %d",
                        $inv->store->name,
                        $inv->lote->codigo,
                        Carbon::parse($inv->lote->fecha_vencimiento)->format('d/m/Y'),
                        $prod->name,
                        $inv->stock_ideal
                    ),
                    'lote_id'          => $inv->lote->id,
                    'inventario_id'    => $inv->id,
                    'stock_ideal'      => $inv->stock_ideal,
                    'store_id'         => $inv->store->id,
                    'store_name'       => $inv->store->name,
                    'barcode'          => $prod->barcode,
                    'product_id'       => $prod->id,
                    // datos de promoción
                    'promo_percent'    => $promo_percent,
                    'promo_min_quantity' => $promo_min_quantity,
                    'promo_applies'    => $promo_applies,
                    'applied_promotion_id' => $applied_promotion_id,
                ];
            }
        }

        return response()->json($results);
    }


    public function create_reg_pago($id)
    {
        $forma_pago_tarjeta = Formapago::Where('tipoformapago', '=', 'TARJETA')->get();
        $forma_pago_otros = Formapago::Where('tipoformapago', '=', 'OTROS')->get();
        $forma_pago_credito = Formapago::Where('tipoformapago', '=', 'CREDITO')->get();

        $dataVenta = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select('sa.*', 'tird.name as namethird', 'c.name as namebodega', 'tird.porc_descuento', 'sa.total_iva', 'sa.vendedor_id')
            ->where('sa.id', $id)
            ->get();

        $vendedorId = $dataVenta[0]->vendedor_id;
        $vendedor = Third::where('id', $vendedorId)->value('name');
        $dataVenta[0]->vendedor_name = $vendedor;

        // dd($dataVenta);

        $venta = Sale::find($id);
        $producto = Product::get();

        $arrayTotales = $this->sumTotales($id);

        $descuento = $dataVenta[0]->porc_descuento / 100 * $arrayTotales['TotalValorAPagar'];
        $subtotal = $arrayTotales['TotalBrutoSinDescuento'] - $arrayTotales['TotalDescuentos'];

        return view('sale.registrar_pago', compact('venta', 'arrayTotales', 'producto', 'dataVenta', 'descuento', 'subtotal', 'forma_pago_tarjeta', 'forma_pago_otros', 'forma_pago_credito'));
    }

    public function sumTotales($id)
    {
        $TotalBrutoSinDescuento = Sale::where('id', $id)->value('total_bruto');
        $TotalDescuentos = Sale::where('id', $id)->value('descuentos');
        $TotalBruto = (float)SaleDetail::Where([['sale_id', $id]])->sum('total_bruto');
        $TotalIva = (float)SaleDetail::Where([['sale_id', $id]])->sum('iva');
        $TotalOtroImpuesto = (float)SaleDetail::Where([['sale_id', $id]])->sum('otro_impuesto');
        $TotalImpAlConusmo = (float)SaleDetail::Where([['sale_id', $id]])->sum('impoconsumo');
        $TotalValorAPagar = (float)SaleDetail::Where([['sale_id', $id]])->sum('total');

        $array = [
            'TotalBruto' => $TotalBruto,
            'TotalBrutoSinDescuento' => $TotalBrutoSinDescuento,
            'TotalDescuentos' => $TotalDescuentos,
            'TotalValorAPagar' => $TotalValorAPagar,
            'TotalIva' => $TotalIva,
            'TotalOtroImpuesto' => $TotalOtroImpuesto,
            'TotalImpAlConusmo' => $TotalImpAlConusmo,
        ];

        return $array;
    }

    public function getventasdetail($ventaId)
    {
        $detalles = DB::table('sale_details as de')
            ->join('products as pro', 'de.product_id', '=', 'pro.id')
            ->select('de.*', 'pro.name as nameprod', 'pro.code', 'de.porc_iva', 'de.iva', 'de.porc_otro_impuesto',)
            ->where([
                ['de.sale_id', $ventaId],
                /*   ['de.status', 1] */
            ])->get();

        return $detalles;
    }

    public function getproducts(Request $request)
    {
        $prod = Product::Where([
            /*   ['category_id',$request->categoriaId], */
            ['status', 1]
        ])->get();
        return response()->json(['products' => $prod]);
    }

    public function savedetail(Request $request)
    {
        try {
            $prodParam = $request->producto;
            $isComboRecetaSinInventario = false;
            $product = null;
            $inventario = null;
            $stockDisponible = null;

            // Detectar si viene un producto combo/receta sin inventario
            if (is_string($prodParam) && Str::startsWith($prodParam, 'CR-')) {
                $isComboRecetaSinInventario = true;
                $productId = (int) Str::after($prodParam, 'CR-');
                $product   = Product::find($productId);

                Log::info('Procesando producto combo/receta sin inventario', [
                    'producto_param' => $prodParam,
                    'product_id'     => $productId,
                ]);

                if (!$product || !in_array($product->type, ['combo', 'receta'])) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'No se encontró el producto combo/receta.'
                    ], 422);
                }

                //
                // === 1) ASIGNAR DINÁMICAMENTE LA BODEGA QUE CONTENGA PALABRAS DEL NOMBRE DE USUARIO ===
                //
                // Tomo el nombre completo del usuario autenticado, lo separo en palabras
                $userName   = auth()->user()->name;
                $nameWords  = preg_split('/\s+/', $userName, -1, PREG_SPLIT_NO_EMPTY);

                // Valor enviado desde la vista: AUTOMÁTICAMENTE será 'AUTOSERVICIO', 'BAR' o 'PARRILLA'
                $bodegaTipo = $request->input('tipobodega');

                // Busco todas las bodegas asignadas al usuario
                $userStoreIds = DB::table('store_user')
                    ->where('user_id', auth()->id())
                    ->pluck('store_id');

                /* // Query para encontrar la primera bodega cuyo nombre contenga alguna palabra del usuario
                $store = Store::whereIn('id', $userStoreIds)
                    ->where(function ($q) use ($nameWords) {
                        foreach ($nameWords as $word) {
                            $q->orWhere('name', 'LIKE', "%{$word}%");
                        }
                    })
                    ->first(); */

                // Busco la primera bodega cuyo nombre contenga la palabra enviada
                $store = Store::whereIn('id', $userStoreIds)
                    ->where('name', 'LIKE', "%{$bodegaTipo}%")
                    ->first();

                if (!$store) {
                    Log::warning('No se encontró bodega con nombre que coincida con palabras del usuario', [
                        'user_id'   => auth()->id(),
                        'store_ids' => $userStoreIds->all(),
                    ]);
                    return response()->json([
                        'status'  => 0,
                        'message' => "No tienes asignada ninguna bodega que contenga '{$bodegaTipo}'."
                    ], 422);
                }

                $storeId = $store->id;

                // Asignar lote por defecto (por ejemplo, 1)
                $loteId = 1;
            } else {
                // Caso normal: producto ya es inventario_id válido
                Log::info('Iniciando proceso de guardado de detalle de venta', [
                    'ventaId'       => $request->ventaId,
                    'inventario_id' => $prodParam,
                    'lote_id'       => $request->lote_id,
                    'store'         => $request->store,
                ]);

                $inventario = Inventario::with('lote')->find($prodParam);
                if (!$inventario) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'No se encontró el inventario seleccionado.'
                    ], 422);
                }

                $product         = $inventario->product;
                $stockDisponible = $inventario->stock_ideal;

                if (is_null($stockDisponible)) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'No hay stock disponible para este producto en la bodega y lote seleccionados.'
                    ], 422);
                }
            }

            //
            // 2) Validación de datos comunes
            //
            $rules = [
                'ventaId'  => 'required',
                'producto' => 'required',
                'price'    => 'required',
                'quantity' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0.1'],
            ];
            $messages = [
                'ventaId.required'  => 'El compensado es requerido',
                'producto.required' => 'El producto es requerido',
                'price.required'    => 'El precio de compra es requerido',
                'quantity.required' => 'La cantidad es requerida.',
                'quantity.numeric'  => 'La cantidad debe ser un número.',
                'quantity.min'      => 'La cantidad debe ser mayor a 0.1.',
            ];

            if ($isComboRecetaSinInventario) {
                // no validar lote ni bodega ni max stock aquí
            } else {
                $rules['lote_id']    = 'required';
                $rules['store']      = 'required';
                $rules['quantity'][] = 'max:' . $stockDisponible;
                $messages['lote_id.required'] = 'El lote es requerido';
                $messages['store.required']   = 'La bodega es requerida';
                $messages['quantity.max']     = 'La cantidad no puede superar el stock disponible (' . $stockDisponible . ').';
            }

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$isComboRecetaSinInventario && $request->quantity > $stockDisponible) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'La cantidad supera el stock disponible (' . $stockDisponible . ').'
                ], 422);
            }

            //
            // 3) Cálculos de precios, descuentos e impuestos
            //
            $formatCantidad = new metodosrogercodeController();
            $price    = $formatCantidad->MoneyToNumber($request->price);
            $price_venta    = $formatCantidad->MoneyToNumber($request->price_venta);
            $quantity = $request->quantity;

            $precioBruto  = $price * $quantity;

            $porcPromo    = $request->get('promo_percent', 0);
            $promoValue   = $precioBruto * ($porcPromo / 100);

            $porcDesc     = $request->get('porc_desc', 0);
            $descProd     = $precioBruto * ($porcDesc / 100);
            $porcDescClie = $request->get('porc_descuento_cliente', 0);
            $descClie     = $precioBruto * ($porcDescClie / 100);
            $totalDesc    = $descProd + $descClie + $promoValue;
            $netoSinImp   = $precioBruto - $totalDesc;

            $porcIva         = $request->get('porc_iva', 0);
            $porcOtroImpto   = $request->get('porc_otro_impuesto', 0);
            $porcImpoconsumo = $request->get('porc_impoconsumo', 0);
            $iva             = $netoSinImp * ($porcIva / 100);
            $otroImpto       = $netoSinImp * ($porcOtroImpto / 100);
            $impoconsumo     = $netoSinImp * ($porcImpoconsumo / 100);
            $totalImpuestos  = $iva + $otroImpto + $impoconsumo;

            //
            // 4) Preparar datos para guardar sale_detail
            //
            if ($isComboRecetaSinInventario) {
                $dataDetail = [
                    'sale_id'           => $request->ventaId,
                    'inventario_id'     => null,
                    'store_id'          => $storeId,
                    'product_id'        => $product->id,
                    'price'             => $price,
                    'quantity'          => $quantity,
                    'lote_id'           => $loteId,
                    'porc_desc'         => $porcDesc,
                    'descuento'         => $descProd,
                    'descuento_cliente' => $descClie,
                    'porc_iva'          => $porcIva,
                    'iva'               => $iva,
                    'porc_otro_impuesto' => $porcOtroImpto,
                    'otro_impuesto'     => $otroImpto,
                    'porc_impoconsumo'  => $porcImpoconsumo,
                    'impoconsumo'       => $impoconsumo,
                    'promo_percent'     => $porcPromo,
                    'promo_value'       => $promoValue,
                    'total_bruto'       => $precioBruto,
                    'price_venta'       => $price_venta,
                    'total'             => $netoSinImp + $totalImpuestos,
                ];
            } else {
                $dataDetail = [
                    'sale_id'           => $request->ventaId,
                    'inventario_id'     => $inventario->id,
                    'store_id'          => $inventario->store_id,
                    'product_id'        => $inventario->product_id,
                    'price'             => $price,
                    'quantity'          => $quantity,
                    'lote_id'           => $inventario->lote_id,
                    'porc_desc'         => $porcDesc,
                    'descuento'         => $descProd,
                    'descuento_cliente' => $descClie,
                    'porc_iva'          => $porcIva,
                    'iva'               => $iva,
                    'porc_otro_impuesto' => $porcOtroImpto,
                    'otro_impuesto'     => $otroImpto,
                    'porc_impoconsumo'  => $porcImpoconsumo,
                    'impoconsumo'       => $impoconsumo,
                    'promo_percent'     => $porcPromo,
                    'promo_value'       => $promoValue,
                    'total_bruto'       => $precioBruto,
                    'price_venta'       => $price_venta,
                    'total'             => $netoSinImp + $totalImpuestos,
                ];
            }

            // 5) Guardar o actualizar detalle
            if ($request->regdetailId > 0) {
                SaleDetail::find($request->regdetailId)->update($dataDetail);
            } else {
                SaleDetail::create($dataDetail);
            }

            // 6) Actualizar totales de la venta
            $sale        = Sale::find($request->ventaId);
            $detalles    = $sale->details;
            $sale->items = $detalles->count();
            $sale->total_bruto           = $detalles->sum(fn($d) => $d->quantity * $d->price);
            $sale->descuentos            = $detalles->sum(fn($d) => $d->descuento + $d->descuento_cliente + $d->promo_value);
            $sale->total_valor_a_pagar   = $detalles->sum('total');
            $sale->total_iva             = $detalles->sum('iva');
            $sale->total_otros_impuestos = $detalles->sum('otro_impuesto');
            $sale->save();

            // 7) Respuesta exitosa
            return response()->json([
                'status'       => 1,
                'message'      => 'Agregado correctamente',
                'array'        => $this->getventasdetail($request->ventaId),
                'arrayTotales' => $this->sumTotales($request->ventaId),
            ]);
        } catch (\Throwable $th) {
            Log::error('Error en savedetail', [
                'error' => $th->getMessage(),
                'stack' => $th->getTraceAsString()
            ]);
            return response()->json([
                'status'  => 0,
                'message' => 'Ocurrió un error interno.'
            ], 500);
        }
    }

    public function store(Request $request) // Guardar venta por domicilio
    {
        try {
            $rules = [
                'ventaId' => 'required',
                'cliente' => 'required',
                'vendedor' => 'required',
                'direccion_envio' => 'required',
                'centrocosto' => 'required',
                'subcentrodecosto' => 'required',
            ];
            $messages = [
                'ventaId.required' => 'El ventaId es requerido',
                'cliente.required' => 'El cliente es requerido',
                'vendedor.required' => 'El proveedor es requerido',
                'direccion_envio.required' => 'La dirección de envio es requerida',
                'centrocosto.required' => 'El centro costo es requerido',
                'subcentrodecosto.required' => 'El subcentro de costo es requerido',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar si ya existe la venta con ventaId
            $getReg = Sale::firstWhere('id', $request->ventaId);
            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Mover al siguiente lunes
                $dateNextMonday = $current_date->format('Y-m-d'); // Formato Y-m-d
                $id_user = Auth::user()->id;

                $venta = new Sale();
                $venta->user_id = $id_user;
                $venta->centrocosto_id = $request->centrocosto;
                $venta->third_id = $request->cliente;
                $venta->direccion_envio = $request->direccion_envio;
                $venta->vendedor_id = $request->vendedor;
                $venta->domiciliario_id = $request->domiciliario;
                $venta->subcentrocostos_id = $request->subcentrodecosto;
                $venta->fecha_venta = $currentDateFormat;
                // $venta->fecha_cierre = $dateNextMonday;  // Puedes habilitar si es necesario
                $venta->total_bruto = 0;
                $venta->descuentos = 0;
                $venta->subtotal = 0;
                $venta->total = 0;
                $venta->total_otros_descuentos = 0;
                $venta->valor_a_pagar_efectivo = 0;
                $venta->valor_a_pagar_tarjeta = 0;
                $venta->valor_a_pagar_otros = 0;
                $venta->valor_a_pagar_credito = 0;
                $venta->valor_pagado = 0;
                $venta->cambio = 0;
                $venta->items = 0;
                $venta->tipo = "1"; // Domicilio

                // --- INICIO: Generación de consecutivo para la facturacion de venta ---
                // Recuperar el centro de costo y su prefijo
                $centroCosto = CentroCosto::find($request->centrocosto);
                if (!$centroCosto) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Centro de costo no encontrado'
                    ], 404);
                }
                $prefijo = $centroCosto->prefijo;
                // Consultar la última venta creada para este centro de costo para determinar el consecutivo
                $lastSale = Sale::where('centrocosto_id', $request->centrocosto)
                    ->orderBy('consec', 'desc')
                    ->first();
                $consecutivo = $lastSale ? $lastSale->consec + 1 : 1;
                // Generar la resolución con el formato {prefijo}-{consecutivo} (con 5 dígitos)
                $generaConsecutivo = $prefijo . '-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);

                $venta->consecutivo = $generaConsecutivo;
                $venta->consec = $consecutivo;     // Campo para llevar el número secuencial numérico
                // --- FIN: Generación de consecutivo ---

                $venta->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    'registroId' => $venta->id
                ]);
            } else {
                // En caso de que ya exista la venta se actualizan algunos campos
                $getReg = Sale::firstWhere('id', $request->ventaId);
                $getReg->third_id = $request->vendedor;
                $getReg->centrocosto_id = $request->centrocosto;
                $getReg->subcentrocostos_id = $request->subcentrodecosto;
                $getReg->factura = $request->factura;
                $getReg->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    'registroId' => 0
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function store_parrilla(Request $request) // Guardar venta parrrilla por domicilio
    {
        try {
            $rules = [
                'ventaId' => 'required',
                'cliente' => 'required',
                'vendedor' => 'required',
                'direccion_envio' => 'required',
                'centrocosto' => 'required',
                'subcentrodecosto' => 'required',
            ];
            $messages = [
                'ventaId.required' => 'El ventaId es requerido',
                'cliente.required' => 'El cliente es requerido',
                'vendedor.required' => 'El proveedor es requerido',
                'direccion_envio.required' => 'La dirección de envio es requerida',
                'centrocosto.required' => 'El centro costo es requerido',
                'subcentrodecosto.required' => 'El subcentro de costo es requerido',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar si ya existe la venta con ventaId
            $getReg = Sale::firstWhere('id', $request->ventaId);
            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Mover al siguiente lunes
                $dateNextMonday = $current_date->format('Y-m-d'); // Formato Y-m-d
                $id_user = Auth::user()->id;

                $venta = new Sale();
                $venta->user_id = $id_user;
                $venta->tipo = '3'; // Parrilla Domicilio
                $venta->centrocosto_id = $request->centrocosto;
                $venta->third_id = $request->cliente;
                $venta->direccion_envio = $request->direccion_envio;
                $venta->vendedor_id = $request->vendedor;
                $venta->domiciliario_id = $request->domiciliario;
                $venta->subcentrocostos_id = $request->subcentrodecosto;
                $venta->fecha_venta = $currentDateFormat;

                $venta->total_bruto = 0;
                $venta->descuentos = 0;
                $venta->subtotal = 0;
                $venta->total = 0;
                $venta->total_otros_descuentos = 0;
                $venta->valor_a_pagar_efectivo = 0;
                $venta->valor_a_pagar_tarjeta = 0;
                $venta->valor_a_pagar_otros = 0;
                $venta->valor_a_pagar_credito = 0;
                $venta->valor_pagado = 0;
                $venta->cambio = 0;
                $venta->items = 0;

                // --- INICIO: Generación de consecutivo para la facturacion de venta ---
                // Recuperar el centro de costo y su prefijo
                $centroCosto = CentroCosto::find($request->centrocosto);
                if (!$centroCosto) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Centro de costo no encontrado'
                    ], 404);
                }
                $prefijo = $centroCosto->prefijo;
                // Consultar la última venta creada para este centro de costo para determinar el consecutivo
                $lastSale = Sale::where('centrocosto_id', $request->centrocosto)
                    ->orderBy('consec', 'desc')
                    ->first();
                $consecutivo = $lastSale ? $lastSale->consec + 1 : 1;
                // Generar la resolución con el formato {prefijo}-{consecutivo} (con 5 dígitos)
                $generaConsecutivo = $prefijo . '-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);

                $venta->consecutivo = $generaConsecutivo;
                $venta->consec = $consecutivo;     // Campo para llevar el número secuencial numérico
                // --- FIN: Generación de consecutivo ---

                $venta->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    'registroId' => $venta->id
                ]);
            } else {
                // En caso de que ya exista la venta se actualizan algunos campos
                $getReg = Sale::firstWhere('id', $request->ventaId);
                $getReg->third_id = $request->vendedor;
                $getReg->centrocosto_id = $request->centrocosto;
                $getReg->subcentrocostos_id = $request->subcentrodecosto;
                $getReg->factura = $request->factura;
                $getReg->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    'registroId' => 0
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $reg = Sale::where('id', $request->id)->first();
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
    }

    public function editCompensado(Request $request)
    {
        $reg = SaleDetail::find($request->id);
        // Asegúrate de que el modelo SaleDetail tenga en su $fillable el campo 'inventario_id'
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        try {
            $compe = SaleDetail::where('id', $request->id)->first();
            $compe->delete();

            $arraydetail = $this->getventasdetail($request->ventaId);

            $arrayTotales = $this->sumTotales($request->ventaId);


            $sale = Sale::find($request->ventaId);
            $sale->items = SaleDetail::where('sale_id', $sale->id)->count();
            $sale->descuentos = 0;
            $sale->total_iva = 0;
            $sale->total_otros_impuestos = 0;
            $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();
            $totalBruto = 0;
            $totalDesc = 0;
            $total_valor_a_pagar = $saleDetails->where('sale_id', $sale->id)->sum('total');
            $sale->total_valor_a_pagar = $total_valor_a_pagar;
            $totalBruto = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->quantity * $saleDetail->price;
            });
            $totalDesc = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->descuento + $saleDetail->descuento_cliente;
            });
            $sale->total_bruto = $totalBruto;
            $sale->descuentos = $totalDesc;
            $sale->save();



            return response()->json([
                'status' => 1,
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales,
                'message' => 'Se realizo con exito'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function destroyVenta(Request $request)
    {
        try {
            $compe = Sale::where('id', $request->id)->first();
            $compe->status = 0;
            $compe->save();

            return response()->json([
                'status' => 1,
                'message' => 'Se realizo con exito'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function SaObtenerPreciosProducto(Request $request)
    {
        $centrocostoId = $request->input('centrocosto');
        $clienteId     = $request->input('cliente');
        $productId     = $request->input('productId');
        $inventarioId  = $request->input('inventario_id', null); // opcional
        $quantity      = $request->input('quantity', 1); // opcional, por defecto 1

        $cliente = Third::find($clienteId);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Obtener el precio desde listapreciodetalle (igual que antes)
        $producto = Listapreciodetalle::join('products as prod', 'listapreciodetalles.product_id', '=', 'prod.id')
            ->join('thirds as t', 'listapreciodetalles.listaprecio_id', '=', 't.id')
            ->where('prod.id', $productId)
            ->where('t.id', $cliente->listaprecio_genericid)
            ->select(
                'listapreciodetalles.precio',
                'listapreciodetalles.precio_venta',
                'prod.iva',
                'prod.otro_impuesto',
                'prod.impoconsumo',
                'listapreciodetalles.porc_descuento'
            )
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Determinar store_id para buscar promociones:
        // preferimos inventario->store_id si se envió inventario_id, si no, tomamos la primera store del centro de costo
        $storeId = null;
        $loteId = null;
        if ($inventarioId) {
            $inv = Inventario::find($inventarioId);
            if ($inv) {
                $storeId = $inv->store_id;
                $loteId = $inv->lote_id;
            }
        }
        if (!$storeId) {
            $firstStore = Store::where('centrocosto_id', $centrocostoId)->first();
            $storeId = $firstStore ? $firstStore->id : null;
        }

        // Buscar promotion detail aplicable usando la cantidad (si viene)
        $appliedPromotion = $this->getApplicablePromotionDetail($productId, $storeId, $quantity, $loteId, $inventarioId);

        $promo_percent = 0;
        $promo_min_quantity = null;
        $promo_value = 0;
        $applied_promotion_id = null;

        $unitPrice = floatval($producto->precio);
        $lineBase = $unitPrice * floatval($quantity); // valor bruto sin impuestos y sin descuentos de cliente

        if ($appliedPromotion) {
            $promo_percent = floatval($appliedPromotion->porc_desc);
            $promo_min_quantity = floatval($appliedPromotion->quantity);
            $applied_promotion_id = $appliedPromotion->id;

            // Calculamos el valor total de la promoción sobre la base lineal (puedes cambiar la base según reglas)
            $promo_value = round(($lineBase * $promo_percent) / 100.0, 2);
        }

        return response()->json([
            'precio' => $producto->precio,
            'precio_venta' => $producto->precio_venta,
            'iva' => $producto->iva,
            'otro_impuesto' => $producto->otro_impuesto,
            'impoconsumo' => $producto->impoconsumo,
            'porc_descuento' => $producto->porc_descuento,
            // promoción calculada
            'promo_percent' => $promo_percent,
            'promo_min_quantity' => $promo_min_quantity,
            'promo_value' => $promo_value,
            'applied_promotion_id' => $applied_promotion_id,
        ]);
    }


    public function storeVentaMostrador(Request $request) // POS-Mostrador
    {
        //   $centros = Centrocosto::WhereIn('id', [1])->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // Selecciona el primer centro de costo como valor por defecto (si existe)
        $defaultCentro = $centros->first();

        $userId = Auth::id();
        $cacheKey = "sale_in_process_user_{$userId}";

        // 1) Si ya hay una venta en curso para este usuario, devolvemos la misma
        if (Cache::has($cacheKey)) {
            return response()->json([
                'status'     => 2,
                'message'    => 'Ya tienes una venta en curso',
                'registroId' => Cache::get($cacheKey . '_id')
            ], 200);
        }

        // 2) Marcamos que hay una venta en proceso 
        Cache::put($cacheKey, true, now()->addSeconds(20));

        DB::beginTransaction();
        try {
            $currentDateTime = Carbon::now();
            $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date->modify('next monday'); // Move to the next Monday
            $dateNextMonday = $current_date->format('Y-m-d');
            $id_user = Auth::user()->id;

            $venta = new Sale();
            $venta->user_id = $id_user;
            $venta->centrocosto_id = $defaultCentro->id;
            $venta->subcentrocostos_id = 7;
            $venta->third_id = ($defaultCentro->id == 8) ? 157 : 1;
            $venta->vendedor_id = 1;

            $venta->fecha_venta = $currentDateFormat;
            //    $venta->fecha_cierre = $currentDateFormat;
            $venta->total_bruto = 0;
            $venta->descuentos = 0;
            $venta->subtotal = 0;
            $venta->total = 0;
            $venta->total_otros_descuentos = 0;
            $venta->valor_a_pagar_efectivo = 0;
            $venta->valor_a_pagar_tarjeta = 0;
            $venta->valor_a_pagar_otros = 0;
            $venta->valor_a_pagar_credito = 0;
            $venta->valor_pagado = 0;
            $venta->cambio = 0;
            $venta->items = 0;
            $venta->valor_pagado = 0;
            $venta->cambio = 0;
            $venta->in_process = true;       // si se usa columna en BD

            $venta->save();

            // --- INICIO: Generación de consecutivo para la facturacion de venta ---
            // Recuperar el centro de costo y su prefijo
            $centroCosto = CentroCosto::find($request->centrocosto);
            if (!$centroCosto) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Centro de costo no encontrado'
                ], 404);
            }
            $prefijo = $centroCosto->prefijo;
            // Consultar la última venta creada para este centro de costo para determinar el consecutivo
            $lastSale = Sale::where('centrocosto_id', $request->centrocosto)
                ->orderBy('consec', 'desc')
                ->first();
            $consecutivo = $lastSale ? $lastSale->consec + 1 : 1;
            // Generar la resolución con el formato {prefijo}-{consecutivo} (con 5 dígitos)
            $generaConsecutivo = $prefijo . '-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);

            $venta->consecutivo = $generaConsecutivo;
            $venta->consec = $consecutivo;     // Campo para llevar el número secuencial numérico
            // --- FIN: Generación de consecutivo ---

            $venta->save();

            DB::commit();

            return response()->json([
                'status'     => 1,
                'message'    => 'Inicio de venta por mostrador',
                'registroId' => $venta->id
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            // Limpiamos la bandera para que puedan reintentar
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . '_id');

            return response()->json([
                'status'  => 0,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeParrillaMostrador(Request $request) // Parrilla-Mostrador
    {
        //   $centros = Centrocosto::WhereIn('id', [1])->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // Selecciona el primer centro de costo como valor por defecto (si existe)
        $defaultCentro = $centros->first();

        $userId = Auth::id();
        $cacheKey = "sale_in_process_user_{$userId}";

        // 1) Si ya hay una venta en curso para este usuario, devolvemos la misma
        if (Cache::has($cacheKey)) {
            return response()->json([
                'status'     => 2,
                'message'    => 'Ya tienes una venta en curso',
                'registroId' => Cache::get($cacheKey . '_id')
            ], 200);
        }

        // 2) Marcamos que hay una venta en proceso 
        Cache::put($cacheKey, true, now()->addSeconds(20));

        DB::beginTransaction();
        try {
            $currentDateTime = Carbon::now();
            $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date->modify('next monday'); // Move to the next Monday
            $dateNextMonday = $current_date->format('Y-m-d');
            $id_user = Auth::user()->id;

            $venta = new Sale();
            $venta->user_id = $id_user;
            $venta->tipo = "2"; // Mostrador parrilla
            $venta->centrocosto_id = $defaultCentro->id;
            $venta->subcentrocostos_id = 2;
            $venta->third_id = ($defaultCentro->id == 8) ? 157 : 1;
            $venta->vendedor_id = 1;
            $venta->fecha_venta = $currentDateFormat;
            // $venta->fecha_cierre = $currentDateFormat;
            $venta->total_bruto = 0;
            $venta->descuentos = 0;
            $venta->subtotal = 0;
            $venta->total = 0;
            $venta->total_otros_descuentos = 0;
            $venta->valor_a_pagar_efectivo = 0;
            $venta->valor_a_pagar_tarjeta = 0;
            $venta->valor_a_pagar_otros = 0;
            $venta->valor_a_pagar_credito = 0;
            $venta->valor_pagado = 0;
            $venta->cambio = 0;
            $venta->items = 0;
            $venta->valor_pagado = 0;
            $venta->cambio = 0;
            $venta->in_process = true;       // si se usa columna en BD

            $venta->save();

            // --- INICIO: Generación de consecutivo para la facturacion de venta ---
            // Recuperar el centro de costo y su prefijo
            $centroCosto = CentroCosto::find($request->centrocosto);
            if (!$centroCosto) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Centro de costo no encontrado'
                ], 404);
            }
            $prefijo = $centroCosto->prefijo;
            // Consultar la última venta creada para este centro de costo para determinar el consecutivo
            $lastSale = Sale::where('centrocosto_id', $request->centrocosto)
                ->orderBy('consec', 'desc')
                ->first();
            $consecutivo = $lastSale ? $lastSale->consec + 1 : 1;
            // Generar la resolución con el formato {prefijo}-{consecutivo} (con 5 dígitos)
            $generaConsecutivo = $prefijo . '-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);

            $venta->consecutivo = $generaConsecutivo;
            $venta->consec = $consecutivo;     // Campo para llevar el número secuencial numérico
            // --- FIN: Generación de consecutivo ---

            $venta->save();

            DB::commit();

            return response()->json([
                'status'     => 1,
                'message'    => 'Inicio de venta por mostrador',
                'registroId' => $venta->id
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            // Limpiamos la bandera para que puedan reintentar
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . '_id');

            return response()->json([
                'status'  => 0,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerNombreCliente($id)
    {
        $venta = Sale::find($id);
        if ($venta) {
            $nombreCliente = $venta->third->name;
            return "Nombre del cliente: " . $nombreCliente;
        } else {
            return "Venta no encontrada";
        }
    }

    public function cargarInventariocr($ventaId)
    {
        DB::beginTransaction();
        try {
            // Log::debug('Inicio de cargarInventariocr', ['ventaId' => $ventaId]);

            // 1) Obtener la venta con sus detalles
            $sale = Sale::with('saleDetails.product')
                ->where('id', $ventaId)
                ->where('status', '0')
                ->firstOrFail();

            /*    Log::debug('Turno diario de la venta', [
                'sale_id'      => $sale->id,
                'turno_diario' => $sale->turno_diario,
                'centrocosto'  => $sale->centrocosto_id,
            ]); */

            if (!$sale) {
                //   Log::debug('Venta no encontrada o ya cerrada', ['ventaId' => $ventaId]);
                return response()->json(['status' => 1, 'message' => 'Venta no encontrada o cerrada.'], 404);
            }
            //    Log::debug('Venta encontrada', ['sale_id' => $sale->id]);

            // 2) Filtrar detalles activos
            $details = $sale->saleDetails->where('status', '1');
            if ($details->isEmpty()) {
                //  Log::debug('No hay detalles de venta activos', ['sale_id' => $sale->id]);
                return response()->json(['status' => 0, 'message' => 'No hay detalles de venta activos.'], 404);
            }

            // 3) Procesar cada detalle según tipo
            foreach ($details as $d) {
                $prod   = $d->product;
                $store  = $d->store_id;
                $qty    = $d->quantity;
                $costo  = $d->total_bruto;

                //  Log::debug('Procesando detalle', ['detail_id' => $d->id, 'product_id' => $prod->id, 'type' => $prod->type, 'store_id' => $store, 'quantity' => $qty]);

                switch ($prod->type) {
                    case 'combo':
                        $this->procesarMovimiento($ventaId, $prod->id, $store, $d->lote_id, $qty, $costo, 'COMBO');
                        $this->descontarComponentes($ventaId, $prod->id, $store, $qty, 'COMPONENTE-COMBO');
                        break;

                    case 'receta':
                        // buscar lote para receta
                        $loteRec = $this->buscarLoteMasCercano($prod->id, $store);
                        if (!$loteRec) {
                            Log::warning('No hay lote válido para receta, asignando lote_id=1', ['product_id' => $prod->id, 'store_id' => $store]);
                            $loteId = 1;
                        } else {
                            $loteId = $loteRec->id;
                            Log::debug('Lote asignado para receta', ['product_id' => $prod->id, 'lote_id' => $loteId]);
                        }
                        $this->procesarMovimiento($ventaId, $prod->id, $store, $loteId, $qty, $costo, 'RECETA');
                        $this->descontarComponentes($ventaId, $prod->id, $store, $qty, 'COMPONENTE-RECETA');
                        break;

                    default:
                        $this->procesarMovimiento($ventaId, $prod->id, $store, $d->lote_id, $qty, $costo, 'ESTÁNDAR');
                }
            }

            // Cuentas por cobrar
            if ($sale->valor_a_pagar_credito > 0) {
                //    Log::debug('Generando cuentas por cobrar');
                $this->cuentasPorCobrar($sale->id);
            }

            // Marcar cierre
            $sale->update(['status' => '1', 'fecha_cierre' => Carbon::now()]);
            //     Log::debug('Venta cerrada', ['sale_id' => $sale->id]);

            DB::commit();
            Log::debug('Transacción completada');

            // Si la venta es tipo 2 o 3, redirige a sale_parrilla.index
            if (in_array($sale->tipo, ['2', '3'])) {
                return redirect()
                    ->route('sale.index_parrilla')
                    ->with('success', 'Venta Parrilla Cargada Exitosamente');
            }

            // En cualquier otro caso (0 o 1), redirige a sale.index
            return redirect()
                ->route('sale.index')
                ->with('success', 'Cargado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cargarInventariocr', [
                'error'   => $e->getMessage(),
                'ventaId' => $ventaId
            ]);

            if (in_array($sale->tipo, ['2', '3'])) {
                return redirect()
                    ->route('sale.index_parrilla')
                    ->with('error', 'Error: ' . $e->getMessage());
            }

            return redirect()
                ->route('sale.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Busca lote más próximo a vencer con inventario
     */
    protected function buscarLoteMasCercano($productId, $storeId)
    {
        return Lote::join('inventarios as i', 'lotes.id', '=', 'i.lote_id')
            ->where('i.product_id', $productId)
            ->where('i.store_id', $storeId)
            ->whereDate('lotes.fecha_vencimiento', '>=', Carbon::now()->toDateString())
            ->orderBy('lotes.fecha_vencimiento')
            ->select('lotes.*')
            ->first();
    }

    /**
     * Procesa movimiento e inventario
     */
    protected function procesarMovimiento($saleId, $productId, $storeId, $loteId, $qty, $costo = null, $ctx = '')
    {
        $tag = "[{$ctx}]";
        // previene duplicados usando nombres de columna correctos
        if (MovimientoInventario::where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->where('store_origen_id', $storeId)
            ->where('lote_id', $loteId)
            ->where('tipo', 'venta')
            ->exists()
        ) {
            //    Log::debug("{$tag} Movimiento ya existe", ['product_id' => $productId, 'lote_id' => $loteId, 'store_id' => $storeId]);
            return;
        }
        $mov = MovimientoInventario::create([
            'product_id'      => $productId,
            'lote_id'         => $loteId,
            'store_origen_id' => $storeId,
            'tipo'            => 'venta',
            'sale_id'         => $saleId,
            'cantidad'        => $qty,
            'costo_unitario'  => $costo ?? 0,
        ]);
        // Log::debug("{$tag} Movimiento creado", ['movimiento_id' => $mov->id]);

        $inv = Inventario::firstOrNew([
            'product_id' => $productId,
            'store_id'   => $storeId,
            'lote_id'    => $loteId,
        ]);
        if (!$inv->exists) {
            $inv->stock_ideal    = 0;
            $inv->cantidad_venta = 0;
            $inv->costo_unitario = 0;
        }
        $inv->cantidad_venta += $qty;
        if (!is_null($costo)) {
            $inv->costo_unitario += $costo;
        }
        $inv->save();
        // Log::debug("{$tag} Inventario actualizado", ['inventario_id' => $inv->id, 'qty' => $inv->cantidad_venta]);
    }

    /**
     * Descuenta componentes, maneja recetas recursivamente
     */
    protected function descontarComponentes($saleId, $productId, $storeId, $qty, $ctx)
    {
        $comps = ProductComposition::where('product_id', $productId)->get();
        foreach ($comps as $c) {
            $cid = $c->component_id;
            $ded = $qty * $c->quantity;
            // Log::debug("{$ctx} Descontando componente", ['component_id' => $cid, 'qty' => $ded]);

            $p = Product::find($cid);
            if ($p && $p->type === 'receta') {
                //  Log::debug('Sub-receta detectada', ['component_id' => $cid]);
                $this->descontarComponentes($saleId, $cid, $storeId, $ded, 'SUB-' . $ctx);
                continue;
            }

            $l = $this->buscarLoteMasCercano($cid, $storeId);
            if (!$l) {
                // Log::error('No lote para componente', compact('cid', 'storeId'));
                continue;
            }
            $this->procesarMovimiento($saleId, $cid, $storeId, $l->id, $ded, null, $ctx);
        }
    }

    /**
     * Carga masiva de ventas al inventario usando fechas estáticas.
     */
    public function cargarInventarioMasivo()
    {
        // ——————————————————————————————
        // Aquí “inyectamos” de forma fija el rango deseado:
        $data = [
            'start_date' => '2025-05-01',
            'end_date'   => '2025-05-02',
            'sale_ids'   => null,  // null para usar siempre fechas
        ];
        // ——————————————————————————————

        // Construcción del query base
        $query = Sale::with(['saleDetails' => function ($q) {
            $q->where('status', '1');
        }])
            ->where('status', '1')
            ->whereBetween('fecha_venta', [
                $data['start_date'],
                $data['end_date'],
            ]);

        // Procesamiento por lotes para no saturar memoria
        $query->orderBy('id')
            ->chunkById(100, function ($salesBatch) {

                foreach ($salesBatch as $sale) {
                    DB::beginTransaction();
                    try {
                        if ($sale->saleDetails->isEmpty()) {
                            Log::debug("Venta {$sale->id} sin detalles activos. Se omite.");
                            DB::rollBack();
                            continue;
                        }

                        // Agrupamos detalles por producto|tienda|lote
                        $grouped = $sale->saleDetails->groupBy(function ($d) {
                            return "{$d->product_id}|{$d->store_id}|{$d->lote_id}";
                        });

                        $movimientosToInsert = [];

                        foreach ($grouped as $key => $details) {
                            [$productId, $storeId, $loteId] = explode('|', $key);

                            $qty   = $details->sum('quantity');
                            $costo = $details->sum('total_bruto');

                            // Si ya existe el movimiento, saltamos
                            $exists = MovimientoInventario::where([
                                'sale_id'           => $sale->id,
                                'product_id'        => $productId,
                                'store_origen_id'   => $storeId,
                                'tipo'              => 'venta',
                                'lote_id'           => $loteId,
                            ])->exists();
                            if ($exists) continue;

                            // Preparo array para bulk-insert
                            $movimientosToInsert[] = [
                                'product_id'       => $productId,
                                'lote_id'          => $loteId,
                                'store_origen_id'  => $storeId,
                                'store_destino_id' => null,
                                'tipo'             => 'venta',
                                'sale_id'          => $sale->id,
                                'cantidad'         => $qty,
                                'costo_unitario'   => $costo,
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];

                            // Actualizo inventario con un solo query
                            Inventario::where([
                                'product_id' => $productId,
                                'store_id'   => $storeId,
                                'lote_id'    => $loteId,
                            ])->increment('cantidad_venta', $qty, [
                                'costo_unitario' => DB::raw("costo_unitario + {$costo}")
                            ]);
                        }

                        // Inserto todos los movimientos de golpe
                        if (!empty($movimientosToInsert)) {
                            MovimientoInventario::insert($movimientosToInsert);
                        }

                        // Si hay crédito pendiente, lo procesamos
                        if ($sale->valor_a_pagar_credito > 0) {
                            $this->cuentasPorCobrar($sale->id);
                        }

                        // Marcamos la venta como cerrada
                        $sale->update([
                            'status'       => '1',
                            'fecha_cierre' => now(),
                        ]);

                        DB::commit();
                        Log::info("Venta {$sale->id} procesada correctamente.");
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Error procesando venta {$sale->id}: {$e->getMessage()}");
                    }
                }
            });

        return response()->json([
            'status'  => 0,
            'message' => "Carga masiva completada para ventas entre {$data['start_date']} y {$data['end_date']}.",
        ], 200);
    }



    public function annulSale($saleId)
    {
        // Se obtiene la venta junto con sus detalles (relación 'details')
        $sale = Sale::with('details')->findOrFail($saleId);

        /*  // Verificar que la venta esté en estado '1' 'closed' (o el estado que permita anulación)
        if ($sale->status !== '1') {
            return response()->json(['error' => 'La venta no puede ser anulada.'], 422);
        } */

        // Obtener la venta junto con sus detalles

        Log::info("Venta encontrada", ['sale_id' => $sale->id]);

        // Verificar que la venta esté en un estado que permita la devolución parcial
        if ($sale->status === '1') {
            // '1' representa ventas elegibles para devolución; se continúa el proceso.
        } elseif ($sale->status === '3') {
            // Para ventas en estado '3' se debe tener exactamente 1 nota de crédito asociada
            if ($sale->credit_notes_count !== 1) {
                Log::warning("La venta ID {$sale->id} en estado '3' no tiene una única nota de crédito. Cantidad: {$sale->credit_notes_count}");
                return redirect()->back()->with('error', 'La venta no puede ser devuelta totalmente.');
            }
            // Continuar el proceso para ventas en estado '3' que cumplen la condición
        } else {
            Log::warning("La venta ID {$sale->id} no se puede devolver totalmente por su estado ({$sale->status}).");
            return redirect()->back()->with('error', 'La venta no puede ser devuelta totalmente.');
        }


        DB::beginTransaction();
        try {
            // Crear la cabecera de la nota de crédito
            $notaCredito = Notacredito::create([
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'total'   => $sale->total, // O bien, sumar los totales de cada detalle
                'status'  => '1',
            ]);

            foreach ($sale->details as $detail) {
                // Crear el detalle de la nota de crédito
                NotaCreditoDetalle::create([
                    'notacredito_id' => $notaCredito->id,
                    'product_id'     => $detail->product_id,
                    'quantity'       => $detail->quantity,
                    'price'          => $detail->price,
                ]);

                // Registrar el movimiento en inventario con el tipo 'notacredito'
                MovimientoInventario::create([
                    'tipo'           => 'notacredito', // Asegúrate de que este valor esté permitido
                    'store_origen_id' => $detail->store_id,
                    'sale_id'        => $sale->id,
                    'lote_id'        => $detail->lote_id, // Suponiendo que el detalle incluya este campo
                    'product_id'     => $detail->product_id,
                    'cantidad'       => $detail->quantity,
                    'costo_unitario' => $detail->price, // O el costo real del producto
                    'total'          => $detail->quantity * $detail->price,
                    'fecha'          => now(),
                ]);

                // Actualizar el inventario: incrementar 'cantidad_notacredito'
                $inventario = Inventario::where('product_id', $detail->product_id)
                    ->where('lote_id', $detail->lote_id)
                    ->where('store_id', $detail->store_id) // Se asume que la venta tiene store_id
                    ->first();

                if ($inventario) {
                    $inventario->cantidad_notacredito += $detail->quantity;
                    $inventario->save();
                }
            }

            // Cambiar el estado de la venta a cancelada
            $sale->status = '2';
            $sale->save();

            DB::commit();
            return response()->json(['message' => 'Venta anulada y nota de crédito generada correctamente.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra el formulario de devolución parcial para una venta.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function partialreturnform($id)
    {
        // Obtener la venta, o abortar con 404 si no se encuentra
        $sale = Sale::findOrFail($id);

        // Obtener los detalles de la venta
        $saleDetails = SaleDetail::where('sale_id', $id)->get();

        // Retornar la vista con la información necesaria
        return view('sale.partial_return', compact('sale', 'saleDetails'));
    }

    public function partialreturnsaledetails(Request $request)
    {
        // Deshabilitar Debugbar (y verificar que no inyecte salida extra)
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        Log::info('partialReturnSaleDetails called', $request->all());

        DB::beginTransaction();
        try {
            $saleId = $request->input('ventaId');
            $returns = $request->input('returns'); // Arreglo: [ sale_detail_id => cantidad_a_devolver, ... ]
            Log::info("Processing sale ID: {$saleId}", ['returns' => $returns]);

            if (empty($returns)) {
                Log::error('No returns provided.');
                if (ob_get_length()) {
                    ob_end_clean();
                }
                return response()->json(['error' => 'No se ha indicado ninguna cantidad a devolver.'], 422);
            }

            $totalNota = 0;
            foreach ($returns as $saleDetailId => $returnQuantity) {
                if ($returnQuantity > 0) {
                    $detail = SaleDetail::findOrFail($saleDetailId);
                    Log::info("Processing detail ID: {$saleDetailId}", [
                        'returnQuantity'   => $returnQuantity,
                        'availableQuantity' => $detail->quantity
                    ]);

                    if ($returnQuantity > $detail->quantity) {
                        throw new \Exception("La cantidad a devolver para el producto {$detail->nameprod} supera la cantidad vendida.");
                    }
                    $totalNota += $detail->price * $returnQuantity;
                }
            }
            Log::info("Total Nota Calculated", ['totalNota' => $totalNota]);

            // Crear la cabecera de la Nota de Crédito
            $notaCredito = Notacredito::create([
                'sale_id' => $saleId,
                'user_id' => auth()->id(),
                'total'   => $totalNota,
                'status'  => 'active',
            ]);
            Log::info("Nota de Crédito creada", ['notaCreditoId' => $notaCredito->id]);

            foreach ($returns as $saleDetailId => $returnQuantity) {
                if ($returnQuantity > 0) {
                    $detail = SaleDetail::findOrFail($saleDetailId);

                    // Crear el detalle de la Nota de Crédito
                    NotaCreditoDetalle::create([
                        'notacredito_id' => $notaCredito->id,
                        'product_id'     => $detail->product_id,
                        'quantity'       => $returnQuantity,
                        'price'          => $detail->price,
                    ]);

                    // Registrar el movimiento en inventario (tipo 'notacredito')
                    MovimientoInventario::create([
                        'tipo'           => 'notacredito',
                        'sale_id'        => $saleId,
                        'lote_id'        => $detail->lote_id,
                        'product_id'     => $detail->product_id,
                        'cantidad'       => $returnQuantity,
                        'costo_unitario' => $detail->price,
                        'total'          => $detail->price * $returnQuantity,
                        'fecha'          => now(),
                    ]);

                    // Actualizar el inventario: incrementar 'cantidad_notacredito'
                    $inventario = Inventario::where('product_id', $detail->product_id)
                        ->where('lote_id', $detail->lote_id)
                        ->where('store_id', $request->input('store_id')) // Se asume que se envía store_id
                        ->first();
                    if ($inventario) {
                        $inventario->cantidad_notacredito += $returnQuantity;
                        $inventario->save();
                    }

                    // Actualizar el detalle de venta (restar la cantidad devuelta)
                    $detail->quantity -= $returnQuantity;
                    $detail->save();
                    Log::info("Processed detail", ['saleDetailId' => $saleDetailId, 'newQuantity' => $detail->quantity]);
                }
            }

            // Opcional: Actualizar el estado de la venta a "devuelta" (3)
            $sale = Sale::findOrFail($saleId);
            $sale->status = 3;
            $sale->save();

            DB::commit();
            Log::info("partialReturnSaleDetails completed successfully for saleId " . $saleId);

            if (ob_get_length()) {
                ob_end_clean();
            }
            return response()->json(['message' => 'Devolución parcial procesada correctamente.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error in partialReturnSaleDetails", ['error' => $e->getMessage()]);
            if (ob_get_length()) {
                ob_end_clean();
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function partialReturn(Request $request)
    {
        Log::info('Recibiendo datos para devolución parcial', $request->all());
        // Validar que se reciba el arreglo "returns" y el ID de la venta
        $validated = $request->validate([
            'ventaId'   => 'required|integer|exists:sales,id',
            'returns'   => 'required|array',
            'returns.*' => 'numeric|min:0',
        ]);
        // Obtener la venta junto con sus detalles
        $sale = Sale::with('details')->findOrFail($validated['ventaId']);
        Log::info("Venta encontrada", ['sale_id' => $sale->id]);

        // Verificar que la venta esté en un estado que permita la devolución parcial
        if ($sale->status === '1') {
            // '1' representa ventas elegibles para devolución; se continúa el proceso.
        } elseif ($sale->status === '3') {
            // Para ventas en estado '3' se debe tener exactamente 1 nota de crédito asociada
            if ($sale->credit_notes_count !== 1) {
                Log::warning("La venta ID {$sale->id} en estado '3' no tiene una única nota de crédito. Cantidad: {$sale->credit_notes_count}");
                return redirect()->back()->with('error', 'La venta no puede ser devuelta parcialmente.');
            }
            // Continuar el proceso para ventas en estado '3' que cumplen la condición
        } else {
            Log::warning("La venta ID {$sale->id} no se puede devolver parcialmente por su estado ({$sale->status}).");
            return redirect()->back()->with('error', 'La venta no puede ser devuelta parcialmente.');
        }

        // Preparar variables para calcular el total parcial a devolver y almacenar los detalles a procesar
        $partialTotal = 0;
        $returnedDetails = [];
        // Recorrer cada retorno indicado en la solicitud
        foreach ($validated['returns'] as $detailId => $returnQuantity) {
            if ($returnQuantity > 0) {
                // Buscar el detalle de venta correspondiente en la colección de detalles
                $detail = $sale->details->where('id', $detailId)->first();
                if (!$detail) {
                    Log::warning("No se encontró el detalle de venta con ID: {$detailId}");
                    continue;
                }
                // Validar que la cantidad a devolver no supere la cantidad vendida actual
                if ($returnQuantity > $detail->quantity) {
                    $msg = "La cantidad a devolver ({$returnQuantity}) supera la cantidad vendida para el producto ID {$detail->product_id}";
                    Log::error($msg);
                    return redirect()->back()->with('error', $msg);
                }
                // Acumular el total a devolver
                $partialTotal += $returnQuantity * $detail->price;
                $returnedDetails[] = [
                    'detail' => $detail,
                    'returnQuantity' => $returnQuantity,
                ];
            }
        }
        if (empty($returnedDetails)) {
            Log::warning("No se especificó ninguna cantidad para devolución parcial en la venta ID {$sale->id}");
            return redirect()->back()->with('error', 'No se especificaron devoluciones válidas.');
        }
        Log::info("Total parcial a devolver: {$partialTotal}");
        // Procesar la devolución parcial mediante nota de crédito
        DB::beginTransaction();
        try {
            // Incrementar el contador de notas de crédito
            $sale->credit_notes_count += 1;

            // Determinar el estado de las notas de crédito
            // Si es la primera nota, establecer como 'partial'
            // Si es la segunda nota, mantener como 'partial' a menos que sea una devolución total
            $sale->credit_note_status = 'partial';

            // Crear la cabecera de la nota de crédito
            $notaCredito = Notacredito::create([
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'total'   => $partialTotal,
                'status'  => '1',
                // Agregar el número de secuencia de la nota de crédito para esta venta
                'credit_note_sequence' => $sale->credit_notes_count,
                // Establecer el tipo de devolución como parcial
                'return_type' => 'partial_return'
            ]);
            Log::info("Nota de crédito creada con ID: {$notaCredito->id}");
            // Recorrer cada detalle a devolver
            foreach ($returnedDetails as $returned) {
                /** @var SaleDetail $detail */
                $detail = $returned['detail'];
                $returnQuantity = $returned['returnQuantity'];
                // Insertar el detalle de la nota de crédito manualmente (lógica previamente en trigger)
                NotaCreditoDetalle::create([
                    'notacredito_id' => $notaCredito->id,
                    'product_id'     => $detail->product_id,
                    'sale_detail_id' => $detail->id,
                    'store_id'       => $detail->store_id,
                    'lote_id'        => $detail->lote_id,
                    'quantity'       => $returnQuantity,
                    'price'          => $detail->price,
                    'inventory_processed' => false
                ]);
                Log::info("Nota de crédito detalle creada para producto ID: {$detail->product_id}, cantidad: {$returnQuantity}");
                // Registrar el movimiento en inventario para la devolución parcial
                $movimiento = MovimientoInventario::create([
                    'tipo'            => 'notacredito',
                    'store_origen_id' => $detail->store_id,
                    'sale_id'         => $sale->id,
                    'lote_id'         => $detail->lote_id,
                    'product_id'      => $detail->product_id,
                    'cantidad'        => $returnQuantity,
                    'costo_unitario'  => $detail->price,
                    'total'           => $returnQuantity * $detail->price,
                    'fecha'           => now(),
                ]);
                Log::info("Movimiento de inventario registrado para producto ID: {$detail->product_id}");
                // Actualizar el inventario: incrementar el campo 'cantidad_notacredito'
                $inventario = Inventario::where('product_id', $detail->product_id)
                    ->where('lote_id', $detail->lote_id)
                    ->where('store_id', $detail->store_id)
                    ->first();
                if ($inventario) {
                    $inventario->cantidad_notacredito += $returnQuantity;
                    $inventario->save();
                    Log::info("Inventario actualizado para producto ID: {$detail->product_id}");
                }

                // --- Funcionalidad 1: si tras restar queda cantidad = 0, marcamos status = '0' ---
                $detail->quantity -= $returnQuantity;
                if ($detail->quantity <= 0) {
                    // Si quedó en 0 (o negativo por seguridad), forzamos a 0 y desactivamos
                    $detail->quantity = 0;
                    $detail->status   = '0';
                }
                $detail->save();
                Log::info("Detalle de venta actualizado para producto ID: {$detail->product_id}, cantidad restante: {$detail->quantity}");
                // --- Fin de la funcionalidad 1 ---

            }

            // —— Funcionalidad 2: recalcular y guardar cada detalle con status = '1'
            $activeDetails = SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->get();

            foreach ($activeDetails as $d) {
                // 1) Recalcular bruto
                $d->total_bruto       = round($d->quantity * $d->price, 2);

                // 2) Recalcular descuento (por % + fijo)
                $calcDescuento        = ($d->total_bruto * ($d->porc_desc / 100))
                    + $d->descuento_cliente;
                $d->descuento         = round($calcDescuento, 2);

                // 3) Neto base para impuestos
                $neto                 = $d->total_bruto - $d->descuento;

                // 4) Recalcular cada impuesto
                $d->iva               = round($neto * ($d->porc_iva           / 100), 2);
                $d->otro_impuesto     = round($neto * ($d->porc_otro_impuesto / 100), 2);
                $d->impoconsumo       = round($neto * ($d->porc_impoconsumo   / 100), 2);

                // 5) Total final del detalle
                $d->total             = round(
                    $neto
                        + $d->iva
                        + $d->otro_impuesto
                        + $d->impoconsumo,
                    2
                );

                // 6) Forzar timestamp de actualización
                $d->updated_at        = now();

                // 7) Guardar cambios
                $d->save();
            }

            // 8) Ahora, recalcular y guardar los totales de la venta
            $TotalBruto        = (float) SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->sum('total_bruto');
            $TotalIva          = (float) SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->sum('iva');
            $TotalOtroImp      = (float) SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->sum('otro_impuesto');
            $TotalImpConsumo   = (float) SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->sum('impoconsumo');
            $TotalAPagar       = (float) SaleDetail::where('sale_id', $sale->id)
                ->where('status',    '1')
                ->sum('total');

            // 9) Mantener descuentos globales
            $TotalDescuentos   = (float) $sale->descuentos + $sale->descuento_cliente;

            $sale->total_bruto           = $TotalBruto;
            $sale->subtotal              = $TotalBruto - $TotalDescuentos;
            $sale->total_iva             = $TotalIva;
            $sale->total_otros_impuestos = $TotalOtroImp + $TotalImpConsumo;

            $sale->total                 = $TotalAPagar;
            $sale->total_valor_a_pagar = $TotalAPagar;

            if ($sale->valor_a_pagar_credito > 0) {
                $sale->valor_a_pagar_credito = $TotalAPagar;
            }

            // — recalculo de totales de Sale ya hecho aquí —

            // 10) Calcular el valor de devolución usando los pagos en efectivo y tarjeta de la venta
            $calculatedValor = round(
                ($sale->valor_a_pagar_efectivo + $sale->valor_a_pagar_tarjeta)
                    - $sale->total,
                2
            );

            // Asignar en la NotaCredito tanto el ID de la forma de pago (global)
            // como el valor que acabamos de calcular
            $notaCredito->forma_pago_id    = $request->input('forma_pago');   // tu campo oculto o select global
            $notaCredito->valor_devolucion = $calculatedValor;

            // Persistimos los cambios en la cabecera de la nota
            $notaCredito->save();

            $sale->status                = '3';

            // Forzar timestamp de la venta
            $sale->updated_at            = now();

            // Guardar venta
            $sale->save();

            DB::commit();
            return redirect()->route('sale.index')
                ->with('success', 'Devolución parcial y recalculo de totales completados.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error en devolución parcial para venta ID {$sale->id}: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
