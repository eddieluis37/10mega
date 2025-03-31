<?php

namespace App\Models\caja;

use App\Models\centros\Centrocosto;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

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

    /**
     * Relación con el centro de costo.
     */
    public function centroCosto()
    {
        return $this->belongsTo(Centrocosto::class, 'centrocosto_id');
    }

    /**
     * Relación con el cajero (usuario).
     */
    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
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

    // Puedes agregar aquí la relación con las ventas (sales)
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id', 'cajero_id');
    }
}
