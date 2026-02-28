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
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable(); 
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('subject')->nullable();
            $table->longText('message')->nullable();
            $table->json('meta')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('staff_id')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_us_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contact_us')->onDelete('cascade');
            $table->string('key')->nullable();
            $table->text('value')->nullable();
            $table->integer('user_id')->nullable();
            $table->json('reactions')->nullable();
            $table->timestamps();
        });
        
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_us_meta');
        Schema::dropIfExists('contact_us');
    }
};
