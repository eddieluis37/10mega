<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;
use App\Models\caja\Caja;
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

        // 7) Salidas
        $salidas     = $caja->salidasEfectivo;
        $totalGastos = $salidas->sum('valor');

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
            'salidas',
            'totalGastos',
            'totalEfectivoCaja',
            'efectivoAEntregar',
            'totalPagosConQR',
            'fechaCierre'
        ))->stream("resumen_caja_{$caja->id}.pdf");
    }
}
