@extends('layouts.app')
@section('title', 'Journal Entry - ' . $entry->entry_number)

@section('css')
<style>
.je-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.je-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
    position: relative; overflow: hidden;
}
.je-header-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
.je-header-banner h1, .je-header-banner .subtitle, .je-header-banner i { color: #fff !important; }
.je-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.je-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.je-entry-badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 8px;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 14px;
    color: #fff;
}
.je-header-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.je-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.je-btn-back:hover { background: rgba(255,255,255,0.25); text-decoration: none; color: #fff; }
.je-btn-edit { background: #fff; color: #7c3aed; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.je-btn-edit:hover { background: #f5f3ff; text-decoration: none; color: #6d28d9; transform: translateY(-2px); }

.je-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
@media (max-width: 1200px) { .je-grid { grid-template-columns: 1fr; } }

.je-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); margin-bottom: 24px; overflow: hidden; border: 1px solid rgba(139, 92, 246, 0.06); }
.je-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
.je-card-header-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 18px; }
.je-card-header h3 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; }
.je-card-body { padding: 24px; }

/* Detail Grid */
.je-details-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
@media (max-width: 768px) { .je-details-grid { grid-template-columns: 1fr; } }
.je-detail-item { }
.je-detail-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; margin-bottom: 6px; }
.je-detail-value { font-size: 15px; color: #1e1b4b; font-weight: 500; }
.je-detail-value.mono { font-family: 'SF Mono', Monaco, monospace; }
.je-detail-value a { color: #7c3aed; text-decoration: none; }
.je-detail-value a:hover { text-decoration: underline; }

/* Status Badges */
.je-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
.je-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.je-status.draft { background: #fef3c7; color: #d97706; }
.je-status.posted { background: #d1fae5; color: #059669; }
.je-status.voided { background: #fee2e2; color: #dc2626; }

.je-type-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; background: #f1f5f9; color: #64748b; }

/* Lines Table */
.je-lines-table { width: 100%; border-collapse: collapse; }
.je-lines-table thead th { 
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); 
    color: #fff; 
    font-size: 11px; 
    font-weight: 600; 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
    padding: 14px 16px; 
    text-align: left; 
}
.je-lines-table thead th:first-child { border-radius: 0; }
.je-lines-table thead th:last-child { border-radius: 0; }
.je-lines-table tbody td { 
    padding: 16px; 
    border-bottom: 1px solid #f5f5f5; 
    vertical-align: middle;
    font-size: 14px;
    color: #374151;
}
.je-lines-table tbody tr:last-child td { border-bottom: none; }
.je-lines-table tbody tr:hover td { background: #faf5ff; }
.je-lines-table tfoot td { 
    padding: 14px 16px; 
    font-weight: 700; 
    background: linear-gradient(135deg, #f8f9fe 0%, #f1f5f9 100%);
    border-top: 2px solid #e5e7eb;
}

.je-account-cell { font-weight: 600; color: #1e1b4b; }
.je-account-code { font-size: 11px; color: #6b7280; font-family: 'SF Mono', Monaco, monospace; display: block; margin-top: 2px; }
.je-amount-cell { font-family: 'SF Mono', Monaco, monospace; font-weight: 600; text-align: right; }
.je-amount-cell.debit { color: #059669; }
.je-amount-cell.credit { color: #dc2626; }
.je-memo-cell { color: #6b7280; font-size: 13px; }
.je-memo-cell:empty::after { content: '—'; color: #d1d5db; }

/* Summary Card */
.je-summary { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); position: sticky; top: 24px; border: 1px solid rgba(139, 92, 246, 0.06); }
.je-summary-header { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); padding: 20px 24px; border-radius: 16px 16px 0 0; }
.je-summary-header h3 { font-size: 18px; font-weight: 600; color: #fff !important; margin: 0; display: flex; align-items: center; gap: 10px; }
.je-summary-body { padding: 24px; }

.je-balance-status { text-align: center; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
.je-balance-status.balanced { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
.je-balance-status.unbalanced { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
.je-balance-icon { font-size: 32px; margin-bottom: 8px; }
.je-balance-status.balanced .je-balance-icon { color: #059669; }
.je-balance-status.unbalanced .je-balance-icon { color: #dc2626; }
.je-balance-text { font-weight: 600; }
.je-balance-status.balanced .je-balance-text { color: #059669; }
.je-balance-status.unbalanced .je-balance-text { color: #dc2626; }

.je-total-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.je-total-row:last-child { border-bottom: none; }
.je-total-label { font-size: 14px; color: #6b7280; }
.je-total-value { font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.je-total-value.debit { color: #059669; }
.je-total-value.credit { color: #dc2626; }

.je-status-card { margin-top: 20px; padding: 16px; border-radius: 12px; background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); }
.je-status-card-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 12px; }
.je-status-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 13px; }
.je-status-item i { width: 18px; color: #8b5cf6; }
.je-status-item span { color: #6b7280; }
.je-status-item strong { color: #1e1b4b; }

/* Action Buttons */
.je-actions { padding: 20px 24px; border-top: 1px solid #f1f5f9; }
.je-btn-action { width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px; text-decoration: none; transition: all 0.2s ease; border: none; }
.je-btn-action:last-child { margin-bottom: 0; }
.je-btn-action.primary { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); color: #fff; border: none; }
.je-btn-action.primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3); color: #fff; text-decoration: none; }
.je-btn-action.secondary { background: #fff; color: #374151; border: 2px solid #e5e7eb; }
.je-btn-action.secondary:hover { background: #f9fafb; color: #374151; text-decoration: none; }
.je-btn-action.success { background: #10b981; color: #fff; border: none; }
.je-btn-action.success:hover { background: #059669; color: #fff; text-decoration: none; }
.je-btn-action.danger { background: #fff; color: #dc2626; border: 2px solid #fecaca; }
.je-btn-action.danger:hover { background: #fef2f2; color: #dc2626; text-decoration: none; }
.je-btn-action:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

/* Memo Section */
.je-memo-section { background: #f8f9fe; border-radius: 10px; padding: 16px; margin-top: 20px; }
.je-memo-section-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.je-memo-section-text { font-size: 14px; color: #6b7280; line-height: 1.6; }
.je-memo-section-text:empty::after { content: 'No memo provided'; font-style: italic; color: #9ca3af; }

/* Animation */
@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.je-card, .je-summary { animation: fadeInUp 0.4s ease forwards; }
</style>
@endsection

@section('content')
<section class="content je-page">
    <div class="je-header-banner">
        <div>
            <h1>
                <i class="fas fa-file-invoice"></i> 
                Journal Entry
                <span class="je-entry-badge">{{ $entry->entry_number }}</span>
            </h1>
            <p class="subtitle">View journal entry details and transactions</p>
        </div>
        <div class="je-header-actions">
            <a href="{{ route('bookkeeping.journal.index') }}" class="je-btn-back"><i class="fas fa-arrow-left"></i> Back to List</a>
            @if($entry->status === 'draft')
            <a href="{{ route('bookkeeping.journal.edit', $entry->id) }}" class="je-btn-edit"><i class="fas fa-edit"></i> Edit Entry</a>
            @endif
        </div>
    </div>

    <div class="je-grid">
        <div class="je-main">
            <!-- Entry Details -->
            <div class="je-card">
                <div class="je-card-header">
                    <div class="je-card-header-icon"><i class="fas fa-info-circle"></i></div>
                    <h3>Entry Details</h3>
                </div>
                <div class="je-card-body">
                    <div class="je-details-grid">
                        <div class="je-detail-item">
                            <div class="je-detail-label">Entry Number</div>
                            <div class="je-detail-value mono">{{ $entry->entry_number }}</div>
                        </div>
                        <div class="je-detail-item">
                            <div class="je-detail-label">Status</div>
                            <div class="je-detail-value">
                                <span class="je-status {{ $entry->status }}">{{ ucfirst($entry->status) }}</span>
                            </div>
                        </div>
                        <div class="je-detail-item">
                            <div class="je-detail-label">Entry Date</div>
                            <div class="je-detail-value">{{ $entry->entry_date->format('F d, Y') }}</div>
                        </div>
                        <div class="je-detail-item">
                            <div class="je-detail-label">Entry Type</div>
                            <div class="je-detail-value">
                                <span class="je-type-badge">{{ str_replace('_', ' ', ucfirst($entry->entry_type ?? 'standard')) }}</span>
                            </div>
                        </div>
                        @if($entry->source_document)
                        <div class="je-detail-item">
                            <div class="je-detail-label">Reference #</div>
                            <div class="je-detail-value mono">{{ $entry->source_document }}</div>
                        </div>
                        @endif
                        @if($entry->contact)
                        <div class="je-detail-item">
                            <div class="je-detail-label">Contact</div>
                            <div class="je-detail-value">{{ $entry->contact->name ?? '—' }}</div>
                        </div>
                        @endif
                    </div>
                    
                    @if($entry->memo)
                    <div class="je-memo-section">
                        <div class="je-memo-section-title"><i class="fas fa-sticky-note"></i> Memo</div>
                        <div class="je-memo-section-text">{{ $entry->memo }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Entry Lines -->
            <div class="je-card">
                <div class="je-card-header">
                    <div class="je-card-header-icon"><i class="fas fa-list"></i></div>
                    <h3>Transaction Lines</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="je-lines-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th style="width: 150px; text-align: right;">Debit</th>
                                <th style="width: 150px; text-align: right;">Credit</th>
                                <th>Memo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $totalDebits = 0; 
                                $totalCredits = 0; 
                            @endphp
                            @forelse($entry->lines as $line)
                            @php
                                if($line->type === 'debit') {
                                    $totalDebits += $line->amount;
                                } else {
                                    $totalCredits += $line->amount;
                                }
                            @endphp
                            <tr>
                                <td>
                                    <span class="je-account-cell">{{ $line->account->name ?? 'Unknown Account' }}</span>
                                    @if($line->account && $line->account->account_code)
                                    <span class="je-account-code">{{ $line->account->account_code }}</span>
                                    @endif
                                </td>
                                <td class="je-amount-cell debit">
                                    {{ $line->type === 'debit' ? '$' . number_format($line->amount, 2) : '' }}
                                </td>
                                <td class="je-amount-cell credit">
                                    {{ $line->type === 'credit' ? '$' . number_format($line->amount, 2) : '' }}
                                </td>
                                <td class="je-memo-cell">{{ $line->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #6b7280;">
                                    <i class="fas fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                    No transaction lines found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: right; font-weight: 600; color: #374151;">Totals</td>
                                <td class="je-amount-cell debit" style="font-size: 16px;">${{ number_format($totalDebits, 2) }}</td>
                                <td class="je-amount-cell credit" style="font-size: 16px;">${{ number_format($totalCredits, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="je-sidebar">
            <div class="je-summary">
                <div class="je-summary-header">
                    <h3><i class="fas fa-balance-scale"></i> Entry Summary</h3>
                </div>
                <div class="je-summary-body">
                    @php
                        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;
                    @endphp
                    <div class="je-balance-status {{ $isBalanced ? 'balanced' : 'unbalanced' }}">
                        <div class="je-balance-icon">
                            <i class="fas fa-{{ $isBalanced ? 'check-circle' : 'exclamation-circle' }}"></i>
                        </div>
                        <div class="je-balance-text">{{ $isBalanced ? 'Entry Balanced' : 'Entry Not Balanced' }}</div>
                    </div>
                    
                    <div class="je-total-row">
                        <span class="je-total-label">Total Debits</span>
                        <span class="je-total-value debit">${{ number_format($totalDebits, 2) }}</span>
                    </div>
                    <div class="je-total-row">
                        <span class="je-total-label">Total Credits</span>
                        <span class="je-total-value credit">${{ number_format($totalCredits, 2) }}</span>
                    </div>
                    @if(!$isBalanced)
                    <div class="je-total-row">
                        <span class="je-total-label">Difference</span>
                        <span class="je-total-value" style="color: #dc2626;">${{ number_format(abs($totalDebits - $totalCredits), 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="je-status-card">
                        <div class="je-status-card-title">Entry Information</div>
                        <div class="je-status-item">
                            <i class="fas fa-user"></i>
                            <span>Created by:</span>
                            <strong>{{ $entry->createdBy->first_name ?? 'System' }} {{ $entry->createdBy->last_name ?? '' }}</strong>
                        </div>
                        <div class="je-status-item">
                            <i class="fas fa-calendar"></i>
                            <span>Created:</span>
                            <strong>{{ $entry->created_at->format('M d, Y H:i') }}</strong>
                        </div>
                        @if($entry->status === 'posted' && $entry->posted_at)
                        <div class="je-status-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Posted:</span>
                            <strong>{{ \Carbon\Carbon::parse($entry->posted_at)->format('M d, Y H:i') }}</strong>
                        </div>
                        @if($entry->postedBy)
                        <div class="je-status-item">
                            <i class="fas fa-user-check"></i>
                            <span>Posted by:</span>
                            <strong>{{ $entry->postedBy->first_name ?? '' }} {{ $entry->postedBy->last_name ?? '' }}</strong>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                
                <div class="je-actions">
                    @if($entry->status === 'draft')
                    <a href="{{ route('bookkeeping.journal.edit', $entry->id) }}" class="je-btn-action primary">
                        <i class="fas fa-edit"></i> Edit Entry
                    </a>
                    <button type="button" class="je-btn-action success btn-post" data-id="{{ $entry->id }}" {{ !$isBalanced ? 'disabled' : '' }}>
                        <i class="fas fa-check"></i> Post Entry
                    </button>
                    @elseif($entry->status === 'posted')
                    <button type="button" class="je-btn-action secondary btn-duplicate" data-id="{{ $entry->id }}">
                        <i class="fas fa-copy"></i> Duplicate Entry
                    </button>
                    <button type="button" class="je-btn-action danger btn-void" data-id="{{ $entry->id }}">
                        <i class="fas fa-ban"></i> Void Entry
                    </button>
                    @endif
                    <a href="{{ route('bookkeeping.journal.index') }}" class="je-btn-action secondary">
                        <i class="fas fa-arrow-left"></i> Back to Journal Entries
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Post Entry
    $('.btn-post').on('click', function() {
        var entryId = $(this).data('id');
        var $btn = $(this);
        
        swal({
            title: 'Post Entry?',
            text: 'Are you sure you want to post this entry? This action cannot be undone.',
            icon: 'info',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: 'Yes, post it!',
                    value: true,
                    visible: true,
                    closeModal: true,
                }
            }
        }).then((willPost) => {
            if (willPost) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Posting...');
                
                $.ajax({
                    url: '{{ url("bookkeeping/journal-entries") }}/' + entryId + '/post',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Entry posted successfully!');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.msg || 'Failed to post entry');
                            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Post Entry');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while posting the entry.');
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Post Entry');
                    }
                });
            }
        });
    });

    // Duplicate Entry
    $('.btn-duplicate').on('click', function() {
        var entryId = $(this).data('id');
        var $btn = $(this);
        
        swal({
            title: 'Duplicate Entry?',
            text: 'This will create a copy of this journal entry as a new draft.',
            icon: 'info',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: 'Yes, duplicate it!',
                    value: true,
                    visible: true,
                    closeModal: true,
                }
            }
        }).then((willDuplicate) => {
            if (willDuplicate) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Duplicating...');
                
                $.ajax({
                    url: '{{ url("bookkeeping/journal-entries") }}/' + entryId + '/duplicate',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Entry duplicated successfully!');
                            setTimeout(function() {
                                window.location.href = response.redirect || '{{ route("bookkeeping.journal.index") }}';
                            }, 1000);
                        } else {
                            toastr.error(response.msg || 'Failed to duplicate entry');
                            $btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Duplicate Entry');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while duplicating the entry.');
                        $btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Duplicate Entry');
                    }
                });
            }
        });
    });

    // Void Entry
    $('.btn-void').on('click', function() {
        var entryId = $(this).data('id');
        var $btn = $(this);
        
        swal({
            title: 'Void Entry?',
            text: 'Are you sure you want to void this entry? This will reverse all ledger impacts.',
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: 'Yes, void it!',
                    value: true,
                    visible: true,
                    closeModal: true,
                }
            },
            dangerMode: true,
        }).then((willVoid) => {
            if (willVoid) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Voiding...');
                
                $.ajax({
                    url: '{{ url("bookkeeping/journal-entries") }}/' + entryId + '/void',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Entry voided successfully!');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.msg || 'Failed to void entry');
                            $btn.prop('disabled', false).html('<i class="fas fa-ban"></i> Void Entry');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while voiding the entry.');
                        $btn.prop('disabled', false).html('<i class="fas fa-ban"></i> Void Entry');
                    }
                });
            }
        });
    });
});
</script>
@endsection
