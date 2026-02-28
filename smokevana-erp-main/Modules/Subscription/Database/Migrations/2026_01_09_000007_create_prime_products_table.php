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
        // Pivot table for prime-only products
        Schema::create('prime_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Plan-specific product access (null = all prime plans)
            $table->unsignedInteger('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            
            // Access type
            $table->enum('access_type', ['exclusive', 'early_access', 'discounted'])->default('exclusive');
            
            // Early access settings
            $table->unsignedInteger('early_access_days')->default(0);
            
            // Additional discount for prime members
            $table->decimal('additional_discount', 5, 2)->default(0);
            
            // Priority/sorting
            $table->unsignedInteger('sort_order')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Validity period
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['product_id', 'plan_id'], 'prime_product_plan_unique');
            
            // Indexes
            $table->index(['business_id', 'is_active']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prime_products');
    }
};
