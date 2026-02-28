@extends('layouts.app')
@section('title', 'Journal Entries')

@section('css')
<style>
/* Journal Entries - Professional Purple Theme */
.je-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.je-header-banner {
    background: #37475a;
    border-radius: 6px; padding: 22px 28px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}
.je-header-banner h1, .je-header-banner .subtitle, .je-header-banner i { color: #fff !important; }
.je-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.je-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.je-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.je-btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }
.je-btn-new { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 1px solid #C7511F !important; color: #fff !important; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.je-btn-new i { color: #fff !important; }
.je-btn-new:hover { color: #fff !important; opacity: 0.95; text-decoration: none; transform: translateY(-2px); }

/* Stats Cards */
.je-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
@media (max-width: 992px) { .je-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .je-stats { grid-template-columns: 1fr; } }

.je-stat-card { background: #fff; border-radius: 14px; padding: 20px 24px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.06); border-left: 4px solid #e5e7eb; display: flex; align-items: center; gap: 16px; transition: all 0.3s ease; }
.je-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12); }
.je-stat-card.draft { border-left-color: #f59e0b; }
.je-stat-card.posted { border-left-color: #10b981; }
.je-stat-card.voided { border-left-color: #ef4444; }
.je-stat-card.total { border-left-color: #8b5cf6; }

.je-stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.je-stat-card.draft .je-stat-icon { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.je-stat-card.posted .je-stat-icon { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.je-stat-card.voided .je-stat-icon { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.je-stat-card.total .je-stat-icon { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }

.je-stat-info { flex: 1; }
.je-stat-value { font-size: 28px; font-weight: 700; margin-bottom: 4px; font-family: 'SF Mono', Monaco, monospace; }
.je-stat-card.draft .je-stat-value { color: #d97706; }
.je-stat-card.posted .je-stat-value { color: #059669; }
.je-stat-card.voided .je-stat-value { color: #dc2626; }
.je-stat-card.total .je-stat-value { color: #7c3aed; }
.je-stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }

/* Filter Section */
.je-filters { background: #fff; border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.je-filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 160px; }
.je-filter-label { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
.je-filter-input { padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 13px; transition: all 0.2s ease; background: #fff; min-width: 160px; }
.je-filter-input:focus { border-color: #8b5cf6; outline: none; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); }

/* Table Card */
.je-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); overflow: hidden; }
.je-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%); }
.je-card-title { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; display: flex; align-items: center; gap: 10px; }
.je-card-title i { color: #8b5cf6; }

/* Table */
.je-table { width: 100%; border-collapse: collapse; }
.je-table thead th { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; }
.je-table tbody td { padding: 14px 16px; border-bottom: 1px solid #f5f5f5; font-size: 14px; color: #374151; }
.je-table tbody tr:hover { background: #faf5ff; }

/* Status Badges */
.je-status { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.je-status.draft { background: #fef3c7; color: #d97706; }
.je-status.posted { background: #d1fae5; color: #059669; }
.je-status.voided { background: #fee2e2; color: #dc2626; }

/* Type Badges */
.je-type { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #ede9fe; color: #7c3aed; }

/* Amount */
.je-amount { font-family: 'SF Mono', Monaco, monospace; font-weight: 600; }
.je-amount.debit { color: #059669; }
.je-amount.credit { color: #dc2626; }

/* Actions Dropdown */
.je-actions-dropdown { position: relative; display: inline-block; }
.je-actions-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 1px solid #C7511F !important; color: #fff !important; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
.je-actions-btn:hover { opacity: 0.95; transform: translateY(-1px); color: #fff !important; }
.je-actions-btn i { font-size: 10px; }
.je-actions-menu { position: absolute; right: 0; top: 100%; margin-top: 4px; background: #fff; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); min-width: 160px; z-index: 1000; display: none; overflow: hidden; border: 1px solid #e5e7eb; }
.je-actions-dropdown.open .je-actions-menu { display: block; animation: dropdownFadeIn 0.2s ease; }
.je-actions-menu a, .je-actions-menu button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 16px; font-size: 13px; color: #374151; background: none; border: none; text-align: left; cursor: pointer; transition: all 0.15s ease; text-decoration: none; }
.je-actions-menu a:hover, .je-actions-menu button:hover { background: #f5f3ff; color: #7c3aed; }
.je-actions-menu a i, .je-actions-menu button i { width: 16px; text-align: center; }
.je-actions-menu .action-view i { color: #7c3aed; }
.je-actions-menu .action-post i { color: #059669; }
.je-actions-menu .action-post:hover { background: #d1fae5; color: #059669; }
.je-actions-menu .action-void i { color: #dc2626; }
.je-actions-menu .action-void:hover { background: #fee2e2; color: #dc2626; }
.je-actions-menu .divider { height: 1px; background: #e5e7eb; margin: 4px 0; }
@keyframes dropdownFadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

/* DataTables Override */
.je-card .dataTables_wrapper { padding: 0; }
.je-card .dataTables_filter, .je-card .dataTables_length { padding: 16px 24px; }
.je-card .dataTables_info, .je-card .dataTables_paginate { padding: 16px 24px; }
.je-card .dataTables_paginate .paginate_button { padding: 6px 12px; border-radius: 6px; margin: 0 2px; }
.je-card .dataTables_paginate .paginate_button.current { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); color: #fff !important; border: none; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.je-stat-card, .je-card { animation: fadeInUp 0.4s ease forwards; }
</style>
@endsection

@section('content')
<section class="content je-page">
    <div class="je-header-banner">
        <div>
            <h1><i class="fas fa-book"></i> Journal Entries</h1>
            <p class="subtitle">Double-entry accounting transactions</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('bookkeeping.dashboard') }}" class="je-btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ route('bookkeeping.journal.create') }}" class="je-btn-new"><i class="fas fa-plus"></i> New Journal Entry</a>
        </div>
    </div>

    <div class="je-stats">
        <div class="je-stat-card draft">
            <div class="je-stat-icon"><i class="fas fa-edit"></i></div>
            <div class="je-stat-info">
                <div class="je-stat-value">{{ $stats['draft'] ?? 0 }}</div>
                <div class="je-stat-label">Draft</div>
            </div>
        </div>
        <div class="je-stat-card posted">
            <div class="je-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="je-stat-info">
                <div class="je-stat-value">{{ $stats['posted'] ?? 0 }}</div>
                <div class="je-stat-label">Posted</div>
            </div>
        </div>
        <div class="je-stat-card voided">
            <div class="je-stat-icon"><i class="fas fa-ban"></i></div>
            <div class="je-stat-info">
                <div class="je-stat-value">{{ $stats['voided'] ?? 0 }}</div>
                <div class="je-stat-label">Voided</div>
            </div>
        </div>
        <div class="je-stat-card total">
            <div class="je-stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="je-stat-info">
                <div class="je-stat-value">${{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                <div class="je-stat-label">Total Posted</div>
            </div>
        </div>
    </div>

    <div class="je-filters">
        <div class="je-filter-group">
            <span class="je-filter-label">Status</span>
            <select class="je-filter-input" id="filter_status">
                <option value="">All Statuses</option>
                @foreach($statuses as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="je-filter-group">
            <span class="je-filter-label">Entry Type</span>
            <select class="je-filter-input" id="filter_type">
                <option value="">All Types</option>
                @foreach($entryTypes as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="je-filter-group" style="flex: 1; min-width: 200px;">
            <span class="je-filter-label">Date Range</span>
            <input type="text" class="je-filter-input" id="date_range" placeholder="Select date range" readonly>
        </div>
        <div style="align-self: flex-end;">
            <button type="button" class="je-btn-back" id="clear_filters" style="background: #f3f4f6; color: #6b7280; border-color: #e5e7eb;">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>

    <div class="je-card">
        <div class="je-card-header">
            <h3 class="je-card-title"><i class="fas fa-list"></i> Journal Entry Records</h3>
        </div>
        <table class="je-table" id="journal_entries_table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Entry #</th>
                    <th>Type</th>
                    <th>Memo</th>
                    <th>Debit</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var entries_table = $('#journal_entries_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("bookkeeping.journal.index") }}',
            data: function(d) {
                d.status = $('#filter_status').val();
                d.entry_type = $('#filter_type').val();
                var dateRange = $('#date_range').val();
                if (dateRange) {
                    var dates = dateRange.split(' - ');
                    d.start_date = dates[0];
                    d.end_date = dates[1];
                }
            }
        },
        columns: [
            { data: 'entry_date', name: 'entry_date', render: function(data) {
                return data ? '<span style="color: #374151;">' + data + '</span>' : '-';
            }},
            { data: 'entry_number', name: 'entry_number', render: function(data, type, row) {
                return '<a href="' + (row.view_url || '#') + '" style="color: #7c3aed; font-weight: 600;">' + (data || '-') + '</a>';
            }},
            { data: 'entry_type', name: 'entry_type', render: function(data) {
                return '<span class="je-type">' + (data || '-') + '</span>';
            }},
            { data: 'memo', name: 'memo', render: function(data) {
                if (!data) return '-';
                return data.length > 40 ? data.substring(0, 40) + '...' : data;
            }},
            { data: 'total_debit', name: 'total_debit', render: function(data) {
                return '<span class="je-amount debit">$' + parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</span>';
            }},
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'status', name: 'status', render: function(data) {
                var statusClass = (data || 'draft').toLowerCase();
                var statusText = statusClass.charAt(0).toUpperCase() + statusClass.slice(1);
                return '<span class="je-status ' + statusClass + '">' + statusText + '</span>';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                var status = (row.status || '').toLowerCase();
                var html = '<div class="je-actions-dropdown">';
                html += '<button type="button" class="je-actions-btn"><i class="fas fa-cog"></i> Actions <i class="fas fa-chevron-down"></i></button>';
                html += '<div class="je-actions-menu">';
                html += '<a href="' + (row.view_url || '#') + '" class="action-view"><i class="fas fa-eye"></i> View Details</a>';
                if (status === 'draft') {
                    html += '<button type="button" class="action-post post-entry" data-href="' + (row.post_url || '') + '"><i class="fas fa-check-circle"></i> Post Entry</button>';
                }
                if (status === 'posted') {
                    html += '<div class="divider"></div>';
                    html += '<button type="button" class="action-void void-entry" data-href="' + (row.void_url || '') + '"><i class="fas fa-ban"></i> Void Entry</button>';
                }
                html += '</div></div>';
                return html;
            }}
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: '<div style="text-align: center; padding: 40px; color: #9ca3af;"><i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;"></i><br>No journal entries found</div>'
        }
    });

    $('#filter_status, #filter_type').change(function() {
        entries_table.ajax.reload();
    });

    $('#date_range').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' }
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        entries_table.ajax.reload();
    });

    $('#clear_filters').click(function() {
        $('#filter_status, #filter_type').val('').trigger('change');
        $('#date_range').val('');
        entries_table.ajax.reload();
    });

    // Toggle dropdown
    $(document).on('click', '.je-actions-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var dropdown = $(this).closest('.je-actions-dropdown');
        $('.je-actions-dropdown').not(dropdown).removeClass('open');
        dropdown.toggleClass('open');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.je-actions-dropdown').length) {
            $('.je-actions-dropdown').removeClass('open');
        }
    });

    // Post entry - using body delegate for dynamically created elements
    $('body').on('click', '.post-entry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var url = $(this).attr('data-href');
        if (!url) {
            toastr.error('Invalid action URL');
            return;
        }
        
        $('.je-actions-dropdown').removeClass('open');
        
        Swal.fire({
            title: 'Do you want to proceed?',
            text: 'This will post the journal entry and update account balances.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.msg || 'Journal entry posted successfully!',
                                confirmButtonColor: '#10b981'
                            });
                            entries_table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'Failed to post entry',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.msg || 'An error occurred',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    });

    // Void entry - using body delegate for dynamically created elements
    $('body').on('click', '.void-entry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var url = $(this).attr('data-href');
        if (!url) {
            toastr.error('Invalid action URL');
            return;
        }
        
        $('.je-actions-dropdown').removeClass('open');
        
        Swal.fire({
            title: 'Do you want to proceed?',
            text: 'This will void the journal entry and reverse account balances.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.msg || 'Journal entry voided successfully!',
                                confirmButtonColor: '#10b981'
                            });
                            entries_table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'Failed to void entry',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.msg || 'An error occurred',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
