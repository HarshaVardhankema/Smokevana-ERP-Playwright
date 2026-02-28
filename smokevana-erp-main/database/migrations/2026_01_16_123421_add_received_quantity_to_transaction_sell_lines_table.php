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
            if (!Schema::hasColumn('transaction_sell_lines', 'received_quantity')) {
                $table->decimal('received_quantity', 22, 4)->nullable()->default(0)->after('quantity');
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
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_sell_lines', 'received_quantity')) {
                $table->dropColumn('received_quantity');
            }
        });
    }
};
