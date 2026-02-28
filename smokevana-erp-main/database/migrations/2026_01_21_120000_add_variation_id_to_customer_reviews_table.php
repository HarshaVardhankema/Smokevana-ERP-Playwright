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
            if (!Schema::hasColumn('customer_reviews', 'variation_id')) {
                $table->unsignedInteger('variation_id')->nullable()->after('product_id');
                $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
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
        Schema::table('customer_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('customer_reviews', 'variation_id')) {
                $table->dropForeign(['variation_id']);
                $table->dropColumn('variation_id');
            }
        });
    }
};
