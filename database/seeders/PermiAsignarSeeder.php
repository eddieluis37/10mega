<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermiAsignarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      /*   $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all()); */

        $editor = Role::create(['name' => 'Editor']);
        $editor->givePermissionTo(['ver_usuarios', 'editar_usuarios']);

        $recibidoPlanta = Role::create(['name' => 'RecibidoPlanta']);
        $recibidoPlanta->givePermissionTo(['ver_compra_productos', 'editar_compra_productos']);
    }
}
