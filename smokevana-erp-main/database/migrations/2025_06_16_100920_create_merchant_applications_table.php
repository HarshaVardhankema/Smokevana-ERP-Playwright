<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('merchant_applications', function (Blueprint $table) {
            $table->id();
            
            // Business Info
            $table->string('legal_business_name');
            $table->string('legal_address');
            $table->string('legal_city');
            $table->string('legal_state');
            $table->string('legal_zip');
            $table->string('dba_name')->nullable();
            $table->string('dba_address')->nullable();
            $table->string('dba_city')->nullable();
            $table->string('dba_state')->nullable();
            $table->string('dba_zip')->nullable();
            $table->string('business_type');
            $table->string('federal_tax_id');
            $table->string('business_age');
            $table->string('business_phone');
            $table->string('website')->nullable();
            $table->string('gateway_option')->nullable();
            // Ownership Info
            $table->string('owner_legal_name');
            $table->decimal('ownership_percentage', 5, 2);
            $table->string('job_title');
            $table->date('date_of_birth');
            $table->string('owner_address');
            $table->string('owner_city');
            $table->string('owner_state');
            $table->string('owner_zip');
            $table->string('owner_email');
            $table->string('owner_phone');
            $table->string('owner_ssn');
            
            // Previous Processing
            $table->boolean('has_previous_processing')->default(false);
            $table->string('processing_duration')->nullable();
            $table->string('previous_processor')->nullable();
            $table->decimal('average_ticket_amount', 10, 2)->nullable();
            $table->decimal('monthly_volume', 12, 2)->nullable();
            
            // Documents
            $table->string('voided_check_path');
            $table->string('driver_license_path');
            $table->string('processing_statements_path')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('admin_response')->nullable();
            
            // Additional fields
            $table->json('additional_owners')->nullable();
            $table->json('additional_documents')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_applications');
    }
}; 