<?php

namespace App\Models;

use App\Models\caja\Caja;
use App\Models\centros\Centrocosto;
use App\Services\TurnoDiarioService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // Si deseas que Laravel trate ciertos campos como instancias de Carbon,
    // puedes agregarlos al array $dates o utilizar $casts.
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function centrocosto()
    {
        return $this->belongsTo(Centrocosto::class);
    }

    /**
     * Bodegas (stores) asociadas a esta venta,
     * a través de su centrocosto_id.
     */
    public function stores(): HasManyThrough
    {
        return $this->hasManyThrough(
            Store::class,      // Modelo final
            Centrocosto::class, // Modelo intermedio
            'id',              // FK de centrocosto hacia sales.centrocosto_id
            'centrocosto_id',  // FK de stores hacia centros.id
            'centrocosto_id',  // Local key en sales
            'id'               // Local key en centrocosto
        );
    }

    /**
     * El “tercero” (cliente) asociado a esta venta.
     */
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class, 'third_id');
    }


    public function thirds()
    {
        return $this->hasOne('App\Models\Third');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }


    public function subcentrocosto()
    {
        return $this->belongsTo(Subcentrocosto::class, 'subcentrocostos_id', 'id');
    }

    /**
     * Relación con los detalles de la venta.
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    /**
     * Scope para filtrar las ventas del turno vigente.     
     * comparando el fecha_venta con el inicio y fin del turno.
     */
    public function scopeTurnoVigente($query)
    {
        // Suponiendo que la fecha se compara con la fecha actual:
        return $query->whereDate('sales.fecha_venta', now()->toDateString());
    }

    public function tercero()
    {
        return $this->belongsTo(\App\Models\Third::class, 'third_id');
    }

    public function formaPagoTarjeta()
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta_id');
    }

    public function formaPagoTarjeta2()
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta2_id');
    }

    public function formaPagoTarjeta3()
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_tarjeta3_id');
    }

    public function formaPagoCredito()
    {
        return $this->belongsTo(\App\Models\Formapago::class, 'forma_pago_credito_id');
    }

    // Ventas relacionadas con la caja mediante la tabla pivot sale_caja
    public function cajas()
    {
        return $this->belongsToMany(Caja::class, 'sale_caja');
    }

    // Recibos de caja asociados (por ejemplo, en abonos o pagos totales)
    public function recibosDeCaja()
    {
        return $this->hasMany(Recibodecaja::class);
    }

    // Pagos realizados sobre la venta (para actualizar la cuenta por cobrar)
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    // Relación con las formas de pago utilizadas
    public function saleFormaPagos()
    {
        return $this->hasMany(SaleFormaPago::class);
    }

    // Cuenta por cobrar asociada a esta venta (para ventas a crédito)
    public function cuentaPorCobrar()
    {
        return $this->hasOne(CuentaPorCobrar::class);
    }

    // Qué cajero (usuario) hizo la venta
    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    /**
     * Nota de crédito asociada a esta venta (asumo 1:1).
     */
    public function notacredito()
    {
        return $this->hasOne(\App\Models\Notacredito::class, 'sale_id');
    }

    /**
     * Forma de pago de la nota de crédito, a través de la relación anterior.
     */
    public function notaFormaPago()
    {
        return $this->hasOneThrough(
            \App\Models\Formapago::class,
            \App\Models\Notacredito::class,
            'sale_id',         // FK en notacreditos
            'id',              // PK en formapagos
            'id',              // PK en sales
            'forma_pago_id'    // FK en notacreditos hacia formapagos
        );
    }
}
