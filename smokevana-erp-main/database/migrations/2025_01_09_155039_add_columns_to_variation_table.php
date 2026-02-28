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
        Schema::table('variations', function (Blueprint $table) {
            $table->string('var_barcode_no')->nullable();
            $table->integer('var_maxSaleLimit')->nullable();
            $table->integer('var_img_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variations', function (Blueprint $table) {
            if (Schema::hasColumn('variations', 'var_barcode_no')) {
                $table->dropColumn('var_barcode_no');
            }
            if (Schema::hasColumn('variations', 'var_maxSaleLimit')) {
                $table->dropColumn('var_maxSaleLimit');
            }
            if (Schema::hasColumn('variations', 'var_img_url')) {
                $table->dropColumn('var_img_url');
            }
        });
    }
};
