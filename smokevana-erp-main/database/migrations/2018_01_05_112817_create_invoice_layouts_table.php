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
        Schema::create('invoice_layouts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('header_text')->nullable();
            $table->string('invoice_no_prefix')->nullable();
            $table->string('invoice_heading')->nullable();
            $table->string('sub_total_label')->nullable();
            $table->string('discount_label')->nullable();
            $table->string('tax_label')->nullable();
            $table->string('total_label')->nullable();
            $table->string('logo')->nullable();

            $table->boolean('show_logo')->default(0);
            $table->boolean('show_business_name')->default(0);
            $table->boolean('show_location_name')->default(1);
            $table->boolean('show_landmark')->default(1);
            $table->boolean('show_city')->default(1);
            $table->boolean('show_state')->default(1);
            $table->boolean('show_zip_code')->default(1);
            $table->boolean('show_country')->default(1);
            $table->boolean('show_mobile_number')->default(1);
            $table->boolean('show_alternate_number')->default(0);
            $table->boolean('show_email')->default(0);
            $table->boolean('show_tax_1')->default(1);
            $table->boolean('show_tax_2')->default(0);
            $table->boolean('show_barcode')->default(0);

            $table->string('highlight_color', 10)->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('is_default')->default(0);
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        // Drop foreign key constraints that reference invoice_layouts first
        // Check if business_locations table has the foreign key
        if (Schema::hasTable('business_locations')) {
            // Drop invoice_layout_id foreign key
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'business_locations' 
                AND COLUMN_NAME = 'invoice_layout_id' 
                AND REFERENCED_TABLE_NAME = 'invoice_layouts'
            ");
            
            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE business_locations DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Also check for sale_invoice_layout_id foreign key (if it exists)
            $saleForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'business_locations' 
                AND COLUMN_NAME = 'sale_invoice_layout_id' 
                AND REFERENCED_TABLE_NAME = 'invoice_layouts'
            ");
            
            if (!empty($saleForeignKeys)) {
                $constraintName = $saleForeignKeys[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE business_locations DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }
        
        // Now drop the invoice_layouts table
        Schema::dropIfExists('invoice_layouts');
    }
};
