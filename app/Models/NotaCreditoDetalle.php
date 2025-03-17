<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoDetalle extends Model
{
    protected $table = 'notacredito_details'; // Si la tabla se llama así en la BD

    protected $fillable = [
        'notacredito_id',
        'product_id',
        'quantity',
        'price',
        // Otros campos que requieras...
    ];

    public function notaCredito()
    {
        return $this->belongsTo(NotaCredito::class, 'notacredito_id');
    }
}
