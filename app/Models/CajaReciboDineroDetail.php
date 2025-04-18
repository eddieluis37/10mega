<?php

namespace App\Models;

use App\Models\caja\Caja;
use Illuminate\Database\Eloquent\Model;

class CajaReciboDineroDetail extends Model
{
    protected $table = 'caja_recibo_dinero_details';

    protected $fillable = [
        'recibodecaja_id',
        'user_id',
        'cuentas_por_cobrar_id',
        'formapagos_id',
        'vr_deuda',
        'vr_pago',
        'nvo_saldo',
        'status',
    ];

    protected $casts = [
        'vr_deuda'      => 'decimal:0',
        'vr_pago'       => 'decimal:0',
        'nvo_saldo'     => 'decimal:0',
        'status'        => 'boolean',
    ];

    // Relaciones
    public function recibo()
    {
        return $this->belongsTo(ReciboDeCaja::class, 'recibodecaja_id');
    }

    public function cuentaPorCobrar()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuentas_por_cobrar_id');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'formapagos_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
