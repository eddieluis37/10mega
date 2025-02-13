<?php

namespace App\Observers;

use App\Models\MovimientoInventario;
use App\Services\InventarioService;

class MovimientoInventarioObserver
{
    /**
     * Handle the MovimientoInventario "created" event.
     */
    public function created(MovimientoInventario $movimiento): void
    {
        InventarioService::actualizarStockIdeal(
            $movimiento->store_destino_id,
            $movimiento->lote_id,
            $movimiento->product_id
        );
    }

    /**
     * Handle the MovimientoInventario "updated" event.
     */
    public function updated(MovimientoInventario $movimientoInventario): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "deleted" event.
     */
    public function deleted(MovimientoInventario $movimientoInventario): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "restored" event.
     */
    public function restored(MovimientoInventario $movimientoInventario): void
    {
        //
    }

    /**
     * Handle the MovimientoInventario "force deleted" event.
     */
    public function forceDeleted(MovimientoInventario $movimientoInventario): void
    {
        //
    }
}
