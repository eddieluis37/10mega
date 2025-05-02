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
                    'store_id'   => $record['store_id'] ?? null,
                    'lote_id'    => $record['lote_id'] ?? null,
                    'product_id' => $record['product_id'] ?? null,
                    'cantidad_inventario_inicial' => $record['cantidad_inventario_inicial'] ?? 0,
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

        // 2) Deshabilitar temporalmente constraints (opcional)
        Schema::disableForeignKeyConstraints();

        $failed = [];

        // 3) Recorrer cada fila y hacer insert o update manual
        foreach ($rows as $rec) {
            try {
                $query = DB::table('inventarios')
                    ->where('store_id',   $rec['store_id'])
                    ->where('lote_id',    $rec['lote_id'])
                    ->where('product_id', $rec['product_id']);

                if ($query->exists()) {
                    // Ya existe: actualizamos solo cantidad y updated_at
                    $query->update([
                        'cantidad_inventario_inicial' => $rec['cantidad_inventario_inicial'],
                        'updated_at'                  => $rec['updated_at'],
                    ]);
                } else {
                    // No existe: insertamos el registro completo
                    DB::table('inventarios')->insert($rec);
                }
            } catch (QueryException $e) {
                $failed[] = [
                    'record' => $rec,
                    'error'  => $e->getMessage(),
                ];
            }
        }

        Schema::enableForeignKeyConstraints();

        // 4) Reportar auditoría en consola y log
        if (count($failed) > 0) {
            $this->command->warn('Se detectaron ' . count($failed) . ' registros con errores:');
            foreach ($failed as $f) {
                $r = $f['record'];
                $this->command->line(
                    "store_id={$r['store_id']}, lote_id={$r['lote_id']}, product_id={$r['product_id']} -> {$f['error']}"
                );
            }
            Log::channel('daily')->error('Errores al importar inventarios', $failed);
        } else {
            $this->command->info('Todos los registros importados/actualizados correctamente.');
        }
    }
}
