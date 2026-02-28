@extends('layouts.app')
@section('title', 'P&L Transactions')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .pl-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    .page-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 24px;
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }

    .page-header-banner::before { display: none; }

    .page-header-banner .header-title-area h1,
    .page-header-banner .header-title-area h1 *,
    .page-header-banner .header-subtitle {
        color: #fff !important;
    }

    .page-header-banner .header-title-area h1 i {
        color: #fff !important;
    }

    .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-title-area h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #fff !important;
    }

    .header-title-area h1 i {
        color: #fff !important;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.95;
        color: #fff !important;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-header-secondary {
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.25);
    }

    .btn-header-secondary:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
        text-decoration: none;
    }

    .btn-header-success {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #C7511F !important;
        color: #fff !important;
    }

    .btn-header-success:hover {
        opacity: 0.95;
        color: #fff !important;
        text-decoration: none;
    }

    .btn-header-danger {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #C7511F !important;
        color: #fff !important;
    }

    .btn-header-danger:hover {
        opacity: 0.95;
        color: #fff !important;
        text-decoration: none;
    }

    .content-area {
        padding: 30px 40px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.income::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.expense::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .stat-card.income-count::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-card.expense-count::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }

    .stat-card.income .stat-icon { background: #d1fae5; color: #059669; }
    .stat-card.expense .stat-icon { background: #fee2e2; color: #dc2626; }
    .stat-card.income-count .stat-icon { background: #dbeafe; color: #2563eb; }
    .stat-card.expense-count .stat-icon { background: #fef3c7; color: #d97706; }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 14px;
        color: #6b7280;
    }

    /* Table Card */
    .table-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.1);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #fafbff 0%, #f5f3ff 100%);
        padding: 20px 24px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e1b4b;
    }

    .filter-controls {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        min-width: 150px;
    }

    .table-body {
        padding: 24px 28px 28px 28px;
        background: #fafbff;
    }

    /* DataTable Styling - Enhanced ERP Theme */
    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
        padding: 0 8px;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        outline: none;
    }

    .dataTables_wrapper .dataTables_info {
        padding: 16px 8px;
        font-size: 14px;
        color: #6b7280;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding: 16px 8px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 8px 14px !important;
        margin: 0 4px !important;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        background: #fff !important;
        color: #374151 !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f5f3ff !important;
        border-color: #c4b5fd !important;
        color: #7c3aed !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        border-color: #7c3aed !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    table.dataTable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: -8px;
    }

    table.dataTable thead th {
        color: #fff;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: none;
        white-space: nowrap;
    }

    table.dataTable thead th:first-child {
        border-radius: 10px 0 0 10px;
    }

    table.dataTable thead th:last-child {
        border-radius: 0 10px 10px 0;
    }

    table.dataTable tbody tr {
        background: #fff;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.06);
        transition: all 0.2s ease;
    }

    table.dataTable tbody tr:hover {
        background: #faf5ff;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.12);
        transform: translateY(-1px);
    }

    table.dataTable tbody td {
        padding: 18px 20px;
        border: none;
        vertical-align: middle;
        font-size: 14px;
        color: #374151;
        background: transparent;
    }

    table.dataTable tbody td:first-child {
        border-radius: 10px 0 0 10px;
        font-weight: 600;
        color: #1e1b4b;
    }

    table.dataTable tbody td:last-child {
        border-radius: 0 10px 10px 0;
    }

    /* Even/Odd row styling */
    table.dataTable.stripe tbody tr.odd,
    table.dataTable.table-striped tbody tr.odd {
        background: #fff;
    }

    table.dataTable.stripe tbody tr.even,
    table.dataTable.table-striped tbody tr.even {
        background: #fafbff;
    }

    table.dataTable.stripe tbody tr.odd:hover,
    table.dataTable.table-striped tbody tr.odd:hover,
    table.dataTable.stripe tbody tr.even:hover,
    table.dataTable.table-striped tbody tr.even:hover {
        background: #f5f3ff;
    }

    /* Badges - Enhanced */
    .badge-type {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .badge-type i {
        font-size: 10px;
    }

    .badge-income {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
    }

    .badge-expense {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
    }

    .badge-status {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .badge-posted {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
    }

    .badge-draft {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #4b5563;
        box-shadow: 0 2px 6px rgba(107, 114, 128, 0.15);
    }

    .badge-voided {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.15);
    }

    .amount-cell {
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: -0.5px;
    }

    .amount-income {
        color: #059669;
    }

    .amount-expense {
        color: #dc2626;
    }

    /* Action Buttons - Enhanced */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .action-btn i {
        font-size: 14px;
    }

    .action-btn-view {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #7c3aed;
    }

    .action-btn-view:hover {
        background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        color: #6d28d9;
        text-decoration: none;
    }

    .action-btn-void {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .action-btn-void:hover {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    }

    /* Category cell styling */
    .category-cell {
        font-weight: 500;
        color: #4b5563;
    }

    /* Description cell styling */
    .description-cell {
        color: #6b7280;
        font-size: 13px;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Contact cell styling */
    .contact-cell {
        font-weight: 500;
        color: #1e1b4b;
    }

    /* Reference cell styling */
    .reference-cell {
        font-family: 'SF Mono', 'Monaco', monospace;
        font-size: 13px;
        color: #6366f1;
        font-weight: 600;
        background: #eef2ff;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Loading state */
    .dataTables_processing {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: #fff !important;
        border-radius: 10px !important;
        padding: 16px 24px !important;
        box-shadow: 0 8px 30px rgba(124, 58, 237, 0.3) !important;
        font-weight: 600 !important;
    }

    @media (max-width: 1200px) {
        table.dataTable tbody td {
            padding: 14px 16px;
        }
        
        .description-cell {
            max-width: 150px;
        }
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .table-body {
            padding: 16px;
            overflow-x: auto;
        }
        
        table.dataTable {
            min-width: 900px;
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .header-content {
            flex-direction: column;
            text-align: center;
        }
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        .header-actions .btn-header {
            width: 100%;
            justify-content: center;
        }
        .content-area {
            padding: 16px;
        }
        .page-header-banner {
            padding: 24px 20px;
        }
        .header-title-area h1 {
            font-size: 22px;
        }
        .filter-controls {
            width: 100%;
        }
        .filter-select {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="pl-wrapper">
    <!-- Header -->
    <div class="page-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1><i class="fas fa-exchange-alt"></i> P&L Transactions</h1>
                <p class="header-subtitle">Manage Income & Expense Entries</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.dashboard') }}" class="btn-header btn-header-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('bookkeeping.pl.income.create') }}" class="btn-header btn-header-success">
                    <i class="fas fa-plus"></i> Add Income
                </a>
                <a href="{{ route('bookkeeping.pl.expense.create') }}" class="btn-header btn-header-danger">
                    <i class="fas fa-minus"></i> Add Expense
                </a>
            </div>
        </div>
    </div>

    <div class="content-area">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card income">
                <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="stat-value">${{ number_format($totalIncome, 2) }}</div>
                <div class="stat-label">Total Income</div>
            </div>
            <div class="stat-card expense">
                <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="stat-value">${{ number_format($totalExpenses, 2) }}</div>
                <div class="stat-label">Total Expenses</div>
            </div>
            <div class="stat-card income-count">
                <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="stat-value">{{ $incomeCount }}</div>
                <div class="stat-label">Income Entries</div>
            </div>
            <div class="stat-card expense-count">
                <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                <div class="stat-value">{{ $expenseCount }}</div>
                <div class="stat-label">Expense Entries</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">All Transactions</h3>
                <div class="filter-controls">
                    <select id="filter_type" class="filter-select">
                        <option value="">All Types</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expense Only</option>
                    </select>
                    <select id="filter_status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="posted">Posted</option>
                        <option value="draft">Draft</option>
                        <option value="voided">Voided</option>
                    </select>
                </div>
            </div>
            <div class="table-body">
                <table id="pl_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Contact</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#pl_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('bookkeeping.pl.index') }}",
            data: function(d) {
                d.transaction_type = $('#filter_type').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { 
                data: 'transaction_date', 
                name: 'transaction_date',
                render: function(data) {
                    if (!data) return '-';
                    var date = new Date(data);
                    return '<span style="font-weight: 600; color: #1e1b4b;">' + date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</span>';
                }
            },
            { 
                data: 'reference_number', 
                name: 'reference_number',
                render: function(data) {
                    return data ? '<span class="reference-cell">' + data + '</span>' : '<span style="color: #9ca3af;">-</span>';
                }
            },
            { 
                data: 'transaction_type', 
                name: 'transaction_type',
                render: function(data) {
                    if (data === 'income') {
                        return '<span class="badge-type badge-income"><i class="fas fa-arrow-up"></i> Income</span>';
                    }
                    return '<span class="badge-type badge-expense"><i class="fas fa-arrow-down"></i> Expense</span>';
                }
            },
            { 
                data: 'category_label', 
                name: 'category',
                render: function(data) {
                    return '<span class="category-cell">' + (data || '-') + '</span>';
                }
            },
            { 
                data: 'description', 
                name: 'description',
                render: function(data) {
                    if (!data) return '<span style="color: #9ca3af;">-</span>';
                    var truncated = data.length > 35 ? data.substring(0, 35) + '...' : data;
                    return '<span class="description-cell" title="' + data + '">' + truncated + '</span>';
                }
            },
            { 
                data: 'contact_name', 
                name: 'contact_name',
                render: function(data) {
                    return data ? '<span class="contact-cell">' + data + '</span>' : '<span style="color: #9ca3af;">-</span>';
                }
            },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    var className = row.transaction_type === 'income' ? 'amount-income' : 'amount-expense';
                    var prefix = row.transaction_type === 'income' ? '+' : '-';
                    return '<span class="amount-cell ' + className + '">' + prefix + '$' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    var badgeClass = 'badge-' + data;
                    var icon = data === 'posted' ? '<i class="fas fa-check-circle" style="margin-right: 4px;"></i>' : 
                               data === 'voided' ? '<i class="fas fa-ban" style="margin-right: 4px;"></i>' : 
                               '<i class="fas fa-edit" style="margin-right: 4px;"></i>';
                    return '<span class="badge-status ' + badgeClass + '">' + icon + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    var html = '<div class="action-buttons">';
                    html += '<a href="' + row.view_url + '" class="action-btn action-btn-view" title="View Details"><i class="fas fa-eye"></i></a>';
                    if (row.status === 'posted') {
                        html += '<button class="action-btn action-btn-void" onclick="voidTransaction(' + row.id + ')" title="Void Transaction"><i class="fas fa-ban"></i></button>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        responsive: true,
        autoWidth: false,
        language: {
            emptyTable: '<div style="padding: 40px; text-align: center;"><i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i><span style="color: #6b7280; font-size: 16px;">No transactions recorded yet</span></div>',
            search: '<i class="fas fa-search" style="color: #9ca3af;"></i>',
            searchPlaceholder: 'Search transactions...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ transactions',
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        drawCallback: function() {
            // Add animation to rows on draw
            $(this.api().table().body()).find('tr').each(function(index) {
                $(this).css('animation', 'fadeIn 0.3s ease ' + (index * 0.03) + 's both');
            });
        }
    });

    // Filter change handlers
    $('#filter_type, #filter_status').change(function() {
        table.ajax.reload();
    });
});

function voidTransaction(id) {
    Swal.fire({
        title: 'Void Transaction?',
        text: 'This will reverse the journal entry and mark the transaction as voided.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, void it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('bookkeeping/pl-transactions') }}/" + id + "/void",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Voided!', response.msg, 'success');
                        $('#pl_table').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error', response.msg, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to void transaction', 'error');
                }
            });
        }
    });
}
</script>
@endsection




