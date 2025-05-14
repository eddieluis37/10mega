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
        'cantidad_inventario_inicial',
        'cantidad_compra_lote',
        'cantidad_alistamiento',
        'cantidad_compra_prod',
        'cantidad_prod_term',   
        'inventario_inicial',
        'cantidad_inicial',
        'cantidad_traslado',
        'cantidad_venta',
        'cantidad_final',
        'stock_ideal',
        'stock_fisico',
        'cantidad_diferencia',
        'costo_unitario',
        'costo_total'
    ];

    protected $casts = [
        'stock_ideal' => 'decimal:2',        
    ];

    /**
     * Relación con la tabla `Lotes`. codigoLote: Utiliza $inventario->lote->codigo para obtener el código del lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relación con la tabla `Bodegas`.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function movements()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
