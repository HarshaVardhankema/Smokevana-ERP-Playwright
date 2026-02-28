@extends('layouts.app')
@section('title', 'Liabilities')

@section('css')
<style>
/* Liabilities - Professional Purple Theme */
.li-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.li-header-banner {
    background: #37475a;
    border-radius: 6px; padding: 22px 28px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}
.li-header-banner h1, .li-header-banner .subtitle, .li-header-banner i { color: #fff !important; }
.li-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.li-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.li-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.li-btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }
.li-btn-new { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 1px solid #C7511F !important; color: #fff !important; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.li-btn-new:hover { color: #fff !important; opacity: 0.95; text-decoration: none; transform: translateY(-2px); }

.li-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
@media (max-width: 992px) { .li-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .li-stats { grid-template-columns: 1fr; } }

.li-stat-card { background: #fff; border-radius: 14px; padding: 20px 24px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.06); border-left: 4px solid #e5e7eb; display: flex; align-items: center; gap: 16px; transition: all 0.3s ease; }
.li-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12); }
.li-stat-card.total { border-left-color: #ef4444; }
.li-stat-card.overdue { border-left-color: #f59e0b; }
.li-stat-card.paid { border-left-color: #10b981; }
.li-stat-card.active { border-left-color: #3b82f6; }

.li-stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.li-stat-card.total .li-stat-icon { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.li-stat-card.overdue .li-stat-icon { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.li-stat-card.paid .li-stat-icon { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.li-stat-card.active .li-stat-icon { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }

.li-stat-info { flex: 1; }
.li-stat-value { font-size: 28px; font-weight: 700; margin-bottom: 4px; font-family: 'SF Mono', Monaco, monospace; }
.li-stat-card.total .li-stat-value { color: #dc2626; }
.li-stat-card.overdue .li-stat-value { color: #d97706; }
.li-stat-card.paid .li-stat-value { color: #059669; }
.li-stat-card.active .li-stat-value { color: #2563eb; }
.li-stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }

.li-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.06); overflow: hidden; }
.li-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
.li-card-title { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; display: flex; align-items: center; gap: 10px; }
.li-card-title i { color: #8b5cf6; }

/* Enhanced Professional Table Styles */
.li-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.li-table thead th { 
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); 
    color: #fff; 
    font-size: 11px; 
    font-weight: 600; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    padding: 16px 20px; 
    text-align: left;
    white-space: nowrap;
}
.li-table thead th:first-child { border-radius: 0; }
.li-table thead th:last-child { border-radius: 0; }

.li-table tbody td { 
    padding: 18px 20px; 
    font-size: 14px; 
    color: #374151; 
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    transition: all 0.2s ease;
}
.li-table tbody tr { transition: all 0.2s ease; }
.li-table tbody tr:hover td { background: #faf5ff; }
.li-table tbody tr:last-child td { border-bottom: none; }

/* DataTables Integration */
#liabilities_table_wrapper { padding: 0; }
#liabilities_table_wrapper .dataTables_length,
#liabilities_table_wrapper .dataTables_filter {
    padding: 0;
    margin: 0;
}
#liabilities_table_wrapper .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 16px;
}
#liabilities_table_wrapper .dataTables_length label,
#liabilities_table_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}
#liabilities_table_wrapper .dataTables_length select {
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    color: #374151;
    background: #fff;
    min-width: 70px;
    cursor: pointer;
}
#liabilities_table_wrapper .dataTables_filter input {
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    width: 220px;
    transition: all 0.2s ease;
}
#liabilities_table_wrapper .dataTables_filter input:focus {
    border-color: #8b5cf6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.12);
}
#liabilities_table_wrapper .bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 12px;
}
#liabilities_table_wrapper .dataTables_info {
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}
/* Pagination styles handled by global app.css */

/* Export Buttons */
.dt-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.dt-buttons .dt-button {
    background: #fff !important;
    border: 1.5px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 8px 14px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #6b7280 !important;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.dt-buttons .dt-button:hover {
    background: #f5f3ff !important;
    border-color: #8b5cf6 !important;
    color: #7c3aed !important;
}

/* Table Cell Styling */
.li-name-cell { font-weight: 600; color: #1e1b4b; }
.li-type-badge { 
    display: inline-block;
    padding: 4px 10px; 
    border-radius: 6px; 
    font-size: 11px; 
    font-weight: 600; 
    text-transform: uppercase;
    background: #f1f5f9;
    color: #64748b;
}
.li-contact-cell { color: #6b7280; }
.li-contact-cell:empty::after { content: '—'; color: #d1d5db; }
.li-amount-cell { 
    font-family: 'SF Mono', Monaco, 'Consolas', monospace; 
    font-weight: 600; 
    color: #374151;
    text-align: right;
}
.li-balance-cell { 
    font-family: 'SF Mono', Monaco, 'Consolas', monospace; 
    font-weight: 700; 
    color: #7c3aed;
    text-align: right;
}
.li-date-cell { color: #6b7280; white-space: nowrap; }

/* Status Badges */
.li-status { 
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px; 
    border-radius: 20px; 
    font-size: 11px; 
    font-weight: 600; 
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.li-status::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.li-status.active { background: #dbeafe; color: #2563eb; }
.li-status.paid { background: #d1fae5; color: #059669; }
.li-status.overdue { background: #fee2e2; color: #dc2626; }
.li-status.partial { background: #fef3c7; color: #d97706; }

/* Action Buttons */
.li-actions { 
    display: flex; 
    gap: 6px; 
    justify-content: flex-start;
    align-items: center;
}
.li-action-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}
.li-action-btn.view { background: #ede9fe; color: #7c3aed; }
.li-action-btn.view:hover { background: #8b5cf6; color: #fff; }
.li-action-btn.edit { background: #dbeafe; color: #3b82f6; }
.li-action-btn.edit:hover { background: #3b82f6; color: #fff; }
.li-action-btn.pay { background: #d1fae5; color: #10b981; }
.li-action-btn.pay:hover { background: #10b981; color: #fff; }

/* Empty State */
.dataTables_empty {
    padding: 60px 20px !important;
    text-align: center;
}
.li-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 40px;
}
.li-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f5f3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #8b5cf6;
}
.li-empty-title { font-size: 18px; font-weight: 600; color: #1e1b4b; margin: 0; }
.li-empty-text { font-size: 14px; color: #6b7280; margin: 0; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.li-stat-card, .li-card { animation: fadeInUp 0.4s ease forwards; }

/* Print Styles - Prevent extra blank page */
@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    html, body {
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }
    .li-page {
        min-height: 0 !important;
        height: auto !important;
        padding: 0 !important;
        background: #fff !important;
    }
    .li-header-banner,
    .li-stats,
    .li-card-header,
    .dt-buttons,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_paginate,
    .dataTables_info,
    .li-actions,
    .li-action-btn,
    #liabilities_table_wrapper .top,
    #liabilities_table_wrapper .bottom {
        display: none !important;
    }
    .li-card {
        box-shadow: none !important;
        border: none !important;
    }
    .li-table {
        page-break-inside: auto !important;
    }
    .li-table tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    .li-table thead {
        display: table-header-group !important;
    }
    .li-table tbody {
        display: table-row-group !important;
    }
    /* Hide action column in print */
    .li-table th:last-child,
    .li-table td:last-child {
        display: none !important;
    }
}
</style>
@endsection

@section('content')
<section class="content li-page">
    <div class="li-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice"></i> Liabilities Management</h1>
            <p class="subtitle">Track payables, loans, and obligations</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('bookkeeping.dashboard') }}" class="li-btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ route('bookkeeping.liabilities.create') }}" class="li-btn-new"><i class="fas fa-plus"></i> Add Liability</a>
        </div>
    </div>

    <div class="li-stats">
        <div class="li-stat-card total">
            <div class="li-stat-icon"><i class="fas fa-credit-card"></i></div>
            <div class="li-stat-info">
                <div class="li-stat-value">${{ number_format($totalLiabilities ?? 0, 2) }}</div>
                <div class="li-stat-label">Total Active</div>
            </div>
        </div>
        <div class="li-stat-card overdue">
            <div class="li-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="li-stat-info">
                <div class="li-stat-value">${{ number_format($overdueAmount ?? 0, 2) }}</div>
                <div class="li-stat-label">Overdue</div>
            </div>
        </div>
        <div class="li-stat-card paid">
            <div class="li-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="li-stat-info">
                <div class="li-stat-value">${{ number_format($totalPaid ?? 0, 2) }}</div>
                <div class="li-stat-label">Paid This Month</div>
            </div>
        </div>
        <div class="li-stat-card active">
            <div class="li-stat-icon"><i class="fas fa-list"></i></div>
            <div class="li-stat-info">
                <div class="li-stat-value">{{ $activeCount ?? 0 }}</div>
                <div class="li-stat-label">Active Items</div>
            </div>
        </div>
    </div>

    <div class="li-card">
        <div class="li-card-header">
            <h3 class="li-card-title"><i class="fas fa-list"></i> Liability Records</h3>
        </div>
        <table class="li-table" id="liabilities_table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Original Amount</th>
                    <th>Balance</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#liabilities_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("bookkeeping.liabilities.index") }}',
        dom: '<"top"lfB>rt<"bottom"ip><"clear">',
        buttons: [
            { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'dt-button' },
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Export Excel', className: 'dt-button' },
            { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'dt-button' },
            { extend: 'colvis', text: '<i class="fas fa-columns"></i> Column visibility', className: 'dt-button' },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> Export PDF', className: 'dt-button' }
        ],
        columns: [
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return '<span class="li-name-cell">' + (data || '—') + '</span>';
                }
            },
            {
                data: 'liability_type', 
                name: 'liability_type',
                render: function(data, type, row) {
                    var typeLabel = data ? data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '—';
                    return '<span class="li-type-badge">' + typeLabel + '</span>';
                }
            },
            {
                data: 'contact_name', 
                name: 'contact_name',
                render: function(data, type, row) {
                    return '<span class="li-contact-cell">' + (data || '—') + '</span>';
                }
            },
            {
                data: 'original_amount', 
                name: 'original_amount',
                render: function(data, type, row) {
                    var amount = parseFloat(data) || 0;
                    return '<span class="li-amount-cell">$' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                }
            },
            {
                data: 'current_balance', 
                name: 'current_balance',
                render: function(data, type, row) {
                    var balance = parseFloat(data) || 0;
                    return '<span class="li-balance-cell">$' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                }
            },
            {
                data: 'due_date', 
                name: 'due_date',
                render: function(data, type, row) {
                    if (!data) return '<span class="li-date-cell">—</span>';
                    var date = new Date(data);
                    var formatted = (date.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                   date.getDate().toString().padStart(2, '0') + '/' + 
                                   date.getFullYear();
                    return '<span class="li-date-cell">' + formatted + '</span>';
                }
            },
            {
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var statusClass = data || 'active';
                    var statusLabel = data ? data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Active';
                    return '<span class="li-status ' + statusClass + '">' + statusLabel + '</span>';
                }
            },
            {
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return '<div class="li-actions">' +
                        '<a href="' + row.view_url + '" class="li-action-btn view" title="View"><i class="fas fa-eye"></i></a>' +
                        '<a href="' + row.edit_url + '" class="li-action-btn edit" title="Edit"><i class="fas fa-edit"></i></a>' +
                        '<button type="button" class="li-action-btn pay btn-coming-soon" title="Make Payment"><i class="fas fa-dollar-sign"></i></button>' +
                    '</div>';
                }
            }
        ],
        order: [[5, 'asc']],
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-purple" role="status"><span class="sr-only">Loading...</span></div></div>',
            emptyTable: '<div class="li-empty-state"><div class="li-empty-icon"><i class="fas fa-file-invoice"></i></div><h4 class="li-empty-title">No Liabilities Found</h4><p class="li-empty-text">Start by adding your first liability record</p></div>',
            zeroRecords: '<div class="li-empty-state"><div class="li-empty-icon"><i class="fas fa-search"></i></div><h4 class="li-empty-title">No Matching Records</h4><p class="li-empty-text">Try adjusting your search criteria</p></div>'
        },
        drawCallback: function() {
            // Add smooth transitions after draw
            $('#liabilities_table tbody tr').css('opacity', '0').each(function(i) {
                $(this).delay(i * 30).animate({opacity: 1}, 200);
            });
        }
    });
    
    // Coming Soon popup for Make Payment button
    $(document).on('click', '.btn-coming-soon', function(e) {
        e.preventDefault();
        swal({
            title: 'Coming Soon',
            text: 'The payment feature is under development and will be available soon!',
            icon: 'info',
            button: 'OK'
        });
    });
});
</script>
@endsection
