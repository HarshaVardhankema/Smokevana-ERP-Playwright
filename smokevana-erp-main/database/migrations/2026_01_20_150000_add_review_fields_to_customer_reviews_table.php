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
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->after('description');
            $table->string('public_name', 255)->nullable()->after('title');
            $table->string('media_url', 2048)->nullable()->after('public_name')->comment('Optional photo/video URL');
            $table->string('media_type', 20)->nullable()->after('media_url')->comment('photo|video or other descriptor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'public_name', 'media_url', 'media_type']);
        });
    }
};
