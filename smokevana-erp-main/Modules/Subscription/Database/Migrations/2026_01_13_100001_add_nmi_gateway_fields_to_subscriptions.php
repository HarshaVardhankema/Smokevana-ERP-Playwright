<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add gateway fields to customer_subscriptions
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_subscriptions', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('source');
            }
            if (!Schema::hasColumn('customer_subscriptions', 'gateway_subscription_id')) {
                $table->string('gateway_subscription_id')->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('customer_subscriptions', 'gateway_customer_id')) {
                $table->string('gateway_customer_id')->nullable()->after('gateway_subscription_id');
            }
            
            // Index for quick lookups
            $table->index('gateway_subscription_id', 'idx_gateway_sub_id');
        });

        // Add gateway fields to subscription_transactions
        Schema::table('subscription_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_transactions', 'gateway_subscription_id')) {
                $table->string('gateway_subscription_id')->nullable()->after('gateway_transaction_id');
            }
        });

        // Add payment fields to subscription_invoices
        Schema::table('subscription_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('subscription_invoices', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_gateway_sub_id');
            $table->dropColumn(['payment_gateway', 'gateway_subscription_id', 'gateway_customer_id']);
        });

        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropColumn('gateway_subscription_id');
        });

        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_reference']);
        });
    }
};
