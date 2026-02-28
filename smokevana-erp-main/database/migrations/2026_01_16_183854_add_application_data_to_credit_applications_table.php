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
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->json('application_data')->nullable()->after('average_revenue_per_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->dropColumn('application_data');
        });
    }
};
