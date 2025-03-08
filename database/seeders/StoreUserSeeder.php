<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoreUser;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Store;

class StoreUserSeeder extends Seeder
{
    public function run(): void
    {
        // Definir las asignaciones de usuarios a bodegas
        $assignments = [
            'admin' => [
                'users' => [1, 2, 13, 23, 24, 25],
                'stores' => Store::all()->pluck('id')->toArray(), // Todas las bodegas
            ],
            'rec_planta' => [
                'users' => [3],
                'stores' => Store::whereIn('id', [2, 3, 6, 7, 8, 9, 10, ])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminCentralGuad' => [
                'users' => [14],
                'stores' => Store::whereIn('id', [13])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminCerdoGuad' => [
                'users' => [15],
                'stores' => Store::whereIn('id', [22, 23])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminGuadCalle' => [
                'users' => [16],
                'stores' => Store::whereIn('id', [12])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminPaloquemao' => [
                'users' => [17],
                'stores' => Store::whereIn('id', [17])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminGalan' => [
                'users' => [18],
                'stores' => Store::whereIn('id', [14, 15, 16])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminSuba' => [
                'users' => [19],
                'stores' => Store::whereIn('id', [18, 19, 20])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminSoacha' => [
                'users' => [20],
                'stores' => Store::whereIn('id', [27, 28, 29])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'AdminLechoneria' => [
                'users' => [21],
                'stores' => Store::whereIn('id', [24, 25, 26])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'SupervisorPuntosDeVenta' => [
                'users' => [22],
                'stores' => Store::whereIn('id', [22, 23])->pluck('id')->toArray(), // Bodegas específicas
            ],   
            'CajeroPuntosDeVentaPrincipal' => [
                'users' => [11],
                'stores' => Store::whereIn('id', [1, 4, 5, 6, 8, 9, 10])->pluck('id')->toArray(), // Bodegas específicas
            ],                   
        ];

        foreach ($assignments as $group) {
            // Obtener los usuarios correspondientes
            $users = User::whereIn('id', $group['users'])->get();

            foreach ($users as $user) {
                foreach ($group['stores'] as $storeId) {
                    DB::table('store_user')->updateOrInsert([
                        'user_id' => $user->id,
                        'store_id' => $storeId
                    ]);
                }
            }
        }
    }
}