<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportAgentConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_agent_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('business_id');
            $table->string('session_id')->nullable();
            $table->json('messages')->nullable();
            $table->string('context_page')->nullable();
            $table->integer('total_messages')->default(0);
            $table->integer('total_tokens_used')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'business_id']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_agent_conversations');
    }
}
