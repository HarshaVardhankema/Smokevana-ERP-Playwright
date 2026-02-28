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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('store_name');
            $table->text('full_address');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('state');
            $table->string('city');
            $table->string('country');
            $table->string('zip_code');
            $table->unsignedInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign key constraint for created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('leads');
    }
};
