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
                'users' => [1, 2, 8, 9, 13, 22, 23, 24, 25, 31, 32, 36, 37, 38, 39, 41, 44, 47, 48, 49, 53, 54, 55, 56],
                'stores' => Store::all()->pluck('id')->toArray(), // Todas las bodegas
            ],
            
            'BarSuba' => [
                'users' => [27],
                'stores' => Store::whereIn('id', [20, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            
            'Causacion' => [
                'users' => [30],
                'stores' => Store::whereIn('id', [10,])->pluck('id')->toArray(), // causacion
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
                'users' => [15,43],
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
                'stores' => Store::whereIn('id', [14, 15, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'BarGalan' => [
                'users' => [34],
                'stores' => Store::whereIn('id', [16, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'Admin2Galan' => [
                'users' => [42],
                'stores' => Store::whereIn('id', [14,15,16,39])->pluck('id')->toArray(), // Bodegas específicas
            ],

            'AdminSuba' => [
                'users' => [19],
                'stores' => Store::whereIn('id', [19, 18, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'BarSuba' => [
                'users' => [35],
                'stores' => Store::whereIn('id', [20, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],

            'AdminSoacha' => [
                'users' => [20],
                'stores' => Store::whereIn('id', [27, 28, 29, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],                      
            'CajeroSoacha1' => [
                'users' => [26],
                'stores' => Store::whereIn('id', [27, 28, 39])->pluck('id')->toArray(), // Bar soacha
            ],
            'CajeroSoacha2' => [
                'users' => [27],
                'stores' => Store::whereIn('id', [27, 28, 39])->pluck('id')->toArray(), // Bar soacha
            ],
            'CajeroSoacha3' => [
                'users' => [28],
                'stores' => Store::whereIn('id', [27, 28, 39])->pluck('id')->toArray(), // Bar soacha
            ],
            'CajeroSoacha4' => [
                'users' => [29],
                'stores' => Store::whereIn('id', [27, 28, 39])->pluck('id')->toArray(), // Bar soacha
            ],
            'BarSoacha' => [
                'users' => [33],
                'stores' => Store::whereIn('id', [29, 39])->pluck('id')->toArray(), // Bar soacha
            ], 

            'AdminLechoneria' => [
                'users' => [21],
                'stores' => Store::whereIn('id', [24, 25, 26, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],             
            'CajeroPuntosDeVentaPrincipal' => [
                'users' => [11],
                'stores' => Store::whereIn('id', [1, 4, 5, 6, 8, 9, 10, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'FacturacionPlanta' => [
                'users' => [36, 37],
                'stores' => Store::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 39])->pluck('id')->toArray(), // Bodegas específicas
            ],
            'Vendedor' => [
                'users' => [40],
                'stores' => Store::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->pluck('id')->toArray(), // Bodegas específicas
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