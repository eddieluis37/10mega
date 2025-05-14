<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public static function actualizarStock($storeId, $loteId, $productId)
    {
        Log::info('Iniciando actualización de stock ideal', [
            'storeId' => $storeId,
            'loteId' => $loteId,
            'productId' => $productId,
        ]);

        // Buscar el inventario correspondiente
        $inventario = Inventario::where('store_id', $storeId)
            ->where('lote_id', $loteId)
            ->where('product_id', $productId)
            ->first();

        if (!$inventario) {
            Log::warning('Inventario no encontrado', [
                'storeId' => $storeId,
                'loteId' => $loteId,
                'productId' => $productId,
            ]);
            return null;
        }

        Log::info('Inventario encontrado', ['inventario' => $inventario->toArray()]);

        // Obtener los movimientos asociados agrupados por tipo (ingresos)
        $movimientos = MovimientoInventario::where('lote_id', $loteId)
            ->where('store_destino_id', $storeId)
            ->where('product_id', $productId)
            ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
            ->groupBy('tipo')
            ->get();

        Log::info('Movimientos de ingreso obtenidos', ['movimientosIngreso' => $movimientos->toArray()]);

        // Sumar cantidades por cada tipo de movimiento de ingresos
        $desposteres      = $movimientos->where('tipo', 'desposteres')->sum('cantidad_total');
        $despostecerdos   = $movimientos->where('tipo', 'despostecerdos')->sum('cantidad_total');
        $enlistments      = $movimientos->where('tipo', 'enlistments')->sum('cantidad_total');
        $compensadores    = $movimientos->where('tipo', 'compensadores')->sum('cantidad_total');
        $trasladoIngreso  = $movimientos->where('tipo', 'traslado_ingreso')->sum('cantidad_total');
        $trasladoSalida   = $movimientos->where('tipo', 'traslado_salida')->sum('cantidad_total');

        Log::info('Totales de movimientos de ingreso', [
            'desposteres'    => $desposteres,
            'despostecerdos' => $despostecerdos,
            'enlistments'    => $enlistments,
            'compensadores'  => $compensadores,
            'trasladoIngreso'=> $trasladoIngreso,
            'trasladoSalida' => $trasladoSalida,
        ]);

        // Obtener los movimientos de salida agrupados por tipo
        $movimientosSalida = MovimientoInventario::where('lote_id', $loteId)
            ->where('store_origen_id', $storeId)
            ->where('product_id', $productId)
            ->select('tipo', DB::raw('SUM(cantidad) AS cantidad_total'))
            ->groupBy('tipo')
            ->get();

        Log::info('Movimientos de salida obtenidos', ['movimientosSalida' => $movimientosSalida->toArray()]);

        $totalVenta  = $movimientosSalida->where('tipo', 'venta')->sum('cantidad_total');
        $totalNotaCredito  = $movimientosSalida->where('tipo', 'notacredito')->sum('cantidad_total');
        
        Log::info('Total de ventas', ['totalVenta' => $totalVenta]);

        // Calcular el stock ideal según la fórmula definida
        $stockIdeal = ($inventario->cantidad_inventario_inicial
                    + $desposteres
                    + $despostecerdos
                    + $enlistments
                    + $compensadores
                    + $inventario->cantidad_prod_term
                    + $trasladoIngreso) - $trasladoSalida - ($totalVenta - $totalNotaCredito);

        Log::info('Stock ideal calculado', [
            'cantidad_inventario_inicial' => $inventario->cantidad_inventario_inicial,
            'cantidad_prod_term'          => $inventario->cantidad_prod_term,
            'stockIdeal'                  => $stockIdeal,
        ]);

        // Actualizar el inventario
        $inventario->stock_ideal = $stockIdeal;
        $result = $inventario->save();

        Log::info('Inventario actualizado', [
            'inventarioId' => $inventario->id,
            'stock_ideal'  => $inventario->stock_ideal,
            'save_result'  => $result,
        ]);

        return $inventario;
    }
}
