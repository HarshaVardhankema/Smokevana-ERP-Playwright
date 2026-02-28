<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add image column to gift_cards table for custom gift card images.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('gift_cards', 'image')) {
                $table->string('image')->nullable()->after('message')->comment('Gift card image filename');
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
        Schema::table('gift_cards', function (Blueprint $table) {
            if (Schema::hasColumn('gift_cards', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
