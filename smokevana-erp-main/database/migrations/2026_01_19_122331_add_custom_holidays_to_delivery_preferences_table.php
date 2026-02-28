<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_preferences', function (Blueprint $table) {
            $table->json('custom_holidays')->nullable()->after('observed_holidays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_preferences', function (Blueprint $table) {
            $table->dropColumn('custom_holidays');
        });
    }
};
