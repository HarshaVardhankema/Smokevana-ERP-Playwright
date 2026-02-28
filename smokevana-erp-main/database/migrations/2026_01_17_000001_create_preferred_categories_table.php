<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Preferred categories (Amazon-style): categories that show first in buyers' search results.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferred_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unique(['location_id', 'category_id']);
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferred_categories');
    }
};
