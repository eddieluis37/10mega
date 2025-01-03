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
            'category_id' => 1,
            'codigo' => 'Lote001',
            "fecha_vencimiento" => "2025-01-21"        	
        ]); 
        Lote::create([
            'category_id' => 1,
            'codigo' => 'Lote002',
            "fecha_vencimiento" => "2025-01-21"        	
        ]); 
        Lote::create([        
            'category_id' => 2,
            'codigo' => 'Lote003',
            "fecha_vencimiento" => "2025-01-21"        	
        ]); 
        Lote::create([            
        	'codigo' => 'Lote004',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 
        Lote::create([            
        	'codigo' => 'Lote005',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 
        Lote::create([
          
        	'codigo' => 'Lote006',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 
        Lote::create([
            
        	'codigo' => 'Lote007',
            "fecha_vencimiento" => "2025-01-22"        	
        ]); 


        Lote::create([
            
        	'codigo' => 'Lote008',
            "fecha_vencimiento" => "2025-01-23"        	
        ]); 

        Lote::create([
          
        	'codigo' => 'Lote009',
            "fecha_vencimiento" => "2025-01-23"        	
        ]); 
    }
}
