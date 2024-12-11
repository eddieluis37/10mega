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
        	'name' => 'ABC1234',
            "fecha_vencimiento" => "2024-01-21"        	
        ]); 
        
        Lote::create([
        	'name' => 'DRF5678',
            "fecha_vencimiento" => "2024-01-22"        	
        ]); 

        Lote::create([
        	'name' => 'HIJ9875',
            "fecha_vencimiento" => "2024-01-23"        	
        ]); 
    }
}
