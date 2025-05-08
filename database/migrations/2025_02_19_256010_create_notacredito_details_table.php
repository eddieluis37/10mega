<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotacreditoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notacredito_details', function (Blueprint $table) {
            $table->bigIncrements('id');
         //   $table->unsignedBigInteger('notacredito_id');
            $table->unsignedBigInteger('product_id');
           /*  $table->unsignedBigInteger('sale_detail_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->unsignedBigInteger('inventario_id')->nullable(); */

            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->decimal('porc_desc', 10, 2)->default(0);
            $table->decimal('descuento', 12, 0)->default(0);
            $table->decimal('descuento_cliente', 10, 0)->default(0);
            $table->decimal('porc_iva', 10, 2)->default(0);
            $table->decimal('iva', 10, 0)->default(0);
            $table->decimal('porc_otro_impuesto', 10, 2)->default(0);
            $table->decimal('otro_impuesto', 12, 0)->default(0);
            $table->decimal('total_bruto', 12, 0)->default(0);
            $table->decimal('total', 12, 0)->default(0);

            $table->tinyInteger('status')->nullable()->default(1);
          /*   $table->tinyInteger('inventory_processed')
                ->default(0)
                ->comment('Indica si ya se actualizó el inventario para este detalle'); */

            $table->timestamps();

          //  $table->foreignId('notacredito_id')->constrained();
            /* $table->foreign('sale_detail_id')->references('id')->on('sale_details');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('lote_id')->references('id')->on('lotes');
            $table->foreign('inventario_id')->references('id')->on('inventarios'); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notacredito_details', function (Blueprint $table) {
            $table->dropForeign(['sale_detail_id']);
            $table->dropColumn('sale_detail_id');
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
            $table->dropForeign(['lote_id']);
            $table->dropColumn('lote_id');
            $table->dropForeign(['inventario_id']);
            $table->dropColumn('inventario_id');
            $table->dropColumn('inventory_processed');
        });
    }
}
