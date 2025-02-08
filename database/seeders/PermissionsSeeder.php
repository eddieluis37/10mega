<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = ['usuarios', 'compra_productos', 'bodegas', 'ventas']; // Agrega los módulos necesarios

        // Crear o actualizar permisos
        foreach ($modules as $module) {
            Permission::updateOrCreate(['name' => "ver_{$module}"]);
            Permission::updateOrCreate(['name' => "crear_{$module}"]);
            Permission::updateOrCreate(['name' => "editar_{$module}"]);
            Permission::updateOrCreate(['name' => "eliminar_{$module}"]);
        }

        // Obtener todos los permisos
        $allPermissions = Permission::all();

        // Crear o actualizar roles
        $admin = Role::updateOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($allPermissions); // Asigna todos los permisos

        $editor = Role::updateOrCreate(['name' => 'Editor']);
        $editor->syncPermissions(['ver_usuarios', 'editar_usuarios']); // Solo permisos específicos

        $viewer = Role::updateOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions(['ver_usuarios']);
        
        $recibidoPlanta = Role::updateOrCreate(['name' => 'RecibidoPlanta']);
        $recibidoPlanta->syncPermissions(['ver_compra_productos', 'editar_compra_productos']);
    }
}
