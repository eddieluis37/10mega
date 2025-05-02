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

        // 2) Preparar auditoría
        $failed = [];

        // 3) Deshabilitar FK y procesar cada fila individualmente
        Schema::disableForeignKeyConstraints();

        foreach ($rows as $rec) {
            try {
                $inserted = DB::table('inventarios')->updateOrInsert(
                    [
                        'store_id'   => $rec['store_id'],  
                        'lote_id'    => $rec['lote_id'],   
                        'product_id' => $rec['product_id'],
                    ],
                    [
                        'cantidad_inventario_inicial' => $rec['cantidad_inventario_inicial'],  
                        'created_at'                  => $rec['created_at'],
                        'updated_at'                  => $rec['updated_at'],
                    ]
                );

                if (! $inserted) {
                    // updateOrInsert returns false when an existing record is updated
                    // Puedes usar esto para auditoría opcional si sólo te interesan inserts
                    // $failed[] = ['rec' => $rec, 'reason' => 'Actualizado, no insertado'];
                }
            } catch (QueryException $e) {
                $failed[] = [
                    'record' => $rec,
                    'error'  => $e->getMessage(),
                ];
                // Continuar con siguientes sin abortar todo el proceso
            }
        }

        Schema::enableForeignKeyConstraints();

        // 4) Reportar auditoría en consola y log
        $countFailed = count($failed);
        if ($countFailed > 0) {
            $this->command->warn("Se detectaron {$countFailed} registros con inconsistencias:");
            foreach ($failed as $f) {
                $this->command->line(
                    "store_id={$f['record']['store_id']}, ".
                    "lote_id={$f['record']['lote_id']}, ".
                    "product_id={$f['record']['product_id']} -> {$f['error']}"
                );
            }

            // Adicional: guardar en un archivo de log
            Log::channel('daily')->error('Errores al importar inventarios', $failed);
        } else {
            $this->command->info('Todos los registros importados/actualizados correctamente.');
        }
    }
}
