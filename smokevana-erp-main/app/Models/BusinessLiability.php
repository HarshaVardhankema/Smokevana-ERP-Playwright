<?php

namespace App\Models;

use App\Business;
use App\Contact;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BusinessLiability extends Model
{
    use SoftDeletes;

    protected $table = 'business_liabilities';

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'terms' => 'array',
        'metadata' => 'array',
    ];

    // Liability Types
    const TYPE_VENDORS_UNPAID = 'vendors_unpaid';
    const TYPE_OWED_TO_PARTNER = 'owed_to_partner';
    const TYPE_CREDIT_CARD = 'credit_card';
    const TYPE_LOAN = 'loan';
    const TYPE_ADVANCE_RECEIVED = 'advance_received';
    const TYPE_EMPLOYEE_PAYABLE = 'employee_payable';
    const TYPE_TAX_PAYABLE = 'tax_payable';
    const TYPE_OTHER = 'other';

    // Status Constants
    const STATUS_ACTIVE = 'active';
    const STATUS_PAID_OFF = 'paid_off';
    const STATUS_DEFAULTED = 'defaulted';
    const STATUS_RESTRUCTURED = 'restructured';

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function liabilityAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'liability_account_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function payments()
    {
        return $this->hasMany(LiabilityPayment::class, 'liability_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('current_balance', '>', 0);
    }

    /**
     * Get liability types
     */
    public static function getLiabilityTypes()
    {
        return [
            self::TYPE_VENDORS_UNPAID => 'Accounts Payable',
            self::TYPE_OWED_TO_PARTNER => 'Owed to Partner',
            self::TYPE_CREDIT_CARD => 'Credit Card',
            self::TYPE_LOAN => 'Loan',
            self::TYPE_ADVANCE_RECEIVED => 'Customer Advance',
            self::TYPE_EMPLOYEE_PAYABLE => 'Employee Payable',
            self::TYPE_TAX_PAYABLE => 'Tax Payable',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAID_OFF => 'Paid Off',
            self::STATUS_DEFAULTED => 'Defaulted',
            self::STATUS_RESTRUCTURED => 'Restructured',
        ];
    }

    /**
     * Get payment frequencies
     */
    public static function getPaymentFrequencies()
    {
        return [
            'one_time' => 'One Time',
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually' => 'Annually',
        ];
    }

    /**
     * Check if liability is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && $this->current_balance > 0;
    }

    /**
     * Make a payment on this liability
     */
    public function makePayment($amount, $data, $userId)
    {
        if ($amount > $this->current_balance) {
            throw new \Exception('Payment amount exceeds current balance.');
        }

        DB::beginTransaction();

        try {
            $payment = LiabilityPayment::create([
                'liability_id' => $this->id,
                'payment_date' => $data['payment_date'],
                'principal_amount' => $data['principal_amount'] ?? $amount,
                'interest_amount' => $data['interest_amount'] ?? 0,
                'total_amount' => $amount,
                'payment_method' => $data['payment_method'] ?? null,
                'from_account_id' => $data['from_account_id'],
                'notes' => $data['notes'] ?? null,
                'reference' => $data['reference'] ?? null,
                'created_by' => $userId,
            ]);

            // Create journal entry for payment
            $entry = JournalEntry::create([
                'business_id' => $this->business_id,
                'entry_number' => JournalEntry::generateEntryNumber($this->business_id),
                'entry_date' => $data['payment_date'],
                'entry_type' => JournalEntry::TYPE_LOAN_PAYMENT,
                'status' => JournalEntry::STATUS_POSTED,
                'memo' => 'Liability Payment - ' . $this->name,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'created_by' => $userId,
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            // Debit - Liability Account (reduce liability)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->liability_account_id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Payment on ' . $this->name,
                'sort_order' => 0,
            ]);

            // Credit - Bank/Cash Account
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $data['from_account_id'],
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Payment on ' . $this->name,
                'sort_order' => 1,
            ]);

            // Update account balances
            $entry->updateAccountBalances();

            $payment->journal_entry_id = $entry->id;
            $payment->save();

            // Update liability balance
            $this->current_balance -= $amount;
            if ($this->current_balance <= 0) {
                $this->current_balance = 0;
                $this->status = self::STATUS_PAID_OFF;
            }
            $this->save();

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}




