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
        Schema::create('product_order_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->nullable(); // if need to apply limit to all variants of a product ( check second)
            $table->unsignedInteger('variant_id')->nullable(); // if need to apply on specific variant (check first )
            $table->integer('order_limit')->nullable();
            $table->boolean('is_active')->default(true);

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('product_order_limits');
    }
};
