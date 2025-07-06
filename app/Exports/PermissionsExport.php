<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionsExport implements WithMultipleSheets
{
    protected $modules = [
        'administracion', 'contabilidad', 'combos', 'dishes',
        'lista_de_precio', 'terceros', 'productos', 'brand',
        'usuarios', 'compras', 'compra_lote', 'compra_productos',
        'alistamiento', 'traslado', 'inventario', 'inventario_stockfisico',
        'cargue_productos_term', 'ventas', 'venta_pos', 'venta_domicilio',
        'venta_autoservicio', 'venta_parrilla', 'venta_bar', 'orders',
        'bodegas', 'CambiarPrecioVenta'
    ];

    public function sheets(): array
    {
       return [
            new ModulesSheet($this->modules),
            new RolesSheet(Role::with('permissions')->get()),
            new UsuariosSheet(User::with('roles')->get()),
        ];
    }
}