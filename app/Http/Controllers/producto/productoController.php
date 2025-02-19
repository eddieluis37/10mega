<?php

namespace App\Http\Controllers\producto;

use App\Http\Controllers\Controller;

use App\Models\caja\Caja;
use App\Models\Category;
use App\Models\Centro_costo_product;
use App\Models\centros\Centrocosto;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Products\Meatcut;
use App\Http\Controllers\metodosgenerales\metodosrogercodeController;
use App\Models\Brand_third;
use App\Models\Levels_products;
use App\Models\Listaprecio;
use App\Models\Listapreciodetalle;
use App\Models\Products\Unitofmeasure;
use App\Models\shopping\shopping_enlistment;
use App\Models\shopping\shopping_enlistment_details;


class productoController extends Controller
{
    public function getProductos()
    {
        $productos = Product::all(); // Asegúrate de tener el modelo correcto
        return response()->json($productos);
    }

    public function index()
    {
        $categorias = Category::orderBy('id')->get();
        $proveedores = Third::Where('proveedor', 1)->get();
        $niveles = Levels_products::Where('status', 1)->get();
        $presentaciones = Unitofmeasure::Where('status', 1)->get();
        $familias = Meatcut::Where('status', 1)->get();

        $brandsThirds = Brand_third::orderBy('id')->get();

        $usuario = User::WhereIn('id', [9, 11, 12])->get();

        $centros = Centrocosto::WhereIn('id', [1])->get();
        return view("producto.index", compact('usuario', 'brandsThirds', 'categorias', 'proveedores', 'niveles', 'presentaciones', 'familias',  'centros'));
    }

    public function show()
    {
        $data = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('meatcuts as cut', 'p.meatcut_id', '=', 'cut.id')
            //  ->join('centro_costo as centro', 'p.centrocosto_id', '=', 'centro.id')
            ->select('p.*', 'c.name as namecategorias', 'cut.name as namefamilia')
            /*  ->where('p.status', 1) */
            ->get();

        //     return $data;
        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            /* ->addColumn('fecha1', function ($data) {
                $fecha1 = Carbon::parse($data->fecha_hora_inicio);
                $formattedDate1 = $fecha1->format('M-d. H:i');
                return $formattedDate1;
            })
            ->addColumn('fecha2', function ($data) {
                $fecha2 = Carbon::parse($data->fecha_hora_cierre);
                $formattedDate = $fecha2->format('M-d. H:i');
                return $formattedDate;
            }) */
            /*  ->addColumn('inventory', function ($data) {
                if ($data->estado == 'close') {
                    $statusInventory = '<span class="badge bg-warning">Cerrado</span>';
                } else {
                    $statusInventory = '<span class="badge bg-success">Abierto</span>';
                }
                return $statusInventory;
            }) */

            /*      <div class="text-center">
            <a href="caja/create/' . $data->id . '" class="btn btn-dark" title="RetiroDinero" >
                <i class="fas fa-money-bill-alt"></i>
            </a>
            <a href="caja/create/' . $data->id . '" class="btn btn-dark" title="CuadreCaja" ' . $status . '>
                <i class="fas fa-money-check-alt"></i>
            </a>					
            <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="VerReciboCaja">
                <i class="fas fa-eye"></i>
            </a> 
             <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteBook">E</a>
            */

            ->addColumn('action', function ($data) {
                $currentDateTime = Carbon::now();

                if ($data->status == 1) {
                    $btn = '
                         <div class="text-center">   
                         
                         <button class="btn btn-dark" title="Editar CabezaOrden" onclick="edit(' . $data->id . ');">
						    <i class="fas fa-edit"></i>
					    </button>
                      
                         				
                         <button class="btn btn-dark" title="Borrar" disabled>
                             <i class="fas fa-trash"></i>
                         </button>
                         
                         </div>
                         ';
                } elseif ($data->status == 0) {
                    $btn = '
                         <div class="text-center">
                         <a href="caja/create/' . $data->id . '" class="btn btn-dark" title="CuadreCaja">
                            <i class="fas fa-money-check-alt"></i>
                         </a>
                        
                         <a href="caja/pdfCierreCaja/' . $data->id . '" class="btn btn-dark" title="PdfCuadreCajaOpen" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>

                         <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="CuadreCajaCerrado" target="_blank">
                         <i class="fas fa-eye"></i>
                         </a>	

                         <button class="btn btn-dark" title="Borrar">
                         <i class="fas fa-trash"></i>
                         </button>
                       
                         </div>
                         ';
                    //ESTADO Cerrada
                } else {
                    $btn = '
                         <div class="text-center">
                         <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="CuadreCajaCerrado" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                         <button class="btn btn-dark" title="Borra" disabled>
                             <i class="fas fa-trash"></i>
                         </button>
                       
                         </div>
                         ';
                }
                return $btn;
            })

            ->rawColumns(['fecha1', 'fecha2', 'inventory', 'action'])
            ->make(true);
    }

    public function pdf($id)
    {
        $caja = Caja::findOrFail($id);

        $pdf = PDF::loadView('cajas.pdf', compact('caja'));

        return $pdf->download('caja.pdf');
    }

    public function showReciboCaja($id)
    {
        $caja = Caja::findOrFail($id)
            ->join('users as u', 'cajas.cajero_id', '=', 'u.id')
            /*   ->join('meatcuts as cut', 'cajas.meatcut_id', '=', 'cut.id')*/
            ->join('centro_costo as centro', 'cajas.centrocosto_id', '=', 'centro.id')
            ->select('cajas.*', 'centro.name as namecentrocosto', 'u.name as namecajero')
            ->where('cajas.status', 1)
            ->where('cajas.id', $id)
            ->get();

        //  dd($caja);

        return view('caja.showReciboCaja', compact('caja'));
    }

    public function storeCierreCaja(Request $request, $ventaId)
    {

        // Obtener los valores

        $efectivo = $request->input('efectivo');
        $efectivo = str_replace(['.', ',', '$', '#'], '', $efectivo);

        $valor_real = $request->input('valor_real');
        $valor_real = str_replace(['.', ',', '$', '#'], '', $valor_real);

        $total = $request->input('total');
        $total = str_replace(['.', ',', '$', '#'], '', $total);

        $diferencia = $request->input('diferencia');
        $diferencia = str_replace(['.', ',', '$', '#'], '', $diferencia);

        $total = $request->input('total');
        $total = str_replace(['.', ',', '$', '#'], '', $total);

        $forma_pago_tarjeta_id = $request->input('forma_pago_tarjeta_id');
        $forma_pago_otros_id = $request->input('forma_pago_otros_id');
        $forma_pago_credito_id = $request->input('forma_pago_credito_id');

        $codigo_pago_tarjeta = $request->input('codigo_pago_tarjeta');
        $codigo_pago_otros = $request->input('codigo_pago_otros');
        $codigo_pago_credito = $request->input('codigo_pago_credito');

        $valor_a_pagar_tarjeta = $request->input('valor_a_pagar_tarjeta');
        $valor_a_pagar_tarjeta = str_replace(['.', ',', '$', '#'], '', $valor_a_pagar_tarjeta);

        $valor_a_pagar_otros = $request->input('valor_a_pagar_otros');
        $valor_a_pagar_otros = str_replace(['.', ',', '$', '#'], '', $valor_a_pagar_otros);

        $valor_a_pagar_credito = $request->input('valor_a_pagar_credito');
        if (is_null($valor_a_pagar_credito)) {
            $valor_a_pagar_credito = 0;
        }
        $valor_a_pagar_credito = str_replace(['.', ',', '$', '#'], '', $valor_a_pagar_credito);

        $valor_pagado = $request->input('valor_pagado');
        $valor_pagado = str_replace(['.', ',', '$', '#'], '', $valor_pagado);

        $cambio = $request->input('cambio');
        $cambio = str_replace(['.', ',', '$', '#'], '', $cambio);

        $estado = 'close';
        $status = '1'; //1 = close

        try {
            $caja = Caja::find($ventaId);
            $caja->user_id = $request->user()->id;
            $caja->efectivo = $efectivo;
            $caja->valor_real = $valor_real;
            $caja->total = $total;
            $caja->diferencia = $diferencia;
            $caja->estado = $estado;
            $caja->status = $status;
            $caja->fecha_hora_cierre = now();
            $caja->save();

            if ($caja->status == 1) {
                return redirect()->route('caja.index');
            }

            return response()->json([
                'status' => 1,
                'message' => 'Guardado correctamente',
                "registroId" => $caja->id,
                'redirect' => route('caja.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'array' => (array) $th
            ]);
        }
    }



    /**
     * Show the form for creating a new resource.
     *    ->whereDate('sa.fecha_venta', now())
     * @return \Illuminate\Http\Response
     * 
     * /*   $valorApagarEfectivo = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.third_id', 33)
            ->sum('sa.cambio');

        dd($valorApagarEfectivo); 
     */

    public function create($id)
    {
        // Validar si el cajero_id de la tabla cajas es igual al user_id de la tabla sales
        $dataAlistamiento = DB::table('cajas as ca')
            ->join('sales as sa', function ($join) {
                $join->on('ca.cajero_id', '=', 'sa.user_id');
            })
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->select('ca.*', 'centro.name as namecentrocosto', 'u.name as namecajero')
            ->where('ca.id', $id)
            ->get();

        if ($dataAlistamiento->isEmpty()) {
            return redirect()->back()->with('warning', 'El cajero no tiene ventas asociadas en la tabla sales.');
        }

        $status = '';
        $estadoVenta = $dataAlistamiento[0]->status ? 'true' : 'false';

        //Suma el total de efectivo, totalTarjetas, totalOtros de la venta del día de ese cajero.
        $arrayTotales = $this->sumTotales($id);

        return view('producto.create', compact('dataAlistamiento', 'status', 'arrayTotales'));
    }

    public function sumTotales($id)
    {
        $valorApagarEfectivo = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.valor_a_pagar_efectivo');

        $valorCambio = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.cambio');

        $valorEfectivo = $valorApagarEfectivo - $valorCambio;

        $valorApagarTarjeta = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.valor_a_pagar_tarjeta');

        $valorApagarOtros = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.centrocosto_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.valor_a_pagar_otros');

        $valorTotal = $valorApagarTarjeta + $valorApagarOtros;


        $array = [
            'valorApagarEfectivo' => $valorApagarEfectivo,
            'valorCambio' => $valorCambio,
            'valorEfectivo' => $valorEfectivo,
            'valorApagarTarjeta' => $valorApagarTarjeta,
            'valorApagarOtros' => $valorApagarOtros,
            'valorTotal' => $valorTotal,
        ];

        return $array;
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
                'productoId' => 'required',
                'categoria' => 'required',
                'marca' => 'required',
                'nivel' => 'required',
                'familia' => 'required',
                'subfamilia' => 'required',                
                'impuestoiva' => 'required|numeric',
                'isa' => 'required|numeric',
                'impoconsumo' => 'required|numeric',
            ];

            $messages = [
                'productoId.required' => 'El es requerido',
                'categoria.required' => 'El cajero es requerido',
                'marca.required' => 'La marca proveedora es requerida',
                'nivel.required' => 'Nivel es requerido',
                'familia.required' => 'Nombre de familia es requerido',
                'subfamilia.required' => 'Nombre de producto requerido',               
                'impuestoiva.required' => 'El IVA es requerido',
                'impuestoiva.numeric' => 'El IVA debe ser un numero',
                'isa.required' => 'El Imp Saludable es requerido',
                'isa.numeric' => 'El ISA debe ser un numero',
                'impoconsumo.required' => 'Impoconsumo es requerido',
                'impoconsumo.numeric' => 'Impoconsumo debe ser un numero',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getReg = Product::firstWhere('id', $request->productoId);
            if ($getReg == null) {
                $prod = new Product();
                $prod->category_id = $request->categoria;
                $prod->brand_third_id = $request->marca;
                $prod->level_product_id = $request->nivel;
                $prod->unitofmeasure_id = $request->presentacion;
                $prod->meatcut_id = $request->familia;
                $prod->name = $request->subfamilia;
                $prod->code = $request->code;
                $prod->barcode = $request->codigobarra;               
                $prod->iva = $request->impuestoiva;
                $prod->otro_impuesto = $request->isa;
                $prod->impoconsumo = $request->impoconsumo;
                $prod->status = '1'; // Activo
                $prod->save();
                $this->CrearProductoEnCentroCosto();
                $this->CrearProductoEnListapreciodetalle();
                return response()->json([
                    'status' => 1,
                    'message' => "Producto: " . $prod->name . ' ' . 'Creado con ID: ' . $prod->id,
                    "registroId" => $prod->id
                ]);
            } else {
                $updateProd = Product::firstWhere('id', $request->productoId);
                $updateProd->category_id = $request->categoria;
                $updateProd->brand_third_id = $request->marca;
                $updateProd->level_product_id = $request->nivel;
                $updateProd->unitofmeasure_id = $request->presentacion;
                $updateProd->meatcut_id = $request->familia;
                $updateProd->name = $request->subfamilia;
                $updateProd->code = $request->code;
                $updateProd->barcode = $request->codigobarra;                
                $updateProd->iva = $request->impuestoiva;
                $updateProd->otro_impuesto = $request->isa;
                $updateProd->impoconsumo = $request->impoconsumo;
                $updateProd->save();

                return response()->json([
                    "status" => 1,
                    "message" => "Producto: " . $updateProd->name . ' ' . 'Editado con ID: ' . $updateProd->id,
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
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */

    public function CrearProductoEnCentroCosto()
    {
        // Obtener el último ID del producto
        $ultimoId = Product::latest('id')->first()->id;

        // Obtener todos los centros de costo Centrocosto::all(); 
        $centrosCosto = Centrocosto::whereIn('id', [1, 2])->orderBy('id', 'asc')->get();

        // Iterar sobre cada centro de costo y crear un producto
        foreach ($centrosCosto as $centroCosto) {
            Centro_costo_product::create([
                'centrocosto_id' => $centroCosto->id,
                'products_id' => $ultimoId,
                'tipoinventario' => 'inicial',
            ]);
        }
    }

    public function CrearProductoEnListapreciodetalle()
    {
        $ultimoId = Product::latest('id')->first()->id;
        $listaprecios = Listaprecio::all();
        foreach ($listaprecios as $listaprecio) {
            $listapreciodetalle = Listapreciodetalle::create([
                'listaprecio_id' => $listaprecio->id,
                'product_id' => $ultimoId,
            ]);
            $listapreciodetalle->save();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productos = Product::where('id', $id)->first();
        return response()->json([
            "id" => $id,
            "listadoproductos" => $productos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caja $caja)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caja $caja)
    {
        //
    }
}
