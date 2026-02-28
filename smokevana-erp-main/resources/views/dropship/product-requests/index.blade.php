@extends('layouts.app')
@section('title', 'Vendor Product Requests')

@section('content')
<style>
    .stats-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .stats-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
    }
    .stats-card .stat-icon.pending { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stats-card .stat-icon.approved { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stats-card .stat-icon.rejected { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .stats-card .stat-value { font-size: 28px; font-weight: 700; color: #1f2937; }
    .stats-card .stat-label { font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }

    .filter-section {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    
    /* Amazon-style filter buttons */
    .amazon-filter-btn {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 32px;
        position: relative;
        z-index: 1;
    }
    
    /* Clear button - Amazon orange style, smaller X icon */
    .amazon-filter-btn-secondary {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #FFFFFF !important;
        border: 1px solid #C7511F !important;
    }
    
    .amazon-filter-btn-secondary:hover {
        background: linear-gradient(to bottom, #FFAC33 0%, #FF9900 100%) !important;
        border-color: #FF9900 !important;
        color: #FFFFFF !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .amazon-filter-btn-secondary:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .amazon-filter-btn-secondary:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.35);
    }
    
    /* Smaller X icon inside Clear Filters button */
    #clear_filters i {
        font-size: 12px;
        margin-right: 4px;
    }

    #requests-table th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }

    .vendor-badge {
        background: #f1f5f9;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    /* View Modal Styles */
    .detail-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .detail-section h6 {
        margin: 0 0 12px 0;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        color: #6b7280;
        font-size: 13px;
    }
    .detail-value {
        color: #1f2937;
        font-weight: 500;
        font-size: 13px;
        text-align: right;
    }
    .variation-table {
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }
    .variation-table th, .variation-table td {
        padding: 8px;
        border: 1px solid #e5e7eb;
    }
    .variation-table th {
        background: #f1f5f9;
        font-weight: 600;
        color: #475569;
    }

    /* Action button hover effects */
    .action-btns button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .action-btns button:active {
        transform: translateY(0);
    }
    
    /* Table styling improvements */
    #requests-table td {
        vertical-align: middle;
    }
    #requests-table .action-btns {
        justify-content: flex-start;
    }
</style>

<!-- Amazon-style banner -->
<section class="content-header">
    <div style="background:#37475a;border-radius:6px;padding:22px 28px;margin-bottom:16px;box-shadow:0 3px 10px rgba(15,17,17,0.4);">
        <h1 style="display:flex;align-items:center;gap:10px;font-size:22px;font-weight:700;margin:0;color:#fff;"><i class="fas fa-clipboard-list" style="color:#fff!important;"></i> Vendor Product Requests</h1>
        <p style="font-size:13px;color:rgba(249,250,251,0.88);margin:4px 0 0 0;">Review and manage vendor product requests (pending, approved, rejected).</p>
    </div>
</section>

<section class="content">
    <!-- Stats Cards -->
    <div class="row tw-mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div>
                        <div class="stat-value" id="stat-pending">{{ $stats['pending'] }}</div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div>
                        <div class="stat-value" id="stat-approved">{{ $stats['approved'] }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-icon approved">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div>
                        <div class="stat-value" id="stat-rejected">{{ $stats['rejected'] }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                    <div class="stat-icon rejected">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
        <!-- Filters -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter by Status</label>
                        <select id="filter_status" class="form-control select2">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter by Vendor</label>
                        <select id="filter_vendor" class="form-control select2">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->company_name ?: $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Filter by Request Type</label>
                        <select id="filter_type" class="form-control select2">
                            <option value="">All Types</option>
                            <option value="new">New Product</option>
                            <option value="existing">Existing Product</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 tw-flex tw-items-end">
                    <button id="clear_filters" class="amazon-filter-btn amazon-filter-btn-secondary" style="margin-bottom: 0;">
                        <i class="fas fa-times" style="margin-right: 4px;"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="requests-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vendor</th>
                        <th>Type</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Pricing</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fas fa-clipboard-list"></i> Product Request Details</h4>
            </div>
            <div class="modal-body" id="viewRequestBody">
                <div class="tw-text-center tw-py-8">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="tw-mt-2">Loading...</p>
                </div>
            </div>
            <div class="modal-footer" id="viewRequestFooter" style="display:none;">
                <button type="button" class="tw-dw-btn tw-dw-btn-ghost" data-dismiss="modal">Close</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-error" id="btn-reject" style="display:none;">
                    <i class="fas fa-times"></i> Reject
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-success" id="btn-approve" style="display:none;">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="tw-dw-btn tw-dw-btn-success" id="btn-create-approve" style="display:none;">
                    <i class="fas fa-plus-circle"></i> Create & Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Confirmation Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="actionModalTitle">Confirm Action</h4>
            </div>
            <div class="modal-body">
                <p id="actionModalText"></p>
                <div class="form-group">
                    <label for="admin_notes">Admin Notes (Optional)</label>
                    <textarea class="form-control" id="admin_notes" rows="3" placeholder="Add any notes for the vendor..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-ghost" data-dismiss="modal">Cancel</button>
                <button type="button" class="tw-dw-btn" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2();

    var currentRequestId = null;
    var currentRequestStatus = null;
    var currentRequestType = null;

    // Initialize DataTable
    var table = $('#requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("dropship.product-requests.data") }}',
            data: function(d) {
                d.status = $('#filter_status').val();
                d.vendor_id = $('#filter_vendor').val();
                d.request_type = $('#filter_type').val();
            }
        },
        columns: [
            { data: 'id', name: 'vendor_product_requests.id' },
            { data: 'vendor_display', name: 'wp_vendors.name' },
            { data: 'type_badge', name: 'vendor_product_requests.request_type' },
            { data: 'product_display', name: 'vendor_product_requests.proposed_name' },
            { data: 'sku_display', name: 'vendor_product_requests.proposed_sku' },
            { data: 'price_display', name: 'vendor_product_requests.proposed_cost_price', orderable: false },
            { data: 'status_badge', name: 'vendor_product_requests.status' },
            { data: 'reviewed_by_display', name: 'reviewer.first_name', orderable: false },
            { data: 'date_display', name: 'vendor_product_requests.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    // Filter handlers
    $('#filter_status, #filter_vendor, #filter_type').on('change', function() {
        table.ajax.reload();
    });

    $('#clear_filters').on('click', function() {
        $('#filter_status, #filter_vendor, #filter_type').val('').trigger('change');
    });

    // View request details
    $(document).on('click', '.view-request', function() {
        var requestId = $(this).data('id');
        currentRequestId = requestId;
        
        $('#viewRequestBody').html('<div class="tw-text-center tw-py-8"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="tw-mt-2">Loading...</p></div>');
        $('#viewRequestFooter').hide();
        $('#viewRequestModal').modal('show');

        $.ajax({
            url: '{{ url("dropship/product-requests") }}/' + requestId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    renderViewModal(response.request);
                    currentRequestStatus = response.request.status;
                    
                    $('#viewRequestFooter').show();
                    currentRequestType = response.request.request_type;
                    
                    if (response.request.status === 'pending') {
                        $('#btn-reject').show();
                        if (response.request.request_type === 'new') {
                            $('#btn-approve').hide();
                            $('#btn-create-approve').show();
                        } else {
                            $('#btn-approve').show();
                            $('#btn-create-approve').hide();
                        }
                    } else {
                        $('#btn-approve, #btn-reject, #btn-create-approve').hide();
                    }
                } else {
                    $('#viewRequestBody').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.msg + '</div>');
                }
            },
            error: function() {
                $('#viewRequestBody').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Failed to load request details.</div>');
            }
        });
    });

    // Render view modal content
    function renderViewModal(req) {
        var html = '';
        
        // Vendor Info Section
        html += '<div class="detail-section">';
        html += '<h6><i class="fas fa-user"></i> Vendor Information</h6>';
        html += '<div class="detail-row"><span class="detail-label">Vendor</span><span class="detail-value">' + escapeHtml(req.vendor_company || req.vendor_name) + '</span></div>';
        html += '<div class="detail-row"><span class="detail-label">Email</span><span class="detail-value">' + escapeHtml(req.vendor_email || 'N/A') + '</span></div>';
        html += '</div>';

        // Request Info Section
        html += '<div class="detail-section">';
        html += '<h6><i class="fas fa-info-circle"></i> Request Information</h6>';
        html += '<div class="detail-row"><span class="detail-label">Request ID</span><span class="detail-value">#' + req.id + '</span></div>';
        html += '<div class="detail-row"><span class="detail-label">Type</span><span class="detail-value">' + (req.request_type === 'new' ? '<span class="badge" style="background:#3b82f6;color:#fff;">New Product</span>' : '<span class="badge" style="background:#6b7280;color:#fff;">Existing</span>') + '</span></div>';
        html += '<div class="detail-row"><span class="detail-label">Status</span><span class="detail-value">' + getStatusBadge(req.status) + '</span></div>';
        html += '<div class="detail-row"><span class="detail-label">Submitted</span><span class="detail-value">' + formatDate(req.created_at) + '</span></div>';
        html += '</div>';

        // Product Info Section
        html += '<div class="detail-section">';
        html += '<h6><i class="fas fa-box"></i> Product Information</h6>';
        
        if (req.request_type === 'new') {
            html += '<div class="detail-row"><span class="detail-label">Product Name</span><span class="detail-value"><strong>' + escapeHtml(req.proposed_name || 'N/A') + '</strong></span></div>';
            html += '<div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value"><code>' + escapeHtml(req.proposed_sku || 'Auto-generate') + '</code></span></div>';
            html += '<div class="detail-row"><span class="detail-label">Barcode</span><span class="detail-value">' + escapeHtml(req.proposed_barcode || 'N/A') + '</span></div>';
            html += '<div class="detail-row"><span class="detail-label">Category</span><span class="detail-value">' + escapeHtml(req.category_name || 'N/A') + '</span></div>';
            html += '<div class="detail-row"><span class="detail-label">Brand</span><span class="detail-value">' + escapeHtml(req.brand_name || 'N/A') + '</span></div>';
            html += '<div class="detail-row"><span class="detail-label">Product Type</span><span class="detail-value">' + (req.proposed_type === 'variable' ? 'Variable' : 'Single') + '</span></div>';
        } else {
            html += '<div class="detail-row"><span class="detail-label">Product</span><span class="detail-value"><strong>' + escapeHtml(req.existing_product_name || 'N/A') + '</strong></span></div>';
            html += '<div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value"><code>' + escapeHtml(req.existing_product_sku || 'N/A') + '</code></span></div>';
        }
        html += '</div>';

        // Pricing Section
        html += '<div class="detail-section">';
        html += '<h6><i class="fas fa-dollar-sign"></i> Pricing</h6>';
        html += '<div class="detail-row"><span class="detail-label">Cost Price</span><span class="detail-value" style="color:#059669;font-weight:600;">' + (req.proposed_cost_price ? '$' + parseFloat(req.proposed_cost_price).toFixed(2) : 'N/A') + '</span></div>';
        html += '<div class="detail-row"><span class="detail-label">Selling Price</span><span class="detail-value" style="color:#2563eb;font-weight:600;">' + (req.proposed_selling_price ? '$' + parseFloat(req.proposed_selling_price).toFixed(2) : 'N/A') + '</span></div>';
        html += '</div>';

        // Variations Section (if variable product)
        if (req.proposed_type === 'variable' && req.proposed_variations && req.proposed_variations.length > 0) {
            html += '<div class="detail-section">';
            html += '<h6><i class="fas fa-layer-group"></i> Variations</h6>';
            html += '<table class="variation-table">';
            html += '<thead><tr><th>Value</th><th>SKU</th><th>Cost</th><th>Sell Price</th></tr></thead>';
            html += '<tbody>';
            req.proposed_variations.forEach(function(v) {
                html += '<tr>';
                html += '<td>' + escapeHtml(v.value || '-') + '</td>';
                html += '<td><code>' + escapeHtml(v.sku || 'Auto') + '</code></td>';
                html += '<td>' + (v.cost_price ? '$' + parseFloat(v.cost_price).toFixed(2) : '-') + '</td>';
                html += '<td>' + (v.selling_price ? '$' + parseFloat(v.selling_price).toFixed(2) : '-') + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            html += '</div>';
        }

        // Description Section
        if (req.proposed_description) {
            html += '<div class="detail-section">';
            html += '<h6><i class="fas fa-align-left"></i> Description</h6>';
            html += '<p style="color:#374151;">' + escapeHtml(req.proposed_description) + '</p>';
            html += '</div>';
        }

        // Notes Section
        if (req.notes) {
            html += '<div class="detail-section">';
            html += '<h6><i class="fas fa-sticky-note"></i> Vendor Notes</h6>';
            html += '<p style="color:#374151;">' + escapeHtml(req.notes) + '</p>';
            html += '</div>';
        }

        // Admin Notes Section (if exists)
        if (req.admin_notes) {
            html += '<div class="detail-section" style="background:#fef3c7;">';
            html += '<h6><i class="fas fa-comment-alt"></i> Admin Notes</h6>';
            html += '<p style="color:#92400e;">' + escapeHtml(req.admin_notes) + '</p>';
            html += '</div>';
        }

        $('#viewRequestBody').html(html);
    }

    // Approve button click (for existing products)
    $('#btn-approve').on('click', function() {
        $('#actionModalTitle').html('<i class="fas fa-check text-success"></i> Approve Request');
        $('#actionModalText').text('Are you sure you want to approve this product request?');
        $('#confirmActionBtn').removeClass('tw-dw-btn-error').addClass('tw-dw-btn-success').text('Approve');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'approve');
    });

    // Create & Approve button click (for new products - from modal)
    $('#btn-create-approve').on('click', function() {
        $('#actionModalTitle').html('<i class="fas fa-plus-circle text-success"></i> Create Product & Approve');
        $('#actionModalText').html('<strong>This will create the product in your product list and approve the request.</strong><br><br>The product will be available for sale immediately after creation.');
        $('#confirmActionBtn').removeClass('tw-dw-btn-error').addClass('tw-dw-btn-success').html('<i class="fas fa-plus-circle"></i> Create & Approve');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'create-approve');
    });

    // Reject button click
    $('#btn-reject').on('click', function() {
        $('#actionModalTitle').html('<i class="fas fa-times text-danger"></i> Reject Request');
        $('#actionModalText').text('Are you sure you want to reject this product request?');
        $('#confirmActionBtn').removeClass('tw-dw-btn-success').addClass('tw-dw-btn-error').text('Reject');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'reject');
    });

    // Quick approve from table (for existing products)
    $(document).on('click', '.approve-request', function() {
        currentRequestId = $(this).data('id');
        $('#actionModalTitle').html('<i class="fas fa-check text-success"></i> Approve Request');
        $('#actionModalText').text('Are you sure you want to approve this product request?');
        $('#confirmActionBtn').removeClass('tw-dw-btn-error').addClass('tw-dw-btn-success').text('Approve');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'approve');
    });

    // Create & Approve from table (for new products)
    $(document).on('click', '.create-approve-request', function() {
        currentRequestId = $(this).data('id');
        $('#actionModalTitle').html('<i class="fas fa-plus-circle text-success"></i> Create Product & Approve');
        $('#actionModalText').html('<strong>This will create the product in your product list and approve the request.</strong><br><br>The product will be available for sale immediately after creation.');
        $('#confirmActionBtn').removeClass('tw-dw-btn-error').addClass('tw-dw-btn-success').html('<i class="fas fa-plus-circle"></i> Create & Approve');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'create-approve');
    });

    // Quick reject from table
    $(document).on('click', '.reject-request', function() {
        currentRequestId = $(this).data('id');
        $('#actionModalTitle').html('<i class="fas fa-times text-danger"></i> Reject Request');
        $('#actionModalText').text('Are you sure you want to reject this product request?');
        $('#confirmActionBtn').removeClass('tw-dw-btn-success').addClass('tw-dw-btn-error').text('Reject');
        $('#admin_notes').val('');
        $('#actionModal').modal('show');
        $('#confirmActionBtn').data('action', 'reject');
    });

    // Confirm action
    $('#confirmActionBtn').on('click', function() {
        var action = $(this).data('action');
        var url = '{{ url("dropship/product-requests") }}/' + currentRequestId + '/' + action;
        var btn = $(this);
        var originalBtnText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: url,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                admin_notes: $('#admin_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    // Show success message with product info if created
                    if (action === 'create-approve' && response.product_name) {
                        toastr.success('Product "' + response.product_name + '" created successfully!');
                    } else {
                        toastr.success(response.msg);
                    }
                    $('#actionModal').modal('hide');
                    $('#viewRequestModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.msg || 'Action failed');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to process request';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Helper functions
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getStatusBadge(status) {
        var badges = {
            'pending': '<span class="badge" style="background:#f59e0b;color:#000;">Pending</span>',
            'approved': '<span class="badge" style="background:#10b981;color:#fff;">Approved</span>',
            'rejected': '<span class="badge" style="background:#ef4444;color:#fff;">Rejected</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }

    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        var date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
});
</script>
@endsection
