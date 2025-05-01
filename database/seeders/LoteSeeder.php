<?php

namespace Database\Seeders;

use App\Models\Lote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LoteSeeder extends Seeder
{
    public function run()
    {
        $file = database_path('data/lote5.csv');
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
                    'codigo'            => $record['codigo'] ?? null,
                    'fecha_vencimiento' => $record['fecha_vencimiento'] ?? null,
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
        $lastId = DB::table('lotes')->max('id') ?? 0;

        // 3) Asignar IDs consecutivos manualmente
        foreach ($data as $i => &$row) {
            $row['id'] = ++$lastId;
        }
        unset($row);

        // 4) Inserción masiva en transacción
        DB::transaction(function () use ($data) {
            // Permitimos la inserción de IDs explícitos
            DB::table('lotes')->insert($data);
        });

        // 5) Ajustar el AUTO_INCREMENT para futuros inserts sin ID
        DB::statement('ALTER TABLE lotes AUTO_INCREMENT = ' . ($lastId + 1));

        $this->command->info('¡Datos de lotes importados y IDs secuenciales desde CSV correctamente!');
    }
}
