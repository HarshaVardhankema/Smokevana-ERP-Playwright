@extends('layouts.vendor_portal')
@section('title', 'Product Requests')

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

.stat-card-icon.warning { background: #fff3cd; color: #856404; }
.stat-card-icon.success { background: #d4edda; color: var(--amazon-success); }
.stat-card-icon.danger { background: #f8d7da; color: #721c24; }
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
    margin-bottom: 20px;
}

/* Table Styling */
#requests-table {
    width: 100% !important;
    border-collapse: collapse;
}

#requests-table thead th {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

#requests-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 14px;
    vertical-align: middle;
}

#requests-table tbody tr:hover {
    background: #fffbf5;
}

#requests-table code {
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #374151;
}

/* DataTables overrides */
.dataTables_wrapper {
    padding: 16px;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 16px;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    padding: 6px 12px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #ff9900;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    border-radius: 4px;
    margin: 0 2px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #ff9900 0%, #ffad33 100%) !important;
    color: #111 !important;
    border: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f3f4f6 !important;
    color: #111 !important;
    border: 1px solid var(--gray-300) !important;
}

/* Action buttons */
.btn-group {
    display: inline-flex;
    gap: 4px;
}

.btn-group .btn {
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.btn-outline-primary {
    color: #ff9900;
    border: 1px solid #ff9900;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #ff9900;
    color: #111;
}

.btn-outline-warning {
    color: #f59e0b;
    border: 1px solid #f59e0b;
    background: transparent;
}

.btn-outline-warning:hover {
    background: #f59e0b;
    color: #111;
}

.btn-outline-danger {
    color: #dc2626;
    border: 1px solid #dc2626;
    background: transparent;
}

.btn-outline-danger:hover {
    background: #dc2626;
    color: #fff;
}

/* Modal Styling */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    border-radius: 12px 12px 0 0;
    padding: 16px 24px;
}

.modal-header .close {
    color: #fff;
    opacity: 0.8;
    text-shadow: none;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-title {
    font-weight: 700;
    font-size: 18px;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--gray-200);
}

.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid var(--gray-100);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    width: 140px;
    font-weight: 600;
    color: var(--gray-600);
    font-size: 13px;
}

.detail-value {
    flex: 1;
    color: var(--gray-900);
    font-size: 14px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 6px;
    font-size: 13px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #ff9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #ff9900 0%, #ffad33 100%);
    border: none;
    color: #111;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 8px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #e88a00 0%, #ff9900 100%);
}

.btn-secondary {
    background: var(--gray-200);
    border: none;
    color: var(--gray-700);
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 8px;
}

.btn-secondary:hover {
    background: var(--gray-300);
}

.variations-list {
    background: var(--gray-50);
    border-radius: 8px;
    padding: 12px;
    margin-top: 8px;
}

.variation-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed var(--gray-200);
    font-size: 13px;
}

.variation-item:last-child {
    border-bottom: none;
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Product Requests</h1>
        <p class="page-subtitle">Manage your product access requests</p>
    </div>
    <a href="{{ route('vendor.product-requests.create') }}" class="sc-btn sc-btn-primary">
        <i class="bi bi-plus"></i> Request Product
    </a>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon warning">
            <i class="bi bi-clock"></i>
        </div>
        <div class="stat-card-label">Pending</div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon success">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-card-label">Approved</div>
        <div class="stat-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon danger">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="stat-card-label">Rejected</div>
        <div class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>
</div>

<!-- Requests Table -->
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title"><i class="bi bi-clipboard-check"></i> All Product Requests</h3>
    </div>
    <div class="table-card-body">
        <table id="requests-table" class="table table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Status</th>
                    <th>Approved By</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>
        
        <!-- Empty State (shown when no data) -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-state-icon"><i class="bi bi-clipboard-check"></i></div>
            <h3 class="empty-state-title">No Product Requests</h3>
            <p class="empty-state-text">You haven't made any product requests yet.</p>
            <a href="{{ route('vendor.product-requests.create') }}" class="sc-btn sc-btn-primary">
                <i class="bi bi-plus"></i> Request Your First Product
            </a>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clipboard-check"></i> Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewRequestBody">
                <div class="text-center py-4">
                    <i class="bi bi-arrow-repeat"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Request Modal -->
<div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRequestForm">
                <div class="modal-body" id="editRequestBody">
                    <div class="text-center py-4">
                        <i class="bi bi-arrow-repeat"></i>
                        <p class="mt-2">Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveEditBtn">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <h5 class="modal-title"><i class="bi bi-trash"></i> Delete Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this request?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    console.log('Initializing Product Requests DataTable...');
    
    var table = $('#requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendor.product-requests') }}",
            type: 'GET',
            data: function(d) {
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                toastr.error('Failed to load product requests');
            }
        },
        columns: [
            { data: 'date', name: 'vendor_product_requests.created_at' },
            { data: 'type_badge', name: 'request_type', orderable: false, searchable: false },
            { data: 'product_display', name: 'proposed_name', orderable: false },
            { data: 'sku_display', name: 'product_sku', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'reviewed_by_display', name: 'reviewed_by', orderable: false, searchable: false },
            { 
                data: 'notes', 
                name: 'notes',
                render: function(data) {
                    if (!data) return '<span class="text-muted">-</span>';
                    if (data.length > 50) {
                        return '<span title="' + data + '">' + data.substring(0, 50) + '...</span>';
                    }
                    return data;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<i class="bi bi-arrow-repeat"></i> Loading...',
            emptyTable: "No product requests found",
            zeroRecords: "No matching requests found"
        },
        drawCallback: function(settings) {
            var api = this.api();
            var info = api.page.info();
            
            if (info.recordsTotal === 0) {
                $('#empty-state').show();
                $('#requests-table').hide();
            } else {
                $('#empty-state').hide();
                $('#requests-table').show();
            }
        }
    });
    
    var currentRequestId = null;
    var currentRequestData = null;
    
    // View request details
    $(document).on('click', '.view-request', function(e) {
        e.preventDefault();
        var requestId = $(this).data('id');
        currentRequestId = requestId;
        
        $('#viewRequestBody').html('<div class="text-center py-4"><i class="bi bi-arrow-repeat"></i><p class="mt-2">Loading...</p></div>');
        $('#viewRequestModal').modal('show');
        
        $.ajax({
            url: "{{ url('vendor-portal/product-requests') }}/" + requestId + "/view",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderViewModal(response.request);
                } else {
                    $('#viewRequestBody').html('<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i><p class="mt-2">' + (response.msg || 'Failed to load') + '</p></div>');
                }
            },
            error: function(xhr) {
                $('#viewRequestBody').html('<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i><p class="mt-2">Failed to load request details</p></div>');
            }
        });
    });
    
    // Render view modal content
    function renderViewModal(req) {
        var html = '';
        
        // Status badge
        var statusClass = {'pending': 'badge-warning', 'approved': 'badge-success', 'rejected': 'badge-danger'}[req.status] || 'badge-secondary';
        html += '<div class="text-center mb-3"><span class="badge ' + statusClass + '" style="font-size: 14px; padding: 8px 16px;">' + req.status.toUpperCase() + '</span></div>';
        
        html += '<div class="detail-row"><div class="detail-label">Request ID</div><div class="detail-value">#' + req.id + '</div></div>';
        html += '<div class="detail-row"><div class="detail-label">Type</div><div class="detail-value">' + (req.request_type === 'new' ? '<span class="badge badge-info">New Product</span>' : '<span class="badge badge-secondary">Existing Product</span>') + '</div></div>';
        html += '<div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">' + new Date(req.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) + '</div></div>';
        
        if (req.request_type === 'new') {
            html += '<div class="detail-row"><div class="detail-label">Product Name</div><div class="detail-value">' + (req.proposed_name || '-') + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">SKU</div><div class="detail-value"><code>' + (req.proposed_sku || 'Auto-generated') + '</code></div></div>';
            html += '<div class="detail-row"><div class="detail-label">Category</div><div class="detail-value">' + (req.category_name || '-') + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Brand</div><div class="detail-value">' + (req.brand_name || '-') + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Cost Price</div><div class="detail-value">$' + (parseFloat(req.proposed_cost_price || 0).toFixed(2)) + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Selling Price</div><div class="detail-value">$' + (parseFloat(req.proposed_selling_price || 0).toFixed(2)) + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">Product Type</div><div class="detail-value">' + (req.proposed_type === 'variable' ? 'Variable' : 'Single') + '</div></div>';
            
            if (req.proposed_description) {
                html += '<div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">' + req.proposed_description + '</div></div>';
            }
            
            // Show variations if any
            if (req.proposed_variations && req.proposed_variations.length > 0) {
                html += '<div class="detail-row"><div class="detail-label">Variations</div><div class="detail-value"><div class="variations-list">';
                req.proposed_variations.forEach(function(v) {
                    html += '<div class="variation-item"><span>' + v.value + '</span><span>$' + (parseFloat(v.cost_price || 0).toFixed(2)) + ' / $' + (parseFloat(v.selling_price || 0).toFixed(2)) + '</span></div>';
                });
                html += '</div></div></div>';
            }
        } else {
            html += '<div class="detail-row"><div class="detail-label">Product</div><div class="detail-value">' + (req.product_name || '-') + '</div></div>';
            html += '<div class="detail-row"><div class="detail-label">SKU</div><div class="detail-value"><code>' + (req.product_sku || '-') + '</code></div></div>';
        }
        
        html += '<div class="detail-row"><div class="detail-label">Notes</div><div class="detail-value">' + (req.notes || '<span class="text-muted">No notes</span>') + '</div></div>';
        
        if (req.rejection_reason) {
            html += '<div class="detail-row"><div class="detail-label">Rejection Reason</div><div class="detail-value text-danger">' + req.rejection_reason + '</div></div>';
        }
        
        $('#viewRequestBody').html(html);
    }
    
    // Edit request
    $(document).on('click', '.edit-request', function(e) {
        e.preventDefault();
        var requestId = $(this).data('id');
        currentRequestId = requestId;
        
        $('#editRequestBody').html('<div class="text-center py-4"><i class="bi bi-arrow-repeat"></i><p class="mt-2">Loading...</p></div>');
        $('#editRequestModal').modal('show');
        
        $.ajax({
            url: "{{ url('vendor-portal/product-requests') }}/" + requestId + "/view",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    currentRequestData = response.request;
                    renderEditModal(response.request);
                } else {
                    $('#editRequestBody').html('<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i><p class="mt-2">' + (response.msg || 'Failed to load') + '</p></div>');
                }
            },
            error: function(xhr) {
                $('#editRequestBody').html('<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i><p class="mt-2">Failed to load request details</p></div>');
            }
        });
    });
    
    // Render edit modal content
    function renderEditModal(req) {
        var html = '<input type="hidden" id="edit_request_id" value="' + req.id + '">';
        html += '<input type="hidden" id="edit_request_type" value="' + req.request_type + '">';
        
        if (req.request_type === 'new') {
            // Product Name
            html += '<div class="form-group">';
            html += '<label>Product Name <span style="color:#dc2626;">*</span></label>';
            html += '<input type="text" class="form-control" id="edit_proposed_name" value="' + escapeHtml(req.proposed_name || '') + '" required>';
            html += '</div>';
            
            // SKU and Barcode Row
            html += '<div class="row">';
            html += '<div class="col-md-6"><div class="form-group"><label>SKU</label><input type="text" class="form-control" id="edit_proposed_sku" value="' + escapeHtml(req.proposed_sku || '') + '" placeholder="Auto-generated if empty"></div></div>';
            html += '<div class="col-md-6"><div class="form-group"><label>Barcode</label><input type="text" class="form-control" id="edit_proposed_barcode" value="' + escapeHtml(req.proposed_barcode || '') + '" placeholder="Optional barcode/UPC"></div></div>';
            html += '</div>';
            
            // Category and Brand Row
            html += '<div class="row">';
            html += '<div class="col-md-6"><div class="form-group"><label>Category</label><select class="form-control" id="edit_proposed_category_id">';
            html += '<option value="">-- Select Category --</option>';
            @foreach($categories ?? [] as $category)
            html += '<option value="{{ $category->id }}" ' + (req.proposed_category_id == {{ $category->id }} ? 'selected' : '') + '>{{ $category->name }}</option>';
            @endforeach
            html += '</select></div></div>';
            html += '<div class="col-md-6"><div class="form-group"><label>Brand</label><select class="form-control" id="edit_proposed_brand_id">';
            html += '<option value="">-- Select Brand --</option>';
            @foreach($brands ?? [] as $brand)
            html += '<option value="{{ $brand->id }}" ' + (req.proposed_brand_id == {{ $brand->id }} ? 'selected' : '') + '>{{ $brand->name }}</option>';
            @endforeach
            html += '</select></div></div>';
            html += '</div>';
            
            // Product Type
            html += '<div class="form-group">';
            html += '<label>Product Type</label>';
            html += '<select class="form-control" id="edit_proposed_type">';
            html += '<option value="single" ' + (req.proposed_type !== 'variable' ? 'selected' : '') + '>Single Product</option>';
            html += '<option value="variable" ' + (req.proposed_type === 'variable' ? 'selected' : '') + '>Variable Product</option>';
            html += '</select>';
            html += '</div>';
            
            // Cost and Selling Price Row
            html += '<div class="row">';
            html += '<div class="col-md-6"><div class="form-group"><label>Cost Price ($)</label><input type="number" class="form-control" id="edit_proposed_cost_price" value="' + (req.proposed_cost_price || '') + '" step="0.01" min="0" placeholder="0.00"></div></div>';
            html += '<div class="col-md-6"><div class="form-group"><label>Selling Price ($)</label><input type="number" class="form-control" id="edit_proposed_selling_price" value="' + (req.proposed_selling_price || '') + '" step="0.01" min="0" placeholder="0.00"></div></div>';
            html += '</div>';
            
            // Description
            html += '<div class="form-group">';
            html += '<label>Description</label>';
            html += '<textarea class="form-control" id="edit_proposed_description" rows="3" placeholder="Product description...">' + escapeHtml(req.proposed_description || '') + '</textarea>';
            html += '</div>';
            
            // Show variations if product is variable
            if (req.proposed_type === 'variable' && req.proposed_variations && req.proposed_variations.length > 0) {
                html += '<div class="form-group">';
                html += '<label>Variations <span style="color:#6b7280; font-weight:normal;">(Edit prices below)</span></label>';
                html += '<div class="variations-edit-container" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px;">';
                html += '<table class="table table-sm" style="margin:0;">';
                html += '<thead><tr><th>Value</th><th>SKU</th><th>Cost ($)</th><th>Sell ($)</th></tr></thead>';
                html += '<tbody>';
                req.proposed_variations.forEach(function(v, idx) {
                    html += '<tr>';
                    html += '<td><input type="text" class="form-control form-control-sm" name="edit_variations[' + idx + '][value]" value="' + escapeHtml(v.value || '') + '"></td>';
                    html += '<td><input type="text" class="form-control form-control-sm" name="edit_variations[' + idx + '][sku]" value="' + escapeHtml(v.sku || '') + '" placeholder="Auto"></td>';
                    html += '<td><input type="number" class="form-control form-control-sm" name="edit_variations[' + idx + '][cost_price]" value="' + (v.cost_price || '') + '" step="0.01" min="0"></td>';
                    html += '<td><input type="number" class="form-control form-control-sm" name="edit_variations[' + idx + '][selling_price]" value="' + (v.selling_price || '') + '" step="0.01" min="0"></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                html += '</div></div>';
            }
        } else {
            // Existing product request - show product info (read-only)
            html += '<div class="alert alert-info" style="border-radius:8px;"><i class="bi bi-info-circle"></i> This is a request for an existing product in the catalog.</div>';
            html += '<div class="detail-row"><div class="detail-label">Product</div><div class="detail-value"><strong>' + escapeHtml(req.product_name || '-') + '</strong></div></div>';
            html += '<div class="detail-row"><div class="detail-label">SKU</div><div class="detail-value"><code>' + escapeHtml(req.product_sku || '-') + '</code></div></div>';
        }
        
        // Notes (editable for both types)
        html += '<div class="form-group" style="margin-top:16px;">';
        html += '<label>Notes / Comments</label>';
        html += '<textarea class="form-control" id="edit_notes" rows="3" placeholder="Add any notes or comments...">' + escapeHtml(req.notes || '') + '</textarea>';
        html += '</div>';
        
        $('#editRequestBody').html(html);
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Save edit
    $('#editRequestForm').on('submit', function(e) {
        e.preventDefault();
        
        var requestId = $('#edit_request_id').val();
        var requestType = $('#edit_request_type').val();
        var data = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            notes: $('#edit_notes').val()
        };
        
        // Add new product fields if applicable
        if (requestType === 'new') {
            data.proposed_name = $('#edit_proposed_name').val();
            data.proposed_sku = $('#edit_proposed_sku').val();
            data.proposed_barcode = $('#edit_proposed_barcode').val();
            data.proposed_category_id = $('#edit_proposed_category_id').val();
            data.proposed_brand_id = $('#edit_proposed_brand_id').val();
            data.proposed_type = $('#edit_proposed_type').val();
            data.proposed_cost_price = $('#edit_proposed_cost_price').val();
            data.proposed_selling_price = $('#edit_proposed_selling_price').val();
            data.proposed_description = $('#edit_proposed_description').val();
            
            // Collect variations if they exist
            var variations = [];
            $('input[name^="edit_variations"]').each(function() {
                var name = $(this).attr('name');
                var match = name.match(/edit_variations\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    var idx = parseInt(match[1]);
                    var field = match[2];
                    if (!variations[idx]) variations[idx] = {};
                    variations[idx][field] = $(this).val();
                }
            });
            
            // Filter out empty variations
            variations = variations.filter(function(v) {
                return v && v.value && v.value.trim() !== '';
            });
            
            if (variations.length > 0) {
                data.proposed_variations = variations;
            }
        }
        
        $('#saveEditBtn').prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Saving...');
        
        $.ajax({
            url: "{{ url('vendor-portal/product-requests') }}/" + requestId,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#editRequestModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.msg || 'Failed to update');
                }
            },
            error: function(xhr) {
                var msg = 'Failed to update request';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                toastr.error(msg);
            },
            complete: function() {
                $('#saveEditBtn').prop('disabled', false).html('<i class="bi bi-save"></i> Save Changes');
            }
        });
    });
    
    // Delete request
    $(document).on('click', '.delete-request', function(e) {
        e.preventDefault();
        currentRequestId = $(this).data('id');
        $('#deleteRequestModal').modal('show');
    });
    
    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        $(this).prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Deleting...');
        
        $.ajax({
            url: "{{ url('vendor-portal/product-requests') }}/" + currentRequestId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#deleteRequestModal').modal('hide');
                    table.ajax.reload();
                    // Update stats
                    location.reload();
                } else {
                    toastr.error(response.msg || 'Failed to delete');
                }
            },
            error: function(xhr) {
                toastr.error('Failed to delete request');
            },
            complete: function() {
                $('#confirmDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
            }
        });
    });
});
</script>
@endsection
