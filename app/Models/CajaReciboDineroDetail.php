<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaReciboDineroDetail extends Model
{
    use HasFactory;
    protected $table = 'caja_recibo_dinero_details';

    protected $fillable = [
        'caja_id',
        'user_id',
        'third_id',
        'quantity',
        'price',
        'porc_desc',
        'descuento',
        'descuento_cliente',
        'porc_iva',
        'iva',
        'porc_otro_impuesto',
        'otro_impuesto',
        'total_bruto',
        'total'
    ];

    // Relación con la caja a la que pertenece el detalle
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
    
    // Relación con el usuario que registró el detalle
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el tercero asociado al movimiento (si aplica)
    public function third()
    {
        return $this->belongsTo(Third::class);
    }
}

