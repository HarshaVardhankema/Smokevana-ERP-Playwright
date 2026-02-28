@extends('layouts.app')
@section('title', __('Dropship Order Details'))

@section('css')
<style>
/* Modern Dropship Order Details Styles */
.dropship-order-page {
    padding: 0 10px;
}

/* Page Header */
.order-page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 24px 30px;
    margin-bottom: 24px;
    color: #fff;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.order-page-header .order-title {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    opacity: 0.85;
    margin-bottom: 8px;
    font-weight: 500;
}

.order-page-header .order-number {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.order-page-header .order-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    font-size: 14px;
    opacity: 0.9;
}

.order-page-header .order-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Order Type Badges */
.order-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.order-type-badge.wc-ds {
    background: rgba(255,255,255,0.2);
    color: #fff;
}

.order-type-badge.erp-ds {
    background: rgba(255,193,7,0.3);
    color: #fff;
}

.order-type-badge.erp {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

/* Modern Cards */
.modern-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    overflow: hidden;
    border: none;
}

.modern-card .card-header {
    background: #f8f9fc;
    padding: 18px 24px;
    border-bottom: 1px solid #eef0f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modern-card .card-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modern-card .card-header h3 i {
    color: #667eea;
    font-size: 18px;
}

.modern-card .card-body {
    padding: 24px;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.info-item {
    padding: 16px;
    background: #f8f9fc;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.info-item:hover {
    background: #f1f3f8;
}

.info-item .info-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #718096;
    margin-bottom: 6px;
    font-weight: 500;
}

.info-item .info-value {
    font-size: 15px;
    font-weight: 600;
    color: #2d3748;
}

.info-item .info-value a {
    color: #667eea;
    text-decoration: none;
}

.info-item .info-value a:hover {
    text-decoration: underline;
}

.info-item.highlight {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border: 1px solid #667eea30;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.shipped {
    background: #e0e7ff;
    color: #3730a3;
}

.status-badge.completed {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.synced {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.not-synced {
    background: #f3f4f6;
    color: #6b7280;
}

.status-badge.failed {
    background: #fee2e2;
    color: #991b1b;
}

/* Products Table */
.products-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.products-table thead th {
    background: #667eea;
    color: #fff;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.products-table thead th:first-child {
    border-radius: 8px 0 0 0;
}

.products-table thead th:last-child {
    border-radius: 0 8px 0 0;
}

.products-table tbody td {
    padding: 16px;
    border-bottom: 1px solid #eef0f5;
    vertical-align: middle;
}

.products-table tbody tr:hover {
    background: #f8f9fc;
}

.products-table .product-name {
    font-weight: 600;
    color: #2d3748;
}

.products-table .product-sku {
    font-size: 12px;
    color: #718096;
    font-family: 'Monaco', 'Consolas', monospace;
    background: #f1f3f8;
    padding: 4px 8px;
    border-radius: 4px;
}

.products-table tfoot td {
    padding: 12px 16px;
    font-weight: 600;
    background: #f8f9fc;
}

.products-table tfoot tr:last-child td {
    font-size: 16px;
    color: #667eea;
}

/* Sidebar Cards */
.sidebar-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    overflow: hidden;
}

.sidebar-card .sidebar-header {
    padding: 16px 20px;
    border-bottom: 1px solid #eef0f5;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-card .sidebar-header i {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 14px;
}

.sidebar-card .sidebar-header.customer i {
    background: #dbeafe;
    color: #1e40af;
}

.sidebar-card .sidebar-header.shipping i {
    background: #fef3c7;
    color: #92400e;
}

.sidebar-card .sidebar-header.actions i {
    background: #e0e7ff;
    color: #3730a3;
}

.sidebar-card .sidebar-header.hierarchy i {
    background: #d1fae5;
    color: #065f46;
}

.sidebar-card .sidebar-header.timeline i {
    background: #fce7f3;
    color: #9d174d;
}

.sidebar-card .sidebar-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
}

.sidebar-card .sidebar-body {
    padding: 20px;
}

/* Customer Info */
.customer-name {
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 12px;
}

.customer-contact {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.customer-contact span {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #718096;
    font-size: 14px;
}

.customer-contact span i {
    width: 18px;
    color: #667eea;
}

/* Shipping Address */
.shipping-address {
    font-size: 14px;
    line-height: 1.7;
    color: #4a5568;
}

.shipping-address strong {
    color: #2d3748;
    display: block;
    margin-bottom: 8px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    width: 100%;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.action-btn.primary:hover {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transform: translateY(-1px);
}

.action-btn.success {
    background: #10b981;
    color: #fff;
}

.action-btn.success:hover {
    background: #059669;
}

.action-btn.warning {
    background: #f59e0b;
    color: #fff;
}

.action-btn.warning:hover {
    background: #d97706;
}

.action-btn.info {
    background: #3b82f6;
    color: #fff;
}

.action-btn.info:hover {
    background: #2563eb;
}

.action-btn.outline {
    background: #fff;
    color: #667eea;
    border: 2px solid #667eea;
}

.action-btn.outline:hover {
    background: #667eea;
    color: #fff;
}

/* Order Hierarchy */
.hierarchy-section {
    padding: 0;
}

.hierarchy-parent {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
    border-radius: 8px;
    margin-bottom: 16px;
}

.hierarchy-parent i {
    color: #667eea;
}

.hierarchy-parent a {
    font-weight: 600;
    color: #667eea;
    text-decoration: none;
}

.hierarchy-children {
    margin-left: 0;
    padding-left: 0;
    list-style: none;
}

.hierarchy-child {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 16px;
    background: #f8f9fc;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}

.hierarchy-child:hover {
    background: #f1f3f8;
}

.hierarchy-child.current {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border: 1px solid #667eea30;
}

.hierarchy-child i {
    color: #718096;
}

.hierarchy-child a {
    font-weight: 600;
    color: #2d3748;
    text-decoration: none;
}

.hierarchy-child a:hover {
    color: #667eea;
}

.type-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.type-badge.wc { background: #dbeafe; color: #1e40af; }
.type-badge.erp-ds { background: #fef3c7; color: #92400e; }
.type-badge.erp { background: #e5e7eb; color: #4b5563; }
.type-badge.current { background: #d1fae5; color: #065f46; }

.hierarchy-summary {
    display: flex;
    justify-content: space-around;
    padding: 12px;
    background: #f8f9fc;
    border-radius: 8px;
    margin-top: 16px;
}

.hierarchy-stat {
    text-align: center;
}

.hierarchy-stat .stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
}

.hierarchy-stat .stat-label {
    font-size: 11px;
    text-transform: uppercase;
    color: #718096;
    letter-spacing: 0.5px;
}

.hierarchy-stat.completed .stat-value { color: #10b981; }
.hierarchy-stat.pending .stat-value { color: #f59e0b; }
.hierarchy-stat.shipped .stat-value { color: #3b82f6; }

/* Timeline */
.modern-timeline {
    position: relative;
    padding-left: 30px;
}

.modern-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e5e7eb;
}

.timeline-event {
    position: relative;
    padding-bottom: 20px;
}

.timeline-event:last-child {
    padding-bottom: 0;
}

.timeline-event::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #667eea;
}

.timeline-event.completed::before {
    background: #10b981;
    box-shadow: 0 0 0 2px #10b981;
}

.timeline-event .event-title {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
    margin-bottom: 4px;
}

.timeline-event .event-time {
    font-size: 12px;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 4px;
}

.timeline-event .event-detail {
    font-size: 13px;
    color: #718096;
    margin-top: 4px;
    padding: 8px 12px;
    background: #f8f9fc;
    border-radius: 6px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 30px;
    color: #718096;
}

.empty-state i {
    font-size: 36px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .order-page-header .order-number {
        font-size: 22px;
    }
    
    .order-page-header .order-meta {
        flex-direction: column;
        gap: 8px;
    }
}

/* Modal Enhancements */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.modal-header {
    background: #f8f9fc;
    border-radius: 12px 12px 0 0;
    padding: 20px 24px;
    border-bottom: 1px solid #eef0f5;
}

.modal-title {
    font-weight: 600;
    color: #2d3748;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #eef0f5;
}
</style>
@endsection

@section('content')

<div class="dropship-order-page">
    @php
        $orderType = $tracking->transaction->sub_type ?? 'erp_dropship_order';
        $typeLabel = 'ERP Dropship';
        $typeBadgeClass = 'erp-ds';
        
        if ($orderType == 'wp_sales_order') {
            $typeLabel = 'WooCommerce Dropship';
            $typeBadgeClass = 'wc-ds';
        } elseif ($orderType == 'erp_sales_order') {
            $typeLabel = 'ERP In-House';
            $typeBadgeClass = 'erp';
        }
    @endphp

    <!-- Page Header -->
    <div class="order-page-header">
        <div class="order-title">Dropship Order Details</div>
        <div class="order-number">
            {{ $tracking->transaction->invoice_no ?? 'N/A' }}
            <span class="order-type-badge {{ $typeBadgeClass }}">
                <i class="fas fa-{{ $orderType == 'wp_sales_order' ? 'cloud' : 'store' }}"></i>
                {{ $typeLabel }}
            </span>
        </div>
        <div class="order-meta">
            <span><i class="fas fa-calendar"></i> {{ $tracking->created_at->format('M d, Y') }} at {{ $tracking->created_at->format('h:i A') }}</span>
            <span><i class="fas fa-store"></i> {{ $tracking->vendor->display_name ?? 'N/A' }}</span>
            <span><i class="fas fa-user"></i> {{ $tracking->parentTransaction->contact->name ?? 'Walk-In Customer' }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-md-7">
            <!-- Order Information Card -->
            <div class="modern-card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Order Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Order Number</div>
                            <div class="info-value">{{ $tracking->transaction->invoice_no ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Parent Order</div>
                            <div class="info-value">
                                <a href="javascript:void(0)" class="view-parent-order-modal" data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], $tracking->parent_transaction_id) }}">
                                    {{ $tracking->parentTransaction->invoice_no ?? 'N/A' }}
                                </a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Vendor</div>
                            <div class="info-value">
                                <a href="{{ route('dropship.vendors.show', $tracking->vendor->id ?? 0) }}">
                                    {{ $tracking->vendor->display_name ?? 'N/A' }}
                                </a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">WooCommerce Order ID</div>
                            <div class="info-value">
                                @if($tracking->woocommerce_order_id)
                                    #{{ $tracking->woocommerce_order_id }}
                                @else
                                    <span style="color: #718096;">Not synced</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Fulfillment Status</div>
                            <div class="info-value">{!! $tracking->status_badge !!}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Sync Status</div>
                            <div class="info-value">{!! $tracking->sync_status_badge !!}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tracking Number</div>
                            <div class="info-value">
                                @if($tracking->tracking_number)
                                    @if($tracking->tracking_url)
                                        <a href="{{ $tracking->tracking_url }}" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> {{ $tracking->tracking_number }}
                                        </a>
                                    @else
                                        {{ $tracking->tracking_number }}
                                    @endif
                                    @if($tracking->carrier)
                                        <br><small style="color: #718096;">via {{ $tracking->carrier }}</small>
                                    @endif
                                @else
                                    <span style="color: #718096;">No tracking yet</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item highlight">
                            <div class="info-label">Order Total</div>
                            <div class="info-value" style="font-size: 20px; color: #667eea;">
                                <span class="display_currency" data-currency_symbol="true">{{ $tracking->transaction->final_total ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="modern-card">
                <div class="card-header">
                    <h3><i class="fas fa-box-open"></i> Order Items</h3>
                    <span style="font-size: 13px; color: #718096;">
                        {{ count($tracking->transaction->sell_lines ?? []) }} item(s)
                    </span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Product</th>
                                    <th>SKU</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tracking->transaction->sell_lines ?? [] as $line)
                                <tr>
                                    <td>
                                        <div class="product-name">{{ $line->product->name ?? 'N/A' }}</div>
                                        @if($line->variation_id && $line->variation && $line->variation->name != 'DUMMY')
                                            <small style="color: #718096;">{{ $line->variation->name ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="product-sku">{{ $line->product->sku ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ (int)$line->quantity }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <span class="display_currency" data-currency_symbol="true">{{ $line->unit_price_inc_tax ?? $line->unit_price }}</span>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            <span class="display_currency" data-currency_symbol="true">{{ ($line->unit_price_inc_tax ?? $line->unit_price) * $line->quantity }}</span>
                                        </strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center" style="padding: 40px;">
                                        <div class="empty-state">
                                            <i class="fas fa-box-open"></i>
                                            <p>No items in this order</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($tracking->transaction->sell_lines ?? []) > 0)
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right">Subtotal</td>
                                    <td class="text-right">
                                        <span class="display_currency" data-currency_symbol="true">{{ $tracking->transaction->total_before_tax ?? 0 }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Tax</td>
                                    <td class="text-right">
                                        <span class="display_currency" data-currency_symbol="true">{{ $tracking->transaction->tax_amount ?? 0 }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right" style="font-size: 16px;">Total</td>
                                    <td class="text-right" style="font-size: 18px; color: #667eea;">
                                        <span class="display_currency" data-currency_symbol="true">{{ $tracking->transaction->final_total ?? 0 }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 col-md-5">
            <!-- Customer Information -->
            <div class="sidebar-card">
                <div class="sidebar-header customer">
                    <i class="fas fa-user"></i>
                    <h4>Customer Information</h4>
                </div>
                <div class="sidebar-body">
                    @php
                        $customer = $tracking->parentTransaction->contact ?? null;
                    @endphp

                    @if($customer)
                        <div class="customer-name">{{ $customer->name }}</div>
                        <div class="customer-contact">
                            @if($customer->email)
                                <span><i class="fas fa-envelope"></i> {{ $customer->email }}</span>
                            @endif
                            @if($customer->mobile)
                                <span><i class="fas fa-phone"></i> {{ $customer->mobile }}</span>
                            @endif
                            @if(!$customer->email && !$customer->mobile)
                                <span class="text-muted">No contact details</span>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p>No customer information</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="sidebar-card">
                <div class="sidebar-header shipping">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>Shipping Address</h4>
                </div>
                <div class="sidebar-body">
                    @php
                        $parent = $tracking->parentTransaction;
                    @endphp

                    @if($parent && ($parent->shipping_address1 || $parent->shipping_city || $parent->shipping_country))
                        <div class="shipping-address">
                            @if($parent->shipping_first_name || $parent->shipping_last_name)
                                <strong>{{ trim($parent->shipping_first_name . ' ' . $parent->shipping_last_name) }}</strong>
                            @endif
                            @if($parent->shipping_company)
                                {{ $parent->shipping_company }}<br>
                            @endif
                            @if($parent->shipping_address1)
                                {{ $parent->shipping_address1 }}<br>
                            @endif
                            @if($parent->shipping_address2)
                                {{ $parent->shipping_address2 }}<br>
                            @endif
                            @if($parent->shipping_city || $parent->shipping_state || $parent->shipping_zip)
                                {{ $parent->shipping_city }}{{ $parent->shipping_state ? ', ' . $parent->shipping_state : '' }} {{ $parent->shipping_zip }}<br>
                            @endif
                            @if($parent->shipping_country)
                                {{ $parent->shipping_country }}
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>No shipping address</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="sidebar-card">
                <div class="sidebar-header actions">
                    <i class="fas fa-bolt"></i>
                    <h4>Quick Actions</h4>
                </div>
                <div class="sidebar-body">
                    <div class="action-buttons">
                        @if($tracking->isWooCommerceManagedOrder())
                            {{-- WooCommerce managed order - show info message --}}
                            <div class="wc-managed-notice" style="background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%); border: 1px solid #3b82f6; border-radius: 10px; padding: 16px; margin-bottom: 12px;">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <i class="fab fa-wordpress" style="font-size: 24px; color: #3b82f6;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #1e40af; margin-bottom: 4px;">WooCommerce Managed Order</div>
                                        <div style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                                            This order is managed by the WooCommerce vendor. Status updates, tracking info, and fulfillment details will automatically sync from WooCommerce.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($tracking->sync_status === 'pending')
                                <div class="sync-pending-notice" style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 12px; margin-bottom: 12px; text-align: center;">
                                    <i class="fas fa-spinner fa-spin" style="color: #f59e0b;"></i>
                                    <span style="font-size: 13px; color: #92400e; margin-left: 8px;">Syncing to WooCommerce...</span>
                                </div>
                            @endif
                            
                            @if($tracking->sync_status === 'failed')
                                <div class="sync-failed-notice" style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                                        <span style="font-weight: 600; color: #991b1b;">Sync Failed</span>
                                    </div>
                                    @if($tracking->sync_error_message)
                                        <div style="font-size: 12px; color: #991b1b; margin-bottom: 8px;">{{ $tracking->sync_error_message }}</div>
                                    @endif
                                    <button class="action-btn info" id="retry-sync-btn" style="width: 100%;">
                                        <i class="fas fa-redo"></i> Retry Sync to WooCommerce
                                    </button>
                                </div>
                            @endif
                        @elseif($tracking->isERPDropshipOrder())
                            {{-- ERP Dropship (Vendor Dropship) order - read-only for admin --}}
                            <div class="vendor-managed-notice" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; border-radius: 10px; padding: 16px; margin-bottom: 12px;">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <i class="fas fa-store" style="font-size: 24px; color: #d97706;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #92400e; margin-bottom: 4px;">Vendor Managed Order</div>
                                        <div style="font-size: 12px; color: #78350f; line-height: 1.5;">
                                            This order is managed by the vendor through their portal. Status updates and tracking information are provided by the vendor. This page is read-only for ERP admin.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- In-house order - allow manual actions --}}
                            @if($tracking->canEdit())
                                <button class="action-btn warning" id="update-status-btn">
                                    <i class="fas fa-sync-alt"></i> Update Status
                                </button>
                            @endif

                            @if($tracking->canAddTracking())
                                <button class="action-btn success" id="add-tracking-btn">
                                    <i class="fas fa-truck"></i> Add Tracking
                                </button>
                            @endif

                            @if($tracking->sync_status === 'failed')
                                <button class="action-btn info" id="retry-sync-btn">
                                    <i class="fas fa-redo"></i> Retry Sync
                                </button>
                            @endif
                        @endif

                        <a href="{{ route('dropship.orders.index') }}" class="action-btn outline">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Hierarchy -->
            @if(isset($hierarchy) && $hierarchy)
            <div class="sidebar-card">
                <div class="sidebar-header hierarchy">
                    <i class="fas fa-sitemap"></i>
                    <h4>Order Hierarchy</h4>
                </div>
                <div class="sidebar-body hierarchy-section">
                    {{-- Parent Order --}}
                    @if(isset($hierarchy['parent']))
                    <div class="hierarchy-parent">
                        <i class="fas fa-folder-open"></i>
                        <span>Parent:</span>
                        <a href="javascript:void(0)" class="view-parent-order-modal" data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], $hierarchy['parent']->id) }}">
                            {{ $hierarchy['parent']->invoice_no }}
                        </a>
                        <span class="type-badge" style="background: #667eea; color: #fff;">SO</span>
                    </div>
                    @endif

                    {{-- Child Orders --}}
                    @if(isset($hierarchy['children']) && $hierarchy['children']->count() > 0)
                    <div style="font-size: 12px; color: #718096; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-code-branch"></i> Child Orders
                    </div>
                    <ul class="hierarchy-children">
                        @foreach($hierarchy['children'] as $child)
                            @php
                                $isCurrentOrder = ($child->id == $tracking->transaction_id);
                                $childTracking = $child->tracking ?? null;
                                $childType = $child->sub_type ?? 'erp_sales_order';
                                
                                if ($childType == 'wp_sales_order') {
                                    $badgeClass = 'wc';
                                    $badgeText = 'WC-DS';
                                    $icon = 'cloud';
                                } elseif ($childType == 'erp_dropship_order') {
                                    $badgeClass = 'erp-ds';
                                    $badgeText = 'VDS';
                                    $icon = 'store';
                                } else {
                                    $badgeClass = 'erp';
                                    $badgeText = 'ERP';
                                    $icon = 'warehouse';
                                }
                            @endphp
                            <li class="hierarchy-child {{ $isCurrentOrder ? 'current' : '' }}">
                                <i class="fas fa-{{ $icon }}"></i>
                                @if($childTracking && in_array($childType, ['wp_sales_order', 'erp_dropship_order']))
                                    <a href="{{ route('dropship.orders.show', $childTracking->id) }}">
                                        {{ $child->invoice_no }}
                                    </a>
                                @else
                                    <span style="font-weight: 600;">{{ $child->invoice_no }}</span>
                                @endif
                                <span class="type-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                @if($childTracking)
                                    {!! $childTracking->status_badge !!}
                                @endif
                                @if($isCurrentOrder)
                                    <span class="type-badge current">Current</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- Summary --}}
                    @if(isset($hierarchy['summary']))
                    <div class="hierarchy-summary">
                        <div class="hierarchy-stat">
                            <div class="stat-value">{{ $hierarchy['summary']['total_children'] }}</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="hierarchy-stat completed">
                            <div class="stat-value">{{ $hierarchy['summary']['completed'] }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="hierarchy-stat pending">
                            <div class="stat-value">{{ $hierarchy['summary']['pending'] }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="hierarchy-stat shipped">
                            <div class="stat-value">{{ $hierarchy['summary']['shipped'] }}</div>
                            <div class="stat-label">Shipped</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="sidebar-card">
                <div class="sidebar-header timeline">
                    <i class="fas fa-history"></i>
                    <h4>Timeline</h4>
                </div>
                <div class="sidebar-body">
                    <div class="modern-timeline">
                        @if($tracking->order_placed_at)
                        <div class="timeline-event">
                            <div class="event-title">Order Placed</div>
                            <div class="event-time">
                                <i class="fas fa-clock"></i> {{ $tracking->order_placed_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif

                        @if($tracking->vendor_notified_at)
                        <div class="timeline-event">
                            <div class="event-title">Vendor Notified</div>
                            <div class="event-time">
                                <i class="fas fa-clock"></i> {{ $tracking->vendor_notified_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif

                        @if($tracking->shipped_at)
                        <div class="timeline-event">
                            <div class="event-title">Shipped</div>
                            <div class="event-time">
                                <i class="fas fa-clock"></i> {{ $tracking->shipped_at->format('M d, Y \a\t h:i A') }}
                            </div>
                            @if($tracking->tracking_number)
                            <div class="event-detail">
                                <i class="fas fa-truck"></i> Tracking: {{ $tracking->tracking_number }}
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($tracking->delivered_at)
                        <div class="timeline-event completed">
                            <div class="event-title">Delivered</div>
                            <div class="event-time">
                                <i class="fas fa-clock"></i> {{ $tracking->delivered_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif

                        @if($tracking->completed_at)
                        <div class="timeline-event completed">
                            <div class="event-title">Completed</div>
                            <div class="event-time">
                                <i class="fas fa-clock"></i> {{ $tracking->completed_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif

                        @if(!$tracking->order_placed_at && !$tracking->shipped_at && !$tracking->delivered_at && !$tracking->completed_at)
                        <div class="empty-state">
                            <i class="fas fa-hourglass-start"></i>
                            <p>No events yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Tracking Modal --}}
<div class="modal fade" id="add-tracking-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fas fa-truck" style="color: #10b981;"></i> Add Tracking Information</h4>
            </div>
            <form id="tracking-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: 600;">Tracking Number <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tracking_number" class="form-control" required placeholder="Enter tracking number">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600;">Carrier</label>
                        <select name="carrier" class="form-control">
                            <option value="">Select Carrier</option>
                            <option value="USPS">USPS</option>
                            <option value="UPS">UPS</option>
                            <option value="FedEx">FedEx</option>
                            <option value="DHL">DHL</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600;">Tracking URL</label>
                        <input type="url" name="carrier_tracking_url" class="form-control" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600;">Shipping Cost</label>
                        <input type="number" name="shipping_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn outline" data-dismiss="modal" style="width: auto;">Cancel</button>
                    <button type="submit" class="action-btn success" style="width: auto;">
                        <i class="fas fa-check"></i> Save Tracking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="update-status-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fas fa-sync-alt" style="color: #f59e0b;"></i> Update Order Status</h4>
            </div>
            <form id="status-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: 600;">Status <span style="color: #ef4444;">*</span></label>
                        <select name="fulfillment_status" class="form-control" required>
                            <option value="pending" {{ $tracking->fulfillment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="vendor_notified" {{ $tracking->fulfillment_status == 'vendor_notified' ? 'selected' : '' }}>Vendor Notified</option>
                            <option value="vendor_accepted" {{ $tracking->fulfillment_status == 'vendor_accepted' ? 'selected' : '' }}>Vendor Accepted</option>
                            <option value="processing" {{ $tracking->fulfillment_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="ready_to_ship" {{ $tracking->fulfillment_status == 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                            <option value="shipped" {{ $tracking->fulfillment_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="in_transit" {{ $tracking->fulfillment_status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivered" {{ $tracking->fulfillment_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="completed" {{ $tracking->fulfillment_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $tracking->fulfillment_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600;">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn outline" data-dismiss="modal" style="width: auto;">Cancel</button>
                    <button type="submit" class="action-btn warning" style="width: auto;">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Parent Order View Modal --}}
<div class="modal fade" id="view-parent-order-modal" tabindex="-1" role="dialog">
    {{-- Content will be loaded via AJAX --}}
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center; padding: 60px;">
                <i class="fas fa-spinner fa-spin fa-3x" style="color: #667eea;"></i>
                <p style="margin-top: 16px; color: #718096;">Loading order details...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // View Parent Order Modal
    $(document).on('click', '.view-parent-order-modal', function() {
        var href = $(this).data('href');
        var $modal = $('#view-parent-order-modal');
        
        // Show loading state
        $modal.find('.modal-dialog').html('<div class="modal-content"><div class="modal-body" style="text-align: center; padding: 60px;"><i class="fas fa-spinner fa-spin fa-3x" style="color: #667eea;"></i><p style="margin-top: 16px; color: #718096;">Loading order details...</p></div></div>');
        
        // Show modal
        $modal.modal('show');
        
        // Load content via AJAX
        $.ajax({
            url: href,
            method: 'GET',
            success: function(response) {
                // The response contains a complete modal-dialog, so replace the content
                $modal.html(response);
                // Reinitialize modal backdrop
                $modal.modal('handleUpdate');
                // Initialize any currency display elements
                if (typeof __currency_trans_from_en !== 'undefined') {
                    $modal.find('.display_currency').each(function() {
                        __currency_trans_from_en($(this), false);
                    });
                }
            },
            error: function(xhr, status, error) {
                $modal.find('.modal-dialog').html('<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title">Error</h4></div><div class="modal-body" style="text-align: center; padding: 60px;"><i class="fas fa-exclamation-triangle fa-3x" style="color: #ef4444;"></i><p style="margin-top: 16px; color: #991b1b;">Failed to load order details</p><p style="color: #718096; font-size: 13px;">' + (xhr.responseJSON?.message || error) + '</p></div></div>');
            }
        });
    });

    // Add Tracking
    $('#add-tracking-btn').click(function() {
        $('#add-tracking-modal').modal('show');
    });

    $('#tracking-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("dropship.orders.add-tracking", $tracking->id) }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#add-tracking-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Tracking');
                }
            },
            error: function() {
                toastr.error('Failed to add tracking');
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Save Tracking');
            }
        });
    });

    // Update Status
    $('#update-status-btn').click(function() {
        $('#update-status-modal').modal('show');
    });

    $('#status-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: '{{ route("dropship.orders.update-status", $tracking->id) }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#update-status-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Update Status');
                }
            },
            error: function() {
                toastr.error('Failed to update status');
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Update Status');
            }
        });
    });

    // Retry Sync
    $('#retry-sync-btn').click(function() {
        if (!confirm('Retry syncing this order to WooCommerce?')) return;
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
        
        $.ajax({
            url: '{{ route("dropship.orders.retry-sync", $tracking->id) }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-redo"></i> Retry Sync');
                }
            },
            error: function() {
                toastr.error('Failed to retry sync');
                $btn.prop('disabled', false).html('<i class="fas fa-redo"></i> Retry Sync');
            }
        });
    });
});
</script>
@endsection
