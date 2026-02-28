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
        Schema::create('business_identifications', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Business Identification fields
            $table->string('legal_business_name');
            $table->string('dba')->nullable();
            $table->string('fein_tax_id')->nullable();
            $table->json('business_types')->nullable(); // Array of: retail, distributor, manufacturer, delivery, ecommerce, other
            $table->string('business_type_other')->nullable();
            
            // Primary Contact Information
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_title')->nullable();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_email')->nullable();
            
            // Address Information
            $table->text('business_address')->nullable();
            $table->text('ship_from_address')->nullable();
            $table->text('ship_to_address')->nullable();
            $table->text('website_marketplaces')->nullable();
            
            // License and Permit Information
            $table->string('resale_certificate_number')->nullable();
            $table->string('resale_certificate_state')->nullable();
            $table->json('state_licenses')->nullable(); // Array of {type, number, expiry}
            
            // Age-Gating Information
            $table->json('age_gating_methods')->nullable(); // Array of: pos_id_scan, third_party, adult_signature, website_gate, other
            $table->string('age_gating_other')->nullable();
            
            // Acknowledgments
            $table->boolean('prohibited_jurisdictions_acknowledged')->default(false);
            
            // Attachments
            $table->json('attachments')->nullable(); // Array of file paths for uploaded documents
            
            // Status and Notes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for foreign keys (no constraints to avoid issues)
            $table->index('contact_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_identifications');
    }
};
