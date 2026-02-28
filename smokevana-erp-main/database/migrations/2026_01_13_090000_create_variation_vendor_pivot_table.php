<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationVendorPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_vendor_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variation_id');
            $table->unsignedBigInteger('wp_vendor_id');
            $table->unsignedBigInteger('product_id'); // Denormalized for easier queries
            
            // Pricing
            $table->decimal('vendor_cost_price', 22, 4)->nullable();
            $table->decimal('markup_percentage', 8, 2)->nullable();
            $table->decimal('markup_amount', 22, 4)->nullable();
            $table->decimal('selling_price', 22, 4)->nullable();
            
            // Stock tracking
            $table->decimal('vendor_stock_qty', 22, 4)->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->boolean('track_stock')->default(true);
            
            // Vendor-specific SKU
            $table->string('vendor_sku', 100)->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'out_of_stock', 'discontinued'])->default('active');
            
            // Sync flags
            $table->boolean('needs_erp_sync')->default(false);
            $table->boolean('needs_ecommerce_sync')->default(false);
            
            // Timestamps for tracking changes
            $table->timestamp('cost_last_updated')->nullable();
            $table->timestamp('price_last_updated')->nullable();
            $table->timestamp('stock_last_updated')->nullable();
            $table->timestamp('erp_synced_at')->nullable();
            $table->timestamp('ecommerce_synced_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['variation_id', 'wp_vendor_id']);
            $table->index(['wp_vendor_id']);
            $table->index(['product_id']);
            $table->index(['needs_erp_sync']);
            $table->index(['needs_ecommerce_sync']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variation_vendor_pivot');
    }
}
