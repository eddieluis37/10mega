<?php

namespace App\Models\alistar_topping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class alistar_topping_details extends Model
{
    use HasFactory;
    protected $table = 'alistar_topping_details';
	protected $fillable = ['alistar_toppings_id','products_id','meatcut_id','kgrequeridos', 'porc_venta', 'costo_total', 'costo_kilo', 'utilidad', 'porc_utilidad', 'newstock','status'];

}
