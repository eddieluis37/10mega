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
            'direccion' => 'Autopista Sur # 62A - 16',
            'prefijo' => 'PLAN',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',

        ]);
        Centrocosto::create([
            'name' => 'CARVAJAL',
            'direccion' => 'Calle 35 sur # 70B - 79 ',
            'prefijo' => 'CARV',
            'resolucion_dian' => '18764073452255',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);

        Centrocosto::create([
            'name' => 'GUADALUPE CALLE',
            'prefijo' => 'GUAA',
            'direccion' => 'Autopista Sur # 62A - 16',
            'resolucion_dian' => '18764073442727',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'MEGACENTRAL',
            'direccion' => 'Autopista Sur # 66 - 78 LOCAL C-28',
            'prefijo' => 'MEGA',
            'resolucion_dian' => '18764073445065',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'GALAN',
            'direccion' => 'Carrera 56 # 4G - 86',
            'prefijo' => 'GALA',
            'resolucion_dian' => '18764073449011',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'PALOQUEMAO',
            'direccion' => 'Av Calle 19 # 22 - 38 ',
            'prefijo' => 'PALO',
            'resolucion_dian' => '18764074158248',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-07-01',
            'fecha_final' => '2026-07-01',
        ]);

        Centrocosto::create([
            'name' => 'SUBAZAR',
            'direccion' => 'Carrera 91 # 145 - 50',
            'prefijo' => 'SUBA',
            'resolucion_dian' => '18764073453207',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'CERDO CENTRAL',
            'direccion' => 'Autopista Sur # 66 -78 LOCAL C-21',
            'prefijo' => 'CCC',
            'resolucion_dian' => '18764074699610',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-07-08',
            'fecha_final' => '2026-07-08',
        ]);
        Centrocosto::create([
            'name' => 'LECHONERIA',
            'direccion' => 'Dg 43 sur # 22a - 67 Santa Lucia',
            'prefijo' => 'LECH',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'SOACHA',
            'direccion' => 'Calle 13 # 8 - 53',
            'prefijo' => 'SOAC',
            'resolucion_dian' => '18764084628567',
            'desde' => '1',
            'hasta' => '10000',
            'fecha_inicial' => '2024-12-02',
            'fecha_final' => '2026-12-02',
        ]);
        Centrocosto::create([
            'name' => 'GRANJA',
            'prefijo' => 'GRAN',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO VEHICULOS',
            'prefijo' => 'GAMV',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'GASTOS Y MANTENIMIENTO MAQUINARIA',
            'prefijo' => 'GMM',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'PLANTA2',
            'prefijo' => 'PL2',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'CARNES FRIAS MEGA',
            'prefijo' => 'CFM',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);

        Centrocosto::create([
            'name' => 'VALIDAR PARA ELIMINAR',
            'prefijo' => 'CEGC',
            'direccion' => '# ',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
    }
}
