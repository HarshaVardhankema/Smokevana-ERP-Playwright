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
        Schema::create('coa_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->string('name', 255);
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('business_id');
            $table->index('location_id');
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coa_categories');
    }
};
