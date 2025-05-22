<?php

namespace Database\Seeders;

use App\Models\Category_comerciales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Category_comercialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category_comerciales::create([
            'name' => 'AVES',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'BAR',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'CARNICOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'CERDO',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'CONGELADOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'LACTEOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'LECHONERIA',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'MAQUILAS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'MUESTRAS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'PANEDERIA',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'PARRILLA',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'PESCADOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category_comerciales::create([
            'name' => 'RES',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
         Category_comerciales::create([
        	'name' => 'SALSAS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
         Category_comerciales::create([
        	'name' => 'SERVICIOS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
         Category_comerciales::create([
        	'name' => 'TERNERA',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
         Category_comerciales::create([
        	'name' => 'VARIOS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
    }
}
