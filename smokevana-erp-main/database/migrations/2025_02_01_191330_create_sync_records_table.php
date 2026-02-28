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
        Schema::create('sync_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('synced_id')->nullable();
            $table->unsignedBigInteger('next_id')->nullable();
            $table->string('synced')->nullable();
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
        Schema::dropIfExists('sync_records');
    }
};
