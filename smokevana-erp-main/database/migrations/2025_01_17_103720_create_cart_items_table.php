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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('variation_id');
            $table->unsignedInteger('qty');
            $table->string('item_type')->nullable()->default('line_item');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->string('lable')->nullable()->default('Item');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
