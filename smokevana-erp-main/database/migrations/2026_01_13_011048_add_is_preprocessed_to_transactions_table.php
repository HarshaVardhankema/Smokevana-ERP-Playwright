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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'is_preprocessed')) {
                $table->boolean('is_preprocessed')
                    ->default(false)
                    ->after('is_direct_sale')
                    ->comment('Marks orders staged in preprocessing before pending');
                $table->index('is_preprocessed');
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
        Schema::table('transactions', function (Blueprint $table) {
              if (Schema::hasColumn('transactions', 'is_preprocessed')) {
                $table->dropIndex(['is_preprocessed']);
                $table->dropColumn('is_preprocessed');
            }

        });
    }
};
