<?php

namespace Database\Seeders;

use App\Models\Sacrificiocerdo;
use Illuminate\Database\Seeder;

class SacrificiocerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sacrificiocerdo::create([
            'name' => '1 FRIGORIFICO GUADALUPE PLANTA CERDO',
            'dni' => 1991458,
            'address' => 'CALLE 124B #17A-37SUR BOGOTA',
            'phone' => 3132623896,
            'email' => 'Frigorificoguada@gmail.com',
            'sacrificio' => 69016,
            'fomento' => 15184,           
            'deguello' => 0,
            'bascula' => 0,
            'transporte' => 4450
            
        ]);

        Sacrificiocerdo::create([
            'name' => '2 FRIGORIFICO GUADALUPE PLANTA CERDO',
            'dni' => 299145893,
            'address' => 'CALLE 124B #17A-37SUR BOGOTA',
            'phone' => 3132623896,
            'email' => 'Frigorificoguada@gmail.com',
            'sacrificio' => 0,
            'fomento' => 0,           
            'deguello' => 0,
            'bascula' => 0,
            'transporte' => 4450

        ]);
        Sacrificiocerdo::create([
            'name' => '3 FRIGORIFICO GUADALUPE PLANTA CERDA',
            'dni' => 299145894,
            'address' => 'CALLE 124B #17A-37SUR BOGOTA',
            'phone' => 3132623896,
            'email' => 'Frigorificoguada@gmail.com',
            'sacrificio' => 84806,
            'fomento' => 15184,           
            'deguello' => 0,
            'bascula' => 0,
            'transporte' => 4450

        ]);
        Sacrificiocerdo::create([
            'name' => '4 FRIGORIFICO GUADALUPE PLANTA CERDA',
            'dni' => 299145895,
            'address' => 'CALLE 124B #17A-37SUR BOGOTA',
            'phone' => 3132623896,
            'email' => 'Frigorificoguada@gmail.com',
            'sacrificio' => 0,
            'fomento' => 0,           
            'deguello' => 0,
            'bascula' => 0,
            'transporte' => 4450

        ]);
        Sacrificiocerdo::create([
            'name' => '5 LOCAL',
            'dni' => 2911111111,
            'address' => 'CALLE 124B #17A-37SUR BOGOTA',
            'phone' => 3132623896,
            'email' => 'Frigorificoguada@gmail.com',
            'sacrificio' => 0,
            'fomento' => 0,           
            'deguello' => 0,
            'bascula' => 0,
            'transporte' => 0
        ]);

    }
}
