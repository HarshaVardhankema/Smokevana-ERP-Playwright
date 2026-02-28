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
        if (!Schema::hasTable('group_sub_taxes')) {
            Schema::create('group_sub_taxes', function (Blueprint $table) {
                $table->integer('group_tax_id')->unsigned();
                $table->foreign('group_tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
                $table->integer('tax_id')->unsigned();
                $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraints first
        if (Schema::hasTable('group_sub_taxes')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'group_sub_taxes'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `group_sub_taxes` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        Schema::dropIfExists('group_sub_taxes');
    }
};
