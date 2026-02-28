<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates debit_notes and debit_note_lines tables for vendor debit note management
     * following US GAAP/IFRS standards for accounts payable adjustments.
     */
    public function up(): void
    {
        // Main Debit Notes table
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id'); // Match business.id type (int unsigned)
            
            // Debit note identification
            $table->string('debit_note_number', 50)->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            
            // Vendor/Supplier relationship
            $table->unsignedInteger('contact_id'); // Vendor/Supplier
            
            // Reference to original purchase/invoice
            $table->unsignedInteger('reference_transaction_id')->nullable();
            $table->string('reference_number', 100)->nullable(); // External reference if any
            
            // Status workflow: draft -> pending_approval -> approved -> sent -> closed
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'sent',
                'partially_settled',
                'closed',
                'cancelled',
                'voided'
            ])->default('draft');
            
            // Reason for debit note
            $table->enum('reason_category', [
                'overcharge',
                'damaged_goods',
                'short_delivery',
                'quality_issue',
                'pricing_discrepancy',
                'return_goods',
                'billing_error',
                'other'
            ]);
            $table->text('reason_description')->nullable();
            
            // Amounts
            $table->decimal('subtotal', 22, 4)->default(0);
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->decimal('total_amount', 22, 4)->default(0);
            $table->decimal('amount_settled', 22, 4)->default(0);
            $table->decimal('balance', 22, 4)->default(0);
            
            // Multi-currency support
            $table->string('currency_code', 10)->default('MYR');
            $table->decimal('exchange_rate', 20, 10)->default(1);
            
            // Tax configuration
            $table->unsignedInteger('tax_id')->nullable();
            $table->enum('tax_type', ['exclusive', 'inclusive'])->default('exclusive');
            
            // Notes and attachments
            $table->text('internal_notes')->nullable();
            $table->text('vendor_notes')->nullable(); // Notes visible to vendor
            $table->string('document')->nullable(); // Attachment path
            
            // Approval workflow
            $table->unsignedInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Sent to vendor tracking
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_via', 50)->nullable(); // email, manual, portal
            $table->string('sent_to_email')->nullable();
            
            // Accounting integration
            $table->unsignedInteger('journal_entry_id')->nullable();
            
            // Audit trail
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('business_id');
            $table->index('contact_id');
            $table->index('status');
            $table->index('issue_date');
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'contact_id']);
            $table->index('reference_transaction_id');
            
            // Foreign keys
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('reference_transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Debit Note Line Items table
        Schema::create('debit_note_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debit_note_id'); // References debit_notes.id (bigint)
            
            // Product/Service reference (optional - can be text description)
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('variation_id')->nullable();
            
            // Line item details
            $table->text('description');
            $table->string('sku', 100)->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            
            // Quantities and amounts
            $table->decimal('quantity', 22, 4)->default(1);
            $table->decimal('unit_price', 22, 4)->default(0);
            $table->decimal('discount_type', 22, 4)->nullable(); // 'percentage' or 'fixed'
            $table->decimal('discount_amount', 22, 4)->default(0);
            $table->decimal('tax_rate', 10, 4)->default(0);
            $table->decimal('tax_amount', 22, 4)->default(0);
            $table->decimal('line_total', 22, 4)->default(0);
            
            // Reference to original purchase line if applicable
            $table->unsignedInteger('purchase_line_id')->nullable();
            
            // Reason specific to this line item
            $table->text('line_reason')->nullable();
            
            // Sort order for display
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('debit_note_id');
            $table->index('product_id');
            
            // Foreign keys
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('purchase_line_id')->references('id')->on('purchase_lines')->onDelete('set null');
        });

        // Debit Note Activity Log for audit trail
        Schema::create('debit_note_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debit_note_id'); // References debit_notes.id (bigint)
            $table->unsignedInteger('user_id');
            
            $table->enum('action', [
                'created',
                'updated',
                'submitted',
                'approved',
                'rejected',
                'sent',
                'settled',
                'closed',
                'cancelled',
                'voided',
                'reopened',
                'line_added',
                'line_updated',
                'line_deleted',
                'comment_added',
                'document_attached'
            ]);
            
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('debit_note_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            
            // Foreign keys
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Debit Note Settlements - track how debit notes are applied/settled
        Schema::create('debit_note_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debit_note_id'); // References debit_notes.id (bigint)
            $table->unsignedInteger('transaction_id')->nullable(); // Purchase invoice being offset
            $table->unsignedInteger('payment_id')->nullable(); // If settled via payment
            
            $table->decimal('amount_applied', 22, 4);
            $table->date('settlement_date');
            $table->enum('settlement_type', [
                'offset_invoice',      // Offset against a purchase invoice
                'cash_refund',         // Cash/bank refund from vendor
                'credit_balance',      // Applied to vendor credit balance
                'write_off',           // Written off
                'other'
            ]);
            
            $table->text('notes')->nullable();
            $table->unsignedInteger('applied_by');
            
            $table->timestamps();
            
            // Indexes
            $table->index('debit_note_id');
            $table->index('transaction_id');
            $table->index('settlement_date');
            
            // Foreign keys
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('payment_id')->references('id')->on('transaction_payments')->onDelete('set null');
            $table->foreign('applied_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_settlements');
        Schema::dropIfExists('debit_note_activities');
        Schema::dropIfExists('debit_note_lines');
        Schema::dropIfExists('debit_notes');
    }
};
