<?php

namespace Database\Seeders;

use App\Models\Formapago;
use Illuminate\Database\Seeder;

class FormapagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formapago = new Formapago([
            "codigo" => "EFECTIVO",
            "nombre" => "EFECTIVO",
            "tipoformapago" => "EFECTIVO",
            "diascredito" => null,
            "cuenta" => "31050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "BANCOLOMBIA CORRIENTE",
            "nombre" => "BANCOLOMBIA CORRIENTE",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "22050501"
        ]);

        $formapago = new Formapago([
            "codigo" => "BBVA",
            "nombre" => "BBVA",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "12050501"
        ]);
        $formapago->save();


        $formapago = new Formapago([
            "codigo" => "CODIGO QR",
            "nombre" => "CODIGO QR",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "11050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "NEQUI",
            "nombre" => "NEQUI",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "22050501"
        ]);
        $formapago->save();

       
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "DAVIPLATA",
            "nombre" => "DAVIPLATA",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "32050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "WOMPI",
            "nombre" => "WOMPI",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "33050502"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "DATAFONO",
            "nombre" => "DATAFONO",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "34050502"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "BOLD",
            "nombre" => "BOLD",
            "tipoformapago" => "TARJETA",
            "diascredito" => null,
            "cuenta" => "34050502"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "VENTA A CREDITO",
            "nombre" => "VENTA A CREDITO",
            "tipoformapago" => "CREDITO",
            "diascredito" => null,
            "cuenta" => "42050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR0",
            "nombre" => "CREDITO A 0 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 0,
            "cuenta" => "52050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR8",
            "nombre" => "CREDITO A 8 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 8,
            "cuenta" => "62050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR15",
            "nombre" => "CREDITO A 15 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 15,
            "cuenta" => "72050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR20",
            "nombre" => "CREDITO A 20 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 20,
            "cuenta" => "82050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR30",
            "nombre" => "CREDITO A 30 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 30,
            "cuenta" => "92050501"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR45",
            "nombre" => "CREDITO A 45 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 45,
            "cuenta" => "10205050"
        ]);
        $formapago->save();

        $formapago = new Formapago([
            "codigo" => "CR60",
            "nombre" => "CREDITO A 60 DIAS",
            "tipoformapago" => "CREDITO",
            "diascredito" => 45,
            "cuenta" => "11205050"
        ]);
        $formapago->save();
    }
}
