<?php

namespace App\Observers;

use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Illuminate\Support\Facades\Log;

class MovimientoInventarioObserver
{
    /**
     * Se ejecuta cuando se crea un movimiento.
     */
    public function created(MovimientoInventario $movimiento): void
    {
      // Log::info('Evento created en MovimientoInventario', ['movimiento' => $movimiento->toArray()]);
        $this->actualizarStock($movimiento);
    }

    /**
     * Se ejecuta cuando se actualiza un movimiento.
     */
    public function updated(MovimientoInventario $movimiento): void
    {
       // Log::info('Evento updated en MovimientoInventario', ['movimiento' => $movimiento->toArray()]);
        $this->actualizarStock($movimiento);
    }

    /**
     * Se ejecuta cuando se elimina un movimiento.
     */
    public function deleted(MovimientoInventario $movimiento): void
    {
       // Log::info('Evento deleted en MovimientoInventario', ['movimiento' => $movimiento->toArray()]);
        $this->actualizarStock($movimiento);
    }

    /**
     * Actualiza el stock ideal para las tiendas involucradas en el movimiento.
     */
    private function actualizarStock(MovimientoInventario $movimiento): void
    {
        // Actualizar para la tienda de destino (movimientos de ingreso)
        if ($movimiento->store_destino_id) {
           /*  Log::info('Actualizando stock para store_destino', [
                'store_destino_id' => $movimiento->store_destino_id,
                'lote_id'          => $movimiento->lote_id,
                'product_id'       => $movimiento->product_id,
            ]); */
            InventarioService::actualizarStock(
                $movimiento->store_destino_id,
                $movimiento->lote_id,
                $movimiento->product_id
            );
        }
        // Actualizar para la tienda de origen (movimientos de salida)
        if ($movimiento->store_origen_id && $movimiento->store_origen_id !== $movimiento->store_destino_id) {
           /*  Log::info('Actualizando stock para store_origen', [
                'store_origen_id' => $movimiento->store_origen_id,
                'lote_id'         => $movimiento->lote_id,
                'product_id'      => $movimiento->product_id,
            ]); */
            InventarioService::actualizarStock(
                $movimiento->store_origen_id,
                $movimiento->lote_id,
                $movimiento->product_id
            );
        }
    }
}
