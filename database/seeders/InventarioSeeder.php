<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;

class InventarioSeeder extends Seeder
{
    public function run()
    {
        $file = database_path('data/inventarios8.csv');
        $rows = [];

        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) !== count($header)) continue;
                $rec = array_combine($header, $row);

                $rows[] = [
                    'store_id'   => $rec['store_id'],
                    'lote_id'    => $rec['lote_id'],
                    'product_id' => $rec['product_id'],
                    'cantidad_inventario_inicial' => $rec['cantidad_inventario_inicial'] ?? 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            fclose($handle);
        }

        if (empty($rows)) {
            $this->command->info('No hay datos en el CSV para importar.');
            return;
        }

        // Ejecuta el upsert en bloque:
        DB::table('inventarios')->upsert(
            $rows,
            // columnas únicas
            ['store_id', 'lote_id', 'product_id'],
            // columnas a actualizar en caso de duplicado
            ['cantidad_inventario_inicial', 'updated_at']
        );

        $this->command->info('Todos los registros importados/actualizados correctamente.');
    }
}
