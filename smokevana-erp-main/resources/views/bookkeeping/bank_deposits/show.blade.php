@extends('layouts.app')
@section('title', 'View Bank Deposit')

@section('css')
<style>
/* Bank Deposit Show - Professional Purple Theme */
.bd-page { background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); min-height: 100vh; padding-bottom: 40px; }

.bd-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25); position: relative; overflow: hidden;
}
.bd-header-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; }
.bd-header-banner h1, .bd-header-banner .subtitle, .bd-header-banner i { color: #fff !important; }
.bd-header-banner h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 12px; }
.bd-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.bd-header-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.bd-btn-back { background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
.bd-btn-back:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }
.bd-btn-action { padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; border: none; cursor: pointer; }
.bd-btn-action.process { background: #10b981; color: #fff; }
.bd-btn-action.process:hover { background: #059669; }
.bd-btn-action.void { background: #ef4444; color: #fff; }
.bd-btn-action.void:hover { background: #dc2626; }
.bd-btn-action.edit { background: #fff; color: #7c3aed; }
.bd-btn-action.edit:hover { background: #f5f3ff; text-decoration: none; color: #6d28d9; }

.bd-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
@media (max-width: 1200px) { .bd-grid { grid-template-columns: 1fr; } }

.bd-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); margin-bottom: 24px; overflow: hidden; border: 1px solid rgba(139, 92, 246, 0.06); }
.bd-card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
.bd-card-header-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 18px; }
.bd-card-header h3 { font-size: 16px; font-weight: 600; color: #1e1b4b; margin: 0; }
.bd-card-body { padding: 24px; }

.bd-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
@media (max-width: 768px) { .bd-info-grid { grid-template-columns: 1fr; } }
.bd-info-item { }
.bd-info-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.bd-info-value { font-size: 16px; font-weight: 600; color: #1e1b4b; }

.bd-lines-table { width: 100%; border-collapse: collapse; }
.bd-lines-table thead th { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; }
.bd-lines-table tbody td { padding: 14px 16px; border-bottom: 1px solid #f5f5f5; font-size: 14px; color: #374151; }
.bd-lines-table tbody tr:hover { background: #faf5ff; }
.bd-lines-table tfoot td { padding: 14px 16px; font-weight: 700; background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); }

/* Summary Card */
.bd-summary { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08); position: sticky; top: 24px; border: 1px solid rgba(139, 92, 246, 0.06); }
.bd-summary-header { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); padding: 20px 24px; border-radius: 16px 16px 0 0; }
.bd-summary-header h3 { font-size: 18px; font-weight: 600; color: #fff !important; margin: 0; display: flex; align-items: center; gap: 10px; }
.bd-summary-body { padding: 24px; }

.bd-total-display { text-align: center; padding: 24px; border-radius: 12px; margin-bottom: 20px; }
.bd-total-display.deposited { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
.bd-total-display.pending { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
.bd-total-display.voided { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
.bd-total-icon { font-size: 36px; margin-bottom: 8px; }
.bd-total-display.deposited .bd-total-icon { color: #059669; }
.bd-total-display.pending .bd-total-icon { color: #d97706; }
.bd-total-display.voided .bd-total-icon { color: #dc2626; }
.bd-total-value { font-size: 32px; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.bd-total-display.deposited .bd-total-value { color: #059669; }
.bd-total-display.pending .bd-total-value { color: #d97706; }
.bd-total-display.voided .bd-total-value { color: #dc2626; }
.bd-total-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
.bd-total-display.deposited .bd-total-label { color: #065f46; }
.bd-total-display.pending .bd-total-label { color: #92400e; }
.bd-total-display.voided .bd-total-label { color: #991b1b; }

.bd-info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.bd-info-row-label { font-size: 14px; color: #6b7280; }
.bd-info-row-value { font-weight: 600; color: #1e1b4b; }

.bd-status { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; }
.bd-status.pending { background: #fef3c7; color: #d97706; }
.bd-status.deposited { background: #d1fae5; color: #059669; }
.bd-status.reconciled { background: #dbeafe; color: #2563eb; }
.bd-status.voided { background: #fee2e2; color: #dc2626; }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.bd-card, .bd-summary { animation: fadeInUp 0.4s ease forwards; }
</style>
@endsection

@section('content')
<section class="content bd-page">
    <div class="bd-header-banner">
        <div>
            <h1><i class="fas fa-piggy-bank"></i> Deposit #{{ $deposit->deposit_number ?? 'N/A' }}</h1>
            <p class="subtitle">{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('F d, Y') }}</p>
        </div>
        <div class="bd-header-actions">
            <a href="{{ route('bookkeeping.deposits.index') }}" class="bd-btn-back"><i class="fas fa-arrow-left"></i> Back</a>
            @if($deposit->status === 'pending')
            <a href="{{ route('bookkeeping.deposits.edit', $deposit->id) }}" class="bd-btn-action edit"><i class="fas fa-edit"></i> Edit</a>
            <button type="button" class="bd-btn-action process" id="process_btn" data-id="{{ $deposit->id }}"><i class="fas fa-check"></i> Process</button>
            <button type="button" class="bd-btn-action void" id="void_btn" data-id="{{ $deposit->id }}"><i class="fas fa-times"></i> Void</button>
            @endif
        </div>
    </div>

    <div class="bd-grid">
        <div class="bd-main">
            <!-- Deposit Details -->
            <div class="bd-card">
                <div class="bd-card-header">
                    <div class="bd-card-header-icon"><i class="fas fa-info-circle"></i></div>
                    <h3>Deposit Details</h3>
                </div>
                <div class="bd-card-body">
                    <div class="bd-info-grid">
                        <div class="bd-info-item">
                            <div class="bd-info-label">Deposit Number</div>
                            <div class="bd-info-value">{{ $deposit->deposit_number ?? '-' }}</div>
                        </div>
                        <div class="bd-info-item">
                            <div class="bd-info-label">Deposit Date</div>
                            <div class="bd-info-value">{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('M d, Y') }}</div>
                        </div>
                        <div class="bd-info-item">
                            <div class="bd-info-label">Deposit To</div>
                            <div class="bd-info-value"><i class="fas fa-university" style="color: #8b5cf6; margin-right: 6px;"></i>{{ $deposit->depositToAccount->name ?? '-' }}</div>
                        </div>
                        <div class="bd-info-item">
                            <div class="bd-info-label">Status</div>
                            <div class="bd-info-value">
                                <span class="bd-status {{ $deposit->status }}">
                                    @if($deposit->status === 'deposited')
                                    <i class="fas fa-check-circle"></i>
                                    @elseif($deposit->status === 'pending')
                                    <i class="fas fa-clock"></i>
                                    @elseif($deposit->status === 'voided')
                                    <i class="fas fa-ban"></i>
                                    @endif
                                    {{ ucfirst($deposit->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="bd-info-item" style="grid-column: span 2;">
                            <div class="bd-info-label">Memo</div>
                            <div class="bd-info-value">{{ $deposit->memo ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deposit Lines -->
            <div class="bd-card">
                <div class="bd-card-header">
                    <div class="bd-card-header-icon"><i class="fas fa-list"></i></div>
                    <h3>Deposit Items ({{ $deposit->lines->count() }})</h3>
                </div>
                <div class="bd-card-body" style="padding: 0;">
                    <table class="bd-lines-table">
                        <thead>
                            <tr>
                                <th>Received From</th>
                                <th>Account</th>
                                <th>Payment Method</th>
                                <th>Ref #</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposit->lines as $line)
                            <tr>
                                <td>{{ $line->contact->name ?? '-' }}</td>
                                <td>{{ $line->account->name ?? '-' }}</td>
                                <td>{{ ucfirst($line->payment_method ?? '-') }}</td>
                                <td>{{ $line->ref_no ?? '-' }}</td>
                                <td style="text-align: right; font-weight: 700; color: #059669; font-family: 'SF Mono', Monaco, monospace;">${{ number_format($line->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i><br>
                                    No deposit items found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 600;">Total:</td>
                                <td style="text-align: right; font-weight: 700; color: #059669; font-family: 'SF Mono', Monaco, monospace; font-size: 18px;">${{ number_format($deposit->total_amount ?? $deposit->lines->sum('amount'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Journal Entry (if processed) -->
            @if($deposit->journalEntry)
            <div class="bd-card">
                <div class="bd-card-header">
                    <div class="bd-card-header-icon"><i class="fas fa-book"></i></div>
                    <h3>Journal Entry</h3>
                </div>
                <div class="bd-card-body" style="padding: 0;">
                    <table class="bd-lines-table">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th style="text-align: right;">Debit</th>
                                <th style="text-align: right;">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deposit->journalEntry->lines as $jeLine)
                            <tr>
                                <td>{{ $jeLine->account->name ?? '-' }}</td>
                                <td style="text-align: right; font-family: 'SF Mono', Monaco, monospace; color: {{ $jeLine->debit_amount > 0 ? '#059669' : '#9ca3af' }};">
                                    {{ $jeLine->debit_amount > 0 ? '$' . number_format($jeLine->debit_amount, 2) : '-' }}
                                </td>
                                <td style="text-align: right; font-family: 'SF Mono', Monaco, monospace; color: {{ $jeLine->credit_amount > 0 ? '#dc2626' : '#9ca3af' }};">
                                    {{ $jeLine->credit_amount > 0 ? '$' . number_format($jeLine->credit_amount, 2) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Summary Sidebar -->
        <div class="bd-sidebar">
            <div class="bd-summary">
                <div class="bd-summary-header">
                    <h3><i class="fas fa-receipt"></i> Summary</h3>
                </div>
                <div class="bd-summary-body">
                    <div class="bd-total-display {{ $deposit->status }}">
                        <div class="bd-total-icon">
                            @if($deposit->status === 'deposited')
                            <i class="fas fa-check-circle"></i>
                            @elseif($deposit->status === 'pending')
                            <i class="fas fa-clock"></i>
                            @else
                            <i class="fas fa-ban"></i>
                            @endif
                        </div>
                        <div class="bd-total-value">${{ number_format($deposit->total_amount ?? $deposit->lines->sum('amount'), 2) }}</div>
                        <div class="bd-total-label">{{ ucfirst($deposit->status) }}</div>
                    </div>

                    <div class="bd-info-row">
                        <span class="bd-info-row-label">Items</span>
                        <span class="bd-info-row-value">{{ $deposit->lines->count() }}</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-row-label">Deposit To</span>
                        <span class="bd-info-row-value">{{ $deposit->depositToAccount->name ?? '-' }}</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-row-label">Created By</span>
                        <span class="bd-info-row-value">{{ $deposit->createdBy->first_name ?? 'System' }} {{ $deposit->createdBy->last_name ?? '' }}</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-row-label">Created</span>
                        <span class="bd-info-row-value">{{ $deposit->created_at ? $deposit->created_at->format('M d, Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Process button
    $('#process_btn').click(function() {
        var depositId = $(this).data('id');
        var $btn = $(this);
        
        Swal.fire({
            title: 'Process Deposit?',
            text: 'This will mark the deposit as processed and create the journal entry.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check"></i> Yes, Process It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ url("bookkeeping/bank-deposits") }}/' + depositId + '/process',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Deposit processed successfully!');
                            setTimeout(function() { location.reload(); }, 1000);
                        } else {
                            toastr.error(response.msg || 'Failed to process deposit.');
                            $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Process');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.msg || 'An error occurred.');
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Process');
                    }
                });
            }
        });
    });
    
    // Void button
    $('#void_btn').click(function() {
        var depositId = $(this).data('id');
        var $btn = $(this);
        
        Swal.fire({
            title: 'Void Deposit?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-times"></i> Yes, Void It',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ url("bookkeeping/bank-deposits") }}/' + depositId + '/void',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg || 'Deposit voided successfully!');
                            setTimeout(function() { location.reload(); }, 1000);
                        } else {
                            toastr.error(response.msg || 'Failed to void deposit.');
                            $btn.prop('disabled', false).html('<i class="fas fa-times"></i> Void');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.msg || 'An error occurred.');
                        $btn.prop('disabled', false).html('<i class="fas fa-times"></i> Void');
                    }
                });
            }
        });
    });
});
</script>
@endsection
