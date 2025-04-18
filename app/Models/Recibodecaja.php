<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recibodecaja extends Model
{
    protected $table = 'recibodecajas';

    protected $fillable = [
        'user_id',
        'third_id',
        'fecha_elaboracion', 
        'tipo',            // '1' => Ingreso, '2' => Egreso, etc.
        'status',
        'realizar_un',
    ];

    protected $casts = [
        'vr_total_deuda'   => 'decimal:0',
        'vr_total_pago'    => 'decimal:0',
        'nvo_total_saldo'  => 'decimal:0',
        'fecha_elaboracion' => 'date',
        'fecha_cierre'      => 'date',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function third()
    {
        return $this->belongsTo(Third::class, 'third_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(Formapago::class, 'formapagos_id');
    }

    public function details()
    {
        return $this->hasMany(Cajarecibodinerodetail::class, 'recibodecaja_id');
    }

    /**
     * Recalcula y actualiza los totales basados en los detalles.
     *
     * @return void
     */
    public function recalculateTotals(): void
    {
        $totales = $this->details()
            ->selectRaw('
                SUM(vr_deuda)      AS total_deuda,
                SUM(vr_pago)       AS total_pago,
                SUM(nvo_saldo)     AS total_saldo
            ')
            ->first();

        $this->update([
            'vr_total_deuda'  => $totales->total_deuda ?? 99,
            'vr_total_pago'   => $totales->total_pago  ?? 99,
            'nvo_total_saldo' => $totales->total_saldo ?? 109,
        ]);
    }
}
