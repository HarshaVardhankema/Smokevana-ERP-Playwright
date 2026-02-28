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
        Schema::create('woocommerce_sync_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->enum('sync_type', ['products', 'orders', 'vendors', 'inventory'])->default('products');
            $table->enum('trigger_type', ['manual', 'cron', 'webhook'])->default('manual');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('total_items')->default(0);
            $table->integer('synced_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->integer('skipped_items')->default(0);
            $table->text('error_message')->nullable();
            $table->json('sync_details')->nullable(); // Store detailed info about each item
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('triggered_by')->nullable(); // User ID who triggered manual sync
            $table->timestamps();

            $table->index(['business_id', 'sync_type']);
            $table->index(['status', 'created_at']);
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('woocommerce_sync_history');
    }
};
