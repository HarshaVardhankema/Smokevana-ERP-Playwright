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
        Schema::create('brand_config', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->json('email_settings')->nullable()->comment('JSON field to store mail_host, mail_port, mail_username, mail_password, mail_from_address, mail_from_name');
            $table->json('template_settings')->nullable()->comment('JSON field to store template_settings ');
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
        Schema::dropIfExists('brand_config');
    }
};

