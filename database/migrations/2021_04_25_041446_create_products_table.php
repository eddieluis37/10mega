<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 20)->unique();
            $table->string('barcode', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0)->nullable();
            $table->decimal('iva', 10)->default(0);
            $table->decimal('otro_impuesto', 10)->default(0);
            $table->decimal('impoconsumo', 10)->default(0);

            // Tipo de producto
            $table->enum('type', ['simple', 'combo', 'dish'])->default('simple');

            $table->decimal('price_fama', 10, 0)->default(1)->nullable(); // precio minimo en la linea de las famas

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('meatcut_id')->default(61); // funciona tambien con subcategoria ERP
            $table->unsignedBigInteger('categories_comerciales_id');
            $table->unsignedBigInteger('subcategory_comerciales_id');


            $table->unsignedBigInteger('unitofmeasure_id')->default(1);
            $table->decimal('quantity', 10, 2)->default(1)->nullable();   // para la composicion        

            $table->decimal('fisico', 18, 2)->default(1); // valor de cantidades en inventario tangible real           
            $table->unsignedBigInteger('level_product_id')->default(2);

            $table->decimal('stock', 18, 2)->default(0); // valor de cantidades de unidades sea KG

            $table->integer('alerts');
            $table->string('image', 100)->nullable();
            $table->boolean('status')->parent_select()->default(true);

            $table->foreign('category_id')->references('id')->on('categories')->onDelete("cascade");
            $table->foreign('meatcut_id')->references('id')->on('meatcuts')->onDelete("cascade");
            $table->foreign('categories_comerciales_id')->references('id')->on('categories_comerciales')->onDelete("cascade");
            $table->foreign('subcategory_comerciales_id')->references('id')->on('subcategory_comerciales')->onDelete("cascade");
            $table->foreign('level_product_id')->references('id')->on('levels_products')->onDelete("cascade");

            $table->foreign('unitofmeasure_id')->references('id')->on('unitsofmeasures')->onDelete("cascade");

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
        Schema::dropIfExists('products');
    }
}
