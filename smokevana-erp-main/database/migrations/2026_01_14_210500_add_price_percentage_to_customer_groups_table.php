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
        Schema::table('customer_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_groups', 'price_percentage')) {
                $table->decimal('price_percentage', 5, 2)->nullable()->after('selling_price_group_id');
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
        Schema::table('customer_groups', function (Blueprint $table) {
            if (Schema::hasColumn('customer_groups', 'price_percentage')) {
                $table->dropColumn('price_percentage');
            }
        });
    }
};
