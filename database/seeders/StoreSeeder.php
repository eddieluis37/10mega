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
            'name' => 'PRODUCTO TERMINADO',
            'description' => 'PRODUCTO QUE FABRICAMOS',            
        ]);
        Store::create([
            'name' => 'AVERIAS',
            'description' => 'PRODUCTO QUE YA NO SE PUEDEN COMERCIALIZAR',         
        ]);
        Store::create([
            'name' => 'LA AREPA',          
            'description' => 'BODEGA PARA DESPACHO DE SOLO ESE CLIENTE',         
        ]);
        Store::create([
            'name' => 'MAQUILA',          
            'description' => 'CARGUE DEL SERVICIO MAQUILA',         
        ]);
        Store::create([
            'name' => 'MEGACENTRAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'GUADALUPE',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'GALAN',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'PALOQUEMADO',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'SUBAZAR',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'CERDO CENTRAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'LECHONERIA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'LOCAL SOACHA',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
        Store::create([
            'name' => 'PRODUCTO A GRANEL',          
            'description' => 'Cliente especiales',         
        ]);
        Store::create([
            'name' => 'CARVAJAL',          
            'description' => 'Para traslado entre Bodegas',         
        ]);
    }
}