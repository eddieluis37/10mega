<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\caja\Caja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'phone',
        'status',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Relación muchos a muchos con Store.
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_user', 'user_id', 'store_id');
    }

    /**
     * Método auxiliar para obtener los centros de costo asociados a las tiendas del usuario.
     */
    public function centrosCosto()
    {
        // Obtiene las tiendas con su centro de costo y luego extrae el centro de costo
        return $this->stores->pluck('centroCosto')->unique('id');
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class, 'cajero_id');
    }
}
