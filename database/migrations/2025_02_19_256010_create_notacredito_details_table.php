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
            $table->id();
            
            $table->foreignId('notacredito_id')->constrained();

            $table->unsignedBigInteger('sale_detail_id')->nullable()->after('product_id');
            $table->foreign('sale_detail_id')->references('id')->on('sale_details');
            
            $table->unsignedBigInteger('store_id')->nullable()->after('sale_detail_id');
            $table->foreign('store_id')->references('id')->on('stores');
            
            $table->unsignedBigInteger('lote_id')->nullable()->after('store_id');
            $table->foreign('lote_id')->references('id')->on('lotes');
            
            $table->unsignedBigInteger('inventario_id')->nullable()->after('lote_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');
            

            $table->foreignId('product_id')->constrained();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            
            $table->decimal('porc_desc', 10, 2)->default(0)->nullable();
            $table->decimal('descuento', 12, 0)->default(0)->nullable();
            $table->decimal('descuento_cliente', 10, 0)->default(0)->nullable();
            $table->decimal('porc_iva', 10, 2)->default(0)->nullable();
            $table->decimal('iva', 10, 0)->default(0)->nullable();
            $table->decimal('porc_otro_impuesto', 10, 2)->default(0)->nullable();
            $table->decimal('otro_impuesto', 12, 0)->default(0)->nullable();
            $table->decimal('total_bruto', 12, 0)->default(0)->nullable();
            $table->decimal('total', 12, 0)->default(0)->nullable();

            $table->boolean('status')->parent_select()->default(true)->nullable();

            $table->boolean('inventory_processed')->default(false)
            ->comment('Indica si ya se actualizó el inventario para este detalle')
            ->after('inventario_id');

            $table->timestamps();
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
