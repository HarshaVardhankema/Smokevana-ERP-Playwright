@extends('layouts.app')

@section('title', __('bookkeeping.create_account'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* Create Account Form - Amazon Theme */
.coa-form-page {
    background: #EAEDED;
    min-height: 100vh;
    padding-bottom: 40px;
}

.coa-form-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
    border-radius: 10px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.coa-form-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    z-index: 1;
}

.coa-form-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.coa-form-header h1 i {
    font-size: 28px;
    color: #fff !important;
}

.coa-form-header .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.coa-form-header .btn-light {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
}

.coa-form-header .btn-light:hover {
    background: rgba(255,255,255,0.3);
    color: #fff;
}

.coa-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.coa-form-card-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    padding: 20px 24px;
    border-bottom: 3px solid #ff9900;
}

.coa-form-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.coa-form-card-title i {
    color: #ff9900;
}

.coa-form-card-body {
    padding: 24px;
}

.coa-form-group {
    margin-bottom: 20px;
}

.coa-form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    margin-bottom: 8px;
    display: block;
}

.coa-form-group label .required {
    color: #ef4444;
}

.coa-form-group .form-control,
.coa-form-group .form-select {
    border-radius: 10px !important;
    border: 1px solid #e5e7eb !important;
    padding: 12px 16px !important;
    font-size: 14px !important;
    transition: all 0.2s ease;
    width: 100% !important;
    display: block !important;
    box-sizing: border-box !important;
    height: auto !important;
    background-color: #fff !important;
}

.coa-form-group select.form-control {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-color: #fff !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 16px center !important;
    background-size: 12px !important;
    padding-right: 40px !important;
    cursor: pointer;
    min-height: 46px !important;
    line-height: 1.5 !important;
}

.coa-form-group select.form-control:focus,
.coa-form-group .form-control:focus {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25) !important;
    outline: none !important;
}

.coa-form-group .help-block {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.coa-form-group .has-error .form-control {
    border-color: #ef4444;
}

.coa-form-group .has-error .help-block {
    color: #ef4444;
}

.coa-form-actions {
    padding: 20px 24px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.coa-form-actions .btn {
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 14px;
}

.coa-form-actions .btn-primary {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border: 1px solid #C7511F;
}

.coa-form-actions .btn-primary:hover {
    opacity: 0.95;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255,153,0,0.35);
}

.coa-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

@media (max-width: 992px) {
    .coa-type-selector { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .coa-type-selector { grid-template-columns: 1fr; }
}

.coa-type-option {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.coa-type-option:hover {
    border-color: #ff9900;
    background: #fff8e7;
}

.coa-type-option.selected {
    border-color: #ff9900;
    background: #fff8e7;
}

.coa-type-option input {
    display: none;
}

.coa-type-option i {
    font-size: 24px;
    margin-bottom: 8px;
    display: block;
}

.coa-type-option.asset i { color: #10b981; }
.coa-type-option.liability i { color: #ef4444; }
.coa-type-option.equity i { color: #3b82f6; }
.coa-type-option.income i { color: #8b5cf6; }
.coa-type-option.expense i { color: #f59e0b; }
.coa-type-option.cogs i { color: #6366f1; }

.coa-type-option .type-label {
    font-weight: 600;
    color: #374151;
    font-size: 13px;
}

.coa-quick-templates {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.coa-quick-template {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.coa-quick-template:hover {
    background: #ede9fe;
    border-color: #c4b5fd;
    color: #7c3aed;
}

.coa-tip-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #D5D9D9;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}

.coa-tip-card h4 {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    margin: -20px -20px 12px -20px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-bottom: 3px solid #ff9900;
}
.coa-tip-card h4 i { color: #ff9900; margin-right: 8px; }

.coa-tip-card p {
    font-size: 13px;
    color: #374151;
    margin: 0;
}

.coa-tip-card ul {
    margin: 8px 0 0;
    padding-left: 20px;
    font-size: 12px;
    color: #6b7280;
}
</style>
@endsection

@section('content')
<section class="content coa-form-page">
    
    <!-- Header -->
    <div class="coa-form-header">
        <div>
            <h1><i class="fas fa-plus-circle"></i> @lang('bookkeeping.create_account')</h1>
            <p class="subtitle">@lang('bookkeeping.add_new_account_to_chart')</p>
        </div>
        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> @lang('messages.back')
        </a>
    </div>

    <form action="{{ route('bookkeeping.accounts.store') }}" method="POST" id="create_account_form">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="coa-form-card">
                    <div class="coa-form-card-header">
                        <h3 class="coa-form-card-title">
                            <i class="fas fa-info-circle"></i>
                            @lang('bookkeeping.account_details')
                        </h3>
                    </div>
                    <div class="coa-form-card-body">
                        
                        <!-- Account Type Selector -->
                        <div class="coa-form-group">
                            <label>@lang('bookkeeping.account_type') <span class="required">*</span></label>
                            <div class="coa-type-selector">
                                @php
                                    $typeIcons = [
                                        'asset' => 'fa-wallet',
                                        'liability' => 'fa-credit-card',
                                        'equity' => 'fa-landmark',
                                        'income' => 'fa-arrow-up',
                                        'expense' => 'fa-arrow-down',
                                        'cost_of_goods_sold' => 'fa-boxes'
                                    ];
                                @endphp
                                @foreach($accountTypes as $type => $label)
                                <label class="coa-type-option {{ str_replace('cost_of_goods_sold', 'cogs', $type) }} {{ old('account_type') == $type ? 'selected' : '' }}">
                                    <input type="radio" name="account_type" value="{{ $type }}" {{ old('account_type') == $type ? 'checked' : '' }} required>
                                    <i class="fas {{ $typeIcons[$type] ?? 'fa-folder' }}"></i>
                                    <span class="type-label">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                            @if($errors->has('account_type'))
                                <span class="help-block text-danger">{{ $errors->first('account_type') }}</span>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('account_code') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.account_code') <span class="required">*</span></label>
                                    <input type="text" name="account_code" class="form-control" 
                                           value="{{ old('account_code') }}" 
                                           placeholder="e.g., 1000" required>
                                    @if($errors->has('account_code'))
                                        <span class="help-block">{{ $errors->first('account_code') }}</span>
                                    @else
                                        <span class="help-block">@lang('bookkeeping.unique_identifier_for_account')</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.account_name') <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g., Cash on Hand" required>
                                    @if($errors->has('name'))
                                        <span class="help-block">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('detail_type') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.detail_type')</label>
                                    <select name="detail_type" class="form-control" id="detail_type">
                                        <option value="">@lang('messages.please_select')</option>
                                    </select>
                                    @if($errors->has('detail_type'))
                                        <span class="help-block">{{ $errors->first('detail_type') }}</span>
                                    @else
                                        <span class="help-block">@lang('bookkeeping.categorize_your_account')</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group">
                                    <label>@lang('bookkeeping.parent_account')</label>
                                    <select name="parent_id" class="form-control" id="parent_id">
                                        <option value="">@lang('bookkeeping.no_parent_top_level')</option>
                                        @foreach($parentAccounts ?? [] as $name => $id)
                                            <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">@lang('bookkeeping.select_parent_for_sub_account')</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group">
                                    <label>@lang('bookkeeping.opening_balance')</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" name="opening_balance" class="form-control" 
                                               value="{{ old('opening_balance', '0.00') }}" 
                                               step="0.01" placeholder="0.00">
                                    </div>
                                    <span class="help-block">@lang('bookkeeping.starting_balance_for_account')</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group">
                                    <label>@lang('bookkeeping.opening_balance_date')</label>
                                    <input type="date" name="opening_balance_date" class="form-control" 
                                           value="{{ old('opening_balance_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="coa-form-group">
                            <label>@lang('bookkeeping.description')</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="@lang('bookkeeping.optional_description')">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="coa-form-actions">
                        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> @lang('messages.cancel')
                        </a>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('create_account_form').submit();">
                            <i class="fas fa-save"></i> @lang('messages.save')
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Tips Card -->
                <div class="coa-tip-card">
                    <h4><i class="fas fa-lightbulb"></i> @lang('bookkeeping.tips')</h4>
                    <p>@lang('bookkeeping.account_code_tip')</p>
                    <ul>
                        <li>1000-1999: @lang('bookkeeping.asset')s</li>
                        <li>2000-2999: @lang('bookkeeping.liability')s</li>
                        <li>3000-3999: @lang('bookkeeping.equity')</li>
                        <li>4000-4999: @lang('bookkeeping.income')</li>
                        <li>5000-5999: COGS</li>
                        <li>6000-9999: @lang('bookkeeping.expense')s</li>
                    </ul>
                </div>

                <!-- Quick Actions - Temporarily hidden until routes are fully implemented -->
                {{-- 
                <div class="coa-form-card">
                    <div class="coa-form-card-header">
                        <h3 class="coa-form-card-title">
                            <i class="fas fa-bolt"></i>
                            @lang('bookkeeping.quick_actions')
                        </h3>
                    </div>
                    <div class="coa-form-card-body">
                        <a href="{{ route('bookkeeping.accounts.templates') }}" class="btn btn-info btn-block" style="margin-bottom: 10px;">
                            <i class="fas fa-th-large"></i> @lang('bookkeeping.use_industry_template')
                        </a>
                        <a href="{{ route('bookkeeping.accounts.import') }}" class="btn btn-success btn-block">
                            <i class="fas fa-file-import"></i> @lang('bookkeeping.import_accounts')
                        </a>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var detailTypes = @json($detailTypes);

    // Handle account type selection
    $('.coa-type-option').on('click', function() {
        $('.coa-type-option').removeClass('selected');
        $(this).addClass('selected');
        
        var accountType = $(this).find('input').val();
        updateDetailTypes(accountType);
    });

    // Update detail types based on account type
    function updateDetailTypes(accountType) {
        var $detailType = $('#detail_type');
        
        $detailType.empty();
        $detailType.append('<option value="">@lang("messages.please_select")</option>');
        
        if (accountType && detailTypes[accountType]) {
            $.each(detailTypes[accountType], function(key, label) {
                $detailType.append('<option value="' + key + '">' + label + '</option>');
            });
        }
    }

    // Initialize detail types if account type is pre-selected
    var initialType = $('input[name="account_type"]:checked').val();
    if (initialType) {
        updateDetailTypes(initialType);
    }
});
</script>
@endsection




