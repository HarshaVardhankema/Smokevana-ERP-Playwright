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
        Schema::create('trash_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type'); // Model class name (e.g., App\Contact)
            $table->unsignedInteger('model_id'); // ID of the merged/deleted model
            $table->string('model_name')->nullable(); // Human-readable name (e.g., Contact name)
            $table->string('action_type'); // 'merged', 'deleted', 'archived', etc.
            $table->unsignedInteger('target_model_id')->nullable(); // ID of target (for merges)
            $table->string('target_model_type')->nullable(); // Target model class name
            $table->unsignedInteger('created_by'); // User who performed the action
            $table->unsignedInteger('business_id'); // Business ID for multi-tenant
            $table->text('json_data')->nullable(); // JSON data holder for all merged data details
            $table->text('description')->nullable(); // Human-readable description
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('model_type');
            $table->index('model_id');
            $table->index('action_type');
            $table->index('created_by');
            $table->index('business_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trash_sources');
    }
};
