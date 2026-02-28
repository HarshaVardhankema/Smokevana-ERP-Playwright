@extends('layouts.vendor_portal')
@section('title', 'Orders')

@section('css')
<style>
/* Filter Section - Amazon Style */
.filter-section {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 20px;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 180px;
}

.filter-group label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 6px;
}

.filter-group select,
.filter-group input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: 4px;
    font-size: 13px;
    transition: all 0.15s;
}

.filter-group select:focus,
.filter-group input:focus {
    outline: none;
    border-color: var(--amazon-orange);
    box-shadow: 0 0 0 2px rgba(255,153,0,0.15);
}

.filter-buttons {
    display: flex;
    gap: 8px;
}

/* DataTable Styling */
#vendor-orders-table {
    width: 100% !important;
}

#vendor-orders-table thead th {
    background: var(--amazon-navy) !important;
    color: #fff !important;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 14px;
    border: none !important;
}

#vendor-orders-table tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-200);
    font-size: 13px;
}

#vendor-orders-table tbody tr:hover {
    background: #fffbf3;
}

/* Order ID Link */
.order-id-link {
    color: var(--amazon-link);
    font-weight: 600;
    text-decoration: none;
}

.order-id-link:hover {
    color: var(--amazon-warning);
    text-decoration: underline;
}

/* Customer Info */
.customer-info strong {
    color: var(--gray-800);
    display: block;
}

.customer-info small {
    color: var(--gray-500);
}

/* Tracking Code */
.tracking-code {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    background: var(--gray-100);
    padding: 4px 8px;
    border-radius: 3px;
    color: var(--gray-700);
}

/* Action Buttons */
.btn-action {
    padding: 5px 10px;
    font-size: 11px;
    border-radius: 4px;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin: 0 2px;
}

.btn-action-view {
    background: #f0f0f0;
    border-color: #d0d0d0;
    color: var(--gray-700);
}

.btn-action-view:hover {
    background: #e0e0e0;
}

.btn-action-accept {
    background: #d4edda;
    border-color: var(--amazon-success);
    color: var(--amazon-success);
}

.btn-action-accept:hover {
    background: var(--amazon-success);
    color: #fff;
}

.btn-action-ship {
    background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
    border-color: #a88734;
    color: var(--gray-900);
}

.btn-action-ship:hover {
    background: linear-gradient(to bottom, #f5d78e, #eeba37);
}

.btn-action-complete {
    background: var(--amazon-success);
    border-color: var(--amazon-success);
    color: #fff;
}

.btn-action-complete:hover {
    background: #056654;
}

/* Modal Styles */
.modal {
    display: none;
}

.modal.in, .modal.show {
    display: block;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="sc-page-header">
    <h1 class="sc-page-title"><strong>Orders</strong></h1>
</div>

<!-- Filters -->
<div class="filter-section">
    <div class="filter-row">
        <div class="filter-group">
            <label for="filter_status">Status</label>
            <select id="filter_status">
                <option value="">All Statuses</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label for="filter_date_range">Date Range</label>
            <input type="text" id="filter_date_range" placeholder="Select date range" readonly>
        </div>
        <div class="filter-buttons">
            <button type="button" class="sc-btn sc-btn-primary" id="apply-filters">
                <i class="bi bi-funnel"></i> Apply
            </button>
            <button type="button" class="sc-btn sc-btn-secondary" id="clear-filters">
                <i class="bi bi-x"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="sc-card">
    <div class="sc-card-header">
        <h3 class="sc-card-title">
            <i class="bi bi-cart"></i>
            Order Management
        </h3>
    </div>
    <div style="padding: 0;">
        <table class="table" id="vendor-orders-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tracking</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Tracking Modal -->
<div class="modal fade" id="add-tracking-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-truck"></i> Ship Order</h4>
                <button type="button" class="close" id="closeTrackingModal"><span>&times;</span></button>
            </div>
            <form id="add-tracking-form">
                <div class="modal-body">
                    <input type="hidden" id="tracking_order_id">
                    <div class="sc-form-group">
                        <label class="sc-form-label">Tracking Number <span class="required">*</span></label>
                        <input type="text" class="sc-form-control" id="tracking_number" required placeholder="Enter tracking number">
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-form-label">Shipping Carrier</label>
                        <select class="sc-form-control" id="carrier">
                            <option value="">Select carrier</option>
                            <option value="usps">USPS</option>
                            <option value="ups">UPS</option>
                            <option value="fedex">FedEx</option>
                            <option value="dhl">DHL</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="sc-form-group">
                        <label class="sc-form-label">Tracking URL (optional)</label>
                        <input type="url" class="sc-form-control" id="carrier_tracking_url" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sc-btn sc-btn-secondary" id="cancelTrackingModal">Cancel</button>
                    <button type="submit" class="sc-btn sc-btn-orange">
                        <i class="bi bi-truck"></i> Confirm Shipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Order Modal -->
<div class="modal fade" id="complete-order-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <h4 class="modal-title"><i class="bi bi-clipboard-check"></i> Complete Order</h4>
                <button type="button" class="close" id="closeCompleteModal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="complete_order_id">
                <div style="text-align: center; padding: 20px 0;">
                    <div style="width: 70px; height: 70px; background: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="bi bi-check" style="font-size: 32px; color: var(--amazon-success);"></i>
                    </div>
                    <h4 style="margin-bottom: 8px;">Mark Order as Completed?</h4>
                    <p style="color: var(--gray-600); font-size: 14px; margin: 0;">
                        Order <strong id="complete_order_display_id"></strong> will be marked as delivered.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" class="sc-btn sc-btn-secondary" id="cancelCompleteModal">Cancel</button>
                <button type="button" class="sc-btn" id="confirmCompleteBtn" style="background: var(--amazon-success); color: #fff;">
                    <i class="bi bi-check"></i> Confirm Completion
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Date range picker
    $('#filter_date_range').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' }
    });
    $('#filter_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('#filter_date_range').on('cancel.daterangepicker', function() {
        $(this).val('');
    });

    // DataTable
    var ordersTable = $('#vendor-orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("vendor.orders") }}',
            data: function(d) {
                d.status = $('#filter_status').val();
                if ($('#filter_date_range').val()) {
                    var dates = $('#filter_date_range').val().split(' to ');
                    d.start_date = dates[0];
                    d.end_date = dates[1];
                }
            }
        },
        columns: [
            { data: 'date', name: 'created_at' },
            { 
                data: 'order_no', 
                name: 'transaction_id',
                render: function(data, type, row) {
                    return '<a href="{{ url("vendor-portal/orders") }}/' + row.id + '" class="order-id-link">' + data + '</a>';
                }
            },
            { 
                data: 'customer', 
                name: 'customer', 
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="customer-info"><strong>' + (data || '-') + '</strong><small>' + (row.city || '') + '</small></div>';
                }
            },
            { data: 'items', name: 'items', orderable: false },
            { data: 'total', name: 'total', orderable: false },
            { data: 'status_badge', name: 'fulfillment_status' },
            { 
                data: 'tracking', 
                name: 'tracking_number',
                render: function(data) {
                    return data ? '<code class="tracking-code">' + data + '</code>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']],
        language: {
            processing: '<i class="bi bi-arrow-repeat"></i> Loading...',
            emptyTable: 'No orders found',
            zeroRecords: 'No matching orders'
        }
    });

    // Filters
    $('#apply-filters').on('click', function() {
        ordersTable.ajax.reload();
    });

    $('#clear-filters').on('click', function() {
        $('#filter_status').val('');
        $('#filter_date_range').val('');
        ordersTable.ajax.reload();
    });

    // Modal helpers
    function showModal(id) {
        $(id).addClass('in').css('display', 'block');
        $('body').addClass('modal-open');
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade in"></div>');
        }
    }
    
    function hideModal(id) {
        $(id).removeClass('in').css('display', 'none');
        if (!$('.modal.in').length) {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }
    }

    // Accept order
    $(document).on('click', '.accept-order', function() {
        var orderId = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i>');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/accept',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    ordersTable.ajax.reload(null, false);
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

    // Add tracking modal
    $(document).on('click', '.add-tracking', function() {
        $('#tracking_order_id').val($(this).data('id'));
        $('#add-tracking-form')[0].reset();
        showModal('#add-tracking-modal');
    });

    $('#closeTrackingModal, #cancelTrackingModal').on('click', function() {
        hideModal('#add-tracking-modal');
    });

    $('#add-tracking-form').on('submit', function(e) {
        e.preventDefault();
        var orderId = $('#tracking_order_id').val();
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Processing...');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/ship',
            method: 'POST',
            data: {
                tracking_number: $('#tracking_number').val(),
                carrier: $('#carrier').val(),
                carrier_tracking_url: $('#carrier_tracking_url').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    hideModal('#add-tracking-modal');
                    ordersTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.msg);
                }
                submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Confirm Shipment');
            },
            error: function() {
                toastr.error('Failed to ship order');
                submitBtn.prop('disabled', false).html('<i class="bi bi-truck"></i> Confirm Shipment');
            }
        });
    });

    // Complete order modal
    $(document).on('click', '.complete-order', function() {
        var orderId = $(this).data('id');
        var orderRef = $(this).data('ref') || orderId;
        $('#complete_order_id').val(orderId);
        $('#complete_order_display_id').text(orderRef);
        showModal('#complete-order-modal');
    });

    $('#closeCompleteModal, #cancelCompleteModal').on('click', function() {
        hideModal('#complete-order-modal');
    });

    $('#confirmCompleteBtn').on('click', function() {
        var orderId = $('#complete_order_id').val();
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i>');
        
        $.ajax({
            url: '{{ url("vendor-portal/orders") }}/' + orderId + '/complete',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    hideModal('#complete-order-modal');
                    ordersTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.msg);
                }
                btn.prop('disabled', false).html('<i class="bi bi-check"></i> Confirm Completion');
            },
            error: function() {
                toastr.error('Failed to complete order');
                btn.prop('disabled', false).html('<i class="bi bi-check"></i> Confirm Completion');
            }
        });
    });

    // Close modals with backdrop click or Escape
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            hideModal('#' + $(this).attr('id'));
        }
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.modal.in').each(function() {
                hideModal('#' + $(this).attr('id'));
            });
        }
    });
});
</script>
@endsection
