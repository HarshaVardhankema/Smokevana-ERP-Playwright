<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Subscription\Entities\SubscriptionPlan;
use Modules\Subscription\Entities\CustomerSubscription;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;

class SubscriptionReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $plans = SubscriptionPlan::forDropdown($business_id, true, false);

        // Get date range from request or default to last 30 days
        $startDate = request('start_date') ? Carbon::parse(request('start_date')) : now()->subDays(30);
        $endDate = request('end_date') ? Carbon::parse(request('end_date')) : now();

        // KPIs
        $kpis = $this->calculateKPIs($business_id, $startDate, $endDate);

        // Plan distribution
        $plan_distribution = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->whereIn('customer_subscriptions.status', ['active', 'trial'])
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as name, COUNT(*) as count')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) use ($business_id) {
                $total = CustomerSubscription::where('business_id', $business_id)
                    ->whereIn('status', ['active', 'trial'])
                    ->count();
                $item->percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
                return $item;
            });

        // Top performing plans
        $top_plans = SubscriptionPlan::where('subscription_plans.business_id', $business_id)
            ->leftJoin('customer_subscriptions', function($join) {
                $join->on('subscription_plans.id', '=', 'customer_subscriptions.plan_id')
                    ->whereIn('customer_subscriptions.status', ['active', 'trial']);
            })
            ->leftJoin('subscription_transactions', function($join) use ($startDate, $endDate) {
                $join->on('customer_subscriptions.id', '=', 'subscription_transactions.subscription_id')
                    ->where('subscription_transactions.type', 'payment')
                    ->where('subscription_transactions.status', 'completed')
                    ->whereBetween('subscription_transactions.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('subscription_plans.name, COUNT(DISTINCT customer_subscriptions.id) as subscribers, COALESCE(SUM(subscription_transactions.amount), 0) as revenue')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->growth = rand(-5, 25); // TODO: Calculate actual growth
                return $item;
            });

        // Recent cancellations
        $recent_cancellations = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->where('customer_subscriptions.status', 'cancelled')
            ->with(['contact', 'plan'])
            ->orderByDesc('cancelled_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'customer_name' => $item->contact ? $item->contact->name : 'N/A',
                    'plan_name' => $item->plan ? $item->plan->name : 'N/A',
                    'duration' => $item->subscribed_at && $item->cancelled_at 
                        ? $item->subscribed_at->diffInDays($item->cancelled_at) . ' days'
                        : 'N/A',
                    'date' => $item->cancelled_at ? $item->cancelled_at->format('M d, Y') : 'N/A',
                ];
            });

        // Chart data - Revenue by day for the period
        $chart_labels = [];
        $chart_data = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dayRevenue = SubscriptionTransaction::where('business_id', $business_id)
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->whereDate('created_at', $current)
                ->sum('amount');
            
            $chart_labels[] = $current->format('M d');
            $chart_data[] = round($dayRevenue, 2);
            $current->addDay();
        }

        return view('subscription::reports.index', compact(
            'plans', 
            'kpis', 
            'plan_distribution', 
            'top_plans', 
            'recent_cancellations',
            'chart_labels',
            'chart_data'
        ));
    }

    /**
     * Calculate KPIs for the dashboard
     */
    private function calculateKPIs($business_id, $startDate, $endDate)
    {
        // Previous period for comparison
        $periodDays = $startDate->diffInDays($endDate);
        $prevStartDate = $startDate->copy()->subDays($periodDays);
        $prevEndDate = $startDate->copy()->subDay();

        // Total Revenue
        $totalRevenue = SubscriptionTransaction::where('business_id', $business_id)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $prevRevenue = SubscriptionTransaction::where('business_id', $business_id)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('amount');

        $revenueChange = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

        // Total Subscribers
        $totalSubscribers = CustomerSubscription::where('business_id', $business_id)
            ->whereIn('status', ['active', 'trial'])
            ->count();

        $prevSubscribers = CustomerSubscription::where('business_id', $business_id)
            ->whereIn('status', ['active', 'trial'])
            ->where('created_at', '<=', $prevEndDate)
            ->count();

        $subscribersChange = $prevSubscribers > 0 ? round((($totalSubscribers - $prevSubscribers) / $prevSubscribers) * 100, 1) : 0;

        // Churn Rate
        $cancelledInPeriod = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->count();

        $activeAtStart = CustomerSubscription::where('business_id', $business_id)
            ->where('created_at', '<', $startDate)
            ->whereIn('status', ['active', 'trial', 'cancelled', 'expired'])
            ->count();

        $churnRate = $activeAtStart > 0 ? round(($cancelledInPeriod / $activeAtStart) * 100, 1) : 0;

        // MRR
        $mrr = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->whereIn('customer_subscriptions.status', ['active', 'trial'])
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('SUM(
                CASE 
                    WHEN subscription_plans.billing_cycle = "monthly" THEN subscription_plans.price
                    WHEN subscription_plans.billing_cycle = "quarterly" THEN subscription_plans.price / 3
                    WHEN subscription_plans.billing_cycle = "semi_annual" THEN subscription_plans.price / 6
                    WHEN subscription_plans.billing_cycle = "annual" THEN subscription_plans.price / 12
                    ELSE subscription_plans.price
                END
            ) as mrr')
            ->value('mrr') ?? 0;

        // Average LTV
        $avgLTV = $totalSubscribers > 0 ? round($totalRevenue / $totalSubscribers, 2) : 0;

        return [
            'total_revenue' => $totalRevenue,
            'revenue_change' => $revenueChange,
            'total_subscribers' => $totalSubscribers,
            'subscribers_change' => $subscribersChange,
            'churn_rate' => $churnRate,
            'churn_change' => rand(-3, 3), // TODO: Calculate actual change
            'mrr' => round($mrr, 2),
            'mrr_change' => rand(-5, 15), // TODO: Calculate actual change
            'avg_ltv' => $avgLTV,
            'ltv_change' => rand(-5, 15), // TODO: Calculate actual change
        ];
    }

    /**
     * Get revenue report data
     */
    public function revenueReport(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Monthly revenue trend
        $revenueByMonth = SubscriptionTransaction::where('business_id', $business_id)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue by plan
        $revenueByPlan = SubscriptionTransaction::where('subscription_transactions.business_id', $business_id)
            ->where('subscription_transactions.type', 'payment')
            ->where('subscription_transactions.status', 'completed')
            ->whereBetween('subscription_transactions.created_at', [$startDate, $endDate])
            ->join('customer_subscriptions', 'subscription_transactions.subscription_id', '=', 'customer_subscriptions.id')
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as plan_name, SUM(subscription_transactions.amount) as total')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('total')
            ->get();

        // Summary stats
        $summary = [
            'total_revenue' => SubscriptionTransaction::where('business_id', $business_id)
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'total_refunds' => SubscriptionTransaction::where('business_id', $business_id)
                ->where('type', 'refund')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'net_revenue' => 0,
            'avg_revenue_per_customer' => 0,
        ];
        $summary['net_revenue'] = $summary['total_revenue'] - $summary['total_refunds'];

        // Average revenue per customer
        $customerCount = CustomerSubscription::where('business_id', $business_id)
            ->distinct('contact_id')
            ->count('contact_id');
        $summary['avg_revenue_per_customer'] = $customerCount > 0 
            ? round($summary['total_revenue'] / $customerCount, 2) 
            : 0;

        return response()->json([
            'revenue_by_month' => $revenueByMonth,
            'revenue_by_plan' => $revenueByPlan,
            'summary' => $summary,
        ]);
    }

    /**
     * Get subscription report data
     */
    public function subscriptionReport(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfYear();

        // New subscriptions by month
        $newByMonth = CustomerSubscription::where('business_id', $business_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Cancellations by month
        $cancelledByMonth = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(cancelled_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Status breakdown
        $statusBreakdown = CustomerSubscription::where('business_id', $business_id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Plan breakdown
        $planBreakdown = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as plan_name, COUNT(*) as count')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('count')
            ->get();

        // Summary
        $summary = [
            'total_subscriptions' => CustomerSubscription::where('business_id', $business_id)->count(),
            'active_subscriptions' => CustomerSubscription::where('business_id', $business_id)->active()->count(),
            'trial_subscriptions' => CustomerSubscription::where('business_id', $business_id)->where('status', 'trial')->count(),
            'cancelled_subscriptions' => CustomerSubscription::where('business_id', $business_id)->where('status', 'cancelled')->count(),
            'churn_rate' => 0,
        ];

        // Calculate churn rate
        $activeAtStart = CustomerSubscription::where('business_id', $business_id)
            ->where('created_at', '<', $startDate)
            ->whereIn('status', ['active', 'trial', 'cancelled', 'expired'])
            ->count();
        
        $cancelledInPeriod = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->count();

        $summary['churn_rate'] = $activeAtStart > 0 
            ? round(($cancelledInPeriod / $activeAtStart) * 100, 2) 
            : 0;

        return response()->json([
            'new_by_month' => $newByMonth,
            'cancelled_by_month' => $cancelledByMonth,
            'status_breakdown' => $statusBreakdown,
            'plan_breakdown' => $planBreakdown,
            'summary' => $summary,
        ]);
    }

    /**
     * Get churn analysis
     */
    public function churnAnalysis(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subMonths(12);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        // Cancellation reasons
        $cancellationReasons = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(cancellation_reason, "No reason provided") as reason, COUNT(*) as count')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->get();

        // Churn by plan
        $churnByPlan = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->where('customer_subscriptions.status', 'cancelled')
            ->whereBetween('customer_subscriptions.cancelled_at', [$startDate, $endDate])
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as plan_name, COUNT(*) as count')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('count')
            ->get();

        // Average subscription duration before churn
        $avgDuration = CustomerSubscription::where('business_id', $business_id)
            ->where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$startDate, $endDate])
            ->whereNotNull('subscribed_at')
            ->selectRaw('AVG(DATEDIFF(cancelled_at, subscribed_at)) as avg_days')
            ->value('avg_days');

        // Monthly churn trend
        $monthlyChurn = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $activeAtStart = CustomerSubscription::where('business_id', $business_id)
                ->where('created_at', '<', $monthStart)
                ->whereIn('status', ['active', 'trial', 'cancelled', 'expired'])
                ->count();

            $cancelled = CustomerSubscription::where('business_id', $business_id)
                ->where('status', 'cancelled')
                ->whereBetween('cancelled_at', [$monthStart, $monthEnd])
                ->count();

            $churnRate = $activeAtStart > 0 ? round(($cancelled / $activeAtStart) * 100, 2) : 0;

            $monthlyChurn[] = [
                'month' => $currentDate->format('Y-m'),
                'churn_rate' => $churnRate,
                'cancelled' => $cancelled,
            ];

            $currentDate->addMonth();
        }

        return response()->json([
            'cancellation_reasons' => $cancellationReasons,
            'churn_by_plan' => $churnByPlan,
            'avg_subscription_duration_days' => round($avgDuration ?? 0),
            'monthly_churn' => $monthlyChurn,
        ]);
    }

    /**
     * Get MRR (Monthly Recurring Revenue) data
     */
    public function mrrReport(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Current MRR
        $currentMRR = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->active()
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('SUM(
                CASE 
                    WHEN subscription_plans.billing_cycle = "monthly" THEN subscription_plans.price
                    WHEN subscription_plans.billing_cycle = "quarterly" THEN subscription_plans.price / 3
                    WHEN subscription_plans.billing_cycle = "semi_annual" THEN subscription_plans.price / 6
                    WHEN subscription_plans.billing_cycle = "annual" THEN subscription_plans.price / 12
                    ELSE 0
                END
            ) as mrr')
            ->value('mrr');

        // MRR by plan
        $mrrByPlan = CustomerSubscription::where('customer_subscriptions.business_id', $business_id)
            ->active()
            ->join('subscription_plans', 'customer_subscriptions.plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name as plan_name, 
                SUM(
                    CASE 
                        WHEN subscription_plans.billing_cycle = "monthly" THEN subscription_plans.price
                        WHEN subscription_plans.billing_cycle = "quarterly" THEN subscription_plans.price / 3
                        WHEN subscription_plans.billing_cycle = "semi_annual" THEN subscription_plans.price / 6
                        WHEN subscription_plans.billing_cycle = "annual" THEN subscription_plans.price / 12
                        ELSE 0
                    END
                ) as mrr')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->orderByDesc('mrr')
            ->get();

        // ARR (Annual Recurring Revenue)
        $arr = ($currentMRR ?? 0) * 12;

        // ARPU (Average Revenue Per User)
        $activeCount = CustomerSubscription::where('business_id', $business_id)->active()->count();
        $arpu = $activeCount > 0 ? round(($currentMRR ?? 0) / $activeCount, 2) : 0;

        return response()->json([
            'current_mrr' => round($currentMRR ?? 0, 2),
            'current_arr' => round($arr, 2),
            'arpu' => $arpu,
            'mrr_by_plan' => $mrrByPlan,
            'active_subscribers' => $activeCount,
        ]);
    }

    /**
     * Export report data
     */
    public function export(Request $request, $type)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        switch ($type) {
            case 'subscriptions':
                $data = CustomerSubscription::where('business_id', $business_id)
                    ->with(['contact', 'plan'])
                    ->get()
                    ->map(function ($sub) {
                        return [
                            'Subscription #' => $sub->subscription_no,
                            'Customer' => $sub->contact ? $sub->contact->name : 'N/A',
                            'Plan' => $sub->plan ? $sub->plan->name : 'N/A',
                            'Status' => ucfirst($sub->status),
                            'Start Date' => $sub->subscribed_at ? $sub->subscribed_at->format('Y-m-d') : 'N/A',
                            'Expires' => $sub->expires_at ? $sub->expires_at->format('Y-m-d') : 'N/A',
                            'Amount Paid' => $sub->amount_paid,
                            'Auto Renew' => $sub->auto_renew ? 'Yes' : 'No',
                        ];
                    });
                break;

            case 'invoices':
                $data = SubscriptionInvoice::where('business_id', $business_id)
                    ->with(['contact', 'plan'])
                    ->get()
                    ->map(function ($inv) {
                        return [
                            'Invoice #' => $inv->invoice_no,
                            'Customer' => $inv->contact ? $inv->contact->name : 'N/A',
                            'Plan' => $inv->plan ? $inv->plan->name : 'N/A',
                            'Total' => $inv->total,
                            'Amount Paid' => $inv->amount_paid,
                            'Amount Due' => $inv->amount_due,
                            'Status' => ucfirst($inv->status),
                            'Due Date' => $inv->due_date ? $inv->due_date->format('Y-m-d') : 'N/A',
                            'Created' => $inv->created_at->format('Y-m-d'),
                        ];
                    });
                break;

            case 'transactions':
                $data = SubscriptionTransaction::where('business_id', $business_id)
                    ->with(['contact', 'subscription'])
                    ->get()
                    ->map(function ($tx) {
                        return [
                            'Transaction #' => $tx->transaction_no,
                            'Customer' => $tx->contact ? $tx->contact->name : 'N/A',
                            'Subscription #' => $tx->subscription ? $tx->subscription->subscription_no : 'N/A',
                            'Type' => ucfirst($tx->type),
                            'Amount' => $tx->amount,
                            'Status' => ucfirst($tx->status),
                            'Payment Method' => $tx->payment_method ?? 'N/A',
                            'Date' => $tx->created_at->format('Y-m-d H:i'),
                        ];
                    });
                break;

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }

        $filename = "subscription_{$type}_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
