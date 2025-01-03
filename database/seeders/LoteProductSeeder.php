<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoteProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
       {
           // Obtener los primeros 50 productos
           $products = DB::table('products')->take(50)->pluck('id');

           // Crear un array para insertar los registros
           $loteProductData = [];
           $loteIds = [1, 2]; // IDs de los dos primeros lotes

           foreach ($loteIds as $loteId) {
               foreach ($products as $productId) {
                   $loteProductData[] = [
                       'lote_id' => $loteId,
                       'product_id' => $productId,
                       'created_at' => Carbon::now(),
                       'updated_at' => Carbon::now(),
                   ];
               }
           }

           // Insertar los registros en la tabla lote_product
           DB::table('lote_products')->insert($loteProductData);
       }
}
