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
        Schema::create('user_table_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('business_id');
            $table->string('table_name', 100); // e.g., 'contact_table', 'sell_table'
            $table->string('view_name', 100)->nullable(); // Optional: user-friendly name for the view
            $table->json('column_preferences'); // JSON array of column visibility settings
            $table->boolean('is_default')->default(false); // If this is the user's default view
            $table->timestamps();
            
            // Indexes for faster lookups
            $table->index(['user_id', 'table_name']);
            $table->index(['business_id', 'table_name']);
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_table_preferences');
    }
};
