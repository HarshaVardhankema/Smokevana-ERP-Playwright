<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Gift order options on guest sessions (from cart) for packing slip / invoice visibility.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guest_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_sessions', 'is_gift')) {
                $table->boolean('is_gift')->default(false)->after('applied_discounts');
            }
            if (!Schema::hasColumn('guest_sessions', 'hide_prices_for_recipient')) {
                $table->boolean('hide_prices_for_recipient')->default(false)->after('is_gift');
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
        Schema::table('guest_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('guest_sessions', 'hide_prices_for_recipient')) {
                $table->dropColumn('hide_prices_for_recipient');
            }
            if (Schema::hasColumn('guest_sessions', 'is_gift')) {
                $table->dropColumn('is_gift');
            }
        });
    }
};
