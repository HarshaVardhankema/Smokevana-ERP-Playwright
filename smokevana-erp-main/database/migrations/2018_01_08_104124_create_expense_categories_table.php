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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('code')->nullable();
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
        // Drop foreign key constraints that reference expense_categories first
        // Check if transactions table has the foreign key
        if (Schema::hasTable('transactions')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'transactions' 
                AND COLUMN_NAME = 'expense_category_id' 
                AND REFERENCED_TABLE_NAME = 'expense_categories'
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
        
        // Now drop the expense_categories table
        Schema::dropIfExists('expense_categories');
    }
};
