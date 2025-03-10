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
use App\Models\Listapreciodetalle;
use App\Models\Sale;
use App\Models\SaleCaja;
use App\Models\SaleDetail;
use App\Models\Store;
use App\Models\Subcentrocosto;
use Illuminate\Support\Facades\Log;

class saleautoservicioController extends Controller
{

    public function index()
    {
        $ventas = Sale::get();
        $centros = Store::Where('status', 1)->get();
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
            ->join('stores as s', 'sa.store_id', '=', 's.id')
            ->select('sa.*', 'tird.name as namethird', 's.name as namecentrocosto')
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
                if ($venta->store_id == 1 || $venta->store_id == 2) {
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


    public function cargarInventariocr($ventaId)
    {
        DB::beginTransaction();
        try {
            // Obtener la venta (compensadores)
            $compensadores = DB::table('sales')
                ->where('id', $ventaId)
                ->where('status', '0')
                ->first();

            Log::debug('Venta obtenida', ['ventaId' => $ventaId, 'compensadores' => $compensadores]);

            if (!$compensadores) {
                Log::debug('Venta no encontrada o cerrada', ['ventaId' => $ventaId]);
                return response()->json([
                    'status'  => 1,
                    'message' => 'Venta no encontrada o cerrada.'
                ], 404);
            }

            // Obtener los detalles de la venta
            $ventadetalle = DB::table('sale_details')
                ->where('sale_id', $ventaId)
                ->where('status', '1')
                ->get();

            Log::debug('Detalles de venta obtenidos', [
                'ventaId'      => $ventaId,
                'detalle_count' => $ventadetalle->count()
            ]);

            if ($ventadetalle->isEmpty()) {
                Log::debug('No hay detalles de venta activos', ['ventaId' => $ventaId]);
                return response()->json([
                    'status'  => 0,
                    'message' => 'No hay detalles de venta activos.'
                ], 404);
            }

            $product_ids = $ventadetalle->pluck('product_id');
            $store_id = $compensadores->store_id;

            Log::debug('IDs de productos y tienda obtenidos', [
                'product_ids' => $product_ids,
                'store_id'    => $store_id
            ]);

            // Obtener los registros de inventario de los productos involucrados para la tienda
            $inventarios = DB::table('inventarios')
                ->whereIn('product_id', $product_ids)
                ->where('store_id', $store_id)
                ->get();

            Log::debug('Registros de inventario obtenidos', [
                'inventarios_count' => $inventarios->count()
            ]);

            $movimientos = [];
            foreach ($inventarios as $inventario) {
                $productId = $inventario->product_id;

                // Calcular los acumulados para el producto a partir de los detalles de venta
                $accumulatedQuantity = $ventadetalle->where('product_id', $productId)->sum('quantity');
                $accumulatedTotalBruto = $ventadetalle->where('product_id', $productId)->sum('total_bruto');

                Log::debug('Acumulados calculados para producto', [
                    'product_id'            => $productId,
                    'accumulatedQuantity'   => $accumulatedQuantity,
                    'accumulatedTotalBruto' => $accumulatedTotalBruto
                ]);

                // Obtener el lote próximo a vencer para el producto
                $productModel = \App\Models\Product::find($productId);
                if (!$productModel) {
                    Log::debug('Producto no encontrado', ['product_id' => $productId]);
                    continue;
                }
                $lote = $productModel->lotesPorVencer()->orderBy('fecha_vencimiento', 'asc')->first();
                if (!$lote) {
                    Log::debug('No se encontró lote próximo a vencer para producto', ['product_id' => $productId]);
                    continue; // Se omite si no se encuentra un lote proximo a vencer
                }
                $lote_id = $lote->id;

                // Preparar el movimiento de inventario (tipo venta)
                $movimientos[] = [
                    'product_id'       => $productId,
                    'lote_id'          => $lote_id,
                    'store_origen_id'  => $store_id,
                    'store_destino_id' => null,
                    'tipo'             => 'venta',
                    'sale_id'          => $ventaId,
                    'cantidad'         => $accumulatedQuantity,
                    'costo_unitario'   => $accumulatedTotalBruto,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                // Actualizar el registro de inventario, incrementando los campos de venta y costo
                DB::table('inventarios')
                    ->where('id', $inventario->id)
                    ->update([
                        'cantidad_venta' => DB::raw("cantidad_venta + $accumulatedQuantity"),
                        'costo_unitario' => DB::raw("costo_unitario + $accumulatedTotalBruto"),
                    ]);

                Log::debug('Inventario actualizado', [
                    'inventario_id'       => $inventario->id,
                    'incremento_cantidad' => $accumulatedQuantity,
                    'incremento_costo'    => $accumulatedTotalBruto
                ]);
            }

            // Insertar los movimientos en una sola operación para optimizar
            if (!empty($movimientos)) {
                $insertResult = DB::table('movimiento_inventarios')->insert($movimientos);
                Log::debug('Movimientos de inventario insertados', [
                    'movimientos'       => $movimientos,
                    'resultado_insert'  => $insertResult
                ]);
            } else {
                Log::debug('No se generaron movimientos de inventario');
            }          

            // Si la venta tiene un valor a pagar en crédito, se debe invocar la función correspondiente
            if ($compensadores->valor_a_pagar_credito > 0) {
                Log::debug('Venta tiene valor a pagar en crédito, se debe invocar cuentasPorCobrar', [
                    'valor_a_pagar_credito' => $compensadores->valor_a_pagar_credito
                ]);
                // Llamar a la función cuentasPorCobrar según la implementación requerida
            }

            // Actualizar la venta: marcarla como cerrada (status = 1) y asignar la fecha de cierre a hoy
            DB::table('sales')
                ->where('id', $ventaId)
                ->update([
                    'status'       => '1',
                    'fecha_cierre' => now()
                ]);
            Log::debug('Venta actualizada a cerrada', ['ventaId' => $ventaId]);

            DB::commit();
            Log::debug('Transacción commit exitosa para venta', ['ventaId' => $ventaId]);

            // Redirigir a la ruta 'sales.index' con un mensaje de éxito
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

    // Opcion 2 sin Eloquent
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

        $prod = Product::where('status', '1')
            ->whereHas('inventarios', function ($query) {
                $query->where('stock_ideal', '>', 0);
            })
            ->whereHas('lotesPorVencer') // Asegura que haya al menos un lote próximo a vencer

            ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $prod->transform(function ($prod) {
            $prod->lotesPorVencer->transform(function ($lote) use ($prod) {
                $lote->producto_lote_vencimiento = "{$prod->name} - {$lote->codigo} - " . \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y');
                return $lote;
            });
            return $prod;
        });


        $ventasdetalle = $this->getventasdetalle($id, $venta->store_id);
        $arrayTotales = $this->sumTotales($id);

        $datacompensado = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('stores as s', 'sa.store_id', '=', 's.id')
            ->select('sa.*', 'tird.name as namethird', 's.name as namestore', 'tird.porc_descuento as porc_descuento_cliente')
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


        return view('sale.create', compact('datacompensado', 'id', 'prod', 'detalleVenta', 'ventasdetalle', 'arrayTotales', 'status', 'statusInventory', 'display'));
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

    public function create_reg_pago($id)
    {
        $forma_pago_tarjeta = Formapago::Where('tipoformapago', '=', 'TARJETA')->get();
        $forma_pago_otros = Formapago::Where('tipoformapago', '=', 'OTROS')->get();
        $forma_pago_credito = Formapago::Where('tipoformapago', '=', 'CREDITO')->get();

        $dataVenta = DB::table('sales as sa')
            ->join('thirds as tird', 'sa.third_id', '=', 'tird.id')
            ->join('stores as s', 'sa.store_id', '=', 's.id')
            ->select('sa.*', 'tird.name as namethird', 's.name as namebodega', 'tird.porc_descuento', 'sa.total_iva', 'sa.vendedor_id')
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
            $rules = [
                'ventaId' => 'required',
                'producto' => 'required',
                'price' => 'required',
                'quantity' => 'required',
            ];
            $messages = [
                'ventaId.required' => 'El compensado es requerido',
                'producto.required' => 'El producto es requerido',
                'price.required' => 'El precio de compra es requerido',
                'quantity.required' => 'El peso es requerido',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }
            //$yourController->yourFunction($request);
            //$total = $request->kgrequeridos * $request->precioventa;
            //$preciov = $request->precioventa * 1.0;
            //$subtotal = $request->price * $request->quantity;

            //$total = $precioUnitarioBruto *  $request->iva;          

            $formatCantidad = new metodosrogercodeController();

            $formatPrVenta = $formatCantidad->MoneyToNumber($request->price);
            $formatPesoKg = $formatCantidad->MoneyToNumber($request->quantity);

            $getReg = SaleDetail::firstWhere('id', $request->regdetailId);

            $porcDescuento = $request->get('porc_descuento');
            $precioUnitarioBruto = ($formatPrVenta * $formatPesoKg);
            $descuentoProducto = $precioUnitarioBruto * ($porcDescuento / 100);
            $porc_descuento_cliente = $request->get('porc_descuento_cliente');
            $descuentoCliente = $precioUnitarioBruto * ($porc_descuento_cliente / 100);
            $totalDescuento = $descuentoProducto + $descuentoCliente;
            $precioUnitarioBrutoConDesc = $precioUnitarioBruto - $totalDescuento;
            $porcIva = $request->get('porc_iva');
            $porcOtroImpuesto = $request->get('porc_otro_impuesto');
            $iva = $precioUnitarioBrutoConDesc * ($porcIva / 100);
            $otroImpuesto = $precioUnitarioBrutoConDesc * ($porcOtroImpuesto / 100);

            $totalOtrosImpuestos =  $precioUnitarioBrutoConDesc * ($request->porc_otro_impuesto / 100);
            $valorApagar = $precioUnitarioBrutoConDesc + $totalOtrosImpuestos;

            if ($getReg == null) {
                $detail = new SaleDetail();
                $detail->sale_id = $request->ventaId;
                $detail->product_id = $request->producto;
                $detail->price = $formatPrVenta;
                $detail->quantity = $formatPesoKg;
                $detail->porc_desc = $porcDescuento;
                $detail->descuento = $descuentoProducto;
                $detail->descuento_cliente = $descuentoCliente;
                $total_sin_impuesto = $precioUnitarioBruto - ($descuentoProducto + $descuentoCliente);
                $detail->porc_iva = $porcIva;
                $detail->iva = $iva;
                $detail->porc_otro_impuesto = $porcOtroImpuesto;
                $detail->otro_impuesto = $otroImpuesto;
                $total_impuestos = $iva + $otroImpuesto;
                $detail->total_bruto = $precioUnitarioBruto;
                $detail->total = $total_sin_impuesto + $total_impuestos;
                $detail->save();
            } else {
                $updateReg = SaleDetail::firstWhere('id', $request->regdetailId);
                $detalleVenta = $this->getventasdetail($request->ventaId);
                $ivaprod = $detalleVenta[0]->porc_iva;
                $updateReg->product_id = $request->producto;
                $updateReg->price = $formatPrVenta;
                $updateReg->quantity = $formatPesoKg;
                $updateReg->porc_desc = $porcDescuento;
                $updateReg->descuento = $descuentoProducto;
                $updateReg->descuento_cliente = $descuentoCliente;
                $total_sin_impuesto = $precioUnitarioBruto - ($descuentoProducto + $descuentoCliente);
                $updateReg->porc_iva = $porcIva;
                $updateReg->iva = $iva;
                $updateReg->porc_otro_impuesto = $porcOtroImpuesto;
                $updateReg->otro_impuesto = $otroImpuesto;
                $total_impuestos = $iva + $otroImpuesto;
                $updateReg->total_bruto = $precioUnitarioBruto;
                $updateReg->total = $total_sin_impuesto + $total_impuestos;
                $updateReg->save();
            }

            $sale = Sale::find($request->ventaId);
            $sale->items = SaleDetail::where('sale_id', $sale->id)->count();
            $sale->descuentos = $totalDescuento;
            $sale->total_iva = $iva;
            $sale->total_otros_impuestos = $totalOtrosImpuestos;
            $sale->total_valor_a_pagar = $valorApagar;
            $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();
            $totalBruto = 0;
            $totalDesc = 0;
            $sale->total_valor_a_pagar = $saleDetails->where('sale_id', $sale->id)->sum('total');
            $totalBruto = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->quantity * $saleDetail->price;
            });
            $totalDesc = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->descuento + $saleDetail->descuento_cliente;
            });
            $sale->total_bruto = $totalBruto;
            $sale->descuentos = $totalDesc;
            $sale->save();

            $arraydetail = $this->getventasdetail($request->ventaId);

            $arrayTotales = $this->sumTotales($request->ventaId);

            return response()->json([
                'status' => 1,
                'message' => "Agregado correctamente",
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
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
                'store' => 'required',
                'subcentrodecosto' => 'required',

            ];
            $messages = [
                'ventaId.required' => 'El ventaId es requerido',
                'cliente.required' => 'El cliente es requerido',
                'vendedor.required' => 'El proveedor es requerido',
                'store.required' => 'La bodega es requerida',
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
                //    $idcc = $request->store;

                $venta = new Sale();
                $venta->user_id = $id_user;
                $venta->store_id = $request->store;
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
                $idcc = $request->store;
                DB::update(
                    "
        UPDATE sales a,    
        (
            SELECT @numeroConsecutivo:= (SELECT (COALESCE (max(consec),0) ) FROM sales where store_id = :vcentrocosto1 ),
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
                $getReg->store_id = $request->store;
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
        $centrocostoId = $request->input('store');
        $clienteId = $request->input('cliente');
        $cliente = Third::find($clienteId);
        $producto = Listapreciodetalle::join('products as prod', 'listapreciodetalles.product_id', '=', 'prod.id')
            ->join('thirds as t', 'listapreciodetalles.listaprecio_id', '=', 't.id')
            ->where('prod.id', $request->productId)
            ->where('t.id', $cliente->listaprecio_genericid)
            ->select('listapreciodetalles.precio', 'prod.iva', 'otro_impuesto', 'listapreciodetalles.porc_descuento') // Select only the
            ->first();
        if ($producto) {
            return response()->json([
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'otro_impuesto' => $producto->otro_impuesto,
                'porc_descuento' => $producto->porc_descuento
            ]);
        } else {
            // En caso de que el producto no sea encontrado
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
    }

    public function buscarPorCodigoBarras(Request $request)
    {
        $codigoBarras = $request->input('codigoBarras');
        $centrocostoId = $request->input('store');
        $clienteId = $request->input('cliente');

        $cliente = Third::find($clienteId);

        $producto = Listapreciodetalle::join('products as prod', 'listapreciodetalles.product_id', '=', 'prod.id')
            ->join('thirds as t', 'listapreciodetalles.listaprecio_id', '=', 't.id')
            ->where('prod.barcode', $codigoBarras) // Buscar por el código de barras escaneado
            ->where('t.id', $cliente->listaprecio_genericid)
            ->select('listapreciodetalles.precio', 'prod.iva', 'otro_impuesto', 'listapreciodetalles.porc_descuento')
            ->first();

        if ($producto) {
            return response()->json([
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'otro_impuesto' => $producto->otro_impuesto,
                'porc_descuento' => $producto->porc_descuento
            ]);
        } else {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
    }


    public function storeVentaMostrador(Request $request) // POS
    {
        try {
            $currentDateTime = Carbon::now();
            $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
            $current_date->modify('next monday'); // Move to the next Monday
            $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format
            $id_user = Auth::user()->id;

            $venta = new Sale();
            $venta->user_id = $id_user;
            $venta->store_id = 1; // Valor estático para el campo store
            $venta->subcentrocostos_id = 2; // Valor estático para el campo Subcentrocosto PUNTO DE VENTA GUAD
            $venta->third_id = 52; // Valor estático para el campo third_id
            $venta->vendedor_id = 52; // Valor estático para el campo vendedor_id

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

            /*     if ($venta->store_id == 1 || $venta->store_id == 2) {
                $count1 = DB::table('sales')->count();
                $count2 = DB::table('notacreditos')->count();
                $count3 = DB::table('notadebitos')->count();
                $count = $count1 + $count2 + $count3;
                $resolucion = 'ERPC ' . (1 + $count);
              //  $venta->resolucion = $resolucion;
                $venta->save();
            }  */

            //ACTUALIZA CONSECUTIVO 
            $idcc = $request->store;
            DB::update(
                "
     UPDATE sales a,    
     (
         SELECT @numeroConsecutivo:= (SELECT (COALESCE (max(consec),0) ) FROM sales where store_id = :vcentrocosto1 ),
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
                $venta->store_id = 1; // Valor estático para el campo store
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
}
