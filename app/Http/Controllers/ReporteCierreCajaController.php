<?php

namespace App\Http\Controllers;

use App\Models\caja\Caja;
use App\Models\Formapago;
use Illuminate\Http\Request;

class ReporteCierreCajaController extends Controller
{
    public function showOriginal($id)
    {
        // 1. Traer la caja y sus ventas activas o con devoluciones y sus relaciones
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

    public function show($id)
    {
        // 1. Traer caja + ventas (status 1 y 3) + relaciones de cobros + notas de crédito
        $caja = Caja::with([
            'sales' => fn($q) => $q->whereIn('status', ['1', '3']),
            'sales.tercero',
            'sales.formaPagoTarjeta',
            'sales.formaPagoTarjeta2',
            'sales.formaPagoTarjeta3',
            'sales.formaPagoCredito',
            'sales.notacredito.formaPago',     // <-- nota de crédito y su formaPago
        ])->findOrFail($id);
        // 2. Determinar todas las formas de pago usadas en las notas de crédito de estas ventas
        $creditForms = $caja->sales
            ->pluck('notacredito')             // colección de Notacredito|null
            ->filter()                         // quita null
            ->pluck('formaPago')               // colección de Formapago
            ->unique('id')
            ->values();
        // 3. Totales por cada forma de pago de nota de crédito
        $totalesDevolucion = [];
        foreach ($creditForms as $fp) {
            $totalesDevolucion[$fp->id] = $caja->sales
                ->filter(fn($s) => $s->notacredito && $s->notacredito->formaPago->id === $fp->id)
                ->sum(fn($s) => $s->notacredito->total);
        }
        // 4. (Tus cálculos existentes de totales de factura, efectivo, cambio, tarjeta y crédito…)
        $totalFactura  = $caja->sales->sum('total_valor_a_pagar');
        $totalEfectivo = $caja->sales->sum('valor_a_pagar_efectivo') - $caja->sales->sum('cambio');
        $totalCambio   = $caja->sales->sum('cambio');
        $tarjetas      = Formapago::where('tipoformapago', 'TARJETA')->get();
        // 4. Totales por tarjeta (agrupados por ID de formaPago)
        $totalesTarjeta = $caja->sales
            ->filter(fn($s) => $s->formaPagoTarjeta || $s->formaPagoTarjeta2 || $s->formaPagoTarjeta3)
            ->groupBy(fn($s) => $s->formaPagoTarjeta->id ?? ($s->formaPagoTarjeta2->id ?? ($s->formaPagoTarjeta3->id)))
            ->map(fn($group) => [
                'tarjeta1' => $group->sum('valor_a_pagar_tarjeta'),
                'tarjeta2' => $group->sum('valor_a_pagar_tarjeta2'),
                'tarjeta3' => $group->sum('valor_a_pagar_tarjeta3'),
            ])
            ->toArray();
        // 5. Filtrar solo las tarjetas que en totalesTarjeta tienen > 0
        $activeTarjetas = $tarjetas->filter(
            fn($t) => (isset($totalesTarjeta[$t->id]) &&
                ($totalesTarjeta[$t->id]['tarjeta1'] > 0 ||
                    $totalesTarjeta[$t->id]['tarjeta2'] > 0 ||
                    $totalesTarjeta[$t->id]['tarjeta3'] > 0))
        );
        $totalCredito   = $caja->sales->sum('valor_a_pagar_credito');
        $showCredito    = $totalCredito > 0;
        // 5. Pasar todo a la vista
        return view('reportes.cierre_caja', compact(
            'caja',
            'tarjetas',
            'activeTarjetas',
            'totalFactura',
            'totalEfectivo',
            'totalCambio',
            'totalesTarjeta',
            'totalCredito',
            'showCredito',
            'creditForms',
            'totalesDevolucion'
        ));
    }
}
