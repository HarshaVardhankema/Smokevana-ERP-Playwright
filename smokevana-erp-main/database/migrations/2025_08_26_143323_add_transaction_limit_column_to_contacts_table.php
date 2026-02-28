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
        Schema::table('contacts', function (Blueprint $table) {
            $table->decimal('transaction_limit', 22, 4)->nullable()->after('credit_limit');
            $table->boolean('is_auto_send_due_notification')->default(false)->nullable()->after('transaction_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('transaction_limit');
            $table->dropColumn('is_auto_send_due_notification');
        });
    }
};
