<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class InventarioSeeder extends Seeder
{
    public function run()
    {
        $file = database_path('data/inventarios8.csv');
        $rows = [];

        // 1) Leer CSV y armar array de datos
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) !== count($header)) {
                    continue;
                }
                $record = array_combine($header, $row);
                $rows[] = [
                    'store_id'                    => $record['store_id'] ?? null,
                    'lote_id'                     => $record['lote_id'] ?? null,
                    'product_id'                  => $record['product_id'] ?? null,
                    'cantidad_inventario_inicial' => $record['cantidad_inventario_inicial'] ?? 0,
                    'created_at'                  => Carbon::now(),
                    'updated_at'                  => Carbon::now(),
                ];
            }
            fclose($handle);
        }

        if (empty($rows)) {
            $this->command->info('No hay datos en el CSV para importar.');
            return;
        }

        // 2) Procesar cada fila con updateOrInsert
        DB::transaction(function () use ($rows) {
            // Deshabilitar temporalmente FKs
            Schema::disableForeignKeyConstraints();

            foreach ($rows as $rec) {
                DB::table('inventarios')->updateOrInsert(
                    // Condición de búsqueda: si existe un inventario con estas 3 claves
                    [
                        'store_id'   => $rec['store_id'],
                        'lote_id'    => $rec['lote_id'],
                        'product_id' => $rec['product_id'],
                    ],
                    // Datos a insertar o actualizar
                    [
                        'cantidad_inventario_inicial' => $rec['cantidad_inventario_inicial'],
                        'created_at'                  => $rec['created_at'],   // sólo usará esta fecha si inserta
                        'updated_at'                  => $rec['updated_at'],
                    ]
                );
            }

            // Volver a habilitar FKs
            Schema::enableForeignKeyConstraints();
        });

        $this->command->info('¡Inventarios sincronizados correctamente (insert/update)!');
    }
}