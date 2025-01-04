<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteProduct extends Model
{
    use HasFactory;
    protected $table = 'lote_products';

    protected $fillable = [
        'lote_id',
        'product_id',
        'cantidad',
        'precio',
        'created_at',
        'update_at'
    ];

}
