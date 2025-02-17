<?php

namespace App\Http\Controllers\transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\centros\Centrocosto;
use App\Models\Category;
use App\Models\transfer\Transfer;
use App\Models\transfer\transfer_details;
use App\Models\Centro_costo_product;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Products\Meatcut;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\Inventario;
use App\Models\Lote;
use App\Models\MovimientoInventario;
use App\Models\updating\updating_transfer;
use App\Models\updating\updating_transfer_details;


class transferController extends Controller
{
    public function index() // Alimenta el modal_create.blade para posterior store
    {
        $user = auth()->user(); // Obtener el usuario autenticado        
        // Obtener solo las bodegas asociadas al usuario en store_user
        $bodegaOrigen = Store::whereIn('id', function ($query) use ($user) {
            $query->select('store_id')
                ->from('store_user')
                ->where('user_id', $user->id);
        })
            ->whereNotIn('id', [40]) // Excluir bodegas específicas si aplica
            ->orderBy('name', 'asc')
            ->get();

        $stores = Store::where('status', 1)->get();

        /*  $stores = DB::table('inventarios as i')
            ->rightJoin('stores as s', 'i.store_id', '=', 's.id')
            ->select('s.id', 's.name')
            ->where('s.status', 1)
          //  ->where('i.stock_ideal', '>', 0) // Filtrar por stock_ideal mayor a 0
            ->distinct() // Asegurarse de que no haya bodegas duplicadas
            ->get(); */

        return view("transfer.index", compact('bodegaOrigen', 'stores'));
    }

    public function store(Request $request) // modal create primer paso del diligenciado y llenado de la tabla transfer.
    {
        try {

            $rules = [
                'transferId' => 'required',
                'bodegaOrigen' => 'required|different:bodegaDestino',
                'bodegaDestino' => 'required',
            ];
            $messages = [
                'transferId.required' => 'El transferId es requerido',
                'bodegaOrigen.required' => 'La bodega es requerida',
                'bodegaOrigen.different' => 'La bodega de origen debe ser diferente a la de destino',
                'bodegaDestino.required' => 'La bodega es requerida',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getReg = Transfer::firstWhere('id', $request->transferId);

            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format

                $id_user = Auth::user()->id;

                $tranf = new Transfer();
                $tranf->users_id = $id_user;
                $tranf->bodega_origen_id = $request->bodegaOrigen;
                $tranf->bodega_destino_id = $request->bodegaDestino;
                //   $tranf->products_id = 2;
                $tranf->fecha_tranfer = $currentDateFormat;
                $tranf->fecha_cierre = $dateNextMonday;
                $tranf->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    "registroId" => $tranf->id
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }

    public function show() // http://2puracarnes.test:8080/transfer  Datatable Traslado | listado
    {
        $data = DB::table('transfers as tra')
            ->join('stores as storeOrigen', 'tra.bodega_origen_id', '=', 'storeOrigen.id')
            ->join('stores as storeDestino', 'tra.bodega_destino_id', '=', 'storeDestino.id')
            ->select('tra.*', 'storeOrigen.name as namecentrocostoOrigen', 'storeDestino.name as namecentrocostoDestino')
            ->where('tra.status', 1)
            ->get();
        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->fecha_tranfer);
                $onlyDate = $date->toDateString();
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
					<a href="transfer/create/' . $data->id . '" class="btn btn-dark" title="tranfar" >
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
					<a href="transfer/create/' . $data->id . '" class="btn btn-dark" title="tranfar" >
						<i class="fas fa-directions"></i>
					</a>
					<button class="btn btn-dark" title="" onclick="showDataForm(' . $data->id . ')">
						<i class="fas fa-eye"></i>
					</button>
					<button class="btn btn-dark" title="Borrar Beneficio" onclick="downTransfer(' . $data->id . ');" ' . $status . '>
						<i class="fas fa-trash"></i>
					</button>
                    </div>
                    ';
                } else {
                    $btn = '
                    <div class="text-center">
					<a href="transfer/create/' . $data->id . '" class="btn btn-dark" title="tranfar" >
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
            ->rawColumns(['date', 'inventory', 'action'])
            ->make(true);
    }

    public function create($id) // http://2puracarnes.test:8080/transfer/create/2  llenado de la vista Translado | Categoria
    {
        // $loteId = 99; //$request->lote_id;
        //  $lotes = Lote::orderBy('id', 'desc')->get();
        // dd($id);
        $lotes = Lote::select('lotes.id', 'lotes.codigo')
            ->join('inventarios', 'lotes.id', '=', 'inventarios.lote_id')
            ->join('transfers', 'inventarios.store_id', '=', 'transfers.bodega_origen_id')
            ->where('transfers.id', $id)
            ->orderBy('lotes.codigo', 'asc')
            ->distinct()
            ->get();


        $dataTransfer = DB::table('transfers as tra')
            ->join('stores as storeOrigen', 'tra.bodega_origen_id', '=', 'storeOrigen.id')
            ->join('stores as storeDestino', 'tra.bodega_destino_id', '=', 'storeDestino.id')
            ->select('tra.*', 'storeOrigen.name as namecentrocostoOrigen', 'storeDestino.name as namecentrocostoDestino')
            ->where('tra.id', $id)
            ->get();
        // dd($dataTransfer);
        /**************************************** */
        $arrayProductsOrigin = DB::table('lote_products as lp')
            ->join('products as p', 'p.id', '=', 'lp.product_id')
            ->join('inventarios as i', 'i.product_id', '=', 'p.id')
            ->select('p.id', 'p.name', 'i.stock_ideal as stock_origen', 'i.stock_ideal as fisico_origen')
            ->where([
                [
                    'p.status',
                    '1'
                ],
                ['i.store_id', $dataTransfer[0]->bodega_origen_id],
            ])
            ->orderBy('p.category_id', 'asc')
            ->orderBy('p.name', 'asc')
            ->get();
        //dd($arrayProductsOrigin);
        /**************************************** */
        $arrayProductsDestination = DB::table('lote_products as lp')
            ->join('products as p', 'p.id', '=', 'lp.product_id')
            ->join('inventarios as i', 'i.product_id', '=', 'p.id')
            ->select('p.id', 'p.name', 'i.stock_ideal as stock_destino', 'i.stock_ideal as fisico_destino')
            ->where([
                [
                    'p.status',
                    '1'
                ],
                ['i.store_id', $dataTransfer[0]->bodega_destino_id],
            ])->get();
        // dd($arrayProductsDestination);
        /**************************************** */
        $status = '';
        $fechaTransferCierre = Carbon::parse($dataTransfer[0]->fecha_cierre);
        $date = Carbon::now();
        $currentDate = Carbon::parse($date->format('Y-m-d'));
        if ($currentDate->gt($fechaTransferCierre)) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($currentDate->lt($fechaTransferCierre)) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }
        /**************************************** */
        $statusInventory = "";
        if ($dataTransfer[0]->inventario == "added") {
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

        $transfers = $this->gettransferdetail($id, $dataTransfer[0]->bodega_origen_id);

        $arrayTotales = $this->sumTotales($id);

        return view('transfer.create', compact('lotes', 'dataTransfer', 'transfers', 'arrayProductsOrigin', 'arrayProductsDestination', 'arrayTotales', 'status', 'statusInventory', 'display'));
    }

    public function getProductsByLote(Request $request)
    {
        $loteId = $request->lote_id;
        $bodegaOrigenId = $request->bodega_origen_id;

        $productos = Product::select('products.id', 'products.name')
            ->join('inventarios as i', 'i.product_id', '=', 'products.id')
            ->where('i.lote_id', $loteId)
            ->where('i.store_id', $bodegaOrigenId) // Filtra por la bodega de origen
            ->where('products.status', '1')
            ->orderBy('products.name', 'asc')
            ->get();

        return response()->json($productos);
    }


    public function obtenerValoresProducto(Request $request)
    {
        $bodegaOrigenId = $request->input('bodegaOrigen');
        $loteId = $request->input('loteTraslado');
        $productId = $request->input('productId');

        // Validación de entrada
        if (!$bodegaOrigenId || !$loteId || !$productId) {
            return response()->json([
                'error' => 'Faltan datos requeridos'
            ], 400);
        }

        $producto = DB::table('inventarios as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->join('lote_products as lp', function ($join) use ($loteId) {
                $join->on('p.id', '=', 'lp.product_id')
                    ->where('i.lote_id', $loteId);
            })
            ->where('i.store_id', $bodegaOrigenId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'costo_unitario', 'costo_total')
            ->first();

        if ($producto) {
            return response()->json([
                'stockOrigen' => $producto->stock_ideal,
                'fisicoOrigen' => $producto->cantidad_inventario_inicial,
                'costoOrigen' => $producto->costo_unitario,
                'costoTotalOrigen' => $producto->costo_total,
            ]);
        } else {
            return response()->json([
                'error' => 'Producto no encontrado en el inventario'
            ], 404);
        }
    }

    public function obtenerValoresProductoDestino(Request $request)
    {
        $bodegaDestinoId = $request->input('bodegaDestino');
        $loteId = $request->input('loteTraslado');
        $productId = $request->input('productId');

        // Validación de entrada
        if (!$bodegaDestinoId || !$loteId || !$productId) {
            return response()->json([
                'error' => 'Faltan datos requeridos'
            ], 400);
        }

        $producto = DB::table('inventarios as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->join('lote_products as lp', function ($join) use ($loteId) {
                $join->on('p.id', '=', 'lp.product_id')
                    ->where('i.lote_id', $loteId);
            })
            ->where('i.store_id', $bodegaDestinoId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'costo_unitario', 'costo_total')
            ->first();

        if ($producto) {
            return response()->json([
                'stockDestino' => $producto->stock_ideal,
                'fisicoDestino' => $producto->cantidad_inventario_inicial,
                'costoDestino' => $producto->costo_unitario,
                'costoTotalDestino' => $producto->costo_total,
            ]);
        } else {
            return response()->json([
                'error' => 'Producto no encontrado en el inventario'
            ], 404);
        }
    }

    public function savedetailOriginal(Request $request)
    {
        $bodegaOrigenId = $request->input('bodegaOrigen'); // 16
        $bodegaDestinoId = $request->input('bodegaDestino');
        $loteId = $request->input('lote'); //18
        $productId = $request->input('producto'); // 297     

        try {
            $rules = [
                'producto' => 'required',
                'kgrequeridos' => [
                    'required',
                    'numeric',
                    'regex:/^\d+(\.\d{1,2})?$/',
                    'min:0.1',
                ],
            ];
            $messages = [
                'producto.required' => 'El producto es requerido',
                'kgrequeridos.required' => 'La cantidad a trasladar es requerida.',
                'kgrequeridos.numeric' => 'La cantidad a trasladar debe ser un número.',
                'kgrequeridos.min' => 'La cantidad a trasladar debe ser mayor a 0.1.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $prodOrigen = DB::table('inventarios as i')
                ->join('products as p', 'i.product_id', '=', 'p.id')
                ->where('i.store_id', $bodegaOrigenId)
                ->where('i.lote_id', $loteId)
                ->where('p.status', '1')
                ->where('p.id', $productId)
                ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
                ->get();

            $prodDestino = DB::table('inventarios as i')
                ->join('products as p', 'i.product_id', '=', 'p.id')
                ->where('i.store_id', $bodegaDestinoId)
                ->where('i.lote_id', $loteId)
                ->where('p.status', '1')
                ->where('p.id', $productId)
                ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
                ->get();

            $formatCantidad = new metodosrogercodeController();
            //$prod = Product::firstWhere('id', $request->producto);

            $formatkgrequeridos = $formatCantidad->MoneyToNumber($request->kgrequeridos);
            $newStockOrigen = $prodOrigen[0]->stock_ideal - $formatkgrequeridos;
            $newStockDestino = $prodDestino[0]->stock_ideal + $formatkgrequeridos;

            $details = new transfer_details();
            $details->transfers_id = $request->transferId;
            $details->lote_prod_traslado_id = $loteId;
            $details->kgrequeridos = $formatkgrequeridos;
            $details->actual_stock_origen = $request->stockOrigen;
            $details->nuevo_stock_origen = $newStockOrigen;
            $details->actual_stock_destino = $request->stockDestino;
            $details->nuevo_stock_destino = $newStockDestino;
            $details->save();

            $arraydetail = $this->gettransferdetail($request->transferId, $request->bodegaOrigen);
            $arrayTotales = $this->sumTotales($request->transferId);

            $newStockOrigen = $request->stockOrigen - $arrayTotales['kgTotalRequeridos'];
            $tranf = Transfer::firstWhere('id', $request->transferId);
            // $tranf->nuevo_stock_origen = $newStockOrigen;
            $tranf->save();

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

    public function savedetail(Request $request)
    {
        $bodegaOrigenId = $request->input('bodegaOrigen');
        $bodegaDestinoId = $request->input('bodegaDestino');
        $loteId = $request->input('lote');
        $productId = $request->input('producto');

        try {
            $rules = [
                'producto' => 'required',
                'kgrequeridos' => [
                    'required',
                    'numeric',
                    'regex:/^\d+(\.\d{1,2})?$/',
                    'min:0.1',
                    // Regla personalizada: no permitir que kgrequeridos sea mayor al stock_ideal en bodega origen.
                    function ($attribute, $value, $fail) use ($bodegaOrigenId, $loteId, $productId) {
                        $prodOrigen = DB::table('inventarios as i')
                            ->join('products as p', 'i.product_id', '=', 'p.id')
                            ->where('i.store_id', $bodegaOrigenId)
                            ->where('i.lote_id', $loteId)
                            ->where('p.status', '1')
                            ->where('p.id', $productId)
                            ->select('i.stock_ideal')
                            ->first();
                        if ($prodOrigen && $value > $prodOrigen->stock_ideal) {
                            $fail('La cantidad a trasladar no puede ser mayor al stock disponible en origen.');
                        }
                    },
                ],
            ];

            $messages = [
                'producto.required' => 'El producto es requerido',
                'kgrequeridos.required' => 'La cantidad a trasladar es requerida.',
                'kgrequeridos.numeric' => 'La cantidad a trasladar debe ser un número.',
                'kgrequeridos.min' => 'La cantidad a trasladar debe ser mayor a 0.1.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Obtener producto en bodega origen
            $prodOrigen = DB::table('inventarios as i')
                ->join('products as p', 'i.product_id', '=', 'p.id')
                ->where('i.store_id', $bodegaOrigenId)
                ->where('i.lote_id', $loteId)
                ->where('p.status', '1')
                ->where('p.id', $productId)
                ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
                ->first();

            // Obtener producto en bodega destino
            $prodDestino = DB::table('inventarios as i')
                ->join('products as p', 'i.product_id', '=', 'p.id')
                ->where('i.store_id', $bodegaDestinoId)
                ->where('i.lote_id', $loteId)
                ->where('p.status', '1')
                ->where('p.id', $productId)
                ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
                ->first();

            // Asignar valores por defecto si prodDestino es null
            $stockDestino = optional($prodDestino)->stock_ideal ?? 0;
            $cantidadInventarioDestino = optional($prodDestino)->cantidad_inventario_inicial ?? 0;

            // Instancia para formateo de números
            $formatCantidad = new metodosrogercodeController();
            $formatkgrequeridos = $formatCantidad->MoneyToNumber($request->kgrequeridos);

            // Calcular nuevos stocks
            $newStockOrigen = optional($prodOrigen)->stock_ideal - $formatkgrequeridos;
            $newStockDestino = $stockDestino + $formatkgrequeridos;

            // Calcular subtotal costo traslado
            $costoUnitarioOrigen = optional($prodOrigen)->costo_unitario ?? 0;
            $subTotalTraslado = $formatkgrequeridos * $costoUnitarioOrigen;


            // Guardar detalle de traslado
            $details = new transfer_details();
            $details->transfers_id = $request->transferId;
            $details->lote_prod_traslado_id = $loteId;
            $details->product_id = $productId;
            $details->kgrequeridos = $formatkgrequeridos;
            $details->actual_stock_origen = $request->stockOrigen;
            $details->nuevo_stock_origen = $newStockOrigen;
            $details->actual_stock_destino = $stockDestino;
            $details->nuevo_stock_destino = $newStockDestino;
            $details->costo_unitario_origen = $costoUnitarioOrigen;
            $details->subtotal_traslado = $subTotalTraslado;
            $details->save();

            // Obtener detalles y totales
            $arraydetail = $this->gettransferdetail($request->transferId, $request->bodegaOrigen);
            $arrayTotales = $this->sumTotales($request->transferId);

            // Actualizar nuevo stock en la transferencia
            $newStockOrigen = $request->stockOrigen - $arrayTotales['kgTotalRequeridos'];
            $tranf = Transfer::firstWhere('id', $request->transferId);
            $tranf->save();

            return response()->json([
                'status' => 1,
                'message' => "Agregado correctamente",
                'array' => $arraydetail,
                'arrayTotales' => $arrayTotales,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function gettransferdetailVersion3($id)
    {
        $detail = DB::table('transfer_details as td')
            ->join('inventarios as i', 'td.lote_prod_traslado_id', '=', 'i.lote_id')
            ->join('lotes as l', 'l.id', '=', 'i.lote_id')
            ->join('products as p', 'i.product_id', '=', 'p.id')

            ->select('l.codigo', 'td.*', 'p.id as products_id', 'p.name as nameprod', 'p.code')
            ->where([
                ['td.transfers_id', $id],

                ['td.status', '1'],
            ])->get();

        return $detail;
    }

    public function gettransferdetail($id)
    {
        return DB::table('transfer_details as td')
            ->join('lotes as l', 'td.lote_prod_traslado_id', '=', 'l.id')
            ->join('products as p', 'td.product_id', '=', 'p.id')
            ->select(
                'l.codigo',
                'td.id',
                'td.kgrequeridos',
                'td.actual_stock_origen',
                'td.nuevo_stock_origen',
                'td.actual_stock_destino',
                'td.nuevo_stock_destino',
                'td.costo_unitario_origen',
                'td.subtotal_traslado',
                'p.id as products_id',
                'p.name as nameprod',
            )
            ->where('td.transfers_id', $id)
            ->where('td.status', '1')
            ->get();
    }

    public function sumTotales($id)
    {
        $kgTotalRequeridos = (float)transfer_details::Where([['transfers_id', $id], ['status', 1]])->sum('kgrequeridos');
        $newTotalStock = (float)transfer_details::Where([['transfers_id', $id], ['status', 1]])->sum('nuevo_stock_origen');
        $newTotalStockDestino = (float)transfer_details::Where([['transfers_id', $id], ['status', 1]])->sum('nuevo_stock_destino');

        $totalTraslado = (float)transfer_details::Where([['transfers_id', $id], ['status', 1]])->sum('subtotal_traslado');

        $array = [
            'kgTotalRequeridos' => $kgTotalRequeridos,
            'newTotalStock' => $newTotalStock,
            'newTotalStockDestino' => $newTotalStockDestino,
            'totalTraslado' => $totalTraslado,
        ];

        return $array;
    }

    public function updatedetail(Request $request)
    {
        // Validar los datos de entrada
        $rules = [
            'id' => 'required|exists:transfer_details,id',
            'newkgrequeridos' => [
                'required',
                'numeric',
                'regex:/^\d+(\.\d{1,2})?$/',
                'min:0.1',
                // Regla personalizada: que la nueva cantidad no exceda el stock disponible
                function ($attribute, $value, $fail) use ($request) {
                    // Obtener el detalle actual
                    $detail = transfer_details::find($request->id);
                    if (!$detail) {
                        $fail('Detalle no encontrado.');
                        return;
                    }
                    // Datos necesarios: lote, producto y bodega origen
                    $loteId = $detail->lote_prod_traslado_id;
                    $productId = $detail->product_id;
                    $bodegaOrigenId = $request->bodegaOrigen;
                    // Consultar el inventario en la bodega origen
                    $inventario = DB::table('inventarios as i')
                        ->join('products as p', 'i.product_id', '=', 'p.id')
                        ->where('i.store_id', $bodegaOrigenId)
                        ->where('i.lote_id', $loteId)
                        ->where('p.status', '1')
                        ->where('p.id', $productId)
                        ->select('i.stock_ideal')
                        ->first();
                    if (!$inventario) {
                        $fail('No se encontró el inventario del producto en la bodega origen.');
                        return;
                    }
                    // Sumar las cantidades de otros detalles para este producto y lote en la transferencia
                    $sumOther = DB::table('transfer_details')
                        ->where('transfers_id', $request->transferId)
                        ->where('lote_prod_traslado_id', $loteId)
                        ->where('product_id', $productId)
                        ->where('id', '!=', $request->id)
                        ->sum('kgrequeridos');
                    // Cantidad disponible para actualizar este detalle
                    $available = $inventario->stock_ideal - $sumOther;
                    if ($value > $available) {
                        $fail('La cantidad a trasladar no puede ser mayor al stock disponible en origen.');
                    }
                },
            ],
        ];

        $messages = [
            'newkgrequeridos.required' => 'La cantidad a trasladar es requerida.',
            'newkgrequeridos.numeric'  => 'La cantidad a trasladar debe ser un número.',
            'newkgrequeridos.min'      => 'La cantidad a trasladar debe ser mayor a 0.1.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors()
            ], 422);
        }

        // Recuperar el detalle a actualizar
        $detail = transfer_details::find($request->id);
        if (!$detail) {
            return response()->json([
                'status' => 0,
                'error' => 'Detalle no encontrado.'
            ], 404);
        }

        // Datos de referencia para recalcular
        $loteId = $detail->lote_prod_traslado_id;
        $productId = $detail->product_id;
        $bodegaOrigenId = $request->bodegaOrigen;
        $bodegaDestinoId = $request->bodegaDestino;

        // Consultar el inventario en la bodega de origen para obtener stock y costo unitario
        $inventarioOrigen = DB::table('inventarios as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->where('i.store_id', $bodegaOrigenId)
            ->where('i.lote_id', $loteId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal', 'i.costo_unitario')
            ->first();
        if (!$inventarioOrigen) {
            return response()->json([
                'status' => 0,
                'error' => 'No se encontró el inventario en la bodega origen.'
            ], 404);
        }

        // Calcular la nueva cantidad, nuevos stocks y subtotal
        $newKgs = $request->newkgrequeridos;

        // Sumar las cantidades de otros detalles para este producto (excluyendo el actual)
        $sumOther = DB::table('transfer_details')
            ->where('transfers_id', $request->transferId)
            ->where('lote_prod_traslado_id', $loteId)
            ->where('product_id', $productId)
            ->where('id', '!=', $request->id)
            ->sum('kgrequeridos');

        // Nuevo stock en origen: se resta la suma de otros detalles más la nueva cantidad
        $newStockOrigen = $inventarioOrigen->stock_ideal - ($sumOther + $newKgs);

        // Consultar el inventario en la bodega destino (si existe)
        $inventarioDestino = DB::table('inventarios as i')
            ->join('products as p', 'i.product_id', '=', 'p.id')
            ->where('i.store_id', $bodegaDestinoId)
            ->where('i.lote_id', $loteId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal')
            ->first();
        $stockDestino = optional($inventarioDestino)->stock_ideal ?? 0;
        // Nuevo stock en destino se calcula sumando la nueva cantidad (suponiendo que cada detalle se suma al stock destino)
        $newStockDestino = $stockDestino + $newKgs;

        // Calcular el subtotal del traslado
        $subtotal = $newKgs * $inventarioOrigen->costo_unitario;

        // Actualizar el detalle
        $detail->kgrequeridos = $newKgs;
        $detail->nuevo_stock_origen = $newStockOrigen;
        $detail->nuevo_stock_destino = $newStockDestino;
        $detail->subtotal_traslado = $subtotal;
        $detail->save();

        // Actualizar la vista: se vuelven a calcular los detalles y totales
        $arraydetail = $this->gettransferdetail($request->transferId, $bodegaOrigenId);
        $arrayTotales = $this->sumTotales($request->transferId);

        // (Opcional) Actualizar el registro de la transferencia si es necesario
        $transfer = Transfer::find($request->transferId);
        $transfer->save();

        return response()->json([
            'status' => 1,
            'message' => 'Detalle actualizado correctamente',
            'array' => $arraydetail,
            'arrayTotales' => $arrayTotales,
        ]);
    }


    public function editTransfer(Request $request)
    {
        $reg = Transfer::where('id', $request->id)->first();
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
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
            $enlist = transfer_details::where('id', $request->id)->first();
            $enlist->status = 0;
            $enlist->save();

            $arraydetail = $this->gettransferdetail($request->transferId, $request->bodegaOrigen);
            $arrayTotales = $this->sumTotales($request->transferId);

            $newStockOrigen = $request->stockOrigen - $arrayTotales['kgTotalRequeridos'];
            $tranf = Transfer::firstWhere('id', $request->transferId);
            //    $tranf->nuevo_stock_origen = $newStockOrigen;
            $tranf->save();

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

    public function destroyTransfer(Request $request)
    {
        try {
            $tranf = Transfer::where('id', $request->id)->first();
            $tranf->status = 0;
            $tranf->save();

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
        $validator = Validator::make($request->all(), [
            'transferId'    => 'required|exists:transfers,id',
            'stockOrigen'   => 'required|numeric',
            'bodegaOrigen'  => 'required|exists:stores,id',
            'bodegaDestino' => 'required|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Datos inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $transferId    = $request->input('transferId');
            $bodegaOrigen  = $request->input('bodegaOrigen');
            $bodegaDestino = $request->input('bodegaDestino');

            // Obtener la transferencia activa (status = '1')
            $transfer = Transfer::where('id', $transferId)
                ->where('status', '1')
                ->first();
            if (!$transfer) {
                throw new \Exception("Transferencia no encontrada o no está activa.");
            }

            // Obtener los detalles de la transferencia activos (status = '1')
            $transferDetails = transfer_details::where('transfers_id', $transferId)
                ->where('status', '1')
                ->get();

            foreach ($transferDetails as $detail) {
                $loteId    = $detail->lote_prod_traslado_id;
                $productId = $detail->product_id;
                $quantity  = $detail->kgrequeridos; // Cantidad a trasladar

                /*** PROCESO EN BODEGA ORIGEN ***/
                // Obtener el inventario de origen
                $inventarioOrigen = Inventario::where('store_id', $bodegaOrigen)
                    ->where('lote_id', $loteId)
                    ->where('product_id', $productId)
                    ->first();

                if (!$inventarioOrigen) {
                    throw new \Exception("No se encontró inventario en la bodega de origen para el producto ID {$productId} y lote ID {$loteId}.");
                }

                // Actualizar stock ideal en origen (se resta la cantidad trasladada)
                $inventarioOrigen->stock_ideal -= $quantity;
                // Acumular la cantidad trasladada en salida
                $inventarioOrigen->cantidad_traslado = ($inventarioOrigen->cantidad_traslado ?? 0) + $quantity;
                $inventarioOrigen->save();

                // Registrar movimiento de traslado salida
                MovimientoInventario::create([
                    'tipo'             => 'traslado_salida',
                    'transfer_id'      => $transferId,
                    'store_origen_id' => $bodegaOrigen, // Bodega que sufre la salida
                    'lote_id'          => $loteId,   
                    'product_id'       => $productId,
                    'cantidad'         => $quantity,    
                ]);

                /*** PROCESO EN BODEGA DESTINO ***/
                // Obtener (o crear) el inventario en destino
                $inventarioDestino = Inventario::where('store_id', $bodegaDestino)
                    ->where('lote_id', $loteId)
                    ->where('product_id', $productId)
                    ->first();

                if ($inventarioDestino) {
                    // Sumar la cantidad trasladada
                    $inventarioDestino->stock_ideal += $quantity;
                    // Acumular la cantidad trasladada en ingreso
                    $inventarioDestino->cantidad_traslado += $quantity;
                    $inventarioDestino->save();
                } else {
                    // Crear un nuevo registro de inventario para la bodega destino
                    $inventarioDestino = Inventario::create([
                        'store_id'                  => $bodegaDestino,
                        'lote_id'                   => $loteId,
                        'product_id'                => $productId,
                        'stock_ideal'               => $quantity,
                        'cantidad_traslado'         => $quantity,
                        // Otros campos (como cantidad_inventario_inicial, costo, etc.) se pueden inicializar según convenga
                    ]);
                }

                // Registrar movimiento de traslado ingreso (almacena transferId)
                MovimientoInventario::create([
                    'tipo'             => 'traslado_ingreso',
                    'transfer_id'      => $transferId,
                    'store_destino_id' => $bodegaDestino,
                    'lote_id'          => $loteId,                                        
                    'product_id'       => $productId,
                    'cantidad'         => $quantity,                  
                ]);
            }

            // (Opcional) Actualizar el estado de la transferencia (por ejemplo, marcarla como completada)
            $transfer->inventario = 'added';
            $transfer->save();

            DB::commit();

            return response()->json([
                'status'  => 1,
                'message' => 'Inventario actualizado correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Error al actualizar el inventario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    public function add_shoppingOriginal(Request $request)
    {
        try {
            $id_user = Auth::user()->id;
            $currentDateTime = Carbon::now();

            DB::beginTransaction();
            $shopp = new updating_transfer();
            $shopp->users_id = $id_user;
            $shopp->transfers_id = $request->transferId;
            //  $shopp->productopadre_id = $request->productoPadre;
            $shopp->centrocostoOrigen_id = $request->bodegaOrigen;
            $shopp->centrocostoDestino_id = $request->bodegaDestino;
            $shopp->stock_actual = $request->stockOrigen;
            $shopp->ultimo_conteo_tangible = $request->pesokg;
            $shopp->nuevo_stock = $request->newStockOrigen;
            $shopp->fecha_actualizacion = $currentDateTime;
            $shopp->save();

            $regProd = $this->gettransferdetail($request->transferId, $request->bodegaOrigen);
            $count = count($regProd);
            if ($count == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No tiene productos agregados'
                ]);
            }
            foreach ($regProd as $key) {
                $shoppDetails = new updating_transfer_details();
                $shoppDetails->updating_transfer_id = $shopp->id;
                $shoppDetails->products_id = $key->products_id;
                $shoppDetails->stock_actual = $key->stock;
                $shoppDetails->conteo_tangible = $key->fisico;
                $shoppDetails->kgrequeridos = $key->kgrequeridos;
                $shoppDetails->nuevo_stock_origen = $key->nuevo_stock_origen;
                $shoppDetails->nuevo_stock_destino = $key->nuevo_stock_destino;
                $shoppDetails->save();

                DB::table('centro_costo_products')
                    ->where('centro_costo_products.products_id', $key->products_id)
                    ->where('centro_costo_products.centrocosto_id', $request->bodegaDestino)
                    ->join('updating_transfer', 'centro_costo_products.centrocosto_id', '=', 'updating_transfer.centrocostoDestino_id')
                    ->update([
                        'centro_costo_products.trasladoing' => DB::raw('centro_costo_products.trasladoing + ' . $key->kgrequeridos),
                        'centro_costo_products.stock' => DB::raw('centro_costo_products.invinicial + centro_costo_products.compralote + centro_costo_products.alistamiento
                         + centro_costo_products.compensados + centro_costo_products.trasladoing - (centro_costo_products.venta + centro_costo_products.trasladosal)')
                    ]);

                DB::table('centro_costo_products')
                    ->where('centro_costo_products.products_id', $key->products_id)
                    ->where('centro_costo_products.centrocosto_id', $request->bodegaOrigen)
                    ->join('updating_transfer', 'centro_costo_products.centrocosto_id', '=', 'updating_transfer.centrocostoOrigen_id')
                    ->update([
                        'centro_costo_products.trasladosal' => DB::raw('centro_costo_products.trasladosal + ' . $key->kgrequeridos),
                        'centro_costo_products.stock' => DB::raw('centro_costo_products.invinicial + centro_costo_products.compralote + centro_costo_products.alistamiento
                        + centro_costo_products.compensados + centro_costo_products.trasladoing - (centro_costo_products.venta + centro_costo_products.trasladosal)')
                    ]);
            }

            $invtranf = Transfer::where('id', $request->transferId)->first();
            $invtranf->inventario = "added";
            $invtranf->save();

            DB::commit();
            return response()->json([
                'status' => 1,
                'transfer' => $regProd,
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
