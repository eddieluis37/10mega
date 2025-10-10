<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoDetalle extends Model
{
    protected $table = 'notacredito_details'; // Si la tabla se llama así en la BD

    protected $fillable = [
        'notacredito_id',
        'sale_detail_id',
        'sale_id',
        'product_id',
        'store_id',
        'lote_id',
        'quantity',
        'unit_price',
        'unit_bruto',
        'unit_descuento',
        'unit_neto',
        'unit_iva',
        'unit_otro_impuesto',
        'unit_impoconsumo',
        'unit_total',

        'total_bruto',
        'descuento_total',
        'neto_total',
        'iva_total',
        'otro_impuesto_total',
        'impoconsumo_total',
        'total',

        'descuento_pct_amount',
        'descuento_fijo_prorrateado',
        'descuento_global_prorrateado',

        'taxable_base',
        'rounding_adjustment',

        'descuento_pct',
        'porc_iva',
        'porc_otro_impuesto',
        'porc_impoconsumo',

        'breakdown',
    ];

    // Casts para facilitar el trabajo
    protected $casts = [
        'taxable_base' => 'decimal:2',
        'rounding_adjustment' => 'decimal:2',
        'breakdown' => 'array',
    ];


    public function notaCredito()
    {
        return $this->belongsTo(NotaCredito::class, 'notacredito_id');
    }
}
