<?php

namespace App\Models\transfer;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    // Tablas, si tu convención no es pluralizado estándar:
    // protected $table = 'transfers';

    protected $fillable = [
        'users_id',
        'bodega_origen_id',
        'bodega_destino_id',
        'inventario',
        'fecha_tranfer',
        'status',
        // 'observaciones', // Agrega esta columna si la necesitas
    ];

    protected $casts = [
        'status'        => 'boolean',
        'fecha_tranfer' => 'date',
    ];

    /**
     * Usuario que genera el traslado
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Bodega de origen
     */
    public function bodegaOrigen(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'bodega_origen_id');
    }

    /**
     * Bodega de destino
     */
    public function bodegaDestino(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'bodega_destino_id');
    }

    /**
     * Detalles del traslado
     */
    public function details(): HasMany
    {
        return $this->hasMany(transfer_details::class, 'transfers_id');
    }
}