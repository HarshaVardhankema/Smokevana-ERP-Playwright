@extends('layouts.vendor_portal')
@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="sc-page-header">
    <h1 class="sc-page-title">Welcome back, <strong>{{ $vendor->display_name }}</strong></h1>
    <div class="sc-header-date">
        <span class="text-muted"><i class="bi bi-calendar" style="font-size: 14px; vertical-align: middle;"></i> {{ now()->format('l, F j, Y') }}</span>
    </div>
</div>

<!-- Alert for Orders Needing Action -->
@if($ordersNeedingAction->count() > 0)
<div class="sc-alert sc-alert-warning">
    <div class="sc-alert-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <div class="sc-alert-content">
        <h4><strong>{{ $ordersNeedingAction->count() }}</strong> Order(s) Need Your Attention</h4>
        <p>You have pending orders that require action. Accept orders and add tracking to fulfill them.</p>
    </div>
    <a href="{{ route('vendor.orders', ['status' => 'pending']) }}" class="sc-btn sc-btn-primary" style="margin-left: auto; flex-shrink: 0;">
        View Orders
    </a>
</div>
@endif

<!-- Metrics Grid -->
<div class="sc-metrics">
    <div class="sc-metric">
        <div class="sc-metric-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="sc-metric-label">Active Products</div>
        <div class="sc-metric-value">{{ $stats['active_products'] ?? 0 }}</div>
        <div class="sc-metric-desc">of {{ $stats['total_products'] ?? 0 }} total products</div>
    </div>

    <div class="sc-metric">
        <div class="sc-metric-icon">
            <i class="bi bi-clock"></i>
        </div>
        <div class="sc-metric-label">Pending Orders</div>
        <div class="sc-metric-value orange">{{ $stats['pending_orders'] ?? 0 }}</div>
        <div class="sc-metric-desc">Awaiting your action</div>
    </div>

    <div class="sc-metric">
        <div class="sc-metric-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="sc-metric-label">Completed</div>
        <div class="sc-metric-value success">{{ $stats['completed_orders'] ?? 0 }}</div>
        <div class="sc-metric-desc">{{ $stats['orders_this_month'] ?? 0 }} this month</div>
    </div>

    <div class="sc-metric">
        <div class="sc-metric-icon">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="sc-metric-label">Monthly Revenue</div>
        <div class="sc-metric-value teal">${{ number_format($stats['revenue_this_month'] ?? 0, 2) }}</div>
        <div class="sc-metric-desc">Earned this month</div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Performance Card -->
    <div class="col-lg-6">
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-graph-up"></i>
                    Performance Summary
                </h3>
            </div>
            <div class="sc-card-body" style="padding: 0;">
                <div style="display: flex; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 50%; padding: 20px; border-bottom: 1px solid var(--gray-200); border-right: 1px solid var(--gray-200);">
                        <div style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; margin-bottom: 6px;">Total Orders</div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--amazon-navy);">{{ $performance['total_orders'] ?? 0 }}</div>
                        <div style="font-size: 12px; color: var(--gray-500);">This month</div>
                    </div>
                    <div style="flex: 1; min-width: 50%; padding: 20px; border-bottom: 1px solid var(--gray-200);">
                        <div style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; margin-bottom: 6px;">Completion Rate</div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--amazon-success);">{{ $performance['completion_rate'] ?? 0 }}%</div>
                        <div style="font-size: 12px; color: var(--gray-500);">Orders fulfilled</div>
                    </div>
                    <div style="flex: 1; min-width: 50%; padding: 20px; border-right: 1px solid var(--gray-200);">
                        <div style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; margin-bottom: 6px;">Avg. Fulfillment</div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--amazon-warning);">{{ $performance['avg_fulfillment_hours'] ?? 0 }}<span style="font-size: 14px; font-weight: 400;"> hrs</span></div>
                        <div style="font-size: 12px; color: var(--gray-500);">Processing time</div>
                    </div>
                    <div style="flex: 1; min-width: 50%; padding: 20px;">
                        <div style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; margin-bottom: 6px;">Total Revenue</div>
                        <div style="font-size: 28px; font-weight: 700; color: var(--amazon-teal);">${{ number_format($performance['total_revenue'] ?? 0, 2) }}</div>
                        <div style="font-size: 12px; color: var(--gray-500);">Lifetime earnings</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-lg-6">
        <div class="sc-card">
            <div class="sc-card-header">
                <h3 class="sc-card-title">
                    <i class="bi bi-lightning-fill"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="sc-card-body">
                <div class="sc-actions-grid">
                    <a href="{{ route('vendor.orders') }}" class="sc-action-card">
                        <i class="bi bi-list-check"></i>
                        <span>View All Orders</span>
                    </a>
                    <a href="{{ route('vendor.products') }}" class="sc-action-card">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <span>Manage Inventory</span>
                    </a>
                    <a href="{{ route('vendor.earnings') }}" class="sc-action-card">
                        <i class="bi bi-wallet"></i>
                        <span>View Earnings</span>
                    </a>
                    <a href="{{ route('vendor.orders', ['status' => 'pending']) }}" class="sc-action-card">
                        <i class="bi bi-clock-history"></i>
                        <span>Pending Orders</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orders Needing Action -->
@if($ordersNeedingAction->count() > 0)
<div class="sc-card">
    <div class="sc-card-header">
        <h3 class="sc-card-title">
            <i class="bi bi-exclamation-triangle-fill" style="color: var(--amazon-orange);"></i>
            Orders Requiring Action
        </h3>
        <span class="sc-badge sc-badge-pending">{{ $ordersNeedingAction->count() }} Pending</span>
    </div>
    <div class="sc-card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordersNeedingAction as $order)
                    <tr>
                        <td>
                            <a href="{{ route('vendor.orders.show', $order->id) }}">
                                {{ $order->transaction->invoice_no ?? 'N/A' }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ $order->parentTransaction->contact->name ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $order->parentTransaction->shipping_city ?? '' }}</small>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>{{ $order->transaction->sell_lines->count() ?? 0 }} item(s)</td>
                        <td>
                            @php
                                $statusClass = match($order->fulfillment_status) {
                                    'pending' => 'sc-badge-pending',
                                    'vendor_notified' => 'sc-badge-pending',
                                    'vendor_accepted' => 'sc-badge-processing',
                                    'processing' => 'sc-badge-processing',
                                    'shipped' => 'sc-badge-shipped',
                                    'completed' => 'sc-badge-completed',
                                    'cancelled' => 'sc-badge-cancelled',
                                    default => 'sc-badge-pending'
                                };
                            @endphp
                            <span class="sc-badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $order->fulfillment_status)) }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($order->fulfillment_status == 'pending' || $order->fulfillment_status == 'vendor_notified')
                                <button class="sc-btn sc-btn-sm sc-btn-primary accept-order" data-id="{{ $order->id }}">
                                    <i class="bi bi-check"></i> Accept
                                </button>
                            @else
                                <button class="sc-btn sc-btn-sm sc-btn-orange add-tracking-quick" data-id="{{ $order->id }}">
                                    <i class="bi bi-truck"></i> Ship
                                </button>
                            @endif
                            <a href="{{ route('vendor.orders.show', $order->id) }}" class="sc-btn sc-btn-sm sc-btn-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Orders -->
<div class="sc-card">
    <div class="sc-card-header">
        <h3 class="sc-card-title">
            <i class="bi bi-clock-history"></i>
            Recent Orders
        </h3>
        @if($recentOrders->count() > 0)
        <a href="{{ route('vendor.orders') }}" class="sc-btn sc-btn-link">
            View All Orders <i class="bi bi-arrow-right"></i>
        </a>
        @endif
    </div>
    <div class="sc-card-body" style="padding: 0;">
        @if($recentOrders->count() > 0)
        <div class="table-responsive">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Tracking</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('vendor.orders.show', $order->id) }}">
                                {{ $order->transaction->invoice_no ?? 'N/A' }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ $order->parentTransaction->contact->name ?? '-' }}</strong>
                            <br><small class="text-muted">{{ $order->parentTransaction->shipping_city ?? '' }}</small>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            @php
                                $statusClass = match($order->fulfillment_status) {
                                    'pending' => 'sc-badge-pending',
                                    'vendor_notified' => 'sc-badge-pending',
                                    'vendor_accepted' => 'sc-badge-processing',
                                    'processing' => 'sc-badge-processing',
                                    'shipped' => 'sc-badge-shipped',
                                    'completed' => 'sc-badge-completed',
                                    'cancelled' => 'sc-badge-cancelled',
                                    default => 'sc-badge-pending'
                                };
                            @endphp
                            <span class="sc-badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $order->fulfillment_status)) }}</span>
                        </td>
                        <td>
                            @if($order->tracking_number)
                                <code style="font-size: 12px; background: var(--gray-100); padding: 4px 8px; border-radius: 4px;">{{ $order->tracking_number }}</code>
                            @else
                                <span class="text-muted fs-sm">Not added</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('vendor.orders.show', $order->id) }}" class="sc-btn sc-btn-sm sc-btn-secondary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="sc-empty">
            <i class="bi bi-inbox"></i>
            <h4>No orders yet</h4>
            <p>When customers place orders for your products, they will appear here.</p>
        </div>
        @endif
    </div>
</div>

<!-- Quick Tracking Modal -->
<div class="modal fade" id="quick-tracking-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-truck"></i> Add Tracking Information</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="quick-tracking-form">
                <div class="modal-body">
                    <input type="hidden" id="quick_tracking_order_id">
                    <div class="sc-form-group">
                        <label class="sc-form-label">Tracking Number <span class="required">*</span></label>
                        <input type="text" class="sc-form-control" id="quick_tracking_number" required placeholder="Enter tracking number">
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-form-label">Shipping Carrier</label>
                        <select class="sc-form-control" id="quick_carrier">
                            <option value="">Select carrier</option>
                            <option value="usps">USPS</option>
                            <option value="ups">UPS</option>
                            <option value="fedex">FedEx</option>
                            <option value="dhl">DHL</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sc-btn sc-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="sc-btn sc-btn-orange">
                        <i class="bi bi-truck"></i> Mark as Shipped
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Accept order
    $(document).on('click', '.accept-order', function() {
        var orderId = $(this).data('id');
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i>');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/accept',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    btn.prop('disabled', false).html('<i class="bi bi-check"></i> Accept');
                }
            },
            error: function() {
                toastr.error('Failed to accept order');
                btn.prop('disabled', false).html('<i class="bi bi-check"></i> Accept');
            }
        });
    });

    // Quick add tracking
    $(document).on('click', '.add-tracking-quick', function() {
        $('#quick_tracking_order_id').val($(this).data('id'));
        $('#quick-tracking-form')[0].reset();
        $('#quick-tracking-modal').modal('show');
    });

    $('#quick-tracking-form').on('submit', function(e) {
        e.preventDefault();
        var orderId = $('#quick_tracking_order_id').val();
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Processing...');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/ship',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tracking_number: $('#quick_tracking_number').val(),
                carrier: $('#quick_carrier').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#quick-tracking-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Mark as Shipped');
                }
            },
            error: function() {
                toastr.error('Failed to add tracking');
                submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Mark as Shipped');
            }
        });
    });
});
</script>
@endsection
