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
        if (!Schema::hasTable('order_tracking_statuses')) {
            Schema::create('order_tracking_statuses', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('transaction_id'); // Changed from unsignedBigInteger to match transactions.id type
                $table->enum('status', ['packed', 'shipped']);
                $table->datetime('status_date')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamps();
                
                // Foreign keys
                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                
                // Indexes
                $table->index(['transaction_id', 'status']);
                $table->unique(['transaction_id', 'status']); // One status per order
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_tracking_statuses');
    }
};
