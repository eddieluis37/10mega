<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioSeeder extends Seeder
{
    public function run()
    {
        $file = database_path('data/inventarios7.csv');
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
                    'store_id' => $record['store_id'] ?? null,
                    'lote_id'  => $record['lote_id'] ?? null,
                    'product_id' => $record['product_id'] ?? null,
                    'cantidad_inventario_inicial' => $record['cantidad_inventario_inicial'] ?? 0,                   
                ];
            }
            fclose($handle);
        }

        // Inserción masiva de registros en una transacción
        DB::transaction(function () use ($data) {
            DB::table('inventarios')->insert($data);
        });

        $this->command->info('¡Datos de inventarios importados desde CSV correctamente!');
    }
}
