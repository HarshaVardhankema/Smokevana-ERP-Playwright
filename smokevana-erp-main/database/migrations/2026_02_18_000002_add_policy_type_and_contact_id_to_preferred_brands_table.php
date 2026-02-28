<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add policy_type (preferred, blocked, restricted) and contact_id (who created it).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('preferred_brands', function (Blueprint $table) {
            $table->enum('policy_type', ['preferred', 'blocked', 'restricted'])->default('preferred')->after('status');
            $table->unsignedInteger('contact_id')->nullable()->after('policy_type')->comment('Contact/user who created this entry');
            $table->index('policy_type');
            $table->index('contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('preferred_brands', function (Blueprint $table) {
            $table->dropIndex(['policy_type']);
            $table->dropIndex(['contact_id']);
            $table->dropColumn(['policy_type', 'contact_id']);
        });
    }
};
