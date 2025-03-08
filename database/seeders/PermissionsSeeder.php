<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = ['usuarios', 'compras', 'compra_lote', 'compra_productos', 'alistamiento', 'traslado', 'inventario', 'cargue_productos_term', 'ventas', 'venta_pos', 'venta_dom', 'orders', 'bodegas', 'ventas']; // Agrega los módulos necesarios

        // Crear o actualizar permisos
        foreach ($modules as $module) {
            Permission::updateOrCreate(['name' => "ver_{$module}"]);
            Permission::updateOrCreate(['name' => "acceder_{$module}"]);
            Permission::updateOrCreate(['name' => "crear_{$module}"]);
            Permission::updateOrCreate(['name' => "editar_{$module}"]);
            Permission::updateOrCreate(['name' => "eliminar_{$module}"]);
        }

        // Obtener todos los permisos
        $allPermissions = Permission::all();

        // Crear o actualizar roles
        $admin = Role::updateOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($allPermissions); // Asigna todos los permisos

        // Crear o actualizar roles
        $comercial = Role::updateOrCreate(['name' => 'Comercial']);
        $comercial->syncPermissions($allPermissions); // Asigna todos los permisos

        $editor = Role::updateOrCreate(['name' => 'Editor']);
        $editor->syncPermissions(['ver_usuarios', 'editar_usuarios']); // Solo permisos específicos

        $viewer = Role::updateOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions(['ver_usuarios']);

        $recibidoPlanta = Role::updateOrCreate(['name' => 'RecibidoPlanta']);
        $recibidoPlanta->syncPermissions([
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',
        ]);

        // Asignar rol "RecibidoPlanta" a un usuario con el nombre "Recibido Planta"
        $user = User::where('name', 'RECIBIDO PLANTA')->first();
        if ($user) {
            $user->assignRole($recibidoPlanta);
        }

        $analistaCostos = Role::updateOrCreate(['name' => 'AnalistaCostos']);
        $analistaCostos->syncPermissions([
            'ver_compras',
            'ver_compra_lote',
            'acceder_compra_lote',

            'ver_alistamiento',
            'acceder_alistamiento',
            'crear_alistamiento',
            'editar_alistamiento',
            'eliminar_alistamiento',

            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

            'ver_inventario',

            'ver_cargue_productos_term',
            'acceder_cargue_productos_term',
            'crear_cargue_productos_term',
            'editar_cargue_productos_term',
            'eliminar_cargue_productos_term',
        ]);

        // Asignar rol "AnalistaCostos" a un usuario con el nombre "ANALISTA DE COSTOS"
        $user = User::where('name', 'ANALISTA DE COSTOS')->first();
        if ($user) {
            $user->assignRole($analistaCostos);
        }

        $adminCentralGuad = Role::updateOrCreate(['name' => 'AdminCentralGuad']);
        $adminCentralGuad->syncPermissions([
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',

            'ver_ventas',
            'ver_venta_pos',
            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',

            'ver_venta_dom',
            'acceder_venta_dom',
            'crear_venta_dom',
            'editar_venta_dom',

            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

            'ver_orders',
            'acceder_orders',
            'crear_orders',
            'editar_orders',
            'eliminar_orders',
        ]);

        // Asignar rol "AdminCentralGuad" a un usuario con el nombre "ADMINISTRADOR CENTRAL GUADALUPE"
        $user = User::where('name', 'ADMINISTRADOR CENTRAL GUADALUPE')->first();
        if ($user) {
            $user->assignRole($adminCentralGuad);
        }
        // Creacion y asignacion de roles y permisos masivos a los administradores
        $rolesUsuarios = [
            'AdminCerdoGuad' => 'ADMINISTRADOR CERDO GUADALUPE',
            'AdminGuadCalle' => 'ADMINISTRADOR GUADALUPE CALLE',
            'AdminPaloquemao' => 'ADMINISTRADOR PALOQUEMADO',
            'AdminGalan' => 'ADMINISTRADOR GALAN',
            'AdminSuba' => 'ADMINISTRADOR SUBA',
            'AdminSoacha' => 'ADMINISTRADOR SOACHA',
            'AdminLechoneria' => 'ADMINISTRADOR LECHONERIA',
        ];

        $permisos = [
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',
            'ver_ventas',
            'ver_venta_pos',
            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',
            'ver_venta_dom',
            'acceder_venta_dom',
            'crear_venta_dom',
            'editar_venta_dom',
            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',
            'ver_orders',
            'acceder_orders',
            'crear_orders',
            'editar_orders',
            'eliminar_orders',
        ];

        foreach ($rolesUsuarios as $roleName => $userName) {
            // Crear o actualizar el rol
            $role = Role::updateOrCreate(['name' => $roleName]);
            $role->syncPermissions($permisos);

            // Asignar el rol al usuario correspondiente
            $user = User::where('name', $userName)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }

        $supervisorPuntosDeVenta = Role::updateOrCreate(['name' => 'SupervisorPuntosDeVenta']);
        $supervisorPuntosDeVenta->syncPermissions([
            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

            'ver_inventario',
            'acceder_inventario',
            'crear_inventario',
            'editar_inventario',
            'eliminar_inventario',

            'ver_ventas',
            'ver_venta_pos',
            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',

            'ver_orders',
            'acceder_orders',
            'crear_orders',
            'editar_orders',
            'eliminar_orders',

        ]);

        $user = User::where('name', 'SUPERVISOR PUNTOS DE VENTA')->first();
        if ($user) {
            $user->assignRole($supervisorPuntosDeVenta);
        }

        $user = User::where('name', 'LIDER AUDITORIA')->first();
        if ($user) {
            $user->assignRole($recibidoPlanta);
            $user->assignRole($analistaCostos);
            $user->assignRole($supervisorPuntosDeVenta);
        }

        User::updateOrCreate(
            ['email' => 'implementador@carnesfriasmega.com'], // Condición para identificar el usuario
            [
                'name' => 'ROBERTO BARROSO',
                'phone' => '3214154625',
                'profile' => 'Comercial',
                'status' => 'Active',
                'password' => bcrypt('Imple2025*.')
            ]
        );

        $user = User::where('name', 'ROBERTO BARROSO')->first();
        if ($user) {
            $user->assignRole($comercial);          
        }

        User::updateOrCreate(
            ['email' => 'subgerente@carnesfriasmega.com'], // Condición para identificar el usuario
            [
                'name' => 'BRAYAN GONZALEZ',
                'phone' => '3014154625',
                'profile' => 'Comercial',
                'status' => 'Active',
                'password' => bcrypt('SubG3r3nt3M@*.')
            ]
        );

        $user = User::where('name', 'BRAYAN GONZALEZ')->first();
        if ($user) {
            $user->assignRole($comercial);          
        }
    }
}
