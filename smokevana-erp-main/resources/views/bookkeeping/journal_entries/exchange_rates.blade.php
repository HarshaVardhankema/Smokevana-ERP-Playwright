@extends('layouts.app')
@section('title', __('bookkeeping.exchange_rates'))

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

.bk-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(124, 58, 237, 0.08);
    margin-bottom: 24px;
}

.bk-card-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 20px 24px;
    border-bottom: 1px solid rgba(124, 58, 237, 0.1);
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

.bk-form-group {
    margin-bottom: 20px;
}

.bk-form-label {
    display: block;
    font-weight: 600;
    color: #4c1d95;
    margin-bottom: 8px;
    font-size: 14px;
}

.bk-form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.bk-form-control:focus {
    border-color: #7c3aed;
    outline: none;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
}

.bk-btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
}

.bk-btn-outline {
    background: #fff;
    color: #7c3aed !important;
    border: 2px solid #7c3aed;
    border-radius: 10px;
    padding: 10px 20px;
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

.bk-table tbody tr:hover {
    background: #faf5ff;
}

.bk-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
}

.currency-pair {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.currency-code {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 13px;
    color: #7c3aed;
}

.rate-value {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

.add-rate-form {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 24px;
    border-radius: 12px;
}

.pagination {
    justify-content: center;
    margin-top: 20px;
}

.pagination .page-link {
    color: #7c3aed;
    border-radius: 8px;
    margin: 0 4px;
}

.pagination .page-item.active .page-link {
    background: #7c3aed;
    border-color: #7c3aed;
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1200px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div>
                <h1><i class="fas fa-exchange-alt"></i> {{ __('bookkeeping.exchange_rates') }}</h1>
                <p class="subtitle">{{ __('bookkeeping.exchange_rates_desc') }}</p>
            </div>
            <a href="{{ route('bookkeeping.journal.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back_to_journal') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <!-- Add Rate Form -->
                <div class="bk-card">
                    <div class="bk-card-header">
                        <h3><i class="fas fa-plus-circle"></i> {{ __('bookkeeping.add_rate') }}</h3>
                    </div>
                    <div class="bk-card-body">
                        <form id="addRateForm">
                            @csrf
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.from_currency') }} *</label>
                                <select name="from_currency" class="bk-form-control" required>
                                    <option value="">{{ __('bookkeeping.select_currency') }}</option>
                                    @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.to_currency') }} *</label>
                                <select name="to_currency" class="bk-form-control" required>
                                    <option value="">{{ __('bookkeeping.select_currency') }}</option>
                                    @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}" {{ $code === 'USD' ? 'selected' : '' }}>{{ $code }} - {{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.exchange_rate') }} *</label>
                                <input type="number" name="rate" class="bk-form-control" step="0.00000001" min="0.00000001" required placeholder="1.00000000">
                            </div>
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.effective_date') }} *</label>
                                <input type="date" name="effective_date" class="bk-form-control" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <button type="submit" class="bk-btn-primary" style="width: 100%;">
                                <i class="fas fa-save"></i> {{ __('bookkeeping.save_rate') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Rates List -->
                <div class="bk-card">
                    <div class="bk-card-header">
                        <h3><i class="fas fa-list"></i> {{ __('bookkeeping.saved_rates') }}</h3>
                    </div>
                    <div class="bk-card-body" style="padding: 0;">
                        @if($rates->count() > 0)
                        <table class="bk-table">
                            <thead>
                                <tr>
                                    <th>{{ __('bookkeeping.currency_pair') }}</th>
                                    <th>{{ __('bookkeeping.rate') }}</th>
                                    <th>{{ __('bookkeeping.effective_date') }}</th>
                                    <th>{{ __('bookkeeping.source') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rates as $rate)
                                <tr>
                                    <td>
                                        <div class="currency-pair">
                                            <span class="currency-code">{{ $rate->from_currency }}</span>
                                            <i class="fas fa-arrow-right" style="color: #9ca3af;"></i>
                                            <span class="currency-code">{{ $rate->to_currency }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="rate-value">{{ number_format($rate->rate, 8) }}</span>
                                    </td>
                                    <td>{{ $rate->effective_date->format('M d, Y') }}</td>
                                    <td>
                                        <span style="color: #6b7280; font-size: 13px;">{{ ucfirst($rate->source ?? 'manual') }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        {{ $rates->links() }}
                        @else
                        <div style="text-align: center; padding: 60px 20px;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <i class="fas fa-exchange-alt" style="font-size: 36px; color: #7c3aed;"></i>
                            </div>
                            <h4 style="color: #4c1d95; margin-bottom: 8px;">{{ __('bookkeeping.no_rates_yet') }}</h4>
                            <p style="color: #6b7280;">{{ __('bookkeeping.no_rates_desc') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#addRateForm').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("bookkeeping.saving") }}...');
        
        $.ajax({
            url: '{{ route("bookkeeping.exchange-rates.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    location.reload();
                } else {
                    toastr.error(response.msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> {{ __("bookkeeping.save_rate") }}');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error saving rate');
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> {{ __("bookkeeping.save_rate") }}');
            }
        });
    });
});
</script>
@endsection



