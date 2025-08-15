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
        // Obtener la caja junto con relaciones necesarias para evitar N+1
        $caja = Caja::with([
            'sales.notacredito.formaPago',
            'sales.formaPagoTarjeta',
            'salidasEfectivo'
        ])->find($id);

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

        // Calcular totales usando la relación salesByCajero (ahora con devoluciones descontadas)
        $arrayTotales = $this->sumTotales($caja);

        // Pasar la caja actualizada y los totales a la vista
        return view('caja.create', compact('caja', 'arrayTotales'));
    }

    /**
     * Calcula los totales de ventas (ajustados por notas de crédito) para la caja
     *
     * @param  \App\Models\caja\Caja  $caja
     * @return array
     */
    protected function sumTotales(Caja $caja)
    {
        // Asegúrate que las relaciones estén cargadas
        $caja->loadMissing(['sales.notacredito.formaPago', 'sales.formaPagoTarjeta', 'salidasEfectivo']);

        // Fecha inicio del turno (Y-m-d)
        $fechaInicio = $caja->fecha_hora_inicio->toDateString();

        // Filtramos las ventas de este cajero en esa fecha y estados válidos
        $ventas = $caja
            ->sales()
            ->whereDate('fecha_cierre', $fechaInicio)
            ->whereIn('status', ['1', '3'])
            ->get();

        // Totales brutos de ventas (antes de devoluciones)
        $valorApagarEfectivo = $ventas->sum('valor_a_pagar_efectivo');
        $valorCambio         = $ventas->sum('cambio');
        $valorEfectivoBruto  = $valorApagarEfectivo - $valorCambio;

        $valorApagarTarjeta  = $ventas->sum('valor_a_pagar_tarjeta');
        $valorApagarOtros    = $ventas->sum('valor_a_pagar_otros');
        $valorApagarCredito  = $ventas->sum('valor_a_pagar_credito');

        // Totales por forma de tarjeta (agrupados por ID de formaPago)
        $totalesTarjeta = $ventas
            ->filter(fn($s) => $s->formaPagoTarjeta)
            ->groupBy(fn($s) => $s->formaPagoTarjeta->id)
            ->map(fn($group) => $group->sum('valor_a_pagar_tarjeta'))
            ->toArray();

        // --- 1) Determinar todas las formas de pago usadas en las notas de crédito ---
        $creditForms = $ventas
            ->pluck('notacredito')      // colección de Notacredito|null
            ->filter()                  // remueve nulls
            ->pluck('formaPago')        // colección de Formapago
            ->filter()                  // por si alguno es null
            ->unique('id')
            ->values();

        // --- 2) Totales de devolución por cada forma de pago (notas de crédito) ---
        $totalesDevolucion = [];
        foreach ($creditForms as $fp) {
            $totalesDevolucion[$fp->id] = $ventas
                ->filter(fn($s) => $s->notacredito && $s->notacredito->formaPago->id === $fp->id)
                ->sum(fn($s) => $s->notacredito->total);
        }

        // --- 3) Restar devoluciones de los totales correspondientes ---
        foreach ($creditForms as $fp) {
            $devol = $totalesDevolucion[$fp->id] ?? 0;
            $tipo  = strtoupper($fp->tipoformapago ?? '');

            switch ($tipo) {
                case 'EFECTIVO':
                    // restar del efectivo
                    $valorApagarEfectivo = max(0, $valorApagarEfectivo - $devol);
                    $valorEfectivoBruto  = max(0, $valorEfectivoBruto - $devol);
                    break;

                case 'TARJETA':
                    // restar del total de tarjetas y del bucket del ID de tarjeta
                    $valorApagarTarjeta = max(0, $valorApagarTarjeta - $devol);
                    if (isset($totalesTarjeta[$fp->id])) {
                        $totalesTarjeta[$fp->id] = max(0, $totalesTarjeta[$fp->id] - $devol);
                    }
                    break;

                case 'OTROS':
                case 'CHEQUE':
                    $valorApagarOtros = max(0, $valorApagarOtros - $devol);
                    break;

                case 'CREDITO':
                    $valorApagarCredito = max(0, $valorApagarCredito - $devol);
                    break;

                default:
                    // Si hay tipos personalizados, intenta deducirlos por campos comunes.
                    // Como fallback, resta del valorTotal (se recalculará luego).
                    // No hacemos nada aquí específico.
                    break;
            }
        }

        // Re-calcular total general (después de devoluciones)
        $valorTotal = max(0, $valorApagarTarjeta + $valorApagarOtros + $valorApagarCredito);

        // 4) Suma de todos los retiros de efectivo (vr_efectivo)
        $valorTotalSalidaEfectivo = $caja
            ->salidasEfectivo()
            ->sum('vr_efectivo');

        // 5) Calculamos el efectivo disponible descontando retiros y devoluciones en efectivo ya descontadas arriba
        $valorEfectivoNeto = max(0, ($valorEfectivoBruto) - $valorTotalSalidaEfectivo);

        return [
            // valores ajustados
            'valorApagarEfectivo'        => $valorApagarEfectivo,
            'valorCambio'                => $valorCambio,
            'valorEfectivo'              => $valorEfectivoBruto,
            'valorApagarTarjeta'         => $valorApagarTarjeta,
            'valorApagarOtros'           => $valorApagarOtros,
            'valorApagarCredito'         => $valorApagarCredito,
            'valorTotal'                 => $valorTotal,
            'valorTotalSalidaEfectivo'   => $valorTotalSalidaEfectivo,
            'valorEfectivoNeto'          => $valorEfectivoNeto,          
            'totalesDevolucion_porForma' => $totalesDevolucion, // [formaPagoId => sumaDevoluciones]
            'totalesTarjeta_porForma'    => $totalesTarjeta,    // [formaPagoTarjetaId => totalTarjetaDespuesDevolucion]
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

        // 1) IDs de los centros de costo del usuario autenticado
        $centroIds = Auth::user()
            ->stores
            ->pluck('centrocosto_id')
            ->unique();

        $data = DB::table('cajas as c')
            ->join('users as u', 'c.cajero_id', '=', 'u.id')
            ->join('centro_costo as s', 'c.centrocosto_id', '=', 's.id')
            ->select('c.*', 's.name as namecentrocosto', 'u.name as namecajero')
            ->whereIn('c.centrocosto_id', $centroIds)
            // Filtro: solo las cajas creadas por el usuario autenticado
            ->where('c.user_id', Auth::id())
            ->orderBy('c.id', 'desc')
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
