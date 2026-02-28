<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add gift_card_code to carts table for applying gift cards during checkout.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'gift_card_code')) {
                $table->string('gift_card_code', 50)->nullable()->after('hide_prices_for_recipient')->comment('Gift card code applied to this cart');
                $table->index('gift_card_code');
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
            if (Schema::hasColumn('carts', 'gift_card_code')) {
                $table->dropIndex(['gift_card_code']);
                $table->dropColumn('gift_card_code');
            }
        });
    }
};
