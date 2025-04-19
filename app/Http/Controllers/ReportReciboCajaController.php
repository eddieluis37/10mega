<?php

namespace App\Http\Controllers;

use App\Models\Recibodecaja;
use Illuminate\Http\Request;

class ReportReciboCajaController extends Controller
{
    public function show($id)
    {
        // Cargo el recibo con TODO lo que necesito de una sola vez:
        $recibo = Recibodecaja::with([
            'user',
            'third',
            'details.cuentaPorCobrar.sale.third',
            'details.paymentMethod',
        ])->findOrFail($id);

        return view('reportes.detalle_recibo_caja', compact('recibo'));
    }
}
