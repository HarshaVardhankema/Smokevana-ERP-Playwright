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
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_subscriptions', 'send_reminders')) {
                $table->boolean('send_reminders')->default(true)->after('auto_renew');
            }
            if (!Schema::hasColumn('customer_subscriptions', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('send_reminders');
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
        Schema::table('customer_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('customer_subscriptions', 'send_reminders')) {
                $table->dropColumn('send_reminders');
            }
            if (Schema::hasColumn('customer_subscriptions', 'reminder_sent_at')) {
                $table->dropColumn('reminder_sent_at');
            }
        });
    }
};
