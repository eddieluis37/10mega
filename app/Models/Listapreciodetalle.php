<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listapreciodetalle extends Model
{
    use HasFactory;

    protected $table = 'listapreciodetalles';
    protected $fillable = ['listaprecio_id', 'product_id', 'precio', 'porciva', 'iva', 'precio_venta'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
