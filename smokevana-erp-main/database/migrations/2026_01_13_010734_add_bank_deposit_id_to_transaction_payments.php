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
        Schema::table('transaction_payments', function (Blueprint $table) {
    $table->unsignedBigInteger('bank_deposit_id')->nullable()->after('business_id');
            $table->index('bank_deposit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
               $table->dropIndex(['bank_deposit_id']);
            $table->dropColumn('bank_deposit_id');

        });
    }
};
