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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // References
            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('customer_subscriptions')->onDelete('cascade');
            $table->unsignedInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            
            // Event tracking
            $table->string('event_type');
            $table->string('event_description');
            
            // Status change tracking
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            
            // Changes tracking
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Context
            $table->string('source')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Metadata
            $table->unsignedInteger('performed_by')->nullable();
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['subscription_id', 'event_type']);
            $table->index('event_type');
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
        Schema::dropIfExists('subscription_logs');
    }
};
