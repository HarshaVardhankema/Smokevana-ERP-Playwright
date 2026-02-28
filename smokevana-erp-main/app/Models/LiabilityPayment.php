<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LiabilityPayment extends Model
{
    protected $table = 'liability_payments';

    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function liability()
    {
        return $this->belongsTo(BusinessLiability::class, 'liability_id');
    }

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}




