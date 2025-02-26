<?php

namespace App\Models;

use App\Models\centros\Centrocosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_store');
    }

    /**
     * Cada tienda pertenece a un centro de costo.
     * Se asume que en la tabla "stores" existe el campo "centro_costo_id".
     */
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centrocosto_id');
    }

    /* public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    } */

    /**
     * Relación inversa con User.
     */

    public function users()
    {
        return $this->belongsToMany(User::class, 'store_user', 'store_id', 'user_id');
    }
}
