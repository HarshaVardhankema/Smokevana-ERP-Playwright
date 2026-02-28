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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->enum('type', ['single', 'variable']);
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('sub_category_id')->unsigned()->nullable();
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('tax')->unsigned()->nullable();
            $table->foreign('tax')->references('id')->on('tax_rates');
            $table->enum('tax_type', ['inclusive', 'exclusive']);
            $table->boolean('enable_stock')->default(0);
            $table->decimal('alert_quantity', 22, 4)->default(0);
            $table->string('sku');
            $table->enum('barcode_type', ['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14']);
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            //Indexing
            $table->index('name');
            $table->index('business_id');
            $table->index('unit_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First, drop foreign keys FROM products table (outgoing foreign keys)
        // This prevents issues when dropping the table
        if (Schema::hasTable('products')) {
            $outgoingForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'products'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($outgoingForeignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `products` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        // Drop all foreign key constraints that reference the products table (incoming foreign keys)
        $referencingTables = DB::select("
            SELECT DISTINCT TABLE_NAME, CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'products'
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
        
        // Now drop the products table
        Schema::dropIfExists('products');
    }
};
