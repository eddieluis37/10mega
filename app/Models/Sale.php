<?php

namespace App\Models;

use App\Models\caja\Caja;
use App\Models\centros\Centrocosto;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'user_id',
        'user_id',
        'store_id',
        'third_id',
        'vendedor_id',
        'domiciliario_id',
        'centrocosto_id',
        'consecutivo',
        'fecha_venta',
        'valor_a_pagar_efectivo',
        'valor_a_pagar_tarjeta',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function centrocosto()
    {
        return $this->belongsTo(Centrocosto::class);
    }

    public function third()
    {
        return $this->belongsTo(Third::class);
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
     * Este scope se debe ajustar a la lógica de tu negocio, por ejemplo,
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
}
