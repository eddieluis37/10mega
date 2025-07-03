<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermissionsExport;

class ExportPermissionsToExcel extends Command
{
    protected $signature = 'export:permissions';
    protected $description = 'Exportar módulos, roles y permisos a un archivo Excel';

    public function handle()
    {
        $fileName = 'permissions_export_' . now()->format('Ymd_Hi') . '.xlsx';
        Excel::store(new PermissionsExport, $fileName, 'local');
        $this->info("Archivo generado en storage/app/{$fileName}");
    }
}