@extends('layouts.vendor_portal')
@section('title', 'Purchase Orders')

@section('css')
<style>
/* Page Header */
.page-header {
    margin-bottom: 24px;
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

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 20px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    border-color: var(--amazon-orange);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stat-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 12px;
}

.stat-card-icon.secondary { background: #e2e6ea; color: #383d41; }
.stat-card-icon.info { background: #d1ecf1; color: var(--amazon-teal); }
.stat-card-icon.warning { background: #fff3cd; color: #856404; }
.stat-card-icon.success { background: #d4edda; color: var(--amazon-success); }

.stat-card-label {
    font-size: 12px;
    color: var(--gray-600);
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
}

.stat-card-value {
    font-size: 28px;
    font-weight: 700;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
}

.table-card-body {
    padding: 0;
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.empty-state-text {
    font-size: 14px;
}


.page-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.page-header-left {
    flex: 1;
}
</style>
@endsection

@section('content')
<div class="page-header-wrapper">
    <div class="page-header-left">
        <h1 class="page-title">Purchase Orders</h1>
        <p class="page-subtitle">View and create purchase orders from your inventory</p>
        @if(!$vendor->contact_id && !$vendor->user_id)
        <div class="alert alert-warning" style="margin-top: 10px; padding: 10px 15px; border-radius: 6px; font-size: 13px;">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Setup Required:</strong> Your vendor account is not linked to a supplier contact or user account. 
            Please contact admin to link your account to see purchase orders.
        </div>
        @endif
    </div>
    <a href="{{ route('vendor.purchase-orders.create') }}" class="sc-btn sc-btn-primary">
        <i class="bi bi-plus"></i> Create Purchase Order
    </a>
</div>

<!-- Debug Info (for admin troubleshooting) -->
<div class="debug-info" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 12px;">
    <strong><i class="bi bi-info-circle"></i> Vendor Account Links:</strong>
    <span style="margin-left: 15px;">
        <strong>Linked Supplier:</strong> 
        @if($linkedContact ?? null)
            {{ $linkedContact->name }} (ID: {{ $linkedContact->id }})
        @else
            <span style="color: #dc3545;">Not linked</span>
        @endif
    </span>
    <span style="margin-left: 15px;">
        <strong>Linked User:</strong> 
        @if($linkedUser ?? null)
            {{ $linkedUser->username }} (ID: {{ $linkedUser->id }})
        @else
            <span style="color: #dc3545;">Not linked</span>
        @endif
    </span>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon secondary">
            <i class="bi bi-file-text"></i>
        </div>
        <div class="stat-card-label">Draft</div>
        <div class="stat-card-value" id="draft-count">{{ $stats['draft'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon info">
            <i class="bi bi-send"></i>
        </div>
        <div class="stat-card-label">Ordered</div>
        <div class="stat-card-value" id="ordered-count">{{ $stats['ordered'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon warning">
            <i class="bi bi-truck"></i>
        </div>
        <div class="stat-card-label">Partial</div>
        <div class="stat-card-value" id="partial-count">{{ $stats['partial'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon success">
            <i class="bi bi-check-all"></i>
        </div>
        <div class="stat-card-label">Received</div>
        <div class="stat-card-value" id="received-count">{{ $stats['received'] ?? 0 }}</div>
    </div>
</div>

<!-- Orders Table -->
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title"><i class="bi bi-file-text"></i> All Purchase Orders</h3>
    </div>
    <div class="table-card-body">
        <table id="po-table" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>PO Number</th>
                    <th>Invoice #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>
        
        <!-- Empty State (shown when no data) -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-state-icon"><i class="bi bi-file-text"></i></div>
            <h3 class="empty-state-title">No Purchase Orders</h3>
            <p class="empty-state-text">You don't have any purchase orders yet.</p>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    console.log('Initializing Purchase Orders DataTable...');
    
    var table = $('#po-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendor.purchase-orders') }}",
            type: 'GET',
            data: function(d) {
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown, xhr.responseText);
                toastr.error('Failed to load purchase orders');
                $('#empty-state').show();
            }
        },
        columns: [
            { data: 'date', name: 't.transaction_date' },
            { data: 'ref_no', name: 't.ref_no' },
            { data: 'invoice_no', name: 't.invoice_no' },
            { data: 'total', name: 't.final_total' },
            { data: 'status_badge', name: 't.status', orderable: false, searchable: false },
            { data: 'payment_badge', name: 't.payment_status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<i class="bi bi-arrow-repeat"></i> Loading...',
            emptyTable: "No purchase orders found",
            zeroRecords: "No matching orders found"
        },
        drawCallback: function(settings) {
            var api = this.api();
            var info = api.page.info();
            console.log('DataTable loaded:', info.recordsTotal, 'records');
            
            if (info.recordsTotal === 0) {
                $('#empty-state').show();
                $('#po-table').hide();
            } else {
                $('#empty-state').hide();
                $('#po-table').show();
            }
        }
    });
});
</script>
@endsection
