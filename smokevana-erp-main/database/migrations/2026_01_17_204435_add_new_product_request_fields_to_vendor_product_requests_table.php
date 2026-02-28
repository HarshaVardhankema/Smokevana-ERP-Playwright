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
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            // Request type: 'existing' for existing product, 'new' for new product creation
            $table->enum('request_type', ['existing', 'new'])->default('existing')->after('wp_vendor_id');
            
            // New product details (used when request_type = 'new')
            $table->string('proposed_name')->nullable()->after('request_type');
            $table->string('proposed_sku')->nullable()->after('proposed_name');
            $table->text('proposed_description')->nullable()->after('proposed_sku');
            $table->unsignedBigInteger('proposed_category_id')->nullable()->after('proposed_description');
            $table->unsignedBigInteger('proposed_brand_id')->nullable()->after('proposed_category_id');
            $table->decimal('proposed_cost_price', 22, 4)->nullable()->after('proposed_brand_id');
            $table->decimal('proposed_selling_price', 22, 4)->nullable()->after('proposed_cost_price');
            $table->string('proposed_unit')->nullable()->after('proposed_selling_price');
            $table->string('proposed_barcode')->nullable()->after('proposed_unit');
            $table->json('proposed_images')->nullable()->after('proposed_barcode');
            
            // Created product ID (set when new product request is approved)
            $table->unsignedBigInteger('created_product_id')->nullable()->after('product_id');
            
            // Add index
            $table->index('request_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            $table->dropIndex(['request_type']);
            $table->dropColumn([
                'request_type',
                'proposed_name',
                'proposed_sku',
                'proposed_description',
                'proposed_category_id',
                'proposed_brand_id',
                'proposed_cost_price',
                'proposed_selling_price',
                'proposed_unit',
                'proposed_barcode',
                'proposed_images',
                'created_product_id',
            ]);
        });
    }
};
