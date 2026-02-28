<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Twilio\Rest\Autopilot\V1\Assistant\UpdateQueryOptions;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedInteger('user_id');
            $table->text('ticket_description');
            $table->string('status')->default('open');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};

