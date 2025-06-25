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
            ->join('centro_costo as c', 'cajas.centrocosto_id', '=', 'c.id')
            ->select('cajas.*', 'c.name as namecentrocosto', 'u.name as namecajero')
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

        $salidaefectivo = $request->input('salidaefectivo');
        $salidaefectivo = str_replace(['.', ',', '$', '#'], '', $salidaefectivo);

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
            $caja->retiro_caja = $salidaefectivo;
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
        $cajas = Caja::all();
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
        $excludeCentroCostoId = 13; // Ejemplo: excluir el centro de costo con id 3

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


        return view("caja.index", compact('usuario', 'cajas', 'centroCostoUser', 'defaultCentroCostoId'));
    }

    /**
     * Muestra la vista para el cierre/cuadre de caja usando la relación salesByCajero.
     *
     * @param  int  $id  Identificador de la caja
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        // Obtener la caja junto con las ventas del turno vigente del cajero
        $caja = Caja::with(['sales' => function ($query) {
            // $query->turnoVigente();
        }])->find($id);

        if (!$caja) {
            return redirect()->back()->with('error', 'Caja no encontrada.');
        }

        if ($caja->salesByCajero->isEmpty()) {
            return redirect()->back()->with('warning', 'El cajero no tiene ventas asociadas en la tabla sales.');
        }

        // Ordenar las ventas por fecha de creación (ascendente)
        $ventas = $caja->sales->sortBy('created_at');

        if ($ventas->isEmpty()) {
            return redirect()->back()->with('warning', 'El cajero no tiene ventas asociadas en el turno vigente.');
        }

        // 3) Ordeno y extraigo consecutivos protegiéndome contra null
        $ventasOrdenadas  = $ventas->sortBy('created_at');
        $facturaInicial   = $ventasOrdenadas->first()?->consecutivo;
        $facturaFinal     = $ventasOrdenadas->last()?->consecutivo;

        if (is_null($facturaInicial) || is_null($facturaFinal)) {
            return redirect()->back()->with('error', 'No se pudo determinar el consecutivo de las facturas.');
        }


        // Actualizar los campos en la caja
        $caja->update([
            'cantidad_facturas' => $ventas->count(),
            'factura_inicial'   => $facturaInicial,
            'factura_final'     => $facturaFinal,
        ]);

        // Calcular totales usando la relación salesByCajero
        $arrayTotales = $this->sumTotales($caja);

        // Pasar la caja actualizada y los totales a la vista
        return view('caja.create', compact('caja', 'arrayTotales'));
    }

    /**
     * Calcula los totales de ventas en efectivo, tarjetas, otros y crédito para la caja
     * utilizando la relación salesByCajero.
     *
     * @param  \App\Models\caja\Caja  $caja
     * @return array
     */
    protected function sumTotales(Caja $caja)
    {
        // Obtener las ventas del cajero para el turno vigente
        // $ventas = $caja->salesByCajero()->whereDate('fecha_cierre', now()->toDateString())->get();

        // Extraemos sólo la parte Y-m-d de la fecha de inicio del turno
        $fechaInicio = $caja->fecha_hora_inicio->toDateString(); // ej. "2025-05-12"

        // Filtramos las ventas de este cajero en esa misma fecha de cierre
        $ventas = $caja
            ->sales()
            ->whereDate('fecha_cierre', $fechaInicio)
            ->whereIn('status', ['1', '3'])
            ->get();

        $valorApagarEfectivo = $ventas->sum('valor_a_pagar_efectivo');
        $valorCambio         = $ventas->sum('cambio');

        // efectivo neto antes de retiros
        $valorEfectivoBruto  = $valorApagarEfectivo - $valorCambio;

        $valorEfectivo       = $valorApagarEfectivo - $valorCambio;
        $valorApagarTarjeta  = $ventas->sum('valor_a_pagar_tarjeta');
        $valorApagarOtros    = $ventas->sum('valor_a_pagar_otros');
        $valorApagarCredito  = $ventas->sum('valor_a_pagar_credito');

        $valorTotal          = $valorApagarTarjeta + $valorApagarOtros + $valorApagarCredito;

        // 4) Suma de todos los retiros de efectivo (vr_efectivo)
        $valorTotalSalidaEfectivo = $caja
            ->salidasEfectivo()
            ->sum('vr_efectivo');

        // 5) Calculamos el efectivo disponible descontando retiros
        $valorEfectivoNeto = $valorEfectivoBruto - $valorTotalSalidaEfectivo;


        return [
            'valorApagarEfectivo' => $valorApagarEfectivo,
            'valorCambio'         => $valorCambio,
            'valorEfectivo'       => $valorEfectivo,
            'valorApagarTarjeta'  => $valorApagarTarjeta,
            'valorApagarOtros'    => $valorApagarOtros,
            'valorApagarCredito'  => $valorApagarCredito,
            'valorTotal'          => $valorTotal,
            'valorTotalSalidaEfectivo'   => $valorTotalSalidaEfectivo,
        ];
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
                $alist->centrocosto_id = $request->centrocosto;
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
            ->join('centro_costo as s', 'c.centrocosto_id', '=', 's.id')
            ->select('c.*', 's.name as namecentrocosto', 'u.name as namecajero')
            ->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('fecha1', function ($row) {
                return Carbon::parse($row->fecha_hora_inicio)->format('M-d. H:i');
            })
            ->addColumn('fecha2', function ($row) {
                return Carbon::parse($row->fecha_hora_cierre)->format('M-d. H:i');
            })
            ->addColumn('inventory', function ($row) {
                return $row->estado === 'close'
                    ? '<span class="badge bg-warning">Cerrado</span>'
                    : '<span class="badge bg-success">Abierto</span>';
            })
            ->addColumn('action', function ($row) {
                // Inicializamos el contenedor
                $btn = '<div class="text-center">';

                // Botones comunes
                $btnPdf       = '<a href="' . route('caja.pdfCierre', $row->id) . '" class="btn btn-dark" title="PDF" target="_blank"><i class="far fa-file-pdf"></i></a>';
                $btnRecibo    = '<a href="' . route('caja.showRecibo', $row->id) . '" class="btn btn-dark" title="Recibo" target="_blank"><i class="fas fa-eye"></i></a>';
                $btnReporte   = '<button class="btn btn-warning" title="Reporte" onclick="openReport(' . $row->id . ');">RC</button>';
                $btnResumen   = '<a href="' . route('caja.resumenDiario', $row->id) . '" class="btn btn-danger" title="Resumen" target="_blank"><i class="far fa-file-pdf"></i></a>';
                $btnApertura  = '<a href="' . route('caja.create', $row->id) . '" class="btn btn-dark" title="Cuadre Caja"><i class="fas fa-money-check-alt"></i></a>';

                if ($row->status == 1) {
                    // Caja ya cerrada
                    $btn .= $btnPdf
                        . $btnRecibo
                        . $btnReporte
                        . $btnResumen;
                } elseif ($row->status === 0) {
                    // Caja abierta, pendiente de cierre
                    $btn .= $btnApertura
                        . $btnPdf
                        . $btnReporte;
                } else {
                    // Otro estado (por ejemplo, status == 2)
                    $btn .= $btnRecibo
                        . $btnReporte
                        . $btnResumen;
                }

                // Cerramos el contenedor
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['fecha1', 'fecha2', 'inventory', 'action'])
            ->make(true);
    }
}
