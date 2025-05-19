<?php

namespace App\Http\Controllers\caja;

use App\Http\Controllers\Controller;


use App\Models\compensado\Compensadores;
use App\Models\Sale;

use App\Models\caja\Cajasalidaefectivo;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class pdfSalidaefectivoController extends Controller
{
    /**
     * Genera el PDF de salida de efectivo
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function pdfFormatopos($id)
    {
        // Cargar el registro de salida con sus relaciones: caja, tercero, cajero y centro de costo
        $salida = Cajasalidaefectivo::with([
            'caja.cajero',        // usuario cajero
            'caja.centroCosto',   // centro de costo
            'tercero'             // datos del tercero
        ])->findOrFail($id);

        // Configurar idioma en español
        Carbon::setLocale('es');

        // Formatear fechas
        $fechaSalida    = Carbon::parse($salida->fecha_hora_salida)->format('Y-m-d H:i');
        $fechaRegistro  = Carbon::parse($salida->created_at)->format('Y-m-d H:i');

        // Formatear fecha y hora de salida
        $fechaSalida = Carbon::parse($salida->fecha_hora_salida)->format('Y-m-d H:i');

        // Generar PDF usando la vista blade
        $pdf = PDF::loadView('caja_salida_efectivo.pdfFormatopos', compact('salida', 'fechaSalida', 'fechaRegistro'));

        // Renderizar en navegador
        return $pdf->stream("salida-efectivo-{$salida->id}.pdf");
    }
}
