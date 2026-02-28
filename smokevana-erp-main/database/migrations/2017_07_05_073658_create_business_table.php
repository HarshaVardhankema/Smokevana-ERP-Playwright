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
        Schema::create('business', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name');
            $table->integer('currency_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->string('tax_number_1', 100);
            $table->string('tax_label_1', 10);
            $table->string('tax_number_2', 100)->nullable();
            $table->string('tax_label_2', 10)->nullable();
            $table->float('default_profit_percent', 5, 2)->default(0);
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('time_zone')->default('Asia/Kolkata');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->enum('accounting_method', ['fifo', 'lifo', 'avco'])->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable();
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('includes');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->string('logo')->nullable();
            $table->string('sku_prefix')->nullable();
            $table->boolean('enable_tooltip')->default(1);
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
        // Drop all foreign key constraints that reference the business table
        $referencingTables = DB::select("
            SELECT DISTINCT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'business'
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
        
        // Now drop the business table
        Schema::dropIfExists('business');
    }
};
