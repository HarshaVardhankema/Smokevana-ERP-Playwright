<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIssuePriorityToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Check if the column doesn't already exist
            if (!Schema::hasColumn('tickets', 'issue_priority')) {
                // Check if issue_type exists to place the column after it
                if (Schema::hasColumn('tickets', 'issue_type')) {
                    $table->enum('issue_priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('issue_type');
                } else {
                    // Add it after ticket_description if issue_type doesn't exist yet
                    $table->enum('issue_priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('ticket_description');
                }
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
            if (Schema::hasColumn('tickets', 'issue_priority')) {
                $table->dropColumn('issue_priority');
            }
        });
    }
}

