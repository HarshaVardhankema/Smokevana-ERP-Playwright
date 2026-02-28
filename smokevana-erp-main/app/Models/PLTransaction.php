<?php

namespace App\Models;

use App\Business;
use App\Contact;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PLTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'pl_transactions';

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    // Transaction type constants
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_POSTED = 'posted';
    const STATUS_VOIDED = 'voided';

    // Income categories
    const INCOME_CATEGORIES = [
        'sales_revenue' => 'Sales Revenue',
        'service_revenue' => 'Service Revenue',
        'consulting_income' => 'Consulting Income',
        'interest_income' => 'Interest Income',
        'rental_income' => 'Rental Income',
        'commission_income' => 'Commission Income',
        'other_income' => 'Other Income',
    ];

    // Expense categories
    const EXPENSE_CATEGORIES = [
        'advertising' => 'Advertising & Marketing',
        'bank_charges' => 'Bank Charges & Fees',
        'contract_labor' => 'Contract Labor',
        'insurance' => 'Insurance',
        'interest_expense' => 'Interest Expense',
        'office_supplies' => 'Office Supplies',
        'professional_fees' => 'Professional Fees',
        'rent' => 'Rent Expense',
        'repairs_maintenance' => 'Repairs & Maintenance',
        'salaries_wages' => 'Salaries & Wages',
        'shipping_delivery' => 'Shipping & Delivery',
        'telephone_internet' => 'Telephone & Internet',
        'travel_expense' => 'Travel Expense',
        'utilities' => 'Utilities',
        'other_expense' => 'Other Expense',
    ];

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function paymentAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeIncome($query)
    {
        return $query->where('transaction_type', self::TYPE_INCOME);
    }

    public function scopeExpenses($query)
    {
        return $query->where('transaction_type', self::TYPE_EXPENSE);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Generate transaction reference number
     * Uses withTrashed() to include soft-deleted records in the search
     */
    public static function generateReferenceNumber($businessId, $type)
    {
        $prefix = $type === self::TYPE_INCOME ? 'INC' : 'EXP';
        $year = date('Y');
        $prefixYear = $prefix . $year;
        $prefixLength = strlen($prefixYear);
        
        // Include soft-deleted records to avoid duplicate reference numbers
        $maxNumber = self::withTrashed()
            ->where('business_id', $businessId)
            ->where('reference_number', 'like', "{$prefixYear}%")
            ->selectRaw("MAX(CAST(SUBSTRING(reference_number, " . ($prefixLength + 1) . ") AS UNSIGNED)) as max_num")
            ->value('max_num');
        
        $newNumber = ($maxNumber ?? 0) + 1;
        
        // Generate the reference number
        $referenceNumber = $prefixYear . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        
        // Safety check with retry logic
        $maxRetries = 10;
        $retry = 0;
        while (self::withTrashed()->where('business_id', $businessId)->where('reference_number', $referenceNumber)->exists() && $retry < $maxRetries) {
            $newNumber++;
            $referenceNumber = $prefixYear . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            $retry++;
        }
        
        return $referenceNumber;
    }

    /**
     * Get transaction types
     */
    public static function getTransactionTypes()
    {
        return [
            self::TYPE_INCOME => 'Income',
            self::TYPE_EXPENSE => 'Expense',
        ];
    }

    /**
     * Get income categories
     */
    public static function getIncomeCategories()
    {
        return self::INCOME_CATEGORIES;
    }

    /**
     * Get expense categories
     */
    public static function getExpenseCategories()
    {
        return self::EXPENSE_CATEGORIES;
    }

    /**
     * Get statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Posted',
            self::STATUS_VOIDED => 'Voided',
        ];
    }

    /**
     * Create journal entry for this transaction
     */
    public function createJournalEntry($userId)
    {
        if ($this->journal_entry_id) {
            throw new \Exception('Journal entry already exists for this transaction.');
        }

        $entryType = $this->transaction_type === self::TYPE_INCOME 
            ? JournalEntry::TYPE_STANDARD 
            : JournalEntry::TYPE_EXPENSE;

        // Create the journal entry
        $entry = JournalEntry::create([
            'business_id' => $this->business_id,
            'entry_number' => JournalEntry::generateEntryNumber($this->business_id),
            'entry_date' => $this->transaction_date,
            'entry_type' => $entryType,
            'status' => JournalEntry::STATUS_POSTED,
            'memo' => $this->description ?? ($this->transaction_type === self::TYPE_INCOME ? 'Income Entry' : 'Expense Entry'),
            'contact_id' => $this->contact_id,
            'source_document' => $this->reference_number,
            'total_debit' => $this->amount,
            'total_credit' => $this->amount,
            'created_by' => $userId,
            'posted_by' => $userId,
            'posted_at' => now(),
        ]);

        // Create journal entry lines based on transaction type
        if ($this->transaction_type === self::TYPE_INCOME) {
            // Income: Debit Cash/Bank, Credit Income Account
            
            // Debit - Payment Account (Cash/Bank)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->payment_account_id,
                'type' => 'debit',
                'amount' => $this->amount,
                'description' => $this->description,
                'contact_id' => $this->contact_id,
                'sort_order' => 0,
            ]);

            // Credit - Income Account
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->account_id,
                'type' => 'credit',
                'amount' => $this->amount,
                'description' => $this->description,
                'contact_id' => $this->contact_id,
                'sort_order' => 1,
            ]);
        } else {
            // Expense: Debit Expense Account, Credit Cash/Bank
            
            // Debit - Expense Account
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->account_id,
                'type' => 'debit',
                'amount' => $this->amount,
                'description' => $this->description,
                'contact_id' => $this->contact_id,
                'sort_order' => 0,
            ]);

            // Credit - Payment Account (Cash/Bank)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->payment_account_id,
                'type' => 'credit',
                'amount' => $this->amount,
                'description' => $this->description,
                'contact_id' => $this->contact_id,
                'sort_order' => 1,
            ]);
        }

        // Update account balances
        $entry->updateAccountBalances();

        // Link journal entry to this transaction
        $this->journal_entry_id = $entry->id;
        $this->status = self::STATUS_POSTED;
        $this->save();

        return $entry;
    }

    /**
     * Void this transaction
     */
    public function voidTransaction($userId)
    {
        if ($this->status === self::STATUS_VOIDED) {
            throw new \Exception('Transaction is already voided.');
        }

        // Void the associated journal entry if exists
        if ($this->journalEntry) {
            $this->journalEntry->void($userId);
        }

        $this->status = self::STATUS_VOIDED;
        $this->save();

        return $this;
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        if ($this->transaction_type === self::TYPE_INCOME) {
            return self::INCOME_CATEGORIES[$this->category] ?? $this->category;
        }
        return self::EXPENSE_CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            self::STATUS_DRAFT => 'bg-gray',
            self::STATUS_POSTED => 'bg-green',
            self::STATUS_VOIDED => 'bg-red',
        ];

        return $classes[$this->status] ?? 'bg-gray';
    }
}



