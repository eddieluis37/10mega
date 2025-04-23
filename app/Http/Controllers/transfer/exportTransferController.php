<?php

namespace App\Http\Controllers\transfer;

use App\Http\Controllers\Controller;
use App\Models\caja\Caja;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\transfer\Transfer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class exportTransferController extends Controller
{
    public function showTransfer($id)
    {
         // Recuperamos el traslado con sus relaciones para evitar N+1
         $transfer = Transfer::with([
            'user:id,name',
            'bodegaOrigen:id,name',
            'bodegaDestino:id,name',
            'details' => function($q) {
                $q->with([
                    'product:id,name,code',
                    'lote:id,codigo,fecha_vencimiento'
                ])->where('status', true);
            }
        ])->findOrFail($id);

        // Detalles ya cargados
        $details        = $transfer->details;
        $detailCount    = $details->count();
        $totalKilos     = $details->sum('kgrequeridos');

        // Fecha formateada
        $now = Carbon::now()->format('Y-m-d H:i');

        // Renderizamos PDF
        $pdf = PDF::loadView('transfer.pdf', compact(
            'transfer',
            'details',
            'detailCount',
            'totalKilos',
            'now'
        ));

        return $pdf->stream("traslado_{$transfer->id}.pdf");
    }
        
}
