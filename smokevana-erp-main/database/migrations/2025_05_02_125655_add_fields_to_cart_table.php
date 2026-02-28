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
        Schema::table('carts', function (Blueprint $table) {
            $table->json('applied_discounts')->nullable();
        });
        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->string('discount_lable')->nullable();
        });
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->integer('barcode_picked_qty')->nullable();
            $table->integer('manual_picked_qty')->nullable();
            $table->integer('shorted_picked_qty')->nullable();
            $table->boolean('is_picked')->default(false);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('isPicked')->default(false)->after('picking_status');
            $table->integer('verifierID')->nullable()->after('pickerID');
            $table->string('supplier_ref_no')->nullable()->comment('If supplier Provide Ref No');
        });
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->string('row_discount_type')->nullable()->after('discount_percent')->comment('fixed or percentage'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('applied_discounts');
        });
        Schema::table('custom_discounts', function (Blueprint $table) {
            $table->dropColumn('discount_lable');
        });
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropColumn('barcode_picked_qty');
            $table->dropColumn('manual_picked_qty');
            $table->dropColumn('shorted_picked_qty');
            $table->dropColumn('is_picked');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('verifierID');
            $table->dropColumn('isPicked');
            $table->dropColumn('supplier_ref_no');
        });
        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->dropColumn('row_discount_type'); 
        });
    }
};
