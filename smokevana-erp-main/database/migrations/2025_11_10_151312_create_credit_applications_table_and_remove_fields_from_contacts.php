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
        // Create credit_applications table
        Schema::create('credit_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->decimal('requested_credit_amount', 15, 2)->nullable();
            $table->decimal('average_revenue_per_month', 15, 2)->nullable();
            $table->json('supporting_documents_paths')->nullable();
            $table->string('authorized_signatory_name')->nullable();
            $table->string('authorized_signatory_email')->nullable();
            $table->string('authorized_signatory_phone')->nullable();
            $table->json('digital_signatures_paths')->nullable();
            $table->string('credit_application_status')->nullable()->default('pending');
            $table->timestamps();
        });

        // Remove credit application fields from contacts table
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'requested_credit_amount',
                'average_revenue_per_month',
                'supporting_documents_paths',
                'authorized_signatory_name',
                'authorized_signatory_email',
                'authorized_signatory_phone',
                'digital_signatures_paths',
                'credit_application_status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Re-add credit application fields to contacts table
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('authorized_signatory_name')->nullable();
            $table->string('authorized_signatory_email')->nullable();
            $table->string('authorized_signatory_phone')->nullable();
            $table->decimal('requested_credit_amount', 15, 2)->nullable();
            $table->decimal('average_revenue_per_month', 15, 2)->nullable();
            $table->json('digital_signatures_paths')->nullable();
            $table->json('supporting_documents_paths')->nullable();
            $table->string('credit_application_status')->nullable();
        });

        // Drop credit_applications table
        Schema::dropIfExists('credit_applications');
    }
};
