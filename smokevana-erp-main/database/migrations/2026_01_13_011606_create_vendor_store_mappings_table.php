<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates table for multi-store vendor profiling (future-proofing)
     * Each vendor can have their own WooCommerce store for order routing
     */
    public function up()
    {
        Schema::create('vendor_store_mappings', function (Blueprint $table) {
            $table->id();
            
            // Vendor reference
            $table->unsignedBigInteger('wp_vendor_id');
            $table->unsignedInteger('business_id');
            
            // Store identification
            $table->string('store_name', 255);
            $table->string('store_code', 50)->nullable()->unique();
            
            // WooCommerce API credentials
            $table->string('woocommerce_url', 500);
            $table->string('woocommerce_consumer_key', 255);
            $table->text('woocommerce_consumer_secret'); // Encrypted
            $table->string('woocommerce_webhook_secret', 255)->nullable();
            
            // Store configuration
            $table->boolean('is_primary')->default(false);
            $table->enum('status', ['active', 'inactive', 'testing'])->default('active');
            
            // Sync settings
            $table->boolean('auto_sync_products')->default(true);
            $table->boolean('auto_sync_orders')->default(true);
            $table->boolean('auto_sync_inventory')->default(true);
            $table->unsignedSmallInteger('sync_interval_minutes')->default(15);
            
            // Connection tracking
            $table->timestamp('last_connection_test')->nullable();
            $table->boolean('connection_healthy')->default(false);
            $table->text('last_connection_error')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('wp_vendor_id');
            $table->index('business_id');
            $table->index('is_primary');
            $table->index('status');
            $table->index(['wp_vendor_id', 'is_primary']);
            
            // Foreign key
            $table->foreign('wp_vendor_id')->references('id')->on('wp_vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('vendor_store_mappings');
    }
};






