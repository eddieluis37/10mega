<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLote extends Model
{
    use HasFactory;
    protected $table = 'product_lote';

    protected $fillable = [
        'product_id',
        'lote_id',
        'quantity',
        'costo',
    ];

    // Relación con el modelo Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con el modelo Lote
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }
}
