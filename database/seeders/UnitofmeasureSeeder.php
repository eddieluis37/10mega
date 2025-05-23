<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products\Unitofmeasure;

class UnitofmeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unitofmeasure::create([
        	'name' => 'GRAMOS',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'KILOGRAMOS',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'LIBRA',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'LITRO',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'PAQUETE',
        	'description' => '',
        	'status' => true
        ]);

        Unitofmeasure::create([
        	'name' => 'UNIDAD',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'COMBO',
        	'description' => '',
        	'status' => true
        ]);
        Unitofmeasure::create([
        	'name' => 'COMPUESTO',
        	'description' => '',
        	'status' => true
        ]);
    }
}
