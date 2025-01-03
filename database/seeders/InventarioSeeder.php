<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventario;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Inventario::create([            
            'store_id' => 1,
            'lote_id' => 1,
            'cantidad_actual' => '0',                               
        ]); 

        Inventario::create([            
            'store_id' => 2,
            'lote_id' => 2,
            'cantidad_actual' => '0',                               
        ]); 
    }
}
