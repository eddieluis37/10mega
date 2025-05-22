<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class Subcategory_comercialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $file = database_path('data/Subcategoriascomerciales.csv');
        $data = [];

        // 1) Leer CSV
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) !== count($header)) {
                    continue;
                }
                $record = array_combine($header, $row);

                $data[] = [
                    'name'            => $record['name'] ?? null,
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ];
            }
            fclose($handle);
        }

        if (empty($data)) {
            $this->command->info('No hay datos para importar.');
            return;
        }

        // 2) Obtener el último ID actual en la tabla
        $lastId = DB::table('subcategory_comerciales')->max('id') ?? 0;

        // 3) Asignar IDs consecutivos manualmente
        foreach ($data as $i => &$row) {
            $row['id'] = ++$lastId;
        }
        unset($row);

        // 4) Inserción masiva en transacción
        DB::transaction(function () use ($data) {
            // Permitimos la inserción de IDs explícitos
            DB::table('subcategory_comerciales')->insert($data);
        });

        // 5) Ajustar el AUTO_INCREMENT para futuros inserts sin ID
        DB::statement('ALTER TABLE subcategory_comerciales AUTO_INCREMENT = ' . ($lastId + 1));

        $this->command->info('¡Datos de subcategory_comerciales importados y IDs secuenciales desde CSV correctamente!');
    }
}
