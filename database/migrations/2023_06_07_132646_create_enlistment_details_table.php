<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnlistmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enlistment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enlistments_id')->nullable();
            $table->foreign('enlistments_id')->references('id')->on('enlistments');

            $table->unsignedBigInteger('products_id')->nullable();
            $table->foreign('products_id')->references('id')->on('products');

            $table->decimal('kgrequeridos', 18, 2)->nullable();
            $table->decimal('precio_minimo', 18, 2)->nullable();
            $table->decimal('total_venta', 18, 2)->nullable();
            $table->decimal('porc_venta', 18, 2)->nullable();
            $table->decimal('costo_total', 18, 2)->nullable();
            $table->decimal('costo_Kilo', 18, 2)->nullable();
            $table->decimal('utilidad', 18, 2)->nullable();
            $table->decimal('porc_utilidad', 18, 2)->nullable();
            $table->decimal('newstock', 18, 2)->nullable();
            $table->decimal('merma', 18, 2)->nullable();
            $table->decimal('porc_merma', 18, 2)->nullable();
            $table->decimal('cost_transformation', 18, 2)->nullable();

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
        Schema::dropIfExists('enlistment_details');
    }
}
