<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            $table->string('proposed_type', 20)->nullable()->default('single')->after('proposed_selling_price');
            $table->string('proposed_image')->nullable()->after('proposed_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_product_requests', function (Blueprint $table) {
            $table->dropColumn(['proposed_type', 'proposed_image']);
        });
    }
};
