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
        Schema::create('sent_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('user_id');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'status', 'deleted']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_messages');
    }
};
