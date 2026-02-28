<?php

namespace App\Models;

use App\Business;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BankDeposit extends Model
{
    use SoftDeletes;

    protected $table = 'bank_deposits';

    protected $guarded = ['id'];

    protected $casts = [
        'deposit_date' => 'date',
        'attachments' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_DEPOSITED = 'deposited';
    const STATUS_RECONCILED = 'reconciled';
    const STATUS_VOIDED = 'voided';

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function depositToAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'deposit_to_account_id');
    }

    public function lines()
    {
        return $this->hasMany(BankDepositLine::class, 'bank_deposit_id');
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
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDeposited($query)
    {
        return $query->where('status', self::STATUS_DEPOSITED);
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', self::STATUS_RECONCILED);
    }

    /**
     * Generate deposit number
     */
    public static function generateDepositNumber($businessId)
    {
        $prefix = 'DEP';
        $year = date('Y');
        
        $lastDeposit = self::where('business_id', $businessId)
                          ->where('deposit_number', 'like', "{$prefix}{$year}%")
                          ->orderBy('deposit_number', 'desc')
                          ->first();
        
        if ($lastDeposit) {
            $lastNumber = (int) substr($lastDeposit->deposit_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate total from lines
     */
    public function recalculateTotal()
    {
        $this->total_amount = $this->lines()->sum('amount');
        return $this->save();
    }

    /**
     * Process the deposit (create journal entry)
     */
    public function processDeposit($userId)
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Only pending deposits can be processed.');
        }

        $hasLines = $this->lines()->count() > 0;

        $journalEntry = DB::transaction(function () use ($userId, $hasLines) {
            // Create journal entry
            $entry = JournalEntry::create([
                'business_id' => $this->business_id,
                'entry_number' => JournalEntry::generateEntryNumber($this->business_id),
                'entry_date' => $this->deposit_date,
                'entry_type' => JournalEntry::TYPE_BANK_DEPOSIT,
                'status' => JournalEntry::STATUS_DRAFT,
                'memo' => $this->memo ?? 'Bank Deposit ' . $this->deposit_number,
                'source_document' => $this->deposit_number,
                'created_by' => $userId,
                'total_debit' => $this->total_amount,
                'total_credit' => $this->total_amount,
            ]);

            // Debit the bank account (increase asset)
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->deposit_to_account_id,
                'type' => 'debit',
                'amount' => $this->total_amount,
                'description' => 'Bank Deposit',
                'sort_order' => 0,
            ]);

            $creditAmount = 0;
            $sortOrder = 1;
            
            if ($hasLines) {
                // Credit each line item's account (only if account exists)
                foreach ($this->lines as $line) {
                    // Check if account exists
                    $account = ChartOfAccount::find($line->account_id);
                    $accountId = $account ? $line->account_id : $this->deposit_to_account_id;
                    
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $accountId,
                        'type' => 'credit',
                        'amount' => $line->amount,
                        'description' => $line->memo ?? 'Deposit Line',
                        'contact_id' => $line->contact_id,
                        'reference' => $line->ref_no,
                        'sort_order' => $sortOrder++,
                    ]);
                    $creditAmount += $line->amount;
                }
            }
            
            // If no lines or credit doesn't match debit, add balancing entry
            if ($creditAmount < $this->total_amount) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $this->deposit_to_account_id,
                    'type' => 'credit',
                    'amount' => $this->total_amount - $creditAmount,
                    'description' => 'Deposit Credit',
                    'sort_order' => $sortOrder,
                ]);
            }

            // Post the journal entry
            $entry->post($userId);

            // Update deposit status
            $this->status = self::STATUS_DEPOSITED;
            $this->journal_entry_id = $entry->id;
            $this->save();

            return $entry;
        });

        return $journalEntry;
    }

    /**
     * Void the deposit
     */
    public function voidDeposit($userId)
    {
        if ($this->status === self::STATUS_VOIDED) {
            throw new \Exception('Deposit is already voided.');
        }

        DB::transaction(function () use ($userId) {
            // Void the associated journal entry if exists
            if ($this->journalEntry) {
                $this->journalEntry->void($userId);
            }

            $this->status = self::STATUS_VOIDED;
            $this->save();
        });

        return $this;
    }

    /**
     * Get available payments for deposit
     */
    public static function getUndeposedPayments($businessId, $locationId = null)
    {
        // Get payments that haven't been deposited yet
        $query = \App\TransactionPayment::where('business_id', $businessId)
                    ->whereNull('bank_deposit_id')
                    ->where('amount', '>', 0);

        if ($locationId) {
            $query->whereHas('transaction', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        return $query->with(['transaction.contact'])
                     ->orderBy('paid_on', 'desc')
                     ->get();
    }

    /**
     * Get statuses for dropdown
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_DEPOSITED => 'Deposited',
            self::STATUS_RECONCILED => 'Reconciled',
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
            self::STATUS_DEPOSITED => 'bg-green',
            self::STATUS_RECONCILED => 'bg-blue',
            self::STATUS_VOIDED => 'bg-red',
        ];

        return $classes[$this->status] ?? 'bg-gray';
    }
}



