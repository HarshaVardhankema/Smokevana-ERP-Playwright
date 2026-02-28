<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Models\BankDeposit;
use App\Models\BankDepositLine;
use App\Models\BusinessLiability;
use App\Models\ChartOfAccount;
use App\Models\InventoryValuation;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\LiabilityPayment;
use App\Models\PartnerTransaction;
use App\Contact;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookkeepingController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;

    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Main Dashboard - Comprehensive bookkeeping overview
     */
    public function dashboard()
    {
        if (!auth()->user()->can('all_expense.access') && !auth()->user()->can('expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if chart of accounts exists, if not create defaults
        $accountsCount = ChartOfAccount::where('business_id', $business_id)->count();
        if ($accountsCount === 0) {
            ChartOfAccount::createDefaultAccounts($business_id, auth()->user()->id);
        }

        // Get financial summary
        $summary = $this->getFinancialSummary($business_id);

        // Get recent transactions
        $recentEntries = JournalEntry::where('business_id', $business_id)
            ->with(['createdBy', 'lines.account'])
            ->orderBy('entry_date', 'desc')
            ->limit(10)
            ->get();

        // Get account balances by type
        $accountBalances = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->selectRaw('account_type, SUM(current_balance) as total_balance')
            ->groupBy('account_type')
            ->pluck('total_balance', 'account_type')
            ->toArray();

        // Get liabilities summary from BusinessLiability (tracked liabilities)
        $liabilitiesSummary = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->selectRaw('liability_type, SUM(current_balance) as total_balance, COUNT(*) as count')
            ->groupBy('liability_type')
            ->get();

        // Total tracked liabilities (from BusinessLiability model)
        $totalTrackedLiabilities = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->sum('current_balance');

        $trackedLiabilitiesCount = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->count();

        // Get overdue liabilities
        $overdueLiabilities = BusinessLiability::where('business_id', $business_id)
            ->overdue()
            ->with('contact')
            ->limit(5)
            ->get();

        // Get ALL active liabilities (not just overdue) for display
        $activeLiabilities = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->with('contact')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Get pending bank deposits
        $pendingDeposits = BankDeposit::where('business_id', $business_id)
            ->where('status', 'pending')
            ->with('depositToAccount')
            ->orderBy('deposit_date', 'desc')
            ->limit(5)
            ->get();

        // Get inventory valuation
        $latestValuation = InventoryValuation::where('business_id', $business_id)
            ->orderBy('valuation_date', 'desc')
            ->first();

        // Get accounts receivable and payable
        $receivables = ChartOfAccount::where('business_id', $business_id)
            ->where('detail_type', 'accounts_receivable')
            ->sum('current_balance');

        $payables = ChartOfAccount::where('business_id', $business_id)
            ->where('detail_type', 'accounts_payable')
            ->sum('current_balance');

        // Get cash and bank balances
        $cashBalances = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->get(['id', 'name', 'current_balance', 'detail_type']);

        return view('bookkeeping.dashboard', compact(
            'summary',
            'recentEntries',
            'accountBalances',
            'liabilitiesSummary',
            'totalTrackedLiabilities',
            'trackedLiabilitiesCount',
            'overdueLiabilities',
            'activeLiabilities',
            'pendingDeposits',
            'latestValuation',
            'receivables',
            'payables',
            'cashBalances'
        ));
    }

    /**
     * Get financial summary data
     */
    private function getFinancialSummary($businessId)
    {
        $accounts = ChartOfAccount::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        $totalAssets = $accounts->where('account_type', 'asset')->sum('current_balance');
        $totalLiabilities = $accounts->where('account_type', 'liability')->sum('current_balance');
        $totalEquity = $accounts->where('account_type', 'equity')->sum('current_balance');
        $totalIncome = $accounts->where('account_type', 'income')->sum('current_balance');
        $totalExpenses = $accounts->where('account_type', 'expense')->sum('current_balance');
        $totalCogs = $accounts->where('account_type', 'cost_of_goods_sold')->sum('current_balance');

        $netIncome = $totalIncome - $totalExpenses - $totalCogs;
        $netWorth = $totalAssets - $totalLiabilities;

        return [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'total_cogs' => $totalCogs,
            'net_income' => $netIncome,
            'net_worth' => $netWorth,
        ];
    }

    // ==================== CHART OF ACCOUNTS ====================

    /**
     * Display Chart of Accounts
     */
    public function chartOfAccounts(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $accounts = ChartOfAccount::where('business_id', $business_id)
                ->with('parent')
                ->orderBy('account_code')
                ->get();

            return DataTables::of($accounts)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group dropdown">
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info dropdown-toggle" 
                            data-toggle="dropdown">Actions <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">';
                    
                    $html .= '<li><a href="' . action([\App\Http\Controllers\BookkeepingController::class, 'editAccount'], [$row->id]) . '">
                        <i class="fa fa-edit"></i> Edit</a></li>';
                    
                    if (!$row->is_system_account) {
                        $html .= '<li><a href="#" class="delete-account" data-href="' . action([\App\Http\Controllers\BookkeepingController::class, 'destroyAccount'], [$row->id]) . '">
                            <i class="fa fa-trash"></i> Delete</a></li>';
                    }
                    
                    $html .= '<li><a href="' . action([\App\Http\Controllers\BookkeepingController::class, 'accountLedger'], [$row->id]) . '">
                        <i class="fa fa-book"></i> View Ledger</a></li>';
                    
                    $html .= '</ul></div>';
                    
                    return $html;
                })
                ->editColumn('name', function ($row) {
                    $name = $row->is_sub_account ? '↳ ' . $row->name : $row->name;
                    if (!$row->is_active) {
                        $name .= ' <span class="label label-default">Inactive</span>';
                    }
                    return $name;
                })
                ->editColumn('account_type', function ($row) {
                    $types = ChartOfAccount::getAccountTypes();
                    $colors = [
                        'asset' => 'bg-green',
                        'liability' => 'bg-red',
                        'equity' => 'bg-blue',
                        'income' => 'bg-aqua',
                        'expense' => 'bg-orange',
                        'cost_of_goods_sold' => 'bg-purple',
                    ];
                    return '<span class="label ' . ($colors[$row->account_type] ?? 'bg-gray') . '">' . ($types[$row->account_type] ?? $row->account_type) . '</span>';
                })
                ->editColumn('current_balance', function ($row) {
                    $class = $row->current_balance >= 0 ? 'text-success' : 'text-danger';
                    return '<span class="' . $class . '">' . number_format($row->current_balance, 2) . '</span>';
                })
                ->addColumn('detail_type_display', function ($row) {
                    $detailTypes = ChartOfAccount::getDetailTypes($row->account_type);
                    return $detailTypes[$row->detail_type] ?? $row->detail_type ?? '-';
                })
                ->rawColumns(['action', 'name', 'account_type', 'current_balance'])
                ->make(true);
        }

        $accountTypes = ChartOfAccount::getAccountTypes();
        
        // Group accounts by type for tree view
        // Load children relationship for balance roll-up calculation (standard accounting practice)
        $groupedAccounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true)->with('children'); // Nested children for deep hierarchy
            }])
            ->orderBy('account_code')
            ->get()
            ->groupBy('account_type');

        return view('bookkeeping.chart_of_accounts.index', compact('accountTypes', 'groupedAccounts'));
    }

    /**
     * Show form for creating account
     */
    public function createAccount()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $accountTypes = ChartOfAccount::getAccountTypes();
        $detailTypes = ChartOfAccount::getDetailTypes();
        
        $parentAccounts = ChartOfAccount::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->pluck('name', 'id');

        return view('bookkeeping.chart_of_accounts.create', compact('accountTypes', 'detailTypes', 'parentAccounts'));
    }

    /**
     * Store new account
     */
    public function storeAccount(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,income,expense,cost_of_goods_sold',
            'account_code' => 'nullable|string|max:20',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            // Check for duplicate account code
            if ($request->account_code) {
                $existing = ChartOfAccount::where('business_id', $business_id)
                    ->where('account_code', $request->account_code)
                    ->exists();
                
                if ($existing) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('status', ['success' => 0, 'msg' => 'Account code already exists.']);
                }
            }

            // Validate parent account (avoid broken foreign key)
            $parent = null;
            $parentId = $request->parent_id;
            if (!empty($parentId)) {
                $parent = ChartOfAccount::where('business_id', $business_id)
                    ->where('id', $parentId)
                    ->first();

                if (!$parent) {
                    // Invalid parent selected, reset to null so FK doesn't fail
                    $parentId = null;
                }
            }

            $account = ChartOfAccount::create([
                'business_id' => $business_id,
                'name' => $request->name,
                'account_code' => $request->account_code,
                'account_type' => $request->account_type,
                'detail_type' => $request->detail_type,
                'description' => $request->description,
                'parent_id' => $parentId,
                'is_sub_account' => !empty($parentId),
                'opening_balance' => $request->opening_balance ?? 0,
                'current_balance' => $request->opening_balance ?? 0,
                'opening_balance_date' => $request->opening_balance_date,
                'created_by' => auth()->user()->id,
            ]);

            // Set full name
            if ($parent) {
                $account->full_name = $parent->name . ':' . $account->name;
            } else {
                $account->full_name = $account->name;
            }
            $account->save();

            return redirect()
                ->route('bookkeeping.accounts.index')
                ->with('status', ['success' => 1, 'msg' => 'Account created successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error creating account: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('status', ['success' => 0, 'msg' => 'Error creating account: ' . $e->getMessage()]);
        }
    }

    /**
     * Edit account form
     */
    public function editAccount($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $account = ChartOfAccount::where('business_id', $business_id)
            ->findOrFail($id);

        $accountTypes = ChartOfAccount::getAccountTypes();
        $detailTypes = ChartOfAccount::getDetailTypes();
        
        $parentAccounts = ChartOfAccount::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->pluck('name', 'id');

        return view('bookkeeping.chart_of_accounts.edit', compact('account', 'accountTypes', 'detailTypes', 'parentAccounts'));
    }

    /**
     * Update account
     */
    public function updateAccount(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,income,expense,cost_of_goods_sold',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $account = ChartOfAccount::where('business_id', $business_id)
                ->findOrFail($id);

            // Check for duplicate account code
            if ($request->account_code && $request->account_code !== $account->account_code) {
                $existing = ChartOfAccount::where('business_id', $business_id)
                    ->where('account_code', $request->account_code)
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($existing) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'msg' => 'Account code already exists.',
                        ], 422);
                    }
                    return redirect()->back()
                        ->withInput()
                        ->with('status', ['success' => false, 'msg' => 'Account code already exists.']);
                }
            }

            $account->update([
                'name' => $request->name,
                'account_code' => $request->account_code,
                'account_type' => $request->account_type,
                'detail_type' => $request->detail_type,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'is_sub_account' => !empty($request->parent_id),
                'is_active' => $request->is_active ?? true,
            ]);

            // Update full name
            if ($account->parent) {
                $account->full_name = $account->parent->name . ':' . $account->name;
            } else {
                $account->full_name = $account->name;
            }
            $account->save();

            // Return JSON for AJAX requests, redirect for regular form submissions
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Account updated successfully.',
                ]);
            }

            return redirect()->route('bookkeeping.accounts.index')
                ->with('status', ['success' => true, 'msg' => 'Account updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error updating account: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Error updating account.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('status', ['success' => false, 'msg' => 'Error updating account.']);
        }
    }

    /**
     * Delete account
     */
    public function destroyAccount($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $account = ChartOfAccount::where('business_id', $business_id)
                ->findOrFail($id);

            if ($account->is_system_account) {
                return response()->json([
                    'success' => false,
                    'msg' => 'System accounts cannot be deleted.',
                ], 422);
            }

            // Check if account has transactions
            $hasTransactions = JournalEntryLine::where('account_id', $id)->exists();
            if ($hasTransactions) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Cannot delete account with transactions. Please deactivate it instead.',
                ], 422);
            }

            $account->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Account deleted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error deleting account.',
            ], 500);
        }
    }

    /**
     * View account ledger
     */
    public function accountLedger($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $account = ChartOfAccount::where('business_id', $business_id)
            ->findOrFail($id);

        $entries = JournalEntryLine::where('account_id', $id)
            ->with(['journalEntry' => function ($q) {
                $q->where('status', 'posted');
            }, 'contact'])
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('bookkeeping.chart_of_accounts.ledger', compact('account', 'entries'));
    }

    /**
     * Get detail types for account type (AJAX)
     */
    public function getDetailTypes($accountType)
    {
        $detailTypes = ChartOfAccount::getDetailTypes($accountType);
        return response()->json($detailTypes);
    }

    // ==================== JOURNAL ENTRIES ====================

    /**
     * List journal entries
     */
    public function journalEntries(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $entries = JournalEntry::where('business_id', $business_id)
                ->with(['createdBy', 'contact'])
                ->orderBy('entry_date', 'desc');

            if ($request->status) {
                $entries->where('status', $request->status);
            }

            if ($request->entry_type) {
                $entries->where('entry_type', $request->entry_type);
            }

            if ($request->start_date && $request->end_date) {
                $entries->whereBetween('entry_date', [$request->start_date, $request->end_date]);
            }

            return DataTables::of($entries)
                ->addColumn('action', function ($row) {
                    return ''; // Actions rendered client-side
                })
                ->addColumn('view_url', function ($row) {
                    return route('bookkeeping.journal.show', $row->id);
                })
                ->addColumn('post_url', function ($row) {
                    return route('bookkeeping.journal.post', $row->id);
                })
                ->addColumn('void_url', function ($row) {
                    return route('bookkeeping.journal.void', $row->id);
                })
                ->editColumn('entry_date', function ($row) {
                    return $row->entry_date->format('M d, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row->status;
                })
                ->editColumn('entry_type', function ($row) {
                    $types = JournalEntry::getEntryTypes();
                    return $types[$row->entry_type] ?? ucwords(str_replace('_', ' ', $row->entry_type));
                })
                ->editColumn('total_debit', function ($row) {
                    return $row->total_debit;
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->createdBy ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $entryTypes = JournalEntry::getEntryTypes();
        $statuses = JournalEntry::getStatuses();
        
        // Stats for the dashboard cards
        $stats = [
            'draft' => JournalEntry::where('business_id', $business_id)->where('status', 'draft')->count(),
            'posted' => JournalEntry::where('business_id', $business_id)->where('status', 'posted')->count(),
            'voided' => JournalEntry::where('business_id', $business_id)->where('status', 'voided')->count(),
            'total_amount' => JournalEntry::where('business_id', $business_id)->where('status', 'posted')->sum('total_debit'),
        ];

        return view('bookkeeping.journal_entries.index', compact('entryTypes', 'statuses', 'stats'));
    }

    /**
     * Create journal entry form
     */
    public function createJournalEntry()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Pass full account objects so view can access id, account_code, and name
        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $contacts = Contact::contactDropdown($business_id, false, false);
        $entryTypes = JournalEntry::getEntryTypes();

        return view('bookkeeping.journal_entries.create', compact('accounts', 'contacts', 'entryTypes'));
    }

    /**
     * Store journal entry
     */
    public function storeJournalEntry(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'entry_date' => 'required',
            'entry_type' => 'required',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            DB::beginTransaction();

            // Calculate totals
            $totalDebit = collect($request->lines)->where('type', 'debit')->sum('amount');
            $totalCredit = collect($request->lines)->where('type', 'credit')->sum('amount');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Journal entry is not balanced. Total debits must equal total credits.',
                ], 422);
            }

            // Convert date from MM/DD/YYYY to YYYY-MM-DD if needed
            $entryDate = $request->entry_date;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $entryDate)) {
                $entryDate = \Carbon\Carbon::createFromFormat('m/d/Y', $entryDate)->format('Y-m-d');
            }

            $entry = JournalEntry::create([
                'business_id' => $business_id,
                'entry_number' => JournalEntry::generateEntryNumber($business_id),
                'entry_date' => $entryDate,
                'entry_type' => $request->entry_type,
                'status' => $request->status ?? 'draft',
                'memo' => $request->memo,
                'contact_id' => $request->contact_id,
                'source_document' => $request->source_document,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by' => auth()->user()->id,
            ]);

            // Create lines
            foreach ($request->lines as $index => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                    'contact_id' => $line['contact_id'] ?? null,
                    'reference' => $line['reference'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Journal entry created successfully.',
                'entry_id' => $entry->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error creating journal entry.',
            ], 500);
        }
    }

    /**
     * Show journal entry
     */
    public function showJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $entry = JournalEntry::where('business_id', $business_id)
            ->with(['lines.account', 'lines.contact', 'createdBy', 'approvedBy', 'postedBy', 'contact'])
            ->findOrFail($id);

        return view('bookkeeping.journal_entries.show', compact('entry'));
    }

    /**
     * Edit journal entry
     */
    public function editJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $entry = JournalEntry::where('business_id', $business_id)
            ->with('lines')
            ->findOrFail($id);

        if ($entry->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft entries can be edited.');
        }

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        $contacts = Contact::contactDropdown($business_id, false, false);
        $entryTypes = JournalEntry::getEntryTypes();

        return view('bookkeeping.journal_entries.edit', compact('entry', 'accounts', 'contacts', 'entryTypes'));
    }

    /**
     * Update journal entry
     */
    public function updateJournalEntry(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'entry_date' => 'required',
            'lines' => 'required|array|min:2',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $entry = JournalEntry::where('business_id', $business_id)
                ->findOrFail($id);

            if ($entry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Only draft entries can be edited.',
                ], 422);
            }

            DB::beginTransaction();

            // Calculate totals
            $totalDebit = collect($request->lines)->where('type', 'debit')->sum('amount');
            $totalCredit = collect($request->lines)->where('type', 'credit')->sum('amount');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Journal entry is not balanced.',
                ], 422);
            }

            // Handle date format conversion (MM/DD/YYYY to Y-m-d)
            $entryDate = $request->entry_date;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $entryDate)) {
                $entryDate = \Carbon\Carbon::createFromFormat('m/d/Y', $entryDate)->format('Y-m-d');
            }

            // Update entry details (keep as draft for now)
            $entry->update([
                'entry_date' => $entryDate,
                'entry_type' => $request->entry_type,
                'memo' => $request->memo,
                'contact_id' => $request->contact_id,
                'source_document' => $request->reference_number ?? $request->source_document,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // Delete existing lines and recreate
            $entry->lines()->delete();

            foreach ($request->lines as $index => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $line['amount'],
                    'description' => $line['description'] ?? null,
                    'contact_id' => $line['contact_id'] ?? null,
                    'reference' => $line['reference'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            // If status is 'posted', post the entry (this handles balance updates)
            if ($request->status === 'posted') {
                $entry->refresh(); // Reload the entry with fresh lines
                $entry->post(auth()->id());
                return response()->json([
                    'success' => true,
                    'msg' => 'Journal entry updated and posted successfully.',
                ]);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Journal entry updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error updating journal entry.',
            ], 500);
        }
    }

    /**
     * Post journal entry
     */
    public function postJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $entry = JournalEntry::where('business_id', $business_id)
                ->findOrFail($id);

            $entry->post(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Journal entry posted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error posting journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Void journal entry
     */
    public function voidJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.',
            ], 403);
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $entry = JournalEntry::where('business_id', $business_id)
                ->with('lines.account')
                ->findOrFail($id);

            if ($entry->status !== JournalEntry::STATUS_POSTED) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Only posted entries can be voided.',
                ], 400);
            }

            $entry->void(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Journal entry voided successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error voiding journal entry: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'msg' => 'Error voiding entry: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create reversing entry
     */
    public function reverseJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $entry = JournalEntry::where('business_id', $business_id)
                ->findOrFail($id);

            $reversingEntry = $entry->createReversingEntry(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Reversing entry created successfully.',
                'entry_id' => $reversingEntry->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating reversing entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Duplicate journal entry
     */
    public function duplicateJournalEntry($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $entry = JournalEntry::where('business_id', $business_id)
                ->with('lines')
                ->findOrFail($id);

            DB::beginTransaction();

            // Create new entry as draft
            $newEntry = JournalEntry::create([
                'business_id' => $business_id,
                'entry_number' => JournalEntry::generateEntryNumber($business_id),
                'entry_date' => now()->format('Y-m-d'),
                'entry_type' => $entry->entry_type,
                'status' => 'draft',
                'memo' => 'Copy of ' . $entry->entry_number . ($entry->memo ? ': ' . $entry->memo : ''),
                'contact_id' => $entry->contact_id,
                'source_document' => $entry->source_document,
                'total_debit' => $entry->total_debit,
                'total_credit' => $entry->total_credit,
                'created_by' => auth()->user()->id,
            ]);

            // Duplicate lines
            foreach ($entry->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $newEntry->id,
                    'account_id' => $line->account_id,
                    'type' => $line->type,
                    'amount' => $line->amount,
                    'description' => $line->description,
                    'contact_id' => $line->contact_id,
                    'reference' => $line->reference,
                    'sort_order' => $line->sort_order,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Journal entry duplicated successfully.',
                'entry_id' => $newEntry->id,
                'redirect' => route('bookkeeping.journal.edit', $newEntry->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error duplicating journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error duplicating journal entry.',
            ], 500);
        }
    }

    

    // ==================== BANK DEPOSITS ====================

    /**
     * List bank deposits
     */
    public function bankDeposits(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Return stats only
        if ($request->ajax() && $request->stats_only) {
            $stats = [
                'pending_count' => BankDeposit::where('business_id', $business_id)->where('status', 'pending')->count(),
                'deposited_count' => BankDeposit::where('business_id', $business_id)->where('status', 'deposited')->count(),
                'reconciled_count' => BankDeposit::where('business_id', $business_id)->where('status', 'reconciled')->count(),
                'total_amount' => BankDeposit::where('business_id', $business_id)
                    ->whereMonth('deposit_date', now()->month)
                    ->whereYear('deposit_date', now()->year)
                    ->whereIn('status', ['deposited', 'reconciled'])
                    ->sum('total_amount'),
            ];
            return response()->json(['stats' => $stats]);
        }

        if ($request->ajax()) {
            $deposits = BankDeposit::where('business_id', $business_id)
                ->with(['depositToAccount', 'createdBy'])
                ->orderBy('deposit_date', 'desc');

            if ($request->status) {
                $deposits->where('status', $request->status);
            }

            if ($request->account_id) {
                $deposits->where('deposit_to_account_id', $request->account_id);
            }

            if ($request->date_range) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    $deposits->whereBetween('deposit_date', [
                        \Carbon\Carbon::parse($dates[0])->startOfDay(),
                        \Carbon\Carbon::parse($dates[1])->endOfDay()
                    ]);
                }
            }

            return DataTables::of($deposits)
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('action', function ($row) {
                    return ''; // Actions rendered client-side
                })
                ->addColumn('view_url', function ($row) {
                    return route('bookkeeping.deposits.show', $row->id);
                })
                ->addColumn('process_url', function ($row) {
                    return route('bookkeeping.deposits.process', $row->id);
                })
                ->addColumn('void_url', function ($row) {
                    return route('bookkeeping.deposits.void', $row->id);
                })
                ->editColumn('deposit_date', function ($row) {
                    return $row->deposit_date->format('M d, Y');
                })
                ->editColumn('status', function ($row) {
                    return strtolower($row->status);
                })
                ->editColumn('total_amount', function ($row) {
                    return $row->total_amount;
                })
                ->addColumn('account_name', function ($row) {
                    return $row->depositToAccount ? $row->depositToAccount->name : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $statuses = BankDeposit::getStatuses();
        $bankAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->pluck('name', 'id');

        // Stats for the view
        $stats = [
            'pending' => BankDeposit::where('business_id', $business_id)->where('status', 'pending')->count(),
            'deposited' => BankDeposit::where('business_id', $business_id)->where('status', 'deposited')->count(),
            'reconciled' => BankDeposit::where('business_id', $business_id)->where('status', 'reconciled')->count(),
            'total_amount' => BankDeposit::where('business_id', $business_id)
                ->whereMonth('deposit_date', now()->month)
                ->whereYear('deposit_date', now()->year)
                ->whereIn('status', ['deposited', 'reconciled'])
                ->sum('total_amount'),
        ];

        return view('bookkeeping.bank_deposits.index', compact('statuses', 'bankAccounts', 'stats'));
    }

    /**
     * Create bank deposit form
     */
    public function createBankDeposit()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $bankAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->get(['id', 'name', 'current_balance']);

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        $contacts = Contact::contactDropdown($business_id, false, false);

        // Get undeposited payments
        $undepositedPayments = TransactionPayment::where('business_id', $business_id)
            ->doesntHave('child_payments')
            ->whereNull('bank_deposit_id')
            ->where('amount', '>', 0)
            ->with(['transaction.contact'])
            ->orderBy('paid_on', 'desc')
            ->get();

        return view('bookkeeping.bank_deposits.create', compact('bankAccounts', 'accounts', 'contacts', 'undepositedPayments'));
    }

    /**
     * Store bank deposit
     */
    public function storeBankDeposit(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'deposit_to_account_id' => 'required|exists:chart_of_accounts,id',
            'deposit_date' => 'required',
            'lines' => 'required|array|min:1',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            // Convert date from MM/DD/YYYY to YYYY-MM-DD if needed
            $depositDate = $request->deposit_date;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $depositDate)) {
                $depositDate = \Carbon\Carbon::createFromFormat('m/d/Y', $depositDate)->format('Y-m-d');
            }

            DB::beginTransaction();

            $deposit = BankDeposit::create([
                'business_id' => $business_id,
                'deposit_to_account_id' => $request->deposit_to_account_id,
                'deposit_date' => $depositDate,
                'deposit_number' => BankDeposit::generateDepositNumber($business_id),
                'memo' => $request->memo,
                'status' => 'pending',
                'created_by' => auth()->user()->id,
            ]);

            $totalAmount = 0;
            foreach ($request->lines as $line) {
                BankDepositLine::create([
                    'bank_deposit_id' => $deposit->id,
                    'contact_id' => $line['contact_id'] ?? null,
                    'account_id' => $line['account_id'],
                    'date' => $line['date'] ?? $request->deposit_date,
                    'type' => $line['type'] ?? 'other',
                    'transaction_payment_id' => $line['transaction_payment_id'] ?? null,
                    'payment_method' => $line['payment_method'] ?? null,
                    'memo' => $line['memo'] ?? null,
                    'ref_no' => $line['ref_no'] ?? null,
                    'amount' => $line['amount'],
                ]);
                $totalAmount += $line['amount'];
            }

            $deposit->total_amount = $totalAmount;
            $deposit->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Bank deposit created successfully.',
                'deposit_id' => $deposit->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating bank deposit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error creating bank deposit.',
            ], 500);
        }
    }

    /**
     * Show bank deposit
     */
    public function showBankDeposit($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $deposit = BankDeposit::where('business_id', $business_id)
            ->with(['depositToAccount', 'lines.account', 'lines.contact', 'journalEntry.lines', 'createdBy'])
            ->findOrFail($id);

        return view('bookkeeping.bank_deposits.show', compact('deposit'));
    }

    /**
     * Edit bank deposit
     */
    public function editBankDeposit($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $deposit = BankDeposit::where('business_id', $business_id)
            ->with('lines')
            ->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending deposits can be edited.');
        }

        $bankAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->get(['id', 'name', 'current_balance']);

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        $contacts = Contact::contactDropdown($business_id, false, false);

        return view('bookkeeping.bank_deposits.edit', compact('deposit', 'bankAccounts', 'accounts', 'contacts'));
    }

    /**
     * Process bank deposit
     */
    public function processBankDeposit($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $deposit = BankDeposit::where('business_id', $business_id)
                ->findOrFail($id);

            $deposit->processDeposit(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Bank deposit processed successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error processing bank deposit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Void bank deposit
     */
    public function voidBankDeposit($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $deposit = BankDeposit::where('business_id', $business_id)
                ->findOrFail($id);

            $deposit->voidDeposit(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Bank deposit voided successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error voiding bank deposit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    // ==================== LIABILITIES ====================

    /**
     * List liabilities
     */
    public function liabilities(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $liabilities = BusinessLiability::where('business_id', $business_id)
                ->with(['liabilityAccount', 'contact', 'createdBy']);

            if ($request->status) {
                $liabilities->where('status', $request->status);
            }

            if ($request->liability_type) {
                $liabilities->where('liability_type', $request->liability_type);
            }

            return DataTables::of($liabilities)
                ->addColumn('action', function ($row) {
                    return ''; // Actions rendered client-side
                })
                ->addColumn('view_url', function ($row) {
                    return route('bookkeeping.liabilities.show', $row->id);
                })
                ->addColumn('edit_url', function ($row) {
                    return route('bookkeeping.liabilities.edit', $row->id);
                })
                ->addColumn('pay_url', function ($row) {
                    return route('bookkeeping.liabilities.payment.create', $row->id);
                })
                ->editColumn('status', function ($row) {
                    // Check if overdue
                    if ($row->isOverdue() && $row->status === 'active') {
                        return 'overdue';
                    }
                    return $row->status;
                })
                ->editColumn('liability_type', function ($row) {
                    return $row->liability_type;
                })
                ->editColumn('original_amount', function ($row) {
                    return $row->original_amount;
                })
                ->editColumn('current_balance', function ($row) {
                    return $row->current_balance;
                })
                ->addColumn('contact_name', function ($row) {
                    return $row->contact ? $row->contact->name : null;
                })
                ->addColumn('description', function ($row) {
                    return $row->description;
                })
                ->editColumn('due_date', function ($row) {
                    return $row->due_date ? $row->due_date->format('Y-m-d') : null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $liabilityTypes = BusinessLiability::getLiabilityTypes();
        $statuses = BusinessLiability::getStatuses();

        // Summary stats - Total Active Liabilities
        $totalLiabilities = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->sum('current_balance');

        // Overdue Amount
        $overdueAmount = BusinessLiability::where('business_id', $business_id)
            ->overdue()
            ->sum('current_balance');

        // Total Paid This Month
        $totalPaid = LiabilityPayment::whereHas('liability', function($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('total_amount');

        // Active Items Count
        $activeCount = BusinessLiability::where('business_id', $business_id)
            ->where('status', 'active')
            ->count();

        return view('bookkeeping.liabilities.index', compact('liabilityTypes', 'statuses', 'totalLiabilities', 'overdueAmount', 'totalPaid', 'activeCount'));
    }

    /**
     * Create liability form
     */
    public function createLiability()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $liabilityAccounts = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->pluck('name', 'id');

        $contacts = Contact::contactDropdown($business_id, false, false);
        $liabilityTypes = BusinessLiability::getLiabilityTypes();
        $paymentFrequencies = BusinessLiability::getPaymentFrequencies();

        return view('bookkeeping.liabilities.create', compact('liabilityAccounts', 'contacts', 'liabilityTypes', 'paymentFrequencies'));
    }

    /**
     * Store liability
     */
    public function storeLiability(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'liability_type' => 'required',
            'liability_account_id' => 'required|exists:chart_of_accounts,id',
            'original_amount' => 'required|numeric|min:0',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $liability = BusinessLiability::create([
                'business_id' => $business_id,
                'name' => $request->name,
                'liability_type' => $request->liability_type,
                'liability_account_id' => $request->liability_account_id,
                'description' => $request->description,
                'original_amount' => $request->original_amount,
                'current_balance' => $request->original_amount,
                'interest_rate' => $request->interest_rate,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'contact_id' => $request->contact_id,
                'payment_frequency' => $request->payment_frequency,
                'payment_amount' => $request->payment_amount,
                'status' => 'active',
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Liability created successfully.',
                'liability_id' => $liability->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating liability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error creating liability.',
            ], 500);
        }
    }

    /**
     * Show liability details
     */
    public function showLiability($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $liability = BusinessLiability::where('business_id', $business_id)
            ->with(['liabilityAccount', 'contact', 'payments.fromAccount', 'createdBy'])
            ->findOrFail($id);

        return view('bookkeeping.liabilities.show', compact('liability'));
    }

    /**
     * Edit liability
     */
    public function editLiability($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $liability = BusinessLiability::where('business_id', $business_id)
            ->findOrFail($id);

        $liabilityAccounts = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->pluck('name', 'id');

        $contacts = Contact::contactDropdown($business_id, false, false);
        $liabilityTypes = BusinessLiability::getLiabilityTypes();
        $paymentFrequencies = BusinessLiability::getPaymentFrequencies();

        return view('bookkeeping.liabilities.edit', compact('liability', 'liabilityAccounts', 'contacts', 'liabilityTypes', 'paymentFrequencies'));
    }

    /**
     * Update liability
     */
    public function updateLiability(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'original_amount' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $liability = BusinessLiability::where('business_id', $business_id)
                ->findOrFail($id);

            // Handle date format conversion (MM/DD/YYYY to Y-m-d)
            $startDate = $request->start_date;
            if ($startDate && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $startDate)) {
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $startDate)->format('Y-m-d');
            }

            $dueDate = $request->due_date;
            if ($dueDate && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dueDate)) {
                $dueDate = \Carbon\Carbon::createFromFormat('m/d/Y', $dueDate)->format('Y-m-d');
            }

            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'interest_rate' => $request->interest_rate,
                'contact_id' => $request->contact_id,
                'payment_frequency' => $request->payment_frequency,
                'payment_amount' => $request->payment_amount,
                'liability_account_id' => $request->liability_account_id,
                'status' => $request->status,
                'reference_number' => $request->reference_number,
                'start_date' => $startDate,
                'due_date' => $dueDate,
            ];

            if (!is_null($request->original_amount)) {
                $updateData['original_amount'] = $request->original_amount;
            }

            if (!is_null($request->current_balance)) {
                $updateData['current_balance'] = $request->current_balance;
            }

            $liability->update($updateData);

            return response()->json([
                'success' => true,
                'msg' => 'Liability updated successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating liability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error updating liability.',
            ], 500);
        }
    }

    /**
     * Create liability payment form
     */
    public function createLiabilityPayment($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $liability = BusinessLiability::where('business_id', $business_id)
            ->findOrFail($id);

        $bankAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->pluck('name', 'id');

        return view('bookkeeping.liabilities.payment', compact('liability', 'bankAccounts'));
    }

    /**
     * Store liability payment
     */
    public function storeLiabilityPayment(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'from_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $liability = BusinessLiability::where('business_id', $business_id)
                ->findOrFail($id);

            $paymentData = [
                'payment_date' => $request->payment_date,
                'principal_amount' => $request->principal_amount ?? $request->total_amount,
                'interest_amount' => $request->interest_amount ?? 0,
                'payment_method' => $request->payment_method,
                'from_account_id' => $request->from_account_id,
                'notes' => $request->notes,
                'reference' => $request->reference,
            ];

            $payment = $liability->makePayment($request->total_amount, $paymentData, auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Payment recorded successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating liability payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    // ==================== REPORTS ====================

    /**
     * Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $normalBalance = ChartOfAccount::getNormalBalance($account->account_type);
            if ($normalBalance === 'debit') {
                $totalDebits += abs($account->current_balance);
            } else {
                $totalCredits += abs($account->current_balance);
            }
        }

        return view('bookkeeping.reports.trial_balance', compact('accounts', 'totalDebits', 'totalCredits'));
    }

    /**
     * Balance Sheet Report
     */
    public function balanceSheet(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get business locations for the filter dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $assets = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $liabilities = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'liability')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $equity = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'equity')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $totalEquity = $equity->sum('current_balance');

        return view('bookkeeping.reports.balance_sheet', compact(
            'business_locations', 'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }

    /**
     * Income Statement Report
     */
    public function incomeStatement(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        // Get income accounts from Chart of Accounts
        $income = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        // Get COGS accounts
        $cogs = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'cost_of_goods_sold')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        // Get expense accounts
        $expenses = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        // Get manual P&L transactions (income entries)
        $manualIncomeTransactions = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'income')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with('account')
            ->get();

        // Get manual P&L transactions (expense entries)
        $manualExpenseTransactions = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with('account')
            ->get();

        // Calculate totals from Chart of Accounts
        $totalIncome = $income->sum('current_balance');
        $totalCogs = $cogs->sum('current_balance');
        $totalExpenses = $expenses->sum('current_balance');
        
        // Add manual P&L transaction totals (these are already reflected in COA balances via journal entries)
        // But we track them separately for reporting breakdown
        $manualIncomeTotal = $manualIncomeTransactions->sum('amount');
        $manualExpenseTotal = $manualExpenseTransactions->sum('amount');
        
        $grossProfit = $totalIncome - $totalCogs;
        $netIncome = $grossProfit - $totalExpenses;

        // Group manual transactions by category for detailed breakdown
        $incomeByCategory = $manualIncomeTransactions->groupBy('category')->map(function ($items) {
            return $items->sum('amount');
        });

        $expensesByCategory = $manualExpenseTransactions->groupBy('category')->map(function ($items) {
            return $items->sum('amount');
        });

        return view('bookkeeping.reports.income_statement', compact(
            'income', 'cogs', 'expenses',
            'totalIncome', 'totalCogs', 'totalExpenses',
            'grossProfit', 'netIncome', 'startDate', 'endDate',
            'manualIncomeTransactions', 'manualExpenseTransactions',
            'manualIncomeTotal', 'manualExpenseTotal',
            'incomeByCategory', 'expensesByCategory'
        ));
    }

    // ==================== PARTNER TRANSACTIONS ====================

    /**
     * List partner transactions (business partner personal assets, loans, advances, capital)
     */
    public function partnerTransactions(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $transactions = PartnerTransaction::where('business_id', $business_id)
                ->with(['partner', 'account', 'createdBy'])
                ->orderBy('transaction_date', 'desc');

            if ($request->transaction_type) {
                $transactions->where('transaction_type', $request->transaction_type);
            }

            if ($request->partner_id) {
                $transactions->where('partner_id', $request->partner_id);
            }

            return DataTables::of($transactions)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group dropdown">
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info dropdown-toggle" 
                            data-toggle="dropdown">Actions <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">';
                    
                    $html .= '<li><a href="' . action([\App\Http\Controllers\BookkeepingController::class, 'showPartnerTransaction'], [$row->id]) . '">
                        <i class="fa fa-eye"></i> View</a></li>';
                    $html .= '<li><a href="' . action([\App\Http\Controllers\BookkeepingController::class, 'editPartnerTransaction'], [$row->id]) . '">
                        <i class="fa fa-edit"></i> Edit</a></li>';
                    
                    $html .= '</ul></div>';
                    
                    return $html;
                })
                ->editColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('Y-m-d');
                })
                ->addColumn('transaction_type_raw', function ($row) {
                    return $row->transaction_type ?: '';
                })
                ->addColumn('amount_raw', function ($row) {
                    return (float) $row->amount;
                })
                ->addColumn('partner_name', function ($row) {
                    return $row->partner ? $row->partner->first_name . ' ' . $row->partner->last_name : '-';
                })
                ->addColumn('account_name', function ($row) {
                    return $row->account ? $row->account->name : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $transactionTypes = PartnerTransaction::getTransactionTypes();
        $partners = User::where('business_id', $business_id)
            ->get(['id', 'first_name', 'last_name'])
            ->pluck('full_name', 'id');

        // Summary stats
        $totalCapital = PartnerTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'capital_contribution')
            ->sum('amount');

        $totalDrawings = PartnerTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'owner_drawing')
            ->sum('amount');

        $totalLoansFromPartners = PartnerTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'loan_from_partner')
            ->sum('amount');

        $totalAdvances = PartnerTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'advance')
            ->sum('amount');

        return view('bookkeeping.partner_transactions.index', compact(
            'transactionTypes', 'partners', 'totalCapital', 'totalDrawings', 'totalLoansFromPartners', 'totalAdvances'
        ));
    }

    /**
     * Create partner transaction form
     */
    public function createPartnerTransaction()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $partners = User::where('business_id', $business_id)
            ->get(['id', 'first_name', 'last_name'])
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->first_name . ' ' . $item->last_name];
            });

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        $transactionTypes = PartnerTransaction::getTransactionTypes();

        return view('bookkeeping.partner_transactions.create', compact('partners', 'accounts', 'transactionTypes'));
    }

    /**
     * Store partner transaction
     */
    public function storePartnerTransaction(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'partner_id' => 'required|exists:users,id',
            'transaction_type' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            DB::beginTransaction();

            $transaction = PartnerTransaction::create([
                'business_id' => $business_id,
                'partner_id' => $request->partner_id,
                'transaction_type' => $request->transaction_type,
                'amount' => $request->amount,
                'transaction_date' => $request->transaction_date,
                'account_id' => $request->account_id,
                'description' => $request->description,
                'reference' => $request->reference,
                'created_by' => auth()->user()->id,
            ]);

            // Create corresponding journal entry
            $transaction->createJournalEntry(auth()->user()->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Partner transaction recorded successfully.',
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating partner transaction: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'msg' => 'Error recording partner transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show partner transaction
     */
    public function showPartnerTransaction($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = PartnerTransaction::where('business_id', $business_id)
            ->with(['partner', 'account', 'journalEntry.lines', 'createdBy'])
            ->findOrFail($id);

        return view('bookkeeping.partner_transactions.show', compact('transaction'));
    }

    /**
     * Edit partner transaction form
     */
    public function editPartnerTransaction($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = PartnerTransaction::where('business_id', $business_id)
            ->findOrFail($id);

        $partners = User::where('business_id', $business_id)
            ->get(['id', 'first_name', 'last_name'])
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->first_name . ' ' . $item->last_name];
            });

        $accounts = ChartOfAccount::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        $transactionTypes = PartnerTransaction::getTransactionTypes();

        return view('bookkeeping.partner_transactions.edit', compact('transaction', 'partners', 'accounts', 'transactionTypes'));
    }

    /**
     * Update partner transaction
     */
    public function updatePartnerTransaction(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = PartnerTransaction::where('business_id', $business_id)
                ->findOrFail($id);

            $transaction->update([
                'description' => $request->description,
                'reference' => $request->reference,
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Partner transaction updated successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating partner transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error updating partner transaction.',
            ], 500);
        }
    }

    /**
     * Delete partner transaction
     */
    public function destroyPartnerTransaction($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = PartnerTransaction::where('business_id', $business_id)
                ->findOrFail($id);

            // Delete associated journal entry if exists
            if ($transaction->journal_entry_id) {
                JournalEntry::where('id', $transaction->journal_entry_id)->delete();
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Partner transaction deleted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting partner transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error deleting partner transaction.',
            ], 500);
        }
    }

    // ==================== INVENTORY VALUATION ====================

    /**
     * Display inventory valuation
     */
    public function inventoryValuation(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get the latest valuation
        $latestValuation = InventoryValuation::where('business_id', $business_id)
            ->orderBy('valuation_date', 'desc')
            ->first();

        // Get valuation history
        $valuationHistory = InventoryValuation::where('business_id', $business_id)
            ->orderBy('valuation_date', 'desc')
            ->limit(12)
            ->get();

        // Get current stock value from products
        $stockSummary = DB::table('products')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
            ->where('products.business_id', $business_id)
            ->where('products.is_inactive', 0)
            ->where('variation_location_details.qty_available', '>', 0)
            ->select(
                DB::raw('SUM(variation_location_details.qty_available) as total_units'),
                DB::raw('SUM(variation_location_details.qty_available * variations.dpp_inc_tax) as total_cost_value'),
                DB::raw('SUM(variation_location_details.qty_available * variations.sell_price_inc_tax) as total_retail_value')
            )
            ->first();

        // Get stock by category for breakdown
        $stockByCategory = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
            ->where('products.business_id', $business_id)
            ->where('products.is_inactive', 0)
            ->where('variation_location_details.qty_available', '>', 0)
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'categories.id as category_id',
                DB::raw("COALESCE(categories.name, 'Unassigned') as category_name"),
                DB::raw('SUM(variation_location_details.qty_available) as total_units'),
                DB::raw('SUM(variation_location_details.qty_available * variations.dpp_inc_tax) as total_cost_value'),
                DB::raw('SUM(variation_location_details.qty_available * variations.sell_price_inc_tax) as total_retail_value')
            )
            ->orderBy('total_cost_value', 'desc')
            ->get();

        // Get stock by brand for breakdown
        $stockByBrand = DB::table('products')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
            ->where('products.business_id', $business_id)
            ->where('products.is_inactive', 0)
            ->where('variation_location_details.qty_available', '>', 0)
            ->groupBy('brands.id', 'brands.name')
            ->select(
                'brands.id as brand_id',
                DB::raw("COALESCE(brands.name, 'Unassigned') as brand_name"),
                DB::raw('SUM(variation_location_details.qty_available) as total_units'),
                DB::raw('SUM(variation_location_details.qty_available * variations.dpp_inc_tax) as total_cost_value'),
                DB::raw('SUM(variation_location_details.qty_available * variations.sell_price_inc_tax) as total_retail_value')
            )
            ->orderBy('total_cost_value', 'desc')
            ->get();

        // Get top selling products (last 30 days)
        $topSellingProducts = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('t.transaction_date', '>=', now()->subDays(30))
            ->groupBy('p.id', 'p.name', 'c.name', 'b.name')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                DB::raw("COALESCE(c.name, 'Unassigned') as category_name"),
                DB::raw("COALESCE(b.name, 'Unassigned') as brand_name"),
                DB::raw('SUM(tsl.quantity) as total_quantity_sold'),
                DB::raw('SUM(tsl.quantity * tsl.unit_price_inc_tax) as total_revenue')
            )
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Get categories for filter dropdown
        $categories = \App\Category::where('business_id', $business_id)
            ->where('category_type', 'product')
            ->orderBy('name')
            ->pluck('name', 'id');

        // Get brands for filter dropdown
        $brands = \App\Brands::where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'id');

        // Get detailed inventory breakdown (for report table)
        $inventoryBreakdown = DB::table('products as p')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->join('variations as v', 'p.id', '=', 'v.product_id')
            ->join('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
            ->where('p.business_id', $business_id)
            ->where('p.is_inactive', 0)
            ->where('vld.qty_available', '>', 0)
            ->groupBy('p.id', 'p.name', 'p.sku', 'c.id', 'c.name', 'b.id', 'b.name')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'c.id as category_id',
                DB::raw("COALESCE(c.name, 'Unassigned') as category_name"),
                'b.id as brand_id',
                DB::raw("COALESCE(b.name, 'Unassigned') as brand_name"),
                DB::raw('SUM(vld.qty_available) as total_units'),
                DB::raw('SUM(vld.qty_available * v.dpp_inc_tax) as total_cost_value'),
                DB::raw('SUM(vld.qty_available * v.sell_price_inc_tax) as total_retail_value')
            )
            ->orderBy('p.name')
            ->get();

        return view('bookkeeping.inventory_valuation.index', compact(
            'latestValuation', 'valuationHistory', 'stockSummary', 'stockByCategory',
            'stockByBrand', 'topSellingProducts', 'categories', 'brands', 'inventoryBreakdown'
        ));
    }

    /**
     * Calculate and store inventory valuation
     */
    public function calculateInventoryValuation(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            // Calculate current inventory value
            $stockSummary = DB::table('products')
                ->join('variations', 'products.id', '=', 'variations.product_id')
                ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.is_inactive', 0)
                ->where('variation_location_details.qty_available', '>', 0)
                ->select(
                    DB::raw('SUM(variation_location_details.qty_available) as total_units'),
                    DB::raw('SUM(variation_location_details.qty_available * variations.dpp_inc_tax) as total_cost_value'),
                    DB::raw('SUM(variation_location_details.qty_available * variations.sell_price_inc_tax) as total_retail_value')
                )
                ->first();

            // Create valuation record
            $valuation = InventoryValuation::create([
                'business_id' => $business_id,
                'valuation_date' => now(),
                'valuation_method' => $request->valuation_method ?? 'fifo',
                'total_units' => $stockSummary->total_units ?? 0,
                'total_cost_value' => $stockSummary->total_cost_value ?? 0,
                'total_retail_value' => $stockSummary->total_retail_value ?? 0,
                'notes' => $request->notes,
                'created_by' => auth()->user()->id,
            ]);

            // Optionally create journal entry for inventory adjustment
            if ($request->create_journal_entry) {
                $valuation->createJournalEntry(auth()->user()->id);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Inventory valuation calculated successfully.',
                'valuation' => $valuation,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error calculating inventory valuation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error calculating inventory valuation.',
            ], 500);
        }
    }

    /**
     * Get inventory valuation history
     */
    public function inventoryValuationHistory(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $valuations = InventoryValuation::where('business_id', $business_id)
                ->with('createdBy')
                ->orderBy('valuation_date', 'desc');

            return DataTables::of($valuations)
                ->editColumn('valuation_date', function ($row) {
                    return $row->valuation_date->format('Y-m-d H:i');
                })
                ->editColumn('total_cost_value', function ($row) {
                    return '$' . number_format($row->total_cost_value, 2);
                })
                ->editColumn('total_retail_value', function ($row) {
                    return '$' . number_format($row->total_retail_value, 2);
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->createdBy ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name : '-';
                })
                ->make(true);
        }

        return view('bookkeeping.inventory_valuation.history');
    }

    // ==================== PROFIT & LOSS TRANSACTIONS ====================

    /**
     * List all P&L transactions (income and expenses)
     */
    public function plTransactions(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $transactions = \App\Models\PLTransaction::where('business_id', $business_id)
                ->with(['contact', 'account', 'paymentAccount', 'createdBy'])
                ->orderBy('transaction_date', 'desc');

            if ($request->transaction_type) {
                $transactions->where('transaction_type', $request->transaction_type);
            }

            if ($request->status) {
                $transactions->where('status', $request->status);
            }

            if ($request->contact_id) {
                $transactions->where('contact_id', $request->contact_id);
            }

            if ($request->start_date && $request->end_date) {
                $transactions->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }

            return DataTables::of($transactions)
                ->addColumn('action', function ($row) {
                    return '';
                })
                ->addColumn('view_url', function ($row) {
                    return route('bookkeeping.pl.show', $row->id);
                })
                ->addColumn('void_url', function ($row) {
                    return route('bookkeeping.pl.void', $row->id);
                })
                ->editColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('M d, Y');
                })
                ->editColumn('amount', function ($row) {
                    return $row->amount;
                })
                ->addColumn('contact_name', function ($row) {
                    return $row->contact ? $row->contact->name : '-';
                })
                ->addColumn('account_name', function ($row) {
                    return $row->account ? $row->account->name : '-';
                })
                ->addColumn('category_label', function ($row) {
                    return $row->category_label;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Summary stats
        $totalIncome = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'income')
            ->where('status', 'posted')
            ->sum('amount');

        $totalExpenses = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'posted')
            ->sum('amount');

        $incomeCount = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'income')
            ->where('status', 'posted')
            ->count();

        $expenseCount = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'posted')
            ->count();

        return view('bookkeeping.pl_transactions.index', compact(
            'totalIncome', 'totalExpenses', 'incomeCount', 'expenseCount'
        ));
    }

    /**
     * Show form for creating income transaction
     */
    public function createIncome()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get income accounts
        $incomeAccounts = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'income')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        // Get bank/cash accounts for payment
        $paymentAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        // Get customers for integration
        $customers = Contact::customersDropdown($business_id, false, true);

        $categories = \App\Models\PLTransaction::getIncomeCategories();

        return view('bookkeeping.pl_transactions.create_income', compact(
            'incomeAccounts', 'paymentAccounts', 'customers', 'categories'
        ));
    }

    /**
     * Show form for creating expense transaction
     */
    public function createExpense()
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get expense accounts
        $expenseAccounts = ChartOfAccount::where('business_id', $business_id)
            ->where('account_type', 'expense')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        // Get bank/cash accounts for payment
        $paymentAccounts = ChartOfAccount::where('business_id', $business_id)
            ->bankAccounts()
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->account_code . ' - ' . $item->name];
            });

        // Get vendors/suppliers for integration
        $vendors = Contact::suppliersDropdown($business_id, false, true);

        $categories = \App\Models\PLTransaction::getExpenseCategories();

        return view('bookkeeping.pl_transactions.create_expense', compact(
            'expenseAccounts', 'paymentAccounts', 'vendors', 'categories'
        ));
    }

    /**
     * Store income transaction
     */
    public function storeIncome(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'payment_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            DB::beginTransaction();

            $transaction = \App\Models\PLTransaction::create([
                'business_id' => $business_id,
                'reference_number' => \App\Models\PLTransaction::generateReferenceNumber($business_id, 'income'),
                'transaction_type' => 'income',
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description,
                'account_id' => $request->account_id,
                'payment_account_id' => $request->payment_account_id,
                'contact_id' => $request->contact_id,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'invoice_number' => $request->invoice_number,
                'status' => 'draft',
                'created_by' => auth()->user()->id,
            ]);

            // Create journal entry and post
            $transaction->createJournalEntry(auth()->user()->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Income transaction recorded successfully.',
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating income transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error recording income transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store expense transaction
     */
    public function storeExpense(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'payment_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            DB::beginTransaction();

            $transaction = \App\Models\PLTransaction::create([
                'business_id' => $business_id,
                'reference_number' => \App\Models\PLTransaction::generateReferenceNumber($business_id, 'expense'),
                'transaction_type' => 'expense',
                'category' => $request->category,
                'transaction_date' => $request->transaction_date,
                'amount' => $request->amount,
                'description' => $request->description,
                'account_id' => $request->account_id,
                'payment_account_id' => $request->payment_account_id,
                'contact_id' => $request->contact_id,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'bill_number' => $request->bill_number,
                'status' => 'draft',
                'created_by' => auth()->user()->id,
            ]);

            // Create journal entry and post
            $transaction->createJournalEntry(auth()->user()->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Expense transaction recorded successfully.',
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating expense transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error recording expense transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show P&L transaction details
     */
    public function showPLTransaction($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = \App\Models\PLTransaction::where('business_id', $business_id)
            ->with(['contact', 'account', 'paymentAccount', 'journalEntry.lines', 'createdBy'])
            ->findOrFail($id);

        return view('bookkeeping.pl_transactions.show', compact('transaction'));
    }

    /**
     * Void P&L transaction
     */
    public function voidPLTransaction($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = \App\Models\PLTransaction::where('business_id', $business_id)
                ->findOrFail($id);

            $transaction->voidTransaction(auth()->user()->id);

            return response()->json([
                'success' => true,
                'msg' => 'Transaction voided successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error voiding P&L transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error voiding transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get P&L summary for dashboard/reports
     */
    public function plSummary(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        // Get manual P&L transactions
        $manualIncome = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'income')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $manualExpenses = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Income by category
        $incomeByCategory = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'income')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Expenses by category
        $expensesByCategory = \App\Models\PLTransaction::where('business_id', $business_id)
            ->where('transaction_type', 'expense')
            ->where('status', 'posted')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json([
            'manual_income' => $manualIncome,
            'manual_expenses' => $manualExpenses,
            'net_from_manual' => $manualIncome - $manualExpenses,
            'income_by_category' => $incomeByCategory,
            'expenses_by_category' => $expensesByCategory,
        ]);
    }

    /**
     * Accounts Receivable Page
     * Displays customer balances integrated with bookkeeping following US GAAP/IFRS
     */
    public function accountsReceivable(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get AR Account from Chart of Accounts
        $arAccount = ChartOfAccount::where('business_id', $business_id)
            ->where('detail_type', 'accounts_receivable')
            ->first();

        // Create AR account if it doesn't exist
        if (!$arAccount) {
            $arAccount = ChartOfAccount::create([
                'business_id' => $business_id,
                'account_code' => '1100',
                'name' => 'Accounts Receivable',
                'full_name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'detail_type' => 'accounts_receivable',
                'is_system_account' => true,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        }

        // DETAILED CUSTOMER AR CALCULATION
        $customersQuery = DB::table('contacts')
            ->leftJoin('transactions as t', function($join) use ($business_id) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.business_id', '=', $business_id);
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both'])
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.mobile',
                'contacts.email',
                'contacts.contact_id as customer_code',
                'contacts.created_at as customer_since',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', t.final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', t.final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', t.final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                DB::raw("SUM(IF(t.type = 'ledger_discount' AND t.sub_type='sell_discount', t.final_total, 0)) as ledger_discount"),
                // Aging calculations
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final' AND DATEDIFF(NOW(), t.transaction_date) <= 30, 
                    t.final_total - COALESCE((SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as current_0_30"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final' AND DATEDIFF(NOW(), t.transaction_date) > 30 AND DATEDIFF(NOW(), t.transaction_date) <= 60, 
                    t.final_total - COALESCE((SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as days_31_60"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final' AND DATEDIFF(NOW(), t.transaction_date) > 60 AND DATEDIFF(NOW(), t.transaction_date) <= 90, 
                    t.final_total - COALESCE((SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as days_61_90"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final' AND DATEDIFF(NOW(), t.transaction_date) > 90, 
                    t.final_total - COALESCE((SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as over_90")
            )
            ->groupBy('contacts.id', 'contacts.name', 'contacts.supplier_business_name', 
                'contacts.mobile', 'contacts.email', 'contacts.contact_id', 'contacts.created_at');

        // Get credit notes per customer (approved/applied credit notes reduce balance)
        $creditNotesByCustomer = \App\Models\CreditNote::where('business_id', $business_id)
            ->whereIn('status', ['approved', 'applied', 'partially_applied'])
            ->selectRaw('contact_id, SUM(amount) as total_credit_notes')
            ->groupBy('contact_id')
            ->pluck('total_credit_notes', 'contact_id');

        $customers = $customersQuery->get()->map(function($customer) use ($creditNotesByCustomer) {
            $invoiceDue = $customer->total_invoice - $customer->invoice_received - $customer->ledger_discount;
            $returnDue = $customer->total_sell_return - $customer->sell_return_paid;
            $openingDue = $customer->opening_balance - $customer->opening_balance_paid;
            
            // Get credit notes amount for this customer
            $creditNotesAmount = $creditNotesByCustomer[$customer->id] ?? 0;
            
            // Balance = Invoices Due - Returns - Credit Notes + Opening Balance Due
            $customer->balance_due = $invoiceDue - $returnDue + $openingDue - $creditNotesAmount;
            $customer->credit_notes_total = $creditNotesAmount;
            
            $customer->display_name = !empty($customer->supplier_business_name) 
                ? $customer->supplier_business_name 
                : $customer->name;
            
            $customer->current_0_30 = max(0, $customer->current_0_30 ?? 0);
            $customer->days_31_60 = max(0, $customer->days_31_60 ?? 0);
            $customer->days_61_90 = max(0, $customer->days_61_90 ?? 0);
            $customer->over_90 = max(0, $customer->over_90 ?? 0);
            
            return $customer;
        })->filter(function($customer) {
            return $customer->balance_due > 0.01;
        })->sortByDesc('balance_due')->values();

        // Get total credit notes for summary
        $totalCreditNotes = \App\Models\CreditNote::where('business_id', $business_id)
            ->whereIn('status', ['approved', 'applied', 'partially_applied'])
            ->sum('amount');

        // Calculate summary totals
        $summary = [
            'total_ar' => $customers->sum('balance_due'),
            'total_customers' => $customers->count(),
            'current_0_30' => $customers->sum('current_0_30'),
            'days_31_60' => $customers->sum('days_31_60'),
            'days_61_90' => $customers->sum('days_61_90'),
            'over_90' => $customers->sum('over_90'),
            'opening_balance_due' => $customers->sum(function($c) {
                return $c->opening_balance - $c->opening_balance_paid;
            }),
            'total_credit_notes' => $totalCreditNotes,
        ];

        // Get recent payments received (last 30 days)
        $recentPayments = DB::table('transaction_payments as tp')
            ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('tp.paid_on', '>=', now()->subDays(30))
            ->select(
                'tp.id', 'tp.amount', 'tp.method', 'tp.paid_on', 'tp.payment_ref_no',
                't.invoice_no', 'c.name as customer_name', 'c.supplier_business_name'
            )
            ->orderBy('tp.paid_on', 'desc')
            ->limit(10)
            ->get()
            ->map(function($payment) {
                $payment->display_name = !empty($payment->supplier_business_name) 
                    ? $payment->supplier_business_name 
                    : $payment->customer_name;
                return $payment;
            });

        // Get top outstanding invoices
        $topOutstandingInvoices = DB::table('transactions as t')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1*amount, amount)) as paid FROM transaction_payments GROUP BY transaction_id) as tp'), 
                't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereRaw('t.final_total - COALESCE(tp.paid, 0) > 0.01')
            ->select(
                't.id', 't.invoice_no', 't.transaction_date', 't.final_total',
                DB::raw('COALESCE(tp.paid, 0) as paid'),
                DB::raw('t.final_total - COALESCE(tp.paid, 0) as balance'),
                DB::raw('DATEDIFF(NOW(), t.transaction_date) as days_outstanding'),
                'c.name as customer_name', 'c.supplier_business_name'
            )
            ->orderBy('balance', 'desc')
            ->limit(10)
            ->get()
            ->map(function($invoice) {
                $invoice->display_name = !empty($invoice->supplier_business_name) 
                    ? $invoice->supplier_business_name 
                    : $invoice->customer_name;
                return $invoice;
            });

        // Update AR Account balance
        if ($arAccount) {
            $arAccount->current_balance = $summary['total_ar'];
            $arAccount->save();
        }

        // Get AR-related Journal Entries
        $arJournalEntries = collect([]);
        if ($arAccount) {
            $arJournalEntries = JournalEntry::where('business_id', $business_id)
                ->where('status', JournalEntry::STATUS_POSTED)
                ->whereHas('lines', function($q) use ($arAccount) {
                    $q->where('account_id', $arAccount->id);
                })
                ->with(['lines' => function($q) use ($arAccount) {
                    $q->where('account_id', $arAccount->id);
                }, 'contact'])
                ->orderBy('entry_date', 'desc')
                ->limit(20)
                ->get()
                ->map(function($entry) {
                    $arLine = $entry->lines->first();
                    // Handle both field structures (type/amount vs debit_amount/credit_amount)
                    if ($arLine) {
                        if (isset($arLine->type)) {
                            $entry->ar_debit = $arLine->type === 'debit' ? $arLine->amount : 0;
                            $entry->ar_credit = $arLine->type === 'credit' ? $arLine->amount : 0;
                        } else {
                            $entry->ar_debit = $arLine->debit_amount ?? 0;
                            $entry->ar_credit = $arLine->credit_amount ?? 0;
                        }
                    } else {
                        $entry->ar_debit = 0;
                        $entry->ar_credit = 0;
                    }
                    $entry->contact_name = $entry->contact ? $entry->contact->name : 'N/A';
                    return $entry;
                });
        }

        $arJournalSummary = [
            'total_debits' => $arJournalEntries->sum('ar_debit'),
            'total_credits' => $arJournalEntries->sum('ar_credit'),
            'entry_count' => $arJournalEntries->count(),
        ];

        return view('bookkeeping.accounts_receivable.index', compact(
            'customers', 'summary', 'recentPayments', 'topOutstandingInvoices',
            'arAccount', 'arJournalEntries', 'arJournalSummary'
        ));
    }

    /**
     * Accounts Payable Page
     * Displays vendor balances integrated with bookkeeping following US GAAP/IFRS
     */
    public function accountsPayable(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get AP Account from Chart of Accounts
        $apAccount = ChartOfAccount::where('business_id', $business_id)
            ->where('detail_type', 'accounts_payable')
            ->first();

        // Create AP account if it doesn't exist
        if (!$apAccount) {
            $apAccount = ChartOfAccount::create([
                'business_id' => $business_id,
                'account_code' => '2000',
                'name' => 'Accounts Payable',
                'full_name' => 'Accounts Payable',
                'account_type' => 'liability',
                'detail_type' => 'accounts_payable',
                'is_system_account' => true,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        }

        // DETAILED VENDOR AP CALCULATION (matching the supplier list logic)
        $vendorsQuery = DB::table('contacts')
            ->leftJoin('transactions as t', function($join) use ($business_id) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.business_id', '=', $business_id);
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['supplier', 'both'])
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.mobile',
                'contacts.email',
                'contacts.contact_id as vendor_code',
                'contacts.created_at as vendor_since',
                'contacts.pay_term_number',
                'contacts.pay_term_type',
                // Purchase totals (following ContactUtil logic)
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void', t.final_total, 0)) as total_purchase"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                // Purchase returns
                DB::raw("SUM(IF(t.type = 'purchase_return', t.final_total, 0)) as total_purchase_return"),
                DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_paid"),
                // Opening balance
                DB::raw("SUM(IF(t.type = 'opening_balance', t.final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                // Ledger discounts
                DB::raw("SUM(IF(t.type = 'ledger_discount', t.final_total, 0)) as ledger_discount"),
                // Aging calculations for purchases
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void' AND DATEDIFF(NOW(), t.transaction_date) <= 30, 
                    t.final_total - COALESCE((SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as current_0_30"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void' AND DATEDIFF(NOW(), t.transaction_date) > 30 AND DATEDIFF(NOW(), t.transaction_date) <= 60, 
                    t.final_total - COALESCE((SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as days_31_60"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void' AND DATEDIFF(NOW(), t.transaction_date) > 60 AND DATEDIFF(NOW(), t.transaction_date) <= 90, 
                    t.final_total - COALESCE((SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as days_61_90"),
                DB::raw("SUM(IF(t.type = 'purchase' AND t.status != 'void' AND DATEDIFF(NOW(), t.transaction_date) > 90, 
                    t.final_total - COALESCE((SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0), 0)) as over_90"),
                // Last payment date
                DB::raw("(SELECT MAX(paid_on) FROM transaction_payments tp 
                    JOIN transactions tr ON tp.transaction_id = tr.id 
                    WHERE tr.contact_id = contacts.id AND tr.type = 'purchase') as last_payment_date"),
                // Last purchase date
                DB::raw("MAX(IF(t.type = 'purchase' AND t.status != 'void', t.transaction_date, NULL)) as last_purchase_date")
            )
            ->groupBy('contacts.id', 'contacts.name', 'contacts.supplier_business_name', 
                'contacts.mobile', 'contacts.email', 'contacts.contact_id', 'contacts.created_at',
                'contacts.pay_term_number', 'contacts.pay_term_type');

        $vendors = $vendorsQuery->get()->map(function($vendor) {
            // Calculate balance due: Purchase Due - Purchase Return Due + Opening Balance Due
            $purchaseDue = $vendor->total_purchase - $vendor->purchase_paid - $vendor->ledger_discount;
            $returnDue = $vendor->total_purchase_return - $vendor->purchase_return_paid;
            $openingDue = $vendor->opening_balance - $vendor->opening_balance_paid;
            
            $vendor->balance_due = $purchaseDue - $returnDue + $openingDue;
            $vendor->display_name = !empty($vendor->supplier_business_name) 
                ? $vendor->supplier_business_name 
                : $vendor->name;
            
            // Ensure aging values are non-negative
            $vendor->current_0_30 = max(0, $vendor->current_0_30 ?? 0);
            $vendor->days_31_60 = max(0, $vendor->days_31_60 ?? 0);
            $vendor->days_61_90 = max(0, $vendor->days_61_90 ?? 0);
            $vendor->over_90 = max(0, $vendor->over_90 ?? 0);
            
            // Payment terms display
            if ($vendor->pay_term_number && $vendor->pay_term_type) {
                $vendor->pay_terms = $vendor->pay_term_number . ' ' . ucfirst($vendor->pay_term_type);
            } else {
                $vendor->pay_terms = 'N/A';
            }
            
            return $vendor;
        })->filter(function($vendor) {
            // Only include vendors with positive balance due
            return $vendor->balance_due > 0.01;
        })->sortByDesc('balance_due')->values();

        // Calculate summary totals
        $summary = [
            'total_ap' => $vendors->sum('balance_due'),
            'total_vendors' => $vendors->count(),
            'current_0_30' => $vendors->sum('current_0_30'),
            'days_31_60' => $vendors->sum('days_31_60'),
            'days_61_90' => $vendors->sum('days_61_90'),
            'over_90' => $vendors->sum('over_90'),
            'opening_balance_due' => $vendors->sum(function($v) {
                return $v->opening_balance - $v->opening_balance_paid;
            }),
        ];

        // Get recent payments made to vendors (last 30 days)
        $recentPayments = DB::table('transaction_payments as tp')
            ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->where('tp.paid_on', '>=', now()->subDays(30))
            ->select(
                'tp.id', 'tp.amount', 'tp.method', 'tp.paid_on', 'tp.payment_ref_no',
                't.ref_no as purchase_ref', 'c.name as vendor_name', 'c.supplier_business_name'
            )
            ->orderBy('tp.paid_on', 'desc')
            ->limit(10)
            ->get()
            ->map(function($payment) {
                $payment->display_name = !empty($payment->supplier_business_name) 
                    ? $payment->supplier_business_name 
                    : $payment->vendor_name;
                return $payment;
            });

        // Get top outstanding purchase invoices
        $topOutstandingBills = DB::table('transactions as t')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->leftJoin(DB::raw('(SELECT transaction_id, SUM(amount) as paid FROM transaction_payments GROUP BY transaction_id) as tp'), 
                't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->where('t.status', '!=', 'void')
            ->whereRaw('t.final_total - COALESCE(tp.paid, 0) > 0.01')
            ->select(
                't.id', 't.ref_no', 't.transaction_date', 't.final_total',
                DB::raw('COALESCE(tp.paid, 0) as paid'),
                DB::raw('t.final_total - COALESCE(tp.paid, 0) as balance'),
                DB::raw('DATEDIFF(NOW(), t.transaction_date) as days_outstanding'),
                'c.name as vendor_name', 'c.supplier_business_name'
            )
            ->orderBy('balance', 'desc')
            ->limit(10)
            ->get()
            ->map(function($bill) {
                $bill->display_name = !empty($bill->supplier_business_name) 
                    ? $bill->supplier_business_name 
                    : $bill->vendor_name;
                return $bill;
            });

        // Update AP Account balance in Chart of Accounts
        if ($apAccount) {
            $apAccount->current_balance = $summary['total_ap'];
            $apAccount->save();
        }

        // Get AP-related Journal Entries
        $apJournalEntries = collect([]);
        if ($apAccount) {
            $apJournalEntries = JournalEntry::where('business_id', $business_id)
                ->where('status', JournalEntry::STATUS_POSTED)
                ->whereHas('lines', function($q) use ($apAccount) {
                    $q->where('account_id', $apAccount->id);
                })
                ->with(['lines' => function($q) use ($apAccount) {
                    $q->where('account_id', $apAccount->id);
                }, 'contact'])
                ->orderBy('entry_date', 'desc')
                ->limit(20)
                ->get()
                ->map(function($entry) {
                    $apLine = $entry->lines->first();
                    $entry->ap_debit = $apLine ? $apLine->debit_amount : 0;
                    $entry->ap_credit = $apLine ? $apLine->credit_amount : 0;
                    $entry->contact_name = $entry->contact ? $entry->contact->name : 'N/A';
                    return $entry;
                });
        }

        $apJournalSummary = [
            'total_debits' => $apJournalEntries->sum('ap_debit'),
            'total_credits' => $apJournalEntries->sum('ap_credit'),
            'entry_count' => $apJournalEntries->count(),
        ];

        return view('bookkeeping.accounts_payable.index', compact(
            'vendors', 'summary', 'recentPayments', 'topOutstandingBills',
            'apAccount', 'apJournalEntries', 'apJournalSummary'
        ));
    }

    /**
     * Sync AP Journal Entries
     * Creates missing journal entries for existing purchase transactions
     */
    public function syncAPJournalEntries(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $synced = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            // Get AP Account
            $apAccount = ChartOfAccount::where('business_id', $business_id)
                ->where('detail_type', 'accounts_payable')
                ->first();

            if (!$apAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accounts Payable GL account not found.'
                ], 400);
            }

            // Get Inventory/Expense account for purchases
            $inventoryAccount = ChartOfAccount::where('business_id', $business_id)
                ->where('detail_type', 'inventory')
                ->first();

            if (!$inventoryAccount) {
                $inventoryAccount = ChartOfAccount::create([
                    'business_id' => $business_id,
                    'account_code' => '1300',
                    'name' => 'Inventory',
                    'full_name' => 'Inventory',
                    'account_type' => 'asset',
                    'detail_type' => 'inventory',
                    'is_system_account' => true,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]);
            }

            // Get Cash account for payments
            $cashAccount = ChartOfAccount::where('business_id', $business_id)
                ->where('detail_type', 'cash_on_hand')
                ->first();

            // Get all purchase transactions without journal entries
            $purchases = \App\Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->where('status', '!=', 'void')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('journal_entries')
                        ->whereRaw('journal_entries.related_transaction_id = transactions.id')
                        ->where('journal_entries.related_transaction_type', 'purchase');
                })
                ->with('contact')
                ->orderBy('transaction_date')
                ->limit(100)
                ->get();

            foreach ($purchases as $purchase) {
                try {
                    // Create journal entry for purchase: Debit Inventory, Credit AP
                    $entry = JournalEntry::create([
                        'business_id' => $business_id,
                        'entry_number' => JournalEntry::generateEntryNumber($business_id),
                        'entry_date' => $purchase->transaction_date ?? now(),
                        'entry_type' => JournalEntry::TYPE_STANDARD,
                        'memo' => "Purchase: {$purchase->ref_no} - " . ($purchase->contact->name ?? 'Vendor'),
                        'source_document' => 'purchase:' . $purchase->id,
                        'related_transaction_id' => $purchase->id,
                        'related_transaction_type' => 'purchase',
                        'contact_id' => $purchase->contact_id,
                        'status' => JournalEntry::STATUS_POSTED,
                        'created_by' => auth()->id(),
                        'posted_by' => auth()->id(),
                        'posted_at' => now(),
                        'total_debit' => $purchase->final_total,
                        'total_credit' => $purchase->final_total,
                    ]);

                    // Debit: Inventory/COGS
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $inventoryAccount->id,
                        'type' => 'debit',
                        'debit_amount' => $purchase->final_total,
                        'credit_amount' => 0,
                        'description' => 'Inventory for Purchase ' . $purchase->ref_no,
                        'contact_id' => $purchase->contact_id,
                    ]);

                    // Credit: Accounts Payable
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $apAccount->id,
                        'type' => 'credit',
                        'debit_amount' => 0,
                        'credit_amount' => $purchase->final_total,
                        'description' => 'Accounts Payable for Purchase ' . $purchase->ref_no,
                        'contact_id' => $purchase->contact_id,
                    ]);

                    $synced++;
                } catch (\Exception $e) {
                    $errors[] = "Purchase {$purchase->ref_no}: {$e->getMessage()}";
                }
            }

            // Sync payments made
            if ($cashAccount) {
                $payments = TransactionPayment::whereHas('transaction', function($q) use ($business_id) {
                    $q->where('business_id', $business_id)
                        ->where('type', 'purchase')
                        ->where('status', '!=', 'void');
                })
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('journal_entries')
                        ->whereRaw("journal_entries.source_document = CONCAT('payment:', transaction_payments.id)")
                        ->where('journal_entries.related_transaction_type', 'payment_made');
                })
                ->with(['transaction.contact'])
                ->limit(100)
                ->get();

                foreach ($payments as $payment) {
                    try {
                        $transaction = $payment->transaction;
                        
                        $entry = JournalEntry::create([
                            'business_id' => $business_id,
                            'entry_number' => JournalEntry::generateEntryNumber($business_id),
                            'entry_date' => $payment->paid_on ?? now(),
                            'entry_type' => JournalEntry::TYPE_STANDARD,
                            'memo' => "Payment to Vendor: " . ($transaction->contact->name ?? 'Vendor') . " - " . $payment->payment_ref_no,
                            'source_document' => 'payment:' . $payment->id,
                            'related_transaction_id' => $transaction->id,
                            'related_transaction_type' => 'payment_made',
                            'contact_id' => $transaction->contact_id,
                            'status' => JournalEntry::STATUS_POSTED,
                            'created_by' => auth()->id(),
                            'posted_by' => auth()->id(),
                            'posted_at' => now(),
                            'total_debit' => $payment->amount,
                            'total_credit' => $payment->amount,
                        ]);

                        // Debit: Accounts Payable (reduce liability)
                        JournalEntryLine::create([
                            'journal_entry_id' => $entry->id,
                            'account_id' => $apAccount->id,
                            'type' => 'debit',
                            'debit_amount' => $payment->amount,
                            'credit_amount' => 0,
                            'description' => 'Payment for Purchase ' . $transaction->ref_no,
                            'contact_id' => $transaction->contact_id,
                        ]);

                        // Credit: Cash/Bank
                        JournalEntryLine::create([
                            'journal_entry_id' => $entry->id,
                            'account_id' => $cashAccount->id,
                            'type' => 'credit',
                            'debit_amount' => 0,
                            'credit_amount' => $payment->amount,
                            'description' => 'Cash payment to ' . ($transaction->contact->name ?? 'Vendor'),
                            'contact_id' => $transaction->contact_id,
                        ]);

                        $synced++;
                    } catch (\Exception $e) {
                        $errors[] = "Payment {$payment->payment_ref_no}: {$e->getMessage()}";
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Sync completed. Created {$synced} journal entries.",
                'synced' => $synced,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Credit Notes - Index
     */
    public function creditNotes(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $creditNotes = \App\Models\CreditNote::forBusiness($business_id)
            ->with(['contact', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_issued' => $creditNotes->sum('amount'),
            'total_applied' => $creditNotes->sum('amount_applied'),
            'total_available' => $creditNotes->where('status', '!=', \App\Models\CreditNote::STATUS_VOIDED)
                ->where('status', '!=', \App\Models\CreditNote::STATUS_CANCELLED)
                ->sum('balance'),
            'count' => $creditNotes->count(),
            'pending_approval' => $creditNotes->where('status', \App\Models\CreditNote::STATUS_DRAFT)->count(),
        ];

        return view('bookkeeping.accounts_receivable.credit_notes', compact('creditNotes', 'summary'));
    }

    /**
     * Credit Notes - Create Form
     */
    public function createCreditNote(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get ALL customers (credit notes can be issued to any customer)
        $customers = DB::table('contacts')
            ->leftJoin('transactions as t', function($join) use ($business_id) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.business_id', '=', $business_id);
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both'])
            ->where('contacts.contact_status', 'active')
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.supplier_business_name',
                'contacts.contact_id as customer_code',
                DB::raw("COALESCE(SUM(IF(t.type = 'sell' AND t.status = 'final', t.final_total, 0)), 0) as total_invoice"),
                DB::raw("COALESCE(SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT COALESCE(SUM(IF(is_return = 1,-1*amount,amount)), 0) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)), 0) as invoice_received")
            )
            ->groupBy('contacts.id', 'contacts.name', 'contacts.supplier_business_name', 'contacts.contact_id')
            ->orderBy('contacts.name')
            ->get()
            ->map(function($customer) {
                $customer->balance_due = max(0, $customer->total_invoice - $customer->invoice_received);
                $customer->display_name = !empty($customer->supplier_business_name) 
                    ? $customer->supplier_business_name 
                    : $customer->name;
                return $customer;
            });

        $reasonCategories = \App\Models\CreditNote::getReasonCategories();
        $nextNumber = \App\Models\CreditNote::generateCreditNoteNumber($business_id);

        // Pre-selected customer if passed via query string
        $selectedCustomerId = $request->get('customer_id');

        return view('bookkeeping.accounts_receivable.create_credit_note', compact(
            'customers', 'reasonCategories', 'nextNumber', 'selectedCustomerId'
        ));
    }

    /**
     * Credit Notes - Store
     */
    public function storeCreditNote(Request $request)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'contact_id' => 'required|integer|exists:contacts,id',
            'amount' => 'required|numeric|min:0.01',
            'credit_date' => 'required|date',
            'reason_category' => 'required|string',
            'reason_description' => 'required|string|min:10',
        ], [
            'reason_description.min' => 'Please provide a detailed reason (at least 10 characters).',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $creditNote = \App\Models\CreditNote::create([
                'business_id' => $business_id,
                'credit_note_number' => \App\Models\CreditNote::generateCreditNoteNumber($business_id),
                'contact_id' => $request->contact_id,
                'credit_date' => $request->credit_date,
                'amount' => $request->amount,
                'amount_applied' => 0,
                'balance' => $request->amount,
                'reason_category' => $request->reason_category,
                'reason_description' => $request->reason_description,
                'reference_type' => $request->reference_type,
                'reference_number' => $request->reference_number,
                'reference_transaction_id' => $request->reference_transaction_id,
                'internal_notes' => $request->internal_notes,
                'status' => \App\Models\CreditNote::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            // Auto-approve if requested
            if ($request->auto_approve) {
                $creditNote->approve(auth()->id());
            }

            return response()->json([
                'success' => true,
                'msg' => 'Credit note created successfully.',
                'credit_note' => $creditNote,
                'redirect' => route('bookkeeping.credit-notes.show', $creditNote->id),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating credit note: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'Error creating credit note: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Credit Notes - Show
     */
    public function showCreditNote($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $creditNote = \App\Models\CreditNote::forBusiness($business_id)
            ->with(['contact', 'createdBy', 'approvedBy', 'journalEntry', 'applications.transaction', 'applications.appliedBy'])
            ->findOrFail($id);

        // Get customer's outstanding invoices for application
        $outstandingInvoices = [];
        if (in_array($creditNote->status, [\App\Models\CreditNote::STATUS_APPROVED, \App\Models\CreditNote::STATUS_PARTIALLY_APPLIED])) {
            $outstandingInvoices = DB::table('transactions as t')
                ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1*amount, amount)) as paid FROM transaction_payments GROUP BY transaction_id) as tp'), 
                    't.id', '=', 'tp.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.contact_id', $creditNote->contact_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereRaw('t.final_total - COALESCE(tp.paid, 0) > 0.01')
                ->select(
                    't.id', 't.invoice_no', 't.transaction_date', 't.final_total',
                    DB::raw('COALESCE(tp.paid, 0) as paid'),
                    DB::raw('t.final_total - COALESCE(tp.paid, 0) as balance')
                )
                ->orderBy('t.transaction_date', 'asc')
                ->get();
        }

        return view('bookkeeping.accounts_receivable.show_credit_note', compact('creditNote', 'outstandingInvoices'));
    }

    /**
     * Credit Notes - Approve
     */
    public function approveCreditNote($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $creditNote = \App\Models\CreditNote::forBusiness($business_id)->findOrFail($id);
            $creditNote->approve(auth()->id());

            return response()->json([
                'success' => true,
                'msg' => 'Credit note approved successfully. Journal entry created.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Credit Notes - Apply to Invoice
     */
    public function applyCreditNote(Request $request, $id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'transaction_id' => 'required|integer|exists:transactions,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            $creditNote = \App\Models\CreditNote::forBusiness($business_id)->findOrFail($id);
            
            $application = $creditNote->applyToInvoice(
                $request->transaction_id,
                $request->amount,
                auth()->id(),
                $request->notes
            );

            return response()->json([
                'success' => true,
                'msg' => 'Credit note applied successfully.',
                'application' => $application,
                'remaining_balance' => $creditNote->fresh()->balance,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Credit Notes - Void
     */
    public function voidCreditNote($id)
    {
        if (!auth()->user()->can('all_expense.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $creditNote = \App\Models\CreditNote::forBusiness($business_id)->findOrFail($id);
            $creditNote->void(auth()->id());

            return response()->json([
                'success' => true,
                'msg' => 'Credit note voided successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get customer outstanding invoices (AJAX)
     */
    public function getCustomerInvoices($contactId)
    {
        if (!auth()->user()->can('all_expense.access')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $business_id = request()->session()->get('user.business_id');

        $invoices = DB::table('transactions as t')
            ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1*amount, amount)) as paid FROM transaction_payments GROUP BY transaction_id) as tp'), 
                't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.contact_id', $contactId)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereRaw('t.final_total - COALESCE(tp.paid, 0) > 0.01')
            ->select(
                't.id', 't.invoice_no', 't.transaction_date', 't.final_total',
                DB::raw('COALESCE(tp.paid, 0) as paid'),
                DB::raw('t.final_total - COALESCE(tp.paid, 0) as balance'),
                DB::raw('DATEDIFF(NOW(), t.transaction_date) as days_outstanding')
            )
            ->orderBy('t.transaction_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'invoices' => $invoices,
        ]);
    }
}

