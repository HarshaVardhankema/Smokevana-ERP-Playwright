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
        // Find and drop any foreign key on product_id column
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'vendor_product_requests' 
            AND COLUMN_NAME = 'product_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `vendor_product_requests` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Make product_id nullable
        DB::statement('ALTER TABLE `vendor_product_requests` MODIFY `product_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make product_id NOT NULL again (this will fail if there are NULL values)
        DB::statement('ALTER TABLE `vendor_product_requests` MODIFY `product_id` BIGINT UNSIGNED NOT NULL');
    }
};
