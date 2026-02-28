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
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('shipping_station_id')->unsigned()->nullable()->after('shipping_status');
            $table->foreign('shipping_station_id')->references('id')->on('shipping_stations')->onDelete('set null');
            $table->index('shipping_station_id');
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
            $table->dropForeign(['shipping_station_id']);
            $table->dropIndex(['shipping_station_id']);
            $table->dropColumn('shipping_station_id');
        });
    }
};
