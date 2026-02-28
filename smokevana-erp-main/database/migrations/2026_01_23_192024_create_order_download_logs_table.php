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
        // Drop table if it exists (from previous failed migration)
        Schema::dropIfExists('order_download_logs');
        
        Schema::create('order_download_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('contact_id'); // User who downloaded
            $table->unsignedInteger('business_id');
            $table->string('download_type'); // 'pdf', 'csv', 'excel'
            $table->string('filename');
            $table->integer('total_orders');
            $table->json('order_numbers'); // Array of order numbers
            $table->json('order_ids'); // Array of order IDs
            $table->json('filters')->nullable(); // Filters applied
            $table->json('date_range')->nullable(); // Date range if applicable
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('contact_id');
            $table->index('business_id');
            $table->index('download_type');
            $table->index('created_at');
            
            // Foreign keys
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_download_logs');
    }
};
