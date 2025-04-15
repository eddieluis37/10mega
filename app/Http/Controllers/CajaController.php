<?php

namespace App\Http\Controllers;

use App\Models\caja\Caja;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    /**
     * Abre una nueva caja (turno).
     */
    public function openCaja(Request $request)
    {
        $validated = $request->validate([
            'centrocosto_id' => 'required|numeric',
            // Puedes incluir otros campos requeridos (ej: base inicial)
        ]);

        $caja = Caja::create([
            'user_id'         => 1,
            'centrocosto_id'  => $validated['centrocosto_id'],
            'cajero_id'       => 1,
            'estado'          => 'open',
            'fecha_hora_inicio' => now(),
            // Otros campos iniciales como "base" pueden ser establecidos aquí
        ]);

        return response()->json(['success' => true, 'caja' => $caja]);
    }

    /**
     * Cierra la caja (turno) actual.
     */
    public function closeCaja(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'required|numeric',
            // Puedes validar otros campos (ej: totales finales, retiro de caja, etc.)
        ]);

        $caja = Caja::findOrFail($validated['caja_id']);
        $caja->estado = 'close';
        $caja->fecha_hora_cierre = now();
        // Actualiza otros totales o diferencias si se requiere
        $caja->save();

        return response()->json(['success' => true, 'caja' => $caja]);
    }
}
