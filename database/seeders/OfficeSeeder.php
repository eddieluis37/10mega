<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;


class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Office::create([
            'name' => 'PLANTA',
        ]);
        Office::create([
            'name' => 'CARVAJAL',
        ]);
        Office::create([
            'name' => 'GUADALUPE CALLE',
        ]);
        Office::create([
            'name' => 'CENTRAL GUADALUPE CERDO',
        ]);
        Office::create([
            'name' => 'GALAN',
        ]);
        Office::create([
            'name' => 'PALOQUEMAO',
        ]);
        Office::create([
            'name' => 'SUBAZAR',
        ]);
        Office::create([
            'name' => 'CERDO CENTRAL',
        ]);
        Office::create([
            'name' => 'LECHONERIA',
        ]);
        Office::create([
            'name' => 'SOACHA',
        ]);
        Office::create([
            'name' => 'GRANJA',
        ]);
        Office::create([
            'name' => 'GASTOS Y MANTENIMIENTO VEHICULOS',
        ]);
        Office::create([
            'name' => 'GASTOS Y MANTENIMIENTO MAQUINARIA',
        ]);
        Office::create([
            'name' => 'PLANTA2',
        ]);
        Office::create([
            'name' => 'CARNES FRIAS MEGA',
        ]);
        Office::create([
            'name' => 'MEGACENTRAL',
        ]);
    }
}
