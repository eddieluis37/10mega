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
        'users_id',
        'store_id',
        'thirds_id',
        'fecha_compensado',
        'fecha_ingreso',
        'fecha_cierre',
        'status',
    ];

    /**
     * Relación con los movimientos de inventario asociados.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'compensador_id');
    }

    //  la relación con múltiples detalles:
    public function detalles()
    {
        return $this->hasMany(Compensadores_detail::class, 'compensadores_id');
    }
}
