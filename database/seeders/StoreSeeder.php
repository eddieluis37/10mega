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
            'name' => 'PRODUCTO TERMINADO',
            'description' => 'PRODUCTO QUE FABRICAMOS',            
        ]);
        Store::create([
            'centrocosto_id' => 2,
            'name' => 'AVERIAS',
            'description' => 'PRODUCTO QUE YA NO SE PUEDEN COMERCIALIZAR',         
        ]);
        Store::create([
            'centrocosto_id' => 2,
            'name' => 'LA AREPA',          
            'description' => 'BODEGA PARA DESPACHO DE SOLO ESE CLIENTE',         
        ]);
        Store::create([
            'centrocosto_id' => 2,
            'name' => 'MAQUILA',          
            'description' => 'CARGUE DEL SERVICIO MAQUILA',         
        ]);
        Store::create([
            'centrocosto_id' => 3,
            'name' => 'MEGACENTRAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 7,
            'name' => 'GUADALUPE',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 8,
            'name' => 'GALAN',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 9,
            'name' => 'PALOQUEMADO',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
           'centrocosto_id' => 10,
            'name' => 'SUBAZAR',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 11,
            'name' => 'CERDO CENTRAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 12,
            'name' => 'LECHONERIA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 14,
            'name' => 'LOCAL SOACHA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'centrocosto_id' => 1,
            'name' => 'PRODUCTO A GRANEL',          
            'description' => 'Cliente especiales',         
        ]);
        Store::create([
            'centrocosto_id' => 3,
            'name' => 'CARVAJAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
    }
}