<?php

namespace App\Http\Controllers\recibodecaja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notacredito;
use App\Models\NotacreditoDetail;
use App\Models\Recibodecaja;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sale;


class pdfRecibodecajaController extends Controller
{
    public function showRecibodecaja($id)
    {
         // Cargar recibo con relaciones y detalles
         $recibo = Recibodecaja::with([
            'user', 'third',
            'details.paymentMethod',
            'details.cuentaPorCobrar.sale.third',
        ])->findOrFail($id);

        // Opcional: calcular totales si no están precargados
        $recibo->vr_total_pago   = $recibo->details->sum('vr_pago');
        $recibo->nvo_total_saldo = $recibo->details->sum('nvo_saldo');

        // Generar PDF (legal = oficio)
        $pdf = PDF::loadView(
            'recibodecaja.reporte',
            compact('recibo')
        )
        ->setPaper('legal', 'portrait');

        return $pdf->stream("recibo_caja_{$recibo->id}.pdf");
    }
}
