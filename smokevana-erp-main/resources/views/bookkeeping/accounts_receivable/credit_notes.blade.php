@extends('layouts.app')
@section('title', 'Credit Notes')

@section('css')
<style>
/* Credit Notes List - Professional UI */
.cn-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding: 20px;
    padding-bottom: 40px;
}

/* Header Banner */
.cn-header-banner {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.25);
    position: relative;
    overflow: hidden;
    animation: slideDown 0.5s ease-out;
}

.cn-header-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.cn-header-banner h1,
.cn-header-banner .subtitle,
.cn-header-banner i { color: #fff !important; }

.cn-header-banner h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.cn-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.cn-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.cn-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.cn-btn-light {
    background: rgba(255,255,255,0.95);
    color: #dc2626;
}

.cn-btn-light:hover {
    background: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    color: #dc2626;
    text-decoration: none;
}

.cn-btn-outline {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.3);
}

.cn-btn-outline:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    text-decoration: none;
}

/* Summary Stats */
.cn-summary-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 1200px) {
    .cn-summary-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .cn-summary-stats { grid-template-columns: 1fr; }
}

.cn-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(220, 38, 38, 0.08);
    border: 1px solid rgba(220, 38, 38, 0.06);
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease-out 0.1s both;
}

.cn-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.12);
}

.cn-stat-card.issued { border-left: 4px solid #dc2626; }
.cn-stat-card.applied { border-left: 4px solid #10b981; }
.cn-stat-card.available { border-left: 4px solid #3b82f6; }
.cn-stat-card.pending { border-left: 4px solid #f59e0b; }

.cn-stat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.cn-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.cn-stat-card.issued .cn-stat-icon { background: #fee2e2; color: #dc2626; }
.cn-stat-card.applied .cn-stat-icon { background: #d1fae5; color: #059669; }
.cn-stat-card.available .cn-stat-icon { background: #dbeafe; color: #2563eb; }
.cn-stat-card.pending .cn-stat-icon { background: #fef3c7; color: #d97706; }

.cn-stat-label {
    font-size: 13px;
    color: #6b7280;
    font-weight: 600;
}

.cn-stat-value {
    font-size: 28px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-stat-card.issued .cn-stat-value { color: #dc2626; }
.cn-stat-card.applied .cn-stat-value { color: #059669; }
.cn-stat-card.available .cn-stat-value { color: #2563eb; }
.cn-stat-card.pending .cn-stat-value { color: #d97706; }

/* Card Styling */
.cn-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
    border: 1px solid rgba(220, 38, 38, 0.08);
    animation: slideUp 0.5s ease-out 0.2s both;
}

.cn-card-header {
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cn-card-header h3 {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cn-card-header h3 i { color: #f87171; }

.cn-card-badge {
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.cn-card-body { padding: 0; }

/* Table Styling */
.cn-table {
    width: 100%;
    border-collapse: collapse;
}

.cn-table th {
    background: #f8f9fe;
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #f1f5f9;
}

.cn-table td {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #374151;
}

.cn-table tbody tr {
    transition: all 0.2s ease;
}

.cn-table tbody tr:hover {
    background: #fef2f2;
}

.cn-credit-number {
    font-weight: 700;
    color: #dc2626;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-customer-name {
    font-weight: 600;
    color: #1e1b4b;
}

.cn-amount {
    font-family: 'SF Mono', Monaco, monospace;
    font-weight: 600;
}

.cn-status {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cn-status.draft { background: #f3f4f6; color: #6b7280; }
.cn-status.approved { background: #dbeafe; color: #2563eb; }
.cn-status.applied { background: #d1fae5; color: #059669; }
.cn-status.partially_applied { background: #fef3c7; color: #d97706; }
.cn-status.voided { background: #fee2e2; color: #dc2626; }
.cn-status.cancelled { background: #f3f4f6; color: #9ca3af; }

.cn-action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    margin-right: 4px;
}

.cn-action-btn.view { background: #ede9fe; color: #7c3aed; }
.cn-action-btn.view:hover { background: #7c3aed; color: #fff; transform: scale(1.1); }

/* Empty State */
.cn-empty {
    text-align: center;
    padding: 60px 20px;
}

.cn-empty i {
    font-size: 64px;
    color: #fca5a5;
    margin-bottom: 20px;
}

.cn-empty h4 {
    font-size: 18px;
    color: #1e1b4b;
    margin-bottom: 8px;
}

.cn-empty p {
    color: #6b7280;
    margin-bottom: 24px;
}

.cn-empty-btn {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #fff;
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.cn-empty-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
    color: #fff;
    text-decoration: none;
}

/* Animations */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection

@section('content')
<section class="content cn-page">
    <!-- Header Banner -->
    <div class="cn-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice-dollar"></i> Credit Notes</h1>
            <p class="subtitle">Manage customer credit notes and adjustments</p>
        </div>
        <div class="cn-header-actions">
            <a href="{{ route('bookkeeping.accounts-receivable.index') }}" class="cn-btn cn-btn-outline">
                <i class="fas fa-arrow-left"></i> Back to AR
            </a>
            <a href="{{ route('bookkeeping.credit-notes.create') }}" class="cn-btn cn-btn-light">
                <i class="fas fa-plus"></i> New Credit Note
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="cn-summary-stats">
        <div class="cn-stat-card issued">
            <div class="cn-stat-header">
                <div class="cn-stat-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="cn-stat-label">Total Issued</div>
            </div>
            <div class="cn-stat-value">${{ number_format($summary['total_issued'] ?? 0, 2) }}</div>
        </div>
        <div class="cn-stat-card applied">
            <div class="cn-stat-header">
                <div class="cn-stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="cn-stat-label">Total Applied</div>
            </div>
            <div class="cn-stat-value">${{ number_format($summary['total_applied'] ?? 0, 2) }}</div>
        </div>
        <div class="cn-stat-card available">
            <div class="cn-stat-header">
                <div class="cn-stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="cn-stat-label">Available Balance</div>
            </div>
            <div class="cn-stat-value">${{ number_format($summary['total_available'] ?? 0, 2) }}</div>
        </div>
        <div class="cn-stat-card pending">
            <div class="cn-stat-header">
                <div class="cn-stat-icon"><i class="fas fa-clock"></i></div>
                <div class="cn-stat-label">Pending Approval</div>
            </div>
            <div class="cn-stat-value">{{ $summary['pending_approval'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Credit Notes Table -->
    <div class="cn-card">
        <div class="cn-card-header">
            <h3><i class="fas fa-list"></i> All Credit Notes</h3>
            <span class="cn-card-badge">{{ $creditNotes->count() }} Records</span>
        </div>
        <div class="cn-card-body">
            @if($creditNotes->count() > 0)
            <table class="cn-table" id="creditNotesTable">
                <thead>
                    <tr>
                        <th>Credit Note #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Reason</th>
                        <th style="text-align: right;">Amount</th>
                        <th style="text-align: right;">Applied</th>
                        <th style="text-align: right;">Balance</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($creditNotes as $cn)
                    <tr>
                        <td><span class="cn-credit-number">{{ $cn->credit_note_number }}</span></td>
                        <td>{{ $cn->credit_date->format('M d, Y') }}</td>
                        <td><span class="cn-customer-name">{{ $cn->getCustomerDisplayName() }}</span></td>
                        <td>{{ $cn->getReasonCategoryLabel() }}</td>
                        <td style="text-align: right;"><span class="cn-amount" style="color: #dc2626;">${{ number_format($cn->amount, 2) }}</span></td>
                        <td style="text-align: right;"><span class="cn-amount" style="color: #059669;">${{ number_format($cn->amount_applied, 2) }}</span></td>
                        <td style="text-align: right;"><span class="cn-amount" style="color: #2563eb;">${{ number_format($cn->balance, 2) }}</span></td>
                        <td style="text-align: center;"><span class="cn-status {{ $cn->status }}">{{ $cn->getStatusLabel() }}</span></td>
                        <td style="text-align: center;">
                            <a href="{{ route('bookkeeping.credit-notes.show', $cn->id) }}" class="cn-action-btn view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="cn-empty">
                <i class="fas fa-file-invoice-dollar"></i>
                <h4>No Credit Notes Yet</h4>
                <p>Create your first credit note to manage customer credits and balance adjustments.</p>
                <a href="{{ route('bookkeeping.credit-notes.create') }}" class="cn-empty-btn">
                    <i class="fas fa-plus"></i> Create Credit Note
                </a>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    if ($('#creditNotesTable').length && $.fn.DataTable) {
        $('#creditNotesTable').DataTable({
            pageLength: 25,
            order: [[1, 'desc']],
            columnDefs: [{ orderable: false, targets: [8] }],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search credit notes...",
            }
        });
    }
});
</script>
@endsection
