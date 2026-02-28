<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // First check if the table exists
        if (!Schema::hasTable('vendor_product_requests')) {
            Schema::create('vendor_product_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('wp_vendor_id')->nullable();
                $table->integer('vendor_id')->unsigned()->nullable();
                $table->integer('product_id')->unsigned();
                $table->integer('variation_id')->unsigned()->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
                $table->text('rejection_reason')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('wp_vendor_id')->references('id')->on('wp_vendors')->onDelete('cascade');
                $table->foreign('vendor_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                
                // Indexes
                $table->index(['wp_vendor_id', 'status']);
                $table->index(['vendor_id', 'status']);
                $table->index(['product_id', 'status']);
            });
        } else {
            // Add wp_vendor_id if it doesn't exist
            if (!Schema::hasColumn('vendor_product_requests', 'wp_vendor_id')) {
                Schema::table('vendor_product_requests', function (Blueprint $table) {
                    $table->unsignedBigInteger('wp_vendor_id')->nullable()->after('id');
                });
                
                // Add foreign key and index separately
                Schema::table('vendor_product_requests', function (Blueprint $table) {
                    $table->foreign('wp_vendor_id')->references('id')->on('wp_vendors')->onDelete('cascade');
                    $table->index(['wp_vendor_id', 'status'], 'vpr_wp_vendor_status_idx');
                });
            }
            
            // Add variation_id if it doesn't exist
            if (!Schema::hasColumn('vendor_product_requests', 'variation_id')) {
                Schema::table('vendor_product_requests', function (Blueprint $table) {
                    $table->integer('variation_id')->unsigned()->nullable()->after('product_id');
                });
            }
            
            // Make vendor_id nullable using raw SQL (no doctrine/dbal needed)
            // This is safe because existing data should already have values
            DB::statement('ALTER TABLE vendor_product_requests MODIFY COLUMN vendor_id INT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasTable('vendor_product_requests')) {
            if (Schema::hasColumn('vendor_product_requests', 'wp_vendor_id')) {
                Schema::table('vendor_product_requests', function (Blueprint $table) {
                    $table->dropForeign(['wp_vendor_id']);
                    $table->dropIndex('vpr_wp_vendor_status_idx');
                    $table->dropColumn('wp_vendor_id');
                });
            }
            if (Schema::hasColumn('vendor_product_requests', 'variation_id')) {
                Schema::table('vendor_product_requests', function (Blueprint $table) {
                    $table->dropColumn('variation_id');
                });
            }
        }
    }
};
