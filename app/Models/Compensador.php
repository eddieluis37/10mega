<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compensador extends Model
{
    use HasFactory;

    protected $table = 'compensadores';

    protected $fillable = [
        'factura', // Número de factura
        'proveedor', // Nombre o ID del proveedor
        'monto_total', // Monto total de la compra compensada
        'fecha', // Fecha de la compensación
    ];

    /**
     * Relación con los movimientos de inventario asociados.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'compensador_id');
    }
}
