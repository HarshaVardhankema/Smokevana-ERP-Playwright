<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Preferred brands (Amazon-style): brands that show first in buyers' search results.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferred_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('brand_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->unique(['location_id', 'brand_id']);
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
        Schema::dropIfExists('preferred_brands');
    }
};
