<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category_comerciales extends Model
{
    use HasFactory;

    protected $table = 'categories_comerciales';

    protected $fillable = ['name','image'];

}
