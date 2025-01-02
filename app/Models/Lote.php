<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = [
        'codigo',
        'fecha_vencimiento',
        'costo',
        'producto_id',
    ];

    /**
     * Relación con la tabla `Productos`.
     */
    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    /**
     * Relación con la tabla `Inventarios`.
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'lote_id');
    }

    /**
     * Relación con la tabla `MovimientoInventarios`.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'lote_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_lote');
    }
}
