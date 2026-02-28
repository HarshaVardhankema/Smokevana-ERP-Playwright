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
        Schema::create('subscription_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Discount type
            $table->enum('type', ['percentage', 'fixed', 'trial_extension', 'free_months'])->default('percentage');
            $table->decimal('value', 22, 4);
            
            // Applicable plans
            $table->json('applicable_plan_ids')->nullable(); // null = all plans
            
            // Usage limits
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('max_uses_per_customer')->default(1);
            $table->unsignedInteger('current_uses')->default(0);
            
            // Duration
            $table->enum('duration', ['once', 'first_n_months', 'forever'])->default('once');
            $table->unsignedInteger('duration_months')->nullable();
            
            // Validity period
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            
            // Requirements
            $table->decimal('minimum_amount', 22, 4)->nullable();
            $table->boolean('first_subscription_only')->default(false);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Metadata
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'is_active']);
            $table->index('code');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_discounts');
    }
};
