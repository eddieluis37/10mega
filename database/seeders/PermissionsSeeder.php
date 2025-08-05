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
        $modules = ['administracion', 'contabilidad', 'combos', 'dishes', 'lista_de_precio', 'terceros', 'productos', 'brand', 'usuarios', 'compras', 'compra_lote', 'compra_productos', 'alistamiento', 'traslado', 'inventario', 'inventario_si', 'inventario_stockfisico', 'cargue_productos_term', 'ventas', 'venta_pos', 'venta_domicilio', 'venta_autoservicio', 'venta_parrilla', 'venta_bar', 'orders', 'bodegas', 'CambiarPrecioVenta']; // Agrega los módulos necesarios

        // Crear o actualizar permisos
        foreach ($modules as $module) {
            Permission::updateOrCreate(['name' => "view {$module}"]);
            Permission::updateOrCreate(['name' => "ver_{$module}"]);
            Permission::updateOrCreate(['name' => "acceder_{$module}"]);
            Permission::updateOrCreate(['name' => "crear_{$module}"]);
            Permission::updateOrCreate(['name' => "editar_{$module}"]);
            Permission::updateOrCreate(['name' => "eliminar_{$module}"]);
            Permission::updateOrCreate(['name' => "Pos_Create"]);
        }

        // Obtener todos los permisos
        $allPermissions = Permission::all();

        // Crear o actualizar roles
        $admin = Role::updateOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($allPermissions); // Asigna todos los permisos

        // Crear o actualizar roles
        $liderAuditoria = Role::updateOrCreate(['name' => 'LiderAuditoria']);
        $liderAuditoria->syncPermissions($allPermissions);

        // Asignar rol "" a un usuario con el nombre ""
        $user = User::where('name', 'LIDER AUDITORIA')->first();
        if ($user) {
            $user->assignRole($liderAuditoria);
        }

        // Asignar LiderAuditoria a ADMINISTRADOR LECHONERIA
        $user2 = User::where('name', 'ADMINISTRADOR LECHONERIA')->first();
        if ($user2) {
            // limpia roles previos y deja sólo LiderAuditoria
            $user2->syncRoles([$liderAuditoria]);
        }

        /*  // Crear o actualizar roles
        $adminCentroCosto = Role::updateOrCreate(['name' => 'AdminCentroCosto']);
        $adminCentroCosto->syncPermissions($allPermissions); // Asigna todos los permisos */

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

            'acceder_cargue_productos_term',
            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

        ]);

        // Asignar rol "RecibidoPlanta" a un usuario con el nombre "Recibido Planta"
        $user = User::where('name', 'RECIBIDO PLANTA')->first();
        if ($user) {
            $user->assignRole($recibidoPlanta);
        }

        $analistaCostos = Role::updateOrCreate(['name' => 'AnalistaCostos']);
        $analistaCostos->syncPermissions($allPermissions);
        /*   $analistaCostos->syncPermissions([
            'ver_compras',
            'ver_compra_lote',
            'acceder_compra_lote',
            'acceder_cargue_productos_term',

            'ver_productos',

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
        ]); */

        // Asignar rol "AnalistaCostos" a un usuario con el nombre "ANALISTA DE COSTOS"
        $user = User::where('name', 'ANALISTA DE COSTOS')->first();
        if ($user) {
            $user->assignRole($analistaCostos);
        }

        // 1. Crear o actualizar el rol "AdminCentroCosto"
        $adminCentroCosto = Role::updateOrCreate(['name' => 'AdminCentroCosto']);

        // 2. Asignar el rol "AdminCentroCosto" a los usuarios que contengan 'ADMINISTRADOR' en su nombre o cuyos IDs estén en el arreglo
        // $idsUsuarios = [20, 21];

        $usuarios = User::where('profile', 'like', '%AdminCentroCosto%')
            //->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($adminCentroCosto);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'ver_administracion',
            'ver_terceros',
            'accceder_terceros',
            'acceder_venta_autoservicio',
            'crear_terceros',
            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'crear_compra_productos',
            'editar_compra_productos',

            'ver_ventas',
            'ver_venta_autoservicio',
            'ver_venta_bar',
            'ver_venta_parrilla',
            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',

            'ver_venta_domicilio',
            'acceder_cargue_productos_term',
            'acceder_ventas',
            'acceder_venta_domicilio',
            'crear_venta_domicilio',
            'editar_venta_domicilio',
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

            'ver_inventario',
            'acceder_inventario',
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
            'ver_administracion',
            'ver_terceros',
            'accceder_terceros',
            'crear_terceros',
            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',
            'acceder_cargue_productos_term',

            'acceder_inventario_stockfisico',

            'ver_contabilidad',
            'acceder_contabilidad',
            'crear_contabilidad',

            'ver_inventario',
            'acceder_inventario',
            'crear_inventario',
            'editar_inventario',
            'eliminar_inventario',

            'ver_ventas',
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
            $user->syncPermissions($allPermissions); // Asigna todos los permisos
        }

        $user = User::where('name', 'LIDER AUDITORIA')->first();
        if ($user) {
            $user->syncPermissions($allPermissions); // Asigna todos los permisos
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
        $user->syncPermissions($allPermissions); // Asigna todos los permisos


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
        User::updateOrCreate(
            ['email' => 'administradora@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'ELSA LILIANA BELLO SENA',
                'phone' => '3014154625',
                'profile' => 'Comercial',
                'status' => 'Active',
                'password' => bcrypt('Bell02005*')
            ]
        );

        $user = User::where('profile', 'like', '%Comercial%')
            //->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($user as $usuario) {
            $usuario->assignRole($comercial);
        }



        // 1. Crear o actualizar el rol "AdminBodega"
        $Cajero = Role::updateOrCreate(['name' => 'Cajero']);


        User::updateOrCreate(
            ['email' => 'soachamega1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 1 SOACHA',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('Cajero01Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 2 SOACHA',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('Cajero02Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega3@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 3 SOACHA',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('Cajero03Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'soachamega4@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 4 SOACHA',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('Cajero04Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'cajero1cerdocentral@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 1 CERDO.CENTRAL',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('01CajaCerdoCentral.2025*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'subamega2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CAJERO 2 SUBA',
                'phone' => '3214154625',
                'profile' => 'Cajero',
                'status' => 'Active',
                'password' => bcrypt('02CajaSub@.2025*')
            ]
        );

        $usuarios = User::where('name', 'like', '%CAJERO%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($Cajero);
        }

        $cajero = Role::updateOrCreate(['name' => 'Cajero']);
        $cajero->syncPermissions([
            'ver_administracion',
            'ver_terceros',
            'acceder_terceros',
            'acceder_venta_autoservicio',
            'acceder_venta_parrilla',
            'crear_terceros',
            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'acceder_cargue_productos_term',
            'crear_compra_productos',
            'editar_compra_productos',
            'ver_ventas',
            'ver_venta_autoservicio',
            'ver_venta_parrilla',
            'acceder_ventas',
            'crear_venta_pos',
            'editar_venta_pos',
            'crear_venta_domicilio',
            'editar_venta_domicilio',
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



        /* ************************ */

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
            'acceder_inventario',
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
            'ver_administracion',
            'ver_terceros',
            'ver_compras',

            'acceder_terceros',

            'ver_compra_lote',
            'acceder_compra_lote',
            'acceder_terceros',

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

        // 1. Crear o actualizar el rol "AdminBodega"
        
        
        
        /* ******************** BAR ***************** */
        $cajaBar = Role::updateOrCreate(['name' => 'CajeroBar']);
        User::updateOrCreate(
            ['email' => 'soachamegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR SOACHA',
                'phone' => '3214154625',
                'profile' => 'CajeroBar',
                'status' => 'Active',
                'password' => bcrypt('Bar05Soacha.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'galanmegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR GALAN',
                'phone' => '3214154625',
                'profile' => 'CajeroBar',
                'status' => 'Active',
                'password' => bcrypt('01Galanmegabar2025*.')
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'subamegabar@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'BAR SUBA',
                'phone' => '3214154625',
                'profile' => 'CajeroBar',
                'status' => 'Active',
                'password' => bcrypt('02Subamegabar2025*.')
            ]
        );

        $usuarios = User::where('name', 'like', '%BAR%')
           // ->orWhere('name', 'like', '%ADMIN%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($cajaBar);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'ver_ventas',
            'ver_venta_bar',

            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'acceder_cargue_productos_term',
            'crear_compra_productos',
            'editar_compra_productos',

            'ver_contabilidad',
            'acceder_contabilidad',
            'crear_contabilidad',

            'acceder_ventas',
            'crear_venta_pos',
            'editar_venta_pos',
            'ver_venta_domicilio',
            'acceder_venta_bar',
            'acceder_venta_domicilio',
            'crear_venta_domicilio',
            'editar_venta_domicilio',

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

            'ver_inventario',
            'ver_inventario_si',

            'acceder_inventario',
            'acceder_inventario_si'
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

        User::updateOrCreate(
            ['email' => 'galanmega2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'ADMIN GALAN 2',
                'phone' => '3214154625',
                'profile' => 'AdminBodega',
                'status' => 'Active',
                'password' => bcrypt('CarnesFriasMega2025@')
            ]
        );

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

        /* ******************** FACTURACION PLANTA ***************** */
        // 1. Crear o actualizar el rol "FacturacionPlanta"

        User::updateOrCreate(
            ['email' => 'facturacionplanta1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'FACTURACIÓN PLANTA 1',
                'phone' => '3214154625',
                'profile' => 'FacturacionPlanta',
                'status' => 'Active',
                'password' => bcrypt('Fact01Planta.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'facturacionplanta2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'FACTURACIÓN PLANTA 2',
                'phone' => '3214154625',
                'profile' => 'FacturacionPlanta',
                'status' => 'Active',
                'password' => bcrypt('Fact02Planta.*')
            ]
        );

        $facturacionPlanta = Role::updateOrCreate(['name' => 'FacturacionPlanta']);

        $usuarios = User::where('name', 'like', '%FACTURACIÓN%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($facturacionPlanta);
        }

        // 3. Definir el listado de permisos a sincronizar
        $facturacionPlanta->syncPermissions([
            'ver_administracion',
            'ver_terceros',
            'acceder_terceros',
            'crear_terceros',

            'Pos_Create',
            'ver_compras',
            'ver_compra_productos',
            'acceder_compra_productos',
            'acceder_cargue_productos_term',
            'crear_compra_productos',
            'editar_compra_productos',

            'ver_ventas',
            'ver_venta_domicilio',
            'ver_venta_pos',

            'Pos_Create',

            'acceder_ventas',
            'crear_venta_pos',
            'editar_venta_pos',

            'acceder_venta_domicilio',
            'acceder_cargue_productos_term',
            'crear_venta_domicilio',
            'editar_venta_domicilio',

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

            'ver_inventario',
            'acceder_inventario',
        ]);

        /* ******************** DESPACHOS PLANTA ***************** */
        // 1. Crear o actualizar el rol "FacturacionPlanta"

        User::updateOrCreate(
            ['email' => 'despachosplanta@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'DESPACHOS PLANTA ',
                'phone' => '3214154625',
                'profile' => 'DespachosPlanta',
                'status' => 'Active',
                'password' => bcrypt('Desp@8Planta.*')
            ]
        );


        $despachosPlanta = Role::updateOrCreate(['name' => 'DespachosPlanta']);

        $usuarios = User::where('name', 'like', '%DESPACHOS%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($despachosPlanta);
        }

        // 3. Definir el listado de permisos a sincronizar
        $despachosPlanta->syncPermissions([

            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

            'ver_inventario',
            'acceder_inventario',

        ]);

        /* ******************** CALIDAD PLANTA ***************** */
        // 1. Crear o actualizar el rol "FacturacionPlanta"

        User::updateOrCreate(
            ['email' => 'calidadplanta@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'CALIDAD PLANTA ',
                'phone' => '3214154625',
                'profile' => 'CalidadPlanta',
                'status' => 'Active',
                'password' => bcrypt('C@l1d@Planta.*')
            ]
        );

        $calidadPlanta = Role::updateOrCreate(['name' => 'CalidadPlanta']);

        $usuarios = User::where('name', 'like', '%CALIDAD%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($calidadPlanta);
        }

        // 3. Definir el listado de permisos a sincronizar
        $calidadPlanta->syncPermissions([

            'ver_traslado',
            'acceder_traslado',
            'crear_traslado',
            'editar_traslado',
            'eliminar_traslado',

            'ver_inventario',
            'acceder_inventario',

        ]);


        /* ******************** COMERCIAL 1 ***************** */
        // 1. Crear o actualizar el rol "FacturacionPlanta"

        User::updateOrCreate(
            ['email' => 'comercial1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'COMERCIAL 1',
                'phone' => '3004154625',
                'profile' => 'Vendedor',
                'status' => 'Active',
                'password' => bcrypt('Com3rC1@l1.*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'analistacomercialmega@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'ANALISTA COMERCIAL 1',
                'phone' => '3004154625',
                'profile' => 'Vendedor',
                'status' => 'Active',
                'password' => bcrypt('1@naC0m3rC1@l.*')
            ]
        );

        $vendedor = Role::updateOrCreate(['name' => 'Vendedor']);

        $usuarios = User::where('name', 'like', '%COMERCIAL%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($vendedor);
        }

        // 3. Definir el listado de permisos a sincronizar
        $vendedor->syncPermissions([
            'ver_orders',
            'acceder_orders',
            'crear_orders',
            'editar_orders',
            'eliminar_orders',

            'open-order',
            'Parametros_Create',

            'ver_ventas',
            'ver_venta_domicilio',

            'Pos_Create',
            'acceder_ventas',

            'acceder_venta_domicilio',
            'acceder_cargue_productos_term',
            'crear_venta_domicilio',
            'editar_venta_domicilio',

            'ver_inventario',
            'acceder_inventario',
        ]);

        /* ******************** ADMINISTRADOR CERDO CENTRAL ***************** */
        // 1. Crear o actualizar el rol "AdminBodegaCerdoCentral"
        $adminBodegaCerdoCentral = Role::updateOrCreate(['name' => 'AdminBodegaCerdoCentral']);
        User::updateOrCreate(
            ['email' => 'centralcerdomega@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'ADMINISTRADOR CERDO CENTRAL',
                'phone' => '3214154625',
                'profile' => 'AdminBodegaCerdoCentral',
                'status' => 'Active',
                'password' => bcrypt('a13.AdminCerdoGuad+')
            ]
        );

        $usuarios = User::where('name', 'like', '%ADMINISTRADOR CERDO CENTRAL%')
            ->orWhere('name', 'like', '%ADMINISTRADOR PALOQUEMADO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($adminBodegaCerdoCentral);
        }

        // 3. Definir el listado de permisos a sincronizar
        $permisos = [
            'acceder_venta_autoservicio',
            'ver_CambiarPrecioVenta',

            'ver_productos',

            'ver_administracion',
            'ver_terceros',
            'acceder_terceros',
            'crear_terceros',

            'ver_contabilidad',
            'acceder_contabilidad',
            'crear_contabilidad',

            'ver_lista_de_precio',
            'acceder_lista_de_precio',
            'crear_lista_de_precio',
            'editar_lista_de_precio',
            'eliminar_lista_de_precio',

            'ver_alistamiento',
            'acceder_alistamiento',
            'crear_alistamiento',
            'editar_alistamiento',
            'eliminar_alistamiento',

            'ver_compras',
            'acceder_compras',
            'crear_compras',
            'editar_compras',
            'eliminar_compras',

            'ver_compra_lote',
            'acceder_compra_lote',
            'crear_compra_lote',
            'editar_compra_lote',
            'eliminar_compra_lote',

            'ver_inventario',
            'acceder_inventario',
        ];

        // 4. Crear o actualizar cada permiso (esto asegura que, si ya existen, se mantengan actualizados)
        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(['name' => $permiso]);
        }

        // 5. Definir roles para asignar a usuarios específicos.
        // La llave es el nombre del rol y el valor es el nombre del usuario al que se le asignará.
        $rolesUsuarios = [
            'AdminBodegaCerdoCentral' => 'ADMINISTRADOR CERDO CENTRAL',
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

        /* ******************** CARTERA Y TESORERIA ***************** */
        // 1. Crear o actualizar el rol "FacturacionPlanta"

        User::updateOrCreate(
            ['email' => 'auxiliar_cartera@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'AUX CARTERA',
                'phone' => '3004154625',
                'profile' => 'Tesoreria',
                'status' => 'Active',
                'password' => bcrypt('AuxLi@rCart3r@2025*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'auxiliar_contable1@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'AUX CONTABLE1',
                'phone' => '3004154625',
                'profile' => 'Tesoreria',
                'status' => 'Active',
                'password' => bcrypt('AuxLi@rC0nt@1Mega2025*')
            ]
        );
        User::updateOrCreate(
            ['email' => 'auxiliar_contable2@carnesfriasmega.co'], // Condición para identificar el usuario
            [
                'name' => 'AUX CONTABLE2',
                'phone' => '3004154625',
                'profile' => 'Tesoreria',
                'status' => 'Active',
                'password' => bcrypt('AuxLi@rC0nt@2*')
            ]
        );

        $tesoreria = Role::updateOrCreate(['name' => 'Tesoreria']);

        $usuarios = User::where('name', 'like', '%CARTERA%')
            // ->orWhere('name', 'like', '%CAJERO%')
            //  ->orWhereIn('id', $idsUsuarios)
            ->get();

        foreach ($usuarios as $usuario) {
            $usuario->assignRole($tesoreria);
        }

        // 3. Definir el listado de permisos a sincronizar
        $tesoreria->syncPermissions([
            'ver_ventas',
            'ver_venta_domicilio',
            'ver_venta_pos',
            'ver_venta_autoservicio',
            'ver_venta_bar',

            'acceder_venta_pos',
            'crear_venta_pos',
            'editar_venta_pos',

            'Pos_Create',
            'acceder_ventas',

            'acceder_venta_domicilio',
            'acceder_cargue_productos_term',
            'crear_venta_domicilio',
            'editar_venta_domicilio',

            'ver_contabilidad',
            'acceder_contabilidad',
            'crear_contabilidad',
            'editar_contabilidad',
            'eliminar_contabilidad',

        ]);
    }
}
