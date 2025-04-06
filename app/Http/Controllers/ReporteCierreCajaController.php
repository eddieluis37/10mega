<?php

namespace App\Http\Controllers;

use App\Models\caja\Caja;
use Illuminate\Http\Request;

class ReporteCierreCajaController extends Controller
{
    /**
     * Muestra el reporte dinámico de cierre de caja.
     *
     * @param  int  $id  Identificador de la caja
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Se carga la caja junto con sus relaciones: centroCosto, cajero y las ventas relacionadas.
        $caja = Caja::with([
            'centroCosto',
            'cajero',
            'sales'  // Asegúrate de que las ventas tengan los campos y relaciones que necesitas (p.ej., cliente, formas de pago, etc.)
        ])->findOrFail($id);

        // Aquí podrías agregar cálculos de totales u otras lógicas adicionales

        return view('reportes.cierre_caja', compact('caja'));
    }
}
