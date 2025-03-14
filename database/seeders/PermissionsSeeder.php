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
        $modules = ['usuarios', 'compras', 'compra_lote', 'compra_productos', 'alistamiento', 'traslado', 'inventario', 'cargue_productos_term', 'ventas', 'venta_autoservicio', 'venta_bar', 'venta_domicilio', 'venta_parrilla', 'orders', 'bodegas']; // Agrega los módulos necesarios

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

        // 1. Crear o actualizar el rol "AdminCentroCosto"
        $adminCentroCosto = Role::updateOrCreate(['name' => 'AdminCentroCosto']);

        // 2. Asignar el rol "AdminCentroCosto" a los usuarios que contengan 'ADMINISTRADOR' en su nombre o cuyos IDs estén en el arreglo
        $idsUsuarios = [20, 21];

        $usuarios = User::where('name', 'like', '%ADMINISTRADOR%')
            ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($adminCentroCosto);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',

            'ver_ventas',
            'ver_venta_autoservicio',
            'ver_venta_bar',
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

        // 4. Crear o actualizar cada permiso (esto asegura que, si ya existen, se mantengan actualizados)
        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(['name' => $permiso]);
        }

        // 5. Definir roles para asignar a usuarios específicos.
        // La llave es el nombre del rol y el valor es el nombre del usuario al que se le asignará.
        $rolesUsuarios = [
            'AdminCentroCosto' => 'ADMINISTRADOR CENTRO COSTO',
        ];

        // 6. Para cada rol definido, se crea o actualiza y se sincronizan los permisos,
        //    luego se asigna al usuario correspondiente si existe.
        foreach ($rolesUsuarios as $roleName => $userName) {
            // Crear o actualizar el rol
            $role = Role::updateOrCreate(['name' => $roleName]);

            // Sincronizar los permisos con el rol
            $role->syncPermissions($permisos);

            // Buscar el usuario por nombre y asignarle el rol
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
            ['email' => 'implementador@carnesfriasmega.co'], // Condición para identificar el usuario
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
            ['email' => 'subgerente@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BRAYAN GONZALEZ',
                'phone' => '3014154625',
                'profile' => 'Comercial',
                'status' => 'Active',
                'password' => bcrypt('SubG3r3nt3M@*')
            ]
        );

        $user = User::where('name', 'BRAYAN GONZALEZ')->first();
        if ($user) {
            $user->assignRole($comercial);
        }

        // 1. Crear o actualizar el rol "AdminBodega"
        $adminBodega = Role::updateOrCreate(['name' => 'AdminBodega']);



        User::updateOrCreate(
            ['email' => 'soachamega1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 1 SOACHA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('Cajero01Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 2 SOACHA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('Cajero02Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega3@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 3 SOACHA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('Cajero03Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega4@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 4 SOACHA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('Cajero04Soacha.*')
            ]
        );

        // 2. Asignar el rol "AdminBodega" a los usuarios que contengan 'ADMINISTRADOR' en su nombre o cuyos IDs estén en el arreglo
        //   $idsUsuarios = [20, 21];

        $usuarios = User::where('name', 'like', '%BAR%')
            ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($adminBodega);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',
           
            'ver_ventas',
            'ver_venta_autoservicio',
            'ver_venta_bar',
            
            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',
            
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

        // 4. Crear o actualizar cada permiso (esto asegura que, si ya existen, se mantengan actualizados)
        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(['name' => $permiso]);
        }

        // 5. Definir roles para asignar a usuarios específicos.
        // La llave es el nombre del rol y el valor es el nombre del usuario al que se le asignará.
        $rolesUsuarios = [
            'AdminBodega' => 'ADMINISTRADOR CENTRO COSTO',
        ];

        // 6. Para cada rol definido, se crea o actualiza y se sincronizan los permisos,
        //    luego se asigna al usuario correspondiente si existe.
        foreach ($rolesUsuarios as $roleName => $userName) {
            // Crear o actualizar el rol
            $role = Role::updateOrCreate(['name' => $roleName]);

            // Sincronizar los permisos con el rol
            $role->syncPermissions($permisos);

            // Buscar el usuario por nombre y asignarle el rol
            $user = User::where('name', $userName)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }

        User::updateOrCreate(
            ['email' => 'cargueinventariosmega@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAUSACION',
                'phone' => '3014154625',
                'profile' => 'Causacion',
                'status' => 'Active',
                'password' => bcrypt('Causant3M3@a*')
            ]
        );
        $causacion = Role::updateOrCreate(['name' => 'Causacion']);
        $causacion->syncPermissions([
            'ver_inventario',
            'ver_cargue_productos_term',
            'acceder_cargue_productos_term',
            'crear_cargue_productos_term',
            'editar_cargue_productos_term',
        ]);

        $user = User::where('name', 'CAUSACION')->first();
        if ($user) {
            $user->assignRole($causacion);
        }

        User::updateOrCreate(
            ['email' => 'comprasmega1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'COMPRAS 1',
                'phone' => '3014154625',
                'profile' => 'Comprador',
                'status' => 'Active',
                'password' => bcrypt('C0mpraM3g@1*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'comprasmega2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'COMPRAS 2',
                'phone' => '3014154625',
                'profile' => 'Comprador',
                'status' => 'Active',
                'password' => bcrypt('C0mpraM3g@2*')
            ]
        );
        $comprador = Role::updateOrCreate(['name' => 'Comprador']);
        $comprador->syncPermissions([
            'ver_compras',

            'ver_compra_lote',
            'acceder_compra_lote',
            'crear_compra_lote',
            'editar_compra_lote',

            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',

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
            'acceder_inventario',
            'crear_inventario',
            'editar_inventario',
            'eliminar_inventario',

            'ver_cargue_productos_term',
            'acceder_cargue_productos_term',
            'crear_cargue_productos_term',
            'editar_cargue_productos_term',
        ]);


        $user = User::where('name', 'like', '%COMPRAS%')
            //->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($user as $usuario) {
            $usuario->assignRole($comprador);
        }

        /* ******************** BAR ***************** */

        User::updateOrCreate(
            ['email' => 'soachamegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR SOACHA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('Bar05Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'galanmegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR GALAN',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('01Galanmegabar2025*.')
            ]
        );
        User::updateOrCreate(
            ['email' => 'subamegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR SUBA',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('02Subamegabar2025*.')
            ]
        );

        $usuarios = User::where('name', 'like', '%BAR%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($adminBodega);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'ver_ventas',
            'ver_venta_bar',
            
            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',       

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

        // 4. Crear o actualizar cada permiso (esto asegura que, si ya existen, se mantengan actualizados)
        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(['name' => $permiso]);
        }

        // 5. Definir roles para asignar a usuarios específicos.
        // La llave es el nombre del rol y el valor es el nombre del usuario al que se le asignará.
        $rolesUsuarios = [
            'AdminBodega' => 'ADMINISTRADOR CENTRO COSTO',
        ];

        // 6. Para cada rol definido, se crea o actualiza y se sincronizan los permisos,
        //    luego se asigna al usuario correspondiente si existe.
        foreach ($rolesUsuarios as $roleName => $userName) {
            // Crear o actualizar el rol
            $role = Role::updateOrCreate(['name' => $roleName]);

            // Sincronizar los permisos con el rol
            $role->syncPermissions($permisos);

            // Buscar el usuario por nombre y asignarle el rol
            $user = User::where('name', $userName)->first();
            if ($user) {
                $user->assignRole($role);
            }
        }
    }
}
