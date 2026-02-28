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
            $table->integer('brand_id')->unsigned()->nullable()->after('location_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->index('brand_id');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->integer('location_id')->unsigned()->nullable()->after('business_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->index('location_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('location_id')->unsigned()->nullable()->after('business_id');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->index('location_id');
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
            $table->dropForeign(['brand_id']);
            $table->dropIndex(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id']);
            $table->dropColumn('location_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id']);
            $table->dropColumn('location_id');
        });
    }
};
