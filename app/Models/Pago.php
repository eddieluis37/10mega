<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'sale_id',
        'formapago_id',
        'fecha_pago',
        'tipo_doc',
        'valor_pagado',
        'concepto',
        'observacion'
    ];

    // Relación con la venta (se actualiza la cuenta por cobrar)
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación con el método o forma de pago
    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'formapago_id');
    }
}