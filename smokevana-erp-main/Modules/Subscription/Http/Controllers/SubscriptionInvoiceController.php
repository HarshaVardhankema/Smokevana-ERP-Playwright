<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionTransaction;
use Modules\Subscription\Entities\CustomerSubscription;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionInvoiceController extends Controller
{
    /**
     * Display invoices list
     */
    public function index()
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Stats for dashboard
        $stats = [
            'total_invoices' => SubscriptionInvoice::where('business_id', $business_id)->count(),
            'paid_amount' => SubscriptionInvoice::where('business_id', $business_id)->where('status', 'paid')->sum('total'),
            'pending_amount' => SubscriptionInvoice::where('business_id', $business_id)->whereIn('status', ['pending', 'overdue'])->sum('amount_due'),
            'overdue_count' => SubscriptionInvoice::where('business_id', $business_id)->where('status', 'overdue')->count(),
        ];

        return view('subscription::invoices.index', compact('stats'));
    }

    /**
     * Get invoices data for DataTables
     */
    public function getInvoices(Request $request)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $invoices = SubscriptionInvoice::where('subscription_invoices.business_id', $business_id)
            ->with(['contact', 'plan', 'subscription'])
            ->select('subscription_invoices.*');

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $invoices->where('status', $request->status);
        }

        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $invoices->whereBetween('created_at', [$dates[0], $dates[1] . ' 23:59:59']);
            }
        }

        return DataTables::of($invoices)
            ->addColumn('invoice_number', function ($row) {
                return $row->invoice_no;
            })
            ->orderColumn('invoice_number', 'subscription_invoices.invoice_no $1')
            ->addColumn('customer_name', function ($row) {
                return $row->contact ? $row->contact->name : 'N/A';
            })
            ->addColumn('plan_name', function ($row) {
                return $row->plan ? $row->plan->name : 'N/A';
            })
            ->addColumn('amount', function ($row) {
                return $row->total;
            })
            ->addColumn('total_formatted', function ($row) {
                return $row->formatted_total;
            })
            ->addColumn('amount_due_formatted', function ($row) {
                return $row->formatted_amount_due;
            })
            ->addColumn('status_badge', function ($row) {
                return '<span class="badge bg-' . $row->status_badge . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
            })
            ->addColumn('due_date_formatted', function ($row) {
                if (!$row->due_date) return 'N/A';
                $class = $row->isOverdue() ? 'text-danger' : '';
                return '<span class="' . $class . '">' . $row->due_date->format('M d, Y') . '</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group">';
                $actions .= '<a href="' . route('subscription.invoices.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                
                if (auth()->user()->can('subscription.create') && !$row->isPaid()) {
                    $actions .= '<button type="button" class="btn btn-sm btn-success record-payment" data-id="' . $row->id . '" data-amount="' . $row->amount_due . '"><i class="fas fa-dollar-sign"></i></button>';
                }
                
                $actions .= '<a href="' . route('subscription.invoices.print', $row->id) . '" class="btn btn-sm btn-secondary" target="_blank"><i class="fas fa-print"></i></a>';
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'due_date_formatted', 'action'])
            ->make(true);
    }

    /**
     * Show invoice details
     */
    public function show($id)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $invoice = SubscriptionInvoice::where('business_id', $business_id)
            ->with(['contact', 'plan', 'subscription', 'transactions'])
            ->findOrFail($id);

        return view('subscription::invoices.show', compact('invoice'));
    }

    /**
     * Print invoice
     */
    public function print($id)
    {
        if (!auth()->user()->can('subscription.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $invoice = SubscriptionInvoice::where('business_id', $business_id)
            ->with(['contact', 'plan', 'subscription', 'transactions'])
            ->findOrFail($id);

        $business = \App\Business::find($business_id);

        return view('subscription::invoices.print', compact('invoice', 'business'));
    }

    /**
     * Record payment for invoice
     */
    public function recordPayment(Request $request, $id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $business_id = request()->session()->get('user.business_id');

        $invoice = SubscriptionInvoice::where('business_id', $business_id)
            ->findOrFail($id);

        if ($invoice->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already paid.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = SubscriptionTransaction::create([
                'business_id' => $business_id,
                'subscription_id' => $invoice->subscription_id,
                'invoice_id' => $invoice->id,
                'contact_id' => $invoice->contact_id,
                'type' => 'payment',
                'status' => 'completed',
                'amount' => $request->amount,
                'currency' => $invoice->currency,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);

            // Update invoice
            $invoice->recordPayment($request->amount, $request->payment_method, $transaction->transaction_no);

            // If fully paid, update subscription
            if ($invoice->isPaid() && $invoice->subscription) {
                $invoice->subscription->amount_paid += $request->amount;
                $invoice->subscription->save();
                
                // Activate subscription if it was pending
                if ($invoice->subscription->status === 'pending') {
                    $invoice->subscription->activate();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create invoice for subscription
     */
    public function createForSubscription($subscription_id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = CustomerSubscription::where('business_id', $business_id)
            ->with('plan')
            ->findOrFail($subscription_id);

        try {
            DB::beginTransaction();

            $plan = $subscription->plan;

            $invoice = SubscriptionInvoice::create([
                'business_id' => $business_id,
                'subscription_id' => $subscription->id,
                'contact_id' => $subscription->contact_id,
                'plan_id' => $plan->id,
                'type' => 'subscription',
                'billing_period_start' => $subscription->current_period_start,
                'billing_period_end' => $subscription->current_period_end,
                'subtotal' => $plan->price,
                'tax_amount' => 0,
                'total' => $plan->price,
                'currency' => $plan->currency,
                'amount_due' => $plan->price,
                'status' => 'pending',
                'due_date' => now()->addDays(7),
                'line_items' => [
                    [
                        'description' => $plan->name . ' Subscription',
                        'quantity' => 1,
                        'unit_price' => $plan->price,
                        'total' => $plan->price,
                    ]
                ],
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('subscription.invoices.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice to customer
     */
    public function send($id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $invoice = SubscriptionInvoice::where('business_id', $business_id)
            ->with(['contact', 'plan'])
            ->findOrFail($id);

        try {
            // TODO: Implement email sending
            // Mail::to($invoice->contact->email)->send(new SubscriptionInvoiceMail($invoice));

            return response()->json([
                'success' => true,
                'message' => 'Invoice sent to customer.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel invoice
     */
    public function cancel($id)
    {
        if (!auth()->user()->can('subscription.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $invoice = SubscriptionInvoice::where('business_id', $business_id)
            ->findOrFail($id);

        if ($invoice->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a paid invoice.'
            ], 400);
        }

        try {
            $invoice->cancelInvoice();

            return response()->json([
                'success' => true,
                'message' => 'Invoice cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
