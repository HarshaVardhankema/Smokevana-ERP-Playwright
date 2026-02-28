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
        Schema::table('variations', function (Blueprint $table) {
            if (!Schema::hasColumn('variations', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->nullable()->after('default_purchase_price')->comment('Average rating for this variation');
            }
            if (!Schema::hasColumn('variations', 'total_reviews')) {
                $table->integer('total_reviews')->default(0)->after('average_rating')->comment('Total active reviews for this variation');
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
        Schema::table('variations', function (Blueprint $table) {
            if (Schema::hasColumn('variations', 'total_reviews')) {
                $table->dropColumn('total_reviews');
            }
            if (Schema::hasColumn('variations', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
        });
    }
};
