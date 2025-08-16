<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\caja\Caja as Caja;
use App\Models\Recibodecaja;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;


class resumenDiarioController extends Controller
{
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

        // Pasar la caja actualizada y los totales a la vista del formulario de cierre (no PDF)
        return view('caja.create', compact('caja', 'arrayTotales'));
    }

    /**
     * Genera el resumen diario en PDF para la caja (usa sumTotales para los cálculos).
     *
     * @param int $id
     * @return \Illuminate\Http\Response (PDF stream)
     */
    public function resumenDiario($id)
    {
        // Cargo la caja con lo necesario
        $caja = Caja::with(['centroCosto', 'cajero', 'salidasEfectivo'])->findOrFail($id);

        // Obtener totales ya ajustados por notas de crédito
        $arrayTotales = $this->sumTotales($caja);

        // Recibos de caja para esta caja, filtrados por user_id y fecha_elaboracion = fecha inicio del turno
        $recibos = Recibodecaja::with([
            'user',
            'third',
            'details.paymentMethod',
            'details.cuentaPorCobrar.sale.third'
        ])
            ->where('user_id', $caja->user_id)
            ->whereDate('fecha_elaboracion', $caja->fecha_hora_inicio)
            ->get()
            ->map(function ($r) {
                $r->vr_total_pago   = $r->details->sum('vr_pago');
                $r->nvo_total_saldo = $r->details->sum('nvo_saldo');
                return $r;
            });

        $totalRecibos = $recibos->sum('vr_total_pago');

        // Agrupación de pagos POR FORMA (across all recibos)
        $pagosPorForma = $recibos
            ->flatMap(fn($r) => $r->details)
            ->groupBy(fn($det) => $det->paymentMethod?->nombre ?? 'Otro')
            ->map(fn($group, $forma) => [
                'forma' => $forma,
                'total' => $group->sum('vr_pago')
            ])
            ->values();

        // Aplanar todos los detalles de todos los recibos
        $allDetails = $recibos->flatMap(fn($r) => $r->details);

        // Recaudo efectivo y electrónicos en recibos
        $detallesEfectivo = $allDetails
            ->filter(
                fn($det) =>
                strcasecmp($det->paymentMethod?->nombre ?? '', 'EFECTIVO') === 0
            );
        $totalRecaudoPagoEfectivo = $detallesEfectivo->sum('vr_pago');

        $detallesElectronicos = $allDetails
            ->filter(
                fn($det) =>
                strcasecmp($det->paymentMethod?->nombre ?? '', 'CODIGO QR') === 0
                    || stripos($det->paymentMethod?->nombre ?? '', 'TARJETA') !== false
            );
        $recaudoPagoElectronicos = $detallesElectronicos->sum('vr_pago');

        // Salidas y gastos
        $salidas     = $caja->salidasEfectivo;
        $totalGastos = $salidas->sum('vr_efectivo');

        // Fecha cierre (si existe fecha_hora_cierre) en español
        Carbon::setLocale('es');
        $fechaCierre = $caja->fecha_hora_cierre
            ? $caja->fecha_hora_cierre->isoFormat('dddd, D [de] MMMM [de] YYYY')
            : Carbon::now()->isoFormat('dddd, D [de] MMMM [de] YYYY');

        // Renderizo PDF, paso arrayTotales y datos auxiliares
        return PDF::loadView('caja.resumenDiario', compact(
            'caja',
            'arrayTotales',
            'recibos',
            'totalRecibos',
            'pagosPorForma',
            'totalRecaudoPagoEfectivo',
            'recaudoPagoElectronicos',
            'salidas',
            'totalGastos',
            'fechaCierre'
        ))->stream("resumen_caja_{$caja->id}.pdf");
    }

    /**
     * Calcula los totales de ventas (ajustados por notas de crédito) para la caja
     *
     * @param  \App\Models\Caja  $caja
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

        // --- 2) Totales de devolución por cada forma de pago (por id) ---
        $totalesDevolucion = [];
        foreach ($creditForms as $fp) {
            $totalesDevolucion[$fp->id] = $ventas
                ->filter(fn($s) => $s->notacredito && $s->notacredito->formaPago->id === $fp->id)
                ->sum(fn($s) => $s->notacredito->total);
        }

        // --- 2b) Crear también un array con nombre de forma + total (útil para la vista) ---
        $totalesDevolucionPorNombre = [];
        foreach ($creditForms as $fp) {
            $id   = $fp->id;
            // intenta obtener el nombre; si no existe, usa un fallback
            $nombre = $fp->nombre ?? $fp->name ?? ('Forma #' . $id);
            $total  = $totalesDevolucion[$id] ?? 0;

            $totalesDevolucionPorNombre[] = [
                'id'     => $id,
                'nombre' => $nombre,
                'total'  => $total,
            ];
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
                    // Si hay tipos personalizados, no los tocamos aquí
                    break;
            }
        }

        // Re-calcular total general (después de devoluciones)
        $valorTotal = max(0, $valorApagarEfectivo + $valorApagarTarjeta + $valorApagarOtros + $valorApagarCredito);

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
            'totalesDevolucion_porForma'        => $totalesDevolucion,        // [formaPagoId => sumaDevoluciones]
            'totalesDevolucion_porForma_nombre' => $totalesDevolucionPorNombre, // [ ['id','nombre','total'], ... ]
            'totalesTarjeta_porForma'           => $totalesTarjeta,
        ];
    }
}
