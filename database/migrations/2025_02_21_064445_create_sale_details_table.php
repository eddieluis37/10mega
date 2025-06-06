<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained();
            
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores'); 
           
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes'); 

            $table->unsignedBigInteger('inventario_id')->nullable();
            $table->foreign('inventario_id')->references('id')->on('inventarios'); 
           
            $table->foreignId('product_id')->constrained(); 
            $table->decimal('quantity',8,2)->default(0)->nullable();         
            $table->decimal('price',12,2);
            $table->decimal('porc_desc',10,2)->default(0)->nullable();
            $table->decimal('descuento',12,0)->default(0)->nullable();
            $table->decimal('descuento_cliente',10,0)->default(0)->nullable();            
            $table->decimal('porc_iva',10,2)->default(0)->nullable(); 
            $table->decimal('iva',10,0)->default(0)->nullable();
            $table->decimal('porc_otro_impuesto',10,2)->default(0)->nullable(); 
            $table->decimal('otro_impuesto',12,0)->default(0)->nullable(); 
            $table->decimal('porc_impoconsumo',10,2)->default(0)->nullable(); 
            $table->decimal('impoconsumo',12,2)->default(0)->nullable(); 
            $table->decimal('total_bruto',12,0)->default(0)->nullable();        
            $table->decimal('total',12,0)->default(0)->nullable(); 
            
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
        Schema::dropIfExists('sale_details');
    }
}
