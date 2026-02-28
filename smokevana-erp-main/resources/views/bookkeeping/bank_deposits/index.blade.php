@extends('layouts.app')
@section('title', 'Bank Deposits')

@section('css')
<style>
/* Bank Deposits - Professional Purple Theme */
.bd-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.bd-header-banner {
    background: #37475a;
    border-radius: 6px; padding: 22px 28px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}
.bd-header-banner h1, .bd-header-banner .subtitle, .bd-header-banner i { color: #fff !important; }
.bd-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.bd-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.bd-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.bd-btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }
.bd-btn-new { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 1px solid #C7511F !important; color: #fff !important; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.bd-btn-new:hover { color: #fff !important; opacity: 0.95; text-decoration: none; transform: translateY(-2px); }

.bd-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
@media (max-width: 992px) { .bd-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .bd-stats { grid-template-columns: 1fr; } }

.bd-stat-card { background: #fff; border-radius: 14px; padding: 20px 24px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.06); border-left: 4px solid #e5e7eb; display: flex; align-items: center; gap: 16px; transition: all 0.3s ease; }
.bd-stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12); }
.bd-stat-card.pending { border-left-color: #f59e0b; }
.bd-stat-card.deposited { border-left-color: #10b981; }
.bd-stat-card.reconciled { border-left-color: #3b82f6; }
.bd-stat-card.total { border-left-color: #8b5cf6; }

.bd-stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.bd-stat-card.pending .bd-stat-icon { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706; }
.bd-stat-card.deposited .bd-stat-icon { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.bd-stat-card.reconciled .bd-stat-icon { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
.bd-stat-card.total .bd-stat-icon { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }

.bd-stat-info { flex: 1; }
.bd-stat-value { font-size: 28px; font-weight: 700; margin-bottom: 4px; font-family: 'SF Mono', Monaco, monospace; }
.bd-stat-card.pending .bd-stat-value { color: #d97706; }
.bd-stat-card.deposited .bd-stat-value { color: #059669; }
.bd-stat-card.reconciled .bd-stat-value { color: #2563eb; }
.bd-stat-card.total .bd-stat-value { color: #7c3aed; }
.bd-stat-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }

.bd-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.06); overflow: hidden; }
.bd-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
.bd-card-title { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; display: flex; align-items: center; gap: 10px; }
.bd-card-title i { color: #8b5cf6; }

.bd-table { width: 100%; border-collapse: collapse; }
.bd-table thead th { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; }
.bd-table tbody td { padding: 14px 16px; border-bottom: 1px solid #f5f5f5; font-size: 14px; color: #374151; }
.bd-table tbody tr:hover { background: #faf5ff; }

.bd-status { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.bd-status.pending { background: #fef3c7; color: #d97706; }
.bd-status.deposited { background: #d1fae5; color: #059669; }
.bd-status.reconciled { background: #dbeafe; color: #2563eb; }
.bd-status.voided { background: #fee2e2; color: #dc2626; }

/* Action Buttons - Horizontal Alignment */
.bd-table tbody td:last-child { white-space: nowrap; text-align: center; }
.bd-action-btn { 
    width: 32px; height: 32px; border-radius: 8px; border: none; 
    display: inline-flex; align-items: center; justify-content: center; 
    cursor: pointer; transition: all 0.2s ease; font-size: 13px; 
    text-decoration: none; margin: 0 2px; vertical-align: middle;
}
.bd-action-btn.view { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); color: #7c3aed; }
.bd-action-btn.view:hover { background: #7c3aed; color: #fff; text-decoration: none; }
.bd-action-btn.process { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.bd-action-btn.process:hover { background: #059669; color: #fff; text-decoration: none; }
.bd-action-btn.void { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.bd-action-btn.void:hover { background: #dc2626; color: #fff; text-decoration: none; }
.bd-action-btn:disabled { opacity: 0.6; cursor: not-allowed; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.bd-stat-card, .bd-card { animation: fadeInUp 0.4s ease forwards; }
</style>
@endsection

@section('content')
<section class="content bd-page">
    <div class="bd-header-banner">
        <div>
            <h1><i class="fas fa-piggy-bank"></i> Bank Deposits</h1>
            <p class="subtitle">Manage your bank deposits and reconciliation</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('bookkeeping.dashboard') }}" class="bd-btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ route('bookkeeping.deposits.create') }}" class="bd-btn-new"><i class="fas fa-plus"></i> New Deposit</a>
        </div>
    </div>

    <div class="bd-stats">
        <div class="bd-stat-card pending">
            <div class="bd-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="bd-stat-info">
                <div class="bd-stat-value">{{ $stats['pending'] ?? 0 }}</div>
                <div class="bd-stat-label">Pending</div>
            </div>
        </div>
        <div class="bd-stat-card deposited">
            <div class="bd-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="bd-stat-info">
                <div class="bd-stat-value">{{ $stats['deposited'] ?? 0 }}</div>
                <div class="bd-stat-label">Deposited</div>
            </div>
        </div>
        <div class="bd-stat-card reconciled">
            <div class="bd-stat-icon"><i class="fas fa-check-double"></i></div>
            <div class="bd-stat-info">
                <div class="bd-stat-value">{{ $stats['reconciled'] ?? 0 }}</div>
                <div class="bd-stat-label">Reconciled</div>
            </div>
        </div>
        <div class="bd-stat-card total">
            <div class="bd-stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="bd-stat-info">
                <div class="bd-stat-value">${{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                <div class="bd-stat-label">Total This Month</div>
            </div>
        </div>
    </div>

    <div class="bd-card">
        <div class="bd-card-header">
            <h3 class="bd-card-title"><i class="fas fa-list"></i> Deposit Records</h3>
        </div>
        <table class="bd-table" id="deposits_table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Deposit #</th>
                    <th>Deposit To</th>
                    <th>Memo</th>
                    <th>Amount</th>
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
    var depositsTable = $('#deposits_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("bookkeeping.deposits.index") }}',
        columns: [
            {data: 'deposit_date', name: 'deposit_date'},
            {data: 'deposit_number', name: 'deposit_number', render: function(data, type, row) {
                return '<a href="' + row.view_url + '" style="color: #7c3aed; font-weight: 600;">' + (data || '-') + '</a>';
            }},
            {data: 'account_name', name: 'account_name', render: function(data) {
                return '<i class="fas fa-university" style="color: #8b5cf6; margin-right: 6px;"></i>' + (data || '-');
            }},
            {data: 'memo', name: 'memo', render: function(data) {
                if (!data) return '-';
                return data.length > 30 ? data.substring(0, 30) + '...' : data;
            }},
            {data: 'total_amount', name: 'total_amount', render: function(data) {
                return '<span style="color: #059669; font-weight: 700; font-family: monospace;">$' + parseFloat(data || 0).toFixed(2) + '</span>';
            }},
            {data: 'status', name: 'status', render: function(data) {
                var colors = {pending: '#f59e0b', deposited: '#10b981', reconciled: '#3b82f6', voided: '#ef4444'};
                var bg = {pending: '#fef3c7', deposited: '#d1fae5', reconciled: '#dbeafe', voided: '#fee2e2'};
                return '<span style="background: ' + (bg[data] || '#f3f4f6') + '; color: ' + (colors[data] || '#6b7280') + '; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase;">' + (data || '-') + '</span>';
            }},
            {data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                var html = '<div style="display: inline-flex; align-items: center; gap: 4px; justify-content: center;">';
                html += '<a href="' + row.view_url + '" class="bd-action-btn view" title="View"><i class="fas fa-eye"></i></a>';
                if (row.status === 'pending') {
                    html += '<button type="button" class="bd-action-btn process" title="Process/Approve" data-id="' + row.id + '"><i class="fas fa-check"></i></button>';
                    html += '<button type="button" class="bd-action-btn void" title="Void/Reject" data-id="' + row.id + '"><i class="fas fa-times"></i></button>';
                }
                html += '</div>';
                return html;
            }}
        ],
        order: [[0, 'desc']],
        language: {
            emptyTable: '<div style="text-align: center; padding: 40px; color: #9ca3af;"><i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;"></i><br>No deposits found</div>'
        }
    });

    // Handle Process (Approve) button click
    $(document).on('click', '.bd-action-btn.process', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var depositId = $(this).data('id');
        var btn = $(this);
        
        if (!depositId) {
            toastr.error('Deposit ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Process Deposit?',
            text: 'This will mark the deposit as processed/deposited.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check"></i> Yes, Process It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ url("bookkeeping/bank-deposits") }}/' + depositId + '/process',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Deposit processed successfully!');
                            depositsTable.ajax.reload(null, false);
                        } else {
                            toastr.error(response.msg || 'Failed to process deposit.');
                            btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Process error:', xhr.responseText);
                        var msg = 'An error occurred while processing the deposit.';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            msg = xhr.responseJSON.msg;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                        btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                    }
                });
            }
        });
    });

    // Handle Void (Reject) button click
    $(document).on('click', '.bd-action-btn.void', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var depositId = $(this).data('id');
        var btn = $(this);
        
        if (!depositId) {
            toastr.error('Deposit ID not found');
            return;
        }
        
        Swal.fire({
            title: 'Void/Reject Deposit?',
            text: 'This will mark the deposit as voided. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-times"></i> Yes, Void It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ url("bookkeeping/bank-deposits") }}/' + depositId + '/void',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Deposit voided successfully!');
                            depositsTable.ajax.reload(null, false);
                        } else {
                            toastr.error(response.msg || 'Failed to void deposit.');
                            btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Void error:', xhr.responseText);
                        var msg = 'An error occurred while voiding the deposit.';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            msg = xhr.responseJSON.msg;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                        btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                    }
                });
            }
        });
    });
});
</script>
@endsection
