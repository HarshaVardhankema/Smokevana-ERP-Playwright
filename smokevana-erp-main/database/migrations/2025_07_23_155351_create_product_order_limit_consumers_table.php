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
        Schema::create('product_order_limit_consumers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('variant_id');
            $table->unsignedInteger('consumer_id');
            $table->unsignedInteger('session_id'); // product_order_limits.id

            $table->integer('order_count')->default(0);
            $table->integer('qty_count')->default(1);

            // other 
            $table->integer('blocked_attempts')->default(0);
            $table->dateTime('blocked_at')->nullable();
            $table->json('meta')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('product_order_limit_consumers');
    }
};
