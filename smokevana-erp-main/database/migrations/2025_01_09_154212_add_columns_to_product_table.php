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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('maxSaleLimit')->nullable();
            // $table->string('barcode_no')->nullable()->unique();
            $table->boolean('enable_selling')->nullable()->default(false);
            $table->json('custom_sub_categories')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('maxSaleLimit');
            // $table->dropColumn('barcode_no');
            if (Schema::hasColumn('products', 'barcode_no')) {
                $table->dropColumn('barcode_no');
            }
            $table->dropColumn('enable_selling');
            $table->dropColumn('custom_sub_categories');
        });
    }
};
