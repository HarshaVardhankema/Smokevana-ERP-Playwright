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
        Schema::table('credit_applications', function (Blueprint $table) {
            // Primary owner fields
            $table->string('owner_name')->nullable()->after('authorized_signatory_phone');
            $table->string('owner_email')->nullable()->after('owner_name');
            $table->date('owner_date_of_birth')->nullable()->after('owner_email');
            $table->string('owner_ssn')->nullable()->after('owner_date_of_birth');
            $table->string('owner_title')->nullable()->after('owner_ssn');
            $table->string('owner_address')->nullable()->after('owner_title');
            $table->string('owner_city_state_zip')->nullable()->after('owner_address');
            $table->string('owner_phone')->nullable()->after('owner_city_state_zip');
            $table->decimal('owner_ownership_percentage', 5, 2)->nullable()->after('owner_phone');
            $table->string('owner_dl_number')->nullable()->after('owner_ownership_percentage');
            $table->string('owner_dl_state')->nullable()->after('owner_dl_number');
            
            // Additional owners (for multiple owners)
            $table->json('additional_owners')->nullable()->after('owner_dl_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_applications', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name',
                'owner_email',
                'owner_date_of_birth',
                'owner_ssn',
                'owner_title',
                'owner_address',
                'owner_city_state_zip',
                'owner_phone',
                'owner_ownership_percentage',
                'owner_dl_number',
                'owner_dl_state',
                'additional_owners'
            ]);
        });
    }
};
