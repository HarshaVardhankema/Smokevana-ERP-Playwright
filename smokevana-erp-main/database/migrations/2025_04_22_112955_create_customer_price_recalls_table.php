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
        Schema::create('customer_price_recalls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('variation_id');
            $table->unsignedInteger('business_id');
            $table->decimal('last_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('variation_id')->references('id')->on('variations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
            $table->foreign('business_id')->references('id')->on('business');
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
        Schema::dropIfExists('customer_price_recalls');
    }
};
