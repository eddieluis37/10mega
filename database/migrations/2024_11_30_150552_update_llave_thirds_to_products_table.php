<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Crear una nueva llave foránea
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_third_id')->nullable()
            ->after('name')->constrained('brand_third')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Eliminar la nueva llave foránea
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_third_id']);
            $table->dropColumn('brand_third_id');
        });
    }
};
