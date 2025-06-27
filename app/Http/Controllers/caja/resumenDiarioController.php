<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;
use App\Models\caja\Caja;
use App\Models\Recibodecaja;
use App\Models\compensado\Compensadores;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class resumenDiarioController extends Controller
{
    public function resumenDiario($id)
    {
        // 1) Cargo la caja con las relaciones necesarias:
        $caja = Caja::with([
            'user',
            'cajero',
            'centroCosto',
            'salidasEfectivo'
        ])->findOrFail($id);

        // 2) Cargo **todas** las ventas de este turno, eager-load de third:
        $ventas = $caja->sales()
            ->with('third')                   // trae el cliente
            ->get();

        // 3) Totales generales:
        $sumEfectivo = $ventas->sum('valor_a_pagar_efectivo');
        $valorCambio = $ventas->sum('cambio');
        $valorEfectivo = $sumEfectivo - $valorCambio;
        $sumQR      = $ventas->sum('valor_a_pagar_tarjeta');
        $sumCredito = $ventas->sum('valor_a_pagar_credito');

        // 4) Ventas a crédito: filtro por monto > 0
        $ventasCredito = $ventas
            ->filter(fn($v) => $v->valor_a_pagar_credito > 0)
            ->values();  // reindexa 0,1,2…

        // 5) Monto total de créditos
        $totalCreditos = $ventasCredito->sum('valor_a_pagar_credito');

        // 6) Preparo el array para la vista
        $creditos = $ventasCredito->map(fn($v) => [
            'cliente' => $v->third?->name ?? '—',
            'monto'   => $v->valor_a_pagar_credito,
        ]);

        // 1) Cargo la caja
        $caja = Caja::findOrFail($id);

        // … tus cálculos de ventas, créditos, etc. …

        // 5) Recibos de caja para esta caja, filtrados por user_id y fecha_elaboracion = hoy
        $hoy = Carbon::today()->toDateString();

        $recibos = Recibodecaja::with([
            'user',
            'third',
            'details.paymentMethod',
            'details.cuentaPorCobrar.sale.third'
        ])
            ->where('user_id', $caja->user_id)         // 1) solo del mismo user que abrió la caja
            ->whereDate('fecha_elaboracion', $caja->fecha_hora_inicio)     // 2) solo los recibos de hoy
            ->get()
            ->map(function ($r) {
                $r->vr_total_pago   = $r->details->sum('vr_pago');
                $r->nvo_total_saldo = $r->details->sum('nvo_saldo');
                return $r;
            });

        $totalRecibos = $recibos->sum('vr_total_pago');

        // 3) **Agrupación de pagos POR FORMA** (across all recibos)
        $pagosPorForma = $recibos
            ->flatMap(fn($r) => $r->details)
            ->groupBy(fn($det) => $det->paymentMethod?->nombre ?? 'Otro')
            ->map(fn($group, $forma) => [
                'forma' => $forma,
                'total' => $group->sum('vr_pago')
            ])
            ->values();

        // 7) Salidas
        $salidas     = $caja->salidasEfectivo;
        $totalGastos = $salidas->sum('vr_efectivo');

        // 1) Aplanar todos los detalles de todos los recibos
        $allDetails = $recibos->flatMap(fn($r) => $r->details);

        // 2) Filtrar solo los que sean EFECTIVO
        $detallesEfectivo = $allDetails
            ->filter(fn($det) => $det->paymentMethod?->nombre === 'EFECTIVO');

        // 3) Sumar vr_pago de esos detalles
        $totalRecaudoPagoEfectivo = $detallesEfectivo->sum('vr_pago');

        // 8) Cálculos finales
        $totalVenta         = $valorEfectivo + $sumQR + $sumCredito;
        $totalEfectivoCaja  = $caja->base + $valorEfectivo;
        $efectivoAEntregar  = $totalEfectivoCaja - $totalGastos;
        $totalPagosConQR    = $sumQR;

        // 9) Fecha en español
        Carbon::setLocale('es');
        $fechaCierre = $caja->fecha_hora_cierre->isoFormat('dddd, D [de] MMMM [de] YYYY');

        // 10) Renderizo PDF
        return PDF::loadView('caja.resumenDiario', compact(
            'caja',
            'valorEfectivo',
            'sumQR',
            'sumCredito',
            'totalVenta',
            'creditos',
            'totalCreditos',
            'recibos',
            'totalRecibos',
            'totalRecaudoPagoEfectivo',
            'pagosPorForma',
            'salidas',
            'totalGastos',
            'totalEfectivoCaja',
            'efectivoAEntregar',
            'totalPagosConQR',
            'fechaCierre'
        ))->stream("resumen_caja_{$caja->id}.pdf");
    }
}
