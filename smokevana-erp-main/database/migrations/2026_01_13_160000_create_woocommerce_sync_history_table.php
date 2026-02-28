<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('woocommerce_sync_history')) {
            Schema::create('woocommerce_sync_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->string('sync_type', 50); // products, orders, customers, stock
                $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
                $table->integer('total_items')->default(0);
                $table->integer('synced_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->integer('skipped_count')->default(0);
                $table->json('details')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                
                $table->index('business_id');
                $table->index('sync_type');
                $table->index('status');
                $table->index(['business_id', 'sync_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_sync_history');
    }
};
