<?php

namespace App\Models\centros;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class Centrocosto extends Model
{
    use HasFactory;
    protected $table = 'centro_costo';
    protected $fillable = ['name', 'status'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'centro_costo_products', 'centro_costo_id', 'product_id')
            ->withPivot('quantity');
    }

    /**
     * Relación con Store.
     * Un centro de costo puede tener muchas tiendas/bodegas asociadas.
     */
    public function stores()
    {
        return $this->hasMany(Store::class, 'centrocosto_id');
    }
}
