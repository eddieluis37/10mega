<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notacredito extends Model
{
    use HasFactory;

    protected $table = 'notacreditos';

    protected $fillable = [
        'sale_id',     // Relaciona la nota de crédito con la venta anulada o con devolución
        'user_id',     // Usuario que genera la nota
        'total',       // Total de la nota (puede ser calculado a partir de los detalles)
        'status',      // Estado de la nota (por ejemplo, 'active', 'anulada', etc.)
        'credit_note_sequence',
        'return_type',
        'forma_pago_id',
        'valor_devolucion',
    ];

    public function detalles()
    {
        return $this->hasMany(NotaCreditoDetalle::class, 'notacredito_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }
}
