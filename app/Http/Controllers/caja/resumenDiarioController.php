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
        // 1) Cargar la caja con sus ventas pivot y salidas
        $caja = Caja::with([
            'user',           // quien abrió la caja
            'cajero',         // quien la cierra
            'centroCosto',    // centro de costo
            'sales',          // ventas asociadas (pivot sale_caja)
            'salidasEfectivo' // gastos / retiros
        ])->findOrFail($id);

        // 2) Extraer las ventas y agrupar por forma de pago
        $ventas     = $caja->sales;
        $sumEfectivo = $ventas->sum('valor_a_pagar_efectivo');
        $sumQR       = $ventas->where('forma_pago', 'TARJETA')->sum('total');
        $sumCredito  = $ventas->where('forma_pago', 'CREDITO')->sum('total');

        // 3) Detalle de clientes a crédito
        $creditos = $ventas
            ->where('forma_pago', 'CREDITO')
            ->map(fn($v) => [
                'cliente' => $v->cliente->name,
                'monto'   => $v->total
            ]);
        $totalCreditos = $sumCredito;

        // 4) Salidas de dinero (gastos/retiros)
        $salidas     = $caja->salidasEfectivo;
        $totalGastos = $salidas->sum('valor');

        // 5) Cálculos finales
        $totalVenta   = $sumEfectivo + $sumQR + $sumCredito;
        $totalEfectivoCaja  = $caja->base + $sumEfectivo;
        $efectivoAEntregar  = $totalEfectivoCaja - $totalGastos;
        $totalPagosConQR    = $sumQR;

        // 6) Formato de fecha en español
        Carbon::setLocale('es');
        $fechaCierre = $caja->fecha_hora_cierre->isoFormat('dddd, D [de] MMMM [de] YYYY');

        // 7) Renderizar PDF
        $pdf = PDF::loadView('caja.resumenDiario', compact(
            'caja',
            'sumEfectivo',
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
        ));

        return $pdf->stream("resumen_caja_{$caja->id}.pdf");
    }
}
