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
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->integer('verified_qty')->nullable();
        });
        Schema::table('business', function (Blueprint $table) {
            $table->string('manage_order_module')->default('manual');
            $table->unsignedBigInteger('overselling_qty_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropColumn('verified_qty');
        });
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('manage_order_module');
            $table->dropColumn('overselling_qty_limit');
        });
    }
};
