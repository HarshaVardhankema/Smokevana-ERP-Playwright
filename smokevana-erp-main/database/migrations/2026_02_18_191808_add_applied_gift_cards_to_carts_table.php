<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add applied_gift_cards and gift_card_amount columns to carts table.
     * applied_gift_cards: JSON array of gift card IDs that are applied to this cart
     * gift_card_amount: Total amount from applied gift cards to deduct from order total
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'applied_gift_cards')) {
                $table->json('applied_gift_cards')->nullable()->after('gift_card_code')->comment('Array of gift card IDs applied to this cart (redeemed balance)');
            }
            if (!Schema::hasColumn('carts', 'gift_card_amount')) {
                $table->decimal('gift_card_amount', 15, 4)->default(0)->after('applied_gift_cards')->comment('Total amount from applied gift cards to deduct from order total');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'gift_card_amount')) {
                $table->dropColumn('gift_card_amount');
            }
            if (Schema::hasColumn('carts', 'applied_gift_cards')) {
                $table->dropColumn('applied_gift_cards');
            }
        });
    }
};
