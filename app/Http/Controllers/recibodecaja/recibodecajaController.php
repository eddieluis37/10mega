<?php

namespace App\Http\Controllers\recibodecaja;


use App\Models\Cuentaporcobrar;
use App\Models\CuentaPorPagar;
use App\Models\Recibodecaja;
use App\Models\CajaReciboDineroDetail;

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

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\caja\Caja;
use App\Models\Centro_costo_product;
use App\Models\Formapago;
use App\Models\Listapreciodetalle;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Subcentrocosto;


class recibodecajaController extends Controller
{
    public function facturasByCliente($cliente_id)
    {
        $facturas = Sale::where('third_id', $cliente_id)->orderBy('id', 'desc')->get(); // despliega las mas reciente
        return response()->json($facturas);
    }

    public function index()
    {
        // 1) IDs de centros de costo del usuario
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // 2) Centros de costo
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // 3) Otros datos
        $ventas = Sale::all();
        $formapagos = Formapago::whereIn('tipoformapago', ['EFECTIVO', 'TARJETA', 'OTROS'])->get();
        $domiciliarios = Third::where('domiciliario', 1)->get();

        // 4) Clientes con deuda > 0, filtrados por centrocosto de la venta
        $clientes = DB::table('thirds')
            ->join('cuentas_por_cobrars', 'cuentas_por_cobrars.third_id', '=', 'thirds.id')
            ->join('sales', 'sales.id', '=', 'cuentas_por_cobrars.sale_id')
            ->select(
                'thirds.id',
                'thirds.name',
                DB::raw('SUM(cuentas_por_cobrars.deuda_x_cobrar) as total_deuda')
            )
            ->where('cuentas_por_cobrars.deuda_x_cobrar', '>', 0)
            ->whereIn('sales.centrocosto_id', $centroIds)
            ->groupBy('thirds.id', 'thirds.name')
            ->having('total_deuda', '>', 0)
            ->get();

        return view('recibodecaja.index', compact(
            'ventas',
            'centros',
            'clientes',
            'formapagos',
            'domiciliarios'
        ));
    }



    // Método para obtener los registros de cuentas_por_cobrars del cliente seleccionado
    public function getClientPayments(Request $request)
    {
        $clienteId = $request->query('client_id');

        // Se consulta la tabla cuentas_por_cobrars y se hacen los join necesarios con thirds y sales
        $records = DB::table('cuentas_por_cobrars')
            ->join('thirds', 'cuentas_por_cobrars.third_id', '=', 'thirds.id')
            ->join('sales', 'cuentas_por_cobrars.sale_id', '=', 'sales.id')
            ->select(
                'cuentas_por_cobrars.id',
                'cuentas_por_cobrars.fecha_inicial as FECHA_VENTA',
                'thirds.identification as identification_cliente',
                'sales.consecutivo as consecutivo',
                'cuentas_por_cobrars.fecha_vencimiento as FECHA_VENCIMIENTO',
                DB::raw('DATEDIFF(CURRENT_DATE, cuentas_por_cobrars.fecha_vencimiento) as DIAS_MORA'),
                'cuentas_por_cobrars.deuda_x_cobrar',
                'cuentas_por_cobrars.deuda_x_pagar',
                'cuentas_por_cobrars.saldo_cartera'
            )
            ->where('cuentas_por_cobrars.third_id', $clienteId)
            ->get();

        return response()->json($records);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show()
    {
        $data = DB::table('recibodecajas as rc')
            //->join('sales as sa', 'rc.sale_id', '=', 'sa.id')
            ->join('thirds as tird', 'rc.third_id', '=', 'tird.id')
            /* ->join('subcentrocostos as centro', 'rc.subcentrocostos_id', '=', 'centro.id') */
            ->select('rc.*', 'tird.name as resolucion_factura', 'tird.name as namethird')
            /*  ->where('rc.status', 1) */
            ->get();

        //  $data = Sale::orderBy('id','desc');

        return Datatables::of($data)->addIndexColumn()
            ->addColumn('status', function ($data) {
                if ($data->status == 1) {
                    $status = '<span class="badge bg-success">Cerrada</span>';
                } else {
                    $status = '<span class="badge bg-danger">Pendiente</span>';
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

                if ($data->status == 1) {
                    $btn = '
                         <div class="text-center">                         
                         <button class="btn btn-warning" title="DetalleRecibo" onclick="openReport(' . $data->id . ');">
						    DE
					     </button> 
                         <a href="recibodecaja/showRecibodecaja/' . $data->id . '" class="btn btn-dark" title="RecibodecajaFormatoGrande" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>				
                        <a href="recibodecaja/showFormatopos/' . $data->id . '" class="btn btn-red" title="FormatoPOS" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                        
                         </div>
                         ';
                } elseif ($data->status == 0) {
                    $btn = '
                         <div class="text-center">
                         <a href="recibodecaja/create/' . $data->id . '" class="btn btn-dark" title="Detalles">
                             <i class="fas fa-directions"></i>
                         </a>
                        
                         <a href="recibodecaja/showRecibodecaja/' . $data->id . '" class="btn btn-dark" title="RecibodecajaPendiente" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                         <button class="btn btn-dark" title="Borrar venta">
                         <i class="fas fa-trash"></i>
                         </button>
                       
                         </div>
                         ';
                    //ESTADO Cerrada
                } else {
                    $btn = '
                         <div class="text-center">
                         <a href="recibodecaja/showRecibodecaja/' . $data->id . '" class="btn btn-dark" title="RecibodecajaCerrado" target="_blank">
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $rules = [
                'recibocajaId' => 'required',
                'cliente' => 'required',
                'formapagos' => 'required',
                'tipo' => 'required',

            ];
            $messages = [
                'recibocajaId.required' => 'El recibocajaId es requerido',
                'cliente.required' => 'El cliente es requerido',
                'formapagos.required' => 'Forma pago es requerido',
                'tipo.required' => 'El tipo es requerido',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getReg = Recibodecaja::firstWhere('id', $request->recibocajaId);

            /*  $SaleIdRC = $getReg->sale_id; */

            // dd ($getReg);

            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format

                $id_user = Auth::user()->id;
                //    $idcc = $request->centrocosto;

                $recibo = new Recibodecaja();
                $recibo->user_id = $id_user;
                $recibo->third_id = $request->cliente;
                $recibo->sale_id = 1;
                $recibo->tipo = $request->tipo;
                $recibo->formapagos_id = $request->formapagos;
                $recibo->abono = 0;

                $recibo->fecha_elaboracion = $request->valor_recibo;
                $recibo->fecha_cierre = $dateNextMonday;

                $recibo->save();

                //ACTUALIZA CONSECUTIVO 
                $idcc = $request->centrocosto;
                DB::update(
                    "
        UPDATE recibodecajas a,    
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
                    "registroId" => $recibo->id
                ]);
            } else {
                $getReg = Recibodecaja::firstWhere('id', $request->recibocajaId);
                dd($getReg);

                $getReg->third_id = $request->vendedor;

                $getReg->tipo = $request->tipo;

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

    public function create($id)
    {
        $venta = Recibodecaja::find($id);
        // dd($venta->third_id);
        $clienteId = $venta->third_id;

        $prod = Sale::Where([
            ['status', '1'],
            ['third_id', $clienteId]
        ])
            /* ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc') */
            ->get();
        $ventasdetalle = $this->getventasdetalle($id, $venta->centrocosto_id);
        $arrayTotales = $this->sumTotales($id);

        $datacompensado = DB::table('recibodecajas as rc')
            ->join('thirds as tird', 'rc.third_id', '=', 'tird.id')
            ->leftjoin('cuentas_por_cobrars as cc', 'rc.sale_id', '=', 'cc.sale_id')
            /*   ->join('subcentrocostos as centro', 'rc.subcentrocostos_id', '=', 'centro.id') */
            ->select('rc.*', 'cc.deuda_inicial', 'tird.name as namethird', 'tird.porc_descuento', 'tird.identification')
            ->where('rc.id', $id)
            ->orderBy('cc.id', 'desc')
            ->get();


        $status = '';
        $fechaCompensadoCierre = Carbon::parse($datacompensado[0]->fecha_cierre);
        $date = Carbon::now();
        $currentDate = Carbon::parse($date->format('Y-m-d'));
        if ($currentDate->gt($fechaCompensadoCierre)) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($currentDate->lt($fechaCompensadoCierre)) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }


        $detalleVenta = $this->getventasdetail($id);


        return view('recibodecaja.create', compact('datacompensado', 'id', 'prod', 'detalleVenta', 'ventasdetalle', 'arrayTotales', 'status'));
    }

    public function getventasdetalle($ventaId, $centrocostoId)
    {
        $detail = DB::table('sale_details as dv')
            ->join('products as pro', 'dv.product_id', '=', 'pro.id')
            ->join('centro_costo_products as ce', 'pro.id', '=', 'ce.products_id')
            ->select('dv.*', 'pro.name as nameprod', 'pro.code',  'ce.fisico')
            ->selectRaw('ce.invinicial + ce.compraLote + ce.alistamiento +
            ce.compensados + ce.trasladoing - (ce.venta + ce.trasladosal) stock')
            ->where([
                ['ce.centrocosto_id', $centrocostoId],
                ['dv.sale_id', $ventaId],
            ])->orderBy('dv.id', 'DESC')->get();

        return $detail;
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

    public function obtenerValores(Request $request)
    {
        $centrocostoId = $request->input('centrocosto');
        $clienteId = $request->input('cliente');
        $cliente = Third::find($clienteId);
        $producto = Sale::join('thirds as t', 'sales.third_id', '=', 't.id')
            ->leftjoin('recibodecajas as rc', 'sales.id', '=', 'rc.sale_id')
            ->where('sales.id', $request->productId)
            ->where('t.id', $cliente->id)
            ->selectRaw('sales.*, sales.valor_a_pagar_credito - SUM(rc.abono) as saldo_pendiente')
            ->groupBy('sales.id', 'rc.id')
            ->orderBy('rc.id', 'desc')
            ->first();
        if ($producto) {
            return response()->json([
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'facturaId' => $request->productId,
                'deuda_inicial' => $producto->valor_a_pagar_credito,
                'saldo_pendiente' => $producto->saldo_pendiente
            ]);
        } else {
            // En caso de que el producto no sea encontrado
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
    }

    public function gurdarrecibodecaja(Request $request)
    {
        try {
            $rules = [
                'recibodecajaId' => 'required',
                /*   'producto' => 'required', */
                'abono' => 'required',

            ];
            $messages = [
                'recibodecajaId.required' => 'El reciboId es requerido',
                /*   'producto.required' => 'La factura es requerida', */
                'abono.required' => 'El abono es requerido',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }


            $getReg = Recibodecaja::firstWhere('id', $request->recibodecajaId);
            $recibodecajaId = $request->get('recibodecajaId');
            $saldo = str_replace('.', '', $request->get('saldo'));
            $abono = str_replace('.', '', $request->get('abono'));
            $nuevo_saldo = str_replace('.', '', $request->get('nuevo_saldo'));
            //  dd($saldo, $abono, $nuevo_saldo);

            if ($getReg == null) {
                $detail = new Recibodecaja();
                $detail->sale_id = $request->ventaId;
                $detail->product_id = $request->producto;

                $detail->save();
            } else {
                $updateReg = Recibodecaja::firstWhere('id', $request->recibodecajaId);
                $updateReg->sale_id = $request->facturaId;
                $updateReg->abono =  $abono;
                $updateReg->nuevo_saldo = 0;
                $updateReg->status = '1';
                $updateReg->observations = $request->get('observations');

                $count1 = DB::table('recibodecajas')->where('status', '1')->count();
                $resolucion = 'RC' . (1 + $count1);
                $updateReg->consecutivo = $resolucion;
                $updateReg->save();
            }


            /*  $arraydetail = $this->getventasdetail($request->ventaId);

            $arrayTotales = $this->sumTotales($request->ventaId); */

            return response()->json([
                'status' => 1,
                'message' => "Agregado correctamente",
                "registroId" => $updateReg->id,
                /*   'redirect' => route('sale.index') */
                /*       'array' => $arraydetail,
                'arrayTotales' => $arrayTotales */
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => (array) $th
            ]);
        }
    }

    public function payment(Request $request)
    {
        // 1) Defino las reglas por fila, incluida la validación condicional de formaPago
        $rules = [
            'cliente'               => 'required|exists:thirds,id',
            'tableData'             => 'required|array|min:1',
            'tableData.*.id'        => 'required|exists:cuentas_por_cobrars,id',
            'tableData.*.vr_deuda'  => 'required|numeric|min:0',
            'tableData.*.vr_pago'   => 'required|numeric|min:0',
            'tableData.*.formaPago' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (preg_match('/tableData\.(\d+)\.formaPago$/', $attribute, $m)) {
                        $index = $m[1];
                        $vrPago = data_get($request->input('tableData'), "$index.vr_pago", 0);
                        if ($vrPago > 0 && empty($value)) {
                            $fail("La forma de pago es obligatoria cuando el pago es mayor a cero (fila #{$index}).");
                        }
                    }
                },
            ],
        ];

        // 2) Construyo el Validator y le añado el chequeo “al menos un vr_pago > 0”
        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $rows = $request->input('tableData', []);
            $hayPago = false;
            foreach ($rows as $row) {
                if (isset($row['vr_pago']) && is_numeric($row['vr_pago']) && $row['vr_pago'] > 0) {
                    $hayPago = true;
                    break;
                }
            }
            if (! $hayPago) {
                // error global en "tableData"
                $validator->errors()->add(
                    'tableData',
                    'Debe registrar al menos un pago mayor a cero en alguna fila.'
                );
            }
        });

        // 3) Lanzo la validación (throws ValidationException si hay errores)
        $validator->validate();

        // 4) Si todo ok, continúo con tu transacción habitual...
        DB::transaction(function () use ($request) {
            $userId = auth()->id();
            $recibo = Recibodecaja::create([
                'user_id'           => $userId,
                'third_id'          => $request->cliente,
                'fecha_elaboracion' => now(),
                'tipo'              => '1',
                'status'            => '1',
                'realizar_un'       => 'Abono a deuda',
            ]);
            foreach ($request->tableData as $row) {
                $recibo->details()->create([
                    'user_id'               => $userId,
                    'cuentas_por_cobrar_id' => $row['id'],
                    'formapagos_id'         => $row['formaPago'] ?? null,
                    'vr_deuda'              => $row['vr_deuda'],
                    'vr_pago'               => $row['vr_pago'],
                    'nvo_saldo'             => $row['nvo_saldo'],
                ]);
                Cuentaporcobrar::find($row['id'])
                    ->updateSaldo($row['nvo_saldo']);
            }
            // ¡Cuidado! refresh() antes de recalculateTotals()
            $recibo->refresh()
                ->recalculateTotals();
        });

        return response()->json([
            'success'     => 'Pago registrado exitosamente.',
            'reloadTable' => true,
        ], 200);
    }
}
