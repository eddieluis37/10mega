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
            'prefijo' => 'PLA',
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
            'prefijo' => 'CC',
        ]);
        Centrocosto::create([
            'name' => 'GALAN',
            'prefijo' => 'GA',
        ]);
        Centrocosto::create([
            'name' => 'PALOQUEMAO',
            'prefijo' => 'PA',
        ]);

        Centrocosto::create([
            'name' => 'SUBAZAR',
            'prefijo' => '',
        ]);
        Centrocosto::create([
            'name' => 'CENTRAL',
            'prefijo' => 'CE',
        ]);
        Centrocosto::create([
            'name' => 'LECHONERIA',
            'prefijo' => 'LE',
        ]);
        Centrocosto::create([
            'name' => 'SOACHA',
            'prefijo' => 'SO',
        ]);
        Centrocosto::create([
            'name' => 'GRANJA',
            'prefijo' => 'GR',
        ]);       
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO VEHICULOS',
            'prefijo' => 'GV',
        ]);
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO MAQUINARIA',
            'prefijo' => 'GM',
        ]);
        Centrocosto::create([
            'name' => 'PLANTA2',
            'prefijo' => '',
        ]);
        Centrocosto::create([
            'name' => 'CARNES FRIAS MEGA',
            'prefijo' => 'CFM',
        ]);
        Centrocosto::create([
            'name' => 'MEGACENTRAL',
            'prefijo' => 'MC',
        ]);
    }
}
