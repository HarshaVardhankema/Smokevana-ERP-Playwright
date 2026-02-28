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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->string('isApproved')->nullable()->default(false);
            $table->rememberToken();
            $table->string('role')->nullable();
            $table->string('fcmToken')->nullable();
            $table->json('usermeta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropColumn('isApproved');
            $table->dropColumn('remember_token');
            $table->dropColumn('role');
            $table->dropColumn('fcmToken');
            $table->dropColumn('usermeta');
        });
    }
};
