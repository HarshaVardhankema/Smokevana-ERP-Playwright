<?php

namespace App\Models;

use App\Business;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'opening_balance_date' => 'date',
        'is_system_account' => 'boolean',
        'is_sub_account' => 'boolean',
        'is_active' => 'boolean',
        'track_depreciation' => 'boolean',
    ];

    // Account type constants
    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';
    const TYPE_EQUITY = 'equity';
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';
    const TYPE_COGS = 'cost_of_goods_sold';

    // Detail type constants (common classifications)
    const DETAIL_TYPES = [
        'asset' => [
            'cash_on_hand' => 'Cash on hand',
            'checking' => 'Checking',
            'savings' => 'Savings',
            'money_market' => 'Money Market',
            'accounts_receivable' => 'Accounts Receivable (A/R)',
            'inventory' => 'Inventory',
            'prepaid_expenses' => 'Prepaid Expenses',
            'fixed_assets' => 'Fixed Assets',
            'accumulated_depreciation' => 'Accumulated Depreciation',
            'other_current_assets' => 'Other Current Assets',
            'other_long_term_assets' => 'Other Long-term Assets',
            'undeposited_funds' => 'Undeposited Funds',
            'receivables_from_vendors' => 'Receivables from Vendors',
        ],
        'liability' => [
            'accounts_payable' => 'Accounts Payable (A/P)',
            'credit_card' => 'Credit Card',
            'line_of_credit' => 'Line of Credit',
            'loan_payable' => 'Loan Payable',
            'notes_payable' => 'Notes Payable',
            'other_current_liabilities' => 'Other Current Liabilities',
            'other_long_term_liabilities' => 'Other Long-term Liabilities',
            'payroll_liabilities' => 'Payroll Liabilities',
            'sales_tax_payable' => 'Sales Tax Payable',
            'customer_deposits' => 'Customer Deposits',
        ],
        'equity' => [
            'owners_equity' => "Owner's Equity",
            'retained_earnings' => 'Retained Earnings',
            'common_stock' => 'Common Stock',
            'preferred_stock' => 'Preferred Stock',
            'paid_in_capital' => 'Paid-in Capital',
            'treasury_stock' => 'Treasury Stock',
            'partner_contributions' => 'Partner Contributions',
            'partner_distributions' => 'Partner Distributions',
        ],
        'income' => [
            'income' => 'Income',
            'sales_of_product_income' => 'Sales of Product Income',
            'service_income' => 'Service/Fee Income',
            'discount_received' => 'Discounts/Refunds Received',
            'interest_earned' => 'Interest Earned',
            'other_income' => 'Other Income',
            'unapplied_cash_payment' => 'Unapplied Cash Payment Income',
        ],
        'expense' => [
            'advertising_promotional' => 'Advertising/Promotional',
            'auto' => 'Auto',
            'bad_debts' => 'Bad Debts',
            'bank_charges' => 'Bank Charges',
            'commissions_fees' => 'Commissions & fees',
            'cost_of_labor' => 'Cost of Labor',
            'dues_subscriptions' => 'Dues & Subscriptions',
            'equipment_rental' => 'Equipment Rental',
            'insurance' => 'Insurance',
            'interest_paid' => 'Interest Paid',
            'legal_professional_fees' => 'Legal & Professional Fees',
            'office_general_admin' => 'Office/General Admin Expenses',
            'other_business_expenses' => 'Other Business Expenses',
            'payroll_expenses' => 'Payroll Expenses',
            'rent_or_lease' => 'Rent or Lease',
            'repair_maintenance' => 'Repair & Maintenance',
            'shipping' => 'Shipping, Freight & Delivery',
            'supplies_materials' => 'Supplies & Materials',
            'taxes_paid' => 'Taxes Paid',
            'travel' => 'Travel',
            'utilities' => 'Utilities',
        ],
        'cost_of_goods_sold' => [
            'cost_of_labor_cos' => 'Cost of Labor - COS',
            'equipment_rental_cos' => 'Equipment Rental - COS',
            'other_costs_of_service' => 'Other Costs of Service - COS',
            'shipping_cos' => 'Shipping, Freight & Delivery - COS',
            'supplies_materials_cogs' => 'Supplies & Materials - COGS',
        ],
    ];

    /**
     * Get normal balance (debit or credit) for account type
     */
    public static function getNormalBalance($accountType)
    {
        $debitBalances = [self::TYPE_ASSET, self::TYPE_EXPENSE, self::TYPE_COGS];
        return in_array($accountType, $debitBalances) ? 'debit' : 'credit';
    }

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function bankDeposits()
    {
        return $this->hasMany(BankDeposit::class, 'deposit_to_account_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeParentAccounts($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubAccounts($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeAssets($query)
    {
        return $query->where('account_type', self::TYPE_ASSET);
    }

    public function scopeLiabilities($query)
    {
        return $query->where('account_type', self::TYPE_LIABILITY);
    }

    public function scopeEquity($query)
    {
        return $query->where('account_type', self::TYPE_EQUITY);
    }

    public function scopeBankAccounts($query)
    {
        return $query->where('account_type', self::TYPE_ASSET)
                     ->whereIn('detail_type', ['cash_on_hand', 'checking', 'savings', 'money_market']);
    }

    /**
     * Helper methods
     */
    public function updateBalance($amount, $type)
    {
        $normalBalance = self::getNormalBalance($this->account_type);
        
        if ($type === $normalBalance) {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }
        
        return $this->save();
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->current_balance, 2);
    }

    /**
     * Get total balance including all sub-accounts (roll-up)
     * This follows standard accounting practice used by QuickBooks, Zoho, Xero, etc.
     */
    public function getTotalBalanceAttribute()
    {
        $total = $this->current_balance ?? 0;
        
        // Add balances from all children recursively
        foreach ($this->children as $child) {
            $total += $child->total_balance; // Recursive call
        }
        
        return $total;
    }

    /**
     * Get formatted total balance including sub-accounts
     */
    public function getFormattedTotalBalanceAttribute()
    {
        return number_format($this->total_balance, 2);
    }

    /**
     * Check if account has any sub-accounts
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getFullAccountNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ':' . $this->name;
        }
        return $this->name;
    }

    /**
     * Get detail types for a given account type
     */
    public static function getDetailTypes($accountType = null)
    {
        if ($accountType && isset(self::DETAIL_TYPES[$accountType])) {
            return self::DETAIL_TYPES[$accountType];
        }
        return self::DETAIL_TYPES;
    }

    /**
     * Get all account types
     */
    public static function getAccountTypes()
    {
        return [
            self::TYPE_ASSET => 'Asset',
            self::TYPE_LIABILITY => 'Liability',
            self::TYPE_EQUITY => 'Equity',
            self::TYPE_INCOME => 'Income',
            self::TYPE_EXPENSE => 'Expense',
            self::TYPE_COGS => 'Cost of Goods Sold',
        ];
    }

    /**
     * Create default chart of accounts for a business
     */
    public static function createDefaultAccounts($businessId, $userId)
    {
        $defaultAccounts = [
            // Assets
            ['account_code' => '1000', 'name' => 'Cash', 'account_type' => 'asset', 'detail_type' => 'cash_on_hand', 'is_system_account' => true],
            ['account_code' => '1010', 'name' => 'Checking Account', 'account_type' => 'asset', 'detail_type' => 'checking', 'is_system_account' => true],
            ['account_code' => '1020', 'name' => 'Savings Account', 'account_type' => 'asset', 'detail_type' => 'savings'],
            ['account_code' => '1100', 'name' => 'Accounts Receivable', 'account_type' => 'asset', 'detail_type' => 'accounts_receivable', 'is_system_account' => true],
            ['account_code' => '1200', 'name' => 'Inventory', 'account_type' => 'asset', 'detail_type' => 'inventory', 'is_system_account' => true],
            ['account_code' => '1300', 'name' => 'Undeposited Funds', 'account_type' => 'asset', 'detail_type' => 'undeposited_funds', 'is_system_account' => true],
            ['account_code' => '1400', 'name' => 'Prepaid Expenses', 'account_type' => 'asset', 'detail_type' => 'prepaid_expenses'],
            ['account_code' => '1500', 'name' => 'Fixed Assets', 'account_type' => 'asset', 'detail_type' => 'fixed_assets'],
            ['account_code' => '1600', 'name' => 'Accumulated Depreciation', 'account_type' => 'asset', 'detail_type' => 'accumulated_depreciation'],
            ['account_code' => '1700', 'name' => 'Receivables from Vendors', 'account_type' => 'asset', 'detail_type' => 'receivables_from_vendors'],
            
            // Liabilities
            ['account_code' => '2000', 'name' => 'Accounts Payable', 'account_type' => 'liability', 'detail_type' => 'accounts_payable', 'is_system_account' => true],
            ['account_code' => '2100', 'name' => 'Credit Card Payable', 'account_type' => 'liability', 'detail_type' => 'credit_card'],
            ['account_code' => '2200', 'name' => 'Loans Payable', 'account_type' => 'liability', 'detail_type' => 'loan_payable'],
            ['account_code' => '2300', 'name' => 'Sales Tax Payable', 'account_type' => 'liability', 'detail_type' => 'sales_tax_payable'],
            ['account_code' => '2400', 'name' => 'Payroll Liabilities', 'account_type' => 'liability', 'detail_type' => 'payroll_liabilities'],
            ['account_code' => '2500', 'name' => 'Customer Deposits', 'account_type' => 'liability', 'detail_type' => 'customer_deposits'],
            ['account_code' => '2600', 'name' => 'Owed to Partners', 'account_type' => 'liability', 'detail_type' => 'other_current_liabilities'],
            
            // Equity
            ['account_code' => '3000', 'name' => "Owner's Equity", 'account_type' => 'equity', 'detail_type' => 'owners_equity', 'is_system_account' => true],
            ['account_code' => '3100', 'name' => 'Retained Earnings', 'account_type' => 'equity', 'detail_type' => 'retained_earnings', 'is_system_account' => true],
            ['account_code' => '3200', 'name' => 'Partner Contributions', 'account_type' => 'equity', 'detail_type' => 'partner_contributions'],
            ['account_code' => '3300', 'name' => 'Partner Distributions', 'account_type' => 'equity', 'detail_type' => 'partner_distributions'],
            
            // Income
            ['account_code' => '4000', 'name' => 'Sales Revenue', 'account_type' => 'income', 'detail_type' => 'sales_of_product_income', 'is_system_account' => true],
            ['account_code' => '4100', 'name' => 'Service Revenue', 'account_type' => 'income', 'detail_type' => 'service_income'],
            ['account_code' => '4200', 'name' => 'Interest Income', 'account_type' => 'income', 'detail_type' => 'interest_earned'],
            ['account_code' => '4300', 'name' => 'Other Income', 'account_type' => 'income', 'detail_type' => 'other_income'],
            
            // Cost of Goods Sold
            ['account_code' => '5000', 'name' => 'Cost of Goods Sold', 'account_type' => 'cost_of_goods_sold', 'detail_type' => 'supplies_materials_cogs', 'is_system_account' => true],
            ['account_code' => '5100', 'name' => 'Cost of Labor', 'account_type' => 'cost_of_goods_sold', 'detail_type' => 'cost_of_labor_cos'],
            
            // Expenses
            ['account_code' => '6000', 'name' => 'Advertising & Marketing', 'account_type' => 'expense', 'detail_type' => 'advertising_promotional'],
            ['account_code' => '6100', 'name' => 'Bank Charges & Fees', 'account_type' => 'expense', 'detail_type' => 'bank_charges'],
            ['account_code' => '6200', 'name' => 'Insurance', 'account_type' => 'expense', 'detail_type' => 'insurance'],
            ['account_code' => '6300', 'name' => 'Interest Expense', 'account_type' => 'expense', 'detail_type' => 'interest_paid'],
            ['account_code' => '6400', 'name' => 'Office Expenses', 'account_type' => 'expense', 'detail_type' => 'office_general_admin'],
            ['account_code' => '6500', 'name' => 'Payroll Expenses', 'account_type' => 'expense', 'detail_type' => 'payroll_expenses'],
            ['account_code' => '6600', 'name' => 'Rent Expense', 'account_type' => 'expense', 'detail_type' => 'rent_or_lease'],
            ['account_code' => '6700', 'name' => 'Repairs & Maintenance', 'account_type' => 'expense', 'detail_type' => 'repair_maintenance'],
            ['account_code' => '6800', 'name' => 'Shipping & Delivery', 'account_type' => 'expense', 'detail_type' => 'shipping'],
            ['account_code' => '6900', 'name' => 'Supplies', 'account_type' => 'expense', 'detail_type' => 'supplies_materials'],
            ['account_code' => '7000', 'name' => 'Taxes & Licenses', 'account_type' => 'expense', 'detail_type' => 'taxes_paid'],
            ['account_code' => '7100', 'name' => 'Travel & Entertainment', 'account_type' => 'expense', 'detail_type' => 'travel'],
            ['account_code' => '7200', 'name' => 'Utilities', 'account_type' => 'expense', 'detail_type' => 'utilities'],
            ['account_code' => '7300', 'name' => 'Professional Fees', 'account_type' => 'expense', 'detail_type' => 'legal_professional_fees'],
            ['account_code' => '7400', 'name' => 'Bad Debt', 'account_type' => 'expense', 'detail_type' => 'bad_debts'],
            ['account_code' => '7500', 'name' => 'Commissions & Fees', 'account_type' => 'expense', 'detail_type' => 'commissions_fees'],
        ];

        foreach ($defaultAccounts as $account) {
            self::create(array_merge($account, [
                'business_id' => $businessId,
                'created_by' => $userId,
                'full_name' => $account['name'],
            ]));
        }
    }

    /**
     * Dropdown for select fields
     */
    public static function forDropdown($businessId, $accountType = null, $activeOnly = true)
    {
        $query = self::where('business_id', $businessId);
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        if ($accountType) {
            $query->where('account_type', $accountType);
        }
        
        return $query->orderBy('account_code')
                     ->pluck('name', 'id');
    }
}



