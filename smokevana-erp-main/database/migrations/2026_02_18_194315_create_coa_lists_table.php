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
        Schema::create('coa_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coa_category_id')->unsigned();
            $table->foreign('coa_category_id')->references('id')->on('coa_categories')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('link', 2048);
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('coa_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coa_lists');
    }
};
