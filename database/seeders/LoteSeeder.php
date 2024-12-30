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
        	'codigo' => 'Lote001',
            "fecha_vencimiento" => "2025-01-21"        	
        ]); 
        
        Lote::create([
        	'codigo' => 'Lote002',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 

        Lote::create([
        	'codigo' => 'Lote003',
            "fecha_vencimiento" => "2025-01-23"        	
        ]); 
    }
}
