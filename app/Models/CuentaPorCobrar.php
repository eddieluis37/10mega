<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
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
        'saldo_cartera',
    ];

    protected $casts = [
        'fecha_inicial'     => 'date',
        'fecha_vencimiento' => 'date',
        'deuda_inicial'     => 'decimal:0',
        'deuda_x_cobrar'    => 'decimal:0',
        'deuda_x_pagar'     => 'decimal:0',
        'valor_anticipo'    => 'decimal:0',
        'saldo_cartera'     => 'decimal:0',
    ];

    // Relaciones
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function third()
    {
        return $this->belongsTo(Third::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cajaDetails()
    {
        return $this->hasMany(CajaReciboDineroDetail::class, 'cuentas_por_cobrar_id');
    }

    /**
     * Actualiza el saldo pendiente luego de un pago.
     *
     * @param  float  $nuevoSaldo
     * @return $this
     */
    public function updateSaldo(float $nuevoSaldo)
    {
        $this->deuda_x_cobrar = $nuevoSaldo;
        return tap($this)->save();
    }

    // Relación con el parámetro contable (si aplica)
    public function parametroContable()
    {
        return $this->belongsTo(ParametroContable::class, 'parametrocontable_id');
    }
}
