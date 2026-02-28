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
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('location_id')->unsigned()->after('business_id');
            $table->foreign('location_id')->references('id')->on('business_locations');

            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraint first
        if (Schema::hasTable('transactions')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'transactions' 
                AND COLUMN_NAME = 'location_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE transactions DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        // Now drop the column (this will also drop the index)
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
};
