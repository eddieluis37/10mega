<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'bodega_id',
        'lote_id',
        'cantidad_actual',
    ];

    /**
     * Relación con la tabla `Lotes`.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Relación con la tabla `Bodegas`.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
