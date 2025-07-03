<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ModulesSheet implements FromArray, WithTitle
{
    private $modules;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function array(): array
    {
        $rows = [['Módulo', 'Permiso']];
        foreach ($this->modules as $mod) {
            $types = ['view', "ver_{$mod}", "acceder_{$mod}", "crear_{$mod}", "editar_{$mod}", "eliminar_{$mod}"];
            foreach ($types as $perm) {
                $rows[] = [$mod, $perm];
            }
        }
        return $rows;
    }

    public function title(): string
    {
        return 'Módulos y Permisos';
    }
}