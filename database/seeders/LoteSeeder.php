<?php

namespace Database\Seeders;

use App\Models\Lote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class LoteSeeder extends Seeder
{

    public function run()
    {
        $file = database_path('data/lote4.csv');
        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            // Se asume que la primera fila es el encabezado
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Verificar que la cantidad de columnas de la fila coincide con la del encabezado
                if (count($row) !== count($header)) {
                    // Puedes registrar un log o simplemente saltar la fila
                    continue;
                }
                
                $record = array_combine($header, $row);
                
                $data[] = [
                    'codigo' => $record['codigo'] ?? null,
                    'fecha_vencimiento'  => $record['fecha_vencimiento'] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),                                     
                ];
            }
            fclose($handle);
        }

        // Inserción masiva de registros en una transacción
        DB::transaction(function () use ($data) {
            DB::table('lotes')->insert($data);
        });

        $this->command->info('¡Datos de lotes importados desde CSV correctamente!');
    }
}
