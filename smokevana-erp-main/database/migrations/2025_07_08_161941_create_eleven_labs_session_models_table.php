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
        Schema::create('eleven_labs_session_models', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('conversation_id');
            $table->mediumText('token');
            $table->string('user_email');
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
        Schema::dropIfExists('eleven_labs_session_models');
    }
};
