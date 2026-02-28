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
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no')->unique();
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // References
            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('customer_subscriptions')->onDelete('cascade');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->unsignedInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('restrict');
            
            // Invoice type
            $table->enum('type', ['subscription', 'renewal', 'upgrade', 'downgrade', 'prorated', 'refund'])->default('subscription');
            
            // Billing period
            $table->timestamp('billing_period_start')->nullable();
            $table->timestamp('billing_period_end')->nullable();
            
            // Amounts
            $table->decimal('subtotal', 22, 4)->default(0);
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->decimal('total', 22, 4)->default(0);
            $table->string('currency', 10)->default('USD');
            
            // Payment status
            $table->enum('status', ['draft', 'pending', 'paid', 'partially_paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 22, 4)->default(0);
            $table->decimal('amount_due', 22, 4)->default(0);
            
            // Payment details
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_invoice_id')->nullable();
            
            // Line items (JSON for flexibility)
            $table->json('line_items')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // PDF storage
            $table->string('pdf_path')->nullable();
            
            // Metadata
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['subscription_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index('due_date');
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
