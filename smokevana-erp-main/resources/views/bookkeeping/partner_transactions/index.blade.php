@extends('layouts.app')
@section('title', 'Partner Transactions')

@section('css')
<style>
/* Partner Transactions - Matching Chart of Accounts UI */
.pt-page { 
    background: linear-gradient(180deg, #f8f9fe 0%, #f3f4f6 100%); 
    min-height: 100vh; 
    padding: 0;
}

/* Header Banner - Amazon style */
.pt-header {
    background: #37475a;
    border-radius: 6px;
    padding: 20px 28px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}
.pt-header-left h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff !important;
}
.pt-header-left h1 i { font-size: 20px; color: #fff !important; }
.pt-header-left .subtitle { 
    font-size: 13px; 
    opacity: 0.9; 
    margin: 0; 
    color: #fff !important;
}
.pt-header-actions { display: flex; gap: 10px; }
.pt-btn-header {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    border: none;
}
.pt-btn-primary { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 1px solid #C7511F !important; color: #fff !important; }
.pt-btn-primary:hover { color: #fff !important; opacity: 0.95; text-decoration: none; }

/* Content Container */
.pt-content { padding: 20px; max-width: 1400px; margin: 0 auto; }

/* Stats Row - Horizontal Cards with Icon Left, Data Right */
.pt-stats-row {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.pt-stat-card {
    flex: 1;
    min-width: 200px;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s ease;
}
.pt-stat-card:hover {
    border-color: #c4b5fd;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
}
.pt-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.pt-stat-icon.green { background: #d1fae5; color: #059669; }
.pt-stat-icon.red { background: #fee2e2; color: #dc2626; }
.pt-stat-icon.blue { background: #dbeafe; color: #2563eb; }
.pt-stat-icon.yellow { background: #fef3c7; color: #d97706; }
.pt-stat-data { flex: 1; }
.pt-stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #1e1b4b;
    line-height: 1.2;
}
.pt-stat-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.pt-stat-badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-left: auto;
}
.pt-stat-badge.active { background: #d1fae5; color: #059669; }
.pt-stat-badge.outflow { background: #fee2e2; color: #dc2626; }
.pt-stat-badge.liability { background: #f3f4f6; color: #6b7280; }
.pt-stat-badge.pending { background: #fef3c7; color: #d97706; }

/* Quick Actions Row */
.pt-quick-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.pt-quick-btn {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border: 1px solid #C7511F;
    border-radius: 8px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #fff !important;
    text-decoration: none;
    transition: all 0.2s ease;
}
.pt-quick-btn:hover {
    opacity: 0.95;
    color: #fff !important;
    text-decoration: none;
}
.pt-quick-btn i {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.pt-quick-btn i.green { background: #d1fae5; color: #059669; }
.pt-quick-btn i.red { background: #fee2e2; color: #dc2626; }
.pt-quick-btn i.blue { background: #dbeafe; color: #2563eb; }
.pt-quick-btn i.purple { background: #ede9fe; color: #7c3aed; }

/* Filter Section */
.pt-filter-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.pt-filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.pt-filter-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e1b4b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pt-filter-title i { color: #8b5cf6; }
.pt-btn-reset {
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}
.pt-btn-reset:hover { background: #e5e7eb; color: #374151; }
.pt-filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 992px) { .pt-filter-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .pt-filter-grid { grid-template-columns: 1fr; } }
.pt-filter-group label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 6px;
}
.pt-filter-group .form-control,
.pt-filter-group .select2-container--default .select2-selection--single {
    height: 40px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
}
.pt-filter-group .form-control:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Table Card */
.pt-table-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}
.pt-table-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    background: #fafbfc;
}
.pt-table-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e1b4b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pt-table-title i { color: #8b5cf6; }
.pt-table-actions { display: flex; gap: 8px; }
.pt-btn-export {
    background: #fff;
    color: #6b7280;
    border: 1.5px solid #e5e7eb;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}
.pt-btn-export:hover { border-color: #8b5cf6; color: #8b5cf6; background: #faf5ff; }

/* DataTable Styling */
#partner_transactions_table { width: 100% !important; }
#partner_transactions_table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border: none;
}
#partner_transactions_table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    color: #374151;
}
#partner_transactions_table tbody tr:hover { background: #faf5ff; }

/* Type Badge */
.pt-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.pt-type-badge.capital_contribution { background: #d1fae5; color: #059669; }
.pt-type-badge.owner_drawing { background: #fee2e2; color: #dc2626; }
.pt-type-badge.loan_from_partner { background: #dbeafe; color: #2563eb; }
.pt-type-badge.loan_to_partner { background: #fef3c7; color: #d97706; }
.pt-type-badge.advance_to_partner { background: #e0e7ff; color: #4f46e5; }
.pt-type-badge.advance_from_partner { background: #fce7f3; color: #db2777; }
.pt-type-badge.loan_repayment { background: #d1fae5; color: #059669; }
.pt-type-badge.interest_payment { background: #f3e8ff; color: #9333ea; }
.pt-type-badge.advance { background: #f3e8ff; color: #7c3aed; }
.pt-type-badge.unknown { background: #f3f4f6; color: #6b7280; }

/* Amount Cell */
.pt-amount { font-weight: 600; font-size: 13px; }
.pt-amount.positive { color: #059669; }
.pt-amount.negative { color: #dc2626; }

/* Partner Cell */
.pt-partner { display: flex; align-items: center; gap: 10px; }
.pt-partner-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}
.pt-partner-name { font-weight: 600; color: #1e1b4b; }

/* Action Buttons */
.pt-actions { display: flex; gap: 6px; }
.pt-action-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}
.pt-action-btn.view { background: #ede9fe; color: #7c3aed; }
.pt-action-btn.view:hover { background: #8b5cf6; color: #fff; }
.pt-action-btn.edit { background: #dbeafe; color: #2563eb; }
.pt-action-btn.edit:hover { background: #3b82f6; color: #fff; }
.pt-action-btn.delete { background: #fee2e2; color: #dc2626; }
.pt-action-btn.delete:hover { background: #ef4444; color: #fff; }

/* DataTable Controls - Compact Layout */
.dataTables_wrapper .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter { 
    padding: 0;
    margin: 0;
    display: inline-flex;
    align-items: center;
}
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 13px;
    color: #6b7280;
}
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1.5px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 13px;
    height: 36px;
}
.dataTables_wrapper .dataTables_filter input {
    width: 200px;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #8b5cf6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.dataTables_wrapper .bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-top: 1px solid #f1f5f9;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate { 
    padding: 0;
    margin: 0;
}
.dataTables_wrapper .dataTables_info {
    font-size: 13px;
    color: #6b7280;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    border: none !important;
    margin: 0 2px;
    padding: 6px 12px !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    color: #fff !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #ede9fe !important;
    color: #7c3aed !important;
}

/* Empty State */
.pt-empty {
    text-align: center;
    padding: 50px 20px;
}
.pt-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #ede9fe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #8b5cf6;
}
.pt-empty h4 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin-bottom: 6px; }
.pt-empty p { font-size: 13px; color: #6b7280; }
</style>
@endsection

@section('content')
<div class="pt-page">
    <!-- Header -->
    <div class="pt-header">
        <div class="pt-header-left">
            <h1><i class="fas fa-users-cog"></i> Partner Transactions</h1>
            <p class="subtitle">Manage business partner capital, drawings, loans, and advances</p>
        </div>
        <div class="pt-header-actions">
            <a href="{{ route('bookkeeping.partner.create') }}" class="pt-btn-header pt-btn-primary">
                <i class="fas fa-plus"></i> Add Transaction
            </a>
        </div>
    </div>

    <div class="pt-content">
        <!-- Stats Row -->
        <div class="pt-stats-row">
            <div class="pt-stat-card">
                <div class="pt-stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="pt-stat-data">
                    <div class="pt-stat-number">${{ number_format($totalCapital ?? 0, 2) }}</div>
                    <div class="pt-stat-label">Capital Contributions</div>
                </div>
                <span class="pt-stat-badge active"><i class="fas fa-arrow-up"></i> Active</span>
            </div>
            <div class="pt-stat-card">
                <div class="pt-stat-icon red"><i class="fas fa-sign-out-alt"></i></div>
                <div class="pt-stat-data">
                    <div class="pt-stat-number">${{ number_format($totalDrawings ?? 0, 2) }}</div>
                    <div class="pt-stat-label">Owner Drawings</div>
                </div>
                <span class="pt-stat-badge outflow"><i class="fas fa-arrow-down"></i> Outflow</span>
            </div>
            <div class="pt-stat-card">
                <div class="pt-stat-icon blue"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="pt-stat-data">
                    <div class="pt-stat-number">${{ number_format($totalLoansFromPartners ?? 0, 2) }}</div>
                    <div class="pt-stat-label">Loans from Partners</div>
                </div>
                <span class="pt-stat-badge liability"><i class="fas fa-minus"></i> Liability</span>
            </div>
            <div class="pt-stat-card">
                <div class="pt-stat-icon yellow"><i class="fas fa-wallet"></i></div>
                <div class="pt-stat-data">
                    <div class="pt-stat-number">${{ number_format($totalAdvances ?? 0, 2) }}</div>
                    <div class="pt-stat-label">Partner Advances</div>
                </div>
                <span class="pt-stat-badge pending"><i class="fas fa-minus"></i> Pending</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="pt-quick-actions">
            <a href="{{ route('bookkeeping.partner.create') }}?type=capital_contribution" class="pt-quick-btn">
                <i class="fas fa-plus-circle green"></i> Record Capital
            </a>
            <a href="{{ route('bookkeeping.partner.create') }}?type=owner_drawing" class="pt-quick-btn">
                <i class="fas fa-minus-circle red"></i> Record Drawing
            </a>
            <a href="{{ route('bookkeeping.partner.create') }}?type=loan_from_partner" class="pt-quick-btn">
                <i class="fas fa-handshake blue"></i> Record Loan
            </a>
            <a href="{{ route('bookkeeping.partner.create') }}?type=loan_repayment" class="pt-quick-btn">
                <i class="fas fa-undo purple"></i> Loan Repayment
            </a>
        </div>

        <!-- Filters -->
        <div class="pt-filter-card">
            <div class="pt-filter-header">
                <div class="pt-filter-title"><i class="fas fa-filter"></i> Filter Transactions</div>
                <button type="button" class="pt-btn-reset" id="reset_filters">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
            <div class="pt-filter-grid">
                <div class="pt-filter-group">
                    <label>Transaction Type</label>
                    <select id="filter_transaction_type" class="form-control select2" style="width:100%">
                        <option value="">All Types</option>
                        @foreach($transactionTypes ?? [] as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-filter-group">
                    <label>Partner</label>
                    <select id="filter_partner" class="form-control select2" style="width:100%">
                        <option value="">All Partners</option>
                        @foreach($partners ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-filter-group">
                    <label>Date Range</label>
                    <input type="text" id="filter_date_range" class="form-control" placeholder="Select date range">
                </div>
                <div class="pt-filter-group">
                    <label>Amount Range</label>
                    <select id="filter_amount_range" class="form-control">
                        <option value="">All Amounts</option>
                        <option value="0-1000">$0 - $1,000</option>
                        <option value="1000-5000">$1,000 - $5,000</option>
                        <option value="5000-10000">$5,000 - $10,000</option>
                        <option value="10000+">$10,000+</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="pt-table-card">
            <div class="pt-table-header">
                <div class="pt-table-title"><i class="fas fa-list-alt"></i> Transaction History</div>
                <div class="pt-table-actions">
                    <button class="pt-btn-export" id="export_csv"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="pt-btn-export" id="export_excel"><i class="fas fa-file-excel"></i> Excel</button>
                    <button class="pt-btn-export" id="export_pdf"><i class="fas fa-file-pdf"></i> PDF</button>
                </div>
            </div>
            <div class="pt-table-body">
                <table class="table" id="partner_transactions_table" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Partner</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade partner_transaction_modal" tabindex="-1" role="dialog"></div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    if ($.fn.daterangepicker) {
        $('#filter_date_range').daterangepicker({
            autoUpdateInput: false,
            locale: { cancelLabel: 'Clear', format: 'MM/DD/YYYY' }
        });
        $('#filter_date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            table.ajax.reload();
        });
        $('#filter_date_range').on('cancel.daterangepicker', function() {
            $(this).val('');
            table.ajax.reload();
        });
    }

    var table = $('#partner_transactions_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('bookkeeping.partner.index') }}",
            data: function(d) {
                d.transaction_type = $('#filter_transaction_type').val();
                d.partner_id = $('#filter_partner').val();
                d.date_range = $('#filter_date_range').val();
                d.amount_range = $('#filter_amount_range').val();
            }
        },
        columns: [
            { 
                data: 'transaction_date', 
                name: 'transaction_date',
                render: function(data) {
                    if (data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    }
                    return '-';
                }
            },
            { 
                data: 'partner_name', 
                name: 'partner.first_name',
                render: function(data) {
                    if (data) {
                        var initials = data.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                        return '<div class="pt-partner"><div class="pt-partner-avatar">' + initials + '</div><span class="pt-partner-name">' + data + '</span></div>';
                    }
                    return '-';
                }
            },
            { 
                data: 'transaction_type_raw', 
                name: 'transaction_type',
                render: function(data) {
                    if (data && data.trim() !== '') {
                        var typeClass = data.toLowerCase().replace(/ /g, '_');
                        var icons = {
                            'capital_contribution': 'fa-plus-circle',
                            'owner_drawing': 'fa-minus-circle',
                            'loan_from_partner': 'fa-handshake',
                            'loan_to_partner': 'fa-hand-holding-usd',
                            'loan_repayment': 'fa-undo',
                            'advance': 'fa-wallet'
                        };
                        var labels = {
                            'capital_contribution': 'Capital Contribution',
                            'owner_drawing': 'Owner Drawing',
                            'loan_from_partner': 'Loan from Partner',
                            'loan_to_partner': 'Loan to Partner',
                            'loan_repayment': 'Loan Repayment',
                            'advance': 'Partner Advance'
                        };
                        var icon = icons[typeClass] || 'fa-exchange-alt';
                        var label = labels[typeClass] || data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        return '<span class="pt-type-badge ' + typeClass + '"><i class="fas ' + icon + '"></i> ' + label + '</span>';
                    }
                    return '<span class="pt-type-badge unknown"><i class="fas fa-question-circle"></i> Unknown</span>';
                }
            },
            { 
                data: 'amount_raw', 
                name: 'amount',
                render: function(data, type, row) {
                    var amount = parseFloat(data) || 0;
                    var formatted = '$' + amount.toLocaleString('en-US', {minimumFractionDigits: 2});
                    var txType = row.transaction_type_raw || '';
                    var cls = ['owner_drawing', 'loan_to_partner', 'advance_to_partner'].includes(txType) ? 'negative' : 'positive';
                    return '<span class="pt-amount ' + cls + '">' + formatted + '</span>';
                }
            },
            { data: 'account_name', name: 'account.name' },
            { 
                data: 'description', 
                name: 'description',
                render: function(data) {
                    if (data && data.length > 35) return '<span title="' + data + '">' + data.substring(0, 35) + '...</span>';
                    return data || '-';
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return '<div class="pt-actions">' +
                        '<button class="pt-action-btn view" title="View" onclick="viewTransaction(' + row.id + ')"><i class="fas fa-eye"></i></button>' +
                        '<button class="pt-action-btn edit" title="Edit" onclick="editTransaction(' + row.id + ')"><i class="fas fa-edit"></i></button>' +
                        '<button class="pt-action-btn delete" title="Delete" onclick="deleteTransaction(' + row.id + ')"><i class="fas fa-trash"></i></button>' +
                        '</div>';
                }
            }
        ],
        order: [[0, 'desc']],
        dom: '<"top"lf>rt<"bottom"ip><"clear">',
        language: {
            search: '<i class="fas fa-search"></i>',
            searchPlaceholder: 'Search transactions...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ transactions',
            paginate: { previous: '<i class="fas fa-chevron-left"></i>', next: '<i class="fas fa-chevron-right"></i>' },
            emptyTable: '<div class="pt-empty"><div class="pt-empty-icon"><i class="fas fa-users"></i></div><h4>No Partner Transactions</h4><p>Start by adding your first partner transaction</p></div>'
        }
    });

    $('#filter_transaction_type, #filter_partner, #filter_amount_range').on('change', function() { table.ajax.reload(); });
    $('#reset_filters').on('click', function() {
        $('#filter_transaction_type').val('').trigger('change');
        $('#filter_partner').val('').trigger('change');
        $('#filter_date_range').val('');
        $('#filter_amount_range').val('');
        table.ajax.reload();
    });

    function exportData(format) {
        var params = {
            export: format,
            transaction_type: $('#filter_transaction_type').val(),
            partner_id: $('#filter_partner').val(),
            date_range: $('#filter_date_range').val()
        };
        window.location.href = "{{ route('bookkeeping.partner.index') }}?" + $.param(params);
    }
    $('#export_csv').on('click', function() { exportData('csv'); });
    $('#export_excel').on('click', function() { exportData('excel'); });
    $('#export_pdf').on('click', function() { exportData('pdf'); });
});

function viewTransaction(id) {
    window.location.href = "{{ route('bookkeeping.partner.show', ':id') }}".replace(':id', id);
}
function editTransaction(id) {
    window.location.href = "{{ route('bookkeeping.partner.edit', ':id') }}".replace(':id', id);
}
function deleteTransaction(id) {
    swal({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        buttons: { cancel: { text: "Cancel", visible: true, closeModal: true }, confirm: { text: "Yes, delete it!", closeModal: false } },
        dangerMode: true
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: "{{ route('bookkeeping.partner.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        swal('Deleted!', response.msg, 'success');
                        $('#partner_transactions_table').DataTable().ajax.reload();
                    } else {
                        swal('Error!', response.msg, 'error');
                    }
                },
                error: function() { swal('Error!', 'Failed to delete transaction', 'error'); }
            });
        }
    });
}
</script>
@endsection
