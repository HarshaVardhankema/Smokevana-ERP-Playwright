<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProposedVariationsToVendorProductRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_product_requests', 'proposed_variations')) {
                $table->json('proposed_variations')->nullable()->after('proposed_image');
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
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_product_requests', 'proposed_variations')) {
                $table->dropColumn('proposed_variations');
            }
        });
    }
}
