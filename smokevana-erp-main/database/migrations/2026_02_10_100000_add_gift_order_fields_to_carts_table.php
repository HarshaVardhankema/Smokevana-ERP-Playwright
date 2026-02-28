<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Gift order options on cart so cart page can display and persist "Send as gift" / "Hide prices for recipient".
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'is_gift')) {
                $table->boolean('is_gift')->default(false);
            }
            if (!Schema::hasColumn('carts', 'hide_prices_for_recipient')) {
                $table->boolean('hide_prices_for_recipient')->default(false);
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
            if (Schema::hasColumn('carts', 'hide_prices_for_recipient')) {
                $table->dropColumn('hide_prices_for_recipient');
            }
            if (Schema::hasColumn('carts', 'is_gift')) {
                $table->dropColumn('is_gift');
            }
        });
    }
};
