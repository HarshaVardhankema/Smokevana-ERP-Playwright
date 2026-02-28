<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $table = 'journal_entry_lines';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }
}

