<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loss extends Model
{
    protected $fillable = ['store_id','product_id','quantity','reason','reported_by'];
}
