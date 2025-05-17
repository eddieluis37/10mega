<?php

namespace App\Http\Controllers;

use App\Models\caja\Caja;
use App\Models\Formapago;
use Illuminate\Http\Request;

class ReporteCierreCajaController extends Controller
{
    public function show($id)
    {
        // 1. Traer la caja y sus ventas activas con relaciones
        $caja = Caja::with([
            'sales' => function ($query) {
                $query->whereIn('status', ['1', '3']); // para incluir status 1 y 3
            },
            'sales.tercero',
            'sales.formaPagoTarjeta',
            'sales.formaPagoCredito',
        ])->findOrFail($id);

        // 2. Todas las formas de pago de tipo TARJETA
        $tarjetas = Formapago::where('tipoformapago', 'TARJETA')->get();

        // 3. Totales generales
        $totalFactura  = $caja->sales->sum('total_valor_a_pagar');
        // Efectivo neto = efectivo recibido menos cambio entregado
        $totalEfectivo = $caja->sales->sum('valor_a_pagar_efectivo')
            - $caja->sales->sum('cambio');
        $totalCambio   = $caja->sales->sum('cambio');

        // 4. Totales por tarjeta (agrupados por ID de formaPago)
        $totalesTarjeta = $caja->sales
            ->filter(fn($s) => $s->formaPagoTarjeta)
            ->groupBy(fn($s) => $s->formaPagoTarjeta->id)
            ->map(fn($group) => $group->sum('valor_a_pagar_tarjeta'))
            ->toArray();

        // 5. Filtrar solo las tarjetas que en totalesTarjeta tienen > 0
        $activeTarjetas = $tarjetas->filter(
            fn($t) => (isset($totalesTarjeta[$t->id]) && $totalesTarjeta[$t->id] > 0)
        );

        // 6. Total y bandera para CRÉDITO
        $totalCredito = $caja->sales->sum('valor_a_pagar_credito');
        $showCredito  = ($totalCredito > 0);

        // 7. Pasar todo a la vista
        return view('reportes.cierre_caja', compact(
            'caja',
            'tarjetas',
            'activeTarjetas',
            'totalFactura',
            'totalEfectivo',
            'totalCambio',
            'totalesTarjeta',
            'totalCredito',
            'showCredito'
        ));
    }
}
