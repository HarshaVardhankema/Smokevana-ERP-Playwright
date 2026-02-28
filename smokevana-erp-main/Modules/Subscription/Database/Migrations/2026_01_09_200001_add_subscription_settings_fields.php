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
        // Add previous_customer_group_id to customer_subscriptions
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('previous_customer_group_id')->nullable()->after('contact_id');
            $table->unsignedInteger('previous_selling_price_group_id')->nullable()->after('previous_customer_group_id');
        });

        // Add subscription_settings to business table if not exists
        if (!Schema::hasColumn('business', 'subscription_settings')) {
            Schema::table('business', function (Blueprint $table) {
                $table->json('subscription_settings')->nullable()->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['previous_customer_group_id', 'previous_selling_price_group_id']);
        });

        if (Schema::hasColumn('business', 'subscription_settings')) {
            Schema::table('business', function (Blueprint $table) {
                $table->dropColumn('subscription_settings');
            });
        }
    }
};
