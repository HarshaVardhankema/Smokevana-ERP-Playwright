<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Advanced Journal Entry Features:
     * - Templates
     * - Recurring Entries
     * - Multi-Currency Support
     * - Inter-Company Entries
     */
    public function up()
    {
        // Journal Entry Templates - Reusable journal entry structures
        Schema::create('journal_entry_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('entry_type', [
                'standard',
                'adjusting',
                'payroll',
                'depreciation',
                'transfer',
                'expense',
                'custom'
            ])->default('standard');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System templates cannot be deleted
            $table->integer('usage_count')->default(0); // Track how often template is used
            $table->json('default_accounts')->nullable(); // Store default account mappings
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'is_active']);
        });

        // Template Lines - Individual debit/credit lines for templates
        Schema::create('journal_entry_template_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('account_id')->nullable(); // Can be null for placeholder
            $table->string('account_placeholder')->nullable(); // e.g., "debit_account", "credit_account"
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 22, 4)->nullable(); // Fixed amount or null for variable
            $table->string('amount_placeholder')->nullable(); // e.g., "total_amount", "tax_amount"
            $table->decimal('percentage', 8, 4)->nullable(); // Percentage of total
            $table->text('description')->nullable();
            $table->string('description_placeholder')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('journal_entry_templates')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            
            $table->index(['template_id']);
        });

        // Recurring Journal Entries - Automated repetitive entries
        Schema::create('journal_entry_recurrences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('template_id')->nullable(); // Based on template
            $table->unsignedBigInteger('source_entry_id')->nullable(); // Or based on existing entry
            $table->enum('frequency', [
                'daily',
                'weekly',
                'bi_weekly',
                'monthly',
                'quarterly',
                'semi_annually',
                'annually'
            ]);
            $table->integer('interval')->default(1); // Every X frequency
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null for indefinite
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->integer('occurrences_limit')->nullable(); // Max number of occurrences
            $table->integer('occurrences_count')->default(0);
            $table->decimal('amount', 22, 4)->nullable(); // Fixed amount per occurrence
            $table->boolean('amount_variable')->default(false); // Can vary each time
            $table->boolean('auto_post')->default(false); // Auto-post or create as draft
            $table->boolean('notify_on_creation')->default(true);
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->json('entry_data')->nullable(); // Store complete entry structure
            $table->json('metadata')->nullable();
            $table->unsignedInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('journal_entry_templates')->onDelete('set null');
            $table->foreign('source_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'status']);
            $table->index(['next_run_date', 'status']);
        });

        // Recurrence Generated Entries - Track entries created by recurrences
        Schema::create('journal_entry_recurrence_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recurrence_id');
            $table->unsignedBigInteger('journal_entry_id');
            $table->date('scheduled_date');
            $table->date('actual_run_date');
            $table->enum('status', ['generated', 'posted', 'skipped', 'failed'])->default('generated');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('recurrence_id')->references('id')->on('journal_entry_recurrences')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            
            $table->index(['recurrence_id', 'scheduled_date']);
        });

        // Add multi-currency and inter-company fields to journal_entries
        Schema::table('journal_entries', function (Blueprint $table) {
            // Multi-Currency Support
            if (!Schema::hasColumn('journal_entries', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('total_credit');
            }
            if (!Schema::hasColumn('journal_entries', 'exchange_rate')) {
                $table->decimal('exchange_rate', 16, 8)->default(1)->after('currency');
            }
            if (!Schema::hasColumn('journal_entries', 'base_currency_total')) {
                $table->decimal('base_currency_total', 22, 4)->nullable()->after('exchange_rate');
            }
            
            // Inter-Company Support
            if (!Schema::hasColumn('journal_entries', 'source_business_id')) {
                $table->unsignedInteger('source_business_id')->nullable()->after('base_currency_total');
            }
            if (!Schema::hasColumn('journal_entries', 'intercompany_entry_id')) {
                $table->unsignedBigInteger('intercompany_entry_id')->nullable()->after('source_business_id');
            }
            if (!Schema::hasColumn('journal_entries', 'is_intercompany')) {
                $table->boolean('is_intercompany')->default(false)->after('intercompany_entry_id');
            }
            
            // Template & Recurrence Links
            if (!Schema::hasColumn('journal_entries', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('is_intercompany');
            }
            if (!Schema::hasColumn('journal_entries', 'recurrence_id')) {
                $table->unsignedBigInteger('recurrence_id')->nullable()->after('template_id');
            }
            if (!Schema::hasColumn('journal_entries', 'duplicated_from_id')) {
                $table->unsignedBigInteger('duplicated_from_id')->nullable()->after('recurrence_id');
            }
        });
        
        // Add indexes separately to avoid duplicate key errors
        try {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->index(['business_id', 'currency'], 'je_business_currency_idx');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }
        
        try {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->index(['is_intercompany'], 'je_intercompany_idx');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        // Add currency fields to journal_entry_lines
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entry_lines', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('journal_entry_lines', 'exchange_rate')) {
                $table->decimal('exchange_rate', 16, 8)->default(1)->after('currency');
            }
            if (!Schema::hasColumn('journal_entry_lines', 'base_currency_amount')) {
                $table->decimal('base_currency_amount', 22, 4)->nullable()->after('exchange_rate');
            }
        });

        // Currency Exchange Rates - Store historical rates
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 16, 8);
            $table->date('effective_date');
            $table->string('source')->nullable(); // API, manual, etc.
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->unique(['business_id', 'from_currency', 'to_currency', 'effective_date'], 'currency_rate_unique');
            $table->index(['business_id', 'effective_date']);
        });

        // Inter-Company Relationships
        Schema::create('intercompany_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_business_id');
            $table->unsignedInteger('child_business_id');
            $table->string('relationship_type')->default('subsidiary'); // subsidiary, affiliate, branch
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('receivable_account_id')->nullable(); // Inter-company receivable
            $table->unsignedBigInteger('payable_account_id')->nullable(); // Inter-company payable
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('parent_business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('child_business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('receivable_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('payable_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            
            $table->unique(['parent_business_id', 'child_business_id'], 'intercompany_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('intercompany_relationships');
        Schema::dropIfExists('currency_exchange_rates');
        
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'base_currency_amount']);
        });
        
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'currency']);
            $table->dropIndex(['is_intercompany']);
            $table->dropColumn([
                'currency', 'exchange_rate', 'base_currency_total',
                'source_business_id', 'intercompany_entry_id', 'is_intercompany',
                'template_id', 'recurrence_id', 'duplicated_from_id'
            ]);
        });
        
        Schema::dropIfExists('journal_entry_recurrence_logs');
        Schema::dropIfExists('journal_entry_recurrences');
        Schema::dropIfExists('journal_entry_template_lines');
        Schema::dropIfExists('journal_entry_templates');
    }
};

