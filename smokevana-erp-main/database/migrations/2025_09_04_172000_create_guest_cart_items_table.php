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
        Schema::create('guest_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_session_id');
            $table->foreign('guest_session_id')->references('id')->on('guest_sessions')->onDelete('cascade');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('variation_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->integer('qty');
            $table->string('item_type')->default('line_item');
            $table->unsignedInteger('discount_id')->nullable();
            $table->string('lable')->default('Item');
            $table->timestamps();
            
            $table->index(['guest_session_id', 'product_id', 'variation_id']);
            $table->index('guest_session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_cart_items');
    }
};
