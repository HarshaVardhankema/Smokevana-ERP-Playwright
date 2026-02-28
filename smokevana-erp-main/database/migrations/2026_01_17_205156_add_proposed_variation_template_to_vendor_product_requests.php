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
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            $table->string('proposed_variation_template')->nullable()->after('proposed_variations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            $table->dropColumn('proposed_variation_template');
        });
    }
};
