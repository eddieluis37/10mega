<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaReciboDineroDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja_recibo_dinero_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recibodecaja_id');           
            $table->foreign('recibodecaja_id')->references('id')->on('recibodecajas');

            $table->unsignedBigInteger('user_id');           
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->unsignedBigInteger('caja_id')->nullable();         
            $table->foreign('caja_id')->references('id')->on('cajas');
           
            $table->unsignedBigInteger('cuentas_por_cobrar_id')->nullable();
            $table->foreign('cuentas_por_cobrar_id')->references('id')->on('cuentas_por_cobrars');                
           
            $table->decimal('vr_deuda', 12, 0)->default(0)->nullable();
            $table->decimal('vr_pago', 12, 0)->default(0)->nullable();
            $table->decimal('nvo_saldo', 12, 0)->default(0)->nullable();
           
            
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
        Schema::dropIfExists('caja_recibo_dinero_details');
    }
}
