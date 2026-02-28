<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add status column for approval workflow: Draft, Pending Approval, Active, Rejected, Expired, Suspended
     *
     * @return void
     */
    public function up()
    {
        Schema::table('preferred_brands', function (Blueprint $table) {
            $table->enum('status', [
                'Draft',
                'Pending Approval',
                'Active',
                'Rejected',
                'Expired',
                'Suspended'
            ])->default('Draft')->after('sort_order');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('preferred_brands', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
