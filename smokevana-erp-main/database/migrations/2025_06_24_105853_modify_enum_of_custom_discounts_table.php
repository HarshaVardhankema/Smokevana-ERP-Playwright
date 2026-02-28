<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN discountType ENUM('productAdjustment', 'cartAdjustment', 'freeShipping', 'buyXgetX', 'buyXgetY') NULL");

        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN applyDate DATETIME NULL");
        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN endDate DATETIME NULL");

        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->longText('description')->nullable();
            $table->integer('per_customer_limit')->nullable();
        });
        // create new col at business table
        Schema::table('business', function (Blueprint $table) {
            $table->string('woocommerce_wh_general_secret')->nullable()->after('woocommerce_wh_od_secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN discountType ENUM('productAdjustment', 'cartAdustment', 'freeShipping', 'buyXgetX', 'buyXgetY') NULL");
        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN applyDate DATE NOT NULL");
        DB::statement("ALTER TABLE custom_discounts MODIFY COLUMN endDate DATE NOT NULL");

        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('per_customer_limit');
        });
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('woocommerce_wh_general_secret');
        });
    }
};
