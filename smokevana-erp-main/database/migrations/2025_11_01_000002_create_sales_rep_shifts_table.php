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
        Schema::create('sales_rep_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('sales_rep_id')->unsigned();
            $table->timestamp('shift_start_time')->nullable();
            $table->timestamp('shift_end_time')->nullable();
            $table->integer('duration_minutes')->nullable()->comment('Total shift duration in minutes');
            $table->string('status')->default('active')->comment('active, ended'); // active or ended
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('sales_rep_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'sales_rep_id']);
            $table->index('shift_start_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_rep_shifts');
    }
};


