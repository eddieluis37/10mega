<?php

namespace App\Models;

use App\Models\centros\Centrocosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_store');
    }

    public function centroCosto()
    {
        return $this->belongsTo(Centrocosto::class);
    }

    /* public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    } */

    public function users()
    {
        return $this->belongsToMany(User::class, 'store_user', 'store_id', 'user_id');
    }
}
