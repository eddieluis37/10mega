<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;

use App\Models\caja\Caja;
use App\Models\Category;
use App\Models\centros\Centrocosto;
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
use App\Models\shopping\shopping_enlistment;
use App\Models\shopping\shopping_enlistment_details;
use App\Models\Store;

class cajaController extends Controller
{

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
            ->join('store as s', 'cajas.store_id', '=', 's.id')
            ->select('cajas.*', 's.name as namecentrocosto', 'u.name as namecajero')
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

    public function index()
    {
        $category = Category::WhereIn('id', [1, 2, 3])->get();
        // Obtener el usuario autenticado
        $authUser = Auth::user();

        // En este ejemplo, encapsulamos el usuario autenticado en un array para poder iterarlo en la vista.
        // Si en el futuro se requieren varios usuarios, el arreglo podrá contener más elementos.
        $usuario = [$authUser];



        $user = auth()->user(); // Obtener el usuario autenticado        
        // Obtener solo las bodegas asociadas al usuario en store_user
        $bodegaUser = Store::whereIn('id', function ($query) use ($user) {
            $query->select('store_id')
                ->from('store_user')
                ->where('user_id', $user->id);
        })
            ->whereNotIn('id', [40]) // Excluir bodegas específicas si aplica
            ->orderBy('name', 'asc')
            ->get();

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Definir el id del centro de costo a excluir (puede venir del request o estar definido estáticamente)
        $excludeCentroCostoId = 3; // Ejemplo: excluir el centro de costo con id 3

        // Obtener los IDs de centro de costo asociados a las tiendas del usuario
        $centroCostoIds = $user->stores->pluck('centrocosto_id')->unique();

        // Consultar los centros de costo, excluyendo el id indicado
        $centroCostoUser = CentroCosto::whereIn('id', $centroCostoIds)
            ->when($excludeCentroCostoId, function ($query, $excludeCentroCostoId) {
                return $query->where('id', '<>', $excludeCentroCostoId);
            })
            ->get();

        // Seleccionar de forma predeterminada el primer centro de costo (si existe)
        $defaultCentroCostoId = $centroCostoUser->first() ? $centroCostoUser->first()->id : null;


        return view("caja.index", compact('usuario', 'category', 'centroCostoUser', 'defaultCentroCostoId'));
    }

    /**
     * Show the form for creating a new resource.
     *    ->whereDate('sa.fecha_venta', now())
     * @return \Illuminate\Http\Response
     * 
     * /*   $valorApagarEfectivo = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.store_id', '=', 'centro.id')
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
            ->join('store as s', 'ca.store_id', '=', 's.id')
            ->select('ca.*', 's.name as namecentrocosto', 'u.name as namecajero')
            ->where('ca.id', $id)
            ->get();

        if ($dataAlistamiento->isEmpty()) {
            return redirect()->back()->with('warning', 'El cajero no tiene ventas asociadas en la tabla sales.');
        }

        $status = '';
        $estadoVenta = $dataAlistamiento[0]->status ? 'true' : 'false';

        //Suma el total de efectivo, totalTarjetas, totalOtros de la venta del día de ese cajero.
        $arrayTotales = $this->sumTotales($id);

        return view('caja.create', compact('dataAlistamiento', 'status', 'arrayTotales'));
    }

    public function sumTotales($id)
    {
        $valorApagarEfectivo = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.store_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.valor_a_pagar_efectivo');

        $valorCambio = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.store_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.cambio');

        $valorEfectivo = $valorApagarEfectivo - $valorCambio;

        $valorApagarTarjeta = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.store_id', '=', 'centro.id')
            ->where('ca.id', $id)
            ->whereDate('sa.fecha_venta', now())
            ->where('sa.tipo', '0')
            ->sum('sa.valor_a_pagar_tarjeta');

        $valorApagarOtros = DB::table('cajas as ca')
            ->join('sales as sa', 'ca.cajero_id', '=', 'sa.user_id')
            ->join('users as u', 'ca.cajero_id', '=', 'u.id')
            ->join('centro_costo as centro', 'ca.store_id', '=', 'centro.id')
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
        // Limpia el campo "base" (elimina puntos y otros caracteres no numéricos)
        $cleanBase = str_replace('.', '', $request->base);

        try {
            $rules = [
                'alistamientoId' => 'required',
                'cajero' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $currentDate = Carbon::now()->format('Y-m-d');
                        $existingRecord = Caja::where('cajero_id', $value)
                            ->whereDate('fecha_hora_inicio', $currentDate)
                            ->exists();
                        if ($existingRecord) {
                            $fail('Ya existe un turno para el cajero en la fecha actual');
                        }
                    },
                ],
                'centrocosto' => 'required',
                'base' => 'required',
            ];

            $messages = [
                'alistamientoId.required' => 'El alistamiento es requerido',
                'cajero.required' => 'El cajero es requerido',
                'centrocosto.required' => 'El centro de costo es requerido',
                'base.required' => 'La base es requerida',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }
            /* 
            $current_date->modify('next monday'); // Move to the next Monday
                $dateNextMonday = $current_date->format('Y-m-d'); // Output the date in Y-m-d format */

            $getReg = Caja::firstWhere('id', $request->alistamientoId);
            if ($getReg == null) {
                $currentDateTime = Carbon::now();
                $currentDateFormat = Carbon::parse($currentDateTime->format('Y-m-d'));
                $current_date = Carbon::parse($currentDateTime->format('Y-m-d'));
                $fechaHoraCierre =  $current_date->addHours(23);

                $fechaalistamiento = $request->fecha1;
                $id_user = Auth::user()->id;
                $alist = new Caja();
                $alist->user_id = $id_user;
                $alist->store_id = $request->centrocosto;
                $alist->cajero_id = $request->cajero;
                $alist->base = $cleanBase;
                //$alist->fecha_alistamiento = $currentDateFormat;
                $alist->fecha_hora_inicio = $currentDateTime;
                $alist->fecha_hora_cierre = $fechaHoraCierre;
                $alist->status = '0'; // Open
                $alist->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Guardado correctamente',
                    "registroId" => $alist->id
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
    public function show()
    {
        $data = DB::table('cajas as c')
            ->join('users as u', 'c.cajero_id', '=', 'u.id')
            ->join('stores as s', 'c.store_id', '=', 's.id')
            ->select('c.*', 's.name as namecentrocosto', 'u.name as namecajero')
            /*  ->where('c.status', 1) */
            ->get();
        //$data = Compensadores::orderBy('id','desc');
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('fecha1', function ($data) {
                $fecha1 = Carbon::parse($data->fecha_hora_inicio);
                $formattedDate1 = $fecha1->format('M-d. H:i');
                return $formattedDate1;
            })
            ->addColumn('fecha2', function ($data) {
                $fecha2 = Carbon::parse($data->fecha_hora_cierre);
                $formattedDate = $fecha2->format('M-d. H:i');
                return $formattedDate;
            })
            ->addColumn('inventory', function ($data) {
                if ($data->estado == 'close') {
                    $statusInventory = '<span class="badge bg-warning">Cerrado</span>';
                } else {
                    $statusInventory = '<span class="badge bg-success">Abierto</span>';
                }
                return $statusInventory;
            })

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
                         <a href="caja/pdfCierreCaja/' . $data->id . '" class="btn btn-dark" title="PdfCuadreCajaPendiente" target="_blank">
                         <i class="far fa-file-pdf"></i>
                         </a>
                      
                         <a href="caja/showReciboCaja/' . $data->id . '" class="btn btn-dark" title="RecibodeCajaCerrado" target="_blank">
                         <i class="fas fa-eye"></i>
                         </a>				
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Caja  $caja
     * @return \Illuminate\Http\Response
     */
    public function edit(Caja $caja)
    {
        //
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
