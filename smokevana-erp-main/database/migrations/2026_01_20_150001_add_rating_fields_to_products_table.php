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
        Schema::table('products', function (Blueprint $table) {
            // Add rating columns to store calculated values from customer reviews
            $table->decimal('average_rating', 3, 2)->nullable()->after('alert_quantity')->comment('Average rating calculated from customer reviews (1-5)');
            $table->integer('total_reviews')->default(0)->after('average_rating')->comment('Total number of active customer reviews');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['average_rating', 'total_reviews']);
        });
    }
};
