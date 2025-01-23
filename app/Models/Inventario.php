<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'store_id',
        'lote_id',
        'product_id',
        'inventario_inicial',
        'cantidad_inicial',
        'cantidad_final',
        'stock_ideal',
        'costo_unitario',
        'costo_total'
    ];

    /**
     * Relación con la tabla `Lotes`. codigoLote: Utiliza $inventario->lote->codigo para obtener el código del lote.
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
