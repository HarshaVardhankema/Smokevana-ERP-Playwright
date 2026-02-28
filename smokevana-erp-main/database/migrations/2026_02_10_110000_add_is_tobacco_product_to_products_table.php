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
        if (!Schema::hasColumn('products', 'is_tobacco_product')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_tobacco_product')
                    ->default(false)
                    ->after('enable_selling');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_tobacco_product')) {
                $table->dropColumn('is_tobacco_product');
            }
        });
    }
};

