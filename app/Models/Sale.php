<?php

namespace App\Models;

use App\Models\caja\Caja;
use App\Models\centros\Centrocosto;
use App\Models\centros\Subcentrocosto;
use App\Services\TurnoDiarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Sale extends Model
{
    protected $table = 'sales';

    protected $dates = ['fecha_cierre'];

    protected $fillable = [
        'user_id',
        'store_id',
        'third_id',
        'vendedor_id',
        'domiciliario_id',
        'centrocosto_id',
        'tipo',
        'consecutivo',
        'fecha_venta',
        'valor_a_pagar_efectivo',
        'valor_a_pagar_tarjeta',
        'valor_a_pagar_tarjeta2',
        'valor_a_pagar_tarjeta3',
        'valor_a_pagar_otros',
        'valor_a_pagar_credito',
        'total',
        'total_iva',
        'items',
        'cash',
        'cambio',
        'status',
        'fecha',
        'resol',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Sale $sale) {
            // Solo para ventas Parrilla (2 y 3) y si tengo centro de costo
            if (in_array($sale->tipo, ['2', '3']) && $sale->centrocosto_id) {
                $sale->turno_diario = TurnoDiarioService::generarParaCentro($sale->centrocosto_id);
            }
        });
    }

    /* -----------------------------
     | Relaciones básicas (BelongsTo)
     | ----------------------------- */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    public function centrocosto(): BelongsTo
    {
        return $this->belongsTo(Centrocosto::class);
    }

    public function subcentrocosto(): BelongsTo
    {
        return $this->belongsTo(Subcentrocosto::class, 'subcentrocostos_id', 'id');
    }

    // tercero / cliente (mantengo nombre english y español)
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class, 'third_id');
    }

    public function tercero(): BelongsTo
    {
        return $this->third();
    }

    /* ------------------------------------
     | Relaciones de formas de pago y caja
     | ------------------------------------ */
    public function formaPagoTarjeta(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta_id');
    }

    public function formaPagoTarjeta2(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta2_id');
    }

    public function formaPagoTarjeta3(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta3_id');
    }

    public function formaPagoCredito(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_credito_id');
    }

    // Relación many-to-many con cajas (pivot sale_caja)
    public function cajas()
    {
        return $this->belongsToMany(Caja::class, 'sale_caja');
    }

    // Recibos, pagos, formas de pago usados en la venta
    public function recibosDeCaja(): HasMany
    {
        return $this->hasMany(Recibodecaja::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function saleFormaPagos(): HasMany
    {
        return $this->hasMany(SaleFormaPago::class);
    }

    /* -----------------------------
     | Detalles de la venta
     | ----------------------------- */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    // alias legacy para compatibilidad
    public function details(): HasMany
    {
        return $this->saleDetails();
    }

    /* ---------------------------------------
     | Stores asociados a través del centro
     | (hasManyThrough: Sale -> Centrocosto -> Store)
     | --------------------------------------- */
    public function stores(): HasManyThrough
    {
        // Nota: la firma de hasManyThrough es:
        // hasManyThrough(Final::class, Through::class, throughForeignKey, finalForeignKey, localKey, throughLocalKey)
        // En este caso: Notar que Centrocosto no tiene FK hacia sale; la relación se construye a partir
        // de que Sale.centrocosto_id == Centrocosto.id y Store.centrocosto_id == Centrocosto.id.
        // La implementación original puede funcionar; aquí la dejamos clara y documentada.
        return $this->hasManyThrough(
            Store::class,
            Centrocosto::class,
            'id',               // FK en Centrocosto que relaciona con sale.centrocosto_id (en este caso usamos 'id' porque sale.centrocosto_id apunta a centros.id)
            'centrocosto_id',   // FK en Store que apunta a Centrocosto.id
            'centrocosto_id',   // local key en sales (campo que contiene el centrocosto_id)
            'id'                // local key en centrocosto
        );
    }

    /* ------------------------------------------
     | Notas de crédito: soporte para múltiples
     | ------------------------------------------ */

    /**
     * notacreditos() => hasMany: una venta puede tener varias notas de crédito.
     */
    public function notacreditos(): HasMany
    {
        return $this->hasMany(\App\Models\Notacredito::class, 'sale_id');
    }

    /**
     * notacredito() => conserva compatibilidad: devuelve la nota "principal" (la más reciente).
     * Esto evita romper código que esperaba hasOne.
     */
    public function notacredito(): HasOne
    {
        // latestOfMany usa la colección hasMany internamente para devolver un hasOne "la más reciente"
        return $this->hasOne(\App\Models\Notacredito::class, 'sale_id')->latestOfMany();
    }

    /**
     * Acceso directo a las formas de pago usadas por las notas de crédito.
     * En lugar de un hasOneThrough confuso, recomendamos usar:
     *    $sale->notacreditos->pluck('formaPago')  // collection de Formapago
     *
     * Si necesitas una relación Eloquent, puedes mapear por medio de las notacreditos.
     * No creamos un hasOneThrough erróneo; en la mayoría de casos trabajar con la colección es más claro.
     */

    /* ------------------------------------------
     | Scopes y utilidades
     | ------------------------------------------ */

    public function scopeTurnoVigente($query)
    {
        return $query->whereDate('sales.fecha_venta', now()->toDateString());
    }
}
