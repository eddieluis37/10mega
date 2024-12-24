<?php

namespace Database\Seeders;

use App\Models\ProductStore;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductStore::create([
            'centro_costo_id' => 6, 'store_id' => 5,  'quantity' => 1        	
        ]); 
        
        ProductStore::create([
          'centro_costo_id' => 7,  'store_id' => 6,  'quantity' => 2   	
        ]); 

        ProductStore::create([
           'centro_costo_id' => 8,  'store_id' => 7, 'quantity' => 3
        ]); 
    }
}
