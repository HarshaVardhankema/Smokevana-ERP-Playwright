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
        Schema::table('variation_location_details', function (Blueprint $table) {
            // if(!Schema::hasColumn('variation_location_details', 'in_stock_qty')){
                $table->unsignedInteger('in_stock_qty')->nullable()->default(0);
            // }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
        // if(!Schema::hasColumn('variation_location_details', 'in_stock_qty')){
            $table->dropColumn('in_stock_qty');
        // }
        });
    }
};
