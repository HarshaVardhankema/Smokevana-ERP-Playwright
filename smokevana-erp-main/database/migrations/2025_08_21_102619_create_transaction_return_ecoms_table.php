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
        Schema::create('transaction_return_ecoms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('variation_id');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('return_price');
            $table->unsignedInteger('parent_sell_line_id');
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
        Schema::dropIfExists('transaction_return_ecoms');
    }
};
