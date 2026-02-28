<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('credit_note_number', 50)->unique();
            $table->unsignedInteger('contact_id'); // Customer
            $table->date('credit_date');
            $table->decimal('amount', 22, 4);
            $table->decimal('amount_applied', 22, 4)->default(0); // Amount already applied to invoices
            $table->decimal('balance', 22, 4); // Remaining balance
            
            // Transaction details - what this credit is for
            $table->string('reason_category')->nullable(); // e.g., 'return', 'discount', 'error_correction', 'price_adjustment', 'goodwill', 'other'
            $table->text('reason_description'); // Detailed explanation for the credit
            $table->string('reference_type')->nullable(); // e.g., 'invoice', 'return', 'general'
            $table->string('reference_number')->nullable(); // Original invoice number or return reference
            $table->unsignedInteger('reference_transaction_id')->nullable(); // Link to original transaction
            
            // Status tracking
            $table->enum('status', ['draft', 'approved', 'applied', 'partially_applied', 'cancelled', 'voided'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            
            // Journal entry integration
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            
            // Additional notes and attachments
            $table->text('internal_notes')->nullable();
            $table->string('attachment')->nullable(); // Document attachment path
            
            // Audit fields
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            
            // Indexes
            $table->index(['business_id', 'contact_id']);
            $table->index(['business_id', 'status']);
            $table->index('credit_date');
        });

        // Credit note applications - tracks which invoices credit notes are applied to
        Schema::create('credit_note_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_note_id');
            $table->unsignedInteger('transaction_id'); // Invoice being credited (matches transactions.id type)
            $table->decimal('amount_applied', 22, 4);
            $table->date('application_date');
            $table->text('notes')->nullable();
            $table->unsignedInteger('applied_by');
            $table->timestamps();

            $table->foreign('credit_note_id')->references('id')->on('credit_notes')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('applied_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['credit_note_id', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_applications');
        Schema::dropIfExists('credit_notes');
    }
};
