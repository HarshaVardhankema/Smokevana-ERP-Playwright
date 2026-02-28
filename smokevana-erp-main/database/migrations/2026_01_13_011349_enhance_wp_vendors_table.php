<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enhances wp_vendors table for complete dropship vendor management
     */
    public function up()
    {
        Schema::table('wp_vendors', function (Blueprint $table) {
            // Link to ERP user for vendor portal access
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            
            // Link to contact (supplier type) for accounting
            $table->unsignedInteger('contact_id')->nullable()->after('user_id');
            
            // Business ownership (multi-tenant support)
            $table->unsignedInteger('business_id')->nullable()->after('contact_id');
            
            // Vendor contact information
            $table->string('email', 255)->nullable()->after('slug');
            $table->string('phone', 50)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('company_name', 255)->nullable()->after('address');
            
            // Vendor status
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('company_name');
            
            // WooCommerce store linkage (for future multi-store support)
            $table->unsignedInteger('woocommerce_store_id')->nullable()->after('wp_term_id');
            
            // Commission/profit structure
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage')->after('margin_percentage');
            $table->decimal('commission_value', 10, 2)->default(0)->after('commission_type');
            $table->decimal('default_markup_percentage', 10, 2)->default(0)->after('commission_value');
            
            // Payment terms
            $table->enum('payment_terms', ['immediate', 'weekly', 'biweekly', 'monthly'])->default('monthly')->after('default_markup_percentage');
            $table->string('payment_method', 100)->nullable()->after('payment_terms');
            
            // Additional metadata
            $table->text('notes')->nullable()->after('payment_method');
            $table->string('logo', 255)->nullable()->after('notes');
            
            // Indexes
            $table->index('business_id');
            $table->index('user_id');
            $table->index('contact_id');
            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('wp_vendors', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['contact_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
            
            $table->dropColumn([
                'user_id',
                'contact_id',
                'business_id',
                'email',
                'phone',
                'address',
                'company_name',
                'status',
                'woocommerce_store_id',
                'commission_type',
                'commission_value',
                'default_markup_percentage',
                'payment_terms',
                'payment_method',
                'notes',
                'logo'
            ]);
        });
    }
};


