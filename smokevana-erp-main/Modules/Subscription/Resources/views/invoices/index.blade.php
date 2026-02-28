@extends('layouts.app')

@section('title', __('subscription::lang.subscription_invoices'))

@section('content')
<style>
    .invoices-page {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(79, 172, 254, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(0,0,0,0.25);
        border-radius: 12px;
        padding: 10px;
        display: flex;
    }
    
    .btn-export {
        background: linear-gradient(90deg, #FFD814 0%, #FCD200 100%);
        color: #131921;
        border: 1px solid #FCD200;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(213, 217, 217, 0.5);
    }
    
    .btn-export:hover {
        background: linear-gradient(90deg, #F7CA00 0%, #F2C200 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(213, 217, 217, 0.5);
        color: #131921;
    }
    
    /* Stats Row */
    .invoice-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .invoice-stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .invoice-stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .invoice-stat-card .stat-icon.total {
        background: linear-gradient(135deg, #00a8e1, #0077a3);
        color: #fff;
    }
    
    .invoice-stat-card .stat-icon.paid {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: #fff;
    }
    
    .invoice-stat-card .stat-icon.pending {
        background: linear-gradient(135deg, #ffd700, #ffb700);
        color: #333;
    }
    
    .invoice-stat-card .stat-icon.overdue {
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: #fff;
    }
    
    .invoice-stat-card .stat-content h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }
    
    .invoice-stat-card .stat-content span {
        font-size: 12px;
        color: #6c757d;
    }
    
    /* Invoices Card */
    .invoices-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .invoices-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
    }
    
    .invoices-card .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .invoices-card .card-header h3 i {
        color: #00a8e1;
    }
    
    /* Filters */
    .filters-bar {
        display: flex;
        gap: 12px;
        padding: 16px 24px;
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
        flex-wrap: wrap;
    }
    
    .filters-bar .form-control,
    .filters-bar .form-select {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 10px 14px;
        font-size: 13px;
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    /* Status dropdown - Amazon-style light appearance */
    .invoice-status-dropdown.select2-dropdown,
    .invoice-status-dropdown {
        background-color: #fff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
    }
    .invoice-status-dropdown .select2-results__option {
        background-color: #fff !important;
        color: #131921 !important;
        padding: 10px 16px;
    }
    .invoice-status-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #FFF3E0 !important;
        color: #131921 !important;
    }
    .invoice-status-dropdown .select2-results__option[aria-selected=true] {
        background-color: #FFF8F0 !important;
        color: #e88b00 !important;
    }
    .invoices-page .filters-bar .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }
    .invoices-page .filters-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #131921 !important;
    }
    
    /* Table */
    .invoices-table {
        margin: 0;
    }
    
    .invoices-table thead th {
        background: #f8f9fa;
        border: none;
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
    }
    
    .invoices-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .invoices-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .invoice-number {
        font-weight: 600;
        color: #4facfe;
    }
    
    .invoice-customer {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .invoice-customer .avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }
    
    .invoice-customer .customer-details h6 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }
    
    .invoice-customer .customer-details span {
        font-size: 11px;
        color: #999;
    }
    
    .invoice-amount {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .badge-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-paid { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-overdue { background: #f8d7da; color: #721c24; }
    .badge-cancelled { background: #e2e3e5; color: #383d41; }
    .badge-refunded { background: #cce5ff; color: #004085; }
    
    .action-btns .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
    }
    
    .btn-view { background: #e3f2fd; color: #1976d2; border: none; }
    .btn-view:hover { background: #1976d2; color: #fff; }
    
    .btn-download { background: #e8f5e9; color: #388e3c; border: none; }
    .btn-download:hover { background: #388e3c; color: #fff; }
    
    .btn-resend { background: #fff3e0; color: #f57c00; border: none; }
    .btn-resend:hover { background: #f57c00; color: #fff; }
    
    @media (max-width: 1200px) {
        .invoice-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .invoice-stats {
            grid-template-columns: 1fr;
        }
    }
    
    @media print {
        .page-header-card .btn-export,
        .filters-bar,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_paginate,
        .dataTables_info,
        .action-btns,
        th:last-child,
        td:last-child {
            display: none !important;
        }
        .invoices-page { background: #fff !important; }
        .invoices-table { border-collapse: collapse; }
        .invoices-table th,
        .invoices-table td { border: 1px solid #ddd !important; }
    }
</style>

<div class="invoices-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                Subscription Invoices
            </h1>
            <a href="{{ route('subscription.reports.export', ['type' => 'invoices']) }}" class="btn btn-export" target="_blank">
                <i class="fas fa-download"></i> Export All
            </a>
        </div>

        {{-- Stats --}}
        <div class="invoice-stats">
            <div class="invoice-stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $stats['total_invoices'] ?? 0 }}</h4>
                    <span>Total Invoices</span>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="stat-icon paid">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>${{ number_format($stats['paid_amount'] ?? 0, 2) }}</h4>
                    <span>Paid Amount</span>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h4>${{ number_format($stats['pending_amount'] ?? 0, 2) }}</h4>
                    <span>Pending Amount</span>
                </div>
            </div>
            <div class="invoice-stat-card">
                <div class="stat-icon overdue">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-content">
                    <h4>{{ $stats['overdue_count'] ?? 0 }}</h4>
                    <span>Overdue Invoices</span>
                </div>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="invoices-card">
            <div class="card-header">
                <h3><i class="fas fa-list-alt"></i> All Invoices</h3>
            </div>
            
            <div class="filters-bar">
                <select class="form-select select2" id="status_filter" style="width: 160px;">
                    <option value="">All Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                </select>
                <input type="text" class="form-control" id="date_range" placeholder="Date Range" style="width: 200px;">
                <input type="text" class="form-control" id="search" placeholder="Search invoice #, customer..." style="width: 250px;">
            </div>
            
            <div class="table-responsive">
                <table class="table invoices-table" id="invoices_table" width="100%">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Status filter - light Amazon-style dropdown
    if ($.fn.select2 && $('#status_filter').length) {
        try {
            if ($('#status_filter').data('select2')) $('#status_filter').select2('destroy');
            $('#status_filter').select2({ dropdownCssClass: 'invoice-status-dropdown' });
        } catch (e) {
            $('#status_filter').select2({ dropdownCssClass: 'invoice-status-dropdown' });
        }
    }
    
    var invoices_table = $('#invoices_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('subscription.invoices.data') }}",
            data: function(d) {
                d.status = $('#status_filter').val();
                d.date_range = $('#date_range').val();
                d.search_term = $('#search').val();
            }
        },
        columns: [
            { 
                data: 'invoice_number', 
                name: 'invoice_number',
                render: function(data) {
                    return '<span class="invoice-number">' + data + '</span>';
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name',
                render: function(data, type, row) {
                    var initials = data.charAt(0).toUpperCase();
                    return '<div class="invoice-customer">' +
                           '<div class="avatar">' + initials + '</div>' +
                           '<div class="customer-details">' +
                           '<h6>' + data + '</h6>' +
                           '<span>' + (row.customer_email || '') + '</span>' +
                           '</div></div>';
                }
            },
            { data: 'plan_name', name: 'plan_name' },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data) {
                    return '<span class="invoice-amount">$' + parseFloat(data).toFixed(2) + '</span>';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    return '<span class="badge-status badge-' + data + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            { data: 'due_date_formatted', name: 'due_date' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var html = '<div class="action-btns">';
                    html += '<a href="{{ url("subscription/invoices") }}/' + row.id + '" class="btn btn-view" title="View"><i class="fas fa-eye"></i></a>';
                    html += '<button type="button" class="btn btn-download download-invoice" data-id="' + row.id + '" title="Download"><i class="fas fa-download"></i></button>';
                    if (row.status === 'pending' || row.status === 'overdue') {
                        html += '<button type="button" class="btn btn-resend resend-invoice" data-id="' + row.id + '" title="Resend"><i class="fas fa-paper-plane"></i></button>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        language: {
            emptyTable: "No invoices found",
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
        }
    });

    // Filters
    $('#status_filter').on('change', function() {
        invoices_table.ajax.reload();
    });

    var searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            invoices_table.ajax.reload();
        }, 500);
    });

    // Date range picker
    if ($.fn.daterangepicker) {
        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            invoices_table.ajax.reload();
        });

        $('#date_range').on('cancel.daterangepicker', function() {
            $(this).val('');
            invoices_table.ajax.reload();
        });
    }
});
</script>
@endsection
