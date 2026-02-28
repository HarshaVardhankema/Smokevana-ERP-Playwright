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
        Schema::table('tickets', function (Blueprint $table) {
            // Add issue_type if it doesn't exist
            if (!Schema::hasColumn('tickets', 'issue_type')) {
                $table->string('issue_type')->nullable()->after('ticket_description');
            }
            
            // Add closed_by if it doesn't exist (closed_at already exists)
            if (!Schema::hasColumn('tickets', 'closed_by')) {
                $table->unsignedInteger('closed_by')->nullable()->after('status');
                $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'closed_by')) {
                $table->dropForeign(['closed_by']);
                $table->dropColumn('closed_by');
            }
            
            if (Schema::hasColumn('tickets', 'issue_type')) {
                $table->dropColumn('issue_type');
            }
        });
    }
};
