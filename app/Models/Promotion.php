<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';

    protected $fillable = [
        'user_id',
        'status',
        'in_process',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function promotionDetails()
    {
        return $this->hasMany(PromotionDetail::class);
    }

}
