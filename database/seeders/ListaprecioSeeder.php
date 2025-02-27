<?php

namespace Database\Seeders;

use App\Models\Listaprecio;

use Illuminate\Database\Seeder;

class ListaprecioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lp = new Listaprecio([
            "centrocosto_id" => "1",
            "nombre" => "1 - DOMICILIO",
            "tipo" => "NICHO",
            "fecha_cierre" => now(),
        ]);
        $lp->save();

        $lp = new Listaprecio([
            "centrocosto_id" => "1",
            "nombre" => "2 - PUNTO DE VENTAS",
            "tipo" => "NICHO",
            "fecha_cierre" => now(),
        ]);
        $lp->save();

        // PARA SEGUNDO CENTRO COSTO

        /*    $lp = new Listaprecio([
            "id"=> "15", 
            "centrocosto_id" => "2", 
            "nombre" => "GENERAL - MIN DE DIOS", 
            "tipo" => "GENERAL"
        ]); $lp->save();
        
        $lp = new Listaprecio([
            "id"=> "16", 
            "centrocosto_id" => "2", 
            "nombre" => "HOGAR - MIN DE DIOS", 
            "tipo" => "NICHO"
        ]); $lp->save();

        $lp = new Listaprecio([
            "id"=> "17", 
            "centrocosto_id" => "2", 
            "nombre" => "PUNTO DE VENTA - MIN DE DIOS", 
            "tipo" => "NICHO"
        ]); $lp->save();
        
        $lp = new Listaprecio([
            "id"=> "18", 
            "centrocosto_id" => "2", 
            "nombre" => "INSTITUCIONAL/FAMA - MIN DE DIOS", 
            "tipo" => "NICHO"
        ]); $lp->save();
        
        $lp = new Listaprecio([
            "id"=> "19", 
            "centrocosto_id" => "2", 
            "nombre" => "REST PROD PORCIONADO - MIN DE DIOS", 
            "tipo" => "NICHO"
        ]); $lp->save();

        $lp = new Listaprecio([
            "id"=> "20", 
            "centrocosto_id" => "2", 
            "nombre" => "REST PROD EN POSTA - MIN DE DIOS", 
            "tipo" => "NICHO"
        ]); $lp->save();      */
    }
}
