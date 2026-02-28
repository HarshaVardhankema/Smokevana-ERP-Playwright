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
        Schema::create('customer_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subscription_no')->unique();
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Customer reference
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Plan reference
            $table->unsignedInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('restrict');
            
            // Location (if applicable)
            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
            
            // Subscription lifecycle
            $table->enum('status', [
                'pending',      // Awaiting payment
                'trial',        // In trial period
                'active',       // Active subscription
                'paused',       // Temporarily paused
                'past_due',     // Payment overdue but in grace period
                'cancelled',    // Cancelled by user
                'expired',      // Subscription period ended
                'suspended'     // Suspended by admin
            ])->default('pending');
            
            // Subscription dates
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            
            // Payment info
            $table->decimal('amount_paid', 22, 4)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_subscription_id')->nullable();
            $table->string('gateway_customer_id')->nullable();
            
            // Auto-renewal settings
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('next_billing_date')->nullable();
            $table->unsignedInteger('billing_attempts')->default(0);
            $table->timestamp('last_billing_attempt')->nullable();
            
            // Grace period
            $table->unsignedInteger('grace_period_days')->default(3);
            $table->timestamp('grace_period_ends_at')->nullable();
            
            // Cancellation info
            $table->text('cancellation_reason')->nullable();
            $table->enum('cancellation_type', ['immediate', 'end_of_period', 'refund'])->nullable();
            
            // Source tracking
            $table->enum('source', ['erp_manual', 'ecommerce_portal', 'api', 'import', 'upgrade', 'renewal'])->default('erp_manual');
            
            // Prime benefits tracking
            $table->json('applied_benefits')->nullable();
            $table->unsignedInteger('reward_points_earned')->default(0);
            $table->decimal('total_savings', 22, 4)->default(0);
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index(['plan_id', 'status']);
            $table->index('status');
            $table->index('expires_at');
            $table->index('next_billing_date');
            $table->index('gateway_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_subscriptions');
    }
};
