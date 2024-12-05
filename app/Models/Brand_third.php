<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand_third extends Model
{
    use HasFactory;

    protected $table = 'brand_third';

    protected $fillable = ['name', 'third_id', 'brand_id'];

    
}
