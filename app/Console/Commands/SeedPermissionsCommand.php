<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\PermissionsSeeder;

class SeedPermissionsCommand extends Command
{
    protected $signature = 'permissions:seed';
    protected $description = 'Ejecuta el seeder de permisos y roles';

    public function handle()
    {
        $this->call(PermissionsSeeder::class);
        $this->info('Permisos y roles actualizados correctamente.');
    }
}
