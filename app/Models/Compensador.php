<?php

namespace App\Models;

use App\Models\centros\Centrocosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compensador extends Model
{
    use HasFactory;

    protected $table = 'compensadores';

    protected $fillable = [
        'factura', // Número de factura
        'users_id',
        'store_id',
        'thirds_id',
        'fecha_compensado',
        'fecha_ingreso',
        'fecha_cierre',
        'observacion',
        'status',
    ];

    /**
     * Relación con los movimientos de inventario asociados.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'compensador_id');
    }

    //  la relación con múltiples detalles:
    public function detalles()
    {
        return $this->hasMany(Compensadores_detail::class, 'compensadores_id');
    }

    // Casteo automático de fechas a Carbon
    protected $casts = [
        'fecha_compensado' => 'datetime',
    ];

    /**
     * Relación con Third (tercero).
     * Un compensador pertenece a un tercero.
     */
    public function third()
    {
        // thirds_id → third.id
        return $this->belongsTo(Third::class, 'thirds_id');
    }

    /**
     * Relación con User (usuario).
     * Un compensador fue creado/gestionado por un usuario.
     */
    public function user()
    {
        // users_id → users.id
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relación con Store (centro de costo).
     * Un compensador está asociado a una tienda/centro.
     */
    public function store()
    {
        // store_id → stores.id
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Accessor opcional para formatear la fecha en español.
     * Si prefieres, puedes usar esto en lugar de parsear en el controlador.
     */
    public function getFechaCompensadoFormattedAttribute(): string
    {
        // Asegúrate de haber seteado Carbon::setLocale('es') en algún sitio de arranque (AppServiceProvider, por ejemplo).
        return $this->fecha_compensado
            ->isoFormat('dddd, D [de] MMMM [de] YYYY');
    }

     /**
     * Relación directa a CentroCosto a través de Store.
     */
    public function centroCosto()
    {
        return $this->hasOneThrough(
            Centrocosto::class, // Modelo destino
            Store::class,       // Modelo intermedio
            'id',               // FK de stores en el modelo intermedio (Store.id)
            'id',               // PK de centrocostos en el modelo destino (Centrocosto.id)
            'store_id',         // FK local en compensadores
            'centrocosto_id'    // FK local en stores
        );
    }

}
