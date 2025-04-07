<?php

namespace App\Models;

use App\Models\centros\Centrocosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

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
}
