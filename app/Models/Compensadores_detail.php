<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compensadores_detail extends Model
{
    use HasFactory;

    protected $table = 'compensadores_details';

    protected $fillable = [
        'compensadores_id',
        'lote_id',
        'products_id',
        'precio_cotiza',
        'peso_cotiza',
        'porc_descuento_cotiza',
        'descuento_cotiza',
        'porc_iva_cotiza',
        'iva_cotiza',
        'porc_otro_imp_cotiza',
        'otro_imp_cotiza',
        'porc_impoconsumo_cotiza',
        'impoconsumo_cotiza',
        'total_bruto_cotiza',
        'subtotal_cotiza',
        'total_cotiza',
        'subtotal_cotiza',
        'pcompra',
        'peso',
        'porc_descuento',
        'descuento',
        'porc_iva',
        'iva',        
        'porc_otro_imp',
        'otro_imp',
        'porc_impoconsumo',
        'impoconsumo',  
        'total_bruto',
        'subtotal',
        'total',
        'status',
    ];  

    
    /**
     * Relación: Un detalle pertenece a un lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Relación: Un detalle pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function third()
    {
        return $this->belongsTo(Third::class, 'third_id', 'id');
    }

    public function category()
    {
        return $this->hasOneThrough(Category::class, Lote::class, 'id', 'id', 'lote_id', 'category_id');
    }
}
