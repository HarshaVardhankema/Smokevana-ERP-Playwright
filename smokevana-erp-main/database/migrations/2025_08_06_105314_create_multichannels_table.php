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
    Schema::create('multichannels', function (Blueprint $table) {
        $table->id();

        // New columns
        // $table->unsignedInteger('user_id');
        // $table->unsignedInteger('business_id');
        // $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        // $table->foreign('user_id')->references('id')->on('contacts')->onDelete('cascade');
        $table->string('type');
        $table->boolean('visibility')->default(true); // or you can use enum if you have multiple visibility types
        $table->string('status')->default('active'); // or use enum if status values are limited
        $table->unsignedInteger('created_by')->nullable();
        $table->unsignedInteger('updated_by')->nullable();
        $table->unsignedInteger('deleted_by')->nullable();

        $table->string('title');
        $table->string('url');
        $table->string('thumbnail_url')->nullable();
        $table->json('short_meta')->nullable();

        $table->json('meta_data')->nullable(); // Example: {"description": "some html or text"}



        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('multichannels');
}

};
