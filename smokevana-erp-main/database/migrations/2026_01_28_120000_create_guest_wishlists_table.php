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
        Schema::create('guest_wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_session_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('business_id');
            $table->timestamps();

            $table->foreign('guest_session_id')->references('id')->on('guest_sessions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->unique(['guest_session_id', 'product_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_wishlists');
    }
};
