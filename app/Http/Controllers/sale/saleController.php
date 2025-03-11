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
use App\Models\Sale;
use App\Models\SaleCaja;
use App\Models\SaleDetail;
use App\Models\Store;
use App\Models\Subcentrocosto;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Log;

class saleController extends Controller
{

    public function index()
    {
        $ventas = Sale::get();
        $centros = Centrocosto::WhereIn('id', [1])->get();
        $clientes = Third::Where('cliente', 1)->get();
        $vendedores = Third::Where('vendedor', 1)->get();
        $domiciliarios = Third::Where('domiciliario', 1)->get();
        $subcentrodecostos = Subcentrocosto::get();

        return view('sale.index', compact('ventas', 'centros', 'clientes', 'vendedores', 'domiciliarios', 'subcentrodecostos'));
    }

    public function show()
    {
        $data = DB::table('sales as sa')
            /*   ->join('categories as cat', 'sa.categoria_id', '=', 'cat.id') */
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('centro_costo as c', 'sa.centrocosto_id', '=', 'c.id')
            ->select('sa.*', 'tird.name as namethird', 'c.name as namecentrocosto')
            /*  ->where('sa.status', 1) */
            ->get();

        //  $data = Sale::orderBy('id','desc');

        return Datatables::of($data)->addIndexColumn()
            ->addColumn('status', function ($data) {
                if ($data->status == 1) {
                    $status = '<span class="badge bg-success">Close</span>';
                } else {
                    $status = '<span class="badge bg-danger">Open</span>';
                }
                return $status;
            })
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->created_at);
                $formattedDate = $date->format('M-d. H:i');
                return $formattedDate;
            })
            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();

                if (Carbon::parse($currentDateTime->format('Y-m-d'))->gt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
					    
                        <a href="sale/showFactura/' . $data->id . '" class="btn btn-dark" title="VerFactura" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>				
					    <button class="btn btn-dark" title="Borrar venta" disabled>
						    <i class="fas fa-trash"></i>
					    </button>
                        </div>
                        ';
                } elseif (Carbon::parse($currentDateTime->format('Y-m-d'))->lt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
					    <a href="sale/create/' . $data->id . '" class="btn btn-dark" title="Detalles">
						    <i class="fas fa-directions"></i>
					    </a>
					   
                        <a href="sale/showFactura/' . $data->id . '" class="btn btn-dark" title="VerFacturaPendiente" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>
					  
                        </div>
                        ';
                    //ESTADO Cerrada
                } else {
                    $btn = '
                        <div class="text-center">
                        <a href="sale/showFactura/' . $data->id . '" class="btn btn-dark" title="VerFacturaCerrada" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>
					    <button class="btn btn-dark" title="Borra la venta" disabled>
						    <i class="fas fa-trash"></i>
					    </button>
					  
                        </div>
                        ';
                }
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
                    $venta->resolucion = $resolucion;
                }
                $venta->save();

                // Llamar al método para cargar el inventario
                $this->cargarInventariocr($ventaId);

                // Regenerar la sesión si es necesario
                session()->regenerate();

                // Redirigir a la ruta sale.index con un mensaje de éxito
                return redirect()->route('sale.index')
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
        $storeIds = [1, 4, 5, 6, 8, 9, 10, 22];

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
        $storeIds = [1, 4, 5, 6, 8, 9, 10, 22];

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
        /*   $ventasdetalle = $this->getventasdetalle($id, $venta->store_id); */
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
        $TotalValorAPagar = (float)SaleDetail::Where([['sale_id', $id]])->sum('total');

        $array = [
            'TotalBruto' => $TotalBruto,
            'TotalBrutoSinDescuento' => $TotalBrutoSinDescuento,
            'TotalDescuentos' => $TotalDescuentos,
            'TotalValorAPagar' => $TotalValorAPagar,
            'TotalIva' => $TotalIva,
            'TotalOtroImpuesto' => $TotalOtroImpuesto,
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
            $porcDescuento = $request->get('porc_descuento');
            $descuentoProducto = $precioUnitarioBruto * ($porcDescuento / 100);
            $porc_descuento_cliente = $request->get('porc_descuento_cliente');
            $descuentoCliente = $precioUnitarioBruto * ($porc_descuento_cliente / 100);
            $totalDescuento = $descuentoProducto + $descuentoCliente;
            $netoSinImpuesto = $precioUnitarioBruto - $totalDescuento;

            $porcIva = $request->get('porc_iva');
            $porcOtroImpuesto = $request->get('porc_otro_impuesto');
            $porcImpoconsumo = $request->get('impoconsumo');
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
                // Se utiliza la información del inventario recuperado
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
                'descuentos'            => $totalDesc,
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
                'centrocosto' => 'required',
                'subcentrodecosto' => 'required',

            ];
            $messages = [
                'ventaId.required' => 'El ventaId es requerido',
                'cliente.required' => 'El cliente es requerido',
                'vendedor.required' => 'El proveedor es requerido',
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

            $getReg = Sale::firstWhere('id', $request->ventaId);


            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format

                $id_user = Auth::user()->id;
                //    $idcc = $request->centrocosto;

                $venta = new Sale();
                $venta->user_id = $id_user;
                $venta->centrocosto_id = $request->centrocosto;
                $venta->third_id = $request->cliente;
                $venta->vendedor_id = $request->vendedor;
                $venta->domiciliario_id = $request->domiciliario;
                $venta->subcentrocostos_id = $request->subcentrodecosto;

                $venta->fecha_venta = $currentDateFormat;
                // $venta->fecha_cierre = $dateNextMonday;

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
                $venta->tipo = "1";
                $venta->save();

                //ACTUALIZA CONSECUTIVO 
                $idcc = $request->centrocosto;
                DB::update(
                    "
        UPDATE sales a,    
        (
            SELECT @numeroConsecutivo:= (SELECT (COALESCE (max(consec),0) ) FROM sales where centrocosto_id = :vcentrocosto1 ),
            @documento:= (SELECT MAX(prefijo) FROM centro_costo where id = :vcentrocosto2 )
        ) as tabla
        SET a.consecutivo =  CONCAT( @documento,  LPAD( (@numeroConsecutivo:=@numeroConsecutivo + 1),5,'0' ) ),
            a.consec = @numeroConsecutivo
        WHERE a.consecutivo is null",
                    [
                        'vcentrocosto1' => $idcc,
                        'vcentrocosto2' => $idcc
                    ]
                );

                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    "registroId" => $venta->id
                ]);
            } else {
                $getReg = Sale::firstWhere('id', $request->ventaId);
                $getReg->third_id = $request->vendedor;
                $getReg->centrocosto_id = $request->centrocosto;
                $getReg->subcentrocostos_id = $request->subcentrodecosto;
                $getReg->factura = $request->factura;
                $getReg->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
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
        $reg = SaleDetail::where('id', $request->id)->first();
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
            ->select('listapreciodetalles.precio', 'prod.iva', 'otro_impuesto', 'prod.impoconsumo', 'listapreciodetalles.porc_descuento') // Select only the
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
        try {
            $currentDateTime = Carbon::now();
            $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date->modify('next monday'); // Move to the next Monday
            $dateNextMonday = $current_date->format('Y-m-d');
            $id_user = Auth::user()->id;

            $venta = new Sale();
            $venta->user_id = $id_user;
            $venta->centrocosto_id = 1;
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

            /*     if ($venta->centrocosto_id == 1 || $venta->centrocosto_id == 2) {
                $count1 = DB::table('sales')->count();
                $count2 = DB::table('notacreditos')->count();
                $count3 = DB::table('notadebitos')->count();
                $count = $count1 + $count2 + $count3;
                $resolucion = 'ERPC ' . (1 + $count);
              //  $venta->resolucion = $resolucion;
                $venta->save();
            }  */

            //ACTUALIZA CONSECUTIVO 
            $idcc = $request->centrocosto;
            DB::update(
                "
     UPDATE sales a,    
     (
         SELECT @numeroConsecutivo:= (SELECT (COALESCE (max(consec),0) ) FROM sales where centrocosto_id = :vcentrocosto1 ),
         @documento:= (SELECT MAX(prefijo) FROM centro_costo where id = :vcentrocosto2 )
     ) as tabla
     SET a.consecutivo =  CONCAT( @documento,  LPAD( (@numeroConsecutivo:=@numeroConsecutivo + 1),5,'0' ) ),
         a.consec = @numeroConsecutivo
     WHERE a.consecutivo is null",
                [
                    'vcentrocosto1' => $idcc,
                    'vcentrocosto2' => $idcc
                ]
            );

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

    /*  // Opcion 2 sin Eloquent
    public function cargarInventariocrOriginal($ventaId)
    {

        $compensadores = DB::table('sales')
            ->where('id', $ventaId)
            ->where('status', '1')
            ->get();


        $ventadetalle = DB::table('sale_details')
            ->where('sale_id', $ventaId)
            ->where('status', '1')
            ->get();

        $product_ids = $ventadetalle->pluck('product_id');
        $store_id = '1';
        $centroCostoProducts = DB::table('centro_costo_products')
            ->whereIn('products_id', $product_ids)
            ->where('store_id', $store_id)
            ->get();

        // Calculate accumulated values and insert into temporary table
        foreach ($centroCostoProducts as $centroCostoProduct) {
            $accumulatedQuantity = DB::table('sale_details')
                ->where('sale_id', $ventaId)
                ->where('status', '1')
                ->where('product_id', $centroCostoProduct->products_id)
                ->sum('quantity');
            //   ->value('quantity');

            $accumulatedTotalBruto = DB::table('sale_details')
                ->where('sale_id', $ventaId)
                ->where('status', '1')
                ->where('product_id', $centroCostoProduct->products_id)
                ->sum('total_bruto');

            DB::table('table_temporary_accumulated_sales')->insert([
                'product_id' => $centroCostoProduct->products_id,
                'accumulated_quantity' => $accumulatedQuantity,
                'accumulated_total_bruto' => $accumulatedTotalBruto
            ]);

            // Update Centro_costo_product records
            $centroCostoProduct = DB::table('centro_costo_products')
                ->where('products_id', $centroCostoProduct->products_id)
                ->first();

            $centroCostoProduct->venta += $accumulatedQuantity;
            $centroCostoProduct->cto_venta_total += $accumulatedTotalBruto;

            DB::table('centro_costo_products')
                ->where('products_id', $centroCostoProduct->products_id)
                ->update([
                    'venta' => $centroCostoProduct->venta,
                    'cto_venta_total' => $centroCostoProduct->cto_venta_total
                ]);
        }

        // Clear the temporary table
        DB::table('table_temporary_accumulated_sales')->truncate();

        // Check and call cuentasPorCobrar function
        if (($compensadores[0]->valor_a_pagar_credito) > 0) {
            // Call cuentasPorCobrar function
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cargado al inventario exitosamente',
            'compensadores' => $compensadores
        ]);
    } */
}


 /* public function cargarInventariocr($ventaId)
    {
        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');
        $compensadores = Sale::where('id', $ventaId)->get();
        $ventadetalle = SaleDetail::where('sale_id', $ventaId)->get();
        $product_ids = $ventadetalle->pluck('product_id');

        $store_id = 1;

        $centroCostoProducts = Centro_costo_product::whereIn('products_id', $product_ids)
            ->where('store_id', $store_id)
            ->get();

        foreach ($centroCostoProducts as $centroCostoProduct) {
            $accumulatedQuantity = SaleDetail::where('sale_id', '=', $ventaId)
                ->where('product_id', $centroCostoProduct->products_id)
                ->sum('quantity');

            $accumulatedTotalBruto = 0;

            $accumulatedTotalBruto += SaleDetail::where('sale_id', '=', $ventaId)
                ->where('product_id', $centroCostoProduct->products_id)
                ->sum('total_bruto');

            DB::table('table_temporary_accumulated_sales')->insert([
                'product_id' => $centroCostoProduct->products_id,
                'accumulated_quantity' => $accumulatedQuantity,
                'accumulated_total_bruto' => $accumulatedTotalBruto
            ]);
        }
        // Recuperar los registros de la tabla table_temporary_accumulated_sales
        $accumulatedQuantitys = DB::table('table_temporary_accumulated_sales')->get();

        foreach ($accumulatedQuantitys as $accumulatedQuantity) {
            $centroCostoProduct = Centro_costo_product::find($accumulatedQuantity->product_id);

            $centroCostoProduct->venta += $accumulatedQuantity->accumulated_quantity;
            $centroCostoProduct->cto_venta_total += $accumulatedQuantity->accumulated_total_bruto;
            $centroCostoProduct->save();

            // Limpiar la tabla table_temporary_accumulated_sales
            DB::table('table_temporary_accumulated_sales')->truncate();
        }

        if (($compensadores[0]->valor_a_pagar_credito) > 0) {
            $this->cuentasPorCobrar($ventaId);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cargado al inventario exitosamente',
            'compensadores' => $compensadores
        ]);
    } */

        /*    public function storeVentaMostrador()
    {
        try {


            // Validación para que solo permita crear la instancia Sale, solo si existe algun nuevo registro en la tabla cajas donde en esa tabla cajas corresponan el campo user_id con cajero_id, fecha_hora_inicio sea igual a la fecha actual, y el campo estado sea igual a open.
            $id_user = Auth::user()->id;
            $caja = Caja::where('user_id', $id_user)
                //  ->where('fecha_hora_inicio', $currentDateTime) 
                ->where('estado', 'open')
                ->first();


            if ($caja) {
                $venta = new Sale();
                $venta->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Inicio de venta por mostrador',
                    'registroId' => $venta->id

                ]);

                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format

                $venta = new Sale();
                $venta->user_id = $id_user;
                $venta->centrocosto_id = 1; // Valor estático para el campo centrocosto
                $venta->third_id = 33; // Valor estático para el campo third_id
                $venta->vendedor_id = 33; // Valor estático para el campo vendedor_id
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

                return response()->json([
                    'status' => 1,
                    'message' => 'venta por mostrador',
                    'registroId' => $venta->id
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'No se puede iniciar una nueva venta por mostrador, ya que no existe una caja abierta.'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }
 */
/*   public function getProductsByStore(Request $request)
    {
        $storeId = $request->store_id;

        // Obtiene los productos que tienen inventario en la bodega seleccionada y stock_ideal > 0.
        // Se hace _eager loading_ de la relación inventarios filtrada por store_id.
        $productos = Product::whereHas('inventarios', function ($query) use ($storeId) {
            $query->where('store_id', $storeId)
                ->where('stock_ideal', '>', 0);
        })
            ->with(['inventarios' => function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            }])
            ->with('lotesPorVencer') // Se asume que usas esta relación para mostrar los lotes.
            ->get();

        // Prepara las opciones para el select (en este ejemplo se hace desde el controlador y se envía vía JSON).
        $options = [];
        foreach ($productos as $producto) {
            // Obtiene el inventario para la tienda (suponiendo que solo hay un registro por producto y tienda)
            $inventario = $producto->inventarios->first();
            foreach ($producto->lotesPorVencer as $lote) {
                $options[] = [
                    'id'               => $producto->id,
                    'text'             => "{$producto->name} - {$lote->codigo} - " .
                        \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') .
                        " - Stock Ideal: " . ($inventario ? $inventario->stock_ideal : 'N/A') .
                        " - Inventario ID: " . ($inventario ? $inventario->id : 'N/A'),
                    'lote_id'          => $lote->id,
                    'inventario_id'    => $inventario ? $inventario->id : '',
                    'stock_ideal'      => $inventario ? $inventario->stock_ideal : '',
                ];
            }
        }

        return response()->json($options);
    }
 */
