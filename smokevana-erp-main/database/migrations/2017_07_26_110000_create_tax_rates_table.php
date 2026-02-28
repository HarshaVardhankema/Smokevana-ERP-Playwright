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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('name');
            //$table->enum('calculation_type', ['fixed', 'percentage']);
            $table->float('amount', 22, 4);
            $table->boolean('is_tax_group')->default('0');
            //$table->enum('rounding_type', ['up', 'down', 'normal']);
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First, drop foreign keys FROM tax_rates table (outgoing foreign keys)
        if (Schema::hasTable('tax_rates')) {
            $outgoingForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'tax_rates'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($outgoingForeignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `tax_rates` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        // Drop all foreign key constraints that reference the tax_rates table (incoming foreign keys)
        $referencingTables = DB::select("
            SELECT DISTINCT TABLE_NAME, CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'tax_rates'
        ");
        
        // Drop each foreign key constraint
        foreach ($referencingTables as $ref) {
            if (Schema::hasTable($ref->TABLE_NAME)) {
                try {
                    DB::statement("ALTER TABLE `{$ref->TABLE_NAME}` DROP FOREIGN KEY `{$ref->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        // Now drop the tax_rates table
        Schema::dropIfExists('tax_rates');
    }
};
