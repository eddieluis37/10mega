<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    ShouldAutoSize
};

class UsersStoresExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Construye una colección de filas para el Excel.
     */
    public function collection(): Collection
    {
        $rows = collect();

        // Traemos todos los usuarios con sus bodegas y centros de costo
        $users = User::with(['stores.centroCosto'])->get();

        foreach ($users as $user) {
            if ($user->stores->isEmpty()) {
                // Si el usuario no tiene bodegas, igual mostramos la fila con nulos
                $rows->push([
                    'user_name'             => $user->name,
                    'user_email'            => $user->email,
                    'user_profile'          => $user->profile,
                    /* 'user_password'         => $user->password, */
                    'centrocosto_name'      => null,
                    'store_name'            => null,
                    'store_description'     => null,
                ]);
            } else {
                foreach ($user->stores as $store) {
                    $rows->push([
                        'user_name'             => $user->name,
                        'user_email'            => $user->email,
                        'user_profile'          => $user->profile,
                        /*  'user_password'         => $user->password, */
                        'centrocosto_name'      => optional($store->centroCosto)->name,
                        'store_name'            => $store->name,
                        'store_description'     => $store->description,
                    ]);
                }
            }
        }

        return $rows;
    }

    /**
     * Cabeceras de las columnas en el Excel.
     */
    public function headings(): array
    {
        return [
            'Usuario',
            'Email',
            'Rol',
            'Centro de Costo',
            /*  'Contraseña (hash)', */
            'Bodega',
            'Descripción Bodega',
        ];
    }
}
