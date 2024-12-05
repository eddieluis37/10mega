<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandThirdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brand_third')->insert([
            ['name' => 'CARNES FRIAS MEGA', 
            'brand_id' => 1, 'third_id' => 1],            
            ['name' => 'CALYPSO', 
            'brand_id' => 2, 'third_id' => 2],
            ['name' => 'CAMPO FRUTAL',
             'brand_id' => 3, 'third_id' => 3],
            ['name' => 'CONDIMENTOS DON DIEGO',
             'brand_id' => 4, 'third_id' => 4],
            ['name' => 'DELIKA',
             'brand_id' => 5, 'third_id' => 5],
             ['name' => 'DEL CASINO',
             'brand_id' => 6, 'third_id' => 6],
             ['name' => 'RIO GRANDE',
             'brand_id' => 7, 'third_id' => 7],
             
             ['name' => 'CONDIMENTOS DON DIEGO DE CARNES FRIAS MEGA',
             'brand_id' => 4, 'third_id' => 1],

             ['name' => 'EL BUEN SURTIR',
             'brand_id' => 8, 'third_id' => 1],
             ['name' => 'QUESOS Y LACTEOS LA TURQUEZA',
             'brand_id' => 9, 'third_id' => 9],

             ['name' => 'LA ANTIOQUEÑA',
             'brand_id' => 10, 'third_id' => 8],
             ['name' => 'GUSTAMAS',
             'brand_id' => 11, 'third_id' => 11],
             ['name' => 'AVICOLA LOS CAMBULOS DE AVICOLA LOS CAMBULES',
             'brand_id' => 12, 'third_id' => 12],
             ['name' => 'AVICOLA LOS CAMBULOS DE CARNES FRIAS MEGA',
             'brand_id' => 12, 'third_id' => 1],
             ['name' => 'SANTA CLARA',
             'brand_id' => 13, 'third_id' => 13],

             ['name' => 'CARNES FRIAS MEGA DE CAMPOS FRUTAL SAS',
             'brand_id' => 1, 'third_id' => 3],

             ['name' => 'AVICOLA LOS CAMBULOS DE CARNES BONANZA JEV S.A.S',
             'brand_id' => 12, 'third_id' => 16],
             
             ['name' => 'APRELLA',
             'brand_id' => 14, 'third_id' => 14],
             ['name' => 'LA GRANJA',
             'brand_id' => 15, 'third_id' => 18],
             ['name' => 'EL BUEN SURTIR DE LA GRANJA',
             'brand_id' => 8, 'third_id' => 18],
             ['name' => 'BARY',
             'brand_id' => 16, 'third_id' => 19],

             ['name' => 'CARNES FRIAS MEGA DE QUESOS Y LACTEO LA TURQUESA',
             'brand_id' => 1, 'third_id' => 9],

             ['name' => 'GENERAL',
             'brand_id' => 17, 'third_id' => 1],            
        ]);
    }
}
