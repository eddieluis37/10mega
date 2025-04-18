<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibodecajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /* Registra los recibos de caja, tanto para ingresos (pagos de clientes) como para egresos (pago a proveedores).
En el caso de abonos totales sobre cuentas por cobrar se utilizará el tipo "Ingreso" y para pagos totales en cuentas por pagar se registrará un recibo de caja tipo "Egreso". */

    public function up()
    {
        Schema::create('recibodecajas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('third_id')->nullable();
            $table->foreign('third_id')->references('id')->on('thirds');       
          

            $table->decimal('vr_total_deuda', 12, 0)->default(0)->nullable();
            $table->decimal('vr_total_pago', 12, 0)->default(0)->nullable();
            $table->decimal('nvo_total_saldo', 12, 0)->default(0)->nullable();

            $table->date('fecha_elaboracion')->nullable();
          

            $table->enum('status', ['0', '1', '2', '3', '4', '5'])->default('0');
            // Tipo de recibo: 
            // '0' => Ninguno, '1' => Ingreso (abono de clientes), '2' => Egreso (pago a proveedores), '3' => Otros.
            $table->enum('tipo', ['0', '1', '2', '3'])->default('0');
            $table->enum('realizar_un', ['Abono a deuda', 'Anticipo', 'Avanzado (Impuestos, descuentos, ajustes)'])
                ->default('Abono a deuda');
            $table->text('observations')->nullable(); // campo text area 

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
        Schema::dropIfExists('recibodecajas');
    }
}
