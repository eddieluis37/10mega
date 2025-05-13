<?php

namespace App\Models\caja;

use App\Models\caja\Caja;
use App\Models\Third;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Cajasalidaefectivo extends Model
{
    use LogsActivity;

    protected $table = 'caja_salida_efectivo';

    protected $fillable = [
        'caja_id',
        'vr_efectivo',
        'concepto',
        'fecha_hora_salida',
        'third_id',
        'status',
    ];

    /** Configuración de Spatie Activitylog **/
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('caja_salida_efectivo')
            ->logOnly(['caja_id', 'vr_efectivo', 'concepto', 'fecha_hora_salida', 'third_id', 'status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Registro de salida de efectivo fue {$eventName}");
    }

    // Relaciones
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Third::class, 'third_id');
    }
}
