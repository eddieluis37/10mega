<?php

namespace App\Models\alistamiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alistamiento extends Model
{
    use HasFactory;

    protected $table = 'enlistments';
	protected $fillable = ['users_id','store_id', 'lote_id', 'meatcut_id','product_id', 'cantidad_padre_a_procesar', 'nuevo_stock_padre' ,'fecha_alistamiento','fecha_cierre','status'];
}
