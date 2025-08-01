<?php

namespace App\Models;

use App\Models\centros\Centrocosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Inventario;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = ['name'];

    /**
     * Cada tienda pertenece a un centro de costo.
     * En la tabla "stores" existe el campo "centrocosto_id".
     */
    public function centroCosto()
    {
        return $this->belongsTo(Centrocosto::class, 'centrocosto_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_store');
    }      

    public function users()
    {
        return $this->belongsToMany(User::class, 'store_user', 'store_id', 'user_id');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'store_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'store_id');
    }
}
