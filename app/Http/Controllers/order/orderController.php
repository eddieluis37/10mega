<?php

namespace App\Http\Controllers\order;

use App\Http\Controllers\Controller;
use App\Models\centros\Centrocosto;
use App\Models\Listapreciodetalle;
use App\Models\Notacredito;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Subcentrocosto;
use App\Models\Third;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\Centro_costo_product;
use App\Models\Cuentas_por_cobrar;
use App\Models\Formapago;
use App\Models\NotacreditoDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Products\Meatcut;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Contracts\Role;


class orderController extends Controller
{
    protected $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getDireccionesByCliente($cliente_id)
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

        $ventas = Order::get();
        // $centros = Centrocosto::Where('status', 1)->get();
        // Obtiene los IDs de los centros de costo asociados a las tiendas del usuario autenticado.
        $centroIds = Auth::user()->stores->pluck('centrocosto_id')->unique();

        // Obtiene los modelos de centros de costo usando los IDs obtenidos
        $centros = Centrocosto::whereIn('id', $centroIds)->get();

        // Selecciona el primer centro de costo como valor por defecto (si existe)
        $defaultCentro = $centros->first();

        $clientes = Third::Where('cliente', 1)->get();
        $vendedores = Third::Where('vendedor', 1)->get();
        $alistadores = Third::Where('alistador', 1)->get();
        $subcentrodecostos = Subcentrocosto::get();
        $formapagos = Formapago::get();




        return view('order.index', compact('ventas', 'direccion', 'centros', 'defaultCentro', 'clientes', 'vendedores', 'alistadores', 'subcentrodecostos', 'formapagos'));
    }

    public function show()
    {
        $data = DB::table('orders as or')
            ->join('thirds as tird', 'or.third_id', '=', 'tird.id')
            ->leftJoin('centro_costo as centro', 'or.centrocosto_id', '=', 'centro.id')
            ->join('thirds as vendedor', 'or.vendedor_id', '=', 'vendedor.id')
            ->select('or.*', 'or.status as status', 'total_valor_a_pagar', 'fecha_order', 'tird.direccion as direccion', 'or.resolucion as resolucion', 'tird.name as namethird', 'centro.name as namecentrocosto', 'total_utilidad', 'vendedor.name as nombre_vendedor')
            ->where('or.status', '=', '0') // Filtro para ordenes abiertas
            ->orWhere('or.status', '=', '1') // Filtro para ordenes cerradas
            ->orWhere('or.status', '=', '2') // Filtro para ordenes entregadas, status = 3 eliminada
            ->get();



        //  $data = Sale::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('status', function ($data) {
                if ($data->status == 1) {
                    $status = '<span class="badge bg-success">Close</span>';
                } elseif ($data->status == 2) {
                    $status = '<span class="badge bg-info">Deliv</span>';
                } else {
                    $status = '<span class="badge bg-warning">Open</span>';
                }
                return $status;
            })
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->created_at);
                $formattedDate = $date->format('M-d. H:i');
                return $formattedDate;
            })
            ->addColumn('date2', function ($data) {
                $fechaEntrega = Carbon::parse($data->fecha_entrega);
                $horaInicial = Carbon::parse($data->hora_inicial_entrega);

                $fechaHoraConcatenada = $fechaEntrega->format('M-d') . ' ' . $horaInicial->format('H:i');

                return $fechaHoraConcatenada;
            })
            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();

                if (Carbon::parse($currentDateTime->format('Y-m-d'))->gt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
					    
                        <a href="order/showPDFOrder/' . $data->id . '" class="btn btn-dark" title="VerOrdenDespuesDeFechaCierre" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>                       			
					    <button class="btn btn-dark" title="BorrarOrden" disabled>
						    <i class="fas fa-trash"></i>
					    </button>
                        </div>
                        ';
                } elseif (Carbon::parse($currentDateTime->format('Y-m-d'))->lt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
					    <a href="order/create/' . $data->id . '" class="btn btn-dark" title="EditarDetallesDeOrden">
						    <i class="fas fa-directions"></i>
					    </a>

                         <button class="btn btn-dark" title="Editar CabezaOrden" onclick="edit(' . $data->id . ');">
						    <i class="fas fa-edit"></i>
					    </button>
					   
                        <a href="order/showPDFOrder/' . $data->id . '" class="btn btn-dark" title="PdfOrdenPendiente" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>
                        <button class="btn btn-dark" title="Borrar Orden" onclick="Confirm(' . $data->id . ');">
						    <i class="fas fa-trash"></i>
					    </button>					  
                        </div>
                        ';
                    //ESTADO Cerrada
                } else {

                    if (Gate::allows('open-order')) {
                        $btn = '                       
                            <div class="text-center"> 
                            <button class="btn btn-dark" title="Confirmar Entrega" onclick="Delivered(' . $data->id . ');">
						            <i class="fas fa-check"></i>
					             </button>                             
                                  <button class="btn btn-dark" title="Abrir Pedido" onclick="Reopen(' . $data->id . ');">
						            <i class="fas fa-box-open"></i>
					             </button>
                                <a href="order/showPDFOrder/' . $data->id . '" class="btn btn-dark" title="Pdf pedido cerrado" target="_blank">
                                   <i class="fas fa-file-pdf"></i> <!-- Icono que representa la apertura de un pedido -->
                                </a>
                            </div>                         
                        ';
                    }
                }

                return $btn;
            })

            ->rawColumns(['status', 'date', 'date2', 'action'])
            ->make(true);
    }

    public function store(Request $request) // Crear nota credito desde ventana modal
    {
        try {
            $rules = [
                'ventaId' => 'required',
                'centrocosto' => 'required',
                'vendedor' => 'required',
                'direccion_envio' => 'required',
                'alistador' => 'required',
                'subcentrodecosto' => 'required',
                'hora_inicial_entrega' => 'required',
                'hora_final_entrega' => [
                    'required',
                    'after:hora_inicial_entrega',
                ],
                'forma_de_pago' => 'required',
            ];
            $messages = [
                'ventaId.required' => 'El ventaId es requerido',
                'centrocosto.required' => 'Centro costo es requerido',
                'vendedor.required' => 'Vendedor es requerido',
                'direccion_envio.required' => 'La dirección de envio es requerida',
                'alistador.required' => 'Alistador es requerido',
                'subcentrodecosto.required' => 'Sub Centro de costo es requerido',
                'hora_inicial_entrega.required' => 'La hora inicial de entrega es requerida',
                'hora_final_entrega.required' => 'La hora final de entrega es requerida',
                'hora_final_entrega.after' => 'La hora final de entrega debe ser posterior a la hora inicial',
                'forma_de_pago.required' => 'Forma de pago es requerido',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }
            $getReg = Order::firstWhere('id', $request->ventaId);

            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format
                $id_user = Auth::user()->id;
                //    $idcc = $request->centrocosto;
                $venta = new Order();
                $venta->user_id = $id_user;
                $venta->third_id = $request->cliente;
                $venta->vendedor_id = $request->vendedor;
                $venta->formapago_id = $request->forma_de_pago;
                $venta->alistador_id = $request->alistador;
                $venta->centrocosto_id = $request->centrocosto;
                $venta->subcentrocostos_id = $request->subcentrodecosto;
                //  dd($request->factura); // es el id de la factura de venta seleccionada en el modal create
                $venta->fecha_order = $currentDateFormat;
                $venta->fecha_entrega = $request->fecha_entrega;
                /*  $venta->fecha_cierre =  now(); */
                $venta->direccion_envio = $request->direccion_envio;
                $venta->hora_inicial_entrega = $request->hora_inicial_entrega;
                $venta->hora_final_entrega = $request->hora_final_entrega;
                $venta->items = 0;
                $venta->observacion = $request->observacion;
                $venta->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    "registroId" => $venta->id
                ]);
            } else {
                $updateOrder = Order::firstWhere('id', $request->ventaId);
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $updateOrder->fecha_order = $currentDateFormat;
                $updateOrder->centrocosto_id = $request->centrocosto;
                $updateOrder->third_id = $request->cliente;
                $updateOrder->direccion_envio = $request->direccion_envio;
                $updateOrder->vendedor_id = $request->vendedor;
                $updateOrder->subcentrocostos_id = $request->subcentrodecosto;
                $updateOrder->alistador_id = $request->alistador;
                $updateOrder->fecha_entrega = $request->fecha_entrega;
                $updateOrder->hora_inicial_entrega = $request->hora_inicial_entrega;
                $updateOrder->hora_final_entrega = $request->hora_final_entrega;
                $updateOrder->formapago_id = $request->forma_de_pago;
                $updateOrder->observacion = $request->observacion;
                $updateOrder->save();

                return response()->json([
                    "status" => 1,
                    "message" => "Guardado correctamente",
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
        $venta = Order::find($id);
        $prod = Product::Where([

            ['status', 1]
        ])
            ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        /*     $ventasdetalle = $this->getventasdetalle($id, $venta->centrocosto_id); */
        $arrayTotales = $this->sumTotales($id);

        $datacompensado = DB::table('orders as or')
            ->join('thirds as tird', 'or.third_id', '=', 'tird.id')
            ->join('centro_costo as centro', 'or.centrocosto_id', '=', 'centro.id')
            ->select('or.*', 'tird.name as namethird', 'centro.name as namecentrocosto', 'tird.porc_descuento')
            ->where('or.id', $id)
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


        return view('order.create', compact('datacompensado', 'id', 'prod', 'detalleVenta', 'arrayTotales', 'status'));
    }

    public function sumTotales($id)
    {
        $TotalBrutoSinDescuento = Order::where('id', $id)->value('total_bruto');
        $TotalDescuentos = Order::where('id', $id)->value('descuentos');
        $TotalBruto = (float)OrderDetail::Where([['order_id', $id]])->sum('total_bruto');
        $TotalIva = (float)OrderDetail::Where([['order_id', $id]])->sum('iva');
        $TotalOtroImpuesto = (float)OrderDetail::Where([['order_id', $id]])->sum('otro_impuesto');
        $TotalValorAPagar = (float)OrderDetail::Where([['order_id', $id]])->sum('total');

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
        $detalles = DB::table('order_details as de')
            ->join('products as pro', 'de.product_id', '=', 'pro.id')
            ->select('de.*', 'pro.name as nameprod', 'pro.code', 'de.porc_iva', 'de.iva', 'de.porc_otro_impuesto', 'de.porc_impoconsumo')
            ->where([
                ['de.order_id', $ventaId],
                /*   ['de.status', 1] */
            ])->get();

        return $detalles;
    }

    /* $count = DB::table('orders')->where('status', '1')->count(); */

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

            $formatCantidad = new metodosrogercodeController();

            $formatPrVenta = $formatCantidad->MoneyToNumber($request->price);
            $formatPesoKg = $formatCantidad->MoneyToNumber($request->quantity);

            $getReg = OrderDetail::firstWhere('id', $request->regdetailId);

            $porcDescuento = $request->get('porc_descuento');
            $precioUnitarioBruto = ($formatPrVenta * $formatPesoKg);
            $descuento_prod = $precioUnitarioBruto * ($porcDescuento / 100);
            $porc_descuento = $request->get('porc_descuento_cli');

            $descuentoCliente = $precioUnitarioBruto * ($porc_descuento / 100);
            $totalDescuento = $descuento_prod + $descuentoCliente;

            $precioUnitarioBrutoConDesc = $precioUnitarioBruto - $totalDescuento;
            $porcIva = $request->get('porc_iva');
            $porcOtroImpuesto = $request->get('porc_otro_impuesto');

            $netoSinImp   = $precioUnitarioBruto - $totalDescuento;

            $porcImpoconsumo = $request->get('porc_impoconsumo', 0);
            $impoconsumo     = $netoSinImp * ($porcImpoconsumo / 100);

            $Impuestos = $porcIva + $request->porc_otro_impuesto;
            $TotalImpuestos = $precioUnitarioBrutoConDesc * ($Impuestos / 100);
            $Total = $TotalImpuestos + $precioUnitarioBrutoConDesc;

            $iva = $precioUnitarioBrutoConDesc * ($porcIva / 100);
            $otroImpuesto = $precioUnitarioBrutoConDesc * ($porcOtroImpuesto / 100);

            $totalOtrosImpuestos =  $precioUnitarioBrutoConDesc * ($request->porc_otro_impuesto / 100);

            $valorApagar = $precioUnitarioBrutoConDesc + $totalOtrosImpuestos;


            $totalCosto = $request->get('costo_prod') * $formatPesoKg;

            $utilidad = $precioUnitarioBrutoConDesc - $totalCosto;
            $porc_utilidad = ($utilidad / $precioUnitarioBrutoConDesc) * 100;

            if ($getReg == null) {
                $detail = new OrderDetail();
                $detail->order_id = $request->ventaId;
                $detail->product_id = $request->producto;
                $detail->price = $formatPrVenta;
                $detail->quantity = $formatPesoKg;
                $detail->observaciones =  $request->get('observaciones');
                $detail->quantity_despachada = $request->get('quantity_despachada');
                $detail->costo_prod = $request->get('costo_prod');
                $detail->porc_desc_prod = $porcDescuento;
                $detail->descuento_prod = $descuento_prod;
                $detail->descuento_cliente = $descuentoCliente;
                $detail->porc_iva = $porcIva;
                $detail->iva = $iva;
                $detail->porc_otro_impuesto = $porcOtroImpuesto;
                $detail->otro_impuesto = $otroImpuesto;
                $detail->porc_impoconsumo = $porcImpoconsumo;
                $detail->impoconsumo = $impoconsumo;
                $detail->total_bruto = $precioUnitarioBrutoConDesc;
                $detail->total_costo = $totalCosto;
                $detail->utilidad = $utilidad;
                $detail->porc_utilidad = $porc_utilidad;
                $detail->total = $Total;
                $detail->save();
            } else {
                $updateReg = OrderDetail::firstWhere('id', $request->regdetailId);
                $detalleVenta = $this->getventasdetail($request->ventaId);
                $ivaprod = $detalleVenta[0]->porc_iva;
                $updateReg->product_id = $request->producto;
                $updateReg->price = $formatPrVenta;
                $updateReg->quantity = $formatPesoKg;
                $updateReg->observaciones =  $request->get('observaciones');
                $updateReg->costo_prod =  $request->get('costo_prod');
                $updateReg->porc_desc_prod = $porcDescuento;
                $updateReg->descuento_prod = $descuento_prod;
                $updateReg->descuento_cliente = $descuentoCliente;
                $updateReg->iva = $iva;
                $updateReg->porc_iva = $porcIva;
                $updateReg->porc_otro_impuesto = $porcOtroImpuesto;
                $updateReg->otro_impuesto = $otroImpuesto;
                $updateReg->porc_impoconsumo = $porcImpoconsumo;
                $updateReg->impoconsumo = $impoconsumo;
                $updateReg->total_bruto = $precioUnitarioBrutoConDesc;
                $updateReg->total_costo = $totalCosto;
                $updateReg->total_costo = $totalCosto;
                $updateReg->utilidad = $utilidad;
                $updateReg->porc_utilidad = $porc_utilidad;
                $updateReg->total = $Total;
                $updateReg->save();
            }

            $order = Order::find($request->ventaId);
            $order->items = OrderDetail::where('order_id', $order->id)->count();
            $order->descuentos = $totalDescuento;
            $order->total_iva = $iva;
            $order->total_otros_impuestos = $totalOtrosImpuestos;
            $order->total_valor_a_pagar = $valorApagar;
            $saleDetails = OrderDetail::where('order_id', $order->id)->get();
            $totalBruto = 0;
            $totalDesc = 0;
            $order->total_valor_a_pagar = $saleDetails->where('order_id', $order->id)->sum('total');
            $order->total_utilidad = $saleDetails->where('order_id', $order->id)->sum('utilidad');
            $totalBruto = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->quantity * $saleDetail->price;
            });
            $totalDesc = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->descuento_prod + $saleDetail->descuento_cliente;
            });
            $order->total_bruto = $totalBruto;
            $order->descuentos = $totalDesc;

            $resolucion = 'OP ' . $request->ventaId;
            $order->resolucion = $resolucion;

            $order->save();

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

    public function edit($id)
    {

        $ordenes = Order::where('id', $id)->first();
        return response()->json([
            "id" => $id,
            "ordenespedidos" => $ordenes,
        ]);
    }

    public function editOrder(Request $request)
    {
        $reg = OrderDetail::where('id', $request->id)->first();
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
    }


    public function destroyDetail(Request $request)
    {
        try {

            $compe = OrderDetail::where('id', $request->id)->first();
            $compe->delete();

            $arraydetail = $this->getventasdetail($request->ventaId);

            $arrayTotales = $this->sumTotales($request->ventaId);


            $order = Sale::find($request->ventaId);
            $order->items = OrderDetail::where('order_id', $order->id)->count();
            $order->descuentos = 0;
            $order->total_iva = 0;
            $order->total_otros_impuestos = 0;
            $saleDetails = OrderDetail::where('order_id', $order->id)->get();
            $totalBruto = 0;
            $totalDesc = 0;
            $total_valor_a_pagar = $saleDetails->where('order_id', $order->id)->sum('total');
            $order->total_valor_a_pagar = $total_valor_a_pagar;
            $totalBruto = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->quantity * $saleDetail->price;
            });
            $totalDesc = $saleDetails->sum(function ($saleDetail) {
                return $saleDetail->descuento_prod + $saleDetail->descuento_cliente;
            });
            $order->total_bruto = $totalBruto;
            $order->descuentos = $totalDesc;
            $order->save();

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

    public function storeOrder(Request $request, $id) // para cerrar detalles y cargar a inventario
    {

        $ventaId = Order::where('id', $request->id)->latest()->first(); // el ultimo mas reciente;

        $SaleIdNC = $ventaId->sale_id;

        //  dd($SaleIdNC);

        // Obtener los valores

        $tipo = $request->get('tipo');


        $status = '1'; //1 = pagado       

        try {

            $venta = Order::where('id', $id)->latest()->first(); // el ultimo mas reciente;
            $venta->user_id = $request->user()->id;
            $venta->status = $status;
            $venta->fecha_cierre = now();

            $venta->save();


            if ($venta->status == 1) {
                return redirect()->route('order.index');
            }

            return response()->json([
                'status' => 1,
                'message' => 'Guardado correctamente',
                "registroId" => $venta->id,
                'redirect' => route('order.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function obtenerValores(Request $request)
    {
        $centrocostoId = $request->input('centrocosto');
        $clienteId = $request->input('cliente');
        $cliente = Third::find($clienteId);
        $producto = Listapreciodetalle::join('products as prod', 'listapreciodetalles.product_id', '=', 'prod.id')
            ->join('thirds as t', 'listapreciodetalles.listaprecio_id', '=', 't.id')
            ->where('prod.id', $request->productId)
            ->where('t.id', $cliente->listaprecio_genericid)
            ->select('listapreciodetalles.precio', 'prod.iva', 'otro_impuesto', 'listapreciodetalles.porc_descuento', 'prod.impoconsumo', 'cost') // Select only the
            ->first();
        if ($producto) {
            return response()->json([
                'precio' => $producto->precio,
                'iva' => $producto->iva,
                'otro_impuesto' => $producto->otro_impuesto,
                'impoconsumo' => $producto->impoconsumo,
                'porc_descuento' => $producto->porc_descuento,
                'costo_prod' => $producto->cost
            ]);
        } else {
            // En caso de que el producto no sea encontrado
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
    }

    public function OriginalReopen(Request $request, $id)
    {
        $status = '0'; //1 = pagado       

        try {

            $venta = Order::where('id', $id)->latest()->first(); // el ultimo mas reciente;
            $venta->user_id = $request->user()->id;
            $venta->status = $status;
            $venta->fecha_cierre = now()->addDays(2);
            $venta->save();


            if ($venta->status == 0) {
                return redirect()->route('order.index');
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }

        //    return $this->index();      

    }

    public function delivered(Request $request, $id)
    {
        try {

            $venta = Order::where('id', $id)->latest()->first(); // el ultimo mas reciente;
            $venta->user_id = $request->user()->id;
            $venta->status = '2';
            $venta->fecha_cierre = now()->addDays(-2);
            $venta->save();
            return response()->json([
                "status" => 201,
                "message" => "Pedido # $id engregado con exito",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 500,
                "message" => (array) $th
            ]);
        }
    }

    public function reopen(Request $request, $id)
    {
        try {

            $venta = Order::where('id', $id)->latest()->first(); // el ultimo mas reciente;
            $venta->user_id = $request->user()->id;
            $venta->status = '0';
            $venta->fecha_cierre = now()->addDays(2);
            $venta->save();
            return response()->json([
                "status" => 201,
                "message" => "Registro # $id abierto con exito",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 500,
                "message" => (array) $th
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $updateOrder = Order::firstWhere('id', $id);
            $updateOrder->status = '3';
            $updateOrder->save();
            return response()->json([
                "status" => 201,
                "message" => "Registro # $id eliminado con exito",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 500,
                "message" => (array) $th
            ]);
        }
    }
}
