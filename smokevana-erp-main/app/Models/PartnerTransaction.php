<?php

namespace App\Models;

use App\Business;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'partner_transactions';

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    // Transaction type constants
    const TYPE_CAPITAL_CONTRIBUTION = 'capital_contribution';
    const TYPE_OWNER_DRAWING = 'owner_drawing';
    const TYPE_LOAN_FROM_PARTNER = 'loan_from_partner';
    const TYPE_LOAN_TO_PARTNER = 'loan_to_partner';
    const TYPE_LOAN_REPAYMENT = 'loan_repayment';
    const TYPE_ADVANCE = 'advance';
    const TYPE_REIMBURSEMENT = 'reimbursement';
    const TYPE_PERSONAL_ASSET = 'personal_asset';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_VOIDED = 'voided';

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Check if this increases company equity/cash
     */
    public function increasesEquity()
    {
        return in_array($this->transaction_type, [
            self::TYPE_CAPITAL_CONTRIBUTION,
            self::TYPE_LOAN_FROM_PARTNER,
        ]);
    }

    /**
     * Check if this decreases company equity/cash
     */
    public function decreasesEquity()
    {
        return in_array($this->transaction_type, [
            self::TYPE_OWNER_DRAWING,
            self::TYPE_LOAN_TO_PARTNER,
            self::TYPE_LOAN_REPAYMENT,
        ]);
    }

    /**
     * Get transaction types for dropdown
     */
    public static function getTransactionTypes()
    {
        return [
            self::TYPE_CAPITAL_CONTRIBUTION => 'Capital Contribution',
            self::TYPE_OWNER_DRAWING => 'Owner Drawing',
            self::TYPE_LOAN_FROM_PARTNER => 'Loan from Partner',
            self::TYPE_LOAN_TO_PARTNER => 'Loan to Partner',
            self::TYPE_LOAN_REPAYMENT => 'Loan Repayment',
            self::TYPE_ADVANCE => 'Partner Advance',
            self::TYPE_REIMBURSEMENT => 'Expense Reimbursement',
            self::TYPE_PERSONAL_ASSET => 'Personal Asset Used',
        ];
    }

    /**
     * Get statuses for dropdown
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_VOIDED => 'Voided',
        ];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            self::STATUS_PENDING => 'bg-yellow',
            self::STATUS_APPROVED => 'bg-info',
            self::STATUS_COMPLETED => 'bg-green',
            self::STATUS_VOIDED => 'bg-red',
        ];

        return $classes[$this->status] ?? 'bg-gray';
    }

    /**
     * Create journal entry for this partner transaction
     */
    public function createJournalEntry($userId)
    {
        $partnerAccount = $this->account;
        
        if (!$partnerAccount) {
            throw new \Exception('No account specified for this transaction.');
        }

        $partner = $this->partner;
        $partnerName = $partner ? $partner->first_name . ' ' . $partner->last_name : 'Partner';
        $transactionTypeName = self::getTransactionTypes()[$this->transaction_type] ?? $this->transaction_type;

        // Create the journal entry
        $entry = JournalEntry::create([
            'business_id' => $this->business_id,
            'entry_number' => JournalEntry::generateEntryNumber($this->business_id),
            'entry_date' => $this->transaction_date,
            'entry_type' => 'partner_transaction',
            'status' => 'posted',
            'memo' => $this->description ?? "Partner transaction: {$transactionTypeName}",
            'total_debit' => $this->amount,
            'total_credit' => $this->amount,
            'created_by' => $userId,
            'posted_by' => $userId,
            'posted_at' => now(),
        ]);

        // Determine debit/credit based on transaction type
        // For capital contribution & loan from partner: Debit Cash, Credit Equity/Liability
        // For owner drawing & loan to partner & loan repayment: Debit Drawings/Receivable, Credit Cash
        $cashIsDebit = $this->increasesEquity();
        
        // First line: Cash/Bank account
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->account_id,
            'type' => $cashIsDebit ? 'debit' : 'credit',
            'amount' => $this->amount,
            'description' => "{$partnerName}: {$transactionTypeName}",
            'sort_order' => 0,
        ]);

        // Find appropriate offsetting account based on transaction type
        $offsetAccount = $this->findOffsetAccount();

        if ($offsetAccount) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $offsetAccount->id,
                'type' => $cashIsDebit ? 'credit' : 'debit',
                'amount' => $this->amount,
                'description' => "{$partnerName}: {$transactionTypeName}",
                'sort_order' => 1,
            ]);
        }

        // Update account balances
        $entry->updateAccountBalances();

        // Link journal entry to this transaction
        $this->journal_entry_id = $entry->id;
        $this->save();

        return $entry;
    }

    /**
     * Find the appropriate offsetting account based on transaction type
     */
    protected function findOffsetAccount()
    {
        $accountType = 'equity';
        
        switch ($this->transaction_type) {
            case self::TYPE_CAPITAL_CONTRIBUTION:
            case self::TYPE_OWNER_DRAWING:
                $accountType = 'equity';
                break;
            case self::TYPE_LOAN_FROM_PARTNER:
            case self::TYPE_LOAN_REPAYMENT:
                $accountType = 'liability';
                break;
            case self::TYPE_LOAN_TO_PARTNER:
            case self::TYPE_ADVANCE:
                $accountType = 'asset';
                break;
        }

        return ChartOfAccount::where('business_id', $this->business_id)
            ->where('account_type', $accountType)
            ->where('is_active', true)
            ->first();
    }
}
