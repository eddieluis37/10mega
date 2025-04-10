<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuentas_por_cobrar extends Model
{
    use HasFactory;
    
    protected $table = 'cuentas_por_cobrars';
    protected $fillable = ['user_id','parametrocontable_id','sale_id','third_id','status','fecha_inicial','fecha_vencimiento','deuda_inicial', 'deuda_x_cobrar'];
}
