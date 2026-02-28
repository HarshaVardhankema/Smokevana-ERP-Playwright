<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
           Schema::table('products', function (Blueprint $table) {
            // Add product_source_type column with enum values
            $table->enum('product_source_type', ['in_house', 'dropshipped'])
                ->default('in_house')
                ->after('type')
                ->comment('in_house = Created in ERP, dropshipped = Synced from WooCommerce');
            
            // Add index for filtering
            $table->index('product_source_type');
        });

        // Migrate existing products: If woocommerce_product_id is set, mark as dropshipped
        DB::statement("
            UPDATE products 
            SET product_source_type = 'dropshipped' 
            WHERE woocommerce_product_id IS NOT NULL 
            AND woocommerce_product_id != ''
        ");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
                Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['product_source_type']);
            $table->dropColumn('product_source_type');
        });

        });
    }
};
