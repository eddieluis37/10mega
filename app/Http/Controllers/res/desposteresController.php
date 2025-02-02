<?php

namespace App\Http\Controllers\res;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use App\Models\Product;
use App\Models\Beneficiore;
use App\Models\Centro_costo_product;
use App\Models\Despostere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Lote;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Yajra\Datatables\Datatables;

use DateTime;
use Carbon\Carbon;

class desposteresController extends Controller
{
    public $consulta;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $prod = Product::Where('category_id', 1)->get();

        return view('categorias.res.desposte.index', compact('prod'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_user = Auth::user()->id;

        $beneficior = DB::table('beneficiores as b')
            ->join('thirds as t', 'b.thirds_id', '=', 't.id')
            ->join('stores as s', 'b.store_id', '=', 's.id')
            ->select('t.name', 'b.id', 's.name as name_store', 'b.codigo_lote as namelote', 'b.factura', 'b.canalplanta', 'b.cantidad', 'b.costokilo', 'b.fecha_cierre')
            ->where('b.id', $id)
            ->get();
        /******************/
        $this->consulta = Despostere::Where([
            ['beneficiores_id', $id],
            ['status', 'VALID'],
        ])->get();
        //  dd(count($this->consulta));
        if (count($this->consulta) === 0) {
            $prod = Product::where([
                ['status', 1],
                ['category_id', 13],
                ['level_product_id', 1],
            ])->whereNotIn('id', [1])->orderBy('name', 'asc')->get();
            foreach ($prod as $key) {
                $despost = new Despostere(); //Se crea una instancia del modelo
                $despost->users_id = $id_user; //Se establecen los valores para cada columna de la tabla
                $despost->beneficiores_id = $id;
                $despost->products_id = $key->id;
                $despost->peso = 0;
                $despost->porcdesposte = 0;
                $despost->costo = 0;
                $despost->costo_kilo = 0;
                $despost->precio = $key->price_fama;
                $despost->totalventa = 0;
                $despost->total = 0;
                $despost->porcventa = 0;
                $despost->porcutilidad = 0;
                $despost->status = 'VALID';
                $despost->save();
            }

            $this->consulta = Despostere::Where([
                ['beneficiores_id', $id],
                ['status', 'VALID'],
            ])->get();
        }
        /****************************************** */
        $status = '';
        $fechaBeneficioCierre = Carbon::parse($beneficior[0]->fecha_cierre);
        //$date = new DateTime();
        $date = Carbon::now();
        $currentDate = Carbon::parse($date->format('Y-m-d'));

        if ($currentDate->gt($fechaBeneficioCierre)) {
            //'Date 1 is greater than Date 2';
            $status = 'false';
        } elseif ($currentDate->lt($fechaBeneficioCierre)) {
            //'Date 1 is less than Date 2';
            $status = 'true';
        } else {
            //'Date 1 and Date 2 are equal';
            $status = 'false';
        }
        /****************************************** */

        $desposters = $this->consulta;
        $TotalDesposte = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('porcdesposte');
        $TotalVenta = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('totalventa');
        $porcVentaTotal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('porcventa');
        $pesoTotalGlobal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('peso');
        $costoTotalGlobal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('costo');
        $costoKiloTotal = 0;
        if ($pesoTotalGlobal != 0) {
            $costoKiloTotal = number_format($costoTotalGlobal / $pesoTotalGlobal, 2, ',', '.');
        }
        //dd(count($desposters));
        //$beneficior = Beneficiore::Where('id',$id)->get();
        return view('categorias.res.desposte.index', compact(
            'beneficior',
            'desposters',
            'TotalDesposte',
            'TotalVenta',
            'porcVentaTotal',
            'pesoTotalGlobal',
            'costoTotalGlobal',
            'costoKiloTotal',
            'status'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function sumTotales($id)
    {

        $TotalDesposte = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('porcdesposte');
        $TotalVenta = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('totalventa');
        $porcVentaTotal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('porcventa');
        $pesoTotalGlobal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('peso');
        $costoTotalGlobal = (float)Despostere::Where([['beneficiores_id', $id], ['status', 'VALID']])->sum('costo');
        $costoKiloTotal = number_format($costoTotalGlobal / $pesoTotalGlobal, 2, ',', '.');

        $array = [
            'TotalDesposte' => $TotalDesposte,
            'TotalVenta' => $TotalVenta,
            'porcVentaTotal' => $porcVentaTotal,
            'pesoTotalGlobal' => $pesoTotalGlobal,
            'costoTotalGlobal' => $costoTotalGlobal,
            'costoKiloTotal' => $costoKiloTotal,
        ];

        return $array;
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
    public function update(Request $request)
    {
        try {
            $despost = Despostere::where('id', $request->id)->first();
            $total_venta = $despost->precio * $request->peso_kilo;
            //$despost->users_id = $id_user; 
            //$despost->beneficiores_id = $request->beneficioId;
            //$despost->products_id = $request->producto;
            $despost->peso = $request->peso_kilo;
            //$despost->porcdesposte = 0; 
            //$despost->costo = 0;
            //$despost->precio = $request->pventa;
            $despost->totalventa = $total_venta;
            //$despost->total = 0;
            //$despost->porcventa = 0;
            //$despost->porcutilidad = 0;
            //$despost->status = 'VALID';
            $despost->save();
            /*************************** */
            $getBeneficiores = Beneficiore::Where('id', $request->beneficioId)->get();

            $beneficior = Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->get();
            $porcentajeVenta = 0;
            $porcentajeDesposte = 0;
            foreach ($beneficior as $key) {
                $sumakilosTotal = (float)Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->sum('peso');
                $porc = (float)number_format($key->peso / $sumakilosTotal, 4);
                $porcentajeDesposte = (float)number_format($porc * 100, 2);

                $sumaTotal = (float)Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->sum('totalventa');
                $porcve = (float)number_format($key->totalventa / $sumaTotal, 4);
                $porcentajeVenta = (float)number_format($porcve * 100, 2);

                $porcentajecostoTotal = (float)number_format($porcentajeVenta / 100, 4);
                $costoTotal = $porcentajecostoTotal * $getBeneficiores[0]->totalcostos;

                $costoKilo = 0;
                if ($key->peso != 0) {
                    $costoKilo = $costoTotal / $key->peso;
                }

                $updatedespost = Despostere::firstWhere('id', $key->id);
                $updatedespost->porcdesposte = $porcentajeDesposte;
                $updatedespost->porcventa = $porcentajeVenta;
                $updatedespost->costo = $costoTotal;
                $updatedespost->costo_kilo = $costoKilo;
                $updatedespost->save();
            }
            /*************************** */
            /*$desposte = Despostere::
            Where([
            ['beneficiores_id',$request->beneficioId],
            ['status','VALID'], 
            ])->get();*/
            $desposte = DB::table('desposteres as d')
                ->join('products as p', 'd.products_id', '=', 'p.id')
                ->select('p.name', 'd.id', 'd.porcdesposte', 'd.precio', 'd.peso', 'd.totalventa', 'd.porcventa', 'd.costo', 'd.costo_kilo')
                ->where([
                    ['d.beneficiores_id', $request->beneficioId],
                    ['d.status', 'VALID'],
                    ['p.status', 1],
                ])
                ->orderBy('p.name', 'asc')
                ->get();
            /*************************************** */
            $arrayTotales = $this->sumTotales($request->beneficioId);

            return response()->json([
                "status" => 1,
                "id" => $request->id,
                "precio" => $despost->precio,
                "totalventa" => $total_venta,
                "benefit" => $request->beneficioId,
                "desposte" => $desposte,
                "arrayTotales" => $arrayTotales,
                "beneficiores" => $getBeneficiores,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 0,
                "message" => $th,
            ]);
        }
    }

    public function cargarInventarioVersionOriginal(Request $request)
    {
        $beneficioId = $request->input('beneficioId');

        $currentDateTime = Carbon::now();
        $formattedDate = $currentDateTime->format('Y-m-d');

        $beneficio = Beneficiore::find($beneficioId);
        $beneficio->fecha_cierre = $formattedDate;


        $beneficio->save();

        // Afectar inventario KG y Utilidad con respectos a Visceras RES
        $centro = Centro_costo_product::where('products_id', 304)->first();
        $centro->compralote = $centro->compralote + $beneficio->cantidad;
        $centro->cto_compralote_total = $centro->cto_compralote_total + $beneficio->visceras;
        $centro->save();

        // Afectar inventario KG y Utilidad con respectos a Piel de RES
        $centro = Centro_costo_product::where('products_id', 301)->first();
        $centro->compralote = $centro->compralote + $beneficio->pieleskg;
        $centro->cto_compralote_total = $centro->cto_compralote_total + $beneficio->tpieles;
        $centro->save();

        $beneficior = Beneficiore::where('id', $beneficioId)->get();


        DB::update(
            "
        UPDATE centro_costo_products c
        JOIN desposteres d ON c.products_id = d.products_id
        JOIN beneficiores b ON b.id = d.beneficiores_id
        JOIN products p ON p.id = d.products_id
        SET c.compralote =  c.compralote + d.peso,
            c.cto_compralote =  c.cto_compralote + d.costo_kilo,
            c.cto_compralote_total  = c.cto_compralote_total + (d.costo_kilo * d.peso),
            p.cost = d.costo_kilo
        WHERE d.beneficiores_id = :beneficioid
        AND b.centrocosto_id = :cencosid 
        AND c.centrocosto_id = :cencosid2 ",
            [
                'beneficioid' => $beneficioId,
                'cencosid' =>  $beneficio->centrocosto_id,
                'cencosid2' =>  $beneficio->centrocosto_id
            ]
        );

        return response()->json([
            'status' => 1,
            'message' => 'Cargado al inventario exitosamente',
            'beneficior' => $beneficior
        ]);



        // return view('categorias.res.desposte.index', ['beneficio' => $beneficio]);
    }

    public function cargarInventario(Request $request)
    {
        $beneficioId = $request->input('beneficioId');

        DB::beginTransaction();

        try {
            // 1. Obtener el modelo Beneficiore
            $beneficiore = Beneficiore::findOrFail($beneficioId);

            Log::info('Beneficio:', ['Beneficio' => $beneficiore]);

            // Crear un nuevo lote
            $lote = Lote::create([
                'category_id' => 13,
                'codigo' => $beneficiore->codigo_lote, // Campo codigo_lote en Beneficiore
                'fecha_vencimiento' => Carbon::now()->addDays(35),
            ]);

            // 2. Obtener los detalles de desposteres
            $detallesDesposte = Despostere::where('beneficiores_id', $beneficioId)->get();
            Log::info('DetalleDesposte:', ['detalle' => $detallesDesposte]);

            // 3. Asociar productos al lote en la tabla lote_products
            foreach ($detallesDesposte as $detalle) {
                if ($detalle->peso > 0) { // Procesar solo si el peso es mayor a 0
                    $lote->products()->attach($detalle->products_id, [
                        'cantidad' => $detalle->peso, // Usamos peso como cantidad
                        'costo' => $detalle->costo,
                    ]);
                }
            }

            // 4 y 5. Actualizar o crear inventario con la cantidad calculada
            foreach ($detallesDesposte as $detalle) {
                if ($detalle->peso > 0) { // Procesar solo si el peso es mayor a 0
                    $inventario = Inventario::firstOrCreate(
                        [
                            'product_id' => $detalle->products_id,
                            'lote_id' => $lote->id,
                            'store_id' => $beneficiore->store_id, // Utilizamos el store_id del modelo Beneficiore
                        ],
                        [
                            'cantidad_inicial' => 0,
                            'cantidad_final' => 0,
                            'costo_unitario' => $detalle->costo_kilo,
                            'costo_total' => 0,
                        ]
                    );

                    // Incrementar cantidad y actualizar inventario
                    $inventario->cantidad_final += $detalle->peso;
                    $inventario->costo_total = $inventario->cantidad_final * $detalle->costo_kilo;
                    $inventario->save();

                    // **Actualizar el campo cost en la tabla products**
                    $product = Product::find($detalle->products_id);
                    if ($product) {
                        $product->cost = $detalle->costo_kilo;
                        $product->save();
                    }
                }
            }

            // 6. Registrar movimientos en la tabla de movimientos
            foreach ($detallesDesposte as $detalle) {
                if ($detalle->peso > 0) { // Procesar solo si el peso es mayor a 0
                    MovimientoInventario::create([
                        'tipo' => 'desposteres', // Tipo de movimiento
                        'desposteres_id' => $detalle->beneficiores_id,
                        'store_origen_id' => null,
                        'store_destino_id' => $beneficiore->store_id, // Utilizamos el store_id del modelo Beneficiore
                        'lote_id' => $lote->id,
                        'product_id' => $detalle->products_id,
                        'cantidad' => $detalle->peso,
                        'costo_unitario' => $detalle->costo_kilo,
                        'total' => $detalle->peso * $detalle->costo_kilo,
                        'fecha' => Carbon::now(),
                    ]);
                }
            }

            // **Cierra BeneficioRes si todo está bien**
            $currentDateTime = Carbon::now();
            $formattedDate = $currentDateTime->format('Y-m-d');

            $beneficio = Beneficiore::find($beneficioId);
            $beneficio->fecha_cierre = $formattedDate;
            $beneficio->save();

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Movimiento de inventario registrado con éxito.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => 'Error al registrar el movimiento de inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function destroy(Request $request)
    {
        try {

            $despost = Despostere::where('id', $request->id)->first();
            $despost->status = 'CANCELED';
            $despost->save();
            /*************************** */
            $getBeneficiores = Beneficiore::Where('id', $request->beneficioId)->get();

            $beneficior = Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->get();
            $porcentajeVenta = 0;
            $porcentajeDesposte = 0;
            foreach ($beneficior as $key) {
                $sumakilosTotal = (float)Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->sum('peso');
                $porc = (float)number_format($key->peso / $sumakilosTotal, 4);
                $porcentajeDesposte = (float)number_format($porc * 100, 2);

                $sumaTotal = (float)Despostere::Where([['beneficiores_id', $request->beneficioId], ['status', 'VALID']])->sum('totalventa');
                $porcve = (float)number_format($key->totalventa / $sumaTotal, 4);
                $porcentajeVenta = (float)number_format($porcve * 100, 2);

                $porcentajecostoTotal = (float)number_format($porcentajeVenta / 100, 4);
                $costoTotal = $porcentajecostoTotal * $getBeneficiores[0]->totalcostos;

                $updatedespost = Despostere::firstWhere('id', $key->id);
                $updatedespost->porcdesposte = $porcentajeDesposte;
                $updatedespost->porcventa = $porcentajeVenta;
                $updatedespost->costo = $costoTotal;
                $updatedespost->save();
            }
            /*************************** */
            $desposte = DB::table('desposteres as d')
                ->join('products as p', 'd.products_id', '=', 'p.id')
                ->select('p.name', 'd.id', 'd.porcdesposte', 'd.precio', 'd.peso', 'd.totalventa', 'd.porcventa', 'd.costo')
                ->where([
                    ['d.beneficiores_id', $request->beneficioId],
                    ['d.status', 'VALID'],
                ])->get();
            /*************************************** */
            $arrayTotales = $this->sumTotales($request->beneficioId);

            return response()->json([
                "status" => 1,
                "id" => $request->id,
                "benefit" => $request->beneficioId,
                "desposte" => $desposte,
                "arrayTotales" => $arrayTotales,
                "beneficiores" => $getBeneficiores,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 500,
                "message" => (array) $th
            ]);
        }
    }
}
