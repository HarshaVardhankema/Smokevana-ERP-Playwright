<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds proper address fields for both Permanent and Current addresses
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Permanent Address fields (if not exists, permanent_address already exists as text)
            if (!Schema::hasColumn('users', 'permanent_city')) {
                $table->string('permanent_city', 100)->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('users', 'permanent_state')) {
                $table->string('permanent_state', 50)->nullable()->after('permanent_city');
            }
            if (!Schema::hasColumn('users', 'permanent_zip')) {
                $table->string('permanent_zip', 20)->nullable()->after('permanent_state');
            }
            if (!Schema::hasColumn('users', 'permanent_country')) {
                $table->string('permanent_country', 50)->nullable()->default('US')->after('permanent_zip');
            }

            // Current Address fields (if not exists, current_address already exists as text)
            if (!Schema::hasColumn('users', 'current_city')) {
                $table->string('current_city', 100)->nullable()->after('current_address');
            }
            if (!Schema::hasColumn('users', 'current_state')) {
                $table->string('current_state', 50)->nullable()->after('current_city');
            }
            if (!Schema::hasColumn('users', 'current_zip')) {
                $table->string('current_zip', 20)->nullable()->after('current_state');
            }
            if (!Schema::hasColumn('users', 'current_country')) {
                $table->string('current_country', 50)->nullable()->default('US')->after('current_zip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop Permanent Address fields
            $columns = ['permanent_city', 'permanent_state', 'permanent_zip', 'permanent_country',
                        'current_city', 'current_state', 'current_zip', 'current_country'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
