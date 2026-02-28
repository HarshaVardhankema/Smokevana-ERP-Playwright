<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Gift order options on transactions (from cart) for packing slip / invoice visibility.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'is_gift')) {
                $table->boolean('is_gift')->default(false);
            }
            if (!Schema::hasColumn('transactions', 'hide_prices_for_recipient')) {
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
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'hide_prices_for_recipient')) {
                $table->dropColumn('hide_prices_for_recipient');
            }
            if (Schema::hasColumn('transactions', 'is_gift')) {
                $table->dropColumn('is_gift');
            }
        });
    }
};
