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
        if (Schema::hasTable('stock_alerts')) {
            return;
        }

        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_recursive')->default(0);
            $table->unsignedInteger('variation_id')->nullable();
            $table->boolean('notified')->default(0);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');

            // Indexes for better query performance
            $table->index('product_id');
            $table->index('contact_id');
            $table->index('variation_id');
            $table->index('notified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_alerts');
    }
};
