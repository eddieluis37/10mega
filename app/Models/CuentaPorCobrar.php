<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    use HasFactory;
    
    protected $table = 'cuentas_por_cobrars';

    protected $fillable = [
        'user_id',
        'parametrocontable_id',
        'sale_id',
        'third_id',
        'status',
        'fecha_inicial',
        'fecha_vencimiento',
        'deuda_inicial',
        'deuda_x_cobrar',
        'deuda_x_pagar',
        'valor_anticipo',
        'saldo_cartera'
    ];

    // Relación con la venta que genera la cuenta por cobrar
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación con el usuario asociado
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el tercero (cliente)
    public function third()
    {
        return $this->belongsTo(Third::class);
    }

    // Relación con el parámetro contable (si aplica)
    public function parametroContable()
    {
        return $this->belongsTo(ParametroContable::class, 'parametrocontable_id');
    }
}
