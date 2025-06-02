<?php

namespace App\Models\alistar_topping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alistar_topping extends Model
{
    use HasFactory;

      protected $table = 'alistar_toppings';
	protected $fillable = ['users_id','store_id', 'lote_id', 'meatcut_id','product_id','stock_actual_padre','cantidad_padre_a_procesar', 'nuevo_stock_padre' ,'fecha_alistamiento','fecha_cierre','status'];
}
