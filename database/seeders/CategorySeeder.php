<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
        	'name' => 'FABRICADOS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
        	'name' => 'NO FABRICADOS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
        	'name' => 'PARRILLA',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
        	'name' => 'CARNES CRUDAS',
        	'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'BAR',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'LECHONERIA',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);       
        Category::create([
            'name' => 'MAQUILAS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'CONDIMENTOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'MATERIAS PRIMAS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'MATERIAS PRIMAS EXENTA',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'NO GRAVADOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'GRAVADOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'RES',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'CERDO',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
        Category::create([
            'name' => 'POLLOS',
            'image' => 'https://dummyimage.com/200x150/5c5756/fff'
        ]);
    }
}
