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
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('visibility')->nullable()->default('public');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->json('body')->nullable();
            $table->unsignedInteger('category')->nullable();
            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            // Check if columns exist before dropping them
            if (Schema::hasColumn('brands', 'visibility')) {
                $table->dropColumn('visibility');
            }

            if (Schema::hasColumn('brands', 'slug')) {
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('brands', 'logo')) {
                $table->dropColumn('logo');
            }

            if (Schema::hasColumn('brands', 'banner')) {
                $table->dropColumn('banner');
            }

            if (Schema::hasColumn('brands', 'body')) {
                $table->dropColumn('body');
            }

            if (Schema::hasColumn('brands', 'category')) {
                $table->dropForeign(['category']); // Drop the foreign key constraint first
                $table->dropColumn('category');
            }
        });
    }
};
