<?php

namespace App\Models\alistamiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class enlistment_details extends Model
{
    use HasFactory;
    protected $table = 'enlistment_details';
	protected $fillable = ['enlistments_id','products_id','meatcut_id','kgrequeridos', 'porc_venta', 'costo_total', 'costo_kilo', 'utilidad', 'porc_utilidad', 'newstock','status'];
}
