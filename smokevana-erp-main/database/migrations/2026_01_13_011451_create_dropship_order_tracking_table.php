<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates table for tracking dropship order fulfillment and sync status
     */
    public function up()
    {
        Schema::create('dropship_order_tracking', function (Blueprint $table) {
            $table->id();
            
            // Order references
            $table->unsignedInteger('transaction_id')->comment('Child order ID (wp_sales_order)');
            $table->unsignedInteger('parent_transaction_id')->comment('Parent sales_order ID');
            $table->unsignedBigInteger('wp_vendor_id');
            $table->unsignedInteger('business_id');
            
            // WooCommerce sync info
            $table->string('woocommerce_order_id', 100)->nullable();
            $table->string('woocommerce_status', 50)->nullable();
            $table->timestamp('woocommerce_last_sync')->nullable();
            
            // Shipping/tracking information
            $table->string('tracking_number', 255)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->string('carrier_tracking_url', 500)->nullable();
            $table->decimal('shipping_cost', 22, 4)->nullable();
            
            // Important dates
            $table->timestamp('order_placed_at')->nullable();
            $table->timestamp('vendor_notified_at')->nullable();
            $table->timestamp('vendor_accepted_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Status tracking
            $table->enum('fulfillment_status', [
                'pending',           // Order created, waiting for vendor
                'vendor_notified',   // Vendor has been notified
                'vendor_accepted',   // Vendor accepted the order
                'processing',        // Vendor is processing
                'ready_to_ship',     // Ready for pickup/shipping
                'shipped',           // Shipped with tracking
                'in_transit',        // In transit to customer
                'out_for_delivery',  // Out for delivery
                'delivered',         // Delivered to customer
                'completed',         // All steps complete
                'cancelled',         // Order cancelled
                'returned'           // Order returned
            ])->default('pending');
            
            $table->enum('sync_status', [
                'pending',    // Not yet synced
                'synced',     // Successfully synced
                'failed',     // Sync failed
                'retrying'    // Retry in progress
            ])->default('pending');
            
            $table->unsignedTinyInteger('sync_attempts')->default(0);
            $table->text('sync_error_message')->nullable();
            
            // Customer communication
            $table->boolean('customer_notified_shipped')->default(false);
            $table->boolean('customer_notified_delivered')->default(false);
            
            // Financial tracking
            $table->decimal('vendor_payout_amount', 22, 4)->nullable();
            $table->enum('vendor_payout_status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('vendor_payout_date')->nullable();
            
            // Notes and metadata
            $table->text('vendor_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('meta_data')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_id');
            $table->index('parent_transaction_id');
            $table->index('wp_vendor_id');
            $table->index('business_id');
            $table->index('woocommerce_order_id');
            $table->index('fulfillment_status');
            $table->index('sync_status');
            $table->index('tracking_number');
            $table->index(['business_id', 'fulfillment_status']);
            $table->index(['wp_vendor_id', 'fulfillment_status']);
            
            // Foreign keys
            $table->foreign('wp_vendor_id')->references('id')->on('wp_vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dropship_order_tracking');
    }
};






