@extends('layouts.vendor_portal')
@section('title', 'Purchase Order Details')

@section('css')
<style>
/* Page Header */
.page-header {
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--gray-600);
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    color: var(--gray-700);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

/* Info Cards */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.info-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 20px;
}

.info-card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--gray-200);
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-label {
    color: var(--gray-600);
}

.info-value {
    font-weight: 600;
    color: var(--gray-900);
}

/* Table Card */
.table-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    overflow: hidden;
}

.table-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-200);
}

.table-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
}

.table-card-body {
    padding: 0;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead th {
    background: var(--gray-100);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--gray-700);
    padding: 12px 14px;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
}

.items-table tbody td {
    padding: 14px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 13px;
    color: var(--gray-700);
}

.items-table tbody tr:hover {
    background: #fffbf3;
}

.items-table tbody tr:last-child td {
    border-bottom: none;
}

.items-table tfoot td {
    padding: 14px;
    font-weight: 700;
    background: var(--gray-50);
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-warning { background: #fff3cd; color: #856404; }
.badge-success { background: #d4edda; color: #155724; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-secondary { background: #e2e6ea; color: #383d41; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Purchase Order: {{ $purchaseOrder->ref_no ?? $purchaseOrder->invoice_no }}</h1>
        <p class="page-subtitle">View purchase order details and line items</p>
    </div>
    <a href="{{ route('vendor.purchase-orders') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to Purchase Orders
    </a>
</div>

<!-- Order Info -->
<div class="info-grid">
    <div class="info-card">
        <h3 class="info-card-title"><i class="bi bi-receipt"></i> Order Information</h3>
        <div class="info-row">
            <span class="info-label">PO Number:</span>
            <span class="info-value">{{ $purchaseOrder->ref_no ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Invoice Number:</span>
            <span class="info-value">{{ $purchaseOrder->invoice_no ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($purchaseOrder->transaction_date)->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                @php
                    $statusClasses = [
                        'draft' => 'badge-secondary',
                        'ordered' => 'badge-info',
                        'partial' => 'badge-warning',
                        'received' => 'badge-success',
                    ];
                    $class = $statusClasses[$purchaseOrder->status] ?? 'badge-secondary';
                @endphp
                <span class="badge {{ $class }}">{{ ucfirst($purchaseOrder->status ?? 'N/A') }}</span>
            </span>
        </div>
    </div>
    
    <div class="info-card">
        <h3 class="info-card-title"><i class="bi bi-currency-dollar"></i> Payment Information</h3>
        <div class="info-row">
            <span class="info-label">Payment Status:</span>
            <span class="info-value">
                @php
                    $paymentClasses = [
                        'paid' => 'badge-success',
                        'partial' => 'badge-warning',
                        'due' => 'badge-danger',
                    ];
                    $pClass = $paymentClasses[$purchaseOrder->payment_status] ?? 'badge-secondary';
                @endphp
                <span class="badge {{ $pClass }}">{{ ucfirst($purchaseOrder->payment_status ?? 'N/A') }}</span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value">${{ number_format($purchaseOrder->final_total ?? 0, 2) }}</span>
        </div>
    </div>
</div>

<!-- Line Items -->
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title"><i class="bi bi-boxes"></i> Line Items</h3>
    </div>
    <div class="table-card-body">
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Variation</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseLines as $index => $line)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $line->product_name }}</strong></td>
                    <td><code>{{ $line->product_sku ?? $line->sub_sku ?? 'N/A' }}</code></td>
                    <td>{{ $line->variation_name != 'DUMMY' ? $line->variation_name : '-' }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>${{ number_format($line->purchase_price ?? 0, 2) }}</td>
                    <td><strong>${{ number_format(($line->purchase_price ?? 0) * $line->quantity, 2) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="bi bi-box-seam" style="font-size: 24px; margin-bottom: 8px;"></i><br>
                        No line items found
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($purchaseLines->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right;">Grand Total:</td>
                    <td><strong>${{ number_format($purchaseOrder->final_total ?? 0, 2) }}</strong></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
