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
        Schema::create('visit_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('sales_rep_id');
            $table->unsignedBigInteger('lead_id');
            $table->datetime('start_time');
            $table->integer('duration')->nullable(); // in minutes
            $table->enum('status', ['completed', 'in_progress', 'missing_proof', 'pending'])->default('pending');
            $table->enum('visit_type', ['initial', 'follow_up', 'demo', 'meeting', 'support'])->default('initial');
            $table->boolean('location_proof')->default(false);
            $table->boolean('photo_proof')->default(false);
            $table->boolean('signature_proof')->default(false);
            $table->boolean('video_proof')->default(false);
            $table->string('location_proof_path')->nullable();
            $table->text('photo_proof_paths')->nullable();
            $table->string('signature_proof_path')->nullable();
            $table->string('video_proof_path')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('sales_rep_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['business_id', 'sales_rep_id']);
            $table->index(['business_id', 'status']);
            $table->index(['start_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visit_tracking');
    }
};
