<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class RolesSheet implements FromCollection, WithTitle
{
    private $roles;

    public function __construct(Collection $roles)
    {
        $this->roles = $roles;
    }

    public function collection()
    {
        $rows = [];
        $rows[] = ['Rol', 'Permisos asignados'];
        foreach ($this->roles as $role) {
            $rows[] = [
                $role->name,
                $role->permissions->pluck('name')->implode(', ')
            ];
        }
        return collect($rows);
    }

    public function title(): string
    {
        return 'Roles y Permisos';
    }
}