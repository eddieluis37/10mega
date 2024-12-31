<?php

namespace Database\Seeders;

use App\Models\Lote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoteSeeder extends Seeder
{

    public function run(): void
    {
        Lote::create([
        	'product_id' => 1,
            'codigo' => 'Lote001',
            "fecha_vencimiento" => "2025-01-21"        	
        ]); 
        
        Lote::create([
            'product_id' => 26,
        	'codigo' => 'Lote002',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 

        Lote::create([
            'product_id' => 27,
        	'codigo' => 'Lote003',
            "fecha_vencimiento" => "2025-01-23"        	
        ]); 

        Lote::create([
            'product_id' => 28,
        	'codigo' => 'Lote004',
            "fecha_vencimiento" => "2025-01-23"        	
        ]); 
    }
}
