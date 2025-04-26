<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Restaurantorderitem extends Model
{
    protected $table = 'restaurant_order_items';
    protected $fillable = ['restaurant_order_id','item_type','item_id','quantity','unit_price'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo(null, 'item_type', 'item_id');
    }
}
