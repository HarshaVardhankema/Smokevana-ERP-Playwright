@extends('layouts.vendor_portal')
@section('title', 'Order Details')
@section('page_title', 'Order Details')

@section('css')
<style>
.order-header {
    background: linear-gradient(135deg, var(--vp-dark) 0%, #312e81 100%);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    color: #fff;
}

.order-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
}

.order-id-box {
    display: flex;
    align-items: center;
    gap: 16px;
}

.order-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #8b5cf6, #ec4899);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.order-id-text h2 {
    margin: 0 0 4px 0;
    font-size: 22px;
    font-weight: 700;
}

.order-id-text span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
}

.order-actions {
    display: flex;
    gap: 10px;
}

.back-btn {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    text-decoration: none;
}

.detail-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--vp-gray-200);
    margin-bottom: 24px;
}

.detail-card-header {
    padding: 18px 24px;
    background: var(--vp-gray-50);
    border-bottom: 1px solid var(--vp-gray-200);
}

.detail-card-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--vp-gray-800);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-card-title i {
    color: var(--vp-primary);
}

.detail-card-body {
    padding: 24px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--vp-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 15px;
    color: var(--vp-gray-800);
    font-weight: 500;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead th {
    background: var(--vp-gray-100);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--vp-gray-600);
    padding: 12px 14px;
    text-align: left;
}

.items-table tbody td {
    padding: 14px;
    border-bottom: 1px solid var(--vp-gray-200);
    font-size: 14px;
    color: var(--vp-gray-700);
}

.items-table tbody tr:last-child td {
    border-bottom: none;
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--vp-gray-100);
}

.product-name {
    font-weight: 600;
    color: var(--vp-gray-800);
}

.product-sku {
    font-size: 12px;
    color: var(--vp-gray-500);
}

.total-row {
    background: var(--vp-gray-50);
}

.total-row td {
    font-weight: 600;
}

.action-section {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 14px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}

.action-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    flex-shrink: 0;
}

.action-content {
    flex: 1;
}

.action-content h4 {
    margin: 0 0 4px 0;
    color: #92400e;
    font-size: 16px;
}

.action-content p {
    margin: 0;
    color: #a16207;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 10px;
    bottom: 10px;
    width: 2px;
    background: var(--vp-gray-200);
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -26px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid var(--vp-gray-300);
}

.timeline-item.active .timeline-dot {
    background: var(--vp-primary);
    border-color: var(--vp-primary);
}

.timeline-item.completed .timeline-dot {
    background: var(--vp-success);
    border-color: var(--vp-success);
}

.timeline-content h5 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--vp-gray-800);
}

.timeline-content span {
    font-size: 12px;
    color: var(--vp-gray-500);
}

.tracking-info {
    background: var(--vp-gray-50);
    border-radius: 10px;
    padding: 16px;
    margin-top: 16px;
}

.tracking-info h5 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--vp-gray-700);
}

.tracking-number {
    font-family: monospace;
    font-size: 16px;
    font-weight: 600;
    color: var(--vp-primary);
    background: var(--vp-gray-100);
    padding: 8px 14px;
    border-radius: 6px;
    display: inline-block;
}
</style>
@endsection

@section('content')
<!-- Order Header -->
<div class="order-header">
    <div class="order-header-top">
        <div class="order-id-box">
            <div class="order-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="order-id-text">
                <h2>{{ $order->transaction->invoice_no ?? 'N/A' }}</h2>
                <span>Created {{ $order->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
        <div class="order-actions">
            <a href="{{ route('vendor.orders') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
</div>

<!-- Action Required Section -->
@if(in_array($order->fulfillment_status, ['pending', 'vendor_notified']))
<div class="action-section">
    <div class="action-icon">
        <i class="bi bi-exclamation-triangle"></i>
    </div>
    <div class="action-content">
        <h4>Action Required</h4>
        <p>This order is waiting for your acceptance. Please accept it to start processing.</p>
    </div>
    <div class="action-buttons">
        <button class="vp-btn vp-btn-success accept-order-btn" data-id="{{ $order->id }}">
            <i class="bi bi-check"></i> Accept Order
        </button>
    </div>
</div>
@elseif(in_array($order->fulfillment_status, ['vendor_accepted', 'processing', 'ready_to_ship']) && !$order->tracking_number)
<div class="action-section">
    <div class="action-icon">
        <i class="bi bi-truck"></i>
    </div>
    <div class="action-content">
        <h4>Ready to Ship?</h4>
        <p>Add tracking information to mark this order as shipped.</p>
    </div>
    <div class="action-buttons">
        <button class="vp-btn vp-btn-primary add-tracking-btn" data-id="{{ $order->id }}">
            <i class="bi bi-truck"></i> Add Tracking
        </button>
    </div>
</div>
@endif

<div class="row">
    <!-- Order Info -->
    <div class="col-lg-8">
        <!-- Customer & Shipping Info -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h3 class="detail-card-title">
                    <i class="bi bi-person"></i> Customer & Shipping
                </h3>
            </div>
            <div class="detail-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Customer Name</span>
                        <span class="info-value">{{ $order->parentTransaction->contact->name ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $order->parentTransaction->contact->email ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $order->parentTransaction->contact->mobile ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Shipping Address</span>
                        <span class="info-value">
                            {{ $order->parentTransaction->shipping_address ?? '' }}
                            @if($order->parentTransaction->shipping_city)
                            <br>{{ $order->parentTransaction->shipping_city }}, {{ $order->parentTransaction->shipping_state ?? '' }} {{ $order->parentTransaction->shipping_zip ?? '' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h3 class="detail-card-title">
                    <i class="bi bi-box"></i> Order Items
                </h3>
            </div>
            <div class="detail-card-body" style="padding: 0;">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lines = $order->transaction->sell_lines ?? collect();
                        @endphp
                        @forelse($lines as $line)
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="{{ $line->product->image ? asset('uploads/img/' . $line->product->image) : asset('img/default-product.png') }}" 
                                         class="product-img" 
                                         alt="{{ $line->product->name ?? '' }}"
                                         onerror="this.src='{{ asset('img/default-product.png') }}'">
                                    <div>
                                        <div class="product-name">{{ $line->product->name ?? 'Unknown Product' }}</div>
                                        <div class="product-sku">SKU: {{ $line->product->sku ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $line->quantity }}</td>
                            <td>${{ number_format($line->unit_price ?? 0, 2) }}</td>
                            <td style="text-align: right;">${{ number_format(($line->unit_price ?? 0) * $line->quantity, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--vp-gray-500);">No items found</td>
                        </tr>
                        @endforelse
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;">Total:</td>
                            <td style="text-align: right; font-size: 16px; color: var(--vp-primary);">
                                ${{ number_format($order->transaction->final_total ?? 0, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h3 class="detail-card-title">
                    <i class="bi bi-info-circle"></i> Status
                </h3>
            </div>
            <div class="detail-card-body">
                <div style="margin-bottom: 16px;">
                    {!! $order->status_badge !!}
                </div>
                
                <div class="timeline">
                    <div class="timeline-item {{ in_array($order->fulfillment_status, ['pending', 'vendor_notified']) ? 'active' : 'completed' }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h5>Order Created</h5>
                            <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    
                    @if($order->accepted_at || in_array($order->fulfillment_status, ['vendor_accepted', 'processing', 'ready_to_ship', 'shipped', 'in_transit', 'delivered', 'completed']))
                    <div class="timeline-item {{ in_array($order->fulfillment_status, ['vendor_accepted', 'processing', 'ready_to_ship']) && !$order->shipped_at ? 'active' : 'completed' }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h5>Accepted</h5>
                            <span>{{ $order->accepted_at ? $order->accepted_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->shipped_at || in_array($order->fulfillment_status, ['shipped', 'in_transit', 'delivered', 'completed']))
                    <div class="timeline-item {{ in_array($order->fulfillment_status, ['shipped', 'in_transit']) ? 'active' : 'completed' }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h5>Shipped</h5>
                            <span>{{ $order->shipped_at ? $order->shipped_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->delivered_at || $order->completed_at || in_array($order->fulfillment_status, ['delivered', 'completed']))
                    <div class="timeline-item {{ in_array($order->fulfillment_status, ['delivered', 'completed']) ? 'completed' : '' }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h5>Delivered</h5>
                            <span>{{ $order->delivered_at ? $order->delivered_at->format('M d, Y h:i A') : ($order->completed_at ? $order->completed_at->format('M d, Y h:i A') : '-') }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                @if($order->tracking_number)
                <div class="tracking-info">
                    <h5><i class="bi bi-truck"></i> Tracking Information</h5>
                    <div class="tracking-number">{{ $order->tracking_number }}</div>
                    @if($order->carrier)
                    <p style="margin: 10px 0 0 0; font-size: 13px; color: var(--vp-gray-600);">
                        Carrier: {{ strtoupper($order->carrier) }}
                    </p>
                    @endif
                    @if($order->carrier_tracking_url)
                    <p style="margin: 8px 0 0 0;">
                        <a href="{{ $order->carrier_tracking_url }}" target="_blank" class="vp-btn vp-btn-outline" style="font-size: 12px; padding: 6px 12px;">
                            <i class="bi bi-box-arrow-up-right"></i> Track Package
                        </a>
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Your Earnings -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h3 class="detail-card-title">
                    <i class="bi bi-wallet"></i> Your Earnings
                </h3>
            </div>
            <div class="detail-card-body">
                <div style="text-align: center; padding: 10px;">
                    <div style="font-size: 28px; font-weight: 700; color: var(--vp-success);">
                        ${{ number_format($order->vendor_payout_amount ?? 0, 2) }}
                    </div>
                    <div style="font-size: 13px; color: var(--vp-gray-500); margin-top: 4px;">
                        @if($order->vendor_payout_status == 'paid')
                            <span style="color: var(--vp-success);"><i class="bi bi-check-circle-fill"></i> Paid</span>
                        @else
                            <span style="color: var(--vp-warning);"><i class="bi bi-clock"></i> Pending Payout</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div class="modal fade" id="tracking-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-truck"></i> Add Tracking Information</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="tracking-form">
                <div class="modal-body">
                    <input type="hidden" id="modal_order_id" value="{{ $order->id }}">
                    <div class="vp-form-group">
                        <label class="vp-form-label">Tracking Number *</label>
                        <input type="text" class="vp-form-control" id="modal_tracking_number" required placeholder="Enter tracking number">
                    </div>
                    <div class="vp-form-group">
                        <label class="vp-form-label">Carrier</label>
                        <select class="vp-form-control" id="modal_carrier">
                            <option value="">Select carrier</option>
                            <option value="usps">USPS</option>
                            <option value="ups">UPS</option>
                            <option value="fedex">FedEx</option>
                            <option value="dhl">DHL</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="vp-form-group">
                        <label class="vp-form-label">Custom Tracking URL (optional)</label>
                        <input type="url" class="vp-form-control" id="modal_tracking_url" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="vp-btn vp-btn-outline" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="vp-btn vp-btn-success">
                        <i class="bi bi-truck"></i> Ship Order
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
    $('.accept-order-btn').on('click', function() {
        var orderId = $(this).data('id');
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Processing...');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/accept',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    btn.prop('disabled', false).html('<i class="bi bi-check"></i> Accept Order');
                }
            },
            error: function() {
                toastr.error('Failed to accept order');
                btn.prop('disabled', false).html('<i class="bi bi-check"></i> Accept Order');
            }
        });
    });

    // Show tracking modal
    $('.add-tracking-btn').on('click', function() {
        $('#tracking-form')[0].reset();
        $('#tracking-modal').modal('show');
    });

    // Submit tracking
    $('#tracking-form').on('submit', function(e) {
        e.preventDefault();
        var orderId = $('#modal_order_id').val();
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Processing...');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/ship',
            method: 'POST',
            data: {
                tracking_number: $('#modal_tracking_number').val(),
                carrier: $('#modal_carrier').val(),
                carrier_tracking_url: $('#modal_tracking_url').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Ship Order');
                }
            },
            error: function() {
                toastr.error('Failed to add tracking');
                submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Ship Order');
            }
        });
    });
});
</script>
@endsection
