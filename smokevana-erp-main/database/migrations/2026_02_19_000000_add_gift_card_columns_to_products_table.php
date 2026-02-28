<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add gift card columns to products table.
     * is_gift_card: exclude from general catalog unless viewing gift card category.
     * gift_card_expires_at, gift_card_stock: used when is_gift_card = 1.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_gift_card')) {
                $table->boolean('is_gift_card')->default(false)->after('is_tobacco_product');
            }
            if (!Schema::hasColumn('products', 'gift_card_expires_at')) {
                $table->date('gift_card_expires_at')->nullable()->after('is_gift_card');
            }
            if (!Schema::hasColumn('products', 'gift_card_stock')) {
                $table->decimal('gift_card_stock', 22, 4)->nullable()->after('gift_card_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'gift_card_stock')) {
                $table->dropColumn('gift_card_stock');
            }
            if (Schema::hasColumn('products', 'gift_card_expires_at')) {
                $table->dropColumn('gift_card_expires_at');
            }
            if (Schema::hasColumn('products', 'is_gift_card')) {
                $table->dropColumn('is_gift_card');
            }
        });
    }
};
