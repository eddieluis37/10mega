<?php

namespace App\Models\caja;

use App\Models\CajaReciboDineroDetail;
use App\Models\centros\Centrocosto;
use App\Models\Recibodecaja;
use App\Models\Sale;
use App\Models\SaleCaja;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $dates = ['fecha_hora_inicio', 'fecha_hora_cierre'];

    protected $fillable = [
        'user_id',
        'centrocosto_id',
        'cajero_id',
        'cantidad_facturas',
        'factura_inicial',
        'factura_final',
        'base',
        'efectivo',
        'retiro_caja',
        'total',
        'valor_real',
        'diferencia',
        'fecha_hora_inicio',
        'fecha_hora_cierre',
        'estado',
        'status',
    ];

    // Opcional: casteo de campos de fecha para usar métodos de Carbon
    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_cierre' => 'datetime', // Si lo usas
    ];

    // Relación con el usuario que creó la caja
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el cajero (usuario).
     */
    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    /**
     * Relación con el centro de costo.
     */
    public function centroCosto()
    {
        return $this->belongsTo(Centrocosto::class, 'centrocosto_id');
    }

    // Relación con los recibos generados en este turno de caja
    public function recibosDeCaja()
    {
        return $this->hasMany(Recibodecaja::class);
    }

    /**
     * Accessor para obtener el nombre del centro de costo.
     */
    public function getNamecentrocostoAttribute()
    {
        return $this->centroCosto ? $this->centroCosto->name : '';
    }

    /**
     * Accessor para obtener el nombre del cajero.
     */
    public function getNamecajeroAttribute()
    {
        return $this->cajero ? $this->cajero->name : '';
    }

    /**
     * Relación a la tabla pivote sale_caja (cada registro indica
     * que una venta pertenece a esta caja).
     */
    public function saleCajas()
    {
        return $this->hasMany(SaleCaja::class, 'caja_id', 'id');
    }

    /**
     * Relación con las ventas a través de la tabla pivote sale_caja.
     * Esta relación se utiliza tanto en el método create (para obtener las ventas del turno vigente)
     * como en el reporte de cierre de caja.
     */
    // Relación many-to-many con las ventas, vía la tabla pivot sale_caja
    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_caja', 'caja_id', 'sale_id');
    }

    // Detalle de los movimientos de dinero asociados a la caja
    public function detallesMovimiento()
    {
        return $this->hasMany(CajaReciboDineroDetail::class, 'caja_id');
    }

    /**
     * (Opcional) Relación alternativa para obtener las ventas asociadas al cajero de la caja.
     * Puede usarse en casos donde la relación se base en que el cajero de la caja
     * sea igual al user_id de la venta.
     */
    public function salesByCajero()
    {
        return $this->hasMany(Sale::class, 'user_id', 'cajero_id');
    }

    /** Retiros de efectivo de esta caja */
    public function salidasEfectivo()
    {
        return $this->hasMany(Cajasalidaefectivo::class, 'caja_id');
    }
}
