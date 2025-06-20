<?php

namespace App\Services;

use App\Models\Sale;
use Carbon\Carbon;

class TurnoDiarioService
{
    /**
     * Devuelve el siguiente turno diario para un centro de costo.
     */
    public static function generarParaCentro(int $centroCostoId): int
    {
        // Contar cuántas ventas con tipo 2 o 3 ya existen hoy en este centro
        $hoy = Carbon::today();
        
        $ultimo = Sale::where('centrocosto_id', $centroCostoId)
            ->whereIn('tipo', ['2', '3'])
            ->whereDate('created_at', $hoy)
            ->max('turno_diario');
        
        // Si no hay ninguno, $ultimo será null => devolvemos 1
        return ($ultimo ?? 0) + 1;
    }
}
