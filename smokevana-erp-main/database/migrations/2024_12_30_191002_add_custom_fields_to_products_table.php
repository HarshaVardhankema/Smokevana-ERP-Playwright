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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('ml')->nullable()->default(0);
            $table->integer('ct')->nullable()->default(0);
            $table->string('productVisibility')->nullable()->default('public');
            $table->json('locationTaxType')->nullable(); //[12,14,16]
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('ml');
            $table->dropColumn('ct');
            $table->dropColumn('productVisibility');
            $table->dropColumn('locationTaxType');
        });
    }
};
