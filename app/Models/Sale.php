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
        'consecutivo',
        'valor_a_pagar_efectivo',
        'total',
        'total_iva',
        'items',
        'cash',
        'cambio',
        'status',
        'fecha',
        'resol',
        'user_id',
        'store_id',
        'third_id',
        'vendedor_id',
        'domiciliario_id',
        'centrocosto_id'
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
     * comparando el created_at con el inicio y fin del turno.
     */
    public function scopeTurnoVigente($query)
    {
        // Ejemplo: suponiendo que el turno es del día actual
        return $query->whereDate('created_at', today());
    }
}
