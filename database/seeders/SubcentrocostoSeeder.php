<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcentrocosto;

class SubcentrocostoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "HOGAR",
        ]);       
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "HORECA",
        ]);
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "COMIDAS RAPIDAS",
        ]);
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "INSTITUCIONAL",
        ]);
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "FAMA",
        ]);
        Subcentrocosto::create([
            'centrocosto_id' => 1,
            'name' => "SUPERMERCADOS",
        ]);        
    }
}
