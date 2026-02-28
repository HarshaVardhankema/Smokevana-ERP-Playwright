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
        Schema::table('multiple_address_customer', function (Blueprint $table) {
            // Add company column if it doesn't exist
            if (!Schema::hasColumn('multiple_address_customer', 'company')) {
                $table->string('company')->nullable()->after('last_name');
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
        Schema::table('multiple_address_customer', function (Blueprint $table) {
            // Only drop if column exists (safety check)
            if (Schema::hasColumn('multiple_address_customer', 'company')) {
                $table->dropColumn('company');
            }
        });
    }
};
