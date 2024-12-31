<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movimiento_inventario;

class Movimiento_inventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        Movimiento_inventario::create([            
            'tipo' => 'compensadores',
            'compensadores_id' => 1,
            'bodega_origen_id' => 1,
            'bodega_origen_id' => 2,
            'lote_id' => 1,
            'fecha' => $now,
            'cantidad' => '10',                                
        ]); 

        Movimiento_inventario::create([            
            'tipo' => 'compensadores',
            'compensadores_id' => 2,
            'bodega_origen_id' => 1,
            'bodega_origen_id' => 2,
            'lote_id' => 1,
            'fecha' => $now,
            'cantidad' => '10',                                
        ]); 
    }
}
