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
        Schema::create('news_letter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->boolean('is_subscribed')->default(true);
            $table->string('custom_1')->nullable();
            $table->string('custom_2')->nullable();
            $table->string('custom_3')->nullable();
            $table->string('custom_4')->nullable();
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
        Schema::dropIfExists('news_letter_subscribers');
    }
};
