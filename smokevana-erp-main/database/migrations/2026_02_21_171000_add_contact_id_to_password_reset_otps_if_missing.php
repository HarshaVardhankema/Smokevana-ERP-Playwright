<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add contact_id to password_reset_otps if missing.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('password_reset_otps')) {
            return;
        }
        if (Schema::hasColumn('password_reset_otps', 'contact_id')) {
            return;
        }
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->nullable()->after('email');
        });
        // Optional: add foreign key only if contacts table exists and we don't have FK yet
        try {
            Schema::table('password_reset_otps', function (Blueprint $table) {
                $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // Ignore if FK already exists or DB doesn't support it
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('password_reset_otps') || !Schema::hasColumn('password_reset_otps', 'contact_id')) {
            return;
        }
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
        });
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->dropColumn('contact_id');
        });
    }
};
