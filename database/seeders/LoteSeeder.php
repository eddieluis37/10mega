<?php

namespace Database\Seeders;

use App\Models\Lote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;


class LoteSeeder extends Seeder
{

    public function run(): void
    {
        Lote::create([       	
            'category_id' => 1,
            'codigo' => '010125T1',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([
            'category_id' => 1,
            'codigo' => '020125T2',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([        
            'category_id' => 2,
            'codigo' => '030125T3',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([            
        	'codigo' => '040125T4',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([            
        	'codigo' => '050125T5',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([
          
        	'codigo' => '060125T6',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
        Lote::create([
            
        	'codigo' => '070125T7',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 


        Lote::create([
            
        	'codigo' => '080125T8',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 

        Lote::create([
          
        	'codigo' => '090125T9',
            'fecha_vencimiento' => Carbon::now()->addMonths(6), 
        ]); 
    }
}
