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
     * Makes product_id and variation_id nullable in transaction_sell_lines
     * to support gift cards which don't have associated products.
     *
     * @return void
     */
    public function up()
    {
        // Get the actual foreign key constraint names from the database
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'transaction_sell_lines' 
            AND COLUMN_NAME IN ('product_id', 'variation_id')
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        // Drop foreign key constraints using their actual names
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE transaction_sell_lines DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist or already dropped, continue
            }
        }

        // Modify columns to be nullable using raw SQL for better compatibility
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY product_id INT UNSIGNED NULL');
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY variation_id INT UNSIGNED NULL');

        // Recreate foreign key constraints (they will allow NULL values)
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            $table->foreign('variation_id')
                  ->references('id')
                  ->on('variations')
                  ->onDelete('cascade');
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
            // Drop foreign key constraints
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variation_id']);
        });

        // Make columns NOT nullable again
        // Note: This will fail if there are existing NULL values
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY product_id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY variation_id INT UNSIGNED NOT NULL');

        // Recreate foreign key constraints
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            $table->foreign('variation_id')
                  ->references('id')
                  ->on('variations')
                  ->onDelete('cascade');
        });
    }
};
