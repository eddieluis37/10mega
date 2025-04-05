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
            'prefijo' => 'PLAN',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
            
        ]);
        Centrocosto::create([
            'name' => 'CARVAJAL',
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
            'resolucion_dian' => '18764073442727',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'CENTRAL GUADALUPE CERDO',
            'prefijo' => 'CEGC',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'GALAN',
            'prefijo' => 'GALA',
            'resolucion_dian' => '18764073449011',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'PALOQUEMAO',
            'prefijo' => 'PALO',
            'resolucion_dian' => '18764074158248',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-07-01',
            'fecha_final' => '2026-07-01',
        ]);

        Centrocosto::create([
            'name' => 'SUBAZAR',
            'prefijo' => 'SUBA',
            'resolucion_dian' => '18764073453207',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'CERDO CENTRAL',
            'prefijo' => 'CCC',
            'resolucion_dian' => '18764074699610',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-07-08',
            'fecha_final' => '2026-07-08',
        ]);
        Centrocosto::create([
            'name' => 'LECHONERIA',
            'prefijo' => 'LECH',
            'resolucion_dian' => '187640',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
        Centrocosto::create([
            'name' => 'SOACHA',
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
            'name' => 'MEGACENTRAL',
            'prefijo' => 'MEGA',
            'resolucion_dian' => '18764073445065',
            'desde' => '1',
            'hasta' => '6000',
            'fecha_inicial' => '2024-06-20',
            'fecha_final' => '2026-06-20',
        ]);
    }
}
