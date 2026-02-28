<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add used_at column to password_reset_otps if missing (e.g. table created before column was in migration).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('password_reset_otps')) {
            return;
        }
        if (Schema::hasColumn('password_reset_otps', 'used_at')) {
            return;
        }
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->timestamp('used_at')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('password_reset_otps') && Schema::hasColumn('password_reset_otps', 'used_at')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->dropColumn('used_at');
            });
        }
    }
};
