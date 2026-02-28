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
        Schema::table('contacts', function (Blueprint $table) {
            // Credit Application Fields
            $table->string('authorized_signatory_name')->nullable();
            $table->string('authorized_signatory_email')->nullable();
            $table->string('authorized_signatory_phone')->nullable();
            $table->decimal('requested_credit_amount', 15, 2)->nullable();
            $table->decimal('average_revenue_per_month', 15, 2)->nullable();
            $table->json('digital_signatures_paths')->nullable(); // Multiple signatures
            $table->json('supporting_documents_paths')->nullable();
            $table->string('credit_application_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'authorized_signatory_name',
                'authorized_signatory_email',
                'authorized_signatory_phone',
                'requested_credit_amount',
                'average_revenue_per_month',
                'digital_signatures_paths',
                'supporting_documents_paths',
                'credit_application_status'

            ]);
        });
    }
};
