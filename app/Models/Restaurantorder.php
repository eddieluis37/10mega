<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurantorder extends Model
{
    protected $fillable = ['sale_id','table_number','waiter_id','status'];
    protected $table = 'restaurant_orders';

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }
}
