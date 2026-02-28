<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_tracking_statuses')) {
            // Delete any records with in_transit or delivered status
            DB::table('order_tracking_statuses')
                ->whereIn('status', ['in_transit', 'delivered'])
                ->delete();

            // Modify the enum column to only include packed and shipped
            // MySQL doesn't support direct ALTER ENUM, so we use MODIFY COLUMN
            DB::statement("ALTER TABLE order_tracking_statuses MODIFY COLUMN status ENUM('packed', 'shipped') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('order_tracking_statuses')) {
            // Restore the enum column to include all statuses
            DB::statement("ALTER TABLE order_tracking_statuses MODIFY COLUMN status ENUM('packed', 'shipped', 'in_transit', 'delivered') NOT NULL");
        }
    }
};
