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
        Schema::table('contacts', function (Blueprint $table) {
              $table->enum('vendor_type', ['normal', 'dropshipping'])->default('normal')->after('type');
            
            // Commission settings for dropshipping vendors
            $table->enum('commission_type', ['percentage', 'fixed'])->nullable()->after('vendor_type');
            $table->decimal('commission_value', 15, 2)->nullable()->after('commission_type');
            
            // Default markup percentage for dropshipping products from this vendor
            $table->decimal('default_markup_percentage', 8, 2)->nullable()->after('commission_value');
            
            // Margin percentage (profit margin) for products from this vendor
            $table->decimal('margin_percentage', 8, 2)->nullable()->after('default_markup_percentage');
            
            // Payment terms for dropshipping: how often vendor payments are processed
            $table->enum('dropship_payment_terms', ['immediate', 'weekly', 'biweekly', 'monthly'])->nullable()->after('margin_percentage');
            
            // Preferred payment method for this vendor
            $table->string('dropship_payment_method', 100)->nullable()->after('dropship_payment_terms');
            
            // Lead time in days for this vendor's products
            $table->integer('lead_time_days')->nullable()->after('dropship_payment_method');
            
            // Minimum order quantity for this vendor
            $table->integer('min_order_qty')->nullable()->after('lead_time_days');
            
            // Auto-forward orders to this vendor
            $table->boolean('auto_forward_orders')->default(false)->after('min_order_qty');
            
            // Notes specific to dropshipping arrangement
            $table->text('dropship_notes')->nullable()->after('auto_forward_orders');
            
            // WooCommerce/External system term ID for sync
            $table->unsignedBigInteger('wp_term_id')->nullable()->after('dropship_notes');
            
            // Index for quick filtering by vendor type
            $table->index('vendor_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
                       $table->dropIndex(['vendor_type']);
            
            $table->dropColumn([
                'vendor_type',
                'commission_type',
                'commission_value',
                'default_markup_percentage',
                'margin_percentage',
                'dropship_payment_terms',
                'dropship_payment_method',
                'lead_time_days',
                'min_order_qty',
                'auto_forward_orders',
                'dropship_notes',
                'wp_term_id',
            ]);


        });
    }
};
