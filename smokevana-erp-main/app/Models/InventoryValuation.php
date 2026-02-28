<?php

namespace App\Models;

use App\Business;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryValuation extends Model
{
    protected $table = 'inventory_valuations';

    protected $guarded = ['id'];

    protected $casts = [
        'valuation_date' => 'datetime',
        'breakdown' => 'array',
    ];

    // Valuation Methods
    const METHOD_FIFO = 'fifo';
    const METHOD_LIFO = 'lifo';
    const METHOD_WEIGHTED_AVERAGE = 'weighted_average';
    const METHOD_SPECIFIC = 'specific_identification';

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function inventoryAssetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'inventory_asset_account_id');
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
     * Get valuation methods
     */
    public static function getValuationMethods()
    {
        return [
            self::METHOD_FIFO => 'FIFO (First In, First Out)',
            self::METHOD_LIFO => 'LIFO (Last In, First Out)',
            self::METHOD_WEIGHTED_AVERAGE => 'Weighted Average',
            self::METHOD_SPECIFIC => 'Specific Identification',
        ];
    }

    /**
     * Create journal entry for inventory adjustment
     */
    public function createJournalEntry($userId)
    {
        if ($this->journal_entry_id) {
            throw new \Exception('Journal entry already exists.');
        }

        // Get inventory asset account
        $inventoryAccount = $this->inventory_asset_account_id 
            ? ChartOfAccount::find($this->inventory_asset_account_id)
            : ChartOfAccount::where('business_id', $this->business_id)
                ->where('detail_type', 'inventory')
                ->first();

        if (!$inventoryAccount) {
            throw new \Exception('Inventory asset account not found.');
        }

        // Get previous valuation to calculate adjustment
        $previousValuation = self::where('business_id', $this->business_id)
            ->where('id', '<', $this->id)
            ->orderBy('valuation_date', 'desc')
            ->first();

        $previousValue = $previousValuation ? $previousValuation->total_cost_value : 0;
        $adjustment = $this->total_cost_value - $previousValue;

        if (abs($adjustment) < 0.01) {
            return null; // No adjustment needed
        }

        DB::beginTransaction();

        try {
            $entry = JournalEntry::create([
                'business_id' => $this->business_id,
                'entry_number' => JournalEntry::generateEntryNumber($this->business_id),
                'entry_date' => $this->valuation_date,
                'entry_type' => JournalEntry::TYPE_INVENTORY_ADJUSTMENT,
                'status' => JournalEntry::STATUS_POSTED,
                'memo' => 'Inventory Valuation Adjustment',
                'total_debit' => abs($adjustment),
                'total_credit' => abs($adjustment),
                'created_by' => $userId,
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            // Get COGS account for adjustment
            $cogsAccount = ChartOfAccount::where('business_id', $this->business_id)
                ->where('account_type', 'cost_of_goods_sold')
                ->first();

            if ($adjustment > 0) {
                // Inventory increased: Debit Inventory, Credit COGS
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $inventoryAccount->id,
                    'type' => 'debit',
                    'amount' => abs($adjustment),
                    'description' => 'Inventory adjustment - increase',
                    'sort_order' => 0,
                ]);
                if ($cogsAccount) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $cogsAccount->id,
                        'type' => 'credit',
                        'amount' => abs($adjustment),
                        'description' => 'Inventory adjustment - increase',
                        'sort_order' => 1,
                    ]);
                }
            } else {
                // Inventory decreased: Debit COGS, Credit Inventory
                if ($cogsAccount) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $cogsAccount->id,
                        'type' => 'debit',
                        'amount' => abs($adjustment),
                        'description' => 'Inventory adjustment - decrease',
                        'sort_order' => 0,
                    ]);
                }
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $inventoryAccount->id,
                    'type' => 'credit',
                    'amount' => abs($adjustment),
                    'description' => 'Inventory adjustment - decrease',
                    'sort_order' => 1,
                ]);
            }

            // Update account balances
            $entry->updateAccountBalances();

            $this->journal_entry_id = $entry->id;
            $this->save();

            DB::commit();

            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}




