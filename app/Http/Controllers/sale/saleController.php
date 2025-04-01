<?php

namespace App\Http\Controllers\sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Third;
use App\Models\centros\Centrocosto;
use App\Models\compensado\Compensadores;
use App\Models\compensado\Compensadores_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\caja\Caja;
use App\Models\Cuentas_por_cobrar;
use App\Models\Formapago;
use App\Models\Inventario;
use App\Models\Listapreciodetalle;
use App\Models\MovimientoInventario;
use App\Models\Notacredito;
use App\Models\NotaCreditoDetalle;
use App\Models\Sale;
use App\Models\SaleCaja;
use App\Models\SaleDetail;
use App\Models\Store;
use App\Models\Subcentrocosto;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Log;

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

    public function show()
    {
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $userCentrocostos = Auth::user()->stores->pluck('centrocosto_id')->unique()->toArray();

        $data = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select('sa.*', 'tird.name as namethird', 'c.name as namecentrocosto')
            ->whereIn('c.id', $userCentrocostos) // Filtra por los centros de costo del usuario
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
                    // Mostrar botón de anulación solo si no hay notas de crédito o hay exactamente 1
                    if ($creditNotesCount == 0 || $creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    }
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
                    if ($creditNotesCount == 1) {
                        $btn .= '<a href="#" class="btn btn-danger" title="Anular la venta" onclick="confirmAnulacion(' . $data->id . ')">
                                    <i class="fas fa-trash"></i>
                                 </a>';
                    }
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

            try {
                // Actualizar la venta
                $venta = Sale::find($ventaId);
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

                // Si la venta corresponde a las tiendas 1 o 2, asignar resolución
                if ($venta->centrocosto_id == 1 || $venta->centrocosto_id == 2) {
                    $count1 = DB::table('sales')->where('status', '1')->count();
                    $count2 = DB::table('notacreditos')->where('status', '1')->count();
                    $count3 = DB::table('notadebitos')->where('status', '1')->count();
                    $count  = $count1 + $count2 + $count3;
                    $resolucion = 'ERPC ' . (1 + $count);
                    // $venta->resolucion = $resolucion;
                }
                $venta->save();

                // Llamar al método para cargar el inventario
                $this->cargarInventariocr($ventaId);

                // Regenerar la sesión si es necesario
                session()->regenerate();

                // Retornar la vista con el script que abre la factura y redirige
                return view('sale.redirectAndInvoice', ['ventaId' => $ventaId])
                    ->with('success', 'Guardado correctamente y cargado al inventario.');
            } catch (\Throwable $th) {
                // En caso de error al actualizar la venta
                return redirect()->route('sale.index')
                    ->with('error', 'Error al actualizar la venta: ' . $th->getMessage());
            }
        } catch (\Throwable $th) {
            // En caso de error general en el proceso de pago
            return redirect()->route('sale.index')
                ->with('error', 'Error al procesar el pago: ' . $th->getMessage());
        }
    }



    public function cuentasPorCobrar($ventaId)
    {
        $venta = Sale::find($ventaId);
        $clienteId = $venta->third_id;
        $formaPagoCreditoId =  $venta->forma_pago_credito_id;
        $formaPagos = Formapago::find($formaPagoCreditoId);
        $diasCredito = $formaPagos->diascredito;
        $cXc = new Cuentas_por_cobrar();
        $cXc->sale_id = $ventaId;
        $cXc->third_id = $clienteId;
        $cXc->deuda_inicial = $venta->valor_a_pagar_credito;
        $cXc->deuda_x_cobrar = $venta->valor_a_pagar_credito;
        $cXc->fecha_vencimiento = now()->addDays($diasCredito);
        $cXc->save();
    }



    public function create($id)
    {
        $venta = Sale::find($id);
        $stores = Store::WhereIn('id', [1, 4, 5, 6, 8, 9, 10])
            ->orderBy('name', 'asc')
            ->get();
        //        $stores = Store::all();

        /*  $prod = Product::where('status', '1')
        ->whereHas('inventarios', function ($query) {
            $query->where('stock_ideal', '>', 0);
        })
        ->whereHas('lotesPorVencer') // Filtra solo productos con lotes próximos a vencer
        ->with('lotesPorVencer') // Carga los lotes próximos a vencer para cada producto
        ->orderBy('category_id', 'asc')
        ->orderBy('name', 'asc')
        ->get();

 */
        /* 
        $prod = Product::where('status', '1')
            ->whereHas('inventarios', function ($query) {
                $query->where('stock_ideal', '>', 0);
            })
            ->whereHas('lotesPorVencer') // Asegura que haya al menos un lote próximo a vencer
            ->with(['lotesPorVencer' => function ($query) {
                $query->select('lotes.*') // Evita problemas de alias duplicados
                    ->orderBy('fecha_vencimiento', 'asc'); // Ordena por fecha más próxima
            }])
            ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(); */



        /*    $prod = Product::where('status', '1')
            ->whereHas('inventarios', function ($query) {
                $query->where('stock_ideal', '>', 0);
            })
            ->whereHas('lotesPorVencer') // Asegura que haya al menos un lote próximo a vencer

            ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();
 

        //  $storeId = [10];
*/
        //$storeIds = [1, 4, 5, 6, 8, 9, 10];
        $storeIds = \DB::table('store_user')
            ->where('user_id', auth()->id())
            ->pluck('store_id')
            ->toArray();

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


        return view('sale.create', compact('datacompensado', 'results', 'stores', 'id', 'detalleVenta', 'ventasdetalle', 'arrayTotales', 'status', 'statusInventory', 'display'));
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
        $query = $request->input('q');
        // $storeIds = [1, 4, 5, 6, 8, 9, 10];
        $storeIds = \DB::table('store_user')
            ->where('user_id', auth()->id())
            ->pluck('store_id')
            ->toArray();


        // Consulta de productos. Se busca por barcode o por nombre o por el código del lote (mediante la relación "lotes")
        $productsQuery = Product::query();

        if ($query) {
            if (preg_match('/^\d{13}$/', $query)) {
                $productsQuery->where('barcode', $query);
            } else {
                $productsQuery->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhereHas('lotes', function ($q2) use ($query) {
                            $q2->where('codigo', 'LIKE', "%{$query}%");
                        });
                });
            }
        }

        // Se filtran los productos que tengan inventarios en las bodegas seleccionadas con stock_ideal > 0
        $productsQuery->whereHas('inventarios', function ($q) use ($storeIds) {
            $q->whereIn('store_id', $storeIds)
                ->where('stock_ideal', '>', 0);
        });

        $products = $productsQuery->get();

        // Se obtienen todos los inventarios que cumplan la condición, cargando además la relación "lote" y "store"
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
            Log::info('Iniciando proceso de guardado de detalle de venta', [
                'ventaId'  => $request->ventaId,
                // Ahora "producto" contiene el id del inventario
                'inventario_id' => $request->producto,
                'lote_id'  => $request->lote_id,
                'store'    => $request->store,
            ]);

            // Se obtiene el registro de inventario mediante el identificador único
            $inventario = Inventario::with('lote')->find($request->producto);
            if (!$inventario) {
                Log::warning('Inventario no encontrado', [
                    'inventario_id' => $request->producto
                ]);
                return response()->json([
                    'status'  => 0,
                    'message' => 'No se encontró el inventario seleccionado.'
                ], 422);
            }

            // Se usa el stock disponible del inventario obtenido
            $stockDisponible = $inventario->stock_ideal;
            Log::info('Stock disponible recuperado', [
                'product_id'  => $inventario->product_id,
                'store_id'    => $inventario->store_id,
                'lote_id'     => $inventario->lote_id,
                'stock_ideal' => $stockDisponible,
            ]);

            if (is_null($stockDisponible)) {
                Log::warning('Stock disponible no encontrado para producto en bodega y lote', [
                    'producto' => $inventario->product_id,
                    'store'    => $inventario->store_id,
                    'lote_id'  => $inventario->lote_id,
                ]);
                return response()->json([
                    'status'  => 0,
                    'message' => 'No se encontró stock disponible para el producto en la bodega y lote seleccionados.'
                ], 422);
            }

            // Validación de datos
            $rules = [
                'ventaId'  => 'required',
                'producto' => 'required', // ahora es el id de inventario
                'price'    => 'required',
                'lote_id'  => 'required',
                'store'    => 'required',
                'quantity' => [
                    'required',
                    'numeric',
                    'regex:/^\d+(\.\d{1,2})?$/',
                    'min:0.1',
                    'max:' . $stockDisponible,
                ],
            ];

            $messages = [
                'ventaId.required'  => 'El compensado es requerido',
                'producto.required' => 'El producto es requerido',
                'price.required'    => 'El precio de compra es requerido',
                'lote_id.required'  => 'El lote es requerido',
                'store.required'    => 'La bodega es requerida',
                'quantity.required' => 'La cantidad es requerida.',
                'quantity.numeric'  => 'La cantidad debe ser un número.',
                'quantity.min'      => 'La cantidad debe ser mayor a 0.1.',
                'quantity.max'      => 'La cantidad no puede ser mayor al stock disponible (' . $stockDisponible . ').',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                Log::warning('Validación fallida en savedetail', [
                    'errors' => $validator->errors(),
                    'data'   => $request->all()
                ]);
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Validación de datos exitosa', ['data' => $request->all()]);

            if ($request->quantity > $stockDisponible) {
                Log::warning('La cantidad ingresada supera el stock disponible', [
                    'quantity'        => $request->quantity,
                    'stockDisponible' => $stockDisponible
                ]);
                return response()->json([
                    'status'  => 0,
                    'message' => 'La cantidad ingresada supera el stock disponible (' . $stockDisponible . ').'
                ], 422);
            }

            // Formateo de valores y cálculos
            $formatCantidad = new metodosrogercodeController();
            $price = $formatCantidad->MoneyToNumber($request->price);
            $quantity = $request->quantity;

            $precioUnitarioBruto = $price * $quantity;
            $porcDescuento = $request->get('porc_desc');
            $descuentoProducto = $precioUnitarioBruto * ($porcDescuento / 100);
            $porc_descuento_cliente = $request->get('porc_descuento_cliente');
            $descuentoCliente = $precioUnitarioBruto * ($porc_descuento_cliente / 100);
            $totalDescuento = $descuentoProducto + $descuentoCliente;
            $netoSinImpuesto = $precioUnitarioBruto - $totalDescuento;

            $porcIva = $request->get('porc_iva');
            $porcOtroImpuesto = $request->get('porc_otro_impuesto');
            $porcImpoconsumo = $request->get('porc_impoconsumo');
            $iva = $netoSinImpuesto * ($porcIva / 100);
            $otroImpuesto = $netoSinImpuesto * ($porcOtroImpuesto / 100);
            $impoconsumo = $netoSinImpuesto * ($porcImpoconsumo / 100);
            $totalImpuestos = $iva + $otroImpuesto + $impoconsumo;
            $valorApagar = $netoSinImpuesto + $totalImpuestos;

            Log::info('Cálculos realizados', [
                'precioUnitarioBruto' => $precioUnitarioBruto,
                'descuentoProducto'   => $descuentoProducto,
                'descuentoCliente'    => $descuentoCliente,
                'totalDescuento'      => $totalDescuento,
                'netoSinImpuesto'     => $netoSinImpuesto,
                'iva'                 => $iva,
                'otroImpuesto'        => $otroImpuesto,
                'impoconsumo'         => $impoconsumo,
                'totalImpuestos'      => $totalImpuestos,
                'valorApagar'         => $valorApagar
            ]);

            // Preparar datos a almacenar en el detalle de venta
            $dataDetail = [
                'sale_id'            => $request->ventaId,
                'inventario_id' => $request->producto, // Aquí se almacena el id único del inventario información del inventario recuperado
                'store_id'           => $inventario->store_id,
                'product_id'         => $inventario->product_id,
                'price'              => $price,
                'quantity'           => $quantity,
                'lote_id'            => $inventario->lote_id,
                'porc_desc'          => $porcDescuento,
                'descuento'          => $descuentoProducto,
                'descuento_cliente'  => $descuentoCliente,
                'porc_iva'           => $porcIva,
                'iva'                => $iva,
                'porc_otro_impuesto' => $porcOtroImpuesto,
                'otro_impuesto'      => $otroImpuesto,
                'porc_impoconsumo'   => $porcImpoconsumo,
                'impoconsumo'        => $impoconsumo,
                'total_bruto'        => $precioUnitarioBruto,
                'total'              => $netoSinImpuesto + $totalImpuestos,
            ];

            // Crear o actualizar el detalle de venta
            if ($request->regdetailId > 0) {
                $detail = SaleDetail::find($request->regdetailId);
                $detail->update($dataDetail);
                Log::info('Detalle de venta actualizado', [
                    'regdetailId' => $request->regdetailId,
                    'dataDetail'  => $dataDetail
                ]);
            } else {
                $newDetail = SaleDetail::create($dataDetail);
                Log::info('Detalle de venta creado', [
                    'newDetailId' => $newDetail->id,
                    'dataDetail'  => $dataDetail
                ]);
            }

            // Actualización de la venta
            $sale = Sale::find($request->ventaId);
            $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();
            $sale->items = $saleDetails->count();
            $totalBruto = $saleDetails->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });
            $totalDesc  = $saleDetails->sum(function ($detail) {
                return $detail->descuento + $detail->descuento_cliente;
            });
            $totalValor = $saleDetails->sum('total');

            $sale->total_bruto = $totalBruto;
            $sale->descuentos = $totalDesc;
            $sale->total_valor_a_pagar = $totalValor;
            $sale->total_iva = $iva;
            $sale->total_otros_impuestos = $netoSinImpuesto * ($porcOtroImpuesto / 100);
            $sale->save();

            Log::info('Venta actualizada', [
                'sale_id'               => $sale->id,
                'items'                 => $sale->items,
                'total_bruto'           => $totalBruto,
                'descuento'            => $totalDesc,
                'total_valor_a_pagar'   => $totalValor,
                'total_iva'             => $iva,
                'total_otros_impuestos' => $sale->total_otros_impuestos,
            ]);

            // Obtener arrays para la respuesta (detalles y totales)
            $arraydetail = $this->getventasdetail($request->ventaId);
            $arrayTotales = $this->sumTotales($request->ventaId);

            Log::info('Proceso savedetail completado exitosamente', [
                'ventaId' => $request->ventaId
            ]);

            return response()->json([
                'status'       => 1,
                'message'      => "Agregado correctamente",
                'array'        => $arraydetail,
                'arrayTotales' => $arrayTotales
            ]);
        } catch (\Throwable $th) {
            Log::error('Error en savedetail', [
                'error' => $th->getMessage(),
                'stack' => $th->getTraceAsString()
            ]);
            return response()->json([
                'status'  => 0,
                'message' => (array) $th
            ]);
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
        $clienteId = $request->input('cliente');
        $cliente = Third::find($clienteId);
        $producto = Listapreciodetalle::join('products as prod', 'listapreciodetalles.product_id', '=', 'prod.id')
            ->join('thirds as t', 'listapreciodetalles.listaprecio_id', '=', 't.id')
            ->where('prod.id', $request->productId)
            ->where('t.id', $cliente->listaprecio_genericid)
            ->select('listapreciodetalles.precio', 'prod.iva', 'prod.otro_impuesto', 'prod.impoconsumo', 'listapreciodetalles.porc_descuento') // Select only the
            ->first();
        if ($producto) {
            return response()->json([
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'otro_impuesto' => $producto->otro_impuesto,
                'impoconsumo' => $producto->impoconsumo,
                'porc_descuento' => $producto->porc_descuento
            ]);
        } else {
            // En caso de que el producto no sea encontrado
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
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
            $venta->subcentrocostos_id = 2;
            $venta->third_id = 1;
            $venta->vendedor_id = 1;

            $venta->fecha_venta = $currentDateFormat;
            $venta->fecha_cierre = $dateNextMonday;
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

            return response()->json([
                'status' => 1,
                'message' => 'Inicio de venta por mostrador',
                'registroId' => $venta->id
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
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
            // Obtener la venta con sus detalles (usando eager loading)
            $sale = \App\Models\Sale::with('saleDetails')
                ->where('id', $ventaId)
                ->where('status', '0')
                ->first();

            Log::debug('Venta obtenida', ['ventaId' => $ventaId, 'compensadores' => $sale]);

            if (!$sale) {
                Log::debug('Venta no encontrada o cerrada', ['ventaId' => $ventaId]);
                return response()->json([
                    'status'  => 1,
                    'message' => 'Venta no encontrada o cerrada.'
                ], 404);
            }

            // Filtrar los detalles activos de la venta
            $saleDetails = $sale->saleDetails->where('status', '1');

            Log::debug('Detalles de venta obtenidos', [
                'ventaId'       => $ventaId,
                'detalle_count' => $saleDetails->count()
            ]);

            if ($saleDetails->isEmpty()) {
                Log::debug('No hay detalles de venta activos', ['ventaId' => $ventaId]);
                return response()->json([
                    'status'  => 0,
                    'message' => 'No hay detalles de venta activos.'
                ], 404);
            }

            // Agrupar los detalles de venta por producto, tienda y lote
            $groupedDetails = $saleDetails->groupBy(function ($detail) {
                return $detail->product_id . '-' . $detail->store_id . '-' . $detail->lote_id;
            });

            foreach ($groupedDetails as $key => $detailsGroup) {
                // Descomponer la llave en product_id, store_id y lote_id
                list($productId, $storeId, $lote_id) = explode('-', $key);

                // Calcular los acumulados para el grupo
                $accumulatedQuantity   = $detailsGroup->sum('quantity');
                $accumulatedTotalBruto = $detailsGroup->sum('total_bruto');

                Log::debug('Acumulados calculados para producto, tienda y lote', [
                    'product_id'            => $productId,
                    'store_id'              => $storeId,
                    'lote_id'               => $lote_id,
                    'accumulatedQuantity'   => $accumulatedQuantity,
                    'accumulatedTotalBruto' => $accumulatedTotalBruto
                ]);

                // Buscar el registro de inventario específico para producto, tienda y lote
                $inventario = \App\Models\Inventario::where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->where('lote_id', $lote_id)
                    ->first();

                if (!$inventario) {
                    Log::debug('No se encontró inventario para producto, tienda y lote', [
                        'product_id' => $productId,
                        'store_id'   => $storeId,
                        'lote_id'    => $lote_id
                    ]);
                    continue;
                }

                // Verificar si ya existe un movimiento para esta venta, producto, tienda y lote
                $existingMovimiento = \App\Models\MovimientoInventario::where('sale_id', $ventaId)
                    ->where('product_id', $productId)
                    ->where('store_origen_id', $storeId)
                    ->where('tipo', 'venta')
                    ->where('lote_id', $lote_id)
                    ->first();

                if ($existingMovimiento) {
                    Log::debug('Movimiento de inventario ya existe para esta venta', [
                        'movimiento_id' => $existingMovimiento->id
                    ]);
                    continue;
                }

                // Crear el movimiento de inventario usando el lote_id obtenido de los detalles
                $movimiento = \App\Models\MovimientoInventario::create([
                    'product_id'       => $productId,
                    'lote_id'          => $lote_id,
                    'store_origen_id'  => $storeId,
                    'store_destino_id' => null, // Para tipo venta, no hay tienda destino
                    'tipo'             => 'venta',
                    'sale_id'          => $ventaId,
                    'cantidad'         => $accumulatedQuantity,
                    'costo_unitario'   => $accumulatedTotalBruto,
                ]);
                Log::debug('Movimiento de inventario creado', ['movimiento' => $movimiento->toArray()]);

                // Actualizar el inventario: incrementar la cantidad de venta y el costo unitario
                $inventario->cantidad_venta += $accumulatedQuantity;
                $inventario->costo_unitario += $accumulatedTotalBruto;
                $inventario->save();

                Log::debug('Inventario actualizado', [
                    'inventario_id'       => $inventario->id,
                    'store_id'            => $storeId,
                    'incremento_cantidad' => $accumulatedQuantity,
                    'incremento_costo'    => $accumulatedTotalBruto
                ]);
            }

            // Si hay valor a pagar en crédito, invocar el proceso correspondiente
            if ($sale->valor_a_pagar_credito > 0) {
                Log::debug('Venta tiene valor a pagar en crédito, se debe invocar cuentasPorCobrar', [
                    'valor_a_pagar_credito' => $sale->valor_a_pagar_credito
                ]);
                // Ejemplo: $this->cuentasPorCobrar($sale);
            }

            // Marcar la venta como cerrada y asignar la fecha de cierre
            $sale->status = '1';
            $sale->fecha_cierre = now();
            $sale->save();

            Log::debug('Venta actualizada a cerrada', ['ventaId' => $ventaId]);

            DB::commit();
            Log::debug('Transacción commit exitosa para venta', ['ventaId' => $ventaId]);

            return redirect()->route('sale.index')
                ->with('success', 'Cargado al inventario exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cargar inventario', [
                'error'   => $e->getMessage(),
                'ventaId' => $ventaId
            ]);
            return redirect()->route('sale.index')
                ->with('error', 'Error al cargar inventario: ' . $e->getMessage());
        }
    }




    public function cargarInventarioMasivo()
    {
        for ($ventaId = 484; $ventaId <= 592; $ventaId++) {
            $this->cargarInventariocr($ventaId);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cargado al inventario masivamente desde el ID 672 hasta el ID 1127'
        ]);
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



    public function partialReturnVersion1(Request $request)
    {
        // Si es POST, procesa la devolución parcial
        if ($request->isMethod('post')) {
            // Registra los datos recibidos para debug
            Log::info('Recibiendo datos para devolución parcial', $request->all());

            // Validar los datos recibidos
            $validated = $request->validate([
                'ventaId'    => 'required|integer|exists:sales,id',
                'returns'    => 'required|array',
                'returns.*'  => 'numeric|min:0',
                'store_ids'  => 'required|array',
                'store_ids.*' => 'integer',
            ]);

            // Buscar la venta
            $sale = Sale::findOrFail($validated['ventaId']);
            Log::info("Venta encontrada", ['sale_id' => $sale->id]);

            // Procesar cada detalle de devolución
            foreach ($validated['returns'] as $detailId => $returnQuantity) {
                if ($returnQuantity > 0) {
                    // Buscar el detalle de venta
                    $saleDetail = SaleDetail::findOrFail($detailId);
                    Log::info("Procesando detalle", ['detail_id' => $detailId, 'returnQuantity' => $returnQuantity]);

                    // Validar que la cantidad a devolver no exceda la cantidad vendida
                    if ($returnQuantity > $saleDetail->quantity) {
                        $errorMsg = 'La cantidad a devolver supera la cantidad vendida para el producto ' . $saleDetail->product->name;
                        Log::error($errorMsg);
                        return redirect()->back()->with('error', $errorMsg);
                    }

                    // Obtener el store_id para este detalle
                    $storeId = $validated['store_ids'][$detailId] ?? null;
                    if (!$storeId) {
                        $errorMsg = 'No se encontró el store_id para el detalle ' . $detailId;
                        Log::error($errorMsg);
                        return redirect()->back()->with('error', $errorMsg);
                    }

                    // Actualizar la cantidad vendida (restarle la cantidad devuelta)
                    $saleDetail->quantity -= $returnQuantity;
                    $saleDetail->save();
                    Log::info("Detalle actualizado", ['detail_id' => $detailId, 'nueva_quantity' => $saleDetail->quantity]);

                    // Aquí podrías actualizar el inventario del store correspondiente
                    // Ejemplo: Inventory::incrementStock($saleDetail->product_id, $returnQuantity, $storeId);
                }
            }

            Log::info("Devolución parcial procesada exitosamente para la venta {$sale->id}");

            return redirect()->route('sale.index')->with('success', 'Devolución parcial procesada exitosamente.');
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

                // Opcional: actualizar el detalle de la venta restando la cantidad devuelta
                // $detail->quantity -= $returnQuantity;
                // $detail->save();
                // Log::info("Detalle de venta ID: {$detail->id} actualizado. Nueva cantidad: {$detail->quantity}");
            }
            // Actualizar el estado de la venta a '3' indicando que la devolución parcial se procesó correctamente
            $sale->status = '3';
            $sale->save();
            Log::info("Venta ID {$sale->id} actualizada a status '3' con {$sale->credit_notes_count} notas de crédito.");
            DB::commit();
            Log::info("Devolución parcial procesada exitosamente para la venta ID: {$sale->id}");
            return redirect()->route('sale.index')->with('success', 'Devolución parcial procesada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error en devolución parcial para venta ID {$sale->id}: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
