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
        Schema::create('shipping_stations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->integer('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->string('name', 256);
            $table->string('station_code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->text('equipment_notes')->nullable(); // For tracking label printers, scales, etc.
            $table->string('printer_name')->nullable(); // Label printer identifier
            $table->integer('user_id')->unsigned()->nullable(); // Assigned user
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->boolean('is_active')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            //Indexing
            $table->index('business_id');
            $table->index('location_id');
            $table->index('user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_stations');
    }
};
