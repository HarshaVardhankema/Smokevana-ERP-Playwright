@extends('layouts.app')
@section('title', __('bookkeeping.recurring_entries'))

@section('css')
<style>
/* Professional Purple Theme */
.bk-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

.bk-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
}

.bk-header-banner h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.bk-header-banner .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.bk-btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
    color: #fff !important;
}

.bk-btn-outline {
    background: #fff;
    color: #7c3aed !important;
    border: 2px solid #7c3aed;
    border-radius: 10px;
    padding: 8px 18px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-outline:hover {
    background: #7c3aed;
    color: #fff !important;
}

.bk-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(124, 58, 237, 0.08);
    margin-bottom: 24px;
    overflow: hidden;
}

.bk-card-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 20px 24px;
    border-bottom: 1px solid rgba(124, 58, 237, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bk-card-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #4c1d95;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.bk-card-body {
    padding: 24px;
}

/* Stats Cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    border: 1px solid rgba(124, 58, 237, 0.08);
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 20px;
}

.stat-card-icon.purple {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #7c3aed;
}

.stat-card-icon.green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.stat-card-icon.yellow {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.stat-card-icon.blue {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.stat-card-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

.stat-card-label {
    font-size: 13px;
    color: #6b7280;
}

/* Table */
.bk-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.bk-table thead th {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b21a8;
    border-bottom: 2px solid rgba(124, 58, 237, 0.15);
}

.bk-table tbody tr {
    transition: all 0.2s ease;
}

.bk-table tbody tr:hover {
    background: #faf5ff;
}

.bk-table tbody td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.bk-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.bk-badge-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.bk-badge-yellow {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.bk-badge-blue {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.bk-badge-gray {
    background: #f3f4f6;
    color: #6b7280;
}

.bk-badge-purple {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #7c3aed;
}

.bk-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 12px;
}

.bk-action-btn-play {
    background: #d1fae5;
    color: #059669;
}

.bk-action-btn-play:hover {
    background: #059669;
    color: #fff;
}

.bk-action-btn-pause {
    background: #fef3c7;
    color: #d97706;
}

.bk-action-btn-pause:hover {
    background: #d97706;
    color: #fff;
}

.bk-action-btn-generate {
    background: #dbeafe;
    color: #2563eb;
}

.bk-action-btn-generate:hover {
    background: #2563eb;
    color: #fff;
}

.bk-action-btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.bk-action-btn-delete:hover {
    background: #dc2626;
    color: #fff;
}

.bk-empty-state {
    text-align: center;
    padding: 60px 20px;
}

.bk-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.bk-empty-icon i {
    font-size: 36px;
    color: #7c3aed;
}

.frequency-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: #f3f4f6;
    color: #4b5563;
}

.next-run-date {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.next-run-date .date {
    font-weight: 600;
    color: #1f2937;
}

.next-run-date .days {
    font-size: 11px;
    color: #6b7280;
}

@media (max-width: 1024px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div>
                <h1><i class="fas fa-sync-alt"></i> {{ __('bookkeeping.recurring_entries') }}</h1>
                <p class="subtitle">{{ __('bookkeeping.recurring_entries_desc') }}</p>
            </div>
            <div style="display: flex; gap: 12px; position: relative; z-index: 2;">
                <a href="{{ route('bookkeeping.journal.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                    <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back_to_journal') }}
                </a>
                <a href="{{ route('bookkeeping.journal.recurring.create') }}" class="bk-btn-primary" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-plus"></i> {{ __('bookkeeping.create_recurring') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-card-icon purple">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stat-card-value">{{ $recurrences->count() }}</div>
                <div class="stat-card-label">{{ __('bookkeeping.total_recurrences') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stat-card-value">{{ $recurrences->where('status', 'active')->count() }}</div>
                <div class="stat-card-label">{{ __('bookkeeping.active') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon yellow">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-card-value">{{ $recurrences->where('status', 'paused')->count() }}</div>
                <div class="stat-card-label">{{ __('bookkeeping.paused') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon blue">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-value">{{ $recurrences->where('next_run_date', '<=', now())->where('status', 'active')->count() }}</div>
                <div class="stat-card-label">{{ __('bookkeeping.due_today') }}</div>
            </div>
        </div>

        <!-- Recurrences Card -->
        <div class="bk-card">
            <div class="bk-card-header">
                <h3><i class="fas fa-list"></i> {{ __('bookkeeping.all_recurring_entries') }}</h3>
            </div>
            <div class="bk-card-body" style="padding: 0;">
                @if($recurrences->count() > 0)
                <table class="bk-table">
                    <thead>
                        <tr>
                            <th>{{ __('bookkeeping.name') }}</th>
                            <th>{{ __('bookkeeping.frequency') }}</th>
                            <th>{{ __('bookkeeping.next_run') }}</th>
                            <th>{{ __('bookkeeping.generated') }}</th>
                            <th>{{ __('bookkeeping.amount') }}</th>
                            <th>{{ __('bookkeeping.status') }}</th>
                            <th style="width: 150px;">{{ __('bookkeeping.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recurrences as $recurrence)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #1f2937;">{{ $recurrence->name }}</div>
                                @if($recurrence->template)
                                <div style="font-size: 12px; color: #6b7280;">
                                    <i class="fas fa-file-alt"></i> {{ $recurrence->template->name }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="frequency-badge">
                                    <i class="fas fa-redo"></i>
                                    {{ $frequencies[$recurrence->frequency] ?? ucfirst($recurrence->frequency) }}
                                    @if($recurrence->interval > 1)
                                    (x{{ $recurrence->interval }})
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="next-run-date">
                                    <span class="date">{{ $recurrence->next_run_date->format('M d, Y') }}</span>
                                    @php
                                        $daysUntil = now()->diffInDays($recurrence->next_run_date, false);
                                    @endphp
                                    <span class="days">
                                        @if($daysUntil < 0)
                                        <span style="color: #dc2626;">{{ abs($daysUntil) }} days overdue</span>
                                        @elseif($daysUntil === 0)
                                        <span style="color: #d97706;">Due today</span>
                                        @else
                                        In {{ $daysUntil }} days
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #7c3aed;">{{ $recurrence->occurrences_count }}</span>
                                @if($recurrence->occurrences_limit)
                                <span style="color: #9ca3af;">/ {{ $recurrence->occurrences_limit }}</span>
                                @endif
                            </td>
                            <td>
                                @if($recurrence->amount)
                                <span style="font-weight: 600;">{{ number_format($recurrence->amount, 2) }}</span>
                                @else
                                <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($recurrence->status)
                                    @case('active')
                                        <span class="bk-badge bk-badge-green">
                                            <i class="fas fa-play-circle"></i> {{ __('bookkeeping.active') }}
                                        </span>
                                        @break
                                    @case('paused')
                                        <span class="bk-badge bk-badge-yellow">
                                            <i class="fas fa-pause-circle"></i> {{ __('bookkeeping.paused') }}
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="bk-badge bk-badge-blue">
                                            <i class="fas fa-check-circle"></i> {{ __('bookkeeping.completed') }}
                                        </span>
                                        @break
                                    @default
                                        <span class="bk-badge bk-badge-gray">
                                            <i class="fas fa-ban"></i> {{ __('bookkeeping.cancelled') }}
                                        </span>
                                @endswitch
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    @if($recurrence->status === 'active')
                                    <button class="bk-action-btn bk-action-btn-generate generate-btn" 
                                            data-id="{{ $recurrence->id }}" title="{{ __('bookkeeping.generate_now') }}">
                                        <i class="fas fa-bolt"></i>
                                    </button>
                                    <button class="bk-action-btn bk-action-btn-pause pause-btn" 
                                            data-id="{{ $recurrence->id }}" title="{{ __('bookkeeping.pause') }}">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                    @elseif($recurrence->status === 'paused')
                                    <button class="bk-action-btn bk-action-btn-play resume-btn" 
                                            data-id="{{ $recurrence->id }}" title="{{ __('bookkeeping.resume') }}">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    @endif
                                    <button class="bk-action-btn bk-action-btn-delete delete-btn" 
                                            data-id="{{ $recurrence->id }}" data-name="{{ $recurrence->name }}" title="{{ __('bookkeeping.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="bk-empty-state">
                    <div class="bk-empty-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 style="color: #4c1d95; margin-bottom: 8px;">{{ __('bookkeeping.no_recurring_entries') }}</h4>
                    <p style="color: #6b7280; margin-bottom: 20px;">{{ __('bookkeeping.no_recurring_entries_desc') }}</p>
                    <a href="{{ route('bookkeeping.journal.recurring.create') }}" class="bk-btn-primary">
                        <i class="fas fa-plus"></i> {{ __('bookkeeping.create_first_recurring') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Pause
    $('.pause-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ url("bookkeeping/recurring-entries") }}/' + id + '/pause',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                }
            }
        });
    });
    
    // Resume
    $('.resume-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ url("bookkeeping/recurring-entries") }}/' + id + '/resume',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                }
            }
        });
    });
    
    // Generate
    $('.generate-btn').on('click', function() {
        var id = $(this).data('id');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ url("bookkeeping/recurring-entries") }}/' + id + '/generate',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-bolt"></i>');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error generating entry');
                $btn.prop('disabled', false).html('<i class="fas fa-bolt"></i>');
            }
        });
    });
    
    // Delete
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        swal({
            title: '{{ __("bookkeeping.confirm_delete") }}',
            text: '{{ __("bookkeeping.delete_recurring_warning") }}'.replace(':name', name),
            icon: 'warning',
            buttons: ['{{ __("messages.cancel") }}', '{{ __("messages.delete") }}'],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ url("bookkeeping/recurring-entries") }}/' + id,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection



