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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('guest_session_id')->nullable()->after('user_id');
            $table->foreign('guest_session_id')->references('id')->on('guest_sessions')->onDelete('cascade');
            $table->index('guest_session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['guest_session_id']);
            $table->dropIndex(['guest_session_id']);
            $table->dropColumn('guest_session_id');
        });
    }
};
