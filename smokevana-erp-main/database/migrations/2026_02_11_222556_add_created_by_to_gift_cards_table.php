<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add created_by_user_id to track which admin/user created the gift card.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('gift_cards', 'created_by_user_id')) {
                $table->unsignedInteger('created_by_user_id')->nullable()->after('purchaser_contact_id')->comment('User/admin who created this gift card');
                $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
                $table->index('created_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            if (Schema::hasColumn('gift_cards', 'created_by_user_id')) {
                $table->dropForeign(['created_by_user_id']);
                $table->dropColumn('created_by_user_id');
            }
        });
    }
};
