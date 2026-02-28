<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialImageToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'initial_image')) {
                // Try to place it after issue_priority if it exists, otherwise after ticket_description
                if (Schema::hasColumn('tickets', 'issue_priority')) {
                    $table->string('initial_image', 255)->nullable()->after('issue_priority');
                } else if (Schema::hasColumn('tickets', 'issue_type')) {
                    $table->string('initial_image', 255)->nullable()->after('issue_type');
                } else {
                    $table->string('initial_image', 255)->nullable()->after('ticket_description');
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
            if (Schema::hasColumn('tickets', 'initial_image')) {
                $table->dropColumn('initial_image');
            }
        });
    }
}

