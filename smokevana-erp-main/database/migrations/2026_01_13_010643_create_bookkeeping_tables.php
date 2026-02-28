<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates comprehensive bookkeeping tables following US GAAP / IFRS standards
     * with double-entry accounting support
     */
    public function up()
    {
        // Chart of Accounts - Core account structure following GAAP/IFRS
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('account_code', 20)->nullable(); // e.g., 1000, 1100, etc.
            $table->string('name');
            $table->string('full_name')->nullable(); // Parent:Child format
            $table->enum('account_type', [
                'asset',           // Assets (Debit balance)
                'liability',       // Liabilities (Credit balance)
                'equity',          // Equity/Owner's Equity (Credit balance)
                'income',          // Revenue/Income (Credit balance)
                'expense',         // Expenses (Debit balance)
                'cost_of_goods_sold' // COGS (Debit balance)
            ]);
            $table->string('detail_type')->nullable(); // Sub-classification (Cash on hand, Accounts Receivable, etc.)
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 22, 4)->default(0);
            $table->date('opening_balance_date')->nullable();
            $table->decimal('current_balance', 22, 4)->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_system_account')->default(false); // Prevent deletion of system accounts
            $table->boolean('is_sub_account')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('track_depreciation')->default(false);
            $table->decimal('depreciation_rate', 8, 4)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->unsignedInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['business_id', 'account_type']);
            $table->index(['business_id', 'account_code']);
            $table->unique(['business_id', 'account_code'], 'unique_account_code');
        });

        // Journal Entries - Core double-entry transactions
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('entry_number', 50); // Auto-generated reference
            $table->date('entry_date');
            $table->enum('entry_type', [
                'standard',        // Regular journal entry
                'adjusting',       // Period-end adjustments
                'closing',         // Closing entries
                'reversing',       // Reversing entries
                'opening',         // Opening balance entries
                'bank_deposit',    // Bank deposit transactions
                'expense',         // Expense transactions
                'transfer',        // Fund transfers
                'payroll',         // Payroll entries
                'depreciation',    // Depreciation entries
                'inventory_adjustment', // Inventory adjustments
                'loan_payment',    // Loan payments
                'advance',         // Advances to partners/employees
            ]);
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'posted',
                'voided'
            ])->default('draft');
            $table->text('memo')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_debit', 22, 4)->default(0);
            $table->decimal('total_credit', 22, 4)->default(0);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedInteger('contact_id')->nullable(); // Customer/Vendor
            $table->unsignedBigInteger('related_transaction_id')->nullable(); // Link to transactions table
            $table->string('related_transaction_type')->nullable();
            $table->unsignedBigInteger('reversed_entry_id')->nullable(); // For reversing entries
            $table->string('source_document')->nullable(); // Invoice #, Receipt #, etc.
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('reversed_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            
            $table->index(['business_id', 'entry_date']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'entry_type']);
            $table->unique(['business_id', 'entry_number']);
        });

        // Journal Entry Lines - Individual debit/credit lines
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('account_id');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 22, 4);
            $table->text('description')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable(); // For inventory-related entries
            $table->integer('quantity')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            
            $table->index(['journal_entry_id']);
            $table->index(['account_id']);
        });

        // Bank Deposits - Enhanced deposit tracking
        Schema::create('bank_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('deposit_to_account_id'); // Bank account
            $table->date('deposit_date');
            $table->string('deposit_number', 50)->nullable();
            $table->decimal('total_amount', 22, 4)->default(0);
            $table->text('memo')->nullable();
            $table->enum('status', ['pending', 'deposited', 'reconciled', 'voided'])->default('pending');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->json('attachments')->nullable();
            $table->unsignedInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('deposit_to_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'deposit_date']);
            $table->index(['business_id', 'status']);
        });

        // Bank Deposit Lines - Individual items in deposit
        Schema::create('bank_deposit_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_deposit_id');
            $table->unsignedInteger('contact_id')->nullable(); // Received from
            $table->unsignedBigInteger('account_id'); // Credit account
            $table->date('date');
            $table->enum('type', ['payment', 'invoice', 'other'])->default('other');
            $table->unsignedBigInteger('transaction_payment_id')->nullable(); // Link to existing payment
            $table->string('payment_method')->nullable();
            $table->text('memo')->nullable();
            $table->string('ref_no')->nullable();
            $table->decimal('amount', 22, 4);
            $table->timestamps();

            $table->foreign('bank_deposit_id')->references('id')->on('bank_deposits')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        // Liabilities - Partner loans, advances, credit cards, etc.
        Schema::create('business_liabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('liability_account_id');
            $table->string('name');
            $table->enum('liability_type', [
                'vendors_unpaid',      // Accounts Payable
                'owed_to_partner',     // Money owed to business partners
                'credit_card',         // Credit card debt
                'loan',                // Business loans
                'advance_received',    // Customer advances
                'employee_payable',    // Wages payable
                'tax_payable',         // Taxes owed
                'other'
            ]);
            $table->text('description')->nullable();
            $table->decimal('original_amount', 22, 4)->default(0);
            $table->decimal('current_balance', 22, 4)->default(0);
            $table->decimal('interest_rate', 8, 4)->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('contact_id')->nullable(); // Partner/Vendor
            $table->enum('payment_frequency', ['one_time', 'weekly', 'bi_weekly', 'monthly', 'quarterly', 'annually'])->nullable();
            $table->decimal('payment_amount', 22, 4)->nullable();
            $table->enum('status', ['active', 'paid_off', 'defaulted', 'restructured'])->default('active');
            $table->json('terms')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('liability_account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'liability_type']);
            $table->index(['business_id', 'status']);
        });

        // Liability Payments - Track payments against liabilities
        Schema::create('liability_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('liability_id');
            $table->date('payment_date');
            $table->decimal('principal_amount', 22, 4)->default(0);
            $table->decimal('interest_amount', 22, 4)->default(0);
            $table->decimal('total_amount', 22, 4);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('liability_id')->references('id')->on('business_liabilities')->onDelete('cascade');
            $table->foreign('from_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Partner Assets & Advances - Track partner-related transactions
        Schema::create('partner_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('partner_id'); // User who is the partner
            $table->unsignedBigInteger('account_id'); // Related COA account
            $table->enum('transaction_type', [
                'contribution',    // Capital contribution
                'distribution',    // Profit distribution
                'loan_to_company', // Partner lends to company
                'loan_from_company', // Company lends to partner
                'advance',         // Advance payment
                'reimbursement',   // Expense reimbursement
                'personal_asset',  // Personal asset used for business
                'withdrawal'       // Capital withdrawal
            ]);
            $table->date('transaction_date');
            $table->decimal('amount', 22, 4);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed', 'voided'])->default('pending');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('approved_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['business_id', 'partner_id']);
            $table->index(['business_id', 'transaction_type']);
        });

        // Inventory Valuation History - Track inventory value changes
        Schema::create('inventory_valuations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->date('valuation_date');
            $table->enum('valuation_method', ['fifo', 'lifo', 'weighted_average', 'specific_identification'])->default('weighted_average');
            $table->decimal('total_cost_value', 22, 4)->default(0);
            $table->decimal('total_retail_value', 22, 4)->default(0);
            $table->decimal('total_units', 22, 4)->default(0);
            $table->unsignedBigInteger('inventory_asset_account_id')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->json('breakdown')->nullable(); // Detailed category breakdown
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('inventory_asset_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'valuation_date']);
        });

        // Financial Period Closings
        Schema::create('financial_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('period_name'); // e.g., "January 2026", "Q1 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('period_type', ['month', 'quarter', 'year'])->default('month');
            $table->enum('status', ['open', 'closing', 'closed', 'locked'])->default('open');
            $table->decimal('opening_retained_earnings', 22, 4)->default(0);
            $table->decimal('net_income', 22, 4)->default(0);
            $table->decimal('closing_retained_earnings', 22, 4)->default(0);
            $table->unsignedBigInteger('closing_journal_entry_id')->nullable();
            $table->unsignedInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->json('closing_balances')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('closing_journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['business_id', 'status']);
            $table->unique(['business_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('financial_periods');
        Schema::dropIfExists('inventory_valuations');
        Schema::dropIfExists('partner_transactions');
        Schema::dropIfExists('liability_payments');
        Schema::dropIfExists('business_liabilities');
        Schema::dropIfExists('bank_deposit_lines');
        Schema::dropIfExists('bank_deposits');
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};



