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
                'users' => [1, 2],
                'stores' => Store::all()->pluck('id')->toArray(), // Todas las bodegas
            ],
            'rec_planta' => [
                'users' => [3, 4],
                'stores' => Store::whereIn('id', [1, 2, 3])->pluck('id')->toArray(), // Bodegas específicas
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