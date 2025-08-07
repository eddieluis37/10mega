<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionDetail extends Model
{
     protected $fillable = ['promotion_id','centrocosto_id','store_id','category_id','lote_id','product_id','quantity','porc_desc','fecha_inicio','hora_inicio','fecha_final','hora_final','observacion','user_id','status'];
}
