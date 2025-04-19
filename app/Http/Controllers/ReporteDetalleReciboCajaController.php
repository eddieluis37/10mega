<?php

namespace App\Http\Controllers;

use App\Models\ReciboDeCaja;
use Illuminate\Http\Request;

class reportedetallerecibocajaController extends Controller
{
    public function show($id)
    {
        // Cargo el recibo con TODO lo que necesito de una sola vez:
        $recibo = ReciboDeCaja::with([
            'user',
            'third',
            'details.cuentaPorCobrar.sale.third',
            'details.formaPago',
        ])->findOrFail($id);

        return view('reportes.detalle_recibo_caja', compact('recibo'));
    }
}
