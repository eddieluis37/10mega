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
        'product_id',
        'pcompra',
        'peso',
        'iva',
        'subtotal',
        'status',
    ];

    /* public function compensadore()
    {
        return $this->belongsTo(Compensadores::class, 'compensadores_id', 'id');
    } */
   /*   public function product()
    {
        return $this->belongsTo(Product::class, 'products_id', 'id');
    } */

    /**
     * Relación: Un detalle pertenece a un compensador.
     */
    public function compensador()
    {
        return $this->belongsTo(Compensador::class, 'compensadores_id');
    }

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
