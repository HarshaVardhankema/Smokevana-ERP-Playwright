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
        Schema::create('verifier_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_assigned')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for better performance
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifier_activity');
    }
};