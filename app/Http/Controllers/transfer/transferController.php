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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

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

        // $stores = Store::where('status', 1)->get();

        $stores = DB::table('inventarios as i')
            ->rightJoin('stores as s', 'i.store_id', '=', 's.id')
            ->select('s.id', 's.name')
            ->where('s.status', 1)
            //  ->where('i.stock_ideal', '>', 0) // Filtrar por stock_ideal mayor a 0
            ->distinct() // Asegurarse de que no haya bodegas duplicadas
            ->get();

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

    // http://2puracarnes.test:8080/transfer  Datatable Traslado | listado

    public function show()
    {
        $user = auth()->user();

        // 1) IDs de tiendas que el usuario tiene asignadas
        $associatedStoreIds = Store::whereIn('id', function ($query) use ($user) {
            $query->select('store_id')
                ->from('store_user')
                ->where('user_id', $user->id);
        })
            ->whereNotIn('id', [40]) // si quieres seguir excluyendo la 40
            ->pluck('id');           // <-- pluck en lugar de ->get()

        // 2) Consulta de transfers filtrada
        $data = DB::table('transfers as tra')
            ->join('stores as storeOrigen',    'tra.bodega_origen_id',     '=', 'storeOrigen.id')
            ->join('stores as storeDestino',   'tra.bodega_destino_id',    '=', 'storeDestino.id')
            ->select(
                'tra.*',
                'storeOrigen.name as namecentrocostoOrigen',
                'storeDestino.name as namecentrocostoDestino'
            )
            ->where('tra.status', 1)
            // 3) Solo transfers donde ambas bodegas estén entre las asociadas
            ->whereIn('tra.bodega_origen_id',  $associatedStoreIds)
            ->whereIn('tra.bodega_destino_id', $associatedStoreIds)
            ->orderBy('tra.fecha_tranfer', 'desc')
            ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('date', function ($transfer) {
                return Carbon::parse($transfer->fecha_tranfer)->toDateString();
            })
            ->addColumn('inventory', function ($transfer) {
                return $transfer->inventario === 'pending'
                    ? '<span class="badge bg-warning">Pendiente</span>'
                    : '<span class="badge bg-success">Agregado</span>';
            })
            ->addColumn('action', function ($transfer) {
                $hoy = Carbon::today();
                $btns = [];

                // Botón PDF siempre
                $btns[] = '<a href="transfer/showTransfer/' . $transfer->id . '" class="btn btn-danger" title="Pdf traslado" target="_blank">
                           <i class="far fa-file-pdf"></i>
                       </a>';

                // Si pasó la fecha_cierre, inhabilito crear y borrar
                if ($hoy->gt(Carbon::parse($transfer->fecha_cierre))) {
                    $btns[] = '<button class="btn btn-dark" disabled><i class="fas fa-directions"></i></button>';
                    $btns[] = '<button class="btn btn-dark" disabled><i class="fas fa-trash"></i></button>';
                }
                // Si aún no pasa la fecha_cierre
                else {
                    // Botón transferir
                    $btns[] = '<a href="transfer/create/' . $transfer->id . '" class="btn btn-dark" title="Transferir">
                               <i class="fas fa-directions"></i>
                           </a>';
                    // Botón borrar (solo si inventario != added)
                    $disabled = $transfer->inventario === 'added' ? 'disabled' : '';
                    $btns[] = '<button onclick="downTransfer(' . $transfer->id . ')" class="btn btn-dark" '
                        . $disabled . '><i class="fas fa-trash"></i></button>';
                }

                return '<div class="text-center">' . implode(' ', $btns) . '</div>';
            })
            ->rawColumns(['date', 'inventory', 'action'])
            ->make(true);
    }

    public function search(Request $request)
    {

        //$bodegaOrigenId = 16;

        $bodegaOrigenId = $request->input('bodegaOrigen');
        if (!$bodegaOrigenId) {
            return response()->json([], 422);
        }

        $query = $request->input('q');

        // Consulta de productos. Se busca por barcode o por nombre o por el código del lote (mediante la relación "lotes")
        $productsQuery = Product::query();

        if ($query) {
            if (preg_match('/^\d{13}$/', $query)) {
                // Si es un EAN-13, buscar por barcode
                $productsQuery->where('barcode', $query);
            } else {
                // Si no, buscar por nombre, código de producto ó código de lote
                $productsQuery->where(function ($q) use ($query) {
                    $q->where('name',   'LIKE', "%{$query}%")
                        ->orWhere('code', 'LIKE', "%{$query}%")
                        ->orWhereHas('lotes', function ($q2) use ($query) {
                            $q2->where('codigo', 'LIKE', "%{$query}%");
                        });
                });
            }
        }

        // Se filtran los productos que tengan inventarios en las bodegas seleccionadas con stock_ideal > 0
        $productsQuery->whereHas('inventarios', function ($q) use ($bodegaOrigenId) {
            $q->where('store_id', $bodegaOrigenId)
                ->where('stock_ideal', '>', 0);
        });

        // Obtener los IDs de productos válidos
        $products = $productsQuery->get();
        // Obtener los IDs de productos válidos
        $productIds = $productsQuery->pluck('id')->toArray();

        // Se obtienen todos los inventarios que cumplan la condición, cargando además la relación "lote" y "store"
        $inventarios = Inventario::with(['store', 'lote'])
            ->where('store_id',    $bodegaOrigenId)
            ->where('stock_ideal', '>', 0)
            ->whereIn('product_id', $productIds)
            ->whereHas('lote', function ($q) {
                $q->where('fecha_vencimiento', '>=', now());
            })
            // Unir con la tabla de lotes para poder ordenar por su fecha
            ->join('lotes', 'inventarios.lote_id', '=', 'lotes.id')
            ->orderBy('lotes.fecha_vencimiento', 'asc')
            ->orderBy('stock_ideal', 'desc')
            ->select('inventarios.*')
            ->get();

        $results = [];

        foreach ($products as $prod) {
            // Filtrar todos los inventarios que correspondan al producto actual
            $inventariosProducto = $inventarios->where('product_id', $prod->id);

            foreach ($inventariosProducto as $inventario) {
                // Validar la fecha de vencimiento del lote
                if ($inventario->lote && \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->gte(\Carbon\Carbon::now())) {
                    $text = "Lt: " . ($inventario->lote ? $inventario->lote->codigo : 'Sin código')
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
                        'barcode'        => $prod->barcode,
                        // Se conserva el id del producto en otra propiedad para otros usos
                        'product_id'     => $prod->id,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    public function create($id) // http://2puracarnes.test:8080/transfer/create/2  llenado de la vista Translado | Categoria
    {
        $dataTransfer = DB::table('transfers as tra')
            ->join('stores as storeOrigen', 'tra.bodega_origen_id', '=', 'storeOrigen.id')
            ->join('stores as storeDestino', 'tra.bodega_destino_id', '=', 'storeDestino.id')
            ->select('tra.*', 'storeOrigen.name as namecentrocostoOrigen', 'storeDestino.name as namecentrocostoDestino')
            ->where('tra.id', $id)
            ->get();

        // Validación para evitar error si $dataTransfer está vacío o indefinido
        if ($dataTransfer->isEmpty()) {
            // Puedes redireccionar o mostrar un mensaje de error
            return redirect()->back()->with('error', 'No se encontró la transferencia');
        }

        // Ahora ya podemos usar $dataTransfer[0] con la seguridad de que existe.
        //  $storeOrigenId = $dataTransfer[0]->bodega_origen_id;
        //  $storeDestinoId = $dataTransfer[0]->bodega_destino_id;
        $storeOrigenId = [0];
        $storeDestinoId = [0];
        // Consulta para productos de origen
        $arrayProductsOrigin = DB::table('products as p')
            ->join('inventarios as i', 'i.product_id', '=', 'p.id')
            ->select('p.id', 'p.name', 'i.stock_ideal as stock_origen', 'i.stock_ideal as fisico_origen')
            ->where([
                ['p.status', '1'],
                ['i.store_id', $storeOrigenId],
            ])
            ->orderBy('p.category_id', 'asc')
            ->orderBy('p.name', 'asc')
            ->get();

        // Consulta para productos de destino
        $arrayProductsDestination = DB::table('products as p')
            ->join('inventarios as i', 'i.product_id', '=', 'p.id')
            ->select('p.id', 'p.name', 'i.stock_ideal as stock_destino', 'i.stock_ideal as fisico_destino')
            ->where([
                ['p.status', '1'],
                ['i.store_id', $storeDestinoId],
            ])
            ->get();

        // Continuas con el resto de tu lógica, por ejemplo:
        $status = '';
        $fechaTransferCierre = Carbon::parse($dataTransfer[0]->fecha_cierre);
        $currentDate = Carbon::now()->startOfDay();

        if ($currentDate->gt($fechaTransferCierre)) {
            $status = 'false';
        } elseif ($currentDate->lt($fechaTransferCierre)) {
            $status = 'true';
        } else {
            $status = 'false';
        }

        $statusInventory = $dataTransfer[0]->inventario == "added" ? "true" : "false";

        $display = ($status == "false" || $statusInventory == "true") ? "display:none;" : "";

        $transfers = $this->gettransferdetail($id, $storeOrigenId);
        $arrayTotales = $this->sumTotales($id);

        $storeIds = \DB::table('store_user')
            ->where('user_id', auth()->id())
            ->pluck('store_id')
            ->toArray();

        // Se obtienen los productos que tengan inventarios en las bodegas seleccionadas con stock_ideal > 0
        $productsQuery = Product::query();
        $productsQuery->whereHas('inventarios', function ($q) use ($storeOrigenId) {
            $q->where('store_id', $storeOrigenId)
                ->where('stock_ideal', '>', 0);
        });
        $products = $productsQuery->get();

        // Se obtienen todos los inventarios que cumplan la condición, cargando las relaciones 'store' y 'lote'
        $inventarios = Inventario::with('store', 'lote')
            ->where('store_id', $storeOrigenId)
            ->where('stock_ideal', '>', 0)
            ->get();

        $results = [];

        foreach ($products as $prod) {
            // Filtrar todos los inventarios que correspondan al producto actual
            $inventariosProducto = $inventarios->where('product_id', $prod->id);

            foreach ($inventariosProducto as $inventario) {
                // Validar la fecha de vencimiento del lote
                if ($inventario->lote && \Carbon\Carbon::parse($inventario->lote->fecha_vencimiento)->gte(\Carbon\Carbon::now())) {
                    $text = "Lt: " . ($inventario->lote ? $inventario->lote->codigo : 'Sin código')
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
                        'barcode'        => $prod->barcode,
                        // Se conserva el id del producto en otra propiedad para otros usos
                        'product_id'     => $prod->id,
                    ];
                }
            }
        }

        return view('transfer.create', compact(
            'dataTransfer',
            'transfers',
            'arrayProductsOrigin',
            'arrayProductsDestination',
            'arrayTotales',
            'status',
            'statusInventory',
            'display',
            'results'
        ));
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
            ->where('i.store_id', $bodegaOrigenId)
            ->where('i.lote_id', $loteId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
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
            ->where('i.store_id', $bodegaDestinoId)
            ->where('i.lote_id', $loteId)
            ->where('p.status', '1')
            ->where('p.id', $productId)
            ->select('i.stock_ideal', 'i.cantidad_inventario_inicial', 'i.costo_unitario', 'i.costo_total')
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
        $loteId = $request->input('loteTraslado');
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
            $formatkgrequeridos = $request->kgrequeridos;

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
        // 1) Validaciones previas
        $validator = Validator::make($request->all(), [
            'transferId'    => 'required|exists:transfers,id',
            'stockOrigen'   => 'required|numeric',
            'bodegaOrigen'  => 'required|exists:stores,id',
            'bodegaDestino' => [
                'required',
                'exists:stores,id',
                function ($attr, $value, $fail) {
                    if (! DB::table('store_user')
                        ->where('user_id', auth()->id())
                        ->where('store_id', $value)
                        ->exists()) {
                        $fail('Usuario no autorizado para operar en esta bodega.');
                    }
                },
            ],
            'loteTraslado'  => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => 'Datos inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // arreglo para guardar errores en llamadas a Traza
        $trazaErrors = [];

        DB::beginTransaction();
        try {
            // 2) Obtengo la transferencia válida
            $transfer = Transfer::where('id', $request->transferId)
                ->where('status', '1')
                ->firstOrFail();

            // 3) Traigo los detalles activos
            $details = transfer_details::where('transfers_id', $transfer->id)
                ->where('status', '1')
                ->get();

            // preparar cliente HTTP (si no existe TRAZA_URL fallará)
            $trazaUrl = config('services.traza.url', env('TRAZA_API_URL'));
            $trazaKey = config('services.traza.key', env('TRAZA_API_KEY'));
            $http = new Client(['timeout' => 10]);

            foreach ($details as $detail) {
                // 4) Me aseguro de tener loteId
                $loteId = $detail->lote_prod_traslado_id
                    ?? $request->input('loteTraslado', null);

                if (! $loteId) {
                    throw new \Exception(
                        "Detalle ID {$detail->id}: lote de traslado no definido."
                    );
                }

                $productId = $detail->product_id;
                $quantity  = $detail->kgrequeridos;

                // 5) Origen
                $invOrigen = Inventario::where('store_id', $request->bodegaOrigen)
                    ->where('lote_id', $loteId)
                    ->where('product_id', $productId)
                    ->first();

                if (! $invOrigen) {
                    throw new \Exception(
                        "No se encontró inventario en origen para producto ID {$productId} y lote ID {$loteId}."
                    );
                }
                $invOrigen->stock_ideal      -= $quantity;
                $invOrigen->cantidad_traslado = ($invOrigen->cantidad_traslado ?? 0) + $quantity;
                $invOrigen->save();

                MovimientoInventario::create([
                    'tipo'            => 'traslado_salida',
                    'transfer_id'     => $transfer->id,
                    'store_origen_id' => $request->bodegaOrigen,
                    'lote_id'         => $loteId,
                    'product_id'      => $productId,
                    'cantidad'        => $quantity,
                ]);

                // 6) Destino
                $invDestino = Inventario::firstOrNew([
                    'store_id'   => $request->bodegaDestino,
                    'lote_id'    => $loteId,
                    'product_id' => $productId,
                ]);

                $invDestino->stock_ideal      = ($invDestino->exists
                    ? $invDestino->stock_ideal + $quantity
                    : $quantity);
                $invDestino->cantidad_traslado = ($invDestino->cantidad_traslado ?? 0) + $quantity;
                $invDestino->save();

                MovimientoInventario::create([
                    'tipo'             => 'traslado_ingreso',
                    'transfer_id'      => $transfer->id,
                    'store_destino_id' => $request->bodegaDestino,
                    'lote_id'          => $loteId,
                    'product_id'       => $productId,
                    'cantidad'         => $quantity,
                ]);

                // -------------- ENVÍO A TRAZA (si destino es 34) ---------------
                if ((int)$request->bodegaDestino === 34 && ! empty($trazaUrl) && ! empty($trazaKey)) {
                    try {
                        // obtenemos product y lote igual que antes
                        $product = Product::with(['brand', 'category', 'unitOfMeasure'])->find($productId);
                        $lote = class_exists(\App\Models\Lote::class)
                            ? Lote::find($loteId)
                            : DB::table('lotes')->where('id', $loteId)->first();

                        // Normalizamos valores y los casteamos a string (evita error 422 que pide "cadena")
                        $insumoIdStr = (string) ($product->erp_id ?? $product->id ?? $productId);
                        $nombreInsumo = trim((string) ($product->name ?? $product->nombre ?? "Producto {$productId}"));
                        $loteCode = trim((string) ($lote->code ?? $lote->codigo ?? "Lote_{$loteId}"));
                        $saldoActual = number_format($invDestino->stock_ideal ?? $quantity, 2, '.', '');
                        $unidadMedida = trim((string) 'KG');
                        $precioUnitario = number_format($product->cost ?? $product->precio ?? 0, 2, '.', '');
                        $nombreProveedor = trim((string) ($product->brand->name ?? $product->brand_name ?? ''));
                        $tipoInsumo = 2; // 1: Insumo, 2: Producto Terminado
                        $marca = trim((string) ($product->brand->name ?? $product->brand_name ?? ''));
                        $fechaIngreso = isset($lote->fecha_ingreso) ? (string)$lote->fecha_ingreso : (string)($lote->fecha ?? null);
                        $fechaVenc = isset($lote->fecha_vencimiento) ? (string)$lote->fecha_vencimiento : (string)($lote->fecha ?? null);
                        $idCategoria = (string) ($product->category->id ?? $product->category_id ?? '');
                        $nombreCategoria = trim((string) ($product->category->name ?? $product->category_name ?? ''));

                        // Armamos payload usando strings + varias variantes de nombre (por compatibilidad)
                        $payload = [
                            // variantes del id/insumo por si la API valida por nombre diferente
                            'insumo_erp_id'   => $insumoIdStr,
                            'insumo'          => $insumoIdStr,
                            'Insumo'          => $insumoIdStr,

                            // nombre
                            'nombre_insumo'   => $nombreInsumo,
                            'nombre'          => $nombreInsumo,

                            // resto de campos (todos como string)
                            'lote_insumo'     => $loteCode,
                            'saldo_actual'    => (string)$saldoActual,
                            'unidad_medida'   => $unidadMedida,
                            'precio_unitario' => (string)$precioUnitario,
                            'nombre_proveedor' => $nombreProveedor,
                            'tipo_insumo'     => $tipoInsumo,
                            'fecha_vencimiento' => $fechaVenc,
                            'id_categoria'    => $idCategoria,
                            'nombre_categoria' => $nombreCategoria,
                        ];

                        // Si la API requiere application/json (POST), enviamos JSON
                        $response = $http->post($trazaUrl, [
                            'headers' => [
                                'X-API-KEY' => $trazaKey,
                                'Accept'    => 'application/json',
                                'Content-Type' => 'application/json',
                            ],
                            'json' => $payload,
                            'timeout' => 15,
                        ]);

                        // OK: guardamos info si queremos
                        Log::info('Traza sync OK', ['product' => $productId, 'lote' => $loteId, 'status' => $response->getStatusCode()]);
                    } catch (ClientException $e) {
                        // 4xx: normalmente 422 con cuerpo JSON explicando errores de validación
                        $resp = $e->getResponse();
                        $status = $resp ? $resp->getStatusCode() : null;
                        $body = $resp ? (string)$resp->getBody() : $e->getMessage();

                        // Intentamos decodificar JSON de respuesta para obtener errores concretos
                        $decoded = null;
                        try {
                            $decoded = json_decode($body, true);
                        } catch (\Throwable $_) {
                            $decoded = null;
                        }

                        $trazaErrors[] = [
                            'product_id' => $productId,
                            'lote_id'    => $loteId,
                            'http_status' => $status,
                            'response_raw' => $body,
                            'response_json' => $decoded,
                        ];

                        Log::error('Error enviando a Traza (ClientException)', [
                            'product_id' => $productId,
                            'lote_id' => $loteId,
                            'status' => $status,
                            'response' => $decoded ?? $body,
                        ]);
                    } catch (RequestException $e) {
                        // otros errores de request (timeout, DNS, etc.)
                        $resp = $e->getResponse();
                        $body = $resp ? (string)$resp->getBody() : $e->getMessage();

                        $trazaErrors[] = [
                            'product_id' => $productId,
                            'lote_id'    => $loteId,
                            'error'      => $body,
                        ];

                        Log::error('Error enviando a Traza (RequestException)', [
                            'product_id' => $productId,
                            'lote_id' => $loteId,
                            'error' => $body,
                        ]);
                    } catch (\Throwable $ex) {
                        $trazaErrors[] = [
                            'product_id' => $productId,
                            'lote_id'    => $loteId,
                            'error'      => $ex->getMessage(),
                        ];
                        Log::error('Error enviando a Traza', [
                            'product_id' => $productId,
                            'lote_id' => $loteId,
                            'exception' => $ex->getMessage(),
                        ]);
                    }
                }
            }

            // 7) Marcar transferencia como “procesada”
            $transfer->inventario = 'added';
            $transfer->save();

            DB::commit();

            $response = [
                'status'  => 1,
                'message' => 'Inventario actualizado correctamente.',
            ];

            if (! empty($trazaErrors)) {
                // devolvemos aviso de errores en las llamadas traza
                $response['warning'] = 'Algunas llamadas al servicio de Traza fallaron.';
                $response['traza_errors'] = $trazaErrors;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Error al actualizar el inventario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
