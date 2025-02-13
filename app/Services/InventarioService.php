<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

/**
 * Class InventarioService.
 */
class InventarioService
{
    /**
     * Recalcula y actualiza el stock ideal para un inventario específico.
     *
     * @param int $storeId
     * @param int $loteId
     * @param int $productId
     * @return Inventario|null
     */
    public static function actualizarStockIdeal($storeId, $loteId, $productId)
    {
        // Buscar el inventario correspondiente
        $inventario = Inventario::where('store_id', $storeId)
            ->where('lote_id', $loteId)
            ->where('product_id', $productId)
            ->first();

        if (!$inventario) {
            return null;
        }

        // Obtener los movimientos asociados agrupados por tipo
        $movimientos = MovimientoInventario::where('lote_id', $loteId)
            ->where('store_destino_id', $storeId)
            ->where('product_id', $productId)
            ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
            ->groupBy('tipo')
            ->get();

        // Sumar cantidades por cada tipo de movimiento
        $desposteres      = $movimientos->where('tipo', 'desposteres')->sum('cantidad_total');
        $despostecerdos   = $movimientos->where('tipo', 'despostecerdos')->sum('cantidad_total');
        $enlistments      = $movimientos->where('tipo', 'enlistments')->sum('cantidad_total');
        $compensadores    = $movimientos->where('tipo', 'compensadores')->sum('cantidad_total');
        $trasladoIngreso  = $movimientos->where('tipo', 'traslado_ingreso')->sum('cantidad_total');
        $trasladoSalida   = $movimientos->where('tipo', 'traslado_salida')->sum('cantidad_total');

        // Calcular el stock ideal según la fórmula definida
        $stockIdeal = (
            $inventario->cantidad_inventario_inicial +
            $desposteres +
            $despostecerdos +
            $enlistments +
            $compensadores +
            $inventario->cantidad_prod_term +
            $trasladoIngreso
        ) - $trasladoSalida;

        // Actualizar el inventario
        $inventario->stock_ideal = $stockIdeal;
        $inventario->save();

        return $inventario;
    }
}
