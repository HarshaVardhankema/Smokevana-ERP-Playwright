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
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_no')->unique();
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // References
            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('customer_subscriptions')->onDelete('cascade');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('subscription_invoices')->onDelete('set null');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Transaction type
            $table->enum('type', [
                'payment',          // Payment received
                'refund',           // Refund issued
                'chargeback',       // Chargeback from gateway
                'credit',           // Credit applied
                'adjustment',       // Manual adjustment
                'fee',              // Setup fee or other fees
                'proration'         // Proration amount
            ])->default('payment');
            
            // Transaction status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded',
                'disputed'
            ])->default('pending');
            
            // Amounts
            $table->decimal('amount', 22, 4);
            $table->string('currency', 10)->default('USD');
            $table->decimal('fee_amount', 22, 4)->default(0);
            $table->decimal('net_amount', 22, 4)->default(0);
            
            // Payment gateway details
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_response_code')->nullable();
            $table->text('gateway_response_message')->nullable();
            $table->json('gateway_response')->nullable();
            
            // Card/Payment details (masked)
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_exp_month', 2)->nullable();
            $table->string('card_exp_year', 4)->nullable();
            
            // Failure tracking
            $table->unsignedInteger('attempt_count')->default(1);
            $table->text('failure_reason')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // IP tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Metadata
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['subscription_id', 'type']);
            $table->index(['contact_id', 'status']);
            $table->index('gateway_transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
