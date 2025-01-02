<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimiento_inventarios';

    protected $fillable = [
        'tipo', // Compra, Venta, Traslado
        'fecha',
        'cantidad',
        'lote_id',
        'compensadores_id',
        'store_origen_id',
        'store_destino_id',
    ];

    /**
     * Relación con la tabla `Lote`.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }


    /**
     * Relación con la tabla `Bodegas` (origen).
     */
    public function storeOrigen()
    {
        return $this->belongsTo(Store::class, 'store_origen_id');
    }

    /**
     * Relación con la tabla `Bodegas` (destino).
     */
    public function storeDestino()
    {
        return $this->belongsTo(Store::class, 'store_destino_id');
    }

    /**
     * Relación con el `Compensador` (si el movimiento es una compra compensada).
     */
    public function compensador()
    {
        return $this->belongsTo(Compensador::class, 'compensador_id');
    }
}