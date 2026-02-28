<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add gift_cards_to_purchase JSON column to carts for purchasing gift cards as part of order.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'gift_cards_to_purchase')) {
                $table->json('gift_cards_to_purchase')->nullable()->after('gift_card_code')->comment('Array of gift cards to purchase in this cart (JSON format)');
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
            if (Schema::hasColumn('carts', 'gift_cards_to_purchase')) {
                $table->dropColumn('gift_cards_to_purchase');
            }
        });
    }
};
