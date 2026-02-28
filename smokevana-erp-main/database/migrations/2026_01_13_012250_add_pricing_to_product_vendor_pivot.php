<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds vendor-specific pricing columns to product-vendor pivot table
     */
    public function up()
    {
        Schema::table('products_wp_vendors_table_pivot', function (Blueprint $table) {
            // Vendor's cost price (what Go Hunter pays the vendor)
            $table->decimal('vendor_cost_price', 22, 4)->nullable()->after('wp_vendor_id');
            
            // Markup configuration
            $table->decimal('vendor_markup_amount', 22, 4)->nullable()->after('vendor_cost_price');
            $table->decimal('vendor_markup_percentage', 10, 2)->nullable()->after('vendor_markup_amount');
            
            // Calculated selling price (to Go Hunter customers)
            $table->decimal('dropship_selling_price', 22, 4)->nullable()->after('vendor_markup_percentage');
            
            // SKU mapping (vendor's SKU vs ERP SKU)
            $table->string('vendor_sku', 100)->nullable()->after('dropship_selling_price');
            
            // Primary vendor flag (for products with multiple vendors)
            $table->boolean('is_primary_vendor')->default(false)->after('vendor_sku');
            
            // Lead time in days for fulfillment
            $table->unsignedSmallInteger('lead_time_days')->default(0)->after('is_primary_vendor');
            
            // Minimum order quantity
            $table->unsignedInteger('min_order_qty')->default(1)->after('lead_time_days');
            
            // Status of this vendor-product relationship
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active')->after('min_order_qty');
            
            // Stock tracking at vendor level
            $table->decimal('vendor_stock_qty', 22, 4)->default(0)->after('status');
            $table->timestamp('stock_last_updated')->nullable()->after('vendor_stock_qty');
            
            // Notes specific to this product-vendor relationship
            $table->text('notes')->nullable()->after('stock_last_updated');
            
            // Indexes for common queries
            $table->index('is_primary_vendor');
            $table->index('status');
            $table->index('vendor_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products_wp_vendors_table_pivot', function (Blueprint $table) {
            $table->dropIndex(['is_primary_vendor']);
            $table->dropIndex(['status']);
            $table->dropIndex(['vendor_sku']);
            
            $table->dropColumn([
                'vendor_cost_price',
                'vendor_markup_amount',
                'vendor_markup_percentage',
                'dropship_selling_price',
                'vendor_sku',
                'is_primary_vendor',
                'lead_time_days',
                'min_order_qty',
                'status',
                'vendor_stock_qty',
                'stock_last_updated',
                'notes'
            ]);
        });
    }
};






