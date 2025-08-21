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
            'sales.notacredito.formaPago',
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

        // 4. Totales generales
        $totalFactura  = $caja->sales->sum('total_valor_a_pagar');
        $totalEfectivo = $caja->sales->sum('valor_a_pagar_efectivo') - $caja->sales->sum('cambio');
        $totalCambio   = $caja->sales->sum('cambio');

        // 5. Reconstruir totales por tarjeta por posición (más fiable que groupBy mixto)
        $totalesTarjeta = []; // estructura: [tarjeta_id => ['tarjeta1' => x,'tarjeta2'=>y,'tarjeta3'=>z]]
        foreach ($caja->sales as $s) {
            if ($s->formaPagoTarjeta) {
                $id = $s->formaPagoTarjeta->id;
                $totalesTarjeta[$id]['tarjeta1'] = ($totalesTarjeta[$id]['tarjeta1'] ?? 0) + ($s->valor_a_pagar_tarjeta ?? 0);
            }
            if ($s->formaPagoTarjeta2) {
                $id = $s->formaPagoTarjeta2->id;
                $totalesTarjeta[$id]['tarjeta2'] = ($totalesTarjeta[$id]['tarjeta2'] ?? 0) + ($s->valor_a_pagar_tarjeta2 ?? 0);
            }
            if ($s->formaPagoTarjeta3) {
                $id = $s->formaPagoTarjeta3->id;
                $totalesTarjeta[$id]['tarjeta3'] = ($totalesTarjeta[$id]['tarjeta3'] ?? 0) + ($s->valor_a_pagar_tarjeta3 ?? 0);
            }
        }

        // 6. Obtener sólo las tarjetas que realmente tienen totales > 0 y además saber qué posiciones mostrar
        $activeTarjetasIds = [];
        $tarjetaColumns = []; // [tarjeta_id => ['tarjeta1','tarjeta2',...]]
        foreach ($totalesTarjeta as $tid => $positions) {
            $cols = [];
            if (($positions['tarjeta1'] ?? 0) > 0) $cols[] = 'tarjeta1';
            if (($positions['tarjeta2'] ?? 0) > 0) $cols[] = 'tarjeta2';
            if (($positions['tarjeta3'] ?? 0) > 0) $cols[] = 'tarjeta3';
            if (count($cols)) {
                $activeTarjetasIds[] = $tid;
                $tarjetaColumns[$tid] = $cols;
            }
        }

        // traer modelos Formapago sólo de los ids activos, manteniendo orden y datos
        $activeTarjetas = Formapago::whereIn('id', $activeTarjetasIds)
            ->where('tipoformapago', 'TARJETA')
            ->get()
            ->keyBy('id');

        $totalCredito   = $caja->sales->sum('valor_a_pagar_credito');
        $showCredito    = $totalCredito > 0;

        // 7. Pasar todo a la vista
        return view('reportes.cierre_caja', compact(
            'caja',
            'activeTarjetas',   // collection keyed by id
            'tarjetaColumns',   // array con posiciones por tarjeta
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
