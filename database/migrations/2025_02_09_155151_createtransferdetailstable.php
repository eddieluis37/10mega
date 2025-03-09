<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfers_id')->nullable();
            $table->foreign('transfers_id')->references('id')->on('transfers');

            $table->unsignedBigInteger('lote_prod_traslado_id')->nullable();
            $table->foreign('lote_prod_traslado_id')->references('id')->on('lotes');    
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');

            $table->decimal('actual_stock_origen', 18, 2)->nullable();
            
            $table->decimal('kgrequeridos', 18, 2)->nullable();

            $table->decimal('nuevo_stock_origen', 18, 2)->nullable();

            $table->decimal('actual_stock_destino', 18, 2)->nullable();

            $table->decimal('nuevo_stock_destino', 18, 2)->nullable();

            $table->decimal('costo_unitario_origen', 18, 2)->nullable();

            $table->decimal('subtotal_traslado', 18, 2)->nullable();

            $table->boolean('status')->parent_select()->default(true)->nullable();  

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
        Schema::dropIfExists('transfer_details');
    }
}
