<?php

namespace App\Http\Controllers\notacredito;

use App\Http\Controllers\Controller;
use App\Models\Notacredito;
use App\Models\NotacreditoDetail;
use App\Models\NotaCreditoDetalle;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sale;

class pdfNotacreditoController extends Controller
{
    public function showNotacredito($id)
    {
        // --- Mantengo tu consulta con joins / selects para obtener datos relevantes de la venta vinculada ---
        $sale = Notacredito::leftJoin('sales as sa', 'sa.id', '=', 'notacreditos.sale_id')
            ->join('thirds as third', 'sa.third_id', '=', 'third.id')
            ->join('users as u', 'sa.user_id', '=', 'u.id')
            ->join('centro_costo as centro', 'sa.centrocosto_id', '=', 'centro.id')
            ->select(
                'sa.*',
                'notacreditos.valor_total as nctotal',
                'notacreditos.resolucion as ncresolucion',
                'u.name as nameuser',
                'third.name as namethird',
                'third.identification',
                'third.direccion',
                'centro.name as namecentrocosto',
                'third.porc_descuento',
                'notacreditos.total_iva',
                'sa.vendedor_id'
            )
            ->where('notacreditos.id', $id)
            ->first(); // primero (un registro)

        // Si por seguridad quieres el modelo Notacredito completo (para campos propios de la NC como forma de pago),
        // lo cargamos aparte (no rompe tu join, solo lo usamos para campos de notacreditos)
        $ncModel = Notacredito::find($id);

        // --- Detalles de la nota crédito (ya tenías esto) ---
        $saleDetails = NotaCreditoDetalle::where('notacredito_id', $id)
            ->join('products as pro', 'notacredito_details.product_id', '=', 'pro.id')
            ->select('notacredito_details.*', 'pro.name as nameprod', 'pro.code')
            ->where('notacredito_details.notacredito_id', $id)
            ->get();

        // --- Preparar mapeo nombres formas de pago (ajusta o reemplaza por lookup real si tienes tabla) ---
        $paymentMethods = [
            1 => 'EFECTIVO',
            2 => 'TARJETA',
            3 => 'CREDITO',
            4 => 'TRANSFERENCIA',
            // agrega los que uses...
        ];

        // --- Cálculo por línea y totales agregados ---
        $detalleItems = [];
        $totales = [
            'subtotal_base' => 0.0,   // sum(unit_neto * qty) o según prefieras
            'descuento' => 0.0,
            'iva' => 0.0,
            'ultra' => 0.0,           // UP (otro impuesto / ultraprocesados)
            'impoconsumo' => 0.0,
            'total_devolver' => 0.0,
        ];

        foreach ($saleDetails as $d) {
            $cantidad = (float) ($d->quantity ?? 0);

            // Campos unitarios: adapta nombres según tu tabla notacredito_details
            $unitario = (float) ($d->unit_price ?? $d->valor_unitario ?? 0);
            $unit_descuento = (float) ($d->unit_descuento ?? 0);

            // Si no tienes unit_descuento, usa descuento_total prorrateado por qty
            if (empty($unit_descuento) && !empty($d->descuento_total) && $cantidad > 0) {
                $unit_descuento = round((float)$d->descuento_total / $cantidad, 4);
            }

            // unit_bruto (base unitaria) — normalmente el precio unitario
            $unit_bruto = $unitario;

            // unit_neto = bruto - descuento_unitario
            $unit_neto = max(0.0, round($unit_bruto - $unit_descuento, 4));

            // tasas (usa las tasas snapshot si las guardaste en notacredito_details)
            $porcIva = (float) ($d->porc_iva ?? 0);
            $porcOtro = (float) ($d->porc_otro_impuesto ?? $d->porc_otro_impuesto ?? 0);
            $porcImpConsumo = (float) ($d->porc_impoconsumo ?? 0);

            // Impuestos unitarios calculados SOBRE unit_neto
            $unit_iva = round($unit_neto * ($porcIva / 100.0), 4);
            $unit_up = round($unit_neto * ($porcOtro / 100.0), 4);
            $unit_ic = round($unit_neto * ($porcImpConsumo / 100.0), 4);

            // unit total (unit_neto + impuestos)
            $unit_total = round($unit_neto + $unit_iva + $unit_up + $unit_ic, 4);

            // Totales por línea (moneda 2 decimales)
            $line_total_bruto = round($unit_bruto * $cantidad, 2);
            $line_descuento = round($unit_descuento * $cantidad, 2);
            $line_neto = round($unit_bruto * $cantidad, 2);
            $line_iva = round($unit_iva * $cantidad, 2);
            $line_up = round($unit_up * $cantidad, 2);
            $line_ic = round($unit_ic * $cantidad, 2);
            $line_total = round($unit_total * $cantidad, 2);

            // Acumular totales
            $totales['subtotal_base'] += $line_neto; // si prefieres usar bruto cambia aquí
            $totales['descuento'] += $line_descuento;
            $totales['iva'] += $line_iva;
            $totales['ultra'] += $line_up;
            $totales['impoconsumo'] += $line_ic;
            $totales['total_devolver'] += $line_total;

            $detalleItems[] = (object)[
                'code' => $d->code ?? null,
                'name' => $d->nameprod ?? $d->descripcion ?? '',
                'cantidad' => $cantidad,
                'unitario' => $unit_bruto,
                'unit_descuento' => $unit_descuento,
                'unit_neto' => $unit_neto,
                'unit_iva' => $unit_iva,
                'unit_up' => $unit_up,
                'unit_ic' => $unit_ic,
                'unit_total' => $unit_total,
                'line_total_bruto' => $line_total_bruto,
                'line_descuento' => $line_descuento,
                'line_neto' => $line_neto,
                'line_iva' => $line_iva,
                'line_up' => $line_up,
                'line_ic' => $line_ic,
                'line_total' => $line_total,
            ];
        }

        // Redondear totales finales para presentación
        $totales = array_map(fn($v) => round($v, 2), $totales);

        // --- Formas de pago: factura (sale) y nota crédito (ncModel) ---
        $salePaymentId = $sale->forma_pago ?? $sale->forma_pago_id ?? null; // viene de sa.* en tu select
        $ncPaymentId = $ncModel->forma_pago_id ?? $ncModel->forma_pago ?? null;

        $salePaymentName = $paymentMethods[$salePaymentId] ?? ($salePaymentId ? "FP ({$salePaymentId})" : 'N/A');
        $ncPaymentName = $paymentMethods[$ncPaymentId] ?? ($ncPaymentId ? "FP ({$ncPaymentId})" : 'N/A');

        // --- Pasar todo a la vista (manteniendo nombres que ya usabas) ---
        // Mantengo la variable 'sale' y 'saleDetails' para compatibilidad con tu vista original.
        // Agrego 'detalleItems' y 'totales' para la lógica de presentación detallada.
        $showFactura = PDF::loadView('notacredito.reporte', [
            'sale' => $sale,
            'saleDetails' => $saleDetails,
            'detalleItems' => $detalleItems,
            'totales' => $totales,
            'salePaymentName' => $salePaymentName,
            'ncPaymentName' => $ncPaymentName,
            'ncModel' => $ncModel, // por si la vista necesita campos directos de la NC
        ]);

        return $showFactura->stream('notacredito.pdf');
    }
}
