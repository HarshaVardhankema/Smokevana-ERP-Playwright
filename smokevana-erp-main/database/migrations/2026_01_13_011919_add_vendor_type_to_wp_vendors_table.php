<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds vendor_type column to support three vendor types:
     * - erp: Normal internal vendor (existing ERP flow)
     * - woocommerce: Auto-synced from WooCommerce (read-only)
     * - erp_dropship: ERP vendors who fulfill dropship orders via portal
     */
    public function up(): void
    {
        Schema::table('wp_vendors', function (Blueprint $table) {
            // Add vendor type column with default 'woocommerce' for backward compatibility
            $table->enum('vendor_type', ['erp', 'woocommerce', 'erp_dropship'])
                  ->default('woocommerce')
                  ->after('business_id')
                  ->comment('erp=internal vendor, woocommerce=auto-synced from WC, erp_dropship=portal fulfillment');
            
            // Add is_synced_from_woocommerce flag for tracking
            $table->boolean('is_synced_from_woocommerce')
                  ->default(false)
                  ->after('vendor_type')
                  ->comment('True if vendor was auto-created from WooCommerce sync');
            
            // Add last_woocommerce_sync timestamp
            $table->timestamp('woocommerce_last_sync')
                  ->nullable()
                  ->after('is_synced_from_woocommerce');
            
            // Add index for vendor_type for faster queries
            $table->index('vendor_type');
        });
        
        // Update existing vendors based on wp_term_id presence
        // If they have wp_term_id, they're likely WooCommerce vendors
        \DB::statement("
            UPDATE wp_vendors 
            SET vendor_type = 'woocommerce', 
                is_synced_from_woocommerce = 1 
            WHERE wp_term_id IS NOT NULL AND wp_term_id > 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wp_vendors', function (Blueprint $table) {
            $table->dropIndex(['vendor_type']);
            $table->dropColumn(['vendor_type', 'is_synced_from_woocommerce', 'woocommerce_last_sync']);
        });
    }
};
