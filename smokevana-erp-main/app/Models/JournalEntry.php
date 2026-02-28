<?php

namespace App\Models;

use App\Business;
use App\Contact;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $table = 'journal_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'entry_date' => 'date',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'attachments' => 'array',
        'metadata' => 'array',
    ];

    // Entry type constants
    const TYPE_STANDARD = 'standard';
    const TYPE_ADJUSTING = 'adjusting';
    const TYPE_CLOSING = 'closing';
    const TYPE_REVERSING = 'reversing';
    const TYPE_OPENING = 'opening';
    const TYPE_BANK_DEPOSIT = 'bank_deposit';
    const TYPE_EXPENSE = 'expense';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_PAYROLL = 'payroll';
    const TYPE_DEPRECIATION = 'depreciation';
    const TYPE_INVENTORY_ADJUSTMENT = 'inventory_adjustment';
    const TYPE_LOAN_PAYMENT = 'loan_payment';
    const TYPE_ADVANCE = 'advance';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_POSTED = 'posted';
    const STATUS_VOIDED = 'voided';

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id')->orderBy('sort_order');
    }

    public function debitLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id')
                    ->where('type', 'debit');
    }

    public function creditLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id')
                    ->where('type', 'credit');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    public function reversingEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reversed_entry_id');
    }

    /**
     * Scopes
     */
    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('entry_type', $type);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Generate next entry number
     */
    public static function generateEntryNumber($businessId)
    {
        $prefix = 'JE';
        $year = date('Y');
        
        $lastEntry = self::where('business_id', $businessId)
                        ->where('entry_number', 'like', "{$prefix}{$year}%")
                        ->orderBy('entry_number', 'desc')
                        ->first();
        
        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if entry is balanced (debits = credits)
     */
    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    /**
     * Recalculate totals from lines
     */
    public function recalculateTotals()
    {
        $this->total_debit = $this->lines()->where('type', 'debit')->sum('amount');
        $this->total_credit = $this->lines()->where('type', 'credit')->sum('amount');
        return $this->save();
    }

    /**
     * Post the journal entry (update account balances)
     */
    public function post($userId)
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced. Debits must equal credits.');
        }

        if ($this->status === self::STATUS_POSTED) {
            throw new \Exception('Journal entry is already posted.');
        }

        DB::transaction(function () use ($userId) {
            // Update all account balances
            foreach ($this->lines as $line) {
                $line->account->updateBalance($line->amount, $line->type);
            }

            $this->status = self::STATUS_POSTED;
            $this->posted_by = $userId;
            $this->posted_at = now();
            $this->save();
        });

        return $this;
    }

    /**
     * Update account balances based on journal entry lines
     * Used when creating already-posted entries (e.g., from partner transactions)
     */
    public function updateAccountBalances()
    {
        // Load lines with account relationship if not already loaded
        $this->load('lines.account');
        
        foreach ($this->lines as $line) {
            if ($line->account) {
                $line->account->updateBalance($line->amount, $line->type);
            }
        }

        return $this;
    }

    /**
     * Void the journal entry (reverse account balance changes)
     */
    public function void($userId)
    {
        if ($this->status !== self::STATUS_POSTED) {
            throw new \Exception('Only posted entries can be voided.');
        }

        DB::transaction(function () use ($userId) {
            // Load lines with account relationship if not already loaded
            $this->load('lines.account');
            
            // Reverse all account balances
            foreach ($this->lines as $line) {
                if ($line->account) {
                    $reverseType = $line->type === 'debit' ? 'credit' : 'debit';
                    $line->account->updateBalance($line->amount, $reverseType);
                }
            }

            $this->status = self::STATUS_VOIDED;
            $this->save();
        });

        return $this;
    }

    /**
     * Create a reversing entry
     */
    public function createReversingEntry($userId, $reverseDate = null)
    {
        if ($this->status !== self::STATUS_POSTED) {
            throw new \Exception('Only posted entries can be reversed.');
        }

        $reversingEntry = DB::transaction(function () use ($userId, $reverseDate) {
            $newEntry = self::create([
                'business_id' => $this->business_id,
                'entry_number' => self::generateEntryNumber($this->business_id),
                'entry_date' => $reverseDate ?? now(),
                'entry_type' => self::TYPE_REVERSING,
                'status' => self::STATUS_DRAFT,
                'memo' => 'Reversing entry for ' . $this->entry_number,
                'reversed_entry_id' => $this->id,
                'created_by' => $userId,
            ]);

            // Create reversed lines (debits become credits and vice versa)
            foreach ($this->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $newEntry->id,
                    'account_id' => $line->account_id,
                    'type' => $line->type === 'debit' ? 'credit' : 'debit',
                    'amount' => $line->amount,
                    'description' => 'Reversal: ' . $line->description,
                    'contact_id' => $line->contact_id,
                    'sort_order' => $line->sort_order,
                ]);
            }

            $newEntry->recalculateTotals();

            return $newEntry;
        });

        return $reversingEntry;
    }

    /**
     * Get entry types for dropdown
     */
    public static function getEntryTypes()
    {
        return [
            self::TYPE_STANDARD => 'Standard Journal Entry',
            self::TYPE_ADJUSTING => 'Adjusting Entry',
            self::TYPE_CLOSING => 'Closing Entry',
            self::TYPE_REVERSING => 'Reversing Entry',
            self::TYPE_OPENING => 'Opening Balance',
            self::TYPE_BANK_DEPOSIT => 'Bank Deposit',
            self::TYPE_EXPENSE => 'Expense',
            self::TYPE_TRANSFER => 'Fund Transfer',
            self::TYPE_PAYROLL => 'Payroll',
            self::TYPE_DEPRECIATION => 'Depreciation',
            self::TYPE_INVENTORY_ADJUSTMENT => 'Inventory Adjustment',
            self::TYPE_LOAN_PAYMENT => 'Loan Payment',
            self::TYPE_ADVANCE => 'Advance',
        ];
    }

    /**
     * Get statuses for dropdown
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
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            self::STATUS_DRAFT => 'bg-gray',
            self::STATUS_PENDING => 'bg-yellow',
            self::STATUS_APPROVED => 'bg-info',
            self::STATUS_POSTED => 'bg-green',
            self::STATUS_VOIDED => 'bg-red',
        ];

        return $classes[$this->status] ?? 'bg-gray';
    }
}



