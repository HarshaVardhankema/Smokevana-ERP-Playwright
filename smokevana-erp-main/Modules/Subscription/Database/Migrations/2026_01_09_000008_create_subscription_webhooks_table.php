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
        Schema::create('subscription_webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id')->nullable();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Webhook identification
            $table->string('webhook_id')->unique();
            $table->string('gateway');
            $table->string('event_type');
            
            // Idempotency
            $table->string('idempotency_key')->nullable();
            
            // Payload
            $table->json('payload');
            $table->json('headers')->nullable();
            
            // Processing status
            $table->enum('status', ['pending', 'processing', 'processed', 'failed', 'skipped'])->default('pending');
            $table->unsignedInteger('processing_attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            
            // Error tracking
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();
            
            // Related records
            $table->unsignedInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('customer_subscriptions')->onDelete('set null');
            $table->unsignedInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('subscription_transactions')->onDelete('set null');
            
            // Metadata
            $table->string('ip_address')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['gateway', 'event_type']);
            $table->index('status');
            $table->index('idempotency_key');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_webhooks');
    }
};
