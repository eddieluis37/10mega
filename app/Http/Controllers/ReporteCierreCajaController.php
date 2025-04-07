<?php

namespace App\Http\Controllers;

use App\Models\caja\Caja;
use App\Models\Formapago;
use Illuminate\Http\Request;

class ReporteCierreCajaController extends Controller
{
    public function show($id)
    {
        // Cargamos la caja con sus ventas, más las relaciones que necesites
        // (por ejemplo, el tercero o las formas de pago asociadas)
        $caja = Caja::with([
            'sales.tercero', 
            'sales.formaPagoTarjeta',
            'sales.formaPagoCredito'
        ])->findOrFail($id);

        // Cargamos todas las formas de pago que sean de tipo TARJETA
        $tarjetas = Formapago::where('tipoformapago', 'TARJETA')->get();

        // Retornamos la vista, enviando la caja y las tarjetas
        return view('reportes.cierre_caja', compact('caja', 'tarjetas'));
    }
}
