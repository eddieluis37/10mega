<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formapago extends Model
{

    protected $table = 'formapagos';
    protected $fillable = ['codigo', 'nombre', 'tipoformapago', 'diascredito', 'cuenta'];

    public function scopeEfectivoTarjeta($query)
    {
        return $query->whereIn('tipoformapago', ['EFECTIVO', 'TARJETA']);
    }
}
