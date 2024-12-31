<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Compensadores;

class CompensadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        Compensadores::create([
            'users_id' => 1,
            'centrocosto_id' => 1,
            'store_id' => 1,
            'lote_id' => 1,
            'thirds_id' => 1,                      
            'fecha_compensado' => $now,
            'fecha_cierre' => $now,
            'factura' => 'FAC112233',   
            'status' => true,           
        ]);

        Compensadores::create([
            'users_id' => 1,
            'centrocosto_id' => 1,
            'store_id' => 1,
            'lote_id' => 1,
            'thirds_id' => 1,                      
            'fecha_compensado' => $now,
            'fecha_cierre' => $now,
            'factura' => 'FAC223344',   
            'status' => true,           
        ]);
    }
}
