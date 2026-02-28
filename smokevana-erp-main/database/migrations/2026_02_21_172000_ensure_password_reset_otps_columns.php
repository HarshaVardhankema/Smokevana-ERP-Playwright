<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure password_reset_otps has all required columns (add any that are missing).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('password_reset_otps')) {
            return;
        }

        if (!Schema::hasColumn('password_reset_otps', 'contact_id')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->unsignedBigInteger('contact_id')->nullable()->after('email');
            });
        }
        if (!Schema::hasColumn('password_reset_otps', 'otp')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->string('otp', 6)->after('contact_id');
            });
        }
        if (!Schema::hasColumn('password_reset_otps', 'expires_at')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('otp');
            });
        }
        if (!Schema::hasColumn('password_reset_otps', 'used_at')) {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->timestamp('used_at')->nullable()->after('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // One-way migration: do not drop columns (could break other envs).
    }
};
