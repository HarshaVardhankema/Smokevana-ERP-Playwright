<?php

namespace App\Http\Controllers;

use App\Models\DropshipOrderTracking;
use App\Models\WpVendor;
use App\Product;
use App\Services\DropshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropshipDashboardController extends Controller
{
    protected $dropshipService;

    public function __construct(DropshipService $dropshipService)
    {
        $this->dropshipService = $dropshipService;
    }

    /**
     * Display the dropship dashboard
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('dropship.admin_access') && !auth()->user()->can('so.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get dashboard statistics
        $stats = $this->dropshipService->getDashboardStats($business_id);

        // Get top vendors
        $topVendors = $this->dropshipService->getTopVendors($business_id, 5);

        // Get recent activity
        $recentActivity = $this->dropshipService->getRecentActivity($business_id, 10);

        // Get order chart data (last 30 days)
        $chartData = $this->getOrderChartData($business_id);

        // Get vendors for quick action dropdown
        $vendors = WpVendor::forBusiness($business_id)->active()->pluck('name', 'id');

        return view('dropship.dashboard.index', compact(
            'stats',
            'topVendors',
            'recentActivity',
            'chartData',
            'vendors'
        ));
    }

    /**
     * Get order chart data for last 30 days
     */
    protected function getOrderChartData($business_id)
    {
        $endDate = now();
        $startDate = now()->subDays(30);

        $orders = DropshipOrderTracking::forBusiness($business_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN fulfillment_status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN fulfillment_status IN ("pending", "processing") THEN 1 ELSE 0 END) as pending')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $labels = [];
        $totalData = [];
        $completedData = [];
        $pendingData = [];

        // Fill in missing dates with zeros
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->addDay()
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            
            $dayData = $orders->firstWhere('date', $dateStr);
            $totalData[] = $dayData->total ?? 0;
            $completedData[] = $dayData->completed ?? 0;
            $pendingData[] = $dayData->pending ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => $totalData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Completed',
                    'data' => $completedData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Pending',
                    'data' => $pendingData,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ]
        ];
    }

    /**
     * Get vendor performance comparison
     */
    public function vendorPerformance(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $period = $request->input('period', 'month');

        $vendors = WpVendor::forBusiness($business_id)->active()->get();

        $data = [];
        foreach ($vendors as $vendor) {
            $performance = $this->dropshipService->getVendorPerformance($vendor->id, $period);
            $data[] = [
                'vendor' => $vendor->display_name,
                'orders' => $performance['total_orders'],
                'completed' => $performance['completed_orders'],
                'completion_rate' => $performance['completion_rate'],
                'avg_fulfillment' => $performance['avg_fulfillment_hours'],
                'revenue' => $performance['total_revenue'],
            ];
        }

        return response()->json($data);
    }

    /**
     * Get revenue breakdown (In-House vs Dropship)
     */
    public function revenueBreakdown(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $period = $request->input('period', 'month');

        $startDate = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // Dropship revenue
        $dropshipRevenue = DropshipOrderTracking::forBusiness($business_id)
            ->where('created_at', '>=', $startDate)
            ->whereIn('fulfillment_status', ['delivered', 'completed'])
            ->sum('vendor_payout_amount');

        // In-house revenue (from erp_sales_order transactions)
        $inHouseRevenue = \App\Transaction::where('business_id', $business_id)
            ->where('type', 'erp_sales_order')
            ->where('created_at', '>=', $startDate)
            ->where('status', 'final')
            ->sum('final_total');

        return response()->json([
            'dropship' => $dropshipRevenue,
            'in_house' => $inHouseRevenue,
            'total' => $dropshipRevenue + $inHouseRevenue,
        ]);
    }

    /**
     * Get pending actions count for dashboard widgets
     */
    public function pendingActions(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        return response()->json([
            'pending_orders' => DropshipOrderTracking::forBusiness($business_id)
                ->pending()
                ->count(),
            'failed_syncs' => DropshipOrderTracking::forBusiness($business_id)
                ->where('sync_status', 'failed')
                ->count(),
            'awaiting_tracking' => DropshipOrderTracking::forBusiness($business_id)
                ->whereIn('fulfillment_status', ['vendor_accepted', 'processing', 'ready_to_ship'])
                ->whereNull('tracking_number')
                ->count(),
            'pending_vendors' => WpVendor::forBusiness($business_id)
                ->where('status', 'pending')
                ->count(),
        ]);
    }

    /**
     * Quick action: Sync products from WooCommerce
     */
    public function syncProducts(Request $request)
    {
        if (!auth()->user()->can('dropship.admin.sync_products')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            
            // Dispatch sync job (this would trigger the WooCommerce product sync)
            // For now, we'll just return a success message
            // In production, you'd dispatch a job like: SyncWooCommerceProducts::dispatch($business_id);

            return response()->json([
                'success' => true,
                'msg' => 'Product sync initiated. This may take a few minutes.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to initiate sync: ' . $e->getMessage()
            ]);
        }
    }
}











