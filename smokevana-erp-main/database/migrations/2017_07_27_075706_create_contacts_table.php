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
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('type')->index();
            $table->string('supplier_business_name')->nullable();
            $table->string('name');
            $table->string('tax_number')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('landmark')->nullable();
            $table->string('mobile');
            $table->string('landline')->nullable();
            $table->string('alternate_number')->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->integer('created_by')->unsigned();
            $table->boolean('is_default')->default(0);
            $table->softDeletes();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        // Drop all foreign key constraints that reference the contacts table
        $referencingTables = DB::select("
            SELECT DISTINCT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'contacts'
            AND REFERENCED_TABLE_NAME IS NOT NULL
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
        
        // Now drop the contacts table
        Schema::dropIfExists('contacts');
    }
};
