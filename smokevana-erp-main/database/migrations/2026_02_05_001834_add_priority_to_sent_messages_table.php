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
        Schema::table('sent_messages', function (Blueprint $table) {
            $table->enum('priority', ['urgent', 'non_urgent'])->default('non_urgent')->after('status');
            $table->index(['user_id', 'priority', 'status', 'deleted']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sent_messages', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'priority', 'status', 'deleted']);
            $table->dropColumn('priority');
        });
    }
};
