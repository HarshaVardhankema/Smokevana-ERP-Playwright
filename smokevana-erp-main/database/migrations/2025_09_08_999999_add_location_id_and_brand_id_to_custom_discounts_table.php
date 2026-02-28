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
        Schema::table('custom_discounts', function (Blueprint $table) {
            //
            $table->json('brand_id')->nullable();
            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->index('location_id');
        });

        Schema::table('location_tax_charges', function (Blueprint $table) {
            $table->json('web_location_id')->nullable()->after('id');
            $table->json('brand_id')->nullable()->after('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_discounts', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['location_id']);
            
            // Drop index if it exists
            $table->dropIndex(['location_id']);
            
            // Then drop the columns
            $table->dropColumn(['brand_id', 'location_id']);
        });

        Schema::table('location_tax_charges', function (Blueprint $table) {
            $table->dropColumn(['web_location_id', 'brand_id']);
        });
    }
};
