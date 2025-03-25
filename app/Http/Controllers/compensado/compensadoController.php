<?php

namespace App\Http\Controllers\compensado;

use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Third;
use App\Models\centros\Centrocosto;
use App\Models\Compensadores;
use App\Models\Compensadores_detail;
use App\Models\Centro_costo_product;
use App\Models\Compensador;
use App\Models\Store;
use App\Models\MovimientoInventario;
use App\Models\Inventario;
use App\Models\Lote;

class compensadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user(); // Obtener el usuario autenticado

        $providers = Third::where('status', 1)->get();
        $centros = Centrocosto::where('status', 1)->get();

        // Obtener solo las bodegas asociadas al usuario en store_user
        $bodegas = Store::whereIn('id', function ($query) use ($user) {
            $query->select('store_id')
                ->from('store_user')
                ->where('user_id', $user->id);
        })
            ->whereNotIn('id', [40]) // Excluir bodegas específicas si aplica
            ->orderBy('name', 'asc')
            ->get();

        return view('compensado.res.index', compact('providers', 'bodegas', 'centros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //$category = Category::WhereIn('id',[1,2,3])->get();
        //$providers = Third::Where('status',1)->get();
        //$centros = Centrocosto::Where('status',1)->get();
        $datacompensado = DB::table('compensadores as comp')
            /*    ->join('categories as cat', 'comp.categoria_id', '=', 'cat.id') */
            ->join('thirds as tird', 'comp.thirds_id', '=', 'tird.id')
            ->join('stores as s', 'comp.store_id', '=', 's.id')
            //    ->join('lotes as l', 'comp.lote_id', '=', 'l.id')
            ->join('centro_costo as centro', 'centro.id', '=', 's.centrocosto_id')
            ->select('comp.*', 'tird.name as namethird', 's.name as namestore', 'centro.name as namecentrocosto')
            ->where('comp.id', $id)
            ->get();

        $lotes = Lote::orderBy('id', 'desc')->get();

        $prod = Product::Where([
            ['status', 1]
        ])
            ->orderBy('category_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        /**************************************** */
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
        /**************************************** */

        $detail = $this->getcompensadoresdetail($id);

        $arrayTotales = $this->sumTotales($id);
        //dd($arrayTotales);
        return view('compensado.create', compact('datacompensado', 'lotes', 'prod', 'id', 'detail', 'arrayTotales', 'status'));
    }

    public function getcompensadoresdetail($compensadoId)
    {

        $detail = DB::table('compensadores_details as de')
            ->join('lotes as l', 'de.lote_id', '=', 'l.id')
            ->join('products as pro', 'de.products_id', '=', 'pro.id')
            ->select('de.*', 'l.codigo as codigo', 'pro.name as nameprod', 'pro.code')
            ->where([
                ['de.compensadores_id', $compensadoId],
                ['de.status', 1]
            ])->get();

        return $detail;
    }

    public function getproducts(Request $request)
    {
        $prod = Product::Where([
            /*   ['category_id',$request->categoriaId], */
            ['status', 1]
        ])->get();
        return response()->json(['products' => $prod]);
    }
    
    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::where('status', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('code', 'LIKE', "%{$query}%");
            })
            ->orderBy('name', 'asc')
            ->get();
            
        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => $product->code . ' - ' . $product->name
            ];
        });
        
        return response()->json($formattedProducts);
    }


    public function savedetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'compensadoId' => 'required',
                'lote' => 'required',
                'producto' => 'required',
                'pcompra' => 'required',
                'pesokg' => [
                    'required',
                    'numeric',
                    'regex:/^\d+(\.\d{1,2})?$/',
                    'min:0.1',
                ],
            ], [
                'compensadoId.required' => 'El compensado es requerido',
                'lote.required' => 'El lote es requerido',
                'producto.required' => 'El producto es requerido',
                'pcompra.required' => 'El precio de compra es requerido',
                'pesokg.required' => 'El peso es requerido',
                'pesokg.numeric'  => 'La cantidad debe ser un número.',
                'pesokg.min'      => 'La cantidad debe ser mayor a 0.1.',
                'pesokg.regex'     => 'La cantidad debe tener hasta dos decimales.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $formatCantidad = new metodosrogercodeController();
            $formatPcompra = $formatCantidad->MoneyToNumber($request->pcompra);

            $pesokg = $request->pesokg;
            $subtotal = $formatPcompra * $pesokg;

            $data = [
                'compensadores_id' => $request->compensadoId,
                'lote_id' => $request->lote,
                'products_id' => $request->producto,
                'pcompra' => $formatPcompra,
                'peso' => $pesokg,
                'iva' => 0,
                'subtotal' => $subtotal,
            ];

            // Actualiza si existe, crea si no
            Compensadores_detail::updateOrCreate(
                ['id' => $request->regdetailId], // Busca por ID si existe
                $data // Asigna datos
            );

            return response()->json([
                'status' => 1,
                'message' => "Agregado correctamente",
                'array' => $this->getcompensadoresdetail($request->compensadoId),
                'arrayTotales' => $this->sumTotales($request->compensadoId),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sumTotales($id)
    {

        // Calcular los totales
        $pesoTotalGlobal = (float)Compensadores_detail::Where([['compensadores_id', $id], ['status', 1]])->sum('peso');
        $totalGlobal = (float)Compensadores_detail::Where([['compensadores_id', $id], ['status', 1]])->sum('subtotal');
        //$costoKiloTotal = number_format($costoTotalGlobal / $pesoTotalGlobal, 2, ',', '.');

        // Actualizar el campo valor_total_factura en el modelo Compensadores
        $compensador = Compensadores::find($id);
        if ($compensador) {
            $compensador->valor_total_factura = $totalGlobal; // Asignar el total calculado
            $compensador->save(); // Guardar los cambios en la base de datos
        }

        // Preparar el array de resultados
        $array = [
            'pesoTotalGlobal' => $pesoTotalGlobal,
            'totalGlobal' => $totalGlobal,
        ];

        return $array;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) // Save primera parte
    {
        try {

            $rules = [
                'compensadoId' => 'required',
                'provider' => 'required',
                'store' => 'required',
                'factura' => 'required',
            ];
            $messages = [
                'compensadoId.required' => 'El compensadoId es requerido',
                'provider.required' => 'El proveedor es requerido',
                'factura.required' => 'La factura es requerida',
                'store.required' => 'La bodega es requerido',
                'factura.required' => 'La factura es requerida',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getReg = Compensadores::firstWhere('id', $request->compensadoId);

            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format

                $id_user = Auth::user()->id;

                $comp = new Compensadores();
                $comp->users_id = $id_user;
                /*     $comp->categoria_id = $request->categoria; */
                $comp->thirds_id = $request->provider;
                $comp->store_id = $request->store;
                /*    $comp->fecha_compensado = $currentDateFormat; */
                $comp->fecha_compensado = $request->fecha_compensado;
                $comp->fecha_ingreso = $request->fecha_ingreso;
                $comp->fecha_cierre = $dateNextMonday;
                $comp->factura = $request->factura;
                $comp->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    "registroId" => $comp->id
                ]);
            } else {
                $getReg = Compensadores::firstWhere('id', $request->compensadoId);
                $getReg->thirds_id = $request->provider;
                $getReg->store_id = $request->store;
                $getReg->fecha_compensado = $request->fecha_compensado;
                $getReg->fecha_ingreso = $request->fecha_ingreso;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $data = DB::table('compensadores as comp')
            /*   ->join('categories as cat', 'comp.categoria_id', '=', 'cat.id') */
            ->join('thirds as tird', 'comp.thirds_id', '=', 'tird.id')
            //->join('lotes as l', 'comp.lote_id', '=', 'l.id')
            ->join('stores as s', 'comp.store_id', '=', 's.id')
            ->select('comp.*', 'tird.name as namethird', 's.name as namestore')
            /*   ->where('comp.status', 1) */
            ->get();
        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            /*->addColumn('status', function($data){
                    if ($data->estado == 1) {
                        $status = '<span class="badge bg-success">Activo</span>';
                    }else{
                        $status= '<span class="badge bg-danger">Inactivo</span>';
                    }
                    return $status;
                })*/
            ->addColumn('date', function ($data) {
                $date = Carbon::parse($data->fecha_compensado);
                $onlyDate = $date->toDateString();
                return $onlyDate;
            })
            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();
                if (Carbon::parse($currentDateTime->format('Y-m-d'))->gt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
                        <a href="" class="btn btn-dark" title="DesposteCerradoPorFecha" target="_blank">
                            <i class="fas fa-check-circle"></i>
                        </a>					   
					    <button class="btn btn-dark" title="Editar Compensado" onclick="showDataForm(' . $data->id . ')" disabled>
                            <i class="fas fa-edit"></i>
					    </button>
					    <button class="btn btn-dark" title="Borrar Compensado" disabled>
						    <i class="fas fa-trash"></i>
					    </button>
                        <a href="compensado/pdfCompensado/' . $data->id . '" class="btn btn-dark" title="VerCompraVencidaPorFecha" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>
                        </div>
                        ';
                } elseif (Carbon::parse($currentDateTime->format('Y-m-d'))->lt(Carbon::parse($data->fecha_cierre))) {
                    $btn = '
                        <div class="text-center">
					    <a href="compensado/create/' . $data->id . '" class="btn btn-dark" title="Detalles" >
						    <i class="fas fa-directions"></i>
					    </a>
					    <button class="btn btn-dark" title="Compensado" onclick="editCompensado(' . $data->id . ');">
						    <i class="fas fa-edit"></i>
					    </button>
                        <a href="compensado/pdfCompensado/' . $data->id . '" class="btn btn-dark" title="VerCompraPendiente" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>
					  
                        </div>
                        ';
                } else {
                    $btn = '
                        <div class="text-center">
                        <a href="" class="btn btn-dark" title="CompraCerrado" target="_blank">
                            <i class="fas fa-check-circle"></i>
                        </a>
					    <button class="btn btn-dark" title="Compensado" disabled>
						    <i class="fas fa-eye"></i>
					    </button>
                        <a href="compensado/pdfCompensado/' . $data->id . '" class="btn btn-dark" title="VerCompraCerrada" target="_blank">
                        <i class="far fa-file-pdf"></i>
					    </a>					  
                        </div>
                        ';
                }
                return $btn;
            })
            ->rawColumns(['date', 'action'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $reg = Compensadores_detail::where('id', $request->id)->first();
        return response()->json([
            'status' => 1,
            'reg' => $reg
        ]);
    }

    public function editCompensado(Request $request)
    {
        $reg = Compensadores::where('id', $request->id)->first();
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
            $compe = Compensadores_detail::where('id', $request->id)->first();
            $compe->status = 0;
            $compe->save();

            $arraydetail = $this->getcompensadoresdetail($request->compensadoId);

            $arrayTotales = $this->sumTotales($request->compensadoId);
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

    public function destroyCompensado(Request $request)
    {
        try {
            $compe = Compensadores::where('id', $request->id)->first();
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

    public function cargarInventariocr(Request $request)
    {
        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');

        // Obtener el compensador
        $compensadorId = $request->input('compensadoId');
        $compensador = Compensador::findOrFail($compensadorId);

    //    Log::info('Detalle Compensado:', ['detalle' => $compensador->detalles]);

        // Actualizar el registro de compensadores
        $compensador->fecha_cierre = $formattedDate;
        $compensador->status = true;
        $compensador->save();

        // Filtrar solo los detalles con status = '1'
        $detallesActivos = $compensador->detalles->where('status', '1');

        // Validar que el compensador tenga detalles activos asociados
        if ($detallesActivos->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message' => 'El compensador no tiene detalles activos asociados.',
            ], 422);
        }

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Iterar sobre los detalles activos del compensador
            foreach ($detallesActivos as $detalle) {
                $loteId = $detalle->lote_id;
                $productId = $detalle->products_id;
                $peso = $detalle->peso;

                // Verificar si el lote existe
                $lote = Lote::with('productos')->find($loteId);
           //     Log::info('Lotes productos:', ['lote_productos' => optional($lote)->productos]);

                if (!$lote) {
                    return response()->json([
                        'status' => 0,
                        'message' => "El lote con ID {$loteId} no existe.",
                    ], 422);
                }

                // Verificar si el producto ya está asociado al lote
                $loteProductExists = $lote->productos()->where('product_id', $productId)->exists();

                if (!$loteProductExists) {
                    // Asociar el producto al lote
                    $lote->productos()->attach($productId, [
                        'cantidad' => 0, // Inicializar la cantidad
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Actualizar o crear el inventario
                $inventario = Inventario::firstOrCreate(
                    [
                        'store_id' => $compensador->store_id,
                        'lote_id' => $loteId,
                        'product_id' => $productId,
                    ],
                    ['cantidad_compra_prod' => 0]
                );

                // Incrementar la cantidad en el inventario
                $inventario->increment('cantidad_compra_prod', $peso);

                // Registrar el movimiento en la tabla de movimientos
                MovimientoInventario::create([
                    'compensador_id' => $compensadorId,
                    'tipo' => 'compensadores',
                    'fecha' => Carbon::now(),
                    'cantidad' => $peso,
                    'lote_id' => $loteId,
                    'product_id' => $productId,
                    'store_destino_id' => $compensador->store_id,
                ]);
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Movimiento de inventario procesado correctamente.',
                'compensadores' => $compensador,
            ], 201);
        } catch (\Exception $e) {
            // Revertir la transacción
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'Error al procesar el movimiento de inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function cargarInventarioVersion1(Request $request)
    {
        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');
        $compensadoId = $request->input('compensadoId');

        // Actualizar el registro de compensadores
        $compensadores = Compensadores::findOrFail($compensadoId);
        $compensadores->fecha_cierre = $formattedDate;
        $compensadores->status = true;
        $compensadores->save();

        $centrocosto_id = $compensadores->centrocosto_id;

        //  Actualizar los productos en centro_costo_products
        DB::update(
            "
            UPDATE centro_costo_products c
            JOIN compensadores_details d ON c.products_id = d.products_id
            JOIN compensadores b ON b.id = d.compensadores_id
            JOIN products p ON p.id = c.products_id
            SET c.cto_compensados = c.cto_compensados + d.pcompra,
                c.cto_compensados_total = c.cto_compensados_total + (d.pcompra * d.peso),
                c.tipoinventario = 'cerrado',
                p.cost = d.pcompra
            WHERE d.compensadores_id = :compensadoresid
            AND b.centrocosto_id = :cencosid 
            AND c.centrocosto_id = :cencosid2
            AND d.status = 1",
            [
                'compensadoresid' => $compensadoId,
                'cencosid' => $centrocosto_id,
                'cencosid2' => $centrocosto_id
            ]
        );

        // Calcular el peso acumulado del producto
        $centroCostoProducts = Centro_costo_product::where('centrocosto_id', $centrocosto_id)->get();

        foreach ($centroCostoProducts as $centroCostoProduct) {
            $accumulatedWeight = Compensadores_detail::where('compensadores_id', '=', $compensadoId)
                ->where('products_id', $centroCostoProduct->products_id)
                ->where('status', '1')
                ->sum('peso');

            // Guarda el accumulated weight en la tabla temporal
            DB::table('temporary_accumulatedweights')->updateOrInsert(
                [
                    'centrocosto_id' => $centroCostoProduct->centrocosto_id,
                    'product_id' => $centroCostoProduct->products_id,
                ],
                [
                    'accumulated_weight' => $accumulatedWeight
                ]
            );
        }

        // Recuperar los registros de la tabla temporary_accumulatedweights 
        $accumulatedWeights = DB::table('temporary_accumulatedweights')->get();

        foreach ($accumulatedWeights as $accumulatedWeight) {
            // Busca el Centro_costo_product por el product_id y centrocosto_id correspondiente
            $centroCostoProduct = Centro_costo_product::where('products_id', $accumulatedWeight->product_id)
                ->where('centrocosto_id', $accumulatedWeight->centrocosto_id)
                ->first();

            // Suma el valor de accumulatedWeight al campo compensados de centroCostoProduct
            if ($centroCostoProduct) {
                $centroCostoProduct->compensados += $accumulatedWeight->accumulated_weight;
                $centroCostoProduct->save();
            }
        }

        // limpia tabla temporary_accumulatedweights table
        DB::table('temporary_accumulatedweights')->truncate();

        session()->regenerate();

        return response()->json([
            'status' => 1,
            'message' => 'Cargado al inventario exitosamente',
            'compensadores' => $compensadores
        ]);
    }

    public function cargarInventarioVersion2(Request $request)
    {
        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');

        // Valores estáticos para prueba
        $staticData = [
            'compensador_id' => 3, // ID de una compra en la base de datos
            'fecha' => '2024-12-28',
            'cantidad' => 99,
            'lote_id' => 1, // ID de un lote existente en la base de datos            
            'store_destino_id' => 2, // ID de una bodega existente en la base de datos
        ];

        // Simular un compensadoId (si necesitas usarlo)
        $compensadoId = 1; // ID de un registro existente en la tabla 'compensadores'

        // Recuperar el registro de compensadores
        $compensadores = Compensadores::findOrFail($compensadoId);

        $centrocosto_id = $compensadores->centrocosto_id;
        $lote_id = $compensadores->lote_id;

        // No validar, ya que estamos usando datos estáticos
        $validated = $staticData;

        DB::beginTransaction();

        try {
            // Buscar o crear el inventario
            $inventario = Inventario::firstOrCreate(
                [
                    'store_id' => $validated['store_destino_id'],
                    'lote_id' => $validated['lote_id'],
                ],
                ['cantidad_actual' => 0]
            );

            // Incrementar la cantidad
            $inventario->increment('cantidad_actual', $validated['cantidad']);

            // Crear el movimiento de inventario
            $movimiento = MovimientoInventario::create([
                'compensador_id' => $validated['compensador_id'],
                'tipo' => 'compensadores',
                'fecha' => $validated['fecha'],
                'cantidad' => $validated['cantidad'],
                'lote_id' => $validated['lote_id'],
                'store_destino_id' => $validated['store_destino_id'],
            ]);

            DB::commit();


            return response()->json([
                'status' => 1,
                'message' => 'Compra registrada exitosamente.',
                'compensadores' => $compensadores,
                'data' => $movimiento
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            session()->regenerate();

            return response()->json([
                'status' => 1,
                'message' => 'Error al registrar la compra.',
                'compensadores' => $compensadores,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cargarInventarioUnSoloLoteporCompra(Request $request)
    {
        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');

        // Obtener el compensador
        $compensadorId = $request->input('compensadoId');

        // Actualizar el registro de compensadores       
        $compensador = Compensadores::findOrFail($compensadorId);
        $compensador->fecha_cierre = $formattedDate;
        $compensador->status = true;
        $compensador->save();

        // Validar que el compensador tenga un lote asociado
        if (!$compensador->lote_id) {
            return response()->json([
                'status' => 0,
                'message' => 'El compensador no tiene un lote asociado.',
            ], 422);
        }

        // Obtener el lote asociado
        $lote = Lote::with('products')->find($compensador->lote_id);

        if (!$lote) {
            return response()->json([
                'status' => 0,
                'message' => 'El lote asociado al compensador no existe.',
            ], 422);
        }

        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Obtener todos los detalles de los productos relacionados con la compra compensada
            $productosCompensados = $compensador->detalle()
                ->select('products_id', 'peso')
                ->get();

            foreach ($productosCompensados as $detalle) {
                $productId = $detalle->products_id;
                $peso = $detalle->peso;

                // Verificar si el producto ya está asociado al lote
                $loteProductExists = DB::table('lote_products')
                    ->where('lote_id', $lote->id)
                    ->where('product_id', $productId)
                    ->exists();

                if (!$loteProductExists) {
                    // Asociar el producto al lote si no está relacionado
                    DB::table('lote_products')->insert([
                        'lote_id' => $lote->id,
                        'product_id' => $productId,
                        'cantidad' => 0, // Inicializa la cantidad
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Calcular la cantidad de la compra
                if ($peso > 0) {
                    // Actualizar o crear el inventario
                    $inventario = Inventario::firstOrCreate(
                        [
                            'store_id' => $compensador->store_id,
                            'lote_id' => $lote->id,
                            'product_id' => $productId,
                        ],
                        ['cantidad_inicial' => 0]
                    );

                    // Incrementar la cantidad en el inventario
                    $inventario->increment('cantidad_inicial', $peso);

                    // Registrar el movimiento en la tabla de movimientos
                    MovimientoInventario::create([
                        'compensador_id' => $compensadorId,
                        'tipo' => 'compensadores',
                        'fecha' => Carbon::now(),
                        'cantidad' => $peso,
                        'lote_id' => $lote->id,
                        'product_id' => $productId,
                        'store_destino_id' => $compensador->store_id,
                    ]);
                }
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Movimiento de inventario procesado correctamente.',
                'compensadores' => $compensador,
            ], 201);
        } catch (\Exception $e) {
            // Revertir la transacción
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'Error al procesar el movimiento de inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }













    public function cargarInventarioMasivo()
    {
        for ($compensadoId = 1; $compensadoId <= 38; $compensadoId++) {
            $currentDateTime = Carbon::now();
            $formattedDate = $currentDateTime->format('Y-m-d');

            $compensadores = Compensadores::find($compensadoId);
            if (!$compensadores) {
                continue; // Si no se encuentra el registro, pasar al siguiente compensadoId
            }

            $compensadores->fecha_cierre = $formattedDate;
            $compensadores->save();
            $centrocosto_id = $compensadores->centrocosto_id;

            DB::update(
                "
            UPDATE centro_costo_products c
            JOIN compensadores_details d ON c.products_id = d.products_id
            JOIN compensadores b ON b.id = d.compensadores_id
            SET c.cto_compensados =  c.cto_compensados + d.pcompra,
                c.cto_compensados_total  = c.cto_compensados_total + (d.pcompra * d.peso),
                c.tipoinventario = 'cerrado'
            WHERE d.compensadores_id = :compensadoresid
            AND b.centrocosto_id = :cencosid 
            AND c.centrocosto_id = :cencosid2
            AND d.status = 1 ",
                [
                    'compensadoresid' => $compensadoId,
                    'cencosid' => $centrocosto_id,
                    'cencosid2' => $centrocosto_id
                ]
            );

            // Calcular el peso acumulado del producto 
            $centroCostoProducts = Centro_costo_product::where('tipoinventario', 'cerrado')
                ->where('centrocosto_id', $centrocosto_id)
                ->get();

            foreach ($centroCostoProducts as $centroCostoProduct) {
                $accumulatedWeight = Compensadores_detail::where('compensadores_id', '=', $compensadoId)
                    ->where('products_id', $centroCostoProduct->products_id)
                    ->where('status', 1)
                    ->sum('peso');

                // Almacenar el peso acumulado en la tabla temporal
                DB::table('temporary_accumulatedweights')->insert([
                    'product_id' => $centroCostoProduct->products_id,
                    'accumulated_weight' => $accumulatedWeight
                ]);
            }

            // Recuperar los registros de la tabla temporary_accumulatedweights
            $accumulatedWeights = DB::table('temporary_accumulatedweights')->get();

            foreach ($accumulatedWeights as $accumulatedWeight) {
                $centroCostoProduct = Centro_costo_product::find($accumulatedWeight->product_id);

                // Sumar el valor de accumulatedWeight al campo compensados
                $centroCostoProduct->compensados += $accumulatedWeight->accumulated_weight;
                $centroCostoProduct->save();

                // Limpiar la tabla temporary_accumulatedweights
                DB::table('temporary_accumulatedweights')->truncate();
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Carga masiva al inventario completada con éxito'
        ]);
    }
}
