<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'name' => 'CARNES FRIAS MEGA',
        ]);
        Brand::create([
            'name' => 'CALYPSO',
        ]);
        Brand::create([
            'name' => 'CAMPO FRUTAL',
        ]);
        Brand::create([
            'name' => 'CONDIMENTOS DON DIEGO',
        ]);
        Brand::create([
            'name' => 'DELIKA',
        ]);
        Brand::create([
            'name' => 'DEL CASINO',
        ]);
        Brand::create([
            'name' => 'RIO GRANDE',
        ]);
        Brand::create([
            'name' => 'CONDIMENTOS DON DIEGO DE CARNES FRIAS MEGA',
        ]);
        Brand::create([
            'name' => 'EL BUEN SURTIR',
        ]);
        Brand::create([
            'name' => 'QUESOS Y LACTEOS LA TURQUEZA',
        ]);
        Brand::create([
            'name' => 'LA ANTIOQUEÑA',
        ]);
        Brand::create([
            'name' => 'GUSTAMAS',
        ]);
        Brand::create([
            'name' => 'AVICOLA LOS CAMBULOS DE LOS CAMBULES',
        ]);
        Brand::create([
            'name' => 'AVICOLA LOS CAMBULOS DE CARNES FRIAS MEGA',
        ]);
        Brand::create([
            'name' => 'SANTA CLARA',
        ]);
        Brand::create([
            'name' => 'CARNES FRIAS MEGA DE CAMPOS FRUTAL SAS',
        ]);
        Brand::create([
            'name' => 'AVICOLA LOS CAMBULOS DE CARNES BONANZA JEV S.A.S',
        ]);
        Brand::create([
            'name' => 'APRELLA',
        ]);
        Brand::create([
            'name' => 'LA GRANJA',
        ]);
        Brand::create([
            'name' => 'EL BUEN SURTIR DE LA GRANJA',
        ]);
        Brand::create([
            'name' => 'BARY',
        ]);
        Brand::create([
            'name' => 'CARNES FRIAS MEGA DE QUESOS Y LACTEOS LA TURQUESA',
        ]);
        Brand::create([
            'name' => 'GENERAL',
        ]);
    }
}
