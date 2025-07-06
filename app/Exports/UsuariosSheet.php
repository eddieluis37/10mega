<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use App\Models\User;

class UsuariosSheet implements FromCollection, WithTitle
{
    private $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        $rows = [
            ['Usuario', 'Email', 'Perfiles (profile)', 'Roles Spatie']
        ];

        foreach ($this->users as $user) {
            $rows[] = [
                $user->name,
                $user->email,
                $user->profile,
                $user->roles->pluck('name')->implode(', ')
            ];
        }

        return collect($rows);
    }

    public function title(): string
    {
        return 'Usuarios y Perfiles';
    }
}
