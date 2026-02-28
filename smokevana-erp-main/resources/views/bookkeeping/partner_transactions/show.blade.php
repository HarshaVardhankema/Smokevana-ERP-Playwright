@extends('layouts.app')
@section('title', 'Partner Transaction Details')

@section('css')
<style>
    /* ===== Page Container ===== */
    .pt-show-wrapper {
        padding: 0;
        background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
        min-height: calc(100vh - 60px);
    }

    /* ===== Header Banner ===== */
    .pt-header-banner {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        padding: 32px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .pt-header-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title-area h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .header-title-area h1 i {
        font-size: 32px;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 15px;
        opacity: 0.85;
        font-weight: 400;
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
        transform: translateY(-2px);
    }

    .btn-header-primary {
        background: #fff;
        color: #7c3aed;
    }

    .btn-header-primary:hover {
        background: #f0e7ff;
        color: #6d28d9;
        transform: translateY(-2px);
    }

    /* ===== Content Area ===== */
    .content-area {
        padding: 30px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* ===== Summary Cards ===== */
    .summary-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(139, 92, 246, 0.1);
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 12px;
    }

    .summary-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .summary-card-icon.purple {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
    }

    .summary-card-icon.green {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
    }

    .summary-card-icon.blue {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #2563eb;
    }

    .summary-card-icon.amber {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
    }

    .summary-card-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    .summary-card-value {
        font-size: 22px;
        font-weight: 700;
        color: #1e1b4b;
        margin-top: 4px;
    }

    .summary-card-value.positive {
        color: #059669;
    }

    .summary-card-value.negative {
        color: #dc2626;
    }

    /* ===== Main Content Grid ===== */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    /* ===== Detail Cards ===== */
    .detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(139, 92, 246, 0.1);
        overflow: hidden;
    }

    .detail-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    }

    .detail-card-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
    }

    .detail-card-header h3 {
        font-size: 17px;
        font-weight: 700;
        color: #1e1b4b;
        margin: 0;
    }

    .detail-card-header p {
        font-size: 13px;
        color: #6b7280;
        margin: 2px 0 0 0;
    }

    .detail-card-body {
        padding: 24px;
    }

    /* ===== Detail Rows ===== */
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-label i {
        color: #8b5cf6;
        width: 16px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e1b4b;
        text-align: right;
    }

    /* ===== Status Badge ===== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }

    .status-badge.approved {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .status-badge.completed {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    /* ===== Transaction Type Badge ===== */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .type-badge.capital_contribution {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .type-badge.owner_drawing {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    .type-badge.loan_from_partner {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    .type-badge.loan_to_partner {
        background: linear-gradient(135deg, #fed7aa, #fdba74);
        color: #9a3412;
    }

    .type-badge.loan_repayment {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0e7490;
    }

    .type-badge.advance {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #6b21a8;
    }

    /* ===== Partner Info Card ===== */
    .partner-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .partner-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
    }

    .partner-details h4 {
        font-size: 15px;
        font-weight: 700;
        color: #1e1b4b;
        margin: 0 0 4px 0;
    }

    .partner-details p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* ===== Amount Display ===== */
    .amount-display {
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .amount-display.positive {
        color: #059669;
    }

    .amount-display.negative {
        color: #dc2626;
    }

    /* ===== Description Box ===== */
    .description-box {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.6;
    }

    .description-box.empty {
        color: #9ca3af;
        font-style: italic;
    }

    /* ===== Journal Entry Link ===== */
    .journal-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .journal-link:hover {
        background: linear-gradient(135deg, #e9d5ff, #ddd6fe);
        color: #6d28d9;
        transform: translateY(-2px);
    }

    /* ===== Quick Actions ===== */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .action-btn.edit {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    .action-btn.edit:hover {
        background: linear-gradient(135deg, #bfdbfe, #93c5fd);
        transform: translateY(-2px);
    }

    .action-btn.print {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .action-btn.print:hover {
        background: linear-gradient(135deg, #a7f3d0, #6ee7b7);
        transform: translateY(-2px);
    }

    .action-btn.delete {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    .action-btn.delete:hover {
        background: linear-gradient(135deg, #fecaca, #fca5a5);
        transform: translateY(-2px);
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
        .summary-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .pt-header-banner {
            padding: 24px 20px;
        }
        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        .content-area {
            padding: 20px;
        }
        .summary-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="pt-show-wrapper">
    <!-- Header Banner -->
    <div class="pt-header-banner">
        <div class="header-content">
            <div class="header-title-area">
                <h1>
                    <i class="fas fa-receipt"></i>
                    Transaction #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="header-subtitle">Partner transaction details and information</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('bookkeeping.partner.index') }}" class="btn-header btn-header-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
                </a>
                <a href="{{ action([\App\Http\Controllers\BookkeepingController::class, 'editPartnerTransaction'], [$transaction->id]) }}" class="btn-header btn-header-primary">
                    <i class="fas fa-edit"></i> Edit Transaction
                </a>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Summary Cards -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-icon purple">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <div class="summary-card-label">Transaction Type</div>
                        @php
                            $typeLabels = [
                                'capital_contribution' => 'Capital Contribution',
                                'owner_drawing' => 'Owner Drawing',
                                'loan_from_partner' => 'Loan from Partner',
                                'loan_to_partner' => 'Loan to Partner',
                                'loan_repayment' => 'Loan Repayment',
                                'advance' => 'Partner Advance',
                            ];
                            $typeLabel = $typeLabels[$transaction->transaction_type] ?? ucwords(str_replace('_', ' ', $transaction->transaction_type));
                        @endphp
                        <div class="summary-card-value">{{ $typeLabel }}</div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-icon green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div>
                        <div class="summary-card-label">Amount</div>
                        @php
                            $isPositive = in_array($transaction->transaction_type, ['capital_contribution', 'loan_from_partner']);
                        @endphp
                        <div class="summary-card-value {{ $isPositive ? 'positive' : 'negative' }}">
                            {{ $isPositive ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-icon blue">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <div class="summary-card-label">Transaction Date</div>
                        <div class="summary-card-value">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <div class="summary-card-icon amber">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div>
                        <div class="summary-card-label">Status</div>
                        <div class="summary-card-value">
                            <span class="status-badge {{ $transaction->status ?? 'pending' }}">
                                {{ ucfirst($transaction->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Transaction Details Card -->
                <div class="detail-card" style="margin-bottom: 24px;">
                    <div class="detail-card-header">
                        <div class="detail-card-header-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h3>Transaction Details</h3>
                            <p>Complete transaction information</p>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-hashtag"></i> Transaction ID
                            </span>
                            <span class="detail-value">#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-tags"></i> Type
                            </span>
                            <span class="detail-value">
                                <span class="type-badge {{ $transaction->transaction_type }}">
                                    {{ $typeLabel }}
                                </span>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-dollar-sign"></i> Amount
                            </span>
                            <span class="detail-value">
                                <span class="amount-display {{ $isPositive ? 'positive' : 'negative' }}">
                                    {{ $isPositive ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </span>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-university"></i> Account
                            </span>
                            <span class="detail-value">
                                {{ $transaction->account->name ?? '-' }}
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-calendar-alt"></i> Date
                            </span>
                            <span class="detail-value">{{ $transaction->transaction_date->format('F d, Y') }}</span>
                        </div>

                        @if($transaction->reference)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-file-alt"></i> Reference
                            </span>
                            <span class="detail-value">{{ $transaction->reference }}</span>
                        </div>
                        @endif

                        @if($transaction->journal_entry_id)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-book"></i> Journal Entry
                            </span>
                            <span class="detail-value">
                                <a href="{{ route('bookkeeping.journal.show', $transaction->journal_entry_id) }}" class="journal-link">
                                    <i class="fas fa-external-link-alt"></i>
                                    View Entry #{{ $transaction->journal_entry_id }}
                                </a>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Description Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-card-header-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div>
                            <h3>Description</h3>
                            <p>Transaction notes and details</p>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        <div class="description-box {{ !$transaction->description ? 'empty' : '' }}">
                            {{ $transaction->description ?: 'No description provided for this transaction.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Partner Info Card -->
                <div class="detail-card" style="margin-bottom: 24px;">
                    <div class="detail-card-header">
                        <div class="detail-card-header-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h3>Partner Information</h3>
                            <p>Transaction partner details</p>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        @if($transaction->partner)
                        <div class="partner-info">
                            <div class="partner-avatar">
                                {{ strtoupper(substr($transaction->partner->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($transaction->partner->last_name ?? '', 0, 1)) }}
                            </div>
                            <div class="partner-details">
                                <h4>{{ $transaction->partner->first_name }} {{ $transaction->partner->last_name }}</h4>
                                <p>{{ $transaction->partner->email ?? 'No email' }}</p>
                            </div>
                        </div>
                        @else
                        <p style="color: #9ca3af; font-style: italic;">Partner information not available</p>
                        @endif
                    </div>
                </div>

                <!-- Audit Info Card -->
                <div class="detail-card" style="margin-bottom: 24px;">
                    <div class="detail-card-header">
                        <div class="detail-card-header-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h3>Audit Trail</h3>
                            <p>Record history</p>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-user-plus"></i> Created By
                            </span>
                            <span class="detail-value">
                                {{ $transaction->createdBy->first_name ?? 'System' }} {{ $transaction->createdBy->last_name ?? '' }}
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-clock"></i> Created At
                            </span>
                            <span class="detail-value">{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
                        </div>

                        @if($transaction->updated_at != $transaction->created_at)
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-sync"></i> Last Updated
                            </span>
                            <span class="detail-value">{{ $transaction->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <div class="detail-card-header-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h3>Quick Actions</h3>
                            <p>Available operations</p>
                        </div>
                    </div>
                    <div class="detail-card-body">
                        <div class="quick-actions">
                            <a href="{{ action([\App\Http\Controllers\BookkeepingController::class, 'editPartnerTransaction'], [$transaction->id]) }}" class="action-btn edit">
                                <i class="fas fa-edit"></i> Edit Transaction
                            </a>
                            <button type="button" class="action-btn print" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Details
                            </button>
                            <button type="button" class="action-btn delete" onclick="deleteTransaction()">
                                <i class="fas fa-trash"></i> Delete Transaction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    function deleteTransaction() {
        Swal.fire({
            title: 'Delete Transaction?',
            text: 'This action cannot be undone. Are you sure you want to delete this transaction?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Coming soon functionality
                Swal.fire({
                    title: 'Coming Soon',
                    text: 'Delete functionality will be available soon.',
                    icon: 'info',
                    confirmButtonColor: '#8b5cf6'
                });
            }
        });
    }
</script>
@endsection





