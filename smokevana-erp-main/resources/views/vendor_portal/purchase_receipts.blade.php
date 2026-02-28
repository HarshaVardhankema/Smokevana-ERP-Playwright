@extends('layouts.vendor_portal')
@section('title', 'Purchase Receipts')

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

.page-header-left {
    flex: 1;
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

.stat-card-icon.success { background: #d4edda; color: var(--amazon-success); }
.stat-card-icon.warning { background: #fff3cd; color: #856404; }
.stat-card-icon.info { background: #d1ecf1; color: var(--amazon-teal); }

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
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title"><i class="bi bi-receipt"></i> Purchase Receipts</h1>
        <p class="page-subtitle">View received inventory and record new stock receipts</p>
    </div>
    <a href="{{ route('vendor.purchase-receipts.create') }}" class="sc-btn sc-btn-primary">
        <i class="bi bi-plus"></i> Receive Inventory
    </a>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon success">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="stat-card-label">Total Receipts</div>
        <div class="stat-card-value">{{ $stats['total'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon info">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-card-label">Received</div>
        <div class="stat-card-value">{{ $stats['received'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon warning">
            <i class="bi bi-clock"></i>
        </div>
        <div class="stat-card-label">Pending</div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
</div>

<!-- Receipts Table -->
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title"><i class="bi bi-receipt"></i> All Purchase Receipts</h3>
    </div>
    <div class="table-card-body">
        <table id="receipts-table" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Invoice #</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>
        
        <!-- Empty State (shown when no data) -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-state-icon"><i class="bi bi-receipt"></i></div>
            <h3 class="empty-state-title">No Purchase Receipts</h3>
            <p class="empty-state-text">You don't have any purchase receipts yet.</p>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var table = $('#receipts-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendor.purchase-receipts') }}",
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.log('DataTables error:', error);
                $('#empty-state').show();
            }
        },
        columns: [
            { data: 'date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'type_badge', name: 'type', orderable: false, searchable: false },
            { data: 'total', name: 'final_total' },
            { data: 'payment_badge', name: 'payment_status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            emptyTable: "No purchase receipts found",
            zeroRecords: "No matching receipts found"
        },
        drawCallback: function(settings) {
            var api = this.api();
            if (api.rows().count() === 0) {
                $('#empty-state').show();
            } else {
                $('#empty-state').hide();
            }
        }
    });
});
</script>
@endsection
