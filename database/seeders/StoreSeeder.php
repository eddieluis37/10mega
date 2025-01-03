<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'PRODUCTO TERMINADO PLANTA',
            'description' => 'PRODUCTO QUE FABRICAMOS',            
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'CONDIMENTOS PLANTA',
            'description' => 'NO TIENE',            
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'MATERIA PRIMA PLANTA',
            'description' => 'NO TIENE',            
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'AVERIAS PLANTA',
            'description' => 'PRODUCTO QUE YA NO SE PUEDEN COMERCIALIZAR',         
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'LA AREPA PLANTA',          
            'description' => 'BODEGA PARA DESPACHO DE SOLO ESE CLIENTE',         
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'MAQUILA PLANTA',          
            'description' => 'CARGUE DEL SERVICIO MAQUILA',         
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'PRODUCTO A GRANEL PLANTA',          
            'description' => 'CLIENTE ESPECIALES',         
        ]);
        Store::create([
            'centrocosto_id' => 2,
            'name' => 'CARVAJAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 3,
            'name' => 'GUADALUPE CALLE',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 4,
            'name' => 'CENTRAL GUADALUPE',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 5,
            'name' => 'GALAN',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 5,
            'name' => 'PARRILLA GALAN',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 5,
            'name' => 'BAR GALAN',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 6,
            'name' => 'PALOQUEMADO',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
           'centrocosto_id' => 7,
            'name' => 'SUBAZAR',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 7,
             'name' => 'PARRILLA SUBAZAR',          
             'description' => 'Para traslado entre Bodegas',         
         ]);
         Store::create([
            'centrocosto_id' => 7,
             'name' => 'BAR SUBA',          
             'description' => 'Para traslado entre Bodegas',         
         ]);
         Store::create([
            'centrocosto_id' => 7,
             'name' => 'SUBAZAR',          
             'description' => 'Para traslado entre Bodegas',         
         ]);
        Store::create([
            'centrocosto_id' => 8,
            'name' => 'CERDO CENTRAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 9,
            'name' => 'LECHONERIA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);       
        Store::create([
            'centrocosto_id' => 9,
            'name' => 'COCINA LECHONERIA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 9,
            'name' => 'MATERIA PRIMA LECHONERIA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 10,
            'name' => 'SOACHA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 10,
            'name' => 'PARRILLA SOACHA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 10,
            'name' => 'BAR SOACHA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);             
    }
}