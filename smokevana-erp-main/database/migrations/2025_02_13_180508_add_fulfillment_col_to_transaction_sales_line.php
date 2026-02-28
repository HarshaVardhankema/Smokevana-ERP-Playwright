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
            $table->unsignedInteger('ordered_quantity')->nullable()->default(null);
            $table->unsignedInteger('picked_quantity')->nullable()->default(null);
            $table->boolean('isVerified')->nullable()->default(false);

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

            if (Schema::hasColumn('transaction_sell_lines', 'ordered_quantity')) {
                $table->dropColumn('ordered_quantity');
                
            }
            $table->dropColumn('picked_quantity');
            $table->dropColumn('isVerified');
        });
    }
};
