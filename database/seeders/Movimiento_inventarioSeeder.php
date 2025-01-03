<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MovimientoInventario;

class Movimiento_inventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        MovimientoInventario::create([            
            'tipo' => 'compensadores',
            'compensador_id' => 1,
            'store_origen_id' => 1,
            'store_destino_id' => 2,
            'lote_id' => 1,
            'fecha' => $now,
            'cantidad' => '0',                                
        ]); 

        MovimientoInventario::create([            
            'tipo' => 'compensadores',
            'compensador_id' => 2,
            'store_origen_id' => 1,
            'store_destino_id' => 2,
            'lote_id' => 2,
            'fecha' => $now,
            'cantidad' => '0',                                
        ]); 
    }
}
