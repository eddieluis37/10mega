<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => 'Eddie Rada',
            'phone' => '3324769453',
            'email' => 'admin@carnesfriasmega.com',
            'profile' => 'Admin',
            'status' => 'Active',
            'password' => bcrypt('ab$')
        ]);
        User::create([
            'name' => 'Jair Rada Rada',
            'phone' => '3008755514',
            'email' => 'gerente-operaciones@carnesfriasmega.com',
            'profile' => 'Admin',
            'status' => 'Active',
            'password' => bcrypt('Jr3016032085*')
        ]);
        User::create([
            'name' => 'RECIBIDO PLANTA',
            'phone' => '3108955514',
            'email' => 'recibidomega@carnesfriasmega.com',
            'profile' => 'RecibidoPlanta',
            'status' => 'Active',
            'password' => bcrypt('RecibidoPlanta03.')
        ]);
        User::create([
            'name' => 'Jenny Contadora',
            'phone' => '3214154625',
            'email' => 'contabilidad@carnesfriasmega.com',
            'profile' => 'Comercial',
            'status' => 'Active',
            'password' => bcrypt('Yc3214154625.')
        ]);
        User::create([
            'name' => 'directivo',
            'phone' => '3008755514',
            'email' => 'directivo@carnesfriasmega.com',
            'profile' => 'Admin',
            'status' => 'Active',
            'password' => bcrypt('3016032085')
        ]);
        User::create([
            'name' => 'comprador',
            'phone' => '3008755514',
            'email' => 'compras@carnesfriasmega.com',
            'profile' => 'Comprador',
            'status' => 'Active',
            'password' => bcrypt('compras@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'produccion',
            'phone' => '3008755514',
            'email' => 'produccion@carnesfriasmega.com',
            'profile' => 'Produccion',
            'status' => 'Active',
            'password' => bcrypt('Produccion2023.')
        ]);
        User::create([
            'name' => 'costos',
            'phone' => '3008755514',
            'email' => 'costos@carnesfriasmega.com',
            'profile' => 'Costos',
            'status' => 'Active',
            'password' => bcrypt('costos@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'Tesoreria',
            'phone' => '3008755514',
            'email' => 'tesoreria@carnesfriasmega.com',
            'profile' => 'Tesoreria',
            'status' => 'Active',
            'password' => bcrypt('tesoreria@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'comercial',
            'phone' => '3008755514',
            'email' => 'comercial@carnesfriasmega.com',
            'profile' => 'Comercial',
            'status' => 'Active',
            'password' => bcrypt('comercial@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'PRINCIPAL',
            'phone' => '3008755514',
            'email' => 'cajaprincipalpcguadalupe@carnesfriasmega.com',
            'profile' => 'Comercial',
            'status' => 'Active',
            'password' => bcrypt('cajaprincipalpcguadalupe@carnesfriasmega.com')
        ]);

        User::create([
            'name' => 'AUXILIAR',
            'phone' => '3008755514',
            'email' => 'cajaauxiliarpcguadalupe@carnesfriasmega.com',
            'profile' => 'Comercial',
            'status' => 'Active',
            'password' => bcrypt('cajaauxiliarpcguadalupe@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ANALISTA DE COSTOS',
            'phone' => '3008755514',
            'email' => 'analistacostosmega@carnesfriasmega.com',
            'profile' => 'AnalistaCostos',
            'status' => 'Active',
            'password' => bcrypt('analistacostosmega@carnesfriasmega.com')
        ]);    
        User::create([
            'name' => 'ADMINISTRADOR CENTRAL GUADALUPE',
            'phone' => '3008755514',
            'email' => 'centralguadalupemega@carnesfriasmega.com',
            'profile' => 'AdminCentralGuad',
            'status' => 'Active',
            'password' => bcrypt('centralguadalupemega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR CERDO GUADALUPE',
            'phone' => '3008755514',
            'email' => 'centralcerdomega@carnesfriasmega.com',
            'profile' => 'AdminCerdoGuad',
            'status' => 'Active',
            'password' => bcrypt('centralcerdomega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR GUADALUPE CALLE',
            'phone' => '3008755514',
            'email' => 'guadalupecallemega@carnesfriasmega.com',
            'profile' => 'AdminGuadCalle',
            'status' => 'Active',
            'password' => bcrypt('guadalupecallemega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR PALOQUEMADO',
            'phone' => '3008755514',
            'email' => 'paloquemadomega@carnesfriasmega.com',
            'profile' => 'AdminPaloquemao',
            'status' => 'Active',
            'password' => bcrypt('paloquemadomega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR GALAN',
            'phone' => '3008755514',
            'email' => 'galanmega@carnesfriasmega.com',
            'profile' => 'AdminGalan',
            'status' => 'Active',
            'password' => bcrypt('galanmega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR SUBA',
            'phone' => '3008755514',
            'email' => 'subamega@carnesfriasmega.com',
            'profile' => 'AdminSuba',
            'status' => 'Active',
            'password' => bcrypt('subamega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR SOACHA',
            'phone' => '3008755514',
            'email' => 'soachamega@carnesfriasmega.com',
            'profile' => 'AdminSoacha',
            'status' => 'Active',
            'password' => bcrypt('soachamega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'ADMINISTRADOR LECHONERIA',
            'phone' => '3008755514',
            'email' => 'lechoneriamega@carnesfriasmega.com',
            'profile' => 'AdminLechoneria',
            'status' => 'Active',
            'password' => bcrypt('lechoneriamega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'SUPERVISOR PUNTOS DE VENTA',
            'phone' => '3008755514',
            'email' => 'supervisorpvmega@carnesfriasmega.com',
            'profile' => 'SupervisorPuntosDeVenta',
            'status' => 'Active',
            'password' => bcrypt('supervisorpvmega@carnesfriasmega.com')
        ]);
        User::create([
            'name' => 'LIDER AUDITORIA',
            'phone' => '3008755514',
            'email' => 'auditoriamega@carnesfriasmega.com',
            'profile' => 'LiderAuditoria',
            'status' => 'Active',
            'password' => bcrypt('auditoriamega@carnesfriasmega.com')
        ]);


        /**********************************************************************/
        /*** Al agregar nuevos roles  se debe agregar el rol en la migracion tabla User
         *   $table->enum('profile',['Admin','Cajero','Vendedor','Compras'])->default('Admin'); */

        // crear permisos componente Admin
        Permission::create(['name' => 'Admin_Menu']);

        // crear permisos componente Cashout
        Permission::create(['name' => 'Cashout_Create']);

        // crear permisos componente categories
        Permission::create(['name' => 'Category_View']);
        Permission::create(['name' => 'Category_Create']);
        Permission::create(['name' => 'Category_Search']);
        Permission::create(['name' => 'Category_Update']);
        Permission::create(['name' => 'Category_Destroy']);

        // crear permisos componente parametros
        Permission::create(['name' => 'Parametros_Create']);

        // crear permisos componente Productos
        Permission::create(['name' => 'Product_View']);
        Permission::create(['name' => 'Product_Create']);
        Permission::create(['name' => 'Product_Search']);
        Permission::create(['name' => 'Product_Update']);
        Permission::create(['name' => 'Product_Destroy']);

        // crear permisos modulo Reportes
        Permission::create(['name' => 'Report_Create']);

        // crear permisos para modulo compras
        Permission::create(['name' => 'Compras_Menu']);

        // crear permisos para modulo Inventario
        Permission::create(['name' => 'Inventory']);
        Permission::create(['name' => 'Cerrar_Inventario']);

        // crear permisos para modulo alistamiento
        Permission::create(['name' => 'Produccion']);

        // crear permisos para modulo traslados
        Permission::create(['name' => 'Traslados']);

        // crear permisos para modulo ventas
        Permission::create(['name' => 'Pos_Create']);

        // crear permisos para modulo Workshop
        Permission::create(['name' => 'Workshop']);


        // crear permisos para modulo Ordenes de pedidos
        Permission::create(['name' => 'open-order']);

        Permission::create(['name' => 'Alistamiento_Menu']);



        // crear role Administrador
        $admin     = Role::create(['name' => 'Admin']);

        // crear role Cajero
        $cajero    = Role::create(['name' => 'Cajero']);

        // crear role compras
        $comprador  = Role::create(['name' => 'Comprador']);

        // crear role alistamiento
        $produccion  = Role::create(['name' => 'Produccion']);

        // crear role costos
        $costos  = Role::create(['name' => 'Costos']);

        // crear role Vendedor
        $ventas  = Role::create(['name' => 'Tesoreria']);

        // crear role Comercial
        $comercial  = Role::create(['name' => 'Comercial']);


        // asignar permisos al rol Admin
        $admin->givePermissionTo([
            'Admin_Menu',
            'Compras_Menu',
            'Produccion',
            'Traslados',
            'Workshop',
            'Pos_Create',
            'Inventory',
            'Cerrar_Inventario',
            'Cashout_Create',
            'Parametros_Create',
            'Category_View',
            'Category_Create',
            'Category_Search',
            'Category_Update',
            'Category_Destroy',
            'Product_View',
            'Product_Create',
            'Product_Search',
            'Product_Update',
            'Product_Destroy',
            'Report_Create',
            'Alistamiento_Menu',
            'open-order'
        ]);

        
        // asignar permisos al usuario comercial o cajero, activa menu ventas y caja
        $comercial->givePermissionTo(['Admin_Menu', 'open-order', 'Parametros_Create', 'Report_Create', 'Pos_Create', 'Product_View', 'Product_Search', 'Inventory']);

        // asignar permisos al rol Cajero
        $cajero->givePermissionTo(['Pos_Create', 'Cashout_Create', 'Category_View', 'Category_Search', 'Product_View', 'Product_Search']);

        // asignar permisos al comprador
        $comprador->givePermissionTo(['Compras_Menu', 'Inventory', 'Workshop', 'Produccion']);

        // asignar permisos a produccion
        $produccion->givePermissionTo(['Produccion']);

        // asignar permisos a costos
        $costos->givePermissionTo(['Compras_Menu', 'Product_View', 'Product_Search', 'Traslados', 'Inventory']);

        // asignar permisos al vendedor
        $ventas->givePermissionTo(['Pos_Create', 'Cashout_Create', 'Category_View', 'Category_Search', 'Product_View', 'Product_Search']);


        /************************ Asignar role Admin al usuario */
        /* $uAdmin = User::find(1);
        $uAdmin->assignRole('Admin'); */

        User::find(1)->assignRole('Admin');
        User::find(2)->assignRole('Admin');
       
        User::find(4)->assignRole('Comercial');
        User::find(5)->assignRole('Comercial');
        User::find(6)->assignRole('Comprador');
        User::find(7)->assignRole('Produccion');
        User::find(8)->assignRole('Costos');
        User::find(9)->assignRole('Tesoreria');
        User::find(10)->assignRole('Comercial');
        User::find(11)->assignRole('Comercial');
        User::find(12)->assignRole('Comercial');
    }
}
