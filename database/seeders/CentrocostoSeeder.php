<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\centros\Centrocosto;



class CentrocostoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Centrocosto::create([
            'name' => 'PLANTA',
            'prefijo' => 'PL1',
        ]);
        Centrocosto::create([
            'name' => 'CARVAJAL',
            'prefijo' => 'CAR',
        ]);

        Centrocosto::create([
            'name' => 'GUADALUPE CALLE',
            'prefijo' => 'GUA',
        ]);
        Centrocosto::create([
            'name' => 'CENTRAL GUADALUPE CERDO',
            'prefijo' => 'CGC',
        ]);
        Centrocosto::create([
            'name' => 'GALAN',
            'prefijo' => 'GAL',
        ]);
        Centrocosto::create([
            'name' => 'PALOQUEMAO',
            'prefijo' => 'PAL',
        ]);

        Centrocosto::create([
            'name' => 'SUBAZAR',
            'prefijo' => 'SUB',
        ]);
        Centrocosto::create([
            'name' => 'CERDO CENTRAL',
            'prefijo' => 'CCC',
        ]);
        Centrocosto::create([
            'name' => 'LECHONERIA',
            'prefijo' => 'LEC',
        ]);
        Centrocosto::create([
            'name' => 'SOACHA',
            'prefijo' => 'SOA',
        ]);
        Centrocosto::create([
            'name' => 'GRANJA',
            'prefijo' => 'GRA',
        ]);       
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO VEHICULOS',
            'prefijo' => 'GMV',
        ]);
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO MAQUINARIA',
            'prefijo' => 'GMM',
        ]);
        Centrocosto::create([
            'name' => 'PLANTA2',
            'prefijo' => 'PL2',
        ]);
        Centrocosto::create([
            'name' => 'CARNES FRIAS MEGA',
            'prefijo' => 'CFM',
        ]);
        Centrocosto::create([
            'name' => 'MEGACENTRAL',
            'prefijo' => 'CMC',
        ]);
    }
}
