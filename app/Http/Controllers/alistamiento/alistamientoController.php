<?php

namespace App\Http\Controllers\alistamiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\centros\Centrocosto;
use App\Models\alistamiento\Alistamiento;
use App\Models\alistamiento\enlistment_details;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Products\Meatcut;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\Centro_costo_product;
use App\Models\shopping\shopping_enlistment;
use App\Models\shopping\shopping_enlistment_details;
use App\Models\Store;
use App\Models\Lote;
use App\Models\Inventario;

class alistamientoController extends Controller
{

    public function getLotes($storeId)
    {
        $loteIds = Inventario::where('store_id', $storeId)->pluck('lote_id');
        $lotes = Lote::whereIn('id', $loteIds)
            ->where('status', '1')
            ->pluck('codigo', 'id'); // Obtener lotes que cumplan con los filtros
        return response()->json($lotes);
    }

    public function getProductos($loteId)
    {
        $productIds = Inventario::where('lote_id', $loteId)->pluck('product_id');
        $productos = Product::whereIn('id', $productIds)
            ->where('level_product_id', 1)
            ->where('status', 1)
            ->pluck('name', 'id'); // Obtener productos que cumplan con los filtros
        return response()->json($productos);
    }

    public function store(Request $request) // alistamientosave Llenado del modal_create.blade
    {
        try {
            // Reglas de validación
            $rules = [
                'alistamientoId' => 'nullable',
                'fecha' => 'required|date',
                'inputstore' => 'required|exists:stores,id',
                'inputlote' => 'required|exists:lotes,id',
                'select2corte' => 'required|exists:products,id',
                'cantidadprocesar' => 'required|numeric|min:0',
            ];
            $messages = [
                'fecha.required' => 'La fecha es requerida.',
                'inputstore.required' => 'La bodega es requerida.',
                'inputlote.required' => 'El lote es requerido.',
                'select2corte.required' => 'El corte padre es requerido.',
                'cantidadprocesar.required' => 'La cantidad a procesar es requerida.',
                'cantidadprocesar.numeric' => 'La cantidad a procesar debe ser un número.',
                'cantidadprocesar.min' => 'La cantidad a procesar no puede ser negativa.',
                'inputstore.exists' => 'La bodega seleccionada no existe.',
                'inputlote.exists' => 'El lote seleccionado no existe.',
                'select2corte.exists' => 'El corte padre seleccionado no existe.',
            ];

            // Validar la solicitud
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Obtener la categoría del producto seleccionado
            $product = Product::find($request->select2corte);
            if (!$product) {
                return response()->json([
                    'status' => 0,
                    'message' => 'El producto seleccionado no existe.',
                ], 404);
            }

            // Generar el código del nuevo lote hijos del alistamiento
            $dateNow = Carbon::now();
            $year = substr($dateNow->year, -2);
            $month = str_pad($dateNow->month, 2, '0', STR_PAD_LEFT);
            $day = str_pad($dateNow->day, 2, '0', STR_PAD_LEFT);
            $newLote = "";
            $reg = Alistamiento::select()->first();

            if ($reg === null) {
                $newLote = $day . $month . $year . "AL1";
            } else {
                $regUltimo = Alistamiento::latest()->first();
                $consecutivo = $regUltimo ? $regUltimo->id + 1 : 1;
                $newLote = $day . $month . $year . "AL" . $consecutivo;
            }

            // Crear el nuevo lote
            $nuevoLote = new Lote();
            $nuevoLote->category_id = $product->category_id;
            $nuevoLote->codigo = $newLote;
            $nuevoLote->fecha_vencimiento = Carbon::now()->addDays(35)->format('Y-m-d');
            $nuevoLote->save();

            // Verificar si es un nuevo registro o una actualización
            $alistamiento = Alistamiento::find($request->alistamientoId);
            if (!$alistamiento) {
                $alistamiento = new Alistamiento();
                $alistamiento->users_id = Auth::id();
            }

            // Obtener el costo unitario desde el modelo Inventario
            $inventario = Inventario::where('store_id', $request->inputstore)
                ->where('lote_id', $request->inputlote)
                ->where('product_id', $request->select2corte)
                ->first();

            $costoUnitarioPadre = $inventario ? $inventario->costo_unitario : 0;

            // Asignar los datos
            $alistamiento->store_id = $request->input('inputstore');
            $alistamiento->lote_id = $request->input('inputlote');
            $alistamiento->product_id = $request->input('select2corte');
            $alistamiento->fecha_alistamiento = $request->input('fecha');
            $alistamiento->lote_hijos_id = $nuevoLote->id;
            $alistamiento->costo_unitario_padre = $costoUnitarioPadre;

            //   $alistamiento->costo_unitario_padre =  $product->cost;

            // Convertir y asignar cantidad_padre_a_procesar
            $cantidadProcesar = number_format((float)$request->cantidadprocesar, 2, '.', '');
            $alistamiento->cantidad_padre_a_procesar = $cantidadProcesar; // Asignar el valor a la propiedad
            $alistamiento->total_costo = $cantidadProcesar * $costoUnitarioPadre;

            // Calcular la fecha de cierre (próximo lunes)
            $fechaCierre = Carbon::now()->next(Carbon::MONDAY);
            $alistamiento->fecha_cierre = $fechaCierre->format('Y-m-d');

            // Guardar el registro
            $alistamiento->save();

            return response()->json([
                'status' => 1,
                'message' => 'Alistamiento guardado correctamente.',
                'registroId' => $alistamiento->id,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Ocurrió un error al guardar el alistamiento.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function show()
    {
        $data = DB::table('enlistments as ali')
            ->join('stores as s', 'ali.store_id', '=', 's.id')
            ->join('lotes as l', 'ali.lote_id', '=', 'l.id')
            ->join('lotes as lh', 'ali.lote_hijos_id', '=', 'lh.id')
            ->join('products as p', 'ali.product_id', '=', 'p.id')
            ->select('ali.*', 'l.codigo as codigolote', 'lh.codigo as codigolotehijo', 's.name as namebodega', 'p.name as namecut')
            ->where('ali.status', 1)
            ->get();

        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('fecha', function ($data) {
                $fecha = Carbon::parse($data->fecha_alistamiento);
                $onlyDate = $fecha->toDateString();
                return $onlyDate;
            })
            ->addColumn('inventory', function ($data) {
                if ($data->inventario == 'pending') {
                    $statusInventory = '<span class="badge bg-warning">Pendiente</span>';
                } else {
                    $statusInventory = '<span class="badge bg-success">Agregado</span>';
                }
                return $statusInventory;
            })
            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();
                if (Carbon::parse($currentDateTime->format('Y-m-d'))->gt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                    <div class="text-center">
					<a href="alistamiento/create/' . $data->id . '" class="btn btn-dark" title="Transformar" >
						<i class="fas fa-directions"></i>
					</a>
					<button class="btn btn-dark" title="" onclick="showDataForm(' . $data->id . ')">
						<i class="fas fa-eye"></i>
					</button>
					<button class="btn btn-dark" title="" disabled>
						<i class="fas fa-trash"></i>
					</button>
                    </div>
                    ';
                } elseif (Carbon::parse($currentDateTime->format('Y-m-d'))->lt(Carbon::parse($data->fecha_cierre))) {
                    $status = '';
                    if ($data->inventario == 'added') {
                        $status = 'disabled';
                    }
                    $btn = '
                    <div class="text-center">
					<a href="alistamiento/create/' . $data->id . '" class="btn btn-dark" title="Transformar" >
						<i class="fas fa-directions"></i>
					</a>
					<button class="btn btn-dark" title="" onclick="showDataForm(' . $data->id . ')">
						<i class="fas fa-eye"></i>
					</button>
					<button class="btn btn-dark" title="Borrar transformación" onclick="downAlistamiento(' . $data->id . ');" ' . $status . '>
						<i class="fas fa-trash"></i>
					</button>
                    </div>
                    ';
                } else {
                    $btn = '
                    <div class="text-center">
					<a href="alistamiento/create/' . $data->id . '" class="btn btn-dark" title="Transformar" >
						<i class="fas fa-directions"></i>
					</a>
					<button class="btn btn-dark" title="" onclick="showDataForm(' . $data->id . ')">
						<i class="fas fa-eye"></i>
					</button>
					<button class="btn btn-dark" title="" disabled>
						<i class="fas fa-trash"></i>
					</button>
                    </div>
                    ';
                }
                return $btn;
            })
            ->rawColumns(['fecha', 'inventory', 'action'])
            ->make(true);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::WhereIn('id', [13, 2, 3])->get();
        //$centros = Centrocosto::Where('status', 1)->get();
        //$centros = Store::whereNotIn('id', [1, 4, 5, 6, 7])
        $stores = Store::whereIn('id', [8, 10])
            ->orderBy('id', 'asc')
            ->get();
        $lotes = Lote::orderBy('id', 'asc')->get();
        return view("alistamiento.index", compact('category', 'stores', 'lotes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //  dd($id);
        $dataAlistamiento = DB::table('enlistments as ali')
            ->join('stores as s', 'ali.store_id', '=', 's.id')
            ->join('lotes as l', 'ali.lote_id', '=', 'l.id')
            ->join('lotes as lh', 'ali.lote_hijos_id', '=', 'lh.id')
            ->join('lote_products as lp', 'ali.product_id', '=', 'lp.product_id')
            ->join('products as p', 'ali.product_id', '=', 'p.id')
            ->join('meatcuts as m', 'p.meatcut_id', '=', 'm.id')
            ->join('inventarios as i', 'p.id', '=', 'i.product_id')
            ->select('ali.*', 'p.id as productopadreId', 'p.name as name', 'i.stock_ideal as stockPadre', 'i.cantidad_inicial',  'i.costo_unitario as costoPadre', 'p.meatcut_id as meatcut_id', 's.name as namebodega', 'l.codigo as codigolote', 'lh.codigo as codigolotehijo')
            ->where('ali.id', $id)
            ->get();

        /* 
        $cortes = DB::table('products as p')
            ->join('inventarios as i', 'p.id', '=', 'i.product_id')
            ->select('p.*', 'i.stock_ideal', 'i.cantidad_inicial', 'p.id as productopadreId')
            ->selectRaw('i.stock_ideal stockPadre')
            /*  ->selectRaw('i.inventario_inicial + i.compraLote + i.alistamiento +
            i.compensados + i.trasladoing - (i.venta + i.trasladosal) stockPadre') 
            ->where([
                ['p.level_product_id', 1],
                ['p.id', $dataAlistamiento[0]->product_id],
                ['p.status', 1],
                ['i.store_id', $dataAlistamiento[0]->store_id],
            ])->get(); */


        //  dd($dataAlistamiento);

        /*****************************************/

        $status = '';
        $fechaAlistamientoCierre = Carbon::parse($dataAlistamiento[0]->fecha_cierre);
        $date = Carbon::now();
        $currentDate = Carbon::parse($date->format('Y-m-d'));
        if ($currentDate->gt($fechaAlistamientoCierre)) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($currentDate->lt($fechaAlistamientoCierre)) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }
        /**************************************** */
        $statusInventory = "";
        if ($dataAlistamiento[0]->inventario == "added") {
            $statusInventory = "true";
        } else {
            $statusInventory = "false";
        }
        /**************************************** */
        //dd($tt = [$status, $statusInventory]);

        $display = "";
        if ($status == "false" || $statusInventory == "true") {
            $display = "display:none;";
        }

        $enlistments = $this->getalistamientodetail($id, $dataAlistamiento[0]->store_id);

        $arrayTotales = $this->sumTotales($id);

        return view('alistamiento.create', compact('dataAlistamiento', 'enlistments', 'arrayTotales', 'status', 'statusInventory', 'display'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function getproducts(Request $request)
    {
        $prod = Product::Where([
            ['meatcut_id', $request->categoriaId],
            ['status', 1],
            ['level_product_id', 2]
        ])->get();
        return response()->json(['products' => $prod]);
    }

    //  Log::info('producto:', ['producto' => $request->producto]);
    // Log::info('storeId:', ['storeId' => $request->storeId]);

    // Log::info('prod:', ['prod' => $prod]);

    public function savedetail(Request $request)
    {
        try {

            $rules = [
                'kgrequeridos' => 'required',
                'producto' => 'required',
            ];
            $messages = [
                'kgrequeridos.required' => 'Los kg requeridos son necesarios',
                'producto.required' => 'El producto es requerido',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $prod = DB::table('products as p')
                ->select('p.stock', 'p.fisico', 'p.cost')
                ->where([
                    ['p.id', $request->producto],
                    ['p.status', 1],

                ])->get();

            $formatCantidad = new metodosrogercodeController();

            // Obtener dato de producto seleccionado
            $product = Product::find($request->producto);
            if (!$product) {
                return response()->json([
                    'status' => 0,
                    'message' => 'El producto seleccionado no existe.',
                ], 404);
            }

            // Si price_fama es null o vacío, asignar 0
            $priceFama = $product->price_fama ?? 0;
            $totalVenta = 0;
            // $porcentajeVenta = 0;
            $costoTotal = 0;
            $costoKilo = 0;
            $utilidad = 0;
            $porcUtilidad = 0;

            $formatkgrequeridos = $formatCantidad->MoneyToNumber($request->kgrequeridos);
            $newStock = $prod[0]->stock + $formatkgrequeridos;

            $alistamiento = Alistamiento::where('id', $request->alistamientoId)->where('status', 1)->first(['total_costo', 'cantidad_padre_a_procesar']);
            if (!$alistamiento) {
                return response()->json(['status' => 0, 'message' => 'Alistamiento no encontrado.'], 404);
            }

            $CantidadPadreProcesar = $alistamiento->cantidad_padre_a_procesar;
            $TotalCosto = $alistamiento->total_costo;

            $details = new enlistment_details();

            $arrayTotales = $this->sumTotales($request->alistamientoId);
            $arraydetail = $this->getalistamientodetail($request->alistamientoId, $request->storeId);

            // Si kgTotalRequeridos es null, vacío o cero, inicializarlo en 0
            $kgTotalRequeridos = !empty($arrayTotales['kgTotalRequeridos']) ? $arrayTotales['kgTotalRequeridos'] : 0;
            $totalPrecioMinimo = !empty($arrayTotales['totalPrecioMinimo']) ? $arrayTotales['totalPrecioMinimo'] : 0;
            $totalCostoTotal = !empty($arrayTotales['totalCostoTotal']) ? $arrayTotales['totalCostoTotal'] : 0;
            $totalCostoKilo = !empty($arrayTotales['totalCostoKilo']) ? $arrayTotales['totalCostoKilo'] : 0;
            $totalUtilidad = !empty($arrayTotales['totalUtilidad']) ? $arrayTotales['totalUtilidad'] : 0;
            $totalPorcUtilidad = !empty($arrayTotales['totalPorcUtilidad']) ? $arrayTotales['totalPorcUtilidad'] : 0;

            // Acumular desde el primer registro
            $kgTotalRequeridos += $formatkgrequeridos;
            $totalPrecioMinimo += $priceFama;

            $totalCostoTotal += $costoTotal;
            $totalCostoKilo += $costoKilo;
            $totalUtilidad += $utilidad;
            $totalPorcUtilidad += $porcUtilidad;

            $details->enlistments_id = $request->alistamientoId;
            $details->products_id = $request->producto;
            $details->kgrequeridos = $formatkgrequeridos;
            $details->precio_minimo = $priceFama;
            $totalVenta = $formatkgrequeridos * $priceFama;

            $details->total_venta = $totalVenta;

            // Evitar división por cero en el cálculo del porcentaje de venta           
            $porcentajeVenta = ($totalVenta  / ($totalVenta ?: 1)) * 100;

            $details->porc_venta = $porcentajeVenta;

            $costoTotal = (($porcentajeVenta) / 100) * $TotalCosto;
            $details->costo_total = $costoTotal;

            $costoKilo = $costoTotal / $kgTotalRequeridos;

            $details->costo_kilo = $costoKilo;

            $utilidad = $totalVenta - $costoTotal;

            $details->utilidad = $utilidad;
            $porcUtilidad = ($totalVenta != 0 ? $utilidad / $totalVenta : 0) * 100;
            $details->porc_utilidad = $porcUtilidad;

            $details->cost_transformation = $prod[0]->cost * $formatkgrequeridos;
            $details->newstock = $newStock;
            $details->merma = $CantidadPadreProcesar - $kgTotalRequeridos;
            $details->save();

            $newStockPadre = $request->stockPadre - $kgTotalRequeridos;
            $alist = Alistamiento::firstWhere('id', $request->alistamientoId);
            $alist->nuevo_stock_padre = $newStockPadre;
            $alist->save();

            // Recalcular y actualizar valores en enlistment_details para todos los registros con el mismo enlistments_id
            $enlistments = enlistment_details::where('enlistments_id', $request->alistamientoId)->get();
            // Calcular la sumatoria del total_venta
            $totalVenta = $enlistments->sum('total_venta');

            foreach ($enlistments as $enlistment) {
                $enlistment->porc_venta = ($enlistment->total_venta / ($totalVenta ?: 1)) * 100;
                $enlistment->costo_total = ($enlistment->porc_venta / 100) * $alistamiento->total_costo;
                $enlistment->costo_kilo = $enlistment->costo_total / $enlistment->kgrequeridos;
                $enlistment->utilidad = $enlistment->total_venta - $enlistment->costo_total;
                $enlistment->porc_utilidad = ($enlistment->utilidad / $enlistment->total_venta) * 100;
                $enlistment->save();
            }

            $arrayTotales = $this->sumTotales($request->alistamientoId);
            $arraydetail = $this->getalistamientodetail($request->alistamientoId, $request->storeId);

            return response()->json([
                'status' => 1,
                'message' => "Agregado correctamente",
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function getalistamientodetail($alistamientoId, $storeId)
    {
        $detail = DB::table('enlistment_details as en')
            ->join('enlistments as e', 'e.id', '=', 'en.enlistments_id')
            ->join('products as pro', 'en.products_id', '=', 'pro.id')

            ->select('e.*', 'en.*', 'pro.name as nameprod', 'pro.code', 'pro.price_fama', 'en.costo_kilo as costo_kilo', 'pro.stock', 'pro.fisico', 'en.cost_transformation')
            ->selectRaw('pro.stock stockHijo')
            ->selectRaw('en.kgrequeridos * pro.price_fama totalVenta')
            // ->selectRaw('e.cantidad_padre_a_procesar - en.kgrequeridos')
            /*  ->selectRaw('ce.invinicial + ce.compraLote + ce.alistamiento +
            ce.compensados + ce.trasladoing - (ce.venta + ce.trasladosal) stockHijo') */
            ->where([
                ['e.store_id', $storeId],
                ['en.enlistments_id', $alistamientoId],
                ['en.status', 1]
            ])->get();

        return $detail;
    }

    public function sumTotales($id)
    {
        // Sumar valores de enlistment_details
        $kgTotalRequeridos = (float)enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('kgrequeridos');
        $totalPrecioMinimo = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('precio_minimo');
        $totalVentaFinal = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('total_venta');
        $totalPorcVenta = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('porc_venta');
        $totalCostoTotal = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('costo_total');
        $totalCostoKilo = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('costo_kilo');
        $totalUtilidad = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('utilidad');
        $totalPorcUtilidad = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('porc_utilidad');
        $totalCostTranf = enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('cost_transformation');
        $newTotalStock = (float)enlistment_details::Where([['enlistments_id', $id], ['status', 1]])->sum('newstock');

        // Obtener cantidad_padre_a_procesar desde enlistments
        $cantidadPadreAProcesar = Alistamiento::where([['id', $id], ['status', 1]])->value('cantidad_padre_a_procesar');

        // Calcular cantidad a procesar
        $merma = ($cantidadPadreAProcesar !== null) ? ($kgTotalRequeridos - $cantidadPadreAProcesar) : 0;
        $porcMerma = ($merma / $cantidadPadreAProcesar) * 100;
        // Retornar el array con los valores calculados
        return [
            'kgTotalRequeridos' => $kgTotalRequeridos,
            'totalPrecioMinimo' => $totalPrecioMinimo,
            'totalVentaFinal' => $totalVentaFinal,
            'totalPorcVenta' => $totalPorcVenta,
            'totalCostoTotal' => $totalCostoTotal,
            'totalCostoKilo' => $totalCostoKilo,
            'totalUtilidad' => $totalUtilidad,
            'totalPorcUtilidad' => $totalPorcUtilidad,
            'totalCostTranf' => $totalCostTranf,
            'newTotalStock' => $newTotalStock,
            'merma' => $merma,
            'porcMerma' => $porcMerma,
        ];
    }

    public function updatedetail(Request $request)
    {
        try {

            $prod = DB::table('products as p')
                //  ->join('centro_costo_products as ce', 'p.id', '=', 'ce.products_id')
                ->select('p.stock', 'p.fisico', 'p.cost')
                ->where([
                    ['p.id', $request->productoId],
                    //  ['ce.centrocosto_id', $request->storeId],
                    ['p.status', 1],

                ])->get();
            //$prod = Product::firstWhere('id', $request->productoId);
            //$newStock = $prod->stock + $request->newkgrequeridos;
            $newStock = $prod[0]->stock + $request->newkgrequeridos;

            $updatedetails = enlistment_details::firstWhere('id', $request->id);
            $updatedetails->kgrequeridos = $request->newkgrequeridos;
            $updatedetails->cost_transformation = $prod[0]->cost * $request->newkgrequeridos;
            $updatedetails->newstock = $newStock;
            $updatedetails->save();

            $arraydetail = $this->getalistamientodetail($request->alistamientoId, $request->storeId);
            $arrayTotales = $this->sumTotales($request->alistamientoId);

            $newStockPadre = $request->stockPadre - $arrayTotales['kgTotalRequeridos'];
            $alist = Alistamiento::firstWhere('id', $request->alistamientoId);
            $alist->nuevo_stock_padre = $newStockPadre;
            $alist->save();

            return response()->json([
                'status' => 1,
                'message' => 'Guardado correctamente',
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function editAlistamiento(Request $request)
    {
        $reg = Alistamiento::where('id', $request->id)->first();
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
    }

    public function getProductsCategoryPadre(Request $request)
    {
        $cortes = Meatcut::Where([
            ['category_id', $request->categoriaId],
            ['status', 1]
        ])->get();
        return response()->json(['products' => $cortes]);
    }

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
            $enlist = enlistment_details::where('id', $request->id)->first();
            $enlist->status = 0;
            $enlist->save();

            $arraydetail = $this->getalistamientodetail($request->alistamientoId, $request->storeId);
            $arrayTotales = $this->sumTotales($request->alistamientoId);

            $newStockPadre = $request->stockPadre - $arrayTotales['kgTotalRequeridos'];
            $alist = Alistamiento::firstWhere('id', $request->alistamientoId);
            $alist->nuevo_stock_padre = $newStockPadre;
            $alist->save();

            return response()->json([
                'status' => 1,
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function destroyAlistamiento(Request $request)
    {
        try {
            $alist = Alistamiento::where('id', $request->id)->first();
            $alist->status = 0;
            $alist->save();

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

    public function add_shopping(Request $request)
    {
        try {
            $id_user = Auth::user()->id;
            $currentDateTime = Carbon::now();

            DB::beginTransaction();
            $shopp = new shopping_enlistment();
            $shopp->users_id = $id_user;
            $shopp->enlistments_id = $request->alistamientoId;
            $shopp->store_id = $request->storeId;
            $shopp->productopadre_id = $request->productoPadre;
            $shopp->centrocosto_id = $request->storeId;
            $shopp->stock_actual = $request->stockPadre;
            $shopp->ultimo_conteo_fisico = $request->pesokg;
            $shopp->nuevo_stock = $request->newStockPadre;
            $shopp->fecha_shopping = $currentDateTime;
            $shopp->save();

            $regProd = $this->getalistamientodetail($request->alistamientoId, $request->storeId);
            $count = count($regProd);
            if ($count == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No tiene productos agregados'
                ]);
            }
            $stockalistpadre = 0;
            foreach ($regProd as $key) {
                $shoppDetails = new shopping_enlistment_details();
                $shoppDetails->shopping_enlistment_id = $shopp->id;
                $shoppDetails->products_id = $key->products_id;
                $shoppDetails->stock_actual = $key->stock;
                $shoppDetails->conteo_fisico = $key->fisico;
                $shoppDetails->kgrequeridos = abs($key->newstock);
                $shoppDetails->newstock = $key->newstock;
                $shoppDetails->save();

                $stockalistpadre = $stockalistpadre + $key->kgrequeridos;

                DB::update(
                    "
                     UPDATE centro_costo_products c 
                     SET c.alistamiento = c.alistamiento + :krequeridos        
                     WHERE c.products_id = :vproducts_id 
                     AND c.centrocosto_id = :vcentrocosto",
                    [
                        'vproducts_id' => $key->products_id,
                        'krequeridos' => $key->kgrequeridos,
                        'vcentrocosto' => $request->storeId
                    ]
                );
            }

            $productopadreId = $shopp->productopadre_id;
            $storeId = $shopp->centrocosto_id;

            DB::update(
                "
                     UPDATE centro_costo_products c 
                     SET c.alistamiento = c.alistamiento + :krequeridos        
                     WHERE c.products_id = :vproducts_id  
                     AND c.centrocosto_id = :vcentrocosto",
                [
                    'vproducts_id' => $productopadreId,
                    'krequeridos' => $stockalistpadre * -1,
                    'vcentrocosto' => $storeId
                ]
            );


            $invalist = Alistamiento::where('id', $request->alistamientoId)->first();
            $invalist->inventario = "added";
            $invalist->save();

            DB::commit();
            return response()->json([
                'status' => 1,
                'alistamiento' => $regProd,
                'count' => $count,
                'message' => 'Se guardo co exito'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }
}
