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
        Schema::table('contact_us', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->after('id');
            $table->unsignedInteger('brand_id')->nullable()->after('location_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->index('location_id');
            $table->index('brand_id');
        });
        
        // add url to brands table
        Schema::table('brands', function (Blueprint $table) {
            $table->string('brand_url')->nullable()->after('logo');
        });

        // add location id and brand id to news letter table
        Schema::table('news_letter_subscribers', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->after('id');
            $table->unsignedInteger('brand_id')->nullable()->after('location_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->index('location_id');
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['brand_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['brand_id']);
            $table->dropColumn('location_id');
            $table->dropColumn('brand_id');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('brand_url');
        });

        Schema::table('news_letter_subscribers', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['brand_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['brand_id']);
            $table->dropColumn('location_id');
            $table->dropColumn('brand_id');
        });


        
    }
};
