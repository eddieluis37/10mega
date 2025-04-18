<?php

namespace App\Http\Controllers;

use App\Models\ReciboDeCaja;
use Illuminate\Http\Request;

class ReporteDetalleReciboCajaController extends Controller
{
    public function show($id)
    {
        // Cargo el recibo con TODO lo que necesito de una sola vez:
        $recibo = ReciboDeCaja::with([
            'user',
            'third',
            'paymentMethod',
            'details.cuentaPorCobrar.sale.third',
        ])->findOrFail($id);

        return view('reportes.detalle_recibo_caja', compact('recibo'));
    }
}
