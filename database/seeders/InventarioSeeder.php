<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                    'store_id'                       => $record['store_id'] ?? null,
                    'lote_id'                        => $record['lote_id'] ?? null,
                    'product_id'                     => $record['product_id'] ?? null,
                    'cantidad_inventario_inicial'    => $record['cantidad_inventario_inicial'] ?? 0,
                    'created_at'                     => Carbon::now(),
                    'updated_at'                     => Carbon::now(),
                ];
            }
            fclose($handle);
        }

        if (empty($rows)) {
            $this->command->info('No hay datos en el CSV para importar.');
            return;
        }

        // 2) Sacar el último ID en la tabla
        $lastId = DB::table('inventarios')->max('id') ?? 0;

        // 3) Asignar IDs secuenciales a todos los registros del CSV
        foreach ($rows as $i => &$rec) {
            $rec['id'] = ++$lastId;
        }
        unset($rec);

        // 4) Upsert masivo: inserta nuevos o actualiza los existentes
        DB::transaction(function () use ($rows) {
            DB::table('inventarios')->upsert(
                // datos
                $rows,
                // columnas únicas para match
                ['store_id', 'lote_id', 'product_id'],
                // columnas a actualizar en caso de match
                ['cantidad_inventario_inicial', 'updated_at']
            );
        });

        // 5) Ajustar el AUTO_INCREMENT para futuros inserts
        DB::statement('ALTER TABLE inventarios AUTO_INCREMENT = ' . ($lastId + 1));

        $this->command->info('¡Inventarios importados/actualizados correctamente, con IDs secuenciales!');
    }
}
