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
        'vr_total_deuda',
        'vr_total_pago',
        'nvo_total_saldo',
        'fecha_cierre',
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
        $this->load('details');

        $total_deuda = $this->details->sum('vr_deuda');
        $total_pago  = $this->details->sum('vr_pago');
        $total_saldo = $this->details->sum('nvo_saldo');

        $this->update([
            'vr_total_deuda'  => $total_deuda,
            'vr_total_pago'   => $total_pago,
            'nvo_total_saldo' => $total_saldo,
        ]);
    }
}
