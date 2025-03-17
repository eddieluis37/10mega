<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    use HasFactory;
    
    protected $table = 'notacreditos';

    protected $fillable = [
        'sale_id',     // Relaciona la nota de crédito con la venta anulada o con devolución
        'user_id',     // Usuario que genera la nota
        'total',       // Total de la nota (puede ser calculado a partir de los detalles)
        'status',      // Estado de la nota (por ejemplo, 'active', 'anulada', etc.)
        // Otros campos que requieras...
    ];

    public function detalles()
    {
        return $this->hasMany(NotaCreditoDetalle::class, 'notacredito_id');
    }

}
