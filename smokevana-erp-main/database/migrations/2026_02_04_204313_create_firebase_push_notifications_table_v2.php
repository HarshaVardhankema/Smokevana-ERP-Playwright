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
        Schema::create('firebase_push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->unsignedBigInteger('user_id');
            $table->string('platform')->default('web'); // web, android, ios
            $table->timestamp('timestamp')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes
            $table->unique(['token', 'user_id']);
            $table->index(['user_id', 'is_active']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firebase_push_notifications');
    }
};
