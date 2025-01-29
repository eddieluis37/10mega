<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnlistmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enlistments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users');

            /*  la llave foranea de store_id se crea mediante otro archivo     */
            /*  la llave foranea de lote_id se crea mediante otro archivo     */

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete("cascade");

            $table->unsignedBigInteger('meatcut_id')->nullable();
            $table->foreign('meatcut_id')->references('id')->on('meatcuts')->onDelete("cascade");

            $table->decimal('nuevo_stock_padre', 18, 2)->default(0);
            $table->decimal('total_cost_transformation', 18, 2)->nullable();
            $table->enum('inventario', ['pending', 'added'])->default('pending');
            $table->date('fecha_alistamiento');
            $table->date('fecha_cierre')->nullable();

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
        Schema::dropIfExists('enlistments');
    }
}
