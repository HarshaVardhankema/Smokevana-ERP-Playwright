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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('reference_no')->nullable()->after('business_id');
            $table->string('visited_by')->nullable();
            $table->enum('status', ['pending', 'visited'])->default('pending')->after('reference_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['reference_no', 'status', 'visited_by']);
        });
    }
};
