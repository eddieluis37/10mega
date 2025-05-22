<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory_comerciales extends Model
{
    use HasFactory;

    protected $table = 'subcategory_comerciales';

    protected $fillable = ['name','image'];
}
