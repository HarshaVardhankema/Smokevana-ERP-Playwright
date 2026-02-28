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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('price', 22, 4)->default(0);
            $table->decimal('setup_fee', 22, 4)->default(0);
            $table->string('currency', 10)->default('USD');
            
            // Billing cycle
            $table->enum('billing_type', ['recurring', 'one_time', 'date_based'])->default('recurring');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annual', 'annual', 'lifetime', 'custom'])->default('monthly');
            $table->unsignedInteger('billing_interval_days')->nullable();
            
            // Trial period
            $table->boolean('has_trial')->default(false);
            $table->unsignedInteger('trial_days')->default(0);
            
            // Customer Group mapping (Prime integration)
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('set null');
            
            // Selling price group for exclusive pricing
            $table->unsignedInteger('selling_price_group_id')->nullable();
            $table->foreign('selling_price_group_id')->references('id')->on('selling_price_groups')->onDelete('set null');
            
            // Features & Benefits JSON
            $table->json('features')->nullable();
            $table->json('benefits')->nullable();
            
            // Prime specific settings
            $table->boolean('is_prime')->default(false);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->unsignedInteger('reward_points_multiplier')->default(1);
            $table->boolean('fast_delivery_enabled')->default(false);
            $table->boolean('prime_products_access')->default(false);
            $table->boolean('bnpl_enabled')->default(false); // Buy Now Pay Later
            $table->decimal('bnpl_limit', 22, 4)->default(0);
            $table->unsignedInteger('bnpl_days')->default(30);
            
            // Plan limits
            $table->unsignedInteger('max_subscribers')->nullable();
            $table->unsignedInteger('current_subscribers')->default(0);
            
            // Status & display
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->nullable();
            
            // Metadata
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'is_active']);
            $table->index(['is_prime', 'is_active']);
            $table->index('customer_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};
